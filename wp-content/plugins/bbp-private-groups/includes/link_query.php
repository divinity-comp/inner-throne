<?php

//When posting a new topic, or replying, users can click the link button.  This brings up a list of recent posts, pages, topics and replies using wp_link_query
//This filter takes the 'results' array from wp-link-query and filters it to exclude topics, replies or forums that the user is not allowed to see

add_filter( 'wp_link_query', 'rpg_link_query', 10, 2 );

Function rpg_link_query ($results, $query) {

	//bring in the $results array that wp_link_query creates and sort through
	foreach ( $results as $key => $result ) {
	$postcheck= $result['ID'] ;
	//look up post type 
	$pg_post_type = get_post_type( $postcheck ) ;
	//test to see if post if topic, reply or forum
	if ( ($pg_post_type == bbp_get_forum_post_type() ) || ($pg_post_type == bbp_get_topic_post_type() ) || ($pg_post_type ==  bbp_get_reply_post_type() ) ) {
		//Get the Forum ID based on Post Type (Reply, Topic, Forum)
		$pg_forum_id = private_groups_get_forum_id_from_post_id($postcheck, $pg_post_type);
	
		//test to see if not allowed this forum
		if (private_groups_can_user_view_post_id($pg_forum_id) == false || topic_permissions_check ($postcheck) == false) {
			//and if so unset the result
		unset ($results[$key]) ;
		}

	}

	}
return apply_filters( 'rpg_link_query', $results, $query );
}




