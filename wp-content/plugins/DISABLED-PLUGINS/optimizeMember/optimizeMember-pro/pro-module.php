<?php
/**
* The main plugin file.
*
* This file loads the plugin after checking
* PHP, WordPress and other compatibility requirements.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress}
*
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
* 	You should have received a copy of the GNU General Public License,
* 	along with this software. In the main directory, see: /licensing/
* 	If not, see: {@link http://www.gnu.org/licenses/}.
*
*
* Unless you have our prior written consent, you must NOT directly or indirectly license,
* sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Module;
* or make an offer to do any of these things. All of these things are strictly
* prohibited with part (2) of the optimizeMember Pro Module.
*
* @package optimizeMember
* @since 1.0
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**
* The installed version of optimizeMember Pro.
*
* @package optimizeMember
* @since 1.0
*
* @var str
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_PRO_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_PRO_VERSION", "1.2.7" /* !#distro-version#! */);
/**
* Minimum PHP version required to run optimizeMember Pro.
*
* @package optimizeMember
* @since 1.0
*
* @var str
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_PHP_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_PHP_VERSION", "5.2" /* !#php-requires-at-least-version#! */);
/**
* Minimum WordPress version required to run optimizeMember Pro.
*
* @package optimizeMember
* @since 1.0
*
* @var str
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_WP_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_WP_VERSION", "3.2" /* !#wp-requires-at-least-version#! */);
/**
* Minimum Framework version required by optimizeMember Pro.
*
* @package optimizeMember
* @since 1.0
*
* @var str
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_FRAMEWORK_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_FRAMEWORK_VERSION", "1.2.7" /* !#distro-version#! */);
/*
Several compatibility checks.
If all pass, load the optimizeMember plugin.
*/
if(version_compare(PHP_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_PHP_VERSION, ">=") && version_compare(get_bloginfo("version"), WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_WP_VERSION, ">=") && defined("WS_PLUGIN__OPTIMIZEMEMBER_VERSION") && defined("WS_PLUGIN__OPTIMIZEMEMBER_MIN_PRO_VERSION") && version_compare(WS_PLUGIN__OPTIMIZEMEMBER_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_FRAMEWORK_VERSION, ">=") && version_compare(WS_PLUGIN__OPTIMIZEMEMBER_PRO_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_MIN_PRO_VERSION, ">=") && !isset($GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]))
	{
		$GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["l"] = __FILE__;
		/*
		Hook before loaded.
		*/
		do_action("ws_plugin__optimizemember_pro_before_loaded");
		/*
		System configuraton.
		*/
		include_once dirname(__FILE__)."/includes/syscon.inc.php";
		/*
		Hooks and Filters.
		*/
		include_once dirname(__FILE__)."/includes/hooks.inc.php";
		/*
		Hook after system config & Hooks are loaded.
		*/
		do_action("ws_plugin__optimizemember_pro_config_hooks_loaded");
		/*
		Function includes.
		*/
		include_once dirname(__FILE__)."/includes/funcs.inc.php";
		/*
		Include Shortcodes.
		*/
		include_once dirname(__FILE__)."/includes/codes.inc.php";
		/*
		Hooks after loaded.
		*/
		do_action("ws_plugin__optimizemember_pro_loaded");
		do_action("ws_plugin__optimizemember_pro_after_loaded");
	}
/*
Else NOT compatible. Do we need admin compatibility errors now?
*/
else if(is_admin()) /* Admin compatibility errors. */
	{
		if(!version_compare(PHP_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_PHP_VERSION, ">="))
			{
				add_action("all_admin_notices", create_function('', 'echo \'<div class="error fade"><p>You need PHP v\' . WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_PHP_VERSION . \'+ to use the optimizeMember Pro Module.</p></div>\';'));
			}
		else if(!version_compare(get_bloginfo("version"), WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_WP_VERSION, ">="))
			{
				add_action("all_admin_notices", create_function('', 'echo \'<div class="error fade"><p>You need WordPress v\' . WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_WP_VERSION . \'+ to use the optimizeMember Pro Module.</p></div>\';'));
			}
		else if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_VERSION") || !defined("WS_PLUGIN__OPTIMIZEMEMBER_MIN_PRO_VERSION") || !version_compare(WS_PLUGIN__OPTIMIZEMEMBER_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_FRAMEWORK_VERSION, ">="))
			{
				add_action("all_admin_notices", create_function('', 'echo \'<div class="error fade"><p>In order to load the optimizeMember Pro Module, you need the <a href="\' . c_ws_plugin__optimizemember_readmes::parse_readme_value ("Plugin URI") . \'" target="_blank">optimizeMember Framework</a>, v\' . WS_PLUGIN__OPTIMIZEMEMBER_PRO_MIN_FRAMEWORK_VERSION . \'+. It\\\'s free.</p></div>\';'));
			}
		else if(!version_compare /* They need to upgrade optimizeMember Pro? */(WS_PLUGIN__OPTIMIZEMEMBER_PRO_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_MIN_PRO_VERSION, ">=") && file_exists(dirname(__FILE__)."/includes/classes/upgrader.inc.php"))
			{
				include_once /* Include upgrader class. optimizeMember Pro autoload functionality will NOT be available in this scenario. Using ``include_once()``. */ dirname(__FILE__)."/includes/classes/upgrader.inc.php";
				add_action("admin_init", "c_ws_plugin__optimizemember_pro_upgrader::upgrade").add_action("all_admin_notices", create_function('', 'echo c_ws_plugin__optimizemember_pro_upgrader::wizard ();'));
			}
	}
?>