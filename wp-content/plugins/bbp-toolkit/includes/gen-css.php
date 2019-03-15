<?php

function bbptoolkit_generate_css() {
	$fullcss = ' ';
	$bbptoolkit_rem_ohbother = get_option('bbptoolkit-rem-ohbother', false);
	if ($bbptoolkit_rem_ohbother) {
		$fullcss = $fullcss . '
		.bbp-template-notice {
			display: none;
		}
		.bbp-template-notice.info,
		.bbp-template-notice.error,
		.bbp-template-notice.important,
		.bbp-template-notice.warning {
			display: block;
		}
		';
	}
	$bbptoolkit_subscr_right = get_option('bbptoolkit-subscr-right', false);
	if ($bbptoolkit_subscr_right) {
		$fullcss = $fullcss . '
		.single-forum .subscription-toggle  {
			float: right !important;
		}
		';
	}
	$bbptoolkit_closed_nogrey = get_option('bbptoolkit-closed-nogrey', false);
	if ($bbptoolkit_closed_nogrey) {
		$fullcss = $fullcss . '
		.status-closed,
		.status-closed a {
			color: #333 !important;
		}
		';
	}
	$bbptoolkit_rem_subf = get_option('bbptoolkit-rem-subf', false);
	if ($bbptoolkit_rem_subf) {
		$fullcss = $fullcss . '
		.bbp-forums {
			display: none;
		}
		';
	}
	$bbptoolkit_rem_pag_info = get_option('bbptoolkit-rem-pag-info', false);
	if ($bbptoolkit_rem_pag_info) {
		$fullcss = $fullcss . '
		.bbp-pagination-count {
			display: none;
		}
		';
	}
	
	$bbptoolkit_css_version = get_option('bbptoolkit-css-version', false);
	if (!$bbptoolkit_css_version) {
		$bbptoolkit_css_version = 0;
	}
	if ($bbptoolkit_css_version > 999) {
		$bbptoolkit_css_version = 0;
	}
	$bbptoolkit_css_version = $bbptoolkit_css_version + 1;
	update_option('bbptoolkit-css-version', $bbptoolkit_css_version);
	file_put_contents(BBPT_PLUGIN_DIR.'/css/bbptoolkit.css', $fullcss, LOCK_EX);
}


?>