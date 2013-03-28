<?php
/*
Template Name: Index Template
*/

get_header();

$post = the_core()->get_post();
?>

<?php
// <div id="post_main"></div>
echo the_core()->render('photoblog_image', $post);
?>


<div id="panel" class="out">
    <div id="panel_handle">
        <div id="panel_handle_arrowbox">
            <div id="panel_handle_arrow"></div>
        </div>
        <h2>Info,&nbsp;rate&nbsp;&amp;&nbsp;Comments</h2>
    </div>
    <div id="panel_content">
        <?php echo the_core()->render('photoblog_content', $post); ?>
        <div class="clr"></div>
        <?php the_core()->comment_form(); ?>
        <div class="clr"></div>
        <div id="comments_area">
            <h3 id="comment_title">
                <?php  echo count($post['comments']); ?> Comment(s)
            </h3>
            <div id="comment_list_item_0">
                <ul class="comments_list">
                    <?php echo the_core()->render('photoblog_comments', $post); ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>
    </div>
</div>

<?php
get_footer();
