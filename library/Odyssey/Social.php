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
 *  Social options functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage Social
 */
class Social
{
    const OPTION_NAME = 'odyssey_settings_social';
    const SUBMIT_NAME = 'odyssey_submit_social';

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
    }

    public function getPageTitle()
    {
        return 'Social settings';
    }
    
    public function getMenuTitle()
    {
        return $this->getPageTitle();
    }
    
    public function getMenuSlug()
    {
        return 'odyssey-settings-social';
    }
    static public function getDefaultSocialSettings()
    {
        return array(
            array('id' => 'Twitter',    'label' => 'Twitter',   'enabled' => false),
            array('id' => 'Facebook',   'label' => 'Facebook',  'enabled' => true),
            array('id' => 'Flickr',     'label' => 'Flickr',    'enabled' => true),
            array('id' => 'GooglePlus', 'label' => 'Google +',  'enabled' => false),
        );
    }

    public function getSocialSettings()
    {
        return get_option(self::OPTION_NAME, self::getDefaultSocialSettings());
    }

    function getSettingPage()
    {
        $settings = $this->getSocialSettings();
//         if (isset($_POST[self::SUBMIT_NAME])) {
//             unset($_POST[self::SUBMIT_NAME]);
//             $doUpdate = false;
//             foreach ($exifSettings as &$exifSetting) {
//                 // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
//                 if (isset($_POST[$exifSetting['id']]) && !$exifSetting['enabled']) {
//                     $exifSetting['enabled'] = true;
//                     $doUpdate = true;
//                 // it's unenabled now (as it is not part of the POST), but was enabled before -> update
//                 } else if (!isset($_POST[$exifSetting['id']]) && $exifSetting['enabled']) {
//                     $exifSetting['enabled'] = false;
//                     $doUpdate = true;
//                 }
//             }
//             $doUpdate && update_option(self::OPTION_NAME, $exifSettings);
//         }
// 
        echo Renderer::getInstance()->render('admin_social', array(
            'settings'   => $settings,
            'submitName' => self::SUBMIT_NAME,
        ));
    }

}

?>