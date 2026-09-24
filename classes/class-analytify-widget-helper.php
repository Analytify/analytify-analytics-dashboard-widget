<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName,Universal.Files.SeparateFunctionsFromOO.Mixed
/**
 * Helper methods for the widget add-on.
 *
 * @package Analytify
 * @subpackage Dashboard Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * A class that contains helper methods for the widget add-on.
 */
class AnalytifyWidgetHelper {

	/**
	 * Shows the message in a markup wrapper.
	 *
	 * @param string $message Message to display. Used in included template.
	 * @return void
	 */
	public static function notice( $message ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- $message is used in included template.
		require_once ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/notice.php';
	}

	/**
	 * Returns start and end date.
	 *
	 * @return array
	 */
	public static function get_dates() {

		$dates = array(
			'start' => '',
			'end'   => '',
		);

		// Check if POST data exists and verify nonce for security.
		// Nonce verification is handled by the REST API endpoint that calls this method.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by REST API endpoint.
		if ( isset( $_POST['startDate'] ) && ! empty( $_POST['startDate'] ) && isset( $_POST['endDate'] ) && ! empty( $_POST['endDate'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by REST API endpoint.
			$dates['start'] = sanitize_text_field( wp_unslash( $_POST['startDate'] ) );
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by REST API endpoint.
			$dates['end'] = sanitize_text_field( wp_unslash( $_POST['endDate'] ) );
			return $dates;
		}

		$dates['start'] = wp_date( 'Y-m-d', strtotime( '- 7 days' ) );
		$dates['end']   = wp_date( 'Y-m-d', strtotime( 'now' ) );

		$differ = get_option( 'analytify_widget_date_differ' );

		if ( $differ ) {
			switch ( $differ ) {

				case 'current_day':
					$dates['start'] = wp_date( 'Y-m-d' );
					break;

				case 'yesterday':
					$dates['start'] = wp_date( 'Y-m-d', strtotime( '-1 days' ) );
					$dates['end']   = wp_date( 'Y-m-d', strtotime( '-1 days' ) );
					break;

				case 'last_7_days':
					$dates['start'] = wp_date( 'Y-m-d', strtotime( '-7 days' ) );
					break;

				case 'last_14_days':
					$dates['start'] = wp_date( 'Y-m-d', strtotime( '-14 days' ) );
					break;

				case 'last_30_days':
					$dates['start'] = wp_date( 'Y-m-d', strtotime( '-1 month' ) );
					break;

				case 'this_month':
					$dates['start'] = wp_date( 'Y-m-01' );
					break;

				case 'last_month':
					$dates['start'] = wp_date( 'Y-m-01', strtotime( '-1 month' ) );
					$dates['end']   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
					break;

				case 'last_3_months':
					$dates['start'] = wp_date( 'Y-m-01', strtotime( '-3 month' ) );
					$dates['end']   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
					break;

				case 'last_6_months':
					$dates['start'] = wp_date( 'Y-m-01', strtotime( '-6 month' ) );
					$dates['end']   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
					break;

				case 'last_year':
					$dates['start'] = wp_date( 'Y-m-01', strtotime( '-1 year' ) );
					$dates['end']   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
					break;

				default:
					break;
			}
		}

		return $dates;
	}
}
