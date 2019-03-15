<?php
/*
Plugin Name: Reclaim your Inner Throne functions
Description: Reclaim your Inner Throne modifications
Author: Eivind Figenschau Skjellum
Version: 0.1
*/

function category_body_class($classes) {
	global $post;
	$cats = get_the_category( $post->ID );
	foreach($cats as $cat) {
		//var_dump($cat);
		$classes[] = "category-" . $cat->slug;
	}
	return $classes;
}
add_filter( 'body_class', 'category_body_class' );


function modify_menu_args($args) {
	global $post;

	//categories that should use vanguard menu
	/*
	$cats = get_the_category( $post->ID );
	foreach($cats as $cat) {
		if($cat->slug == "vanguard-blog") {
			$args['menu'] = "3";	
		}
	}

	//pages that should use Vanguard menu
	$vanguard_pages = array(2352,2412,2354,2356,2355,2357,2377,2378,2387,2375,2388,2119,2374,2383,1868);
	if(in_array($post->ID, $vanguard_pages)) {
		$args['menu'] = "3";
	}
	return $args;
	*/

	//pages that should use Ryit autumn 2015 menu
	$ryit_pages = array(2354,4089, 4097,4114, 4100, 5910);
	if(in_array($post->ID, $ryit_pages)) {
		if(current_user_can('access_optimizemember_ccap_ryit')) { //user is part of training
			$args['menu'] = "52";
		}
	}
	return $args;
}

add_filter( 'wp_nav_menu_args', 'modify_menu_args' );


//12-month payment plan
function show_9_month_plan() {
	$var = $_GET['show_plan'];
	if($var) {
		$echo .= "<style type='text/css'>";
		$echo .= "#edd_price_option_657_twelvemonthlypaymentsof { display: block; }";
		$echo .= "</style>";
	}
	return $echo;
}

add_shortcode( '9_month_plan' , 'show_9_month_plan' );



function shortcode_membership_buy() {
	$users = get_users();
	$vanguard_count = 0;
	$high_level_users = 4; //admins etc
	$available_users = get_field('kotv_seats_available','options');
	foreach($users as $user) {
		if(user_can($user->ID, access_optimizemember_level4)) {
			$vanguard_count++;
		}
	}
	$vanguard_count -= $high_level_users;
	$vanguard_users_remaining = $available_users - $vanguard_count;
	if($vanguard_count < $available_users) {
	//$echo .= "<p class='vanguard_message'>We have a limited number of new seats available each month.";
	//$echo .= "<p class='vanguard_message'><em>" . $vanguard_users_remaining . " seats left</em> this month</p>";
	$echo .= do_shortcode('[optimizeMember-PayPal-Button level="4" ccaps="" desc="Inner Circle membership." ps="ryit" lc="" cc="USD" dg="0" ns="1" custom="hwww.inner-throne.com" success="http://www.inner-throne.com/inner-circle-thank-you" ta="9" tp="30" tt="D" ra="49" rp="1" rt="M" rr="1" rrt="" rra="1" image="http://www.inner-throne.com/wp-content/uploads/2015/08/join-now-button.png" output="button" /]');
	$echo .= "<p class='vanguard_message small'>Clicking button will take you to Paypal";
	} else {
	$echo .= "<p class='vanguard_message'>No seats left this month. Register for the mailing list below to be the first to know when new seats open.</p>";
	$echo .= "<div class='createsend-button' style='height:27px;display:inline-block;' data-listid='r/48/355/9C7/4E063E55B9C8AD18'>
	</div><script type='text/javascript'>(function () { var e = document.createElement('script'); e.type = 'text/javascript'; e.async = true; e.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://btn.createsend1.com/js/sb.min.js?v=3'; e.className = 'createsend-script'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s); })();</script>";
	}

	return $echo;
}

add_shortcode( 'membership_buy' , 'shortcode_membership_buy' );

//acf_add_options_page( $page );
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();	
}

// Add role class to body
function add_role_to_body($classes) {
	if( is_user_logged_in() ) {
		global $current_user;
		$user_role = array_shift($current_user->roles);
		$classes[] = 'role-'. $user_role;
	}

	if(current_user_can("access_optimizemember_level4")) {
		$classes[] = 'is-vanguard';
	}

	return $classes;
}
add_filter('body_class','add_role_to_body');
add_filter('admin_body_class', 'add_role_to_body');


function latest_vanguard_blog() {
	global $post;
	$posts = get_posts('category_name=inner-circle-blog&showposts=1&sort=ASC');

	return "<h3 class='latest_post' style='font-size: 21px; text-align: center'><span style='font-size: 18px; color: #333'>Latest post</span> <br/><a href='" . get_permalink($posts[0]->ID) . "'>" . get_the_title($posts[0]->ID) . "</a></h3>";
}

add_shortcode('latest_vanguard_blog', 'latest_vanguard_blog');


function ryit_list_archives($archive) {
	$args = array(
		'type' => 'monthly',
		'show_post_count' => false,
		'format' => 'html',
		'echo' => false
	);

	$archive = wp_get_archives( $args );

	return "<ul>" . $archive . "</ul>";
}

add_shortcode('ryit_list_archives', 'ryit_list_archives');
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin() && !current_user_can('editor')) {
	  show_admin_bar(false);
	}
}


function shortcode_portal_menu() {
	$echo = "";

	$echo .= "<div class='menu'>";
	$echo .= "<h2>Menu</h2><ul>";

	if (current_user_can("access_optimizemember_ccap_curr_round")) {
		$echo .= "<li><a href='/training'>RYIT training</a></li>";
	}

	if (current_user_can("access_optimizemember_ccap_alumni")) {
		$echo .= "<li><a href='/alumni-training'>RYIT Alumni training</a></li>";
	}

	if (current_user_can("access_optimizemember_ccap_ryit")) {
		$echo .= "<li><a href='/coaching'>Want 1-on-1 support?</a></li>";
	}

	if (current_user_can("access_optimizemember_level4")) {
		$echo .= "<li><a href='/members'>The Inner Circle</a></li>";
	}
	
	$echo .= "<li><a href='/forums'>Member forums</a></li></ul>";
	
	if(!current_user_can("access_optimizemember_level4") && !current_user_can("access_optimizemember_ccap_ryit")) :
		$echo .= "<h2>Products &amp; Services</h2><ul>";		
		if(!current_user_can("access_optimizemember_level4")) $echo .= "<li><a href='/join-the-vanguard'>The Inner Circle</a></li>";
		if(!current_user_can("access_optimizemember_ccap_ryit")) $echo .= "<li><a href='/enroll'>Reclaim your Inner Throne</a></li>";
		$echo .= "</ul><p style='margin: -1em auto 2em; max-width: 300px; text-align: center; font-style: italic;'>You must buy access to these services. Click the links for more information.</p>";
	endif;

	if (current_user_can("access_optimizemember_level1")) {
		if(current_user_can("access_optimizemember_ccap_ryit")) {
			$echo .= "<p><a href='/ryit-profile'>My account/profile</a>";
		}
		else {
			$echo .= "<p><a href='/my-account'>My account/profile</a>";
		}
		//$echo .= "<span style='margin: 0 10px; color: silver;'>|</span><a href='/affiliate-dashboard'>Affiliate area</a></p>";
	}

	return $echo;
}

add_shortcode( 'portal_menu' , 'shortcode_portal_menu' );



function shortcode_latest_blog() {
	$echo = "";

	$latest_blog = get_posts('category_name=blog&showposts=1');

	foreach($latest_blog as $blog) {
		$echo = "<h3>Latest blog post:</h3>" . "<p><a href='" . get_permalink($blog->ID) . "'>" . get_the_title($blog->ID) . "</a> <span style='text-align: right;'>(" . get_the_date('l, F j, Y', $blog->ID) . ")</span></a></p>";
	}

	return $echo;
}

add_shortcode( 'latest_blog' , 'shortcode_latest_blog' );


function ryit_testimonials() {
	$echo = "";

	$testimonials = get_posts('post_type=testimonials&showposts=-1&orderby=rand');
	
	$index = 1;
	foreach($testimonials as $testimonial) {
		if($index % 2 != 0) $echo .= "<div class='row'>";	
		$echo .= "<div class='testimonial'>";
		$echo .= "<img src='" . get_field('ryit_testimonial_portrait', $testimonial->ID) . "' />";
		$echo .= "<div class='text'>";
		$echo .= "<blockquote>" . get_field('ryit_testimonial_text', $testimonial->ID) . "</blockquote>";
		$echo .= "<cite>" . get_the_title($testimonial->ID) . "</cite>";	
		if($index % 2 == 0) $echo .= "</div>";		
		$echo .= "</div></div>";
		$index++;
	}

	return "<div class='testimonials'>" . $echo . "</div>";
}

add_shortcode( 'ryit_testimonials' , 'ryit_testimonials' );


function ryit_profile_page() {
	$echo = "";
	$echo .= "<div class='profile'>";
	$echo .= "<h1>" . do_shortcode('[wps-display-name]') . "</h1>";
	$echo .= "<div class='line'>";
	$echo .= "<div class='col' id='wps_activity_page_avatar'>" . do_shortcode('[wps-avatar size="190,190"]') . "</div>";
	$echo .= "<div class='col' id='wps_display_map'>" . do_shortcode('[wps-usermeta meta="wpspro_map" size="190,190"]') . "</div>";
	$echo .= "<div class='col' id='data'><h2>Location</h2><h3>City</h3>" . do_shortcode('[wps-usermeta meta="wpspro_home"]') . "<h3>Country</h3>" . do_shortcode('[wps-usermeta meta="wpspro_country"]') . "</div>";
	$echo .= "</div></div>";
	$echo .= "<div class='bio-meta'>";
	$echo .= "<h2>More information</h2>";

	// Shortcodes
	$movies = do_shortcode('[wps-extended slug="favorite-movies" show_if_empty="0"]');
	$music = do_shortcode('[wps-extended slug="favorite-music" show_if_empty="0"]');
	$twitter = do_shortcode('[wps-extended slug="twitter" before="Twitter page" show_if_empty="0"]');
	$growth_work = do_shortcode('[wps-extended slug="other-growth-work-ive-done" show_if_empty="0"]');
	$purpose = do_shortcode('[wps-extended slug="my-mission-in-life" before="My purpose" show_if_empty="0"]');
	$birthdate = do_shortcode('[wps-extended slug="birthdate" show_if_empty="0"]');
	$skype = do_shortcode('[wps-extended slug="skype-id" show_if_empty="0"]');
	$facebook = do_shortcode('[wps-extended slug="facebook" before="Facebook page" show_if_empty="0"]');
	$website = do_shortcode('[wps-extended slug="website" show_if_empty="0"]');

	if($movies) {
		$echo .= "<h3>Favorite movies</h3>";
		$echo .= "<p>" . $movies . "</p>";
	}

	if($music) {
		$echo .= "<h3>Favorite music</h3>";
		$echo .= "<p>" . $music . "</p>";
	}

	if($growth_work) {
		$echo .= "<h3>Previous growth work</h3>";
		$echo .= "<p>" . $growth_work . "</p>";
	}

	if($purpose) {
		$echo .= "<h3>My purpose</h3>";
		$echo .= "<p>" . $purpose . "</p>";
	}

	if($birthdate) {
		$echo .= "<h3>Birthdate</h3>";
		$echo .= "<p>" . $birthdate . "</p>";
	}

	if($skype) {
		$echo .= "<h3>Skype ID</h3>";
		$echo .= "<p>" . $skype . "</p>";
	}

	if($facebook) {
		$echo .= "<h3>Facebook page</h3>";
		$echo .= "<p>" . $facebook . "</p>";
	}

	if($twitter) {
		$echo .= "<h3>Twitter page</h3>";
		$echo .= "<p>" . $twitter . "</p>";
	}

	if($website) {
		$echo .= "<h3>Website</h3>";
		$echo .= "<p>" . $website . "</p>";
	}

	$echo .= "<h2>Activity feed</h2>";
	$echo .= do_shortcode('[wps-activity include_friends="0" login_url="/member_login" before="Activity stream" after="" show_if_empty="0"]');
	$echo .= do_shortcode('[wps-activity-post]');
	$echo .= "</div>";
	return $echo;
}

add_shortcode( 'ryit-profile-page' , 'ryit_profile_page' );


/* Remove menu elements reserved for logged in users for those who are not */

add_action('wp_footer','ryit_scripts');

function ryit_scripts() {
	$output = "
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
			if(!jQuery('body').hasClass('logged-in')) {
				jQuery('.logged-in-show').remove();
			}
		});
	</script>
	";

	echo $output;
}

/* End remove menu elements */


add_action('wp_head','ryit_favicon');

function ryit_favicon() {
	echo "<link rel='shortcut icon' href='https://www.inner-throne.com/wp-content/uploads/2015/04/crown-icon.ico' />";
	//this function resolves a problem in which the favicon isn't loaded with https, for some strange reason
}



function ryit_custom_post_types() {

     $supports = array('title');

     $labels = array(
          'name' => 'Testimonials',
          'singular_name' => 'Testimonial',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New Testimonial',
          'edit_item' => 'Edit Testimonial',
          'new_item' => 'New Testimonial',
          'view_item' => 'View Testimonial',
          'search_items' => 'Search Testimonials',
          'not_found' => 'No testimonials found',
          'not_found_in_trash' => 'No testimonials found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Testimonials'
     );


     $args = array(
          'label' => 'Testimonials',
          'public' => true,
          'show_ui' => true,
          'hierarchial' => false,
          'show_in_menu' => true,
          'menu_position' => 5,
          'rewrite' => true,
          'supports' => $supports,
          'labels' => $labels
     );

     register_post_type( 'testimonials' , $args );
}

add_action( 'init', 'ryit_custom_post_types' );


function ryit_loadstyles() {
	echo "<link rel='stylesheet' href='" . plugins_url( 'ryit/ryit.css', dirname(__FILE__)) . "'>";
}

add_action ('wp_head', 'ryit_loadstyles');



function ryit_add_editor_styles() {
    add_editor_style( '../plugins/ryit/ryit-editor-styles.css');
}
add_action( 'after_setup_theme', 'ryit_add_editor_styles' );


/*
add_filter( 'redirect_canonical', 'custom_disable_redirect_canonical' );
function custom_disable_redirect_canonical( $redirect_url ) {
    if ( is_paged() && is_singular() ) $redirect_url = false; 
    return $redirect_url; 
}
*/


function ryit_wordpress_comments() {
	//return wp_list_comments( 'type=comment&callback=mytheme_comment&echo=0' );
}

if(!function_exists(get_user_by_email)) {
	function get_user_by_email($email) {
	    return get_user_by('email', $email);
	}
}

function mytheme_comment($comment, $args, $depth) {

	if ( 'div' === $args['style'] ) {
	    $tag       = 'div';
	    $add_below = 'comment';
	} else {
	    $tag       = 'li';
	    $add_below = 'div-comment';
	}
	?>

	<?php
		var_dump(get_user_by_email(get_comment_author_email()));
	?>

	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	    <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	    <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
	    <?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author_link() ); ?>
	</div>
	<?php if ( $comment->comment_approved == '0' ) : ?>
	     <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
	      <br />
	<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
	    <?php
	    /* translators: 1: date, 2: time */
	    printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)' ), '  ', '' );
	    ?>
	</div>

	<?php comment_text(); ?>

	<div class="reply">
	    <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
	<?php
}



add_shortcode( 'ryit-wordpress-comments' , 'ryit_wordpress_comments' );


function appSumo() {
?>
<script src="//load.sumome.com/" data-sumo-site-id="03c434f78d73cc34fea09f34fab1bf85e8a02f27fedb4eee40151a37c8cd412f" async="async"></script>
<?php
}

add_action('wp_head', 'appSumo');



/* Fix pagination in blog category . For OptimizePress. Breaks avada
function bamboo_request($query_string )
{
    if( isset( $query_string['page'] ) ) {
        if( ''!=$query_string['page'] ) {
            if( isset( $query_string['name'] ) ) {
                unset( $query_string['name'] );
            }
        }
    }
    return $query_string;
}
add_filter('request', 'bamboo_request');


add_action('pre_get_posts','bamboo_pre_get_posts');
function bamboo_pre_get_posts( $query ) { 
    if( $query->is_main_query() && !$query->is_feed() && !is_admin() ) { 
        $query->set( 'paged', str_replace( '/', '', get_query_var( 'page' ) ) ); 
    } 
}
*/



/* End fix blog pagination */
/*


function print_r_html($arr, $style = "display: none; margin-left: 10px;")
{ static $i = 0; $i++;
  echo "\n<div id=\"array_tree_$i\" class=\"array_tree\">\n";
  foreach($arr as $key => $val)
  { switch (gettype($val))
    { case "array":
        echo "<a onclick=\"document.getElementById('";
        echo array_tree_element_$i."').style.display = ";
        echo "document.getElementById('array_tree_element_$i";
        echo "').style.display == 'block' ?";
        echo "'none' : 'block';\"\n";
        echo "name=\"array_tree_link_$i\" href=\"#array_tree_link_$i\">".htmlspecialchars($key)."</a><br />\n";
        echo "<div class=\"array_tree_element_\" id=\"array_tree_element_$i\" style=\"$style\">";
        echo print_r_html($val);
        echo "</div>";
      break;
      case "integer":
        echo "<b>".htmlspecialchars($key)."</b> => <i>".htmlspecialchars($val)."</i><br />";
      break;
      case "double":
        echo "<b>".htmlspecialchars($key)."</b> => <i>".htmlspecialchars($val)."</i><br />";
      break;
      case "boolean":
        echo "<b>".htmlspecialchars($key)."</b> => ";
        if ($val)
        { echo "true"; }
        else
        { echo "false"; }
        echo  "<br />\n";
      break;
      case "string":
        echo "<b>".htmlspecialchars($key)."</b> => <code>".htmlspecialchars($val)."</code><br />";
      break;
      default:
        echo "<b>".htmlspecialchars($key)."</b> => ".gettype($val)."<br />";
      break; }
    echo "\n"; }
  echo "</div>\n"; }
*/

?>