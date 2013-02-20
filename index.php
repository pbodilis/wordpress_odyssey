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

get_footer();
?>
