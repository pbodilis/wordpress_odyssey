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

    const SYNDICATION_OPTION_NAME = 'odyssey_settings_headerbar_Syndication';
    const SYNDICATION_SUBMIT_NAME = 'odyssey_submit_headerbar_Syndication';

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

    static public function getDefaultHeaderBarSyndicationSettings()
    {
        $blog = Core::getInstance()->getBlog();
        
        return array(
            array('enabled' => true,  'id' => 'RSS',        'label' => 'RSS Feed',          'value' => $blog['rss2_url']),
            array('enabled' => false, 'id' => 'ComRSS',     'label' => 'Comments RSS Feed', 'value' => $blog['comments_rss2_url']),
            array('enabled' => false, 'id' => 'Atom',       'label' => 'Atom Feed',         'value' => $blog['atom_url']),
            array('enabled' => false, 'id' => 'Facebook',   'label' => 'Facebook',          'value' => __('Facebook page url')),
            array('enabled' => false, 'id' => 'Twitter',    'label' => 'Twitter',           'value' => __('Twitter page url')),
            array('enabled' => false, 'id' => 'Flickr',     'label' => 'Flickr',            'value' => __('Flickr page url')),
            array('enabled' => false, 'id' => 'GooglePlus', 'label' => 'Google +',          'value' => __('Google+ page url')),
        );
    }

    public function getHeaderBarSyndicationSettings()
    {
        return get_option(self::SYNDICATION_OPTION_NAME, self::getDefaultHeaderBarSyndicationSettings());
    }

    static public function getDefaultHeaderBarOtherLinksSettings()
    {
        return array(
            array('enabled' => true,  'id' => 'random', 'label' => 'Random post'),
            array('enabled' => true,  'id' => 'pages',  'label' => 'Pages'),
        );
    }

    public function getHeaderBarOtherLinksSettings()
    {
        return get_option(self::OTHER_LINKS_OPTION_NAME, self::getDefaultHeaderBarOtherLinksSettings());
    }

    function get_setting_page()
    {
        $data = array('configureSetting' => array());

        $settings = $this->getHeaderBarSyndicationSettings();
        if (isset($_POST[self::SYNDICATION_SUBMIT_NAME])) {
            unset($_POST[self::SYNDICATION_SUBMIT_NAME]);
            foreach ($settings as &$setting) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$setting['id']]) && !$setting['enabled']) {
                    $setting['enabled'] = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$setting['id']]) && $setting['enabled']) {
                    $setting['enabled'] = false;
                }
                if (isset($_POST['tf-' . $setting['id']])) {
                    $setting['value'] = $_POST['tf-' . $setting['id']];
                }
            }
            update_option(self::SYNDICATION_OPTION_NAME, $settings);
        }
        $data['section'][] = array(
            'name'        => __('Syndication links'),
            'description' => __('Links to the platforms on which this blog can be followed:'),
            'settings'    => $settings,
            'submitName'  => self::SYNDICATION_SUBMIT_NAME,
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
            'name'        => __('Other links'),
            'description' => __('Enable other links:'),
            'settings'    => $settings,
            'submitName'  => self::OTHER_LINKS_SUBMIT_NAME,
        );

        echo Renderer::getInstance()->render('admin_headerbar', $data);
    }

    function getRendering()
    {
        $blog = Core::getInstance()->getBlog();
        
        $settings = $this->getHeaderBarSyndicationSettings();
        $syndication = array();
        foreach ($settings as &$setting) {
            if ($setting['enabled']) {
                $syndication[] = array(
                    'class' => self::id2CssClass($setting['id']),
                    'title' => $setting['label'],
                    'url'   => $setting['value'],
                );
            }
        }
        $blog['syndication'] = $syndication;
        
        $settings = $this->getHeaderBarOtherLinksSettings();
        $others = array();
        foreach ($settings as &$setting) {
            if ($setting['enabled']) {
                switch ($setting['id']) {
                    case 'random': // get a valid random post id
                        $randomUrl = Core::getInstance()->get_random_post_url();
                        if ($randomUrl !== false) {
                            $others[] = array(
                                'url'   => $randomUrl,
                                'title' => __('Random post'),
                                'name'  => __('Random'),
                            );
                        }
                        break;
                    case 'pages': // get the page list
                        $pages = Core::getInstance()->getPages();
                        foreach ($pages as $page) {
                            $others[] = array(
                                'url'   => $page->guid,
                                'title' => $page->post_excerpt,
                                'name'  => $page->post_title,
                            );
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        $blog['others'] = $others;
        
        return Renderer::getInstance()->render(self::TEMPLATE_FILE, $blog);
    }
    
    static function id2CssClass($id) {
        $id2Class = array(
            'RSS'        => 'menu_but_rss',
            'ComRSS'     => 'menu_but_rss',
            'Atom'       => 'menu_but_rss',
            'Facebook'   => 'menu_but_fb',
            'Twitter'    => 'menu_but_tw',
            'Flickr'     => 'menu_but_fr',
            'GooglePlus' => 'menu_but_go',
        );
        
        return $id2Class[$id];
    }
    
}

?>