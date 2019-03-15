<?php
/**
 * Shortcode `[s2Eot /]` (inner processing routines).
 *
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_sc_eots_in'))
{
	/**
	 * Shortcode `[s2Eot /]` (inner processing routines).
	 *
	 * @package OptimizeMember\s2Eot
	 * @since 150713
	 */
	class c_ws_plugin__optimizemember_sc_eots_in
	{
		/**
		 * Handles the Shortcode for: `[opmEot /]`.
		 *
		 * @attaches-to ``add_shortcode('opmEot');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Value of the requested data.
		 */
		public static function sc_eot_details($attr = array(), $content = '', $shortcode = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__optimizemember_before_sc_eot_details', get_defined_vars());
			unset($__refs, $__v); // Allow variables to be modified by reference.

			c_ws_plugin__optimizemember_no_cache::no_cache_constants(true);

			$mode = ''; // Initialize shortcode mode and validate.
			if(!empty($attr['mode']) && in_array(strtolower($attr['mode']), array('fixed', 'next'), TRUE))
				$mode = strtolower($attr['mode']); // A specific mode; i.e., `fixed`, `next`.

			if(empty($attr['user_id']) || !(integer)$attr['user_id'])
				$attr['user_id'] = $user_id = get_current_user_id();
			else $user_id = (integer)$attr['user_id'];

			$subscr_gateway = get_user_option('optimizemember_subscr_gateway', $user_id);
			$subscr_id      = get_user_option('optimizemember_subscr_id', $user_id);
			$subscr_cid     = get_user_option('optimizemember_subscr_cid', $user_id);
			$auto_eot_time  = get_user_option('optimizemember_auto_eot_time', $user_id);

			$attr = shortcode_atts( // Attributes.
				array(
					'debug'                => 'no', // Off.
					'user_id'              => '0', // Current.
					'date_format'          => 'M jS, Y, g:i a T',
					'round_to'             => '', // Optional rounding.
					'offset'               => '0', // Optional time offset.
					'timezone'             => '', // Default timezone; i.e., GMT/UTC.
					'future_format'        => $mode ? '%%date%%' : '<strong class="optimizemember-sc-eot-label -future">'._x('Access Expires:', 's2member-front', 's2member').'</strong> <span class="optimizemember-sc-eot-date -future">%%date%%</span>',
					'past_format'          => $mode ? '%%date%%' : '<strong class="optimizemember-sc-eot-label -past">'._x('Access Expired:', 's2member-front', 's2member').'</strong> <span class="optimizemember-sc-eot-date -past">%%date%%</span>',
					'next_format'          => $mode ? '%%date%%' : '<strong class="optimizemember-sc-eot-label -next">'._x('Next Payment:', 's2member-front', 's2member').'</strong> <span class="optimizemember-sc-eot-date -next">%%date%%</span>',
					'empty_format'         => $mode ? (in_array($subscr_gateway, array('stripe', 'paypal', 'clickbank'), TRUE) ? _x('N/A', 's2member-front', 's2member') : _x('—', 's2member-front', 's2member')) : '',
				),
				c_ws_plugin__optimizemember_utils_strings::trim_qts_deep((array)$attr)
			);
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__optimizemember_before_sc_eot_details_after_shortcode_atts', get_defined_vars());
			unset($__refs, $__v); // Allow variables to be modified by reference.

			// Collect and cache the EOT for this user.

			$prefix    = 'opm_eot_'; // Transient prefix for this shortcode.
			$hash_vars = $user_id.$subscr_gateway.$subscr_id.$subscr_cid.$auto_eot_time;
			$transient = $prefix.md5('optimizemember_sc_eot_'.$mode.serialize($attr).$hash_vars);

			if(!is_array($eot = get_transient($transient)))
			{
				$eot = c_ws_plugin__optimizemember_utils_users::get_user_eot($user_id, true, $mode);
				set_transient($transient, $eot, DAY_IN_SECONDS / 2);
			}
			if($eot['time'] && $attr['round_to'])
				$eot['time'] = strtotime($attr['round_to'], $eot['time']);

			if($eot['time'] && (integer)$attr['offset'])
				$eot['time'] = $eot['time'] + (integer)$attr['offset'];

			// Initialize EOT details/output format.

			if($eot['type'] === 'fixed' && $eot['time'] && $eot['tense'] === 'past')
				$details = $attr['past_format'];

			else if($eot['type'] === 'fixed' && $eot['time'] && $eot['tense'] === 'future')
				$details = $attr['future_format'];

			else if($eot['type'] === 'next' && $eot['time'] && $eot['tense'] === 'future')
				$details = $attr['next_format'];

			else $details = $attr['empty_format'];

			// Initialize EOT details/output date format.

			$time = null; // Initialize the time calculation.
			if($eot['time']) // // Do we have a time to work with?
				{
					$time = new DateTime(date('Y-m-d H:i:s', $eot['time']));
					if($attr['timezone'] && strtoupper($attr['timezone']) !== 'UTC')
						$time->setTimezone(new DateTimeZone($attr['timezone']));
				}
			if($time && $attr['date_format'] === 'timestamp')
				$date = (string)$time->getTimestamp();

			else if($time && $attr['date_format'] === 'default')
				$date = $time->format(get_option('date_format'));

			else if($time && $attr['date_format'])
				$date = $time->format($attr['date_format']);

			else if($time) // Default date/time format.
				$date = $time->format('M jS, Y, g:i a T');

			else $date = ''; // Default date; i.e., nothing.

			$details = str_ireplace('%%date%%', esc_html($date), $details);

			// Check special considerations and the current mode.

			if($eot['type'] === 'fixed' && !$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['auto_eot_system_enabled'])
				{
					$details = $attr['empty_format']; // EOTs are disabled on this site.
					$eot['debug'] = 'OptimizeMember\'s Auto-EOT System is disabled on this site.';
				}
			else if($eot['type'] === 'fixed' && $mode === 'next')
				{
					$details = $attr['empty_format']; // Empty this.
					$eot['debug'] = 'No fixed EOT time was found for this user.';
				}
			else if($eot['type'] === 'next' && $mode === 'fixed')
				{
					$details = $attr['empty_format']; // Empty this.
					$eot['debug'] = 'No more payments needed from this user.';
				}
			// Wrapper and debug info...

			if($details) // Wrapper for CSS styling.
				$details = '<span class="ws-plugin--optimizemember-sc-eot">'.$details.'</span>';

			if(filter_var($attr['debug'], FILTER_VALIDATE_BOOLEAN))
				$details .= '<pre>'.esc_html($eot['debug'] ? $eot['debug'] : 'Unknown error.').'</pre>';

			// Return the details/output from this shortcode.

			return apply_filters('ws_plugin__optimizemember_sc_eot_details', $details, get_defined_vars());
		}
	}
}
