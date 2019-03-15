<?php

function w3dev_bulk_change_role_css()
{
	wp_register_style('w3dev-bulk-role-change-css', plugins_url('../css/style.css', __FILE__));
	wp_enqueue_style('w3dev-bulk-role-change-css');
}
add_action( 'admin_enqueue_scripts', 'w3dev_bulk_change_role_css' );


function w3dev_bulk_change_role_js()
{ 
	wp_register_script('w3dev-bulk-role-change-js', plugins_url('../javascript/app.js', __FILE__), array('jquery'), false, false);
	wp_enqueue_script('w3dev-bulk-role-change-js');
}
add_action( 'admin_enqueue_scripts', 'w3dev_bulk_change_role_js' );

?>