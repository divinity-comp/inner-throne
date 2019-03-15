<?php 
/**
 * A custom WordPress comment walker class to implement the Bootstrap 3 Media object in wordpress comment list.
 *
 * @package     WP Bootstrap Comment Walker
 * @version     1.0.0
 * @author      Edi Amin <to.ediamin@gmail.com>
 * @license     http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link        https://github.com/ediamin/wp-bootstrap-comment-walker
 */
class RYIT_Comment_Walker extends Walker_Comment {
    /**
    * Output a comment in the HTML5 format.
    *
    * @access protected
    * @since 1.0.0
    *
    * @see wp_list_comments()
    *
    * @param object $comment Comment to display.
    * @param int    $depth   Depth of comment.
    * @param array  $args    An array of arguments.
    */
    var $tree_type = 'comment';
    var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );

    // constructor – wrapper for the comments list
    function __construct() { ?>

        <section class="comments-list">

    <?php }

    // start_lvl – wrapper for child comments list
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $GLOBALS['comment_depth'] = $depth + 2; ?>
        
        <section class="child-comments comments-list">

    <?php }

    // end_lvl – closing wrapper for child comments list
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $GLOBALS['comment_depth'] = $depth + 2; ?>

        </section>

    <?php }

    // start_el – HTML for comment template
    function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
        $depth++;
        $GLOBALS['comment_depth'] = $depth;
        $GLOBALS['comment'] = $comment;
        $parent_class = ( empty( $args['has_children'] ) ? '' : 'parent' ); 

        if ( 'article' == $args['style'] ) {
            $tag = 'article';
            $add_below = 'comment';
        } else {
            $tag = 'article';
            $add_below = 'comment';
        } 

        var_dump($comment->user_id);

        $anonymous = get_field('user_profile_anonymous', 'user_' . $comment->user_id);
        var_dump($author);
        ?>

        <article <?php comment_class(empty( $args['has_children'] ) ? '' :'parent') ?> id="comment-<?php comment_ID() ?>" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
            <figure class="gravatar"><?php echo get_avatar( $comment, 65, '[default gravatar URL]', 'Author’s gravatar' ); ?></figure>
            <div class="comment-meta post-meta" role="complementary">
                <h2 class="comment-author">
                    <?php if(!$anonymous) : ?>
                    <a class="comment-author-link" href="<?php comment_author_url(); ?>" itemprop="author"><?php comment_author(); ?></a>
                    <?php else: ?>
                    Anonymous   
                    <?php endif; ?>
                </h2>
                <time class="comment-meta-item" datetime="<?php comment_date('Y-m-d') ?>T<?php comment_time('H:iP') ?>" itemprop="datePublished"><?php comment_date('jS F Y') ?>, <a href="#comment-<?php comment_ID() ?>" itemprop="url"><?php comment_time() ?></a></time>
                <?php edit_comment_link('<p class="comment-meta-item">Edit this comment</p>','',''); ?>
                <?php if ($comment->comment_approved == '0') : ?>
                <p class="comment-meta-item">Your comment is awaiting moderation.</p>
                <?php endif; ?>
            </div>
            <div class="comment-content post-content" itemprop="text">
                <?php comment_text() ?>
                <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            </div>

    <?php }

    // end_el – closing HTML for comment template
    function end_el(&$output, $comment, $depth = 0, $args = array() ) { ?>

        </article>

    <?php }

    // destructor – closing wrapper for the comments list
    function __destruct() { ?>

        </section>
    
    <?php
    }
} 
?>