<?php

//display bbPress search form above every single topic and forum
function bbptoolkit_show_search_form(){
	$bbptoolkit_show_search_everywhere = get_option('bbptoolkit-show-search-everywhere', false);
	if ($bbptoolkit_show_search_everywhere) {
		if ( bbp_allow_search()) {
			?>
			<div class="bbp-search-form">
				<?php bbp_get_template_part( 'form', 'search' ); ?>
			</div>
			<?php
		}
	}
}
add_action( 'bbp_template_before_single_forum', 'bbptoolkit_show_search_form' );
add_action( 'bbp_template_before_single_topic', 'bbptoolkit_show_search_form' );