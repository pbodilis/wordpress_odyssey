<?php get_header(); ?>

    <div id="archives_menu">
        <ul>
            <li>
                <h3>
                    <?php echo __( 'Monthly Archives:', 'odyssey' ); ?>
                </h3>
                <?php echo the_core()->get_rendered_monthly_archive_menu(); ?>
            </li>
            <?php /*wp_list_categories('title_li=<h3>' . __( 'Categories:', 'odyssey' ) . '</h3>');*/ ?>
        </ul>
    </div>

    <div id="archives_block">
        <h1 class="archive-title">
        <?php
            if ( is_day() ) {
                printf( __( 'Daily Archives: %s', 'odyssey' ), '<span>' . get_the_date() . '</span>' );
            } else if ( is_month() ) {
                printf( __( 'Monthly Archives: %s', 'odyssey' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'odyssey' ) ) . '</span>' );
            } else if ( is_year() ) {
                printf( __( 'Yearly Archives: %s', 'odyssey' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'odyssey' ) ) . '</span>' );
            } else if ( is_category() ) {
                printf( __( 'Category Archives: %s', 'odyssey' ), '<span>' . single_cat_title( '', false ) . '</span>' );
            } else {
                _e( 'Archives', 'odyssey' );
            };
        ?>
        </h1>
        <ul>
            <?php
            query_posts( $query_string . '&posts_per_page=-1' );
            while ( have_posts() ) { /* Start the Loop */
                the_post();
                echo '<li>' . PHP_EOL;

                // try first to get the post thumbnail
                if ( has_post_thumbnail(  ) ) {
                    $post_thumbnail_id = get_post_thumbnail_id(  );
                } else { // then try to get the first attachment thumbnail
                    $args = array(
                        'numberposts'    => 1,
                        'order'          => 'ASC',
                        'post_mime_type' => 'image',
                        'post_parent'    => $post->ID,
                        'post_status'    => null,
                        'post_type'      => 'attachment'
                    );

                    $attachments = get_children( $args );
                    if ($attachments) {
                        $attachment = current($attachments);
                        $post_thumbnail_id = $attachment->ID;
                    }
                }
                $thumbnail = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );

                echo '    <a class="thumbnail" href="' . get_permalink() .
                        '" title="' . get_the_title() .
                        '" style="background-image: url(' . $thumbnail[0] . ')"></a>' . PHP_EOL;
                echo '</li>' . PHP_EOL;
            }
            ?>
        </ul>
    </div>

<?php get_footer(); ?>
