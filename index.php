<?php
/*
Template Name: Index Template
*/

get_header();
?>

<div id="photo_frame"></div>

<?php

theCore()->render('photoblog_panel', theCore()->getPanelState());
//     theCore()->render('photoblog_image', theCore()->getPost());

// <iframe src="http://www.facebook.com/plugins/like.php?href=urlencode(get_permalink($post->ID))&amp;layout=standard&amp;show_faces=false&amp;width=150&amp;action=like&amp;colorscheme=dark" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:150px; height:60px; top: 100px; left: 50px; position: absolute">
// </iframe>


get_footer();
?>
