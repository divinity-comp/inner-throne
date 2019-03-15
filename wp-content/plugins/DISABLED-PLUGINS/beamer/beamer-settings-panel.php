<?php
	/* ----------------------------------------------------------------------------------------------------

		BEAMER SETTINGS -> PANEL
		Adds layout to the Beamer options page

	---------------------------------------------------------------------------------------------------- */
?>

<div class="wrap">
	<h2>Beamer Settings</h2>
	<div class="bmrCard">
		<div class="bmrBanner">
			<span>You don't have a Beamer account?</span>
			<a class="bmrButton" href="<?php bmr_url_signup(true); ?>" target="_blank" rel="nofollow">Get one for free</a>
		</div>
		<div class="bmrHeader">
			<a class="bmrLogo"></a>
		</div>
		<div class="bmrSubheader">
			<a class="bmrTab" href="<?php bmr_url('settings', 'app', true); ?>" target="_blank" rel="nofollow">Go to Beamer Dashboard</a>
			<a class="bmrTab" href="<?php bmr_url('blog/how-to-use-beamer-as-a-wordpress-plugin/', 'www', true); ?>" target="_blank" rel="nofollow">Tutorial</a>
			<a class="bmrTab" href="<?php bmr_url('docs', 'www', true); ?>" target="_blank" rel="nofollow">Documentation</a>
			<a class="bmrTab" href="<?php bmr_url('help', 'www', true); ?>" target="_blank" rel="nofollow">Help Center</a>
		</div>
		<div class="bmrContent">
			<form method="post" action="options.php">
				<?php
					settings_fields( 'beamer_settings_option_name' );
					do_settings_sections( 'beamer-settings-admin' );
					submit_button();
				?>
			</form>
			<br>
		</div>
		<div class="bmrCoda">
			<p><b>©2017-2018 Beamer.</b> Designed with &#10084; by <a href="http://www.hibox.co/?ref=wp_plugin" target="_blank" rel="nofollow">Hibox</a> – Version <?php echo bmr_version(); ?>
		</div>
	</div>
</div>