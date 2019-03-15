<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/***

	* WordPress default function
	* wp_enqueue_style, wp_enqueue_script and esc_url
*/

wp_enqueue_style("cc_bootstrap", plugin_dir_url( __FILE__ ).'css/bootstrap.min.css');
wp_enqueue_style("installer", plugin_dir_url( __FILE__ ).'css/installer.css');

wp_enqueue_script("installer", plugin_dir_url( __FILE__ ).'js/installer.js');
wp_enqueue_script('jquery');
if(!function_exists('curl_init')){
	wp_enqueue_script("jqcurl", plugin_dir_url( __FILE__ ).'js/checkcurl.js');
}
$cometchat_logo = esc_url(plugin_dir_url(__FILE__).'images/cometchat_logo.png');

?>
<!DOCTYPE html>
<html>
<head>
	<title>CometChat | Installer</title>
</head>
<body>
	<div class="container" style="">
	  	<div class="row">
			<div class="ccplugin_outerframe">
				<div class="ccplugin_middleform" >
					<div class="cometchat_logo_div">
						<img src="<?php echo $cometchat_logo; ?>" class="cometchat_logo_image">
					</div>
					<div class="module form-module" id="license-form">
						<div class="cometchat_form">
							<!-- <h2 >CometChat Installation Process</h2> -->
							<form action="javascript:cometchatInstall();" method="post" id="ccInstallProcess">
								<input type="text" name="license" id= "license" placeholder="Enter license key" required="true" />
								<input type="submit" name="submit" id="cometchat_install" value="Install" />
								<input type="hidden" name="currentTime" class="login_inputbox currentTime">
							</form>
						</div>
						<div id="" class="col-sm-12" style="color:red;padding-top:30px;text-align: center;">
							<a target="_blank" href="https://www.cometchat.com/pricing">Don't have a license key?</a>
						</div>
					</div>

					<div class="col-lg-12" id="" style="">
						<div class="col-sm-2" id="" style=""></div>
						<div class="col-sm-8" id="installer-process" style="display: none;">
							<h3 style="text-align: center;">Installation Process</h3>
							<p>Progress:</p>
							<div class="progress">
								<div id="progressbar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" >
								<!-- 40% -->
								</div>
							</div>
						</div>
						<div class="col-sm-2" id="" style=""></div>
					</div>

					<div id="error" class="col-sm-12" style="color:red;padding-top:30px;display:none;text-align: center;">
					If you face any issue with the update, please contact our support team to assist you.
					</div>
					<div id="cancel-btn" class="row col-md-12" style="display:none;text-align: center;padding-top:30px;">
					<a href="" class="btn btn-danger">Cancel</a>
					</div>
				</div>
			</div>
	    </div>
	</div>
</body>

</html>