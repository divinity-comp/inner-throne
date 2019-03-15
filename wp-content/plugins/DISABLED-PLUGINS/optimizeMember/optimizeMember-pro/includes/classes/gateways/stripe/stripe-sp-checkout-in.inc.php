<?php
/**
 * Stripe Specific Post/Page Forms (inner processing routines).
 *
 */
if(!defined('WPINC')) // MUST have WordPress.
    exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_sp_checkout_in'))
{
    /**
     * Stripe Specific Post/Page Forms (inner processing routines).
     *
     * @package optimizeMember\Stripe
     * @since 140617
     */
    class c_ws_plugin__optimizemember_pro_stripe_sp_checkout_in
    {
        /**
         * Handles processing of Pro-Forms for Specific Post/Page checkout.
         *
         * @package optimizeMember\Stripe
         * @since 140617
         *
         * @attaches-to ``add_action('init');``
         *
         * @return null Or exits script execution after a custom URL redirection.
         */
        public static function stripe_sp_checkout()
        {
            if(!empty($_POST['optimizemember_pro_stripe_sp_checkout']['nonce'])
               && ($nonce = $_POST['optimizemember_pro_stripe_sp_checkout']['nonce'])
               && wp_verify_nonce($nonce, 'optimizemember-pro-stripe-sp-checkout')
            )
            {
                $GLOBALS['ws_plugin__optimizemember_pro_stripe_sp_checkout_response'] = array(); // This holds the global response details.
                $global_response                                                = &$GLOBALS['ws_plugin__optimizemember_pro_stripe_sp_checkout_response'];

                $post_vars         = c_ws_plugin__optimizemember_utils_strings::trim_deep(stripslashes_deep($_POST['optimizemember_pro_stripe_sp_checkout']));
                $post_vars['attr'] = !empty($post_vars['attr']) ? (array)unserialize(c_ws_plugin__optimizemember_utils_encryption::decrypt($post_vars['attr'])) : array();
                $post_vars['attr'] = apply_filters('ws_plugin__optimizemember_pro_stripe_sp_checkout_post_attr', $post_vars['attr'], get_defined_vars());

                $post_vars['name']  = trim($post_vars['first_name'].' '.$post_vars['last_name']);
                $post_vars['email'] = apply_filters('user_registration_email', sanitize_email($post_vars['email']), get_defined_vars());

                $post_vars = c_ws_plugin__optimizemember_utils_captchas::recaptcha_post_vars($post_vars); // Collect reCAPTCHA™ post vars.

                if(!c_ws_plugin__optimizemember_pro_stripe_responses::stripe_form_attr_validation_errors($post_vars['attr']))
                {
                    if(!($form_submission_validation_errors // Validate sp-checkout input form fields.
                        = c_ws_plugin__optimizemember_pro_stripe_responses::stripe_form_submission_validation_errors('sp-checkout', $post_vars))
                    ) // If this fails the global response is set to the error(s) returned during form field validation.
                    {
                        unset($_POST['optimizemember_pro_stripe_sp_checkout']['source_token']); // Good one-time only.
                        unset($_POST['optimizemember_pro_stripe_sp_checkout']['source_token_summary']); // Good one-time only.

                        $is_bitcoin        = !empty($post_vars['source_token']) && stripos($post_vars['source_token'], 'btcrcv_') === 0;
                        $cp_attr           = c_ws_plugin__optimizemember_pro_stripe_utilities::apply_coupon($post_vars['attr'], $post_vars['coupon'], 'attr', array('affiliates-silent-post'));
                        $cost_calculations = c_ws_plugin__optimizemember_pro_stripe_utilities::cost(NULL, $cp_attr['ra'], $post_vars['state'], $post_vars['country'], $post_vars['zip'], $cp_attr['cc'], $cp_attr['desc'], $is_bitcoin);

                        if(!$global_response)
                            if($cost_calculations['total'] > 0)
                            {
                                if(!is_object($stripe_customer = c_ws_plugin__optimizemember_pro_stripe_utilities::get_customer(get_current_user_id(), $post_vars['email'], $post_vars['first_name'], $post_vars['last_name'], array(), $post_vars)))
                                    $global_response = array('response' => $stripe_customer, 'error' => TRUE);

                                else if(!is_object($stripe_customer = $stripe_customer_with_source = c_ws_plugin__optimizemember_pro_stripe_utilities::set_customer_source($stripe_customer->id, $post_vars['source_token'], $post_vars, $post_vars['attr']['reject_prepaid'])))
                                    $global_response = array('response' => $stripe_customer, 'error' => TRUE);

                                else if(!is_object($stripe_charge = c_ws_plugin__optimizemember_pro_stripe_utilities::create_customer_charge($stripe_customer->id, $cost_calculations['total'], $cost_calculations['cur'], $cost_calculations['desc'], array(), $post_vars, $cost_calculations)))
                                    $global_response = array('response' => $stripe_charge, 'error' => TRUE);

                                else // We got what we needed here.
                                {
                                    $new__txn_cid = $stripe_customer->id;
                                    $new__txn_id  = $stripe_charge->id;
                                }
                            }
                        if(!$global_response)
                        {
                            if(empty($new__txn_cid)) $new__txn_cid = strtoupper('free-'.uniqid());
                            if(empty($new__txn_id)) $new__txn_id = strtoupper('free-'.uniqid());

                            $ipn['txn_type'] = 'web_accept';
                            $ipn['txn_cid']  = $new__txn_cid;
                            $ipn['txn_id']   = $new__txn_id;
                            $ipn['custom']   = $post_vars['attr']['custom'];

                            $ipn['mc_gross']    = $cost_calculations['total'];
                            $ipn['mc_currency'] = $cost_calculations['cur'];
                            $ipn['tax']         = $cost_calculations['tax'];

                            $ipn['payer_email'] = $post_vars['email'];
                            $ipn['first_name']  = $post_vars['first_name'];
                            $ipn['last_name']   = $post_vars['last_name'];

                            if(is_user_logged_in() && ($referencing = c_ws_plugin__optimizemember_utils_users::get_user_subscr_or_wp_id()))
                            {
                                $ipn['option_name1']      = 'Referencing Customer ID';
                                $ipn['option_selection1'] = $referencing;
                            }
                            else // Otherwise, default to the originating domain.
                            {
                                $ipn['option_name1']      = 'Originating Domain';
                                $ipn['option_selection1'] = $_SERVER['HTTP_HOST'];
                            }
                            $ipn['option_name2']      = 'Customer IP Address';
                            $ipn['option_selection2'] = $_SERVER['REMOTE_ADDR'];

                            $ipn['item_name']   = $cost_calculations['desc'];
                            $ipn['item_number'] = $post_vars['attr']['sp_ids_exp'];

                            $ipn['optimizemember_paypal_proxy']              = 'stripe';
                            $ipn['optimizemember_paypal_proxy_use']          = 'pro-emails';
                            $ipn['optimizemember_paypal_proxy_coupon']       = array('coupon_code' => $cp_attr['_coupon_code'], 'full_coupon_code' => $cp_attr['_full_coupon_code'], 'affiliate_id' => $cp_attr['_coupon_affiliate_id']);
                            $ipn['optimizemember_paypal_proxy_verification'] = c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen();
                            $ipn['optimizemember_paypal_proxy_return_url']   = $post_vars['attr']['success'];

                            $ipn['optimizemember_stripe_proxy_return_url'] = trim(c_ws_plugin__optimizemember_utils_urls::remote(home_url('/?optimizemember_paypal_notify=1'), $ipn, array('timeout' => 20)));

                            setcookie('optimizemember_sp_tracking', ($optimizemember_sp_tracking = c_ws_plugin__optimizemember_utils_encryption::encrypt($new__txn_id)), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).
                            setcookie('optimizemember_sp_tracking', $optimizemember_sp_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).
                            ($_COOKIE['optimizemember_sp_tracking'] = $optimizemember_sp_tracking);

                            if(($sp_access_url = c_ws_plugin__optimizemember_sp_access::sp_access_link_gen($post_vars['attr']['ids'], $post_vars['attr']['exp'])))
                            {
                                $global_response = array('response' => sprintf(_x('<strong>Thank you.</strong> Your purchase has been approved.<br />&mdash; Please <a href="%s" rel="nofollow">click here</a> to proceed.', 'optimizemember-front', 'optimizemember'), esc_attr($sp_access_url)));

                                if($post_vars['attr']['success'] && substr($ipn['optimizemember_stripe_proxy_return_url'], 0, 2) === substr($post_vars['attr']['success'], 0, 2)
                                   && ($custom_success_url = str_ireplace(array('%%s_response%%', '%%response%%'), array(urlencode(c_ws_plugin__optimizemember_utils_encryption::encrypt($global_response['response'])), urlencode($global_response['response'])), $ipn['optimizemember_stripe_proxy_return_url']))
                                   && ($custom_success_url = trim(preg_replace('/%%(.+?)%%/i', '', $custom_success_url)))
                                ) wp_redirect(c_ws_plugin__optimizemember_utils_urls::add_optimizemember_sig($custom_success_url, 's2p-v')).exit ();
                            }
                            else $global_response = array('response' => _x('<strong>Oops.</strong> Unable to generate Access Link. Please contact Support for assistance.', 'optimizemember-front', 'optimizemember'), 'error' => TRUE);
                        }
                    }
                    else // Input form field validation errors.
                        $global_response = $form_submission_validation_errors;
                }
            }
        }
    }
}
