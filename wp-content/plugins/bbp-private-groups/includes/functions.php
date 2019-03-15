<?php

add_action('bbp_template_redirect', 'private_group_enforce_permissions', 1);
add_filter('protected_title_format', 'pg_remove_protected_title');
add_filter('private_title_format', 'pg_remove_private_title');
//handle favorites
add_filter( 'bbp_get_user_favorites', 'rpg_get_user_favorites' );

//links and topic counts
add_filter('bbp_get_forum_freshness_link', 'pg_get_forum_freshness_link', 10, 2 );
add_filter ('bbp_get_single_forum_description' , 'rpg_get_single_forum_description' , 10 , 2) ;
add_filter ('bbp_get_forum_topic_count' , 'rpg_get_forum_topic_count', 10 , 2 ) ;
add_filter ('bbp_get_forum_reply_count' , 'rpg_get_forum_reply_count', 10 , 2 ) ;
add_filter ('bbp_get_forum_post_count' , 'rpg_get_forum_post_count', 10 , 2 ) ;

add_filter ('bbp_get_forum_last_active_id' , 'rpg_get_forum_last_active_id', 10 , 2 ) ;
add_filter( 'bbp_user_can_view_forum', 'rpg_user_can_view_forum', 10, 3 );

//filter back end access to topics and replies to ensure that moderators can only see their forums they have access to
add_filter( 'bbp_request', 'rpg_filter_bbp_request', 10, 1 );
    


//This code hides the author link if needed - we only potentailly need to get this link if forum visibility is active, otherwise it is not needed.
//and user can't see the post - but then function will test that !
global $rpg_settingsf ;
if (!empty ($rpg_settingsf['set_forum_visibility']) ) {
remove_filter( 'bbp_get_author_link',          'bbp_suppress_private_author_link', 10, 2 );
		remove_filter( 'bbp_get_topic_author_link',    'bbp_suppress_private_author_link', 10, 2 );
		remove_filter( 'bbp_get_reply_author_link',    'bbp_suppress_private_author_link', 10, 2 );
		add_filter( 'bbp_get_author_link', 'pg_check_profile', 10, 2 ) ;
		add_filter( 'bbp_get_topic_author_link', 'pg_check_profile', 10, 2 ) ;
		add_filter( 'bbp_get_reply_author_link', 'pg_check_profile', 10, 2 ) ;
}

//add assign role to register, 
add_action ('bbp_user_register', 'pg_role_group') ;

//and to wp-login
global $rpg_roles ;
if (!empty ($rpg_roles['login'])) {
	add_action('wp_login', 'pg_assign_role_on_login', 10, 2);
}
//and on every visit
if (!empty ($rpg_roles['change_on_visit'] )) {
	add_action('init', 'pg_assign_role_on_init');
}

//  ADD FORUM ID column to admin 
add_action("manage_edit-forum_columns", 'rpg_ID_column_add');
add_filter("manage_forum_posts_custom_column", 'rpg_ID_column_value', 10, 3);

//add groups to forum lists
add_action("manage_edit-forum_columns", 'rpg_groups_column_add') ;
add_filter("manage_forum_posts_custom_column", 'rpg_groups_column_value', 10, 3);


// add filters for bbp-style-pack if stylepack is running
	add_filter('bsp_get_freshness_display_title', 'pg_get_forum_freshness_title' );
    add_filter('bsp_display_topic_index_query', 'pg_display_topic_index_query_filter');
	add_filter('bsp_display_forum_query', 'pg_display_forum_query_filter');
	add_filter('bsp_activity_widget', 'pg_latest_activity_forum_query_filter') ;
	
// add filters for bbp-shortcodes plugin shortcodes if plugin is running
    add_filter('asc_display_topic_index_query', 'pg_display_topic_index_query_filter');
	add_filter('asc_display_forum_query', 'pg_display_forum_query_filter');

	
	

/*
 * Check if the current user has rights to view the given Post ID
 * Some of this core code is from the work of Aleksandar Adamovic in his Tehnik BBPress Permissions - thanks !
 * 
 */

function private_groups_check_can_user_view_post() {
//uses $post_id and $post_type to get the forum ($forum_id) that the post belongs to
    global $wp_query;

    // Get Forum Id for the current post    
    $post_id = $wp_query->post->ID;
    $post_type = $wp_query->get('post_type');
	
	if (bbp_is_topic_super_sticky($post_id)) return true;
	
	
    $forum_id = private_groups_get_forum_id_from_post_id($post_id, $post_type);
	//then call the function that checks if the user can view this forum, and hence this post
	if (private_groups_can_user_view_post_id($forum_id) && topic_permissions_check ($post_id)) {
			return true ;
		}
}



/**
 * Use the given query to determine which forums the user has access to. 
 * 
 * returns: an array of post IDs which user has access to.
 */
function private_groups_get_permitted_post_ids($post_query) {
    
    //Init the Array which will hold our list of allowed posts
    $allowed_posts = array();
    

    //Loop through all the posts
    while ($post_query->have_posts()) :
        $post_query->the_post();
		//Get the Post ID and post type
        $post_id = $post_query->post->ID;
		$post_type = $post_query->post->post_type;
        //Get the Forum ID based on Post Type (Reply, Topic, Forum)
        $forum_id = private_groups_get_forum_id_from_post_id($post_id, $post_type);
		//Check if User has permissions to view this Post ID
		//by calling the function that checks if the user can view this forum, and hence this post
        if (private_groups_can_user_view_post_id($forum_id) && topic_permissions_check ($post_id)) {
		
			//User can view this post - add it to the allowed array
            array_push($allowed_posts, $post_id);
        }

    endwhile;

    //Return the list
    return $allowed_posts;
}


/*
 * Returns the bbPress Forum ID from given Post ID and Post Type
 * 
 * returns: bbPRess Forum ID
 */
function private_groups_get_forum_id_from_post_id($post_id, $post_type = 0) {
	if (empty ($post_type )) $post_type = get_post_type ($post_id) ;
     $forum_id = 0;

    // Check post type
    switch ($post_type) {
        // Forum
        case bbp_get_forum_post_type() :
            $forum_id = bbp_get_forum_id($post_id);
            break;

        // Topic
        case bbp_get_topic_post_type() :
            $forum_id = bbp_get_topic_forum_id($post_id);
            break;

        // Reply
        case bbp_get_reply_post_type() :
            $forum_id = bbp_get_reply_forum_id($post_id);
            break;
    }

    return $forum_id;
}

//enforce permission to ensure users only see permitted posts
function private_group_enforce_permissions() {
    global $rpg_settingsf ;
	// Bail if not viewing a bbPress item
    if (!is_bbpress())
        return;

    // Bail if not viewing a single item or if user has caps
    if (!is_singular() || bbp_is_user_keymaster() )
        
		return;

    if (!private_groups_check_can_user_view_post()   ) {
        if (!is_user_logged_in()) {
			if($rpg_settingsf['redirect_page2']) {
				$link=$rpg_settingsf['redirect_page2'] ;
				wp_redirect($link);
				exit;
			}
			else {		
				auth_redirect();
			}
		}
		else {
			if($rpg_settingsf['redirect_page1']) {
				$link=$rpg_settingsf['redirect_page1'] ;
				wp_redirect($link);
				exit;
			}	
			else {
				bbp_set_404();
			}
  	
		}
	}
}




function pg_remove_private_title($title) {
	global $rpg_settingsg ;
	if (isset( $rpg_settingsg['activate_remove_private_prefix']) ) {
	return '%s';
	}
		else {
		Return $title ;
		}
}

function pg_remove_protected_title($title) {
	global $rpg_settingsg ;
	if (isset ($rpg_settingsg['activate_remove_private_prefix'])  ) {
	return '%s';
	}
		else {
		Return $title ;
		}
}

function pg_get_forum_freshness_link ($anchor, $forum_id) {
	$anchor = pg_get_forum_freshness_link_anchor($forum_id) ;
	if ($anchor ['title'] == 'no_topics' ) {
		$anchor = esc_html__( 'No Topics', 'bbpress' );
	}
	else {
		$link_url = $anchor ['link_url'] ;
		$title = $anchor ['title'] ;
		$display = $anchor ['display'] ;
		$anchor = '<a href="' .esc_url( $link_url) . '" title="' . esc_attr( $title ) . '">' .esc_html( $display ) .'</a>';
	}
	return $anchor ;
}

function pg_get_forum_freshness_title () {
	$anchor = pg_get_forum_freshness_link_anchor() ;
	if ($anchor ['title'] == 'no_topics' ) {
		$anchor = esc_html__( 'No Topics', 'bbpress' );
	}
	else {
		$link_url = $anchor ['link_url'] ;
		$title = $anchor ['title'] ;
		$display = $anchor ['display'] ;
		$anchor = $anchor = '<a href="' .esc_url( $link_url) . '" title="' . esc_attr( $title ) . '">' .esc_html( $title ) .'</a><p/>';
	}
	return $anchor ;
}

function rpg_get_forum_last_active_id ($active_id, $forum_id) {
	$sub_forums = private_groups_get_permitted_subforums($forum_id) ;
	if ( !empty( $sub_forums ) ) {
		$active_id = 0;
		$show = array();
		//find the latest permissible 
		foreach ( $sub_forums as $sub_forum ) {
			$sub_forum_id =  $sub_forum->ID ;
			$active_id = get_post_meta( $sub_forum_id , '_bbp_last_active_id', true );
			$last_active = get_post_meta( $sub_forum_id, '_bbp_last_active_time', true );
			if ( empty( $active_id ) ) { // not replies, maybe topics ?
				$active_id = bbp_get_forum_last_topic_id( $sub_forum_id );
				if ( !empty( $active_id ) ) {
					$last_active = bbp_get_topic_last_active_time( $active_id );
				}
			}
			if ( !empty( $active_id ) ) {
				$curdate = strtotime($last_active);
				$show[$curdate] = $active_id ;
			}
		}
		$mostRecent= 0;
		foreach($show as $date=>$value){
			if ($date > $mostRecent) {
				 $mostRecent = $date;
			}
		}
		if ($mostRecent != 0) {
			$active_id = $show[$mostRecent] ;
		} else {
			$active_id = 0;
		}
	}
	return apply_filters( 'pg_get_forum_last_active_id', $active_id, $forum_id );
}



function pg_get_forum_freshness_link_anchor( $forum_id = 0 ) {
	global $rpg_settingsf ;
		$forum_id  = bbp_get_forum_id( $forum_id );
		$active_id = bbp_get_forum_last_active_id( $forum_id );
		
		
		if ( empty( $active_id ) )
			$active_id = bbp_get_forum_last_reply_id( $forum_id );

		if ( empty( $active_id ) )
			$active_id = bbp_get_forum_last_topic_id( $forum_id );
		
		//then reset forum_id to the forum of the active topic in case it is a sub forum
		$forum_id = private_groups_get_forum_id_from_post_id($active_id) ;
		$link_url  = $title = '';

		if ( bbp_is_topic( $active_id ) ) {
			$link_url = bbp_get_forum_last_topic_permalink( $forum_id );
			$title    = bbp_get_forum_last_topic_title( $forum_id );
			
			
		} elseif ( bbp_is_reply( $active_id ) ) {
			$link_url = bbp_get_forum_last_reply_url( $forum_id );
			$title    = bbp_get_forum_last_reply_title( $forum_id );
			
			
		}

		$time_since = bbp_get_forum_last_active_time( $forum_id );
		

	if ( !empty( $time_since ) && !empty( $link_url ) ) {
			//test if user can see this post, and post link if they can
			$user_id = wp_get_current_user()->ID;
			//now we can check if the user can view this, and if it's not private
			if (private_groups_can_user_view_post($user_id,$forum_id) &&  !bbp_is_forum_private($forum_id) && topic_permissions_check ($active_id))  {
				$anchor ['link_url'] = $link_url ;
				$anchor ['title'] = $title;
				$anchor ['display'] = $time_since;
				
			}
			//if it is private, then check user can view
			elseif (private_groups_can_user_view_post($user_id,$forum_id) && bbp_is_forum_private($forum_id) && current_user_can( 'read_private_forums' ) && topic_permissions_check ($active_id) ) {
				$anchor ['link_url'] = $link_url ;
				$anchor ['title'] = $title;
				$anchor ['display'] = $time_since;
			}
			//else we need to work out what to show so 
			
			elseif (private_groups_can_user_view_post($user_id,$forum_id) && !topic_permissions_check ($active_id)) {
			//now we check if it was only that user wasn't allowed to view the topic, and then find the last active topic for this user ('own topics' active)
				$last_active_topic = rpg_own_topic_freshness ($forum_id);
				//if user has no topics then  we return with 0
				if (empty($last_active_topic)) $anchor ['title'] = 'no_topics';
				else {
					$last_active_id = get_post_meta ($last_active_topic, '_bbp_last_active_id', true) ; 
					$anchor ['link_url'] =  bbp_get_topic_permalink( $last_active_id);	
					$anchor ['title'] =  bbp_get_topic_title($last_active_id );
					$anchor ['display'] = bbp_get_topic_last_active_time($last_active_topic ) ;
				}
			}
			//so no access unless... if visibility is set...
		
			elseif (!empty ($rpg_settingsf['set_forum_visibility'])) {
						
			//so we need to set up a $link that will determine where they go, and change the date to a freshness message if set
			//so set up for not logged in and logged in
			if (!is_user_logged_in()) {
				if($rpg_settingsf['redirect_page2']) {
						$link_url=$rpg_settingsf['redirect_page2'] ;
					}
					else {		
						$link_url="/wp-login";
					}
			}
			//so if logged in, 
			else {
				if($rpg_settingsf['redirect_page1']) {
					$link_url=$rpg_settingsf['redirect_page1'] ;
				}	
				else {
					$link_url='/404';
				}
  			}
			//now see if there is a freshness message
			if (!empty ($rpg_settingsf['set_freshness_message']) ) {
				$title=$rpg_settingsf['freshness_message'] ;
			}
			else {
				$title = esc_html( $time_since ) ;
			}
			
				
			//so we have a link and a title, so create the anchor
			$anchor ['link_url'] = $link_url ;
			$anchor ['title'] = $title;
			$anchor ['display'] = $title;
		
		
			}
			
		else {
			//visibility is not set, and we have no topics to show the user so
			$anchor ['title'] = 'no_topics';
		}
		
	
	}		
	else {
		// we have no topics, or something else has gone wrong !
		$anchor ['title'] = 'no_topics';
	}
	
	return $anchor;
}

//I can't see where I use this function !!
//this function is only used if we have a user with 'access own topics' only to forum
//basically it just changes the post_id called to the correct one
function rpg_get_last_active_author ($args) {
	$user_id = wp_get_current_user()->ID;
	$forum_id = 0 ;
	$active_id = $args['post_id'] ;
	if (private_groups_can_user_view_post($user_id,$forum_id) && !topic_permissions_check ($active_id)){
		$last_active_topic = rpg_own_topic_freshness ($forum_id);
		//if user has no topics then  we return with 0
		if (!empty($last_active_topic)) {
			$post_id = get_post_meta ($last_active_topic, '_bbp_last_active_id', true) ; 
			$args['post_id'] = $post_id ;
		}
	}
return $args ;

}


//we only need to get this link if forum visibility is active, otherwise it is not needed.
//and if user can't see the freshness link
function pg_check_profile($author_link, $args ) {
	//so we start with the code from the original suppress function
	// Assume the author link is the return value
	$retval = $author_link;

	// Show the normal author link
	if ( !empty( $args['post_id'] ) && !current_user_can( 'read_private_forums' ) ) {

		// What post type are we looking at?
		$post_type = get_post_field( 'post_type', $args['post_id'] );

		switch ( $post_type ) {

			// Topic
			case bbp_get_topic_post_type() :
				if ( bbp_is_forum_private( bbp_get_topic_forum_id( $args['post_id'] ) ) )
					$retval = '';

				break;

			// Reply
			case bbp_get_reply_post_type() :
				if ( bbp_is_forum_private( bbp_get_reply_forum_id( $args['post_id'] ) ) )
					$retval = '';

				break;

			// Post
			default :
				if ( bbp_is_forum_private( $args['post_id'] ) )
					$retval = '';

				break;
		}
	}
	//so now to code for private groups
	//so if retval isn't already blank, lets see if we should blank the author as the user isn't allowed to see this post
	//we also check whether $args['post_id'] is blank as for a new forum with no topics AND any super sticky topics in other forums, then post_id is blank
	if (!empty( $args['post_id'] ) && $retval !='') {
		$forum_id_check = private_groups_get_forum_id_from_post_id($args['post_id']);
		$current_user = wp_get_current_user()->ID;
		//now we can check if the user can view this
			if (!private_groups_can_user_view_post($current_user,$forum_id_check)) 
				$retval = '' ;
	}
	
	return apply_filters( 'pg_no_profile', $retval );
	
}
	
	
	

//This function is added to bbp_user_register which in turn hooks to wordpress user_register.  It checks if the user role has a group set against it, and if so assigns that to the user

function pg_role_group ($user_id) {
	if ($user_id == 0) return ;  // bail if no user ID
	global $rpg_roles ;
	if (empty ($rpg_roles)) return ; //bail if not set in $rpg_roles
	pg_set_user_group ($user_id) ;
}

//this function assigns user to roles on login
function pg_assign_role_on_login($user_login, $user) {
	$user_id = $user->ID ; 
	//bail if user groups are set for this user 
	$blank_test = get_user_meta ($user_id , 'private_group', true) ;
	if (!empty ($blank_test)) return ; //has an entry in the database
	//bail if login option not set in $rpg_roles
	global $rpg_roles ;
	if (empty ($rpg_roles)) return ;  //bail if not set in $rpg_roles
	pg_set_user_group ($user_id) ;
}

//this function assigns user to roles on every visit (and every page refresh!) 
function pg_assign_role_on_init() {
	global $rpg_roles ;
	$user = wp_get_current_user();
	$user_id = $user->ID ; 
	if (empty ($rpg_roles)) return ;  //bail if not set in $rpg_roles
	pg_set_user_group ($user_id) ;
}

//this function has been superseded by the one below to allow for multiple wordpress roles
function pg_set_user_group_old ($user_id) {
	global $rpg_roles ;
	$user_info = get_userdata($user_id);
	if (empty ($user_info)) return ;
    $user_roles = $user_info->roles ;
		foreach ((array)$user_roles as $list=>$role) {
			if (!empty ($rpg_roles[$role] ) ) {
				$group = $rpg_roles[$role] ;
					if ($group != 'no-group' && (!empty($group)) ) {
					update_user_meta( $user_id, 'private_group', $group); 
					}
			}
		}
	
}

function pg_set_user_group ($user_id) {
	global $rpg_roles ;
	$string='' ;
	$user_info = get_userdata($user_id);
	if (empty ($user_info)) return ;
    $user_roles = $user_info->roles ;
		foreach ((array)$user_roles as $list=>$role) {
			if (!empty ($rpg_roles[$role] ) ) {
				$group = $rpg_roles[$role] ;
					if ($group != 'no-group' && (!empty($group)) ) {
					$string.= $group.'*' ;
					}
			}
		}
	if (!empty ($string)) update_user_meta( $user_id, 'private_group', '*'.$string);
}








//function for style pack topic index query
function pg_display_topic_index_query_filter ($args) {
		//get forums this user is allowed to see
		$allowed_posts = pg_get_user_forums () ;
		if (empty ($allowed_posts))  $allowed_posts[] = -1 ;
	     // now we have $allowed forums, so then we need to only have forums that are common to both (intersect)
		if (!empty($args['post_parent__in'])) {
		$allowed_posts = array_intersect( $allowed_posts, $args['post_parent__in']);
		}
		
		//if there are no allowed forums set post_type to rubbish to ensure a nil return (otherwise it shows all allowed as post__in is blank)
		if (empty ($allowed_posts)) $args['post_type'] = 'qvyzzvxx' ;
		
		//then we can create the post__in data		
        $args['post_parent__in'] = $allowed_posts;
		
              
	return apply_filters( 'pg_display_topic_index_query_filter', $args );
}


//function for style pack topic index query
function pg_display_forum_query_filter ($args) {
		//get forums this user is allowed to see
		$allowed_posts = pg_get_user_forums () ;
		if (empty ($allowed_posts))  $allowed_posts[] = -1 ;
	     // now we have $allowed forums, so then we need to only have forums that are common to both (intersect)
		if (!empty($args['post__in'])) {
		$allowed_posts = array_intersect( $allowed_posts, $args['post__in']);
		}
		//if there are no allowed forums set post_type to rubbish to ensure a nil return (otherwise it shows all allowed as post__in is blank)
		if (empty ($allowed_posts)) $args['post_type'] = 'qvyzzvxx' ;
		
		//then we can create the post__in data		
        $args['post__in'] = $allowed_posts;
		
              
	return apply_filters( 'pg_display_topic_index_query_filter', $args );
}


//function to create array used by above two functions
function pg_get_user_forums () {
//create an array of current forums this user can view
	$query_data = array(
            'post_type' => bbp_get_forum_post_type(),
			'post_status' => bbp_get_public_status_id(),
            'posts_per_page' => get_option('_bbp_forums_per_page', 50),
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
		//PRIVATE GROUPS Get an array of forums which the current user has permissions to view
        $allowed_posts = private_groups_get_permitted_post_ids(new WP_Query($query_data));
		return $allowed_posts ;
}

//function for style pack latest activity widget
function pg_latest_activity_forum_query_filter ($topics_query) {
	    $topics_query['posts_per_page'] = 200;
		$allowed_posts = private_groups_get_permitted_post_ids(new WP_Query($topics_query));
		$topics_query['post__in'] = $allowed_posts ;
	return apply_filters( 'pg_latest_activity_forum_query_filter', $topics_query ) ;
}





function rpg_get_user_favorites( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	// If user has favorites, load them
	$favorites = bbp_get_user_favorites_topic_ids( $user_id );
	//create a filtered list	
	if ( !empty( $favorites ) ) {
		$filtered_favorites = array();
		foreach ($favorites as $list=>$fav) {
			$post_type = get_post_type ($fav) ;
			$forum_id = private_groups_get_forum_id_from_post_id($fav, $post_type);
			//then call the function that checks if the user can view this forum, and hence this post
    		if ( private_groups_can_user_view_post_id($forum_id, $post_type) && topic_permissions_check ($fav) ) array_push($filtered_favorites, $fav);
		}
	}
	//then show if any..
	if ( !empty( $filtered_favorites ) ) {
		$query = bbp_has_topics( array( 'post__in' => $filtered_favorites ) );
	} else {
		$query = false;
	}
	return apply_filters( 'rpg_get_user_favorites', $query, $user_id, $favorites );
}

//amended single forum description
function rpg_get_single_forum_description(  $retstr, $r  ) {
	$forum_id = bbp_get_forum_id( $r['forum_id'] );
	// check if this forum has sub-forums and if it hasn't then just return
	$children = bbp_forum_query_subforum_ids( $forum_id );
	if ( empty( $children ) ) {
	return apply_filters( 'rpg_get_single_forum_description1', $retstr, $r );
	}

		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_forum_permalink', 'bbp_add_view_all' );

		// Get some forum data
		$tc_int      = bbp_get_forum_topic_count( $forum_id, false );
		$rc_int      = bbp_get_forum_reply_count( $forum_id, false );
		$topic_count = bbp_get_forum_topic_count( $forum_id );
		$reply_count = bbp_get_forum_reply_count( $forum_id );
				
		$last_active = bbp_get_forum_last_active_id( $forum_id );
	
		// Has replies
		if ( !empty( $reply_count ) ) {
			$reply_text = sprintf( _n( '%s reply', '%s replies', $rc_int, 'bbpress' ), $reply_count );
		}

		$topic_text      = sprintf( _n( '%s topic', '%s topics', $tc_int, 'bbpress' ), $topic_count );
		
		// Forum has active data
		if ( !empty( $last_active ) ) {
			$last_updated_by = bbp_get_author_link( array( 'post_id' => $last_active, 'size' => $r['size'] ) );
			$forum_id = private_groups_get_forum_id_from_post_id($last_active) ;
			$time_since      = bbp_get_forum_last_active_time( $forum_id );

			if ( !empty( $reply_count ) ) {

				if ( bbp_is_forum_category( $forum_id ) ) {
					$retstr = sprintf( esc_html__( 'This category contains %1$s and %2$s, and was last updated by %3$s %4$s.', 'bbpress' ), $topic_text, $reply_text, $last_updated_by, $time_since );
				} else {
					$retstr = sprintf( esc_html__( 'This forum contains %1$s and %2$s, and was last updated by %3$s %4$s.',    'bbpress' ), $topic_text, $reply_text, $last_updated_by, $time_since );
				}

			} else {

				if ( bbp_is_forum_category( $forum_id ) ) {
					$retstr = sprintf( esc_html__( 'This category contains %1$s, and was last updated by %2$s %3$s.', 'bbpress' ), $topic_text, $last_updated_by, $time_since );
				} else {
					$retstr = sprintf( esc_html__( 'This forum contains %1$s, and was last updated by %2$s %3$s.',    'bbpress' ), $topic_text, $last_updated_by, $time_since );
				}
			}

		// Forum has no last active data
		} else {

			if ( !empty( $reply_count ) ) {

				if ( bbp_is_forum_category( $forum_id ) ) {
					$retstr = sprintf( esc_html__( 'This category contains %1$s and %2$s.', 'bbpress' ), $topic_text, $reply_text );
				} else {
					$retstr = sprintf( esc_html__( 'This forum contains %1$s and %2$s.',    'bbpress' ), $topic_text, $reply_text );
				}

			} else {

				if ( !empty( $topic_count ) ) {

					if ( bbp_is_forum_category( $forum_id ) ) {
						$retstr = sprintf( esc_html__( 'This category contains %1$s.', 'bbpress' ), $topic_text );
					} else {
						$retstr = sprintf( esc_html__( 'This forum contains %1$s.',    'bbpress' ), $topic_text );
					}

				} else {
					$retstr = esc_html__( 'This forum is empty.', 'bbpress' );
				}
			}
		}

		// Add the 'view all' filter back
		add_filter( 'bbp_get_forum_permalink', 'bbp_add_view_all' );

		// Combine the elements together
		$retstr = $r['before'] . $retstr . $r['after'];

		// Return filtered result
		return apply_filters( 'rpg_get_single_forum_description', $retstr, $r );
		
}


//function to deal with cases where a forum/category has sub forums, but the user might not be able to see all these sub forums, and gather data
function rpg_check_subforums ($forum_id) {
	$forum_id = bbp_get_forum_id( $forum_id );
	$sub_forums = private_groups_get_permitted_subforums($forum_id) ;
		if ( !empty( $sub_forums ) ) {
		//set up an array $show with dates for each forum set to last active topic ID 
			foreach ( $sub_forums as $sub_forum ) {
				$sub_forum_id =  $sub_forum->ID ;
				$active_id = bbp_get_forum_last_active_id( $sub_forum_id );
				$last_active = get_post_meta( $active_id, '_bbp_last_active_time', true );
				$curdate = strtotime($last_active);
				$show[$curdate] = $active_id ;
			}
			//then loop thorugh this array to find the most recent date
				$mostRecent= 0;
				foreach($show as $date=>$value){
					if ($date > $mostRecent) {
						 $mostRecent = $date;
					}
				}
			//then assemble the anchor for latest topic
				$latest_topic = $show[$mostRecent] ;
				$link_url = bbp_get_topic_permalink( $latest_topic );
				$title    = bbp_get_topic_title( $latest_topic  );
				$time_since = bbp_get_topic_last_active_time( $latest_topic );
				$anchor ['latest_topic'] = $latest_topic ;
				$anchor ['link_url'] = $link_url ;
				$anchor ['title'] = $title;
				$anchor ['display'] = $time_since;
			}
			else {
				//pass back a title which we can use to say none
					$anchor ['title'] = 'no_topics';
			}
return $anchor ;
}


function rpg_get_forum_topic_count ($topics, $forum_id) {
	$forum_id = bbp_get_forum_id( $forum_id );
	//do a check to ensure that user can see all items in this forum and if not calculate topics by author - for 'access own' forums
	if (!rpg_access_own_topics_check ($forum_id)) {
		$topics = rpg_calaculate_own_topics ($forum_id) ;
		return apply_filters('rpg_get_forum_topic_count3' , $topics, $forum_id );
	}
	$sub_forums = private_groups_get_permitted_subforums($forum_id) ;
	
		if ( !empty( $sub_forums ) ) {
			$topics =  0 ;
			foreach ( $sub_forums as $sub_forum ) {
				$sub_forum_id =  $sub_forum->ID ;
				//and increment topic totals
				$topics = $topics + get_post_meta( $sub_forum_id, '_bbp_total_topic_count', true ); 
				}
					//if main forum is also a forum not a category, then add this in
					if (bbp_get_forum_type($forum_id) == 'forum') {
						$topics = $topics + get_post_meta( $forum_id, '_bbp_topic_count', true ); 
					}
				return apply_filters('rpg_get_forum_topic_count1' , $topics, $forum_id );
		}
		else {
			return apply_filters('rpg_get_forum_topic_count2', $topics, $forum_id );
		}
}

function rpg_calaculate_own_topics ($forum_id) {
	$count=0 ;
	$user_id = wp_get_current_user()->ID;
	//Get an array of topics 
	global $wpdb;
	$post=bbp_get_topic_post_type() ;
	$post_ids=$wpdb->get_col("select ID from $wpdb->posts where post_type = '$post' and post_parent = '$forum_id' and post_status <> 'trash'") ;
	foreach ($post_ids as $post_id) {
		$author_id  = bbp_get_topic_author_id( $post_id);
		if ($user_id == $author_id) 
			$count++ ;
		}		
	return $count;
}

function rpg_calaculate_own_replies ($forum_id) {
	$count=0 ;
	$topics = array();
	$user_id = wp_get_current_user()->ID;
	//Get an array of topics first
	global $wpdb;
	$post=bbp_get_topic_post_type() ;
	$post_ids=$wpdb->get_col("select ID from $wpdb->posts where post_type = '$post' and post_parent = '$forum_id' and post_status <> 'trash'") ;
	foreach ($post_ids as $post_id) {
		$author_id  = bbp_get_topic_author_id( $post_id);
		if ($user_id == $author_id) {
			//get the reply count
		$count= $count + get_post_meta ($post_id, '_bbp_reply_count', true) ;
		}	
	}		
	return $count;
}
	
	

function rpg_get_forum_reply_count ($replies, $forum_id) {
	$forum_id = bbp_get_forum_id( $forum_id );
	if (!rpg_access_own_topics_check ($forum_id)) {
		$replies = rpg_calaculate_own_replies ($forum_id) ;
	return apply_filters('rpg_get_forum_reply_count3' , $replies, $forum_id );
	}
	$sub_forums = private_groups_get_permitted_subforums($forum_id) ;
		if ( !empty( $sub_forums ) ) {
			$replies  = 0 ;
			foreach ( $sub_forums as $sub_forum ) {
				$sub_forum_id =  $sub_forum->ID ;
				//and increment reply totals
				$replies = $replies + get_post_meta( $sub_forum_id, '_bbp_total_reply_count', true ); 
				}
				//if main forum is also a forum not a category, then add this in
					if (bbp_get_forum_type($forum_id) == 'forum') {
						$replies = $replies + get_post_meta( $forum_id, '_bbp_reply_count', true ); 
					}
			return apply_filters('rpg_get_forum_reply_count1' , $replies, $forum_id );
		}
		else {
			return apply_filters('rpg_get_forum_reply_count2', $replies, $forum_id );
		}
}

function rpg_get_forum_post_count ($retval, $forum_id) {
	$forum_id = bbp_get_forum_id( $forum_id );
	if (!rpg_access_own_topics_check ($forum_id)) {
		$retval = rpg_calaculate_own_replies ($forum_id) ;
	return apply_filters('rpg_get_forum_reply_count3' , $retval, $forum_id );
	}
	$sub_forums = private_groups_get_permitted_subforums($forum_id) ;
		if ( !empty( $sub_forums ) ) {
			$replies  = $topics = $retval = 0 ;
			foreach ( $sub_forums as $sub_forum ) {
				$sub_forum_id =  $sub_forum->ID ;
				//and increment reply totals
				$replies = $replies + get_post_meta( $sub_forum_id, '_bbp_total_reply_count', true );
				$topics = $topics + get_post_meta( $sub_forum_id, '_bbp_total_topic_count', true ); 
			}
			//if main forum is also a forum not a category, then add this in
		/*if (bbp_get_forum_type($forum_id) == 'forum') {
				$replies = $replies + get_post_meta( $sub_forum_id, '_bbp_reply_count', true );
				$topics = $topics + get_post_meta( $sub_forum_id, '_bbp_topic_count', true ); 						
			}*/
			$retval = 	$replies + $topics ;
			return apply_filters('rpg_get_forum_post_count1' , $retval, $forum_id );
		}
		else {
			return apply_filters('rpg_get_forum_post_count2', $retval, $forum_id );
		}
}




//add private groups to forum lists

//////////////////////////////  ADD FORUM ID column to admin if not added by style pack

function rpg_ID_column_add($columns)  {
	$new = array();
  foreach($columns as $key => $title) {
    if ($key=='bbp_forum_topic_count') // Put the forum ID column before the Topics column
      $new['rpg_id'] = 'Forum ID';
    $new[$key] = $title;
  }
  return $new;
}
	
function rpg_ID_column_value($column_name, $id) {
		if ($column_name == 'rpg_id') echo $id;
}


function rpg_groups_column_add($columns)  {
	$new = array();
  foreach($columns as $key => $title) {
    if ($key=='bbp_forum_topic_count') // Put the forum ID column before the Topics column
      $new['rpg_pg'] = 'Private Groups';
    $new[$key] = $title;
  }
  return $new;
}
	
function rpg_groups_column_value($column_name, $id) {
	if ($column_name == 'rpg_pg') {
		global $rpg_groups ;
		$meta = get_post_meta( $id, '_private_group', false );
		echo '<ul>' ;
		foreach ($meta as $group) {
			$groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ;
			$details = $rpg_groups[$group] ;
			echo '<li>'.$groupname.' '.$details.'</li>' ;
		}
		echo '</ul>' ;
	}
		
}

function rpg_user_can_view_forum ($retval, $forum_id, $user_id ){
	//if it's already false then just return it, otherwise...
	if ($retval == true) {
		$retval =  private_groups_can_user_view_post( $user_id, $forum_id ) ;
	}
	return $retval ;
}


//filter back end topics and replies to ensure that moderators can only see their own forums
function rpg_filter_bbp_request( $array ) {
	//only apply if we are in backend admin (otherwise all forum displays go to 404 !)
	$link = $_SERVER['REQUEST_URI'];
	$topic_admin = '/wp-admin/edit.php?post_type='.bbp_get_topic_post_type() ;
	$reply_admin = '/wp-admin/edit.php?post_type='.bbp_get_reply_post_type() ;
	if ($link == $topic_admin || $link == $reply_admin ) {
		global $wpdb;
		$forum=bbp_get_forum_post_type() ;
		$forum_ids=$wpdb->get_col("select ID from $wpdb->posts where post_type = '$forum'") ;
		//check this list against those the user is allowed to see, and create a list of valid ones for the wp_query
		$allowed_forums = private_groups_check_permitted_forums($forum_ids) ;
		$array['meta_key']   = '_bbp_forum_id';
		$array['meta_value'] = $allowed_forums;
		$array['compare']= 'IN' ;
	}
	return $array ;
}