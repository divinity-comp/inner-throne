<?php
/**
 * Stripe Menu Pages.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_menu_pages'))
{
	/**
	 * Stripe Menu Pages.
	 *
	 * @package optimizeMember\Menu_Pages
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_menu_pages
	{
		/**
		 * Add the pages for this Payment Gateway.
		 *
		 * @package optimizeMember\Menu_Pages
		 * @since 140617
		 *
		 * @attaches-to ``add_filter('ws_plugin__optimizemember_during_add_admin_options_add_divider_3');``
		 *
		 * @param bool  $add_divider Expects a boolean value, passed through by the Filter.
		 * @param array $vars Expects an array of defined variables passed through by the Filter.
		 *
		 * @return bool Passes back the original value of ``$add_divider``.
		 */
		public static function stripe_admin_options($add_divider, $vars)
		{
			add_submenu_page($vars['menu'], '', '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>', 'create_users', '#');
			add_submenu_page($vars['menu'], 'optimizeMember / Stripe Options', 'Stripe Options', 'create_users', 'ws-plugin--optimizemember-pro-stripe-ops', 'c_ws_plugin__optimizemember_pro_stripe_menu_pages::stripe_ops_page');
			add_submenu_page($vars['menu'], 'optimizeMember / Stripe Pro Forms', 'Stripe Pro-Forms', 'create_users', 'ws-plugin--optimizemember-pro-stripe-forms', 'c_ws_plugin__optimizemember_pro_stripe_menu_pages::stripe_forms_page');

			return $add_divider; // Now add the divider.
		}

		/**
		 * Builds the documentation for Scripting / API Constants related to this Payment Gateway.
		 *
		 * @package optimizeMember\Menu_Pages
		 * @since 140617
		 *
		 * @attaches-to ``add_action('ws_plugin__optimizemember_during_scripting_page_during_left_sections_during_list_of_api_constants');``
		 *
		 * @param array $vars Expects an array of defined variables passed through by the Action Hook.
		 */
		public static function stripe_scripting_page_api_constants($vars)
		{
			include_once dirname(dirname(dirname(dirname(__FILE__)))).'/menu-pages/stripe-s-api-c.inc.php';
		}

		/**
		 * Builds the options panel for this Payment Gateway.
		 *
		 * @package optimizeMember\Menu_Pages
		 * @since 140617
		 */
		public static function stripe_ops_page()
		{
			c_ws_plugin__optimizemember_menu_pages::update_all_options(); // Updates options.

			$logs_dir = $GLOBALS['WS_PLUGIN__']['optimizemember']['c']['logs_dir'];

			if(!is_dir($logs_dir) && is_writable(dirname(c_ws_plugin__optimizemember_utils_dirs::strip_dir_app_data($logs_dir))))
				mkdir($logs_dir, 0777, TRUE).clearstatcache();

			$htaccess          = $GLOBALS['WS_PLUGIN__']['optimizemember']['c']['logs_dir'].'/.htaccess';
			$htaccess_contents = trim(c_ws_plugin__optimizemember_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['optimizemember']['c']['logs_dir_htaccess'])));

			if(is_dir($logs_dir) && is_writable($logs_dir) && !file_exists($htaccess))
				file_put_contents($htaccess, $htaccess_contents).clearstatcache();

			if($GLOBALS['WS_PLUGIN__']['optimizemember']['o']['gateway_debug_logs']) // Logging enabled?
			{
				if(!is_dir($logs_dir)) // If the security-enabled logs directory does not exist yet.
					c_ws_plugin__optimizemember_admin_notices::display_admin_notice('The security-enabled logs directory (<code>'.esc_html(c_ws_plugin__optimizemember_utils_dirs::doc_root_path($logs_dir)).'</code>) does not exist. Please create this directory manually &amp; make it writable (chmod 777).', TRUE);

				else if(!is_writable($logs_dir)) // If the logs directory is not writable yet.
					c_ws_plugin__optimizemember_admin_notices::display_admin_notice('Permissions error. The security-enabled logs directory (<code>'.esc_html(c_ws_plugin__optimizemember_utils_dirs::doc_root_path($logs_dir)).'</code>) is not writable. Please make this directory writable (chmod 777).', TRUE);

				if(!file_exists($htaccess)) // If the .htaccess file has not been created yet.
					c_ws_plugin__optimizemember_admin_notices::display_admin_notice('The .htaccess protection file (<code>'.esc_html(c_ws_plugin__optimizemember_utils_dirs::doc_root_path($htaccess)).'</code>) does not exist. Please create this file manually. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);

				else if(!preg_match('/deny from all/i', file_get_contents($htaccess))) // Else if the .htaccess file does not offer the required protection.
					c_ws_plugin__optimizemember_admin_notices::display_admin_notice('Unprotected. The .htaccess protection file (<code>'.esc_html(c_ws_plugin__optimizemember_utils_dirs::doc_root_path($htaccess)).'</code>) does not contain <code>deny from all</code>. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);
			}

			include_once dirname(dirname(dirname(dirname(__FILE__)))).'/menu-pages/stripe-ops.inc.php';
		}

		/**
		 * Builds the Forms page for this Payment Gateway.
		 *
		 * @package optimizeMember\Menu_Pages
		 * @since 140617
		 */
		public static function stripe_forms_page()
		{
			if(c_ws_plugin__optimizemember_pro_stripe_responses::stripe_form_api_validation_errors()) // Report error if Stripe Options are not yet configured.
				c_ws_plugin__optimizemember_admin_notices::display_admin_notice('Please configure <code>optimizeMember -› Stripe Options</code> first. Once all of your Stripe Options are configured, return to this page &amp; generate your Stripe Form(s).', TRUE);

			include_once dirname(dirname(dirname(dirname(__FILE__)))).'/menu-pages/stripe-forms.inc.php';
		}
	}
}