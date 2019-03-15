"use strict";

let $ = jQuery;
let table,
	form,
	submit_button,
	progress_bar,
	progress_percent,
	message;

let RCP_Batch = {

	/**
	 * Listens for job submissions and initiates job processing.
	 */
	listen: function () {

		let batch = this;

		form = $('.rcp-batch-form');

		form.on('submit', function (event) {

			event.preventDefault();

			table = $(this).parents('.rcp-batch-processing-job-table');

			batch.set_vars( table );

			submit_button.prop('disabled', true).hide();
			table.find('.spinner').addClass('is-active').show();

			message.text('');

			let data = {
				action: 'rcp_process_batch',
				job_id: table.find('.rcp-batch-processing-job-id').val(),
				rcp_batch_nonce: rcp_batch_vars.batch_nonce
			};

			batch.process(data, table.find('.rcp-batch-processing-job-step').val(), table);

		});
	},

	/**
	 * Set variables.
	 *
	 * @param object table
	 */
	set_vars: function(table) {

		submit_button = table.find('.button-primary');

		progress_bar = table.find('.rcp-batch-processing-job-progress-bar span');

		progress_percent = table.find('.rcp-batch-processing-job-percent-complete');

		message = table.find('.rcp-batch-processing-message');

	},

	/**
	 * Process the specified job.
	 *
	 * @param object data  Job data
	 * @param int    step  Step to process
	 * @param object table Table object for modifying the DOM
	 */
	process: function(data, step, table) {
		let batch = this;

		data.step = step;

		batch.set_vars( table );

		$.ajax({
			dataType: 'json',
			data: data,
			type: 'POST',
			url: ajaxurl,
			success: function(response) {
				// TODO: clean up this whole function
				if(false === response.success) {

					console.log('batch not successful');

					console.log(response.data.message);

					submit_button.prop('disabled', false).show();
					table.find('.spinner').removeClass('is-active').hide();

					message.text(response.data.message + ' ' + rcp_batch_vars.i18n.job_retry);

					return;
				}

				if(response.data.complete) {

					progress_bar.css('width', response.data.percent_complete + '%');

					progress_percent.text(response.data.percent_complete);

					table.find('.spinner').removeClass('is-active').hide();

					message.text(response.data.complete_message).show();

				} else if(response.data.error) {
					// TODO: show errors
					console.log('error processing batch');

					console.log(response.data.error);

				} else if(response.data.next_step) {

					progress_bar.css('width', response.data.percent_complete + '%');

					progress_percent.text(response.data.percent_complete);

					batch.process(data, response.data.next_step, table);

				} else {

					// wtf happened
					console.log(response);

					message.text(response.data.message + ' ' + rcp_batch_vars.i18n.job_retry);

				}
			},
			error: function(response) {
				console.log(new Date() + ' error');
				console.log(response);
			}
		});
	}
};

/**
 * Loads the job listener.
 */
$(document).ready(function() {
	RCP_Batch.listen();
} );