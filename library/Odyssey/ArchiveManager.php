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

        $ret = array();
        $i = -1;
        $current_year = -1;
        foreach($months as $month) {
            if ($current_year != $month->year ) {
                $current_year = $month->year;
                $i = array_push($ret, array(
                    'count'  => 0,
                    'year'   => $current_year,
                    'link'   => get_year_link( $current_year ),
                    'months' => array(),
                )) - 1;
            }
            $ret[ $i ]['months'][] = array(
                'count' => $month->post_count,
                'month' => $wp_locale->get_month($month->month),
                'link'  => get_month_link( $current_year, $month->month ),
            );
            $ret[ $i ]['count'] += $month->post_count;
        }
        return $ret;
    }

    function get_monthly_archive_menu_rendering() {
        return Renderer::get_instance()->render(self::ARCHIVE_MENU_TEMPLATE_FILE, array('archives' => $this->get_monthly_archive_counts()));
    }
}

?>