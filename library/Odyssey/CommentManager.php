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
    const OPTION_NAME = 'odyssey_option_comment';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        Admin::get_instance()->register($this);
        add_action('admin_init', array(&$this, 'admin_init'));

        add_action('comment_post', array(&$this, 'comment_post'), 20, 2);
        add_filter('comment_post_redirect', array(&$this, 'no_comment_redirection'));
    }


    public function admin_init() {
        register_setting(Admin::OPTION_GROUP, self::OPTION_NAME);
        add_settings_section(
            self::OPTION_NAME,                // section id
            __('Comment Settings', 'odyssey'), // section title
            array(&$this, 'section_text'),    // callback to the function displaying the output of the section
            Admin::OPTION_PAGE           // menu page (slug of the theme setting page)
        );
        foreach($this->get_option() as $key => $value) {
            add_settings_field(
                $key,
                self::option_id2label($key),
                array(&$this, 'option_field'),
                Admin::OPTION_PAGE,       // menu page (slug of the theme setting page)
                self::OPTION_NAME,             // the option name it is recoreded into
                array('label_for' => $key, 'value' => $value)
            );
        }
    }
    public function section_text() {
        echo '<p>Comment settings</p>' . PHP_EOL;
    }
    public function get_option() {
        $default = self::get_default_options();
        $option = get_option(self::OPTION_NAME, self::get_default_options());
        if (empty($option)) {
            $option = array();
        }
        foreach($default as $key => $value) {
            if (array_key_exists($key, $option) && $option[$key] === true) {
                $default[$key] = true;
            } else {
                $default[$key] = false;
            }
        }

        return array_merge($default, $option);
    }

    function option_field($args) {
        echo '<input id="' . $args['label_for'] . '" ' .
            'name="' . self::OPTION_NAME . '[' . $args['label_for'] . ']" ' .
            'type="checkbox"' .
            ($args['value'] ? 'checked="checked"' : '') .
            ' />';
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

    public function get_post_comments_number($post_id) {
        $comments_count = wp_count_comments( $post_id );
        return $comments_count->approved;
    }

    /**
     * @returns the html list of post comments
     */
    public function get_post_comments($post_id) {
        $args = array(
            'post_id' => $post_id,
            'orderby' => 'comment_date',
            'order'   => 'ASC',
        );
    
        ob_start();
        wp_list_comments( array(
            'style'       => 'ol',
            'short_ping'  => true,
            'avatar_size' => 74,
        ), get_comments( $args ));


        $comments = ob_get_contents();
        ob_end_clean();
        
        return $comments;
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

