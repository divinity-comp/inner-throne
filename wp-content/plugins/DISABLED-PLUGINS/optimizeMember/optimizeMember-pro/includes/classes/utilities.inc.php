<?php
/**
* optimizeMember Pro utilities.
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
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_utilities"))
	{
		/**
		* optimizeMember Pro utilities.
		*
		* @package optimizeMember\Utilities
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_utilities
			{
				/**
				* Converts Currency Codes to Currency Symbols ( deprecated ).
				*
				* @package optimizeMember\Utilities
				* @since 1.5
				*
				* @param str $currency Expects a 3 character Currency Code.
				* @return str A Currency Symbol. Defaults to the `$` sign.
				*
				* @deprecated Starting with v110531, please use:
				* 	``c_ws_plugin__optimizemember_utils_cur::symbol()``
				*/
				public static function currency_symbol ($currency = FALSE)
					{
						return c_ws_plugin__optimizemember_utils_cur::symbol ($currency);
					}
				/**
				* Expands a state/province abbrev into it's full version.
				*
				* This works for the United States and Canada only.
				*
				* @package optimizeMember\Utilities
				* @since 1.5
				*
				* @param str $state A state/province abbreviation.
				* @param str $country A country code. One of `US|CA`.
				* @return str The full state/province name.
				*/
				public static function full_state ($state = FALSE, $country = FALSE)
					{
						static $lines; /* Optimizes this routine for repeated usage. */
						/**/
						if (strlen ($state = strtoupper ($state)) === 2 && preg_match ("/^US|CA$/", ($country = strtoupper ($country))))
							{
								if (!isset ($lines[$country])) /* If $lines are NOT already established. */
									{
										if ($country === "US") /* Handle lookups for the United States. */
											{
												$txt = file_get_contents (dirname (dirname (__FILE__)) . "/usps-states.txt");
											}
										else if ($country === "CA") /* Lookups for Canada. */
											{
												$txt = file_get_contents (dirname (dirname (__FILE__)) . "/ca-provinces.txt");
											}
										/**/
										$lines[$country] = preg_split ("/[\r\n\t]+/", trim (strtoupper ($txt)));
									}
								/**/
								foreach ($lines[$country] as $line) /* Find full version. */
									/**/
									if ($line = trim ($line)) /* Do NOT process empty lines. */
										{
											list ($full, $abbr) = preg_split ("/;/", trim ($line));
											if ($abbr === $state && $full)
												return ucwords ($full);
										}
							}
						/**/
						return $state; /* Full state name. */
					}
			}
	}
?>