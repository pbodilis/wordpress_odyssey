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
    const OPTION_NAME = 'odyssey_settings_comment';
    const SUBMIT      = 'odyssey_submit_comment';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        Admin::get_instance()->register($this);

        Javascript::get_instance()->add_template('render_comments', 'photoblog_comments.mustache.html');

        add_action('comment_post', array(&$this, 'comment_post'), 20, 2);
    }

    static public function setting_id2label($setting_id) {
        $id2label = array(
            'comment_form_ajax_enabled' => __('Ajax comments: '),
       );
        return $id2label[ $setting_id ];
    }

    static public function get_default_settings() {
        return array(
            'comment_form_ajax_enabled' => true,
        );
    }

    public function get_settings() {
        return get_option(self::OPTION_NAME, self::get_default_settings());
    }

    public function get_setting($s) {
        $settings = $this->get_settings();
        return $settings[ $s ];
    }

    public function get_setting_page() {
        $settings = $this->get_settings();
        if (isset($_POST[self::SUBMIT])) {
            $doUpdate = false;
            foreach ($settings as $setting => &$enabled) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[ $setting ] ) && ! $enabled) {
                    $enabled = true;
                    $doUpdate = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[ $setting ] ) && $enabled) {
                    $enabled = false;
                    $doUpdate = true;
                }
            }
            $doUpdate && update_option(self::OPTION_NAME, $settings);
        }

        $data = array();
        foreach ($settings as $setting => &$enabled) {
            $data[] = array('id' => $setting, 'enabled' => $enabled, 'setting' => self::setting_id2label($setting));
        }
        return Renderer::get_instance()->render('admin_comments', array(
            'settings' => $data,
            'submit'   => self::SUBMIT,
        ));
    }

    public function get_comment_form() {
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? ' aria-required="true"' : '' );
        
        $args = array(
            'id_form'   => 'commentform',
            'id_submit' => 'comment_submit',
            'title_reply' => __( 'Leave a comment' ),
        //     'title_reply_to' => __( 'Leave a Reply to %s' ),
        //     'cancel_reply_link' => __( 'Cancel Reply' ),
            'label_submit' => __( 'Post Comment' ),
            'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
        //     'must_log_in' => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
        //     'logged_in_as' => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
            'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . '</p>',
            'comment_notes_after' => '<p id="comment_status" ></p>',
        //     'fields' => apply_filters( 'comment_form_default_fields', array(
        //         'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
        //         'email' => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
        //         'url' => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'domainreference' ) . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>' ) )
        );
        comment_form($args);
    }

    public function get_post_comments_title($post_id) {
        return sprintf( _n( 'One comment ', '%1$s comments', get_comments_number($post_id), 'odyssey' ), number_format_i18n( get_comments_number() ) );
    }

    /**
     * build a tree of comments
     *  - 'id'
     *  - 'author'
     *  - 'author_url'
     *  - 'date'
     *  - 'content'
     *  - 'children'
     */
    public function get_post_comments($post_id) {
        $list = array();
        $tree = array();
        
        $args = array(
            'post_id' => $post_id,
            'status'  => 'approve',
            'orderby' => 'comment_date',
            'order'   => 'ASC',
        );

        $comments = get_comments($args);
        foreach($comments as $comment) {
            $c = array(
                'id'         => $comment->comment_ID,
                'author'     => $comment->comment_author,
                'author_url' => $comment->comment_author_url,
                'date'       => $comment->comment_date,
                'content'    => apply_filters('comment_text', $comment->comment_content),
                'leaf'       => true,
                'comments'   => array(),
            );
            if (0 == $comment->comment_parent) {
                $i = array_push($tree, $c);
                $list[$comment->comment_ID] =& $tree[$i - 1];
            } else {
                $parent =& $list[$comment->comment_parent];
                $parent['leaf'] = false;
                $i = array_push($parent['comments'], $c);
                $list[$comment->comment_ID] =& $parent['comments'][$i - 1];
            }
        }
        return array_values($tree);
    }

    public function comment_post($comment_ID, $comment_status) {
        if ( ! $this->get_setting( 'comment_form_ajax_enabled' ) ) return;

        if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            switch( $comment_status ) {
                case '0': //notify moderator of unapproved comment
                    wp_notify_moderator($comment_ID);
                case '1': //Approved comment
                    $comment = &get_comment($comment_ID);
                    $post    = &get_post($comment->comment_post_ID);
                    wp_notify_postauthor($comment_ID, $comment->comment_type);
                    $ret = array(
                        'author'     => $comment->comment_author,
                        'author_url' => $comment->comment_author_url,
                        'date'       => $comment->comment_date,
                        'content'    => apply_filters('comment_text', $comment->comment_content),
                    );
                    echo json_encode(array($ret));
                    break;
                default:
                    echo "error";
            }
            die();
        }
    }
}

?>