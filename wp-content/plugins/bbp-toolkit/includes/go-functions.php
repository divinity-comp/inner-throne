<?php

// Add icon in front of forum title
add_post_type_support('forum', array('thumbnail'));
function bbptoolkit_forum_icons() {
	$bbptoolkit_activate_forum_icon = get_option('bbptoolkit-activate-forum-icon', false);
	if ($bbptoolkit_activate_forum_icon) {
		$bbptoolkit_activate_forum_icon_width = get_option('bbptoolkit-activate-forum-icon-width', '30');
		if ( 'forum' == get_post_type() ) {
			global $post;
		    if ( has_post_thumbnail($post->ID) ) {
		    	echo get_the_post_thumbnail($post->ID,array($bbptoolkit_activate_forum_icon_width,$bbptoolkit_activate_forum_icon_width),array('class' => 'forum-icon'));
		    }
		 }
	}
}
add_action('bbp_theme_before_forum_title','bbptoolkit_forum_icons');

// Activate the basic TinyMCE editor
function bbptoolkit_activate_tinymce_editor( $args = array() ) {
	$bbptoolkit_activate_tinymce = get_option('bbptoolkit-activate-tinymce', false);
	if ($bbptoolkit_activate_tinymce) {
		$args['tinymce'] = true;
		$args['quicktags'] = false;
	}
	return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbptoolkit_activate_tinymce_editor' );

// Fix issue with HTML codes
function bbptoolkit_tinymce_paste_plain_text( $plugins = array() ) {
	$bbptoolkit_activate_tinymce = get_option('bbptoolkit-activate-tinymce', false);
	if ($bbptoolkit_activate_tinymce) {
		$plugins[] = 'paste';
	}
	return $plugins;
}
add_filter( 'bbp_get_tiny_mce_plugins', 'bbptoolkit_tinymce_paste_plain_text' );

// Change admin links separator
function bbptoolkit_change_admin_links_sep($args) {
	$bbptoolkit_adm_links = get_option('bbptoolkit-adm-links', false);
	if ($bbptoolkit_adm_links) {
		$bbptoolkit_adm_links_sep = get_option('bbptoolkit-adm-links-sep', false);
		if ($bbptoolkit_adm_links_sep) {
			$args['sep'] = $bbptoolkit_adm_links_sep;
		}
	}
	return $args;
}
add_filter('bbp_after_get_topic_admin_links_parse_args', 'bbptoolkit_change_admin_links_sep' );
add_filter('bbp_after_get_reply_admin_links_parse_args', 'bbptoolkit_change_admin_links_sep' );

// Change separator of subforums and remove topic and reply count
function bbptoolkit_bbpress_list_forums() {
	$args = array();
	$bbptoolkit_subforum_separator = get_option('bbptoolkit-subforum-separator', false);
	if ($bbptoolkit_subforum_separator) {
		$args['separator'] = $bbptoolkit_subforum_separator;
	}
	$bbptoolkit_subforum_hide_counters = get_option('bbptoolkit-subforum-hide-counters', false);
	if ($bbptoolkit_subforum_hide_counters) {
		$args['show_topic_count'] = false;
		$args['show_reply_count'] = false;
	}
	return $args;
}
add_filter('bbp_after_list_forums_parse_args', 'bbptoolkit_bbpress_list_forums' );

// Changes the logic on replying to topics to check the box for notify of replies, rather than have it blank
function bbptoolkit_auto_check_subscribe( $checked, $topic_subscribed  ) {
	$bbptoolkit_tick_notify = get_option('bbptoolkit-tick-notify', false);
	if ($bbptoolkit_tick_notify) {
		// option exists, so always tick the 'Notify me of follow-up replies via email' 
		return checked( true, true, false );
        } else {
        	// option does not exist so keep the value as before (by default this is not ticked)
		return checked( $topic_subscribed, true, false );
        }
}
add_filter( 'bbp_get_form_topic_subscribed', 'bbptoolkit_auto_check_subscribe', 10, 2 );

// Breadcrumb remove or adapt
$bbptoolkit_rem_breadcrumb = get_option('bbptoolkit-rem-breadcrumb', false);
if ($bbptoolkit_rem_breadcrumb) {
	remove_filter('bbp_before_get_breadcrumb_parse_args', 'bbptoolkit_breadcrumb_changes');
	add_filter( 'bbp_no_breadcrumb', '__return_true' );
} else {
	remove_filter( 'bbp_no_breadcrumb', '__return_true' );
	add_filter('bbp_before_get_breadcrumb_parse_args', 'bbptoolkit_breadcrumb_changes');
}

// Breadcrumb changes
function bbptoolkit_breadcrumb_changes($args) {
	$bbptoolkit_breadcrumb_before = get_option('bbptoolkit-breadcrumb-before', false);
	if ($bbptoolkit_breadcrumb_before) {
		$Newbreadcrumbbefore = get_option('bbptoolkit-new-breadcrumbbefore', false);
		if ($Newbreadcrumbbefore) {
			$args['before'] = '<div class="bbp-breadcrumb"><p><span class="bbp-breadcrumb-text">' . $Newbreadcrumbbefore . ' </span>';
		}
	}
	$bbptoolkit_breadcrumb_sep = get_option('bbptoolkit-breadcrumb-sep', false);
	if ($bbptoolkit_breadcrumb_sep) {
		$newbreadcrumbsep = get_option('bbptoolkit-new-breadcrumbsep', false);
		if ($newbreadcrumbsep) {
			$args['sep'] = ' ' . $newbreadcrumbsep . ' ';
		}
	}
	$bbptoolkit_breadcrumb_home = get_option('bbptoolkit-breadcrumb-home', false);
	if ($bbptoolkit_breadcrumb_home) {
		$newbreadcrumbhome = get_option('bbptoolkit-new-breadcrumbhome', false);
		if ($newbreadcrumbhome) {
			$args['home_text'] = $newbreadcrumbhome;
		} else {
			$args['include_home'] = false;
		}
	}
	$bbptoolkit_breadcrumb_root = get_option('bbptoolkit-breadcrumb-root', false);
	if ($bbptoolkit_breadcrumb_root) {
		$newbreadcrumbroot = get_option('bbptoolkit-new-breadcrumbroot', false);
		if ($newbreadcrumbroot) {
			$args['root_text'] = $newbreadcrumbroot;
		} else {
			$args['include_root'] = false;
		}
	}
	$bbptoolkit_breadcrumb_current = get_option('bbptoolkit-breadcrumb-current', false);
	if ($bbptoolkit_breadcrumb_current) {
		$args['include_current'] = false;
	}

	return $args;
}

// Desc not asc sort for replies in a topic
function bbptoolkit_has_replies( $args ) { 
	if( function_exists( 'is_bbpress' ) ) {
		if( is_bbpress() ) {
			$bbptoolkit_reply_inverse = get_option('bbptoolkit-reply-inverse', false);
			if ($bbptoolkit_reply_inverse) {
				if ( bbp_is_single_topic() && !bbp_is_single_user() ) { 
					$args['orderby'] .= 'post_modified';
					$args['order'] .= 'DESC';
				}
			}
		}
	}
	return $args; 
} 
add_filter('bbp_before_has_replies_parse_args', 'bbptoolkit_has_replies', 10, 1);

// Desc sort but keep lead topic as first one
function bbptoolkit_show_lead_topic( $show_lead ) {
	$bbptoolkit_reply_inverse = get_option('bbptoolkit-reply-inverse', false);
	if ($bbptoolkit_reply_inverse) {
		$bbptoolkit_reply_inverse_keep_lead = get_option('bbptoolkit-reply-inverse-keep-lead', false);
		if ($bbptoolkit_reply_inverse_keep_lead) {
			$show_lead[] = 'true';
		}
	}
	return $show_lead;
}
add_filter('bbp_show_lead_topic', 'bbptoolkit_show_lead_topic' );

// Title limit
function bbptoolkit_title_limit_maxchar( $default ) {
	$bbptoolkit_title_maxchar = get_option('bbptoolkit-title-maxchar', false);
	if ($bbptoolkit_title_maxchar) {
		$bbptoolkit_title_maxchar_text = get_option('bbptoolkit-title-maxchar-text', false);
		if ($bbptoolkit_title_maxchar_text) {
			$default = $bbptoolkit_title_maxchar_text;
		}
	}
	return $default ;
}
add_filter('bbp_get_title_max_length','bbptoolkit_title_limit_maxchar') ;

// Secure Profile
function bbptoolkit_secure_profile() {
	if ( !is_user_logged_in() ) {
		$bbptoolkit_sec_profile = get_option('bbptoolkit-sec-profile', false);
		if ($bbptoolkit_sec_profile) {
			$bbptoolkit_sec_profile_path = get_option('bbptoolkit-sec-profile-path', false);
			if (!$bbptoolkit_sec_profile_path) {
				$bbptoolkit_sec_profile_path = $_SERVER['HTTP_REFERER'];
			}
			get_header();
			echo '<br><br><h3 class="bbpt-sec-profile">'; _e('Sorry, You must be logged in to view this area.', 'bbp-toolkit'); echo '<br><br>';
			header( "refresh:5;url=".$bbptoolkit_sec_profile_path ); 
			_e('You will be redirected in about 5 secs. If not, click', 'bbp-toolkit'); echo('<a href="'.$bbptoolkit_sec_profile_path.'">'); _e('here', 'bbp-toolkit'); echo'</a>.<h3><br><br><br>';
			exit;
		}
	}
}
add_action( 'bbp_template_before_user_profile', 'bbptoolkit_secure_profile', 10, 0 );
add_action( 'bbp_template_before_user_details', 'bbptoolkit_secure_profile', 10, 0 );

?>