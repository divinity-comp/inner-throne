<?php
/**
* Deprecated functions from previous versions of optimizeMember.
*
* See: {@link http://en.wikipedia.org/wiki/Deprecation}
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
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**
* Deprecated in optimizeMember v3.5+.
*
* The s2Clean theme; prior to s2Clean v1.2.5 looked for the existence of this function.
* In fact, all older optimizePress called upon the activate/deactivate functions.
*
* @package optimizeMember
* @since 1.0
*
* @deprecated Starting with optimizeMember v3.5+, please use:
* 	``c_ws_plugin__optimizemember_installation::activate()``
*
* @see optimizeMember\Installation\c_ws_plugin__optimizemember_installation::activate()
*/
function ws_plugin__optimizemember_activate ()
	{
		return c_ws_plugin__optimizemember_installation::activate ();
	}
/**
* Deprecated in optimizeMember v3.5+.
*
* The s2Clean theme; prior to s2Clean v1.2.5 looked for the existence of this function.
* In fact, all older optimizePress called upon the activate/deactivate functions.
*
* @package optimizeMember
* @since 1.0
*
* @deprecated Starting with optimizeMember v3.5+, please use:
* 	``c_ws_plugin__optimizemember_installation::deactivate()``
*
* @see optimizeMember\Installation\c_ws_plugin__optimizemember_installation::deactivate()
*/
function ws_plugin__optimizemember_deactivate ()
	{
		return c_ws_plugin__optimizemember_installation::deactivate ();
	}
/**
* Deprecated in optimizeMember v3.5+.
*
* Needed by the optimizeMember Pro upgrader prior to optimizeMember Pro v1.5+.
*
* @package optimizeMember
* @since 3.0
*
* @deprecated Starting with optimizeMember v3.5+, please use:
* 	``c_ws_plugin__optimizemember_utils_strings::trim_deep()``
*
* @see optimizeMember\Utilities\c_ws_plugin__optimizemember_utils_strings::trim_deep()
*/
function ws_plugin__optimizemember_trim_deep ($data = FALSE)
	{
		return c_ws_plugin__optimizemember_utils_strings::trim_deep ($data);
	}
/**
* Deprecated in optimizeMember v3.5+.
*
* Needed by the optimizeMember Pro upgrader prior to optimizeMember Pro v1.5+.
*
* @package optimizeMember
* @since 3.0
*
* @deprecated Starting with optimizeMember v3.5+, please use:
* 	``c_ws_plugin__optimizemember_utils_urls::remote()``
*
* @see optimizeMember\Utilities\c_ws_plugin__optimizemember_utils_urls::remote()
*/
function ws_plugin__optimizemember_remote ($url = FALSE, $post_vars = FALSE, $args = array ())
	{
		return c_ws_plugin__optimizemember_utils_urls::remote ($url, $post_vars, $args);
	}
/**
* Deprecated in optimizeMember v3.5+.
*
* Needed by the optimizeMember Pro upgrader prior to optimizeMember Pro v1.5+.
*
* @package optimizeMember
* @since 3.0
*
* @deprecated Starting with optimizeMember v3.5+, please use:
* 	``c_ws_plugin__optimizemember_admin_notices::enqueue_admin_notice()``
*
* @see optimizeMember\Admin_Notices\c_ws_plugin__optimizemember_admin_notices::enqueue_admin_notice()
*/
function ws_plugin__optimizemember_enqueue_admin_notice ($notice = FALSE, $on_pages = FALSE, $error = FALSE, $time = FALSE, $dismiss = FALSE)
	{
		return c_ws_plugin__optimizemember_admin_notices::enqueue_admin_notice ($notice, $on_pages, $error, $time, $dismiss);
	}
?>