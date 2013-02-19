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

        $core = \Odyssey\Core::getInstance();
        $core->init(array(
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

function oddyssey_filter_content($content) {
    switch (get_post_format()) {
        case 'image':
            $content = strip_shortcodes($content);
            // ouch, now, that's a ugly hack :/
            // remove the image, the link and the paragraph in which the image is.
            $content = preg_replace('/<img[^>]+\>/i', __('Download image'), $content, 1);
            break;
        default:
            break;
    }
    
    return $content;
}
add_filter('the_content', 'oddyssey_filter_content');
// remove_filter('the_content', 'wpautop');

?>