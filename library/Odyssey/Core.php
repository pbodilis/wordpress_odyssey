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
    protected $admin;
    protected $exifManager;
    protected $jsHandle;
    protected $renderer;

    protected $postCache;

    static private $instance;
    static public function getInstance(array $params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array())
    {
        $this->init($params);

        $this->postCache = array();
    }

    public function init(array $params = array())
    {
        $this->admin       = Admin::getInstance();
        $this->exifManager = ExifManager::getInstance();
        $this->renderer    = Renderer::getInstance();
        $this->social      = Social::getInstance();
        if (isset($params['enable_js']) && $params['enable_js']) {
            $this->jsHandle = Javascript::getInstance();
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
        echo $this->renderer->render($template, $data);
    }

    /**
     * \returns various information about the blog, including:
     *  - name
     *  - uri
     *  - description
     */
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
        $ret['image']    = $this->getPostImage($post->ID);
        $ret['comments'] = $this->getPostComments($post->ID);
//      $ret = array_merge($ret, $this->getPostImage($post->ID));

        $ret['title']   = $post->post_title;
        $ret['url']     = get_permalink();
        $ret['ID']      = $post->ID;
        $ret['content'] = apply_filters('the_content', $post->post_content);

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

    /**
     * @return the first attached image of a post as the main post image
     */
    public function getPostImage($postId)
    {
        $ret = array();

        $args = array(
            'numberposts'    => 1,
            'order'          => 'ASC',
            'post_mime_type' => 'image',
            'post_parent'    => $postId,
            'post_status'    => null,
            'post_type'      => 'attachment'
        );

        $attachments = get_children( $args );
        if ($attachments) {
            $attachment = current($attachments);
            $data = wp_get_attachment_image_src($attachment->ID, 'full');
            $ret['url']    = $data[0];
            $ret['width']  = $data[1];
            $ret['height'] = $data[2];

            $imgFilename = get_attached_file($attachment->ID);
            $ret['captureDate'] = $this->exifManager->getCaptureDate($imgFilename);
            $ret['exifs']       = $this->exifManager->getImageExif($imgFilename);
        }

        return $ret;
    }

    public function getPostComments($postId)
    {
        $ret = array();

        $args = array(
            'post_id' => $postId,
            'status'  => 'approve',
        );

        $comments = get_comments($args);
        foreach($comments as $comment) {
            $ret[] = array(
                'author'    => $comment->comment_author,
                'authorUrl' => $comment->comment_author_url,
                'date'      => $comment->comment_date,
                'content'   => $comment->comment_content,
            );
        }
        
//         var_dump($ret);
        return $ret;
    }

    public function getPanelState()
    {
        return array(
            'panelClass' => isset($_COOKIE['odyssey_theme_panelVisibility']) ? $_COOKIE['odyssey_theme_panelVisibility'] : '',
        );
    }
}

?>
