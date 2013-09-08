    <section id="comments" class="comments-area">
        <div id="comments_area">
            <h3 id="comment_title">
                <?php echo count($post['comments']); ?> Comment(s)
            </h3>
            <ol class="comment-list">
                <?php echo $post['comments']; ?>
            </ol>
        </div>
        <?php the_core()->comment_form($post['ID']); ?>
    </section>
