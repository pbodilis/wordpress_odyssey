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
 *  Core functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage JavaScript
 */
class Core
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
            $this->jsHandle = new Javascript();
        }
        if (isset($params['template_engine'])) {
            $this->templateEngine = $params['template_engine'];
        }
    }

    public function embedJavascript()
    {
        if (isset($this->jsHandle)) {
            $this->jsHandle->embedJavascript();
        }
    }

    public function embedTemplates()
    {
        if (isset($this->jsHandle)) {
            echo $this->jsHandle->embedTemplates();
        }
    }

    public function render($template, $data)
    {
        $tpl = $this->templateEngine->loadTemplate($template);
        echo $tpl->render($data);
    }


    /**
     * \returns an array with the following info:
     */
    public function getPost($id = NULL, $getAdjacent = true)
    {
        $ret = array();

        if (is_null($id)) {
            $post = current(get_posts(array(
//                 'order' => 'ASC',
                'limit' => 1
            )));
        } else {
            $post = get_post($postId);
        }

        $image = \YapbImage::getInstanceFromDb($post->ID);
        if (!is_null($image)) { // that's a yapb post
            $post->image = $image;
            $ret['imageUri'] = $post->image->uri;
        } // carry on as usual
//             $next = get_next_post();
//             $previous = get_previous_post();

        $ret['postTitle'] = $post->post_title;
        $ret['postUri']   = get_permalink($post->ID);

        if ($getAdjacent) {
            $nextPost = get_next_post();
            if (!empty($nextPost)) {
                $ret['next'] = $this->getPost($nextPost->ID, false);
            }

            $prevPost = get_previous_post();
            if (!empty($prevPost)) {
                $ret['previous'] = $this->getPost($prevPost->ID, false);
            }
        }
        return $ret;
    }
}

?>
