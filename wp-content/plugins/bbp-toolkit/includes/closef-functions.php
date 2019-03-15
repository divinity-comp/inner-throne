<?php

// Fix issue where non-admin or -moderator users get a You cannot create new topics message 
// when using [bbp-topic-form] after a search
function bbptoolkit_access_topic_form( $retval ) {
	if ( bbp_is_search_results() ) {
		$retval = bbp_current_user_can_publish_topics();
	}
	return $retval;
}
add_filter( 'bbp_current_user_can_access_create_topic_form', 'bbptoolkit_access_topic_form' );

// Remove closed forums from the dropdown list 
function bbptoolkit_remove_closed_from_dropdown( $r ) {
	$closed_forum_ids = get_option('bbptoolkit-closedforums', false);
	if ($closed_forum_ids) {
		$a = array_filter(explode("*",$closed_forum_ids));
		$closed_forum_rolesok = get_option('bbptoolkit-closedforums-rolesok', false);
		global $current_user;
		$userallowed=false;
		foreach($current_user->roles as $role) {
			if (strpos($closed_forum_rolesok, '*'.$role.'*') !== FALSE) {
				// User is allowed thanks to his role
				$userallowed=true;
			}
		}
		if(!$userallowed) {
			$r["exclude"] = $a;
		}
	}
	return $r; 
}; 
add_filter( "bbp_before_get_dropdown_parse_args", 'bbptoolkit_remove_closed_from_dropdown', 10, 1);

// Close forums if ticked
function bbptoolkit_lock_forums(){
	global $post;
	if($post->post_type!='forum') return;

	// Forum is closed ?
	$closed_forum_ids = get_option('bbptoolkit-closedforums', false);
	// Nothing ticked, just continue working
	if(!$closed_forum_ids) return;
	// Check if forum is in the list of closed ones
	if (strpos($closed_forum_ids, '*'.strval($post->ID).'*') !== FALSE) {
		// Check if user has right to post anyway
		$closed_forum_rolesok = get_option('bbptoolkit-closedforums-rolesok', false);
		global $current_user;
		$userallowed=false;
		foreach($current_user->roles as $role) {
			if (strpos($closed_forum_rolesok, '*'.$role.'*') !== FALSE) {
				// User is allowed thanks to his role
				$userallowed=true;
			}
		}
		if(!$userallowed) {
			// Close forum
			$closed_forum_text = get_option('bbptoolkit-closedforums-text', false);
			if ($closed_forum_text) echo '<fieldset class="bbp-form">' . $closed_forum_text . '</fieldset>';
			ob_start();
			add_action( 'bbp_theme_after_topic_form', 'bbptoolkit_stop_lock');
			return;
		}
	} 
}
add_action( 'bbp_theme_before_topic_form', 'bbptoolkit_lock_forums');

function bbptoolkit_stop_lock(){
	ob_get_clean();
	remove_action('bbp_theme_after_topic_form', 'bbptoolkit_stop_lock');
}

?>