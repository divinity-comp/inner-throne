<?php
/**
 * Stripe Update Forms (inner processing routines).
 *
 */
if(!defined('WPINC')) // MUST have WordPress.
    exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_update_in'))
{
    /**
     * Stripe Update Forms (inner processing routines).
     *
     * @package optimizeMember\Stripe
     * @since 140617
     */
    class c_ws_plugin__optimizemember_pro_stripe_update_in
    {
        /**
         * Handles processing of Pro-Form billing updates.
         *
         * @package optimizeMember\Stripe
         * @since 140617
         *
         * @attaches-to ``add_action('init');``
         */
        public static function stripe_update()
        {
            if(!empty($_POST['optimizemember_pro_stripe_update']['nonce'])
               && ($nonce = $_POST['optimizemember_pro_stripe_update']['nonce'])
               && wp_verify_nonce($nonce, 'optimizemember-pro-stripe-update')
            )
            {
                $GLOBALS['ws_plugin__optimizemember_pro_stripe_update_response'] = array(); // This holds the global response details.
                $global_response                                           = & $GLOBALS['ws_plugin__optimizemember_pro_stripe_update_response'];

                $post_vars         = c_ws_plugin__optimizemember_utils_strings::trim_deep(stripslashes_deep($_POST['optimizemember_pro_stripe_update']));
                $post_vars['attr'] = (!empty($post_vars['attr'])) ? (array)unserialize(c_ws_plugin__optimizemember_utils_encryption::decrypt($post_vars['attr'])) : array();
                $post_vars['attr'] = apply_filters('ws_plugin__optimizemember_pro_stripe_update_post_attr', $post_vars['attr'], get_defined_vars());

                $post_vars = c_ws_plugin__optimizemember_utils_captchas::recaptcha_post_vars($post_vars); // Collect reCAPTCHA™ post vars.

                if(!c_ws_plugin__optimizemember_pro_stripe_responses::stripe_form_attr_validation_errors($post_vars['attr']))
                {
                    if(!($form_submission_validation_errors // Validate update input form fields.
                        = c_ws_plugin__optimizemember_pro_stripe_responses::stripe_form_submission_validation_errors('update', $post_vars))
                    ) // If this fails the global response is set to the error(s) returned during form field validation.
                    {
                        if(is_user_logged_in() && ($user = wp_get_current_user()) && ($user_id = $user->ID)) // Are they logged in?
                        {
                            if(($cur__subscr_cid = get_user_option('optimizemember_subscr_cid')) && ($cur__subscr_id = get_user_option('optimizemember_subscr_id')))
                            {
                                if(is_object($stripe_subscription = c_ws_plugin__optimizemember_pro_stripe_utilities::get_customer_subscription($cur__subscr_cid, $cur__subscr_id)) && !preg_match('/^canceled$/i', $stripe_subscription->status) && !$stripe_subscription->cancel_at_period_end)
                                {
                                    unset($_POST['optimizemember_pro_stripe_update']['source_token']); // These are good one-time only.
                                    unset($_POST['optimizemember_pro_stripe_update']['source_token_summary']);

                                    if(is_object($set_customer_source = c_ws_plugin__optimizemember_pro_stripe_utilities::set_customer_source($cur__subscr_cid, $post_vars['source_token'], $post_vars, $post_vars['attr']['reject_prepaid'])))
                                    {
                                        $global_response = array('response' => _x('<strong>Confirmed.</strong> Your billing information has been updated.', 'optimizemember-front', 'optimizemember'));

                                        if($post_vars['attr']['success']
                                           && ($custom_success_url = str_ireplace(array('%%s_response%%', '%%response%%'), array(urlencode(c_ws_plugin__optimizemember_utils_encryption::encrypt($global_response['response'])), urlencode($global_response['response'])), $post_vars['attr']['success']))
                                           && ($custom_success_url = trim(preg_replace('/%%(.+?)%%/i', '', $custom_success_url)))
                                        ) wp_redirect(c_ws_plugin__optimizemember_utils_urls::add_optimizemember_sig($custom_success_url, 's2p-v')).exit ();
                                    }
                                    else $global_response = array('response' => $set_customer_source, 'error' => TRUE);
                                }
                                else $global_response = array('response' => _x('<strong>Unable to update.</strong> You have NO recurring fees. Or, your billing profile is no longer active. Please contact Support if you need assistance.', 'optimizemember-front', 'optimizemember'), 'error' => TRUE);
                            }
                            else $global_response = array('response' => _x('<strong>Oops.</strong> No Customer|Subscr. ID. Please contact Support for assistance.', 'optimizemember-front', 'optimizemember'), 'error' => TRUE);
                        }
                        else $global_response = array('response' => _x('You\'re <strong>NOT</strong> logged in.', 'optimizemember-front', 'optimizemember'), 'error' => TRUE);
                    }
                    else // Input form field validation errors.
                        $global_response = $form_submission_validation_errors;
                }
            }
        }
    }
}
