<?php
/**
 * Main dashboard widget view template.
 *
 * @package Analytify
 * @subpackage Dashboard Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="analytify_wraper">
	<div class="analytify-activation-card-header">
		<img src="<?php echo esc_url( ANALYTIFY_WIDGET_PATH . 'assets/images/logo.svg' ); ?>">
	</div>
	<div class="analytify-widget-form">
		<div class="analytify_main_setting_bar">
			<div class="analytify_setting">
				<div class="analytify_select_date">
					<form class="analytify_form_date" action="" method="post">
						<div class="analytify_select_date_fields">
							<input type="hidden" name="st_date" id="analytify_start_val">
							<input type="hidden" name="ed_date" id="analytify_end_val">
							<input type="hidden" name="analytify_widget_date_differ" id="analytify_widget_date_differ">

							<input type="hidden" name="analytify_date_start" id="analytify_date_start" value="<?php echo esc_attr( isset( $date['start'] ) ? $date['start'] : '' ); ?>">
							<input type="hidden" name="analytify_date_end" id="analytify_date_end" value="<?php echo esc_attr( isset( $date['end'] ) ? $date['end'] : '' ); ?>">

							<label for="analytify_start"><?php esc_html_e( 'From:', 'analytify-analytics-dashboard-widget' ); ?></label>
							<input type="text" required id="analytify_start" value="">
							<label for="analytify_end"><?php esc_html_e( 'To:', 'analytify-analytics-dashboard-widget' ); ?></label>
							<input type="text" onpaste="return: false;" oncopy="return: false;" autocomplete="off" required id="analytify_end" value="">

							<div class="analytify_arrow_date_picker"></div>
						</div>
						<div class="analytify-dashboard-stats-opts">
							<select  id="analytify_dashboard_stats_type">
								<option value="general-statistics"><?php esc_html_e( 'General Statistics', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="real-time-statistics"><?php esc_html_e( 'Real-Time', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="top-pages-by-views"><?php esc_html_e( 'Top Pages', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="top-posts-by-views"><?php esc_html_e( 'Top Posts', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="top-countries"><?php esc_html_e( 'Top Countries', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="top-cities"><?php esc_html_e( 'Top Cities', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="keywords"><?php esc_html_e( 'Keywords', 'analytify-analytics-dashboard-widget' ); ?></option>
								<?php
								if ( 'ga3' === $ga_mode ) {
									?>
									<option value="social-media"><?php esc_html_e( 'Social Media', 'analytify-analytics-dashboard-widget' ); ?></option><?php } ?>
								<option value="top-reffers"><?php esc_html_e( 'Top Referrers', 'analytify-analytics-dashboard-widget' ); ?></option>
								<option value="visitors-devices"><?php esc_html_e( 'Visitors Devices', 'analytify-analytics-dashboard-widget' ); ?></option>
							</select>
							<input type="submit" value="<?php esc_attr_e( 'View Stats', 'analytify-analytics-dashboard-widget' ); ?>" name="view_data" class="analytify_submit_date_btn">
						</div>
						<?php
						$date_list = WPANALYTIFY_Utils::get_date_list();
						if ( $date_list ) {
							echo wp_kses_post( $date_list );
						}
						?>
					</form>
				</div>
			</div>
		</div>
		<div class="analytify-dashboard-inner">
			<div class="analytify_wraper">
				<div id="inner_analytify_dashboard" class="stats_loading">
					<div id="analytify_chart_visitor_devices"> </div>
					<div class="analytify_general_status analytify_status_box_wraper analytify_widget_return_wrapper"></div>
				</div>
			</div>
		</div>
	</div>
	<?php
	if ( $footer ) {
		echo wp_kses_post( $footer );
	}
	?>
</div>
