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

    const OPTION_NAME = 'odyssey_options_exif';
    const SUBMIT      = 'odyssey_submit_exif';
    const RESET       = 'odyssey_reset_exif';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        Admin::get_instance()->register($this);
        add_action('admin_init', array(&$this, 'admin_init'));
    }

    public function admin_init() {
        register_setting(Admin::OPTION_GROUP, self::OPTION_NAME);
        add_settings_section(
            self::OPTION_NAME,                // section id
            __('Exif Management', 'odyssey'), // section title
            array(&$this, 'section_text'),    // callback to the function displaying the output of the section
            'odyssey_theme_options'           // menu page (slug of the theme setting page)
        );
        foreach($this->get_option() as $key => $value) {
            add_settings_field(
                $key,
                self::option_id2label($key),
                array(&$this, 'option_field'),
                'odyssey_theme_options',       // menu page (slug of the theme setting page)
                self::OPTION_NAME,             // the option name it is recoreded into
                array('label_for' => $key, 'value' => $value)
            );
        }
        
    }

    public function section_text() {
        echo '<p>Please select the exif field to display</p>' . PHP_EOL;
    }
    function option_field($args) {
        $options = get_option(self::OPTION_NAME);
        echo '<input id="plugin_text_string" ' .
            'name="odyssey_options_exif[' . $options['text_string'] . ']" ' .
            'type="checkbox"' .
            ($args['value'] ? 'checked="checked"' : '') .
            ' />';
    }
    static public function option_id2label($option_id) {
        $id2label = array(
            'Make'             => __('Manufacturer: ', 'odyssey' ),
            'Model'            => __('Model Name: ', 'odyssey' ),
            'DateTimeOriginal' => __('Date: ', 'odyssey' ),
            'ExposureProgram'  => __('Exposure Program: ', 'odyssey' ),
            'ExposureTime'     => __('Exposure Time: ', 'odyssey' ),
            'FNumber'          => __('F Number: ', 'odyssey' ),
            'ISOSpeedRatings'  => __('ISO: ', 'odyssey' ),
            'FocalLength'      => __('Focal Length: ', 'odyssey' ),
            'MeteringMode'     => __('Metering Mode: ', 'odyssey' ),
            'LightSource'      => __('Light Source: ', 'odyssey' ),
            'SensingMethod'    => __('Sensing Method: ', 'odyssey' ),
            'ExposureMode'     => __('Exposure Mode: ', 'odyssey' ),

            'FileName'         => __('File Name: ', 'odyssey' ),
            'FileSize'         => __('File Size: ', 'odyssey' ),
            'Software'         => __('Software: ', 'odyssey' ),
            'XResolution'      => __('X Resolution: ', 'odyssey' ),
            'YResolution'      => __('Y Resolution: ', 'odyssey' ),
            'ExifVersion'      => __('Exif Version: ', 'odyssey' ),

            'Title'            => __('Title: ', 'odyssey' ),
        );
        return $id2label[ $option_id ];
    }

    static public function get_default_options() {
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

    public function get_option() {
        return get_option(self::OPTION_NAME, self::get_default_options());
    }

    function get_option_page() {
        echo '<div>' . PHP_EOL;
        echo '  <h2>Exif Management</h2>' . PHP_EOL;
        echo '  <form action="options.php" method="post">' . PHP_EOL;
        settings_fields(self::OPTION_NAME);
        do_settings_sections('odyssey_theme_options');

        echo '<input name="Submit" type="submit" value="' . esc_attr_e('Save Changes') . '" />' . PHP_EOL;
        echo '  </form>' . PHP_EOL;
        echo '</div>' . PHP_EOL;

//         if ( isset( $_POST[ self::RESET ] ) ) {
//             delete_option(self::OPTION_NAME);
//         }
//         $options = $this->get_options();
//         if (isset($_POST[self::SUBMIT])) {
//             $doUpdate = false;
//             foreach ($options as $option => &$enabled) {
//                 // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
//                 if (isset($_POST[ $option ] ) && ! $enabled) {
//                     $enabled = true;
//                     $doUpdate = true;
//                 // it's unenabled now (as it is not part of the POST), but was enabled before -> update
//                 } else if (!isset($_POST[ $option ] ) && $enabled) {
//                     $enabled = false;
//                     $doUpdate = true;
//                 }
//             }
//             $doUpdate && update_option(self::OPTION_NAME, $options);
//         }
// 
//         $data = array();
//         foreach ($options as $option => &$enabled) {
//             $data[] = array('id' => $option, 'enabled' => $enabled, 'exif' => self::option_id2label($option));
//         }
//         return Renderer::get_instance()->render('admin_exif', array(
//             'options' => $data,
//             'submit'   => self::SUBMIT,
//             'reset'    => self::RESET,
//         ));
    }

    /**
     * use native php exif_read_data to retrieve exif data instead of yapb lib phpExifRW and ExifUtils
     * this method still retrieves all selected exif filter in yapb to return the required exif info
     *
     * @return array of selected exif, with at least captureDate
     */
    public function get_image_exif($post_id, $filename) {
        $options = $this->get_options();
        if (false === $options) {
            return false;
        }
        $exifs = $this->read_exifs($post_id, $filename);
        $ret = array();

        foreach ($options as $option => $enabled) {
            if ( ! $enabled || ! isset( $exifs[ $option ] ) || 'a' === $exifs[ $option ] ) {
                continue;
            }

            switch ($option) {
                case 'FNumber':
                case 'FocalLength':
                    $value = self::compute_math($exifs[ $option ]);
                    break;
                case 'ExposureProgram':
                    $value = self::exposure_program($exifs[ $option ]);
                    break;
                default:
                    $value = $exifs[ $option ];
                    break;
            }
            if (false !== $value) {
                $ret[self::option_id2label($option)] = $value;
            }
        }
        return $ret;
    }

    public function get_capture_date($post_id, $filename) {
        $exifs = $this->read_exifs($post_id, $filename);
        if (isset($exifs['DateTimeOriginal']) && 'a' !== $exifs['DateTimeOriginal']) {
            return date_i18n(get_option('date_format'), strtotime($exifs['DateTimeOriginal']));
        } else {
            return false;
        }
    }

    /**
     * first tries to retrieve metadata from curstom wordpress meta data, and if not, read them from the file and then update the db
     */
    private function read_exifs($post_id, $filename) {
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
    static private function compute_math($math_string) {
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
    static private function exposure_program($ep) {
        $ep2str = array(
            0 => false,
            1 => __( 'Manual',            'odyssey' ),
            2 => __( 'Normal program',    'odyssey' ),
            3 => __( 'Aperture priority', 'odyssey' ),
            4 => __( 'Shutter priority',  'odyssey' ),
            5 => __( 'Creative program',  'odyssey' ),
            6 => __( 'Action program',    'odyssey' ),
            7 => __( 'Portrait mode',     'odyssey' ),
            8 => __( 'Landscape mode',    'odyssey' ),
        );
        if ( ! isset($ep2str[ $ep ]) ) {
            $ep = 0;
        }
        return $ep2str[ $ep ];
    }

}

?>