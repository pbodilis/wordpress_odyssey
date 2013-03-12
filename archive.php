<?php get_header(); ?>


    <div id="archives_block">
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

<?php get_footer(); ?>
