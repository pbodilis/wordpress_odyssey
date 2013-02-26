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

    static private $instance;
    static public function getInstance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct()
    {
        // add the callbacks
        add_action('admin_menu', array(&$this, 'build_admin_page'));
    }

    public function register(&$m)
    {
        $this->managers[] = &$m;
    }

    public function build_admin_page() {

        add_submenu_page(
            'themes.php',                  // parent_slug
            'Odyssey theme settings',      // page_title
            'Odyssey theme settings',      // menu_title
            'manage_options',              // capability
            'odyssey_theme_settings',      // menu_slug
            array(&$this, 'get_setting_page')); // renderer
//         add_menu_page(
//             'Odyssey theme settings',      // page_title
//             'Odyssey theme settings',      // menu_title
//             'manage_options',              // capability
//             'odyssey_settings',            // menu_slug
//             array(&$this, 'get_setting_page')); // renderer
// 
//         foreach($this->managers as $manager) {
//             add_submenu_page(
//                 'odyssey_settings',                  // parent_slug
//                 $manager->getPageTitle(),            // page_title
//                 $manager->getMenuTitle(),            // menu_title
//                 'manage_options',                    // capability
//                 $manager->getMenuSlug(),             // menu_slug
//                 array(&$manager, 'get_setting_page')); // renderer
//         }
        
    }
    
    public function get_setting_page()
    {
        echo '<div id="icon-themes" class="icon32"><br></div>' . PHP_EOL;
        echo '<h2>Odyssey Theme Settings</h2>' . PHP_EOL;
        foreach($this->managers as $manager) {
            $manager->get_setting_page();
        }
    }

}

?>