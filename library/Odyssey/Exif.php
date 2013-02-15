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
 *  Exif helper functions
 *  @package Odyssey Theme for WordPress
 *  @subpackage Exif
 */
class Exif
{
    public function __construct()
    {
        // add the callbacks
    }

// This tells WordPress to call the function named "setup_theme_admin_menus"
// when it's time to create the menu pages.
add_action("admin_menu", "setup_theme_admin_menus");

function setup_theme_admin_menus() {
    add_menu_page(
        'Odyssey theme settings',  // page_title
        'Odyssey theme settings',  // menu_title
        'manage_options',          // capability
        'odyssey_settings',        // menu_slug
        'odyssey_settings');       // renderer

    add_submenu_page(
        'odyssey_settings',        // parent_slug
        'Exif settings',           // page_title
        'Exif settings',           // menu_title
        'manage_options',          // capability
        'odyssey-settings-exifs',  // menu_slug
        'odyssey_settings_exifs'); // renderer
}

function odyssey_settings() {
    $output = '';
    $output .= '<h2>PROUT</h2>';
    echo $output;
}

function odyssey_settings_exifs() {
    $output = '';
    $output .= '<h2>Display Exif Settings</h2>';
    $output .= '<form action="" method="post" id="display_exif_form" style="margin: auto; width: 600px; ">';
    $output .= '<hr size="1" />';
    $output .= '<span style="font-weight: bold;" >&bull; Items to display:</span><br />';

    $display_exif_switches = display_exif_get_default_option_value();
    foreach( $display_exif_switches as $key => $value ) {
        $op_value = $value[ 0 ];
        $op_title = $value[ 1 ];

        $output .= '<input id="' . $key . '" name="' . $key . '" type="checkbox" value="1" ';
        if( $op_value ) $output .= 'checked';
        $output .= ' style="margin-left: 10px;" /> ' . $op_title . '<br />';
    } /* foreach */
    $output .= '*Only valid data will be displayed.<br />';
    $output .= '<hr size="1" />';
    $output .= '<p class="submit"><input type="submit" name="display_exif_submit" value="'. 'Update options &raquo;' .'" /></p>';
    $output .= '</form>';

    echo $output;
}

function display_exif_get_default_option_value() {
    $display_exif_switches_default_value = array(
        'Make' => array( 1, 'Manufacturer' ),
        'Model'  => array( 1, 'Model Name' ),
        'DateTimeOriginal' => array( 0, 'Date' ),
        'ExposureProgram' => array( 1, 'Exposure Program' ),
        'ExposureTime' => array( 1, 'Exposure Time' ),
        'FNumber' => array( 1, 'F Number' ),
        'ISOSpeedRatings' => array( 1, 'ISO' ),
        'FocalLength' => array( 1, 'Focal Length' ),
        'MeteringMode' => array( 1, 'Metering Mode' ),
        'LightSource' => array( 0, 'Light Source' ),
        'SensingMethod' => array( 0, 'Sensing Method' ),
        'ExposureMode' => array( 0, 'Exposure Mode' ),

        'FileName' => array( 0, 'File Name' ),
        'FileSize' => array( 0, 'File Size' ),
        'Software' => array( 0, 'Software' ),
        'XResolution' => array( 0, 'X Resolution' ),
        'YResolution' => array( 0, 'Y Resolution' ),

        'ExifVersion' => array( 0, 'Exif Version' ),

        'title' => array( 1, 'Title' )
    );
    return( $display_exif_switches_default_value );
}
}

?>