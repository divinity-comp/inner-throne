<?php
function pg_plugin_info() {
	//get the info (thanks Pascal for this code !)
	
	$sysinfo = array();
	
	// wp version
	$newarray = array ( 'WP version' => get_bloginfo('version') );
	$sysinfo = array_merge($sysinfo, $newarray);

	// theme
	$mytheme = wp_get_theme();
	$newarray = array ( 'Theme' => $mytheme["Name"].' '.$mytheme["Version"] );
	$sysinfo = array_merge($sysinfo, $newarray);

	// PHP version
	$newarray = array ( 'PHP version' => phpversion() );
	$sysinfo = array_merge($sysinfo, $newarray);

	// bbpress version
	if (function_exists('bbPress')) {
		$bbp = bbpress();
	} else {
		global $bbp;
	}
	if (isset($bbp->version)) {
		$bbpversion = $bbp->version;
	} else {
		$bbpversion = '???';
	}		
	$newarray = array ( 'bbPress version' => $bbpversion );
	$sysinfo = array_merge($sysinfo, $newarray);

	// site url		
	$newarray = array ( 'site url' => get_bloginfo('url') );
	$sysinfo = array_merge($sysinfo, $newarray);

	// Active plugins
	$newarray = array ( 'Active Plugins' => 'Name and Version' );
	$sysinfo = array_merge($sysinfo, $newarray);
	$plugins=get_plugins();
	$activated_plugins=array();
	$i = 1;
	foreach (get_option('active_plugins') as $p){           
		if(isset($plugins[$p])){
			$linetoadd = $plugins[$p]["Name"] . ' ' . $plugins[$p]["Version"] . '<br>';
			$newarray = array ( '- p'.$i => $linetoadd );
		       	$sysinfo = array_merge($sysinfo, $newarray);
		       	$i = $i + 1;
		}           
	}
	
		
	//start output
	global $rpg_settingsf ;
	global $rpg_settingsg ;
	global $rpg_groups ;
	global $rpg_disable_groups ;
	global $rpg_topic_permissions ;
	echo '<h3>'; _e('Plugin Information', 'bbp-private-groups'); echo'</h3>';
	echo '<table >';
	array_walk($sysinfo, create_function('$item1, $key', 'echo "<tr><td>$key</td><td>$item1</td></tr>";'));
	?>
	<tr>
	</tr>
	<tr>
		<th>
			<?php echo 'Forum Visibility Settings' ?> 
		</th>
	</tr>
	<tr>
		<td>
			<?php echo 'Visibility Activated :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsf['set_forum_visibility'] ) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php echo 'URL of redirect page for LOGGED-IN user :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsf['redirect_page1'] ) ? $rpg_settingsf['redirect_page1']  : '');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php echo 'URL of redirect page for NON-LOGGED-IN user:' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsf['redirect_page2'] ) ? $rpg_settingsf['redirect_page2']  : '');
				echo $item ; ?>
		</td>
	</tr>
	
	
	
	<tr>
		<td>
			<?php echo 'Freshness Settings Activated :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsf['set_freshness_message'] ) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php echo 'Freshness Message :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsf['freshness_message'] ) ? $rpg_settingsf['freshness_message']  : '');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
	</tr>
	<tr>
		<th>
			<?php echo 'General Settings' ?> 
		</th>
	</tr>
	
	<tr>
		<td>
			<?php echo 'Hide topic and reply counts  :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsg['hide_counts']  ) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	
	<tr>
		<td>
			<?php echo 'List Sub-forums in column :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsg['list_sub_forums_as_column'] ) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php echo 'Show sub-forum content (Descriptions) :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsg['activate_descriptions']) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php echo 'Remove Private prefix :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_settingsg['activate_remove_private_prefix'] ) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
	</tr>
	<tr>
		<th>
			<?php echo 'Topic Permissions' ?> 
		</th>
	</tr>
	
	<tr>
		<td>
			<?php echo 'Topic Permissions activated :' ?>
		</td>
		<td>
			<?php $item = (!empty($rpg_topic_permissions ) ? 'true'  : 'false');
			echo $item ; ?>
		</td>
	</tr>
	
	<tr>
	</tr>
	<tr>
		<th>
			<?php echo 'Groups' ?> 
		</th>
	</tr>
	
	<?php
	$count=count ($rpg_groups) ;
			for ($i = 0 ; $i < $count ; ++$i) {
				$g=$i+1 ;
				$display=__( 'Group', 'bbp-private-groups' ).$g ;
				$name="group".$g ;
				$item="rpg_groups[".$name."]" ;
				
				?>
			<!-------------------------  Group  --------------------------------------------->		
				<tr>
					<td>
						<?php echo $display ;
						if (!empty($rpg_disable_groups[$name])) {
								echo ' <i>' ;
								_e('Disabled', 'bbp-private-groups') ;
								echo '</i>' ;
						}
						?>
					</td>
					
					<td>
						<?php _e('No. users in this group : ' , 'bbp-private-groups') ; ?>
						<?php 
							global $wpdb;
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
					</td>
				</tr>
				
				<tr>
					<td>
						<?php echo esc_html( $rpg_groups[$name] ) ; ?>
					</td>
					
					<td>
						<?php _e('Forums in this group :' , 'bbp-private-groups') ; ?>
					</td>
				</tr>
					<?php
						$forum = bbp_get_forum_post_type() ;
						$forums=$wpdb->get_col("select ID from $wpdb->posts where post_type='$forum'") ;
						$countu=0 ;
						echo '<tr><td></td><td><i>' ;
						foreach ($forums as $forum) {
							$meta = (array)get_post_meta( $forum, '_private_group', false );
							foreach ($meta as $meta2) {
								if ($meta2==$name) {
									$ftitle=bbp_forum_title($forum) ;
									echo $ftitle.'<br>' ;
									$countu++ ;
								}
							}
								
						}
					echo '</i></td></tr>' ;
					?>
		<!-------------------------  FORUMS  --------------------------------------------->	
	<?php }
	?>
	<tr>
	</tr>
	
	<tr>
		<th>
			<?php echo 'Forums' ?> 
		</th>
		<th>
			<?php echo 'Groups' ?> 
		</th>
		<?php if (!empty ($rpg_topic_permissions) ) echo '<th>Topic Permissions</th>' ; ?>
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
	
	echo '</table>';
	
}
?>