<?php
// Blocked users should NOT get an email to subscribed topics
// Waiting for https://bbpress.trac.wordpress.org/ticket/3046
function bbptoolkit_fltr_get_forum_subscribers( $user_ids ) {
	if (!empty( $user_ids ) ) {
		$new_user_ids = array();
		foreach ($user_ids as $uid) {
			if (bbp_get_user_role($uid) != 'bbp_blocked') {
				$new_user_ids[] = $uid;
			}
		}
		return $new_user_ids;
	} else {
		return $user_ids;
	} 
}; 
// add the filter 
add_filter( 'bbp_forum_subscription_user_ids', 'bbptoolkit_fltr_get_forum_subscribers', 10, 1 ); 

// All forums, topics and replies should have closed pingbacks (and comments, later)
//
// In the DB:
// UPDATE wp_posts SET ping_status='closed' 
//	WHERE post_status = 'publish' AND post_type = 'post';
//
// FORUMS:
// bbp_update_forum_id updates a posts forum meta ID.
//   it has a filter called bbp_update_forum_id
//
// TOPICS:
// bbp_update_topic handles all the extra meta stuff from posting a new topic.
//   it launches  bbp_update_topic_topic_id that has a filter bbp_update_topic_topic_id
//
// REPLIES
// bbp_update_reply handles all the extra meta stuff from posting a new reply.
//   it launches  bbp_update_reply_topic_id that has a filter bbp_update_reply_topic_id

function bbptoolkit_fltr_update_forum_id( $forum_id, $post_id ) { 
	$my_post = array(
		'ID'              => $post_id,
		'ping_status'     => 'closed',
		'comment_status'  => 'closed',
	);
//	wp_update_post( $my_post );
	return $forum_id; 
}; 
//add_filter( 'bbp_update_forum_id', 'bbptoolkit_fltr_update_forum_id', 10, 2 );


