<?php
	wp_enqueue_style("ccadmin", plugin_dir_url( __FILE__ ).'css/admin.css');
?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
	<div class="cometchat_outerframe">
		<div class="cometchat_middleform" >
			<div class="cometchat-tab-content">
				<div id="cometchat_adminpanel">
					<div class="cometchat_logo_div">
						<img src="<?php echo $cometchatLogo; ?>" id="ccimg" style="opacity:0.1;">
					</div>
					<div id="cc_dashboard">
						<p id="cc_admin_para">
							To Change the layout or further customize cometchat please visit admin panel.
						</p>
						<h1>
							CometChat Admin Panel
						</h1>
						<iframe src="<?php echo $adminpanelurl; ?>" id="ccadminpanel"></iframe>
						<button type="button" id="cc_plugin_admin_panel" class="ccadminpanel ccadminpanel-primary" onclick="openCCAdminPanel('<?php echo $adminpanelurl; ?>');">
							Launch Admin Panel
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>