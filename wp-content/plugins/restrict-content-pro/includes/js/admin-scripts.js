jQuery(document).ready(function($) {

	// Tooltips
	$('.rcp-help-tip').tooltip({
		content: function() {
			return $(this).prop('title');
		},
		position: {
			my: 'center top',
			at: 'center bottom+10',
			collision: 'flipfit'
		},
		hide: {
			duration: 500
		},
		show: {
			duration: 500
		}
	});

	var restriction_control        = $('#rcp-restrict-by');
	var role_control               = $('#rcp-metabox-field-role');
	var sub_levels_control         = $('#rcp-metabox-field-levels');
	var sub_levels_select          = $('.rcp-subscription-levels');
	var sub_levels_radio           = $('input[name=rcp_subscription_level_any_set]');
	var access_levels_control      = $('#rcp-metabox-field-access-levels');
	var additional_options_control = $('#rcp-metabox-field-options');

	var Settings_Controls = {
		prepare_type: function(type) {
			if ('unrestricted' === type) {
				role_control.hide();
				sub_levels_control.hide();
				access_levels_control.hide();
				additional_options_control.hide();
			}

			if ('registered-users' === type) {
				role_control.show();
				sub_levels_control.hide();
				access_levels_control.hide();
				additional_options_control.show();
			}

			if ('subscription-level' === type) {
				role_control.show();
				sub_levels_control.show();
				access_levels_control.hide();
				additional_options_control.show();
			}

			if ('access-level' === type) {
				role_control.show();
				sub_levels_control.hide();
				access_levels_control.show();
				additional_options_control.show();
			}
		},

		prepare_sub_levels: function(type) {
			if ('any' === type) {
				sub_levels_select.hide();
			}

			if ('any-paid' === type) {
				sub_levels_select.hide();
			}

			if ('specific' === type) {
				sub_levels_radio.show();
				sub_levels_select.show();
				access_levels_control.hide();
				additional_options_control.show();
			}
		}
	}

	var restriction_type = restriction_control.val();
	Settings_Controls.prepare_type(restriction_type);

	// restrict content metabox
	restriction_control.on('change', function() {
		var type = $(this).val();
		Settings_Controls.prepare_type(type);
	});

	sub_levels_radio.on('change', function() {
		var type = $(this).val();
		Settings_Controls.prepare_sub_levels(type);
	});

	// Deselect all roles if "All" is selected.
	$('.rcp-user-role').change(function (e) {
		var this_role_value = $(this).val();
		if ( 'all' === this_role_value && this.checked ) {
			// Uncheck all other roles when selecting "All".
			$(this).parents('p').find('input:checkbox').each(function() {
				if ( $(this).val() !== 'all' ) {
					$(this).removeAttr( 'checked' );
				}
			});
		} else if ( 'all' !== this_role_value && this.checked ) {
			// Uncheck "All" when selecting another role.
			$('#rcp_user_level_all').removeAttr( 'checked' );
		}
	});

	// settings tabs

	//when the history state changes, gets the url from the hash and display
	$(window).bind( 'hashchange', function(e) {

		if ( $('#rcp-settings-wrap').length == 0) {
			return;
		}

		var url = jQuery.param.fragment();

		//hide all
		jQuery( '#rcp-settings-wrap #tab_container .tab_content' ).hide();
		jQuery( '#rcp-settings-wrap #tab_container' ).children(".tab_content").hide();
		jQuery( '#rcp-settings-wrap .nav-tab-wrapper a' ).removeClass("nav-tab-active");

		//find a href that matches url
		if (url) {
			jQuery( '#rcp-settings-wrap .nav-tab-wrapper a[href="#' + url + '"]' ).addClass( 'nav-tab-active' );
			jQuery("#rcp-settings-wrap #tab_container #" + url).addClass("selected").fadeIn();
		} else {
			jQuery( '#rcp-settings-wrap  h2.nav-tab-wrapper a[href="#general"]' ).addClass( 'nav-tab-active' );
			jQuery("#rcp-settings-wrap  #tab_container #general").addClass("selected nav-tab-active").fadeIn();
		}
	});

	// Since the event is only triggered when the hash changes, we need to trigger
	// the event now, to handle the hash the page may have loaded with.
	$(window).trigger( 'hashchange' );


	if($('.rcp-datepicker').length > 0 ) {
		var dateFormat = 'yy-mm-dd';
		$('.rcp-datepicker').datepicker({dateFormat: dateFormat});
	}
	if($('.rcp-datetimepicker').length > 0 ) {
		$('.rcp-datetimepicker').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss'
		});
	}
	$('.rcp_cancel').click(function() {
		if ($(this).is('[disabled=disabled]')) {
			return false;
		}

		if(confirm(rcp_vars.cancel_user)) {
			return true;
		} else {
			return false;
		}
	});
	$('#rcp-expire-membership-button').click(function() {
		if(confirm(rcp_vars.expire_membership)) {
			return true;
		} else {
			return false;
		}
	});
	$('.rcp_delete_subscription').click(function() {
		if(confirm(rcp_vars.delete_subscription)) {
			return true;
		}
		return false;
	});
	$('.rcp-delete-payment').click(function() {
		if(confirm(rcp_vars.delete_payment)) {
			return true;
		}
		return false;
	});
	$('.rcp_delete_discount').click(function() {
		if(confirm(rcp_vars.delete_discount)) {
			return true;
		}
		return false;
	});
	$('.rcp-delete-reminder').click(function () {
		if(confirm(rcp_vars.delete_reminder)) {
			return true;
		}
		return false;
	});
	$('.rcp-delete-membership').click(function () {
		if(confirm(rcp_vars.delete_membership)) {
			return true;
		}
		return false;
	});
	$('.rcp-delete-customer').click(function () {
		if(confirm(rcp_vars.delete_customer)) {
			return true;
		}
		return false;
	});
	$('#rcp-add-new-member').submit(function() {
		if($('#rcp-user').val() == '') {
			alert(rcp_vars.missing_username);
			return false;
		}
		return true;
	});
	// make columns sortable via drag and drop
	if( $('.rcp-subscriptions tbody').length ) {
		$(".rcp-subscriptions tbody").sortable({
			handle: '.rcp-drag-handle', items: '.rcp-subscription', opacity: 0.6, cursor: 'move', axis: 'y', update: function() {
				var order = $(this).sortable("serialize") + '&action=update-subscription-order';
				$.post(ajaxurl, order, function(response) {
					// response here
				});
			}
		});
	}

	// auto calculate the subscription expiration when manually adding a user
	$('#rcp-membership-level').change(function() {
		var level_id = $('option:selected', this).val();
		data = {
			action: 'rcp_get_subscription_expiration',
			subscription_level: level_id
		};
		$.post(ajaxurl, data, function(response) {
			let expiration = $( '#rcp-membership-expiration' );
			let no_expiration = $( '#rcp-membership-expiration-none' );

			if ( 'none' === response ) {
				expiration.val( '' );
				no_expiration.prop( 'checked', true );
			} else {
				expiration.val( response );
				no_expiration.prop( 'checked', false );
			}
		});
	});

	$('.rcp-user-search').keyup(function() {
		var user_search = $(this).val();
		$('.rcp-ajax').show();
		data = {
			action: 'rcp_search_users',
			user_name: user_search,
			rcp_nonce: rcp_vars.rcp_member_nonce
		};

		$.ajax({
         type: "POST",
         data: data,
         dataType: "json",
         url: ajaxurl,
			success: function (search_response) {

				$('.rcp-ajax').hide();

				$('#rcp_user_search_results').html('');

				if(search_response.id == 'found') {
					$(search_response.results).appendTo('#rcp_user_search_results');
				} else if(search_response.id == 'fail') {
					$('#rcp_user_search_results').html(search_response.msg);
				}
			}
		});
	});
	$('body').on('click.rcpSelectUser', '#rcp_user_search_results a', function(e) {
		e.preventDefault();
		var login = $(this).data('login');
		$('#rcp-user').val(login);
		$('#rcp_user_search_results').html('');
	});

	$( '#rcp-graphs-date-options' ).change( function() {
		var $this = $(this);
		if( $this.val() == 'other' ) {
			$( '#rcp-date-range-options' ).show();
		} else {
			$( '#rcp-date-range-options' ).hide();
		}
	});

	$( '#rcp-unlimited' ).change( function() {
		var $this = $(this);
		if( $this.attr( 'checked' ) ) {
			$( '#rcp-expiration' ).val('none');
		} else if( 'none' == $( '#rcp-expiration' ).val() ) {
			$( '#rcp-expiration' ).val('').trigger('focus');
		}
	});

	// WP 3.5+ uploader
	var file_frame;
	$('body').on('click', '.rcp-upload', function(e) {

		e.preventDefault();

		var formfield = $(this).prev();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'select',
			title: rcp_vars.choose_logo,
			multiple: false,
			library: {
				type: 'image'
			},
			button: {
				text: rcp_vars.use_as_logo
			}
		});

		file_frame.on( 'menu:render:default', function(view) {
	        // Store our views in an object.
	        var views = {};

	        // Unset default menu items
	        view.unset('library-separator');
	        view.unset('gallery');
	        view.unset('featured-image');
	        view.unset('embed');

	        // Initialize the views in our view object.
	        view.set(views);
	    });

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {

			var attachment = file_frame.state().get('selection').first().toJSON();
			formfield.val(attachment.url);

		});

		// Finally, open the modal
		file_frame.open();
	});

	$('#rcp-bulk-select-all').on('change', function() {
		if( $(this).prop('checked') ) {
			$('#rcp-members-form .rcp-member-cb').prop('checked', true );
		} else {
			$('#rcp-members-form .rcp-member-cb').prop('checked', false );
		}
	});


	// Cancel user's subscription when updating status to "Cancelled".
	$('#rcp-status').on('change', function () {
		if ( 'cancelled' == $(this).val() ) {
			if ( rcp_vars.can_cancel_member ) {
				$(this).parent().append('<p id="rcp-cancel-subscription-wrap"><input type="checkbox" id="rcp-cancel-subscription" name="cancel_subscription" value="1"><label for="rcp-cancel-subscription">' + rcp_vars.cancel_subscription + '</label></p>');
			}
			$('#rcp-revoke-access-wrap').show();
		} else {
			$('#rcp-cancel-subscription-wrap').remove();
			$('#rcp-revoke-access-wrap').hide();
		}
	});

	// Show "Revoke access now" checkbox when marking as cancelled via bulk edit.
	$('#rcp-bulk-member-action').on('change', function () {
		if ('mark-cancelled' == $(this).val()) {
			$('#rcp-revoke-access-wrap').show();
		} else {
			$('#rcp-revoke-access-wrap').hide();
		}
	});

	// Show/hide auto renew default based on settings.
	$('#rcp_settings_auto_renew').on('change', function() {
		if( '3' == $(this).val() ) {
			$(this).parents('tr').next().css('display', 'table-row');
		} else {
			$(this).parents('tr').next().css('display', 'none');
		}
	});

	// Show/hide email fields based on their activation state.
	$('.rcp-disable-email').on('change', function () {
		var subject  = $(this).parents('tr').next();
		var body     = subject.next();
		var disabled = false;

		if( 'SELECT' == $(this).prop('tagName') && 'off' == $(this).val() ) {
			// Select dropdowns, like email verification.
			disabled = true;
		} else {
			// Checkboxes.
			disabled = $(this).prop('checked');
		}

		if( true === disabled ) {
			subject.css('display', 'none');
			body.css('display', 'none');
		} else {
			subject.css('display', 'table-row');
			body.css('display', 'table-row');
		}
	});

	// Show/hide "times billed" box when option changes.
	$( '#rcp-maximum-renewals-setting' ).change( function() {
		var after_payment_field = $(this).parents('tr').next();
		var show = 'forever' !== $(this).val();

		$('#rcp-maximum-renewals').toggle( show );
		after_payment_field.toggle( show );
	});

	// Changes the currency symbol in the Currency Position dropdown to match the Currency setting.
	var currencySelect = $('#rcp_settings\\[currency\\]');
	if (currencySelect.length) {
		var currencies = JSON.parse(rcp_vars.currencies);
		var currentSymbol = currencies[currencySelect.val()].match(/\(([^)]+)\)/)[1];
		var currencyPositionBefore = $('#rcp_settings\\[currency_position\\] option[value="before"]');
		var currencyPositionAfter = $('#rcp_settings\\[currency_position\\] option[value="after"]');

		currencyPositionBefore.text(function () {
			return $(this).text().replace("$", currentSymbol);
		});

		currencyPositionAfter.text(function () {
			return $(this).text().replace("$", currentSymbol);
		});

		$(currencySelect).on('change', function () {
			var newCurrency = currencies[$(this).val()].match(/\(([^)]+)\)/)[1];

			currencyPositionBefore.text(function () {
				return $(this).text().replace(currentSymbol, newCurrency);
			});
			currencyPositionAfter.text(function () {
				return $(this).text().replace(currentSymbol, newCurrency);
			});

			currentSymbol = newCurrency;
		});
	}

	// Customer: show/hide fields based on new or existing user.
	$('input[name="rcp_customer_user_account"]:radio').change(function() {
		if ( 'new' === this.value ) {
			$('.rcp-customer-new-user-field').show();
		} else {
			$('.rcp-customer-new-user-field').hide();
		}
	});

	/**
	 * Membership management screen JS
	 */
	var RCP_Membership = {

		vars: {
			member_card_wrap_editable: $('.rcp-membership-card-wrapper .rcp-editable'),
			member_card_wrap_edit_item: $('.rcp-membership-card-wrapper .rcp-edit-item')
		},

		init: function () {
			this.recurring_select();
			this.edit_created_date();
			this.edit_expiration();
			this.never_expires();
			this.edit_gateway_customer_id();
			this.edit_gateway_subscription_id();
			this.set_billing_cycle();

			// Trigger change to set cycle on initial page load.
			$('#rcp-membership-level').trigger( 'change' );
		},

		/**
		 * Edit Subscription Text Input
		 *
		 * @param link object The edit/cancelled element the user clicked.
		 * @param input object The editable field.
		 *
		 * @since 3.0
		 */
		edit_membership_input: function ( link, input ) {

			// User clicks edit
			if ( link.text() === rcp_vars.action_edit ) {
				// Preserve current value
				link.data( 'current-value', input.val() );
				// Update text to 'cancel'
				link.text( rcp_vars.action_cancel );
			} else {
				// User clicked cancel, return previous value
				input.val( link.data('current-value') );
				// Update link text back to 'edit'
				link.text( rcp_vars.action_edit );
			}

		},

		/**
		 * Editing the "recurring" flag for a membership.
		 *
		 * @since 3.0
		 */
		recurring_select: function () {
			$( document.body ).on( 'change', '#rcp-recurring', function ( e ) {
				$( '#rcp-membership-recurring-update-notice' ).slideToggle();
			} );
		},

		/**
		 * Editing the expiration date for a membership.
		 *
		 * @since 3.0
		 */
		edit_created_date: function () {
			$( '#rcp-edit-membership-created' ).on( 'click', function ( e ) {
				e.preventDefault();

				let link = $( this );
				let created_input = $( '#rcp-membership-created' );

				RCP_Membership.edit_membership_input( link, created_input );

				$( '.rcp-membership-created' ).toggle();
			} );
		},

		/**
		 * Editing the expiration date for a membership.
		 *
		 * @since 3.0
		 */
		edit_expiration: function () {
			$( '#rcp-edit-membership-expiration' ).on( 'click', function ( e ) {
				e.preventDefault();

				let link = $( this );
				let expiration_input = $( '#rcp-membership-expiration' );

				RCP_Membership.edit_membership_input( link, expiration_input );

				$( '.rcp-membership-expiration' ).toggle();
				$( '#rcp-membership-expiration-update-notice' ).slideToggle();
				$( '.rcp-membership-expiration-none-wrap' ).toggle();
			} );
		},

		/**
		 * When "never expires" is checked, wipe the expiration date.
		 */
		never_expires: function () {
			$( '#rcp-membership-expiration-none' ).change( function () {
				if ( $( this ).is( ':checked' ) ) {
					$( '#rcp-membership-expiration' ).val( '' );
				}
			} );
		},

		/**
		 * Editing the gateway customer ID for a membership.
		 *
		 * @since 3.0
		 */
		edit_gateway_customer_id: function () {
			$( '#rcp-edit-membership-gateway-customer-id' ).on( 'click', function ( e ) {
				e.preventDefault();

				let link = $( this );
				let gateway_input = $( '#rcp-membership-gateway-customer-id' );

				RCP_Membership.edit_membership_input( link, gateway_input );

				$( '#rcp-membership-gateway-customer-id' ).toggle();
			} );
		},

		/**
		 * Editing the gateway subscription ID for a membership.
		 *
		 * @since 3.0
		 */
		edit_gateway_subscription_id: function () {
			$( '#rcp-edit-membership-gateway-subscription-id' ).on( 'click', function ( e ) {
				e.preventDefault();

				let link = $( this );
				let gateway_input = $( '#rcp-membership-gateway-subscription-id' );

				RCP_Membership.edit_membership_input( link, gateway_input );

				$( '#rcp-membership-gateway-subscription-id' ).toggle();
				$( '#rcp-membership-gateway-subscription-id-update-notice' ).slideToggle();
			} );
		},

		/**
		 * Set the billing cycle fields when changing the membership level.
		 *
		 * @since 3.0
		 */
		set_billing_cycle: function () {
			$('#rcp-add-membership-info').on('change', '#rcp-membership-level', function () {
				let object_id = $(this).val();
				let initial_amount = $('#rcp-initial-amount');
				let recurring_amount = $('#rcp-recurring-amount');
				let recurring_wrap = $('#rcp-billing-cycle-recurring');

				let data = {
					action: 'rcp_get_membership_billing_cycle',
					object_id: object_id,
					rcp_nonce: rcp_vars.rcp_member_nonce
				};

				$.post(ajaxurl, data, function(response) {

					if ( 'free' === response.data ) {
						initial_amount.val('0.00');
						recurring_amount.val('0.00');
					} else {
						initial_amount.val(response.data.initial_amount);
						recurring_amount.val(response.data.recurring_amount);
					}

					if ( 'free' === response.data || 0 == response.data.recurring_amount ) {
						recurring_wrap.hide();
					} else {
						recurring_wrap.show();
					}
				});
			});
		},

	};
	RCP_Membership.init();

	if( window.adminpage === 'restrict_page_rcp-settings' ) {
		RCP_Settings.init();
	}
});

let RCP_Settings = {

	init: function() {
		this.listeners();
	},

	listeners: function() {
		let sandboxCheckbox = document.getElementById( 'rcp_settings[sandbox]' );

		if( ! sandboxCheckbox ) {
			return;
		}

		sandboxCheckbox.addEventListener( 'change', function() {
			let testpub = document.getElementById( 'rcp_settings[stripe_test_publishable]' );
			let livepub = document.getElementById( 'rcp_settings[stripe_live_publishable]' );
			let notice = document.getElementById( 'rcp-sandbox-toggle-notice' );

			if( this.checked && testpub.value === '' ) {
				RCP_Settings.showErrorNotice( notice );
			}

			if( ! this.checked && livepub.value === '' ) {
				RCP_Settings.showErrorNotice( notice );
			}
		} );
	},

	showErrorNotice: function( element = false ) {
		if( ! element ) {
			return;
		}

		element.className = 'notice error';
		element.style.visibility = 'visible';
	}
};