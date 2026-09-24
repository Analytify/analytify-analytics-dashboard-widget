<?php
/**
 * Generates the view for the notice.
 *
 * @package Analytify
 * @subpackage Dashboard Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="analytify-activation-cards">
	<div class="analytify-activation-card-header">
		<img src="<?php echo esc_url( ANALYTIFY_WIDGET_PATH . 'assets/images/logo.svg' ); ?>" alt="Analytify">
	</div>
	<div class="analytify-activation-card-body">
		<?php
		if ( $message ) {
			echo wp_kses_post( $message );
		}
		?>
	</div>
</div>
