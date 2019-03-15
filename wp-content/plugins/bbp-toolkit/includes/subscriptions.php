<?php

/**
* Add new users to the default forum(s)
*/
function bbptoolkit_insert_forums_new_user( $meta, $user, $update ) {
	// $update = true means user has been updated, so only act if $update == false
	if ($update == false) {
		// Get default forums to add
		$default_forums = get_option('bbptoolkit-def-subscr-forums',false);
		if ($default_forums) {
			$user_id = $user->ID;
			$a = array_filter(explode("*",$default_forums));
			foreach ($a as $forum_id) {
				// add subscription for user to default forums except for categories
				if (($user_id) && (bbp_is_forum($forum_id)) && (!bbp_is_forum_category($forum_id))) {
					bbp_add_user_forum_subscription( $user_id, $forum_id ); 
				}
			}
		}
	}
	return $meta; 
};
add_filter( 'insert_user_meta', 'bbptoolkit_insert_forums_new_user', 10, 3 ); 

/**
* Add 'Subscriptions' as action for forum
*/

function bbptoolkit_forum_subscr_action_link($actions, $post) {
	if ( $post->post_type == "forum" ) {
		if ((bbp_current_user_can_publish_forums()) && (!((bbp_is_forum_category($post->ID))))) {
			$actions['mng_subscr'] = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&forum_id=' . $post->ID . '">' . __( 'Subscriptions', 'bbp-toolkit' ) . '</a>';
		}
	}
	return $actions;
}
add_filter('page_row_actions', 'bbptoolkit_forum_subscr_action_link', 10, 2);

/**
* Add 'Subscriptions' as action for topics
*/

function bbptoolkit_topic_subscr_action_link($actions, $post) {
	if ( $post->post_type == "topic" ) {
		if ((bbp_current_user_can_publish_forums()) && (!((bbp_is_forum_category($post->ID))))) {
			$actions['mng_subscr'] = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&topic_id=' . $post->ID . '">' . __( 'Subscriptions', 'bbp-toolkit' ) . '</a>';
		}
	}
	return $actions;
}
add_filter('post_row_actions', 'bbptoolkit_topic_subscr_action_link', 10, 2);

/**
* Add 'Subscriptions' as action for user
*/

function bbptoolkit_user_subscr_action_link($actions, $user_object) {
	if (bbp_current_user_can_publish_forums()) {
		$actions['mng_subscr'] = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&user_id=' . $user_object->ID . '">' . __( 'Subscriptions', 'bbp-toolkit' ) . '</a>';
	}
		return $actions;
}
add_filter('user_row_actions', 'bbptoolkit_user_subscr_action_link', 10, 2);


/**
* Add Subscriptions metabox to forum
*/
function bbptoolkit_forum_subscrip_metabox() {
	echo '<br>';
	if (bbp_is_forum_category(get_the_ID())) {
		echo __('No subscriptions for categories', 'bbp-toolkit');
	} else {
		$forum_id = get_the_ID();
		$users_arr = bbp_get_forum_subscribers($forum_id);
		$subscriber_count = count($users_arr);
		
		echo '<a class="preview button" href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&forum_id=' . $forum_id . '">'; _e('Manage Subscriptions', 'bbp-toolkit'); echo ' (' . $subscriber_count . ')</a>';
	}
	echo '<br>';
	echo '<br>';
}
function bbptoolkit_subscrip_attributes_metabox() {
	// Meta data
	add_meta_box(
		'bbptoolkit_forum_subscrip_metabox',
		__( 'Forum Subscriptions', 'bbp-toolkit' ),
		'bbptoolkit_forum_subscrip_metabox',
		'forum',
		'side'
	);
}
add_action('add_meta_boxes', 'bbptoolkit_subscrip_attributes_metabox');

/**
* Add Subscriptions column to forum list
*/

function bbptksub_edit_forum_column( $columns ) {
	$columns['manage_subscriptions'] = 'Subscriptions';
	return $columns;
}
add_filter( 'manage_edit-forum_columns', 'bbptksub_edit_forum_column' );

function bbptksub_manage_forum_column($column_name, $id) {
	global $wpdb;
	switch ($column_name) {
	case 'manage_subscriptions':
		if (bbp_is_forum_category($id)) {
			echo '';
		} else {
			if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
				$url = $_SERVER['REQUEST_URI'] . '&page=forum_subscriptions&forum_id=' . $id;
			} else {
				$url = $_SERVER['REQUEST_URI'] . '?page=forum_subscriptions&forum_id=' . $id;
			}
			$users_arr = bbp_get_forum_subscribers($id);
			$subscriber_count = count($users_arr);
			echo '<a href="' . $url . '">' . $subscriber_count . ' subscriber(s)</a>';
		}
		break;
	default:
		break;
	} // end switch
}  
add_action('manage_forum_posts_custom_column', 'bbptksub_manage_forum_column', 10, 2);

/**
* Add Subscriptions column to topic list
*/

function bbptksub_edit_topic_column( $columns ) {
	$columns['manage_subscriptions'] = 'Subscriptions';
	return $columns;
}
add_filter( 'manage_edit-topic_columns', 'bbptksub_edit_topic_column' );

function bbptksub_manage_topic_column($column_name, $id) {
	global $wpdb;
	switch ($column_name) {
	case 'manage_subscriptions':
		$url = site_url( '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&topic_id=' . $id );
		$users_arr = bbp_get_topic_subscribers($id);
		$subscriber_count = count($users_arr);
		echo '<a href="' . $url . '">' . $subscriber_count . ' subscriber(s)</a>';
		break;
	default:
		break;
	} // end switch
}  
add_action('manage_topic_posts_custom_column', 'bbptksub_manage_topic_column', 10, 2);

/**
* Add Subscriptions menu item under forums
*/
add_action('admin_menu', 'bbptoolkit_subscr_submenu');
function bbptoolkit_subscr_submenu(){
	$confHook = add_submenu_page('edit.php?post_type=forum', 'Subscriptions', 'Subscriptions', 'edit_forums', 'forum_subscriptions', 'forum_subscriptions_page');
	add_action("admin_head-$confHook", 'bbptksub_admin_header');
}

function bbptksub_admin_header() {
	echo '<script type=\'text/javascript\'>';
	echo 'function bbptksubtoggleall(master,group) {';
	echo '	var cbarray = document.getElementsByClassName(group);';
	echo '	for(var i = 0; i < cbarray.length; i++){';
	echo '		var cb = document.getElementById(cbarray[i].id);';
	echo '		cb.checked = master.checked;';
	echo '	}';
	echo '}';
	echo '</script>';
	echo '<style type="text/css">';
	echo '.wp-list-table .subscr-yes-button {
	  -webkit-border-radius: 28;
	  -moz-border-radius: 28;
	  border-radius: 28px;
	  font-family: Arial;
	  color: #ffffff;
	  font-size: 12px;
	  background: #3498db;
	  padding: 3px 7px 3px 7px;
	  text-decoration: none;
	  }
	.wp-list-table .subscr-no-button {
	  -webkit-border-radius: 28;
	  -moz-border-radius: 28;
	  border-radius: 28px;
	  font-family: Arial;
	  color: #ffffff;
	  font-size: 10px;
	  background: #D8D8D8;
	  padding: 3px 7px 3px 7px;
	  text-decoration: none;
	  }';
	echo '</style>';
}

/**
* MAIN PAGE
*/
function forum_subscriptions_page() {
	global $wpdb;
	
	// Security check: only if user can publish_forums (standard Moderators and Keymasters)
	if (!bbp_current_user_can_publish_forums()) {
		echo __('Sorry, you do not have enough permissions', 'bbp-toolkit');
		return;
	}
	
	if (!(isset($_GET['forum_id']) || isset($_GET['user_id']) || isset($_GET['topic_id']))) {
		// Get forum list here to start
		echo '<h1>Manage Subscriptions</h1>';
		echo '<p>To manage subscriptions of a forum, <a href="' . site_url() . '/wp-admin/edit.php?post_type=forum' . '">edit the forums</a> and click on "Subscriptions" as an action of the forum, or edit the forum and find the "Manage Subscriptions" button (somewhere below the Forum Attributes).</p>';
		echo '<p>To manage subscriptions for topics, <a href="' . site_url() . '/wp-admin/edit.php?post_type=topic' . '">edit the topics</a> and find "Subscriptions" as an action for each topic.</p>';
		echo '<p>To manage subscriptions for a user, <a href="' . site_url() . '/wp-admin/users.php' . '">edit the users</a> and find "Subscriptions" as an action for each user.</p>';
		return;
	}
	
	if (isset($_GET['forum_id'])) {
		$forum_id = $_GET['forum_id'];
		if (bbp_is_forum_category($forum_id)) {
			echo '<h1>Manage Subscriptions</h1>';
			echo 'Categories do not have subscriptions...';
			return;
		}

		if (isset($_POST["bbptksubsubmit"])) {
			if ($_POST["bbptksubsubmit"] == 'Apply') {
				// POST form received for FORUM
				if ($_POST["action"] == 'Subscribe') {
					foreach ($_POST["bbptksubcb"] as $user_id) {
						if (!bbp_is_forum_category($_POST['forum_id'])) {
							bbp_add_user_subscription($user_id, $_POST['forum_id']);
						}
					}
				}
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bbptksubcb"] as $user_id) {
						bbp_remove_user_subscription($user_id, $_POST['forum_id']);
					}
				}		
			}
		}
		
		// Check page number from URL for forums
		$paged = 1;
		if (isset($_GET['paged'])) {
			$paged = $_GET['paged'];
		}


		// Check number of records per page from URL
		$number = 20;
		if (isset($_GET['number'])) {
			$number = $_GET['number'];
		}
		
		// Get users
		$args = array(
			'orderby' => 'display_name',
			'order' => 'ASC',
			'number' => $number,
			'paged' => $paged,
		);
		$all_users = get_users( $args );
		
		echo '<h1><b>Manage subscriptions</b> for forum: ' . bbp_get_forum_title($forum_id) . '</h1>'; 
		echo '<h3>' . bbp_get_forum_content($forum_id) . '</h3>'; 
		echo '<form action="" method="post" id="bbptksubform">';
		echo '<select name="action">';
		echo '<option value=""></option>';
		echo '<option value="Subscribe">Subscribe Selected Users</option>';
		echo '<option value="Unsubscribe">Unsubscribe Selected Users</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bbptksubsubmit" value="Apply" />';
		echo '<br><br>';
		
		echo '<table id="bbptksub-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bbptksubcbgroup_master" onchange="bbptksubtoggleall(this,\'bbptksubcbgroup\')" /></th><th><b>ID</b></th><th><b>Name</b></th><th><b>Subscriptions</b></th><th><b>Roles</b></th></tr>';
		$i = 0;
		$cap_with_prefix = $wpdb->prefix . 'capabilities';
		foreach ( $all_users as $user ) {
			$user_id = $user->ID;
			// Check subscription
			$is_subscribed = bbp_is_user_subscribed_to_forum($user_id, $forum_id);
			if ($is_subscribed) {
				$is_subscribed = '<button disabled class="subscr-yes-button">Subscribed</button>';
			} else {
				$is_subscribed = '&nbsp;&nbsp;<button disabled class="subscr-no-button">No subscr</button>';
			}
			// Get roles
			$caps = get_user_meta($user_id, $cap_with_prefix, true);
			$roles = array_keys((array)$caps);
			// User details
			$user_det_arr = array();
			if ($user->display_name) $user_det_arr[] = $user->user_login;
			if (($user->user_login) && ($user->user_login != $user->display_name)) $user_det_arr[] = $user->user_login;
			if (($user->user_nicename) && ($user->user_nicename!= $user->display_name)) $user_det_arr[] = $user->user_login;
			$user_det = implode(" - ", $user_det_arr);

			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bbptksubcbgroup" id="bbptksubcb_'.$user_id.'" name="bbptksubcb[]" value="' . $user_id . '"></td>';
			echo '<td>' . $user->ID . '</td><td>' . $user_det . '</td><td>' . $is_subscribed . '</td>';
			echo '<td>' . implode(", ", $roles) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="forum_id" value="' . $forum_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging
		$query = $_GET;
		$query['paged'] = $paged + 1;
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged > 1) {
			$query['paged'] = $paged - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text"> Prev Page </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo ' Page ' . $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text"> Next Page </span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
	}
	
	if (isset($_GET['topic_id'])) {
		$topic_id = $_GET['topic_id'];

		if (isset($_POST["bbptksubsubmit"])) {
			if ($_POST["bbptksubsubmit"] == 'Apply') {
				// POST form received for TOPIC
				if ($_POST["action"] == 'Subscribe') {
					foreach ($_POST["bbptksubcb"] as $user_id) {
						bbp_add_user_subscription($user_id, $_POST['topic_id']);
					}
				}
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bbptksubcb"] as $user_id) {
						bbp_remove_user_subscription($user_id, $_POST['topic_id']);
					}
				}		
			}
		}
		
		// Check page number from URL for forums
		$paged = 1;
		if (isset($_GET['paged'])) {
			$paged = $_GET['paged'];
		}


		// Check number of records per page from URL
		$number = 20;
		if (isset($_GET['number'])) {
			$number = $_GET['number'];
		}
		
		// Get users
		$args = array(
			'orderby' => 'display_name',
			'order' => 'ASC',
			'number' => $number,
			'paged' => $paged,
		);
		$all_users = get_users( $args );
		
		echo '<h1><b>Manage subscriptions</b> for topic: ' . bbp_get_topic_title($topic_id) . '</h1>'; 
		echo '<h3>' . bbp_get_topic_content($topic_id) . '</h3>'; 
		echo '<form action="" method="post" id="bbptksubform">';
		echo '<select name="action">';
		echo '<option value=""></option>';
		echo '<option value="Subscribe">Subscribe Selected Users</option>';
		echo '<option value="Unsubscribe">Unsubscribe Selected Users</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bbptksubsubmit" value="Apply" />';
		echo '<br><br>';
		
		echo '<table id="bbptksub-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bbptksubcbgroup_master" onchange="bbptksubtoggleall(this,\'bbptksubcbgroup\')" /></th><th><b>ID</b></th><th><b>Name</b></th><th><b>Subscriptions</b></th><th><b>Roles</b></th></tr>';
		$i = 0;
		$cap_with_prefix = $wpdb->prefix . 'capabilities';
		foreach ( $all_users as $user ) {
			$user_id = $user->ID;
			// Check subscription
			$is_subscribed = bbp_is_user_subscribed_to_topic($user_id, $topic_id);
			if ($is_subscribed) {
				$is_subscribed = '<button disabled class="subscr-yes-button">Subscribed</button>';
			} else {
				$is_subscribed = '&nbsp;&nbsp;<button disabled class="subscr-no-button">No subscr</button>';
			}
			// Get roles
			$caps = get_user_meta($user_id, $cap_with_prefix, true);
			$roles = array_keys((array)$caps);
			// User details
			$user_det_arr = array();
			if ($user->display_name) $user_det_arr[] = $user->user_login;
			if (($user->user_login) && ($user->user_login != $user->display_name)) $user_det_arr[] = $user->user_login;
			if (($user->user_nicename) && ($user->user_nicename!= $user->display_name)) $user_det_arr[] = $user->user_login;
			$user_det = implode(" - ", $user_det_arr);

			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bbptksubcbgroup" id="bbptksubcb_'.$user_id.'" name="bbptksubcb[]" value="' . $user_id . '"></td>';
			echo '<td>' . $user->ID . '</td><td>' . $user_det . '</td><td>' . $is_subscribed . '</td>';
			echo '<td>' . implode(", ", $roles) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="topic_id" value="' . $topic_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging
		$query = $_GET;
		$query['paged'] = $paged + 1;
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged > 1) {
			$query['paged'] = $paged - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text"> Prev Page </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo ' Page ' . $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text"> Next Page </span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
	}
	
	if (isset($_GET['user_id'])) {
		$user_id = $_GET['user_id'];

		if (isset($_POST["bbptksubsubmit"])) {
			if ($_POST["bbptksubsubmit"] == 'Apply') {
				// POST form received for FORUMS
				if ($_POST["action"] == 'Subscribe') {
					$forum_id = $_POST["bbptksubcb"];
					bbp_add_user_forum_subscription($user_id, $forum_id);
				}
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bbptksubcb"] as $forum_id) {
						bbp_remove_user_forum_subscription($user_id, $forum_id);
					}
				}		
			}
		}
		
		if (isset($_POST["bbptksubtopsubmit"])) {
			if ($_POST["bbptksubtopsubmit"] == 'Apply') {
				// POST form received for TOPICS
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bbptksubtopcb"] as $forum_id) {
						bbp_remove_user_topic_subscription($user_id, $forum_id);
					}
				}		
			}
		}
		
		// Check page number from URL for forums
		$paged = 1;
		if (isset($_GET['paged'])) {
			$paged = $_GET['paged'];
		}
		
		// Check number of records per page from URL
		$number = 20;
		if (isset($_GET['number'])) {
			$number = $_GET['number'];
		}
		
		$forum_ids = bbp_get_user_subscribed_forum_ids($user_id);
		// User details
		$user = get_userdata($user_id);
		$user_det_arr = array();
		if ($user->display_name) $user_det_arr[] = $user->user_login;
		if (($user->user_login) && ($user->user_login != $user->display_name)) $user_det_arr[] = $user->user_login;
		if (($user->user_nicename) && ($user->user_nicename!= $user->display_name)) $user_det_arr[] = $user->user_login;
		$user_det = implode(" - ", $user_det_arr);

		echo '<h1><b>Manage subscriptions</b> for user: ' . $user_det . '</h1>';
		echo '<h2>Remove Forum subscriptions</h2>';
		echo '<form action="" method="post" id="bbptksubform">';
		echo '<select name="action">';
		echo '<option value=""></option>';
		echo '<option value="Unsubscribe">'; _e('Unsubscribe from Selected Forums', 'bbp-toolkit'); echo '</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bbptksubsubmit" value="'; _e('Apply', 'bbp-toolkit'); echo '" />';
		echo '&nbsp;';
		
		// Table for FORUM unsubscribe
		echo '<br><br>';
		
		echo '<table id="bbptksub-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bbptksubcbgroup_master" onchange="bbptksubtoggleall(this,\'bbptksubcbgroup\')" /></th><th><b>ID</b></th><th><b>Forum Name</b></th><th><b>Forum Description</b></th></tr>';
		$i = 0;
		foreach($forum_ids as $forum_id) {
			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bbptksubcbgroup" id="bbptksubcb_'.$forum_id.'" name="bbptksubcb[]" value="' . $forum_id . '"></td>';
			echo '<td>' . $forum_id . '</td><td>' . bbp_get_forum_title($forum_id) . '</td><td>' . bbp_get_forum_content($forum_id) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="user_id" value="' . $user_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging
		$query = $_GET;
		$query['paged'] = $paged + 1;
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged > 1) {
			$query['paged'] = $paged - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text"> Prev Page </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo ' Page ' . $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text"> Next Page </span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
		echo '<br><br>';

		echo '<h2>Add Forum subscription</h2>';
		// Dropdown with forum
		if ( bbp_has_forums() ) {
			$forumarray = array();
			echo '<form action="" method="post">';
			echo 'Subscribe ' . $user_det . ' to ';
			echo '<select name="bbptksubcb">';
			echo '<option value=""></option>';
			while (bbp_forums() ) {
				bbp_the_forum();
				$forum_id = bbp_get_forum_id();
				$forum_title = bbp_get_forum_title($forum_id);
				echo '<option value="'.$forum_id.'">'.$forum_title.'</option>';
				array_push($forumarray, array($forum_id,$forum_title));
				$subf1 = bbp_forum_get_subforums($forum_id);
				if ($subf1) {
					foreach ($subf1 as $sub1forum) {
						$sub1_forum_id = $sub1forum->ID;
						$forum_title = '&nbsp;&nbsp;'.bbp_get_forum_title($sub1_forum_id);
						array_push($forumarray, array($sub1_forum_id,$forum_title));
						echo '<option value="'.$sub1_forum_id.'">'.$forum_title.'</option>';
					}
				}
			}
			echo '</select>&nbsp;&nbsp;<input type="submit" name="bbptksubsubmit" value="Apply" />';
			echo '<input type="hidden" name="action" value="Subscribe" />';
			echo '</form>';
		}

		echo '<h2>Remove Topic subscriptions</h2>';
		echo '<form action="" method="post" id="bbptksubtopform">';
		echo '<select name="action">';
		echo '<option value=""></option>';
		echo '<option value="Unsubscribe">'; _e('Unsubscribe from Selected Topics', 'bbp-toolkit'); echo '</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bbptksubtopsubmit" value="'; _e('Apply', 'bbp-toolkit'); echo '" />';
		echo '&nbsp;';
		
		// Table for TOPIC unsubscribe
		echo '<br><br>';
		$topic_ids = bbp_get_user_subscribed_topic_ids($user_id);
		
		echo '<table id="bbptksubtop-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bbptksubtopcbgroup_master" onchange="bbptksubtoggleall(this,\'bbptksubtopcbgroup\')" /></th><th><b>ID</b></th><th><b>Topic Name</b></th><th><b>Topic Extract</b></th></tr>';
		$i = 0;
		foreach($topic_ids as $topic_id) {
			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bbptksubtopcbgroup" id="bbptksubtopcb_'.$topic_id.'" name="bbptksubtopcb[]" value="' . $topic_id . '"></td>';
			echo '<td>' . $topic_id . '</td><td>' . bbp_get_topic_title($topic_id) . '</td><td>' . bbptoolkit_removehtml_and_cutwords(bbp_get_topic_content($topic_id) , 50) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="user_id" value="' . $user_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging

		// Check page number from URL for topics
		$paged_topics = 1;
		if (isset($_GET['paged_topics'])) {
			$paged_topics = $_GET['paged_topics'];
		}

		$query = $_GET;
		$query['paged_topics'] = $paged_topics + 1;
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged_topics > 1) {
			$query['paged_topics'] = $paged_topics - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text"> Prev Page </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo ' Page ' . $paged_topics . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text"> Next Page </span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
		echo '<br><br>';
	}
}
?>