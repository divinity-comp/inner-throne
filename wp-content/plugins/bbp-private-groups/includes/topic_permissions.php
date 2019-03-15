<?php

//group capabilities settings page

function rpg_topic_permissions () {
	global $rpg_topic_permissions ;
	global $rpg_groups ;  
	?>
	 <Form method="post" action="options.php">
	<?php wp_nonce_field( 'rpg_topic_permissions', 'rpg_topic_permissions-nonce' ) ?>
	<?php settings_fields( 'rpg_topic_permissions' );
	?>
	<table>
	
	<tr><td>
	<p>	
	<?php _e('For the majority of Private Groups sites, the functionality provided in the other settings will satisfy the requirements.', 'bbp-private-groups'); ?>
	</p><p>
	<?php _e('Topic Permissions is designed for sites where users need to have different permissions to different forums.  For instance the ability for users to contribute to one forum whilst only being able to view another, or only start topics in one forum, or only reply to topics in another.', 'bbp-private-groups'); ?>
	</p><p>
	<?php _e('It is possible to create extremely complex relationships with users with multiple groups, forums with multiple groups and varying topic permissions - Please test your configuration for each type of user.', 'bbp-private-groups'); ?>
	<p>	
		<?php _e('Create/Edit/view OWN Topics etc. is intended for say a private help desk forum, where the topic is posted by the user, and then say forum moderators respond, and a dialogue can continue.', 'bbp-private-groups'); ?>
		</p>
	</td>
	</table>

	
	
	<table class="form-table">
	
	<tr><td colspan=2><hr></td>
			
			<!-- checkbox to activate login -->
					</tr>
					<tr valign="top">  
					<th><?php _e('Activate Topic Permissions', 'bbp-private-groups'); ?></th>
					<td>
					<?php global $rpg_topic_permissions ;
					$item = (!empty( $rpg_topic_permissions['activate'] ) ?  $rpg_topic_permissions['activate'] : '');
					
					echo '<input name="rpg_topic_permissions[activate]" id="rpg_topic_permissions[activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
					?>
					</td>
					</tr>
					</table>
					<!-- save the options -->
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-private-groups' ); ?>" />
				</p>
				</form>
		
		<table>
		<tr>
		<td>
		<?php _e('Once activated, new settings for Group Topic Permissions will appear in the settings for a forum in the Forum Groups box, once the forum is updated', 'bbp-private-groups'); ?>
		</td>
		<td><?php echo '<img src="' . plugins_url( 'images/topic-permisisons1.JPG',dirname(__FILE__)  ) . '" > '; ?></td>
		</tr>
		</table>
		<p>	
		<?php _e('You can set permisisons to :', 'bbp-private-groups'); ?>
		
		<li>
		<?php _e('Create/Edit Topics and Replies (and view all topics/replies)', 'bbp-private-groups'); ?>
		</li>
		<li>	
		<?php _e('Create/Edit Topics (and view all topics/replies))', 'bbp-private-groups'); ?>
		</li>
		<li>	
		<?php _e('Create/Edit Replies (and view all topics/replies)', 'bbp-private-groups'); ?>
		</li>
		<li>	
		<?php _e('Create/Edit/view OWN Topics, create/edit Replies to those topics and view any Replies to those topics', 'bbp-private-groups'); ?>
		</li>
		<li>	
		<?php _e('Only View Topics/Replies', 'bbp-private-groups'); ?>
		
		<li>	
		<?php _e('For non-logged in users, you can opt to let them view topics and replies - this might be useful where you want non-logged in users to fully see a forum, but want to restruict who can access' , 'bbp-private-groups'); ?>
		</li>
		</li>
		</p>
		
		
		
		
		<p>	
		<?php _e('Users with multiple groups will be given the greatest access that the combination allows.', 'bbp-private-groups'); ?>
		</p>
			
		<p>	
		<?php _e('For BBpress standard roles :', 'bbp-private-groups'); ?>
		<li>	
		<?php _e('Keymasters will always get access', 'bbp-private-groups'); ?>
		</li>
		<li>	
		<?php _e('Moderators and participants will get access as per Group Topic Permissions', 'bbp-private-groups'); ?>
		</li>
		<li>	
		<?php _e('Spectators will only ever get view permissions', 'bbp-private-groups'); ?>
		</li>
		</p>
		<p>	
		<?php _e('If you have custom bbpress roles, then fully test all permutations to ensure that this is working as you wish', 'bbp-private-groups'); ?>
		</p>
		

		
				
				
				
		</div><!--end sf-wrap-->
	</div><!--end wrap-->
	
	 
		<?php
		}
		

	
