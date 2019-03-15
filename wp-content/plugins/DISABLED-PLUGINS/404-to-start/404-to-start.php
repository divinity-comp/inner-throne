<?php
/*
Plugin Name: 404 to Start
Plugin URI: http://1manfactory.com/4042start
Description: Send 404 page not found error directly to start page (or any other page/site) to overcome problems with search engines. With optional email alert.
Version: 1.5.9
Author: Jürgen Schulze
Author URI: http://1manfactory.com
License: GNU GPL
*/

/*  Copyright 2010-2013 Juergen Schulze, 1manfactory.com (email : 1manfactory@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


f042start_set_lang_file();
add_action('admin_init', 'f042start_register_settings' );
add_action('admin_menu', 'f042start_plugin_admin_menu');
add_action('template_redirect', 'f042start_output_header');
register_activation_hook(__FILE__, 'f042start_activate');
register_deactivation_hook(__FILE__, 'f042start_deactivate');
register_uninstall_hook(__FILE__, 'f042start_uninstall');

function f042start_register_settings() { // whitelist options
	register_setting( 'f042start_option-group', 'f042start_type' );
	register_setting( 'f042start_option-group', 'f042start_target', 'f042start_check_values');
	register_setting( 'f042start_option-group', 'f042start_emailalert' );
	register_setting( 'f042start_option-group', 'f042start_exclude' );
	register_setting( 'f042start_option-group', 'f042start_exclude2' );
	register_setting( 'f042start_option-group', 'f042start_exclude3' );
	register_setting( 'f042start_option-group', 'f042start_emailaddres' );
	
}


function f042start_set_lang_file() {
	# set the language file
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('f042start', $moFile);
	}
}

function f042start_deactivate() {
	// needed
	delete_option('f042start_type');
	delete_option('f042start_target');
	delete_option('f042start_emailalert');
	delete_option('f042start_exclude');
	delete_option('f042start_exclude2');
	delete_option('f042start_exclude3');
	delete_option('f042start_emailaddres');
	
}

function f042start_activate() {
	# setting default values
	add_option('f042start_type', '301');
	add_option('f042start_target', home_url());
	add_option('f042start_emailalert', '');
	add_option('f042start_exclude', '');
	add_option('f042start_exclude2', '');
	add_option('f042start_exclude3', '');
	add_option('f042start_emailaddres', '');
}


function f042start_uninstall() {
	# delete all data stored
	delete_option('f042start_type');
	delete_option('f042start_target');
	delete_option('f042start_emailalert');
	delete_option('f042start_exclude');
	delete_option('f042start_exclude2');
	delete_option('f042start_exclude3');
	delete_option('f042start_emailaddres');
}

function f042start_plugin_admin_menu() {
	add_options_page(__('404 to Start Settings', 'f042start'), "404 to Start", 'manage_options', basename(__FILE__), 'f042start_plugin_options');
}


function f042start_plugin_options(){

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	if (get_option("f042start_target")=="") {
		// setting target value to home url if empty (maybe because of plugin update)
		add_option('f042start_target', home_url());
	}
	
	print '<div class="wrap">';
	
	print '<h2>'.__('404 to Start Settings', 'f042start').'</h2>';

	print '<form method="post" action="options.php" id="f042start_form">';
	wp_nonce_field('update-options', '_wpnonce');
	settings_fields( 'f042start_option-group');

	print'
		<input type="hidden" name="page_options" value="f042start_type, f042start_target, f042start_emailalert, f042start_emailaddres, f042start_exclude, f042start_exclude2, f042start_exclude3" />
		<table class="form-table">
		<tr valign="top">
		<th scope="row">'.__('404 Redirect', 'f042start').'</th>
		</tr>
		<tr>
		<td>
		<input type="radio" name="f042start_type" value="off" '.f042start_checked("f042start_type", "off").'/> '.__('off', 'f042start').' <br />
		<input type="radio" name="f042start_type" value="301" '.f042start_checked("f042start_type", "301").'/> '.__('301 - Moved permanently', 'f042start').'<br />
		<input type="radio" name="f042start_type" value="302" '.f042start_checked("f042start_type", "302").'/> '.__('302 - Found/ Moved temporarily (not recommended)', 'f042start').'<br />
		<input type="checkbox" name="f042start_emailalert" value="1" '.f042start_checked("f042start_emailalert", "1").'/> '.__('Email alert to: ', 'f042start').'<input type="text" name="f042start_emailaddres" value="'.get_option("f042start_emailaddres").'" size="50">
		<br />
		&nbsp;&nbsp;&nbsp;<input type="checkbox" name="f042start_exclude" value="1" '.f042start_checked("f042start_exclude", "1").'/> '.__('Exclude logged in users from triggering email alert', 'f042start').'
		<br />
		&nbsp;&nbsp;&nbsp;<input type="checkbox" name="f042start_exclude2" value="1" '.f042start_checked("f042start_exclude2", "1").'/> '.__('Exclude search engine agents from triggering email alert', 'f042start').'		
		<br />
		&nbsp;&nbsp;&nbsp;<input type="checkbox" name="f042start_exclude3" value="1" '.f042start_checked("f042start_exclude3", "1").'/> '.__('Exclude hacking attempts from triggering email alert. (Experimental, leave this unchecked if you feel unsure.)', 'f042start').'		
		<br />
		</td>
		</tr>
		<tr>
		<td>
		'.__('Target Url: (Start with http://)', 'f042start').' <input type="text" name="f042start_target" value="'.get_option("f042start_target").'" size="100">
		</td>
		</tr>
		</table>
		<input type="hidden" name="action" value="update" />

	';


	print '<p class="submit"><input type="submit" name="submit" value="'.__('Save Changes', 'f042start').'" /></p>';

	print '</form>';
	print '</div>';

	print '<br /><br /><br />';
	require_once('whatsup.php');
	
}

// sanitize function
function f042start_check_values($input) {
	# check target url
	if (!f042start_is_valid_url($input)) {
		add_settings_error('f042start_option-group', 'settings_updated', __('This is not a valid Url', 'f042start'), $type = 'error');
	}
	return $input;
}


function f042start_checked($checkOption, $checkValue) {
	return get_option($checkOption)==$checkValue ? " checked" : "";
}


// Trap 404 errors and redirect them to start page
// 301=permanently moved
// 302=temporary
function f042start_output_header() {

	# setting default target to prevent errors
	if (get_option('f042start_target')=="") {
		$target=home_url();
	} else {
		$target=get_option("f042start_target");
	}
	
	if ( is_404() && get_option("f042start_emailalert") ) {
	
		if  ( 
				f042start_is_infinitescroll() ||
				(get_option("f042start_exclude") && is_user_logged_in())
				|| (get_option("f042start_exclude2") && f042start_is_crawlers())
				|| (get_option("f042start_exclude3") && f042start_is_hacker(f042start_curPageURL()))
			) {
			// no mail
		} else {
			// send email alert
			$message=get_bloginfo('name')."\n";
			$message.=get_bloginfo('wpurl')."\n";
			$message.="False URL: ".f042start_curPageURL()."\n";
			$message.="Referer URL: ".$_SERVER['HTTP_REFERER']."\n";
			$message.="User agent: ".$_SERVER['HTTP_USER_AGENT']."\n";
			$message.="Remote Host: ".$_SERVER['REMOTE_HOST']."\n";
			$message.="Remote Addres: ".$_SERVER['REMOTE_ADDR']."\n";
			$returnvalue=wp_mail( get_option("f042start_emailaddres"), __('404 alert from ', 'f042start').get_bloginfo('name'), $message, "From: ".get_bloginfo('admin_email') );
		}	
	
	}
	
	if ( !is_404() || get_option("f042start_type")=="off" ) return;
	wp_redirect( $target, get_option("f042start_type") );
}


# http://www.roscripts.com/snippets/show/156
function f042start_is_valid_url ( $url )
{
		$url = @parse_url($url);

		if ( ! $url) {
			return false;
		}

		$url = array_map('trim', $url);
		$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
		$path = (isset($url['path'])) ? $url['path'] : '';

		if ($path == '')
		{
			$path = '/';
		}

		$path .= ( isset ( $url['query'] ) ) ? "?$url[query]" : '';

		if ( isset ( $url['host'] ) AND $url['host'] != gethostbyname ( $url['host'] ) )
		{
			if ( PHP_VERSION >= 5 )
			{
				$headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
			}
			else
			{
				$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

				if ( ! $fp )
				{
					return false;
				}
				fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
				$headers = fread ( $fp, 128 );
				fclose ( $fp );
			}
			$headers = ( is_array ( $headers ) ) ? implode ( "\n", $headers ) : $headers;
			return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
		}
		return false;
}


function f042start_curPageURL() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function f042start_is_crawlers() {
	$sites = 'MnoGoSearch|facebookexternalhit|Squider|NING|genieo|butterfly|JS-Kit|InAGist|BUbiNG|crawler|Java|Google|Yahoo|Ask|bot|spider|Twikle|flipboard|longurl|crowsnest|peerindex|UnwindFetchor'; // Add the rest of the search-engines 
	return (preg_match("/$sites/i", $_SERVER['HTTP_USER_AGENT']) > 0) ? true : false;  	
}

function f042start_is_hacker($checkurl) {
	$hackersurl = 'wp-content\/plugins\/'; // don't check this
	return (preg_match("/$hackersurl/i", $checkurl) > 0) ? true : false;  	
}

function f042start_is_infinitescroll() {
	# page/4
	if (preg_match("/page\/\d+/", f042start_curPageURL())) {
		# error_log("f042start: ".f042start_curPageURL());
		return true;
	}
	return false;
}
?>