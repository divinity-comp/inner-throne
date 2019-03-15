<?php

// User profile edit/display actions
add_action( 'edit_user_profile', 'rpg_user_profile_field', 50,2 )  ;
add_action( 'edit_user_profile_update', 'bbp_edit_user_pg' );


function rpg_user_profile_field() {
	 global $current_user;
	 global $rpg_groups ;
	 global $rpg_disable_groups ;
		
		
	 if (isset($_REQUEST['user_id'])) {
		$user_id = $_REQUEST['user_id'];
	 } else {
		$user_id = bbp_get_displayed_user_id(); 
	 }
		?>
	 <table class="form-table">
			<tbody>
				<tr>
					<th><label for="bbp-private-groups"><?php esc_html_e( 'Private Groups', 'bbp-private-groups' ); ?></label></th>
					<td>

						<?php global $rpg_groups ;
							if (empty( $rpg_groups ) ) : ?>
							
								<option value=""><?php esc_html_e( '&mdash; No groups yet set up &mdash;', 'bbp-private-groups' ); ?></option>

								<?php 
								else : ?>
							
							
							<!-- checkbox to activate -->
								<?php $private_group = get_user_meta($user_id, 'private_group', true); ?>
				
					
					<?php 
					foreach ( $rpg_groups as $group => $details ) : ?>
						<?php 
						//if not disabled then show...
						if (empty($rpg_disable_groups[$group])) {
						?>
							<tr valign="top">  
								<?php $groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ; ?>
								<th>
									<?php echo $groupname." ".$details ; ?>
								</th>
								
								<td>
									<?php
									$check=0 ;
									if (strpos($private_group, '*'.$group.'*') !== FALSE) $check=1 ;
									elseif($private_group == 	$group) $check=1 ;
										echo '<input name="'.$group.'" id="group" type="checkbox" ' ;
									if( $check ) echo 'checked="checked"'; 
									echo ' />' ;
									_e ('Click to add this group', 'bbp-private-groups' );
									?>
								</td>
							</tr>
					<?php
						}
					endforeach; ?>
				<?php 
				endif;
				?>
							

			</select>
			</td>
		</tr>

	</tbody>
		</table>
		<?php
		
		
		}
		
		
		
function bbp_edit_user_pg( $user_id ) {
	global $rpg_groups ;
	$string='*' ;
		foreach ( (array) $rpg_groups as $pggroup => $details) { 
		$item='private_group_'.$pggroup ;
		$data = (!empty ($_POST[$pggroup]) ? $_POST[$pggroup] : '' );
		//$data = ($_POST[$pggroup] );
			if ($data=='on') {
			$string=$string.$pggroup.'*' ;
		}
	}
	if ($string=='*') $string = '' ;
	update_user_meta( $user_id, 'private_group', $string);
	//then update subscriptions
	if (get_option( '_bbp_enable_subscriptions' )) rpg_amend_subscriptions ($user_id) ;
}
	
Function rpg_amend_subscriptions ($user_id){
	//this function removes forums and topics subscription on group changes
	//eg on a group change, a user may have subscriptions to forums or topics which they no longer should have access to.
	
	//first forums
	$bbp_subscription_forums = bbp_get_user_subscribed_forum_ids ($user_id) ;
	foreach ($bbp_subscription_forums as $forum_id) {
				if (!private_groups_can_user_view_post( $user_id, $forum_id)) {
						bbp_remove_user_forum_subscription( $user_id, $forum_id ); 
					}
	}
	//then topics
	$bbp_subscription_topics = bbp_get_user_subscribed_topic_ids ($user_id) ;
	foreach ($bbp_subscription_topics as $topic_id) {
		$forum_id = private_groups_get_forum_id_from_post_id($topic_id ) ;
				if (!private_groups_can_user_view_post( $user_id, $forum_id)) {
					bbp_remove_user_topic_subscription( $user_id, $topic_id ); 
					}
	}
	
}