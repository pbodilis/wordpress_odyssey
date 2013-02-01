<?php

$postId = 4;

// $post = get_post($my_id);
// 
// $ret = array(
//     'imageName'  => $post->image->uri,
//     'imageTitle' => $post->post_title,
// );

$ret = array(
    'imageName'  => 'wp-content/uploads/2013/01/20121024-20120822-ParDeLaVilette-001177.jpg',
    'imageTitle' => 'coin title',
);

print json_encode($ret);
?>