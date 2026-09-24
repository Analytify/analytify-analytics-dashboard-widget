<?php
/**
 * Handle Analytify REST end points.
 *
 * @package Analytify
 * @subpackage Dashboard Widget
 */

// phpcs:disable WordPress.Files.FileName.InvalidClassFileName

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle Analytify REST end points.
 */
class Analytify_Widget_Rest_API {

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
	 * GA version (ga4 or ga3).
	 *
	 * @var string
	 */
	private $ga_mode;

	/**
	 * Selected 'start state'.
	 *
	 * @var string
	 */
	private $start_date;

	/**
	 * Selected 'End state'.
	 *
	 * @var string
	 */
	private $end_date;

	/**
	 * Selected 'Date Difference'.
	 *
	 * @var string
	 */
	private $date_differ;

	/**
	 * Return message.
	 *
	 * @var string
	 */
	private $message;

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

		// Register API endpoints.
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		// Formate 'general_statistics', add labels and description.
		add_filter( 'analytify_widget_formate_general_statistics', array( $this, 'formate_general_statistics' ), 10, 1 );
	}

	/**
	 * Register end point.
	 */
	public function rest_api_init() {

		$this->wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$this->ga_mode      = method_exists( 'WPANALYTIFY_Utils', 'get_ga_mode' ) ? WPANALYTIFY_Utils::get_ga_mode() : 'ga3';

		register_rest_route(
			'wp-analytify/v1',
			'/get_widget_report/(?P<request_type>[a-zA-Z0-9-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE, // Get Request.
					'callback'            => array( $this, 'handle_request' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
			)
		);
	}

	/**
	 * Checks access permission.
	 * Checks if the user is logged-in and checks of the user role has access.
	 *
	 * @return boolean
	 */
	public function permission_check() {
		$is_access_level = $this->wp_analytify->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard', array( 'administrator' ) );
		return (bool) $this->wp_analytify->pa_check_roles( $is_access_level );
	}

	/**
	 * Handle the request.
	 *
	 * @param WP_REST_Request $request WP Request object.
	 *
	 * @return array|WP_Error
	 */
	public function handle_request( WP_REST_Request $request ) {

		$request_type = $request->get_param( 'request_type' );

		$this->start_date  = $request->get_param( 'sd' );
		$this->end_date    = $request->get_param( 'ed' );
		$this->date_differ = $request->get_param( 'differ' );

		if ( $this->date_differ ) {
			update_option( 'analytify_widget_date_differ', $this->date_differ );
		}

		switch ( $request_type ) {
			case 'general-statistics':
				$stats = 'ga4' === $this->ga_mode ? $this->get_general_statistics_ga4() : '';

				$stats = apply_filters( 'analytify_widget_formate_general_statistics', $stats );

				$stats['success']    = true;
				$stats['pagination'] = false;
				$stats['title']      = esc_html__( 'General Statistics', 'analytify-analytics-dashboard-widget' );

				wp_send_json( $stats );
				return;

			case 'top-pages-by-views':
				$data = 'ga4' === $this->ga_mode ? $this->get_top_pages_by_views_ga4() : '';

				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Pages by Views', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Top Pages and Posts.', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Title', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data;

				wp_send_json( $stats );
				return;

			case 'top-posts-by-views':
				$data = 'ga4' === $this->ga_mode ? $this->get_top_posts_by_views_ga4() : '';

				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Posts ', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Top Posts by Views.', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Title', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data;

				wp_send_json( $stats );
				return;

			case 'top-countries':
				$data = 'ga4' === $this->ga_mode ? $this->get_top_countries_ga4() : '';

				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Countries by Views', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Top Countries', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Country', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data;

				wp_send_json( $stats );
				return;

			case 'top-cities':
				$data = 'ga4' === $this->ga_mode ? $this->get_top_cities_ga4() : '';

				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Cities by Views', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Top Cities', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'City', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data;

				wp_send_json( $stats );
				return;

			case 'keywords':
				$data = 'ga4' === $this->ga_mode ? $this->get_keywords_ga4() : '';

				$stats['success']       = $data ? true : false;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Keywords by Views', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Ranked Keywords', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Keyword', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data ? $data : array();
				if ( ! $data ) {
					$stats['message'] = $this->message;
				}

				wp_send_json( $stats );
				return;

			case 'social-media':
				if ( 'ga3' !== $this->ga_mode ) {
					return new WP_Error( 'analytify_invalid_endpoint', esc_html__( 'Invalid endpoint.', 'analytify-analytics-dashboard-widget' ), array( 'status' => 404 ) );
				}

				$data = 'ga4' === $this->ga_mode ? $this->get_social_media_ga4() : '';

				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Social Media by Views', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Number of users coming to your site from social media.', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Social Media', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data;

				wp_send_json( $stats );
				return;

			case 'top-reffers':
				$data = 'ga4' === $this->ga_mode ? $this->get_top_reffers_ga4() : '';

				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Top Referrers by Views.', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Top Referrers to your website.', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Refer', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $data;

				wp_send_json( $stats );
				return;

			case 'visitors-devices':
				$chart_description     = array(
					'visitor_devices' => array(
						'title'  => esc_html__( 'Devices of Visitors', 'analytify-analytics-dashboard-widget' ),
						'type'   => 'PIE',
						'stats'  => array(
							'mobile'  => array(
								'label'  => esc_html__( 'Mobile', 'analytify-analytics-dashboard-widget' ),
								'number' => 0,
							),
							'tablet'  => array(
								'label'  => esc_html__( 'Tablet', 'analytify-analytics-dashboard-widget' ),
								'number' => 0,
							),
							'desktop' => array(
								'label'  => esc_html__( 'Desktop', 'analytify-analytics-dashboard-widget' ),
								'number' => 0,
							),
						),
						'colors' => ( function () {
							$default_colors = array( '#444444', '#ffbc00', '#ff5252' );
							$filtered_colors = apply_filters( 'analytify_visitor_devices_chart_colors', array() );
							return array_replace( $default_colors, $filtered_colors );
						} )(),
					),
				);
				$device_category_stats = array();
				$device_category_stats = $this->wp_analytify->get_reports(
					'show-default-overall-device-dashboard',
					array(
						'sessions',
					),
					$this->get_dates(),
					array(
						'deviceCategory',
					),
					array(
						'type' => 'dimension',
						'name' => 'deviceCategory',
					)
				);
				if ( $device_category_stats['rows'] ) {
					foreach ( $device_category_stats['rows'] as $device ) {
						$chart_description['visitor_devices']['stats'][ $device['deviceCategory'] ]['number'] = $device['sessions'];
					}
				}
				$stats['success']       = true;
				$stats['pagination']    = true;
				$stats['title']         = esc_html__( 'Visitors Devices', 'analytify-analytics-dashboard-widget' );
				$stats['bottom_info']   = esc_html__( 'Devices of visitors on your website.', 'analytify-analytics-dashboard-widget' );
				$stats['stats']['head'] = array(
					esc_html__( 'Refer', 'analytify-analytics-dashboard-widget' ),
					esc_html__( 'Views', 'analytify-analytics-dashboard-widget' ),
				);
				$stats['stats']['data'] = $chart_description;
				wp_send_json( $stats );
				return;

			default:
				// If no request type match, Return error.
				return new WP_Error( 'analytify_invalid_endpoint', esc_html__( 'Invalid endpoint.', 'analytify-analytics-dashboard-widget' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Returns start and end date as an array to be used for GA4's get_reports()
	 *
	 * @return array
	 */
	private function get_dates() {
		return array(
			'start' => $this->start_date,
			'end'   => $this->end_date,
		);
	}

	/**
	 * Return 'general_statistics' for GA4
	 *
	 * @return array
	 */
	private function get_general_statistics_ga4() {
		$stats = array();

		$raw_new = $this->wp_analytify->get_reports(
			'widget-show-overall-dashboard',
			array(
				'sessions',
				'totalUsers',
				'bounceRate',
				'screenPageViews',
				'averageSessionDuration',
				'screenPageViewsPerSession',
				'userEngagementDuration',
				'newUsers',
				'activeUsers',
			),
			$this->get_dates(),
		);

		// Request to get new vs returning user sessions.
		$new_vs_returning = $this->wp_analytify->get_reports(
			'widget-show-new-vs-returning',
			array(
				'activeUsers',
			),
			$this->get_dates(),
			array(
				'newVsReturning',
			)
		);

		$new_users       = 0;
		$returning_users = 0;

		if ( ! empty( $new_vs_returning['rows'] ) ) {
			foreach ( $new_vs_returning['rows'] as $row ) {
				$type  = $row['newVsReturning'] ?? '';
				$value = isset( $row['activeUsers'] ) ? (int) $row['activeUsers'] : 0;

				if ( 'new' === $type ) {
					$new_users = $value;
				} elseif ( 'returning' === $type ) {
					$returning_users = $value;
				}
			}
		}

		$new_users       = WPANALYTIFY_Utils::pretty_numbers( $new_users );
		$returning_users = WPANALYTIFY_Utils::pretty_numbers( $returning_users );

		$new_v_returning_visitors = sprintf(
			// translators: %1$s: New visitors count, %2$s: Returning visitors count.
			__( '%1$s vs %2$s', 'analytify-analytics-dashboard-widget' ),
			'<span class="analytify_general_stats_value">' . $new_users . '</span>',
			'<span class="analytify_general_stats_value">' . $returning_users . '</span>'
		);

		$boxes = array();

		$boxes['sessions']          = isset( $raw_new['aggregations']['sessions'] ) ? number_format( $raw_new['aggregations']['sessions'] ) : 0;
		$boxes['visitors']          = isset( $raw_new['aggregations']['totalUsers'] ) ? number_format( $raw_new['aggregations']['totalUsers'] ) : 0;
		$boxes['bounce_rate']       = isset( $raw_new['aggregations']['bounceRate'] ) ? WPANALYTIFY_Utils::fraction_to_percentage( $raw_new['aggregations']['bounceRate'] ) : 0;
		$boxes['avg_time_on_site']  = isset( $raw_new['aggregations']['sessions'] ) ? ( ( $raw_new['aggregations']['sessions'] <= 0 ) ? '00:00:00' : $this->wp_analytify->pa_pretty_time( $raw_new['aggregations']['averageSessionDuration'] ) ) : 0;
		$boxes['pages_per_session'] = isset( $raw_new['aggregations']['sessions'] ) ? ( ( $raw_new['aggregations']['sessions'] <= 0 ) ? '0.00' : number_format( round( $raw_new['aggregations']['screenPageViewsPerSession'] ) ) ) : 0;
		$boxes['page_views']        = isset( $raw_new['aggregations']['screenPageViews'] ) ? ( ( $raw_new['aggregations']['screenPageViews'] <= 0 ) ? '0' : number_format( $raw_new['aggregations']['screenPageViews'] ) ) : 0;
		$boxes['new_users']         = $new_users;
		// Note: GA4 missing percentNewSessions metric.
		$boxes['new_v_returning_visitors'] = $new_v_returning_visitors;
		$boxes['bottom_info']              = isset( $raw_new['aggregations']['userEngagementDuration'] ) ? $this->wp_analytify->pa_pretty_time( $raw_new['aggregations']['userEngagementDuration'] ) : 0;

		return $boxes;
	}



	/**
	 * Adds labels and description to 'general_statistics'.
	 *
	 * @param array $raw_stats Raw numbers.
	 * @return array
	 */
	public function formate_general_statistics( $raw_stats = array() ) {

		$boxes = array(
			'sessions'                 => array(
				'title' => esc_html__( 'Sessions', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'A session is a time period in which a user is actively engaging your website.', 'analytify-analytics-dashboard-widget' ),
			),
			'visitors'                 => array(
				'title' => esc_html__( 'Visitors', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'Users who complete a minimum one session on your website or content.', 'analytify-analytics-dashboard-widget' ),
			),
			'bounce_rate'              => array(
				'title'  => esc_html__( 'Bounce Rate', 'analytify-analytics-dashboard-widget' ),
				'append' => '<sub>%</sub>',
				'info'   => esc_html__( 'Percentage of Single page visits (i.e number of visits in which a visitor leaves your website from the landing page without browsing your website).', 'analytify-analytics-dashboard-widget' ),
			),
			'avg_time_on_site'         => array(
				'title' => esc_html__( 'Avg. time on site', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'Total time that a single user spends on your website.', 'analytify-analytics-dashboard-widget' ),
			),
			'pages_per_session'        => array(
				'title' => esc_html__( 'Pages/Session', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'Pages/Session (Average Page Depth) is the number of pages viewed by a user during a session. Repeated views are counted.', 'analytify-analytics-dashboard-widget' ),
			),
			'page_views'               => array(
				'title' => esc_html__( 'Page Views', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'Page Views are the total number of Pageviews. This including repeated views.', 'analytify-analytics-dashboard-widget' ),
			),
			'new_users'                => array(
				'title' => esc_html__( 'New Users', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'The number of users who interacted with your site or launched your website for the first time', 'analytify-analytics-dashboard-widget' ),
			),
			'new_sessions'             => array(
				'title' => esc_html__( '%New Sessions', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'A new session is a time period when a new user comes to your website and is actively engaged with your website.', 'analytify-analytics-dashboard-widget' ),
			),
			'new_v_returning_visitors' => array(
				'title' => esc_html__( 'New vs Returning Visitors', 'analytify-analytics-dashboard-widget' ),
				'info'  => esc_html__( 'New visitors are those who land to your site for the first time, whereas Returning Visitors are the ones who come back to your site after a recent visit.', 'analytify-analytics-dashboard-widget' ),
			),
		);

		if ( 'ga4' === $this->ga_mode ) {
			unset( $boxes['new_sessions'] );
		} else {
			unset( $boxes['new_users'] );
		}

		foreach ( $raw_stats as $key => $number ) {
			if ( isset( $boxes[ $key ] ) ) {
				$boxes[ $key ]['number'] = $number;
			}
		}

		// Translators: %s total time.
		$bottom_text = sprintf( esc_html__( 'Total time visitors spent on your site: %s', 'analytify-analytics-dashboard-widget' ), $raw_stats['bottom_info'] );

		return array(
			'boxes'       => $boxes,
			'bottom_info' => isset( $raw_stats['bottom_info'] ) ? $bottom_text : '',
		);
	}

	/**
	 * Return 'top_pages_by_views' for GA4
	 *
	 * @return array
	 */
	private function get_top_pages_by_views_ga4() {
		$stats = array();

		// API request limit.
		$api_request_limit = apply_filters( 'analytify_api_limit_widget_addon', 50, 'top_pages_by_views' );

		$raw_stats = $this->wp_analytify->get_reports(
			'widget-show-top-pages-dashboard',
			array(
				'screenPageViews',
			),
			$this->get_dates(),
			array(
				'pageTitle',
			),
			array(
				'order' => 'desc',
				'type'  => 'metric',
				'name'  => 'screenPageViews',
			),
			array(),
			$api_request_limit
		);

		if ( $raw_stats && isset( $raw_stats['rows'] ) ) {
			foreach ( $raw_stats['rows'] as $row ) {
				$stats[] = array(
					$row['pageTitle'],
					$row['screenPageViews'],
				);
			}
		}

		return $stats;
	}

	/**
	 * Return 'top_posts_by_views' for GA4
	 *
	 * @return array
	 */
	private function get_top_posts_by_views_ga4() {
		$stats = array();

		// API request limit.
		$api_request_limit = apply_filters( 'analytify_api_limit_widget_addon', 10, 'top_posts_by_views' );

		$raw_stats = $this->wp_analytify->get_reports(
			'widget-show-top-posts-dashboard',
			array(
				'screenPageViews',
			),
			$this->get_dates(),
			array(
				'pagePathPlusQueryString',
			),
			array(
				'order' => 'desc',
				'type'  => 'metric',
				'name'  => 'screenPageViews',
			),
			array(
				'logic'   => 'OR',
				'filters' => array(
					array(
						'type'       => 'dimension',
						'name'       => 'pagePathPlusQueryString',
						'match_type' => 5, // Regex match.
						'value'      => '^/\\d{4}/\\d{2}/\\d{2}/.+', // Assuming posts have date-based URL structure.
					),
					array(
						'type'       => 'dimension',
						'name'       => 'pagePathPlusQueryString',
						'match_type' => 1, // Contains.
						'value'      => '/blog/',
					),
					array(
						'type'       => 'dimension',
						'name'       => 'pagePathPlusQueryString',
						'match_type' => 1, // Contains.
						'value'      => '/news/',
					),
				),
			),
			$api_request_limit
		);
		if ( $raw_stats && isset( $raw_stats['rows'] ) ) {
			foreach ( $raw_stats['rows'] as $row ) {
				$stats[] = array(
					$row['pagePathPlusQueryString'],
					$row['screenPageViews'],
				);
			}
		}

		return $stats;
	}

	/**
	 * Return 'top_countries' for GA4
	 *
	 * @return array
	 */
	private function get_top_countries_ga4() {
		$stats = array();

		// API request limit.
		$api_request_limit = apply_filters( 'analytify_api_limit_widget_addon', 50, 'top_countries' );

		$raw_stats = $this->wp_analytify->get_reports(
			'widget-show-top-countries-dashboard',
			array(
				'sessions',
			),
			$this->get_dates(),
			array(
				'country',
			),
			array(
				'order' => 'desc',
				'type'  => 'metric',
				'name'  => 'sessions',
			),
			array(
				'logic'   => 'AND',
				'filters' => array(
					array(
						'type'           => 'dimension',
						'name'           => 'country',
						'match_type'     => 4,
						'value'          => '(not set)',
						'not_expression' => true,
					),
				),
			),
			$api_request_limit
		);

		if ( $raw_stats && isset( $raw_stats['rows'] ) ) {
			foreach ( $raw_stats['rows'] as $row ) {
				$stats[] = array(
					$row['country'],
					$row['sessions'],
				);
			}
		}

		return $stats;
	}


	/**
	 * Return 'top_cities' for GA4
	 *
	 * @return array
	 */
	private function get_top_cities_ga4() {
		$stats = array();

		// API request limit.
		$api_request_limit = apply_filters( 'analytify_api_limit_widget_addon', 50, 'top_cities' );

		$raw_stats = $this->wp_analytify->get_reports(
			'widget-show-top-cities-dashboard',
			array(
				'sessions',
			),
			$this->get_dates(),
			array(
				'city',
			),
			array(
				'order' => 'desc',
				'type'  => 'metric',
				'name'  => 'sessions',
			),
			array(
				'logic'   => 'AND',
				'filters' => array(
					array(
						'type'           => 'dimension',
						'name'           => 'city',
						'match_type'     => 4,
						'value'          => '(not set)',
						'not_expression' => true,
					),
				),
			),
			$api_request_limit
		);

		if ( $raw_stats && isset( $raw_stats['rows'] ) ) {
			foreach ( $raw_stats['rows'] as $row ) {
				$stats[] = array(
					$row['city'],
					$row['sessions'],
				);
			}
		}

		return $stats;
	}


	/**
	 * Return 'keywords' for GA4
	 *
	 * @return array
	 */
	private function get_keywords_ga4() {
		$stats = array();

		// API request limit.
		$api_request_limit = apply_filters( 'analytify_api_limit_widget_addon', 50, 'keywords' );

		$raw_stats = $this->wp_analytify->get_search_console_stats(
			'show-default-keyword-dashboard',
			$this->get_dates(),
			$api_request_limit
		);

		if ( isset( $raw_stats['error']['status'] ) && isset( $raw_stats['error']['message'] ) ) {
			$error_status  = isset( $raw_stats['error']['status'] ) ? $raw_stats['error']['status'] : '';
			$error_message = isset( $raw_stats['error']['message'] ) ? $raw_stats['error']['message'] : '';
			$this->message = esc_html__( 'Unable To Fetch Reports', 'analytify-analytics-dashboard-widget' ) . '<br />' . esc_html( $error_status ) . '<br />' . esc_html( $error_message );
			return false;
		}

		if ( ! empty( $raw_stats['response']['rows'] ) ) {
			foreach ( $raw_stats['response']['rows'] as $row ) {
				$stats[] = array(
					$row['keys'][0],
					$row['clicks'],
				);
			}
		} else {
			$this->message = esc_html__( 'No activity during this period.', 'analytify-analytics-dashboard-widget' );
		}

		return $stats;
	}


	/**
	 * Return 'social-media' for GA4
	 *
	 * @return array
	 */
	private function get_social_media_ga4() {
		$stats = array();

		// Note: Social media tracking is not available in GA4.

		return $stats;
	}


	/**
	 * Return 'top-reffers' for GA4
	 *
	 * @return array
	 */
	private function get_top_reffers_ga4() {
		$stats = array();

		// API request limit.
		$api_request_limit = apply_filters( 'analytify_api_limit_widget_addon', 50, 'top_reffers' );

		$raw_stats = $this->wp_analytify->get_reports(
			'widget-show-top-reffers-dashboard',
			array(
				'sessions',
			),
			$this->get_dates(),
			array(
				'sessionSource',
			),
			array(
				'order' => 'desc',
				'type'  => 'metric',
				'name'  => 'sessions',
			),
			array(),
			$api_request_limit
		);

		if ( $raw_stats && isset( $raw_stats['rows'] ) ) {
			foreach ( $raw_stats['rows'] as $row ) {
				$stats[] = array(
					$row['sessionSource'],
					$row['sessions'],
				);
			}
		}

		return $stats;
	}
}

/**
 * Init the instance.
 */
Analytify_Widget_Rest_API::get_instance();
