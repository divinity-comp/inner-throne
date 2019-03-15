<?php



function rpg_programmers_help () {
	?>
	<table class="form-table">
						
						
					
	<tr valign="top">
	<th colspan="2">
	
	<h4><?php _e ('Programmers help' , 'bbp-private-groups' ) ; ?>	</h4>
	


<p><?php _e ('Database : ', 'bbp-private-groups') ; ?></p>

<p><?php _e ('Users are set in usermeta meta_key \'private_group\' and groups are named \'group1\', \'group2\' etc. and seperated by \'*\'  so an entry might be \'*group1*group3*\' ' , 'bbp-private-groups') ; ?></p>

<p><?php _e ('Groups for forums are set in postmeta against \'_private_group\' with an entry per group ' , 'bbp-private-groups') ; ?></p>

<p><?php _e ('Group names are set in options under \'rpg_groups\' ' , 'bbp-private-groups') ; ?></p>



</td></tr>
	
	<tr valign="top">
	<th colspan="2">
						
	<h3><?php _e ('Add and remove users from groups' , 'bbp-private-groups' ) ; ?>	</h3>

<p><?php _e ('Included in this plugin is a class to allow programmers to interact with this plugin and add or remove users from groups' , 'bbp-private-groups') ; ?></p>


<p><?php _e('example use:', 'bbp-private-groups') ; ?></p>

<p><?php _e('$user_id = 1191 ;', 'bbp-private-groups') ; ?></p>
<p><?php _e('$group = \'group2\' ;', 'bbp-private-groups') ; ?></p>
<p><?php _e('$bbp_private_groups_adds = new rpg_Bbp_Private_Groups_Adds( $user_id , $group );', 'bbp-private-groups') ; ?></p>
<p><?php _e('then', 'bbp-private-groups') ; ?></p>
<p><?php _e('$bbp_private_groups_adds->add() ;', 'bbp-private-groups') ; ?></p>
<p><?php _e('or', 'bbp-private-groups') ; ?></p>
<p><?php _e('$bbp_private_groups_adds->remove() ;', 'bbp-private-groups') ; ?></p>
	


</tr>
					

</table>
</div><!--end sf-wrap-->
</div><!--end wrap-->
<?php
}
?>