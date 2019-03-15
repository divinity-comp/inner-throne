<?php
if(!defined('WPINC'))
	exit("Do not access this file directly.");
?>

<div id="s2p-form"></div><!-- This is for hash anchors; do NOT remove please. -->

<form id="optimizemember-pro-stripe-update-form" class="optimizemember-pro-stripe-form optimizemember-pro-stripe-update-form" method="post" action="%%action%%">

	<!-- Response Section (this is auto-filled after form submission). -->
	<div id="optimizemember-pro-stripe-update-form-response-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-update-form-section optimizemember-pro-stripe-form-response-section optimizemember-pro-stripe-update-form-response-section">
		<div id="optimizemember-pro-stripe-update-form-response-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-update-form-div optimizemember-pro-stripe-form-response-div optimizemember-pro-stripe-update-form-response-div">
			%%response%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Checkout Description (this is the desc="" attribute from your Shortcode). -->
	<div id="optimizemember-pro-stripe-update-form-description-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-update-form-section optimizemember-pro-stripe-form-description-section optimizemember-pro-stripe-update-form-description-section">
		<div id="optimizemember-pro-stripe-update-form-description-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-update-form-div optimizemember-pro-stripe-form-description-div optimizemember-pro-stripe-update-form-description-div">
			%%description%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Billing Method (powered by Stripe). -->
	<div id="optimizemember-pro-stripe-update-form-billing-method-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-update-form-section optimizemember-pro-stripe-form-billing-method-section optimizemember-pro-stripe-update-form-billing-method-section">
		<div id="optimizemember-pro-stripe-update-form-billing-method-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-update-form-section-title optimizemember-pro-stripe-form-billing-method-section-title optimizemember-pro-stripe-update-form-billing-method-section-title">
			<?php echo _x("New Billing Method", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-update-form-source-token-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-update-form-div optimizemember-pro-stripe-form-source-token-div optimizemember-pro-stripe-update-form-source-token-div">
			<button id="optimizemember-pro-stripe-update-form-source-token-button" class="optimizemember-pro-stripe-form-source-token-button optimizemember-pro-stripe-update-form-source-token-button" type="button">
				<i><?php echo _x("[+]", "s2member-front", "s2member"); ?></i> <span><?php echo _x("New Billing Method", "s2member-front", "s2member"); ?></span>
			</button>
			<div id="optimizemember-pro-stripe-update-form-source-token-summary" class="optimizemember-pro-stripe-form-source-token-summary optimizemember-pro-stripe-update-form-source-token-summary">
				%%card_token_summary%%
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Captcha ( A reCaptcha section, with a required security code will appear here; if captcha="1" ). -->
	%%captcha%%

	<!-- Checkout Now (this holds the submit button, and also some dynamic hidden input variables). -->
	<div id="optimizemember-pro-stripe-update-form-submission-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-update-form-section optimizemember-pro-stripe-form-submission-section optimizemember-pro-stripe-update-form-submission-section">
		<div id="optimizemember-pro-stripe-update-form-submission-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-update-form-section-title optimizemember-pro-stripe-form-submission-section-title optimizemember-pro-stripe-update-form-submission-section-title">
			<?php echo _x("Update Billing Information", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-update-form-submit-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-update-form-div optimizemember-pro-stripe-form-submit-div optimizemember-pro-stripe-update-form-submit-div">
			%%hidden_inputs%% <!-- Auto-filled by the s2Member software. Do NOT remove this under any circumstance. -->
			<button style="padding:15px;" type="submit" id="optimizemember-pro-stripe-update-submit" class="optimizemember-pro-stripe-submit optimizemember-pro-stripe-update-submit btn btn-primary" tabindex="300"><?php echo esc_html(_x("Submit Form", "s2member-front", "s2member")); ?></button>
		</div>
		<div style="clear:both;"></div>
	</div>
</form>