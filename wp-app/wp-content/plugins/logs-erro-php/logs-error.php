<?php
/*
Plugin Name: Logs Error in PHP
Plugin URI: https://hotelilbelvedere.com
Description: Check if there are error in logs for PHP
Version: 0.1
Author: marrano92
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: logs-erro-php
*/

/********************************
 * Globals definitions *
 *******************************/

defined( 'ABSPATH' ) || die( 'Error 403: Access Denied/Forbidden!' );
defined( 'HOUR_IN_SECONDS' ) || define( 'HOUR_IN_SECONDS', 3600 );
define( 'ERRLOGS_PLUGIN_DIR', ( function_exists( 'plugin_dir_path' ) ? plugin_dir_path( __FILE__ ) : __DIR__ . '/' ) );