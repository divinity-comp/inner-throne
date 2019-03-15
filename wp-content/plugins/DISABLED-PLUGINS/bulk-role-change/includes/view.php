<?php

add_action( 'admin_menu', 'bulk_change_role_menu' );
function bulk_change_role_menu() {
	add_options_page( 
		'Bulk change role', 
		'Bulk change role', 
		'manage_options', 
		'bulk_change_role_page.php', 
		'bulk_change_role', 
		'dashicons-businessman', 6
		);
}
function bulk_change_role()
{
	global $wp_roles;
	$roles 	= $wp_roles->get_names();
	$name 	= get_file_data( __FILE__, array ( 'Plugin Name' ), 'plugin' );
	?>

	<h1>Bulk Change Role</h1>

	<div id="bulk-change-role-container">

	 	<div id="ps-roles" class="cf">
	 		<h3>What does this WP Plugin do?</h3>
	 		<p>This plugin allows you to transfer all users belonging to one role, to another. So for example you could transfer all users under the role 'Public' to 'Editor'.</p>

		 	<span class="ver">Ver 1.0</span>
		 	
			<div id="js-alert-success" class="alert alert-success" role="alert" style="display:none;"></div>
			<div id="js-alert-error" class="alert alert-danger" role="alert" style="display:none;"></div>

		 	<div class="form" style="padding: 0 0 20px 0;">
		 	<div class="col-1-2">
			 	<p>
			 	<span class="text-primary " style="font-weight: bold; font-size: 1.4em;">Step 1.</span><br />
			 	What is the current role you want to transfer users from?</p>
				<div style="padding-right: 20px;">
				<select data-target-id="input-transfer-from" id="js-transfer-from" class="js-transfer-roles">
					<option>Please select...</option>
					<?php foreach($roles as $role_id => $role_name) { ?>
						<option value="<?php echo $role_id; ?>"><?php echo $role_name; ?></option>
					<?php } ?>
				</select>
				<input readonly type="text" name="input-transfer-from" id="input-transfer-from" /> Number of User(s)
				</div>
			</div>
		 
		 	<div class="col-1-2">
			 	<p>
				<span class="text-primary " style="font-weight: bold; font-size: 1.4em;">Step 2.</span><br />			 	
			 	Select the new role to transfer the users to:</p>
			 	<div style="padding-right: 20px;">
				<select data-target-id="input-transfer-to" id="js-transfer-to" class="js-transfer-roles">
					<option>Please select...</option>
					<?php foreach($roles as $role_id => $role_name) { ?>
						<option value="<?php echo $role_id; ?>"><?php echo $role_name; ?></option>
					<?php } ?>
				</select>
				<input readonly type="text" name="input-transfer-to" id="input-transfer-to" /> Number of Users(s)
				</div>
			</div>
			</div>
		</div>

		<div id="form-actions">
			<p><input id="input-confirm-transfer" type="checkbox" /> <span class="text-danger">I understand that all users belonging to the role <strong>'<span class="label-transfer-from">...</span>'</strong> will be transfered to the role <strong>'<span class="label-transfer-to">...</span>'</strong>.</span></p>
			<button id="btn-transfer-role" class="btn btn-success btn-disabled">Transfer users</button>
			<div id="confirm-transfer-role" style="display:none;">
				<button class="btn btn-link">Are you sure?</button>
				<button id="btn-confirm-transfer-role" class="btn btn-success">Yes</button>
				<button id="btn-cancel-transfer-role" class="btn btn-danger">No</button>
			</div>
		</div>

		<textarea style="display:none;margin-top:30px;margin-bottom:20px;height:150px;width:100%;" id="js-output"></textarea>

	</div>
	
	<?php

}

?>