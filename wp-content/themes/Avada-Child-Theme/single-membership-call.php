<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>

<div id="content" <?php Avada()->layout->add_style( 'content_style' ); ?>>

	<?php if ( ( Avada()->settings->get( 'blog_pn_nav' ) && 'no' != get_post_meta( $post->ID, 'pyre_post_pagination', true ) ) || ( ! Avada()->settings->get( 'blog_pn_nav' ) && 'yes' == get_post_meta( $post->ID, 'pyre_post_pagination', true ) ) ) : ?>
		<div class="single-navigation clearfix">
			<?php previous_post_link( '%link', esc_attr__( 'Previous', 'Avada' ) ); ?>
			<?php next_post_link( '%link', esc_attr__( 'Next', 'Avada' ) ); ?>
		</div>
	<?php endif; ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
			<?php $full_image = ''; ?>
			<?php if ( 'above' == Avada()->settings->get( 'blog_post_title' ) ) : ?>
				<?php echo avada_render_post_title( $post->ID, false, '', '1' ); ?>
			<?php elseif ( 'disabled' == Avada()->settings->get( 'blog_post_title' ) && Avada()->settings->get( 'disable_date_rich_snippet_pages' ) ) : ?>
				<span class="entry-title" style="display: none;"><?php the_title(); ?></span>
			<?php endif; ?>

			<?php if ( ! post_password_required( $post->ID ) ) : ?>
				<?php if ( Avada()->settings->get( 'featured_images_single' ) ) : ?>
					<?php if ( 0 < avada_number_of_featured_images() || get_post_meta( $post->ID, 'pyre_video', true ) ) : ?>
						<?php Avada()->images->set_grid_image_meta( array( 'layout' => strtolower( 'large' ), 'columns' => '1' ) ); ?>
						<div class="fusion-flexslider flexslider fusion-flexslider-loading post-slideshow fusion-post-slideshow">
							<ul class="slides">
								<?php if ( get_post_meta( $post->ID, 'pyre_video', true ) ) : ?>
									<li>
										<div class="full-video">
											<?php echo get_post_meta( $post->ID, 'pyre_video', true ); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if ( has_post_thumbnail() && 'yes' != get_post_meta( $post->ID, 'pyre_show_first_featured_image', true ) ) : ?>
									<?php $attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?>
									<?php $full_image       = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?>
									<?php $attachment_data  = wp_get_attachment_metadata( get_post_thumbnail_id() ); ?>
									<li>
										<?php if ( Avada()->settings->get( 'status_lightbox' ) && Avada()->settings->get( 'status_lightbox_single' ) ) : ?>
											<a href="<?php echo $full_image[0]; ?>" data-rel="iLightbox[gallery<?php the_ID(); ?>]" title="<?php echo get_post_field( 'post_excerpt', get_post_thumbnail_id() ); ?>" data-title="<?php echo get_post_field( 'post_title', get_post_thumbnail_id() ); ?>" data-caption="<?php echo get_post_field( 'post_excerpt', get_post_thumbnail_id() ); ?>">
												<span class="screen-reader-text"><?php esc_attr_e( 'View Larger Image', 'Avada' ); ?></span>
												<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
											</a>
										<?php else : ?>
											<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
										<?php endif; ?>
									</li>
								<?php endif; ?>
								<?php $i = 2; ?>
								<?php while ( $i <= Avada()->settings->get( 'posts_slideshow_number' ) ) : ?>
									<?php $attachment_new_id = fusion_get_featured_image_id( 'featured-image-' . $i, 'post' ); ?>
									<?php if ( $attachment_new_id ) : ?>
										<?php $attachment_image = wp_get_attachment_image_src( $attachment_new_id, 'full' ); ?>
										<?php $full_image       = wp_get_attachment_image_src( $attachment_new_id, 'full' ); ?>
										<?php $attachment_data  = wp_get_attachment_metadata( $attachment_new_id ); ?>
										<li>
											<?php if ( Avada()->settings->get( 'status_lightbox' ) && Avada()->settings->get( 'status_lightbox_single' ) ) : ?>
												<a href="<?php echo $full_image[0]; ?>" data-rel="iLightbox[gallery<?php the_ID(); ?>]" title="<?php echo get_post_field( 'post_excerpt', $attachment_new_id ); ?>" data-title="<?php echo get_post_field( 'post_title', $attachment_new_id ); ?>" data-caption="<?php echo get_post_field( 'post_excerpt', $attachment_new_id ); ?>">
													<?php echo wp_get_attachment_image( $attachment_new_id, 'full' ); ?>
												</a>
											<?php else : ?>
												<?php echo wp_get_attachment_image( $attachment_new_id, 'full' ); ?>
											<?php endif; ?>
										</li>
									<?php endif; ?>
									<?php $i++; ?>
								<?php endwhile; ?>
							</ul>
						</div>
						<?php Avada()->images->set_grid_image_meta( array() ); ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
			<h3 id="top-title"><a href="/membership-calls">Fellowship Membership Calls</a></h3>
			<?php if ( 'below' == Avada()->settings->get( 'blog_post_title' ) ) : ?>
				<?php echo avada_render_post_title( $post->ID, false, '', '1' ); // RYIT customized ?>
			<?php endif; ?>
			<div class="post-content">
				<?php the_content(); ?>
				<?php
					$show_video = false;
					if(empty($user_id)) {
						$user_id = ryit_get_user_ID();
					}

					$fellowship_level = get_field('ryit_user_fellowship_level', 'user_' . $user_id);
					$subscription_id = rcp_get_subscription_id($user_id);

					if(!empty($fellowship_level) && $fellowship_level != 'none') {
						if($fellowship_level == 'silver' || $fellowship_level == 'gold') {
							$show_video = true;
						}
					}
					else {
						if(user_can($user_id,'edit_pages')) { //leadership team
							$show_video = true;
						}
						if($subscription_id == 6) {
							$show_video = true;
						}
					}

					if($show_video) {
						$vimeo_ID = get_field('ryit_vimeo_ID');
						echo get_video_embed_vimeo($vimeo_ID,true);
					}
					else {
						echo '<h2>Sorry, you do not have access<h2>';
						echo '<p>Upgrade to Silver Level membership to watch recordings</p>';	
					}
				?>
				<?php avada_link_pages(); ?>
			</div>

			<?php if ( ! post_password_required( $post->ID ) ) : ?>
				<?php echo avada_render_post_metadata( 'single' ); ?>

				<?php if ( ( Avada()->settings->get( 'author_info' ) && 'no' != get_post_meta( $post->ID, 'pyre_author_info', true ) ) || ( ! Avada()->settings->get( 'author_info' ) && 'yes' == get_post_meta( $post->ID, 'pyre_author_info', true ) ) ) : ?>
					<div class="about-author">
						<?php ob_start(); ?>
						<?php the_author_posts_link(); ?>
						<?php $title = sprintf( __( 'About the Author: %s', 'Avada' ), ob_get_clean() ); ?>
						<?php echo Avada()->template->title_template( $title, '3' ); ?>
						<div class="about-author-container">
							<div class="avatar">
								<?php echo get_avatar( get_the_author_meta( 'email' ), '72' ); ?>
							</div>
							<div class="description">
								<?php the_author_meta( 'description' ); ?>
							</div>
							<div class="security-plead">

							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php
				/**
				 * Render Related Posts
				 */
				echo avada_render_related_posts( get_post_type() );
				?>

				<?php if ( ( Avada()->settings->get( 'blog_comments' ) && 'no' != get_post_meta( $post->ID, 'pyre_post_comments', true ) ) || ( ! Avada()->settings->get( 'blog_comments' ) && 'yes' == get_post_meta( $post->ID, 'pyre_post_comments', true ) ) ) : ?>
					<?php wp_reset_query(); ?>
					<?php ryit_fb_apps(); ?>
					<?php comments_template(); ?>
				<?php endif; ?>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
	<?php wp_reset_query(); ?>
</div>

<?php do_action( 'avada_after_content' ); ?>
<?php get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
