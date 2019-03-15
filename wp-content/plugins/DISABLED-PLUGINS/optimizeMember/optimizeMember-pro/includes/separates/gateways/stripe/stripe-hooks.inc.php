<?php
/**
 * Primary Hooks/Filters for Stripe.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');
/*
Add the plugin Actions/Filters here.
*/
add_action('init', 'c_ws_plugin__optimizemember_pro_stripe_notify::stripe_notify', 4);

add_action('init', 'c_ws_plugin__optimizemember_pro_stripe_update::stripe_update');
add_action('init', 'c_ws_plugin__optimizemember_pro_stripe_checkout::stripe_checkout');
add_action('init', 'c_ws_plugin__optimizemember_pro_stripe_sp_checkout::stripe_sp_checkout');
add_action('init', 'c_ws_plugin__optimizemember_pro_stripe_registration::stripe_registration');
add_action('init', 'c_ws_plugin__optimizemember_pro_stripe_cancellation::stripe_cancellation');

add_filter('ws_plugin__optimizemember_during_constants_c', 'c_ws_plugin__optimizemember_pro_stripe_constants::stripe_constants', 10, 2);

add_action('wp_ajax_ws_plugin__optimizemember_pro_stripe_ajax_tax', 'c_ws_plugin__optimizemember_pro_stripe_utilities::stripe_ajax_tax');
add_action('wp_ajax_nopriv_ws_plugin__optimizemember_pro_stripe_ajax_tax', 'c_ws_plugin__optimizemember_pro_stripe_utilities::stripe_ajax_tax');

add_action('ws_plugin__optimizemember_during_css', 'c_ws_plugin__optimizemember_pro_stripe_css_js::stripe_css');
add_action('ws_plugin__optimizemember_during_js_w_globals', 'c_ws_plugin__optimizemember_pro_stripe_css_js::stripe_js_w_globals');
add_action('ws_plugin__optimizemember_during_menu_pages_js', 'c_ws_plugin__optimizemember_pro_stripe_admin_css_js::stripe_menu_pages_js');

add_filter('ws_plugin__optimizemember_during_add_admin_options_add_divider_3', 'c_ws_plugin__optimizemember_pro_stripe_menu_pages::stripe_admin_options', 10, 2);

add_action('ws_plugin__optimizemember_during_scripting_page_during_left_sections_during_list_of_api_constants', 'c_ws_plugin__optimizemember_pro_stripe_menu_pages::stripe_scripting_page_api_constants');
add_action('ws_plugin__optimizemember_during_scripting_page_during_left_sections_during_list_of_api_constants_farm', 'c_ws_plugin__optimizemember_pro_stripe_menu_pages::stripe_scripting_page_api_constants');

add_filter('ws_plugin__optimizemember_check_force_ssl', 'c_ws_plugin__optimizemember_pro_stripe_ssl::sc_stripe_form_auto_force_ssl', 10, 2);