<?php
/*
Template Name: Index Template
*/

get_header();
?>

<div id="photo_frame"></div>


<div id="panel" class="out">
    <div id="panel_handle">
        <div id="panel_handle_arrowbox">
            <div id="panel_handle_arrow"></div>
        </div>
        <h2>Info,&nbsp;rate&nbsp;&amp;&nbsp;Comments</h2>
    </div>
    <div id="panel_content">
        <div id="content"></div>
        <div class="clr"></div>
<?php the_core()->get_comment_form(); ?>
        <div class="clr"></div>
        <div id="comments_area">
            <h3 id="comment_title"></h3>
            <div id="comment_list_item_0"></div>
        </div>
        <div class="clr"></div>
    </div>
</div>

<?php


get_footer();
