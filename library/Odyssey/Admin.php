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
 *  Admin helper functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage Admin
 */
class Admin
{
    private $managers = array();

    const OPTION_GROUP = 'odyssey_options';
    const OPTION_PAGE = 'odyssey_option_page';

    static private $instance;
    static public function get_instance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct()
    {
        // add the callbacks
//         add_action('admin_init', array(&$this, 'theme_options_init'));
        add_action('admin_menu', array(&$this, 'theme_options_add_page'));

    }

    public function register(&$m)
    {
//         $this->managers[] = &$m;
    }

//     public function theme_options_init()
//     {
//         register_setting( 'sample_options', 'sample_theme_options');
//     }
    public function theme_options_add_page()
    {
        add_theme_page(
            __('Odyssey theme options', 'odyssey'),      // page_title
            __('Odyssey theme options', 'odyssey'),      // menu_title
            'manage_options',                            // capability
            'odyssey_theme_options',                     // menu_slug
            array(&$this, 'get_option_page'));           // renderer
    }
    public function get_option_page()
    {
        global $select_options;
        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = false;
        }

        screen_icon();
        echo '<h2>' . __('Odyssey Theme Settings', 'odyssey') . '</h2>' . PHP_EOL;

        if ( false !== $_REQUEST['settings-updated'] ) {
            echo '<div><p><strong>' . _e( 'Options saved', 'customtheme' ) . '</strong></p></div>' . PHP_EOL;
        }

        echo '<form method="post" action="options.php">';
        settings_fields( self::OPTION_GROUP );
        submit_button();
        $this->do_settings_sections( self::OPTION_PAGE );
        echo '</form>' . PHP_EOL;

    }

    // basically, copy/paste of the original do_settings_sections, with additional shit to organise each section in floating div blocks
    function do_settings_sections( $page ) {
        global $wp_settings_sections, $wp_settings_fields;

        if ( ! isset( $wp_settings_sections ) || !isset( $wp_settings_sections[$page] ) )
            return;

        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            echo '<div class=".wrap" style="float: left;">' . PHP_EOL;
            if ( $section['title'] )
                echo "<h3>{$section['title']}</h3>\n";

            if ( $section['callback'] )
                call_user_func( $section['callback'], $section );

            if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
                continue;
            echo '<table class="form-table">';
            do_settings_fields( $page, $section['id'] );
            echo '</table>';
            echo '</div>' . PHP_EOL;
        }
    }
}

?>