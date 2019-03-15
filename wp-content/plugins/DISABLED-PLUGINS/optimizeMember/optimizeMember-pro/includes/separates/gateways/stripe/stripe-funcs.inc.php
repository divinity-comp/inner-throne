<?php
/**
 * Loads functions created for Stripe.
*/
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit ('Do not access this file directly.');
/*
Include all functions that came with this Payment Gateway.
*/
if(is_dir($ws_plugin__optimizemember_pro_temp_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/functions/separates/gateways/stripe'))
	foreach(scandir($ws_plugin__optimizemember_pro_temp_dir) as $ws_plugin__optimizemember_pro_temp_s) // Scan all files in this directory.
		if(preg_match('/\.php$/', $ws_plugin__optimizemember_pro_temp_s) && preg_match('/^stripe-/i', $ws_plugin__optimizemember_pro_temp_s))
			include_once $ws_plugin__optimizemember_pro_temp_dir.'/'.$ws_plugin__optimizemember_pro_temp_s;

unset($ws_plugin__optimizemember_pro_temp_dir, $ws_plugin__optimizemember_pro_temp_s);