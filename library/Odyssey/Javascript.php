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
 *  JavaScript helper functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage JavaScript
 */
class Javascript
{
    const POST_NONCE_EMBEDNAME = 'post_nonce';
    const POST_NONCE           = 'oddyssey-ajax-post-nonce';

    const COMMENTS_NONCE_EMBEDNAME = 'comments_nonce';
    const COMMENTS_NONCE           = 'oddyssey-ajax-comments-nonce';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct() {
        // add the callbacks
        add_action('wp_ajax_odyssey_get_json_post_and_adjacents',        array(&$this, 'get_json_post_and_adjacents'));
        add_action('wp_ajax_nopriv_odyssey_get_json_post_and_adjacents', array(&$this, 'get_json_post_and_adjacents'));
        add_action('wp_ajax_odyssey_get_json_post',                      array(&$this, 'get_json_post'));
        add_action('wp_ajax_nopriv_odyssey_get_json_post',               array(&$this, 'get_json_post'));

        add_action('wp_head',            array(&$this, 'enqueue_templates'), 0);
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_javascript'));
    }

    /**
     * Injects the HTML markup for the JavaScript files used by Odyssey
     *
     * @since 0.1
     */
    public function enqueue_javascript() {
        // template engine
        wp_enqueue_script('mustache',            get_template_directory_uri() . '/js/mustache.js',            array('jquery'));
        wp_enqueue_script('chevron',             get_template_directory_uri() . '/js/chevron.js',             array('jquery'));

        // pub sub implementation
        wp_enqueue_script('pubsub',              get_template_directory_uri() . '/js/ba-tiny-pubsub.js',      array('jquery'));

        // js hsitory management
        wp_enqueue_script('history',             get_template_directory_uri() . '/js/history.js');
        wp_enqueue_script('history-adapter',     get_template_directory_uri() . '/js/history.adapter.native.js');

        // embed the javascript file that makes the AJAX request
        wp_enqueue_script('odyssey-core',        get_template_directory_uri() . '/js/odyssey.core.js',        array('jquery'), false, true);
        wp_enqueue_script('odyssey-cookie',      get_template_directory_uri() . '/js/odyssey.cookie.js',      array('jquery'), false, true);
        wp_enqueue_script('odyssey-image',       get_template_directory_uri() . '/js/odyssey.image.js',       array('jquery'), false, true);
        wp_enqueue_script('odyssey-header',      get_template_directory_uri() . '/js/odyssey.header.js',      array('jquery'), false, true);
        wp_enqueue_script('odyssey-panel',       get_template_directory_uri() . '/js/odyssey.panel.js',       array('jquery'), false, true);
        wp_enqueue_script('odyssey-keyboard',    get_template_directory_uri() . '/js/odyssey.keyboard.js',    array('jquery'), false, true);
        wp_enqueue_script('odyssey-history',     get_template_directory_uri() . '/js/odyssey.history.js',     array('history', 'history-adapter'), false, true);
        wp_enqueue_script('odyssey-navigation',  get_template_directory_uri() . '/js/odyssey.navigation.js',  array('jquery'), false, true);
        wp_enqueue_script('odyssey-commentform', get_template_directory_uri() . '/js/odyssey.commentform.js', array('jquery', 'comment-reply'), false, true);
        wp_enqueue_script('odyssey',             get_template_directory_uri() . '/js/odyssey.js',             array('jquery'), false, true);

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        wp_localize_script('odyssey-core', 'odyssey', array(
            'ajaxurl'                   => admin_url('admin-ajax.php'),
            'posts'                     => json_encode(Core::get_instance()->get_post_and_adjacents()),
            'comment_form_ajax_enabled' => json_encode(CommentManager::get_instance()->get_setting('comment_form_ajax_enabled')),
            self::POST_NONCE_EMBEDNAME  => wp_create_nonce(self::POST_NONCE),
        ));
//         <script type="text/javascript">
//             var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
//             document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//         </script>
//         <script type="text/javascript">
//             var pageTracker = _gat._getTracker("UA-3348687-1");
//             pageTracker._initData();
//             pageTracker._trackPageview();
//         </script>
    }

    public function enqueue_templates() {
        $ret = '';
        $tpls = array(
            'photoblog_image'    => 'photoblog_image.mustache.html',
            'photoblog_content'  => 'photoblog_content.mustache.html',
            'photoblog_comments' => 'photoblog_comments.mustache.html',
        );
        $tplDir = get_template_directory_uri() . '/templates/';
        foreach($tpls as $tplName => $tplFile) {
            $ret .= '<link href="' . $tplDir . $tplFile . '" rel="template" id="' . $tplName . '"/>' . PHP_EOL;
        }
        echo $ret;
    }
        
    /**
     * \returns a JSON array
     */
    public function get_json_post() {
        $nonce = isset($_GET[self::POST_NONCE_EMBEDNAME]) ? $_GET[self::POST_NONCE_EMBEDNAME] : null;
        // check to see if the submitted nonce matches with the generated nonce we created earlier
        if (!wp_verify_nonce($nonce, self::POST_NONCE))
            die (json_encode(false));

        // find something better than direct access to $_GET/$_POST
        $ret = array(
            'post'                     => Core::get_instance()->get_post($_GET['id']),
            self::POST_NONCE_EMBEDNAME => wp_create_nonce(self::POST_NONCE),
        );
        echo json_encode($ret);
        die();
    }

    /**
     * \returns a JSON array
     */
    public function get_json_post_and_adjacents() {
        $nonce = isset($_GET[self::POST_NONCE_EMBEDNAME]) ? $_GET[self::POST_NONCE_EMBEDNAME] : null;
        // check to see if the submitted nonce matches with the generated nonce we created earlier
        if (!wp_verify_nonce($nonce, self::POST_NONCE))
            die (json_encode(false));

        // find something better than direct access to $_GET/$_POST
        $ret = array(
            'posts'                    => Core::get_instance()->get_post_and_adjacents($_GET['id']),
            self::POST_NONCE_EMBEDNAME => wp_create_nonce(self::POST_NONCE),
        );
        echo json_encode($ret);
        die();
    }
}

?>