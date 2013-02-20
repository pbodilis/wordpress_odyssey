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
        add_action('admin_menu', array(&$this, 'buildAdminPage'));
    }

    public function register(&$m)
    {
        $this->managers[] = &$m;
    }

    public function buildAdminPage() {
        add_menu_page(
            'Odyssey theme settings',      // page_title
            'Odyssey theme settings',      // menu_title
            'manage_options',              // capability
            'odyssey_settings',            // menu_slug
            array(&$this, 'getSettingPage')); // renderer

        foreach($this->managers as $manager) {
            add_submenu_page(
                'odyssey_settings',                  // parent_slug
                $manager->getPageTitle(),            // page_title
                $manager->getMenuTitle(),            // menu_title
                'manage_options',                    // capability
                $manager->getMenuSlug(),             // menu_slug
                array(&$manager, 'getSettingPage')); // renderer
        }
        
    }
    
    public function getSettingPage()
    {
        $output = '';
        $output .= '<h2>PROUT</h2>';
        echo $output;
    }

}

?>