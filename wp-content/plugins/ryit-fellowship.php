<?php

/*
Plugin Name: [RYIT] Fellowship Code
Description: Adds the Fellowship features to the RYIT website
Version:     0.1
Author:      Eivind Figenschau Skjellum
*/

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


/************************** THE FELLOWSHIP MEMBER DIRECTORY **************************/

add_action('wp_ajax_member_directory', 'ryit_member_directory');

function ryit_member_directory() {
	$user_id = ryit_get_user_ID();
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		$is_ajax = false;
		$display_type = get_user_meta($user_id, "alumnus_directory_display_type", true);
		$sort_type = get_user_meta($user_id, "alumnus_directory_sort_type", true);
		$filter_type = get_user_meta($user_id, "alumnus_directory_filter_type", true);
		if (!isset($display_type)) $display_type = 0;
		if (!isset($sort_type)) $sort_type = 0;
		if (!isset($filter_type)) $filter_type = 0;
	}
	else {
		$is_ajax = true;

		if (isset($_GET['display_type'])) {
			$display_type = $_GET['display_type'];
			update_user_meta($user_id, "alumnus_directory_display_type", $display_type);
		}
		if (isset($_GET['sort_type'])) {
			$sort_type = $_GET['sort_type'];
			update_user_meta($user_id, "alumnus_directory_sort_type", $sort_type);
		}
		if (isset($_GET['filter_type'])) {
			$filter_type = $_GET['filter_type'];
			update_user_meta($user_id, "alumnus_directory_filter_type", $filter_type);
		}
	}

	$header_echo = "";

	if (ryit_user_is_current() && !user_can($user_id,'edit_pages')) {
		$header_echo .= '<h1 class="title-heading-center"><p style="text-align:center;">The Fellowship</p></h1>';
		$header_echo .= '<div class="fusion-text maxwidth-600" style="margin-bottom: 40px;"><p style="text-align: center;">The men traveling with you through the Realm of Forgotten Kings.</p>
		</div>';
	}
	else {
		$header_echo .= '<h1 class="title-heading-center"><p style="text-align:center;">The Fellowship</p></h1>';
		$header_echo .= '<div class="fusion-text maxwidth-600" style="margin-bottom: 40px;"><p style="text-align: center;">The full Member Directory.<br/>Display of other member\'s profiles still incomplete.</p></div>';
	}

	//Gather members
	if ($filter_type == 0) { //all members
		//echo "fellowship";
		$fellowship = rcp_get_members('active', 1, 0, 999999, 'ASC');
		$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
		$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
		$members = array_merge($fellowship, $alumni, $current);
	}
	else if ($filter_type == 2) { //RYIT current & alumni
		//echo "not fellowship";
		$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
		$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
		$members = array_merge($alumni, $current);
	}
	else if ($filter_type == 12) { //In my country
		$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
		$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
		$members = array_merge($alumni, $current);
		$my_country = sanitize_title(get_field('ryit_user_profile_country','user_' . $user_id));
		foreach ($members as $key=>$member) {
			$country = get_field('ryit_user_profile_country','user_' . $member->ID);
			if(sanitize_title($country) != $my_country || empty($country)) {
				unset($members[$key]);
			}
		}	
	}
	else if ($filter_type == 13) { //In my city
		$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
		$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
		$members = array_merge($alumni, $current);
		$my_city = sanitize_title(get_field('ryit_user_profile_city','user_' . $user_id));
		foreach ($members as $key=>$member) {
			$city = get_field('ryit_user_profile_city','user_' . $member->ID);
			if(sanitize_title($city) != $my_city || empty($city)) {
				unset($members[$key]);
			}
		}	
	}
	else if ($filter_type == 14) { //Stretch goal
		$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
		$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
		$members = array_merge($alumni, $current);
		foreach ($members as $key=>$member) {
			if(!ryit_user_stretch_goal_is_initiated($member->ID)) {
				unset($members[$key]);
			}
		}		
	}
	/*
	else if ($filter_type == 3) { //RYIT Leaders
		//echo "not fellowship";
		$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
		$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
		$members = array_merge($alumni, $current);
	}
	*/

	//Ignore members (developer account etc)
	$ignore_members = array(
		84,
		252
	);

	if ($sort_type == 0) { //Sort by name
		foreach ($members as $member) {
			if (in_array($member->ID, $ignore_members)) continue;
			$member_data = get_userdata($member->ID);
			$key = ucfirst($member_data->first_name) . " " . ucfirst($member_data->last_name);
			$alumni_names[$key]['first_name'] = ucfirst($member_data->first_name);
			$alumni_names[$key]['last_name'] = ucfirst($member_data->last_name);
			$alumni_names[$key]['id'] = $member_data->ID;
		}
		ksort($alumni_names);
	}
	else if ($sort_type == 1) { //Sort by RYIT round
		$rounds = array();
		$round_number_last = 0;
		foreach ($members as $member) {
			if (in_array($member->ID, $ignore_members)) continue;
			$round_number = get_field('ryit_round_number', 'user_' . $member->ID);
			$comma_index = strpos($round_number, ','); //If user took part in several rounds then...
			if (!empty($comma_index)) {
				$round_number = substr($round_number, 0, strpos($round_number, ',')); //....list them as participating in only the first
				
			}
			if (isset($round_number)) {
				$rounds[$round_number][] = $member->ID;
			}
		}
		ksort($rounds);
	}
	else if ($sort_type == 2) {

	}

	//set up AJAX javascript
	ob_start();
	if (!$is_ajax):

	//prepare popup that shows when user changaes view settings
	//ryit_ui_feedback_popup('<img src="' . get_stylesheet_directory_uri() . '/images/spinner.gif" class="loader" /><p>Refreshing View ...</p>', true);

	ryit_get_fp_javascript('member-directory');
	endif;

	$form_js = ob_get_clean();

	/**************** CREATE SEARCH AND FILTER FORM *****************/

	if (!ryit_user_is_current() || (ryit_user_is_current() && user_can($user_id,'edit_pages'))) {
		$display_types = array();
		$display_types[] = array(
			'display_name',
			'Name only'
		);
		$display_types[] = array(
			'display_portrait',
			'Name & Portrait'
		);

		$sort_types = array();

		$sort_types[] = array(
			'sort_by_name',
			'Name'
		);
		if (ryit_user_is_alumnus() || (ryit_user_is_current() && user_can($user_id,'edit_pages'))) {
			$sort_types[] = array(
				'sort_by_round',
				'RYIT Round'
			);
		}

		$filter_types[] = array(
			'filter_fellowship',
			'All members'
		);
		$filter_types[] = array(
			'filter_ryit_header',
			'Reclaim your Inner Throne',
			true
		);
		$filter_types[] = array(
			'filter_ryit_alumni',
			'-- The alumni'
		);
		$filter_types[] = array(
			'filter_ryit_myround',
			'-- Men from my round *coming*',
			true
		);
		$filter_types[] = array(
			'filter_ryit_leadership',
			'Leadership team *coming*',
			true
		);
		$filter_types[] = array(
			'filter_fellowship',
			'The Fellowship *coming*',
			true
		);
		$filter_types[] = array(
			'filter_fellowship_free',
			'-- Free members *coming*',
			true
		);
		$filter_types[] = array(
			'filter_fellowship_bronze',
			'-- Bronze members *coming*',
			true
		);
		$filter_types[] = array(
			'filter_fellowship_silver',
			'-- Silver members *coming*',
			true
		);
		$filter_types[] = array(
			'filter_fellowship_bronze',
			'-- Gold members *coming*',
			true
		);
		$filter_types[] = array(
			'filter_my_skills',
			'Men with my skills *coming*',
			true
		);
		$filter_types[] = array(
			'filter_skills_needed',
			'Men with skills I\'m in need of *coming*',
			true
		);
		$filter_types[] = array(
			'filter_my_country',
			'Men in my country',
		);
		$filter_types[] = array(
			'filter_my_city',
			'Men in my city',
		);
		$filter_types[] = array(
			'filter_stretch_goal',
			'Men with active stretch goal',
		);
		if(ryit_user_is_alumnus()) {
			$filter_types[] = array(
				'filter_my_round',
				'Men from my RYIT *coming*',
				true
			);
		}

		$form_echo = "";
		$form_echo .= "<form id='member_directory_settings' class='clearfix'>";
		$form_echo .= "<div id='display_type'><h3>View type</h3>";
		$form_echo .= "<select id='display_type_input'>";

		$i = 0;
		foreach ($display_types as $type) {
			$form_echo .= '<option id="' . $type[0] . '"' . ' value="' . $type[0] . '"';
			if ($display_type == $i) $form_echo .= " selected='selected'";
			$form_echo .= "'>" . $type[1] . "</option>";
			$i++;
		}

		$form_echo .= "</select>";
		$form_echo .= "</div>";
		$form_echo .= "<div id='sort_type'><h3>Sort by</h3>";
		$form_echo .= "<select id='sort_type_input'>";

		if (ryit_user_is_current() && !user_can($user_id,'edit_pages')) {
			$form_echo .= '<option disabled selected="selected">Not available</option>';
		}
		else {
			$i = 0;
			foreach ($sort_types as $type) {
				$form_echo .= "<option id='" . $type[0] . "'";
				if ($sort_type == $i) $form_echo .= " selected='selected'";
				$form_echo .= ">" . $type[1] . "</option>";
				$i++;
			}
		}

		$form_echo .= "</select>";
		$form_echo .= "</div>";
		$form_echo .= "<div id='filter_type'><h3>Filter</h3>";
		$form_echo .= "<select id='filter_type_input'>";
		$i = 0;
		foreach ($filter_types as $type) {
			$form_echo .= "<option id='" . $type[0] . "'";
			if ($filter_type == $i) $form_echo .= " selected='selected'";
			if(!empty($type[2])) {
				if($type[2]) $form_echo .= " disabled";
			}
			$form_echo .= ">" . $type[1] . "</option>";
			$i++;
		}
		$form_echo .= "</select>";
		$form_echo .= "</div>";
		$form_echo .= "</form>";

		$echo = "";
	}

	/**************** LIST USERS *****************/

	//Define default avatar
	$upload_dir = wp_upload_dir();
	$default_avatar =  $upload_dir['baseurl'] . "/2014/12/crown-logo.png";

	//only show current round to men undergoing their initiation
	if (ryit_user_is_current() && !user_can($user_id,'edit_pages')) {
		$curr_round = get_field('ryit_round_number', 'options');
		$temp_rounds = $rounds[$curr_round];
		$rounds = array();
		$rounds[$curr_round] = $temp_rounds;
	}

	if ($sort_type == 0) { /************* Sort alumni by name ******************/
		foreach ($alumni_names as $alumnus) {
			$alumnus_data = get_userdata($alumnus['id']);
			$user_id = $alumnus_data->ID;

			$avatar_status = "";
			$avatar = get_field('field_5a576ed8b86eb', 'user_' . $user_id);
			if (empty($avatar)) {
				$args = array(
					'size' => 150,
					'default' => 'blank'
				);
				$avatar = get_avatar_url($user_id, $args);
				$avatar_status = " has-avatar";
			}
			else {
				$avatar_status = " has-avatar";
			}

			if(empty($first_letter)) $first_letter = "";
			if(empty($first_letter_prev)) $first_letter_prev = "";

			switch ($display_type) { //Return HTML based on display type				
				case 0: //Show name only
					$first_letter = mb_substr($alumnus['first_name'], 0, 1);
					if ($first_letter != $first_letter_prev || empty($first_letter_prev)) $echo .= '<li class="letter">' . $first_letter . '</li>';
					$echo .= "<li><a href='/user-profile?user_id=" . $alumnus_data->ID . "'>" . $alumnus['first_name'] . " " . $alumnus['last_name'] . "</a></li>";
					$first_letter_prev = $first_letter;
					break;
				case 1: //Show name and photo
					$first_letter = mb_substr($alumnus['first_name'], 0, 1);
					if ($first_letter != $first_letter_prev || empty($first_letter_prev)) {
						$echo .= "<div class='member letter'><div class='portrait'><span>" . $first_letter . "</span></div><h4></h4></div>";
						$first_letter_prev = $first_letter;
					}
					$echo .= "<div class='member" . $avatar_status . "'><div class='default_img'></div>";
					$echo .= "<a href='/user-profile?user_id=" . $alumnus_data->ID . "'><div class='portrait' style='background-image: url(" . $avatar . ");'>";
					//$echo .= "<div class='hover'><div class='hover_bg'></div></a></div>";
					$echo .= "</div></a>";
					$echo .= "<h4><a href='/user-profile?user_id=" . $alumnus_data->ID . "'>" . $alumnus_data->first_name . " " . $alumnus_data->last_name . "</a></h4>";
					$echo .= "</div>";
				break;
			}
		}

		$title = "<h2>Sorted by First Name <span>Count: " . count($alumni_names) . "</span></h2>";

		if ($display_type == 0) {
			$echo = $title . "<ul id='alumnus_names'>" . $echo . "</ul>";
		}
		else {
			$echo = $title . $echo;
		}

		if (!$is_ajax) {
			$echo = $header_echo . $form_js . $form_echo . "<div id='directory_listing'><div class='member-group'>" . $echo . "</div></div>";
		}
		else {
			$echo = "<div class='member-group'>" . $echo . "</div>";
		}
		//End sort type 0
		
	}
	else if ($sort_type == 1) { /******* Sort alumni by RYIT round ********/
		foreach ($rounds as $round_number => $users) {
			$round_echo = "";

			foreach ($users as $user_id) {
				$alumnus = get_userdata($user_id);
				$avatar = get_field('field_5a576ed8b86eb', 'user_' . $user_id);
				if (empty($avatar)) {
					$avatar = $default_avatar;
					$avatar_status = "";
				}
				else {
					$avatar_status = " has-avatar";
				}

				switch ($display_type) { //Gather
					case 0: //Show name only
						$round_echo .= "<li class='member'><h4><a href='/user-profile?user_id=" . $alumnus->ID . "'>" . $alumnus->first_name . " " . $alumnus->last_name . "</a></h4></li>";
					break;
					case 1:
						$round_echo .= "<div class='member" . $avatar_status . "'>";
						$round_echo .= "<a href='/user-profile?user_id=" . $alumnus->ID . "'><div class='portrait' style='background-image: url(" . $avatar . ");'>";
						//$echo .= "<div class='hover'><div class='hover_bg'></div></a></div>";
						$round_echo .= "</div></a>";
						$round_echo .= "<h4><a href='/user-profile?user_id=" . $alumnus->ID . "'>" . $alumnus->first_name . " " . $alumnus->last_name . "</a></h4>";
						$round_echo .= "</div>";
					break;
				}
			}

			switch ($display_type) { //Gather
				case 0: //Show name only
					$echo .= "<div class='member-group'>";
					$echo .= "<h2>Round " . $round_number . "</h2>";
					$echo .= "<ul>" . $round_echo . "</ul>";
					$echo .= "</div>";
				break;
				case 1: //Show name and photo
					$echo .= "<div class='member-group'>";
					$echo .= "<h2>Round " . $round_number . "</h2>";
					$echo .= $round_echo;
					$echo .= "</div>";
				break;
			}
		}
		if ($is_ajax) {
			$echo = "<div id='rounds'>" . $echo . "</div>";
		}
		else {
			$echo = $header_echo . $form_js . $form_echo . "<div id='directory_listing'><div id='rounds'>" . $echo . "</div></div>";
		}
	}
	else {
		$echo = $header_echo . $form_js . $form_echo . "<div id='directory_listing'><div class='member-group'>" . $echo . "</div></div>";
	}

	if (ryit_user_is_current() && !user_can($user_id,'edit_pages')) {
		$echo .= '<div style="clear:both; padding-top: 80px;"><p style="text-align: center; max-width: 600px; margin: -0.5em auto 3em; color: #999;">When you complete the training, the full alumni will be visible to you here, with networking opportunities etc.</p></div>';
	}

	if ($is_ajax) {
		$return['echo'] = $echo;
		if (isset($display_type)) {
			$return['display_type'] = $display_type;
		}
		if (isset($sort_type)) {
			$return['sort_type'] = $sort_type;
		}
		if (isset($filter_type)) {
			$return['filter_type'] = $filter_type;
		}
		wp_send_json_success($return);
		die();
	}
	else {
		return $echo;
	}
}

add_shortcode('member_directory', 'ryit_member_directory');


//**Custom Gravatar**/
/*
add_filter( 'avatar_defaults', 'ryit_custom_gravatar' );
function ryit_custom_gravatar($avatar_defaults) {
	$myavatar = wp_upload_dir() . '/2014/12/crown-logo.png';
	$avatar_defaults[$myavatar] = 'RYIT crown';
	return $avatar_defaults;
}
*/

/************************** FELLOWSHIP FUNCTIONS **************************/

add_action('avada_before_body_content', 'ryit_init_startup_guide');

function ryit_init_startup_guide() {
	$user_id = get_current_user_id();

	if(is_fellowship_page()) {
		ob_start();
	?>
		<script type="text/javascript">
			jQuery('document').ready(function($j) {
				$j('#setup-guide .toggle').on('click', function() {
					if($j(this).parents('#setup-guide').hasClass('maximized')) {
						$j(this).parents('#setup-guide').removeClass('maximized');
						$j(this).parents('#setup-guide').addClass('minimized');
						var mode = "minimized";
					}
					else {
						$j(this).parents('#setup-guide').removeClass('minimized');
						$j(this).parents('#setup-guide').addClass('maximized');
						var mode = "maximized";
					}

					var data = {
						action: 'update_setup_state',
						mode: mode,
						user_id: $j('body').attr('user_id')
					};

					$j.ajax({
						url: ajaxurl,
						type: 'GET', // the kind of data we are sending
						data: data,        
						dataType: 'json',
						success: function(response) {
						}, error: function() {
							console.log("Something went wrong");
						}
					});
				});
			});
		</script>
	<?php
		$echo = ob_get_clean();

		$portrait_defined = !empty(get_field('ryit_user_profile_image', 'user_' . $user_id)) ? get_field('ryit_user_profile_image', 'user_' . $user_id) : false ;
		$location_defined = (!empty(get_field('ryit_user_profile_city','user_' . $user_id)) && !empty(get_field('ryit_user_profile_country','user_' . $user_id))) ? true : false;
		$life_assessment_defined = !empty(get_user_meta($user_id, 'ryit_user_life_assessment_total_average', true)) ? true : false;
		$stretch_goal_defined = false;
		//$purposed_section_defined = false;

		if(ryit_user_stretch_goal_validate($user_id)) {
			$stretch_goal_defined = true;
		}

		if($portrait_defined && $location_defined && $life_assessment_defined && $stretch_goal_defined) {
			return;
		}

		$setup_guide_state = get_user_meta($user_id, 'ryit_setup_guide_state', true);
		$state_class = (get_user_meta($user_id, 'ryit_setup_guide_state', true) == "maximized") ? "maximized" : "minimized";

		//First login. Highlight setup guide
		//get_user_meta(get_current_user_id(), 'ryit_user_welcome_video_played', true);
		if(get_user_meta($user_id, 'ryit_user_welcome_video_played', true) == true || get_user_meta(1,'ryit_user_fellowship_login_count',true) > 0) {
			$state_class = $state_class;
		}
		else {
			$state_class .= ' inactive';
		}

		$echo .= '<div id="setup-guide" class="' . $state_class . '">';
		$echo .= '<div class="toggle"><i class="far fa-window-maximize"></i><i class="far fa-window-minimize"></i></div>';
		$echo .= '<div class="innerwrap">';
		$echo .= '<ul>';
		$echo .= $portrait_defined ? '<li class="complete"><span>Upload Profile Picture</span></li>' : '<li><span>Upload Profile Picture</span></li>';
		$echo .= $location_defined ? '<li class="complete"><span>Tell us where you\'re from</span></li>' : '<li><span>Tell us where you you\'re from</span></li>';
		$echo .= $life_assessment_defined ? '<li class="complete"><span>Complete Life Assessment</span></li>' : '<li><span>Complete <a href="/user-profile/?active-section=life-assessment">Life Assessment</a></span></li>';
		$echo .= '</ul>';
		$echo .= $stretch_goal_defined ? '<li class="complete"><span>Initiate a Stretch Goal</span></li>' : '<li><span>Initiate a Stretch Goal</span></li>';
		//$echo .= $purposed_section_defined ? '<li class="complete"><span>Purpose Section defined</span></li>' : '<li><span>Purpose Section defined</span></li>';
		$echo .= '<p>Complete sequence to remove box.</p>';
		$echo .= '</div>';
		$echo .= '</div>';
		echo $echo;
	}
}


add_action('wp_ajax_update_setup_state', 'ryit_update_setup_state');

function ryit_update_setup_state() {
	$user_id = $_GET['user_id'];
	$mode = $_GET['mode'];
	//echo $user_id . " mode:" . $mode;
	update_user_meta($user_id, 'ryit_setup_guide_state', $mode);
	die();
}

/************************** FELLOWSHIP FUNCTIONS **************************/


function fellowship_menu() {
	$echo = ryit_get_fp_javascript('change-view-mode');

	$args = array(
		'menu' => 290,
		'container' => 'nav'
	);

	ob_start();
	wp_nav_menu($args);
	$fellowship_menu = ob_get_clean();

	$echo .= '<div id="fellowship-menu"><div id="menu-wrap">';
	$echo = $echo . $fellowship_menu;

	//Code below about user profile mode switch
	global $post;
	$user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();

	if (is_page('user-profile') && (ryit_user_is_alumnus($user_id) || (ryit_user_is_current($user_id) && user_can($user_id,'edit_pages')))) {
		if(ryit_user_is_alumnus($current_user_id) || (ryit_user_is_current($user_id) && user_can($user_id,'edit_pages'))) { //only show toggle to men who have been on RYIT
			$view_mode = ryit_get_user_view_mode();

			if (empty($view_mode)) $view_mode = 'goals';

			$viewmode_class = $view_mode;
			$echo .= '<div class="right" id="view-mode"><span>View mode</span><ul id="mode-switches" class="' . $viewmode_class . '">';

			$link = get_permalink($post->ID);

			if ($view_mode == "ryit") {
				$echo .= '<li id="mode-ryit" mode="ryit"><a href="' . $link . '"></a></li>';
			}
			else {
				$echo .= '<li id="mode-ryit" class="switch-mode" mode="ryit"><a href="' . $link . '"></a></li>';
			}

			if ($view_mode == "goals") {
				$echo .= '<li id="mode-goals" mode="goals"><a href="' . $link . '"></a></li>';
			}
			else {
				$echo .= '<li id="mode-goals" class="switch-mode" mode="goals"><a href="' . $link . '"></a></li>';
			}
			$echo .= '</ul></div>';
		}
	}

	$echo .= '</div></div>';

	echo $echo;
}

/************************** ALUMNUS/USER PROFILE **************************/


//Set up wysiwyg field
add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );
function my_toolbars( $toolbars )
{

	// Add a new toolbar called "Very Simple"
	// - this toolbar has only 1 row of buttons
	$toolbars['Very Simple' ] = array();
	$toolbars['Very Simple' ][1] = array('bold' , 'italic' , 'underline', 'numlist', 'bullist', 'blockquote','styleselect');
	unset( $toolbars['Very Simple' ][2]);

	// Edit the "Full" toolbar and remove 'code'
	// - delet from array code from http://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
	/*if( ($key = array_search('file' , $toolbars['Very Simple' ][2])) !== false )
	{*/
	 //   unset( $toolbars['Very Simple' ][0]);
	//}

	// remove the 'Basic' toolbar completely
	//unset( $toolbars['Basic' ] );

	// return $toolbars - IMPORTANT!
	return $toolbars;
}

add_action('wp_ajax_ryit_member_profile', 'ryit_member_profile');

function ryit_member_profile() {
	acf_form_head();
	wp_enqueue_style("media-upload", get_site_url() . "/wp-includes/css/media-views.min.css"); //ensure that image uploader looks correct

	global $post;
	$user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();
	$user_data = get_userdata($user_id);

	if (!empty(get_avatar($user_data->ID))) {
		$args = array(
			'size' => 250,
			'default' => 'mysteryman'
		);
		$avatar = get_avatar_url($user_data->ID, $args);
	}

	$echo = ryit_get_fp_javascript('user-profile');

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		$is_ajax = false;
		$active_section = $_POST['active_section'];
		$view_mode = ryit_get_user_view_mode();
		//prepare popup that shows when user changes view mode

		if (!empty($active_section)) {
			$echo .= "<h1>" . $active_section . "</h1>";
		}
	}
	else {
		$is_ajax = true;
		$view_mode = $_GET['view_mode'];
		update_user_meta($user_id,'ryit_user_profile_viewmode',$view_mode);
		if ($view_mode == "ryit") {
			$active_section = 'call-to-adventure';
		}
		else {
			$active_section = 'life-assessment';
		}
	}

	$current_user_id = get_current_user_id();

	if(!empty($_GET['active-section'])) {
		$active_section = $_GET['active-section'];
	}

	if(empty($view_mode)) {
		$view_mode = 'goals';
	}

/*
if((ryit_user_is_alumnus($current_user_id) || ryit_user_is_fellowship($current_user_id)) && $view_mode != 'ryit' || (ryit_user_is_current() && user_can($current_user_id,'edit_pages') && $view_mode != 'ryit')) { //Alumnus
	echo "this should work;"
}*/


	if((ryit_user_is_alumnus($current_user_id) || ryit_user_is_fellowship($current_user_id)) && $view_mode != 'ryit' || (ryit_user_is_current() && user_can($current_user_id,'edit_pages') && $view_mode != 'ryit')) { //Alumnus

		if($user_id == $current_user_id) {
			$fields_life_assessment = array(
				false, //Special function used to call this section
				false //Hide menu option on the user profiles of others
			);
		}

		$fields_vision = array(
			'field_5c686fcbc8c50', //info message
			'field_5b31265e34b15', //vision
			'field_5b7ab42bbe941', //mission
		);

		$fields_goals = array(
			'field_5c686fcbc8c50', //info message
			'ryit_user_goal_ten_year', //ten year goal
			//'ryit_user_goal_five_year_toggle', //five year goal toggle
			'ryit_user_goal_five_year', //five year goal
			'ryit_user_goal_three_year', //three year goal
			'ryit_user_goal_one_year',
		);

		$fields_stretch_goals = array(
			false //Special function used to call this section
		);

		$fields_daily_practice = array(
			'field_5c7bc6a93c8db',
			'field_5c7bc7543c8df',
			'field_5c7be38a73faa'
		);

/*
		$fields_interests = array(
			'field_5b7ead2fe4691', //more info about your interests
			'field_5b338694a6537',
			//fields of interest or skill
		);*/

		$fields = array(
			array(
				"Life Assessment",
				$fields_life_assessment
			) ,
			array(
				"Vision & Mission",
				$fields_vision
			) ,
			array(
				"Long term Goals",
				$fields_goals
			) ,
			array(
				"Stretch Goals",
				$fields_stretch_goals
			) ,
			array(
				"Daily Practice",
				$fields_daily_practice
			)/* ,
			array(
				"Purpose & Business",
				$fields_interests
			) */
		);

		if(ryit_user_is_setting_up($user_id)) {
			if (empty($active_section)) {
				$active_section = sanitize_title($fields[ryit_user_get_setup_progress($user_id)-1][0]);
			}
		}
		else {
			if($user_id == $current_user_id) {
				if (empty($active_section)) {
					$active_section = 'life-assessment';
				}
			}
			else {
				if (empty($active_section)) {
					$active_section = 'vision-mission';
				}
			}
		}
	}
	else if (ryit_user_is_current($current_user_id) || (ryit_user_is_alumnus($current_user_id) && $view_mode == 'ryit') || (ryit_user_is_current($current_user_id) && !user_can($current_user_id, 'edit_pages') && $view_mode == 'ryit')) { //Active course participants
		$fields_week1 = array(
			'field_5bdb128593646', // Archetypal life wheel
			'field_5bdb1937f5228'
			// Intensity
		);

		$fields_week2 = array(
			'field_5bdad881a4bd1', // Commitments
			'field_5bdae7b751dc2'
			// Traumas		
		);

		$fields_week3 = array(
			'field_5bdb1d215394e'
			// Addictions
		);

		$fields_week8 = array(
			'field_5bf5bc868e089'
			// Life commitments
		);

		$fields_week9 = array(
			'field_5beaec6a90723'
			// Life sacrifices
		);

		$fields_week12 = array(
			'field_5b31265e34b15', //vision
			'field_5b7ab42bbe941', //mission
			'field_5b338e4e928e8', //ten year goal
			'field_5b338e3b928e7', //five year goal
			'field_5c101d40c01b3', //three year goal
			'field_5b338e2b928e6'
			//one-year goal
		);

		$fields_week13 = array(
			'field_5c0ffd33f0fe1', //Life Wheel redux
			'field_5c100bcb1544a'
			//Goals & commitments message
		);

		$feedback = array(
			'field_5b338fa3bec2c', //Review score
			'field_5b351e7869da3', //Favortie part of the training
			'field_5b339027bec2e', //What could be improved?
			'field_5b3520585ff0f', //How have you changed?
			'field_5c100f5db4936', //Testimonial
			'field_5b3391c8e4b76', //Offers
			'field_5c10f7c8f229c'
			//Message
		);

		$fields = array(
			array(
				"Call to Adventure",
				$fields_week1
			) ,
			array(
				"Path of Unknowing",
				$fields_week2
			) ,
			array(
				"Mapmaker of the East",
				$fields_week3
			) ,
			array(
				"Mystic Glade",
				$fields_week8
			) ,
			array(
				"Valley of the Black Knight",
				$fields_week9
			) ,
			array(
				"Reclaim your Inner Throne",
				$fields_week12
			) ,
			array(
				"Leaving a Legacy",
				$fields_week13
			) ,
			array(
				"Feedback & Integration",
				$feedback
			)
		);

		if (empty($active_section)) {
			$active_section = 'call-to-adventure';
		}
	}

	if (!empty($fields)) {
		if(!$is_ajax) $nav_echo = ryit_get_profile_navigation($user_id,$fields);

		$fields_store = $fields; //store $fields for use with ajax
		$fields_with_val = 0;
		$fields_echo = ""; 

		$step_index = 1;
		$setup_sequence_length = 5;

		//Set up fields inside categories
		foreach ($fields as $field_group) {
			if(ryit_user_is_setting_up($user_id) && $step_index > ryit_user_get_setup_progress($user_id) && $view_mode == 'goals')  {
				break;
			}

			$field_group_id = sanitize_title($field_group[0]);
			$fields_echo .= '<div class="field-group" id="field-group-' . $field_group_id . '">';
			$fields = $field_group[1];
			$field_echo = "";

			/* Print step indicators on initial setup */
			if(ryit_user_is_setting_up($user_id)) {
				//echo ryit_user_get_setup_progress(get_current_user_id());
				if($step_index <= ryit_user_get_setup_progress($user_id) && $user_id == $current_user_id && $view_mode == 'goals') {
					$fields_echo .= '<div class="setup-steps">';
					$fields_echo .= '<h3>Step <span>' . $step_index . '</span> of ' . $setup_sequence_length . '</h3>';
					$fields_echo .= '</div>';
				}
				$step_index++;
			}

			switch($field_group_id) {
				case 'stretch-goals' : 
					$field_echo .= ryit_get_profile_stretch_goals($user_id); //Retrieve stretch goals
					$fields_with_val++;
					break;
				case 'life-assessment' :
					if($user_id == $current_user_id) {
						$field_echo .= ryit_user_life_assessment(); //Retrieve stretch goals
						$fields_with_val++;
					}
					break;
					/*
				case 'daily-practice' :
					$field_echo .= ryit_user_daily_practice(); //Retrieve stretch goals
					$fields_with_val++;
					break;*/
				default :
					foreach ($fields as $field_id) {		// Retrieve normal fields
						$field_data = ryit_get_profile_field_values($field_id,$user_id,$active_section);
						$field_echo .= $field_data['output'];
						if(!empty($field_data['has_value'])) {
							$fields_with_val++;
						}
					}
			}

			/* Print field values to screen */
			if ($fields_with_val > 0 || !$field_group[1][0]) { //Show if values have been filled in by user or if no fields are associated as content is fetched by custom function above
				if($field_group_id != 'life-assessment') {
					$fields_echo .= '<h2>' . $field_group[0];
					if($view_mode == 'goals' && $user_id == $current_user_id) {
						$fields_echo .= '<i class="fas fa-question"><span>Help</span></i>';
					}
					$fields_echo .= '</h2>';
				}
				$fields_echo .= $field_echo;
			}

			$fields_echo .= '</div>'; //end #field-group
		}

		if($is_ajax) {
			if(!empty($view_mode)) {
				$ajax_echo = $fields_echo; //This is the output which will be returned in an Ajax call	
				$return['dropdown'] = ryit_get_profile_navigation($user_id, $fields_store, true);
				$return['output'] = $ajax_echo;
				$return['user_id'] = $user_id;
				wp_send_json_success($return);
				die();
			}
		}
		
		//---- Will not continue beyond this point if Ajax call without mode shift
		

		$fields_echo = '<div id="field-groups">' . $fields_echo . '</div>';

		//Add RCP profile and purchase history sections
		if ($user_id == $current_user_id) {
			$echo .= '<div class="field-group" id="field-group-edit-account">';
			ob_start();
			echo do_shortcode('[edd_profile_editor]');
			$content = ob_get_clean();
			$echo .= '<div class="field">' . $content . '</div>';
			$echo .= '</div>';

			$echo .= '<div class="field-group" id="field-group-purchase-history">';
			ob_start();
			echo do_shortcode('[purchase_history]');
			$content = ob_get_clean();
			$echo .= '<div class="field">' . $content . '</div>';
			$echo .= '</div>';
		}

		//do_shortcode('[edd_subscriptions]')

		if ($fields_with_val > 0 && !$is_ajax) { //Fields are filled in & IS NOT AJAX
			$echo = $nav_echo . $echo . $fields_echo;
		}
		else if($fields_with_val > 0 && $is_ajax) { //Fields are filled in & IS AJAX
			$echo = $echo . $fields_echo;
		}
		else {
			$echo = $echo;
		}

		/* Set up main */
		$view_mode = empty(ryit_get_user_view_mode($user_id)) ? 'goals' : ryit_get_user_view_mode($user_id);

		if($user_id == $current_user_id) {
			if((ryit_user_is_setting_up($user_id) && $view_mode == 'goals') && empty($active_section)) {
				$active_section = sanitize_title($fields_store[ryit_user_get_setup_progress(get_current_user_id())-1][0]);
			}			
		}
		else {
			if($_GET['active-section']) {
				$active_section = $_GET['active-section'];	
			}
			else {
				$active_section = 'vision-mission';
			}
		}
		$echo = '<div class="main" active-section="' . $active_section . '">' . $echo . '</div>'; //Close main
		
		/* Set up sidebar */
		$echo .= ryit_get_profile_sidebar($user_id);

		if ($fields_with_val <= 0) { //No values filled in by user
			$echo .= '<div id="profile" class="incomplete">';
			$echo .= '<h3 style="text-align: center; margin-bottom: 1em;">' . $user_data->first_name . ' has not filled in his profile.</h3>';

			if (empty(get_field('user_ryit_triad', 'user_' . $user_id))) {
				$echo .= '<h2 style="margin-top: 30px; padding-left: 0;">' . $user_data->first_name . ' needs a triad</h2>';
				$echo .= '<p>Is ' . $user_data->first_name . ' in your triad? You can claim him as your triad Brother here :)';
				$echo .= '<div id="triad-member-claim" style="padding: 15px 0;"><div class="fusion-button simple" id="button-claim-triad-member" style="margin: 0 auto; display: table;">Claim ' . $user_data->first_name . ' for your Triad</div></div>';
				$echo .= '<p class="clear" style="font-style: italic; margin-top: 30px; color: #999;">NB! Clicking this button will send an e-mail to ' . $user_data->first_name . ' notifying you of your wish to have him fill in his profile and assign himself to your triad. Don\'t use it if he is not in your triad.</p>';
			}
			//$echo .= '<div id="send-profile-challenge"><p>Challenge ' . $user_data->first_name . '</p></div>';
			$echo .= '</div>';
		}

		//$display_sidebar = (alumnus_sidebar_empty($user_id) && $user_id != $current_user_id) ? " " : " display_sidebar";
		$display_sidebar = " display_sidebar";

		if($is_ajax) {
			$echo = $ajax_output;
		}
		else {
			//$popup = ryit_ui_feedback_popup('<img src="' . get_stylesheet_directory_uri() . '/images/spinner.gif" class="loader" /><p>Refreshing View ...</p>',true,false);
			$echo = $popup . '<div id="profile" user_id="' . $user_id . '" class="clearfix ' . $display_sidebar . '"><h1>' . $user_data->first_name . ' ' . $user_data->last_name . '</h1>' . $echo . '</div>';
		}
	}

	return $echo;
}

add_shortcode('alumnus_profile', 'ryit_member_profile');

function ryit_get_help_popups() {
	$user_id = get_current_user_id();
	try {
		//echo 'test ' . rcp_get_subscription_id($user_id);

		$echo = '';
		$view_mode = ryit_get_user_view_mode($user_id);

		if($view_mode == 'goals' && ryit_user_is_setting_up($user_id) && is_page('user-profile')) {
			$setup_progress = ryit_user_get_setup_progress($user_id);
			$setup_help_popups = get_user_meta($user_id, 'ryit_user_setup_popups', true);
	
			//var_dump($setup_help_popups);

			if(empty($setup_help_popups)) {
				update_user_meta($user_id,'ryit_user_setup_popups',$setup_progress);
			}
			else {
				$popups = explode('|', $setup_help_popups);
			
				$help_popup_shown = false;

				foreach ($popups as $popup_id) {
					if($setup_progress == $popup_id) {
						$help_popup_shown = true;
					}
				}

				if(!$help_popup_shown) {
					$echo .= ryit_get_help_popup($setup_progress,true);
					update_user_meta($user_id, 'ryit_user_setup_popups', $setup_help_popups . '|' . $setup_progress);
				}
			}
		}
		echo $echo;
	}
	catch(Exception $e) {
	  echo 'Message: ' .$e->getMessage();
	}
}

add_action('avada_before_body_content', 'ryit_get_help_popups');


function ryit_get_help_popup($popup_id,$first_display = false,$include_wrap=true) {
	$echo = '';
	$user_id = ryit_get_user_ID();

	if($popup_id == 1) {
		return false;
	}
	if($popup_id == 2) {
		$echo .= '<h3>Vision & Mission</h3>';
		if($first_display) {
			$echo .= '<p>Okay, thank you for filling in your Life Assessment! (you will be returning to it regularly when you do your stretch goal)</p>';
		}
		$echo .= '<p>You have now moved on to the Vision & Mission section. This is the highest level bird&apos;s-eye view of your purpose and direction in this life.</p><p>You may not yet have real clarity on these two, but you must start <em>somewhere</em>, right?</p><p>If this is hard for you to know, then sit down and write about what you care about.</p><p>Let the image below guide your hand. (And soon, we&apos;ll create an instructions video with further information on this process.)</p><img src="/wp-content/uploads/2016/01/purpose-mandala.jpg" />';
	}
	else if($popup_id == 3) {
		$echo .= '<h3>Long term Goals</h3>';
		if($first_display) {
			$echo .= '<p>Fabulous work! You have established your high-level view of life and are ready to start narrowing it down.</p>';
		}
		$echo .= '<p>Now, please refine further your clarity about where you are going by establishing goals for 10,5<sup>*</sup>,3, and 1-year periods.</p><p>Without clarity, you don&apos;t know where you&apos;re going, so this is very important!</p><p>If it serves you, you can set goals with an eye on the Mind, Body, People, Purpose dimensions!</p><p class="footnote"><sup>*</sup> The 5-year goal is optional.</p>';
	}
	else if($popup_id == 4) {
		$echo .= '<h3>Stretch Goals</h3>';
		if($first_display) {
			try {
				$echo .= '<p>Congratulations, ' . 	ryit_get_user_name() . '! Your excellent work has prepared you for the Stretch Goal system :)</p>';
			
			} catch (Exception $e) {
				echo '<p>Error Occured: </p>',  $e->getMessage(), "<p>Expected Message: Congratulations, <user name>! Your excellent work has prepared you for the Stretch Goal system :)</p>";
			}
		}
		$echo .= '<p>This is the core of this platform and it will change your life in dramatic ways if you take it on seriously.</p><p>Define your subgoals in all four dimensions, give the 3-month stretch goal a name, and define when the stretch goal starts (the system automatically determines when it ends).</p><p>The system will let you know when each subgoal is ready by giving it a green checkmark, and when everything has been done, the submit button will become active and you can pursue the goal like the badass that you are (accountability system is coming).</p><p>Good luck!</p>';
	}
	else if($popup_id == 5) {
		$echo .= '<h3>Daily Practice</h3>';
		$echo .= '<p>In the daily practice system, you will create rituals for your mornings and evenings.</p><h4>For the mornings, here are our recommendations</h4><ul><li>We recommend including at least one option from each of the four Mind, Body, People, Purpose categories</li><li>Body: Do physical exercise & eat a healthy breakfast</li><li>Mind: Read a book and journal</li><li>People: Express love to people you care about and/or send messages to folks you haven&apos;t connected with in a while</li><li>Purpose: Plan the day/set goals</li></ul><h4>For the evenings, here are our recommendations</h4><ul><li>No purpose work</li><li>Connect with a loved one in a relaxing way, though ONLY if you live under the same roof</li><li>Turn off intense lights and screens minimum one hour prior to bed</li><li>Take a warm shower</li><li>Do some simple stretching/yoga (avoid intense physical exercise)</li><li>Do not do meditation immediately before bed, as it can surprisingly make it harder to sleep (it reduces what is commonly known as sleep pressure)</li></ul><p>Good luck!</p>';
	}
	else if($popup_id == 6) {
		$echo .= '<h3>Skillshare System</h3>';
		$echo .= '<p>This system will soon go live.</p>';
	}
	if(!empty($echo)) {
		$close_btn = '<a href="#" class="close"><span class="fa fa-times"></span></a>';
		$popup_start = '<div id="ryit-popup" class="help" style="top: 150px;"><div class="innerwrap">';
		$popup_end = '</div></div>';
		if($first_display) {
			$echo = $echo . '<div class="first-time"><p>If you need to see this help again, please click the help button to the right of the section headline. <i class="fas fa-question"><span>Help</span></i></div>';
		}
		if($include_wrap) {
			$echo = $popup_start . $echo . $close_btn . $popup_end;
		}
		$bg_overlay = '<div id="ryit-popup-overlay"></div>';
		$script = '<script type="text/javascript">
				jQuery("document").ready(function($j) {
					var scrollTop = $j(document).scrollTop();
					$j("#ryit-popup").css("top", scrollTop + 150 + "px");
				});</script>';
		if($include_wrap) {
			return $script . $bg_overlay . $echo;
		}
		else {
			return $echo;
		}
	}
	else {
		return false;
	}
	
}

/*
function is_user_logged_in() {
	$logged_in_users = get_transient('online_status');
	$user = get_current_user_id();
	$no_need_to_update = isset($logged_in_users[$user->ID]) && $logged_in_users[$user->ID] >  (time() - (15 * 60));

	if(!$no_need_to_update){
	  $logged_in_users[$user->ID] = time();
	  set_transient('online_status', $logged_in_users, $expire_in = (30*60)); // 30 mins 
	}

	print_r($logged_in_users);
}
*/
//add_action('init', 'is_user_logged_in');

function ryit_user_get_setup_progress($user_id) {
	$setup_progress = get_user_meta($user_id, 'ryit_user_setup_progress', true);
	if(empty($setup_progress)) $setup_progress = 1;
	return $setup_progress;
}

function ryit_user_is_setting_up($user_id) {
	$setup_complete_index = 5; //If index is this high, setup steps are already completed
	if(ryit_user_get_setup_progress($user_id) <= $setup_complete_index) {
		return true;
	}
	else {
		return false;
	}
}

function ryit_user_update_setup_progress($user_id=false) {
	//Reset to 1 with line below. Uncomment for dev purposes
	//update_user_meta(1,'ryit_user_setup_progress', 1);
	if(empty($user_id)) $user_id = get_current_user_id();

	$setup_complete_index = 5; //If index is this high, setup steps are already completed

	//Get how far a user has come in the first goal-setting sequence
	$setup_progress = get_user_meta($user_id, 'ryit_user_setup_progress', true);
	
	if(empty($setup_progress)) {
		$setup_progress = 1;
	}

	if($setup_progress >= $setup_complete_index ) {
		return;
	}
	else {
		//update_user_meta(1,'ryit_user_debug_field','user id ' . $user_id);
		update_user_meta($user_id,'ryit_user_setup_progress', ++$setup_progress);
	}
}



/* DEVELOPER FUNCTIONS - Comment out when not developing



function ryit_test() {
	$user_id = get_current_user_id();
	//update_user_meta(1,'ryit_user_debug_field','Function is being run');
	//echo '<h2>' . get_user_meta(1,'ryit_user_fellowship_login_count',true) . '</h2>';
	//echo 'user meta ' . get_user_meta($user_id,'ryit_user_stretch_goal_defined',true);
	if($user_id == 1) update_user_meta($user_id, 'ryit_user_setup_progress', 2);
}

add_action('init', 'ryit_test');

function ryit_test() {
	//update_user_meta(1,'ryit_user_debug_field','Function is being run');
	//echo '<h2>' . get_user_meta(1,'ryit_user_fellowship_login_count',true) . '</h2>';
	echo get_user_meta($user_id,'ryit_user_stretch_goal_initiated',false);
	delete_user_meta($user_id,'ryit_user_stretch_goal_defined');
}

add_action('init', 'ryit_test');
 

 function debug_stuff() {
	echo '<h2>Debug: ' . get_user_meta(1,'ryit_user_fellowship_login_count', true) . '</h2>';
}

add_action('wp_head', 'debug_stuff');

function ryit_reset_counters_admin_user() {
	update_field('ryit_user_fellowship_login_count', 2, 'user_1');
	update_field('ryit_user_initiation_login_count', 2, 'user_1');
	update_user_meta(1,'ryit_user_setup_progress', 2);
}

add_action('init','ryit_reset_counters_admin_user',10000);



function ryit_reset_counters_admin_user() {
	update_user_meta(1,'ryit_user_stretch_goal_start', '20190114');
}

add_action('init','ryit_reset_counters_admin_user',10000);
function dev_function() {
	update_user_meta(1,'ryit_user_stretch_goal_start', '20190114');
}

add_action('init','dev_function',10000);




function dev_function() {
	update_user_meta(1,'ryit_user_stretch_goal_start', '20190114');
}

add_action('init','dev_function',10000);
*/

function ryit_get_profile_navigation($user_id, $fields,$is_ajax=false) {

	if(!$user_id) $user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();

	//START: SET UP NAVIGATION
	$nav_echo = '<nav id="profile-menu" class="clearfix">';

	//Dropdown echo returned for ajax calls
	$dropdown_echo = '<div id="ryit-menu" class="dropdown"><select>';
	$dropdown_echo .= '<option id="inactive" disabled>-- Select --</option>';

	$view_mode = ryit_get_user_view_mode();
	$view_mode = empty($view_mode) ? 'goals' : $view_mode;

	//echo $view_mode;

	if(!empty($_GET['active-section'])) {
		//echo "not empty";
		$active_section = $_GET['active-section'];	
	}
	else {
		if($view_mode == 'ryit') {
			$active_section = 'call-to-adventure';	
		}
		else {
			if(!ryit_is_ajax()) {
				if(ryit_user_is_setting_up($user_id)) {
					$active_section = sanitize_title($fields[ryit_user_get_setup_progress($current_user_id)-1][0]);
				}
				else {
					if($user_id == $current_user_id) {
						$active_section = 'life-assessment';	
					}
					else {
						$active_section = 'vision-mission';
					}
				}
			}
			else {
				if($user_id == $current_user_id) {
					$active_section = 'life-assessment';	
				}
				else {
					$active_section = 'vision-mission';
				}
			}
		}
	}

	$setup_progress = empty(ryit_user_get_setup_progress($user_id)) ? 1 : ryit_user_get_setup_progress($user_id);
	$setup_sequence_length = 5;

	//Menu items to skip on the profiles of other folks
	$others_profiles_menuitem_skip = array('life-assessment');

	$i = 1;
	foreach ($fields as $field) {
		if($i <= $setup_progress || $view_mode != 'goals') {
			//echo $i . '|';
			if($user_id != $current_user_id && in_array(sanitize_title($field[0]), $others_profiles_menuitem_skip)) { $i++; continue; }

			$dropdown_echo .= '<option id="tab-' . sanitize_title($field[0]) . '" value="' . sanitize_title($field[0]) . '"'; 
			$dropdown_echo .= ($active_section == sanitize_title($field[0])) ? 'class="active" selected>' : '>';

			if($setup_progress <= $setup_sequence_length && $view_mode != 'ryit') {
				if($user_id == $current_user_id) {
					$dropdown_echo .= '#' . strval($i) . ': '; //Add Step # prefix to menu options when in the setup sequence
				}
			} 
			$dropdown_echo .= $field[0] . '</option>';
		}
		else {
			break;
		}
		$i++;
	}

	if($user_id == $current_user_id && $setup_progress < $setup_sequence_length) {
		$dropdown_echo .= '<option value="complete-current-step" disabled="disabled">Complete #' . strval($i-1) . ' to progress</option>';
	}

	$dropdown_echo .= '</select></div>';

	if($is_ajax) {
		return $dropdown_echo;
	}

	$nav_echo .= $dropdown_echo;
	$nav_echo .= '<ul class="tabs">';

	if ($user_id == get_current_user_id()) {
		$ui_buttons = array(
			array(
				'edit-account',
				'Edit Account'
			) ,
			array(
				'purchase-history',
				'Purchase History'
			)
		);
		$button_count = count($ui_buttons);

		for ($i = 0;$i < $button_count;$i++) {
			$nav_echo .= '<li id="tab-' . $ui_buttons[$i][0] . '"';
			if ($ui_buttons[$i][0] == $active_section) {
				$nav_echo .= ' class="active"';
			}
			$nav_echo .= '>' . $ui_buttons[$i][1] . '</li>';
		}
	}
	$nav_echo .= '</ul>';
	$nav_echo .= '</nav>';

	return $nav_echo;
}





//Establish templates for dynamically created images
if ( function_exists( 'fly_add_image_size' ) ) {
    fly_add_image_size( 'user_avatar', 300, 300, true );
}


function ryit_user_daily_practice() {
	$user_id = ryit_get_user_ID();
	$echo = get_field('ryit_user_daily_practice_morning', 'user_' . $user_id);
	$echo .= get_field('ryit_user_daily_practice_evening', 'user_' . $user_id);
	return $echo;
}

function ryit_get_profile_stretch_goals($user_id=false) {
	/*
	$echo = '<div id="stretch-goals" style="margin: 0; padding: 0 45px 0 18px;">';
	//$echo .= '<div class="innerwrap clearfix">';
	$echo .= '<p>3-month stretch goals and a daily practice are at the core of your strategy for kicking ass and serving as many people as possible in ways you enjoy.</p><p>The stretch goal and daily practice interfaces are not entirely done, but very close. Stay tuned for more info!';
	//$echo .= '</div>';	
	$echo .= '</div>';

	return $echo;
*/
	if(!$user_id) $user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();

	$goal_categories = array(array('Mind','ryit_user_stretch_goal_mind'), array('Body','ryit_user_stretch_goal_body'),array('People','ryit_user_stretch_goal_people'),array('Purpose','ryit_user_stretch_goal_purpose'));

	foreach ($goal_categories as $goal_cat) {
		$field_obj = get_field_object($goal_cat[1], 'user_' . $user_id);	
		$can_edit = ($user_id == $current_user_id) ? " can-edit" : "";	

		//Measure if goal is fully defined
		$completion_score = 0;
		$completion_score = !empty($field_obj['value']['goal_image']) ? $completion_score += 1 : $completion_score;
		$completion_score = !empty($field_obj['value']['goal_name']) ? $completion_score += 1 : $completion_score;
		$completion_score = !empty($field_obj['value']['goal_area_focus']) ? $completion_score += 1 : $completion_score;
		$completion_score = !empty($field_obj['value']['goal_score_goal'] && $field_obj['value']['goal_score_increase'] != "err") ? $completion_score += 1 : $completion_score;

		if(empty($field_obj['value']['goal_image'])) {
			$image = get_stylesheet_directory_uri() . '/images/crosshair.png';
		}
		else {
			$image = $field_obj['value']['goal_image'];
			$class = "";
		}

		$class = $completion_score < 4 ? ' undefined' : '';
		$class .= !empty($field_obj['value']['goal_image']) ? ' image-defined' : '';
		$class = trim($class);

		if(empty($stretch_goals_echo)) $stretch_goals_echo = '';
		$stretch_goals_echo .= '<div class="stretch-goal closed ' . $class . $can_edit . '" id="cat-' . sanitize_title($goal_cat[0]) . '">';
		$stretch_goals_echo .= '<div class="state-closed">';

		$stretch_goals_echo .= '<div class="image_title" style="background-image: url(' . $image . ');">';
		$stretch_goals_echo .= '<h3><span>' . $field_obj['value']['goal_name'] . '</span></h3>';
		$stretch_goals_echo .= '</div>';
		$stretch_goals_echo .= '<div class="text">';
	
		if(!empty($field_obj['value']['goal_score_start']) && !empty($field_obj['value']['goal_score_goal'])) {
			$score_start = $field_obj['value']['goal_score_start'];
			$score_start = (strlen($field_obj['value']['goal_score_start']) == 1) ? $score_start . '.0' : $score_start;
			$stretch_goals_echo .= '<div class="score">' . $field_obj['value']['goal_score_start'] . '</div>';
			$stretch_goals_echo .= '<img src="' . get_stylesheet_directory_uri() . '/images/arrow-right.png" class="arrow" />';
			$stretch_goals_echo .= '<div class="score">' . $field_obj['value']['goal_score_goal'] . '</div>';
		}

		if($completion_score >= 4) {
			$stretch_goals_echo .= '<div class="completion-indicator complete"><i class="fas fa-check"></i></div>';
		}
		else if ($completion_score <= 3 && $completion_score >= 1) {
			$stretch_goals_echo .= '<div class="completion-indicator in-process"><i class="fas fa-hourglass-half"></i></div>';	
		}
		else {
			$stretch_goals_echo .= '<div class="completion-indicator in-process"><i class="fas fa-hourglass-start"></i></div>';
		}

		$stretch_goals_echo .= '</div>'; // end .text
		$stretch_goals_echo .= '</div>'; // end .state-closed

		if ($user_id == $current_user_id) {
			$settings = array(
				'post_id' => 'user_' . $user_id,
				'html_updated_message' => '',
				'fields' => array(
					$goal_cat[1]
				) ,
				'html_after_fields' => '<input type="hidden" class="active_section" name="_active_section" value="stretch-goals" /><input type="hidden" name="_form_context" value="user_profile" />',
				'form_attributes' => array(
					'class' => 'clearfix'
				),
				'submit_value' => 'Save Stretch Goal',
				//'uploader' => 'basic',
				'id' => 'form-stretch-goal-' . $goal_cat[0]
			);
			ob_start();
			acf_form($settings);
			$form = ob_get_clean();
			$stretch_goals_echo .= $form;
		}
		$stretch_goals_echo .= '</div>';
	} // end foreach

	$echo = ryit_get_fp_javascript('stretch-goals');

	//Create the parent level form that "holds" all the others
	$parent_form_echo = "";
	$fields = array('ryit_user_stretch_goal_title', 'ryit_user_stretch_goal_start');

	$stretch_goal_start = get_field('ryit_user_stretch_goal_start', 'user_' . $user_id);
	$start_timecode = date('Ymd', $stretch_goal_start);
	$server_timecode = date('Ymd', time());

	/*
	if($stretch_goal_initiated = ryit_user_stretch_goal_initiated($current_user_id)) {

	}*/

	//	echo '<h2>Defined? ' . ryit_user_stretch_goal_is_initiated($user_id) . '</h2>';

	if(ryit_user_stretch_goal_validate($field_obj) || ryit_user_stretch_goal_is_initiated($user_id)) {
		$submit_text = 'Unlock Goal';
	}
	else {
		$submit_text = 'Initiate Goal!';	
	}

	if ($user_id == $current_user_id) {
		$settings = array(
			'post_id' => 'user_' . $user_id,
			'html_updated_message' => '',
			'fields' => $fields,
			'submit_value' => $submit_text,
			'id' => 'form-stretch-goal-parent'
		);
		ob_start();
		acf_form($settings);
		$parent_form_echo .= ob_get_clean();
	}
	else { //Substite 'form'. Just add header to the goals, displaying the goal title for visitor to the user's profile page
		$parent_form_echo .= '<div id="stretch-goal-header"><h3>' . get_field('ryit_user_stretch_goal_title', 'user_' . $user_id) . '</h3></div>';
	}

	/* End Parent Form */
	$classes = ryit_stretch_goal_classes($field_obj);
	$classes .= ryit_user_is_setting_up($user_id) ? ' user-is-setting-up' : '';
	$classes .= ($user_id != $current_user_id) ? ' not-my-goal' : '';
	$classes = empty($classes) ? '' : ' class="' . trim($classes) . '"';

	$echo .= '<div id="stretch-goals"' . $classes . '>';
	$echo .= $parent_form_echo;
	$echo .= '<div class="innerwrap clearfix">';
	$echo .= '<a href="#" class="close"><span class="fa fa-times"></span></a>';
	$echo .= $stretch_goals_echo;
	$echo .= '</div>';	
	if($user_id == $current_user_id) {
		$echo .= '<div id="lock-status"><i class="fas fa-lock"></i><i class="fas fa-unlock"></i><i class="fas fa-lock-open"></i></div>';
	}
	$echo .= '<p class="cancel-goal">Stop stretch goal</p>';
	$echo .= '</div>';

	return $echo;
}

function ryit_stretch_goal_classes($field_obj) {
	$user_id = ryit_get_user_ID();
	$stretch_goal_start = get_field('ryit_user_stretch_goal_start', 'user_' . $user_id);
	$start_timecode =date('Ymd', $stretch_goal_start);
	$server_timecode = date('Ymd', time());
	$classes = '';

	if(ryit_user_stretch_goal_validate($user_id)) {
		$classes .= 'goal-defined';
	}
	else {
		$classes .= 'goal-undefined';
	}
	if(ryit_user_stretch_goal_is_initiated($user_id)) {
		$classes .= ' goal-locked';
	}
	if((!empty($start_timecode) && !empty($server_timecode)) && $start_timecode <= $server_timecode) {
		$classes .= ' goal-running';
	}
	if(empty($classes)) {
		return '';
	}
	else {
		return trim($classes);
	}
}

/*

function mytest() {
	echo '<h2>test : ' . ryit_user_stretch_goal_is_initiated($field_obj) . '</h2>';
}

add_action('init', 'mytest');
*/

function ryit_user_subgoal_validate($field_obj, $user_id=false) {
	if(!$user_id) $user_id = ryit_get_user_ID();
	//Measure if goal is fully defined
	$completion_score = 0;
	$completion_score = !empty($field_obj['value']['goal_image']) ? $completion_score += 1 : $completion_score;
	$completion_score = !empty($field_obj['value']['goal_name']) ? $completion_score += 1 : $completion_score;
	$completion_score = !empty($field_obj['value']['goal_area_focus']) ? $completion_score += 1 : $completion_score;
	$completion_score = !empty($field_obj['value']['goal_score_goal'] && $field_obj['value']['goal_score_increase'] != "err") ? $completion_score += 1 : $completion_score;

/*
	$goal_title_defined = (get_field('ryit_user_stretch_goal_title', 'user_' . $user_id) != null);
	$goal_start_defined = (get_field('ryit_user_stretch_goal_start', 'user_' . $user_id) != null);

	$start_date_timecode = get_field('ryit_user_stretch_goal_start', 'user_' . $user_id);
	$server_timecode = date('Ymd', time());

	$goal_in_future = ($start_date_timecode > $server_timecode) ? true : false;
	if(ryit_user_stretch_goal_is_initiated())  { //Goal has begun. Override setting so that a started goal can be redefined
		$goal_in_future = true;
	}
*/
	//if($completion_score >= 4 && $goal_title_defined && $goal_start_defined && $goal_in_future) {
	if($completion_score >= 4) {
		return true;
	}
	else {
		return false;
	}
}

//Check if stretch goal has been successfully established
function ryit_user_stretch_goal_validate($user_id = false) {
	if(!$user_id) $user_id = ryit_get_user_ID();
	$has_mind = (empty(ryit_user_subgoal_validate(get_field_object('ryit_user_stretch_goal_mind', 'user_' . $user_id))) == false);
	$has_body = (empty(ryit_user_subgoal_validate(get_field_object('ryit_user_stretch_goal_body', 'user_' . $user_id))) == false);
	$has_people = (empty(ryit_user_subgoal_validate(get_field_object('ryit_user_stretch_goal_people', 'user_' . $user_id))) == false);
	$has_purpose = (empty(ryit_user_subgoal_validate(get_field_object('ryit_user_stretch_goal_purpose', 'user_' . $user_id))) == false);
	$has_title = (empty(get_field('ryit_user_stretch_goal_title', 'user_' . $user_id) == false));
	$has_date = (empty(get_field('ryit_user_stretch_goal_start', 'user_' . $user_id) == false));

	if($has_mind && $has_body && $has_people && $has_purpose && $has_title && $has_date) {
		return true;
	}
	else {
		return false;
	}
}

add_action('wp_ajax_initiate_stretch_goal', 'ryit_user_stretch_goal_initiate');
// stretch goal start of process
function ryit_user_stretch_goal_initiate() {
	$user_id = get_current_user_id();
	if(ryit_user_stretch_goal_validate()) {
		$stretch_goal_start = get_field('ryit_user_stretch_goal_start', 'user_' . $user_id);
		$start_timecode = strtotime($stretch_goal_start);
		$server_timecode = time();

		if(!empty($stretch_goal_start)) {
			if ($start_timecode < $server_timecode) {
				$datediff = $server_timecode - $start_timecode;
				$days = ceil($datediff / (60 * 60 * 24));
				$response['class'] = 'now';
				$days = 90 - $days;
			}
			else {
				$datediff = $start_timecode - $server_timecode;
				$days = ceil($datediff / (60 * 60 * 24));
				$response['class'] = 'future';
			}

			$response['days'] = $days;
			$response['pct'] = 100 * ($days/90);
		}
		else {
			$response['check'] = 'error';
			die();
		}

		update_user_meta($user_id,'ryit_user_stretch_goal_initiated',true);
		ryit_user_update_setup_progress($user_id);
		$response['check'] = true;
	}
	else {
		update_user_meta($user_id,'ryit_user_stretch_goal_initiated',false);
		$response['check'] = false;
	}
	wp_send_json_success($response);
	die();
}


function ryit_user_stretch_goal_is_initiated($user_id=false) {
	if(!$user_id) $user_id = ryit_get_user_ID();
	$check = get_user_meta($user_id,'ryit_user_stretch_goal_initiated',true);

	if(empty($check)) {
		return false;
	}
	else {
		return true;
	}
}

add_action('wp_ajax_ryit_stop_stretch_goal', 'ryit_user_stop_stretch_goal');

function ryit_user_stop_stretch_goal() {
	$user_id = $_GET['user_id'];
	if(empty($user_id)) {
		return "error";
		die();
	}
	else {
		update_field('ryit_user_stretch_goal_start',false,'user_' . $user_id);
		update_user_meta($user_id,'ryit_user_stretch_goal_initiated',false);
		return true;
		die();
	}
}

function ryit_is_ajax() {
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {	
		return false;
	}
	else {
		return true;
	}
}


function ryit_get_profile_sidebar($user_id=false) {
	//error_reporting(E_ALL);
	if(!$user_id) $user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();

	$profile_image = get_field('ryit_user_profile_image', 'user_' . $user_id);
	$country = get_field('ryit_user_profile_country', 'user_' . $user_id);
	$state = get_field('ryit_user_profile_state', 'user_' . $user_id);
	$city = get_field('ryit_user_profile_city	', 'user_' . $user_id);
	$dob = get_field('ryit_user_profile_birthdate', 'user_' . $user_id);
	$triad = get_field('field_5bdc3a83b2f62', 'user_' . $user_id);

	//Start Sidebar
	//if (!alumnus_sidebar_empty($user_id) || $user_id == $current_user_id) {
		
		$echo = '<div id="sidebar">';
		$echo .= '<div id="user-data">';
		
		/* Set up profile fields batch #1 */
		if (!empty($profile_image)) {
			$attachment_id = attachment_url_to_postid($profile_image);
			$resized_image = fly_get_attachment_image($attachment_id, 'user_avatar');
			$echo .= '<div class="portrait">' . $resized_image . '</div>';
		}
		else {
			$default_avatar_attachment_id = 60777;
			$resized_image = fly_get_attachment_image($default_avatar_attachment_id, 'user_avatar');
			$echo .= '<div class="portrait default">' . $resized_image . '</div>';
		}

		$user_data = get_userdata($user_id);


		//-------- ADD I --------

		$echo .= ryit_user_get_badges($user_id);

		$legend_echo = "";
		$intensity = get_field('user_ryit0_intensity', 'user_' . $user_id);
		$intensity = $intensity['value'];
		if (!empty($intensity) && ryit_user_is_current($user_id)) {
			$legend_echo .= '<div id="legend-intensity" class="' . $intensity . '"></div>';
		}
	
		if (!empty($legend_echo)) { 
			$echo .= '<div id="legend">' . $legend_echo . '</div>';
		}

		/* Set up profile fields batch #2 */
		$echo .= '<ul class="personal-data">';
			
		if (!empty($country)) {
			$echo .= '<li class="country"><p class="description">Country</p><p class="data">' . $country . '</p></li>';
		}

		if (!empty($state)) {
			$echo .= '<li class="state"><p class="description">State</p><p class="data">' . $state . '</p></li>';
		}

		if (!empty($city)) {
			$echo .= '<li class="city"><p class="description">City</p><p class="data">' . $city . '</p></li>';
		}

		if (!empty($dob)) {
			$date = explode("/", $dob);
			$time = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			$echo .= '<li class="dob"><p class="description">Date of birth</p><p class="data">' . date('F d, Y', $time) . '</p></li>';
		}

		$echo .= '<li class="email"><p class="description">E-mail</p><p class="data"><a href="mailto:' . $user_data->user_email . '">' . $user_data->user_email . '</a></p></li>';

		if(ryit_user_stretch_goal_is_initiated()) {
			$stretch_goal_start = get_field('ryit_user_stretch_goal_start', 'user_' . $user_id);
			//echo 'time-test ' . $stretch_goal_start;
			$start_timecode = strtotime($stretch_goal_start);
			$server_timecode = time();

			if(!empty($stretch_goal_start)) {
				if ($start_timecode < $server_timecode) {
					$datediff = $server_timecode - $start_timecode;
					$days = ceil($datediff / (60 * 60 * 24));
					$class_time = ' now';
					$days = 90 - $days;
				}
				else {
					$datediff = $start_timecode - $server_timecode;
					$days = ceil($datediff / (60 * 60 * 24));
					$class_time = ' future';
				}

				$pct = 100 * ($days/90);

				$echo .= '<li class="goal' . $class_time . '"><p class="description">Stretch Goal</p><span class="icon"></span><div class="timescale" days="' . $days . '"><span class="time"></span></div><span class="days"></span></li>';
			}
		}

		//Certificates
		if($user_id == $current_user_id){
			$round_number = get_field('ryit_round_number', 'user_' . $user_id);
			$upload_dir = wp_get_upload_dir();
			$certificate = '/ryit-certificates/round-' . $round_number . '/certificate-of-sovereignty-ID_' . $user_id . '.pdf';
			$certificate_check = file_exists($upload_dir['basedir'] . $certificate);
			if($certificate_check) {
				$echo .= '<li class="certificate"><p class="description">RYIT Certificate</p><p class="data"><a href="' . $upload_dir['baseurl'] . $certificate . '" target="_blank">Download your Certificate</a></p></li>';
			}
		}

		$echo .= ryit_user_get_assessment_results($user_id);

		$echo .= '</ul>';

		
		if (!empty($triad) && get_field('ryit_current_week', 'options') >= 3) {
			$post_id = $triad->ID;
			$echo .= '<div id="banner"><div id="banner-symbol-bg" style="background-color:' . get_field('ryit_triad_color', $post_id) . '"><div id="banner-symbol-texture"></div><div id="banner-symbol-texture-layer2"></div><div id="banner-symbol" style="background-image: url(' . get_field('ryit_triad_banner_symbol', $post_id) . ')"></div></div>';
			$echo .= '<h3 id="banner-name">' . get_the_title($post_id) . '</h3>';
			$echo .= '</div>';

			$alumni = rcp_get_members('free', 2, 0, 999999, 'ASC');
			$current = rcp_get_members('free', 3, 0, 999999, 'ASC');
			$users = array_merge($alumni, $current);

			foreach ($users as $user) {
				if (!isset($triad_members)) {
					$triad_members = array();
				}
				$triad = get_field('user_ryit_triad', 'user_' . $user->ID);
				if ($user->ID == 84) continue; //skip dev account
				if ($triad->ID == $post_id) {
					$triad_members[] = $user->ID;
				}
			}

			foreach ($triad_members as $member_id) {
				$user_info = get_userdata($member_id);
				$triad_member_html .= '<div class="triad-member">';
				$triad_member_html .= '<a href="/user-profile?user_id=' . $user_info->ID . '"><div class="portrait" style="background-image: url(' . get_field("ryit_user_profile_image", "user_" . $member_id) . ')"></div>';
				$triad_member_html .= '<h4>' . $user_info->first_name . ' ' . $user_info->last_name . '</a></h4>';
				$triad_member_html .= '</div>';
			}

			if (count($triad_members) < 3 && $user_id == $current_user_id) {
				$triad_member_html = '<div class="triad-members">' . $triad_member_html . '<p style="text-align: center;">Your triad is not complete. Go <a href="/the-brotherhood">claim your triad members</a>.</div>';
			}
			else {
				$triad_member_html = '<div class="triad-members">' . $triad_member_html . '</div>';
			}
		}
	
		if ($user_id == $current_user_id) {
			$echo .= '<div id="edit-user-data" class="button" style="margin-bottom: 30px;">Edit profile data</div>';
		}

		$echo .= '</div>'; /* End <div id="user-data"> */
		$args = array('triad_member_html'=>$triad_member_html, 'post_id'=>$post_id);
		$echo .= ryit_get_fp_javascript('sidebar',$args);
	//}

	//Sidebar when edited
	if ($user_id == $current_user_id) {
		$echo .= '<div id="user-data-form-wrapper">';

		$fields = array(
			'ryit_user_profile_image', //image
			'ryit_user_profile_country', //country
			'ryit_user_profile_state', //state
			'ryit_user_profile_city', //city
			'ryit_user_profile_birthdate', //birthday
			'ryit_user_hide_assessment_results', //privacy
			'field_5bdc3a83b2f62' //triad
		);

		$settings = array(
			'post_id' => 'user_' . $user_id,
			'html_updated_message' => '',
			'fields' => $fields,
			'id' => 'user-data-form'
		);

		ob_start();
		acf_form($settings);
		$echo .= ob_get_clean();
		$echo .= '<div class="button cancel" id="edit-user-data-cancel">Cancel</div>';
		$echo .= '</div>'; //end form wrapper
		
	}

	$echo .= '</div>'; //end sidebar	

	//$echo .= '</div>';

	return $echo;
}


function ryit_user_get_badges($user_id) {
	$user_data = get_userdata($user_id);
	$echo = '';
	$echo .= '<div id="user-badges"><div class="innerwrap">';
	$echo .= '<ul class="clearfix">';

	if(ryit_user_is_current($user_id) || ryit_user_is_alumnus($user_id) || (ryit_user_is_current($user_id) && user_can($user_id,'edit_pages'))) {
		$echo .= '<li id="ryit-badge" class="active">';
		$user_round_number = get_field('ryit_round_number', 'user_' . $user_id);
		$curr_round_number = get_field('ryit_round_number', 'options');
		
		if(($user_round_number < $curr_round_number) && $user_id != 1) {
			$word = "was";
		}
		else {
			$word = "is";
		}

		if($user_id == 1) {
			$sentence = ' the founder of Reclaim your Inner Throne.';
		}
		else {
			$sentence = ' a participant in Reclaim your Inner Throne round ' . $user_round_number;
		}
		$echo .= '<div class="badge-info">' . $user_data->first_name . ' ' . $word . $sentence .'</div>';
		$echo .= '<div class="image_wrap"><img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-ryit.png" /></div>';
		$echo .= '</li>';
	}

	$trainings = get_field('ryit_user_trainings_participated', 'user_' . $user_id);

	if(in_array('uima', $trainings)) {
		$echo .= '<li id="fellowship-academy" class="active">';

		//echo "level " . $fellowship_level;
		$echo .= '<div class="badge-info">' . $user_data->first_name .' has participated in an Inner Throne Academy training</div>';
		$echo .= '<div class="image_wrap"><img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-academy.png" /></div>';
		$echo .= '</li>';
	}

	$fellowship_level = ryit_user_get_fellowship_level($user_id);

	//echo $fellowship_level;

	if(!empty($fellowship_level)) {
		$echo .= '<li id="fellowship-level" class="active">';

		//echo "level " . $fellowship_level;
		$echo .= '<div class="badge-info">' . $user_data->first_name .' is a ' . $fellowship_level . '-level member of the Fellowship</div>';
		$echo .= '<div class="image_wrap"><img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-' . $fellowship_level . '.png" /></div>';
		$echo .= '</li>';
	}

	$trainings = get_field('ryit_user_trainings_participated', 'user_' . $user_id);
	if(in_array('coaching', $trainings)) {
		$echo .= '<li id="fellowship-coaching" class="active">';

		//echo "level " . $fellowship_level;
		$echo .= '<div class="badge-info">' . $user_data->first_name .' has received coaching from a Reclaim your Inner Throne coach </div>';
		$echo .= '<div class="image_wrap"><img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-coaching.png" /></div>';
		$echo .= '</li>';
	}


	if(ryit_user_stretch_goal_is_initiated()) {
		$stretch_goal_start = get_field('ryit_user_stretch_goal_start', 'user_' . $user_id);

		if(!empty($stretch_goal_start)) {
			$server_timecode = date('Ymd', time());

			$echo .= '<li id="stretch-goal" class="active">';

			if($stretch_goal_start > $server_timecode) {
				$start_date_timecode = strtotime($stretch_goal_start);
				$text = 'which starts on ' . date('M j Y', $start_date_timecode); 
			}
			else {
				$start_date_timecode = strtotime($stretch_goal_start);
				$text = 'which started on ' . date('M j Y', $start_date_timecode); 
			}

			//echo "level " . $fellowship_level;
			$echo .= '<div class="badge-info">' . $user_data->first_name .' has initiated a stretch goal ' . $text . '</div>';
			$echo .= '<div class="image_wrap"><img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-stretch-goal.png" /></div>';
			$echo .= '</li>';		
		}
	}

	$echo .= '</ul>';
	$echo .= '</div></div>';
	return $echo;
}



function ryit_get_profile_field_values($field_id = false,$user_id = false,$active_section=false) {
	$field_has_value = false;
	if(empty($field_id)) return false;
	if(!$user_id) $user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();
	$field_echo = "";
	
	if(strpos($field_id, 'function') !== false) { //Enable functions to be called instead of fields. Possibly a redundant function at this stage
		$function = substr($field, 9);
		//$field_echo .= call_user_func($function);
	}
	else {
		//get essential profile data
		$field_obj = get_field_object($field_id, 'user_' . $user_id);

/*
		if($field_id == 'field_5c7bc6a93c8db') {
			echo "group";
			var_dump($field_obj['label']);
		}
*/


		if (!empty($field_obj['value']) || ($user_id == $current_user_id) || $field_obj['type'] == "group") {
			$field_has_value = true;

			$can_edit = ($user_id == $current_user_id) ? " can-edit" : "";
			$is_message = ($field_obj['type'] == "message") ? " message" : "";
			$field_echo .= '<div id="' . $field_obj['name'] . '" class="field' . $can_edit . $is_message . '">';
			$field_echo .= '<h3>' . $field_obj['label'] . '</h3>';
			$field_echo .= '<div class="field-data">';
			$is_group = false;		

			if (empty($field_obj['value']) && ($user_id == $current_user_id) || $field_obj['type'] == "group") { //Field does not have a value assigned
				if ($field_obj['type'] == "message") {
					$field_echo .= '<div class="message"><p>' . nl2br($field_obj['message']) . '</p></div>';
				}
				else {
					$field_echo .= '<div class="field-content"><p><em style="color: rgb(75,115,115); font-weight: bold;">Instructions</em>: ' . $field_obj['instructions'] . '</p><span class="add-response button">Add your response</span></div>';
				}
			}
			else { //Field does have a value
				if(is_array($field_obj['value'])) { //Repeater field
					$i = 0;
					$val_output = "";
					$val = $field_obj['value'];

					if (!empty($val['label'])) { //Select field stored as $arrayName = array('' => , );
						$val_output .= $val['label'];
					}
					else {
						foreach ($field_obj['value'] as $val) {
							if (is_array($val)) { //repeater field
								if (key($val) == "group") { //group inside repeater field
									$is_group = true;
									$group = current($val);
									$val_output .= '<div class="content-group"><h4>' . current($group) . '</h4>'; //headline
									$val_output .= next($group) . '</div>';
								}
								else { //Not a group field
									if (empty($val_output)) $val_output .= "<ol>";
									if (is_array($val)) {
										$output_temp = current($val);
										if (is_array($output_temp)) {
											$val_output .= "<li>" . current($output_temp) . " : " . next($output_temp) . "</li>";
										}
										else {
											$val_output .= "<li>" . current($val) . "</li>";
										}
									}
									else {
										$val_output .= "<li>" . $val . "</li>";
									}
								}
							}
							else {
								if ($i > 0) {
									$val_output .= ", " . $val;
								}
								else {
									$val_output .= $val;
								}
							}
							$i++;
						}

						if(!$is_group) {
							$val_output = '<ol>' . $val_output . '</ol>';
						}
					}
					$field_echo .= '<div class="field-content">' . $val_output . '</div>';
				}
				else { //Simple text field
					$field_echo .= '<div class="field-content">' . $field_obj['value'] . '</div>';
				}
			}

/*
			if(ryit_user_is_setting_up($current_user_id)) {
				unset($active_section);
			}
*/
			//set up form
			if ($user_id == $current_user_id) {
				$settings = array(
					'post_id' => 'user_' . $user_id,
					'html_updated_message' => '',
					'fields' => array(
						$field_id
					) ,
					'html_after_fields' => '<input type="hidden" class="active_section" name="_active_section" value="' . $active_section . '" /><input type="hidden" name="_form_context" value="user_profile" /><input type="hidden" class="_form_id" name="_form_id" value="form_' . $field_obj['name'] . '" />',
					'form_attributes' => array(
						'class' => 'clearfix'
					) ,
					'id' => 'form_' . $field_obj['name']
				);
				ob_start();
				acf_form($settings);
				$form = ob_get_clean();
				$field_echo .= $form;
			}

			$field_echo .= '</div>'; //end field-data
			$field_echo .= '</div>'; //end
		}	

		if($field_has_value) {
			$field_data['has_value'] = true;
		}

		$field_data['output'] = $field_echo;
		
		return $field_data;
	}
}


function ryit_user_get_assessment_results($user_id=false) {
	//Function for use in the sidebar

	if(!$user_id) $user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();

	//Determine whether to show life assessment results or not
	$assessment_visibility = get_field('field_5c10ddb1887d4', 'user_' . $user_id);
	if ($assessment_visibility == "hide_outside_triad") {
		$triad_name_user = get_field('field_5bdc3a83b2f62', 'user_' . $user_id);
		$triad_name_user = $triad_name_user->post_name;
		$triad_name_curruser = get_field('field_5bdc3a83b2f62', 'user_' . $current_user_id);
		$triad_name_curruser = $triad_name_curruser->post_name;
		if ($triad_name_curruser == $triad_name_curruser) {
			$show_assessment_results = true;
		}
		else {
			$show_assessment_results = false;
		}
	}
	else if ($assessment_visibility == "hide_outside_round") {
		$user_round = get_field('field_5b6c4fe1e4873', 'user_' . $user_id);
		$curruser_round = get_field('field_5b6c4fe1e4873', 'user_' . $current_user_id);
		if ($user_round == $curruser_round) {
			$show_assessment_results = true;
		}
		else {
			$show_assessment_results = false;
		}
	}
	else if ($assessment_visibility == "hide") {
		if ($user_id == $current_user_id) {
			$show_assessment_results = true;
		}
		else {
			$show_assessment_results = false;
		}
	}
	else {
		$show_assessment_results = true;
	}

	//Always show to team members
	if (current_user_can('edit_pages')) {
		$show_assessment_results = true;
	}

	if ($assessment_visibility != 'hide') {
		/* Life assessment output */
		$total_avg = get_user_meta($user_id, 'ryit_user_life_assessment_total_average', true);
		$mind_avg = get_user_meta($user_id, 'ryit_user_life_assessment_mind_average', true);
		$body_avg = get_user_meta($user_id, 'ryit_user_life_assessment_body_average', true);
		$people_avg = get_user_meta($user_id, 'ryit_user_life_assessment_people_average', true);
		$purpose_avg = get_user_meta($user_id, 'ryit_user_life_assessment_purpose_average', true);

		$echo .= '<li id="life-assessment">';
		if (!empty(get_user_meta($user_id, 'ryit_user_life_assessment_total_average'))) {
			$echo .= '<p class="description">Life assessment</p>';
			$echo .= '<ul>';
			if (!empty($mind_avg)) {
				$echo .= '<li><span>' . $mind_avg . '</span><p>Mind</p></li>';
			}
			if (!empty($body_avg)) {
				$echo .= '<li><span>' . $body_avg . '</span><p>Body</p></li>';
			}
			if (!empty($people_avg)) {
				$echo .= '<li><span>' . $people_avg . '</span><p>People</p></li>';
			}
			if (!empty($purpose_avg)) {
				$echo .= '<li><span>' . $purpose_avg . '</span><p>Purpose</p></li>';
			}
			if (!empty($total_avg)) {
				$echo .= '<li><span>' . $total_avg . '</span><p>Average</p></li>';
			}
			if($user_id == $current_user_id && !ryit_user_is_setting_up($get_current_user_id)) {
				$echo .= '<li class="redo"><a href="/user-profile?active-section=life-assessment"><span><i class="fas fa-redo"></i></span><p>Redo</p></a></li>';
			}

			//echo '<li><span style="background-color: rgba(100, 140, 140, 1);">1-5</span><p>Scale is:</p></li>';
			$echo .= '</ul>';
		}
		$echo .= '</li>';
	}

	return $echo;
}

function ryit_user_life_assessment() {
	$user_id = ryit_get_user_ID();
	$current_user_id = get_current_user_id();
	$assessment_tool = get_field('ryit_life_assessment_tool', 'options');

	ob_start();
?>

	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/rangeslider.js-2.3.0/rangeslider.min.js"></script>
	<script type="text/javascript">
		jQuery('document').ready(function($j) {
			$j('input[type="range"]').rangeslider({
			   polyfill : false/*,
			   onInit : function() {
				this.output = $( '<div class="range-output" />' ).insertAfter( this.$range ).html( this.$element.val() );
			   },
			   onSlide : function( position, value ) {
				this.output.html( value );
			}*/
			});

			$j('input[type="range"]').val(3).change(); //ensure that slider is drawn correctly
			$j('input[type="range"]').rangeslider('update', true);

			var dimension_group = $j('#dimension-groups > div:first-of-type');
			var is_first_dimension = true; //track if it's the first dimension in the group
			var dimension;
			var dimension_next;
			var group_metrics = "";
			var full_metrics = "";

			$j('#progress_button').on('click', function() {
				var button = $j(this);
				if(button.hasClass('disabled')) { console.log('disabled'); return; } //cancel function
				button.addClass('disabled'); //Button temporarily inactive to prevent bugs
				if($j('#assessment-intro').length > 0) { //Intro is showing. Fade it out and show the first assessment group
					$j('#progress_button').removeClass('disabled');
					button.text('Continue'); //update button text
					$j('#assessment-intro').fadeOut(300, function(){
						$j('#assessment-intro').remove(); //remove general intro slide
						dimension_group.addClass('intro show'); //show dimension group intro slide
						update_menu_highlight(dimension_group.children('h2').text().toLowerCase());
					});
				} 
				else {
					show_next_dimension(dimension_group);
				}
			});

			function update_menu_highlight(dim_group) {
				$j('#life-assessment-tool ul li').removeClass('active');
				$j('#life-assessment-tool ul').find('.' + dim_group).addClass('active');
			}

			function show_next_dimension(dim_group) {
				dimension = dim_group.children('div:first-of-type');
				dimension_next = dimension.next('.dimension');

				//Store value
				if(!dimension_group.hasClass('intro')) {
					group_metrics += dimension.find('span.var-name').text() + "," + dimension.find('input').val().toString() + "|";
				}

				if(dimension_next.length <= 0) { //last dimension in group, so switch groups and continue
					dimension.fadeOut(500, function() {
						$j('#progress_button').removeClass('disabled'); //enable button again

						dimension.remove();
						dim_group.remove();
						$j('#progress_button').text('Start section'); //update button text

						//Handle metric strings
						group_metrics = group_metrics.slice(0,-1); //remove last comma in string
						if($j('#dimension-groups div').length > 0) { //More groups exist
							full_metrics += group_metrics + ";";
						}
						else {
							full_metrics += group_metrics;
						}

						group_metrics = "";

						if($j('#dimension-groups div').length > 0) { //More groups exist
							dimension_group = $j('#dimension-groups > div:first-of-type');
							dimension_group.addClass('show intro');
							update_menu_highlight(dimension_group.children('h2').text().toLowerCase());
							is_first_dimension = true;
						}
						else { //This was the last group - complete assessment 
							var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

							var data = {
								action: 'save_assessment_results',
								full_metrics: full_metrics,
								user_id: $j('body').attr('user_id')
							};

							$j('#progress_button').fadeOut(500);

							//Remove existing popup from source, if it exists
							hide_popup();

							//Create new popup
							show_popup('<h3>Saving assessment results</h3>','assessment');

							$j.ajax({
								url: ajaxurl,
								type: 'GET', // the kind of data we are sending
								data: data,        
								dataType: 'json',
								success: function(response) {
									show_popup('<h3>Results successfully saved</h3><p><strong>Note</strong>: You can edit who can see your results by clicking "Edit profile data" in the sidebar</p><p>Page will reload to display your results in a moment...</p>');
									setTimeout(
										function() {
											location.reload();
										}, 5000);
								}, error: function() {
									show_popup('<h3>Oops! We couldn\'t save your data.</h3><p>Please contact <a href="https://innerthrone.kartra.com/help/helpdesk" target="_blank">Helpdesk</a></p>');
								}
							}); 
						}
					});
				}
				else {
					if(is_first_dimension && dim_group.hasClass('intro')) {
							dim_group.fadeTo(500, 0, function() { //Fade out group title
							$j('#progress_button').removeClass('disabled'); //enable button again
							switch_dimension_text();
							dim_group.removeClass('intro');
							dim_group.fadeTo(500, 1);
							dimension.addClass('show');
						});
					}
					else { //Normal dimension iteration
						dim_group.fadeTo(500,0, function() {
							$j('#progress_button').removeClass('disabled'); //enable button again
							dimension.remove();
							if(dimension_next.length > 0) {
								dimension = dimension_next;
								switch_dimension_text();
								dimension.addClass('show');
								dim_group.fadeTo(500,1);
								is_first_dimension = false;
							}
						});
					}
				}

				function switch_dimension_text() {
					/* SWITCH DIMENSION HEADLINE & DESCRIPTION */

					var dimension_headline = dimension_group.children('h2');
					var dimension_description = dimension_group.children('.description');

					//Switch dimension headline
					dimension_headline.text(dimension.children('h3').text());
					dimension_description.text(dimension.children('.description').text());

					if(dim_group.find('.dimension').length <= 1) {
					$j('#progress_button').text('Complete section'); //update button text
				}
				else {
					$j('#progress_button').text('Continue'); //update button text
				}
			}
		}
	});
	</script>

	<?php
	$script = ob_get_clean();
	$echo = $script;

	$echo .= '<div id="life-assessment-tool">';
	$echo .= '<div id="assessment-tool-header"><h3>Life Assessment Tool</h3><ul><li class="mind">Mind</li><li class="body">Body</li><li class="people">People</li><li class="purpose">Purpose</li></ul></div>';
	//$echo .= '<img src="' . get_stylesheet_directory_uri() . '/images/four-quadrant-assessment-circle.png" id="quadrants">';
	$echo .= '<div id="assessment-intro">';
	$echo .= '<div class="maxwidth-650"><h2>Assess the Quality of your Life</h2><p>Using this simple assessment tool, you can establish a clear idea of how you are doing in your life. You will be assessing yourself in the four dimensions of Mind, Body, People & Purpose, which is the official coaching framework of Reclaim your Inner Throne*.</p><p>Based on these metrics, you will be able to utilize our other tools and track your progress. What you track improves and so this is a simple yet crucial step on your journey.</p><blockquote>If you can\'t measure it, you can\'t improve it. &ndash; Peter Drucker</blockquote><p style="font-style: italic; color: #aaa; font-size: 0.9em;"><sup>*</sup>A Coaching framework is about goals and direction, and is drastically different to an initiation framework which is about death and rebirth. Though in effect coaching <em>can</em> inadvertently <em>become</em> an initiation, but it\'s not intended as such from the outset.</div>';
	$echo .= '</div>';
	$echo .= '<div id="dimension-groups">';
	foreach ($assessment_tool as $group) {
		$echo .= '<div class="dimension-group">';
		$echo .= '<h2>' . $group['group_name'] . '</h2>';
		$echo .= '<p class="description">' . $group['group_description'] . '</p>';
		$dimensions = $group['dimensions'];
		foreach ($dimensions as $dimension) {
			$dimension = $dimension['dimension'];
			$echo .= '<div class="dimension"><h3 style="display: none;">' . $dimension['dimension_name'] . '</h3>';
			$echo .= '<div class="description" style="display: none;">' . $dimension['dimension_description'] . '</div>';
			$echo .= '<div class="slider"><input type="range" min="1" max="5" step="1" value="3" data-rangeslider role="input-range"></input>';
			$echo .= '<ul class="scale-steps"><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li></ul>';
			$echo .= '</div>';
			$echo .= '<p class="min-text">' . $dimension['min_text'] . '</p>';
			$echo .= '<p class="max-text">' . $dimension['max_text'] . '</p>';
			$echo .= '<span class="var-name" style="display: none;">' . $dimension['dimension_var_name'] . '</span>';
			$echo .= '</div>';
		}
		$echo .= '</div>';
	}
	$echo .= '</div>';

	if($user_id == $current_user_id) {
		$echo .= '<div id="progress_button" class="button simple">Start the Assessment</div>';
	}
	$echo .= '</div>';
	return $echo;
}

add_shortcode('life_assessment', 'ryit_user_life_assessment');

//Ensure that active section is retained when submitting form
function my_acf_pre_submit_form($form) {
	if ($_POST['_form_context'] == "user_profile") {
		//Handle active section
		$active_section = $_POST['_active_section'];//strpos($form['return'], 'active-section');
		if(!ryit_user_is_setting_up(get_current_user_id())) {
			$form['return'] = '?active-section=' . $active_section;
		}
	}
	// return
	return $form;
}

add_filter('acf/pre_submit_form', 'my_acf_pre_submit_form', 10, 1);


function ryit_validate_fields($fields) {
	foreach($fields as $field) {
		$value = get_field($field, 'user_' . get_current_user_id());
		if(empty($value)) {
			return false;
		}
	}
	return true;
}

//add_action('wp_head','ryit_validate_fields');

function my_acf_submit_form($form) {
	$user_id = get_current_user_id();
	//Process forms on the Fellowship Portal
	if(is_fellowship_page()) {
		$active_section = $_POST['_active_section'];//strpos($form['return'], 'active-section');

		if($active_section == 'vision-mission') {
			if(ryit_validate_fields(array('ryit_user_mission', 'ryit_user_vision'))) {
				if(ryit_user_is_setting_up($user_id)) {
					ryit_user_update_setup_progress($user_id);
				}
			}
		}
		else if($active_section == 'long-term-goals') {
			if(ryit_validate_fields(array('ryit_user_goal_ten_year', 'ryit_user_goal_three_year','ryit_user_goal_one_year'))) {
				if(ryit_user_is_setting_up($user_id)) {
					ryit_user_update_setup_progress($user_id);
				}
			}
		}
		else if($active_section == 'stretch-goals') {
			if(ryit_user_stretch_goal_validate($user_id)) {
				update_user_meta($user_id,'ryit_user_stretch_goal_initiated',true);
				if(ryit_user_is_setting_up($user_id) && !ryit_user_stretch_goal_is_initiated($user_id)) {
					ryit_user_update_setup_progress($user_id);
				}
			}
			else {
				update_user_meta($user_id,'ryit_user_stretch_goal_initiated',false);
			}			
		}


		/* WHY DOESN'T SWITCH WORK? DEBUG LATER
		switch($active_section) {
			case 'vision-mission' :
				if(ryit_validate_fields(array('ryit_user_mission', 'ryit_user_vision'))) {
					//update_user_meta(1,'ryit_user_debug_field','Fields defined');
					ryit_user_update_setup_progress(get_current_user_id());
				}
				else {
					//update_user_meta(1,'ryit_user_debug_field','Fields not defined');
				}
				break;
			case 'long-term-goals' :
				if(ryit_validate_fields(array('ryit_user_goal_ten_year', 'ryit_user_goal_five_year','ryit_user_goal_three_year','ryit_user_goal_one_year'))) {
					ryit_user_update_setup_progress(get_current_user_id());
				}
				break;
		}*/
	}
}

add_filter('acf/submit_form', 'my_acf_submit_form', 10, 1);



add_action('wp_ajax_save_assessment_results', 'ryit_save_assessment_results');

function ryit_save_assessment_results() {
	$full_metrics = $_REQUEST['full_metrics'];
	$full_metrics = explode(';', $full_metrics);
	$metric_sum = 0;
	$total_avg = 0;
	if (!empty($_REQUEST['user_id'])) {
		$user_id = $_REQUEST['user_id'];
	}
	else {
		$user_id = 1;
	}

	for ($i = 0;$i < count($full_metrics);$i++) {
		$group_metrics = $full_metrics[$i];
		$group_metrics = explode('|', $group_metrics);

		foreach ($group_metrics as $metric) {
			$metric = explode(',', $metric);
			update_user_meta($user_id, $metric[0], $metric[1]);
			$metric_sum += intval($metric[1]);
			if (!next($group_metrics)) { //extract if group is Mind,Body,People or Purpose based on variable names
				$var_name = $metric[0];
				$var_name = explode("_", $var_name);
				$var_name = $var_name[4];
			}
		}

		//Calculate averages
		$group_avg = round($metric_sum / count($group_metrics), 1);
		$group_avg = (strlen($group_avg) == 1) ? $group_avg . '.0' : $group_avg;
		$total_avg += $group_avg;

		update_user_meta($user_id, 'ryit_user_life_assessment_' . $var_name . '_average', $group_avg);

		$group_avg = $metric_sum = 0; //Reset counters
		
	}

	$total_avg = round($total_avg / count($full_metrics) , 1);
	$total_avg = (strlen($total_avg) == 1) ? $total_avg . '.0' : $total_avg;
	update_user_meta($user_id, 'ryit_user_life_assessment_total_average', $total_avg);
	
	//If user is setting up account for the first time, move the process ahead
	if(ryit_user_is_setting_up(get_current_user_id())) {
		ryit_user_update_setup_progress(get_current_user_id());
	}
	die();
}


add_action('wp_ajax_update_stretch_goal_title', 'ryit_update_stretch_goal_title');
function ryit_update_stretch_goal_title() {
	$title = $_GET['title'];
	$user_id =  $_GET['user_id'];

	if(empty($title) || empty($user_id)) {
		die();
	}

	//echo "title : " . $title . "     user id: " . $user_id;

	update_field('ryit_user_stretch_goal_title', $title, 'user_' . $user_id);
	die();
}

add_action('wp_ajax_update_stretch_goal_date', 'ryit_update_stretch_goal_date');
function ryit_update_stretch_goal_date() {
	$date = $_GET['date'];
	$user_id =  $_GET['user_id'];

	if(empty($date) || empty($user_id)) {
		die();
	}

	$date = DateTime::createFromFormat('d/m/Y', $date);
	$date = $date->format('Ymd');

	update_field('ryit_user_stretch_goal_start', $date, 'user_' . $user_id);
	die();
}

function ryit_get_fp_javascript($context = false,$args=false) {
	$user_id = ryit_get_user_ID();

	if(empty($context)) {
		return;
	}

	if($context == 'sidebar') :
		if($args) {
			$triad_member_html = $args['triad_member_html'];
			$post_id = $args['post_id'];
		}
		ob_start();
		?>
		<script type="text/javascript">   
			jQuery('document').ready(function($j) {
				$j(document).on('click', '#banner', function(e) {
					e.preventDefault();
					show_popup('<div id="popup-banner"><img src="<?php echo get_field('ryit_triad_banner', $post_id); ?>" /></div><div id="popup-content"><h2><?php echo get_the_title($post_id); ?></h2><div id="triad-mission"><?php echo trim(get_field('ryit_triad_mission', $post_id)); ?></div><h3>Members</h3><?php echo $triad_member_html; ?>','triad');
				});

				$j(document).on('click', 'a.close', function(e) {
					e.preventDefault();
					hide_popup();
				});

				$j(document).on('mouseenter', '#user-badges li img', function(e) {
					$j(this).parent().parent().addClass('hover');
				});
				
				$j(document).on('mouseleave', '#user-badges li img', function(e) {
					$j(this).parent().parent().removeClass('hover');
				});


				//Update stretch goal progress meter
				if(!$j('#user-data li.goal').hasClass('future')) {
					var timescale = $j('#sidebar .goal .timescale');
					var time_width = $j('.personal-data li.goal').width() - 30;
					var progress_pct = (timescale.attr('days')/90);
					var time_fill = timescale.find('.time');
					//time_fill.width(time_width - (time_width * (progress_pct)));
					setTimeout(function() { 
						time_fill.animate({
							width: (time_width - (time_width * (progress_pct)))
						}, 2000);
					}, 5000);
				}
			});
		</script>
		<?php
		$echo = ob_get_clean();
	endif;



	if($context == 'user-profile') :
		ob_start();
	 //echo get_user_meta($user_id, 'ryit_user_life_assessment_purpose_average', true);
	?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
			var theme_path = '<?php echo get_stylesheet_directory_uri(); ?>';
			jQuery('document').ready(function($j) {
				$j('body').removeClass('is-loading');
				$j.urlParam = function(name){ //get URL parameter
				    var results = new RegExp('[\?&]' + name + '=([^]*)').exec(window.location.href);
				    if (results==null){
				       return null;
				    }
				    else{
				       return results[1] || 0;
				    }
				}

				$j('#profile .field.can-edit .field-content').append('<div class="edit"><i class="fas fa-pencil-alt"></i></div>');
				$j('#profile .field.can-edit form .acf-form-submit').append('<div class="cancel"><i class="fas fa-times-circle"></i></div>');

				//Disable auto-complete for stretch goal title fields
				$j('.acf-field.goal-name input').attr('autocomplete','off');
				$j('.acf-field.score input').attr('autocomplete','off');
				$j('#form-stretch-goal-parent .acf-field.title input').attr('autocomplete','off');

				set_goal_scores();

 				$j(".acf-image-uploader p").each(function(){
					$j(this).replaceWith($j(this).html().replace("No image selected", ""));
				});

				$j("#stretch-goals .acf-image-uploader .acf-button").text('Upload Goal Image (required)');
	
			});

			function set_goal_scores() {
				$j('.stretch-goal#cat-mind .acf-field.score-current input').val(<?php echo get_user_meta($user_id, 'ryit_user_life_assessment_mind_average', true); ?>);
				$j('.stretch-goal#cat-body .acf-field.score-current input').val(<?php echo get_user_meta($user_id, 'ryit_user_life_assessment_body_average', true); ?>);
				$j('.stretch-goal#cat-people .acf-field.score-current input').val(<?php echo get_user_meta($user_id, 'ryit_user_life_assessment_people_average', true); ?>);
				$j('.stretch-goal#cat-purpose .acf-field.score-current input').val(<?php echo get_user_meta($user_id, 'ryit_user_life_assessment_purpose_average', true); ?>);
			}

			//disable stretch goal score current & increase from interaction
			$j(document).on('focus', '.acf-field.score-current input,.acf-field.score-increase input', function(e) {
				$j(this).blur();
				e.preventDefault();
			});

			$j(document).on('click', '#profile .field.can-edit .field-content', function(e) {
				e.preventDefault();
				$j(this).slideUp(250,function() {
					$j(this).parents('.field').find('form').slideDown(250);  
				});
			});

			$j(document).on('click', '#profile .cancel', function(e) {
				e.preventDefault();
				$j(this).parents('form').slideUp(250,function() {
					$j(this).parents('.field').find('.field-content').slideDown(250);  
				});
			});

			$j(document).on('click', '#edit-user-data', function(e) {
				$j('#user-data').fadeOut(500,function() {
					$j('#user-data-form-wrapper').fadeIn(500);
				})
			});

			$j(document).on('click', '#edit-user-data-cancel', function(e) {
				$j('#user-data-form-wrapper').fadeOut(500,function() {
					$j('#user-data').fadeIn(500);
				})
			});

			$j(document).on('click', '.tabs li', function() {
				$j('.tabs li').removeClass('active');
				$j(this).addClass('active');
				var active_section = $j(this).attr('id');
				active_section = active_section.substring(4);
				$j('.main').attr('active-section',active_section);

				//Update rangeslider (is this needed now?)
				$j('input[type="range"]').rangeslider('update', true);
				$j('input[type="range"]').val(3).change();

				//Unselect dropdown
				$j('#ryit-menu select').prop('selectedIndex', 0);

				//Change address bar
				var stateObj = { foo: "bar" };
				history.pushState(stateObj, "Active field", "?active-section=" + active_section);
				$j('form').attr('return', '?active-section=' + active_section + '&updated=true');

				//Update menu selected var used to keep track of what tab to show after form is submitted
				$j('input.active_section').val(active_section);
			});

			$j(document).on('change', '.dropdown select', function() {
				$j('.tabs li').removeClass('active');
				$j(this).find('option').removeClass('active');
				$j(this).find(':selected').addClass('active');
				var active_section = $j(this).find(':selected').attr('id');
				active_section = active_section.substring(4);
				$j('.main').attr('active-section',active_section);

				//Change address bar
				var stateObj = { foo: "bar" };
				var user_id = $j.urlParam('user_id');
				if(user_id == null)  {
					history.pushState(stateObj, "Active field", "?active-section=" + active_section);
				}
				else {
					history.pushState(stateObj, "Active field", "?active-section=" + active_section + '&user_id=' + user_id);	
				}
				$j('form').attr('return', '?active-section=' + active_section + '&updated=true');

				//Update menu selected var used to keep track of what tab to show after form is submitted
				$j('input.active_section').val(active_section);
			});





			$j(document).on('click', '.field-group > h2 i', function(e) {
				if($j(this).parents('#field-group-vision-mission').length > 0) {
					show_popup('<?php echo ryit_get_help_popup(2,true,false); ?>','help');
				}
				else if($j(this).parents('#field-group-long-term-goals').length > 0) {
					show_popup('<?php echo ryit_get_help_popup(3,false,false); ?>','help');
				}
				else if($j(this).parents('#field-group-stretch-goals').length > 0) {
					show_popup('<?php echo ryit_get_help_popup(4,false,false); ?>','help');
				}
				else if($j(this).parents('#field-group-daily-practice').length > 0) {
					show_popup('<?php echo ryit_get_help_popup(5,false,false); ?>','help');
				}
			/*
				if($j(this).parents('#field-group-vision-mission').length > 0) {
					show_popup('<h3>Vision & Mission help</h3><p>This is the highest level bird\'s-eye view of your purpose and direction in this life.</p><p>You may not yet have real clarity on these two, but you must start <em>somewhere</em>, right?</p><p>If this is hard for you to know, then sit down and write about what you care about. Let the image below guide your hand. And then await the instructions video that will soon arrive from course leader Richard Arsic.</p><img src="/wp-content/uploads/2016/01/purpose-mandala.jpg" />','help');
				}
				else if($j(this).parents('#field-group-stretch-goals').length > 0) {
					show_popup('<h3>The Stretch Goal system</h3><p>This is the core of this platform and it will change your life in dramatic ways if you take it on seriously.</p><p>Define your subgoals in all four dimensions and save them. Give the 3-month stretch goal a name, and define when the stretch goal starts (the system automatically determines when it ends).</p><p>The system will let you know when each subgoal is ready by giving it a green checkmark, and when everything has been done, the submit button will become active and you can pursue the goal like the badass that you are (accountability system is coming).</p><p><p>Good luck!</p>','help');
				}
				else if($j(this).parents('#field-group-long-term-goals').length > 0) {
					show_popup('<h3>Long term Goals</h3><p>Further refine your clarity about where you are going by establishing goals for 10,5,3, and 1-year periods.</p><p>Without clarity, you don\'t know where you\'re going, so this is very important!</p><p>We recommend that you set goals with an eye on the Mind, Body, People, Purpose dimensions!</p>','help');
				}
				else if($j(this).parents('#field-group-daily-practice').length > 0) {
					show_popup('<h3>Daily Practice System</h3><p>In the daily practice system, you will create rituals for your mornings and evenings.</p><h4>For the mornings, here are our recommendations</h4><ul><li>We recommend including at least one option from each of the four Mind, Body, People, Purpose categories</li><li>Body: Do physical exercise & eat a healthy breakfast</li><li>Mind: Read a book and journal</li><li>People: Express love to people you care about and/or send messages to folks you haven\'t connected with in a while</li><li>Purpose: Plan the day/set goals</li></ul><h4>For the evenings, here are our recommendations</h4><ul><li>No purpose work</li><li>Connect with a loved one in a relaxing way, though ONLY if you live under the same roof</li><li>Turn off intense lights and screens minimum one hour prior to bed</li><li>Take a warm shower</li><li>Do some simple stretching/yoga (avoid intense physical exercise)</li><li>Do not do meditation immediately before bed, as it can surprisingly make it harder to sleep (it reduces what is commonly known as sleep pressure)</li></ul><p>Good luck!</p>','help');
				} */
			});

			$j(document).on('click', '#ryit_popup .close', function(e) {
				e.preventDefault();
				$j('#ryit_popup_overlay').remove();
				$j('#ryit_popup').remove();
			});

			$j(document).on('click', '#button-claim-triad-member', function() {
				var data = {
					action: 'email_ping_triad_member',
					user_id: $j('#profile').attr('user_id')
				};

				$j.ajax({
					url: ajaxurl,
					type: 'POST', // the kind of data we are sending
					data: data,        
					dataType: 'json',
					success: function(response) {
						$j('#triad-member-claim .fusion-button').fadeOut(500, function() {
							$j('#triad-member-claim').html('<h2 style="border: none; margin-top: 1em;">Congratulations, the e-mail has been sent successfully!</h2>');
						});
					}
				});
			});


		</script>

		<?php
		$echo = ob_get_clean(); //Set up javascript
	endif;


	if($context == 'member-directory') :
?>

	<script type='text/javascript'>
	jQuery('document').ready(function($j) {
		var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

		$j(document).on('change', '#member_directory_settings #display_type select', function(e) {
			e.preventDefault();
			update_member_directory_view();
		});

		$j(document).on('change', '#member_directory_settings #sort_type select', function(e) {
			e.preventDefault();  
			if($j(this).prop('selectedIndex') == 1) { //If sort type is "RYIT round", change filter to RYIT alumni
				$j('#member_directory_settings #filter_type select').prop('selectedIndex',2); 
			}
			update_member_directory_view();   
		});

		$j(document).on('change', '#member_directory_settings #filter_type select', function(e) {
			e.preventDefault();
			//if($j(this).prop('selectedIndex') == 2) { //If filter type is "RYIT alumni", change sort  to RYIT alumni
				$j('#member_directory_settings #sort_type select').prop('selectedIndex',0); 
			//}
			update_member_directory_view();   
		});

		function update_member_directory_view() {
			var display_val = $j('#display_type select').prop('selectedIndex');
			var sort_val = $j('#sort_type select').prop('selectedIndex');
			var filter_val = $j('#filter_type select').prop('selectedIndex');

			show_popup('loading','fixed',false);

			var data = {
			action: 'member_directory',
				display_type: display_val,
				sort_type: sort_val,
				filter_type: filter_val
			};

			$j.ajax({
				url: ajaxurl,
				type: 'GET', // the kind of data we are sending
				data: data,        
				dataType: 'json',
				success: function(response) {
					$j('#directory_listing').html(response.data.echo);
					$j('#display_type_val').text(response.data.display_type);
					hide_popup();
				}
			});
		}
	});
	</script>

  <?php
	endif;


	if($context == 'stretch-goals') :
		ob_start();
	?>
		<script type="text/javascript">
			jQuery('document').ready(function($j) {
				$j('.stretch-goal').on('click', function() {
					//Close all open stretch goals
					$j('#stretch-goals .stretch-goal').removeClass('open');
					$j('#stretch-goals .stretch-goal').addClass('closed');
					$j('#stretch-goals').addClass('goal-open');

					//Open new stretch goal
					$j(this).addClass('open');
					$j(this).removeClass('closed');
					$j(this).parent().addClass('open');
					$j(this).parent().removeClass('closed');

					$j('form#form-stretch-goal-parent .start-date, form#form-stretch-goal-parent .acf-form-submit').fadeOut();
				});

				$j('#stretch-goals a.close').on('click', function(e) {
					e.preventDefault();
					$j('#stretch-goals').removeClass('goal-open');
					$j('#stretch-goals .stretch-goal').removeClass('open');
					$j('#stretch-goals .stretch-goal').addClass('closed');
					$j(this).parent().addClass('closed');
					$j(this).parent().removeClass('open');
					$j('form#form-stretch-goal-parent .start-date, form#form-stretch-goal-parent .acf-form-submit').fadeIn();
				});
	
				$j(document).on('keydown', '.start-date input', function(e) {
					if(e.keyCode == 46 || e.keyCode == 8) {
						$j(this).val('');
					}
					else {
						e.preventDefault();
					}
				});

				$j(document).on('mousedown','#stretch-goals .title input',function(e) {
					if($j('#stretch-goals').hasClass('goal-locked')) {
						e.preventDefault();
						return false;
					}
				});

				$j(document).on('focusout', '.acf-field.score-goal input', function(e) {
					e.preventDefault();
					
					if($j(this).val().length == 0) {
						$j(this).val("0.0");
					}
					if($j(this).val().length > 0 && $j(this).val().length < 3) {
						$j(this).val($j(this).val() + "0");
					}

					//Update score increment
					var parent = $j(this).parents('.stretch-goal');
					var increment = parseFloat(parent.find('.score-goal input').val() - parent.find('.score-current input').val()).toFixed(1);

					if(increment<0) {
						$j('.acf-field.score-increase input').val('err');
						window.alert("Goal must be equal or higher than current");
					}
					else {
						parent.find('.score-increase input').val(increment);
					}
				});

				$j(document).on('keydown', '.acf-field.score input', function(e) {
					e.preventDefault();
					
					/* requires use of keydown event */
					var charnum = e.which;
					if(charnum >= 96 && charnum <= 105) { //handle numeric keypad
						charnum -= 48;
					}
					console.log(charnum);
					//Backspace = 8, delete = 46
					var delete_keys = new Array(8,46);
					if(delete_keys.indexOf(charnum) != -1) {
						$j(this).val('');
						return;
					}

					var character = String.fromCharCode(charnum);
					
					if($j(this).val().length == 0) { //First letter being typed in
						var valid_numbers = new Array(0,1,2,3,4,5);
						if(valid_numbers.indexOf(Number(character)) != -1) {
							if(character == 5)  {
								$j(this).val(String.fromCharCode(charnum) + ".0");				
							}
							else {
								//console.log("this is it");
								$j(this).val(String.fromCharCode(charnum) + ".");
							}
						}
						else {
							//console.log("character is invalid");
						}
					}
					else {
						//console.log("Second if");
						if($j(this).val().length >= 3) { //Input is full
							return;
						}
						var string = $j(this).val();
						if(string.charAt(0) == "5") {
							var valid_numbers = new Array(0);
						}
						else {
							var valid_numbers = new Array(0,1,2,3,4,5,6,7,8,9);
						}
						if(valid_numbers.indexOf(Number(character)) != -1) {
							if($j(this).val().length == 2) {
								$j(this).val(string + String.fromCharCode(charnum));	

								//Update score increment
								var parent = $j(this).parents('.stretch-goal');
								parent.find('.score-increase input').val(parseFloat(parent.find('.score-goal input').val() - parent.find('.score-current input').val()).toFixed(1));
							}
							else {
								$j(this).val(string + String.fromCharCode(charnum));		
							}
						}
					}
				});

				$j(document).on('focusout','#stretch-goals .title input', function(e) {
					document.body.style.cursor = "progress";
					var data = {
						user_id: $j('body #profile').attr('user_id'),
						action: 'update_stretch_goal_title',
						title: $j(this).val()
					};

					$j.ajax({
						url: ajaxurl,
						type: 'GET', // the kind of data we are sending
						data: data,        
						dataType: 'json',
						success: function(response) {
							update_goal_submit_state();
							document.body.style.cursor = "default";
							//console.log('Title was saved');
						}, error: function() {
							//console.log("Title wasn't saved");
						}
					});
				});

				$j(document).on('mousedown', '#stretch-goals .start-date input.hasDatepicker', function(e) {
					if($j('#stretch-goals').hasClass('goal-locked')) {
						e.preventDefault();
						return false;
					}
				});

				$j(document).on('change', '#stretch-goals .start-date input.hasDatepicker', function(e) {

					document.body.style.cursor = "progress";

					var goal_ready = is_goal_ready();
					if(goal_ready === false) {
						document.body.style.cursor = "default";
						return false;
					}
					else {
						var data = {
							user_id: $j('body #profile').attr('user_id'),
							action: 'update_stretch_goal_date',
							date: $j(this).val()
						};

						$j.ajax({
							url: ajaxurl,
							type: 'GET', // the kind of data we are sending
							data: data,        
							dataType: 'json',
							success: function(response) {
								//console.log('date updated');
								document.body.style.cursor = "default";
								update_goal_submit_state();
							}, error: function() {
								document.body.style.cursor = "default";
								//console.log("Title wasn't saved");
							}
						});	
					}			
				});

				$j(document).on('mousedown', '#stretch-goals input, #stretch-goals textarea, #stretch-goals .acf-actions a, #stretch-goals option, .select2.select2-container', function(e) {
					if($j('#stretch-goals').hasClass('goal-running') && $j('#stretch-goals').hasClass('goal-update') && $j(this).hasClass('hasDatepicker')) { //Prevent date change after goal has started
						e.preventDefault();
						show_popup('Your Goal has started.<br/>Date cannot be changed.', 'fixed', true);
						return false;
					}
					if($j('#stretch-goals').hasClass('goal-locked') && $j(this).attr('type') != 'submit') {
						e.preventDefault();
						show_popup('Please unlock goal to edit','fixed',true);
						return false;
					}
				});

				$j(document).on('mousedown','#stretch-goals p.cancel-goal',function(e) {
					show_popup('<h3>Really stop Stretch Goal?</h3><p>Once you stop your stretch goal, the start date will be erased. <br/>All of your stretch goal specifications will remain, but you can only resume the stretch goal today or in the future.</p><p class="button stop-button" style="margin: 35px auto 20px; display: table; padding: 10px 50px;">Yes, stop stretch goal!</p>','stop-goal-popup',true);
				});

				$j(document).on('mousedown','#ryit-popup.stop-goal-popup .stop-button',function(e) {
					hide_popup();
					show_popup('<img src="' + theme_path + '/images/spinner.gif" class="loader" /><p>Stopping Stretch Goal ...</p>','fixed ui-feedback',false);
					var data = {
						action: 'ryit_stop_stretch_goal',
						user_id: $j('body #profile').attr('user_id')
					};

					$j.ajax({
						url: ajaxurl,
						type: 'GET', // the kind of data we are sending
						data: data,        
						dataType: 'json',
						success: function(response) {
							//console.log('date updated');
							hide_popup();
							show_popup('<p style="margin: 0;">Your Stretch Goal has been stopped</p>','fixed',true,4000);
							$j('#stretch-goals').removeClass();
							$j('#stretch-goals').addClass('goal-undefined');
							$j('#stretch-goals #form-stretch-goal-parent input.button-primary').val('Initiate Goal!');
							$j('#sidebar li.goal').fadeOut().remove();
							$j('#stretch-goals').find('.hasDatepicker').val('');
						}, error: function() {
							show_popup('<p>Oops, something went wrong! Please contact <a href="/helpdesk" target="_blank">our helpdesk</a>.</p>');
						}
					});
				});
				

				$j(document).on('click','#lock-status',function(e) {
					e.preventDefault();
					if($j('#stretch-goals').hasClass('goal-locked')) {
						$j('#stretch-goals').removeClass('goal-locked');
						$j('#stretch-goals').addClass('goal-update');
						$j('#form-stretch-goal-parent > .acf-form-submit input').attr('value','Lock Goal');
					}
					else {
						$j('#stretch-goals').removeClass('goal-update');
						$j('#stretch-goals').addClass('goal-locked');
						$j('#form-stretch-goal-parent > .acf-form-submit input').attr('value','Unlock Goal');
					}
				});

				//Develop click cancellation for iframes as well

				//BELOW: Control whether stretch goal has been fully defined before it can be initiated

				function is_goal_ready(context='default') {
					var completion_check = $j('#stretch-goals .completion-indicator');
					var is_ready = true;
					var ui_message = new Array();
				
					if(completion_check.length < 4) {
						is_ready = false;
					}

					var sub_goals_defined = true;
					completion_check.each(function() {
						var check_status = $j(this).hasClass('complete');

						if(check_status === false) {
							is_ready = false;
							sub_goals_defined = false;
						}
					});

					if(sub_goals_defined === false) {
						ui_message.push('Please define all sub-Goals');
					}

					if(!$j('#stretch-goals').hasClass('goal-running')) {
						var date = $j('form .start-date input.hasDatepicker').val();

						if(date.length === 0) {
							is_ready = false;
							ui_message.push('Date is not defined');
						}
						else {
							var timecode_goal = date.substr(6,4) + date.substr(3,2) + date.substr(0,2);
							var timecode_now = <?php echo date('Ymd', time()); ?>;
							if(timecode_goal < timecode_now) {
								ui_message.push('Date is in the past');
								is_ready = false;
							}
						}
					}

					if($j('.acf-field.title input').val().length <= 0) {
						ui_message.push('Stretch Goal Title not defined');
						is_ready = false;
					}

					if(ui_message.length > 0) {
						var temp_ui_message = '';
						for (i = 0; i< ui_message.length; i++) {
							temp_ui_message += '<li>' + ui_message[i] + '</li>';
						}
						ui_message = '<h3>Fix to enable saving:</h3><ul>' + temp_ui_message + '</ul>';
					}
						
					if(is_ready) {
						$j('#stretch-goals').removeClass('goal-undefined');
						$j('#stretch-goals').addClass('goal-defined');
						if($j('#submit-feedback').length > 0) {
							$j('#submit-feedback').remove();
						}
						return true;
					}
					else {
						if(!$j('#stretch-goals').hasClass('goal-locked')) {
							$j('#stretch-goals').removeClass('goal-defined');
							$j('#stretch-goals').addClass('goal-undefined');
							if($j('#submit-feedback').length > 0) {
								//console.log("first");
								$j('#submit-feedback').html('<div class="innerwrap">' + ui_message + '</div>');
							}
							else {
								//console.log("second");
								$j('body').append('<div id="submit-feedback"><div class="innerwrap">' + ui_message + '</div></div>');
							}
						}
						return false;
					}
				} 

				var submit_button = $j('#form-stretch-goal-parent .acf-form-submit input');

				submit_button.on('mouseover', function(e) {
					var goal_ready = is_goal_ready('submit_check');
					if(goal_ready === false) {
						$j('#submit-feedback').addClass('visible');
					}
					else {
						$j('#submit-feedback').removeClass('visible');
					}
				});

				submit_button.on('mouseout', function(e) {
					$j('#submit-feedback').removeClass('visible');
				});

				submit_button.on('click', function(e) {
					if($j(this).hasClass('inactive') || $j('#stretch-goals').hasClass('goal-undefined')) {
						e.preventDefault();
						return false;
					}
					if($j('#stretch-goals').hasClass('goal-update')) {
						e.preventDefault();
						$j('#stretch-goals').removeClass('goal-update');
						$j('#stretch-goals').addClass('goal-locked');
						$j('#form-stretch-goal-parent > .acf-form-submit input').attr('value','Unlock Goal');
						return false;
					}
					if($j('#stretch-goals').hasClass('goal-locked')) {
						e.preventDefault();
						$j('#stretch-goals').removeClass('goal-locked');
						$j('#stretch-goals').addClass('goal-update');
						//show_popup('<h3>Goal Unlocked</h3><p>Before the stretch goal period starts, you can unlock your goal as many times as you want.</p><p>After it begins, however, we\'ll likely introduce a limitation for how many times you can unlock it, to facilitate focus, clarity and commitment.','',true,10000);
						$j('#form-stretch-goal-parent > .acf-form-submit input').attr('value','Lock Goal');
					}
					else {
						e.preventDefault();

						if($j('#stretch-goals').hasClass('goal-undefined')) {
							return false;
						}

						document.body.style.cursor = "progress";

						if($j('#stretch-goals').hasClass('goal-update')) {
							show_popup('<img src="' + theme_path + '/images/spinner.gif" class="loader" /><p>Updating Stretch Goal ...</p>','fixed ui-feedback',false);
						}
						else {
							show_popup('<img src="' + theme_path + '/images/spinner.gif" class="loader" /><p>Initiating Stretch Goal ...</p>','fixed ui-feedback',false);	
						}

						var data = {
							user_id: $j('body #profile').attr('user_id'),
							action: 'initiate_stretch_goal'
						};

						$j.ajax({
							url: ajaxurl,
							type: 'GET', // the kind of data we are sending
							data: data,        
							dataType: 'json',
							success: function(response) {
								document.body.style.cursor = "default";
								if(response.data.check === true) {
									$j('#stretch-goals').removeClass('goal-update goal-undefined');
									$j('#stretch-goals').addClass('goal-locked');
									$j('#form-stretch-goal-parent > .acf-form-submit input').attr('value','Unlock Goal');
									hide_popup();
									show_popup('<p style="margin: 0;">Congratulations, Stretch Goal <br/>successfully initiated!</p>', 'fixed', true, 3000);
									
									//Update UI
									if($j('#sidebar li.goal').length <= 0) {
										console.log("doesn't exist");
										$j('#sidebar li.email').after('<li class="goal ' + response.data.class + '"><p class="description">Stretch Goal</p><span class="icon"></span><div class="timescale" days="' + response.data.days + '"><span class="time" style="width:' + ($j('#sidebar li.goal').width() * response.data.pct) + 'px;"></span></div><span class="days"></span></li>');
									}
									else {
										console.log("does exist");
										$j('#sidebar li.goal').removeClass();
										$j('#sidebar li.goal').addClass('goal' + response.data.class);
										$j('#sidebar li.goal .time').width($j('#sidebar li.goal').width() * response.data.pct);
										$j('#sidebar li.goal .timescale').attr('days',response.data.days);
									}

									//Stretch starts today. Update accordingly
									if(response.data.class == 'now') {
										$j('#stretch-goals').addClass('goal-running');
									}

								}
								//console.log('Title was saved');
							}, error: function() {
								hide_popup();
								show_popup('<h3>Ooops, something went wrong!</h3><p>Please contact our <a href="/helpdesk" target="_blank">Helpdesk</a>!</p>','fixed',false);
							}
						});
					}
				});

				function update_goal_submit_state() {
					if(is_goal_ready()) {
						if(!$j('#stretch-goals').hasClass('goal-defined')) {
							$j('#stretch-goals').removeClass('goal-undefined');
							$j('#stretch-goals').addClass('goal-defined');
						}
						submit_button.removeClass('inactive');
						submit_button.addClass('active');
					}
					else {
						$j('#stretch-goals').removeClass('goal-defined');
						$j('#stretch-goals').addClass('goal-undefined');
						submit_button.removeClass('active');
						submit_button.addClass('inactive');				
					}
				}

				//Prepare for AJAX saving of stretch goals
				/*
				$j('#stretch-goals .stretch-goal form :submit').click(function(event){
					console.log("SUBMIT GOAL");
					event.preventDefault();
					var stretch_goal = $j(this).parents('.stretch-goal');
					var form_data = {'action' : 'acf/validate_save_post'};
					stretch_goal.find(':input').each(function(){
						form_data[$j(this).attr('name')] = $j(this).val();
					});

					console.log(ajaxurl + '  |  ' + form_data);

					form_data.action = 'save_my_data';
					$j.post(ajaxurl, form_data).done(function(save_data) {
						console.log("it worked!");
						show_popup('Category goal successfully saved','fixed', true, 3000);
					});
				});
				*/
			});
		</script>
	<?php
		$echo = ob_get_clean();
	endif;

	if($context == 'user-feedback') :
		ob_start();
	?>
		<script type="text/javascript">
			jQuery('document').ready(function($j) {
				$j('#ryit-feedback').on('click', function(e) {
					if($j('#ryit-feedback').hasClass('minimized')) {
						$j('#ryit-feedback').removeClass('minimized')
					}
					else {
						$j('#ryit-feedback').addClass('minimized')
					}
				});
			});
		</script>

<?php
		$echo = ob_get_clean();
	endif;

	if($context == 'change-view-mode') :
		ob_start();
	?>
		<script type="text/javascript">
			jQuery('document').ready(function($j) {
				$j(document).on('click', 'ul#mode-switches li', function(e) {
					//console.log("loading new mode");
					$j('body').addClass('loading');
					e.preventDefault();
					if(!$j(this).hasClass('switch-mode')) return;
					var mode = $j(this).attr('mode');
					show_popup('loading','fixed ui-feedback',false);

					$j('#mode-switches li').addClass('switch-mode');
					$j(this).removeClass('switch-mode');


					var data = {
						action: 'ryit_member_profile',
						view_mode: mode,
						user_id: $j('#profile').attr('user_id')
					};

					$j.ajax({
					  url: ajaxurl,
					  type: 'GET', // the kind of data we are sending
					  data: data,        
					  dataType: 'json',
					  success: function(response) {
					      //$j('#profile').replaceWith(response.data.output);
					      //console.log("it worked");
					      $j('#field-groups').animate({
					        opacity:0,
					        marginTop: "50px"
					      }, 400, function() {
					        $j('#profile #field-groups').html(response.data.output);

					        //Ensure that ACF jQuery works when changing view mode
					        acf.do_action('append', $j('#field-groups'));
	
					        $j('#ryit-menu').replaceWith(response.data.dropdown);
					        
					        if(mode == "goals") {
					          $j('.main').attr('active-section', 'life-assessment');
					        }
					        else if(mode == "ryit") {
					          $j('.main').attr('active-section', 'call-to-adventure');
					        }
					        
					        $j('div.main').attr('view-mode', mode);

					        $j('#profile .field.can-edit .field-content').append('<div class="edit"><i class="fas fa-pencil-alt"></i></div>');
								$j('#profile .field.can-edit form .acf-form-submit').append('<div class="cancel"><i class="fas fa-times-circle"></i></div>');

					        if(response.data.user_id == null) {
					        	var stateObj = { foo: "bar" };
					        	history.replaceState(stateObj, "Active field", "/user-profile");
					        }
					        else {
									var stateObj = { foo: "bar" };
									history.pushState(stateObj, "User ID", "?user_id=" + response.data.user_id);				        	
					        }
					        
					        $j('#field-groups').animate({
					          opacity: 1,
					          marginTop: "0"
					        }, 400);
					      $j('body').removeClass('loading');
					      hide_popup(500);
					    });
					  }
					});
				});

				function stretch_goal_ready() {
					$j('.completion-indicator').each(function() {
						if(!$j(this).hasClass('complete')) {
							return false;
						}
					});
				}
			});
		</script>
	<?php
		$echo = ob_get_clean();
	endif;

	if($context == 'feedback-popup') :
			ob_start();
		?>
			<!-- nothing -->
		<?php
		$echo = ob_get_clean();
	endif;

	return $echo;
}


//Modify Triad Post Object field to prevent user to be able to choose triad from different rounds
function ryit_post_object_query($args, $field, $post_id) {
	$user_id = ryit_get_user_ID();

	$query_args = array(
		'post_type' => 'triads',
		'posts_per_page' => - 1
	);
	$triads = get_posts($query_args);
	$triads_in_round = array();

	foreach ($triads as $triad) {
		if (get_field('ryit_round_number', 'user_' . $user_id) == get_field('ryit_round_number', $triad->ID)) {
			$triads_in_round[] = $triad->ID;
		}
	}

	// only show children of the current post being edited
	$args['post__in'] = $triads_in_round;

	// return
	return $args;
}

// Filter triads
add_filter('acf/fields/post_object/query/name=user_ryit_triad', 'ryit_post_object_query', 10, 3);

function alumnus_sidebar_empty($user_id) {
	$profile_image = get_field('ryit_user_profile_image', 'user_' . $user_id);
	$country = get_field('field_59e748b89988c', 'user_' . $user_id);
	$dob = get_field('field_5a5a255e8bae6', 'user_' . $user_id);
	if (empty($profile_image) && (empty($country) || $country == "undefined") && empty($dob)) {
		return true;
	}
	else {
		return false;
	}
}
/*
function validate_gravatar($email) {
	// Craft a potential url and test its headers
	$hash = md5(strtolower(trim($email)));
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);
	if (!preg_match("|200|", $headers[0])) {
		$has_valid_avatar = false;
	}
	else {
		$has_valid_avatar = true;
	}
	return $has_valid_avatar;
}*/

/************ E-MAIL FUNCTIONS *******************/

add_action('wp_ajax_email_ping_triad_member', 'ryit_email_ping_triad_member');

function ryit_email_ping_triad_member() {
	if (isset($_POST['user_id'])) {
		$user_id = $_POST['user_id'];
	}
	else {
		return false;
		die();
	}
	$user_data_to = get_userdata($user_id);
	$user_data_from = get_userdata(get_current_user_id());
	$subject = $user_data_from->first_name . ' says you\'re in his triad, ' . $user_data_to->first_name . '! (open to see new features)';
	$message = '<p>Hello ' . $user_data_to->first_name . '!</p>';
	$message .= '<p>A new feature has launched on the Reclaim your Inner Throne platform and ' . $user_data_from->first_name . ' is eager for you to use it.</p>';
	$message .= '<p>Please <a href="https://www.inner-throne.com/member_login">log in</a> as you normally do and then go to your <a href="https://www.inner-throne.com/user-profile/">User Profile</a> under My Accounts. Assign yourself to the right triad, feed your results from early work on this training into the system and ensure that all your triad members join. This is the start of an amazing new feature, that will only expand in the coming weeks.</p>';
	$message .= '<p>(You can also find a complete list of the men in your training on the new <a href="https://www.inner-throne.com/the-brotherhood">Brotherhood page</a>.)</p>';
	$message .= '<p>Come next week (November 12), we\'ll give you a proper introduction to all these new features (and where we\'ll take them next)!</p>';
	$message .= '<p>See you on <a href="https://www.inner-throne.com/member_login">the Course Pages</a>!</p>';
	$message .= '<p><em>The Reclaim your Inner Throne Bot,<br/>on behalf of ' . $user_data_from->first_name . ' ' . $user_data_from->last_name . '</em></p>';
	$headers[] = "From: RYIT Bot <support@inner-throne.com>";
	wp_mail($user_data_to->user_email, $subject, $message, $headers);
	$message = '<p>Hi ' . $user_data_from->first_name . '!</p><p>This is just a friendly notification to inform you that your message to ' . $user_data_to->first_name . ' ' . $user_data_to->last_name . ' was successfully delivered :)</p>';
	$message .= '<p><em>Sincerely<br/>The Reclaim your Inner Throne bot</em></p>';
	wp_mail($user_data_from->user_email, 'Your message was delivered', $message, $headers);
	die();
}


?>