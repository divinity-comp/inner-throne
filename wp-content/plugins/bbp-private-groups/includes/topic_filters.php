<?php


// filter the topics so only those viewable by user are shown, and topic and reply forms only shown where needed
//only add filters if topic permissions set
global $rpg_topic_permissions ;
if (!empty ($rpg_topic_permissions['activate']) ) {
	//filter display
	add_filter('bbp_before_has_topics_parse_args', 'private_groups_topics', 10, 2);
	//filter forms being shown
	add_filter ( 'bbp_current_user_can_access_create_topic_form', 'pg_current_user_can_access_create_topic_form') ;
	add_filter ( 'bbp_current_user_can_access_create_reply_form', 'pg_current_user_can_access_create_reply_form') ;
}
 
function private_groups_topics ($args='') {
		//we only need to filter topics if this forum has topic permissions set to 5 - Create/Edit/view OWN Topics, create/edit Replies to those topics and view any Replies to those topics
		//so check if this is the case
		$valuecheck = pg_check_topic_permissions(bbp_get_forum_id()) ;
		//then we'll quit unless $valuecheck['check5']='1', as we don't need to filter
		if (empty($valuecheck['check5'])) return $args;
		//but then we need to check if this user has permission as part of other groups, if so quit
		if (!empty($valuecheck['check1'])  || !empty($valuecheck['check2']) || !empty($valuecheck['check3']) || !empty($valuecheck['check4']) ) return $args ;
		//so now as we only have check5 - only show topics by the author
		$args['author'] = wp_get_current_user()->ID;
		return $args ;
}

function topic_permissions_check ($post_id = 0) {
	//determines if topic permissions are active, and if so if user can see a post 
	global $rpg_topic_permissions ;
	if (empty ($rpg_topic_permissions['activate']) )
		return true;
	//now we test if this is a forum - if so then topic permissions don't apply so we return true - revised in 3.6.2 for 'access own' type forums
	$post_type = get_post_type ($post_id) ;
	if ($post_type == bbp_get_forum_post_type()) 
		return true;
	//first check if this is a sticky topic and if so show it
		if (bbp_is_topic_super_sticky($post_id) || bbp_is_topic_sticky($post_id))
			return true;
	//now we do a topic permissions check
			$forum_id = private_groups_get_forum_id_from_post_id($post_id);
	//and check if user can see this topic by checking if he only has $valuecheck['check5']='1' for the forum
				if (rpg_access_own_topics_check ($forum_id, $post_id))
					return true;
	
}


function rpg_access_own_topics_check ($forum_id, $post_id=0) {
	$valuecheck = pg_check_topic_permissions($forum_id) ;
	//then we'll quit unless $valuecheck['check5']='1', as we don't need to filter
	if (empty($valuecheck['check5'])) 
		return true;
	//but then we need to check if this user has permission as part of other groups, if so quit
	if (!empty($valuecheck['check1'])  || !empty($valuecheck['check2']) || !empty($valuecheck['check3']) || !empty($valuecheck['check4']) ) 
		return true;
	//so now as we only have check5 - only show topics, or replies to topics, by the author
	if (!empty ($post_id)) {
		if (bbp_is_reply ($post_id) ) $topic_id = bbp_get_reply_topic_id($post_id) ;
		else $topic_id = bbp_get_topic_id ($post_id) ;
		$author_id  = bbp_get_topic_author_id( $topic_id);
		$user_id = wp_get_current_user()->ID;
		if ($user_id == $author_id) 
			return true ;
	}
}


function rpg_own_topic_freshness ($forum_id) {
	$user_id = wp_get_current_user()->ID;
	$latest = array() ;
	//Get an array of topics 
	global $wpdb;
	$post=bbp_get_topic_post_type() ;
	$post_ids=$wpdb->get_col("select ID from $wpdb->posts where post_type = '$post' and post_parent = '$forum_id' and post_status <> 'trash'") ;
	foreach ($post_ids as $post_id) {
		$author_id  = bbp_get_topic_author_id( $post_id);
		if ($user_id == $author_id) {
			$time = get_post_meta ($post_id, '_bbp_last_active_time', true) ;
			//then add to the array the last active time
			$latest[$post_id] = $time ;
		}
	}
	if (empty($latest)) $retval = 0 ;
	else	{
		$maxs[0] = array_keys($latest, max($latest)) ;
		$retval = implode ('',$maxs[0]) ;
	}
	return $retval ;
}
	
	

function pg_current_user_can_access_create_reply_form() {
	// Users need to earn access
	$retval = false;

	$user_id = wp_get_current_user()->ID;
	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;
	}
	//disallow editing of closed topics unless keymaster
	elseif (!bbp_is_user_keymaster() && bbp_is_topic_closed()) {
		$retval = false;	
	}
			
	//for replies
	
	//we are in a single topic so check if user is allowed replies in the forum the topic belongs to
	//or we are editing an existing reply - so check even though user should not have been able to create !
		
	
	
	// Looking at a single topic, topic is open, and forum is open
	elseif ( ( bbp_is_single_topic() || is_page() || is_single() ) && bbp_is_topic_open() && bbp_is_forum_open() ) {
		//so now find the topic, and then check if user has topic pernissions that need checking, by finding the forum
		$topic = bbp_get_topic_id() ;
		//we should only ever have a topic ID, and in theory the next lines aren't needed, but here just in case
		if (!empty ($topic)) $forum_id = get_post_field( 'post_parent', $topic );
		//if no topic is set, then we are in theory creating a new topic so should not be in this part, but just in case  - we don't have  topic=forum, so we need to find if we're in a single forum, and check that
		elseif (bbp_is_single_forum() ) $forum_id = bbp_get_forum_id() ;
		else {
			//else no idea why we are in this function so return false to be safe
			$retval = false;
			return (bool) apply_filters( 'pg_current_user_can_access_create_reply_form_a', (bool) $retval );
		}
		//else we have a forum, so check permissions
		
		$valuecheck = pg_check_topic_permissions ($forum_id) ;
		
			
		//so we exit with potentials of on the $valuecheck array, and now need to check these in order
		//users can have multiple groups, so we start with highest permission and work down
			if (empty ($valuecheck) || !empty ($valuecheck['check4']) ) {
				//if the array doesn't exist, then either the forum has no topic permissions, or the user doesn't have any matching so we can return
				//if we have a $value('check4'] then we can return, as access has already been decided and we can return
				//either with retval true as the user is admin or if the user can edit
				$retval = bbp_current_user_can_publish_replies();
				return (bool) apply_filters( 'pg_current_user_can_access_create_reply_form_b', (bool) $retval );		
			}
			//if $value['check3'] exists, then user can edit replies, so set user capabilities and return
			if (!empty ($valuecheck['check3']) ) {
				$retval = bbp_current_user_can_publish_replies(); 
				return (bool) apply_filters( 'pg_current_user_can_access_create_reply_form_c', (bool) $retval );
			}
					
			//if $value['check5'] (helpdesk type forum) exists, then user can create and edit his own topics - so he can create create replies to his own topics
			if (!empty ($valuecheck['check5']) ) {
				//so we see if he is author of this topic, set to true to allow reply
				$user_id = wp_get_current_user()->ID;
				$author_id      = bbp_get_topic_author_id( $topic);
				if ($user_id == $author_id) 
					$retval = bbp_current_user_can_publish_replies(); 
				else $retval = false ;
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_d', (bool) $retval );	
			}	
				
				
			//if $value['check2'] or $value ['check1'] exists, then user can only either edit topics or view, so set to false and return
			if (!empty ($valuecheck['check2']) || !empty ($valuecheck['check1']) ) {	
				$retval = false;
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_e', (bool) $retval );	
			}
		
	// or.. User can edit this reply
	} elseif ( bbp_is_reply_edit() ) {
		 $topic = bbp_get_topic_id() ;
		if (!empty ($topic)) $forum_id = get_post_field( 'post_parent', $topic );
			$valuecheck = pg_check_topic_permissions ($forum_id) ;
			//so we exit with potentials of on the $valuecheck array, and now need to check these in order
			//users can have multiple groups, so we start with highest permission and work down
			//var_dump ($valuecheck) ;
			if (empty ($valuecheck) || !empty ($valuecheck['check4']) ) {
				//if the array doesn't exist, then either the forum has no topic permissions, or the user doesn't have any matching so we can return
				//if we have a $value('check4'] then we can return, as access has already been decided and we can return
				//either with retval true as the user is admin or if the user can edit
				$retval = current_user_can( 'edit_reply', bbp_get_topic_id() );
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_f', (bool) $retval );		
			}
			//if $value['check3'] exists, then user can edit reply, so set to true then test user capabilities and return
			if (!empty ($valuecheck['check3']) ) {
				//echo '<br> value check is 2 create replies' ;
					$retval = current_user_can( 'edit_reply', bbp_get_topic_id() );
					return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_g', (bool) $retval );
					}
			//if $value['check5'] (helpdesk type forum) exists, then user can create and edit his own topics - so he can edit his own topic 
			//he shouldn't be able to see or edit other topics, but we'll do a check just in case , and set to false if needbe
			//- for instance if he is a moderator, then we might not want him moderating this forum
			if (!empty ($valuecheck['check5']) ) {
				//so we see if he is author of this topic, set to true
				$user_id = wp_get_current_user()->ID;
				$author_id      = bbp_get_topic_author_id( $topic);
				if (user_id == $author_id) 
					$retval = current_user_can( 'edit_reply', $topic );
				else $retval = false ;
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_h', (bool) $retval );	
			}	
			//if $value['check2'] or $value ['check1'] exists, then user can only either edit toopics or view  , so set to false and return
			if (!empty ($valuecheck['check2']) || !empty ($valuecheck['check1']) ) {	
			//echo '<br> value check is 2 or 1  create replies or only view - so currently set to false as only done for replies' ;
			$retval = false;
			return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_j', (bool) $retval );	
			}
		
	
	}  //end of reply edit
return (bool) apply_filters( 'pg_current_user_can_access_create_reply_form', (bool) $retval );	
}

function pg_current_user_can_access_create_topic_form() {
	// Users need to earn access
	$retval = false;

	$user_id = wp_get_current_user()->ID;
	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;
	}
	//disallow editing of closed topics unless keymaster
	elseif (!bbp_is_user_keymaster() && bbp_is_topic_closed()) {
		$retval = false;	
	}
			
	//for topics 
	//either we are in multiple forums - in which case this function is not called -  'private_groups_get_dropdown_forums' in forum functions takes care of restricting access
	//or we are in a single forum so check if user is allowed topics in that forum
	//or we are editing an existing topic - so check even though user should not have been able to create !
		
	
	
	// Looking at a single forum & forum is open
	elseif ( ( bbp_is_single_forum() || is_page() || is_single() ) && bbp_is_forum_open() ) {
		$forum_id = bbp_get_forum_id() ;
		$valuecheck = pg_check_topic_permissions ($forum_id) ;
		//so we exit with potentials of on the $valuecheck array, and now need to check these in order
		//users can have multiple groups, so we start with highest permission and work down
			if (empty ($valuecheck) || !empty ($valuecheck['check4']) ) {
				//if the array doesn't exist, then either the forum has no topic permissions, or the user doesn't have any matching so we can return
				//if we have a $value('check4'] then we can return, as access has already been decided and we can return
				//either with retval true as the user is admin or if the user can edit
				$retval = bbp_current_user_can_publish_topics();
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_a', (bool) $retval );		
			}
			//if $value['check2'] exists, then user can edit topic, so set to true then test user capabilities and return
			if (!empty ($valuecheck['check2']) ) {
				$retval = bbp_current_user_can_publish_topics(); 
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_b', (bool) $retval );
			}
					
			//if $value['check5'] (helpdesk type forum) exists, then user can create and edit his own topics - so he can create topics
			if (!empty ($valuecheck['check5']) ) {
				$retval = bbp_current_user_can_publish_topics();
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_c', (bool) $retval );	
			}	
			//if $value['check3'] or $value ['check1'] exists, then user can only either edit replies or view, so set to false and return
			if (!empty ($valuecheck['check3']) || !empty ($valuecheck['check1']) ) {	
			//echo '<br> value check is 3 or 1  create topics or only view - so currently set to false as only done for replies' ;
			$retval = false;
			return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_d', (bool) $retval );	
			}
		
	// or.. User can edit this topic
	} elseif ( bbp_is_topic_edit() ) {
		 $topic = bbp_get_topic_id() ;
		if (!empty ($topic)) $forum_id = get_post_field( 'post_parent', $topic );
			$valuecheck = pg_check_topic_permissions ($forum_id) ;
			//so we exit with potentials of on the $valuecheck array, and now need to check these in order
			//users can have multiple groups, so we start with highest permission and work down
			//var_dump ($valuecheck) ;
			if (empty ($valuecheck) || !empty ($valuecheck['check4']) ) {
				//if the array doesn't exist, then either the forum has no topic permissions, or the user doesn't have any matching so we can return
				//if we have a $value('check4'] then we can return, as access has already been decided and we can return
				//either with retval true as the user is admin or if the user can edit
				$retval = current_user_can( 'edit_topic', bbp_get_topic_id() );
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_e', (bool) $retval );		
			}
			//if $value['check2'] exists, then user can edit topic, so set to true then test user capabilities and return
			if (!empty ($valuecheck['check2']) ) {
				//echo '<br> value check is 2 create topics' ;
				$retval = true;
				//unless their base permission changes it back to false
					$retval = current_user_can( 'edit_topic', bbp_get_topic_id() );
					return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_f', (bool) $retval );
					}
			//if $value['check5'] (helpdesk type forum) exists, then user can create and edit his own topics - so he can edit his own topic 
			//he shouldn't be able to see or edit other topics, but we'll do a check just in case , and set to false if needbe
			//- for instance if he is a moderator, then we might not want him moderating this forum
			if (!empty ($valuecheck['check5']) ) {
				//so we see if he is author of this topic, set to true
				$user_id = wp_get_current_user()->ID;
				$topic_id = bbp_get_topic_id() ;
				$author_id      = bbp_get_topic_author_id( $topic_id);
				if (user_id == $author_id) 
					$retval = current_user_can( 'edit_topic', bbp_get_topic_id() );
				else $retval = false ;
				return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_g', (bool) $retval );	
			}	
			//if $value['check3'] or $value ['check1'] exists, then user can only either edit replies or view  , so set to false and return
			if (!empty ($valuecheck['check3']) || !empty ($valuecheck['check1']) ) {	
			//echo '<br> value check is 3 or 1  create replies or only view - so currently set to false as only done for replies' ;
			$retval = false;
			return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form_h', (bool) $retval );	
			}
		
	
	}  //end of topic edit
return (bool) apply_filters( 'pg_current_user_can_access_create_topic_form', (bool) $retval );	
}


function pg_check_topic_permissions ($forum_id = 0) {
	if ($forum_id == 0 ) {
	return ;
	}
	$valuecheck = array();
	if (!is_user_logged_in() ) {
		//if user is not logged-in, then we only allow access if topic permissions are activated AND anonymous posting is allowed, AND  anon user is allowed to create topics
		global $rpg_topic_permissions ;
			if (!empty ($rpg_topic_permissions['activate']) && bbp_allow_anonymous() ) {
			$value = get_post_meta( $forum_id, '_private_group_nonloggedin', true );
						if ($value == '1') $valuecheck['check1']='1' ;
						if ($value == '2' ) $valuecheck['check2']='1' ;
						if ($value == '3') $valuecheck['check3']='1' ;
						if ($value == '4') $valuecheck['check4']='1' ;
			}
			//if not then it will return an empty array, so no access
	}
		else{
	//else user is logged in	
		$user_id = wp_get_current_user()->ID;
		$groups = get_post_meta( $forum_id, '_private_group', false );
		//so now we know which forum it is in - now we need to know if user has access to any groups of this forum  - so we get the groups this user has
		$check=get_user_meta( $user_id, 'private_group',true);
			foreach ( $groups as $group ) {
					$has_group = false ;
					//single group set?
					if ($check==$group ) $has_group = true;
					//multiple group set
					if (strpos($check, '*'.$group.'*') !== FALSE) $has_group = true;
					//so if user has this group, so check if forum has this set in topic permissions.
					if (!empty($has_group)) {
					//user might have multiple groups with different topic permissions, so we need to create an array of these.
						$tp = '_private_group_'.$group ;
						$value = get_post_meta( $forum_id, $tp, true);
						//$value = (!empty (get_post_meta( $forum_id, $tp, true)) ? get_post_meta( $forum_id, $tp, true ) : '' );
						if ($value == '1') $valuecheck['check1']='1' ;
						if ($value == '2' ) $valuecheck['check2']='1' ;
						if ($value == '3') $valuecheck['check3']='1' ;
						if ($value == '4') $valuecheck['check4']='1' ;
						if ($value == '5') $valuecheck['check5']='1' ;
					}
			} //end of foreach
		} // end of else
return ($valuecheck) ;
}


