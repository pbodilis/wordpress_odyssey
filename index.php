<?php
/*
Template Name: Index Template
*/

get_header();

$post = the_core()->get_post();
?>

<div id="page" class="<?php echo $post['class']; ?>">

<?php

if (locate_template('single-' . $post['format'] . '.php') != '') { // template part exists \o/
    get_template_part('single', $post['format']);
} else {
    get_template_part('single', 'default');
}



comments_template();
?>

</div>

<?php

get_footer();





