<?php

if ( class_exists( 'SWP_Header_Output' ) ) :

/**
 * Register and output open graph tags, Twitter cards, and custom color CSS.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Unknown     | Created
 * @since     2.2.4 | 05 MAY 2017 | Added the global options for og:type values.
 * @since     3.0.0 | 21 FEB 2018 | Refactored into a class-based system.
 * @since     3.0.8 | 23 MAY 2018 | Added compatibility for custom color/outline combos.
 * @since     3.1.0 | 05 JUL 2018 | Added global $post variable.
 * @since     3.5.0 | 18 DEC 2018 | Refactored for code optimization.
 *
 * Hook into the core header filter
 *
 * Create and return the values to be used in the header meta tags
 *
 * To view which meta values are processed,
 * see setup_open_graph() and setup_twitter_card().
 *
 */
class SWP_Pro_Header_Output extends SWP_Header_Output {
	public function __construct() {
		global $post, $swp_user_options;

		$this->options = $swp_user_options;
		$this->establish_custom_colors();

		add_filter( 'swp_header_html', array( $this, 'render_meta_html' ) );
		add_filter( 'swp_header_html', array( $this, 'output_custom_color' ) );
	}

	/**
	 * Parses user options and prepares data for header output.
	 *
	 * Any <meta> tags which can be configured with options or post_meta will be
	 * touched by the callbacks in this method body.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param void
	 * @return void
	 */
	public function establish_header_values() {
		global $post;

		if( false === is_singular() || !is_object( $post ) ) {
			return;
		}

		$this->post = $post;
		$this->setup_open_graph();
		$this->setup_twitter_card();
	}

	/**
	 * Takes stored class data and returns meta tag HTML.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @hook   swp_header_html | filter | origin SWP_Header_Output
	 * @param  string $meta_html Ready to print HTML for the <head>.
	 * @return string $meta_html Ready to print HTML for the <head>.
	 *
	 */
	public function render_meta_html( $meta_html ) {
		$this->establish_header_values();

		if( !empty( $this->open_graph_data ) ) {
			$open_graph_html = $this->generate_meta_html( $this->open_graph_data );
			$meta_html .= $open_graph_html;
		}

		if ( !empty( $this->twitter_card_data) ) {
			$twitter_card_html = $this->generate_meta_html( $this->twitter_card_data );
			$meta_html .= $twitter_card_html;
		}

		return $meta_html;
	}

	/**
	 * Open Graph metadata can come from a variety of sources.
	 *
	 * This method prioritizes prioritizes Open Graph data as follows:
	 * 1. Values stored in Yoast fields
	 * 2. Values stored in Social Warfare fields
	 * 3. Values inferred from WordPress fields.
	 *
	 * However, we would rather use any valid value over an empty value, so
	 * if the field does not exist by step 3, we keep the values found in 1 or 2.
	 *
	 * The resulting array is stored locally for use in $this->render_meta_html().
	 *
	 * @see $this render_meta_html()
	 * @since 3.5.0 | 19 DEC 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	public function setup_open_graph() {
		if ( false === SWP_Utility::get_option( 'og_tags' ) && false === SWP_Utility::get_option( 'twitter_cards' ) ) {
			return;
		}

		add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );
		add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );

		$known_fields = array(
			'og:type' => 'article',
			'og:url' => get_permalink(),
			'og:site_name' => get_bloginfo( 'name' ),
			'article:published_time' => get_post_time( 'c' ),
			'article:modified_time' => get_post_modified_time( 'c' ),
			'og:updated_time' => get_post_modified_time( 'c' )
		);

		/**
		 * We prioritize the source of a value in this order:
		 * 1 Post meta
		 * 2 Yoast (OG)
		 * 3 Yoast (Social)
		 * 4 Post content.
		 */
		$fields = $this->fetch_social_warfare_open_graph_fields();
		$fields = $this->fetch_yoast_open_graph_fields( $fields );
		$fields = $this->apply_default_open_graph_fields( $fields );

		foreach( $fields as $key => $value ) {
			$og_key = str_replace('og_', 'og:', $key);
			unset($fields[$key]);
			$fields[$og_key] = $value;
		}

		$fields = array_map( 'htmlspecialchars', $fields );
		$this->open_graph_data = array_merge( $known_fields, $fields );
	}

	/**
	 * Grabs OG data based on Social Warfare settings.
	 *
	 * This is the most valuable source for og metadata. If we find a value
	 * for a key here, then the remaining checks skip that key.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param void
	 * @return array $fields Social Warfare field data.
	 *
	 */
	protected function fetch_social_warfare_open_graph_fields() {
		$fields = array(
			'og_title',         // These have a meta field.
			'og_description',
			'og_image_url',
			'og_image_width',
			'og_image_height',
			'og_url',           // These do not have a meta field.
			'og_site_name',
		);

		foreach ($fields as $index => $key) {
			$maybe_value = SWP_Utility::get_meta( $this->post->ID, "swp_$key" );
			// Go from indexed array to associative, with possibly missing values.
			unset($fields[$index]);
			$fields[$key] = $maybe_value;
		}

		return $fields;
	}

	/**
	 * Sets values for Twitter card from known SW values.
	 *
	 * This is the most valuable source for twitter metadata. If we find a value for
	 * a key here, then the remaining checks skip that key.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param  array $fields array('og_key' => $og_value)
	 * @return array $fields array('og_key2' => $default_og_value)
	 *
	 */
	protected function fetch_social_warfare_twitter_fields() {
		$twitter_fields = array(
			'twitter_title' => false,
			'twitter_description' => false,
			'twitter_image' => false
		);

		foreach ($twitter_fields as $key => $value) {
			$field = str_replace( 'twitter_', 'swp_twitter_card_', $key );
			$maybe_value = SWP_Utility::get_meta( $this->post->ID, $field );

			if ( !empty( $maybe_value ) ) {
				$twitter_fields[$key] = $maybe_value;
			}
		}

		$twitter_id = SWP_Utility::get_option( 'twitter_id' );
		if ( !empty( $twitter_id ) ) {
			$twitter_id = '@' . str_replace( '@' , '' , trim ( $twitter_id ) );
			$twitter_fields['twitter_site'] = $twitter_id;
		}

		$author_twitter_handle = get_the_author_meta( 'swp_twitter' );
		if ( !empty( $author_twitter_handle ) ) {
			$twitter_fields['twitter_creator'] = '@' . str_replace( '@' , '' , trim ( $author_twitter_handle ) );
		} else {
			$twitter_fields['twitter_creator'] = $twitter_id;
		}

		return $twitter_fields;
	}

	/**
	 * Grabs OG data based on Yoast boxes.
	 *
	 * If a value already exists for $key in $fields, we'll skip that one.
	 * Only sets values for a key that still needs one.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param array $fields Open graph field data
	 * @return array $fields Open graph field data
	 *
	 */
	protected function fetch_yoast_open_graph_fields( $fields ) {
		if ( !defined( 'WPSEO_VERSION' ) ) {
			return $fields;
		}

		global $wpseo_og;
		if ( has_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ) ) ) {
			remove_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ), 30 );
		}

		// Establish the relationship between swp_keys => _yoast_keys
		$yoast_og_map = array(
			'og_title' => '_yoast_wpseo_opengraph-title',
			'og_description' => '_yoast_wpseo_opengraph-description',
			'og_image'	=> '_yoast_wpseo_opengraph-image',
		);

		$yoast_social_map = array(
			'og_title'	=> '_yoast_wpseo_title',
			'og_description'	=> '_yoast_wpseo_metadesc'
		);

		// Fill in values based on priority.
		foreach ($fields as $swp_meta_key => $maybe_value) {
			if ( isset( $maybe_value ) ) {
				// post_meta value already exists from SWP.
				continue;
			}

			// OG Values
			if ( array_key_exists( $swp_meta_key, $yoast_og_map ) ) :
				foreach ($yoast_og_map as $swp_og_key => $yoast_og_key) {
					$yoast_og_value = SWP_Utility::get_meta( $this->post->ID, $yoast_og_key );

					if ( !empty( $yoast_og_value ) ) {
						if ( function_exists (' wpseo_replace_vars' ) ) {
							$yoast_og_value = wpseo_replace_vars( $yoast_og_value, $this->post );
						}
						$fields[$swp_meta_key] = $yoast_og_value;
						$maybe_value = $yoast_og_value;
					}
				}
			endif;

			// Social Values
			if ( empty( $maybe_value ) && array_key_exists( $swp_meta_key, $yoast_social_map) ) :
				foreach( $yoast_social_map as $swp_og_key => $yoast_social_key ) {
					$yoast_social_value = SWP_Utility::get_meta( $this->post->ID, $yoast_social_key );

					if ( !empty( $yoast_og_value ) ) {
						if ( function_exists (' wpseo_replace_vars' ) ) {
							$yoast_og_value = wpseo_replace_vars( $yoast_og_value, $this->post );
						}
						$fields[$swp_meta_key] = $yoast_social_value;
					}
				}
			endif;
		}

		return $fields;
	}

	/**
	 * Sets values for meta tags from default sources.
	 *
	 * This method will fill in the gaps missed by meta boxes and Yoast.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param  array $fields array('meta_key' => $meta_value)
	 * @return array $fields array('meta_key2' => $default_meta_value)
	 *
	 */
	protected function apply_default_open_graph_fields( $fields ) {
		$defaults = array(
			'og_description' => html_entity_decode( SWP_Utility::convert_smart_quotes( htmlspecialchars_decode( SWP_Utility::get_the_excerpt( $this->post->ID ) ) ) ),
			'og_title' => trim( SWP_Utility::convert_smart_quotes( htmlspecialchars_decode( get_the_title() ) ) )
		);

		// Author.
		$author = get_the_author_meta( 'swp_fb_author' );
		if ( empty( $author ) ) {
			$author = get_the_author_meta( 'facebook' );
			if ( empty( $author ) )  {
				$author = get_the_author();
			}
		}
		$defaults['article_author'] = $author;

		// Publisher.
		$publisher = SWP_Utility::get_option('facebook_publisher_url');
		if ( empty( $publisher ) ) {
			//@TODO Before this update, there was a call to $wpseo_social['facebook_site']. Where does $wpseo_social come from, is it a global?
			// $publisher = $wpseo_social['facebook_site'];
			$publisher = $author;
		}
		$defaults['article_publisher'] = $publisher;

		// Image.
		$thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id( $this->post->ID ) );
		if ( $thumbnail_url ) {
			$defaults['og_image'] = $thumbnail_url;
		}

		// Facebook App ID.
		$app_id = SWP_Utility::get_option( 'facebook_app_id' );
		if ( empty( $app_id ) ) {
			// $wpseo_social['fbadminapp'];
			$app_id = '529576650555031';
		}

		return array_merge( $defaults, $fields );
	}

	/**
	 * Sets values for Twitter card from known Yoast values.
	 *
	 * This method will fill in the gaps missed by meta boxes and Yoast.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param  array $fields array('og_key' => $og_value)
	 * @return array $fields array('og_key2' => $default_og_value)
	 *
	 */
	protected function fetch_yoast_twitter_fields( $fields ) {
		if ( !defined( 'WPSEO_VERSION' ) ) {
			return $fields;
		}

		$yoast_to_twitter = array(
			'_yoast_wpseo_twitter-title' => 'twitter_title',
			'_yoast_wpseo_twitter-title' => 'twitter_description',
			'_yoast_wpseo_twitter-image' => 'twitter_image'
		);

		foreach( $yoast_to_twitter as $yoast => $twitter ) {
			$maybe_value = SWP_Utility::get_meta( $this->post->ID, $yoast );
			if ( !empty( $maybe_value ) ) {
				if ( function_exists (' wpseo_replace_vars' ) ) {
					$maybe_value = wpseo_replace_vars( $maybe_value, $this->post );
				}
				$fields[$twitter] = $maybe_value;
			}
		}

		return $fields;
	}

	/**
	 * Sets values for Open Graph meta tags from known Twitter values.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Created.
	 * @param  [type] $fields [description]
	 * @return [type]         [description]
	 */
	protected function apply_open_graph_to_twitter( $twitter_fields ) {
		$shared_fields = array();
		$field_map = array(
			'og:title'	=> 'twitter_title',
			'og:description' => 'twitter_description',
			'og:author'	=> 'twitter_creator',
			'og:image'	=> 'twitter_image'
		);

		foreach ( $field_map as $og => $twitter ) {
			if ( !empty( $this->open_graph_data[$og] ) ) {
				$shared_fields[$twitter] = $this->open_graph_data[$og];
			}
		}

		if ( false !== SWP_Utility::get_meta( $this->post->ID, 'swp_twitter_use_open_graph' ) ) {
			return array_merge($twitter_fields, $shared_fields);
		}

		return array_merge( $shared_fields, $twitter_fields );
	}


	 /**
	  * Loops through open graph data to create <meta> tags for the <head>
	  *
	  * @since  3.5.0 | 19 DEC 2018 | Created.
	  * @param  array $fields array('og_key' => $og_value)
	  * @return string The HTML for meta tags.
	  *
	  */
	public function generate_meta_html( $fields ) {
		$meta = '';

		if ( !is_array($fields)) {
			error_log(__METHOD__.' (caught) Parameter \$fields should be an array. I got ' . gettype($fields) . ' :'.var_export($fields, 1));
			return '';
		}

		foreach ( $fields as $key => $content ) {
			if ( $key == 'og:image_url' ) {
				$meta .= '<meta name="image" property="og:image" content="' . $content . '">';
				continue;
			}
			$meta .= '<meta property="' . $key . '" content="' . $content . '">';
		}

		return $meta;
	}


	/**
	* Loops through open graph data to create <meta> tags for the <head>
	*
	* @since  3.5.0 | 19 DEC 2018 | Created.
	* @param  void
	* @return array $fields array('og_key' => $og_value)
	*
	*/
	public function setup_twitter_card() {
		if ( !SWP_Utility::get_option( 'twitter_cards' ) ) {
			return;
		}

		add_filter( 'jetpack_disable_twitter_cards', '__return_true', 99 );

		$fields = $this->fetch_social_warfare_twitter_fields();
		$fields = $this->apply_open_graph_to_twitter( $fields );
		$fields = $this->fetch_yoast_twitter_fields( $fields );

		$fields['twitter_card'] = !empty( $fields['twitter_image']) ? 'summary_large_image' : 'summary';

		$this->twitter_card_data = $fields;
	}


	/**
	 * Verifies that the color has been properly set.
	 *
	 * @since 3.0.8 | MAY 23 2018 | Created the method.
	 * @param string $hex The color to check.
	 * @return string $hex The sanitized color string.
	 *
	 */
	private function parse_hex_color( $hex ) {
		if ( !isset( $hex ) ) :
			//* Default to a dark grey.
			return  "#333333";
		endif;

		if ( strpos( $hex, "#" !== 0 ) ) :
			$hex = "#" . $hex;
		endif;

		return $hex;
	}

	/**
	 * Localizes the custom color settings from admin.
	 *
	 * @since  3.0.8 | MAY 23 2018 | Created the method.
	 * @param  none
	 * @return void
	 *
	 */
	private function establish_custom_colors() {

		//* Static custom color.
		if ( SWP_Utility::get_option('default_colors') == 'custom_color' || SWP_Utility::get_option('single_colors') == 'custom_color' || SWP_Utility::get_option('hover_colors') == 'custom_color' ) :

			$custom_color = $this->parse_hex_color( $this->options['custom_color'] );
			$this->custom_color = $custom_color;

		else :
			$this->custom_color = '';
		endif;

		//* Float custom color.
		if ( SWP_Utility::get_option('float_default_colors') == 'float_custom_color' ||  SWP_Utility::get_option('float_single_colors') == 'float_custom_color' ||  SWP_Utility::get_option('float_hover_colors') == 'float_custom_color' ) :

			if ( true === $this->options['float_style_source'] ) :
				//* Inherit the static button style.
				$this->float_custom_color = $this->custom_color;
			else :
				$this->float_custom_color = $this->parse_hex_color( $this->options['float_custom_color'] );
			endif;

		else :
			$this->float_custom_color = '';
		endif;

		//* Static custom outlines.
		if ( SWP_Utility::get_option('default_colors') == 'custom_color_outlines' ||  SWP_Utility::get_option('single_colors') == 'custom_color_outlines' ||  SWP_Utility::get_option('hover_colors') == 'custom_color_outlines' ) :

			$custom_color_outlines = $this->parse_hex_color( $this->options['custom_color_outlines'] );
			$this->custom_color_outlines = $custom_color_outlines;

		else:
			$this->custom_color_outlines = '';
		endif;

		if (  SWP_Utility::get_option('float_default_colors') == 'float_custom_color_outlines' ||  SWP_Utility::get_option('float_single_colors') == 'float_custom_color_outlines' ||  SWP_Utility::get_option('float_hover_colors') == 'float_custom_color_outlines' ) :
			if ( true === $this->options['float_style_source'] ) :

				//* Inherit the static button style.
				$this->float_custom_color_outlines = $this->custom_color_outlines;
			else:
				$this->float_custom_color_outlines = $this->parse_hex_color( $this->options['float_custom_color_outlines'] );
			endif;

		else:
			$this->float_custom_color_outlines = '';
		endif;

	}


	/**
	 * A function to render custom color CSS
	 *
	 * @since  3.0.8 | 25 MAY 2018 | Moved the .swp_social_panel to a variable to avoid making
	 *                               the default override the hover states on the floaters.
	 * @param  boolean $floating True = floating CSS, False = non-floating CSS.
	 * @return string            The CSS to be output.
	 *
	 */
	private function get_css( $floating = false ) {
		$float = '';
		$class = '';
		$panel = '';
		$custom_color = $this->custom_color;
		$custom_outlines = $this->custom_color_outlines;

		if ( $floating ) {
			$float = 'float_';
			$class = '.swp_social_panelSide';
			$custom_color = $this->float_custom_color;
			$custom_outlines = $this->float_custom_color_outlines;
		} else {
			$panel = '.swp_social_panel';
		}

		$css = '';


		/**
		 * DEFAULT
		 *
		 *
		 */
		// Default: Custom Color
		if ( SWP_Utility::get_option($float . "default_colors") === $float . "custom_color" ) :
			$css .= "

			$class.swp_default_custom_color a
				{color:white}
			$class$panel.swp_default_custom_color .nc_tweetContainer
				{
					background-color:" . $custom_color . ";
					border:1px solid " . $custom_color . ";
				}
			";
		endif;

		// Default: Custom Outlines
		if ( SWP_Utility::get_option($float . "default_colors") === $float . "custom_color_outlines" ) :
				$css .= "

			$class.swp_default_custom_color_outlines a
				{color: " . $custom_outlines . "}
			$class.swp_default_custom_color_outlines .nc_tweetContainer
				{
					background-color: transparent ;
					border:1px solid " . $custom_outlines . " ;
				}
			";
		endif;


		/**
		 * INDIVIDUAL
		 *
		 *
		 */
		// Individual: Custom Color
		if ( SWP_Utility::get_option($float . "single_colors") === $float . "custom_color" ) :
			$css .= "

			html body $class$panel.swp_individual_custom_color .nc_tweetContainer:not(.total_shares):hover a
				{color:white !important}
			html body $class$panel.swp_individual_custom_color .nc_tweetContainer:not(.total_shares):hover
				{
					background-color:" . $custom_color . "!important;
					border:1px solid " . $custom_color . "!important;
				}
			";
		endif;

		// Individual: Custom Outlines
		if ( SWP_Utility::get_option($float . "single_colors") === $float . "custom_color_outlines" ) :
			$css .= "

			html body $class.swp_individual_custom_color_outlines .nc_tweetContainer:not(.total_shares):hover a
				{color:" . $custom_outlines . " !important}
			html body $class.swp_individual_custom_color_outlines .nc_tweetContainer:not(.total_shares):hover
				{
					background-color: transparent !important;
					border:1px solid " . $custom_outlines . "!important ;
				}
			";
		endif;


		/**
		 * OTHER
		 *
		 *
		 */
		// Other: Custom Color
		if ( SWP_Utility::get_option($float . "hover_colors") === $float . "custom_color" ) :
			$css .= "

			body $class$panel.swp_other_custom_color:hover a
				{color:white}
			body $class$panel.swp_other_custom_color:hover .nc_tweetContainer
				{
					background-color:" . $custom_color . ";
					border:1px solid " . $custom_color . ";
				}
			";
		endif;

		// Other: Custom Outlines
		if (SWP_Utility::get_option($float . "hover_colors") === $float . "custom_color_outlines" ) :
			$css .= "

			html body $class.swp_other_" . $float . "custom_color_outlines:hover a
				{color:" . $custom_outlines . " }
			html body $class.swp_other_" . $float . "custom_color_outlines:hover .nc_tweetContainer
				{
					background-color: transparent ;
					border:1px solid " . $custom_outlines . " ;
				}
			";
		endif;

		return $css;

	}


	/**
	 * Output the CSS for custom selected colors
	 *
	 * Don't nest the CSS. This way it will be fully "minified" on output.
	 *
	 * @since  1.4.0
	 * @access public
	 * @param  array $info The array of information about the post
	 * @return array $info The modified array
	 *
	 */
	public function output_custom_color( $meta_html ) {
		$static = $this->get_css();
		$floaters_on = SWP_Utility::get_option( 'floating_panel' );
		$floating = $this->get_css( $floaters_on );

		$css = $static . $floating;

		if ( !empty( $css) ) :
			$css = '<style type="text/css">' . $css . '</style>';
		endif;

		//* Replaces newlines and excessive whitespace with a single space.
		$meta_html .= trim( preg_replace( '/\s+/', ' ', $css ) );
		// $meta_html .= $css;
		return $meta_html;
	}

	/**
	 * Output custom CSS for Click To Tweet
	 *
	 * Note: This is done in the header rather than in a CSS file to
	 * avoid having the styles called from a CDN
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  array  $info An array of information about the post
	 * @return array  $info The modified array
	 */
	public function output_ctt_css( $meta_html ) {
		if (!empty($this->options['ctt_css']) && count($this->options)['ctt_css'] > 0) {
			// Add it to our array if we're using the frontend Head Hook
			$meta_html .= PHP_EOL . '<style id=ctt-css>' . $this->options['ctt_css'] . '</style>';

		}

		return $meta_html;
	}
}

endif;
