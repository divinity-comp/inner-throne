<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

<div id="s2p-form"></div><!-- This is for hash anchors; do NOT remove please. -->

<form id="optimizemember-pro-stripe-cancellation-form" class="optimizemember-pro-stripe-form optimizemember-pro-stripe-cancellation-form" method="post" action="%%action%%">

	<!-- Response Section (this is auto-filled after form submission). -->
	<div id="optimizemember-pro-stripe-cancellation-form-response-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-cancellation-form-section optimizemember-pro-stripe-form-response-section optimizemember-pro-stripe-cancellation-form-response-section">
		<div id="optimizemember-pro-stripe-cancellation-form-response-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-cancellation-form-div optimizemember-pro-stripe-form-response-div optimizemember-pro-stripe-cancellation-form-response-div">
			%%response%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Cancellation Description (this will display details about what they're cancelling). -->
	<div id="optimizemember-pro-stripe-cancellation-form-description-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-cancellation-form-section optimizemember-pro-stripe-form-description-section optimizemember-pro-stripe-cancellation-form-description-section">
		<div id="optimizemember-pro-stripe-cancellation-form-description-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-cancellation-form-div optimizemember-pro-stripe-form-description-div optimizemember-pro-stripe-cancellation-form-description-div">
			%%description%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Captcha ( A reCaptcha section, with a required security code will appear here; if captcha="1" ). -->
	%%captcha%%

	<!-- Confirm Cancellation (this holds the submit button, and also some dynamic hidden input variables). -->
	<div id="optimizemember-pro-stripe-cancellation-form-submission-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-cancellation-form-section optimizemember-pro-stripe-form-submission-section optimizemember-pro-stripe-cancellation-form-submission-section">
		<div id="optimizemember-pro-stripe-cancellation-form-submission-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-cancellation-form-section-title optimizemember-pro-stripe-form-submission-section-title optimizemember-pro-stripe-cancellation-form-submission-section-title">
			<?php echo _x ("Confirm Cancellation", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-cancellation-form-submit-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-cancellation-form-div optimizemember-pro-stripe-form-submit-div optimizemember-pro-stripe-cancellation-form-submit-div">
			%%hidden_inputs%% <!-- Auto-filled by the s2Member software. Do NOT remove this under any circumstance. -->
			<button style="padding:15px;" type="submit" id="optimizemember-pro-stripe-cancellation-submit" class="optimizemember-pro-stripe-submit optimizemember-pro-stripe-cancellation-submit btn btn-warning" tabindex="100"><?php echo esc_html (_x ("Submit Form", "s2member-front", "s2member")); ?></button>
		</div>
		<div style="clear:both;"></div>
	</div>
</form>