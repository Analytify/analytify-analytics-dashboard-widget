<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Plugin Name: Analytify - Analytics Dashboard widget
 * Plugin URI: https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/
 * Description: It is a Free Add-on for Analytify plugin to show Google Analytics widget at WordPress dashboard. This is developed on the requests of our users.
 * Version: 1.0.1
 * Author: Analytify
 * Author URI: https://analytify.io/
 * Developer: Analytify LLC
 * Developer URI: https://analytify.io/
 * License: GPLv2+
 * Text Domain: wp-analytify
 * Domain Path: /languages
 */

define( 'ANALYTIFY_DASHBOARD_VERSION', '1.0.1' );
define( 'ANALYTIFY_DASHBOARD_ROOT_PATH', dirname( __FILE__ ) );


add_action( 'plugins_loaded', 'wp_install_analytify_dashboard', 20 );

/**
 * Run onf plugins Loaded.
 *
 * @since 1.0.0
 */
function wp_install_analytify_dashboard() {

	global $pagenow;

	if ( 'index.php' != $pagenow && defined('DOING_AJAX') && ! DOING_AJAX ) { // Run only on main dashboard page.
		return;
	}

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {
		add_action( 'admin_notices', 'pa_install_free_dashboard' );
		add_action( 'wp_dashboard_setup','add_analytify_widget' );
		return;
	}

	if ( ! in_array( 'wp-analytify/wp-analytify.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_action( 'admin_notices', 'pa_activate_free_dashboard' );
		add_action( 'wp_dashboard_setup','add_analytify_widget' );
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

	$message = sprintf('%1$s<a href="%2$s">%3$s</a>' , esc_html__( 'Google analytics dashboard widget works with Analytify plugin as an add-on. Please Install Analytify(Core) Free version. ', 'wp-analytify' ), $link, esc_html__( 'Click Here' ) );

 	analytify_widget_notice( $message, 'wp-analytify-danger' );
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

	$message = 	sprintf( '%1$s<a href="%2$s">%3$s</a>' , esc_html__( 'Analytify Dashboard addon works with the Analytify Free plugin. Please activate ', 'wp-analytify' ), $link, esc_html__( 'Analytify Free.' ) );

	analytify_widget_notice( $message, 'wp-analytify-danger' );

}


/**
 * Add dashboard widget with warning message.
 *
 * @since 1.0.3
 */
function add_analytify_widget() {
	wp_add_dashboard_widget( 'analytify-dashboard-addon-warning', __( 'Google Analytics Dashboard By Analytify', 'wp-analytify' ), 'wpa_general_dashboard_area', null , null );
}

/**
 * Dashboard Widget
 *
 * @since 1.0.3
 */
function wpa_general_dashboard_area( $var, $dashboard_id ) {

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {
		pa_install_free_dashboard();
		return;
	}

	if ( ! in_array( 'wp-analytify/wp-analytify.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		pa_activate_free_dashboard();
		return;
	}

}

/**
 * Add custom admin notice
 * @param  string $message Custom Message
 * @param  string $class   wp-analytify-success,wp-analytify-danger
 *
 * @since 1.0.3
 */
 function analytify_widget_notice( $message, $class ) {
		echo '<div class="wp-analytify-notification '. $class .'">
							<a class="" href="#" aria-label="Dismiss the welcome panel"></a>
							<div class="wp-analytify-notice-logo">
								<img src="' . plugins_url( 'assets/images/logo.svg', __FILE__ ) . '" alt="">
							</div>
							<div class="wp-analytify-notice-discription">
								<p>' . $message .'</p>
							</div>
				</div>';
 }


/**
 * Add Css for admin notice
 *
 * @since 1.0.3
 */
 add_action( 'admin_enqueue_scripts', 'analytify_widget_scripts' );
 function analytify_widget_scripts( $page ) {
	 if ( 'index.php' == $page ) {
		 wp_enqueue_style( 'analytify-widget-admin', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ANALYTIFY_DASHBOARD_VERSION );
	 }
 }
?>
