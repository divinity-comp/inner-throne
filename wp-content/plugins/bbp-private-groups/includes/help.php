<?php

 //********************************************start of help
 
 
 
 function pg_help() {
 ?>
			
	<table class="form-table">
						
						
					
	<tr valign="top">
	<th colspan="2">
	
	<h3><?php _e ('Setting up' , 'bbp-private-groups' ) ; ?>	</h3>
	
<p><?php _e ('In essence this plugin is very simple!' , 'bbp-private-groups') ; ?></p>

<p><?php _e ('You set up groups and give them names.  Each user can then belong to one or more groups.  Each forum can also belong to one or more groups.' , 'bbp-private-groups') ; ?></p>	

<p><?php _e ('If a user and forum both belong to the same group, then the user can access that forum.' , 'bbp-private-groups') ; ?></p>	

<p><?php _e ('Additionally you can set forum visibility to show forums exist, or have them only show to authorised users.' , 'bbp-private-groups') ; ?></p>	

<p><?php _e ('To add to this, you can limit a group\'s access to a forum to just creating topics, just creating replies, view only access or full access' , 'bbp-private-groups') ; ?></p>	

<p><?php _e ('Forums not allocated to any group can be fully visible or visible to just logged in users' , 'bbp-private-groups') ; ?></p>	


<p><?php _e ('Using this, as both users and forums can belong to more than one group, you can set up very complex envoronment, with fully public and readable forums, alongside forums showing they exist, but requiring say membership, to hidden forums only available to groups, and even restriction on a users access to a forum.' , 'bbp-private-groups') ; ?></p>	

<p><?php _e ('Example: ' , 'bbp-private-groups') ; ?></p>	

<p><?php _e ('User A belongs to group 1' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('User B belongs to group 2' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('User C belongs to group 3' , 'bbp-private-groups') ; ?></p>
<p/>
<p><?php _e ('Forum X is set to allow group 2' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('Forum Y is set to allow group 2 and group 3' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('Forum Y is set to allow group 1 and group 3' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('Then :' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('User A can access only forum Z' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('User B can access forum X and forum Y' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('User C can access forum Y and forum Z' , 'bbp-private-groups') ; ?></p>
<p/>
<p><?php _e ('Example One  – Single restricted forum per user group.  For instance if you wanted separate forums for doctors and nurses, so the users will only ever have one restricted forum (and any number of public unrestricted), this becomes quite easy, you can just set a group for each forum. Then each user is set to be either a doctor or a nurse. The doctors forum is then set to allow the doctor group and the nurses forum the nurse group' , 'bbp-private-groups') ; ?></p>
<p><?php _e ('Example two – Users across two restricted forums For instance if you had forums ‘teachers’ and ‘pupils’, and you wanted only teachers to access the teachers forum, but teachers and pupils to access the pupils forum the you could set up a ‘teacher’ group and a ‘pupil’ group. The teachers forum would only permit the group ‘teacher’,  but the pupil forum would permit both the teacher and the pupil groups.' , 'bbp-private-groups') ; ?></p>
</td></tr>
	
	<tr valign="top">
	<th colspan="2">
						
	<h3><?php _e ('Forum Visibility Settings' , 'bbp-private-groups' ) ; ?>	</h3>

<p><?php _e ('You have various display options' , 'bbp-private-groups') ; ?></p>

<h4><span style="color:blue"><?php _e('Only logged in users see group forums, and even then only ones that they have access to.' , 'bbp-private-groups' ) ; ?></span></h4>

<p>
<?php _e ('DESCRIPTION: the default view.  Allows unlimited groups with unique combination of access to forums, but they only see those they have access to.' , 'bbp-private-groups') ; ?></p>

<p><?php _e('TO SET : Do not set the visibility.' , 'bbp-private-groups' ) ; ?></p>

<h4><span style="color:blue">
<?php _e('All forum titles (and optionally descriptions) visible to both logged on and non-logged on users.' , 'bbp-private-groups' ) ; ?>
</span></h4>

<p>
<?php _e('DESCRIPTION : Users and non-users will be able to see that group forums exist, but not access topics/replies.' , 'bbp-private-groups' ) ; ?></p>


<p>
<?php _e('POSSIBLE USAGE : show that lots of forums exist, with ability to go to “sign-up” page.' , 'bbp-private-groups' ) ; ?></p>

<p>
<?php _e('TO SET : Set visibility to public and set forums to public in each forum’s attributes (dashboard>forums).' , 'bbp-private-groups' ) ; ?></p>

<h4><span style="color:blue">
<?php _e('Public forums titles (and optionally descriptions) visible to logged on and non-logged on users. Private forums set visible to logged on users' , 'bbp-private-groups' ) ; ?>
</span></h4>

<p>
<?php _e ('DESCRIPTION : Non-logged in will only see group forums that are public, but not access topics/replies.  Logged in users will see and access topics/replies for Private Group forums that are set to public, private forums that have no groups set will be accessible to all logged in users, but private group forums that the user does not belong to will be hidden.' , 'bbp-private-groups' ) ; ?> </p> 

<p>
<?php _e('POSSIBLE USAGE : Show some group forums exists, whilst keeping others visible only for private groups.  Allows rich display options for each group, and user.' , 'bbp-private-groups' ) ; ?></p>

<p>
<?php _e('TO SET : Set visibility to public and set any private forums to private in that forum’s attributes (dashboard>forums).' , 'bbp-private-groups' ) ; ?> </p>

 </th>
</tr>
</table>
<table class="form-table">
					
<tr valign="top">
<th colspan="2">
<h3><?php _e('Role Capabilities' , 'bbp-private-groups' ) ; ?></h3>
										
<tr>
<td>
<td><?php echo '<img src="' . plugins_url( 'images/help1	.JPG',dirname(__FILE__)  ) . '" > '; ?></td>
</td>
</tr>
</table>
<table class="form-table">
					
<tr valign="top">
	<th colspan="2">
<h3>
<?php _e('Group Allocation' , 'bbp-private-groups' ) ; ?></h3>
<tr>
<td colspan="2">
<p>
<?php _e('You can allocate users to groups in two places' , 'bbp-private-groups' ) ; ?></p>
<p>
<?php _e('1. Within this settings area, you can use "user management" To set single groups, you can use either "bulk actions" or edit each user (you will see an edit when you hover over the user). ' , 'bbp-private-groups' ) ; ?>
<?php _e('The "bulk actions" CANNOT be used to set a multiple groups group against a user or users.  To set multiple groups, edit each user.' , 'bbp-private-groups' ) ; ?>  </p>
<p>
<?php _e(' 2. Within Dashboard>users and edit user you can set one or more groups against the user.' , 'bbp-private-groups' ) ; ?></p>

</td>
</tr>					
<tr>
<td><?php echo '<img src="' . plugins_url( 'images/help2.JPG',dirname(__FILE__)  ) . '" > '; ?></td>
<td>
<p>
<?php _e('EXAMPLE 1 : A simple relationship where a forum belongs to a group, and users are allocated to that group.' , 'bbp-private-groups' ) ; ?></p>
<p>
<?php _e('EXAMPLE 2 : Group 1 users can only see forum A, but group 2 users can see both forum A and B.' , 'bbp-private-groups' ) ; ?></p>
<p><p><p>
<?php _e('EXAMPLE 3 : A complex arrangement.  User M can only see forum A, User N can see forum A and C, and users P and Q can see both Forum A and B.' , 'bbp-private-groups' ) ; ?></p>
					
</td>
</tr>
					

</table>
</div><!--end sf-wrap-->
</div><!--end wrap-->
<?php
}
?>
