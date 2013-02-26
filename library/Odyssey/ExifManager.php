<?php
/**
 * This file is part of Odyssey theme for wordpress.
 *
 * (c) 2013 Pierre Bodilis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Odyssey;

/**
 *  Exif helper functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage Exif
 */
class ExifManager
{
    const METADATA_NAME = '_wp_odyssey_metadata_exif';

    const OPTION_NAME = 'odyssey_settings_exif';
    const SUBMIT      = 'odyssey_submit_exif';
    const RESET       = 'odyssey_reset_exif';

    static private $instance;
    static public function getInstance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array())
    {
        Admin::getInstance()->register($this);
        $this->exifCache = array();
    }

    public function getPageTitle()
    {
        return 'Exif settings';
    }
    
    public function getMenuTitle()
    {
        return $this->getPageTitle();
    }
    
    public function getMenuSlug()
    {
        return 'odyssey-settings-exifs';
    }

    static public function exif_id2label($exif_id)
    {
        $id2label = array(
            'Make'             => __('Manufacturer: '),
            'Model'            => __('Model Name: '),
            'DateTimeOriginal' => __('Date: '),
            'ExposureProgram'  => __('Exposure Program: '),
            'ExposureTime'     => __('Exposure Time: '),
            'FNumber'          => __('F Number: '),
            'ISOSpeedRatings'  => __('ISO: '),
            'FocalLength'      => __('Focal Length: '),
            'MeteringMode'     => __('Metering Mode: '),
            'LightSource'      => __('Light Source: '),
            'SensingMethod'    => __('Sensing Method: '),
            'ExposureMode'     => __('Exposure Mode: '),

            'FileName'         => __('File Name: '),
            'FileSize'         => __('File Size: '),
            'Software'         => __('Software: '),
            'XResolution'      => __('X Resolution: '),
            'YResolution'      => __('Y Resolution: '),
            'ExifVersion'      => __('Exif Version: '),

            'Title'            => __('Title: '),
        );
        return $id2label[ $exif_id ];
    }

    static public function get_default_exif_settings()
    {
        return array(
            'Make'             => false,
            'Model'            => true,
            'DateTimeOriginal' => true,
            'ExposureProgram'  => true,
            'ExposureTime'     => true,
            'FNumber'          => true,
            'ISOSpeedRatings'  => true,
            'FocalLength'      => true,
            'MeteringMode'     => false,
            'LightSource'      => false,
            'SensingMethod'    => false,
            'ExposureMode'     => true,

            'FileName'         => false,
            'FileSize'         => false,
            'Software'         => false,
            'XResolution'      => false,
            'YResolution'      => false,
            'ExifVersion'      => false,

            'Title'            => false,
        );
    }

    public function get_exif_settings()
    {
        return get_option(self::OPTION_NAME, self::get_default_exif_settings());
    }

    function get_setting_page()
    {
        if ( isset( $_POST[ self::RESET ] ) ) {
            delete_option(self::OPTION_NAME);
        }
        $settings = $this->get_exif_settings();
        if (isset($_POST[self::SUBMIT])) {
            $doUpdate = false;
            foreach ($settings as $setting => &$enabled) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[ $setting ] ) && ! $enabled) {
                    $enabled = true;
                    $doUpdate = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[ $setting ] ) && $enabled) {
                    $enabled = false;
                    $doUpdate = true;
                }
            }
            $doUpdate && update_option(self::OPTION_NAME, $settings);
        }

        $data = array();
        foreach ($settings as $setting => &$enabled) {
            $data[] = array('id' => $setting, 'enabled' => $enabled, 'exif' => self::exif_id2label($setting));
        }
        echo Renderer::getInstance()->render('admin_exif', array(
            'settings' => $data,
            'submit'   => self::SUBMIT,
            'reset'    => self::RESET,
        ));
    }

    /**
     * use native php exif_read_data to retrieve exif data instead of yapb lib phpExifRW and ExifUtils
     * this method still retrieves all selected exif filter in yapb to return the required exif info
     *
     * @return array of selected exif, with at least captureDate
     */
    public function get_image_exif($post_id, $filename)
    {
        $settings = $this->get_exif_settings();
        if (false === $settings) {
            return false;
        }
        $exifs = $this->read_exifs($post_id, $filename);
        $ret = array();

        foreach ($settings as $setting => $enabled) {
            if ( ! $enabled || ! isset( $exifs[ $setting ] ) || 'a' === $exifs[ $setting ] ) {
                continue;
            }

            switch ($setting) {
                case 'FNumber':
                case 'FocalLength':
                    $value = self::compute_math($exifs[ $setting ]);
                    break;
                case 'ExposureProgram':
                    $value = self::exposure_program($exifs[ $setting ]);
                    break;
                default:
                    $value = $exifs[ $setting ];
                    break;
            }
            if (false !== $value) {
                $ret[] = array('name' => self::exif_id2label($setting), 'value' => $value);
            }
        }
        return $ret;
    }

    public function get_capture_date($post_id, $filename)
    {
        $exifs = $this->read_exifs($post_id, $filename);
        if (isset($exifs['DateTime']) && 'a' !== $exifs['DateTime']) {
            return date_i18n(get_option('date_format'), strtotime($exifs['DateTime']));
        } else {
            return false;
        }
    }

    /**
     * first tries to retrieve metadata from curstom wordpress meta data, and if not, read them from the file and then update the db
     */
    private function read_exifs($post_id, $filename)
    {
        $exifs = array();
        $meta = get_post_meta($post_id, self::METADATA_NAME);
        if (empty($meta)) {
            $exifs = @exif_read_data($filename, 'EXIF');
            update_post_meta($post_id, self::METADATA_NAME, $exifs);
        } else {
            $exifs = current($meta);
        }

        return $exifs;
    }

    /**
     * compute mathematic string (such as the one contained in an exif field) without the use of eval
     */
    static private function compute_math($math_string)
    {
        $math_string = trim($math_string);                                   // trim white spaces
        $math_string = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $math_string); // remove any non-numbers chars; exception for math operators
        if (empty($math_string)) {
            return false;
        }

        $compute = create_function("", "return (" . $math_string . ");" );
        return 0 + $compute();
    }

    /**
     *
     */
    static private function exposure_program($ep)
    {
        $ep2str = array(
            0 => false,
            1 => 'Manual',
            2 => 'Normal program',
            3 => 'Aperture priority',
            4 => 'Shutter priority',
            5 => 'Creative program',
            6 => 'Action program',
            7 => 'Portrait mode',
            8 => 'Landscape mode',
        );
        if ( ! isset($ep2str[ $ep ]) ) {
            $ep = 0;
        }
        return $ep2str[ $ep ];
    }

}

?>