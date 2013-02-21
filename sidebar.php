<?php

    if ( is_active_sidebar( 'heading-sidebar' ) ) {
        echo '<div id="secondary" class="widget-area" role="complementary">';
        dynamic_sidebar( 'heading-sidebar' );
        echo '</div>';
    }

?>