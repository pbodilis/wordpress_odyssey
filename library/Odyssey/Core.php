<?php
/**
 * This file is part of Odyssey Theme for WordPress.
 */

/**
 *  Core functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage JavaScript
 */
class Odyssey_Core
{
    protected $jsHandle;
    protected $templateEngine;

    static private $instance;

    private function __construct(array $params = array())
    {
        $this->init($params);
    }

    static public function getInstance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function init(array $params = array())
    {
        if (isset($params['enable_js']) && $params['enable_js']) {
            $this->jsHandle = new Odyssey_Javascript();
        }
        if (isset($params['template_engine'])) {
            $this->templateEngine = $params['template_engine'];
        }
    }

    function embedJs()
    {
        if (isset($this->jsHandle)) {
            $this->jsHandle->embed();
        }
    }

    function render($template, $data)
    {
        $tpl = $this->templateEngine->loadTemplate($template);
        echo $tpl->render($data);
    }


    /**
     * \returns an array with the following info:
     */
    function getPost($id = NULL)
    {
        $ret = array();
    //     $postId = 6;
    //     $post = get_post($postId);
    // var_dump($_REQUEST);

        $post = current(get_posts(array(
    //         'order' => 'ASC',
            'limit' => 1
        )));



        $image = YapbImage::getInstanceFromDb($post->ID);
        if (!is_null($image)) { // that's a yapb post
            $post->image = $image;
            $ret['imageName'] = $post->image->uri;
        } // carry on as usual

        $ret['imageTitle'] = $post->post_title;
        return $ret;
    }
}

?>
