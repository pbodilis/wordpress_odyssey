<?php
/*
Template Name: Index Template
*/

get_header();

$post = the_core()->get_post();
?>

<div id="page" class="<?php echo $post['class']; ?>">

<?php
echo the_core()->render('photoblog_image', $post);
?>

    <?php echo the_core()->render('photoblog_content', $post); ?>
    <section id="comments">
        <?php the_core()->comment_form($post['ID']); ?>
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
    </section>
</div>

<?php
get_footer();





