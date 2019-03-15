<?php
/**
* User utilities.
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
* @package optimizeMember\Utilities
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_utils_users"))
	{
		/**
		* User utilities.
		*
		* @package optimizeMember\Utilities
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_utils_users
			{
				/**
				* Determines the total Users/Members in the database.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @return int Number of Users in the database, total.
				*/
				public static function users_in_database ()
					{
						global $wpdb; /* Global database object reference. */

                        $wpdb->query("SELECT SQL_CALC_FOUND_ROWS `".$wpdb->users."`.`ID` FROM `".$wpdb->users."`, `".$wpdb->usermeta."` WHERE `".$wpdb->users."`.`ID` = `".$wpdb->usermeta."`.`user_id` AND `".$wpdb->usermeta."`.`meta_key` = '".esc_sql($wpdb->prefix."capabilities")."' LIMIT 1");
                        $users = (int)$wpdb->get_var("SELECT FOUND_ROWS()");
						/**/
						return $users;
					}
				/**
				* Obtains Custom String for an existing Member, referenced by a Subscr. or Transaction ID.
				*
				* A second lookup parameter can be provided as well *( optional )*.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $subscr_or_txn_id Either a Paid Subscr. ID, or a Paid Transaction ID.
				* @param str $os0 Optional. A second lookup parameter, usually the `os0` value for PayPal integrations.
				* @return str|bool The Custom String value on success, else false on failure.
				*/
				public static function get_user_custom_with ($subscr_or_txn_id = FALSE, $os0 = FALSE)
					{
						global $wpdb; /* Need global DB obj. */
						/**/
						if ($subscr_or_txn_id && $os0) /* This case includes some additional routines that can use the ``$os0`` value. */
							{
								if (($q = $wpdb->get_row ("SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE (`meta_key` = '" . $wpdb->prefix . "optimizemember_subscr_id' OR `meta_key` = '" . $wpdb->prefix . "optimizemember_first_payment_txn_id') AND (`meta_value` = '" . esc_sql ($subscr_or_txn_id) . "' OR `meta_value` = '" . esc_sql ($os0) . "') LIMIT 1"))/**/
								|| ($q = $wpdb->get_row ("SELECT `ID` AS `user_id` FROM `" . $wpdb->users . "` WHERE `ID` = '" . esc_sql ($os0) . "' LIMIT 1")))
									if (($custom = get_user_option ("optimizemember_custom", $q->user_id)))
										return $custom;
							}
						else if ($subscr_or_txn_id) /* Otherwise, if all we have is a Subscr./Txn. ID value. */
							{
								if (($q = $wpdb->get_row ("SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE (`meta_key` = '" . $wpdb->prefix . "optimizemember_subscr_id' OR `meta_key` = '" . $wpdb->prefix . "optimizemember_first_payment_txn_id') AND `meta_value` = '" . esc_sql ($subscr_or_txn_id) . "' LIMIT 1")))
									if (($custom = get_user_option ("optimizemember_custom", $q->user_id)))
										return $custom;
							}
						/**/
						return false; /* Otherwise, return false. */
					}
				/**
				* Obtains the User ID for an existing Member, referenced by a Subscr. or Transaction ID.
				*
				* A second lookup parameter can be provided as well *( optional )*.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $subscr_or_txn_id Either a Paid Subscr. ID, or a Paid Transaction ID.
				* @param str $os0 Optional. A second lookup parameter, usually the `os0` value for PayPal integrations.
				* @return int|bool A WordPress User ID on success, else false on failure.
				*/
				public static function get_user_id_with ($subscr_or_txn_id = FALSE, $os0 = FALSE)
					{
						global $wpdb; /* Need global DB obj. */
						/**/
						if ($subscr_or_txn_id && $os0) /* This case includes some additional routines that can use the ``$os0`` value. */
							{
								if (($q = $wpdb->get_row ("SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE (`meta_key` = '" . $wpdb->prefix . "optimizemember_subscr_id' OR `meta_key` = '" . $wpdb->prefix . "optimizemember_first_payment_txn_id') AND (`meta_value` = '" . esc_sql ($subscr_or_txn_id) . "' OR `meta_value` = '" . esc_sql ($os0) . "') LIMIT 1"))/**/
								|| ($q = $wpdb->get_row ("SELECT `ID` AS `user_id` FROM `" . $wpdb->users . "` WHERE `ID` = '" . esc_sql ($os0) . "' LIMIT 1")))
									return $q->user_id;
							}
						else if ($subscr_or_txn_id) /* Otherwise, if all we have is a Subscr./Txn. ID value. */
							{
								if (($q = $wpdb->get_row ("SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE (`meta_key` = '" . $wpdb->prefix . "optimizemember_subscr_id' OR `meta_key` = '" . $wpdb->prefix . "optimizemember_first_payment_txn_id') AND `meta_value` = '" . esc_sql ($subscr_or_txn_id) . "' LIMIT 1")))
									return $q->user_id;
							}
						/**/
						return false; /* Otherwise, return false. */
					}
				/**
				* Obtains the Email Address for an existing Member, referenced by a Subscr. or Transaction ID.
				*
				* A second lookup parameter can be provided as well *( optional )*.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $subscr_or_txn_id Either a Paid Subscr. ID, or a Paid Transaction ID.
				* @param str $os0 Optional. A second lookup parameter, usually the `os0` value for PayPal integrations.
				* @return int|bool A User's Email Address on success, else false on failure.
				*/
				public static function get_user_email_with ($subscr_or_txn_id = FALSE, $os0 = FALSE)
					{
						global $wpdb; /* Need global DB obj. */
						/**/
						if ($subscr_or_txn_id && $os0) /* This case includes some additional routines that can use the ``$os0`` value. */
							{
								if (($q = $wpdb->get_row ("SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE (`meta_key` = '" . $wpdb->prefix . "optimizemember_subscr_id' OR `meta_key` = '" . $wpdb->prefix . "optimizemember_first_payment_txn_id') AND (`meta_value` = '" . esc_sql ($subscr_or_txn_id) . "' OR `meta_value` = '" . esc_sql ($os0) . "') LIMIT 1"))/**/
								|| ($q = $wpdb->get_row ("SELECT `ID` AS `user_id` FROM `" . $wpdb->users . "` WHERE `ID` = '" . esc_sql ($os0) . "' LIMIT 1")))
									if (is_object ($user = new WP_User ($q->user_id)) && !empty ($user->ID) && ($email = $user->user_email))
										return $email;
							}
						else if ($subscr_or_txn_id) /* Otherwise, if all we have is a Subscr./Txn. ID value. */
							{
								if (($q = $wpdb->get_row ("SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE (`meta_key` = '" . $wpdb->prefix . "optimizemember_subscr_id' OR `meta_key` = '" . $wpdb->prefix . "optimizemember_first_payment_txn_id') AND `meta_value` = '" . esc_sql ($subscr_or_txn_id) . "' LIMIT 1")))
									if (is_object ($user = new WP_User ($q->user_id)) && !empty ($user->ID) && ($email = $user->user_email))
										return $email;
							}
						/**/
						return false; /* Otherwise, return false. */
					}
				/**
				* Retrieves IPN Signup Vars & validates their Subscription ID.
				*
				* The ``$user_id`` can be passed in directly; or a lookup can be performed with ``$subscr_id``.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param int|str $user_id Optional. A numeric WordPress User ID.
				* @param str $subscr_id Optional. Can be used instead of passing in a ``$user_id``.
				* 	If ``$subscr_id`` is passed in, it has to match the one found inside the resulting IPN Signup Vars collected by this routine.
				* 	If neither of these parameters are passed in, the current User is assumed instead, obtained through ``wp_get_current_user()``.
				* @return array|bool A User's IPN Signup Vars on success, else false on failure.
				*/
				public static function get_user_ipn_signup_vars ($user_id = FALSE, $subscr_id = FALSE)
					{
						if ($user_id || ($subscr_id && ($user_id = c_ws_plugin__optimizemember_utils_users::get_user_id_with ($subscr_id))) || (!$user_id && !$subscr_id && is_object ($user = wp_get_current_user ()) && !empty ($user->ID) && ($user_id = $user->ID)))
							{
								if (($_subscr_id = get_user_option ("optimizemember_subscr_id", $user_id)) && (!$subscr_id || $subscr_id === $_subscr_id) && ($subscr_id = $_subscr_id))
									if (is_array ($ipn_signup_vars = get_user_option ("optimizemember_ipn_signup_vars", $user_id)))
										if ($ipn_signup_vars["subscr_id"] === $subscr_id)
											return $ipn_signup_vars;
							}
						/**/
						return false; /* Otherwise, return false. */
					}
				/**
				* Retrieves IPN Signup Var & validates their Subscription ID.
				*
				* The ``$user_id`` can be passed in directly; or a lookup can be performed with ``$subscr_id``.
				*
				* @package optimizeMember\Utilities
				* @since 110912
				*
				* @param str $var Required. The requested Signup Var.
				* @param int|str $user_id Optional. A numeric WordPress User ID.
				* @param str $subscr_id Optional. Can be used instead of passing in a ``$user_id``.
				* 	If ``$subscr_id`` is passed in, it has to match the one found inside the resulting IPN Signup Vars collected by this routine.
				* 	If neither of these parameters are passed in, the current User is assumed instead, obtained through ``wp_get_current_user()``.
				* @return mixed|bool A User's IPN Signup Var on success, else false on failure.
				*/
				public static function get_user_ipn_signup_var ($var = FALSE, $user_id = FALSE, $subscr_id = FALSE)
					{
						if (!empty ($var) && is_array ($user_ipn_signup_vars = c_ws_plugin__optimizemember_utils_users::get_user_ipn_signup_vars ($user_id, $subscr_id)))
							{
								if (isset ($user_ipn_signup_vars[$var])) /* Available? */
									return $user_ipn_signup_vars[$var];
							}
						/**/
						return false; /* Otherwise, return false. */
					}
				/**
				* Obtains a User's Paid Subscr. ID *( if available )*; otherwise their WP User ID.
				*
				* If ``$user`` IS passed in, this function will return data from a specific ``$user``, or fail if not possible.
				* If ``$user`` is NOT passed in, check the current User/Member.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param obj $user Optional. A `WP_User` object.
				* 	In order to check the current User, you must call this function with no arguments/parameters.
				* @return int|str|bool If possible, the User's Paid Subscr. ID, else their WordPress User ID, else false.
				*/
				public static function get_user_subscr_or_wp_id ($user = FALSE)
					{
						if ((func_num_args () && (!is_object ($user) || empty ($user->ID))) || (!func_num_args () && (!is_object ($user = (is_user_logged_in ()) ? wp_get_current_user () : false) || empty ($user->ID))))
							{
								return false; /* The ``$user`` was passed in but is NOT an object; or nobody is logged in. */
							}
						else /* Else return Paid Subscr. ID ( if available ), otherwise return their WP database User ID. */
							return ($subscr_id = get_user_option ("optimizemember_subscr_id", $user->ID)) ? $subscr_id : $user->ID;
					}
				/**
				* Determines whether or not a Username/Email is already in the database.
				*
				* Returns the WordPress User ID if they exist.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $user_login A User's Username.
				* @param str $user_email A User's Email Address.
				* @return int|bool If exists, a WordPress User ID, else false.
				*/
				public static function user_login_email_exists ($user_login = FALSE, $user_email = FALSE)
					{
						global $wpdb; /* Global database object reference. */
						/**/
						if ($user_login && $user_email) /* Only if we have both of these. */
							if (($user_id = $wpdb->get_var ("SELECT `ID` FROM `" . $wpdb->users . "` WHERE `user_login` LIKE '" . esc_sql (c_ws_plugin__optimizemember_utils_strings::like_escape ($user_login)) . "' AND `user_email` LIKE '" . esc_sql (c_ws_plugin__optimizemember_utils_strings::like_escape ($user_email)) . "' LIMIT 1")))
								return $user_id; /* Return the associated WordPress ID. */
						/**/
						return false; /* Else return false. */
					}
				/**
				* Determines whether or not a Username/Email is already in the database for this Blog.
				*
				* Returns the WordPress User ID if they exist.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $user_login A User's Username.
				* @param str $user_email A User's Email Address.
				* @param int|str $blog_id A numeric WordPress Blog ID.
				* @return int|bool If exists *( but not on Blog )*, a WordPress User ID, else false.
				*/
				public static function ms_user_login_email_exists_but_not_on_blog ($user_login = FALSE, $user_email = FALSE, $blog_id = FALSE)
					{
						if ($user_login && $user_email) /* Only if we have both of these. */
							if (is_multisite () && ($user_id = c_ws_plugin__optimizemember_utils_users::user_login_email_exists ($user_login, $user_email)) && !is_user_member_of_blog ($user_id, $blog_id))
								return $user_id;
						/**/
						return false; /* Else return false. */
					}
				/**
				* Determines whether or not a Username/Email is already in the database for this Blog.
				*
				* This is an alias for: `c_ws_plugin__optimizemember_utils_users::ms_user_login_email_exists_but_not_on_blog()`.
				*
				* Returns the WordPress User ID if they exist.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $user_login A User's Username.
				* @param str $user_email A User's Email Address.
				* @param int|str $blog_id A numeric WordPress Blog ID.
				* @return int|bool If exists *( but not on Blog )*, a WordPress User ID, else false.
				*/
				public static function ms_user_login_email_can_join_blog ($user_login = FALSE, $user_email = FALSE, $blog_id = FALSE)
					{
						return c_ws_plugin__optimizemember_utils_users::ms_user_login_email_exists_but_not_on_blog ($user_login, $user_email, $blog_id);
					}
				/**
				* Retrieves a field value. Also supports Custom Fields.
				*
				* @package optimizeMember\Utilities
				* @since 3.5
				*
				* @param str $field_id Required. A unique Custom Registration/Profile Field ID, that you configured with optimizeMember.
				* 	Or, this could be set to any property that exists on the WP_User object for a particular User;
				* 	( i.e. `id`, `ID`, `user_login`, `user_email`, `first_name`, `last_name`, `display_name`, `ip`, `IP`,
				* 	`optimizemember_registration_ip`, `optimizemember_custom`, `optimizemember_subscr_id`, `optimizemember_subscr_or_wp_id`,
				* 	`optimizemember_subscr_gateway`, `optimizemember_custom_fields`, `optimizemember_file_download_access_[log|arc]`,
				* 	`optimizemember_auto_eot_time`, `optimizemember_last_payment_time`, `optimizemember_paid_registration_times`,
				* 	`optimizemember_access_role`, `optimizemember_access_level`, `optimizemember_access_label`,
				* 	`optimizemember_access_ccaps`, etc, etc. ).
				* @param int|str $user_id Optional. Defaults to the current User's ID.
				* @return mixed The value of the requested field, or false if the field does not exist.
				*/
				public static function get_user_field ($field_id = FALSE, $user_id = FALSE) /* Very powerful function here. */
					{
						global $wpdb; /* Global database object reference. We'll need this to obtain the right database prefix. */
						/**/
						$current_user = wp_get_current_user (); /* Current User's object ( used when/if `$user_id` is empty ). */
						/**/
						if (is_object ($user = ($user_id) ? new WP_User ($user_id) : $current_user) && !empty ($user->ID) && ($user_id = $user->ID))
							{
								if (isset ($user->$field_id)) /* Immediate User object property? ( most likely ) */
									return $user->$field_id;
								/**/
								else if (isset ($user->data->$field_id)) /* Also try the data object property. */
									return $user->data->$field_id;
								/**/
								else if (isset ($user->{$wpdb->prefix . $field_id})) /* Immediate prefixed? */
									return $user->{$wpdb->prefix . $field_id};
								/**/
								else if (isset ($user->data->{$wpdb->prefix . $field_id})) /* Data prefixed? */
									return $user->data->{$wpdb->prefix . $field_id};
								/**/
								else if (strcasecmp ($field_id, "full_name") === 0) /* First/last full name? */
									return trim ($user->first_name . " " . $user->last_name);
								/**/
								else if (preg_match ("/^(email|user_email)$/i", $field_id)) /* Email address? */
									return $user->user_email;
								/**/
								else if (preg_match ("/^(login|user_login)$/i", $field_id)) /* Username / login? */
									return $user->user_login;
								/**/
								else if (strcasecmp ($field_id, "optimizemember_access_role") === 0) /* Role name/ID? */
									return c_ws_plugin__optimizemember_user_access::user_access_role ($user);
								/**/
								else if (strcasecmp ($field_id, "optimizemember_access_level") === 0) /* Access Level? */
									return c_ws_plugin__optimizemember_user_access::user_access_level ($user);
								/**/
								else if (strcasecmp ($field_id, "optimizemember_access_label") === 0) /* Access Label? */
									return c_ws_plugin__optimizemember_user_access::user_access_label ($user);
								/**/
								else if (strcasecmp ($field_id, "optimizemember_access_ccaps") === 0) /* Custom Caps? */
									return c_ws_plugin__optimizemember_user_access::user_access_ccaps ($user);
								/**/
								else if (strcasecmp ($field_id, "ip") === 0 && is_object ($current_user) && !empty ($current_user->ID) && $current_user->ID === ($user_id = $user->ID))
									return $_SERVER["REMOTE_ADDR"]; /* The current User's IP address, right now. */
								/**/
								else if (strcasecmp ($field_id, "optimizemember_registration_ip") === 0 || strcasecmp ($field_id, "reg_ip") === 0 || strcasecmp ($field_id, "ip") === 0)
									return get_user_option ("optimizemember_registration_ip", $user_id);
								/**/
								else if (strcasecmp ($field_id, "optimizemember_subscr_or_wp_id") === 0)
									return ($subscr_id = get_user_option ("optimizemember_subscr_id", $user_id)) ? $subscr_id : $user_id;
								/**/
								else if (is_array ($fields = get_user_option ("optimizemember_custom_fields", $user_id)))
									if (isset ($fields[preg_replace ("/[^a-z0-9]/i", "_", strtolower ($field_id))]))
										return $fields[preg_replace ("/[^a-z0-9]/i", "_", strtolower ($field_id))];
							}
						/**/
						return false; /* Default, return false. */
					}
			/**
			 * Auto EOT time, else NPR (next payment time).
			 *
			 * @param int|string $user_id Optional. Defaults to the current User's ID.
			 * @param bool $check_gateway Defaults to a true value. If this is false, it is only possible to return a fixed EOT time.
			 * 	In other words, if this is false and there is no EOT time, empty values will be returned. Be careful with this, because not checking
			 * 	the payment gateway can result in an inaccurate return value. Only set to false if you want to limit the check to a fixed hard-coded EOT time.
			 * @param string $favor Defaults to a value of `fixed`; i.e., if a fixed EOT time is available, that is returned in favor of a next payment time.
			 * 	You can set this to `next` if you'd like to favor a next payment time (when applicable) instead of returning a fixed EOT time.
			 *
			 * @return array An associative array of EOT details; with the following elements.
			 *
			 * - `type` One of `fixed` (a fixed EOT time), `next` (next payment time; i.e., an ongoing recurring subscription); or an empty string if there is no EOT for the user.
			 * - `time` The timestamp (UTC time) that represents the EOT (End Of Term); else `0` if there is no EOT time.
			 * - `tense` If time is now (or earlier) this will be `past`. If time is in the future, this will be `future`. If there is no time, this is an empty string.
			 * - `debug` A string of details that explain to a developer what was returned. For debugging only.
			 */
			public static function get_user_eot($user_id = 0, $check_gateway = TRUE, $favor = 'fixed')
			{
				if(!($user_id = (integer)$user_id)) // Empty user ID in this call?
					$user_id = get_current_user_id(); // Assume current user.

				if(!$favor || !in_array($favor, array('fixed', 'next'), TRUE))
					$favor = 'fixed'; // Default behavior.

				$now            = time(); // Current timestamp.
				$grace_time     = (isset($GLOBALS['WS_PLUGIN__']['optimizemember']['o']['eot_grace_time'])) ? (integer)$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['eot_grace_time'] : 0;
				$grace_time     = (integer)apply_filters('ws_plugin__optimizemember_eot_grace_time', $grace_time);
				$demotion_role  = c_ws_plugin__optimizemember_option_forces::force_demotion_role('subscriber');
				$empty_response = array('type' => '', 'time' => 0, 'tense' => '', 'debug' => '');

				if(!$user_id || !($user = new WP_User($user_id)) || !$user->ID)
					return array_merge($empty_response, array('debug' => 'Invalid user ID.'));

				$ipn_signup_vars     = self::get_user_ipn_signup_vars($user->ID);
				$subscr_gateway      = (string)get_user_option('optimizemember_subscr_gateway', $user->ID);
				$subscr_id           = (string)get_user_option('optimizemember_subscr_id', $user->ID);
				$subscr_cid          = (string)get_user_option('optimizemember_subscr_cid', $user->ID);
				$last_auto_eot_time  = (integer)get_user_option('optimizemember_last_auto_eot_time', $user->ID);
				$auto_eot_time       = (integer)get_user_option('optimizemember_auto_eot_time', $user->ID);

				if($auto_eot_time) // They have a hard-coded EOT time at present?
					return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
								 'debug' => 'This is a fixed EOT time recorded by OptimizeMember. It can be altered in the WordPress Dashboard for this user.');

				if(!$subscr_gateway && !$subscr_id && !$subscr_cid && $last_auto_eot_time // EOTd?
					&& (!user_can($user->ID, 'access_optimizemember_level1') || c_ws_plugin__optimizemember_user_access::user_access_role($user) === $demotion_role)
					&& !c_ws_plugin__optimizemember_user_access::user_access_ccaps($user) // And no CCAPs either?
				) return array('type' => 'fixed', 'time' => $last_auto_eot_time, 'tense' => $last_auto_eot_time <= $now ? 'past' : 'future',
							   'debug' => 'This is an archived/fixed EOT time recorded by s2Member; i.e., the date this customer\'s access expired.');

				if(!$subscr_gateway || !$subscr_id || !is_array($ipn_signup_vars) || !$ipn_signup_vars)
					return array_merge($empty_response, array('debug' => 'This user has no subscription; i.e., missing `subscr_id`, `subscr_gateway` or `ipn_signup_vars`.'));

				if(empty($ipn_signup_vars['txn_type']) || $ipn_signup_vars['txn_type'] !== 'subscr_signup')
					return array_merge($empty_response, array('debug' => 'This user has no subscription; i.e., `txn_type` != `subscr_signup`.'));

				$auto_eot_time // Update this now; i.e., build a new EOT time based on IPN signup vars.
					= c_ws_plugin__optimizemember_utils_time::auto_eot_time($user->ID, $ipn_signup_vars['period1'], $ipn_signup_vars['period3']);

				if($check_gateway) switch($subscr_gateway) // A bit different for each payment gateway.
				{
					case 'paypal': // PayPal (PayPal Pro only).

						if(!c_ws_plugin__optimizemember_utils_conds::pro_is_installed()
							|| !class_exists('c_ws_plugin__optimizemember_pro_paypal_utilities')
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['paypal_api_username']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['paypal_api_password']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['paypal_api_signature']
						) return array_merge($empty_response, array('debug' => 'PayPal Pro API credentials missing in your OptimizeMember configuration.'));

						if($GLOBALS['WS_PLUGIN__']['optimizemember']['o']['paypal_payflow_api_username'])
						{
							if(!($api_response = c_ws_plugin__optimizemember_pro_paypal_utilities::payflow_get_profile($subscr_id)) || !empty($api_response['__error']))
								return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says there is no subscription for this user.'));

							if(preg_match('/^(?:Pending|PendingProfile)$/i', $api_response['STATUS']))
								return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says the subscription for this user is currently pending changes. Unable to determine at this moment. Please try again in 15 minutes.'));

							if(!preg_match('/^(?:Active|ActiveProfile)$/i', $api_response['STATUS']))
								return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
											 'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription is no longer active, and thus, access should be terminated at this time.');

							if($api_response['TERM'] > 0 && $api_response['PAYMENTSLEFT'] <= 0)
								return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
											 'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription has reached its last payment, and thus, access should be terminated at this time.');

							if($api_response['TERM'] <= 0 || $api_response['PAYMENTSLEFT'] > 0)
								if($api_response['NEXTPAYMENT'] && strlen($api_response['NEXTPAYMENT']) === 8) // MMDDYYYY format is not `strtotime()` compatible.
									if(($time = strtotime(substr($api_response['NEXTPAYMENT'], -4).'-'.substr($api_response['NEXTPAYMENT'], 0, 2).'-'.substr($api_response['NEXTPAYMENT'], 2, 2))) > $now)
										return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
													 'debug' => 'The PayPal Pro API says this is the next payment time.');
						}
						else // Use PayPal Pro API (old flavor).
						{
							$api_args = array(
								'METHOD'    => 'GetRecurringPaymentsProfileDetails',
								'PROFILEID' => $subscr_id,
							);
							if(!($api_response = c_ws_plugin__optimizemember_paypal_utilities::paypal_api_response($api_args)) || !empty($api_response['__error']))
								return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says there is no subscription for this user.'));

							if(preg_match('/^(?:Pending|PendingProfile)$/i', $api_response['STATUS']))
								return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says the subscription for this user is currently pending changes. Unable to determine at this moment. Please try again in 15 minutes.'));

							if(!preg_match('/^(?:Active|ActiveProfile)$/i', $api_response['STATUS']))
								return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
											 'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription is no longer active, and thus, access should be terminated at this time.');

							if($api_response['TOTALBILLINGCYCLES'] > 0 && $api_response['NUMCYCLESREMAINING'] <= 0)
								return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
											 'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription has reached its last payment, and thus, access should be terminated at this time.');

							if($api_response['TOTALBILLINGCYCLES'] <= 0 || $api_response['NUMCYCLESREMAINING'] > 0)
								if($api_response['NEXTBILLINGDATE'] && ($time = strtotime($api_response['NEXTBILLINGDATE'])) > $now)
									return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
												 'debug' => 'The PayPal Pro API says this is the next payment time.');
						}
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and there are no more payments needed from this user.'));

						break; // Break switch.

					case 'authnet': // Authorize.Net (EOT only; w/ limited functionality).

						if(!c_ws_plugin__optimizemember_utils_conds::pro_is_installed()
							|| !class_exists('c_ws_plugin__optimizemember_pro_authnet_utilities')
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_authnet_api_login_id']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_authnet_api_trans_key']
						) return array_merge($empty_response, array('debug' => 'Authorize.Net API credentials missing in your OptimizeMember configuration.'));

						$api_args = array(
							'x_method'          => 'status',
							'x_subscription_id' => $subscr_id,
						);
						if(!($api_response = c_ws_plugin__optimizemember_pro_authnet_utilities::authnet_arb_response($api_args)) || !empty($api_response['__error']))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the Authorize.Net API says there is no subscription for this user.'));

						if(!preg_match('/^(?:active)$/i', $api_response['subscription_status']))
							return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
										 'debug' => 'This is the estimated EOT time. The Authorize.Net API says this subscription is no longer active, and thus, access should be terminated at this time.');

						// Next payment time not possible with Authorize.Net at this time.
						// Fixed recurring intervals not possible to query with Authorize.Net at this time.
						return array_merge($empty_response, array('debug' => 'Partially-supported payment gateway; unable to determine.'));

						break; // Break switch.

					case 'stripe': // Stripe payment gateway (best).

						if(!c_ws_plugin__optimizemember_utils_conds::pro_is_installed()
							|| !class_exists('c_ws_plugin__optimizemember_pro_stripe_utilities')
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_stripe_api_publishable_key']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_stripe_api_secret_key']
						) return array_merge($empty_response, array('debug' => 'Stripe API credentials missing in your OptimizeMember configuration.'));

						if(!$subscr_cid) return array_merge($empty_response, array('debug' => 'No fixed EOT, and no `subscr_cid` on file. Unable to determine.'));

						if(!is_object($stripe_subscription = c_ws_plugin__optimizemember_pro_stripe_utilities::get_customer_subscription($subscr_cid, $subscr_id)) || empty($stripe_subscription->id))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the Stripe API says there is no subscription for this user.'));

						if((integer)$stripe_subscription->ended_at > 0) // Done?
						{
							$time = $stripe_subscription->ended_at + $grace_time;
							return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										 'debug' => 'The Stripe API says this subscription reached an expiration on this date + grace time.');
						}
						if(in_array($stripe_subscription->status, array('canceled', 'unpaid'), TRUE) || $stripe_subscription->cancel_at_period_end)
						{
							$time = $stripe_subscription->current_period_end + $grace_time;
							return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										 'debug' => 'The Stripe API says this subscription was cancelled, and thus, should EOT on this date + grace time.');
						}
						if(isset($stripe_subscription->plan->metadata->recurring, $stripe_subscription->plan->metadata->recurring_times)
							&& !$stripe_subscription->plan->metadata->recurring) // Non-recurring subscription?
						{
							$time = (integer)$stripe_subscription->start;
							$time += $stripe_subscription->plan->trial_period_days * DAY_IN_SECONDS;

							switch($stripe_subscription->plan->interval)
							{
								case 'day': // Every X days in this case.
									$time += (DAY_IN_SECONDS * $stripe_subscription->plan->interval_count) * 1;
									break; // Break switch now.

								case 'week': // Every X weeks in this case.
									$time += (WEEK_IN_SECONDS * $stripe_subscription->plan->interval_count) * 1;
									break; // Break switch now.

								case 'month': // Every X months in this case.
									$time += ((WEEK_IN_SECONDS * 4) * $stripe_subscription->plan->interval_count) * 1;
									break; // Break switch now.

								case 'year': // Every X years in this case.
									$time += (YEAR_IN_SECONDS * $stripe_subscription->plan->interval_count) * 1;
									break; // Break switch now.
							}
							if($favor === 'next' && $stripe_subscription->current_period_end + 1 < $time)
							{
								if($stripe_subscription->current_period_end + 1 > $now)
								{
									$time = $stripe_subscription->current_period_end + 1;
									return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
												 'debug' => 'The Stripe API says this is the next payment time.');
								}
								return array_merge($empty_response, array('debug' => 'Stripe says no more payments needed from this user.'));
							}
							$time += $grace_time; // Now add grace to the final EOT time.
							return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										 'debug' => 'The Stripe API says this subscription will be completely over on this date + grace time.');
						}
						if(isset($stripe_subscription->plan->metadata->recurring, $stripe_subscription->plan->metadata->recurring_times)
							&& $stripe_subscription->plan->metadata->recurring && $stripe_subscription->plan->metadata->recurring_times <= 0)
						{
							if($stripe_subscription->current_period_end + 1 > $now)
							{
								$time = $stripe_subscription->current_period_end + 1;
								return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
											 'debug' => 'The Stripe API says this is the next payment time.');
							}
							return array_merge($empty_response, array('debug' => 'Stripe says no more payments needed from this user.'));
						}
						if(isset($stripe_subscription->plan->metadata->recurring, $stripe_subscription->plan->metadata->recurring_times)
							&& $stripe_subscription->plan->metadata->recurring && $stripe_subscription->plan->metadata->recurring_times > 0)
						{
							$time = (integer)$stripe_subscription->start;
							$time += $stripe_subscription->plan->trial_period_days * DAY_IN_SECONDS;

							switch($stripe_subscription->plan->interval)
							{
								case 'day': // Every X days in this case.
									$time += (DAY_IN_SECONDS * $stripe_subscription->plan->interval_count)
										* $stripe_subscription->plan->metadata->recurring_times;
									break; // Break switch now.

								case 'week': // Every X weeks in this case.
									$time += (WEEK_IN_SECONDS * $stripe_subscription->plan->interval_count)
										* $stripe_subscription->plan->metadata->recurring_times;
									break; // Break switch now.

								case 'month': // Every X months in this case.
									$time += ((WEEK_IN_SECONDS * 4) * $stripe_subscription->plan->interval_count)
										* $stripe_subscription->plan->metadata->recurring_times;
									break; // Break switch now.

								case 'year': // Every X years in this case.
									$time += (YEAR_IN_SECONDS * $stripe_subscription->plan->interval_count)
										* $stripe_subscription->plan->metadata->recurring_times;
									break; // Break switch now.
							}
							if($favor === 'next' && $stripe_subscription->current_period_end + 1 < $time)
							{
								if($stripe_subscription->current_period_end + 1 > $now)
								{
									$time = $stripe_subscription->current_period_end + 1;
									return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
												 'debug' => 'The Stripe API says this is the next payment time.');
								}
								return array_merge($empty_response, array('debug' => 'Stripe says no more payments needed from this user.'));
							}
							$time += $grace_time; // Now add grace to the final EOT time.
							return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										 'debug' => 'The Stripe API says this subscription will be completely over on this date + grace time.');
						}
						if($stripe_subscription->current_period_end + 1 > $now)
						{
							$time = $stripe_subscription->current_period_end + 1;
							return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										 'debug' => 'The Stripe API says this is the next payment time.');
						}
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and Stripe says there are no more payments needed from this user.'));

						break; // Break switch.

					case 'clickbank': // ClickBank (limited functionality).

						if(!c_ws_plugin__optimizemember_utils_conds::pro_is_installed()
							|| !class_exists('c_ws_plugin__optimizemember_pro_clickbank_utilities')
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_clickbank_username']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_clickbank_clerk_key']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_clickbank_developer_key']
							|| !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_clickbank_secret_key']
						) return array_merge($empty_response, array('debug' => 'ClickBank API credentials missing in your OptimizeMember configuration.'));

						if(empty($ipn_signup_vars['txn_id'])) // ClickBank receipt number.
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and no `txn_id` on file. Unable to determine.'));

						if(!($api_response = c_ws_plugin__optimizemember_pro_clickbank_utilities::clickbank_api_order($ipn_signup_vars['txn_id'])))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the ClickBank API says there is no subscription for this user.'));

						if(!preg_match('/^(?:TEST_)?SALE$/i', $api_response['txnType']) || !$api_response['recurring'])
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the ClickBank API says there is no recurring subscription for this user.'));

						if(strcasecmp($api_response['status'], 'active') !== 0 || $api_response['futurePayments'] <= 0)
							return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
										 'debug' => 'This is the estimated EOT time. The ClickBank API says this subscription no longer active, or it has reached its last payment, and thus, access should be terminated at this time.');

						if($api_response['nextPaymentDate'] && ($time = strtotime($api_response['nextPaymentDate'])) > $now)
							return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										 'debug' => 'The ClickBank API says this is the next payment time.');

						return array_merge($empty_response, array('debug' => 'No fixed EOT, and there are no more payments needed from this user.'));

						break; // Break switch.

					default: // Default case handler.
						return array_merge($empty_response, array('debug' => 'Partially-supported payment gateway; unable to determine.'));
				}
				return array_merge($empty_response, array('debug' => 'Payment gateway check disabled; unable to determine.'));
			}
			}
	}