<?php
/*
Template Name: Index Template
*/

get_header();
?>

<div id="photo_frame"></div>


<div id="panel" class="">
    <div id="panel_handle">
        <div id="panel_handle_arrowbox">
            <div id="panel_handle_arrow"></div>
        </div>
        <h2>Info,&nbsp;rate&nbsp;&amp;&nbsp;Comments</h2>
    </div>
    <div id="panel_content">
        <div id="content"></div>
<?php
$args = array(
    'id_form'   => 'comment_form',
    'id_submit' => 'comment_submit',
    'title_reply' => __( 'Leave a comment' ),
//     'title_reply_to' => __( 'Leave a Reply to %s' ),
//     'cancel_reply_link' => __( 'Cancel Reply' ),
    'label_submit' => __( 'Post Comment' ),
    'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" rows="8" aria-required="true"></textarea></p>',
//     'must_log_in' => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
//     'logged_in_as' => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
//     'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
    'comment_notes_after' => '',
//     'fields' => apply_filters( 'comment_form_default_fields', array(
//         'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
//         'email' => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
//         'url' => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'domainreference' ) . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>' ) )
);
comment_form($args);
?>
        <div class="clr"></div>
        <div id="comments"></div>
    </div>
</div>

<?php


// theCore()->render('photoblog_panel', array());
//     theCore()->render('photoblog_image', theCore()->getPost());

// <iframe src="http://www.facebook.com/plugins/like.php?href=urlencode(get_permalink($post->ID))&amp;layout=standard&amp;show_faces=false&amp;width=150&amp;action=like&amp;colorscheme=dark" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:150px; height:60px; top: 100px; left: 50px; position: absolute">
// </iframe>


get_footer();
?>
