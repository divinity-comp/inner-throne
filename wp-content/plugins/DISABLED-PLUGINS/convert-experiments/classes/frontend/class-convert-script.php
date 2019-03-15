<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YCE_Convert_Script {

	/**
	 * Hook script method to wp_head
	 */
	public function __construct() {
		// Index 1 because script needs to load before _everything_.
		add_action( 'wp_head', array( $this, 'script' ), 1 );
	}

	/**
	 * Output the Convert script in the header
	 */
	public function script() {

		// Check if user entered a project number
		if ( '' != Yoast_Convert_Experiments::get_project_ID() ) {

			// Setup extra var string
			$extra_vars = '';

			// Add extra vars based on page type
			if ( is_category() || is_tag() || is_tax() ) {

				$term = get_queried_object();

				$extra_vars .= "var _conv_page_type='{$term->taxonomy}';";
				$extra_vars .= "var _conv_product_name='{$term->slug}';";
				$extra_vars .= "var _conv_category_id='{$term->term_id}';";
				$extra_vars .= "var _conv_category_name='{$term->slug}';";

			} elseif ( is_home() || is_front_page() ) {
				$extra_vars .= "var _conv_page_type='home';";
			} elseif ( is_single() || is_page() ) {
				if ( is_singular() ) {
					global $post;
					$extra_vars .= "var _conv_page_type='".$post->post_type."';";
				}
				$post_title = str_replace( "'", "\'", get_the_title() );
				$extra_vars .= "var _conv_product_name='{$post_title}';";
				if ( is_singular('post') ) {
					$the_cats = get_the_category();
					foreach ( $the_cats as $cat ) {
						$ids[]   = $cat->cat_ID;
						$names[] = str_replace( "'", "\'", $cat->cat_name );
					}
					if ( ! empty( $ids ) ) {
						$extra_vars .= "var _conv_category_id='" . implode( ';', $ids ) . "';";
					}
					if ( ! empty( $names ) ) {
						$extra_vars .= "var _conv_category_name='" . implode( ';', $names ) . "';";
					}
				}
			}

			// Close extra var string
			if ( ! empty( $extra_vars ) && substr( $extra_vars, - strlen( ';' ) ) !== ';' ) {
				$extra_vars .= ';';
			}

			// Get project number parts
			$parts = explode( "_", Yoast_Convert_Experiments::get_project_ID() );

			// Start script part
			echo "<!-- Begin Convert Experiments code-->\n";

			// Ouput extra vars if there are any
			if ( '' != trim( $extra_vars ) ) {
				echo '<script type="text/javascript">';
				echo $extra_vars;
				echo "</script>\n";
			}

			// Ouput default tags
			echo '<script src="//cdn-3.convertexperiments.com/js/' . $parts[0] . '-' . $parts[1] . '.js" type="text/javascript"></script>' . "\n";

			echo "<!-- End Convert	Experiments code -->\n";
		}

	}

}