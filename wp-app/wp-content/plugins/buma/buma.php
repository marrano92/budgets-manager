<?php
/*
Plugin Name: Buma
Plugin URI: https://motor.budgetmanager.com
Description: Manage Budgets in MotorK
Version: 0.1
Author: marrano92
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: buma
*/

/********************************
 * Globals definitions *
 *******************************/

defined( 'ABSPATH' ) || die( 'Error 403: Access Denied/Forbidden!' );
defined( 'HOUR_IN_SECONDS' ) || define( 'HOUR_IN_SECONDS', 3600 );
define( 'Buma_PLUGIN_DIR', ( function_exists( 'plugin_dir_path' ) ? plugin_dir_path( __FILE__ ) : __DIR__ . '/' ) );

/**
 * Autoloader init
 */
if ( file_exists( Buma_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once Buma_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * Shorthand for a PluginOptions instance
 *
 * @return \Buma\PluginOptions
 */
function options_factory() {
	$locale = \Buma\Locale::create();

	return \Buma\PluginOptions::create( $locale );
}

/**
 * Init action
 *
 * @return void
 */
add_action( 'init', function () {
	/**
	 * WP actions removal
	 */
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );

	/**
	 * Plugin builders
	 */
	\Buma\Cpt\Builder::init();
	\Buma\Route\Builder::init();

});

/**
 * Admin menu action
 *
 * @return void
 */
add_action( 'admin_menu', function () {
	\Buma\OptionsPage::init( options_factory() );
} );