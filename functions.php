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

add_filter('body_class', 'odyssey_body_class');
function odyssey_body_class($classes) {
    // add 'class-name' to the $classes array
    $color = isset($_COOKIE['odyssey_theme_color']) ? $_COOKIE['odyssey_theme_color'] : 'white';
    $classes[] = $color;
    // return the $classes array
    return $classes;
}

add_action('comment_post', 'odyssey_comment');
function odyssey_comment($comment_ID, $comment_status) {
echo "COIN\n;";
//     if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
//         //If AJAX Request Then
//         switch($comment_status){
//             case '0':
//                 //notify moderator of unapproved comment
//                 wp_notify_moderator($comment_ID);
//             case '1': //Approved comment
//                 echo "success";
//                 $commentdata=&get_comment($comment_ID, ARRAY_A);
//                 $post=&get_post($commentdata['comment_post_ID']);
//                 wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
//             break;
//                 default:
//                 echo "error";
//         }
//         exit;
//     }
}

// $defaults = array(
//     'default-color'          => 'rgb(85, 85, 85)',
//     'default-image'          => '',
//     'wp-head-callback'       => '_custom_background_cb',
//     'admin-head-callback'    => '',
//     'admin-preview-callback' => ''
// );
// add_theme_support('custom-background', $defaults);


// register_sidebar(array(
//   'name' => __('the Sidebar on top'),
//   'id' => 'heading-sidebar',
//   'description' => __( 'Widgets in this area will be shown inline on top of the screen.' ),
// ));
// 
// function your_widget_display($args) {
//    extract($args);
//    echo $before_widget;
//    echo $before_title . 'My Unique Widget' . $after_title;
//    echo $after_widget;
//    // print some HTML for the widget to display here
//    echo "Your Widget Test";
// }
// 
// wp_register_sidebar_widget(
//     'your_widget_1',        // your unique widget id
//     'Your Widget',          // widget name
//     'your_widget_display',  // callback function
//     array(                  // options
//         'description' => 'Description of what your widget does'
//     )
// );

?>