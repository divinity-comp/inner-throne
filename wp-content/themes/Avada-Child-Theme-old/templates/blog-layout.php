<?php
/**
 * Render the blog layouts.
 *
 * @author 		ThemeFusion
 * @package 	Avada/Templates
 * @version     1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) { exit( 'Direct script access denied.' ); }

global $wp_query;

// Set the correct post container layout classes.
$blog_layout = avada_get_blog_layout();
$post_class  = 'fusion-post-' . $blog_layout;

$container_class = 'fusion-blog-layout-' . $blog_layout . ' ';
if ( 'grid' == $blog_layout ) {
	$container_class = 'fusion-blog-layout-' . $blog_layout . ' fusion-blog-layout-' . $blog_layout . '-' . Avada()->settings->get( 'blog_grid_columns' ) . ' isotope ';
}

// Set class for scrolling type.
if ( Avada()->settings->get( 'blog_pagination_type' ) == 'Infinite Scroll' ||
	 Avada()->settings->get( 'blog_pagination_type' ) == 'load_more_button'
) {
	$container_class .= 'fusion-blog-infinite fusion-posts-container-infinite ';
} else {
	$container_class .= 'fusion-blog-pagination ';
}

if ( ! Avada()->settings->get( 'featured_images' ) ) {
	$container_class .= 'fusion-blog-no-images ';
}

// Add the timeline icon.
if ( 'timeline' == $blog_layout ) {
	echo '<div class="fusion-timeline-icon"><i class="fusion-icon-bubbles"></i></div>';
}

if ( is_search() &&
	 Avada()->settings->get( 'search_results_per_page' )
) {
	$number_of_pages = ceil( $wp_query->found_posts / Avada()->settings->get( 'search_results_per_page' ) );
} else {
	$number_of_pages = $wp_query->max_num_pages;
}

echo '<div id="posts-container" class="' . $container_class . 'fusion-blog-archive fusion-clearfix" data-pages="' . $number_of_pages . '">';

if ( 'timeline' == $blog_layout ) {
	// Initialize the time stamps for timeline month/year check.
	$post_count = 1;
	$prev_post_timestamp = null;
	$prev_post_month = null;
	$prev_post_year = null;
	$first_timeline_loop = false;

	// Add the container that holds the actual timeline line.
	echo '<div class="fusion-timeline-line"></div>';
}

	// Start the main loop.
while ( have_posts() ) :  the_post();
	// Set the time stamps for timeline month/year check.
	$alignment_class = '';
	if ( 'timeline' == $blog_layout ) {
		$post_timestamp = get_the_time( 'U' );
		$post_month     = date( 'n', $post_timestamp );
		$post_year      = get_the_date( 'Y' );
		$current_date   = get_the_date( 'Y-n' );

		// Set the correct column class for every post.
		if ( $post_count % 2 ) {
			$alignment_class = 'fusion-left-column';
		} else {
			$alignment_class = 'fusion-right-column';
		}

		// Set the timeline month label.
		if ( $prev_post_month != $post_month || $prev_post_year != $post_year ) {

			if ( $post_count > 1 ) {
				echo '</div>';
			}
			echo '<h3 class="fusion-timeline-date">' . get_the_date( Avada()->settings->get( 'timeline_date_format' ) ) . '</h3>';
			echo '<div class="fusion-collapse-month">';
		}
	}

	// Set the has-post-thumbnail if a video is used. This is needed if no featured image is present.
	$thumb_class = '';
	if ( get_post_meta( get_the_ID(), 'pyre_video', true ) ) {
		$thumb_class = ' has-post-thumbnail';
	}

	$post_classes = $post_class . ' ' . $alignment_class . ' ' . $thumb_class . ' post fusion-clearfix';
	ob_start();
	post_class( $post_classes );
	$post_classes = ob_get_clean();

	echo '<article id="post-' . get_the_ID() . '" ' . $post_classes . '>';
	// Add an additional wrapper for grid layout border.
	if ( 'grid' == $blog_layout ) {
		echo '<div class="fusion-post-wrapper">';
	}

		// Get featured images for all but large-alternate layout.
	if ( ( ( is_search() && Avada()->settings->get( 'search_featured_images' ) ) || ( ! is_search() && Avada()->settings->get( 'featured_images' ) ) ) && 'large-alternate' == $blog_layout ) {
		get_template_part( 'new-slideshow' );
	}

		// Get the post date and format box for alternate layouts.
	if ( 'large-alternate' == $blog_layout || 'medium-alternate' == $blog_layout ) {
		echo '<div class="fusion-date-and-formats">';

		/**
		 * The avada_blog_post_date_adn_format hook.
		 *
		 * @hooked avada_render_blog_post_date - 10 (outputs the HTML for the date box).
		 * @hooked avada_render_blog_post_format - 15 (outputs the HTML for the post format box).
		 */
		do_action( 'avada_blog_post_date_and_format' );

		echo '</div>';
	}

	// Get featured images for all but large-alternate layout.
	if ( ( ( is_search() && Avada()->settings->get( 'search_featured_images' ) ) || ( ! is_search() && Avada()->settings->get( 'featured_images' ) ) ) && 'large-alternate' != $blog_layout ) {
		get_template_part( 'new-slideshow' );
	}

		// The post-content-wrapper is only needed for grid and timeline.
	if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) {
		echo '<div class="fusion-post-content-wrapper">';
	}

	// Add the circles for timeline layout.
	if ( 'timeline' == $blog_layout ) {
		echo '<div class="fusion-timeline-circle"></div>';
		echo '<div class="fusion-timeline-arrow"></div>';
	}

	echo '<div class="fusion-post-content post-content">';

	// Render the post title.
	echo avada_render_post_title( get_the_ID() );

	// Render post meta for grid and timeline layouts.
	if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) {
		echo avada_render_post_metadata( 'grid_timeline' );

		if ( ( Avada()->settings->get( 'post_meta' ) && ( Avada()->settings->get( 'post_meta_author' ) || Avada()->settings->get( 'post_meta_date' ) || Avada()->settings->get( 'post_meta_cats' ) || Avada()->settings->get( 'post_meta_tags' ) || Avada()->settings->get( 'post_meta_comments' ) || Avada()->settings->get( 'post_meta_read' ) ) ) && 0 < Avada()->settings->get( 'excerpt_length_blog' ) ) {
			echo '<div class="fusion-content-sep"></div>';
		}
		// Render post meta for alternate layouts.
	} elseif ( 'large-alternate' == $blog_layout || 'medium-alternate' == $blog_layout ) {
		echo avada_render_post_metadata( 'alternate' );
	}

	echo '<div class="fusion-post-content-container">';

	/**
	 * The avada_blog_post_content hook.
	 *
	 * @hooked avada_render_blog_post_content - 10 (outputs the post content wrapped with a container).
	 */
	do_action( 'avada_blog_post_content' );

	echo '</div>';
	echo '</div>'; // End post-content.

	if ( 'medium' == $blog_layout || 'medium-alternate' == $blog_layout ) {
		echo '<div class="fusion-clearfix"></div>';
	}

	// Render post meta data according to layout.
	if ( ( Avada()->settings->get( 'post_meta' ) && ( Avada()->settings->get( 'post_meta_author' ) || Avada()->settings->get( 'post_meta_date' ) || Avada()->settings->get( 'post_meta_cats' ) || Avada()->settings->get( 'post_meta_tags' ) || Avada()->settings->get( 'post_meta_comments' ) || Avada()->settings->get( 'post_meta_read' ) ) ) ) {
		echo '<div class="fusion-meta-info">';
		if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) {
			// Render read more for grid/timeline layouts.
			echo '<div class="fusion-alignleft">';
			if ( Avada()->settings->get( 'post_meta_read' ) ) {
				$link_target = '';
				if ( fusion_get_page_option( 'link_icon_target', get_the_ID() ) == 'yes' ||
				fusion_get_page_option( 'post_links_target', get_the_ID() ) == 'yes' ) {
					$link_target = ' target="_blank" rel="noopener noreferrer"';
				}
				echo '<a href="' . get_permalink() . '" class="fusion-read-more"' . $link_target . '>' . apply_filters( 'avada_blog_read_more_link', esc_attr__( 'Read More', 'Avada' ) ) . '</a>';
			}
			echo '</div>';

			// Render comments for grid/timeline layouts.
			echo '<div class="fusion-alignright">';
			if ( Avada()->settings->get( 'post_meta_comments' ) ) {
				if ( ! post_password_required( get_the_ID() ) ) {
					comments_popup_link( '<i class="fusion-icon-bubbles"></i>&nbsp;0', '<i class="fusion-icon-bubbles"></i>&nbsp;' . __( '1', 'Avada' ), '<i class="fusion-icon-bubbles"></i>&nbsp;%' );
				} else {
					echo '<i class="fusion-icon-bubbles"></i>&nbsp;' . esc_attr__( 'Protected', 'Avada' );
				}
			}

			echo '</div>';
		} else {
			// Render all meta data for medium and large layouts.
			if ( 'large' == $blog_layout || 'medium' == $blog_layout ) {
				echo avada_render_post_metadata( 'standard' );
			}

			// Render read more for medium/large and medium/large alternate layouts.
			echo '<div class="fusion-alignright">';
			if ( Avada()->settings->get( 'post_meta_read' ) ) {
				$link_target = '';
				if ( fusion_get_page_option( 'link_icon_target', get_the_ID() ) == 'yes' ||
				fusion_get_page_option( 'post_links_target', get_the_ID() ) == 'yes' ) {
					$link_target = ' target="_blank" rel="noopener noreferrer"';
				}
				echo '<a href="' . get_permalink() . '" class="fusion-read-more"' . $link_target . '>' . apply_filters( 'avada_read_more_name', esc_attr__( 'Read More', 'Avada' ) ) . '</a>';
			}
			echo '</div>';
		}
		echo '</div>'; // End meta-info.
	}
	if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) {
		echo '</div>'; // End post-content-wrapper.
	}
	if ( 'grid' == $blog_layout ) {
		echo '</div>'; // End post-wrapper.
	}
	echo '</article>'; // End post.

	// Adjust the timestamp settings for next loop.
	if ( 'timeline' == $blog_layout ) {
		$prev_post_timestamp = $post_timestamp;
		$prev_post_month     = $post_month;
		$prev_post_year      = $post_year;
		$post_count++;
	}
endwhile; // End have_posts().

if ( 'timeline' == $blog_layout && 1 < $post_count ) {
	echo '</div>';
}
echo '</div>'; // End posts-container.

// If infinite scroll with "load more" button is used.
if ( Avada()->settings->get( 'blog_pagination_type' ) == 'load_more_button' ) {
	echo '<div class="fusion-load-more-button fusion-blog-button fusion-clearfix">' . apply_filters( 'avada_load_more_posts_name', esc_attr__( 'Load More Posts', 'Avada' ) ) . '</div>';
}

// Get the pagination.
fusion_pagination( $pages = '', $range = 2 );

wp_reset_query();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
