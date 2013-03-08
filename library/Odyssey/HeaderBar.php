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

    const SYNDICATION_OPTION_NAME = 'odyssey_options_headerbar_syndication';
    const SYNDICATION_SUBMIT_NAME = 'odyssey_submit_headerbar_syndication';
    const SYNDICATION_RESET_NAME  = 'odyssey_reset_headerbar_syndication';

    const OTHER_LINKS_OPTION_NAME = 'odyssey_options_headerbar_other_links';
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

    static public function option_id2label($option_id) {
        $id2label = array(
            'rss'         => __( 'RSS Feed', 'odyssey' ),
            'com_rss'     => __( 'Comments RSS Feed', 'odyssey' ),
            'atom'        => __( 'Atom Feed', 'odyssey' ),
            'facebook'    => __( 'Facebook', 'odyssey' ),
            'twitter'     => __( 'Twitter', 'odyssey' ),
            'flickr'      => __( 'Flickr', 'odyssey' ),
            'google_plus' => __( 'Google +', 'odyssey' ),

            'random'      => __( 'Random post', 'odyssey' ),
            'pages'       => __( 'Pages', 'odyssey' ),
        );
        return $id2label[ $option_id ];
    }
    static public function get_default_header_bar_syndication_options() {
        $blog = Core::get_instance()->get_blog();
        
        return array(
            'rss'         => array('enabled' => true,  'value' => $blog['rss2_url']),
            'com_rss'     => array('enabled' => false, 'value' => $blog['comments_rss2_url']),
            'atom'        => array('enabled' => false, 'value' => $blog['atom_url']),
            'facebook'    => array('enabled' => false, 'value' => __('Facebook page url', 'odyssey' )),
            'twitter'     => array('enabled' => false, 'value' => __('Twitter page url', 'odyssey' )),
            'flickr'      => array('enabled' => false, 'value' => __('Flickr page url', 'odyssey' )),
            'google_plus' => array('enabled' => false, 'value' => __('Google+ page url', 'odyssey' )),
        );
    }

    public function get_header_bar_syndication_options() {
        return get_option(self::SYNDICATION_OPTION_NAME, self::get_default_header_bar_syndication_options());
    }

    static public function get_default_header_bar_other_links_options() {
        return array(
            'random' => true,
            'pages'  => true,
        );
    }

    public function getHeaderBarOtherLinksSettings() {
        return get_option(self::OTHER_LINKS_OPTION_NAME, self::get_default_header_bar_other_links_options());
    }

    function get_option_page() {
        $data = array('configureSetting' => array());

        if (isset($_POST[self::SYNDICATION_RESET_NAME])) {
            delete_option(self::SYNDICATION_OPTION_NAME);
        }
        $options = $this->get_header_bar_syndication_options();
        if (isset($_POST[self::SYNDICATION_SUBMIT_NAME])) {
            unset($_POST[self::SYNDICATION_SUBMIT_NAME]);
            foreach ($options as $option => &$value) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$option]) && !$value['enabled']) {
                    $value['enabled'] = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$option]) && $value['enabled']) {
                    $value['enabled'] = false;
                }
                if (isset($_POST['tf-' . $option])) {
                    $value['value'] = $_POST['tf-' . $option];
                }
            }
            update_option(self::SYNDICATION_OPTION_NAME, $options);
        }
        $data_options = array();
        foreach ($options as $option => &$value) {
            $data_options[] = array(
                'id'      => $option,
                'label'   => self::option_id2label($option),
                'enabled' => $value['enabled'],
                'value'   => $value['value'],
            );
        }
        $data['section'][] = array(
            'name'        => __('Syndication links', 'odyssey' ),
            'description' => __('Links to the platforms on which this blog can be followed:', 'odyssey' ),
            'options'    => $data_options,
            'submit'      => self::SYNDICATION_SUBMIT_NAME,
            'reset'       => self::SYNDICATION_RESET_NAME,
        );

        if (isset($_POST[self::OTHER_LINKS_RESET_NAME])) {
            delete_option(self::OTHER_LINKS_OPTION_NAME);
        }
        $options = $this->getHeaderBarOtherLinksSettings();
        if (isset($_POST[self::OTHER_LINKS_SUBMIT_NAME])) {
            unset($_POST[self::OTHER_LINKS_SUBMIT_NAME]);
            foreach ($options as $option => &$enabled) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[$option]) && !$enabled) {
                    $enabled = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[$option]) && $enabled) {
                    $enabled = false;
                }
            }
            update_option(self::OTHER_LINKS_OPTION_NAME, $options);
        }
        $data_options = array();
        foreach ($options as $option => &$enabled) {
            $data_options[] = array(
                'id'      => $option,
                'label'   => self::option_id2label($option),
                'enabled' => $enabled,
            );
        }
        $data['section'][] = array(
            'name'        => __('Other links', 'odyssey' ),
            'description' => __('Enable other links:', 'odyssey' ),
            'options'    => $data_options,
            'submit'      => self::OTHER_LINKS_SUBMIT_NAME,
            'reset'       => self::OTHER_LINKS_RESET_NAME,
        );
        return Renderer::get_instance()->render('admin_headerbar', $data);
    }

    function get_rendering() {
        $blog = Core::get_instance()->get_blog();
        
        $options = $this->get_header_bar_syndication_options();
        $syndication = array();
        foreach ($options as $option => &$value) {
            if ($value['enabled']) {
                $syndication[] = array(
                    'class' => self::option_id2css_class($option),
                    'title' => self::option_id2label($option),
                    'url'   => $value['value'],
                );
            }
        }
        $blog['syndication'] = $syndication;
        
        $options = $this->getHeaderBarOtherLinksSettings();
        $others = array();
        foreach ($options as $option => $enabled) {
            if ( ! $enabled) {
                continue;
            }
            switch ($option) {
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

    static function option_id2css_class($id) {
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