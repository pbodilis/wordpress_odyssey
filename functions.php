<?php

/**
 *   This file is part of Odyssey Theme for WordPress.
 */

require dirname(__FILE__) . '/oembend_featured_image.php';


// include odysssey libs
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

// enable comments on non-single pages (basically, home)
$withcomments = 1;


?>
