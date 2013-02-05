<?php
/**
 * This file is part of Odyssey Theme for WordPress.
 */


    /**
     *  JavaScript helper functions
     *  @package Odyssey Theme for WordPress
     *  @subpackage JavaScript
     */
class Odyssey_Javascript
{
    public function __construct()
    {
        // add the callbacks
        add_action('wp_ajax_odyssey_get_json_post',        array(&$this, 'getJsonPost'));
        add_action('wp_ajax_nopriv_odyssey_get_json_post', array(&$this, 'getJsonPost'));
    }

    /**
     * Injects the HTML markup for the JavaScript files used by Odyssey
     *
     * @since 0.1
     */
    public function embed()
    {
        // template engine
        wp_enqueue_script('odyssey-mustache',           get_template_directory_uri() . '/js/mustache.js',        array('jquery'));
//         wp_enqueue_script('odyssey-jquery-mustache',    get_template_directory_uri() . '/js/jquery.mustache.js', array('jquery'));
        wp_enqueue_script('odyssey-chevron',    get_template_directory_uri() . '/js/chevron.js', array('jquery'));

        // embed the javascript file that makes the AJAX request
        wp_enqueue_script('odyssey-ajax-request',       get_template_directory_uri() . '/js/odyssey.js',         array('jquery'));

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        wp_localize_script('odyssey-ajax-request', 'OdysseyAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajaxtpl' => get_template_directory_uri() . '/templates/',
        ));
//         <script type="text/javascript">
//             var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
//             document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//         </script>
//         <script type="text/javascript">
//             var pageTracker = _gat._getTracker("UA-3348687-1");
//             pageTracker._initData();
//             pageTracker._trackPageview();
//         </script>
    }

    /**
     * \returns a JSON array
     */
    public function getJsonPost() {
        $ret = Odyssey_Core::getInstance()->getPost();
        echo json_encode($ret);
        die();
    }
}

?>