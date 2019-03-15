<?php

// Fix issue in 2.5 when topic has are over 1000 replies
// https://bbpress.org/forums/topic/topics-with-1000-replies-do-not-add-page-numbers-to-reply-permalinks/
add_filter( 'bbp_number_format', 'bbptoolkit_number_format', 10 , 5) ;
function bbptoolkit_number_format ($number_format, $number, $decimals, $dec_point, $thousands_sep) {
	$bbptoolkit_fix_topic_over_thousand = get_option('bbptoolkit-fix-topic-over-thousand', false);
	if ($bbptoolkit_fix_topic_over_thousand) {
		$thousands_sep = '' ;
		return apply_filters( 'bbptoolkit_number_format', number_format( $number, $decimals, $dec_point, $thousands_sep ), $number, $decimals, $dec_point, $thousands_sep );
	} else {
		return $number_format;
	}
}