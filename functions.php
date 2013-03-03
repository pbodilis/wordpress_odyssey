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
function the_core() {
    static $core;
    if (!isset($core)) {
        $core = \Odyssey\Core::get_instance();
    }
    return $core;
}
the_core();

// meh, no idea what do with that
if (!isset($content_width)) {
    $content_width = 900;
}


// remove_filter('the_content', 'wpautop');


remove_filter('check_comment_flood', 'check_comment_flood_db');


//add_action( 'template_redirect', 'redirect' );
function redirect() {
    $args = array(
        'numberposts' => 1,
        'post_status' => 'publish'
    );
    $last = wp_get_recent_posts($args);
    $last_id = $last['0']['ID'];
    if ( is_home() && ! is_paged() && ! is_archive() && ! is_tag() && !isset($_GET['ptype'])  ) :
        wp_redirect( get_permalink($last_id) , 301 ); 
        exit;
    endif;
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