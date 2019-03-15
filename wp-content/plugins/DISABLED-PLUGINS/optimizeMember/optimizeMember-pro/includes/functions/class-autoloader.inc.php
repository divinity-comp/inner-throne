<?php
/**
* optimizeMember Pro class autoloader.
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
* @package optimizeMember
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/*
The __autoload function for optimizeMember Pro classes.
*/
if (!function_exists ("ws_plugin__optimizemember_pro_classes"))
	{
		/**
		* optimizeMember Pro class autoloader.
		*
		* The __autoload function for optimizeMember Pro classes.
		* This highly optimizes optimizeMember Pro. Giving it a much smaller footprint.
		* See: {@link http://www.php.net/manual/en/function.spl-autoload-register.php}
		*
		* @package optimizeMember
		* @since 1.5
		*
		* @param str $class The class that needs to be loaded. Passed in by PHP itself.
		* @return null
		*/
		function ws_plugin__optimizemember_pro_classes ($class = FALSE) /* Build dynamic __autoload function. */
			{
				static $c; /* Holds the classes directory location ( location is optimized with a static var ). */
				static $c_class_dirs; /* All possible dir & sub-directory locations ( with a static var ). */
				/**/
				if (strpos ($class, "c_ws_plugin__optimizemember_pro_") === 0) /* Make sure this is an optimizeMember Pro class. */
					{
						$c = (!isset ($c)) ? dirname (dirname (__FILE__)) . "/classes" : $c; /* Configures location of classes. */
						$c_class_dirs = (!isset ($c_class_dirs)) ? array_merge (array ($c), _ws_plugin__optimizemember_pro_classes_scan_dirs_r ($c)) : $c_class_dirs;
						/**/
						$class = str_replace ("_", "-", str_replace ("c_ws_plugin__optimizemember_pro_", "", $class));
						/**/
						foreach ($c_class_dirs as $class_dir) /* Start looking for the class. */
							if ($class_dir === $c || strpos ($class, basename ($class_dir)) === 0)
								if (file_exists ($class_dir . "/" . $class . ".inc.php"))
									{
										include_once $class_dir . "/" . $class . ".inc.php";
										/**/
										break; /* Now stop looking. */
									}
					}
				/**/
				return; /* Return for uniformity. */
			}
		/**
		* Scans recursively for class sub-directories.
		*
		* Used by the optimizeMember Pro autoloader.
		*
		* @package optimizeMember
		* @since 1.5
		*
		* @param str $starting_dir The directory to start scanning from.
		* @return str[] An array of class directories.
		*/
		function _ws_plugin__optimizemember_pro_classes_scan_dirs_r ($starting_dir = FALSE)
			{
				$dirs = array (); /* Initialize dirs array. */
				/**/
				foreach (func_get_args () as $starting_dir)
					if (is_dir ($starting_dir)) /* Does this directory exist? */
						foreach (scandir ($starting_dir) as $dir) /* Scan this directory. */
							if ($dir !== "." && $dir !== ".." && is_dir ($dir = $starting_dir . "/" . $dir))
								$dirs = array_merge ($dirs, array ($dir), _ws_plugin__optimizemember_pro_classes_scan_dirs_r ($dir));
				/**/
				return $dirs; /* Return array of all directories. */
			}
		/**/
		spl_autoload_register("ws_plugin__optimizemember_pro_classes"); /* Register __autoload. */
	}
?>