    <section id="comments">
        <div id="comments_area">
            <h3 class="comments-title">
                <?php echo $post['comments_number']; ?> Comment(s)
            </h3>
            <ol class="comment-list">
                <?php echo $post['comments']; ?>
            </ol>
        </div>
        <?php the_core()->comment_form($post['ID']); ?>
    </section>
