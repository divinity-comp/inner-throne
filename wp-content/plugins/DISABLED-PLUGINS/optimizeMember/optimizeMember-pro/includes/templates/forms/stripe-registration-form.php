<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

<div id="s2p-form"></div><!-- This is for hash anchors; do NOT remove please. -->

<form id="optimizemember-pro-stripe-registration-form" class="optimizemember-pro-stripe-form optimizemember-pro-stripe-registration-form" method="post" action="%%action%%">

	<!-- Response Section (this is auto-filled after form submission). -->
	<div id="optimizemember-pro-stripe-registration-form-response-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-registration-form-section optimizemember-pro-stripe-form-response-section optimizemember-pro-stripe-registration-form-response-section">
		<div id="optimizemember-pro-stripe-registration-form-response-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-response-div optimizemember-pro-stripe-registration-form-response-div">
			%%response%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Options Section (this is filled by Shortcode options; when/if specified). -->
	<div id="optimizemember-pro-stripe-registration-form-options-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-registration-form-section optimizemember-pro-stripe-form-options-section optimizemember-pro-stripe-registration-form-options-section">
		<div id="optimizemember-pro-stripe-registration-form-options-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-registration-form-section-title optimizemember-pro-stripe-form-options-section-title optimizemember-pro-stripe-registration-form-options-section-title">
			<?php echo _x("Registration Options", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-registration-form-options-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-options-div optimizemember-pro-stripe-registration-form-options-div">
			<select name="s2p-option" id="optimizemember-pro-stripe-registration-options" class="optimizemember-pro-stripe-options optimizemember-pro-stripe-registration-options form-control" tabindex="-1">
				%%options%%
			</select>
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Registration Description (this is the desc="" attribute from your Shortcode). -->
	<div id="optimizemember-pro-stripe-registration-form-description-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-registration-form-section optimizemember-pro-stripe-form-description-section optimizemember-pro-stripe-registration-form-description-section">
		<div id="optimizemember-pro-stripe-registration-form-description-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-description-div optimizemember-pro-stripe-registration-form-description-div">
			%%description%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Registration Details (Name, Email, Username, Password). -->
	<!-- Name fields will be hidden automatically when/if your Registration/Profile Field options dictate this behavior. -->
	<div id="optimizemember-pro-stripe-registration-form-registration-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-registration-form-section optimizemember-pro-stripe-form-registration-section optimizemember-pro-stripe-registration-form-registration-section">
		<div id="optimizemember-pro-stripe-registration-form-registration-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-registration-form-section-title optimizemember-pro-stripe-form-registration-section-title optimizemember-pro-stripe-registration-form-registration-section-title">
			<?php echo _x ("Create Profile", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-registration-form-first-name-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-first-name-div optimizemember-pro-stripe-registration-form-first-name-div">
			<label for="optimizemember-pro-stripe-registration-first-name" id="optimizemember-pro-stripe-registration-form-first-name-label" class="optimizemember-pro-stripe-form-first-name-label optimizemember-pro-stripe-registration-form-first-name-label">
				<span><?php echo _x ("First Name", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_registration[first_name]" id="optimizemember-pro-stripe-registration-first-name" class="optimizemember-pro-stripe-first-name optimizemember-pro-stripe-registration-first-name form-control" value="%%first_name_value%%" tabindex="10" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-registration-form-last-name-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-last-name-div optimizemember-pro-stripe-registration-form-last-name-div">
			<label for="optimizemember-pro-stripe-registration-last-name" id="optimizemember-pro-stripe-registration-form-last-name-label" class="optimizemember-pro-stripe-form-last-name-label optimizemember-pro-stripe-registration-form-last-name-label">
				<span><?php echo _x ("Last Name", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_registration[last_name]" id="optimizemember-pro-stripe-registration-last-name" class="optimizemember-pro-stripe-last-name optimizemember-pro-stripe-registration-last-name form-control" value="%%last_name_value%%" tabindex="20" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-registration-form-email-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-email-div optimizemember-pro-stripe-registration-form-email-div">
			<label for="optimizemember-pro-stripe-registration-email" id="optimizemember-pro-stripe-registration-form-email-label" class="optimizemember-pro-stripe-form-email-label optimizemember-pro-stripe-registration-form-email-label">
				<span><?php echo _x ("Email Address", "s2member-front", "s2member"); ?> *</span><br />
				<input type="email" aria-required="true" data-expected="email" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_registration[email]" id="optimizemember-pro-stripe-registration-email" class="optimizemember-pro-stripe-email optimizemember-pro-stripe-registration-email form-control" value="%%email_value%%" tabindex="30" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-registration-form-username-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-username-div optimizemember-pro-stripe-registration-form-username-div">
			<label for="optimizemember-pro-stripe-registration-username" id="optimizemember-pro-stripe-registration-form-username-label" class="optimizemember-pro-stripe-form-username-label optimizemember-pro-stripe-registration-form-username-label">
				<span><?php echo _x ("Username (lowercase letters and/or numbers)", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="60" autocomplete="off" name="optimizemember_pro_stripe_registration[username]" id="optimizemember-pro-stripe-registration-username" class="optimizemember-pro-stripe-username optimizemember-pro-stripe-registration-username form-control" value="%%username_value%%" tabindex="40" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-registration-form-password-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-password-div optimizemember-pro-stripe-registration-form-password-div">
			<label for="optimizemember-pro-stripe-registration-password1" id="optimizemember-pro-stripe-registration-form-password-label" class="optimizemember-pro-stripe-form-password-label optimizemember-pro-stripe-registration-form-password-label">
				<span><?php echo _x ("Password (type this twice please)", "s2member-front", "s2member"); ?> *</span><br />
				<input type="password" aria-required="true" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_registration[password1]" id="optimizemember-pro-stripe-registration-password1" class="optimizemember-pro-stripe-password1 optimizemember-pro-stripe-registration-password1 form-control" value="%%password1_value%%" tabindex="50" />
			</label>
			<input type="password" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_registration[password2]" id="optimizemember-pro-stripe-registration-password2" class="optimizemember-pro-stripe-password2 optimizemember-pro-stripe-registration-password2 form-control" value="%%password2_value%%" tabindex="60" />
			<div id="optimizemember-pro-stripe-registration-form-password-strength" class="ws-plugin--optimizemember-password-strength optimizemember-pro-stripe-form-password-strength optimizemember-pro-stripe-registration-form-password-strength"><em><?php echo _x ("password strength indicator", "s2member-front", "s2member"); ?></em></div>
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Custom Fields (Custom Registration/Profile Fields will appear here, when/if they've been configured). -->
	%%custom_fields%%

	<!-- Captcha ( A reCaptcha section, with a required security code will appear here; if captcha="1" ). -->
	%%captcha%%

	<!-- Complete Registration (this holds the submit button, and also some dynamic hidden input variables). -->
	<div id="optimizemember-pro-stripe-registration-form-submission-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-registration-form-section optimizemember-pro-stripe-form-submission-section optimizemember-pro-stripe-registration-form-submission-section">
		<div id="optimizemember-pro-stripe-registration-form-submission-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-registration-form-section-title optimizemember-pro-stripe-form-submission-section-title optimizemember-pro-stripe-registration-form-submission-section-title">
			<?php echo _x ("Complete Registration", "s2member-front", "s2member"); ?>
		</div>
		%%opt_in%% <!-- s2Member will fill this when/if there are list servers integrated, and the Opt-In Box is turned on. -->
		<div id="optimizemember-pro-stripe-registration-form-submit-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-registration-form-div optimizemember-pro-stripe-form-submit-div optimizemember-pro-stripe-registration-form-submit-div">
			%%hidden_inputs%% <!-- Auto-filled by the s2Member software. Do NOT remove this under any circumstance. -->
			<button style="padding:15px;" type="submit" id="optimizemember-pro-stripe-registration-submit" class="optimizemember-pro-stripe-submit optimizemember-pro-stripe-registration-submit btn btn-primary" tabindex="400"><?php echo esc_html (_x ("Submit Form", "s2member-front", "s2member")); ?></button>
		</div>
		<div style="clear:both;"></div>
	</div>
</form>