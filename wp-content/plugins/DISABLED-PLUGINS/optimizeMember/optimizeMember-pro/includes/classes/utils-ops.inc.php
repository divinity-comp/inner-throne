<?php
/**
* optimizeMember Pro option utilities.
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
* @package optimizeMember\Utilities
* @since 110815
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_utils_ops"))
	{
		/**
		* optimizeMember Pro option utilities.
		*
		* @package optimizeMember\Utilities
		* @since 110815
		*/
		class c_ws_plugin__optimizemember_pro_utils_ops
			{
				/**
				* Handles optimizeMember option Replacement Codes.
				*
				* @package optimizeMember\Utilities
				* @since 110815
				*
				* @param array $ops An array of optimizeMember options, or an option value array.
				* @param bool $fill Optional. If true, Replacement Codes are filled, else false.
				* @return mixed The end result, after handling Replacement Codes.
				*/
				public static function op_replace ($ops = FALSE, $fill = FALSE)
					{
						global $current_site, $current_blog; /* Multisite. */
						/**/
						if (is_array ($ops) && !empty ($ops)) /* Only if array. */
							{
								foreach ($ops as &$op) /* Begin looping sequence. */
									{
										if (is_array ($op) && !empty ($op)) /* Array? */
											$op = c_ws_plugin__optimizemember_pro_utils_ops::op_replace ($op, $fill);
										/**/
										else if (is_string ($op) && !$fill) /* Handle Replacement Codes. */
											{
												$op = (is_multisite ()) ? preg_replace ("/" . preg_quote (rtrim ($current_site->domain . $current_site->path, "/") . "/", "/") . "/", "%%_op__current_site_domain_path/%%", $op) : $op;
												$op = (is_multisite ()) ? preg_replace ("/" . preg_quote (rtrim ($current_site->domain . $current_site->path, "/"), "/") . "/", "%%_op__current_site_domain_path%%", $op) : $op;
												/**/
												$op = (is_multisite ()) ? preg_replace ("/" . preg_quote (rtrim ($current_blog->domain . $current_blog->path, "/") . "/", "/") . "/", "%%_op__current_blog_domain_path/%%", $op) : $op;
												$op = (is_multisite ()) ? preg_replace ("/" . preg_quote (rtrim ($current_blog->domain . $current_blog->path, "/"), "/") . "/", "%%_op__current_blog_domain_path%%", $op) : $op;
												/**/
												$op = preg_replace ("/" . preg_quote (rtrim (site_url (), "/"), "/") . "/", "%%_op__site_url%%", preg_replace ("/" . preg_quote (rtrim (site_url (), "/") . "/", "/") . "/", "%%_op__site_url/%%", $op));
												$op = preg_replace ("/" . preg_quote (rtrim (home_url (), "/"), "/") . "/", "%%_op__home_url%%", preg_replace ("/" . preg_quote (rtrim (home_url (), "/") . "/", "/") . "/", "%%_op__home_url/%%", $op));
												/**/
												$op = preg_replace ("/" . preg_quote ($_SERVER["HTTP_HOST"], "/") . "/i", "%%_op__domain%%", preg_replace ("/" . preg_quote (get_bloginfo ("name"), "/") . "/", "%%_op__blog_name%%", $op));
											}
										else if (is_string ($op) && $fill) /* Handle Replacement Codes. */
											{
												$op = (is_multisite ()) ? preg_replace ("/%%_op__current_site_domain_path\/%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim ($current_site->domain . $current_site->path, "/") . "/"), $op) : preg_replace ("/%%_op__current_site_domain_path\/%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (site_url (), "/") . "/"), $op);
												$op = (is_multisite ()) ? preg_replace ("/%%_op__current_site_domain_path%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim ($current_site->domain . $current_site->path, "/")), $op) : preg_replace ("/%%_op__current_site_domain_path%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (site_url (), "/")), $op);
												/**/
												$op = (is_multisite ()) ? preg_replace ("/%%_op__current_blog_domain_path\/%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim ($current_blog->domain . $current_blog->path, "/") . "/"), $op) : preg_replace ("/%%_op__current_blog_domain_path\/%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (site_url (), "/") . "/"), $op);
												$op = (is_multisite ()) ? preg_replace ("/%%_op__current_blog_domain_path%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim ($current_blog->domain . $current_blog->path, "/")), $op) : preg_replace ("/%%_op__current_blog_domain_path%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (site_url (), "/")), $op);
												/**/
												$op = preg_replace ("/%%_op__site_url%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (site_url (), "/")), preg_replace ("/%%_op__site_url\/%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (site_url (), "/") . "/"), $op));
												$op = preg_replace ("/%%_op__home_url%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (home_url (), "/")), preg_replace ("/%%_op__home_url\/%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (rtrim (home_url (), "/") . "/"), $op));
												/**/
												$op = preg_replace ("/%%_op__domain%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs ($_SERVER["HTTP_HOST"]), preg_replace ("/%%_op__blog_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs (get_bloginfo ("name")), $op));
											}
									}
							}
						/**/
						return $ops; /* Now return the $ops. */
					}
			}
	}
?>