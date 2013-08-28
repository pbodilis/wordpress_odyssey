    <section id="comments" class="comments-area">
        <div id="comments_area">
            <h3 id="comment_title">
                <?php  echo count($post['comments']); ?> Comment(s)
            </h3>
<!--            <div id="comment_list_item_0">
                <ul class="comments_list">-->
		<ol class="comment-list">
                    <?php
//  echo the_core()->render('photoblog_comments', $post);
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 74,
				), get_comments(array('post_id' => $post['ID'])) );
 ?>
		</ol>
<!--                </ul>
            </div>-->
        </div>
        <?php the_core()->comment_form($post['ID']); ?>
    </section>
