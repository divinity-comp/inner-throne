<?php
/**
* PayPal API Functions *( for site owners )*.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* This WordPress plugin ( optimizeMember Pro ) is comprised of two parts:
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
* 	You should have received a copy of the GNU General Public License,
* 	along with this software. In the main directory, see: /licensing/
* 	If not, see: {@link http://www.gnu.org/licenses/}.
*
* o (2) All other parts of ( optimizeMember Pro ); including, but not limited to:
* 	the CSS code, some JavaScript code, images, and design;
* 	are licensed according to the license purchased.
* 	See: {@link http://www.optimizepress.com/prices/}
*
* Unless you have our prior written consent, you must NOT directly or indirectly license,
* sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Module;
* or make an offer to do any of these things. All of these things are strictly
* prohibited with part (2) of the optimizeMember Pro Module.
*
* Your purchase of optimizeMember Pro includes free lifetime upgrades via optimizeMember.com
* ( i.e. new features, bug fixes, updates, improvements ); along with full access
* to our video tutorial library: {@link http://www.optimizepress.com/videos/}
*
* @package optimizeMember\API_Functions
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**
* Verifies `s2p-v` in a given query string argument; from a custom URL for success.
*
* This can be used to verify the integrity of variables in a success query string.
* Example usage: ``if(optimizemember_pro_paypal_s2p_v_query_ok($_SERVER["QUERY_STRING"])){ }``
*
* @package optimizeMember\API_Functions
* @since 1.5
*
* @param str $url_uri_query A full URL, a partial URI, or just the query string.
* @param bool $ignore_time Optional. Defaults to false. If true, timestamp is ignored.
* @param str|int $exp_secs Optional. Defaults to (int)10. If ``$ignore_time`` is false, optimizeMember will check the signature timestamp.
* 	By default, the signature timestamp cannot be older than 10 seconds, but you can modify this if you prefer.
* @return bool True if the query string is OK/verified, else false.
*/
if (!function_exists ("optimizemember_pro_paypal_s2p_v_query_ok"))
	{
		function optimizemember_pro_paypal_s2p_v_query_ok ($url_uri_query = FALSE, $ignore_time = FALSE, $exp_secs = FALSE)
			{
				$check_time = /* Make this compatible with ``$check_time``. */ ($ignore_time) ? false : true;
				return c_ws_plugin__optimizemember_utils_urls::optimizemember_sig_ok ($url_uri_query, $check_time, $exp_secs, "s2p-v");
			}
	}
?>