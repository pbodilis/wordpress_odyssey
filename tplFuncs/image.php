<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');

$postId = 4;

// $post = get_post($my_id);
/*
if (have_posts()) the_post();

$ret = array(
    'have_posts()' => have_posts(),
    'imageName'  => $post->image->uri,
    'imageTitle' => $post->post_title,
);*/

$ret = array(
    'imageName'  => 'wp-content/uploads/2013/01/20121024-20120822-ParDeLaVilette-001177.jpg',
    'imageTitle' => 'coin title',
);

print json_encode($ret);
?>