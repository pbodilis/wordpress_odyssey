<?php
/*
Template Name: Index Template
*/

get_header();

$post = the_core()->get_post();

?>

<nav class="main-nav">
    <a class="previous" title="Previous" href="<?php echo $post->previous_url; ?>"><?php echo $post->previous_title; ?></a>
    <a class="next"     title="Next"     href="<?php echo $post->next_url;     ?>"><?php echo $post->next_title; ?></a>
</nav>


<div id="page" class="<?php echo $post->class; ?>">

<?php

if (locate_template('single-' . $post->format . '.php') != '') { // template part exists \o/
    get_template_part('single', $post->format);
} else {
    get_template_part('single', 'default');
}

?>

</div>

<?php

get_footer();





