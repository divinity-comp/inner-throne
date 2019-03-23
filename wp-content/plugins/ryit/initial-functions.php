<?php 
function create_stretch_goal_posttype() {
	$labels = array(
		'name' => __('Stretch Goal') ,
		'singular_name' => __('Stretch Goal') ,
		'menu_name' => __('Stretch Goal') ,
		'parent_item_colon' => __('Parent -Stretch Goal') ,
		'all_items' => __('All Stretch Goals') ,
		'view_item' => __('View Stretch Goal') ,
		'add_new_item' => __('Add new Stretch Goal') ,
		'add_new' => __('Add new Stretch Goal') ,
		'edit_item' => __('Edit Stretch Goal') ,
		'update_item' => __('Edit Stretch Goal') ,
		'search_items' => __('Search Stretch Goals') ,
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
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'taxonomies' => array(
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
			'slug' => 'stretch-goal'
		) ,
		'can_export' => true,
		'publicly_queryable' => true,
	);

	// Registering your Custom Post Type
	register_post_type('stretch-goal', $args);
}

add_action('init', 'create_stretch_goal_posttype');


function create_fellowship_call_posttype() {
	flush_rewrite_rules();
	$labels = array(
		'name' => __('Fellowship Call') ,
		'singular_name' => __('Fellowship Call') ,
		'menu_name' => __('Fellowship Call') ,
		'parent_item_colon' => __('Fellowship Call') ,
		'all_items' => __('All Fellowship Calls') ,
		'view_item' => __('View Fellowship Calls') ,
		'add_new_item' => __('Add new Fellowship Calls') ,
		'add_new' => __('Add new Fellowship Call') ,
		'edit_item' => __('Edit Fellowship Call') ,
		'update_item' => __('Edit Fellowship Call') ,
		'search_items' => __('Search Fellowship Calls') ,
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
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'taxonomies' => array(
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
			'slug' => 'membership-calls'
		) ,
		'can_export' => true,
		'publicly_queryable' => true,
	);

	// Registering your Custom Post Type
	register_post_type('membership-call', $args);
}

add_action('init', 'create_fellowship_call_posttype');


function ryit_user_get_fellowship_level($user_id) {
	if(empty($user_id)) {
		$user_id = ryit_get_user_ID();
	}

	$fellowship_level = get_field('ryit_user_fellowship_level', 'user_' . $user_id);
	$subscription_id = rcp_get_subscription_id($user_id);

	if(!empty($fellowship_level) && $fellowship_level != 'none') {
		return $fellowship_level;
	}
	else {
		if(user_can($user_id,'edit_pages')) { //leadership team
			return 'gold';
		}
		if($subscription_id == 6) {
			return 'silver';
		}
		if(ryit_user_is_alumnus($user_id)) {
			return 'bronze';
		}
		if($subscription_id==1) {
			return 'bronze';
		}
		if(ryit_user_is_current($user_id)) {
			return 'initiate';
		}
	}
}


function ryit_get_user_ID() {
	if(!empty($_GET['user_id'])) {
		$user_id = $_GET['user_id'];
	}

	if(empty($user_id)) {
		$user_id = get_current_user_id();
	}

	return $user_id;
}

//Return view mode if no paramater defined. When checking for view_mode, returns boolean
function ryit_get_user_view_mode($user_id=false,$view_mode=false) {
	if(!$user_id) {
		$user_id = ryit_get_user_ID();
	}

	if(!$view_mode) {
		$view_mode = empty($_GET['view-mode']) ? get_user_meta($user_id,'ryit_user_profile_viewmode',true) : $_GET['view-mode'];
		return $view_mode;
	}
	else {
		$user_view_mode = get_user_meta($user_id,'ryit_user_profile_viewmode',true);		
		if($user_view_mode == $view_mode) {
			return true;
		}
		else {
			return false;
		}
	}
}

	
function ryit_update_user_login_count($login) {
	//get_current_user_id() doesn't work in wp_login action
	$user = get_user_by('login',$login);
   $user_id = $user->ID;

	
	if(ryit_user_is_current($user_id) && !user_can($user_id, 'edit_pages')) {
		$login_count = get_user_meta($user_id, 'ryit_user_initiation_login_count', true);
		$new_login_count = !isset($login_count) ? 0 : $login_count+1;
		update_user_meta($user_id, 'ryit_user_initiation_login_count', $new_login_count);
	}
	else {
		$login_count = get_user_meta($user_id, 'ryit_user_fellowship_login_count', true);
		$new_login_count = !isset($login_count) ? 0 : $login_count+1;
		update_user_meta($user_id, 'ryit_user_fellowship_login_count', $new_login_count);
	}
}

add_action('wp_login', 'ryit_update_user_login_count',99);


function ryit_login_popup() {
	$welcome_video_played = get_user_meta(get_current_user_id(), 'ryit_user_welcome_video_played', true);
	if(is_fellowship_page() && is_user_logged_in() && empty($welcome_video_played)) {
		$login_count = get_user_meta(get_current_user_id(), 'ryit_user_fellowship_login_count', true);
		if(empty($login_count) || $login_count <= 1) {
			ob_start();
		?>
		<script src="https://player.vimeo.com/api/player.js"></script>
		<script type="text/javascript">
			jQuery('document').ready(function($j) {
				var iframe = $j('#login-popup iframe');
				var player = new Vimeo.Player(iframe);

				player.on('ended', function() {
					hide_popup();
				});

				$j('#login-popup .button').on('click', function() {
					$j('#login-popup p.info').fadeOut(1000);
					hide_popup();
				});

				setTimeout(function() { $j('#login-popup p.info').animate({ opacity: 0},1000); }, 8000);

				function hide_popup() {
					$j('#login-popup .video, #login-popup .button').animate({
						opacity: 0,
						maxHeight: 0
					}, 500, function() {
						$j('#login-popup .video, #login-popup .button').remove();
						$j('#login-popup .text.hide').removeClass('hide');
						$j('#setup-guide').removeClass('inactive');
						$j('#setup-guide').addClass('animate');
						setTimeout(
							function() { 
								$j('#login-popup').animate({
									opacity: 0
								}, 1000, function() {
									$j('#login-popup').remove();
									$j('#setup-guide').removeClass('animate');
									$j('#setup-guide').addClass('minimized');
								});
							}, 6000);
						});
				}
			});
		</script>
		<?php
			echo '<div id="login-popup">';
			echo '<div class="innerwrap">';
			echo '<p class="info"><i class="fa fa-volume-up"></i> Please enable sound in the Video player <img src="' . get_stylesheet_directory_uri() . '/images/vimeo-volume.png" /></p>';
			echo '<div style="padding:56.25% 0 0 0;position:relative;" class="video"><iframe src="https://player.vimeo.com/video/313825945?autoplay=1&title=0&byline=0&portrait=0&muted=1" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allow="autoplay" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
			echo '<p class="button button_simple">Skip ahead</p>';
			echo '<h3 class="text hide">Initiating Fellowship Portal</h3>';
			echo '</div>';
			echo '</div>';
		}
	}
	update_user_meta(get_current_user_id(), 'ryit_user_welcome_video_played', true);
}

//add_action('wp_head','ryit_login_popup',100);



function ryit_beta_feedback() {
	if(is_fellowship_page()) {
		$echo = "";
		$echo .= ryit_get_fp_javascript('user-feedback');
		$echo .= '<div id="ryit-feedback" class="minimized">';
		$echo .= '<i class="far fa-comments"></i>';
		$echo .= '<div class="text">';
		$echo .= '<p>This system is in beta. If you have problems, please help us fix them and improve your experience by sending feedback using <a href="/helpdesk" target="_blank">the helpdesk</a>. Thank you!</p>';
		$echo .= '</div></div>';
		echo $echo;
	}
}

add_action('avada_before_body_content', 'ryit_beta_feedback',101);



function ryit_add_fellowship_styles() {
	global $post;
	if(is_single() || is_archive()) {
		$post_type = get_post_type($post->ID);
		if($post_type == 'membership-call') {
			echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/css/fellowship.css" />';
		}
	}
}

add_action('wp_head', 'ryit_add_fellowship_styles', 101);
