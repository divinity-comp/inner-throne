<?php
/**
* optimizeMember's Security Gate.
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
* @package optimizeMember\Security
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_security"))
	{
		/**
		* optimizeMember's Security Gate.
		*
		* @package optimizeMember\Security
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_security
			{
				/**
				* optimizeMember's Security Gate ( protects WordPress content ).
				*
				* @package optimizeMember\Security
				* @since 3.5
				*
				* @attaches-to ``add_action("wp");``
				*
				* @return null May redirect a browser *(exiting script execution)*, when/if content is NOT available to the current User/Member.
				*/
				public static function security_gate () /* optimizeMember's Security Gate. */
					{
						do_action ("ws_plugin__optimizemember_before_security_gate", get_defined_vars ());
						/**/
						if (is_category ()) /* Categories & other inclusives. */
							c_ws_plugin__optimizemember_catgs::check_catg_level_access ();
						/**/
						else if (is_tag ()) /* Post/Page Tags & other inclusives. */
							c_ws_plugin__optimizemember_ptags::check_ptag_level_access ();
						/**/
						else if (is_single ()) /* All Posts & other inclusives. */
							c_ws_plugin__optimizemember_posts::check_post_level_access ();
						/**/
						else if (is_page ()) /* All Pages & other inclusives. */
							c_ws_plugin__optimizemember_pages::check_page_level_access ();
						/**/
						else /* Else, we simply look at URIs & other inclusives. */
							c_ws_plugin__optimizemember_ruris::check_ruri_level_access ();
						/**/
						do_action ("ws_plugin__optimizemember_after_security_gate", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
				/**
				* optimizeMember's Security Gate ( protects WordPress queries ).
				*
				* @package optimizeMember\Security
				* @since 3.5
				*
				* @attaches-to ``add_action("pre_get_posts");``
				*
				* @param obj $wp_query Global ``$wp_query``, by reference.
				* @return null May filter WordPress queries, by hiding protected content which is NOT available to the current User/Member.
				*/
				public static function security_gate_query (&$wp_query = FALSE) /* optimizeMember's Security Gate. */
					{
						do_action ("ws_plugin__optimizemember_before_security_gate_query", get_defined_vars ());
						/**/
						c_ws_plugin__optimizemember_querys::query_level_access ($wp_query); /* By reference. */
						/**/
						do_action ("ws_plugin__optimizemember_after_security_gate_query", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}

				/**
				 * wp_list_pages filter for excluding protected pages
				 * @param array $exclude
				 * @return array
				 */
				public static function excludePages($exclude)
				{
					if(is_array($_drips = c_ws_plugin__optimizemember_utils_gets::get_unavailable_singular_ids_with_dripped_content(wp_get_current_user())) && !empty($_drips)) {
						$exclude = array_merge($exclude, $_drips);
					}
					return $exclude;
				}
			}
	}