<?php

if( ! defined('ABSPATH') ){
	// exit if accessed directly
	exit;
}

if ( class_exists( 'WPANALYTIFY_AJAX' ) ){

	//wp_die('Test');

	if( ! class_exists( 'WPANALYTIFYPRO_AJAX') ) {

		/*
		 * Handling all the AJAX calls in WP Analytify
		 * @since 1.2.4
		 * @class WPANALYTIFY_AJAX
		 */
		class WPANALYTIFYPRO_AJAX extends WPANALYTIFY_AJAX{

			public static function init(){

				parent::init();

				$ajax_calls = array(
					'load_mobile_stats'	     => false,
					'load_real_time_stats'	 => false,
					'load_online_visitors'	 => true,
					'load_ajax_error'        => false,
					'load_404_error'         => false,
					'load_javascript_error'  => false,
					'load_default_ajax_error' => false,
					'load_default_404_error' => false,
					'load_default_javascript_error'  => false,
					'load_detail_realtime_stats' => false,
					'export_csv' => false,
					);

				foreach ($ajax_calls as $ajax_call => $no_priv) {
					# code...
					add_action( 'wp_ajax_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );

					if ( $no_priv ) {
						add_action( 'wp_ajax_nopriv_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );
					}
				}
			}


			public static function load_mobile_stats() {

				$wp_analytify = $GLOBALS['WP_ANALYTIFY'];

				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET["start_date"];
				$end_date             = $_GET["end_date"];

				if (is_array( self::$show_settings ) and in_array( 'show-mobile-dashboard', self::$show_settings )){

					$mobile_stats = get_transient( md5('show-mobile-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ) ;
					if( $mobile_stats === false ) {
						$mobile_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:mobileDeviceInfo', '-ga:sessions', false, 5);
						set_transient(  md5('show-mobile-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $mobile_stats, 60 * 60 * 20 );
					}

					if ( isset( $mobile_stats->totalsForAllResults )) {
					  include ANALYTIFY_PRO_ROOT_PATH . '/views/admin/mobile-stats.php';
					  pa_include_mobile($wp_analytify, $mobile_stats);
					}
				}

				die();
			}


			// public static function load_real_time_stats(){

			// 	$wp_analytify = $GLOBALS['WP_ANALYTIFY'];

			// 	if (is_array( self::$show_settings ) and in_array( 'show-real-time', self::$show_settings )){

			// 		include ANALYTIFY_PRO_ROOT_PATH . '/views/admin/realtime-stats.php';
			// 		pa_include_realtime( self );

			// 	}

			// 	die();
			// }



			public static function load_online_visitors() {

				//echo 'Ok';
				//die('kkk');


				if (! isset( $_POST['pa_security'] ) OR ! wp_verify_nonce( $_POST['pa_security'] , 'pa_get_online_data' ) ) {
					return;
				}

				if (! function_exists( 'curl_version' ) ) {
					die('cURL not exists.');
				}

				print_r( stripslashes( json_encode( self::pa_realtime_data( ) ) ) );

				die();
			}

			/**
			 * Grab RealTime Data
			 */
			public static function pa_realtime_data() {

				// revoke, if already quota error.
				if ( get_transient( 'analytify_quota_exception' ) ) {
					return false;
				}

				$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
				$profile_id   = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
				$metrics      = 'ga:activeVisitors';
				$dimensions   = 'ga:source,ga:keyword,ga:trafficType,ga:visitorType';

				try {

					$data = $wp_analytify->service->data_realtime->get ( 'ga:' . $profile_id, $metrics, array(
						'dimensions' => $dimensions
					) );

				} catch ( Exception $e ) {
					return false;
				}

				return $data;
			}

			/**
			 * Run on details realtime stats.
			 *
			 * @since 2.0.0
			 */
			public static function load_detail_realtime_stats() {
				if (! isset( $_POST['pa_security'] ) OR ! wp_verify_nonce( $_POST['pa_security'] , 'pa_get_online_data' ) ) {
					return;
				}

				if (! function_exists( 'curl_version' ) ) {
					die('cURL not exists.');
				}

				if ( defined( 'JSON_UNESCAPED_UNICODE' ) ){
					print_r( stripslashes( json_encode( self::pa_details_realtime_data( ), JSON_UNESCAPED_UNICODE ) ) );
				} else {
					print_r( stripslashes( json_encode( self::pa_details_realtime_data( ) ) ) );
				}

				die();
			}

				/**
				 * Grab data for detail realtime stats.
				 *
				 *
				 * @since 2.0.0
				 */
			public static function pa_details_realtime_data() {

				$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
				$profile_id   = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
				$metrics      = 'ga:activeVisitors';
				$dimensions   = 'ga:pagePath,ga:source,ga:keyword,ga:trafficType,ga:visitorType,ga:pageTitle';


				try {

					$data = $wp_analytify->service->data_realtime->get ( 'ga:' . $profile_id, $metrics,  array (
					'dimensions' => $dimensions
					)  );
				}
				catch ( Exception $e ) {
					update_option ( 'pa_lasterror_occur', esc_html($e));
					return '';
				}

				return $data;
			}


			public static function load_ajax_error( ) {

				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET['start_date'];
				$end_date             = $_GET['end_date'];

				$ajax_error = get_transient( md5( 'show-ajax-error' . $dashboard_profile_ID . $start_date . $end_date ) );

				if ( $ajax_error === false ) {
					$ajax_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==Ajax Error', 5 );
					set_transient( md5( 'show-ajax-error' . $dashboard_profile_ID . $start_date . $end_date ) , $ajax_error, 60 * 60 * 20 );
				}

				if ( isset( $ajax_error->totalsForAllResults ) ) {
					include ANALYTIFY_PRO_ROOT_PATH . '/views/admin/miscellaneous-error-stats.php';

					pa_include_miscellaneous_error_stats( $wp_analytify , $ajax_error , 'Ajax Errors' );
				}
				wp_die(  );
			}

			public static function load_404_error( ) {
				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET['start_date'];
				$end_date             = $_GET['end_date'];

				$page_404_error = get_transient( md5( 'show-404-error' . $dashboard_profile_ID . $start_date . $end_date ) );

				if ( $page_404_error === false ) {
					$page_404_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==404 Error', 5 );
					set_transient( md5( 'show-404-error' . $dashboard_profile_ID . $start_date . $end_date ) , $page_404_error, 60 * 60 * 20 );
				}

				if ( $page_404_error->totalsForAllResults ) {

					include ANALYTIFY_PRO_ROOT_PATH . '/views/admin/miscellaneous-error-stats.php';
					pa_include_miscellaneous_error_stats( $wp_analytify , $page_404_error , '404 Errors' );
				}

				wp_die( );
			}

			public static function load_javascript_error( ) {

				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET['start_date'];
				$end_date             = $_GET['end_date'];

				$javascript_error = get_transient( md5( 'show-javascript-error' . $dashboard_profile_ID . $start_date . $end_date ) );

				if ( $javascript_error === false ) {
					$javascript_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==JavaScript Error', 5 );
					set_transient( md5( 'show-javascript-error' . $dashboard_profile_ID . $start_date . $end_date ) , $javascript_error, 60 * 60 * 20 );
				}

				if ( $javascript_error->totalsForAllResults ) {
					include ANALYTIFY_PRO_ROOT_PATH . '/views/admin/miscellaneous-error-stats.php';
					pa_include_miscellaneous_error_stats( $wp_analytify , $javascript_error , 'Javascript Errors' );
				}

				wp_die( );
			}

			public static function load_default_ajax_error() {

				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET['start_date'];
				$end_date             = $_GET['end_date'];

				$ajax_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==Ajax Error', 5, 'show-top-ajax-errors' );

				if ( $ajax_error ) {
					include ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/ajax-error.php';
					fetch_error( $wp_analytify, $ajax_error );

				}

				wp_die( );
			}

			public static function load_default_404_error() {

				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET['start_date'];
				$end_date             = $_GET['end_date'];

				$page_404_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==404 Error', 5, 'show-top-404-pages' );

				if ( $page_404_error ) {
					include ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/404-error.php';
					fetch_error( $wp_analytify, $page_404_error );
				}

				wp_die();

			}

			public static function load_default_javascript_error() {

				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
				$start_date           = $_GET['start_date'];
				$end_date             = $_GET['end_date'];

				$javascript_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==JavaScript Error', 5, 'show-top-js-errors' );

				if ( $javascript_error ) {
					include ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/javascript-error.php';
					fetch_error( $wp_analytify, $javascript_error );
				}

				wp_die();

			}

			/**
			* Calculate the Stats on Export
			*
			* @since 2.0.17
			*/
			public static function export_csv() {

				check_ajax_referer( 'analytify_export_nonce', 'security' );

				$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
				$start_date           = $_POST['start_date'];
				$end_date             = $_POST['end_date'];
				$stats_type           = sanitize_text_field( wp_unslash( $_POST['stats_type'] ) );

				if ( 'top-pages' == $stats_type ) {
					$top_page_stats = $wp_analytify->pa_get_analytics_dashboard('ga:pageviews,ga:avgTimeOnPage,ga:bounceRate', $start_date, $end_date, 'ga:PageTitle,ga:pagePath', '-ga:pageviews', false, 100 );

					$modify_data = 	$top_page_stats['rows'];
					$dashboard_profile_ID = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
					$site_url = WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' );
					foreach (	$top_page_stats['rows'] as $key => $value ) {
						$modify_data[ $key ][1] = $site_url . $value[1];
					}
					$_columns = array( array(
						'0' => 'Title',
						'1' => 'Link',
						'2' => 'Views',
						'3' => 'Avg. Time',
						'4' => 'Bounce Rate',
					) );
					$data = array_merge( $_columns, $modify_data );

				} elseif ( 'top-countries' == $stats_type ) {
					$countries_stats 	= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:country' , '-ga:sessions' , 'ga:country!=(not set)', 100 );

					$_columns = array( array(
						'0' => 'Country',
						'1' => 'Views'
					) );
					$data = array_merge( $_columns, $countries_stats['rows'] );

				} elseif ( 'top-cities' == $stats_type ) {
					$cities_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:city,ga:country' , '-ga:sessions' , 'ga:city!=(not set);ga:country!=(not set)', 100 );

					$_columns = array( array(
						'0' => 'City',
						'1' => 'Country',
						'2' => 'Views'
					) );
					$data = array_merge( $_columns, $cities_stats['rows'] );

				} elseif ( 'top-keywords' == $stats_type ) {
					$keyword_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 100 );
					$_columns = array( array(
						'0' => 'Keyword',
						'1' => 'Views'
					) );
					$data = array_merge( $_columns, $keyword_stats['rows'] );

				} elseif ( 'top-social-media' == $stats_type ) {
					$social_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions', 'ga:socialNetwork!=(not set)', 100 );
					$_columns = array( array(
						'0' => 'Social Media',
						'1' => 'Views'
					) );
					$data = array_merge( $_columns, $social_stats['rows'] );

				} elseif ( 'top-reffers' == $stats_type ) {
					$referr_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 100 );
					$_columns = array( array(
						'0' => 'Referrers',
						'1' => 'Type',
						'2' => 'Views'
					) );
					$data = array_merge( $_columns, $referr_stats['rows'] );

				} elseif ( 'what-happen' == $stats_type ) {
					$page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:entrances,ga:exits,ga:entranceRate,ga:exitRate', $start_date, $end_date , 'ga:pageTitle,ga:pagePath' , '-ga:entrances' , false, 100 );

					$modify_data = 	$page_stats['rows'];
					$dashboard_profile_ID = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
					$site_url = WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' );

					foreach (	$page_stats['rows'] as $key => $value ) {
						$modify_data[ $key ][1] = $site_url . $value[1];
					}

					$_columns = array( array(
						'0' => 'Title',
						'1' => 'Link',
						'2' => 'Entrance',
						'3' => 'Exits',
						'4' => 'Entrance%',
						'5' => 'Exits%',
					) );

					$data = array_merge( $_columns, $modify_data );
				} elseif ( 'top-ajax' == $stats_type ) {
					$ajax_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==Ajax Error', 100 );
					$_columns = array( array(
						'0' => 'Error',
						'1' => 'Link',
						'2' => 'Hits'
					) );
					$data = array_merge( $_columns, $ajax_error['rows'] );

				} elseif ( 'top-404' == $stats_type ) {
					$page_404_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==404 Error', 100 );
					$_columns = array( array(
						'0' => 'Error',
						'1' => 'Link',
						'2' => 'Hits'
					) );
					$data = array_merge( $_columns, $page_404_error['rows'] );

				} elseif ( 'top-js-error' == $stats_type ) {
					$javascript_error = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventAction,ga:eventLabel', '-ga:totalEvents' , 'ga:eventCategory==JavaScript Error', 100 );
					$_columns = array( array(
						'0' => 'Error',
						'1' => 'Link',
						'2' => 'Hits'
					) );
					$data = array_merge( $_columns, $javascript_error['rows'] );

				} elseif ( 'top-browsers' == $stats_type ) {
					$browser_stats 	= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:browser,ga:operatingSystem' , '-ga:sessions' , 'ga:browser!=(not set);ga:operatingSystem!=(not set)', 100 );
					$_columns = array( array(
						'0' => 'Browser',
						'1' => 'Operating System',
						'2' => 'Visits'
					) );
					$data = array_merge( $_columns, $browser_stats['rows'] );

				} elseif ( 'top-operating-system' == $stats_type ) {
					$os_stats 			= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:operatingSystem,ga:operatingSystemVersion' , '-ga:sessions' , 'ga:operatingSystemVersion!=(not set)', 100 );
					$_columns = array( array(
						'0' => 'Operating System',
						'1' => 'Version',
						'2' => 'Visits'
					) );
					$data = array_merge( $_columns, $os_stats['rows'] );

				} elseif ( 'top-mobile-device' == $stats_type ) {
					$mobile_stats 	= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:mobileDeviceBranding,ga:mobileDeviceModel' , '-ga:sessions' , 'ga:mobileDeviceModel!=(not set);ga:mobileDeviceBranding!=(not set)', 100 );
					$_columns = array( array(
						'0' => 'Operating System',
						'1' => 'Version',
						'2' => 'Visits'
					) );
					$data = array_merge( $_columns, $mobile_stats['rows'] );

				}
				update_option( 'analytify_csv_data', $data );

				wp_die();
			}


		}

		WPANALYTIFYPRO_AJAX::init();

	}
}
