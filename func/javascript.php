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
//         echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/jquery-1.7.2.min.js"></script>';
        echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/jquery-1.9.0.min.js"></script>';
        echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/jquery.mousewheel.js"></script>';
        // <!-- <script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/mwheelIntent.js"></script>'; -->
        echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/jquery.jscrollpane.min.js"></script>';

        // template engine
        echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/mustache.js"></script>';
        echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/jquery.mustache.js"></script>';

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
        echo '<script type="text/javascript" src="' . ODYSSEY_TEMPLATE_DIR . '/js/functions.desktop.unpack.js"></script>';
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


?>