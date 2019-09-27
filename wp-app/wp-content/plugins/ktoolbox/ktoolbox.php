<?php
/**
Plugin Name: KToolbox
Plugin URI: https://bitbucket.org/drivek/ktoolbox/src/master/
Description: A set of namespaced utility classes you can use to develop your stuff.
Version: 1.0
Author: Riccardo Oliva <riccardo.oliva@drivek.com>
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /lang
Text Domain: ktoolbox
*/

defined( 'ABSPATH' ) || die( 'Error 403: Access Denied/Forbidden!' );
define( 'KTOOLBOX_PLUGIN_PATH', ( function_exists( 'plugin_dir_path' ) ? plugin_dir_path( __FILE__ ) : __DIR__ . '/' ) );

/**
 * Autoloader init
 */
if ( file_exists( KTOOLBOX_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once KTOOLBOX_PLUGIN_PATH . 'vendor/autoload.php';
}