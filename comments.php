    <section id="comments">
        <div id="comments_area">
            <h3 class="comments-title">
                <?php 
                printf( _nx( 'One comment', '%1$s comments', $post['comments_number'], 'comments title', 'odyssey' ),
                    number_format_i18n( $post['comments_number'] ));
                ?>
            </h3>
            <ol class="comment-list">
                <?php echo $post['comments']; ?>
            </ol>
        </div>
        <?php the_core()->comment_form($post['ID']); ?>
    </section>
