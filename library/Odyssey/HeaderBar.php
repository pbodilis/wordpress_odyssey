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
    const SYNDICATION_OPTION_NAME = 'odyssey_option_headerbar_syndication';
    const OTHER_LINKS_OPTION_NAME = 'odyssey_option_headerbar_other_links';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        Admin::get_instance()->register($this);
        add_action('admin_init', array(&$this, 'admin_init_syndication'));
        add_action('admin_init', array(&$this, 'admin_init_other_links'));
    }

    public function admin_init_syndication() {
        // syndication is tricky, as there's a check box to enable/disable the field, and a textfield.
        register_setting(Admin::OPTION_GROUP, self::SYNDICATION_OPTION_NAME, array(&$this, 'sanitize_option_syndication'));
        add_settings_section(
            self::SYNDICATION_OPTION_NAME,                // section id
            __('Syndication links management', 'odyssey'), // section title
            array(&$this, 'section_text_syndication'),    // callback to the function displaying the output of the section
            Admin::OPTION_PAGE           // menu page (slug of the theme setting page)
        );
        foreach($this->get_option_syndication() as $key => $value) {
            add_settings_field(
                $key,
                self::option_id2label($key),
                array(&$this, 'option_field_syndication'),
                Admin::OPTION_PAGE,            // menu page (slug of the theme setting page)
                self::SYNDICATION_OPTION_NAME, // the option name it is recorded into
                array('label_for' => $key, 'value' => $value)
            );
        }
    }
    public function section_text_syndication() {
        echo '<p>Please select the syndication to display in header bar</p>' . PHP_EOL;
    }
    public function get_option_syndication() {
        $default = self::get_default_option_syndication();
        $option = get_option(self::SYNDICATION_OPTION_NAME, $default);
        if (empty($option)) {
            $option = array();
        }
        foreach($default as $key => $value) {
            if (array_key_exists($key, $option) && $option[$key] === true) {
                $default[$key]['enabled'] = true;
            } else {
                $default[$key]['enabled'] = false;
            }
        }

        return array_merge($default, $option);
    }

    public function option_field_syndication($args) {
        echo '<input id="' . $args['label_for'] . '" ' .
            'name="' . self::SYNDICATION_OPTION_NAME . '[' . $args['label_for'] . ']" ' .
            'type="checkbox"' .
            ($args['value']['enabled'] ? 'checked="checked"' : '') .
            ' />';
        echo '<input name="' . $args['label_for'] . '" type="text" size="60" value="' . $args['value']['value'] . '" class="regular-text" />' . PHP_EOL;
    }

    public function sanitize_option_syndication($input) {
        $option = get_option(self::SYNDICATION_OPTION_NAME, self::get_default_option_syndication());
        foreach($option as $key => $enabled) {
            if (array_key_exists($key, $input) && $input[$key] === 'on') {
                $option[$key]['enabled'] = true;
                $option[$key]['value']   = $_REQUEST[$key];
            } else {
                $option[$key]['enabled'] = false;
            }
        }
        return $option;
    }
    static public function get_default_option_syndication() {
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




    public function admin_init_other_links() {
        register_setting(Admin::OPTION_GROUP, self::OTHER_LINKS_OPTION_NAME);
        add_settings_section(
            self::OTHER_LINKS_OPTION_NAME,    // section id
            __('Other links management', 'odyssey'), // section title
            array(&$this, 'section_text_other_links'),    // callback to the function displaying the output of the section
            Admin::OPTION_PAGE           // menu page (slug of the theme setting page)
        );
        foreach($this->get_option_other_links() as $key => $value) {
            add_settings_field(
                $key,
                self::option_id2label($key),
                array(&$this, 'option_field'),
                Admin::OPTION_PAGE,       // menu page (slug of the theme setting page)
                self::OTHER_LINKS_OPTION_NAME,             // the option name it is recoreded into
                array('label_for' => $key, 'value' => $value)
            );
        }
    }
    public function section_text_other_links() {
        echo '<p>Please select the other links to display in the header bar</p>' . PHP_EOL;
    }
    public function get_option_other_links() {
        $default = self::get_default_header_bar_other_links_option();
        $option = get_option(self::OTHER_LINKS_OPTION_NAME, $default);
        if (empty($option)) {
            $option = array();
        }
        foreach($default as $key => $value) {
            if (array_key_exists($key, $option) && $option[$key] === true) {
                $default[$key] = true;
            } else {
                $default[$key] = false;
            }
        }

        return array_merge($default, $option);
    }

    function option_field($args) {
        echo '<input id="' . $args['label_for'] . '" ' .
            'name="' . self::OTHER_LINKS_OPTION_NAME . '[' . $args['label_for'] . ']" ' .
            'type="checkbox"' .
            ($args['value'] ? 'checked="checked"' : '') .
            ' />';
    }

    static public function get_default_header_bar_other_links_option() {
        return array(
            'random'   => true,
            'archives' => true,
            'pages'    => true,
        );
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

    function get_syndication() {
        $options = $this->get_option_syndication();
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
        $options = $this->get_option_other_links();
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