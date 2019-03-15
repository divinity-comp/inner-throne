<?php
/**
 * Shortcodes for Stripe.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');
/*
Add WordPress Editor Shortcodes.
*/
add_shortcode('optimizeMember-Pro-Stripe-Form', 'c_ws_plugin__optimizemember_pro_stripe_form::sc_stripe_form');
add_shortcode('optimizeMember-Pro-Stripe-xFormOption', 'c_ws_plugin__optimizemember_pro_stripe_form::sc_stripe_form_option');