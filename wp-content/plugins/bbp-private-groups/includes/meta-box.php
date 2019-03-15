<?php
/**
 * This adds the meta box for the private groups
 */
/** 
 */

add_action( 'admin_menu', 'private_groups_create_meta_box' );

/* Saves the content permissions metabox data to a custom field. */
add_action( 'save_post', 'private_groups_save_meta', 1, 2 );


function private_groups_create_meta_box() {
	add_meta_box( 'forum-group-meta-box', __( 'Forum Groups' , 'bbp-private-groups') , 'private_groups_meta_box', 'forum', 'normal', 'high' );
}

/**
 * Controls the display of the content permissions meta box.  This allows users
 * to select groups that should have access to an individual post/page.
 */
 
function private_groups_meta_box( $object, $box ) {
	global $post ?>

	<input type="hidden" name="private_groups_meta_nonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
	
	<p>
		<label for="groups"><?php _e('<strong>Groups:</strong> Restrict the content to these groups on the front end of the site.  If all boxes are left unchecked, everyone can view the content.', 'bbp-private-groups'); ?></label>
	</p>

	<div style="overflow: hidden;">
	

		<?php

		/* Get the meta*/
		
		$meta = get_post_meta( $post->ID, '_private_group', false );
		global $rpg_groups ;
		global $rpg_disable_groups ;

		/* Loop through each of the available roles. */
		if(empty($rpg_groups)) {
			echo _e('<b>No groups have yet been set up - go to Dashboard>Settings>bbp Private Groups to set</b>', 'bbp-private-groups') ; 
		}
		else {
			foreach ( $rpg_groups as $group => $details ) {	
				//if it isn't disabled...
				if (empty ( $rpg_disable_groups [$group] )) {
					$checked = false;
		
					/* If the role has been selected, make sure it's checked. */
					if ( is_array( $meta ) && in_array( $group, $meta ) ) $checked = ' checked="checked" '; ?>
						<p style="width: 32%; float: left; margin-right: 0;">
							<label for="group-<?php echo $group; ?>">
								<?php $groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ; ?>
								<input type="checkbox" name=<?php echo $group ; ?> id=<?php echo $group ; ?> <?php echo $checked; ?> value="<?php echo $group; ?>" /> 
								<?php echo $groupname." ".$details ; ?>
							</label>
						</p>
				<?php
				}
			}
		?>
		<?php
		global $rpg_topic_permissions ;
		if (!empty ($rpg_topic_permissions['activate']) ) {
			?>
			<div>
			<label for="group_topics"><?php _e('<strong>Group Topic Permsissions:</strong> <i> These will appear for any groups set for this forum when you publish or update this forum.</i>', 'bbp-private-groups'); ?></label></p>
			<p>
				<label for="group_topics"><?php _e('For each group, you can set whether group members are able to view, create topics and replies.', 'bbp-private-groups'); ?></label>
			</p>
			<p>
				<label for="group_topics"><?php _e('Please see the <a href="/wp-admin/options-general.php?page=bbp-private-group-settings&tab=topic_permissions" target="_blank">Topic Permissions settings</a> to understand the impact of this', 'bbp-private-groups'); ?></label>
			</p>
	<table>
	<?php foreach ( $rpg_groups as $group => $details ) { ?>
			
			<?php
			
			//if this group is selected
			if ( is_array( $meta ) && in_array( $group, $meta ) ) { 
			//set up $test, so we can see if we need to display non-logged in section below, it will be null unless at least one group is set, otherwise it looks like 
			//an open forum with no grouops can;t be seen by non-logged in, which of course isn't true.
			$test = true ;
			$groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ;
			$tp = '_private_group_'.$group ;
			$valuename1 = __('Only View Topics/Replies', 'bbp-private-groups') ;
			$valuename2 = __('Create/Edit Topics (and view all topics/replies)', 'bbp-private-groups') ;
			$valuename3 = __('Create/Edit Replies (and view all topics/replies)', 'bbp-private-groups') ;
			$valuename4 = __('Create/Edit Topics and Replies (and view all topics/replies)', 'bbp-private-groups') ;
			$valuename5 = __('Create/Edit/view OWN Topics, create/edit Replies to those topics and view any Replies to those topics', 'bbp-private-groups') ;
			$perm = get_post_meta($post->ID , $tp, true) ;
			$value =  (!empty ($perm ) ? $perm : '4' );
			$valuex = 'valuename'.$value ;
			$valuename = $$valuex ;
			?>	

			<tr>
			<td>
			</td>
			<td>
			</td>
			<td style ="width:150px">
			<?php echo _e('<b>For this forum...</b>', 'bbp-private-groups'); ?> 
			</td>
			</tr>
			<tr>
			<td> <?php echo $groupname ; ?> </td>
			<td style ="width:250px"> <?php echo $details ; ?> </td>
			<td style ="width:150px">
			<select name="<?php echo $tp ; ?>">
			<?php echo '<option value="'.esc_html( $value).'">'.esc_html( $valuename) ; 
			 
			if ($value != 4) echo '<option value="4">'.$valuename4.'</option>' ; 
			if ($value != 2) echo '<option value="2">'.$valuename2.'</option>' ; 
			if ($value != 3) echo '<option value="3">'.$valuename3.'</option>' ; 
			if ($value != 5) echo '<option value="5">'.$valuename5.'</option>' ; 
			if ($value != 1) echo '<option value="1">'.$valuename1.'</option>' ; 
			?>
			
			
			</select>
			</td>
			</tr>
			
			<?php
							
			} // end of if is array
				
		} // end of foreach
		
		//now we'll work out if we need to allow non-logged in /anonymous viewing or posting
		
		if (!empty($test)) {
		//if $test is not true, then we don't want to display this, as it looks like we are setting to no view
		?>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td colspan=2>
			<?php 	echo _e('<b>For this forum...</b>', 'bbp-private-groups'); ?>
			
				
			</td>
			</tr>
		<tr>
			<?php if (bbp_allow_anonymous()) {	
			$desc = __('Non-logged in users & Anonymous Users', 'bbp-private-groups'); 
			}
			else {
			$desc = __('Non-logged in users', 'bbp-private-groups'); 
			} ?>
			<td colspan=2>
				<?php echo $desc; ?> 
				<?php $name =  '_private_group_nonloggedin' ;
				$item = get_post_meta( $post->ID, '_private_group_nonloggedin', true ); 
				if (empty ($item)) $item=0 ;
				?>
			</td>
			
			<?php
		
			//set up the anon paramters
			$tp = '_private_group_nonloggedin' ;
			$valuename0 = __('No visibility or access to this forum', 'bbp-private-groups') ;
			$valuename1 = __('Only View Topics/Replies', 'bbp-private-groups') ;
			$valuename2 = __('Create/Edit Topics (and view all topics/replies)', 'bbp-private-groups') ;
			$valuename3 = __('Create/Edit Replies (and view all topics/replies)', 'bbp-private-groups') ;
			$valuename4 = __('Create/Edit Topics and Replies (and view all topics/replies)', 'bbp-private-groups') ;
			$perm = get_post_meta($post->ID , $tp, true) ;
			$value =  (!empty ($perm ) ? $perm : '0' );
			//now reset value to just view if anon posting not allowed, but this has been set higher previously
			if (!bbp_allow_anonymous()) if ($value>1) $value = 1 ;
			$valuex = 'valuename'.$value ;
			$valuename = $$valuex ;
			?>
			
			
			<td style ="width:250px">
			<select name="<?php echo $tp ; ?>">
			<?php echo '<option value="'.esc_html( $value).'">'.esc_html( $valuename) ; 
			if ($value != 0) echo '<option value="0">'.$valuename0.'</option>' ; 
			if ($value != 1) echo '<option value="1">'.$valuename1.'</option>' ; 
			if (bbp_allow_anonymous()) {	
				if ($value != 2) echo '<option value="2">'.$valuename2.'</option>' ; 
				if ($value != 3) echo '<option value="3">'.$valuename3.'</option>' ; 
				if ($value != 4) echo '<option value="4">'.$valuename4.'</option>' ; 
			}
		} //end of if $test
			
			?>
			
			
			</select>
			</td>
			
		
		</tr>
		
		
		</table>
		</div>
		
		<?php }  // end of if $rpg_topic_permissions
		
		} //end of else ?>
	</div>
	 
	<?php
}
/**
 * Saves the content permissions metabox data to a custom field.
 *
 */
function private_groups_save_meta( $post_id, $post ) {
	//bail of not a forum
	if ( bbp_get_forum_post_type() != $post->post_type ) 
		return;
	
	//bail if this creating a new forum and we are entering, so we don't have a post_id yet !
	if (empty ($post_id))
		return ;
	
	/* Only allow users that can edit the current post to submit data. */
	if ( 'post' == $post->post_type && !current_user_can( 'edit_posts', $post_id ) )
		return;

	/* Only allow users that can edit the current page to submit data. */
	elseif ( 'page' == $post->post_type && !current_user_can( 'edit_pages', $post_id ) )
		return;

	/* Don't save if the post is only a revision. */
	if ( 'revision' == $post->post_type )
		return;
	
	global $rpg_groups ;
	/* Loop through each of the site's available groups. */
	if(!empty($rpg_groups)) {
		
		foreach ( $rpg_groups as $group => $details ){	
		//Set topic permissions for this group
			$tp = '_private_group_'.$group ;
			if (!empty ($_POST[$tp]) ) update_post_meta( $post_id, $tp, $_POST[$tp], false );
		
			/* Get post metadata for the custom field key 'group'. */
			$meta = (array)get_post_meta( $post_id, '_private_group', false );
		      
			/* Check if the group was selected. */
			if ( !empty ($_POST[$group] )){
			
				/* If selected and already saved, continue looping through the groups and do nothing for this group. */
				if ( in_array( $group, $meta ) )
					continue;

				/* If the group was selected and not already saved, add the group as a new value to the 'group' custom field. */
				else
					add_post_meta( $post_id, '_private_group', $group, false );
			}

			/* If role not selected, delete. */
			else {
				delete_post_meta( $post_id, '_private_group', $group );
				//and delete topic permissions also
				delete_post_meta( $post_id, $tp);
				//now if we are deleting a group that was previously set, then we need to set a check for later
				if ( in_array( $group, $meta ) ) {
					$subs_check = true ;
				}
			}
				
		} // End of foreach
		
		//now set nonlogged in permissions
		if ($_POST['_private_group_nonloggedin'] ) {
			update_post_meta( $post_id, '_private_group_nonloggedin', $_POST['_private_group_nonloggedin'], false );
		}
		else //delete
			delete_post_meta( $post_id, '_private_group_nonloggedin');
		
		//now if we need to alter subscriptions
		if ( $subs_check == true ) {
			//go through all users and unset any subscriptions for this forum for those users
			$users= get_users () ;
				//loop through all users
				foreach ( $users as $user ) {
					//and within each 
					$user_id = $user->ID ;
					rpg_amend_subscriptions ($user_id) ;
				}
		}
		
	
	}
}
?>