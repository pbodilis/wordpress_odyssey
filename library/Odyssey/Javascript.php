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
    public function __construct()
    {
        // add the callbacks
        add_action('wp_ajax_odyssey_get_json_post',        array(&$this, 'getJsonPost'));
        add_action('wp_ajax_nopriv_odyssey_get_json_post', array(&$this, 'getJsonPost'));
    }

    /**
     * Injects the HTML markup for the JavaScript files used by Odyssey
     *
     * @since 0.1
     */
    public function embedJavascript()
    {
        // template engine
        wp_enqueue_script('odyssey-mustache' ,    get_template_directory_uri() . '/js/mustache.js', array('jquery'));
        wp_enqueue_script('odyssey-chevron',      get_template_directory_uri() . '/js/chevron.js',  array('jquery'));

        // pub sub implementation
        wp_enqueue_script('odyssey-pubsub',       get_template_directory_uri() . '/js/ba-tiny-pubsub.js', array('jquery'));

        // embed the javascript file that makes the AJAX request
        wp_enqueue_script('odyssey-core',         get_template_directory_uri() . '/js/odyssey.core.js',  array('jquery'));
        wp_enqueue_script('odyssey-image',        get_template_directory_uri() . '/js/odyssey.image.js', array('jquery'));
        wp_enqueue_script('odyssey-keyboard',     get_template_directory_uri() . '/js/odyssey.keyboard.js', array('jquery'));
        wp_enqueue_script('odyssey',              get_template_directory_uri() . '/js/odyssey.js',       array('jquery'));

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        wp_localize_script('odyssey-core', 'odyssey', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'postStr' => json_encode(Core::getInstance()->getPost()),
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

    public function embedTemplates()
    {
        $ret = '';
        $tpls = array(
            'photoblog_image' => 'photoblog_image.mustache.html',
        );
        $tplDir = get_template_directory_uri() . '/templates/';
        foreach($tpls as $tplName => $tplFile) {
            $ret .= '<link href="' . $tplDir . $tplFile . '" rel="template" id="' . $tplName . '"/>' . PHP_EOL;
        }
        return $ret;
    }
        


    /**
     * \returns a JSON array
     */
    public function getJsonPost($id) {
        // find something better than direct access to $_GET/$_POST
        $ret = Core::getInstance()->getPost($_GET['id']);
        echo json_encode($ret);
        die();
    }
}

?>