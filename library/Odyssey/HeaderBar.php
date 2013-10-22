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
//     const TEMPLATE_FILE = 'photoblog_header';

    const OPTION_NAME = 'odyssey_option_header';

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
        add_action('admin_init', array(&$this, 'admin_init'));
    }

    public function admin_init() {
        register_setting(Admin::OPTION_GROUP, self::OPTION_NAME);
        add_settings_section(
            self::OPTION_NAME,                // section id
            __('Exif Management', 'odyssey'), // section title
            array(&$this, 'section_text_syndication'),    // callback to the function displaying the output of the section
            Admin::OPTION_PAGE           // menu page (slug of the theme setting page)
        );
        foreach($this->get_option_syndication() as $key => $value) {
            add_settings_field(
                $key,
                self::option_id2label($key),
                array(&$this, 'option_field_syndication'),
                Admin::OPTION_PAGE,       // menu page (slug of the theme setting page)
                self::OPTION_NAME,             // the option name it is recoreded into
                array('label_for' => $key, 'value' => $value)
            );
        }
    }
    public function section_text_syndication() {
        echo '<p>Please select the syndication to display in header bar</p>' . PHP_EOL;
    }
    public function get_option_syndication() {
        $default = self::get_default_option();
        $option = get_option(self::OPTION_NAME, self::get_default_option());
        foreach($default as $key => $value) {
            if (array_key_exists($key, $option) && $option[$key] === true) {
                $default[$key]['enabled'] = true;
            } else {
                $default[$key]['enabled'] = false;
            }
        }

        return array_merge($default, $option);
    }

    function option_field_syndication($args) {
        echo '<input id="' . $args['label_for'] . '" ' .
            'name="' . self::OPTION_NAME . '[' . $args['label_for'] . ']" ' .
            'type="checkbox"' .
            ($args['value']['enabled'] ? 'checked="checked"' : '') .
            ' />';
        echo '<input name="tf-' . $args['label_for'] . '" type="text" size="60" value="' . $args['value']['value'] . '" class="regular-text" />' . PHP_EOL;

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
            'archives'    => __( 'Archives page', 'odyssey' ),
            'pages'       => __( 'Pages', 'odyssey' ),
        );
        return $id2label[ $option_id ];
    }

    static public function get_default_option() {
        $blog = Core::get_instance()->get_blog();
        
        return array(
            'rss'         => array('enabled' => true,  'value' => $blog->rss2_url),
            'com_rss'     => array('enabled' => false, 'value' => $blog->comments_rss2_url),
            'atom'        => array('enabled' => false, 'value' => $blog->atom_url),
            'facebook'    => array('enabled' => false, 'value' => __('Facebook page url', 'odyssey' )),
            'twitter'     => array('enabled' => false, 'value' => __('Twitter page url', 'odyssey' )),
            'flickr'      => array('enabled' => false, 'value' => __('Flickr page url', 'odyssey' )),
            'google_plus' => array('enabled' => false, 'value' => __('Google+ page url', 'odyssey' )),
        );
    }

//     public function get_header_bar_syndication_options() {
//         return get_option(self::SYNDICATION_OPTION_NAME, self::get_default_header_bar_syndication_options());
//     }
// 
    static public function get_default_header_bar_other_links_options() {
        return array(
            'random'   => true,
            'archives' => true,
            'pages'    => true,
        );
    }

    public function get_header_bar_other_links_options() {
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
        $options = $this->get_header_bar_other_links_options();
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
            'options'     => $data_options,
            'submit'      => self::OTHER_LINKS_SUBMIT_NAME,
            'reset'       => self::OTHER_LINKS_RESET_NAME,
        );
        return Renderer::get_instance()->render('admin_headerbar', $data);
    }

    function get_syndication() {
        $options = $this->get_header_bar_syndication_options();
        $syndication = array();
        foreach ($options as $option => &$value) {
            if ($value['enabled']) {
                $s = new \stdClass();
                $s->class = self::option_id2css_class($option);
                $s->title = self::option_id2label($option);
                $s->url   = $value['value'];
                $syndication[] = $s;
            }
        }
        return $syndication;
    }

    function get_other_links() {
        $options = $this->get_header_bar_other_links_options();
        $others = array();
        foreach ($options as $option => $enabled) {
            if ( ! $enabled) {
                continue;
            }
            switch ($option) {
                case 'random': // get a valid random post id
                    $others[] = Core::get_instance()->get_random_post_url();
                    break;
                case 'archives': // get a link to the latest archive
                    $others[] = Core::get_instance()->get_archives_url();
                    break;
                default:
                    break;
            }
        }
        return $others;
    }
//         $blog['others'] = $others;
// // echo '<pre>' . PHP_EOL;
// // var_dump($blog['others']);
//         
//         return Renderer::get_instance()->render(self::TEMPLATE_FILE, array_merge($blog, $post));
//     }

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