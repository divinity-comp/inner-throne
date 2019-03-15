var rcp_validating_discount = false;
var rcp_validating_gateway  = false;
var rcp_validating_level    = false;
var rcp_processing          = false;
var rcp_calculating_total   = false;
var gateway_submits_form    = false;

jQuery(document).ready(function($) {

	// Initial validation of subscription level and gateway options
	rcp_validate_form( true );
	rcp_calc_total();

	// Trigger gateway change event when gateway option changes
	$('#rcp_payment_gateways select, #rcp_payment_gateways input').change( function() {

		$('body').trigger( 'rcp_gateway_change' );

	});

	// Trigger subscription level change event when level selection changes
	$('.rcp_level').change(function() {

		$('body').trigger( 'rcp_level_change' );

	});

	$('body').on( 'rcp_gateway_change', function() {

		rcp_validate_form( true );

	}).on( 'rcp_level_change', function() {

		rcp_validate_form( true );

	});

	// Validate discount code
	$('#rcp_apply_discount').on( 'click', function(e) {

		e.preventDefault();

		$('body').trigger( 'rcp_discount_change' );

		rcp_validate_discount();

	});

	$(document.getElementById('rcp_auto_renew')).on('change', rcp_calc_total);
	$('body').on( 'rcp_discount_change rcp_level_change rcp_gateway_change', rcp_calc_total);

	/**
	 * If reCAPTCHA is enabled, disable the submit button
	 * until it is successfully completed, at which point
	 * it triggers rcp_validate_recaptcha().
	 */
	if ( '1' === rcp_script_options.recaptcha_enabled ) {
		jQuery('#rcp_registration_form #rcp_submit').prop('disabled', true);
	}

	$(document).on('click', '#rcp_registration_form #rcp_submit', function(e) {

		e.preventDefault();

		var submission_form = document.getElementById('rcp_registration_form');
		var form = $('#rcp_registration_form');
		var form_id = form.attr('id');

		if( typeof submission_form.checkValidity === "function" && false === submission_form.checkValidity() ) {
			return;
		}

		var submit_register_text = $(this).val();

		form.block({
			message: rcp_script_options.pleasewait,
			css: {
				border: 'none',
				padding: '15px',
				backgroundColor: '#000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: .5,
				color: '#fff'
			}
		});

		$('#rcp_submit', form).val( rcp_script_options.pleasewait );

		// Don't allow form to be submitted multiple times simultaneously
		if( rcp_processing ) {
			return;
		}

		rcp_processing = true;

		$.post( rcp_script_options.ajaxurl, form.serialize() + '&action=rcp_process_register_form&rcp_ajax=true', function(response) {

			$('.rcp-submit-ajax', form).remove();
			$('.rcp_message.error', form).remove();

		}).success(function( response ) {
		}).done(function( response ) {
			window.setTimeout(function() {
				$('.rcp_message.error').attr("tabindex",-1).focus();
			}, 10);
		}).fail(function( response ) {
			console.log(response);
		}).always(function( response ) {
		});

	});

	$(document).ajaxComplete( function( event, xhr, settings ) {

		// Check for the desired ajax event
		if ( ! settings.hasOwnProperty('data') || settings.data.indexOf('rcp_process_register_form') === -1 ) {
			return;
		}

		// Check for the required properties
		if ( ! xhr.hasOwnProperty('responseJSON') || ! xhr.responseJSON.hasOwnProperty('data') ) {
			return;
		}

		if ( xhr.responseJSON.data.success !== true ) {
			$('#rcp_registration_form #rcp_submit').val( rcp_script_options.register );
			$('#rcp_registration_form #rcp_submit').before( xhr.responseJSON.data.errors );
			$('#rcp_registration_form #rcp_register_nonce').val( xhr.responseJSON.data.nonce );
			$('#rcp_registration_form').unblock();
			rcp_processing = false;
			return;
		}

		// Check if gateway supports form submission
		if ( xhr.responseJSON.data.gateway.supports && xhr.responseJSON.data.gateway.supports.indexOf('gateway-submits-form') !== -1 ) {
			gateway_submits_form = true;
		} else {
			gateway_submits_form = false;
		}

		$('body').trigger('rcp_register_form_submission', [xhr.responseJSON.data, event.target.forms.rcp_registration_form.id] );

		if ( xhr.responseJSON.data.total == 0 || ! gateway_submits_form ) {
			document.getElementById('rcp_registration_form').submit();
		}

	});

});

function rcp_validate_form( validate_gateways ) {

	// Validate the subscription level
	rcp_validate_subscription_level();

	if( validate_gateways ) {
		// Validate the discount selected gateway
		rcp_validate_gateways();
	}

	rcp_validate_discount();

}

function rcp_validate_subscription_level() {

	if( rcp_validating_level ) {
		return;
	}

	var $         = jQuery;
	var is_free   = false;
	var options   = [];
	var level     = $( '#rcp_subscription_levels input:checked' );
	var full      = $('.rcp_gateway_fields').hasClass( 'rcp_discounted_100' );
	var lifetime  = level.data( 'duration' ) == 'forever';
	var level_has_trial = rcp_script_options.trial_levels.indexOf(level.val()) !== -1;

	rcp_validating_level = true;

	if( level.attr('rel') == 0 ) {
		is_free = true;
	}

	if( is_free ) {

		$('.rcp_gateway_fields,#rcp_auto_renew_wrap,#rcp_discount_code_wrap').hide();
		$('.rcp_gateway_fields').removeClass( 'rcp_discounted_100' );
		$('#rcp_discount_code_wrap input').val('');
		$('.rcp_discount_amount,#rcp_gateway_extra_fields').remove();
		$('.rcp_discount_valid, .rcp_discount_invalid').hide();
		$('#rcp_auto_renew_wrap input').prop('checked', false);

	} else {

		if( full ) {
			$('#rcp_gateway_extra_fields').remove();
		} else if( lifetime ) {
			$('#rcp_auto_renew_wrap input').prop('checked', false);
			$('#rcp_auto_renew_wrap').hide();
		} else {
			$('.rcp_gateway_fields,#rcp_auto_renew_wrap').show();
		}

		if( level_has_trial ) {
			$('#rcp_auto_renew_wrap input').prop('checked', true);
			$('#rcp_auto_renew_wrap').hide();
		}

		$('#rcp_discount_code_wrap').show();

	}

	rcp_validating_level = false;

}

function rcp_get_gateway() {
	var gateway;
	var $ = jQuery;

	if( $('#rcp_payment_gateways').length > 0 ) {

		gateway = $( '#rcp_payment_gateways select option:selected' );

		if( gateway.length < 1 ) {

			// Support radio input fields
			gateway = $( 'input[name="rcp_gateway"]:checked' );

		}

	} else {

		gateway = $( 'input[name="rcp_gateway"]' );

	}

	return gateway;
}

function rcp_validate_gateways() {

	if( rcp_validating_gateway ) {
		return;
	}

	var $        = jQuery;
	var form     = $('#rcp_registration_form');
	var is_free  = false;
	var options  = [];
	var level    = $( '#rcp_subscription_levels input:checked' );
	// register-single.php template loaded
	if ( ! level.val() ) {
		var level = $('#rcp_submit_wrap input[name="rcp_level"]');
	}
	var full     = $('.rcp_gateway_fields').hasClass( 'rcp_discounted_100' );
	var lifetime = level.data( 'duration' ) == 'forever';
	var level_has_trial = rcp_script_options.trial_levels.indexOf(level.val()) !== -1;
	var gateway  = rcp_get_gateway();

	rcp_validating_gateway = true;

	if( level.attr('rel') == 0 ) {
		is_free = true;
	}

	$('.rcp_message.error', form).remove();

	if( is_free ) {

		$('.rcp_gateway_fields').hide();
		$('#rcp_gateway_extra_fields').remove();

	} else {

		if( full ) {

			$('#rcp_gateway_extra_fields').remove();

		} else {

			form.block({
				message: rcp_script_options.pleasewait,
				css: {
					border: 'none',
					padding: '15px',
					backgroundColor: '#000',
					'-webkit-border-radius': '10px',
					'-moz-border-radius': '10px',
					opacity: .5,
					color: '#fff'
				}
			});

			$('.rcp_gateway_fields').show();
			var data = { action: 'rcp_load_gateway_fields', rcp_gateway: gateway.val() };

			$.post( rcp_script_options.ajaxurl, data, function(response) {
				$('#rcp_gateway_extra_fields').remove();
				if( response.success && response.data.fields ) {
					if( $('.rcp_gateway_fields' ).length ) {

						$( '<div class="rcp_gateway_' + gateway.val() + '_fields" id="rcp_gateway_extra_fields">' + response.data.fields + '</div>' ).insertAfter('.rcp_gateway_fields');

					} else {

						// Pre 2.1 template files
						$( '<div class="rcp_gateway_' + gateway.val() + '_fields" id="rcp_gateway_extra_fields">' + response.data.fields + '</div>' ).insertAfter('.rcp_gateways_fieldset');

					}
				}
				form.unblock();
			});
		}

		// Auto Renew checkbox
		if ( 'yes' == gateway.data('supports-recurring') ) {
			// Set up defaults
			$('#rcp_auto_renew_wrap input').prop('checked', rcp_script_options.auto_renew_default);
			$('#rcp_auto_renew_wrap').show();

			// Uncheck and hide if free level, lifetime level, or 100% discount applied
			// @todo one-time discounts
			if ( full || lifetime || is_free ) {
				$('#rcp_auto_renew_wrap input').prop('checked', false);
				$('#rcp_auto_renew_wrap').hide();
			}

			// Check and hide if both level and gateway support trial
			if ( level_has_trial && 'yes' == gateway.data( 'supports-trial' ) && ! rcp_script_options.user_has_trialed ) {
				$('#rcp_auto_renew_wrap input').prop('checked', true);
				$('#rcp_auto_renew_wrap').hide();
			}

		} else {
			// Uncheck and hide since gateway doesn't support recurring
			$('#rcp_auto_renew_wrap input').prop('checked', false);
			$('#rcp_auto_renew_wrap').hide();
		}


		$('#rcp_discount_code_wrap').show();

	}

	rcp_validating_gateway = false;

}

function rcp_validate_discount() {

	if( rcp_validating_discount ) {
		return;
	}

	var $ = jQuery;
	var gateway_fields = $('.rcp_gateway_fields');
	var discount = $('#rcp_discount_code').val();
	var is_free   = false;
	var level     = $( '#rcp_subscription_levels input:checked' );

	if( level.attr('rel') == 0 ) {
		is_free = true;
	}

	if( $('#rcp_subscription_levels input:checked').length ) {

		var subscription = $('#rcp_subscription_levels input:checked').val();

	} else {

		var subscription = $('input[name="rcp_level"]').val();

	}

	if( ! discount ) {

		// Reset everything in case a previous discount was just removed.
		$('.rcp_discount_valid, .rcp_discount_invalid').hide();
		if ( is_free ) {
			$('#rcp_auto_renew_wrap').hide();
		} else {
			$('#rcp_auto_renew_wrap').show();
		}
		gateway_fields.show().removeClass('rcp_discounted_100');
		rcp_validate_gateways();

		return;
	}

	var data = {
		action: 'validate_discount',
		code: discount,
		subscription_id: subscription
	};

	rcp_validating_discount = true;

	$.post(rcp_script_options.ajaxurl, data, function(response) {

		$('.rcp_discount_amount').remove();
		$('.rcp_discount_valid, .rcp_discount_invalid').hide();

		if( ! response.valid ) {

			// code is invalid
			$('.rcp_discount_invalid').show();
			gateway_fields.removeClass('rcp_discounted_100');
			$('.rcp_gateway_fields,#rcp_auto_renew_wrap').show();
			rcp_validate_gateways();

		} else if( response.valid ) {

			// code is valid
			$('.rcp_discount_valid').show();
			$('#rcp_discount_code_wrap label').append( '<span class="rcp_discount_amount"> - ' + response.amount + '</span>' );

			if( response.full ) {

				$('#rcp_auto_renew_wrap').hide();
				gateway_fields.hide().addClass('rcp_discounted_100');
				$('#rcp_gateway_extra_fields').remove();

			} else {

				$('#rcp_auto_renew_wrap').show();
				gateway_fields.show().removeClass('rcp_discounted_100');
				rcp_validate_gateways();

			}

		}

		rcp_validating_discount = false;
		$('body').trigger('rcp_discount_applied', [ response ] );

	});
}

function rcp_calc_total() {

	var $      = jQuery;
	var $total = $( '.rcp_registration_total' );
	var form   = $( '#rcp_registration_form' );
	var values = form.serializeArray();
	var skip   = [ 'rcp_register_nonce', 'rcp_user_pass', 'rcp_user_pass_confirm', 'rcp_card_number' ];
	var data   = {
		action: 'rcp_calc_discount'
	};

	if ( ! $total.length ) {
		return;
	}

	rcp_calculating_total = true;

	// loop through form values and exclude those we've marked to skip
	for (var i = 0; i < values.length; i++) {
		if (-1 !== skip.indexOf(values[i]['name'])) {
			continue;
		}

		data[values[i]['name']] = values[i]['value'];
	}

	$.post( rcp_script_options.ajaxurl, data, function(response) {
		rcp_calculating_total = false;

		if (undefined !== response.total ) {
			$total.html(response.total);
		}

	});

}

/**
 * Enables the submit button when a successful
 * reCAPTCHA response is triggered.
 *
 * This function is referenced via the data-callback
 * attribute on the #rcp_recaptcha element.
 */
function rcp_validate_recaptcha(response) {
	jQuery('#rcp_registration_form #rcp_submit').prop('disabled', false);
}
