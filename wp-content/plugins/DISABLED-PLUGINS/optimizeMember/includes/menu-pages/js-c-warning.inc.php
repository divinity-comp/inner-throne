<?php
/**
* JS conflict warning for Menu Pages.
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
* @package optimizeMember\Menu_Pages
* @since 110912
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_menu_pages_js_c_warning"))
	{
		/**
		* Newsletter/Feed Boxes for Menu Pages.
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_menu_pages_js_c_warning
			{
				public function __construct ()
					{
						echo '<p style="margin-top:0;"><strong>Dashboard JavaScript Conflict</strong></p>' . "\n";
						echo '<p>Something is conflicting with the jQuery JavaScript framework, which optimizeMember uses; causing this warning to be visible to you.</p>' . "\n";
						echo '<p style="margin-bottom:0;">Please deactivate one other plugin at a time until you find the culprit. Or, in Firefox, see <code>Tools -> Error Console</code>.</p>';
					}
			}
	}
/**/
new c_ws_plugin__optimizemember_menu_pages_js_c_warning ();
?>