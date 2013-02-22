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
 *  HeaderBar options functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage HeaderBar
 */
class HeaderBar
{
    const SOCIAL_OPTION_NAME = 'odyssey_settings_headerbar_social';
    const SOCIAL_SUBMIT_NAME = 'odyssey_submit_headerbar_social';

    const OTHER_LINKS_OPTION_NAME = 'odyssey_settings_headerbar_other_links';
    const OTHER_LINKS_SUBMIT_NAME = 'odyssey_submit_headerbar_other_links';

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
        return 'HeaderBar settings';
    }
    
    public function getMenuTitle()
    {
        return $this->getPageTitle();
    }
    
    public function getMenuSlug()
    {
        return 'odyssey-settings-headerbar';
    }

    static public function getDefaultHeaderBarSocialSettings()
    {
        return array(
            array('enabled' => false, 'id' => 'Twitter',    'label' => 'Twitter',  'value' => __('Twitter page url')),
            array('enabled' => false, 'id' => 'Facebook',   'label' => 'Facebook', 'value' => __('Facebook page url')),
            array('enabled' => false, 'id' => 'Flickr',     'label' => 'Flickr',   'value' => __('Flickr page url')),
            array('enabled' => false, 'id' => 'GooglePlus', 'label' => 'Google +', 'value' => __('Google+ page url')),
        );
    }

    public function getHeaderBarSocialSettings()
    {
        return get_option(self::SOCIAL_OPTION_NAME, self::getDefaultHeaderBarSocialSettings());
    }

    static public function getDefaultHeaderBarOtherLinksSettings()
    {
        return array(
            array('enabled' => true,  'id' => 'RSS',    'label' => 'RSS post link'),
            array('enabled' => true,  'id' => 'random', 'label' => 'Random post'),
            array('enabled' => true,  'id' => 'pages',  'label' => 'Pages'),
        );
    }

    public function getHeaderBarOtherLinksSettings()
    {
        return get_option(self::OTHER_LINKS_OPTION_NAME, self::getDefaultHeaderBarOtherLinksSettings());
    }



    function getSettingPage()
    {
        $data = array('configureSetting' => array());

        $settings = $this->getHeaderBarSocialSettings();
        if (isset($_POST[self::SOCIAL_SUBMIT_NAME])) {
            unset($_POST[self::SOCIAL_SUBMIT_NAME]);
            foreach ($settings as &$setting) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$setting['id']]) && !$setting['enabled']) {
                    $setting['enabled'] = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$setting['id']]) && $setting['enabled']) {
                    $setting['enabled'] = false;
                }
                $setting['value'] = $_POST['tf-' . $setting['id']];
            }
            update_option(self::SOCIAL_OPTION_NAME, $settings);
        }
        $data['section'][] = array(
            'name'        => 'Social links',
            'description' => 'Enable social links:',
            'settings'    => $settings,
            'submitName'  => self::SOCIAL_SUBMIT_NAME,
        );

        $settings = $this->getHeaderBarOtherLinksSettings();
        if (isset($_POST[self::OTHER_LINKS_SUBMIT_NAME])) {
            unset($_POST[self::OTHER_LINKS_SUBMIT_NAME]);
            foreach ($settings as &$setting) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$setting['id']]) && !$setting['enabled']) {
                    $setting['enabled'] = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$setting['id']]) && $setting['enabled']) {
                    $setting['enabled'] = false;
                }
            }
            update_option(self::OTHER_LINKS_OPTION_NAME, $settings);
        }
        $data['section'][] = array(
            'name'        => 'Other links',
            'description' => 'Enable optionnal links:',
            'settings'    => $settings,
            'submitName'  => self::OTHER_LINKS_SUBMIT_NAME,
        );

        echo Renderer::getInstance()->render('admin_headerbar', $data);
    }

}

?>