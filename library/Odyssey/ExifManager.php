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
    const OPTION_NAME = 'odyssey_settings_exif';
    const SUBMIT_NAME = 'odyssey_submit_exif';

    private $exifCache;

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
        return 'Exif sttings';
    }
    
    public function getMenuTitle()
    {
        return $this->getPageTitle();
    }
    
    public function getMenuSlug()
    {
        return 'odyssey-settings-exifs';
    }

    static public function getDefaultExifSettings()
    {
        return array(
            array('enabled' => false, 'id' => 'Make',             'exif' => 'Manufacturer'),
            array('enabled' => true,  'id' => 'Model',            'exif' => 'Model Name'),
            array('enabled' => true,  'id' => 'DateTimeOriginal', 'exif' => 'Date'),
            array('enabled' => true,  'id' => 'ExposureProgram',  'exif' => 'Exposure Program'),
            array('enabled' => true,  'id' => 'ExposureTime',     'exif' => 'Exposure Time'),
            array('enabled' => true,  'id' => 'FNumber',          'exif' => 'F Number'),
            array('enabled' => true,  'id' => 'ISOSpeedRatings',  'exif' => 'ISO'),
            array('enabled' => true,  'id' => 'FocalLength',      'exif' => 'Focal Length'),
            array('enabled' => false, 'id' => 'MeteringMode',     'exif' => 'Metering Mode'),
            array('enabled' => true,  'id' => 'LightSource',      'exif' => 'Light Source'),
            array('enabled' => true,  'id' => 'SensingMethod',    'exif' => 'Sensing Method'),
            array('enabled' => false, 'id' => 'ExposureMode',     'exif' => 'Exposure Mode'),

            array('enabled' => false, 'id' => 'FileName',         'exif' => 'File Name'),
            array('enabled' => false, 'id' => 'FileSize',         'exif' => 'File Size'),
            array('enabled' => false, 'id' => 'Software',         'exif' => 'Software'),
            array('enabled' => false, 'id' => 'XResolution',      'exif' => 'X Resolution'),
            array('enabled' => false, 'id' => 'YResolution',      'exif' => 'Y Resolution'),
            array('enabled' => false, 'id' => 'ExifVersion',      'exif' => 'Exif Version'),

            array('enabled' => false, 'id' => 'Title',            'exif' => 'Title'),
        );
    }

    public function getExifSettings()
    {
        return get_option(self::OPTION_NAME, self::getDefaultExifSettings());
    }

    function getSettingPage()
    {
        $settings = $this->getExifSettings();
        if (isset($_POST[self::SUBMIT_NAME])) {
            unset($_POST[self::SUBMIT_NAME]);
            $doUpdate = false;
            foreach ($settings as &$setting) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$setting['id']]) && !$setting['enabled']) {
                    $setting['enabled'] = true;
                    $doUpdate = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$setting['id']]) && $setting['enabled']) {
                    $setting['enabled'] = false;
                    $doUpdate = true;
                }
            }
            $doUpdate && update_option(self::OPTION_NAME, $settings);
        }

        echo Renderer::getInstance()->render('admin_exif', array(
            'settings'   => array_values($settings),
            'submitName' => self::SUBMIT_NAME,
        ));
    }

    /**
     * use native php exif_read_data to retrieve exif data instead of yapb lib phpExifRW and ExifUtils
     * this method still retrieves all selected exif filter in yapb to return the required exif info
     *
     * @return array of selected exif, with at least captureDate
     */
    public function getImageExif($filename)
    {
        $settings = $this->getExifSettings();
        $exifs = $this->readExif($filename);
        $ret = array();

        foreach($settings as $setting) {
            $exifTagname = $setting['id'];
            if (!$setting['enabled'] || isset($exifs[$exifTagname]) === false) {
                continue;
            }

            switch ($exifTagname) {
                case 'FNumber':
                case 'FocalLength':
                    $tagvalue = self::computeMath($exifs[$exifTagname]);
                    break;
                case 'ExposureProgram':
                    $tagvalue = self::exposureProgram($exifs[$exifTagname]);
                    break;
                default:
                    $tagvalue = $exifs[$exifTagname];
                    break;
            }
            $ret[] = array('name' => __($setting['exif'].': '), 'value' => $tagvalue);
        }
        return $ret;
    }

    public function getCaptureDate($filename)
    {
        $exifs = $this->readExif($filename);
        if (isset($exifs['DateTime'])) {
            return date_i18n(get_option('date_format'), strtotime($exifs['DateTime']));
        } else {
            return false;
        }
    }

    private function readExif($filename)
    {
        if (!isset($this->exifCache[$filename])) {
            $this->exifCache[$filename] = @exif_read_data($filename, 'EXIF');
        }

        return $this->exifCache[$filename];
    }

    /**
     * compute mathematic string (such as the one contained in an exif field) without the use of eval
     */
    static private function computeMath($mathString)
    {
        $mathString = trim($mathString);                                   // trim white spaces
        $mathString = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $mathString); // remove any non-numbers chars; exception for math operators

        $compute = create_function("", "return (" . $mathString . ");" );
        return 0 + $compute();
    }

    /**
     *
     */
    static private function exposureProgram($ep)
    {
        $ep2str = array(
            0 => 'Not defined',
            1 => 'Manual',
            2 => 'Normal program',
            3 => 'Aperture priority',
            4 => 'Shutter priority',
            5 => 'Creative program',
            6 => 'Action program',
            7 => 'Portrait mode',
            8 => 'Landscape mode',
        );
        if (!isset($ep2str[$ep])) {
            $ep = 0;
        }
        return $ep2str[$ep];
    }

}

?>