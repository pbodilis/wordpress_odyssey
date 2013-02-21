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

?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;
width=450&amp;action=like&amp;colorscheme=dark" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:60px; top: 100px; left: 50px; position: absolute">
</iframe>
<?php


get_footer();
?>
