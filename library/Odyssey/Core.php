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

    protected $postCache;

    private function __construct(array $params = array())
    {
        $this->init($params);

        $this->postCache = array();
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
    public function getPost($postId = NULL, $getAdjacent = true)
    {
        $ret = array();

        if (is_null($postId)) {
            if (is_null($url)) {
                $post = current(get_posts(array(
                    //'order' => 'ASC',
                    'limit' => 1
                )));
            } else {
                $postId = url_to_postid($url);
                $post = get_post($postId);
            }
        } else {
            $post = get_post($postId);
        }

        if (isset($this->postCache[$postId])) {
            return $this->postCache[$postId];
        }

// //             if (have_posts()) {
// //                 while (have_posts())
// //                     the_post();
// //             }
//         } else {
//         }

        $ret['image'] = $this->getPostImage($post->ID);
//		$ret = array_merge($ret, $this->getPostImage($post->ID));

        $ret['postTitle'] = $post->post_title;
        $ret['postUri']   = get_permalink($post->ID);
//echo"<pre>";
//var_dump($post);

        if ($getAdjacent) {
            $nextPost = get_next_post();
//var_dump($nextPost);
            if (!empty($nextPost)) {
                $ret['next'] = $this->getPost($nextPost->ID, false);
            }

            $prevPost = get_previous_post();
//var_dump($prevPost);
            if (!empty($prevPost)) {
                $ret['previous'] = $this->getPost($prevPost->ID, false);
            }
        }
        return $ret;
    }

    public function getPostImage($postId)
    {
        $ret = array();
        $image = \YapbImage::getInstanceFromDb($postId);
        if (!is_null($image)) { // that's a yapb post
            $ret['uri']    = $image->uri;
            $ret['width']  = $image->width;
            $ret['height'] = $image->height;

           $ret['exif'] = $this->getPostImageExif($image);
        }
        return $ret;
    }

    public function getPostImageExif($image)
    {
        $exifs = \ExifUtils::getExifData($image);
        if (isset($exifs['DateTime'])) {
            $d = new \DateTime($exifs['DateTime']);
            $exifs['captureDate'] = $d->format('Y-m-d');
        }
        return $exifs;
    }

}

?>
