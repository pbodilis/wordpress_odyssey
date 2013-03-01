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
    protected $exif_manager;
    protected $renderer;
    protected $header_bar;
    protected $comment_manager;
    protected $js_handle;

    protected $postCache;

    protected $blog;

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
        $this->admin           = Admin::getInstance();
        $this->exif_manager    = ExifManager::getInstance();
        $this->renderer        = Renderer::getInstance();
        $this->header_bar      = HeaderBar::getInstance();
        $this->comment_manager = CommentManager::getInstance();
        $this->js_handle       = Javascript::getInstance();

        add_action('after_switch_theme', array(&$this, 'install'));

        add_theme_support('post-formats', array('image', 'video'));
    }


    public function install()
    {
        update_option('default_post_format', 'image');
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
        if (!isset($this->blog)) {
            $this->blog = array(
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
        }
        return $this->blog;
    }

    public function get_post_and_adjacents($post_id = NULL)
    {
        $current = $this->get_post($post_id);
        $ret = array(
            'currentID'    => $current['ID'],
            $current['ID'] => $current,
        );
        if (isset($current['nextID'])) {
            $next = $this->get_post($current['nextID']);
            $ret[$next['ID']] = $next;
        }
        if (isset($current['previousID'])) {
            $prev = $this->get_post($current['previousID']);
            $ret[$prev['ID']] = $prev;
        }
        return $ret;
    }

    /**
     * \returns an array with the following info:
     */
    public function get_post($post_id = NULL)
    {
        $ret = array();

        if (is_null($post_id)) {
            if (have_posts()) {
                the_post();
            }
            global $post;
            if (isset($this->postCache[$post->ID])) { // we already gather info for this post
                return $this->postCache[$post->ID];   // return the cached data
            }
        } else {
            if (isset($this->postCache[$post_id])) { // we already gather info for this post
                return $this->postCache[$post_id];   // return the cached data
            }
            global $post;
            $post = get_post($post_id);
        }
        $ret['image']    = $this->get_post_image($post->ID);
        $ret['comments'] = $this->comment_manager->get_post_comments($post->ID);
//      $ret = array_merge($ret, $this->get_post_image($post->ID));

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
    public function get_post_image($post_id)
    {
        $ret = array();

        $args = array(
            'numberposts'    => 1,
            'order'          => 'ASC',
            'post_mime_type' => 'image',
            'post_parent'    => $post_id,
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

            $img_filename = get_attached_file($attachment->ID);
            $ret['capture_date'] = $this->exif_manager->get_capture_date($attachment->ID, $img_filename);
            $ret['exifs']        = $this->exif_manager->get_image_exif($attachment->ID, $img_filename);
        }


        return $ret;
    }

    public function get_random_post()
    {
        if (!isset($this->randomPost)) {
            $args = array(
                'posts_per_page'  => 1,
                'orderby'         => 'rand',
                'post_type'       => 'post',
                'post_status'     => 'publish',
                'suppress_filters' => true,
            );
            $posts = get_posts($args);
            if (empty($posts)) {
                $this->randomPost = false;
            } else {
                $this->randomPost = current($posts);
            }
        }
        return $this->randomPost;
    }

    public function get_random_post_url()
    {
        $post = $this->get_random_post();
        if ($post !== false) {
            return $post->guid;
        } else {
            return false;
        }
    }

    public function getPages()
    {
        return get_pages();
    }

    public function getRenderedHeaderBar() {
        return $this->header_bar->getRendering();
    }
    public function get_comment_form() {
        return $this->comment_manager->get_comment_form();
    }
}

?>
