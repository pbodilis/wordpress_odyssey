<?php
/**
 * This file is part of Odyssey theme for wordpress.
 *
 * (c) 2013 Pierre Bodilis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Odyssey;

/**
 *  CommentManager functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage CommentManager
 */
class CommentManager
{
    static private $instance;
    static public function getInstance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        add_action('comment_post', array(&$this, 'post'), 20, 2);
    }

    public function get_comment_form() {
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' );
        
        $args = array(
            'id_form'   => 'comment_form',
            'id_submit' => 'comment_submit',
            'title_reply' => __( 'Leave a comment' ) . '<div id="comment_status" ></div>',
        //     'title_reply_to' => __( 'Leave a Reply to %s' ),
        //     'cancel_reply_link' => __( 'Cancel Reply' ),
            'label_submit' => __( 'Post Comment' ),
            'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
        //     'must_log_in' => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
        //     'logged_in_as' => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
            'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . '</p>',
            'comment_notes_after' => '',
        //     'fields' => apply_filters( 'comment_form_default_fields', array(
        //         'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
        //         'email' => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
        //         'url' => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'domainreference' ) . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>' ) )
        );
        comment_form($args);
    }
    
    public function get_post_comments($post_id) {
        $ret = array();

        $args = array(
            'post_id' => $post_id,
            'status'  => 'approve',
            'orderby' => 'comment_date',
            'order'   => 'ASC',
        );

        $comments = get_comments($args);
        foreach($comments as $comment) {
            $ret[] = array(
                'author'     => $comment->comment_author,
                'author_url' => $comment->comment_author_url,
                'date'       => $comment->comment_date,
                'content'    => apply_filters('comment_text', $comment->comment_content),
            );
        }
        
//         var_dump($ret);
        return $ret;
    }

    function post($comment_ID, $comment_status) {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            //If AJAX Request Then
            switch($comment_status) {
                case '0':
                    //notify moderator of unapproved comment
                    wp_notify_moderator($comment_ID);
                case '1': //Approved comment
                    echo "success";
                    $commentdata = &get_comment($comment_ID, ARRAY_A);
                    $post = &get_post($commentdata['comment_post_ID']);
                    wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
echo json_encode($commentdata);
                    break;
                default:
                    echo "error";
            }
            die();
        }
    }
   
}

?>