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
                "post_status = 'publish' and post_date <= now( ) and post_type = 'post' GROUP BY month, year " .
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
                    'class'        => 'menu_level_1',
                    'extended'     => $current_year == $displayed_year ? 'extended' : '',
                    'link'         => get_year_link( $current_year ),
                    'menu_entries' => array(),
                )) - 1;
            }
            $archives[ $i ]['menu_entries'][] = array(
                'count'        => $month->post_count,
                'name'         => $wp_locale->get_month($month->month),
                'class'        => 'menu_level_2',
                'extended'     => $current_year == $displayed_year && $month->month == $displayed_month ? 'extended' : '',
                'link'         => get_month_link( $current_year, $month->month ),
                'menu_entries' => array(),
            );
            $archives[ $i ]['count'] += $month->post_count;
        }
        return array(
            'extended'     => 'extended',
            'title'        => __( 'Monthly Archives:', 'odyssey' ),
            'menu_entries' => $archives,
        );
    }

    public function get_categories() {
echo '<pre>' . PHP_EOL;
        $categories = get_categories();

        $cats = array();
        $i = -1;
        foreach($categories as $category) {
            $i = array_push($cats, array(
                'name'  => $category->cat_name,
                'count' => $category->category_count,
                'link'  => get_category_link( $category->term_id ),
            ));
        }
        var_dump($cats);
    }


    function get_monthly_archive_menu_rendering() {
// $this->get_categories();
        return Renderer::get_instance()->render(self::ARCHIVE_MENU_TEMPLATE_FILE, $this->get_monthly_archive_counts());
    }
}

?>