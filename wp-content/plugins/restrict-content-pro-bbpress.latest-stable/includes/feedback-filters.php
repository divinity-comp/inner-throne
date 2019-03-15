<?php

function rcp_feedback_messages( $translated_text, $text, $domain ) {

	switch( $translated_text ) {
		case 'Oh bother! No topics were found here!':
			$translated_text = __( 'You must be a premium user to view this content.', 'rcp-bbpress' );
         break;
	}
	
	return $translated_text;
}


function rcp_apply_feedback_messages() {
	global $user_ID;

	if( rcp_forum_is_premium() && ( ! rcp_is_active( $user_ID ) && ! current_user_can( 'moderate' ) ) ) {
		add_filter( 'gettext', 'rcp_feedback_messages', 20, 3 );
	}
}
add_action( 'template_redirect', 'rcp_apply_feedback_messages' );