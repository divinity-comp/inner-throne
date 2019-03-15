<?php //************************* Management Info *************************// 
			function management_info () {
			?>
			<?php 
			global $wpdb; 
			global $rpg_disable_groups ;
			global $rpg_groups ;
			global $rpg_topic_permissions ;
			?>	
			<?php settings_fields( 'rpg_group_settings' ); ?>
			
			<table class="form-table">
			
				<?php 
				$count=count ($rpg_groups) ;
				for ($i = 0 ; $i < $count ; ++$i) {
					$g=$i+1 ;
					$display=__( 'Group', 'bbp-private-groups' ).$g ;
					$name="group".$g ;
					$item="rpg_groups[".$name."]" ;
					
				?>
			<!-------------------------  Group  --------------------------------------------->		
						<tr valign="top">
							<th>
								<?php echo $display ;
								if (!empty($rpg_disable_groups[$name])) {
								echo ' <i>' ;
								_e('Disabled', 'bbp-private-groups') ;
								echo '</i>' ;
								}
								?>
							</th>
							
							<td>
								<?php _e('Group name :', 'bbp-private-groups') ; ?>
								<?php $role_name = (!empty($rpg_groups[$name]) ? $rpg_groups[$name] : ''); ?>
								<?php echo $role_name.'<br>' ; ?>
								<?php _e('No. users in this group : ' , 'bbp-private-groups') ; ?>
								<?php 
								$users=$wpdb->get_col("select ID from $wpdb->users") ;
								$countu=0 ;
								foreach ($users as $user) {
					
									$check=  get_user_meta( $user, 'private_group',true);
									//single user check
									if ($check==$name) $countu++ ;
									//multiple group set
									if (strpos($check, '*'.$name.'*') !== FALSE) $countu++;
								}
								echo $countu ;
								?>
					

								<br>
								<?php _e('Forums in this group :' , 'bbp-private-groups') ; ?>
								<?php 
								$forum = bbp_get_forum_post_type() ;
								$forums=$wpdb->get_col("select ID from $wpdb->posts where post_type='$forum'") ;
								$countu=0 ;
								echo '<ul><i>' ;
								foreach ($forums as $forum) {
									$meta = (array)get_post_meta( $forum, '_private_group', false );
									foreach ($meta as $meta2) {
										if ($meta2==$name) {
											$ftitle=bbp_forum_title($forum) ;
											echo '<li>'.$ftitle.'</li>' ;
											$countu++ ;
											}
									}
								
								}
								echo '</ul></i>' ;
					
								_e('No. forums that have this group set : ', 'bbp-private-groups' ) ;
								echo $countu ;
								?>
							</td>
						</tr>
	
				
						<!-------------------------  FORUMS  --------------------------------------------->	
<?php } 
	?>
	</table>
	<table>
	<tr>
	</tr>
	
	<tr>
		<th>
			<?php echo 'Forums' ?> 
		</th>
		<th>
			<?php echo 'Groups' ?> 
		</th>
		<?php 
		
		if (!empty ($rpg_topic_permissions) ) echo '<th>Topic Permissions</th>' ; ?>
	</tr>
	
	<?php if ( bbp_has_forums() ) : 

		while ( bbp_forums() ) : bbp_the_forum(); 
		?>
		<tr>
			<td style="vertical-align:top">
				<?php bbp_forum_title() ; ?>
			</td>
			
			<td>
			<?php 			
			$id = get_the_ID () ;
			$meta = get_post_meta( $id, '_private_group', false );
			foreach ( $rpg_groups as $group => $details ) {
				if ( is_array( $meta ) && in_array( $group, $meta ) ) { 
					$groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ;
					$tp = '_private_group_'.$group ;
					$perm = get_post_meta($id, $tp, true) ;
					$value =  (!empty ($perm ) ? $perm : '4' );
			
					$valuename1 = __('Only View Topics/Replies', 'bbp-private-groups') ;
					$valuename2 = __('Create/Edit Topics (and view all topics/replies)', 'bbp-private-groups') ;
					$valuename3 = __('Create/Edit Replies (and view all topics/replies)', 'bbp-private-groups') ;
					$valuename4 = __('Create/Edit Topics and Replies (and view all topics/replies)', 'bbp-private-groups') ;
					$valuename5 = __('Create/Edit/view OWN Topics, create/edit Replies to those topics and view any Replies to those topics', 'bbp-private-groups') ;
					$valuex = 'valuename'.$value ;
					$valuename = $$valuex ;
					echo $groupname.'  '.$details ;
					if (!empty ($rpg_topic_permissions) ) echo '<td>'.$valuename.'</td>' ;
					echo '</td></tr><tr><td></td><td>';
				} // end of if is in array $meta
			}  // end of foreach $rpg groups
			//now display non logged in users
			$valuename0 = __('No visibility or access to this forum', 'bbp-private-groups') ;
			if (bbp_allow_anonymous()) {	
			
			$desc = __('Non-logged in & Anonymous Users', 'bbp-private-groups'); 
			}
			else {
			$desc = __('Non-logged in users', 'bbp-private-groups'); 
			}
			echo '<tr><td></td><td>' ;
			echo $desc ;
			echo '</td><td>' ;
			$perm = get_post_meta($id ,'_private_group_nonloggedin', true) ;
			$value =  (!empty ($perm ) ? $perm : '0' );
			$valuex = 'valuename'.$value ;
			$valuename = $$valuex ;
			echo $valuename ;
			?>
			</td>
		</tr>
			
		<?php 
		$sub_forums = bbp_forum_get_subforums() ; 
		if (!empty ($sub_forums) ) {
			foreach ( $sub_forums as $sub_forum ) {
				$id = $sub_forum->ID ;
				$title     = bbp_get_forum_title( $sub_forum->ID );
				echo '<tr><td style="vertical-align:top"><i>'.$title.'</i></td><td>' ; ?>
				<?php global $rpg_topic_permissions ;
				$meta = get_post_meta( $sub_forum->ID, '_private_group', false );
				foreach ( $rpg_groups as $group => $details ) {
					if ( is_array( $meta ) && in_array( $group, $meta ) ) { 
						$groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ;
						$tp = '_private_group_'.$group ;
						$perm = get_post_meta($id, $tp, true) ;
						$value =  (!empty ($perm ) ? $perm : '4' );
						$valuename1 = __('Only View Topics/Replies', 'bbp-private-groups') ;
						$valuename2 = __('Create/Edit Topics (and view all topics/replies)', 'bbp-private-groups') ;
						$valuename3 = __('Create/Edit Replies (and view all topics/replies)', 'bbp-private-groups') ;
						$valuename4 = __('Create/Edit Topics and Replies (and view all topics/replies)', 'bbp-private-groups') ;
						$valuename5 = __('Create/Edit/view OWN Topics, create/edit Replies to those topics and view any Replies to those topics', 'bbp-private-groups') ;
						$valuex = 'valuename'.$value ;
						$valuename = $$valuex ;
						echo $groupname.'  '.$details ;
						if (!empty ($rpg_topic_permissions) ) echo '<td>'.$valuename.'</td>' ;
						echo '</td></tr><tr><td></td><td>';
					} // end of if is in array $meta
				}  // end of foreach $rpg groups
			?>
			</td>
		</tr>
			<?php
		
		} //end of foreach sub forums
		} //end of !empty sub forums
		?>
		
		
		
		
		
		<?php
		endwhile; 

	endif; 
	
	
						
			
				
				?>
													
	</table>
					
	</form>
	</div><!--end sf-wrap-->
	</div><!--end wrap-->
	
<?php
}