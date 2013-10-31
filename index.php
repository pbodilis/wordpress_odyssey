<?php
/*
Template Name: Index Template
*/

get_header();

$post = the_core()->get_post();

?>

<nav class="main-nav">
    <?php 
    echo '<a class="previous" title="Previous" href="' . (isset($post->previous_url) ? $post->previous_url : '') . '"></a>' . PHP_EOL;
    echo '<a class="next" title="next" href="' . (isset($post->next_url) ? $post->next_url : '') . '"></a>' . PHP_EOL;
    ?>
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





