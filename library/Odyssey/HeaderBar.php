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
    const TEMPLATE_FILE = 'photoblog_header';

    const SYNDICATION_OPTION_NAME = 'odyssey_settings_headerbar_syndication';
    const SYNDICATION_SUBMIT_NAME = 'odyssey_submit_headerbar_syndication';
    const SYNDICATION_RESET_NAME  = 'odyssey_reset_headerbar_syndication';

    const OTHER_LINKS_OPTION_NAME = 'odyssey_settings_headerbar_other_links';
    const OTHER_LINKS_SUBMIT_NAME = 'odyssey_submit_headerbar_other_links';
    const OTHER_LINKS_RESET_NAME  = 'odyssey_reset_headerbar_other_links';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        Admin::get_instance()->register($this);
    }

    static public function setting_id2label($setting_id) {
        $id2label = array(
            'rss'         => 'RSS Feed',
            'com_rss'     => 'Comments RSS Feed',
            'atom'        => 'Atom Feed',
            'facebook'    => 'Facebook',
            'twitter'     => 'Twitter',
            'flickr'      => 'Flickr',
            'google_plus' => 'Google +',

            'random'      => 'Random post',
            'pages'       => 'Pages',
        );
        return $id2label[ $setting_id ];
    }
    static public function get_default_header_bar_syndication_settings() {
        $blog = Core::get_instance()->get_blog();
        
        return array(
            'rss'         => array('enabled' => true,  'value' => $blog['rss2_url']),
            'com_rss'     => array('enabled' => false, 'value' => $blog['comments_rss2_url']),
            'atom'        => array('enabled' => false, 'value' => $blog['atom_url']),
            'facebook'    => array('enabled' => false, 'value' => __('Facebook page url')),
            'twitter'     => array('enabled' => false, 'value' => __('Twitter page url')),
            'flickr'      => array('enabled' => false, 'value' => __('Flickr page url')),
            'google_plus' => array('enabled' => false, 'value' => __('Google+ page url')),
        );
    }

    public function get_header_bar_syndication_settings() {
        return get_option(self::SYNDICATION_OPTION_NAME, self::get_default_header_bar_syndication_settings());
    }

    static public function get_default_header_bar_other_links_settings() {
        return array(
            'random' => true,
            'pages'  => true,
        );
    }

    public function getHeaderBarOtherLinksSettings() {
        return get_option(self::OTHER_LINKS_OPTION_NAME, self::get_default_header_bar_other_links_settings());
    }

    function get_setting_page() {
        $data = array('configureSetting' => array());

        if (isset($_POST[self::SYNDICATION_RESET_NAME])) {
            delete_option(self::SYNDICATION_OPTION_NAME);
        }
        $settings = $this->get_header_bar_syndication_settings();
        if (isset($_POST[self::SYNDICATION_SUBMIT_NAME])) {
            unset($_POST[self::SYNDICATION_SUBMIT_NAME]);
            foreach ($settings as $setting => &$value) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$setting]) && !$value['enabled']) {
                    $value['enabled'] = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$setting]) && $value['enabled']) {
                    $value['enabled'] = false;
                }
                if (isset($_POST['tf-' . $setting])) {
                    $value['value'] = $_POST['tf-' . $setting];
                }
            }
            update_option(self::SYNDICATION_OPTION_NAME, $settings);
        }
        $data_settings = array();
        foreach ($settings as $setting => &$value) {
            $data_settings[] = array(
                'id'      => $setting,
                'label'   => self::setting_id2label($setting),
                'enabled' => $value['enabled'],
                'value'   => $value['value'],
            );
        }
        $data['section'][] = array(
            'name'        => __('Syndication links'),
            'description' => __('Links to the platforms on which this blog can be followed:'),
            'settings'    => $data_settings,
            'submit'      => self::SYNDICATION_SUBMIT_NAME,
            'reset'       => self::SYNDICATION_RESET_NAME,
        );

        if (isset($_POST[self::OTHER_LINKS_RESET_NAME])) {
            delete_option(self::OTHER_LINKS_OPTION_NAME);
        }
        $settings = $this->getHeaderBarOtherLinksSettings();
        if (isset($_POST[self::OTHER_LINKS_SUBMIT_NAME])) {
            unset($_POST[self::OTHER_LINKS_SUBMIT_NAME]);
            foreach ($settings as $setting => &$enabled) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$setting]) && !$enabled) {
                    $enabled = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$setting]) && $enabled) {
                    $enabled = false;
                }
            }
            update_option(self::OTHER_LINKS_OPTION_NAME, $settings);
        }
        $data_settings = array();
        foreach ($settings as $setting => &$enabled) {
            $data_settings[] = array(
                'id'      => $setting,
                'label'   => self::setting_id2label($setting),
                'enabled' => $enabled,
            );
        }
        $data['section'][] = array(
            'name'        => __('Other links'),
            'description' => __('Enable other links:'),
            'settings'    => $data_settings,
            'submit'      => self::OTHER_LINKS_SUBMIT_NAME,
            'reset'       => self::OTHER_LINKS_RESET_NAME,
        );
        return Renderer::get_instance()->render('admin_headerbar', $data);
    }

    function get_rendering() {
        $blog = Core::get_instance()->get_blog();
        
        $settings = $this->get_header_bar_syndication_settings();
        $syndication = array();
        foreach ($settings as $setting => &$value) {
            if ($value['enabled']) {
                $syndication[] = array(
                    'class' => self::setting_id2css_class($setting),
                    'title' => self::setting_id2label($setting),
                    'url'   => $value['value'],
                );
            }
        }
        $blog['syndication'] = $syndication;
        
        $settings = $this->getHeaderBarOtherLinksSettings();
        $others = array();
        foreach ($settings as $setting => $enabled) {
            if ( ! $enabled) {
                continue;
            }
            switch ($setting) {
                case 'random': // get a valid random post id
                    $others[] = Core::get_instance()->get_random_post_url();
                    break;
                case 'pages': // get the page list
                    $others = array_merge($others, Core::get_instance()->get_pages_url());
                    break;
                default:
                    break;
            }
        }
        $blog['others'] = $others;
        
        return Renderer::get_instance()->render(self::TEMPLATE_FILE, $blog);
    }

    static function setting_id2css_class($id) {
        $id2Class = array(
            'rss'         => 'menu_but_rss',
            'com_rss'     => 'menu_but_rss',
            'atom'        => 'menu_but_rss',
            'facebook'    => 'menu_but_fb',
            'twitter'     => 'menu_but_tw',
            'flickr'      => 'menu_but_fr',
            'google_plus' => 'menu_but_go',
        );
        
        return $id2Class[$id];
    }
    
}

?>