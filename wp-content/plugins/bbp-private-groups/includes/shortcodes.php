<?php

//new shortcodes first
// then versions of bbpress ones with filtering



//********************  NEW SHORTCODES   ***************
add_shortcode('list-pg-users', 'list_pg_users');  
add_shortcode('pg-single-forum', 'pg_single_forum');
add_shortcode('pg-single-topic', 'pg_display_topic');  
add_shortcode('pg-single-reply', 'pg_display_reply');  


function list_pg_users ($attr) {
global $rpg_groups ;
$users= get_users() ;
	if ( !empty( $attr['group'] ) )  {
	//we have a group name !
	$content=$attr['group'] ;
	foreach ( $rpg_groups as $group => $details ){
		if ($details == $content) {
		echo '<b>'.$details.'</b>';
		echo '<ul>' ;
				foreach ( $users as $user ) {
				$groupcheck=get_user_meta( $user->ID, 'private_group',true);
				if ($groupcheck == $group) echo '<li>'.esc_html( $user->display_name ).'</li>' ;
				}
		echo '</ul>' ;
		}
	}
	}
	else {
	// we don't have a group name, so show all !
		foreach ( $rpg_groups as $group => $details ){
		if ($details == '') $details = $group.' - no name' ;
		echo '<b>'.$details.'</b>';
		echo '<ul>' ;
			foreach ( $users as $user ) {
			$groupcheck=get_user_meta( $user->ID, 'private_group',true);
			if ($groupcheck == $group) echo '<li>'.esc_html( $user->display_name ).'</li>' ;
			}
		echo '</ul>' ;
		}
	}
}


//***********************  NEW PG VERSIONS OF EXISTING SHORTCODES   *****************


/**
	 * Display the contents of a specific forum ID in an output buffer
	 * and return to ensure that post/page contents are displayed first.
	 *
	 * @since bbPress (r3031)
	 *
	 * @param array $attr
	 * @param string $content
	 * @uses get_template_part()
	 * @uses bbp_single_forum_description()
	 * @return string
	 */
	function pg_single_forum( $attr, $content = '' ) {

		// Sanity check required info
		if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
			return $content;

		// Set passed attribute to $forum_id for clarity
		$forum_id = bbpress()->current_forum_id = $attr['id'];

		// Bail if ID passed is not a forum
		if ( !bbp_is_forum( $forum_id ) )
			return $content;

		// Start output buffer
		pg_start( 'bbp_single_forum' );
		
		//check if pg user cannot view forum
		if (!private_groups_can_user_view_post_id($forum_id)) {
			if (!empty($attr['message'])) {
			echo $attr['message'] ;
			}
		return pg_end() ;
		}
		

		// Check forum caps
		if ( bbp_user_can_view_forum( array( 'forum_id' => $forum_id ) ) ) {
			bbp_get_template_part( 'content',  'single-forum' );

		// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( $forum_id, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access'    );
		}

		// Return contents of output buffer
		return pg_end();
	}
	
/**
	 * Display the contents of a specific topic ID in an output buffer
	 * and return to ensure that post/page contents are displayed first.
	 *
	 * @since bbPress (r3031)
	 *
	 * @param array $attr
	 * @param string $content
	 * @uses get_template_part()
	 * @return string
	 */
function pg_display_topic( $attr, $content = '' ) {

		// Sanity check required info
		if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
			return $content;

		// Unset globals
		pg_unset_globals();

		// Set passed attribute to $forum_id for clarity
		$topic_id = bbpress()->current_topic_id = $attr['id'];
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		// Bail if ID passed is not a topic
		if ( !bbp_is_topic( $topic_id ) )
			return $content;

		// Reset the queries if not in theme compat
		if ( !bbp_is_theme_compat_active() ) {

			$bbp = bbpress();

			// Reset necessary forum_query attributes for topics loop to function
			$bbp->forum_query->query_vars['post_type'] = bbp_get_forum_post_type();
			$bbp->forum_query->in_the_loop             = true;
			$bbp->forum_query->post                    = get_post( $forum_id );

			// Reset necessary topic_query attributes for topics loop to function
			$bbp->topic_query->query_vars['post_type'] = bbp_get_topic_post_type();
			$bbp->topic_query->in_the_loop             = true;
			$bbp->topic_query->post                    = get_post( $topic_id );
		}

		// Start output buffer
		pg_start( 'bbp_single_topic' );
		
		//check if pg user cannot view post
		$post_id = $attr['id'] ;
		$post_type = get_post_type( $post_id ) ;
		$forum_id = private_groups_get_forum_id_from_post_id($post_id, $post_type );
		if (!private_groups_can_user_view_post_id($forum_id) || topic_permissions_check ($post_id) == false) {
			if (!empty($attr['message'])) {
			echo $attr['message'] ;
			}
		return pg_end() ;
		}

		// Check forum caps
		if ( bbp_user_can_view_forum( array( 'forum_id' => $forum_id ) ) ) {
			bbp_get_template_part( 'content', 'single-topic' );

		// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( $forum_id, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access'    );
		}

		// Return contents of output buffer
		return pg_end();
}




	/**
	 * Display the contents of a specific reply ID in an output buffer
	 * and return to ensure that post/page contents are displayed first.
	 *
	 * @since bbPress (r3031)
	 *
	 * @param array $attr
	 * @param string $content
	 * @uses get_template_part()
	 * @return string
	 */
function pg_display_reply( $attr, $content = '' ) {

		// Sanity check required info
		if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
			return $content;

		// Unset globals
		pg_unset_globals();

		// Set passed attribute to $reply_id for clarity
		$reply_id = bbpress()->current_reply_id = $attr['id'];
		$forum_id = bbp_get_reply_forum_id( $reply_id );

		// Bail if ID passed is not a reply
		if ( !bbp_is_reply( $reply_id ) )
			return $content;

		// Reset the queries if not in theme compat
		if ( !bbp_is_theme_compat_active() ) {

			$bbp = bbpress();

			// Reset necessary forum_query attributes for replys loop to function
			$bbp->forum_query->query_vars['post_type'] = bbp_get_forum_post_type();
			$bbp->forum_query->in_the_loop             = true;
			$bbp->forum_query->post                    = get_post( $forum_id );

			// Reset necessary reply_query attributes for replys loop to function
			$bbp->reply_query->query_vars['post_type'] = bbp_get_reply_post_type();
			$bbp->reply_query->in_the_loop             = true;
			$bbp->reply_query->post                    = get_post( $reply_id );
		}

		// Start output buffer
		pg_start( 'bbp_single_reply' );
		
		//check if pg user cannot view post
		$post_type = get_post_type( $reply_id ) ;
		$forum_id = private_groups_get_forum_id_from_post_id($reply_id, $post_type );
		if (!private_groups_can_user_view_post_id($forum_id) || topic_permissions_check ($reply_id) == false) {
			if (!empty($attr['message'])) {
			echo $attr['message'] ;
			}
		return pg_end() ;
		}

		// Check forum caps
		if ( bbp_user_can_view_forum( array( 'forum_id' => $forum_id ) ) ) {
			bbp_get_template_part( 'content',  'single-reply' );

		// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( $forum_id, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access'    );
		}

		// Return contents of output buffer
		return pg_end();
	}
	
//***********************  START END UNSET PG VERSIONS   *****************
	
	function pg_start( $query_name = '' ) {

		// Set query name
		bbp_set_query_name( $query_name );

		// Start output buffer
		ob_start();
	}
	
	function pg_end() {

		// Unset globals
		pg_unset_globals();

		// Reset the query name
		bbp_reset_query_name();

		// Return and flush the output buffer
		return ob_get_clean();
	}
	
	function pg_unset_globals() {
		$bbp = bbpress();

		// Unset global queries
		$bbp->forum_query  = new WP_Query();
		$bbp->topic_query  = new WP_Query();
		$bbp->reply_query  = new WP_Query();
		$bbp->search_query = new WP_Query();

		// Unset global ID's
		$bbp->current_view_id      = 0;
		$bbp->current_forum_id     = 0;
		$bbp->current_topic_id     = 0;
		$bbp->current_reply_id     = 0;
		$bbp->current_topic_tag_id = 0;

		// Reset the post data
		wp_reset_postdata();
	}