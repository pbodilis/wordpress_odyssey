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
 *  ArchiveManager options functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage ArchiveManager
 */
class ArchiveManager
{
    const ARCHIVE_MENU_TEMPLATE_FILE = 'archive_menu';

    static private $instance;
    static public function get_instance(array $params = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    public function __construct(array $params = array()) {
    }

    public function get_link_to_most_recent_archive() {
        global $wpdb;
        global $wp_locale;
        $limit = 0;
        $year_prev = null;
        $month = $wpdb->get_results(
            "SELECT " .
                "MONTH( post_date ) AS month, " .
                "YEAR( post_date ) AS year " .
            "FROM $wpdb->posts " .
            "WHERE " .
                "post_status = 'publish' AND " .
                "post_date <= now( ) AND " .
                "post_type = 'post' " .
            "ORDER BY post_date DESC " .
            "LIMIT 1"
        );
        return get_month_link( $month[0]->year, $month[0]->month );
    }
    
    public function get_monthly_archive_counts() {
        global $wpdb;
        global $wp_locale;
        $limit = 0;
        $year_prev = null;
        $months = $wpdb->get_results(
            "SELECT " .
                "DISTINCT MONTH( post_date ) AS month, " .
                "YEAR( post_date ) AS year, " .
                "COUNT( id ) as post_count " .
            "FROM $wpdb->posts " .
            "WHERE " .
                "post_status = 'publish' AND " .
                "post_date <= now( ) AND " .
                "post_type = 'post' " .
            "GROUP BY month, year " .
            "ORDER BY post_date DESC"
        );

        $displayed_year = get_the_time('Y');
        $displayed_month = get_the_time('m');
        $archives = array();
        $i = -1;
        $current_year = -1;
        foreach($months as $month) {
            if ($current_year != $month->year ) {
                $current_year = $month->year;
                $i = array_push($archives, array(
                    'count'        => 0,
                    'name'         => $current_year,
                    'extended'     => !is_category() && $current_year == $displayed_year ? 'extended' : '',
                    'link'         => get_year_link( $current_year ),
                    'menu_entries' => array(),
                )) - 1;
            }
            $archives[ $i ]['menu_entries'][] = array(
                'count'        => $month->post_count,
                'name'         => $wp_locale->get_month($month->month),
                'extended'     => !is_category() && $current_year == $displayed_year && $month->month == $displayed_month ? 'extended' : '',
                'link'         => get_month_link( $current_year, $month->month ),
                'menu_entries' => array(),
            );
            $archives[ $i ]['count'] += $month->post_count;
        }
        return array(
            'extended'     => 'extended',
            'title'        => __( 'Archives:', 'odyssey' ),
            'menu_entries' => $archives,
        );
    }

    /**
     * @returns array of parent categories id
     */
    private function get_parent_categories($id) {
        if (0 == $id) {
            return array(0);
        }

        $parent = get_category( $id );
        return array_merge($this->get_parent_categories($parent->category_parent), array(intval($id)));
    }

    public function get_categories() {
        $parent_cats = $this->get_parent_categories(get_query_var('cat'));

        $categories = get_categories(array(
            'hide_empty' => false,
            'orderby'    => 'id',
        ));
        $cats = array();
        $i = -1;
        $l = array();
        foreach($categories as $category) {
            if ($category->category_parent == 0) {
                $tmp =& $cats;
            } else {
                $tmp =& $l[$category->category_parent]['menu_entries'];
            }
            $i = array_push($tmp, array(
                'count'        => $category->category_count,
                'name'         => $category->cat_name,
                'extended'     => in_array($category->cat_ID, $parent_cats) ? 'extended' : '',
                'link'         => get_category_link( $category->term_id ),
                'menu_entries' => array(),
            )) - 1;
            $l[$category->cat_ID] =& $tmp[$i];
        }
        return array(
            'extended'     => 'extended',
            'title'        => __( 'Categories:', 'odyssey' ),
            'menu_entries' => $cats,
        );
    }


    function get_monthly_archive_menu_rendering() {
        $archives_section = array(
            $this->get_monthly_archive_counts(),
            $this->get_categories()
        );
        
        echo '<div id="archives_menu">' . PHP_EOL;
        echo '    <ul>' . PHP_EOL;
        foreach($archives_section as $archive_section) {
            echo '        <li>' . PHP_EOL;
            echo '            <span class="menu_head">' . PHP_EOL;
            echo '                <h3>' . $archive_section['title'] . '</h3>' . PHP_EOL;
            echo '            </span>' . PHP_EOL;
            $this->get_sub_archive_menu_rendering($archive_section);
            echo '        </li>' . PHP_EOL;
        }
        echo '    </ul>' . PHP_EOL;
        echo '</div>' . PHP_EOL;
    }

    function get_sub_archive_menu_rendering($top_menu_entry) {
        echo '            <ul class="menu_content ' . $top_menu_entry['extended'] . '">' . PHP_EOL;
        foreach($top_menu_entry['menu_entries'] as $menu_entry) {
            echo '    <li class="menu ' . $menu_entry['extended'] . '">' . PHP_EOL;
            echo '        <span class="menu_head">' . PHP_EOL;
            if (!empty($menu_entry['menu_entries'])) {
                echo '            <span class="menu_extend"><span class="menu_extend_arrow"></span></span>' . PHP_EOL;
            }

            if (array_key_exists('count', $menu_entry)) {
                echo '            <a href="' . $menu_entry['link'] . '"> ' . $menu_entry['name'];
                echo ' <span class="post_count">' . $menu_entry['count'] . '</span></a>' . PHP_EOL;
            } else {
                echo $archive_section['name'] . PHP_EOL;
            }
            echo '        </span>' . PHP_EOL;
            $this->get_sub_archive_menu_rendering($menu_entry);
            echo '    </li>' . PHP_EOL;
        }
        echo '</ul>' . PHP_EOL;
    }

}

?>