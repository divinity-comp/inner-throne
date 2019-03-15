<?php
/**
 * Pro login redirections.
 *
 * Copyright: © 2009-2011
 * {@link http://websharks-inc.com/ WebSharks, Inc.}
 * (coded in the USA)
 *
 * This WordPress plugin (optimizeMember Pro) is comprised of two parts:
 *
 * o (1) Its PHP code is licensed under the GPL license, as is WordPress.
 *   You should have received a copy of the GNU General Public License,
 *   along with this software. In the main directory, see: /licensing/

 *
 * o (2) All other parts of (optimizeMember Pro); including, but not limited to:
 *   the CSS code, some JavaScript code, images, and design;
 *   are licensed according to the license purchased.

 *
 * Unless you have our prior written consent, you must NOT directly or indirectly license,
 * sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Add-on;
 * or make an offer to do any of these things. All of these things are strictly
 * prohibited with part (2) of the optimizeMember Pro Add-on.
 *
 * Your purchase of optimizeMember Pro includes free lifetime upgrades via optimizeMember.com
 * (i.e., new features, bug fixes, updates, improvements);
 *
 * @package optimizeMember\Login_Redirects
 * @since 110720
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_login_redirects'))
{
	/**
	 * Pro login redirections.
	 *
	 * @package optimizeMember\Login_Redirects
	 * @since 110720
	 */
	class c_ws_plugin__optimizemember_pro_login_redirects
	{
		/**
		 * Handles Pro login redirections.
		 *
		 * @package optimizeMember\Login_Redirects
		 * @since 110720
		 *
		 * @attaches-to ``add_filter('ws_plugin__optimizemember_login_redirect');``
		 *
		 * @param bool|string $redirect Expects a boolean value of true|false, or a non-empty string passed through by the filter.
		 * @param array       $vars Expects an array of defined variables, passed in by the filter.
		 *
		 * @return bool|string A One-Time-Offer redirection URL, else the original value.
		 */
		public static function login_redirect($redirect = FALSE, $vars = array())
		{
			if($redirect && !empty($vars['user']) && !empty($vars['logins']))
				foreach(preg_split('/['."\r\n".']+/', $GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_login_welcome_page_otos'], -1, PREG_SPLIT_NO_EMPTY) as $_oto)
					if(($_oto = trim($_oto)) && preg_match('/^(?:(?P<number_of_logins>[0-9]+)\:)(?:(?P<level>[0-9]+)\:)?(?P<url>.+)$/', $_oto, $_m))
					{
						$number_of_logins = isset($_m['number_of_logins'][0]) ? (int)$_m['number_of_logins'] : 0;
						$level            = isset($_m['level'][0]) ? (int)$_m['level'] : '';
						$url              = isset($_m['url'][0]) ? $_m['url'] : '';

						if((int)$number_of_logins === (int)$vars['logins'])
							if(!is_numeric($level) || (int)c_ws_plugin__optimizemember_user_access::user_access_level($vars['user']) === (int)$level)
								if($url && ($url = c_ws_plugin__optimizemember_login_redirects::fill_login_redirect_rc_vars($url, $vars['user'])))
									return ($redirect = $url);
					}
			return $redirect; // Filter through as-is.
		}
	}
}