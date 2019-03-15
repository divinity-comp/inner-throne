<?php
// Only return one entry for revision log otherwise it gets cluttered
function bbptoolkit_only_last_revision_log( $r='' ) {
	$arr = array();
	$arr = $r;
	$bbptoolkit_only_last_revision_log = get_option('bbptoolkit-only-last-revision-log', false);
	if ($bbptoolkit_only_last_revision_log) {
		$arr = array( end( $r ));
		reset( $r );
	}
	return( $arr );
}
add_filter('bbp_get_reply_revisions', 'bbptoolkit_only_last_revision_log', 20, 1);
add_filter('bbp_get_topic_revisions', 'bbptoolkit_only_last_revision_log', 20, 1);

function bbptoolkit_change_translate_text( $translated_text ) {
	if ( $translated_text == _x('Oh bother! No topics were found here!', 'Exact bbPress translation in your language!', 'bbp-toolkit')) {
		$bbptoolkit_tick_ohbother = get_option('bbptoolkit-tick-ohbother', false);
		$NewOhBother = get_option('bbptoolkit-new-ohbother', false);
		if (($bbptoolkit_tick_ohbother) and ($NewOhBother)) {
			$translated_text = $NewOhBother;
		}
	}
	if ( $translated_text == _x('Your account has the ability to post unrestricted HTML content.', 'Exact bbPress translation in your language!', 'bbp-toolkit')) {
		$bbptoolkit_tick_unreshtml = get_option('bbptoolkit-tick-unreshtml', false);
		$NewUnresHTML = get_option('bbptoolkit-new-unres-html', false);
		if (($bbptoolkit_tick_unreshtml) and ($NewUnresHTML)) {
			$translated_text = $NewUnresHTML;
		}
	}
	return $translated_text;
}
add_filter( 'gettext', 'bbptoolkit_change_translate_text', 20 );

// Change freshness time by removing the last part, so '1 month, 3 days ago' becomes '1 month ago'
function bbptoolkit_shorten_freshness_time($return) {
	$bbptoolkit_short_fresh = get_option('bbptoolkit-short-fresh', false);
	if ($bbptoolkit_short_fresh) {
		$return = preg_replace( '/, .*[^ago]/', ' ', $return );
	}
	return $return;
}
add_filter( 'bbp_get_time_since', 'bbptoolkit_shorten_freshness_time' );
add_filter( 'bp_core_time_since', 'bbptoolkit_shorten_freshness_time');


// Remove Oh Bother and Unrestricted HTML messages completely
function bbptoolkit_queue_bbp_script() {
	if( function_exists( 'is_bbpress' ) ) {
		if( is_bbpress() ) {
			$bbptoolkit_css_version = get_option('bbptoolkit-css-version', false);
			wp_enqueue_style('bbptoolkit', BBPT_URL_PATH.'css/bbptoolkit.css', false, $bbptoolkit_css_version);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'bbptoolkit_queue_bbp_script' );


// Remove bbpress CSS from all pages expect where forums are
function bbptoolkit_unqueue_bbp_scripts() {
	if( ! is_bbpress() ) {
		$bbptoolkit_rem_defstyle = get_option('bbptoolkit-rem-defstyle', false);
		if ($bbptoolkit_rem_defstyle) {
			wp_dequeue_style('bbp-default');
			wp_dequeue_style('bbp-default-rtl');
		}
	}
}
add_action( 'bbp_enqueue_scripts', 'bbptoolkit_unqueue_bbp_scripts', 15 );


// Remove or adapt info This forum contains 37 topics and 130 replies, and was last updated by  John Doe 2 days, 4 hours ago
function bbptoolkit_remove_foruminfo( $retstr ) {
	$bbptoolkit_rem_forum_info = get_option('bbptoolkit-rem-forum-info', false);
	if ($bbptoolkit_rem_forum_info) {
		$bbptoolkit_tick_new_forum_info = get_option('bbptoolkit-tick-new-forum-info', false);
		if ($bbptoolkit_tick_new_forum_info) {
			$bbptoolkit_new_forum_info = get_option('bbptoolkit-new-forum-info', false);
			return '<div class="bbp-template-notice info"><p class="bbp-forum-description">' . $bbptoolkit_new_forum_info . '</p></div>';
		} else {
			__return_false();
		}
	} else {
		return $retstr;
	}
	
}
add_filter( 'bbp_get_single_forum_description', 'bbptoolkit_remove_foruminfo', 10 );

// Remove or adapt info This topic contains 8 replies, has 3 voices, and was last updated by  Jane Doe 1 day, 7 hours ago.
function bbptoolkit_remove_topicinfo( $retstr ) {
	$bbptoolkit_rem_topic_info = get_option('bbptoolkit-rem-topic-info', false);
	if ($bbptoolkit_rem_topic_info) {
		$bbptoolkit_tick_new_topic_info = get_option('bbptoolkit-tick-new-topic-info', false);
		if ($bbptoolkit_tick_new_topic_info) {
			$bbptoolkit_new_topic_info = get_option('bbptoolkit-new-topic-info', false);
			return '<div class="bbp-template-notice info"><p class="bbp-topic-description">' . $bbptoolkit_new_topic_info . '</p></div>';
		} else {
			__return_false();
		}
	} else {
		return $retstr;
	}
	
}
add_filter( 'bbp_get_single_topic_description', 'bbptoolkit_remove_topicinfo', 10 );

// Remove the word 'Private' in front of a forum
function bbptoolkit_remove_private_prefix($title) {
	$bbptoolkit_rem_priv_prefix = get_option('bbptoolkit-rem-priv-prefix', false);
	if ($bbptoolkit_rem_priv_prefix) {
		return '%s';
	} else {
		return $title;
	}
}
add_filter('private_title_format', 'bbptoolkit_remove_private_prefix');
?>