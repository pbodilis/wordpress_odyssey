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
<?php
theCore()->get_comment_form();
?>
        <div class="clr"></div>
        <div id="comments"></div>
        <div class="clr"></div>
    </div>
</div>

<?php


// theCore()->render('photoblog_panel', array());
//     theCore()->render('photoblog_image', theCore()->getPost());

// <iframe src="http://www.facebook.com/plugins/like.php?href=urlencode(get_permalink($post->ID))&amp;layout=standard&amp;show_faces=false&amp;width=150&amp;action=like&amp;colorscheme=dark" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:150px; height:60px; top: 100px; left: 50px; position: absolute">
// </iframe>


get_footer();
?>
