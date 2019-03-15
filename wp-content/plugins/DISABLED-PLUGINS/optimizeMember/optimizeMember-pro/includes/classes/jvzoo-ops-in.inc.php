<?php
/**
* optimizeMember Pro Remote Operations API ( inner processing routines ).
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* This WordPress plugin ( optimizeMember Pro ) is comprised of two parts:
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
*   You should have received a copy of the GNU General Public License,
*   along with this software. In the main directory, see: /licensing/
*   If not, see: {@link http://www.gnu.org/licenses/}.
*
* o (2) All other parts of ( optimizeMember Pro ); including, but not limited to:
*   the CSS code, some JavaScript code, images, and design;
*   are licensed according to the license purchased.
*   See: {@link http://www.optimizepress.com/prices/}
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
* @package optimizeMember\API_Jvzoo_Ops
* @since 271014
*/
if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
    exit("Do not access this file directly.");
}
/**/
if (!class_exists("c_ws_plugin__optimizemember_pro_jvzoo_ops_in")) {
    /**
    * optimizeMember Pro Remote Operations API ( inner processing routines ).
    *
    * @package optimizeMember\API_Jvzoo_Ops
    * @since 271014
    */
    class c_ws_plugin__optimizemember_pro_jvzoo_ops_in
    {
        /**
         * Check if this request is a specific transaction action
         * @see http://support.jvzoo.com/Knowledgebase/Article/View/17/2/jvzipn
         * @since 200515
         * @param  string  $action
         * @param  array  $op
         * @return boolean
         */
        public static function is_action($action, $op = null)
        {
            if (null !== $op && !empty($op['ctransaction']) && strtolower($op['ctransaction']) === strtolower($action)) {
                return true;
            }

            return false;
        }

        /**
        * Creates a new User.
        *
        * @package optimizeMember\API_Jvzoo_Ops
        * @since 271014
        *
        * @param array An input array of JVzoo Operation parameters.
        * @return str Returns a serialized array with an `ID` element object on success,
        *   else returns a string beginning with `Error:` on failure; which will include details regarding the error.
        */
        public static function create_user($op = NULL)
        {
            if (!empty($_GET["op"]) && $_GET["op"] === "create_user") {
                // We need to check for refund transaction - if this is the case we should delete customer
                if (c_ws_plugin__optimizemember_pro_jvzoo_ops_in::is_action('RFND', $op)) {
                    $_GET['op'] = 'delete_user';
                    return c_ws_plugin__optimizemember_pro_jvzoo_ops_in::delete_user($op);
                }

                // Check modify_if_login_exists flag and redirect to modify_user action
                if (!empty($_GET["modify_if_login_exists"])) {
                    if (!empty($op["ccustemail"]) && ($_user = new WP_User((string)$op["ccustemail"])) && !empty($_user->ID)) {
                        $_GET['op'] = 'modify_user';
                        return c_ws_plugin__optimizemember_pro_jvzoo_ops_in::modify_user($op);
                    }
                }
                /**/
                $GLOBALS["ws_plugin__optimizemember_registration_vars"] = array ();
                $v = &$GLOBALS["ws_plugin__optimizemember_registration_vars"];
                /**/
                $v["ws_plugin__optimizemember_custom_reg_field_user_login"] = (string)@$op["ccustemail"];
                $v["ws_plugin__optimizemember_custom_reg_field_user_email"] = (string)@$op["ccustemail"];
                /**/
                $password = wp_generate_password();
                $GLOBALS["ws_plugin__optimizemember_generate_password_return"] = $password;
                /**/
                if (isset($op['ccustname']) && !empty($op['ccustname'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_first_name"] = (string)$op['ccustname'];
                }
                /**/
                if (isset($_GET['optimizemember_level'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_level"] = sanitize_text_field($_GET["optimizemember_level"]);
                }
                if (isset($_GET['optimizemember_ccaps'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_ccaps"] = sanitize_text_field($_GET["optimizemember_ccaps"]);
                }
                /**/
                if (isset($_GET['optimizemember_registration_ip'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_registration_ip"] = sanitize_text_field($_GET["optimizemember_registration_ip"]);
                }
                /**/
                if (isset($_GET['optimizemember_subscr_gateway'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_subscr_gateway"] = sanitize_text_field($_GET["optimizemember_subscr_gateway"]);
                }
                if (isset($_GET['optimizemember_subscr_id'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_subscr_id"] = sanitize_text_field($_GET["optimizemember_subscr_id"]);
                }
                if (isset($_GET['optimizemember_custom'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_custom"] = sanitize_text_field($_GET["optimizemember_custom"]);
                }
                /**/
                if (isset($_GET['optimizemember_auto_eot_time'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_auto_eot_time"] = sanitize_text_field($_GET["optimizemember_auto_eot_time"]);
                }
                /**/
                if (isset($_GET['optimizemember_notes'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_optimizemember_notes"] = sanitize_text_field($_GET["optimizemember_notes"]);
                }
                /**/
                if (isset($_GET['opt_in'])) {
                    $v["ws_plugin__optimizemember_custom_reg_field_opt_in"] = sanitize_text_field($_GET["opt_in"]);
                }
                /**/
                if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"]) {
                    foreach (json_decode ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"], true) as $field) {
                        $field_var = preg_replace ("/[^a-z0-9]/i", "_", strtolower ($field["id"]));
                        $field_id_class = preg_replace ("/_/", "-", $field_var);
                        /**/
                        if (isset($_GET["custom_fields"][$field_var])) {
                            $v["ws_plugin__optimizemember_custom_reg_field_" . $field_var] = $_GET["custom_fields"][$field_var];
                        }
                    }
                }
                $create = array ("user_login" => (string)$op["ccustemail"], "user_pass" => (string)$password, "user_email" => (string)$op["ccustemail"]);
                /**/
                if (((is_multisite () && ($new = $user_id = c_ws_plugin__optimizemember_registrations::ms_create_existing_user($create["user_login"], $create["user_email"], $create["user_pass"]))) || ($new = $user_id = wp_create_user($create["user_login"], $create["user_pass"], $create["user_email"]))) && !is_wp_error($new)) {
                    if (is_object($user = new WP_User($user_id)) && !empty($user->ID) && ($user_id = $user->ID)) {
                        if (!empty($_GET["notification"])) {
                            // wp_new_user_notification ($user_id, $password);
                            if (version_compare(get_bloginfo("version"), "4.3.1", ">=")) {
                                wp_new_user_notification($user_id, null, "both", $password);
                            } else if (version_compare(get_bloginfo("version"), "4.3", ">=")) {
                                wp_new_user_notification($user_id, "both", $password);
                            } else {
                                wp_new_user_notification($user_id, $password);
                            }
                        }
                        /**/
                        return serialize(array("ID" => $user_id));
                    }
                    return "Error: Creation may have failed. Unable to obtain WP_User ID.";
                }
                else if (is_wp_error($new) && $new->get_error_code()) {
                    return "Error: " . $new->get_error_message ();
                }
                /**/
                return "Error: User creation failed for an unknown reason. Please try again.";
            }
            return "Error: Empty or invalid request ( `create_user` ). Please try again.";
        }

        /**
        * Modifies an existing User.
        *
        * @package optimizeMember\API_Jvzoo_Ops
        * @since 271014
        *
        * @param array An input array of JVzoo Operation parameters.
        * @return str Returns a serialized array with an `ID` element object on success,
        *   else returns a string beginning with `Error:` on failure; which will include details regarding the error.
        */
        public static function modify_user($op = NULL)
        {
            if (!empty ($_GET["op"]) && $_GET["op"] === "modify_user") {
                // We need to check for refund transaction - if this is the case we should delete customer
                if (c_ws_plugin__optimizemember_pro_jvzoo_ops_in::is_action('RFND', $op)) {
                    $_GET['op'] = 'delete_user';
                    return c_ws_plugin__optimizemember_pro_jvzoo_ops_in::delete_user($op);
                }

                if(!empty($op["user_id"]) && ($_user = new WP_User((integer)$op["user_id"])) && !empty($_user->ID)) {
                    $user = $_user;
                } else if(!empty($op["ccustemail"]) && ($_user = new WP_User((string)$op["ccustemail"])) && !empty($_user->ID)) {
                    $user = $_user;
                } else {
                    return "Error: Modification failed. Unable to obtain WP_User object instance with data supplied (i.e. ID/Username not found).";
                }
                /**/
                if (is_multisite () && !is_user_member_of_blog ($user->ID)) {
                    return "Error: Modification failed. Unable to obtain WP_User object instance with data supplied (i.e. ID/Username not a part of this Blog).";
                }
                /**/
                if(is_super_admin($user->ID) || $user->has_cap("administrator")) {
                    return "Error: Modification failed. This API will not modify Administrators.";
                }
                /**/
                $userdata["ID"] = /* Needed for database update. */ $user->ID;
                /**/
                if (!empty ($op["ccustemail"])) {
                    if (is_email ((string)$op["ccustemail"]) && !email_exists ((string)$op["ccustemail"])) {
                            $userdata["user_email"] = (string)$op["ccustemail"];
                    }
                }
                /**/
                if (isset ($_GET["optimizemember_level"]) && (integer)$_GET["optimizemember_level"] === 0)
                {
                    if /* Not the same? */ (c_ws_plugin__optimizemember_user_access::user_access_role ($user) !== get_option("default_role")) {
                        $userdata["role"] = get_option ("default_role");
                    }
                }  else if (!empty ($_GET["optimizemember_level"]) && (integer)$_GET["optimizemember_level"] > 0) {
                    if /* Not the same? */ (c_ws_plugin__optimizemember_user_access::user_access_role ($user) !== "optimizemember_level".(integer)$_GET["optimizemember_level"]) {
                        $userdata["role"] = "optimizemember_level".(integer)$_GET["optimizemember_level"];
                    }
                }
                wp_update_user /* OK. Now send this array for an update. */($userdata);
                /**/
                $old_user = /* Copy existing User obj. */ unserialize(serialize($user));
                $user = /* Update our object instance. */ new WP_User($user->ID);
                /**/
                $role = c_ws_plugin__optimizemember_user_access::user_access_role ($user);
                $level = c_ws_plugin__optimizemember_user_access::user_access_role_to_level($role);
                /**/
                if(!empty($_GET["auto_opt_out_transition"])) {
                    $_p["ws_plugin__optimizemember_custom_reg_auto_opt_out_transitions"] = TRUE;
                }
                /**/
                if /* In this case, we need to fire Hook: `ws_plugin__optimizemember_during_collective_mods`. */(!empty($userdata["role"])) {
                    do_action("ws_plugin__optimizemember_during_collective_mods", $user->ID, get_defined_vars(), "user-role-change", "modification", $role, $user, $old_user);
                }
                /**/
                if (!empty($_GET["optimizemember_ccaps"]) && preg_match ("/^-all/", str_replace ("+", "", (string)$_GET["optimizemember_ccaps"]))) {
                    foreach ($user->allcaps as $cap => $cap_enabled) {
                        if (preg_match ("/^access_optimizemember_ccap_/", $cap))
                            $user->remove_cap ($ccap = $cap);
                    }
                }
                /**/
                if (!empty($_GET["optimizemember_ccaps"]) && preg_replace ("/^-all[\r\n\t\s;,]*/", "", str_replace ("+", "", (string)$_GET["optimizemember_ccaps"]))) {
                    foreach (preg_split ("/[\r\n\t\s;,]+/", preg_replace ("/^-all[\r\n\t\s;,]*/", "", str_replace ("+", "", (string)$_GET["optimizemember_ccaps"]))) as $ccap) {
                        if (strlen ($ccap = trim (strtolower (preg_replace ("/[^a-z_0-9]/i", "", $ccap))))) {
                            $user->add_cap ("access_optimizemember_ccap_" . $ccap);
                        }
                    }
                }
                /**/
                if(isset($_GET["optimizemember_originating_blog"]) && is_multisite()) {
                    update_user_meta($user->ID, "optimizemember_originating_blog", (integer)$_GET["optimizemember_originating_blog"]);
                }
                /**/
                if(isset($_GET["optimizemember_subscr_gateway"])) {
                    update_user_option($user->ID, "optimizemember_subscr_gateway", (string)$_GET["optimizemember_subscr_gateway"]);
                }
                /**/
                if(isset($_GET["optimizemember_subscr_id"]))
                    update_user_option($user->ID, "optimizemember_subscr_id", (string)$_GET["optimizemember_subscr_id"]);
                /**/
                if(isset($_GET["optimizemember_custom"])) {
                    update_user_option($user->ID, "optimizemember_custom", (string)$$_GET["optimizemember_custom"]);
                }
                /**/
                if(isset($_GET["optimizemember_registration_ip"])) {
                    update_user_option($user->ID, "optimizemember_registration_ip", (string)$_GET["optimizemember_registration_ip"]);
                }
                /**/
                if(isset($_GET["optimizemember_notes"])) {
                    update_user_option($user->ID, "optimizemember_notes", trim(get_user_option("optimizemember_notes", $user->ID)."\n\n".(string)$_GET["optimizemember_notes"]));
                }
                /**/
                if(isset($_GET["optimizemember_auto_eot_time"])) {
                    update_user_option($user->ID, "optimizemember_auto_eot_time", ((!empty($_GET["optimizemember_auto_eot_time"])) ? strtotime((string)$_GET["optimizemember_auto_eot_time"]) : ""));
                }
                /**/
                if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"]) {
                    $_existing_fields = get_user_option("optimizemember_custom_fields", $user->ID);
                    /**/
                    foreach(json_decode($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"], true) as $field)
                    {
                        $field_var = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
                        $field_id_class = preg_replace("/_/", "-", $field_var);
                        /**/
                        if(!isset($_GET["custom_fields"][$field_var]))
                        {
                            if(isset($_existing_fields[$field_var]) && ((is_array($_existing_fields[$field_var]) && !empty($_existing_fields[$field_var])) || (is_string($_existing_fields[$field_var]) && strlen($_existing_fields[$field_var]))))
                                $fields[$field_var] = $_existing_fields[$field_var];
                            else unset($fields[$field_var]);
                        }
                        else // Default case handler.
                        {
                            if((is_array($_GET["custom_fields"][$field_var]) && !empty($_GET["custom_fields"][$field_var])) || (is_string($_GET["custom_fields"][$field_var]) && strlen($_GET["custom_fields"][$field_var])))
                                $fields[$field_var] = $_GET["custom_fields"][$field_var];
                            else unset($fields[$field_var]);
                        }
                    }
                    if(!empty($fields)) {
                        update_user_option($user->ID, "optimizemember_custom_fields", $fields);
                    } else {
                        delete_user_option($user->ID, "optimizemember_custom_fields");
                    }
                }
                if ($level > 0) {
                    $pr_times = get_user_option("optimizemember_paid_registration_times", $user->ID);
                    $pr_times["level"] = (empty($pr_times["level"])) ? time() : $pr_times["level"];
                    $pr_times["level".$level] = (empty($pr_times["level".$level])) ? time() : $pr_times["level".$level];
                    update_user_option ($user->ID, "optimizemember_paid_registration_times", $pr_times);
                }
                if (!empty($_GET["opt_in"]) && !empty($role) && $level >= 0) {
                    c_ws_plugin__optimizemember_list_servers::process_list_servers($role, $level, $user->user_login, ((!empty($_GET["user_pass"])) ? (string)$_GET["user_pass"] : ""), $user->user_email, $user->first_name, $user->last_name, false, true, true, $user->ID);
                }
                /**/
                if (!empty($_GET["reset_ip_restrictions"])) {
                    c_ws_plugin__optimizemember_ip_restrictions::delete_reset_specific_ip_restrictions(strtolower($user->user_login));
                }
                /**/
                if (!empty($_GET["reset_file_download_access_log"])) {
                    delete_user_option ($user->ID, "optimizemember_file_download_access_log");
                }
                /**/
                return serialize(array("ID" => $user->ID));
            }
            return "Error: Empty or invalid request ( `modify_user` ). Please try again.";
        }

        /**
        * Deletes an existing User.
        *
        * @package optimizeMember\API_Jvzoo_Ops
        * @since 271014
        *
        * @param array An input array of JVzoo Operation parameters.
        * @return str Returns a serialized array with an `ID` element object on success,
        *   else returns a string beginning with `Error:` on failure; which will include details regarding the error.
        */
        public static function delete_user($op = NULL)
        {
            if (!empty ($_GET["op"]) && $_GET["op"] === "delete_user") {
                if (!empty($_GET["user_id"]) && ($_user = new WP_User((integer)$_GET["user_id"])) && !empty($_user->ID)) {
                    $user = $_user;
                } else if (!empty($op['ccustemail']) && ($_user = new WP_User((string)$op['ccustemail'])) && !empty($_user->ID)) {
                    $user = $_user;
                } else {
                    return "Error: Deletion failed. Unable to obtain WP_User object instance.";
                }
                /**/
                if (is_super_admin($user->ID) || $user->has_cap("administrator")) {
                    return "Error: Deletion failed. This API will not delete Administrators.";
                }
                /**/
                include_once ABSPATH . "wp-admin/includes/admin.php";
                wp_delete_user($user->ID);
                /**/
                return serialize(array("ID" => $user->ID));
            }
            return "Error: Empty or invalid request (`delete_user`). Please try again.";
        }
    }
}