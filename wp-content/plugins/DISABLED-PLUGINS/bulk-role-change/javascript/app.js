jQuery(document).ready(function($) {
		$('.js-transfer-roles').on('change', function(e) {
			var _this 	= $(this);
			var _role 	= _this.val();
			var _target = _this.data('target-id');
			$('#'+_target).val('Please wait...');
			if (_target == 'input-transfer-from') { $('.label-transfer-from').text(_role); }
			if (_target == 'input-transfer-to') { $('.label-transfer-to').text(_role); }
			var data = {
				'action': 'bulk_get_num_users',
				'role': _role
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				if ( response == '' || isNaN(response) )
					$('#'+_target).val(0);

				else
					$('#'+_target).val(response);
			});
		});
		$('#input-confirm-transfer').on('change', function(e) {
			if ($(this).is(':checked')) {
				$('#btn-transfer-role').removeClass('btn-disabled');
			} else {
				$('#btn-transfer-role').addClass('btn-disabled');
			}
		});
		$('#btn-transfer-role').on('click', function(e) {
		
			// validation checks go here...
			// --
			var _transfer_from = $.trim($('#js-transfer-from').val());
			var _transfer_to = $.trim($('#js-transfer-to').val());
			var _users_to_move = parseInt($('#input-transfer-from').val());
			// make sure both selects aren't the same
			// --
			if (_transfer_from == _transfer_to) {
				$('.alert').hide();
				$('#js-alert-error').html('<strong>Ooops!</strong> You can\'t transfer users to the same role.').show();
			// make sure the from select has users to transfer
			// --
			} else if (_users_to_move == '0' || _users_to_move == '' || isNaN(_users_to_move)) {
				$('.alert').hide();
				$('#js-alert-error').html('<strong>Ooops!</strong> There are no users in this role to transfer.').show();
			} else {
				$(this).hide();
				$('#confirm-transfer-role').show();
			}
		});
		$('#btn-cancel-transfer-role').on('click', function(e) {
		
			$('#confirm-transfer-role').hide();
			$('#btn-transfer-role').show();
		});
		$('#btn-confirm-transfer-role').on('click', function(e) {
			if ($('#input-confirm-transfer').is(':checked')) {
				var _transfer_from = $.trim($('#js-transfer-from').val());
				var _transfer_to = $.trim($('#js-transfer-to').val());
				if (_transfer_from != '' && _transfer_to != '') {
					var data = {
						'action': 'bulk_change_role',
						'current_role': _transfer_from,
						'new_role': _transfer_to
					};
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post(ajaxurl, data, function(response) {
						$('#js-output').val(response);
						$('#js-output').show();
					});
					$('#confirm-transfer-role').hide();
					$('#btn-transfer-role').show();
					$('.alert').hide();
					$('#js-alert-success').html('<strong>Success!</strong> The users have been transfered to their new role.').show();
				} else {
					$('.alert').hide();
					$('#js-alert-error').html('<strong>Ooops!</strong> Please select a role from both drop downs.').show();
				}
			}
		});
	});