<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

/* optimizeMember-only mode. Only load the optimizeMember plugin, exclude all others. */

$o_ws_plugin__s2member = preg_replace ("/-o\.php$/", ".php", __FILE__);
$o_ws_plugin__optimizemember_is_loaded_already = (defined ("WS_PLUGIN__OPTIMIZEMEMBER_VERSION")) ? true : false;
$o_ws_plugin__plugins_s2member = WP_PLUGIN_DIR . "/" . basename (dirname ($o_ws_plugin__s2member)) ."/" . basename($o_ws_plugin__s2member);
/**/
if ((!file_exists ($o_ws_plugin__plugins_s2member) || @is_link ($o_ws_plugin__plugins_s2member)) && file_exists ($o_ws_plugin__s2member) && !$o_ws_plugin__optimizemember_is_loaded_already)
	include_once $o_ws_plugin__s2member; /* optimizeMember in a strange location? */
/**/
else if (in_array ($o_ws_plugin__plugins_s2member, wp_get_active_and_valid_plugins ()) && file_exists ($o_ws_plugin__plugins_s2member) && !$o_ws_plugin__optimizemember_is_loaded_already)
	include_once $o_ws_plugin__plugins_s2member;
/**/
else if (apply_filters ("ws_plugin_optimizemember_o_force", false) && !$o_ws_plugin__optimizemember_is_loaded_already) /* Off by default. Force optimizeMember to load? */
	include_once $o_ws_plugin__s2member;
/**/
unset ($o_ws_plugin__plugins_s2member, $o_ws_plugin__optimizemember_is_loaded_already, $o_ws_plugin__s2member);