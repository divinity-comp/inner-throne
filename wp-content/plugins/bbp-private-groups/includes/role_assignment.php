<?php //************************* Group name settings *************************// 
Function pg_role_assignment () { ?>
			<?php global $rpg_roles;
				global $wp_roles;
				global $rpg_groups ;
				global $rpg_disable_groups ;
				$all_roles = $wp_roles->roles ;
				
?>
			<form method="post" action="options.php">
			
			<?php settings_fields( 'rpg_roles_settings' ); ?>
			<h2> 
				<?php _e ('Assign Groups to Roles' , 'bbp-private-groups' ) ; ?>
			</h2>
			<p>
				<?php _e ('This section is optional and designed to allow those of you who use membership plugins etc. to assign a group against a wordpress or custom role.', 'bbp-private-groups') ; ?>
			</p>
			
			
			<p>
			<strong>
				<?php _e ('Please read these notes carefully to understand what this does!' , 'bbp-private-groups' ) ; ?>
			</strong>
			</p>
			<p> 
				<?php _e ( 'By entering a group against a role, new users with that role will be allocated the group automatically as part of the Wordpress registration process.', 'bbp-private-groups'); ?> 
			</p>
				
				
				
			<p>
				<?php _e ('This section works on a "one-role one-group" basis - that is every role will only have a single group assigned (which of course can have multiple forums).' , 'bbp-private-groups' ) ; ?>
			</p>
			<p>
				<?php _e ('For instance you may want all wordpress authors to automatically belong to a particular group, or you may have created a role called "member" in your membership plugin, and want to automatically give these access to a private group called say membership.' , 'bbp-private-groups') ; ?>
			</p>
					
			<p>	
				<?php _e ('You can subsequently change individual users, and (unless you have selected login or assign on every visit options - see below) elements in this tab will only affect new registrations, not change anyone who has already registered.', 'bbp-private-groups'); ?>
			</p>
			<p> 
				<?php _e ( 'Additionally you can assign roles on login or every visit - see each element for details', 'bbp-private-groups'); ?> 
			</p>		
			<p>
				<?php _e ('This section only applies to wordpress or custom or membership roles, NOT bbpress roles !' , 'bbp-private-groups' ) ; ?>
			</p>
					
				
			
			<table class="form-table">
			
			<!-- checkbox to activate login -->
			<tr valign="top">  
				<th colspan=2>
					<hr>
					<?php _e('Add group on first or no-group login', 'bbp-private-groups'); ?>
				</th>
			
			<tr valign="top">
				<td colspan=2>
				<p>
					<?php _e('You can optionally select to assign roles on login for users <strong>who do not have a role assigned</strong>. This is useful where your membership plugin does not use Wordpress registration, so registration does not work! ', 'bbp-private-groups'); ?>
				</p>
				<p>
					<?php _e('You can assign a <i> different </i> role to individual users manually, but if you change an individual user to have no groups, then the group for that role will be assigned on next login as the plugin sees this as blank!', 'bbp-private-groups'); ?>
				</p>
				</td>
			</tr>
					
			<tr valign="top">  
				<td>
					<?php _e('Add group on first or no-group login', 'bbp-private-groups'); ?>
				</td>
				
				<td>
					<?php
					$item = (!empty( $rpg_roles['login'] ) ?  $rpg_roles['login'] : '');
					echo '<input name="rpg_roles[login]" id="rpg_roles[login]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
					?>
				</td>
			</tr>
			
			
			<!-- checkbox to change role on visit -->
			
			<tr valign="top">  
				<th colspan=2>
				<hr>
					<?php _e('Assign group on every visit', 'bbp-private-groups'); ?>
				</th>
			</tr>
			
			<tr valign="top">
				<td colspan=2>
					<p>
						<?php _e('If your users change role as a result of your upgrading/downgrading process, you can choose to automatically change to the relevant group on each visit.', 'bbp-private-groups'); ?>
					</p>
					<p>
						<?php _e('BEWARE by setting this each time a user visits they will be assigned the relevant group, and any individual changes you make to a user will be overwritten each time they visit.', 'bbp-private-groups'); ?>
					</p>
				</td>
			</tr>
					
			<tr valign="top">  
				<td>
					<?php _e('Assign group on every visit', 'bbp-private-groups'); ?>
				</td>
				
				<td>
					<?php 
					$item = (!empty( $rpg_roles['change_on_visit'] ) ?  $rpg_roles['change_on_visit'] : '');
					echo '<input name="rpg_roles[change_on_visit]" id="rpg_roles[change_on_visit]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
					?>
				</td>
			</tr>
			
			<tr>
				<th colspan=2>
					<hr>
					<?php _e('Set groups', 'bbp-private-groups'); ?>
				</th>	
			</tr>
			
			<?php foreach($all_roles as $role=>$value) { 
			$name = $value['name'] ; 
			$item="rpg_roles[".$role."]" ;
			if (substr($role,0,4) != 'bbp_') {
			?>
			<!-------------------------  Role  --------------------------------------------->		
			<tr valign="top">
				<th>
					<?php echo $name ?>
				</th>
				
				<td>
					<?php echo '<select name="'.$item.'">'; ?>
					<?php echo ' ' ; ?>
				 	<?php if ($rpg_roles[$role] != 'no-Group') {
					$name2= $rpg_roles[$role] ;
					$group_name = ucwords ($name2) ;
					$role_name = (!empty($rpg_groups[$name2]) ? $rpg_groups[$name2] : '');
					$item2=$group_name.'  '.$role_name ;
					?>
					<option value="<?php echo $name2 ?>"><?php echo $item2 ?></option>
					<?php  }		?>			
					<option value="no-group"> <?php _e( 'no-Group', 'bbp-private-groups') ?></option>
					<?php
					//sets up the groups as actions
						$count=count ($rpg_groups) ;
						for ($i = 0 ; $i < $count ; ++$i) { 
							$g=$i+1 ;
							$name2="group".$g ;
							//only show if not disabled..
							if (empty ($rpg_disable_groups[$name2] ) ) {
								$role_name = (!empty($rpg_groups[$name2]) ? $rpg_groups[$name2] : '');
								$item2=__( 'Group', 'bbp-private-groups' ).$g.'  '.$role_name  ;
								?>
								<option value="<?php echo $name2 ?>"><?php echo $item2 ?></option>
						<?php 
						}	
						
						} ?>
					</select>
				</td>
			</tr>
			<?php }}
			?>
					
			</table>
			<!-- save the options -->
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Changes','bbp-private-groups' ); ?>" class="button action doaction" name="">
				</p>
			</form>
		</div><!--end sf-wrap-->
	</div><!--end wrap-->
	
<?php
}
?>