<?php

 //********************************************start of widget warning
 
 
 
 function pg_shortcode_warning() {
 ?>
			
						<table class="form-table">
					
					<tr valign="top">
						<th colspan="2">
						
						<h3>
						<?php _e ('Shortcode Warning' , 'bbp-private-groups' ) ; ?>
						</h3>
						
<h4><span style="color:blue"><?php _e('The [bbp-single-forum], [bbp-single-topic] and [bbp-single-reply] shortcodes widgets will still show all forums, topics and replies etc.','bbp-private-groups' ) ; ?></span></h4>
<h4><span style="color:blue"><?php _e('DO NOT USE THESE SHORTCODES - instead use the replacements shown below','bbp-private-groups' ) ; ?></span></h4>
<h4><span style="color:blue"><?php _e('Additionally do not use [bbp-single-view], [bbp-topic-tags] or [bbp-single-tag] shortcodes as these may show unintended content','bbp-private-groups' ) ; ?></span></h4>
</td>
</tr>
					
					<tr valign="top">
						<th colspan="2">
						<h3>
						<?php _e ('Additional Shortcodes' , 'bbp-private-groups' ) ; ?>
						</h3>
<p><tt>[pg-single-forum id=2923 ]</tt> 	</p>					
<p><tt>[pg-single-forum id=2923 message ="You do not have permission to view this"]</tt> </p>
<p> <?php _e('Displays a single forum with an ID of 2923, with optional message for non-logged in users or users without permission for this forum', 'bbp-private-groups' ) ; ?>
</p>
<p><tt>[pg-single-topic id=15646 ]</tt></p>
<p><tt>[pg-single-topic id=15646 message ="You do not have permission to view this]</tt> </p>  
<p> <?php _e('Displays a single topic with an ID of 15646, with optional message for non-logged in users or users without permission for this forum', 'bbp-private-groups' ) ; ?></p>
<p><tt>[pg-single-reply id=17658 message ="You do not have permission to view this]</tt></p>
<p><tt>[pg-single-reply id=17658 message ="You do not have permission to view this]</tt></p>
<p><?php _e('Displays a single reply with an ID of 17658, with optional message for non-logged in users or users without permission for this forum', 'bbp-private-groups' ) ; ?></p>
 						


 
 
</div><!--end sf-wrap-->
</div><!--end wrap-->
<?php
}
?>
