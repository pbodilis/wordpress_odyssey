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
	const OPTION_NAME = 'odyssey_settings_exifs';
	const SUBMIT_NAME = 'odyssey_exif_submit';
    static private $instance;

    public function __construct()
    {
        Admin::getInstance()->register($this);
    }

    static public function getInstance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
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
			array('id' => 'Make',             'exif' => 'Manufacturer',     'checked' => false),
			array('id' => 'Model',            'exif' => 'Model Name',       'checked' => true),
			array('id' => 'DateTimeOriginal', 'exif' => 'Date',             'checked' => true),
			array('id' => 'ExposureProgram',  'exif' => 'Exposure Program', 'checked' => true),
			array('id' => 'ExposureTime',     'exif' => 'Exposure Time',    'checked' => true),
			array('id' => 'FNumber',          'exif' => 'F Number',         'checked' => true),
			array('id' => 'ISOSpeedRatings',  'exif' => 'ISO',              'checked' => true),
			array('id' => 'FocalLength',      'exif' => 'Focal Length',     'checked' => true),
			array('id' => 'MeteringMode',     'exif' => 'Metering Mode',    'checked' => false),
			array('id' => 'LightSource',      'exif' => 'Light Source',     'checked' => true),
			array('id' => 'SensingMethod',    'exif' => 'Sensing Method',   'checked' => true),
			array('id' => 'ExposureMode',     'exif' => 'Exposure Mode',    'checked' => false),
			
			array('id' => 'FileName',         'exif' => 'File Name',        'checked' => false),
			array('id' => 'FileSize',         'exif' => 'File Size',        'checked' => false),
			array('id' => 'Software',         'exif' => 'Software',         'checked' => false),
			array('id' => 'XResolution',      'exif' => 'X Resolution',     'checked' => false),
			array('id' => 'YResolution',      'exif' => 'Y Resolution',     'checked' => false),
			array('id' => 'ExifVersion',      'exif' => 'Exif Version',     'checked' => false),

			array('id' => 'Title',            'exif' => 'Title',            'checked' => false),
		);
    }

	public function getExifSettings()
	{
		return get_option(self::OPTION_NAME, self::getDefaultExifSettings());
    }
    
    function getSettingPage()
    {
    	$exifSettings = $this->getExifSettings();
        if (isset($_POST[self::SUBMIT_NAME])) {
        	unset($_POST[self::SUBMIT_NAME]);
			$doUpdate = false;
    		foreach ($exifSettings as &$exifSetting) {
    			// it's checked now (as it is part of the POST), but wasn't checked before -> update
    			if (isset($_POST[$exifSetting['id']]) && !$exifSetting['checked']) {
    				$exifSetting['checked'] = true;
    				$doUpdate = true;
    			// it's unchecked now (as it is not part of the POST), but was checked before -> update
				} else if (!isset($_POST[$exifSetting['id']]) && $exifSetting['checked']) {
    				$exifSetting['checked'] = false;
    				$doUpdate = true;
    			}
			}
            $doUpdate && update_option(self::OPTION_NAME, $exifSettings);
        }
		
        echo Renderer::getInstance()->render('admin_exif', array(
        	'exifSettings' => array_values($exifSettings),
        	'submitName'   => self::SUBMIT_NAME,
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
        $exifSettings = $this->getExifSettings();
        $exifs = @exif_read_data($filename, 'EXIF' );
        $ret = array();

        foreach($exifSettings as $exifSetting) {
        	if (isset($exifs[$exifSetting['id']]) === false) {
                continue;
        	}
			
			$exifTagname = $exifSetting['id'];
            switch ($exifTagname) {
                case 'FNumber':
                case 'FocalLength':
                    $tagvalue = self::computeMath($exifs[$exifTagname]);
                    break;
                case 'DateTime':
                case 'DateTimeOriginal':
                    $tagvalue = date_i18n(get_option('date_format'), strtotime($exifs[$exifTagname]));
		            $ret['captureDate'] = $tagvalue;
                    break;
                default:
                    $tagvalue = $exifs[$exifTagname];
                    break;
            }
            $ret[] = array('name' => __($exifSetting['exif']), 'value' => $tagvalue);
        }
        return $ret;
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

}

?>