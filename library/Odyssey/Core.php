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
 *  @subpackage Core
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
     * \returns various information about the blog, including:
     *  - name
     *  - uri
     *  - description
	 *      */
    public function getBlog()
    {
		$ret = array(
		    'title'             => wp_title('&raquo;', false),
		    'name'              => get_bloginfo('name'),
		    'url'               => home_url('/'),
		    'wpurl'             => site_url('/'),
		    'version'           => get_bloginfo('version'),
		    'html_type'         => get_bloginfo('html_type'),
		    'description'       => get_bloginfo('description'),
		    'stylesheet_url'    => get_bloginfo('stylesheet_url'),
		    'rss2_url'          => get_bloginfo('rss2_url'),
		    'comments_rss2_url' => get_bloginfo('comments_rss2_url'),
		    'atom_url'          => get_bloginfo('atom_url'),
		    'charset'           => get_bloginfo('charset'),
		);
		return $ret;
    }

    public function getPostAndAdjacents($postId = NULL)
    {
        $current = $this->getPost($postId);
        $ret = array(
            'currentID'    => $current['ID'],
            $current['ID'] => $current,
        );
        if (isset($current['nextID'])) {
            $next = $this->getPost($current['nextID']);
            $ret[$next['ID']] = $next;
        }
        if (isset($current['previousID'])) {
            $prev = $this->getPost($current['previousID']);
            $ret[$prev['ID']] = $prev;
        }
        return $ret;
    }

    /**
     * \returns an array with the following info:
     */
    public function getPost($postId = NULL)
    {
        $ret = array();

        if (is_null($postId)) {
            if (have_posts()) {
                the_post();
            }
            global $post;
        } else {
            if (isset($this->postCache[$postId])) { // we already gather info for this post
                return $this->postCache[$postId];   // return the cached data
            }
            global $post;
            $post = get_post($postId);
        }

        $ret['image'] = $this->getPostImage($post->ID);
//      $ret = array_merge($ret, $this->getPostImage($post->ID));

        $ret['title'] = $post->post_title;
        $ret['uri']   = get_permalink($post->ID);
        $ret['ID']    = $post->ID;

        $nextPost = get_next_post();
        if (!empty($nextPost)) {
            $ret['nextID'] = $nextPost->ID;
        $this->postCache[$post->ID] = $ret;
        }

        $prevPost = get_previous_post();
        if (!empty($prevPost)) {
            $ret['previousID'] = $prevPost->ID;
        }

        $this->postCache[$post->ID] = $ret;
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
