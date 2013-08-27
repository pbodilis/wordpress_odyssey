    <section id="comments" class="comments-area">
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
        <?php the_core()->comment_form($post['ID']); ?>
    </section>
