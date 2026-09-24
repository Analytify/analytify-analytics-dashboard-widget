<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName
/**
 * Main dashboard widget addon class.
 *
 * @package Analytify
 * @subpackage Dashboard Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AnalytifyWidgetAddon' ) ) {

	/**
	 * Main dashboard widget addon class.
	 */
	class AnalytifyWidgetAddon {

		/**
		 * The single instance of the class.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * The main Analytify object.
		 *
		 * @var object
		 */
		private $wp_analytify;

		/**
		 * Hold if Pro is active.
		 *
		 * @var bool
		 */
		private $is_pro_active;

		/**
		 * Does the pro version needs to be updated?
		 *
		 * @var bool
		 */
		private $pro_updated;

		/**
		 * Holds the access token.
		 *
		 * @var string
		 */
		private $access_token;

		/**
		 * Profile ID container.
		 *
		 * @var string
		 */
		private $profile_id;

		/**
		 * GA version (ga4 or ga3).
		 *
		 * @var string
		 */
		private $ga_mode;

		/**
		 * Returns the single instance of the class.
		 *
		 * @return object Class instance
		 */
		public static function get_instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Class constructor.
		 */
		private function __construct() {
			$this->hooks();
		}

		/**
		 * Registers WP hooks.
		 *
		 * @return void
		 */
		private function hooks() {

			if ( isset( $GLOBALS['WP_ANALYTIFY'] ) ) {
				$this->wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			} else {
				$this->wp_analytify = null;
			}

			if ( ! $this->has_access() ) {
				return;
			}

			if ( ! isset( $this->wp_analytify->settings ) ) {
				return;
			}

			$this->is_pro_active = class_exists( 'WP_Analytify_Pro_Base' );
			$this->pro_updated   = defined( 'ANALYTIFY_PRO_VERSION' ) && 0 <= version_compare( ANALYTIFY_PRO_VERSION, '5.0.0' ) ? true : false;
			$this->access_token  = get_option( 'post_analytics_token' );
			$this->profile_id    = $this->wp_analytify->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile', '' );
			$this->ga_mode       = method_exists( 'WPANALYTIFY_Utils', 'get_ga_mode' ) ? WPANALYTIFY_Utils::get_ga_mode() : 'ga3';

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_script' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );

			if ( ! empty( $this->wp_analytify->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile', '' ) ) ) {
				add_action( 'wp_ajax_AnalytifyWidgetAddon', array( $this, 'analytify_general_stats' ) );
			}
		}

		/**
		 * Check if the current user (based on the user-role) have access to check dashboard.
		 *
		 * @since 1.0.5
		 *
		 * @return bool
		 */
		private function has_access() {
			$is_access_level = $this->wp_analytify->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard', array( 'administrator' ) );
			return $this->wp_analytify->pa_check_roles( $is_access_level );
		}

		/**
		 * Enqueue scripts (and localize) and styles.
		 *
		 * @return void
		 */
		public function enqueue_dashboard_script() {

			$current_screen = get_current_screen();

			if ( 'dashboard' !== $current_screen->id ) {
				// Run only on main dashboard page.
				return;
			}

			wp_enqueue_script(
				'analytify-dashboard-addon',
				ANALYTIFY_WIDGET_PATH . '/assets/js/wp-analytify-dashboard.js',
				array(),
				ANALYTIFY_DASHBOARD_VERSION,
				true
			);

			$localize_vars = array(
				'url'                       => esc_url_raw( rest_url( 'wp-analytify/v1/get_widget_report/' ) ),
				'pro_url'                   => esc_url_raw( rest_url( 'wp-analytify/v1/get_pro_report/' ) ),
				'pro_active'                => $this->is_pro_active ? 'active' : 'inactive',
				'pro_updated'               => $this->pro_updated ? 'true' : 'false',
				'nonce'                     => wp_create_nonce( 'wp_rest' ),
				'empty_message'             => esc_html__( 'No activity during this period.', 'analytify-analytics-dashboard-widget' ),
				'top_cities_per_page'       => apply_filters( 'analytify_widget_top_cities_per_page', 5 ),
				'top_countries_filter'      => apply_filters( 'analytify_widget_top_countries_filter', 5 ),
				'top_pages_by_views_filter' => apply_filters( 'analytify_widget_top_pages_by_views_filter', 5 ),
				'top_keywords_filter'       => apply_filters( 'analytify_widget_top_keywords_filter', 5 ),
				'top_refferals_filter'      => apply_filters( 'analytify_widget_top_refferals_filter', 5 ),
				'graph'                     => apply_filters( 'analytify_widget_graph_display_filter', false ),
			);

			if ( 'ga4' === $this->ga_mode ) {
				$localize_vars['real_time_labels'] = array(
					'online'  => esc_html__( 'Visitors Online', 'analytify-analytics-dashboard-widget' ),
					'desktop' => esc_html__( 'Desktop Visitors', 'analytify-analytics-dashboard-widget' ),
					'tablet'  => esc_html__( 'Tablet Visitors', 'analytify-analytics-dashboard-widget' ),
					'mobile'  => esc_html__( 'Mobile Visitors', 'analytify-analytics-dashboard-widget' ),
				);
			} else {
				$localize_vars['real_time_labels'] = array(
					'online'    => esc_html__( 'Visitors Online', 'analytify-analytics-dashboard-widget' ),
					'referral'  => esc_html__( 'Referral', 'analytify-analytics-dashboard-widget' ),
					'organic'   => esc_html__( 'Organic', 'analytify-analytics-dashboard-widget' ),
					'social'    => esc_html__( 'Social', 'analytify-analytics-dashboard-widget' ),
					'direct'    => esc_html__( 'Direct', 'analytify-analytics-dashboard-widget' ),
					'new'       => esc_html__( 'New', 'analytify-analytics-dashboard-widget' ),
					'returning' => esc_html__( 'Returning', 'analytify-analytics-dashboard-widget' ),
				);
			}

			if ( ! $this->is_pro_active ) {
				$localize_vars['real_time_pro_message'] = '<a class="analytify_general_stats_btn" href="https://analytify.io/pricing/?utm_source=analytify-widget-lite&utm_medium=dashboard-widget&utm_content=RealTime+Section&utm_campaign=pro-upgrade" target="_blank">' . esc_html__( 'Upgrade to Analytify Pro to Unlock RealTime Analytics in WordPress.', 'analytify-analytics-dashboard-widget' ) . '</a>';
			} elseif ( ! $this->pro_updated ) {
				$localize_vars['real_time_pro_message'] = '<span class="analytify_general_stats_btn">' . esc_html__( 'You have an outdated version of Analytify Pro. Please update Analytify Pro to see real-time stats.', 'analytify-analytics-dashboard-widget' ) . '</span>';
			}

			wp_localize_script(
				'analytify-dashboard-addon',
				'analytify_dashboard_widget',
				$localize_vars
			);
		}

		/**
		 * Add dashboard widget.
		 *
		 * @return void
		 */
		public function add_widget() {

			global $wp_meta_boxes;

			wp_add_dashboard_widget(
				'analytify-dashboard-addon',
				esc_html__( 'Google Analytics Dashboard By Analytify', 'analytify-analytics-dashboard-widget' ),
				array( $this, 'widget_view' ),
				null,
				null
			);

			// Place the widget at the top.
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required to reorder dashboard widgets.
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			$widget_instance  = array( 'analytify-dashboard-addon' => $normal_dashboard['analytify-dashboard-addon'] );

			unset( $normal_dashboard['analytify-dashboard-addon'] );

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required to reorder dashboard widgets.
			$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $widget_instance, $normal_dashboard );
		}

		/**
		 * Create Widget Container.
		 * Call back registered in $this->add_widget().
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function widget_view() {

			if ( ! $this->access_token || ! $this->profile_id || ! get_option( 'pa_google_token' ) ) {
				$message  = '<p>' . esc_html__( 'Connect Analytify to your Google Analytics account to setup Analytics for your website.', 'analytify-analytics-dashboard-widget' ) . '</p>';
				$message .= '<a href="' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '" class="analytify-active-card-button">' . esc_html__( 'Configure Analytify', 'analytify-analytics-dashboard-widget' ) . '</a>';
				AnalytifyWidgetHelper::notice( $message );
				return;
			}

			$date    = AnalytifyWidgetHelper::get_dates();
			$ga_mode = $this->ga_mode;
			$footer  = $this->view_footer();
			require_once ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/main.php';
		}

		/**
		 * Undocumented function
		 *
		 * @return string
		 */
		private function view_footer() {

			$menu = array();

			$html = '<div class="analytify-dashboard-widget-footer">';

			// Use SVGs from assets/images directory.
			$assets_url = plugins_url( 'assets/images', __DIR__ );
			$doc_svg    = '<img src="' . esc_url( $assets_url . '/document.svg' ) . '" alt="Documentation" class="analytify-widget-footer-icon" />';
			$addons_svg = '<img src="' . esc_url( $assets_url . '/addons.svg' ) . '" alt="Add-on" class="analytify-widget-footer-icon" />';

			$menu[] = '<a href="' . esc_url( 'https://analytify.io/documentation?utm_source=analytify-widget-lite&utm_medium=dashboard-widget&utm_content=Documentation&utm_campaign=user-guide' ) . '" target="_blank">' . $doc_svg . esc_html__( 'Documentation', 'analytify-analytics-dashboard-widget' ) . '</a>';

			if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
				$menu[] = '<a href="' . esc_url( 'https://analytify.io/add-ons?utm_source=analytify-widget-lite&utm_medium=dashboard-widget&utm_content=Addons&utm_campaign=pro-upgrade' ) . '" target="_blank">' . $addons_svg . esc_html__( 'Add-ons', 'analytify-analytics-dashboard-widget' ) . '</a>';
			} else {
				$menu[] = '<a href="' . esc_url( 'https://analytify.io/pricing?utm_source=analytify-widget-lite&utm_medium=dashboard-widget&utm_content=Go+Pro&utm_campaign=pro-upgrade' ) . '" class="analytify-dashboard-widget-go-pro" target="_blank"><span class="dashicons dashicons-cart"></span> ' . esc_html__( 'Go Pro', 'analytify-analytics-dashboard-widget' ) . '</a>';
			}

			$html .= implode( ' | ', $menu );

			$html .= '</div>';

			return $html;
		}
	}
}

/**
 * Init the instance.
 */
AnalytifyWidgetAddon::get_instance();
