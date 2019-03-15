<?php
/**
 * Comments template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

do_action( 'avada_before_comments' );

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
?>

<?php if ( post_password_required() ) : ?>
	<?php return; ?>
<?php endif; ?>

<?php if ( have_comments() ) : ?>
	<div id="comments" class="comments-container">
		<ul class='list-unstyled'>
	        <?php
	            // Register Custom Comment Walker
	            require_once('homestudy-comment-walker.php');

	            wp_list_comments( array(
	                'style'         => 'ul',
	                'short_ping'    => true,
	                'avatar_size'   => '64',
	                'walker'        => new RYIT_Comment_Walker(),
	            ) );
	        ?>
		</ul><!-- .comment-list -->

		<?php comment_form(); ?>

		<?php if ( function_exists( 'the_comments_navigation' ) ) : ?>
			<?php the_comments_navigation(); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
	<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'Avada' ); ?></p>
<?php endif; ?>

<?php /* Omit closing PHP tag to avoid "Headers already sent" issues. */ ?>
