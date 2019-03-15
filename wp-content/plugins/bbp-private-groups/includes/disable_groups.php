<?php

 //************************* Disable Groups *************************// 
 
Function rpg_disable_groups() { 
global $rpg_groups ;
global $rpg_disable_groups ;

?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'disable_groups', 'disable-nonce' ) ?>
	<?php settings_fields( 'rpg_disable_groups' ); ?>
		<p>
			<?php _e('This plugin does not allow for the deletion of groups - sorry, but that is just the way it was written.' , 'bbp-private-groups' ) ; ?>
		</p>
		<p>
			<?php _e('However this section allows you to disable groups <b> which have no forums or users associated with them </b>.' , 'bbp-private-groups' ) ; ?>
		</p>
		<p>
			<?php _e('Tick a group, and this group will not be visible.  Untick a group and you can make it visible and re-use that group.' , 'bbp-private-groups' ) ; ?>
		</p>
				
			<?php 
			
			//only display if no forums or users are associated with it.
			
			//start with a list of groups and set them to false
			$count=count ($rpg_groups) ;
			for ($i = 0 ; $i < $count ; ++$i) {
				$g=$i+1 ;
				$name="group".$g ;
				//set $check for the group number  = false, so we can safely disbale if still false at the end
				$check[$name] = false ;
				
			}
			
			//now go through the users to create a list of groups that are active
				$users= get_users () ;
					if ( $users ) :
						foreach ( $users as $user ) :
							$private_group = get_user_meta($user->ID, 'private_group', true); 
							if (!empty ($private_group)) {
								//if multiple groups
								if (strpos($private_group, '*')!== FALSE) {
									foreach ( $rpg_groups as $group => $details ) {
										if (strpos($private_group, '*'.$group.'*') !== FALSE) {
										$check[$group] = true ;
										}
									}
								}
								//if only one group
								else {
								$check[$private_group] = true ;
								}
							}
						endforeach ;
					endif ;
					
					
			//now go through forums	
			global $wpdb;
			$forum=bbp_get_forum_post_type() ;
			$forums =$wpdb->get_col("select ID from $wpdb->posts where post_type = '$forum'") ;

			update_option ('rew' , $forums ) ;
			foreach ( $forums as $forum ) :
					//cycle through each forum
					$meta = (array)get_post_meta( $forum, '_private_group', false );
						if (!empty ($meta)) {
							foreach ($meta as $group ) :
							//set check = true for each group set in this forum
								$check[$group] = true ;
							endforeach ;
						}
				endforeach ;

				
			?>
			<table class="form-table">		
			<?php	
			$count=count ($rpg_groups) ;
			for ($i = 0 ; $i < $count ; ++$i) {
				$g=$i+1 ;
				$name="group".$g ;
				//only display if false
				if ($check[$name] == false) {
				
				
				$display=__( 'Group', 'bbp-private-groups' ).$g ;
				$groupname=__('Group','bbp-private-groups').substr($name,5,strlen($name)) ;
				$groupdisplay = $rpg_groups[$name] ;
				
					$item="rpg_groups[".$name."]" ;
					$value = (!empty ($rpg_groups[$name]) ? $rpg_groups[$name] : '' ) ;
					?>
					<!-------------------------  Group  --------------------------------------------->		
					<tr valign="top">
					<td>
					<?php echo $groupname.' '.$groupdisplay ; ?>
					
					</td>
					<td>
						<?php
						$item1="rpg_disable_groups[".$name."]" ;
						$item2 = (!empty( $rpg_disable_groups[$name] ) ?  $rpg_disable_groups[$name] : '');
						echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$item2, false ) . ' />';
						_e ('Disable this Group' , 'bbp-private-groups' );
						?>
					</td>
					<td>
					</td>
				</tr>
				<?php 
				}
			
					
				
			}
					 			
?>		
					
		</table>
<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Groups', 'bbp-private-groups' ); ?>" />
	</p>
</form>
		
		
</div><!--end sf-wrap-->
</div><!--end wrap-->
	
<?php
}
?>