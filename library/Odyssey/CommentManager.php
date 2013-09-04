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
    const OPTION_NAME = 'odyssey_options_comment';
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
        add_filter('comment_post_redirect', array(&$this, 'no_comment_redirection'));
    }

    static public function option_id2label($option_id) {
        $id2label = array(
            'comment_form_ajax_enabled' => __( 'Ajax comments: ', 'odyssey' ),
       );
        return $id2label[ $option_id ];
    }

    static public function get_default_options() {
        return array(
            'comment_form_ajax_enabled' => true,
        );
    }

    public function get_options() {
        return get_option(self::OPTION_NAME, self::get_default_options());
    }

    public function get_option($s) {
        $options = $this->get_options();
        return $options[ $s ];
    }

    public function get_option_page() {
        $options = $this->get_options();
        if (isset($_POST[self::SUBMIT])) {
            $doUpdate = false;
            foreach ($options as $option => &$enabled) {
                // it's enabled now (as it is part of the POST), but wasn't enabled before -> update
                if (isset($_POST[ $option ] ) && ! $enabled) {
                    $enabled = true;
                    $doUpdate = true;
                // it's unenabled now (as it is not part of the POST), but was enabled before -> update
                } else if (!isset($_POST[ $option ] ) && $enabled) {
                    $enabled = false;
                    $doUpdate = true;
                }
            }
            $doUpdate && update_option(self::OPTION_NAME, $options);
        }

        $data = array();
        foreach ($options as $option => &$enabled) {
            $data[] = array('id' => $option, 'enabled' => $enabled, 'option' => self::option_id2label($option));
        }
        return Renderer::get_instance()->render('admin_comments', array(
            'options' => $data,
            'submit'   => self::SUBMIT,
        ));
    }

    public function comment_form($post_id = NULL) {
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? ' aria-required="true"' : '' );
        
        $args = array(
            'id_form'   => 'commentform',
            'id_submit' => 'comment_submit',
            'title_reply' => __( 'Leave a comment', 'odyssey' ),
        //     'title_reply_to' => __( 'Leave a Reply to %s' ),
        //     'cancel_reply_link' => __( 'Cancel Reply' ),
            'label_submit' => __( 'Post Comment', 'odyssey' ),
            'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
        //     'must_log_in' => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
        //     'logged_in_as' => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
            'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.', 'odyssey' ) . '</p>',
            'comment_notes_after' => '<p id="comment_status" ></p>',
        //     'fields' => apply_filters( 'comment_form_default_fields', array(
        //         'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
        //         'email' => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
        //         'url' => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'domainreference' ) . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>' ) )
        );
        comment_form($args, $post_id);
    }

    static private $cache;
    static private function cache_post_comments($post_id) {
        if (! isset(self::$cache[ $post_id ])) {
            $args = array(
                'post_id' => $post_id,
                'status'  => 'approve',
                'orderby' => 'comment_date',
                'order'   => 'ASC',
            );
    
            self::$cache[ $post_id ] = get_comments($args);
        }
        return self::$cache[ $post_id ];
    }

    public function get_post_comments_number($post_id) {
        return count(self::cache_post_comments($post_id));
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
        
        foreach(self::cache_post_comments($post_id) as $comment) {
            $c = array(
                'id'         => $comment->comment_ID,
                'author'     => $comment->comment_author,
                'author_url' => $comment->comment_author_url,
                'date'       => date_i18n(get_option('date_format'), strtotime($comment->comment_date)),
                'time'       => date_i18n(get_option('time_format'), strtotime($comment->comment_date)),
                'content'    => apply_filters('comment_text', $comment->comment_content),
                'leaf'       => true,
                'comments'   => array(),
                'avatar'     => get_avatar($comment, 30)
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
        if ( ! $this->get_option( 'comment_form_ajax_enabled' ) ) return;

        wp_list_comments( array(
            'style'       => 'ol',
            'short_ping'  => true,
            'avatar_size' => 74,
        ), array(get_comment($comment_ID)));
    }

    public function no_comment_redirection($location) {
        die();
    }
}

