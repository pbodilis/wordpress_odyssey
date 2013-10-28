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
class Core {
    protected $admin;
    protected $exif_manager;
    protected $header_bar;
    protected $comment_manager;
    protected $archive_manager;
    protected $js_handle;

    protected $blog;

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
        $this->init($params);
    }

    public function init(array $params = array()) {
        $this->admin           = Admin::get_instance();
        $this->exif_manager    = ExifManager::get_instance();
        $this->header_bar      = HeaderBar::get_instance();
        $this->comment_manager = CommentManager::get_instance();

        $this->archive_manager = ArchiveManager::get_instance();

        $this->js_handle       = Javascript::get_instance();

        // add the templates related to the image and the content
        $this->js_handle->add_template('render_image',   'photoblog_image.mustache.html');
        $this->js_handle->add_template('render_content', 'photoblog_content.mustache.html');

        // set default stuff on this theme installation
        add_action('after_switch_theme', array(&$this, 'install'));
        
        // add couple of filters on body_class, and content
        add_filter('body_class',  array(&$this, 'body_class'));
//         add_filter('the_content', array(&$this, 'filter_content'));

        // the list of format support by the theme
        add_theme_support('post-formats', array('image', 'video'));
        add_theme_support('post-thumbnails');

        add_action('init', array(&$this, 'rmheadlink'));
    }

    public function rmheadlink() {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
    }

    public function install() {
        update_option('default_post_format', 'image');
    }

    function body_class($classes) {
        if(is_home() || is_single()) {
            // apend the current color chosen for the theme, to the $classes array
            $classes[] = isset($_COOKIE['odyssey_theme_color']) ? $_COOKIE['odyssey_theme_color'] : 'white';
         }
        return $classes;
    }

//     public function filter_content($content) {
//         switch (get_post_format()) {
//             case 'image':
//                 $content = strip_shortcodes($content);
//                 // ouch, now, that's a ugly hack :/
//                 // remove the image, the link and the paragraph in which the image is.
//                 $content = preg_replace('/<p\><a[^>]+\><img[^>]+\><\/a><\/p>/', '', $content, 1);
// //                 $content = preg_replace('/<p[^>]*>[\s|&nbsp;]*<\/p>/', '', $content);
//     //            $content = preg_replace('/(width|height)="\d*"\s/', '', $content);
//                 break;
//             default:
//                 break;
//         }
//         
//         return $content;
//     }


    /**
     * \returns various information about the blog, including:
     *  - name
     *  - uri
     *  - description
     */
    public function get_blog() {
        if (!isset($this->blog)) {
            $this->blog = new \stdClass();
            $this->blog->title             = wp_title('&raquo;', false);
            $this->blog->name              = get_bloginfo('name');
            $this->blog->home_url          = home_url('/');
            $this->blog->wpurl             = site_url('/');
            $this->blog->version           = get_bloginfo('version');
            $this->blog->html_type         = get_bloginfo('html_type');
            $this->blog->description       = get_bloginfo('description');
            $this->blog->stylesheet_url    = get_bloginfo('stylesheet_url');
            $this->blog->rss2_url          = get_bloginfo('rss2_url');
            $this->blog->comments_rss2_url = get_bloginfo('comments_rss2_url');
            $this->blog->atom_url          = get_bloginfo('atom_url');
            $this->blog->charset           = get_bloginfo('charset');
        }
        return $this->blog;
    }

    public function get_syndication() {
        return $this->header_bar->get_syndication();
    }
    public function get_other_links() {
        return $this->header_bar->get_other_links();
    }

    public function get_post_and_adjacents($post_id = NULL, $adjacent = 'both') {
        $current = $this->get_post($post_id);
        $ret = array(
            'current_ID' => $current->ID,
            $current->ID => $current,
        );
        if (('both' == $adjacent || 'next' == $adjacent) && isset($current->next_ID)) {
            $next = $this->get_post($current->next_ID);
            $ret[$next->ID] = $next;
        }
        if (('both' == $adjacent || 'previous' == $adjacent) && isset($current->previous_ID)) {
            $prev = $this->get_post($current->previous_ID);
            $ret[$prev->ID] = $prev;
        }
        return $ret;
    }

    /**
     * \returns an array with the following info:
     */
    protected $post_cache = array();
    public function get_post($post_id = NULL) {
        $ret = new \stdClass();
        if (is_null($post_id)) {
            if (isset($this->post_cache[-1])) {
                return $this->post_cache[-1];
            }
            global $query_string;
            query_posts( $query_string . 'posts_per_page=1' );
            if (have_posts()) {
                the_post();
            }
            global $post;
        } else if ('random' == $post_id) {
            global $post;
            $post = $this->get_random_post();
            if (isset($this->post_cache[$post->ID])) {
                return $this->post_cache[$post->ID];
            }
        } else {
            if (isset($this->post_cache[$post_id])) {
                return $this->post_cache[$post_id];
            }
            global $post;
            $post = get_post($post_id);
        }

        $ret->ID      = $post->ID;
        $ret->title   = $post->post_title;
        $ret->url     = get_permalink($post->ID);

        $after_more = explode('<!--more-->', $post->post_content);
        if (array_key_exists(1, $after_more) ) {
            $ret->content = apply_filters('the_content', $after_more[1]);
        } else {
            $ret->content = apply_filters('the_content', $after_more[0]);
        }

        $ret->class   = implode(' ', get_post_class('', $post->ID));

        $ret->format  = get_post_format($post->ID);
        if ($ret->format == 'image') {
            $ret->image = $this->get_post_image($post->ID);
        }

        $ret->comments_number = $this->comment_manager->get_post_comments_number($post->ID);
        $ret->comments        = $this->comment_manager->get_post_comments($post->ID);
        $ret->categories      = $this->get_post_categories($post->ID);
//      $ret = array_merge($ret, $this->get_post_image($post->ID));

        if (function_exists('the_ratings')) {
            $ret->ratings = the_ratings('div', $post->ID, false);
        }

        $next_post = get_next_post();
        if (!empty($next_post)) {
            $ret->next_ID    = $next_post->ID;
            $ret->next_title = $next_post->post_title;
            $ret->next_url   = get_permalink($next_post->ID);
        }

        $prev_post = get_previous_post();
        if (!empty($prev_post)) {
            $ret->previous_ID    = $prev_post->ID;
            $ret->previous_title = $prev_post->post_title;
            $ret->previous_url   = get_permalink($prev_post->ID);
        }

        wp_reset_query();
        return $ret;
    }

    /**
     * @return the first attached image of a post as the main post image
     */
    public function get_post_image($post_id) {
        $ret = new \stdClass();

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
            $ret->url    = $data[0];
            $ret->width  = $data[1];
            $ret->height = $data[2];

            $img_filename = get_attached_file($attachment->ID);
            $ret->capture_date = $this->exif_manager->get_capture_date($attachment->ID, $img_filename);
            $ret->exifs        = $this->exif_manager->get_image_exif($attachment->ID, $img_filename);
        }

        return $ret;
    }

    public function get_post_categories($post_id) {
        $ret = array();
        $categories = get_the_category($post_id);
        foreach($categories as $category) {
            $c = new \stdClass();
            $c->url = get_category_link( $category->term_id );
            $c->name = $category->cat_name;
            $ret[] = $c;
        }
        return $ret;
    }

    public function get_random_post_id() {
        if (!isset($this->random_post)) {
            $args = array(
                'posts_per_page'   => 1,
                'orderby'          => 'rand',
                'post_type'        => 'post',
                'post_status'      => 'publish',
                'suppress_filters' => true,
            );
            $posts = get_posts($args);
            if (empty($posts)) {
                $this->random_post = false;
            } else {
                $this->random_post = current($posts);
            }
        }
        return $this->random_post->ID;
    }

    public function get_random_post_url() {
        $post_id = $this->get_random_post_id();
        if (false !== $post_id) {
            $r = new \stdClass();
            $r->url     = get_permalink($post_id);
            $r->title   = __( 'Random post', 'odyssey' );
            $r->name    = __( 'Random', 'odyssey' );
            $r->classes = 'random';
            return $r;
        } else {
            return false;
        }
    }

    public function get_archives_url() {
        $displayed_year = get_the_time('Y');
        $displayed_month = get_the_time('m');

        $a = new \stdClass();
        $a->url     = $this->archive_manager->get_link_to_most_recent_archive();
        $a->title   = __( 'Archives', 'odyssey' );
        $a->name    = __( 'Archives', 'odyssey' );
        $a->classes = 'archives';
        return $a;
    }

    public function get_page_list() {
        $args = array(
            'sort_order'   => 'ASC',
            'sort_column'  => 'ID',
            'hierarchical' => 1,
            'parent'       => -1,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'title_li'     => '<p id="page_list_title">' . __('Pages') . '</p>',
            'echo'         => 0,
        );
        return wp_list_pages( $args );
    }

    public function comment_form($post_id = NULL) {
        $this->comment_manager->comment_form($post_id);
    }

    public function get_monthly_archive_counts() {
        return $this->archive_manager->get_monthly_archive_counts();
    }
    public function get_rendered_monthly_archive_menu() {
        return $this->archive_manager->get_monthly_archive_menu_rendering();
    }
}

?>
