<?php
 
  
 //********************************************start of user management

	 
	function pg_user_management($group) {
		//add the script for checkall ?>
		<script type="text/javascript" language="javascript">// <![CDATA[
		function checkAllPG(formname, checktoggle)
		{
		  var checkboxes = new Array(); 
		  checkboxes = document[formname].getElementsByTagName('input');
		 
		  for (var i=0; i<checkboxes.length; i++)  {
			if (checkboxes[i].type == 'checkbox')   {
			  checkboxes[i].checked = checktoggle;
			}
		  }
		}
		// ]]></script>
		<?php

		global $user_ID;
		global $rpg_groups ;
		global $rpg_disable_groups ;
		global $wp_roles;
		global $users ;
				
		//from settings.php we enter this with $group = 'all' so unless changed it will show all users
		
		//calls the set function to change bulk users to no-group
		if ( $_POST && 'no-group' == $_POST['action'] && wp_verify_nonce( $_POST['confirm-bulk-action-nonce'], 'confirm-bulk-action' ) ) :
			pg_no_group_users( $_POST['users'] );
			
		//calls the set function to change bulk users to a specific group
		elseif ( $_POST && 'group' == (substr($_POST['action'],0,5)) && wp_verify_nonce( $_POST['confirm-bulk-action-nonce'], 'confirm-bulk-action' )&& !empty($_POST['users']) ) :
			pg_group_users( $_POST['users'] );
			
		//then check if action 2 has a post, and if so extract the group from action 2 which will have filtergroupx
		elseif ((!empty($_POST['action2'])) && 'filter' == (substr($_POST['action2'],0,6)) ) :
		$group=substr($_POST['action2'],6,strlen($_POST['action2'])) ;
		//now check if filter has been set for roles
		elseif ((!empty($_POST['action3'])) && 'filter' !== (substr($_POST['action2'],0,6)) ) :
		$group = 'role' ;
		endif;
				
		//WHICH USERS TO SHOW?
		
		//if not been set, then $group will be 'all'
		if ($group=='all') $users= get_users () ;
		
		//if set to nogroup, then only show those
		if ($group == 'nogroup') $users = get_users( array( 'meta_key' => 'private_group','meta_compare' => 'NOT EXISTS'  )) ;
		
		//if set to all groups, then don't show any without a group set
		if ($group == 'allgroups') {
			$users = get_users( array( 'meta_key' => 'private_group','meta_compare' => '!=', 'meta_value' => ''  )) ;
			
		}
		//if a specific group, then only show that group
		if (substr($group,0,5) =='group') {
				$users1 = get_users( array( 'meta_key' => 'private_group', 'meta_value' => $group )) ;
				$filtercheck='*'.$group.'*' ;
				$users2 = get_users( array( 
					'meta_key' => 'private_group',
					'meta_value' => '*'.$group.'*',
					'meta_compare' => 'LIKE'
					)
				) ;
				$users=array_merge($users1,$users2);
		}
		
		//now check if filter has been set for roles
		if ($group == 'role')   {
		$role = $_POST['action3'] ;
		$users = get_users( array( 'meta_key' => 'wp_capabilities','meta_compare' => 'LIKE', 'meta_value' => $role )) ;
		}
		//adds the ability for an additional filter to change user display list
		global $filter_check ;
		do_action( 'pg_user_management_filter_results' );
		if (!empty ($_POST['action4'] )) $users = $filter_check ;
		
		?>

		<div class="icon32" id="icon-users"><br></div>
		<p> <b>
		<?php _e('Warning : The "bulk actions" CANNOT be used to set a multiple groups group against a user or users' , 'bbp-private-groups' ) ; ?></b> </p>
		<p> <b>
		<?php _e( 'Edit users at individual level if allocating multiple groups to users.' , 'bbp-private-groups' ) ; ?>
		</b></p>


		<form name="UserManagement" method="post">

			<?php wp_nonce_field( 'confirm-bulk-action', 'confirm-bulk-action-nonce' ) ?>

			<div class="tablenav top">
				<select name="action">
					<option value=""><?php _e( 'Bulk Actions', 'bbp-private-groups' ); ?></option>
					<option value="no-group"> <?php _e( 'No-Group', 'bbp-private-groups') ?></option>
					<?php
					//sets up the groups as actions
						$count=count ($rpg_groups) ;
						for ($i = 0 ; $i < $count ; ++$i) { 
							$g=$i+1 ;
							$name="group".$g ;
							$item=$name.'  '.$rpg_groups[$name] ;
							$display=__( 'Group', 'bbp-private-groups' ).$g.'  '.$rpg_groups[$name]  ;
							if (empty ($rpg_disable_groups[$name] ) ) {
								?>
								<option value="<?php echo $name ?>"><?php echo $display ?></option>
								<?php
							}								
						}
			?>
				</select>
				<input type="submit" value="<?php _e( 'Apply' , 'bbp-private-groups' ); ?>" class="button action doaction" name="">
				
				
				<select name="action2">
					<option value=""><?php _e( 'Filter user list by groups' , 'bbp-private-groups' ); ?></option>
					<option value="filterallgroups"> <?php _e( 'All Groups' , 'bbp-private-groups') ?></option>
					<option value="filternogroup"> <?php _e( 'No-Group-set' , 'bbp-private-groups' ) ?></option>
					<?php
					//sets up the groups as actions
						for ($i = 0 ; $i < $count ; ++$i) {
							$g=$i+1 ;
							$name="group".$g ;
							$name2="filtergroup".$g ;
							$item=$name.'  '.$rpg_groups[$name] ;
							$display=__( 'Group', 'bbp-private-groups' ).$g.'  '.$rpg_groups[$name]  ;
							
								?>
								<option value="<?php echo $name2 ?>"><?php echo $display ?></option>
								<?php								
						}
			?>
				</select>
			<input type="submit" value="<?php _e( 'Filter' , 'bbp-private-groups' ); ?>" class="button action doaction" name="" >
				
			<select name="action3">
					<option value=""><?php _e( 'Filter user list by roles' , 'bbp-private-groups' ); ?></option>
					<?php
					//sets up the wordpress roles
						$all_roles = $wp_roles->roles ;
						foreach($all_roles as $role=>$value) { 
						$name = $value['name'] ; 
						?>
						<option value="<?php echo $role ?>"><?php echo $name ?></option>
						<?php			
						}
			?>
				</select>
				<input type="submit" value="<?php _e( 'Filter' , 'bbp-private-groups' ); ?>" class="button action doaction" name="" >
				
			<?php do_action( 'pg_user_management_filter' ); ?>
			
				
				
				
				
				
			</div>

			<table class="widefat">
				<thead>
					<tr>
						<th id="cb-select-all-1">
						<a onclick="javascript:checkAllPG('UserManagement', true);" href="javascript:void();">check all</a> |
						<a onclick="javascript:checkAllPG('UserManagement', false);" href="javascript:void();">uncheck all</a>
						</th>
						<th id="gravatar"><?php _e( 'Gravatar', 'bbp-private-groups' ); ?></th>
						<th id="display_name"><?php _e( 'Name', 'bbp-private-groups' ); ?></th>
						<th id="private_group"><?php _e( 'Private Group(s)', 'bbp-private-groups' ); ?></th>
						<?php do_action( 'pg_user_management_column_head' ); ?>
						<th id="role"><?php _e( 'Wordpress & bbPress Roles', 'bbp-private-groups' ); ?></th>
						
						
					</tr>
				</thead>
				<tbody>
					<?php
					if ( $users ) :
						$i = 1;
						foreach ( $users as $user ) :
							$class = ( $i % 2 == 1 ) ? 'alternate' : 'default';
							$user_data = get_userdata( $user->ID );
							$user_registered = mysql2date(get_option('date_format'), $user->user_registered);
							$private_group = get_user_meta($user->ID, 'private_group', true); 
							?>
							<tr id="user-<?php echo $user->ID ?>" class="<?php echo $class ?>">
								<th>
									<?php if ( $user->ID != $user_ID ) :?>
										<input type="checkbox" name="users[]" value="<?php echo $user->ID ?>">
									<?php endif; ?>
								</th>
								<td><img class="gravatar" src="http://www.gravatar.com/avatar/<?php echo md5( $user->user_email ) ?>?s=32"></td>
								<td>
									<a href="user-edit.php?user_id=<?php echo $user->ID ?>"><?php echo $user->display_name ?></a>
									<div class="row-actions">
										<?php if ( current_user_can( 'edit_user',  $user->ID ) ) : ?>
											<span class="edit"><a href="<?php echo admin_url( 'user-edit.php?user_id=' . $user->ID  ) ?>"><?php _e( 'Edit', 'bbp-private-groups' ); ?></a>
										<?php endif; ?>
										<?php if ( current_user_can( 'edit_user',  $user->ID ) && current_user_can( 'delete_user', $user->ID ) && $user_ID != $user->ID ) : ?>
											&nbsp;|&nbsp;</span>
										<?php endif; ?>
										<?php if ( current_user_can( 'delete_user', $user->ID ) && $user_ID != $user->ID ) : ?>
											<span class="delete"><a href="<?php echo admin_url( 'users.php?action=delete&user=' . $user->ID . '&_wpnonce=' . wp_create_nonce( 'bulk-users' ) ) ?>"><?php _e( 'Delete' ); ?></a></span>
										<?php endif; ?>
									</div>
								</td>
								
								<td><?php
								//if no groups
								if ($private_group == '') _e ('no group set', 'bbp-private-groups') ;
								//if multiple groups
								elseif (strpos($private_group, '*')!== FALSE) {
									foreach ( $rpg_groups as $group => $details ) {
										if (strpos($private_group, '*'.$group.'*') !== FALSE) {
										$groupname=__('Group','bbp-private-groups').substr($group,5,strlen($group)) ;
										echo $groupname." ".$details.'<br>' ; 
										}
									}
								}
								//if only one group
								
								else {
								$groupname=__('Group','bbp-private-groups').substr($private_group,5,strlen($private_group)) ;
								echo $groupname.' '.$rpg_groups[$private_group] ;
								}
								?>
								</td>
								<?php 
								global $user_action_id ;
								$user_action_id = $user->ID ;
								do_action( 'pg_user_management_column_entry' ); 
								?>
								
								<td>
									<?php
									if ( $user_data->roles ) :

										foreach ( $user_data->roles as $role ) :
										if ((substr($role,0,4)) == 'bbp_') $role = substr($role,4,strlen($role)) ;    

											echo _x( ucfirst( $role ), 'bbp-private-groups' ) . '<br>';

										endforeach;

									endif;
									?>
								</td>
								
								
							</tr>
							<?php
							$i++;
						endforeach;

					else :

						?>
						<tr>
							<td colspan="6"><strong><?php _e( 'No Users found', 'bbp-private-groups' ); ?></strong></td>
						</tr>
						<?php

					endif;
					?>
				</tbody>
			</table>

		</form>
		<?php
	}


/**
	 * Bulk no group users
	 * changes users to no-group
	 **/
	function pg_no_group_users( array $user_ids ) {
		$user = wp_get_current_user();
		if ( $user_ids && current_user_can( 'edit_user',  $user->ID ) ) :

			foreach ( $user_ids as $user_id ) :

				if ( is_numeric( $user_id ) ) :
					
				delete_user_meta( $user_id, 'private_group' ) ;
				//then update subscriptions
				if (get_option( '_bbp_enable_subscriptions' )) rpg_amend_subscriptions ($user_id) ;
				endif;

			endforeach;

			?>
			<div class="updated message">
				<?php if ( 1 == count( $user_ids) ) : ?>
					<p><?php _e( '1 user amended', 'bbp-private-groups' ) ?></p>
				<?php else : ?>
					<p><?php echo count( $user_ids ) .  ' ' . __( 'users amended', 'bbp-private-groups' ) ?></p>
				<?php endif; ?>
			</div>
			<?php

		endif;
	}
	
	/**
	 * Bulk group users
	 * changes users to a group
	 **/
	function pg_group_users( array $user_ids ) {
		$user = wp_get_current_user();
		if ( $user_ids && current_user_can( 'edit_user',  $user->ID ) ) :

			foreach ( $user_ids as $user_id ) :

				if ( is_numeric( $user_id ) ) :
					
				update_user_meta( $user_id, 'private_group', $_POST['action']);
				//then update subscritions
				if (get_option( '_bbp_enable_subscriptions' )) rpg_amend_subscriptions ($user_id) ;
				endif;

			endforeach;

			?>
			<div class="updated message">
				<?php if ( 1 == count( $user_ids) ) : ?>
					<p><?php _e( '1 user amended', 'bbp-private-groups' ) ?></p>
				<?php else : ?>
					<p><?php echo count( $user_ids ) .  ' ' . __( 'users amended', 'bbp-private-groups' ) ?></p>
				<?php endif; ?>
			</div>
			<?php

		endif;
}