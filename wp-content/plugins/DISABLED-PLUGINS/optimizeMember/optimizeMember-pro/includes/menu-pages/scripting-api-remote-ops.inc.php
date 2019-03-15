<?php
/**
* Menu page for optimizeMember Pro ( Scripting, Remote Operations ).
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
* @package optimizeMember\Menu_Pages
* @since 110713
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_menu_page_remote_ops_api"))
	{
		/**
		* Menu page for optimizeMember Pro ( Scripting, Remote Operations ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110713
		*/
		class c_ws_plugin__optimizemember_pro_menu_page_remote_ops_api
			{
				public function __construct ()
					{
						if (!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ())
							{
								echo '<div class="ws-menu-page-group" title="Pro API For Remote Operations">' . "\n";
								/**/
								echo '<div class="ws-menu-page-section ws-plugin--optimizemember-api-remote-operations-section">' . "\n";
								echo '<h3>Pro API For Remote Operations ( PHP scripting required )</h3>' . "\n";
								echo '<p>With optimizeMember Pro installed, you have access to the optimizeMember Pro API For Remote Operations. This is made available for developers that wish to create User/Member accounts dynamically through custom scripts of their own. optimizeMember\'s Remote Operations API requires a secret API Key in order to POST authenticated requests to your installation of optimizeMember.</p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<h4>Remote Operations API: <strong>Your secret API Key</strong></h4>'."\n";
								echo '<input type="text" autocomplete="off" value="' . format_to_edit (c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen ()) . '" style="width:99%;" />' . "\n";
								/**/
								echo '<p><em><strong class="ws-menu-page-hilite">Experimental:</strong> The Remote Operations API is currently in an experimental state. The Operations that are currently possible include: <code>create_user</code>, <code>modify_user</code>, <code>delete_user</code>. In a future release of optimizeMember Pro, we will add further documentation and some additional Remote Operations to this API. Thanks for your patience.</em></p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<h4>Remote Operation: <code>create_user</code> (or update existing Users/Members)</h4>'."\n";
								echo '<p>' . c_ws_plugin__optimizemember_utils_strings::highlight_php (str_replace ("www.example.com", $_SERVER["HTTP_HOST"], str_replace ("http://www.example.com/", site_url ("/"), str_replace ("[API Key]", c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen (), file_get_contents (dirname (__FILE__) . "/code-samples/remote-op-create-user.x-php"))))) . '</p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<h4>Remote Operation: <code>modify_user</code> (updates existing Users/Members)</h4>'."\n";
								echo '<p>' . c_ws_plugin__optimizemember_utils_strings::highlight_php (str_replace ("www.example.com", $_SERVER["HTTP_HOST"], str_replace ("http://www.example.com/", site_url ("/"), str_replace ("[API Key]", c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen (), file_get_contents (dirname (__FILE__) . "/code-samples/remote-op-modify-user.x-php"))))) . '</p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<h4>Remote Operation: <code>delete_user</code> (deletes existing Users/Members)</h4>'."\n";
								echo '<p>' . c_ws_plugin__optimizemember_utils_strings::highlight_php (str_replace ("www.example.com", $_SERVER["HTTP_HOST"], str_replace ("http://www.example.com/", site_url ("/"), str_replace ("[API Key]", c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen (), file_get_contents (dirname (__FILE__) . "/code-samples/remote-op-delete-user.x-php"))))) . '</p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<h4>Remote Operation: <code>get_levels</code> (returns membership levels)</h4>'."\n";
								echo '<p>' . c_ws_plugin__optimizemember_utils_strings::highlight_php (str_replace ("www.example.com", $_SERVER["HTTP_HOST"], str_replace ("http://www.example.com/", site_url ("/"), str_replace ("[API Key]", c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen (), file_get_contents (dirname (__FILE__) . "/code-samples/remote-op-get-levels.x-php"))))) . '</p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<h4>Remote Operation: <code>get_packages</code> (returns membership packages)</h4>'."\n";
								echo '<p>' . c_ws_plugin__optimizemember_utils_strings::highlight_php (str_replace ("www.example.com", $_SERVER["HTTP_HOST"], str_replace ("http://www.example.com/", site_url ("/"), str_replace ("[API Key]", c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen (), file_get_contents (dirname (__FILE__) . "/code-samples/remote-op-get-packages.x-php"))))) . '</p>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								//echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.optimizepress.com/codex/" target="_blank" rel="external">optimizeMember Codex</a>.<br />' . "\n";
								//echo '<strong>See Also:</strong> <a href="http://www.optimizepress.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">optimizeMember Codex -> API Constants</a>, and <a href="http://www.optimizepress.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">optimizeMember Codex -> API Functions</a>.</p>' . "\n";
								echo '</div>' . "\n";
								/**/
								echo '</div>' . "\n";
							}
					}
			}
	}
/**/
new c_ws_plugin__optimizemember_pro_menu_page_remote_ops_api ();
?>