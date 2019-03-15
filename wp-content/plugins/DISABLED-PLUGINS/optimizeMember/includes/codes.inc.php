<?php
/**
* Shortcodes for the optimizeMember plugin.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember
* @since 3.0
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/*
Add WordPress Editor Shortcodes.
*/
add_shortcode ("opmKey", "c_ws_plugin__optimizemember_sc_keys::sc_get_key");
add_shortcode ("opmGet", "c_ws_plugin__optimizemember_sc_gets::sc_get_details");
add_shortcode('opmEot', 'c_ws_plugin__optimizemember_sc_eots::sc_eot_details');
add_shortcode ("opmFile", "c_ws_plugin__optimizemember_sc_files::sc_get_file");
add_shortcode ("opmStream", "c_ws_plugin__optimizemember_sc_files::sc_get_stream");
/**/
add_shortcode ("opmIf", "c_ws_plugin__optimizemember_sc_if_conds::sc_if_conditionals");
add_shortcode ("_opmIf", "c_ws_plugin__optimizemember_sc_if_conds::sc_if_conditionals");
add_shortcode ("__opmIf", "c_ws_plugin__optimizemember_sc_if_conds::sc_if_conditionals");
add_shortcode ("___opmIf", "c_ws_plugin__optimizemember_sc_if_conds::sc_if_conditionals");
/**/
add_shortcode ("optimizeMember-Profile", "c_ws_plugin__optimizemember_sc_profile::sc_profile");
add_shortcode ("optimizeMember-PayPal-Button", "c_ws_plugin__optimizemember_sc_paypal_button::sc_paypal_button");
add_shortcode ("optimizeMember-Security-Badge", "c_ws_plugin__optimizemember_sc_s_badge::sc_s_badge");