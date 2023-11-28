<?php
/**
 * Plugin Name:     Generic Post Types for Theater
 * Plugin URI:      https://wp.theater
 * Description:     Feature plugin to test the concept of generic post types in the Theater for WordPress plugin.
 * Author:          Slim & Dapper
 * Author URI:      https://slimndap.com
 * Version:         1.0
 * Text Domain: 	Theater_Generic_Post_Types
 */

/**
 * Bail if called directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    return;
}

define( 'Theater\Generic_Post_Types\VERSION', '1.23' );
define( 'Theater\Generic_Post_Types\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'Theater\Generic_Post_Types\PLUGIN_URI', plugin_dir_url( __FILE__ ) );

include_once \Theater\Generic_Post_Types\PLUGIN_PATH.'includes/Generic_Post_Types.php';