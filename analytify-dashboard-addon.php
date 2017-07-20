<?php

if ( ! class_exists( 'Analytify_Dashboard_Addon' ) ) {

	class Analytify_Dashboard_Addon {

		public function __construct() {

			if ( ! $this->is_access() ) { return; }

			add_action( 'wp_dashboard_setup', array( $this, 'add_analytify_widget' ) );


			if ( $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile', '' ) != '' ) {

				add_action( 'admin_enqueue_scripts', array( $this, 'pa_dashboard_script' ) );
				add_action( 'wp_ajax_analytify_dashboard_addon', array( $this, 'analytify_general_stats' ) );

			}
		}

		function pa_dashboard_script() {
			wp_enqueue_script( 'analytify-dashboard-addon', plugins_url( '/assets/js/wp-analytify-dashboard.js', __FILE__ ), false, ANALYTIFY_DASHBOARD_VERSION );
		}


		public function add_analytify_widget() {

			wp_add_dashboard_widget( 'analytify-dashboard-addon', __( 'Google Analytics Dashboard By Analytify', 'analytify-analytics-dashboard-widget' ), array( $this, 'wpa_general_dashboard_area' ), null , null );

		}

		/**
		* Create Widget Container.
		*
		* @since 1.0.0
		*/
		public function wpa_general_dashboard_area( $var, $dashboard_id ) {

			$start_date_val = strtotime( '- 7 days' );
			$end_date_val   = strtotime( 'now' );
			$s_date         = date( 'Y-m-d', $start_date_val );
			$ed_date        = date( 'Y-m-d', $end_date_val );

			$acces_token  = get_option( 'post_analytics_token' );
			if ( isset( $acces_token ) && ! empty( $acces_token ) && get_option( 'pa_google_token' ) ) {
				if ( $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile', '' ) != '' ) {
					?>
					<div class="analytify_wraper">
						<form id="analytify_dashboard" name="analytify_dashboard" method="POST" class="analytify-widget-form">
							<div class="analytify_main_setting_bar">
								<div class="analytify_pull_right analytify_setting">
									<div class="analytify_select_date">
										<form class="analytify_form_date" action="" method="post">
											<div class="analytify_select_date_fields">
												<input type="hidden" name="st_date" id="analytify_start_val">
												<input type="hidden" name="ed_date" id="analytify_end_val">

												<label for="analytify_start"><?php analytify_e( 'From:', 'wp-analytify' ) ?></label>
												<input type="text" id="analytify_start" value="<?php echo isset( $s_date ) ? $s_date :
													'' ?>">
												<label for="analytify_end"><?php analytify_e( 'To:', 'wp-analytify' ) ?></label>
												<input type="text" id="analytify_end" value="<?php echo isset( $ed_date ) ? $ed_date :
													'' ?>">
												<div class="analytify_arrow_date_picker"></div>
											</div>
											<input type="submit" value="<?php _e( 'View Stats', 'analytify-analytics-dashboard-widget' ) ?>" name="view_data" class="analytify_submit_date_btn">
											<select  id="analytify_dashboard_stats_type">
												<option value="general-statistics"><?php analytify_e( 'General Statistics', 'wp-analytify' ) ?></option>
												<option value="top-pages-by-views"><?php _e( 'Top pages', 'analytify-analytics-dashboard-widget' ) ?></option>
												<option value="top-countries"><?php _e( 'Top Countries', 'analytify-analytics-dashboard-widget' ) ?></option>
												<option value="top-cities"><?php _e( 'Top Cities', 'analytify-analytics-dashboard-widget' ) ?></option>
												<option value="keywords"><?php _e( 'Keywords', 'analytify-analytics-dashboard-widget' ) ?></option>
												<option value="social-media"><?php analytify_e( 'Social Media', 'wp-analytify' ) ?></option>
												<option value="top-reffers"><?php analytify_e( 'Top Referrers', 'wp-analytify' ) ?></option>
											</select>
											<ul class="analytify_select_date_list">
												<li><?php analytify_e( 'Last 30 days', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_30_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

												<li><?php analytify_e( 'This month', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_this_month_start_date"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

												<li><?php analytify_e( 'Last month', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_month_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


												<li><?php analytify_e( 'Last 3 months', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_3_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

												<li><?php analytify_e( 'Last 6 months', 'wp-analytify' )?>  <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_6_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


												<li><?php analytify_e( 'Last year', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_year_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


												<li><?php analytify_e( 'Custom Range', 'wp-analytify' )?> <span class="custom_range"><?php _e( 'Select a custom date', 'wp-analytify' )?></span></li>
											</ul>
										</form>
									</div>
								</div>
							</div>
						</form>
					</div>
					<?php
				} else {
					echo __( 'Select the Profile', 'analytify-analytics-dashboard-widget' );
				}
			} else {
				echo __( 'Connect your Google account with Analytify', 'analytify-analytics-dashboard-widget' );
			}
		}

				/**
				* Runs on Every Ajax.
				*
				* @since 1.0.0
				*/
				public static function analytify_general_stats() {

					$start_date_val  = strtotime( '- 7 days' );
					$end_date_val    = strtotime( 'now' );
					$start_date 	   = isset( $_POST['startDate'] ) ? $_POST['startDate'] : date( 'Y-m-d', $start_date_val );
					$end_date 		   = isset( $_POST['endDate'] )  ? $_POST['endDate'] : date( 'Y-m-d', $end_date_val );
					$stats_type 		 = isset( $_POST['stats_type'] ) ? $_POST['stats_type'] : 'general-statistics';
					$wp_analytify 	 = $GLOBALS['WP_ANALYTIFY'];

					$acces_token  = get_option( 'post_analytics_token' );
					if ( $acces_token ) {
						?>
						<div class="analytify_wraper">
							<div id="inner_analytify_dashboard">
								<?php
								$_analytify_profile = get_option( 'wp-analytify-profile' );
								$dashboard_profile_id = $_analytify_profile['profile_for_dashboard'];

								if ( 'general-statistics' === $stats_type ) {
									$stats = get_transient( md5( 'show-overall-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
									if ( $stats === false ) {
										$stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users', $start_date, $end_date );
										set_transient( md5( 'show-overall-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $stats, 60 * 60 * 20 );
									}

									// New vs Returning Users
									$new_returning_stats = get_transient( md5( 'show-default-new-returning-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
									if ( $new_returning_stats === false ) {
										$new_returning_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:userType' );
										set_transient( md5( 'show-default-new-returning-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $new_returning_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/general-stats.php';
									pa_include_general( $wp_analytify, $stats, $new_returning_stats );

								} else if( 'top-pages-by-views' === $stats_type ) {

									$top_page_stats = get_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
									if ( false === $top_page_stats ) {
										$top_page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', 'ga:pageTitle!=(not set)', 50 );
										set_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_page_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-pages-stats.php';
									pa_include_top_pages_stats( $wp_analytify, $top_page_stats );

								} else if( 'top-countries' === $stats_type ){

									$top_countries_stats = get_transient( md5( 'show-top-countries-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
									if ( false === $top_countries_stats ) {
										$top_countries_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:country' , '-ga:sessions' , 'ga:country!=(not set)', 50 );
										set_transient( md5( 'show-top-countries-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_countries_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-countries-stats.php';
									pa_include_countries_pages_stats( $wp_analytify, $top_countries_stats );

								} else if( 'top-cities' === $stats_type ){

									$top_cities_stats = get_transient( md5( 'show-top-cities-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
									if ( false === $top_cities_stats ) {
										$top_cities_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:city' , '-ga:sessions' , 'ga:city!=(not set)', 50 );
										set_transient( md5( 'show-top-cities-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_cities_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-cities-stats.php';
									pa_include_cities_stats( $wp_analytify, $top_cities_stats );

								} else if( 'keywords'=== $stats_type ) {

									$top_keywords_stats = get_transient( md5( 'show-top-keywords-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );

									if ( false === $top_keywords_stats ) {
										$top_keywords_stats = $wp_analytify->pa_get_analytics_dashboard(  'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 50 );
										set_transient( md5( 'show-top-keywords-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_keywords_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-keywords-stats.php';
									pa_include_keywords_stats( $wp_analytify, $top_keywords_stats );

								}	else if( 'social-media' === $stats_type ) {

									$top_socialmedia_stats = get_transient( md5( 'show-top-socialmedia-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );

									if ( false === $top_socialmedia_stats ) {
										$top_socialmedia_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions', 'ga:socialNetwork!=(not set)' , 50 );
										set_transient( md5( 'show-top-socialmedia-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_socialmedia_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-socialmedia-stats.php';
									pa_include_socialmedia_stats( $wp_analytify, $top_socialmedia_stats );

								} else if( 'top-reffers' === $stats_type ) {

									$top_reffers_stats = get_transient( md5( 'show-top-reffers-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );

									if ( false === $top_reffers_stats ) {
										$top_reffers_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 50 );
										set_transient( md5( 'show-top-reffers-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_reffers_stats, 60 * 60 * 20 );
									}

									include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-reffers-stats.php';
									pa_include_reffers_stats( $wp_analytify, $top_reffers_stats );

								}

								?>
							</div></div>
							<?php
						}
						wp_die();
					}

					/**
					 * Check is user have access to check deshboard.
					 * @return boolean
					 *
					 * @since 1.0.5
					 */
					function is_access() {
						$is_access_level = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard', array( 'administrator' ) );

						return	$GLOBALS['WP_ANALYTIFY']->pa_check_roles( $is_access_level );
					}

				} // End of class.
			}



			/**
			* Helper function for translation.
			*/
			if ( ! function_exists( 'analytify__' ) ) {
				/**
				* Wrapper for __() gettext function.
				* @param  string $string     Translatable text string
				* @param  string $textdomain Text domain, default: wp-analytify
				* @return void
				*/
				function analytify__( $string, $textdomain = 'wp-analytify' ) {
					return __( $string, $textdomain );
				}
			}

			if ( ! function_exists( 'analytify_e' ) ) {
				/**
				* Wrapper for _e() gettext function.
				* @param  string $string     Translatable text string
				* @param  string $textdomain Text domain, default: wp-analytify
				* @return void
				*/
				function analytify_e( $string, $textdomain = 'wp-analytify' ) {
					echo __( $string, $textdomain );
				}
			}

			?>
