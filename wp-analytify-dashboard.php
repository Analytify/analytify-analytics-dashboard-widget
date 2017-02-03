<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Plugin Name: Analytify - Analytics Dashboard widget
 * Plugin URI: https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/
 * Description: It is a Free Add-on for Analytify plugin to show Google Analytics widget at WordPress dashboard. This is developed on the requests of our users.
 * Version: 1.0.0
 * Author: Analytify
 * Author URI: https://analytify.io/
 * Developer: Analytify LLC
 * Developer URI: https://analytify.io/
 * License: GPLv2+
 * Text Domain: wp-analytify
 * Domain Path: /languages
 */

define( 'ANALYTIFY_DASHBOARD_VERSION', '1.0.0' );
define( 'ANALYTIFY_DASHBOARD_ROOT_PATH', dirname( __FILE__ ) );


add_action( 'plugins_loaded', 'wp_install_analytify_dashboard', 20 );

/**
 * Run onf plugins Loaded.
 *
 * @since 1.0.0
 */
function wp_install_analytify_dashboard() {

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {
		add_action( 'admin_notices' , 'pa_install_free_dashboard' );
		return;
	}

	if ( ! in_array( 'wp-analytify/wp-analytify.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_action( 'admin_notices', 'pa_activate_free_dashboard' );
		return;
	}

	include_once ANALYTIFY_DASHBOARD_ROOT_PATH . '/analytify-dashboard-addon.php';
	new Analytify_Dashboard_Addon;
}

/**
 * Check If Analytify Free is download.
 *
 * @since 1.0.0
 */
function pa_install_free_dashboard() {
	$action = 'install-plugin';
	$slug   = 'wp-analytify';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

	printf('<div class="notice notice-error is-dismissible">
	<p>%1$s<a href="%2$s">%3$s</a></p></div>' , esc_html__( 'Google analytics dashboard widget works with Analytify plugin as an add-on. Please Install Analytify(Core) Free version. ', 'wp-analytify' ), $link, esc_html__( 'Click Here' ) );
}

/**
 * Active Analytify Free.
 *
 * @since 1.0.0
 */
function pa_activate_free_dashboard() {

	$action = 'activate';
	$slug   = 'wp-analytify/wp-analytify.php';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );

	printf('<div class="notice notice-error is-dismissible">
	<p>%1$s<a href="%2$s">%3$s</a></p></div>' , esc_html__( 'Analytify Dashboard addon works with the Analytify Free plugin. Please activate ', 'wp-analytify' ), $link, esc_html__( 'Analytify Free.' ) );

}
