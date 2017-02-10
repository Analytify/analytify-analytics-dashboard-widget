<?php


function pa_include_top_pages_stats( $current, $top_page_stats) {
?>
  	<div class="analytify_general_status analytify_status_box_wraper">
      	<div class="analytify_status_header">
        	<h3><?php  _e( 'Top pages by views.', 'wp-analytify-dashboard-widget' ); ?></h3>
      	</div>
      	<div class="analytify_status_body">
		  	<table class="analytify_data_tables wp_analytify_paginated">
				<thead>
					<tr>
						<th class="analytify_num_row">#</th>
						<th class="analytify_txt_left"><?php esc_html_e( 'Title', 'wp-analytify-dashboard-widget' ); ?></th>
						<th class="analytify_value_row"><?php esc_html_e( 'Views', 'wp-analytify-dashboard-widget' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					  $i = 1;

					  if( $top_page_stats['rows'] > 0 ) {

						foreach ($top_page_stats['rows'] as $top_page) {

						  ?>
							<tr>
							  <td class="analytify_txt_center"><?php echo $i; ?></td>
							  <td><?php echo $top_page[0]; ?></td>
							  <td class="analytify_txt_center"><?php echo $current->wpa_number_format( $top_page[1] ); ?></td>
							</tr>

						  <?php
						  $i++;

							}
						 }
					  ?>
				</tbody>
			</table>
		</div>
		<div class="analytify_status_footer">
			<div class="wp_analytify_pagination"></div>
		</div>
	</div>
<?php
}
?>
