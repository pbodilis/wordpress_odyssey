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
    static private $instance;

    public function __construct()
    {
        // add the callbacks
        add_action('admin_menu', array(&$this, 'buildAdminPage'));
    }

    static public function getInstance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    function buildAdminPage() {
        add_menu_page(
            'Odyssey theme settings',      // page_title
            'Odyssey theme settings',      // menu_title
            'manage_options',              // capability
            'odyssey_settings',            // menu_slug
            array(&$this, 'settingPage')); // renderer

        foreach() {
            
        }
        
        add_submenu_page(
            'odyssey_settings',        // parent_slug
            'Exif settings',           // page_title
            'Exif settings',           // menu_title
            'manage_options',          // capability
            'odyssey-settings-exifs',  // menu_slug
            array(&$this, 'settinPage')); // renderer
    }


// This tells WordPress to call the function named "setup_theme_admin_menus"
// when it's time to create the menu pages.
add_action("admin_menu", "setup_theme_admin_menus");

function setup_theme_admin_menus() {
}


}

?>