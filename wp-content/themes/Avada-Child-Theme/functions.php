<?php
//Defer Parsing Of JavaScript (#32 from http://www.onlinemediamasters.com/why-is-wordpress-so-slow/#host-google-analytics-locally)
/* Creates problems with ACF
if (!(is_admin() )) {
function defer_parsing_of_js ( $url ) {
if ( FALSE === strpos( $url, '.js' ) ) return $url;
if ( strpos( $url, 'jquery.js' ) ) return $url;
// return "$url' defer ";
return "$url' defer onload='";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
}
*/

//acf_add_options_page( $page );
if (function_exists('acf_add_options_page')) {
	acf_add_options_page();
}

function ryit_initialize() {
	if (is_front_page()) {
		ryit_next_upcoming_event();
	}
}

add_action('wp_loaded', 'ryit_initialize');

function ryit_user_last_login( $user_login, $user ) {
	update_user_meta($user->ID, 'ryit_user_last_login', time() );
}
add_action( 'wp_login', 'ryit_user_last_login', 10, 2 );


function ryit_list_users_logged_in() {
	$users = get_users();
	$echo = '';
	foreach ($users as $user) {
		if(get_user_meta($user->ID, 'ryit_user_last_login', true)) {
			$echo .= '<li>' . $user->first_name . ' ' . $user->last_name . ': ' . date('Y-m-d H:i:s', get_user_meta($user->ID, 'ryit_user_last_login', true)) . '</li>';
		}
	}
	return '<ul>' . $echo . '</ul>';
}

add_shortcode('ryit_list_users_logged_in','ryit_list_users_logged_in');


function ryit_list_archives($archive) {
	$args = array(
		'type' => 'monthly',
		'show_post_count' => false,
		'format' => 'html',
		'echo' => false
	);

	$archive = wp_get_archives($args);

	return "<ul>" . $archive . "</ul>";
}

add_shortcode('ryit_list_archives', 'ryit_list_archives');


/* Remove admin bar for all users who are not admins or editors */
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin() && !current_user_can('editor')) {
		show_admin_bar(false);
	}
}

// Disable WP Rocket Lazy load on archive pages (it causes prbolems with Avada grid)
add_filter('wp', '__deactivate_rocket_lazyload');
function __deactivate_rocket_lazyload() {
	if (is_archive()) {
		add_filter('do_rocket_lazyload', '__return_false');
	}
}

/* Limit media uploader image listing to images uploaded by user */
// More info - https://www.isitwp.com/restricting-users-to-view-only-media-library-items-they-upload/

function my_files_only( $wp_query ) {
	if ($wp_query->query_vars['post_type']=='attachment'){
		if (!current_user_can('administrator')) {
			global $current_user;
			$wp_query->set( 'author', $current_user->id );
		}
	}
}
 
add_filter('parse_query', 'my_files_only' );


function shortcode_latest_blog() {
	$echo = "";

	$latest_blog = get_posts('category_name=blog&showposts=1');

	foreach ($latest_blog as $blog) {
		$echo = "<h3>Latest blog post:</h3>" . "<p><a href='" . get_permalink($blog->ID) . "'>" . get_the_title($blog->ID) . "</a> <span style='text-align: right;'>(" . get_the_date('l, F j, Y', $blog->ID) . ")</span></a></p>";
	}

	return $echo;
}

add_shortcode('latest_blog', 'shortcode_latest_blog');

function ryit_testimonials() {
	$echo = "";

	$testimonials = get_posts('post_type=testimonials&showposts=-1&orderby=rand');

	$index = 1;
	foreach ($testimonials as $testimonial) {
		if ($index % 2 != 0) $echo .= "<div class='row'>";
		$echo .= "<div class='testimonial'>";
		$echo .= "<img src='" . get_field('ryit_testimonial_portrait', $testimonial->ID) . "' />";
		$echo .= "<div class='text'>";
		$echo .= "<blockquote>" . get_field('ryit_testimonial_text', $testimonial->ID) . "</blockquote>";
		$echo .= "<cite>" . get_the_title($testimonial->ID) . "</cite>";
		if ($index % 2 == 0) $echo .= "</div>";
		$echo .= "</div></div>";
		$index++;
	}

	return "<div class='testimonials'>" . $echo . "</div>";
}

add_shortcode('ryit_testimonials', 'ryit_testimonials');

function ryit_video_list($atts) {

	$type = $atts['type'];
	if (empty($type)) {
		$type = "inspiring";
	}

	global $post;

	/*
	 var_dump($test);
	 return print_r($test);
	*/

	/*
	 $subscription_id = rcp_get_subscription_id( get_current_user_id() );
	 if( $subscription_id == 2 ) {
	   // do something here
	 }*/

	//$echo = "<div class='video_list'>";
	if ($type == "inspiring") {
		$videos = get_field('ryit_inspiring_videos', $post->ID);
	}
	else if ($type == "coaching") {
		$videos = get_field('ryit_coaching_videos', $post->ID);
	}

	$video_count = count($videos);
	$video_index = 1;

	foreach ($videos as $video) {

		//First Column
		$echo .= '<div class="fusion-layout-column fusion_builder_column fusion_builder_column_2_3  fusion-two-third fusion-column-first 2_3" style="margin-top:0px;margin-bottom:20px;width:66.66%;width:calc(66.66% - ( ( 4% ) * 0.6666 ) );margin-right: 4%;">';
		$echo .= '<div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left   top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">';
		$echo .= '<div class="fusion-video" style="max-width:700px;max-height:390px;"><div class="video-shortcode"><div class="fluid-width-video-wrapper">';

		if ($video['type'] == "youtube") {
			$echo .= '<iframe src="https://www.youtube.com/embed/' . $video['id'] . '?rel=0&amp;controls=1&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe><div class="fusion-clearfix"></div>';
		}
		elseif ($video['type'] == "vimeo") {
			$echo .= '<iframe src="https://player.vimeo.com/video/' . $video['id'] . '" width="640" height="360" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen></iframe><div class="fusion-clearfix"></div>';
		}

		$echo .= '</div></div></div>';
		//$echo .= '</div>';
		$echo .= '<div class="fusion-clearfix"></div></div></div>';

		//Second column (text etc)
		$echo .= '<div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_3  fusion-one-third fusion-column-last 1_3" style="margin-top:0px;margin-bottom:20px;width:33.33%;width:calc(33.33% - ( ( 4% ) * 0.3333 ) );">';
		$echo .= '<div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">';
		$echo .= '<div class="fusion-title title fusion-sep-none fusion-title-size-three fusion-border-below-title" style="margin-top:0px;margin-bottom:15px;"><h3 class="title-heading-left">' . $video['title'] . '</h3></div>';
		$echo .= '<div class="fusion-text">' . wpautop($video['text']) . '</div></div></div>';

		/*
		  $echo .= '<div class="video-text">' . wpautop($video['text']) . '</div>';
		  $echo .= '</div>';
		*/

		//Break apart videos
		if ($video_index < $video_count) {
			$echo .= '<div class="fusion-clearfix" style="padding-bottom: 35px;"></div>';
		}

		$video_index++;
	}

	//$echo .= '</div>';
	if ($type == "inspiring") {
		return $echo . '<div class="fusion-clearfix test" style="padding-bottom: 35px;"></div>';
	}
	else { //coaching videos
		$subscription_id = rcp_get_subscription_id(get_current_user_id());
		if ($subscription_id != 3 && !current_user_can('edit_pages')) {
			return '<p style="text-align: center;">Recordings of coaching calls are only visible<br/> to members of the current Fellowship.</p><div class="fusion-clearfix" style="padding-bottom: 120px;"></div>';
		}
		return $echo . '<div class="fusion-clearfix" style="padding-bottom: 100px;"></div>';
	}
}

add_shortcode('ryit_video_list', 'ryit_video_list');


function get_video_embed_vimeo($video_id=false,$responsive = true,$width=640,$height=360) {
	if(!$video_id) return false;

	if($responsive) {
		return '<div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/' . $video_id . '?title=0&byline=0&portrait=0" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>';
	}
	else {
		return '<iframe src="https://player.vimeo.com/video/' . $video_id . '" width="' . $width . '" height="' . $height . '" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen></iframe><div class="fusion-clearfix"></div>';
	}
}


/* Remove menu elements reserved for logged in users for those who are not */

add_action('wp_head', 'ryit_get_javascripts');

function ryit_get_javascripts() {
	ob_start();
?>
    <script type='text/javascript'>
	   $j = jQuery.noConflict();

	   $j('document').ready(function() {
			if(!$j('body').hasClass('logged-in')) {
			 $j('.logged-in-show').remove();
			}
	   });

   	function show_popup(content,classes='',close_btn=true,fadeout_delay=false,load_url=false) {
			$j('body').addClass('has-popup');
			$j('#ryit-popup').remove();
			$j('body').append('<div id="ryit-popup-overlay"></div>');
			if(classes !== '') classes = ' class="' + classes + '"';
			var close_btn = close_btn == true ? '<a href="#" class="close"><span class="fa fa-times"></span></a>' : '';

			if(content == 'loading') {
				content = '<span class="spinner"></span><p>Refreshing View ...</p>';
			}
			$j('body').append('<div id="ryit-popup"' + classes + '><div class="innerwrap">' + content + close_btn + '</div></div>');
			var scrollTop = $j(document).scrollTop();
			if(classes.indexOf('fixed') == -1) {
				$j("#ryit-popup").css("top", scrollTop + 150 + "px");
			}

			if(fadeout_delay !== false) {
				setTimeout(function() { 
					hide_popup(true,500); 
					if(load_url !== false) {
						location.reload(load_url);
					}
				}, fadeout_delay);
			}
			else {
				if(load_url !== false) {
					location.reload(load_url);
				}
			}
		}

		function hide_popup(fadeout=false,fadeout_time=false) {
			if(fadeout) {
				if(fadeout_time === false) {
					fadeout_time = 500;
				}
				$j('#ryit-popup').fadeOut(fadeout_time,function() {
					$j('#ryit-popup').remove();
				});
				$j('#ryit-popup-overlay').fadeOut(fadeout_time,function() {
					$j('#ryit-popup-overlay').remove();
				});
			}
			else {
				$j('#ryit-popup').remove();
				$j('#ryit-popup-overlay').remove();
			}
		}

		$j('document').on('click','body.has-popup #ryit-popup', function(e) {
			e.stopPropagation();
			console.log("clicking one");
		});

		$j('document').on('click','body.has-popup', function(e) {
			console.log("clicking body");
			$j("#ryit-popup").fadeOut(350);
		});

   </script>
<?php
	echo ob_get_clean();
}

/* End remove menu elements */

add_action('wp_head', 'ryit_favicon');

function ryit_favicon() {
	echo '<code style="overflow: scroll;"><link rel="shortcut icon" href="https://www.inner-throne.com/favicon.ico" type="image/ico" /></code>';
}

function ryit_custom_post_types() {

	$supports = array(
		'title'
	);

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
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'rewrite' => true,
		'supports' => $supports,
		'labels' => $labels
	);

	register_post_type('testimonials', $args);

	$labels = array(
		'name' => 'Triad',
		'singular_name' => 'Triad',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Triad',
		'edit_item' => 'Edit Triad',
		'new_item' => 'New Triad',
		'view_item' => 'View Triad',
		'search_items' => 'Search Triads',
		'not_found' => 'No triads found',
		'not_found_in_trash' => 'No triads found in Trash',
		'parent_item_colon' => '',
		'menu_name' => 'Triads'
	);

	$args = array(
		'label' => 'Triads',
		'public' => true,
		'show_ui' => true,
		'hierarchial' => false,
		'show_in_menu' => true,
		'menu_position' => 5,
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'rewrite' => true,
		'supports' => $supports,
		'labels' => $labels
	);

	register_post_type('triads', $args);

	$labels = array(
		'name' => 'RYIT popups',
		'singular_name' => 'RYIT popup',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New popup',
		'edit_item' => 'Edit popup',
		'new_item' => 'New popup',
		'view_item' => 'View popup',
		'search_items' => 'Search popups',
		'not_found' => 'No popups found',
		'not_found_in_trash' => 'No popups found in Trash',
		'parent_item_colon' => '',
		'menu_name' => 'Popups'
	);

	$args = array(
		'label' => 'RYIT popups',
		'public' => true,
		'show_ui' => true,
		'hierarchial' => false,
		'show_in_menu' => true,
		'menu_position' => 5,
		'rewrite' => true,
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'supports' => $supports,
		'labels' => $labels
	);

	register_post_type('ryit_popup', $args);
}

add_action('init', 'ryit_custom_post_types');

/**************************************/
/****** ADD MOBILE DETECT CLASS *******/
/**************************************/

require_once 'includes/Mobile_Detect.php';
$detect = new Mobile_Detect;

/*********************************/
/****** CUSTOM RYIT POPUPS *******/
/*********************************/

//This function has not yet been completed. It's not yet generalized; only works for front page popup
function ryit_popup($popup_id) {

	$cookie_exists = $_COOKIE['ryit_popup_' . $popup_id];
	if($cookie_exists) {
		return false;
	}

	global $post;
	$page_ids = array(
		44306,
		4711,
		46119,
		54255,
		44199,
		44884,
		44702,
		44135
	); //pages to show popup
	if (is_front_page() || is_archive() || in_array($post->ID, $page_ids)) {
		$delay_in_ms = 15000;
	}
	else if (is_single()) {
		$delay_in_ms = 30000;
	}
	else {
		return false; //hide popup on anything but front page and blog posts
		
	}

	//Read array_values(input)
	$title_1 = get_field('ryit_popup_title_1', $popup_id);
	$title_2 = get_field('ryit_popup_title_2', $popup_id);
	$text = get_field('ryit_popup_text', $popup_id);
	$bullets = get_field('ryit_popup_bullets', $popup_id);

	if ($bullets) {
		foreach ($bullets as $bullet) {
			$popup_bullets = $bullet;
		}
	}

	$echo = "<div id='ryit-popup-overlay' class='hidden'></div>";
	$echo .= "<div id='ryit-popup' class='hidden'>";
	$echo .= "<div class='innerwrap'>";
	$echo .= "<div class='logo'></div>";

	// Step #1
	$echo .= "<div class='step step_one'>";
	$echo .= "<div class='content'>";
	$echo .= "<h2>" . $title_1 . "</h2>";
	$echo .= "<h3>" . $title_2 . "</h3>";
	if (!empty($text)):
		$echo .= "<div id='ryit_popup_text'>" . $text . "</div>";
	endif;
	$echo .= "</div>"; //end content
	$echo .= "<div class='ui'>";
	$echo .= "<ul><li class='yes'><a href='#'>Yes</a></li><li class='sep-text'>Or</li><li class='no'><a href='#'>No</a></li></ul>";
	$echo .= "</div>"; //end ui
	$echo .= "</div>"; //end step
	

	//Step #2
	$echo .= "<div class='step step_two'>";
	$echo .= "<div class='content'><h3>Good Call! :)</h3><p>Register below and we&rsquo;ll send you this powerful document designed to <strong>boost your POWER</strong> and set you on the Path to Sovereignty. We think you'll love it! :)</p></div>";
	$echo .= "<div class='ui'>";
	$echo .= "<div class='kartra_optin_containere4da3b7fbbce2345d7772b0674a318d5'></div><script src='https://app.kartra.com/optin/XZhs5bwnzu9c'></script>";
	/*
	   $echo .= '<form action="https://masculinity-movies.createsend.com/t/r/s/' . $mailing_list . '/" method="post" id="subForm" class="clearfix">
		  <div><input type="text" name="cm-name" placeholder="Your first name" id="name" /></div>
		  <div><input type="email" spellcheck="false" name="cm-hjtykuu-' . $mailing_list . '" id="email" placeholder="Your email address" /></div>';
		  if($opt_in) :
			 if($opt_in_hidden) :
				$echo .= '<div><input id="listdtuldjd" name="' . $opt_in . '" type="checkbox" checked="checked" style="display: none;" /></div>';
			 else :
				$echo .= '<p>
			 <label>Opt into another list</label><br />
			 <input id="listdtuldjd" name="' . $opt_in . '" type="checkbox" /> <label for="listdtuldjd">' . $opt_in_title . '</label>
			 </p>';
			 endif;
		  endif;
	   $echo .= '<button type="submit" value="Subscribe">Get Access NOW</button>
	   </form>';*/
	$echo .= '</div>'; //end ui
	$echo .= '</div>'; //end wrap
	$echo .= '<a href="#" class="close"><span class="fa fa-times"></span></a>'; //end wrap
	//end of wrapper
	$echo .= "</div>";

	//Wrap up popups
	$echo .= "</div>";

	$echo .= '<script type="text/javascript">
	   $j = jQuery.noConflict();

	   $j(document).ready(function() {

		//set up cookies
		var cookie = Cookies.get("ryit_popup_' . $popup_id . '");
		console.log("cookie is: " + cookie);


		if(cookie == null || cookie == "undefined") {
		    console.log("no cookie");
		    var delay = ' . $delay_in_ms . ';
		    setTimeout(function() {
			   Cookies.set("ryit_popup_' . $popup_id . '", "true", { expires: 30, path: "/" });   
			   $j("#ryit-popup-overlay").removeClass("hidden");
			   $j("#ryit-popup-overlay").addClass("visible");
			   $j("#ryit-popup").removeClass("hidden");
			   $j("#ryit-popup").addClass("visible magnet");
			   var scrollTop = $j(document).scrollTop();
			   console.log("scroll top " + scrollTop);
			   $j("#ryit-popup").css("top", scrollTop + 150 + "px");

		    }, delay);
		}


		$j(".ui .yes a").on("click", function(e) {
		    e.preventDefault();
		    $j(".step_two").fadeIn(500, function() {
			   $j(".step_one").fadeOut(500);
		    });
		});


		$j("#ryit-popup a.close").on("click", function(e) {
		    e.preventDefault();
		    $j("#ryit-popup-overlay").clearQueue();
		    $j("#ryit-popup").clearQueue();
		    $j("#ryit-popup-overlay").fadeOut(500);
		    $j("#ryit-popup").fadeOut(500);
		});

		$j(".ui .no a").on("click", function(e) {
		    e.preventDefault();
		    $j("#ryit-popup .content").html("<h3>Got it!</h3><p>If you should change your mind, you can find the Free E-Book in the \"Our Offerings\"-menu! We think you will like it :)</p>");
		    $j("#ryit-popup .ui").fadeOut(500);
		    $j("#ryit-popup-overlay").delay(8000).fadeOut(500);
		    $j("#ryit-popup").delay(8000).fadeOut(500);
		});

		$j(function () {
		  $j("#subForm").submit(function (e) {
		    e.preventDefault();
		    $j("#email").removeClass();
		    $j(".modal button").fadeTo(200,0.5);
		    $j.getJSON(
		    this.action + "?callback=?",
		    $j(this).serialize(),
		    function (data) {
			   $j("button").stop().fadeTo(200,1);
			   if (data.Status === 400) { 
				  if($j("#name").val() === "") {
					 $j("#name").parent().addClass("error");
				  } else {
					$j("#name").parent().removeClass("error"); 
				  }
				  $j("#email").parent().addClass("error"); 
			   } else { 
				  $j("#ryit-popup .content").html("' . $sub_response . '");
				  $j("#ryit-popup .ui").fadeOut(300);
			   }
		    });
		  });
		});
	   });
    </script>';

	echo $echo;
	//echo "<h2>modal wrap</h2>";
	
}

//Popup for page view / ui updates
/*
function ryit_ui_feedback_popup($html = "", $fixed = false, $print = true) {
	$echo = ryit_get_javascript('feedback-popup');
	if ($fixed) $fixed = " fixed";
	$echo .= '<div id="ryit-popup-overlay" class="hidden"></div>';
	$echo .= '<div id="ryit-popup" class="hidden' . $fixed . '">';
	$echo .= '<div class="innerwrap">';
	$echo .= $html;
	$echo .= '</div>';
	$echo .= '</div>';
	if($print) {
		echo $echo;
	}
	else {
		return $echo; 
	}
}*/

/*************************************/
/****** INITIALIZE RYIT PAGES ********/
/*************************************/

function is_ryit_page() {
	global $post;
	$ryit_home_page = 45618;
	if (is_page($ryit_home_page) || ($post->post_parent == $ryit_home_page)) {
		return true;
	}
}

/* SET UP STYLES */

function ryit_body_classes() {
	global $post;
	$classes = array();

	if (is_front_page()) {
		$classes[] = "home";
	}

	if (is_single()) {
		$classes[] = "single";
	}

	$classes[] = sanitize_title(get_the_title());

	$body_width = get_field('ryit_body_width');
	if ($body_width != "default" && !empty($body_width)):
		if ($body_width == "small"):
			$classes[] = "content-width-small";
		elseif ($body_width == "medium"):
			$classes[] = "content-width-medium";
		elseif ($body_width == "large"):
			$classes[] = "content-width-large";
		endif;
	endif;

	$splash_page = get_field('ryit_splash_page');
	if (!empty($splash_page)) {
		if ($splash_page[0] == "yes") {
			$classes[] = "splash-page";
		}
	}

	//admin class
	if (current_user_can('administrator')) {
		$classes[] = "administrator";
	}

	$post_type = get_post_type($post->ID);
	if($post_type != false) $classes[] = 'post-type-' . $post_type;

	//category classes
	$cats = get_the_category($post->ID);
	foreach ($cats as $cat) {
		//var_dump($cat);
		$classes[] = "category-" . $cat->slug;
	}

	//Restrict content pro
	$subscription_id = rcp_get_subscription_id(get_current_user_id());

	//logged in class
	if (is_user_logged_in()) {
		$classes[] = "logged-in";
		if (current_user_can('editor') || current_user_can('administrator')) {
			$classes[] = "membership-level-1";
			$classes[] = "membership-level-2";
			$classes[] = "membership-level-3";
			$classes[] = "membership-level-4";
		}
		else {
			$classes[] = "membership-level-" . $subscription_id;
		}
	}
	else {
		$classes[] = "logged-out";
	}

	/* Journey to the Great Self class */

	if (is_jgs_page()) {
		$classes[] = "jgs-course-page";
		if (jgs_user_has_answered()) {
			$classes[] = "jgs-access-granted";
		}
	}

 	$entrepreneur_track_enabled = get_field('field_5bbb7710d14c0', 'user_' . get_current_user_id());
 	if($entrepreneur_track_enabled) {
 		$classes[] = 'entrepreneur-track-enabled';
 	}

	if(is_fellowship_page()) :
		$classes[] = 'is-loading';
		//when using the name 'is-loading', Wordpress doesn't use it. WHY??
		return $classes;    
	endif;

	return $classes;
}

add_filter('body_class', 'ryit_body_classes');

function ryit_add_entrepreneur_track() {
	ob_start();
	if (is_ryit_page()):
?>
  <!-- RYIT Javascripts -->
  <script type="text/javascript">   
    //Set up AJAX 
    
    var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

    jQuery(document).ready(function($j){
		 $j(document).on('click', '#enable-entrepreneur-track', function(e) {
		   e.preventDefault();

		   var data = {
			action : 'ryit_change_acf_field_value',
			field_key : 'field_5bbb7710d14c0',  
			post_id : 'user_' + $j('body').attr('user_id'),
			new_value : 1
		   };

		   $j.ajax({
				url: ajaxurl,
				type: 'GET', // the kind of data we are sending
				data: data,        
				dataType: 'json',
				success: function(response) {
				  console.log('ajax is run');
				  $j('.entrepreneur-track-enable').fadeOut();
				  $j('.entrepreneur-track').fadeIn();      
				}
		   });
		 });
    });
  </script>
  <!-- End RYIT Javascripts -->
<?php
	endif;
	echo ob_get_clean();
}
add_action('wp_head', 'ryit_add_entrepreneur_track');

function remove_admin_access_for_non_editor() {
	if (is_admin() && !current_user_can('edit_posts') && !(defined('DOING_AJAX') && DOING_AJAX)) {
		wp_redirect(home_url());
		exit;
	}
}
add_action('init', 'remove_admin_access_for_non_editor');

//Add backend styles
function ryit_add_editor_styles() {
	add_editor_style('../plugins/ryit/ryit-editor-styles.css');
}
add_action('after_setup_theme', 'ryit_add_editor_styles');

//from https://wpcurve.com/wordpress-speed/ - remove query strings from static resources
function ewp_remove_script_version($src) {
	return remove_query_arg('ver', $src);
}
add_filter('script_loader_src', 'ewp_remove_script_version', 15, 1);
add_filter('style_loader_src', 'ewp_remove_script_version', 15, 1);

function ryit_wordpress_comments() {
	//return wp_list_comments( 'type=comment&callback=mytheme_comment&echo=0' );
	
}

function mytheme_comment($comment, $args, $depth) {

	if ('div' === $args['style']) {
		$tag = 'div';
		$add_below = 'comment';
	}
	else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>

    <?php
	var_dump(get_user_by_email(get_comment_author_email()));
?>

    <<?php echo $tag ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
    <?php if ('div' != $args['style']): ?>
	   <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
    <?php
	endif; ?>
    <div class="comment-author vcard">
	   <?php if ($args['avatar_size'] != 0) echo get_avatar($comment, $args['avatar_size']); ?>
	   <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>') , get_comment_author_link()); ?>
    </div>
    <?php if ($comment->comment_approved == '0'): ?>
	    <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></em>
		<br />
    <?php
	endif; ?>

    <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>">
	   <?php
	/* translators: 1: date, 2: time */
	printf(__('%1$s at %2$s') , get_comment_date() , get_comment_time()); ?></a><?php edit_comment_link(__('(Edit)') , '  ', '');
?>
    </div>

    <?php comment_text(); ?>

    <div class="reply">
	   <?php comment_reply_link(array_merge($args, array(
		'add_below' => $add_below,
		'depth' => $depth,
		'max_depth' => $args['max_depth']
	))); ?>
    </div>
    <?php if ('div' != $args['style']): ?>
    </div>
    <?php
	endif; ?>
    <?php
}

add_shortcode('ryit-wordpress-comments', 'ryit_wordpress_comments');

add_action('admin_init', 'disable_dashboard');

function disable_dashboard() {
	if (!current_user_can('publish_posts') && !defined('DOING_AJAX')) {
		wp_redirect(home_url());
		exit;
	}
}

/****************************************************/
/******* RESTRICT CONTENT PRO PAGE ACCESS FIX *******/
/* PREVENTS DB SLOWOWN BUT STILL DENIES PAGE ACCESS */
/****************************************************/

function ag_rcp_redirect_from_restricted_post() {
	global $rcp_options, $post;
	$member = new RCP_Member(get_current_user_id());
	// Bail if we're not on a single post/page.
	if (!is_singular()) {
		return;
	}
	// Bail if current user has permission to view this post/page.
	if ($member->can_access($post->ID)) {
		return;
	}
	$redirect_page_id = $rcp_options['redirect_from_premium'];
	// Use chosen redirect page, or homepage if not set.
	$redirect_url = (!empty($redirect_page_id) && $post->ID != $redirect_page_id) ? get_permalink($redirect_page_id) : home_url();
	wp_redirect($redirect_url);
	exit;
}

add_action('template_redirect', 'ag_rcp_redirect_from_restricted_post', 999);

/*

function appSumo() {
?>
<script src="//load.sumome.com/" data-sumo-site-id="03c434f78d73cc34fea09f34fab1bf85e8a02f27fedb4eee40151a37c8cd412f" async="async"></script>
<?php
}

add_action('wp_head', 'appSumo');
*/

function theme_enqueue_styles() {
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array(
		'avada-stylesheet'
	));
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain('Avada', $lang);
}
add_action('after_setup_theme', 'avada_lang_setup');

function ryit_add_script() {
	wp_register_script('ryit_script', get_stylesheet_directory_uri() . '/ryit-scripts.js', '', '', true);
	wp_enqueue_script('ryit_script');
}

add_action('wp_enqueue_scripts', 'ryit_add_script', 999);

// Our custom post type function
function create_coursepage_posttype() {
	$labels = array(
		'name' => __('Course page') ,
		'singular_name' => __('Course page') ,
		'menu_name' => __('Course page') ,
		'parent_item_colon' => __('Course page') ,
		'all_items' => __('Course pages') ,
		'view_item' => __('View course page') ,
		'add_new_item' => __('Add new course page') ,
		'add_new' => __('Add new') ,
		'edit_item' => __('Edit course page') ,
		'update_item' => __('Edit course page') ,
		'search_items' => __('Search course pages') ,
		'not_found' => __('Not found') ,
		'not_found_in_trash' => __('Not found in Trash') ,
	);

	$args = array(
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'initiation', // if you need slug
			'with_front' => false,
		) ,
		'labels' => $labels,
		'taxonomies' => array(
			'course-type'
		) ,
		'supports' => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'page-attributes'
		) ,
		'can_export' => true,
		'publicly_queryable' => true,
	);

	// Registering your Custom Post Type
	register_post_type('course-page', $args);
}

// Hooking up our function to theme setup
add_action('init', 'create_coursepage_posttype');

//Advanced Custom Fields override
//add_filter('acf/settings/remove_wp_meta_box', '__return_false'); //don't remove custom fields meta box


// Our custom post type function
function create_blog_posttype() {
	$labels = array(
		'name' => __('Blog') ,
		'singular_name' => __('Blog') ,
		'menu_name' => __('Blog') ,
		'parent_item_colon' => __('Parent blog') ,
		'all_items' => __('All blogs') ,
		'view_item' => __('View blog') ,
		'add_new_item' => __('Add new blog') ,
		'add_new' => __('Add new') ,
		'edit_item' => __('Edit blog') ,
		'update_item' => __('Edit blog') ,
		'search_items' => __('Search blogs') ,
		'not_found' => __('Not found') ,
		'not_found_in_trash' => __('Not found in Trash') ,
	);

	$args = array(
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'has_archive' => true,
		'rewrite' => array(
			'slug' => 'blog', // if you need slug
			'with_front' => false,
		) ,
		'labels' => $labels,
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'taxonomies' => array(
			'category',
			'post_tag'
		) ,
		'supports' => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'comments'
		) ,
		'can_export' => true,
		'publicly_queryable' => true,
	);

	// Registering your Custom Post Type
	register_post_type('blog', $args);
}

// Hooking up our function to theme setup
add_action('init', 'create_blog_posttype');




/*
function create_icblog_posttype() {
	$labels = array(
		'name' => __('IC Blog') ,
		'singular_name' => __('IC Blog') ,
		'menu_name' => __('IC Blog') ,
		'parent_item_colon' => __('Parent IC blog') ,
		'all_items' => __('All IC blogs') ,
		'view_item' => __('View IC blog') ,
		'add_new_item' => __('Add new IC blog') ,
		'add_new' => __('Add new') ,
		'edit_item' => __('Edit IC blog') ,
		'update_item' => __('Edit IC blog') ,
		'search_items' => __('Search IC blogs') ,
		'not_found' => __('Not found') ,
		'not_found_in_trash' => __('Not found in Trash') ,
	);

	$args = array(
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'has_archive' => true,
		'labels' => $labels,
		'taxonomies' => array(
			'ic-category',
			'post_tag'
		) ,
		'supports' => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'comments'
		) ,
		'rewrite' => array(
			'slug' => 'ic-blog'
		) ,
		'can_export' => true,
		'publicly_queryable' => true,
	);

	// Registering your Custom Post Type
	register_post_type('inner-circle-blog', $args);
}

// Hooking up our function to theme setup
add_action('init', 'create_icblog_posttype');

*/
function namespace_add_custom_types($query) {
	if (is_category() || is_tag() && empty($query->query_vars['suppress_filters'])) {
		$query->set('post_type', array(
			'post',
			'nav_menu_item',
			'blog'
		));
		return $query;
	}
}
add_filter('pre_get_posts', 'namespace_add_custom_types');

//ADD IC taxonomy
// hook into the init action and call create_book_taxonomies when it fires
add_action('init', 'ryit_create_taxonomies', 0);

function ryit_create_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name' => _x('Categories', 'taxonomy general name', 'textdomain') ,
		'singular_name' => _x('Category', 'taxonomy singular name', 'textdomain') ,
		'search_items' => __('Categories', 'textdomain') ,
		'all_items' => __('All categories', 'textdomain') ,
		'parent_item' => __('Parent categories', 'textdomain') ,
		'parent_item_colon' => __('Parent category:', 'textdomain') ,
		'edit_item' => __('Edit category', 'textdomain') ,
		'update_item' => __('Update category', 'textdomain') ,
		'add_new_item' => __('Add new category', 'textdomain') ,
		'new_item_name' => __('New category', 'textdomain') ,
		'menu_name' => __('IC category', 'textdomain') ,
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'genre'
		) ,
	);

	register_taxonomy('ic-category', array(
		'ic-blog'
	) , $args);

	/* Register course page taxonomy */

	$labels = array(
		'name' => _x('Course types', 'taxonomy general name', 'textdomain') ,
		'singular_name' => _x('Course types', 'taxonomy singular name', 'textdomain') ,
		'search_items' => __('Course types', 'textdomain') ,
		'all_items' => __('All course types', 'textdomain') ,
		'parent_item' => __('Parent course types', 'textdomain') ,
		'parent_item_colon' => __('Parent course type:', 'textdomain') ,
		'edit_item' => __('Edit course type', 'textdomain') ,
		'update_item' => __('Update course type', 'textdomain') ,
		'add_new_item' => __('Add new course type', 'textdomain') ,
		'new_item_name' => __('New course type', 'textdomain') ,
		'menu_name' => __('Course type', 'textdomain') ,
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'course-type'
		) ,
	);

	register_taxonomy('course-type', array(
		'course-page'
	) , $args);
}

/****************************************/
/********* BBpress FUNCTIONS ************/
/****************************************/

function bbp_enable_visual_editor($args = array()) {
	$args['tinymce'] = true;
	return $args;
}
add_filter('bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor');

function bbp_tinymce_paste_plain_text($plugins = array()) {
	$plugins[] = 'paste';
	return $plugins;
}
add_filter('bbp_get_tiny_mce_plugins', 'bbp_tinymce_paste_plain_text');

add_filter('bbp_after_get_the_content_parse_args', 'bavotasan_bbpress_upload_media');
/**
 * Allow upload media in bbPress
 *
 * This function is attached to the 'bbp_after_get_the_content_parse_args' filter hook.
 */
function bavotasan_bbpress_upload_media($args) {
	$args['media_buttons'] = true;

	return $args;
}

/****************************************/
/*********** EDD FUNCTIONS **************/
/****************************************/

/**
 * Variable price output
 *
 * Outputs variable pricing options for each download or a specified downloads in a list.
 * The output generated can be overridden by the filters provided or by removing
 * the action and adding your own custom action.
 *
 * @since 1.2.3
 * @param int $download_id Download ID
 * @return void
 */
function ryit_purchase_variable_pricing($download_id = 0, $args = array()) {
	global $edd_displayed_form_ids;

	// If we've already generated a form ID for this download ID, append -#
	$form_id = '';
	if ($edd_displayed_form_ids[$download_id] > 1) {
		$form_id .= '-' . $edd_displayed_form_ids[$download_id];
	}

	$variable_pricing = edd_has_variable_prices($download_id);

	if (!$variable_pricing) {
		return;
	}

	$prices = apply_filters('edd_purchase_variable_prices', edd_get_variable_prices($download_id) , $download_id);

	// If the price_id passed is found in the variable prices, do not display all variable prices.
	if (false !== $args['price_id'] && isset($prices[$args['price_id']])) {
		return;
	}

	$type = edd_single_price_option_mode($download_id) ? 'checkbox' : 'radio';
	$mode = edd_single_price_option_mode($download_id) ? 'multi' : 'single';
	$schema = edd_add_schema_microdata() ? ' itemprop="offers" itemscope itemtype="http://schema.org/Offer"' : '';

	// Filter the class names for the edd_price_options div
	$css_classes_array = apply_filters('edd_price_options_classes', array(
		'edd_price_options',
		'edd_' . esc_attr($mode) . '_mode'
	) , $download_id);

	// Sanitize those class names and form them into a string
	$css_classes_string = implode(array_map('sanitize_html_class', $css_classes_array) , ' ');

	if (edd_item_in_cart($download_id) && !edd_single_price_option_mode($download_id)) {
		return;
	}

	// show longer payment plans on enroll page
	$var = $_GET['payment_plan'];
	if ($var && $var == 9) {
		$echo .= "<style type='text/css'>";
		$echo .= "#edd_price_option_657_9-monthplan { display: table-row !important; }";
		$echo .= "</style>";
	}
	else if ($var && $var == 12) {
		$echo .= "<style type='text/css'>";
		$echo .= "#edd_price_option_657_9-monthplan, #edd_price_option_657_12-monthplan { display: table-row !important; }";
		$echo .= "</style>";
	}
	else if ($var && $var == "custom") {
		$echo .= "<style type='text/css'>";
		$echo .= "#edd_price_option_657_josephcasansplan { display: table-row !important; }";
		$echo .= "</style>";
	}
	echo $echo;

	//RYIT download id = 657
	//Donation download it = 46649
	do_action('edd_before_price_options', $download_id); ?>
    <table class="<?php echo esc_attr(rtrim($css_classes_string)); ?>">
	   <thead>
	   <?php if ($download_id != 46649): ?>
		  <th>Plan type</th>
		  <th>Price & plan info</th>
	   <?php
	else: ?>
		  <th>Donation type</th>
		  <th>Donation size</th>
	   <?php
	endif; ?>
	   </thead>
	   <tbody>
		  <?php
	if ($download_id == 657) {
		$product_price = edd_get_variable_prices(657);
		$product_price = $product_price[1]['amount'];
	}
	elseif ($download_id == 55179) {
		$product_price = 329;
	}
	else {
		$product_price = 100000;
	}

	if ($prices):
		$checked_key = isset($_GET['price_option']) ? absint($_GET['price_option']) : edd_get_default_variable_price($download_id);

		foreach ($prices as $key => $price):

			if ($price['recurring'] == 'yes') { //item has recurring payments
				$price_desc = "";
				$price_total = $price['amount'] * $price['times'] + $price['signup_fee'];
				$price_msg = "$" . strval($price['amount']) . " x " . strval($price['times']);
				if ($price['signup_fee']) {
					$price_desc .= " + $" . $price['signup_fee'] . " extra first month<sup>*</sup>. ";
				}
				/*
				    else {
					   $price_desc .= " &ndash; ";
				    }*/
				if ($price_total > $product_price) {
					$price_desc .= " ($" . strval($price_total - $product_price) . " plan fees, $" . $price_total . " total)";
				}
			}
			else {
				$price_msg = "$" . $price['amount'];
				//$price_desc = " &ndash; One payment. No fees.";
				
			}

			echo '<tr id="edd_price_option_' . $download_id . '_' . sanitize_key($price['name']) . $form_id . '"' . $schema . '>';
			echo '<td class="plan">';
			echo '<label for="' . esc_attr('edd_price_option_' . $download_id . '_' . $key . $form_id) . '">';
			echo '<input type="' . $type . '" ' . checked(apply_filters('edd_price_option_checked', $checked_key, $download_id, $key) , $key, false) . ' name="edd_options[price_id][]" id="' . esc_attr('edd_price_option_' . $download_id . '_' . $key . $form_id) . '" class="' . esc_attr('edd_price_option_' . $download_id) . '" value="' . esc_attr($key) . '" data-price="' . edd_get_price_option_amount($download_id, $key) . '"/>&nbsp;';
			$item_prop = edd_add_schema_microdata() ? ' itemprop="description"' : '';
			echo '<span class="edd_price_option_name"' . $item_prop . '>' . esc_html($price['name']) . '</span>';
			echo '</td>';
			echo '<td class="desc">';
			echo '<span class="edd_price_option_price">' . $price_msg . '</span>';
			echo '<span class="edd_price_option_desc">' . $price_desc . '</span>';
			if (edd_add_schema_microdata()) {
				echo '<meta itemprop="price" content="' . esc_attr($price['amount']) . '" />';
				echo '<meta itemprop="priceCurrency" content="' . esc_attr(edd_get_currency()) . '" />';
			}
			echo '</label>';
			do_action('edd_after_price_option', $key, $price, $download_id);
			echo '</td>';
			echo '</tr>';
		endforeach;
	endif;
	do_action('edd_after_price_options_list', $download_id, $prices, $type);
?>
	   </tbody>
    </table><!--end .edd_price_options-->
    <?php if ($download_id == 657): ?>
    <p style="font-size: 13px; text-align: center; margin: 2em 0 1.5em 0;"> Payment plan fees incur due to the additional risk and administration work involved for us. <br/>If you have a discount code, it will be applied on the next page.<br/><br/><sup>*</sup> For long plans, we ask 30% up front.</p>
    <?php
	elseif ($download_id == 46649): ?>
    <p style="font-size: 13px;">All donations will be recorded manually and be visible for all site members to see. Being featured on website and newsletter is optional. If you don't want it, we'll not do it.</p>
    <?php
	endif; ?>
<?php
	do_action('edd_after_price_options', $download_id);
}

remove_action('edd_purchase_link_top', 'edd_purchase_variable_pricing', 10, 2);
add_action('edd_purchase_link_top', 'ryit_purchase_variable_pricing', 10, 2);

/**
 * Shows the final purchase total at the bottom of the checkout page
 *
 * @since 1.5
 * @return void
 */
function ryit_checkout_final_total() {
?>
<p id="edd_final_total_wrap">
    <strong><?php _e('Pay now:', 'easy-digital-downloads'); ?></strong>
    <span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></span>
</p>
<?php
}
remove_action('edd_purchase_form_before_submit', 'edd_checkout_final_total', 999);
add_action('edd_purchase_form_before_submit', 'ryit_checkout_final_total', 999);

/* Update remaining seats for Journey to the Great Self upon payment */

function ryit_edd_after_purchase($payment_id) {
	$cart_items = edd_get_payment_meta_cart_details($payment_id);
	foreach ($cart_items as $item) {
		if ($item['id'] == 55179) { //Journey to the Great Self
			$available_seats = get_field('ryit_greatself_seats', 'option');
			if ($available_seats > 0) {
				update_field('ryit_greatself_seats', --$available_seats, 'option');
			}
			break;
		}
		elseif ($item['id'] == 657) { //Reclaim your Inner Throne
			$user_id = get_current_user_id();
			$args = array(
				'subscription_id' => 5,
				'status' => 'free'
			);

			rcp_add_user_to_subscription($user_id, $args); //This adds user to the RYIT Basecamp access level
			//update available seats
			$available_seats = get_field('ryit_seats', 'option');
			if ($available_seats > 0) {
				update_field('ryit_seats', --$available_seats, 'option');
			}

			//Holiday discount updates
			$holiday_disount = 55715;
			$discount = edd_get_discount($holiday_disount);

			$amount = $discount->amount;
			if ($amount >= 200) {
				$amount -= 100;
			}

			$args = array(
				'name' => $discount->name,
				'amount' => $amount,
				'code' => $discount->code,
				'type' => $discount->type
			);

			edd_store_discount($args, $holiday_disount);
		}
		elseif ($item['id'] == 57423) { //RYIT deposit
			//update available seats
			$available_seats = get_field('ryit_seats', 'option');
			if ($available_seats > 0) {
				update_field('ryit_seats', --$available_seats, 'option');
			}
		}
	}
}
add_action('edd_complete_purchase', 'ryit_edd_after_purchase');

function ryit_greatself_seats() {
	return get_field('ryit_greatself_seats', 'option');
}

add_shortcode('ryit_greatself_seats', 'ryit_greatself_seats');

function ryit_seats() {
	return get_field('ryit_seats', 'option');
}

add_shortcode('ryit_seats', 'ryit_seats');

function fb_comments($atts) {
	extract(shortcode_atts(array(
		'href' => '',
		'numposts' => 20,
		'title' => 'Comments',
		'width' => '600'
	) , $atts));
	$echo = "<h2 style='text-align: center; margin-bottom: 30px;'>" . $title . "</h2><div style='text-align: center; max-width:" . $width . "px; margin: 0 auto;'><div class='fb-comments' data-href='" . $href . "' data-width='" . $width . "' data-numposts='" . $numposts . "' ></div></div>";
	return $echo;
}

add_shortcode('fb_comments', 'fb_comments');

function pre_avada_shortcodes() {
	$shortcode = get_field('pre_avada_shortcode');
	if ($shortcode) {
		echo do_shortcode($shortcode);
	}
}

add_filter('avada_before_body_content', 'pre_avada_shortcodes');

function ryit_start_time($args, $format) {
	$args = shortcode_atts(array(
		'day_offset' => 0,
		'format' => "F j Y"
	) , $args);

	$day_offset = $args['day_offset'];
	$format = $args['format'];

	$date = get_field('ryit_start_time', 'options', false, false);
	$date = new DateTime($date);

	if (!empty($day_offset)) {
		$offset = 86400 * $day_offset;
		return date($format, strtotime($date->format('Y-m-d')) + $offset);
	}
	else {
		return date($format, strtotime($date->format('Y-m-d')));
	}
}

add_shortcode('ryit_start_time', 'ryit_start_time');

function ryit_earlybird_end($args, $format) {
	$args = shortcode_atts(array(
		'day_offset' => 0,
		'format' => "F j Y"
	) , $args);

	$day_offset = $args['day_offset'];
	$format = $args['format'];

	$date = get_field('ryit_earlybird_end', 'options', false, false);
	$date = new DateTime($date);

	if (!empty($day_offset)) {
		$offset = 86400 * $day_offset;
		return date($format, strtotime($date->format('Y-m-d')) + $offset);
	}
	else {
		return date($format, strtotime($date->format('Y-m-d')));
	}
}

add_shortcode('ryit_earlybird_end', 'ryit_earlybird_end');

function ryit_next_upcoming_event() {
	if (is_front_page()):
		$events = tribe_get_events(array(
			'eventDisplay' => 'upcoming',
			'posts_per_page' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' => array(
						'community-call',
						'alumni-call',
						'webinar'
					)
				)
			)
		));

		if ($events):
			$event = $events[0];
			$event = get_object_vars($event);
			$thumbnail = get_the_post_thumbnail($event['ID'], 'thumbnail');
			$start_date = $event['EventStartDate'];
			$start_date = substr($start_date, 0, 10);

			$now = new DateTime(date("Y-m-d")); // or your date as well
			$start_date = new DateTime($start_date);

			$datediff = $start_date->diff($now)->format("%a");
			$when_echo = $datediff >= 1 ? "In " . $datediff . " days" : "Today";

			$echo = "<div id='upcoming-event'><a href='" . $event['guid'] . "'><div class='thumbnail'>" . $thumbnail . "</a></div><div class='event-content'><h4>Next event you can attend</h4><h3><a href='" . $event['guid'] . "'>" . $event['post_title'] . "</a></h3><p>" . $when_echo . "<span style='margin: 0 8px; color: #aaa;'>|</span><a href='/calendar'>Full calendar</a></p></div></a></div>";
			return $echo;
		endif;
	endif;
}

add_shortcode('list_upcoming_event', 'ryit_next_upcoming_event');

function ryit_video_training_html() {
	return get_field('ryit_video_training_html', 'options');
}

add_shortcode('ryit_video_training_html', 'ryit_video_training_html');

function add_upcoming_event() {
	if (is_front_page()):
		do_shortcode('[ryit_next_upcoming_event]');
?>
    <script type="text/javascript">
	   $j = jQuery.noConflict();

	   function toggleClass() {
		  $j('#upcoming-event').removeClass('show');
	   }

	   $j(document).ready(function() {
		  $j("#upcoming-event").appendTo('.flexslider.main-flex');
		  $j('#upcoming-event').addClass('show');
		  setTimeout(toggleClass, 6000);
	   });
    </script>
<?php
	endif;
}

add_filter('avada_before_body_content', 'add_upcoming_event');

function ryit_fb_apps() {
	global $post;
	$url = get_permalink($post->ID);
	$post_time = get_post_time('U');
	$switch_time = 1485043200; //Jan 22, 2017
	//we switched to https and trailing slash was added
	//Page IDs where Facebook has stored likes without a trailing slash
	$fb_pages_array = array(
		45526,
		45542
	);

	if ($post_time <= $switch_time) {
		$url = str_replace('https', 'http', $url);
		if (in_array($post->ID, $fb_pages_array)) {
			$like_url = rtrim($url, '/');
		}
		else {
			$like_url = $url;
		}
	}

	$fb_like = '<div class="fb-like" data-href="' . $like_url . '" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>';
	$fb_comments = '<div class="fb-comments" data-href="' . $url . '" data-width="600" data-numposts="5"></div>';
	return "<div id='fbApps'>" . $fb_like . $fb_comments . "</div>";
}

add_shortcode('ryit_fb_apps', 'ryit_fb_apps');

function ryit_scroll_alert() {
?>
    <script type="text/javascript">
	   $j = jQuery.noConflict();
	   $j(document).ready(function() {
		  if ($j('.flexslider.main-flex').length) {
			 //console.log('icon: yes');
			 $j('.flexslider.main-flex').prepend('<div class="ryit-arrow-down" aria-hidden="true"></div>');
		  }
	   });
    </script>
    
<?php
}

add_filter('avada_before_body_content', 'ryit_scroll_alert');

function ryit_show_ssl_header() {
?>
<script type="text/javascript"> //<![CDATA[ 
var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.comodo.com/" : "http://www.trustlogo.com/");
document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
</script>
<?php
}

function ryit_show_ssl_footer() {
?>
    <div id="comodo-trust">
    <script language="JavaScript" type="text/javascript">
    TrustLogo("https://www.inner-throne.com/wp-content/uploads/2017/03/comodo_secure_seal_113x59_transp.png", "CL1", "trust-logo");
    </script>
    <a href="https://ssl.comodo.com" id="comodoTL">Comodo SSL</a>
    </div>
<?php
}

function ryit_ssl_logo_check() {
	global $post;
	if (get_field('ryit_show_ssl_logo', $post->ID)) {
		add_action('wp_head', 'ryit_show_ssl_header');
		add_action('avada_after_main_content', 'ryit_show_ssl_footer');
	}
}

add_action('wp_enqueue_scripts', 'ryit_ssl_logo_check');

//flush_rewrite_rules( false ); - THIS DOES NOT SEEM NECESSARY
add_filter('avada_before_main_container', 'ryit_lesson_slider');

function ryit_lesson_slider() {
	global $post;
	$post_id = $post->ID;
	if (get_field('ryit_activate_slider')):
		$ryit = (is_page(45618) || wp_get_post_parent_id($post_id) == 45618) ? " is_ryit" : "";
?>
    <div id='sliders-container' class="fullscreen<?php echo $ryit; ?>">

	   <div class='fusion-slider-container fusion-slider-52277 -container' style='height: 762px; max-width: 100%; max-height: 762px;'>
	   <style type='text/css' scoped='scoped'>
		  .fusion-slider-52277 .flex-direction-nav a {
			 width:63px;height:63px;line-height:63px;font-size:25px; 
		  }
	   </style>

	   <?php
		$title = get_field('ryit_slider_title');
		$top_text = get_field('ryit_slider_top');
		$caption = do_shortcode(get_field('ryit_slider_caption'));
		$align = get_field('ryit_slider_align');
		if (!$align) $align = "center";
?>

	   <div class='tfs-slider flexslider main-flex' style='max-width: 100%; height: 762px;' data-slider_width='100%' data-slider_height='400px' data-slider_content_width='' data-full_screen='1' data-parallax='0' data-nav_arrows='1' data-nav_box_width='63px' data-nav_box_height='63px' data-nav_arrow_size='25px' data-pagination_circles='0' data-autoplay='1' data-loop='0' data-animation='fade' data-slideshow_speed='7000' data-animation_speed='600' data-typo_sensitivity='1' data-typo_factor='1.5'>
		<?php
		if (get_field('ryit_bottom_text')):
			echo "<div class='bottom-text'>" . get_field('ryit_bottom_text') . "</div>";
		endif;
?>
		  <ul class='slides' style='max-width: 100%; width: 100%;'>
			 <li data-mute='yes' data-loop='yes' data-autoplay='yes' class='flex-active-slide' style='width: 100%; float: left; margin-right: -100%; position: relative; opacity: 1; display: block; z-index: 2;'>
			 <div class='slide-content-container slide-content-<?php echo $align; ?>' style=''>
				<div class='slide-content' style='opacity: 1; margin-top: 0px;'>
				    <?php if ($top_text): ?>
				    <div class='chapter-header with-bg'>
					   <div class='fusion-title-sc-wrapper' style='background-color: rgba(0,0,0, 0.4);'>
						  <div class='fusion-title title fusion-sep-none fusion-title-center fusion-title-size-two fusion-border-below-title' style='margin-top:0px;margin-bottom:0px;'>
							 <h4 class='title-heading-center' style='color: rgb(255, 255, 255); font-size: 59.7px; line-height: 79.6px;' data-inline-fontsize='true' data-inline-lineheight='true' data-fontsize='60' data-lineheight='80'><?php echo $top_text; ?></h4>
						  </div>                                  
					   </div>
				    </div>
				    <?php
		endif; ?>
				    <?php if ($title): ?>
				    <div class='heading with-bg'>
					   <div class='fusion-title-sc-wrapper' style='background-color: rgba(0,0,0, 0.4);'>
						  <div class='fusion-title title fusion-sep-none fusion-title-center fusion-title-size-two fusion-border-below-title' style='margin-top:0px;margin-bottom:0px;'>
							 <h2 class='title-heading-center' style='color: rgb(255, 255, 255); font-size: 59.7px; line-height: 79.6px;' data-inline-fontsize='true' data-inline-lineheight='true' data-fontsize='60' data-lineheight='80'><?php echo $title; ?></h2>
						  </div>                                  
					   </div>
				    </div>
				    <?php
		endif; ?>
				    <?php if ($caption): ?>
				    <div class='caption with-bg'>
					   <div class='fusion-title-sc-wrapper' style='background-color: rgba(0, 0, 0, 0.4);'>
						  <div class='fusion-title title fusion-sep-none fusion-title-<?php echo $align; ?> fusion-title-size-three fusion-border-below-title' style='margin-top:0px;margin-bottom:0px;'>
							 <h3 class='title-heading-center' style='color: rgb(255, 255, 255); font-size: 20px; line-height: 32px;' data-inline-fontsize='true' data-inline-lineheight='true' data-fontsize='20' data-lineheight='32'><?php echo $caption; ?></h2></h3>
						  </div>                                  
					   </div>
				    </div>
				    <?php
		endif; ?>
				    <div class='buttons'>
				    </div>
				</div>
			 </div>
			 <?php if (is_jgs_page()): ?>
				<div class='background background-image bg-overlay' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>); max-width: 100%; height: 762px; width: 100%;' data-imgwidth='1920'></div>
			 <?php
		endif; ?>
			 <div class='background background-image' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>); max-width: 100%; height: 762px; width: 100%;' data-imgwidth='1920'></div>
			 </li>
		  </ul>
		  <ul class='flex-direction-nav'><li><a class='flex-prev flex-disabled' href='#' tabindex='-1'></a></li><li><a class='flex-next flex-disabled' href='#' tabindex='-1'></a></li></ul></div>
	   </div>
    </div>
<?php
	endif;
}

/* Shared functions */

function ryit_get_user_name($atts) {
	$atts = shortcode_atts(array(
		'fullname' => false
	) , $atts, 'ryit_get_user_name');

	$user_id = get_current_user_id();
	$fullname = $atts['fullname'];

	global $current_user;
	get_currentuserinfo();

	if ($fullname == false) {
		$name = $current_user->user_firstname;
		//in case mother's maiden name is stored in first name, remove it
		$name = explode(" ", $name);
		$first_name = $name[0];
		return $first_name;
	}
	else {
		$name = $current_user->user_firstname . " " . $current_user->user_lastname;
		return $name;
	}
}

add_shortcode('ryit_name', 'ryit_get_user_name');


function print_r_html($input) {
	if(empty($input)) return;
	echo '<pre>';
	echo print_r($input);
	echo '</pre>';
}

function ryit_comment() {
	ob_start();
	comments_template('/comments-view.php');
	$comments = ob_get_clean();
	return $comments;
}

add_shortcode('ryit_comment', 'ryit_comment');

function ryit_list_available_weeks() {
	// Set up the objects needed
	$my_wp_query = new WP_Query();
	$all_wp_pages = $my_wp_query->query(array(
		'post_type' => 'page',
		'posts_per_page' => '-1'
	));

	// Get the page as an Object
	$ryit_parent_page = get_post(45618);

	// Filter through all pages and find Portfolio's children
	$ryit_ancestor_pages = get_page_children($ryit_parent_page->ID, $all_wp_pages);
	//var_dump(expression);
	$ryit_ancestor_pages = array_reverse($ryit_ancestor_pages); //show oldest pages first
	ob_start();
	foreach ($ryit_ancestor_pages as $page) {
		$image_url = wp_get_attachment_url(get_post_thumbnail_id($page->ID));
		if (rcp_user_can_access(get_current_user_id() , $page->ID)):
?>
	   <div class="fusion-builder-row fusion-builder-row-inner fusion-row">
		  <div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_4  fusion-one-fourth fusion-column-first week-1 1_4" style="margin-top: 0px;margin-bottom: 20px;width:25%;width:calc(25% - ( ( 4% ) * 0.25 ) );margin-right:4%;">
			 <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
				<span style="-moz-box-shadow: 2px 3px 7px rgba(0,186,0,.3);-webkit-box-shadow: 2px 3px 7px rgba(0,186,0,.3);box-shadow: 2px 3px 7px rgba(0,186,0,.3);" class="fusion-imageframe imageframe-dropshadow imageframe-1 hover-type-zoomin"><a class="fusion-no-lightbox" href="<?php echo get_permalink($page->ID); ?>" target="_self" aria-label="call-to-adventure-fullhd"><img src="<?php echo $image_url; ?>" width="600" height="338" alt="" class="img-responsive"></a></span>
			 </div>
		  </div>
		  <div class="fusion-layout-column fusion_builder_column fusion_builder_column_3_4  fusion-three-fourth fusion-column-last week-1 3_4" style="margin-top: 0px;margin-bottom: 20px;width:75%;width:calc(75% - ( ( 4% ) * 0.75 ) );">
			 <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
				<div class="fusion-title title fusion-sep-none fusion-title-size-three fusion-border-below-title">
				    <h3 class="title-heading-left"><?php echo $page->post_title; ?></h3>
				</div>
				<div class="fusion-text">
				    <?php echo get_field('ryit_week_description', $page->ID); ?><p class="readmore"><a href="<?php echo get_permalink($page->ID); ?>" data-hover="Read more&nbsp;»"><span>Read more&nbsp;»</span></a></p>
				</div>
			 </div>
		  </div>
	   </div>
    <?php
		endif;
	}
	return "<div id='course-weeks'>" . ob_get_clean() . "</div>";
}

add_shortcode('ryit_week_list', 'ryit_list_available_weeks');

function ryit_list_courses() {

	/*$ryit_id = 45618;
	   $jgs_id = 52269;
	   $alumnus_id = 51901;*/

	$subscription_id = rcp_get_subscription_id(get_current_user_id());

	$echo = "";
	if ($subscription_id == 2 || $subscription_id == 3) { //rcp_user_can_access(get_current_user_id(), $ryit_id) || rcp_user_can_access(get_current_user_id(), $alumnus_id)) { //RYIT
		$echo .= '<div class="course">';
		$echo .= '<a href="/courses/ryit">';
		$echo .= '<img src="https://www.inner-throne.com/wp-content/uploads/2017/12/ryit-box-325x375.jpg" />';
		$echo .= '<h2>Reclaim your Inner Throne</h2>';
		$echo .= '</a>';
		$echo .= '</div>';
	}
	if ($subscription_id == 4) { //rcp_user_can_access(get_current_user_id(), $jgs_id)) { //JGS
		$echo .= '<div class="course">';
		$echo .= '<a href="/courses/jgs">';
		$echo .= '<img src="https://www.inner-throne.com/wp-content/uploads/2017/12/greatself-box-325x375.jpg" />';
		$echo .= '<h2>Journey to the Great Self</h2>';
		$echo .= '</a>';
		$echo .= '</div>';
	}

	if (!empty($echo)) {
		$echo = '<div class="course-listing">' . $echo . '</div>';
	}
	else {
		$echo = "<div class='maxwidth-600' style='text-align: center;'><h2>You haven't purchased any trainings yet</h2><p><a href='/services'>Check them out</a></p></div>";
	}
	return $echo;
}

add_shortcode('ryit_list_courses', 'ryit_list_courses');

function ryit_next_workshop() {
	$events = tribe_get_events(array(
		'eventDisplay' => 'upcoming',
		'posts_per_page' => - 1,
		'tax_query' => array(
			array(
				'taxonomy' => 'tribe_events_cat',
				'field' => 'slug',
				'terms' => 'workshop'
			)
		)
	));

	$echo = "";

	foreach ($events as $event) {
		$echo .= '<h2>Next workshop</h2>';
		$echo .= '<h3>' . $event->post_title . '</h3>';
		$echo .= '<p class="date">' . $event->EventStartDate . ' &ndash; ' . $event->EventEndDate . '</p>';
		$echo .= '<p><a href="' . $event->guid . '">Read more</a></p>';
	}

	if (empty($echo)) $echo = "<h2>Your city next?</h2><h3>We're currently planning our next workshop. Stay tuned.</h3>";
	return '<div id="upcoming-workshop">' . $echo . '</div>';

}

add_shortcode('ryit_next_workshop', 'ryit_next_workshop');


/*******************************************/
/**** GENERAL ACF FIELD UPDATE FUNCTION ****/
/*******************************************/

function ryit_change_acf_field_value() {
	if (isset($_GET['field_key'])) {
		$field_key = $_GET['field_key'];
	}
	else {
		die();
	}

	if (isset($_GET['new_value'])) {
		$new_value = $_GET['new_value'];
	}
	else {
		die();
	}

	if (isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}
	else {
		die();
	}

	update_field($field_key, $new_value, $post_id);
	wp_send_json_success(true);
	die();
}

add_action('wp_ajax_ryit_change_acf_field_value', 'ryit_change_acf_field_value');
add_action('wp_ajax_nopriv_ryit_change_acf_field_value', 'ryit_change_acf_field_value');



/************ PROMOTIONAL CAMPAIGNS ************/


function xmas_offer() {
	$holiday_disount = 55715;
	$discount = edd_get_discount($holiday_disount);
	$amount = $discount->amount;

	$echo = '<div id="discount_offer" class="maxwidth-600 campaign"><img src="' . get_stylesheet_directory_uri() . '/images/xmas-presents.png" /><h3>Ho-ho-holiday Offer</h3><p> (valid until Dec 25)</p><p>Use the discount code HOLIDAY_DISCOUNT when checking out (remember it well!) to <strong>get $' . $amount . ' off</strong> the already discounted early bird price.</p><p>Discount drops by $100 for each new registration, to a minimum of $100. Act now for the best possible deal (highest discount is $500)</p><p><a href="/enroll">Yes, I want to register NOW!</a></p><p><em>After paying, we will have to have a screening call with you. Normally we do these before paying, but to enable you to take advantage of the offer right now, we have changed our routines. If after the call, we conclude this training is not a fit for you, we\'ll refund you in full!</em></p></div>';

	return $echo;
}

add_shortcode('xmas_offer', 'xmas_offer');




/************ DETERMINE USER TYPE ************/

function ryit_user_is_alumnus($user_id = NULL) {
	if (empty($user_id)) {
		$user_id = get_current_user_id();
	}
	$subscription_id = rcp_get_subscription_id($user_id);

	if ($subscription_id == 2) {
		return true; //user counted as on current round. includes leadership team
		
	}
	else {
		return false; //user considered an alumnus
	}
}

function ryit_user_is_fellowship($user_id = NULL) {
	if (empty($user_id)) {
		$user_id = get_current_user_id();
	}
	$subscription_id = rcp_get_subscription_id($user_id);

	if ($subscription_id == 1) {
		return true; //user counted as on current round. includes leadership team
	}
	else {
		return false; //user considered an alumnus
	}
}

function ryit_user_is_current($user_id = NULL) {
	if (empty($user_id)) {
		$user_id = get_current_user_id();
	}
	$subscription_id = rcp_get_subscription_id($user_id);

	if ($subscription_id == 3) {
		return true; //user counted as on current round. includes leadership team
	}
	else {
		return false; //user considered an alumnus
	}
}



//Add RYIT image sizes
add_image_size('RYIT Stretch Goal',700,300,array( 'center', 'top' ));

//Remove Avada image sizes
add_action( 'after_setup_theme', 'my_child_theme_image_size', 11 );
function my_child_theme_image_size() {
	remove_image_size('medium_large');
	remove_image_size('portfolio-full');
	remove_image_size('portfolio-one');
	remove_image_size('portfolio-two');
	remove_image_size('portfolio-three');
	remove_image_size('portfolio-four');
	remove_image_size('portfolio-five');
	remove_image_size('blog-large');
	remove_image_size('blog-medium');
	remove_image_size('recent-posts');
	remove_image_size('200');
	remove_image_size('400');
	remove_image_size('600');
	remove_image_size('800');
	remove_image_size('1200');
	remove_image_size('recent-works-thumbnail');
	remove_image_size('rpwe-thumbnail');
}

//Remove Wordpress default image sizes
add_filter( 'intermediate_image_sizes_advanced', 'prefix_remove_default_images' );
// Remove default image sizes here. 
function prefix_remove_default_images( $sizes ) {
	unset( $sizes['small']); // 150px
	unset( $sizes['medium']); // 300px
	unset( $sizes['large']); // 1024px
	unset( $sizes['medium_large']); // 768px
	return $sizes;
}


/******** GET FELLOWSHIP & JGS RESOURCES ***********/

function is_fellowship_page($page_id=false) {
	if(empty($page_id)) {
		global $post;
		$page_id = $post->ID;
	}
	//echo "page " . $page_id;
	//if fellowship.css is added to page, identify it as a fellowship page
	$add_css_files = get_field('ryit_add_css_files', $page_id);
	if (!empty($add_css_files)) {
		foreach ($add_css_files as $file) {
			if (strpos($file['css_file'], 'fellowship.css')) {
				return true;
			}
		}
	}
	return false;
}


function is_jgs_page() {
	global $post;
	if (!is_object($post)) return;
	$parent = wp_get_post_parent_id($post->ID);
	if ($parent == 52269) {
		return true;
	}
	else {
		return false;function is_jgs_page() {
	global $post;
	if (!is_object($post)) return;
	$parent = wp_get_post_parent_id($post->ID);
	if ($parent == 52269) {
		return true;
	}
	else {
		return false;
	}
}
	}
}
/*
function ryit_get_resources() {
	global $post;
	$post_id = get_the_ID();
	//echo "called func";
	if(is_fellowship_page()) {
		require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/Avada-Child-Theme/functions-fellowship.php' );
	}
	else if(is_jgs_page()) {
		require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/Avada-Child-Theme/functions-jgs.php' );
	}
}

add_action( 'wp_head', 'ryit_get_resources');
*/



function preload_spinner() {
?>
  <div class="lds-css ng-scope">
  <div class="lds-spinner" style="width: 100%; height:100%"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
<?php
}



//Add Frontend form code
/*
function ryit_frontend_form() {
	global $post;
	if (!is_object($post)) return;
	if (get_field('ryit_acf_form_head', $post->ID)) {
		acf_form_head();
	}
}

add_action('init', 'ryit_frontend_form');
*/

function ryit_text_comment($atts) {
	$user_id = get_current_user_id();
	$args = shortcode_atts(array(
		'content' => '',
		'label' => 'Tip'
	) , $atts);

	return "<span class='tip-wrap'><span class='tip-bg'></span><span class='tip'><span class='tip-icon'>" . $args['label'] . "</span></span><span class='content'>" . $args['content'] . "</span></span>";
}

add_shortcode('ryit_text_comment', 'ryit_text_comment');

function ryit_get_reward_balance() {
	$user_id = get_current_user_id();
	$curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
	return $curr_balance;
}

add_shortcode('jgs_draco_count', 'ryit_get_reward_balance');


/********** ZAPIER INTEGRATION ***********/

function ryit_zapier_user_update($user_id) {

	$zap_type = $_REQUEST['zap_type'];

	switch($zap_type) {
		case 'fellowship_silver' :
			$args = array(
			    'subscription_id'    => 6,
			    'status'             => 'active',
			    'expiration'         => date( 'Y-m-d 23:59:59', strtotime( '+1 year', current_time( 'timestamp' ) ) )
			);

			rcp_add_user_to_subscription( $user_id, $args );
			break;
		case 'ryit' :
			$args = array(
			    'subscription_id'    => 5,
			    'status'             => 'active',
			    'expiration'         => date( 'Y-m-d 23:59:59', strtotime( '+1 year', current_time( 'timestamp' ) ) )
			);
			$seats = get_field('ryit_seats','option');
			if($seats > 1) {
				$seats--;
			}
			update_field('ryit_seats', $seats, 'option');
			rcp_add_user_to_subscription( $user_id, $args );
			break;
		default :
			//update_user_meta(1,'ryit_debug', 'WP Zapier - No account');
			break;
	}

	//echo get_user_meta(1,'ryit_debug',true);
}

add_action('wp_zapier_after_create_user','ryit_zapier_user_update',10,1);
add_action('wp_zapier_after_update_user','ryit_zapier_user_update',10,1);


//add_action('init','ryit_zapier_user_update',10,1);


function zapier_debug() {
	if( current_user_can('administrator')) {
		echo get_user_meta(1,'ryit_debug',true);
		delete_user_meta(1, 'ryit_debug');
	}
}

add_action('init','zapier_debug');


/* BBpress */
/*

function ryit_forum_back_link() {
?>
    <div class="nav_menu">Return to forum: <a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a></div>
    <?php
}

add_action('bbp_template_before_replies_loop', 'ryit_forum_back_link');

add_filter('bbp_get_do_not_reply_address', 'scap_bbp_no_reply_email');

function scap_bbp_no_reply_email() {
	$admin_email = get_option('admin_email');
	return $admin_email;
}

*/



/************** E-MAIL functions ****************/

function wpse27856_set_content_type() {
	return "text/html";
}
add_filter('wp_mail_content_type', 'wpse27856_set_content_type');

/**
 * Extend get terms with post type parameter.
 *
 * @global $wpdb
 * @param string $clauses
 * @param string $taxonomy
 * @param array $args
 * @return string
 */
function df_terms_clauses($clauses, $taxonomy, $args) {
	if (isset($args['post_type']) && !empty($args['post_type']) && $args['fields'] !== 'count') {
		global $wpdb;

		$post_types = array();

		if (is_array($args['post_type'])) {
			foreach ($args['post_type'] as $cpt) {
				$post_types[] = "'" . $cpt . "'";
			}
		}
		else {
			$post_types[] = "'" . $args['post_type'] . "'";
		}

		if (!empty($post_types)) {
			$clauses['fields'] = 'DISTINCT ' . str_replace('tt.*', 'tt.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent', $clauses['fields']) . ', COUNT(p.post_type) AS count';
			$clauses['join'] .= ' LEFT JOIN ' . $wpdb->term_relationships . ' AS r ON r.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN ' . $wpdb->posts . ' AS p ON p.ID = r.object_id';
			$clauses['where'] .= ' AND (p.post_type IN (' . implode(',', $post_types) . ') OR p.post_type IS NULL)';
			$clauses['orderby'] = 'GROUP BY t.term_id ' . $clauses['orderby'];
		}
	}
	return $clauses;
}

add_filter('terms_clauses', 'df_terms_clauses', 10, 3);

?>