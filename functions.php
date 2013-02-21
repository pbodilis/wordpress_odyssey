<?php

/**
 *   This file is part of Odyssey Theme for WordPress.
 */

// include mustache engine
require dirname(__FILE__) . '/library/Mustache/Autoloader.php';
Mustache_Autoloader::register();

// include odysssey engine
require dirname(__FILE__) . '/library/Odyssey/Autoloader.php';
\Odyssey\Autoloader::register();

// launch odyssey engine
function theCore() {
    static $core;
    if (!isset($core)) {

        $core = \Odyssey\Core::getInstance(array(
            'enable_js' => true,
        ));
    }
    return $core;
}
theCore();

// meh, no idea what do with that
if (!isset($content_width)) {
    $content_width = 900;
}

add_theme_support('post-formats', array('image', 'video'));

function odyssey_filter_content($content) {
    switch (get_post_format()) {
        case 'image':
            $content = strip_shortcodes($content);
            // ouch, now, that's a ugly hack :/
            // remove the image, the link and the paragraph in which the image is.
            $content = preg_replace('/<img[^>]+\>/', __('Download image'), $content, 1);
            $content = preg_replace('/<p[^>]*>[\s|&nbsp;]*<\/p>/', '', $content);
            $content = preg_replace('/(width|height)="\d*"\s/', '', $content);
            break;
        default:
            break;
    }
    
    return $content;
}
add_filter('the_content', 'odyssey_filter_content');
// remove_filter('the_content', 'wpautop');



add_filter('body_class','odyssey_body_class');
function odyssey_body_class($classes) {
    // add 'class-name' to the $classes array
    $color = isset($_COOKIE['odyssey_theme_color']) ? $_COOKIE['odyssey_theme_color'] : '';
    $classes[] = $color;
    // return the $classes array
    return $classes;
}

// $defaults = array(
//     'default-color'          => '000000',
//     'default-image'          => '',
//     'wp-head-callback'       => '_custom_background_cb',
//     'admin-head-callback'    => '',
//     'admin-preview-callback' => ''
// );
// add_theme_support( 'custom-background', $defaults );

?>