<?php
function cptt_avada_render_post_metadata( $layout, $settings = array() ) {

    global $fusion_settings;
    if ( ! $fusion_settings ) {
        $fusion_settings = Fusion_Settings::get_instance();
    }

    $html = $author = $date = $metadata = '';

    $settings = ( is_array( $settings ) ) ? $settings : array();

    $default_settings = array(
        'post_meta'          => fusion_library()->get_option( 'post_meta' ),
        'post_meta_author'   => fusion_library()->get_option( 'post_meta_author' ),
        'post_meta_date'     => fusion_library()->get_option( 'post_meta_date' ),
        'post_meta_cats'     => fusion_library()->get_option( 'post_meta_cats' ),
        'post_meta_tags'     => fusion_library()->get_option( 'post_meta_tags' ),
        'post_meta_comments' => fusion_library()->get_option( 'post_meta_comments' ),
        'meta_terms1'        => fusion_library()->get_option( 'meta_terms1'),
        'meta_terms2'        => fusion_library()->get_option( 'meta_terms2' ),

    );

    $settings = wp_parse_args( $settings, $default_settings );
   $post_meta = get_post_meta( get_queried_object_id(), 'pyre_post_meta', true );

    // Check if meta data is enabled.
		if ( ( $settings['post_meta'] && 'no' !== $post_meta ) || ( ! $settings['post_meta'] && 'yes' === $post_meta ) ) {

		// For alternate, grid and timeline layouts return empty single-line-meta if all meta data for that position is disabled.
        if ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) && ! $settings['post_meta_author'] && ! $settings['post_meta_date'] && ! $settings['meta_terms1'] && ! $settings['meta_terms2'] && ! $settings['post_meta_cats'] && ! $settings['post_meta_tags'] && ! $settings['post_meta_comments'] ) {
            return '';
        }

        // Render author meta data.
        if ( $settings['post_meta_author'] ) {
            ob_start();
            the_author_posts_link();
            $author_post_link = ob_get_clean();

           // Check if rich snippets are enabled.
				if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) && $fusion_settings->get( 'disable_rich_snippet_author' ) ) {
					$metadata .= sprintf( esc_html__( 'By %s', 'fusion-builder' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
				} else {
					$metadata .= sprintf( esc_html__( 'By %s', 'fusion-builder' ), '<span>' . $author_post_link . '</span>' );
				}
				$metadata .= '<span class="fusion-inline-sep">|</span>';
			} else { // If author meta data won't be visible, render just the invisible author rich snippet.
				$author .= fusion_builder_render_rich_snippets_for_pages( false, true, false );
			}

        // Render the updated meta data or at least the rich snippet if enabled.
        if ( $settings['post_meta_date'] ) {
            $metadata .= fusion_builder_render_rich_snippets_for_pages( false, false, true );

            $formatted_date = get_the_time( $fusion_settings->get( 'date_format' ) );
            $date_markup = '<span>' . $formatted_date . '</span><span class="fusion-inline-sep">|</span>';
            $metadata .= apply_filters( 'fusion_post_metadata_date', $date_markup, $formatted_date );
        } else {
            $date .= fusion_builder_render_rich_snippets_for_pages( false, false, true );
        }

        // Render rest of meta data.
			// Render categories.
			if ( $settings['post_meta_cats'] ) {
				ob_start();
				the_category( ', ' );
				$categories = ob_get_clean();

				if ( $categories ) {
					$metadata .= ( $settings['post_meta_tags'] ) ? sprintf( esc_html__( 'Categories: %s', 'fusion-builder' ), $categories ) : $categories;
					$metadata .= '<span class="fusion-inline-sep">|</span>';
				}
			}

			// Render tags.
			if ( $settings['post_meta_tags'] ) {
				ob_start();
				the_tags( '' );
				$tags = ob_get_clean();

				if ( $tags ) {
					$metadata .= '<span class="meta-tags">' . sprintf( esc_html__( 'Tags: %s', 'fusion-builder' ), $tags ) . '</span><span class="fusion-inline-sep">|</span>';
				}
			}
        
	//render terms for custom taxonomies
			
			global $post;
			$taxonomy1_name = get_taxonomy( $settings['meta_terms1'] );
			$taxonomy2_name = get_taxonomy( $settings['meta_terms2'] );
			
			if ( $settings['meta_terms1'] ) {
				ob_start();				
				the_terms( $post->ID, $settings['meta_terms1'], $taxonomy1_name->labels->name.': ', ' , ' );				
				$cus_term1 = ob_get_clean();
				
				if ( $cus_term1 ) {
					
					$metadata .= sprintf( '%s<span class="fusion-inline-sep">|</span>', $cus_term1 );
				}
								
			}	
			
			if ( $settings['meta_terms2'] ) {
				ob_start();
				the_terms( $post->ID, $settings['meta_terms2'], $taxonomy2_name->labels->name.': ', ' , ' );
				$cus_term2 = ob_get_clean();

				if( $cus_term2 ) {
					$metadata .= sprintf( '<span class="meta-tags">%s %s</span><span class="fusion-inline-sep">|</span>','', $cus_term2 );
				}
			}
			
// Render comments.
			if ( $settings['post_meta_comments'] && 'grid_timeline' !== $layout ) {
				ob_start();
				comments_popup_link( esc_html__( '0 Comments', 'fusion-builder' ), esc_html__( '1 Comment', 'fusion-builder' ), esc_html__( '% Comments', 'fusion-builder' ) );
				$comments = ob_get_clean();
				$metadata .= '<span class="fusion-comments">' . $comments . '</span>';
			}

			// Render the HTML wrappers for the different layouts.
			if ( $metadata ) {
				$metadata = $author . $date . $metadata;

				if ( 'single' === $layout ) {
					$html .= '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">' . $metadata . '</div></div>';
				} elseif ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) ) {
					$html .= '<p class="fusion-single-line-meta">' . $metadata . '</p>';
				} else {
					$html .= '<div class="fusion-alignleft">' . $metadata . '</div>';
				}
			} else {
				$html .= $author . $date;
			}
		} else {
			// Render author and updated rich snippets for grid and timeline layouts.
			if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) ) {
				$html .= fusion_builder_render_rich_snippets_for_pages( false );
			}
		}

    return apply_filters( 'fusion_post_metadata_markup', $html );
}
