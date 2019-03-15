<?php
/**
* The main plugin file.
*
* This file loads the plugin after checking
* PHP, WordPress and other compatibility requirements.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ OptimizePress}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember
* @since 1.0
*/
/* -- This section for WordPress parsing. ------------------------------------------------------------------------------

Version: 1.2.7
Stable tag: 1.0.3

SSL Compatible: yes
bbPress Compatible: yes
WordPress Compatible: yes
BuddyPress Compatible: yes
WP Multisite Compatible: yes
Multisite Blog Farm Compatible: yes

PayPal Standard Compatible: yes
PayPal Pro Compatible: yes w/optimizeMember Pro
Authorize.Net Compatible: yes w/optimizeMember Pro
Stripe Compatible: yes w/optimizeMember Pro
ClickBank Compatible: yes w/optimizeMember Pro

Tested up to: 4.7
Requires at least: 3.5

Copyright: © 2009 OptimizePress
License: GNU General Public License
Contributors: optimizePress

Author: OptimizePress
Author URI: http://www.optimizepress.com/

Text Domain: s2member
Domain Path: /includes/translations

Plugin Name: OptimizeMember

Description: optimizeMember, a powerful (free) membership plugin for WordPress. Protect/secure members only content with roles/capabilities.
Tags:, membership, users, user, members, member, subscribers, subscriber, members only, roles, capabilities, capability, register, signup, paypal, paypal pro, pay pal, authorize, authorize.net, google checkout, clickbank, click bank, buddypress, buddy press, bbpress, bb press, shopping cart, cart, checkout, ecommerce

-- end section for WordPress parsing. ------------------------------------------------------------------------------- */
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**
* The installed version of optimizeMember.
*
* @package optimizeMember
* @since 3.0
*
* @var String
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_VERSION", "1.2.7" /* !#distro-version#! */);
/**
* Minimum PHP version required to run optimizeMember.
*
* @package optimizeMember
* @since 3.0
*
* @var String
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_MIN_PHP_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_MIN_PHP_VERSION", "5.2" /* !#php-requires-at-least-version#! */);
/**
* Minimum WordPress version required to run optimizeMember.
*
* @package optimizeMember
* @since 3.0
*
* @var String
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_MIN_WP_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_MIN_WP_VERSION", "3.2" /* !#wp-requires-at-least-version#! */);
/**
* Minimum Pro version required by the Framework.
*
* @package optimizeMember
* @since 3.0
*
* @var String
*/
if(!defined("WS_PLUGIN__OPTIMIZEMEMBER_MIN_PRO_VERSION"))
	define("WS_PLUGIN__OPTIMIZEMEMBER_MIN_PRO_VERSION", "1.2.7" /* !#distro-version#! */);
/*
Several compatibility checks.
If all pass, load the optimizeMember plugin.
*/
if(version_compare(PHP_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_MIN_PHP_VERSION, ">=") && version_compare(get_bloginfo("version"), WS_PLUGIN__OPTIMIZEMEMBER_MIN_WP_VERSION, ">=") && !isset($GLOBALS["WS_PLUGIN__"]["optimizemember"]))
	{
		$GLOBALS["WS_PLUGIN__"]["optimizemember"]["l"] = __FILE__;
		/*
		Hook before loaded.
		*/
		do_action("ws_plugin__optimizemember_before_loaded");
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
		do_action("ws_plugin__optimizemember_config_hooks_loaded");
		/*
		Load a possible Pro module, if/when available.
		*/
		if(apply_filters("ws_plugin__optimizemember_load_pro", true) && file_exists(dirname(__FILE__) . "/optimizeMember-pro/pro-module.php"))
			include_once dirname(__FILE__) . "/optimizeMember-pro/pro-module.php";
		/*
		Configure options and their defaults.
		*/
		ws_plugin__optimizemember_configure_options_and_their_defaults();
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
		do_action("ws_plugin__optimizemember_loaded");
		do_action("ws_plugin__optimizemember_after_loaded");
	}
/*
Else NOT compatible. Do we need admin compatibility errors now?
*/
else if(is_admin()) /* Admin compatibility errors. */
	{
		if(!version_compare(PHP_VERSION, WS_PLUGIN__OPTIMIZEMEMBER_MIN_PHP_VERSION, ">="))
			{
				add_action("all_admin_notices", create_function('', 'echo \'<div class="error fade"><p>You need PHP v\' . WS_PLUGIN__OPTIMIZEMEMBER_MIN_PHP_VERSION . \'+ to use the optimizeMember plugin.</p></div>\';'));
			}
		else if(!version_compare(get_bloginfo("version"), WS_PLUGIN__OPTIMIZEMEMBER_MIN_WP_VERSION, ">="))
			{
				add_action("all_admin_notices", create_function('', 'echo \'<div class="error fade"><p>You need WordPress v\' . WS_PLUGIN__OPTIMIZEMEMBER_MIN_WP_VERSION . \'+ to use the optimizeMember plugin.</p></div>\';'));
			}
	}

	// used in update script below
	define('OPM_PLUGIN_SLUG', plugin_basename(__FILE__));

	/*
	 * called on admin_init
	*/
	function load_opm_plugin_screen() {
		add_thickbox();
		add_action( 'admin_notices', 'update_opm_nag_screen');
	}

	/*
	 * checking and adding admin notices for plugin update
	* @return void
	*/
	function update_opm_nag_screen() {
		//PLUGIN
		$response = get_transient('op_opmplugin_update');

		$plugin_version = WS_PLUGIN__OPTIMIZEMEMBER_VERSION;
		$plugin_slug = OPM_PLUGIN_SLUG;
		list ($t1, $t2) = explode('/', $plugin_slug);
		$pluginName = str_replace('.php', '', $t2);

		if (false === $response)
			return;

		$update_url = wp_nonce_url( 'update.php?action=upgrade-plugin&amp;plugin=' . urlencode($plugin_slug), 'upgrade-plugin_' . $plugin_slug);
		$update_onclick = '';

		if (isset($response->new_version) &&  version_compare( $plugin_version, $response->new_version, '<' ) ) {
			echo '<div id="update-nag">';
			printf( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.',
			'OptimizeMember Plugin',
			$response->new_version,
			admin_url('plugin-install.php?tab=plugin-information&plugin=' . $pluginName  .'&section=changelog&TB_iframe=true&width=640'),
			'OptimizeMember Plugin',
			$update_url,
			$update_onclick
			);
			echo '</div>';
			echo '<div id="' . $pluginName . '_' . 'changelog" style="display:none;">';
			echo wpautop($response->sections['changelog']);
			echo '</div>';
		}
		//c_ws_plugin__optimizemember_admin_notices::enqueue_admin_notice('bla', ":", true);
		//c_ws_plugin__optimizemember_admin_notices::display_admin_notice('bla', true);
	}

	// Take over the Plugin info screen
	add_filter('plugins_api', 'opm_plugin_screen', 10, 3);

	function opm_plugin_screen($def, $action, $args) {
		$plugin_slug = OPM_PLUGIN_SLUG;
		if ($args->slug != $plugin_slug)
			return false;

		$obj = get_transient('op_opmplugin_update');

		if (false !== $obj) {
			$res = $obj;
		}

		return $res;
	}

	/**
	 * Check SL service for new version
	 * @param array existing WordPress transient array
	 * @return bool|WP_Error
	 */
	function checkOpmUpdate($transient)
	{
		$plugin_version = WS_PLUGIN__OPTIMIZEMEMBER_VERSION;
		$plugin_slug = OPM_PLUGIN_SLUG;
		list ($t1, $t2) = explode('/', $plugin_slug);
		$pluginName = str_replace('.php', '', $t2);


		if (!defined('OP_SN')) {
			return $transient;
		}
		$apiResponse = op_sl_update('opm');

		if (is_wp_error($apiResponse)) {
			return $transient;
		}

		if (version_compare($plugin_version, $apiResponse->new_version, '<')) {
			//prepare object for WordPress
			$obj 					= new stdClass();
			$obj->name				= __('OptimizeMember Plugin', OP_SN);
			$obj->slug 				= $plugin_slug;
			$obj->version 			= $apiResponse->new_version;
			$obj->new_version 		= $apiResponse->new_version;
			$obj->homepage 			= $apiResponse->url;
			$obj->url	 			= $apiResponse->url;
			$obj->download_url 		= $apiResponse->package;
			$obj->package	 		= $apiResponse->package;
			$obj->requires			= '3.5';
			$obj->tested			= '4.7';
			$obj->sections			= array(
					'description' => $apiResponse->section->description,
					'changelog' => $apiResponse->section->changelog,
			);

			$transient->response[$plugin_slug] = $obj;

			// set transient for 2 hours
			set_transient('op_opmplugin_update', $obj, (HOUR_IN_SECONDS * 12));
		}

		return $transient;
	}

	//this is for debug only, DON'T USE IN PRODUCTION
	//set_site_transient('update_plugins', null); //check version in every request, but also check op_theme_update transient. If is set, nothing will happen

	/*
	 * called on admin_init
	*/
	function membership_option_check() {
		$options = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"];
		if($options['membership_options_page'] == '')
			c_ws_plugin__optimizemember_admin_notices::enqueue_admin_notice('<strong>NOTE:</strong> OptimizeMember security restrictions will NOT be enforced until you\'ve configured a Membership Options Page. See: <code>optimizeMember -> General Options -> Membership Options Page</code>.', ":", true);
	}

	add_filter('pre_set_site_transient_update_plugins', 'checkOpmUpdate');
	add_action('admin_init', 'load_opm_plugin_screen');
	add_action('admin_init', 'membership_option_check');
?>