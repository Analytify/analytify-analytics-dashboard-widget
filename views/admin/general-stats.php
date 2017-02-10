<?php

/*
 * View of General Statistics
 */

function pa_include_general( $current, $stats) {
?>
    <div class="analytify_general_status analytify_status_box_wraper">
      <div class="analytify_status_header">
        <h3><?php _e( 'General Statistics', 'wp-analytify-dashboard-widget'); ?></h3>
      </div>

      <div class="analytify_status_body">
        <div class="analytify_general_status_boxes_wraper">

          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( 'Sessions', 'wp-analytify-dashboard-widget' ); ?></h4>
            <div class="analytify_general_stats_value"><?php echo number_format( $stats->totalsForAllResults['ga:sessions'] ); ?></div>

          </div>

          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( 'visitors', 'wp-analytify-dashboard-widget' ); ?></h4>
            <div class="analytify_general_stats_value"><?php echo number_format($stats->totalsForAllResults['ga:users']); ?></div>

          </div>


          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( 'Bounce rate', 'wp-analytify-dashboard-widget' ); ?></h4>
            <div class="analytify_general_stats_value"><?php if ($stats->totalsForAllResults['ga:entrances'] <= 0) { ?>
                        0.00%
                    <?php
                      } //$stats->totalsForAllResults['ga:entrances'] <= 0
                      else {
                        echo number_format(round(($stats->totalsForAllResults['ga:bounces'] / $stats->totalsForAllResults['ga:entrances']) * 100, 2), 2);
                    ?><sub>%</sub>
            <?php } ?>
            </div>

          </div>




          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( 'Avg. time on Page', 'wp-analytify-dashboard-widget' ); ?></h4>
            <div class="analytify_general_stats_value"><?php
                  if ($stats->totalsForAllResults['ga:sessions'] <= 0) {
                ?>
                    00:00:00
                <?php
                  } //$stats->totalsForAllResults['ga:sessions'] <= 0
                  else {
                ?>
                <?php
                    echo $current->pa_pretty_time($stats->totalsForAllResults['ga:avgTimeOnPage']);
                ?>
                <?php } ?>
            </div>

          </div>




          <div class="analytify_general_status_boxes">
            <h4><?php echo _e('AVERAGE PAGES' , 'wp-analytify-dashboard-widget')?></h4>
            <div class="analytify_general_stats_value">
                <?php
                  if ($stats->totalsForAllResults['ga:sessions'] <= 0) {
                ?>
                0.00
                <?php
                  } //$stats->totalsForAllResults['ga:sessions'] <= 0
                  else {
                ?>
                <?php
                    echo number_format(round($stats->totalsForAllResults['ga:pageviews'] / $stats->totalsForAllResults['ga:sessions'], 2), 2);
                ?>
                <?php } ?>
            </div>

          </div>



          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( 'Page views', 'wp-analytify-dashboard-widget' ); ?></h4>
            <div class="analytify_general_stats_value"><?php
                  if ($stats->totalsForAllResults['ga:pageviews'] <= 0) {
                      echo '0';
                  }
                  else {
                     echo $current->wpa_number_format( $stats->totalsForAllResults['ga:pageviews']);
                  }
                  ?>
              </div>

          </div>

          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( '% New sessions', 'wp-analytify-dashboard-widget' ); ?></h4>
            <div class="analytify_general_stats_value"><?php

                  $total_sessions   =  $stats->totalsForAllResults['ga:sessions'];
                  $newusers         =  $stats->totalsForAllResults['ga:newUsers'];

                  if( $total_sessions > 0 ){
                      echo number_format(round(($newusers / $total_sessions) * 100, 2), 2);
                  }
                  else{
                      echo '0';
                  }
              ?>
              <sub>%</sub>
            </div>

          </div>

          <div class="analytify_general_status_boxes">
            <h4><?php esc_html_e( 'New vs Returning visitors', 'wp-analytify-dashboard-widget' ); ?></h4>
                <?php echo "<span class='analytify_general_stats_value'>" . $stats->totalsForAllResults['ga:newUsers']. "</span> vs <span class='analytify_general_stats_value'>" . ( $stats->totalsForAllResults['ga:users'] -   $stats->totalsForAllResults['ga:newUsers'] ) . "</span>" ?>

          </div>

        </div>
      </div>

      <div class="analytify_status_footer">
        <span class="analytify_info_stats">
          <?php  _e('Did you know that total time on your site is' , 'wp-analytify-dashboard-widget')?>
          <?php
              echo $current->pa_pretty_time($stats->totalsForAllResults['ga:sessionDuration']);
          ?>
        </span>
      </div>
    </div>


<?php } ?>
