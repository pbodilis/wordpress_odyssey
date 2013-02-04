<?php
/**
 * This file is part of Odyssey Theme for WordPress.
 */


    /**
     *  JavaScript helper functions
     *  @package Odyssey Theme for WordPress
     *  @subpackage JavaScript
     */

    if(!defined('ODYSSEY_THEME_VERSION') ) die(basename(__FILE__));


    /* functions */

    /**
     * odyssey_embed_javascripts() - Injects the HTML markup for the JavaScript files used by Odyssey
     *
     * @since 0.1
     */
    function odyssey_embed_javascripts()
    {
        // template engine
        wp_enqueue_script('odyssey-mustache',           get_template_directory_uri() . '/js/mustache.js',                 array('jquery'));
        wp_enqueue_script('odyssey-jquery-mustache',    get_template_directory_uri() . '/js/jquery.mustache.js',          array('jquery'));

        // embed the javascript file that makes the AJAX request
        wp_enqueue_script('odyssey-ajax-request',       get_template_directory_uri() . '/js/odyssey.js', array('jquery'));

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        wp_localize_script('odyssey-ajax-request', 'OdysseyAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajaxtpl' => get_template_directory_uri() . '/templates/',
        ));

//         <script type="text/javascript">
//             var imageWidth = <IMAGE_WIDTH>;
//             var imageHeight = <IMAGE_HEIGHT>;
//             var dE = document.documentElement;
//             var defaultValue = 'Comment...'; // You can change this parameter
//             var sendingValue = 'Sending...'; // You can change this parameter
// 
//             var imgId     = <IMAGE_ID>;
//             var imgPrevId = <IMAGE_PREVIOUS_ID>;
//             var imgNextId = <IMAGE_NEXT_ID>;
//         </script>
//         echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/functions.desktop.unpack.js"></script>';
//         echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/functions.comments.unpack.js"></script>';

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
    function odyssey_get_json_post() {
    //     $postId = 6;
    //     $post = get_post($postId);
    // var_dump($_REQUEST);

        $post = current(get_posts(array(
    //         'order' => 'ASC',
            'limit' => 1
        )));

        $ret = array();


        $image = YapbImage::getInstanceFromDb($post->ID);
        if (!is_null($image)) { // that's a yapb post
            $post->image = $image;
            $ret['imageName'] = $post->image->uri;
        } // carry on as usual

        $ret['imageTitle'] = $post->post_title;

    //     header('Content-type: application/json');
        echo json_encode($ret);
        die();
    }
    add_action('wp_ajax_odyssey_get_json_post', 'odyssey_get_json_post');
    add_action('wp_ajax_nopriv_odyssey_get_json_post', 'odyssey_get_json_post');

?>