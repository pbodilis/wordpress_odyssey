<?php
/*
Template Name: Index Template
*/

get_header();

$post = the_core()->get_post();
?>

<div id="page" class="<?php echo $post['class']; ?>">

<?php
get_template_part('single', $post['format']);

comments_template();
?>

</div>

<?php

get_footer();





