<?php
if(!defined('WPINC'))
	exit("Do not access this file directly.");
?>

<div id="s2p-form"></div><!-- This is for hash anchors; do NOT remove please. -->

<form id="optimizemember-pro-stripe-sp-checkout-form" class="optimizemember-pro-stripe-form optimizemember-pro-stripe-sp-checkout-form" method="post" action="%%action%%">

	<!-- Response Section (this is auto-filled after form submission). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-response-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-response-section optimizemember-pro-stripe-sp-checkout-form-response-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-response-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-response-div optimizemember-pro-stripe-sp-checkout-form-response-div">
			%%response%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Options Section (this is filled by Shortcode options; when/if specified). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-options-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-options-section optimizemember-pro-stripe-sp-checkout-form-options-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-options-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-sp-checkout-form-section-title optimizemember-pro-stripe-form-options-section-title optimizemember-pro-stripe-sp-checkout-form-options-section-title">
			<?php echo _x("Checkout Options", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-options-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-options-div optimizemember-pro-stripe-sp-checkout-form-options-div form-control">
			<select name="s2p-option" id="optimizemember-pro-stripe-sp-checkout-options" class="optimizemember-pro-stripe-options optimizemember-pro-stripe-sp-checkout-options" tabindex="-1">
				%%options%%
			</select>
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Checkout Description (this is the desc="" attribute from your Shortcode). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-description-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-description-section optimizemember-pro-stripe-sp-checkout-form-description-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-description-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-description-div optimizemember-pro-stripe-sp-checkout-form-description-div">
			%%description%%
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Coupon Code ( this will ONLY be displayed if your Shortcode has this attribute: accept_coupons="1" ). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-coupon-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-coupon-section optimizemember-pro-stripe-sp-checkout-form-coupon-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-coupon-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-sp-checkout-form-section-title optimizemember-pro-stripe-form-coupon-section-title optimizemember-pro-stripe-sp-checkout-form-coupon-section-title">
			<?php echo _x("Coupon Code", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-coupon-response-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-coupon-response-div optimizemember-pro-stripe-sp-checkout-form-coupon-response-div">
			%%coupon_response%% <!-- A Coupon response (w/Discounts) will be displayed here; based on the Coupon Code that was entered. -->
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-coupon-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-coupon-div optimizemember-pro-stripe-sp-checkout-form-coupon-div">
			<label for="optimizemember-pro-stripe-sp-checkout-coupon" id="optimizemember-pro-stripe-sp-checkout-form-coupon-label" class="optimizemember-pro-stripe-form-coupon-label optimizemember-pro-stripe-sp-checkout-form-coupon-label">
				<span><?php echo _x("Have a Coupon Code?", "s2member-front", "s2member"); ?></span><br />
				<input type="text" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_sp_checkout[coupon]" id="optimizemember-pro-stripe-sp-checkout-coupon" class="optimizemember-pro-stripe-coupon optimizemember-pro-stripe-sp-checkout-coupon form-control" value="%%coupon_value%%" tabindex="1" />
			</label>
			<input type="button" id="optimizemember-pro-stripe-sp-checkout-coupon-apply" class="optimizemember-pro-stripe-coupon-apply optimizemember-pro-stripe-sp-checkout-coupon-apply btn btn-default" value="<?php echo esc_attr(_x("Apply Coupon", "s2member-front", "s2member")); ?>" tabindex="-1" />
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Contact Info/Details (Name, Email). -->
	<!-- Some of this information will be prefilled automatically when/if a User/Member is already logged-in. -->
	<!-- Name fields will NOT be hidden automatically here; even if your Registration/Profile Field options dictate this behavior. -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-registration-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-registration-section optimizemember-pro-stripe-sp-checkout-form-registration-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-registration-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-sp-checkout-form-section-title optimizemember-pro-stripe-form-registration-section-title optimizemember-pro-stripe-sp-checkout-form-registration-section-title">
			<?php echo _x("Contact Info", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-first-name-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-first-name-div optimizemember-pro-stripe-sp-checkout-form-first-name-div">
			<label for="optimizemember-pro-stripe-sp-checkout-first-name" id="optimizemember-pro-stripe-sp-checkout-form-first-name-label" class="optimizemember-pro-stripe-form-first-name-label optimizemember-pro-stripe-sp-checkout-form-first-name-label">
				<span><?php echo _x("First Name", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="50" autocomplete="off" name="optimizemember_pro_stripe_sp_checkout[first_name]" id="optimizemember-pro-stripe-sp-checkout-first-name" class="optimizemember-pro-stripe-first-name optimizemember-pro-stripe-sp-checkout-first-name form-control" value="%%first_name_value%%" tabindex="10" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-last-name-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-last-name-div optimizemember-pro-stripe-sp-checkout-form-last-name-div">
			<label for="optimizemember-pro-stripe-sp-checkout-last-name" id="optimizemember-pro-stripe-sp-checkout-form-last-name-label" class="optimizemember-pro-stripe-form-last-name-label optimizemember-pro-stripe-sp-checkout-form-last-name-label">
				<span><?php echo _x("Last Name", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="50" autocomplete="off" name="optimizemember_pro_stripe_sp_checkout[last_name]" id="optimizemember-pro-stripe-sp-checkout-last-name" class="optimizemember-pro-stripe-last-name optimizemember-pro-stripe-sp-checkout-last-name form-control" value="%%last_name_value%%" tabindex="20" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-email-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-email-div optimizemember-pro-stripe-sp-checkout-form-email-div">
			<label for="optimizemember-pro-stripe-sp-checkout-email" id="optimizemember-pro-stripe-sp-checkout-form-email-label" class="optimizemember-pro-stripe-form-email-label optimizemember-pro-stripe-sp-checkout-form-email-label">
				<span><?php echo _x("Email Address", "s2member-front", "s2member"); ?> *</span><br />
				<input type="email" aria-required="true" data-expected="email" maxlength="100" autocomplete="off" name="optimizemember_pro_stripe_sp_checkout[email]" id="optimizemember-pro-stripe-sp-checkout-email" class="optimizemember-pro-stripe-email optimizemember-pro-stripe-sp-checkout-email form-control" value="%%email_value%%" tabindex="30" />
			</label>
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Billing Method (powered by Stripe). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-billing-method-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-billing-method-section optimizemember-pro-stripe-sp-checkout-form-billing-method-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-billing-method-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-sp-checkout-form-section-title optimizemember-pro-stripe-form-billing-method-section-title optimizemember-pro-stripe-sp-checkout-form-billing-method-section-title">
			<?php echo _x("Billing Method", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-source-token-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-source-token-div optimizemember-pro-stripe-sp-checkout-form-source-token-div">
			<button id="optimizemember-pro-stripe-sp-checkout-form-source-token-button" class="optimizemember-pro-stripe-form-source-token-button optimizemember-pro-stripe-sp-checkout-form-source-token-button" type="button">
				<i><?php echo _x("[+]", "s2member-front", "s2member"); ?></i> <span><?php echo _x("Add Billing Method", "s2member-front", "s2member"); ?></span>
			</button>
			<div id="optimizemember-pro-stripe-sp-checkout-form-source-token-summary" class="optimizemember-pro-stripe-form-source-token-summary optimizemember-pro-stripe-sp-checkout-form-source-token-summary">
				%%card_token_summary%%
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Billing Address (hidden dynamically when/if no tax details are necessary; and/or when no billing info has been provided yet). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-billing-address-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-billing-address-section optimizemember-pro-stripe-sp-checkout-form-billing-address-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-billing-address-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-sp-checkout-form-section-title optimizemember-pro-stripe-form-billing-address-section-title optimizemember-pro-stripe-sp-checkout-form-billing-address-section-title">
			<?php echo _x("Tax Location", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-state-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-state-div optimizemember-pro-stripe-sp-checkout-form-state-div">
			<label for="optimizemember-pro-stripe-sp-checkout-state" id="optimizemember-pro-stripe-sp-checkout-form-state-label" class="optimizemember-pro-stripe-form-state-label optimizemember-pro-stripe-sp-checkout-form-state-label">
				<span><?php echo _x("State / Province", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="2" autocomplete="off" name="optimizemember_pro_stripe_sp_checkout[state]" id="optimizemember-pro-stripe-sp-checkout-state" class="optimizemember-pro-stripe-state optimizemember-pro-stripe-sp-checkout-state form-control" value="%%state_value%%" tabindex="220" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-zip-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-zip-div optimizemember-pro-stripe-sp-checkout-form-zip-div">
			<label for="optimizemember-pro-stripe-sp-checkout-zip" id="optimizemember-pro-stripe-sp-checkout-form-zip-label" class="optimizemember-pro-stripe-form-zip-label optimizemember-pro-stripe-sp-checkout-form-zip-label">
				<span><?php echo _x("Postal / Zip Code", "s2member-front", "s2member"); ?> *</span><br />
				<input type="text" aria-required="true" maxlength="20" autocomplete="off" name="optimizemember_pro_stripe_sp_checkout[zip]" id="optimizemember-pro-stripe-sp-checkout-zip" class="optimizemember-pro-stripe-zip optimizemember-pro-stripe-sp-checkout-zip form-control" value="%%zip_value%%" tabindex="230" />
			</label>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-country-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-country-div optimizemember-pro-stripe-sp-checkout-form-country-div">
			<label for="optimizemember-pro-stripe-sp-checkout-country" id="optimizemember-pro-stripe-sp-checkout-form-country-label" class="optimizemember-pro-stripe-form-country-label optimizemember-pro-stripe-sp-checkout-form-country-label">
				<span><?php echo _x("Country", "s2member-front", "s2member"); ?> *</span><br />
				<select aria-required="true" name="optimizemember_pro_stripe_sp_checkout[country]" id="optimizemember-pro-stripe-sp-checkout-country" class="optimizemember-pro-stripe-country optimizemember-pro-stripe-sp-checkout-country form-control" tabindex="240">
					%%country_options%%
				</select>
			</label>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-ajax-tax-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-ajax-tax-div optimizemember-pro-stripe-sp-checkout-form-ajax-tax-div">
			<!-- Sales Tax will be displayed here via Ajax; based on state, country, and/or zip code range. -->
		</div>
		<div style="clear:both;"></div>
	</div>

	<!-- Captcha ( A reCaptcha section, with a required security code will appear here; if captcha="1" ). -->
	%%captcha%%

	<!-- Checkout Now (this holds the submit button, and also some dynamic hidden input variables). -->
	<div id="optimizemember-pro-stripe-sp-checkout-form-submission-section" class="optimizemember-pro-stripe-form-section optimizemember-pro-stripe-sp-checkout-form-section optimizemember-pro-stripe-form-submission-section optimizemember-pro-stripe-sp-checkout-form-submission-section">
		<div id="optimizemember-pro-stripe-sp-checkout-form-submission-section-title" class="optimizemember-pro-stripe-form-section-title optimizemember-pro-stripe-sp-checkout-form-section-title optimizemember-pro-stripe-form-submission-section-title optimizemember-pro-stripe-sp-checkout-form-submission-section-title">
			<?php echo _x("Checkout Now", "s2member-front", "s2member"); ?>
		</div>
		<div id="optimizemember-pro-stripe-sp-checkout-form-submit-div" class="optimizemember-pro-stripe-form-div optimizemember-pro-stripe-sp-checkout-form-div optimizemember-pro-stripe-form-submit-div optimizemember-pro-stripe-sp-checkout-form-submit-div">
			%%hidden_inputs%% <!-- Auto-filled by the s2Member software. Do NOT remove this under any circumstance. -->
			<button style="padding:15px;" type="submit" id="optimizemember-pro-stripe-sp-checkout-submit" class="optimizemember-pro-stripe-submit optimizemember-pro-stripe-sp-checkout-submit btn btn-primary" tabindex="500"><?php echo esc_html(_x("Submit Form", "s2member-front", "s2member")); ?></button>
		</div>
		<div style="clear:both;"></div>
	</div>
</form>