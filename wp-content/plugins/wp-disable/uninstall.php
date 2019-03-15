<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }
require_once dirname( __FILE__ ) . '/lib/class-wpperformance.php';
require_once dirname( __FILE__ ) . '/lib/class-optimisationio-dashboard.php';
WpPerformance::delete_options();
WpPerformance::delete_transients();
WpPerformance::unschedule_spam_comments_delete();
Optimisationio_Dashboard::delete_transients();