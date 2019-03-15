<?php
/**
 * optimizeMember Pro Gateways.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
    exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_gateways'))
{
    /**
     * s2Member Pro Gateways.
     *
     * @package optimizeMember\Gateways
     * @since 1.5
     */
    class c_ws_plugin__optimizemember_pro_gateways
    {
        /**
         * Array of available Payment Gateways supported by s2Member Pro.
         *
         * @package optimizeMember\Gateways
         * @since 1.5
         *
         * @return array Array of available Payment Gateways.
         */
        public static function available_gateways() // Payment Gateways available.
        {

            $gateways = array(
                'paypal' => '<strong>PayPal Website Payments Pro</strong> <em>(w/ Pro-Forms)</em><br /><span style="font-size:80%;">&uarr; supports Buy Now &amp; Recurring Products.</span>',
                'stripe' => '<strong>Stripe</strong> <em>(w/ Pro-Forms)</em><br /><span style="font-size:80%;">&uarr; supports Buy Now &amp; Recurring Products.</span>',
                'authnet' => '<strong>Authorize.Net</strong> <em>(w/ Pro-Forms)</em><br /><span style="font-size:80%;">&uarr; supports Buy Now &amp; Recurring Products.</span>',
                'clickbank' => '<strong>ClickBank</strong> <em>(w/ Buttons)</em><br /><span style="font-size:80%;">&uarr; supports Buy Now &amp; Recurring Products.</span>',
                'google' => '<span style="opacity:0.5;"><strong>Google Wallet</strong> <em>(w/ Buttons)</em><br /><span style="font-size:80%;">&uarr; retired by Google March 2nd, 2015.</span></span>',
                'alipay' => '<span style="opacity:0.5;"><strong>AliPay</strong> <em>(w/ Buttons)</em><br /><span style="font-size:80%;">&uarr; deprecated June 15th, 2015.</span></span>',
                'ccbill' => '<span style="opacity:0.5;"><strong>ccBill</strong> <em>(w/ Buttons)</em><br /><span style="font-size:80%;">&uarr; deprecated June 15th, 2015.</span></span>',
            );

            return apply_filters('ws_plugin__optimizemember_pro_available_gateways', $gateways, get_defined_vars());
        }

        /**
         * Adds to the list of Payment Gateways in User Profile management panels.
         *
         * @package optimizeMember\Gateways
         * @since 1.5
         *
         * @attaches-to ``add_filter('ws_plugin__optimizemember_profile_optimizemember_subscr_gateways');``
         *
         * @param array $gateways Expects an array of Payment Gateways, passed through by the Filter.
         *
         * @return array Array of Payment Gateways to appear in Profile editing panels.
         */
        public static function profile_subscr_gateways($gateways)
        {
            $available_gateways = array_keys(c_ws_plugin__optimizemember_pro_gateways::available_gateways());

            foreach(($others = array('alipay' => 'AliPay (code: alipay)', 'stripe' => 'Stripe (code: stripe)', 'authnet' => 'Authorize.Net (code: authnet)', 'ccbill' => 'ccBill (code: ccbill)', 'clickbank' => 'ClickBank (code: clickbank)'/*, 'google' => 'Google Wallet (code: google)'*/)) as $other => $gateway)
                if(!in_array($other, $available_gateways))
                    unset($others[$other]);

            return apply_filters('ws_plugin__optimizemember_pro_profile_subscr_gateways', array_unique(array_merge((array)$gateways, $others)), get_defined_vars());
        }

        /**
         * Loads Hooks/Functions/Codes for other Payment Gateways.
         *
         * @package optimizeMember\Gateways
         * @since 1.5
         *
         * @attaches-to ``add_action('ws_plugin__optimizemember_after_loaded');``
         */
        public static function load_gateways() // Load Hooks/Functions/Codes for other Gateways.
        {
            foreach(array_keys(c_ws_plugin__optimizemember_pro_gateways::available_gateways()) as $gateway)
                if(in_array($gateway, $GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_gateways_enabled']))
                {
                    include_once dirname(dirname(__FILE__)).'/separates/gateways/'.$gateway.'/'.$gateway.'-hooks.inc.php';
                    include_once dirname(dirname(__FILE__)).'/separates/gateways/'.$gateway.'/'.$gateway.'-funcs.inc.php';
                    include_once dirname(dirname(__FILE__)).'/separates/gateways/'.$gateway.'/'.$gateway.'-codes.inc.php';
                }
        }
    }
}