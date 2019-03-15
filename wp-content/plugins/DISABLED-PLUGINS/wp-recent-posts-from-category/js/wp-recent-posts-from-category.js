/***
WP Recent Posts From Category plugin for Wordpress.
Copyright 2014  Daniele De Santis  (email : info@danieledesantis.net)
***/

jQuery(window).load(function() {
	
	var	inputCategory    =   jQuery('#shortcode_category'),
		inputChildren    =   jQuery('#display_children_categories'),
		inputPosts       =   jQuery('#posts'),
		inputExcerpt     =   jQuery('#display_excerpt'),
		inputMeta        =   jQuery('#display_author_date'),
		inputContainer   =   jQuery('#container_class'),
		resultTitle      =   jQuery('#shortcode_title'),
		result           =   jQuery('#shortcode')
	;
	
	jQuery('#generate_shortcode_form').submit(function(e) {
		
		e.preventDefault();
		
		var	category    =
			children    =
			posts	    =
			excerpt     =
			meta	    =
			container   =   ''
		;
		
		if(inputCategory.val() != 0) {
			var category = ' category="' + inputCategory.val() + '"';
		}
		
		if(!inputChildren.attr('checked')) {
			var children = ' children="false"';
		}
		
		if(inputPosts.val() != 5) {
			var posts = ' posts="' + inputPosts.val() + '"';
		}
		
		if(inputExcerpt.attr('checked')) {
			var excerpt = ' excerpt="true"';
		}
		
		if(inputMeta.attr('checked')) {
			var meta = ' meta="true"';
		}
		
		if(inputContainer.val() != '') {
			var container = ' container="' + inputContainer.val() + '"';
		}
		
		resultTitle.html('<strong>Copy and paste the shortcode below in your page, post or widget:</strong>')
		result.html('[rpfc_recent_posts_from_category' + category + children + posts + excerpt + meta + container + ']');		
	})
	
});