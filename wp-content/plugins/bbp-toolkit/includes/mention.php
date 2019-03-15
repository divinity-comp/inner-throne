<?php

// Add actions
add_action( 'bbp_new_topic_post_extras', 'bbptoolkit_send_mention' );
add_action( 'bbp_new_reply_post_extras', 'bbptoolkit_send_mention' );
add_action( 'bbp_edit_topic_post_extras', 'bbptoolkit_send_mention' );
add_action( 'bbp_edit_reply_post_extras', 'bbptoolkit_send_mention' );

// Check !function_exists('buddypress') to make sure buddypress is not running
function bbptoolkit_send_mention( $post_id ) {
	$bbptoolkit_mentions = get_option('bbptoolkit-mentions', false);
	if ($bbptoolkit_mentions) {
		$bbptoolkit_mentions_subject = get_option('bbptoolkit-mentions-subject', 'You have been mentioned.');

		$data  			= get_post($post_id);
		$content 		= $data->post_content;
		$type 			= $data->post_type;
		if ($type == 'reply') {
			$cpt = 'reply';
			$title = get_the_title($data->post_parent);
			$link = bbp_get_reply_url( $post_id );
			$forum_title = bbp_get_topic_forum_title(bbp_get_reply_topic_id($post_id));
		} else {
			$cpt = 'topic';
			$title = $data->post_title;
			$link = get_the_permalink($post_id);
			$forum_title = bbp_get_topic_forum_title($post_id);
		}

		$mentions = bbp_find_mentions($content);
		if( $mentions ) {
			// we have mentions
			foreach( $mentions as $username ) {
				$user_id = get_user_by( 'slug', $username )->ID;
				if ( get_userdata( $user_id ) ) {
					// user exists so start notify
					$message = $title . "\r\n" . $link . "\r\n\r\n" . $content;
					$subject = '[Forum: ' . $forum_title . '] ' . $bbptoolkit_mentions_subject;
					$email = get_userdata( $user_id )->user_email;
					wp_mail( $email, $subject, $message );
				}
			}
		}
	}
}

?>