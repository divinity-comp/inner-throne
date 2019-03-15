<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

function pa_include_detail_demographic () {

  $dashboard_profile_ID = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );

  $is_access_level =  $GLOBALS['WP_ANALYTIFY']->settings->get_option('show_analytics_roles_dashboard','wp-analytify-dashboard');

  $acces_token  = get_option( "post_analytics_token" );
  if( ! $acces_token ) {
    analytify_e( 'You must be authenticate to see the Analytics Dashboard.', 'wp-analytifye' );
  }

  if( ! $GLOBALS['WP_ANALYTIFY']->pa_check_roles( $is_access_level ) ) {
    analytify_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify' );
    return;
  }

  $start_date_val = strtotime( '-1 month' );
  $end_date_val   = strtotime( 'now' );
  $start_date     = date( 'Y-m-d', $start_date_val );
  $end_date       = date( 'Y-m-d', $end_date_val );

  if ( isset( $_POST['view_data'] ) ) {

    $s_date   = sanitize_text_field( wp_unslash( $_POST['st_date'] ) );
    $ed_date  = sanitize_text_field( wp_unslash( $_POST['ed_date'] ) );
  }

  if ( isset( $s_date ) ) {
    $start_date = $s_date ;
  }

  if ( isset( $ed_date ) ) {
    $end_date = $ed_date;
  }

  $demogragphic_stats = $GLOBALS['WP_ANALYTIFY']->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date,'ga:userAgeBracket,ga:userGender', false, false, 20, 'show-demographic-stats' );

  ?>
  <div class="analytify_wraper">
    <div class="analytify_main_title_section">

      <h1 class="analytify_pull_left analytify_main_title"><a href="<?php echo admin_url('admin.php?page=analytify-dashboard');?>"><?php esc_html_e( 'Dashboard', 'wp-analytify-pro' ); ?></a>

        <span class="analytify_stats_of"><?php esc_html_e( 'Demographic Statistics of the Site', 'wp-analytify-pro' ); ?> <a href="<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?>" target="_blank"><?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?></a></span></h1>
        <div class="analytify_select_dashboard analytify_pull_right">

          <?php do_action( 'analytify_dashboad_dropdown' ); ?>

        </div>
      </div>

      <div class="analytify_main_setting_bar">
        <!-- <h2 class="analytify_pull_left analytify_t_pad"><?php //esc_html_e( 'REALTIME STATS', 'wp-analytify' ); ?></h2> -->
        <div class="analytify_pull_right analytify_setting">
          <div class="analytify_select_date">
            <form class="analytify_form_date" action="" method="post">
              <div class="analytify_select_date_fields">
                <input type="hidden" name="st_date" id="analytify_start_val">
                <input type="hidden" name="ed_date" id="analytify_end_val">

                <label for="analytify_start"><?php _e( 'From:', 'wp-analytify' )?></label>
                <input type="text" required id="analytify_start" value="<?php echo isset( $s_date ) ? $s_date :
                  '' ?>">
                  <label for="analytify_end"><?php _e( 'To:', 'wp-analytify' )?></label>
                  <input type="text" required id="analytify_end" value="<?php echo isset( $ed_date ) ? $ed_date :
                    '' ?>">
                    <div class="analytify_arrow_date_picker"></div>
                  </div>
                  <input type="submit" value="<?php _e( 'View Stats', 'wp-analytify' ) ?>" name="view_data" class="analytify_submit_date_btn">
                  <?php echo WPANALYTIFY_Utils::get_date_list() ?>
                </form>
              </div>
            </div>
          </div>

          <div class="analytify_general_status analytify_status_box_wraper">
            <div class="analytify_status_header">
              <h3><?php _e( 'Demographic Stats' , 'wp-analytify-pro' ) ?></h3>
            </div>
            <div class="analytify_status_body">
              <div id="" style="">

                <table class="analytify_data_tables">
                  <thead>
                    <tr>
                      <th class="analytify_num_row">#</th>
                      <th class="analytify_txt_left"><?php esc_html_e( 'Age', 'wp-analytify-pro' ); ?></th>
                      <th class="analytify_txt_left"><?php esc_html_e( 'Gender', 'wp-analytify-pro' ); ?></th>
                      <th class="analytify_value_row"><?php esc_html_e( 'Total Views', 'wp-analytify-pro' ); ?></th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php

                    if ( isset( $demogragphic_stats['rows'] ) && $demogragphic_stats['rows'] > 0 ) {

                      $i = 1;
                      foreach ( $demogragphic_stats['rows'] as $demographic ) :
                        ?>
                        <tr>
                          <td class="analytify_txt_center"><?php echo $i; ?></td>
                          <td><?php echo $demographic[0]; ?></td>
                          <td><?php echo $demographic[1]; ?></td>
                          <td class="analytify_txt_center"><?php echo WPANALYTIFY_Utils::pretty_numbers( $demographic[2] ); ?></td>
                        </tr>
                        <?php
                        $i++;
                      endforeach;
                    } else {
                      echo ' <tr> <td  class="analytify_td_error_msg" colspan="3">';
                      $GLOBALS['WP_ANALYTIFY']->no_records();
                      echo  '</td> </tr>';
                    }
                    ?>

                  </tbody>
                </table>
                <div class="analytify_status_footer">
                  <span class="analytify_info_stats"><?php esc_html_e( 'Demographic Stats', 'wp-analytify-pro' ); ?></span>
                </div>

              </div>
            </div>
          </div>


        </div>

  <?php

}


?>
