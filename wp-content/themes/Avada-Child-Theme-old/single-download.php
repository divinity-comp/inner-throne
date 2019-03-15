<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>

<div id="content" <?php Avada()->layout->add_style( 'content_style' ); ?>>

	<?php if ( ( Avada()->settings->get( 'blog_pn_nav' ) && 'no' != get_post_meta( $post->ID, 'pyre_post_pagination', true ) ) || ( ! Avada()->settings->get( 'blog_pn_nav' ) && 'yes' == get_post_meta( $post->ID, 'pyre_post_pagination', true ) ) ) : ?>

	<?php endif; ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
	<?php wp_reset_query(); ?>
</div>

<?php do_action( 'avada_after_content' ); ?>
<?php get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
