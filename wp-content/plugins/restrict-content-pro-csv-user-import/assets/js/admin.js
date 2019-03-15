jQuery(document).ready(function ($) {

	var subscription_level = $('#rcp_level');
	var member_status      = $('#rcp_status');
	var edit_status        = $('#rcp_csvui_edit_status');

	var RCP_CSV_UI = {

		/**
		 * Load up some functions on change/click events.
		 */
		init: function() {
			subscription_level.on('change', this.enable_disable_status);
			edit_status.on('click', this.edit_status);
		},

		/**
		 * Disable status editing on free levels, enable it on paid ones.
		 */
		enable_disable_status: function() {
			var is_free_level = rcp_csvui_vars.free_level_ids.indexOf(subscription_level.val()) !== -1;

			if ( ! is_free_level ) {
				// Enable the status select box and hide the "Edit" link.
				member_status.val('active').prop('disabled', false).change();
				edit_status.hide();
			} else {
				// Set the status to "free", disable editing, and show the "Edit" link.
				member_status.val('free').prop('disabled', true).change();
				edit_status.show();
			}
		},

		/**
		 * Show a confirm window when clicking the "Edit Status" link. If confirmed, enable editing on the status.
		 * @param e
		 */
		edit_status: function(e) {
			e.preventDefault();

			if ( confirm( rcp_csvui_vars.confirm_edit_status ) ) {
				member_status.prop('disabled', false).change();
				edit_status.hide();
			}
		}

	};

	RCP_CSV_UI.init();
	RCP_CSV_UI.enable_disable_status();

});