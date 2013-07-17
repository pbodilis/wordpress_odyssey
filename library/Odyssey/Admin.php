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
        add_action('admin_menu', array(&$this, 'build_admin_page'));
    }

    public function register(&$m)
    {
        $this->managers[] = &$m;
    }

    public function build_admin_page()
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
        echo '<div id="icon-themes" class="icon32"><br></div>' . PHP_EOL;
        echo '<h2>' . __('Odyssey Theme Settings', 'odyssey') . '</h2>' . PHP_EOL;
        foreach($this->managers as $manager) {
            echo $manager->get_option_page();
        }
    }

}

?>