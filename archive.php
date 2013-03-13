<?php get_header(); ?>


    <div id="archives_menu">
        <?php get_search_form(); ?>

        <ul>
            <li>
                <h3>
                    <?php echo __( 'Monthly Archives:', 'odyssey' ); ?>
                </h3>
                <ul>
                <?php wp_get_archives('type=monthly'); ?>
                </ul>
            </li>
            <?php wp_list_categories('title_li=<h3>' . __( 'Categories:', 'odyssey' ) . '</h3>'); ?>
        </ul>
    </div>

    <div id="archives_block">
        <?php
        /* Start the Loop */
        while ( have_posts() ) : the_post();

            the_title();
        endwhile;
        ?>
    </div>

<?php get_footer(); ?>
