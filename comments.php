    <section id="comments" class="comments-area">
        <div id="comments_area">
            <h3 id="comment_title">
                <?php  echo count($post['comments']); ?> Comment(s)
            </h3>
            <ol class="comment-list">
                    <?php
                    wp_list_comments( array(
                        'style'       => 'ol',
                        'short_ping'  => true,
                        'avatar_size' => 74,
                    ), get_comments( array('post_id' => $post['ID'] ) ) );
                    ?>
            </ol>
        </div>
        <?php the_core()->comment_form($post['ID']); ?>
    </section>
