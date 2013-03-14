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
        foreach($months as $month) {
            if (!isset($ret[ $month->year ])) {
                $ret[ $month->year ] = array();
                $ret[ $month->year ]['total'] = 0;
            }
            $ret[ $month->year ][ $month->month ] = $month->post_count;
            $ret[ $month->year ]['total'] += $month->post_count;
        }
        return $ret;
    }
}

?>