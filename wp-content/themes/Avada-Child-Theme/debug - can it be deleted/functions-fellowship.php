<?php


/************************** THE FELLOWSHIP MEMBER DIRECTORY **************************/

add_action('wp_ajax_member_directory', 'ryit_member_directory');
add_action('wp_ajax_nopriv_member_directory', 'ryit_member_directory');

function ryit_member_directory() {

	$user_id = get_current_user_id();
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

	if (ryit_user_is_current()) {
		$header_echo .= '<h1 class="title-heading-center"><p style="text-align:center;">The Fellowship</p></h1>';
		$header_echo .= '<div class="fusion-text maxwidth-600" style="margin-bottom: 40px;"><p style="text-align: center;">The men traveling with you through the Realm of Forgotten Kings.</p>
		</div>';
	}
	else {
		$header_echo .= '<h1 class="title-heading-center"><p style="text-align:center;">The Fellowship</p></h1>';
		$header_echo .= '<div class="fusion-text maxwidth-600" style="margin-bottom: 40px;"><p style="text-align: center;">The full Member Directory.</p></div>';
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
	ryit_ui_feedback_popup('<img src="' . get_stylesheet_directory_uri() . '/images/spinner.gif" class="loader" /><p>Refreshing View ...</p>', true);
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

			//console.log(display_val);
			$j('#ryit_popup').removeClass('hidden');
			$j('#ryit_popup_overlay').removeClass('hidden');

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
					//console.log("response : " + response);
					$j('#directory_listing').html(response.data.echo);
					$j('#display_type_val').text(response.data.display_type);

					$j('#ryit_popup').addClass('hidden');
					$j('#ryit_popup_overlay').addClass('hidden');
				}
			});
		}
	});
	</script>

  <?php
	endif;

	$form_js = ob_get_clean();

	/**************** CREATE SEARCH AND FILTER FORM *****************/

	if (!ryit_user_is_current()) {
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
		if (ryit_user_is_alumnus()) {
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
			'filter_my_country',
			'Men with my skills *coming*',
			true
		);
		$filter_types[] = array(
			'filter_my_country',
			'Men with skills I\'m in need of *coming*',
			true
		);
		$filter_types[] = array(
			'filter_my_country',
			'Men in my country *coming*',
			true
		);
		$filter_types[] = array(
			'filter_my_country',
			'Men in my city *coming*',
			true
		);
		$filter_types[] = array(
			'filter_my_country',
			'Men with active stretch goal *coming*',
			true
		);
		if(ryit_user_is_alumnus()) {
			$filter_types[] = array(
				'filter_my_round',
				'Men from my RYIT *coming*',
				true
			);
		}

		$form_echo = "";
		$form_echo .= "<form id='member_directory_settings'>";
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

		if (ryit_user_is_current()) {
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
	$default_avatar = $upload_dir['baseurl'] . "/2014/12/crown-logo.png";

	//only show current round to men undergoing their initiation
	if (ryit_user_is_current()) {
		$curr_round = get_field('ryit_round_number', 'options');
		$temp_rounds = $rounds[$curr_round];
		$rounds = array();
		$rounds[$curr_round] = $temp_rounds;
	}

	if ($sort_type == 0) { /************* Sort alumni by name ******************/
		foreach ($alumni_names as $alumnus) {
			$alumnus_data = get_userdata($alumnus['id']);
			$user_id = $alumnus_data->ID;

			$avatar = get_field('field_5a576ed8b86eb', 'user_' . $user_id);
			if (empty($avatar)) {
				$avatar = $default_avatar;
				$avatar_status = "";
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
					$echo .= "<div class='member" . $avatar_status . "'>";
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

	if (ryit_user_is_current()) {
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



/************************** FELLOWSHIP FUNCTIONS **************************/

add_action('wp_head', 'ryit_init_startup_guide');

function ryit_init_startup_guide() {
	$user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : get_current_user_id();

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
						user_id: $j('#profile').attr('user_id')
					}

					$j.ajax({
						url: ajaxurl,
						type: 'GET', // the kind of data we are sending
						data: data,        
						dataType: 'json',
						success: function(response) {
							//console.log("State updated");
						}, error: function() {
							//console.log("Something went wrong");
						}
					});
				});
			});
		</script>
	<?php
		$echo = ob_get_clean();

		$portrait_defined = !empty(get_field('field_5a576ed8b86eb', 'user_' . $user_id)) ? get_field('field_5a576ed8b86eb', 'user_' . $user_id) : false ;
		$location_defined = (!empty(get_field('field_5a4be6cf193be','user_' . $user_id)) && !empty(get_field('field_59e748b89988c','user_' . $user_id))) ? true : false;
		$life_assessment_defined = !empty(get_user_meta($user_id, 'ryit_user_life_assessment_total_average', true)) ? true : false;
		$stretch_goal_defined = false;
		//$purposed_section_defined = false;

		if($portrait_defined && $location_defined && $life_assessment_defined && $stretch_goal_defined) {
			return;
		}

		$setup_guide_state = get_user_meta($user_id, 'ryit_setup_guide_state', true);
		$state_class = (get_user_meta($user_id, 'ryit_setup_guide_state', true) == "maximized") ? "maximized" : "minimized";

		$echo .= '<div id="setup-guide" class="' . $state_class . '">';
		$echo .= '<div class="toggle"><i class="far fa-window-maximize"></i><i class="far fa-window-minimize"></i></div>';
		$echo .= '<div class="innerwrap">';
		$echo .= '<ul>';
		$echo .= $portrait_defined ? '<li class="complete"><span>Upload Profile Picture</span></li>' : '<li><span>Uploaded Profile Picture</span></li>';
		$echo .= $location_defined ? '<li class="complete"><span>Fill in location</span></li>' : '<li><span>Location Defined</span></li>';
		$echo .= $life_assessment_defined ? '<li class="complete"><span>Complete Life Assessment</span></li>' : '<li><span>Complete <a href="/user-profile/?active-section=life-assessment">Life Assessment</a></span></li>';
		$echo .= $stretch_goal_defined ? '<li class="complete"><span>Initiate a Stretch Goal</span></li>' : '<li><span>Initiate a Stretch Goal</span></li>';
		//$echo .= $purposed_section_defined ? '<li class="complete"><span>Purpose Section defined</span></li>' : '<li><span>Purpose Section defined</span></li>';
		$echo .= '<p>Complete sequence to remove box.</p>';
		$echo .= '</div>';
		$echo .= '</div>';
	}
	echo $echo;
}


add_action('wp_ajax_update_setup_state', 'ryit_update_setup_state');
add_action('wp_ajax_nopriv_update_setup_state', 'ryit_update_setup_state');

function ryit_update_setup_state() {
	$user_id = $_GET['user_id'];
	$mode = $_GET['mode'];
	//echo $user_id . " mode:" . $mode;
	update_user_meta($user_id, 'ryit_setup_guide_state', $mode);
	die();
}

/************************** FELLOWSHIP FUNCTIONS **************************/


function ryit_get_user_ID() {
	$user_id = $_GET['user_id'];

	if (is_user_logged_in() && !isset($user_id)) {
		$user_id = get_current_user_id();
	}
	return $user_id;
}


function fellowship_menu() {
	$echo = ryit_get_javascript('change-view-mode');

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

	if ($post->post_name == "user-profile" && ryit_user_is_alumnus($user_id)) {
		$view_mode = get_user_meta($user_id,'ryit_user_profile_viewmode',true);

		if (empty($view_mode)) $view_mode = 'goals';

		$viewmode_class = $view_mode;
		$echo .= '<div class="right"><span>View mode</span><ul id="mode-switches" class="' . $viewmode_class . '">';

		$link = get_permalink($post->ID);

		if ($view_mode == "ryit") {
			$echo .= '<li id="mode-ryit" mode="ryit"><a href="' . $link . '?view_mode=ryit"></a></li>';
		}
		else {
			$echo .= '<li id="mode-ryit" class="switch-mode" mode="ryit"><a href="' . $link . '?view_mode=ryit"></a></li>';
		}

		if ($view_mode == "goals") {
			$echo .= '<li id="mode-goals" mode="goals"><a href="' . $link . '?view_mode=goals"></a></li>';
		}
		else {
			$echo .= '<li id="mode-goals" class="switch-mode" mode="goals"><a href="' . $link . '?view_mode=goals"></a></li>';
		}
		$echo .= '</ul></div>';
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
add_action('wp_ajax_nopriv_ryit_member_profile', 'ryit_member_profile');

function ryit_member_profile() {
	acf_form_head();
	wp_enqueue_style("media-upload", get_site_url() . "/wp-includes/css/media-views.min.css"); //ensure that image uploader looks correct
	global $post;
	$user_id = $_GET['user_id'];

	if (is_user_logged_in() && !isset($user_id)) {
		$user_id = get_current_user_id();
	}
	$user_data = get_userdata($user_id);

	if (!empty(get_avatar($user_data->ID))) {
		$args = array(
			'size' => 250,
			'default' => 'mysteryman'
		);
		$avatar = get_avatar_url($user_data->ID, $args);
	}

	$echo = ryit_get_javascript('user-profile');

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		$is_ajax = false;
		$active_section = $_POST['active_section'];
		$view_mode = get_user_meta($user_id,'ryit_user_profile_viewmode',true);
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
			$active_section = 'vision-mission';
		}
	}

	$current_user_id = get_current_user_id();

	if(!empty($_GET['active-section'])) {
		$active_section = $_GET['active-section'];
	}

	$subscription_id = rcp_get_subscription_id($user_id);

	//sub id 1 = fellowship member | sub id 2 = alumnus | sub id 3 = current ryit
	if(empty($view_mode)) {
		$view_mode = 'goals';
	}

	if((ryit_user_is_alumnus($user_id) || ryit_user_is_fellowship($user_id)) && $view_mode != 'ryit') { //Alumnus
		$fields_vision = array(
			'field_5b31265e34b15', //vision
			'field_5b7ab42bbe941', //mission
		);

		$fields_goals = array(
			'field_5b338e4e928e8', //ten year goal
			'field_5b338e3b928e7', //five year goal
			'field_5c101d40c01b3', //three year goal
			'field_5b338e2b928e6'
		);

		$fields_stretch_goals = array(
			'function_ryit_get_profile_stretch_goals', //Call function
		);

		$fields_daily_practice = array();

		$fields_interests = array(
			'field_5b7ead2fe4691', //more info about your interests
			'field_5b338694a6537',
			//fields of interest or skill
		);

		$fields = array(
			array(
				"Vision & Mission",
				$fields_vision
			) ,
			array(
				"Long term goals",
				$fields_goals
			) ,
			array(
				"Stretch goals",
				$fields_stretch_goals
			) ,
			array(
				"Daily Practice",
				$fields_daily_practice
			) ,
			array(
				"Purpose & Business",
				$fields_interests
			)
		);

		if (empty($active_section)) {
			$active_section = 'vision-mission';
		}
	}
	else if (ryit_user_is_current($user_id) || (ryit_user_is_alumnus($user_id) && $view_mode == 'ryit')) { //Active course participants
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

		$fields_ajax = $fields; //store $fields for use with ajax
		$fields_with_val = 0;
		$fields_echo = ""; 

		//Set up fields inside categories
		foreach ($fields as $field_group) {
			$field_group_id = sanitize_title($field_group[0]);
			$fields_echo .= '<div class="field-group" id="field-group-' . $field_group_id . '">';
			$fields = $field_group[1];
			$field_echo = "";

			if($field_group_id != "stretch-goals") {
				foreach ($fields as $field_id) {		// Retrieve normal fields
					$field_data = ryit_get_profile_field_values($field_id,$user_id,$active_section);
					$field_echo .= $field_data['output'];
					if($field_data['has_value']) {
						$fields_with_val++;
					}
				}
			}
			else {
				$field_echo = ryit_get_profile_stretch_goals($user_id); //Retrieve stretch goals
			}

			/* Print field values to screen */
			if ($fields_with_val > 0) { //Show if values have been filled in by user :
				$fields_echo .= "<h2>" . $field_group[0] . "</h2>" . $field_echo;
			}

			$fields_echo .= '</div>'; //end #field-group
		}

		if($is_ajax) {
			if(!empty($view_mode)) {
				$ajax_echo = $fields_echo; //This is the output which will be returned in an Ajax call	
				$return['dropdown'] = ryit_get_profile_navigation($user_id, $fields_ajax, true);
				$return['output'] = $ajax_echo;
				wp_send_json_success($return);
				die();
			}
		}

		//Will not continue beyond this point if Ajax call without mode shift
		//$fields_echo = '<div id="field-groups">' . $fields_echo . '</div>';

		//Add RCP profile and purchase history sections
		if ($user_id == $current_user_id) {
			$echo .= '<div class="field-group" id="field-group-life-assessment">';
			ob_start();
			echo do_shortcode('[life_assessment]');
			$content = ob_get_clean();
			$echo .= '<div class="field">' . $content . '</div>';
			$echo .= '</div>';

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

		$display_sidebar = (alumnus_sidebar_empty($user_id) && $user_id != $current_user_id) ? " " : " display_sidebar";

		if($is_ajax) {
			$echo = $ajax_output;
		}
		else {
			$popup = ryit_ui_feedback_popup('<img src="' . get_stylesheet_directory_uri() . '/images/spinner.gif" class="loader" /><p>Refreshing View ...</p>',true,false);
			$echo = $popup . '<div id="profile" user_id="' . $user_id . '" class="clearfix ' . $display_sidebar . '"><h1>' . $user_data->first_name . ' ' . $user_data->last_name . '</h1>' . $echo . '</div>';
		}
	}

	return $echo;
}

add_shortcode('alumnus_profile', 'ryit_member_profile');

function ryit_category_focus() {
	return "<h2>This is a test</h2>s";
}

function ryit_get_profile_navigation($user_id, $fields,$is_ajax=false) {

	if(!$user_id) $user_id = $_GET['user_id'];
	$current_user_id = get_current_user_id();

	//START: SET UP NAVIGATION
	$nav_echo = '<nav id="profile-menu" class="clearfix">';

	//Dropdown echo returned for ajax calls
	$dropdown_echo = '<div id="ryit-menu" class="dropdown"><select>';
	$dropdown_echo .= '<option id="inactive" disabled>-- Select --</option>';

	$active_section = $_GET['active-section'];

	foreach ($fields as $field) {
		$dropdown_echo .= '<option id="tab-' . sanitize_title($field[0]) . '" value="' . sanitize_title($field[0]) .'"';


		if ($active_section == sanitize_title($field[0])) {
			$dropdown_echo .= 'class="active" selected';
		}
		$dropdown_echo .= '>' . $field[0] . '</option>';
	}

	$dropdown_echo .= '</select></div>';

	if($is_ajax) {
		return $dropdown_echo;
	}

	$nav_echo .= $dropdown_echo;
	$nav_echo .= '<ul class="tabs">';

	if ($user_id == $current_user_id) {
		$ui_buttons = array(
			array(
				'life-assessment',
				'Life Assessment'
			) ,
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


function ryit_get_profile_stretch_goals($user_id=false) {
	if(!$user_id) $user_id = $_GET['user_id'];
	$current_user_id = get_current_user_id();

	$goal_categories = array(array('Mind','field_5c34e58ed4f7c'), array('Body','field_5c445c311e899'),array('Purpose','field_5c45e84959d94'),array('People','field_5c4707f77e581'));

	foreach ($goal_categories as $goal_cat) {
		$field_obj = get_field_object($goal_cat[1], 'user_' . $user_id);	
		$can_edit = ($user_id == $current_user_id) ? " can-edit" : "";	

		//Measure if goal is fully defined
		$completion_score = 0;
		$completion_score = !empty($field_obj['value']['goal_image']) ? $completion_score += 1 : $completion_score;
		$completion_score = !empty($field_obj['value']['goal_name']) ? $completion_score += 1 : $completion_score;
		$completion_score = !empty($field_obj['value']['category_focus']) ? $completion_score += 1 : $completion_score;
		$completion_score = !empty($field_obj['value']['score_goal'] && $field_obj['value']['score_increase'] != "err") ? $completion_score += 1 : $completion_score;

		if(empty($field_obj['value']['goal_image'])) {
			if($completion_score <= 0 ) {
				$class = ' undefined';
				$image = get_stylesheet_directory_uri() . '/images/crosshair.png';
			}
			else {
				$image = "";
			}
		}
		else {
			$image = $field_obj['value']['goal_image'];
			$class = "";
		}

		$stretch_goals_echo .= '<div class="stretch-goal closed' . $class . $can_edit . '" id="cat-' . sanitize_title($goal_cat[0]) . '">';
		$stretch_goals_echo .= '<div class="state-closed">';

		$stretch_goals_echo .= '<div class="title" style="background-image: url(' . $image . ');">';
		$stretch_goals_echo .= '<h3><span>' . $field_obj['value']['goal_name'] . '</span></h3>';
		$stretch_goals_echo .= '</div>';
		$stretch_goals_echo .= '<div class="text">';
		
		if(!empty($field_obj['value']['score_current']) && !empty($field_obj['value']['score_goal'])) {
			$stretch_goals_echo .= '<div class="score">' . $field_obj['value']['score_current'] . '</div>';
			$stretch_goals_echo .= '<img src="' . get_stylesheet_directory_uri() . '/images/arrow-right.png" class="arrow" />';
			$stretch_goals_echo .= '<div class="score">' . $field_obj['value']['score_goal'] . '</div>';

			if($completion_score >= 4) {
				$stretch_goals_echo .= '<div class="completion-indicator complete"><i class="fas fa-check"></i></div>';
			}
			else {
				$stretch_goals_echo .= '<div class="completion-indicator in-process"><i class="fas fa-hourglass-half"></i></div>';	
			}
		}
		$stretch_goals_echo .= '</div>';
		$stretch_goals_echo .= '</div>';
		
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
				//'uploader' => 'basic',
				'id' => 'form-stretch-goal-' . $goal_cat[0]
			);
			ob_start();
			acf_form($settings);
			$form = ob_get_clean();
			$stretch_goals_echo .= $form;
		}
		$stretch_goals_echo .= '</div>';
	}

	$echo = ryit_get_javascript('stretch-goals');

	//Create the parent level form that "holds" all the others
	$parent_form_echo = "";
	$fields = array('field_5c461a1e6e811', 'field_5c461a466e812');

	if ($user_id == $current_user_id) {
		$settings = array(
			'post_id' => 'user_' . $user_id,
			'html_updated_message' => '',
			'fields' => $fields,
			'submit_value' => 'Pursue this Goal',
			//'uploader' => 'basic',
			'id' => 'form-stretch-goal-parent'
		);
		ob_start();
		acf_form($settings);
		$form = ob_get_clean();
		$parent_form_echo .= $form;
	}

	/* End Parent Form */


	$echo .= '<div id="stretch-goals">';
	$echo .= '<div class="innerwrap clearfix">';
	$echo .= $parent_form_echo;
	$echo .= '<a href="#" class="close"><span class="fa fa-times"></span></a>';
	$echo .= $stretch_goals_echo;
	$echo .= '</div>';	
	$echo .= '</div>';

	return $echo;
}

function ryit_get_profile_sidebar($user_id=false) {
	//error_reporting(E_ALL);
	if(!$user_id)  $user_id = $_GET['user_id'];
	$current_user_id = get_current_user_id();

	$profile_image = get_field('ryit_user_profile_image', 'user_' . $user_id);
	$country = get_field('field_59e748b89988c', 'user_' . $user_id);
	$city = get_field('field_5a4be6cf193be', 'user_' . $user_id);
	$dob = get_field('field_5a5a255e8bae6', 'user_' . $user_id);
	$triad = get_field('field_5bdc3a83b2f62', 'user_' . $user_id);

	//Start Sidebar
	if (!alumnus_sidebar_empty($user_id) || $user_id == $current_user_id) {
		
		$echo = '<div id="sidebar">';
		$echo .= ryit_get_javascript('sidebar');
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

		//-------- ADD BADGES --------

		$echo .= '<div id="user-badges"><div class="innerwrap">';
		$echo .= '<ul class="clearfix">';
		
		if(ryit_user_is_current() || ryit_user_is_alumnus()) {
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
			$echo .= '<img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-ryit.png" />';
			$echo .= '</li>';
		}

		$badges = get_field('ryit_user_badges', 'user_' . $user_id);
		$fellowship_level = ryit_user_get_fellowship_level($user_id);
		//echo "level " . $fellowship_level;

		if(!empty($fellowship_level) || user_can($user_id, 'edit_pages')) {
			$echo .= '<li id="fellowship-level" class="active">';
			if(user_can($user_id, 'edit_pages')) {
				//echo "should not see this";
				$fellowship_level = 'gold';
			}

			//echo "level " . $fellowship_level;
			$echo .= '<div class="badge-info">' . $user_data->first_name .' is a ' . $fellowship_level . '-level member of the Fellowship</div>';
			$echo .= '<img src="' . get_stylesheet_directory_uri() . '/images/badges/badge-' . $fellowship_level . '.png" />';
			$echo .= '</li>';
		}

		$echo .= '</ul>';
		$echo .= '</div></div>';

		//-------- END BADGES --------


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
			$echo .= '<li><p class="description">Country</p><p class="data">' . $country . '</p></li>';
		}

		if (!empty($city)) {
			$echo .= '<li><p class="description">City</p><p class="data">' . $city . '</p></li>';
		}

		if (!empty($dob)) {
			$date = explode("/", $dob);
			$time = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			$echo .= '<li><p class="description">Date of birth</p><p class="data">' . date('F d, Y', $time) . '</p></li>';
		}

		$echo .= '<li><p class="description">E-mail</p><p class="data"><a href="mailto:' . $user_data->user_email . '">' . $user_data->user_email . '</a></p></li>';

		$echo .= ryit_user_get_assessment_results($user_id);
		
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
		$echo .= '</ul>';
	
		if ($user_id == $current_user_id) {
			$echo .= '<div id="edit-user-data" class="button" style="margin-bottom: 30px;">Edit profile data</div>';
		}

		$echo .= '</div>'; /* End <div id="user-data"> */
	}

	//Sidebar when edited
	if ($user_id == $current_user_id) {
		$echo .= '<div id="user-data-form-wrapper">';

		$fields = array(
			'field_5a576ed8b86eb', //image
			'field_59e748b89988c', //country
			'field_5a4be6cf193be', //city
			'field_5a5a255e8bae6', //birthdate
			'field_5c10ddb1887d4', //hide assessment results
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

function ryit_get_profile_field_values($field_id = false,$user_id = false,$active_section=false) {
	$field_has_value = false;
	if(empty($field_id)) return false;
	if(!$user_id) $user_id = $_GET['user_id'];
	$current_user_id = get_current_user_id();
	
	if(strpos($field_id, 'function') !== false) { //Enable functions to be called instead of fields. Possibly a redundant function at this stage
		$function = substr($field, 9);
		$field_echo .= call_user_func($function);
	}
	else {
		//get essential profile data
		$field_obj = get_field_object($field_id, 'user_' . $user_id);

		if (!empty($field_obj['value']) || ($user_id == $current_user_id)) {
			$field_has_value = true;

			$can_edit = ($user_id == $current_user_id) ? " can-edit" : "";
			$is_message = ($field_obj['type'] == "message") ? " message" : "";
			$field_echo .= '<div id="' . $field_obj['name'] . '" class="field' . $can_edit . $is_message . '">';
			$field_echo .= '<h3>' . $field_obj['label'] . '</h3>';
			$field_echo .= '<div class="field-data">';				

			if (empty($field_obj['value']) && ($user_id == $current_user_id)) { //Field does not have a value assigned
				if ($field_obj['type'] == "message") {
					$field_echo .= '<div class="message"><p>' . nl2br($field_obj['message']) . '</p></div>';
				}
				else {
					$field_echo .= '<div class="field-content"><p><em>Instructions</em>: ' . $field_obj['instructions'] . '</p><span class="add-response button">Add your response</span></div>';
				}
			}
			else { //Field does have a value
				if (is_array($field_obj['value'])) { //Repeater field
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
									$is_group = false;
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

			//set up form
			if ($user_id == $current_user_id) {
				$settings = array(
					'post_id' => 'user_' . $user_id,
					'html_updated_message' => '',
					'fields' => array(
						$field_id
					) ,
					'html_after_fields' => '<input type="hidden" class="active_section" name="_active_section" value="' . $active_section . '" /><input type="hidden" name="_form_context" value="user_profile" />',
					'form_attributes' => array(
						'class' => 'clearfix'
					) ,
					'id' => 'form_' . $field_obj['name']
				);
				ob_start();
				acf_form($settings);
				$form = ob_get_clean();
				//$field_echo .= $form;
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
	if(!$user_id) $user_id = $_GET['user_id'];
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
	if (current_user_can('edit_posts')) {
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

			//echo '<li><span style="background-color: rgba(100, 140, 140, 1);">1-5</span><p>Scale is:</p></li>';
			$echo .= '</ul>';
		}
		$echo .= '</li>';
	}

	return $echo;
}

function ryit_life_assessment() {
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
				if(button.hasClass('disabled')) { return; } //cancel function
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
							if($j('#ryit_popup').length > 0) {
								$j('#ryit_popup_overlay').remove();
								$j('#ryit_popup').remove();
							}

							//Create new popup
							$j('body').append('<div id="ryit_popup_overlay"></div>');
							$j('body').append('<div id="ryit_popup" class="assessment"><h3>Saving assessment results</h3><a href="#" class="close"><span class="fa fa-times"></span></a></div>');
							var scrollTop = $j(document).scrollTop();
							$j("#ryit_popup").css("top", scrollTop + 100);

							$j.ajax({
								url: ajaxurl,
								type: 'GET', // the kind of data we are sending
								data: data,        
								dataType: 'json',
								success: function(response) {
									$j('#ryit_popup').html('<h3>Results successfully saved</h3><p><strong>Note</strong>: You can edit who can see your results by clicking "Edit profile data" in the sidebar</p><p>Page will reload to display your results in a moment...</p>');
									setTimeout(
										function() {
											location.reload();
										}, 5000);
								}, error: function() {
									$j('#ryit_popup').html('<h3>Oops! We couldn\'t save your data.</h3><p>Please contact <a href="https://innerthrone.kartra.com/help/helpdesk" target="_blank">Helpdesk</a></p>');
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
			$echo .= '<div class="slider"><input type="range" min="1" max="5" step="1" value="3" data-rangeslider role="input-range">' . $dimension['dimension'] . '</input></div>';
			$echo .= '<p class="min-text">' . $dimension['min_text'] . '</p>';
			$echo .= '<p class="max-text">' . $dimension['max_text'] . '</p>';
			$echo .= '<span class="var-name" style="display: none;">' . $dimension['dimension_var_name'] . '</span>';
			$echo .= '</div>';
		}
		$echo .= '</div>';
	}
	$echo .= '</div>';
	$echo .= '<ul class="scale-steps"><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li></ul>';
	$echo .= '<div id="progress_button" class="button simple">Start the Assessment</div>';
	$echo .= '</div>';
	return $echo;
}

add_shortcode('life_assessment', 'ryit_life_assessment');

//Used to send active field variable as form data on user profile page
function my_acf_pre_submit_form($form) {
	if ($_POST['_form_context'] == "user_profile") {
		$return = $form['return'];
		$section_defined = strpos($form['return'], 'active-section');
		if ($section_defined !== false) { //Active section already defined
			$form['return'] = '?updated=true&active-section=' . $_POST["_active_section"];
		}
		else {
			$form['return'] .= '&active-section=' . $_POST["_active_section"];
		}
	}
	// return
	return $form;
}

add_filter('acf/pre_submit_form', 'my_acf_pre_submit_form', 10, 1);

add_action('wp_ajax_save_assessment_results', 'ryit_save_assessment_results');
add_action('wp_ajax_nopriv_save_assessment_results', 'ryit_save_assessment_results');

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
		$group_avg = round($metric_sum / count($group_metrics) , 1);
		$total_avg += $group_avg;

		update_user_meta($user_id, 'ryit_user_life_assessment_' . $var_name . '_average', $group_avg);

		$group_avg = $metric_sum = 0; //Reset counters
		
	}

	$total_avg = round($total_avg / count($full_metrics) , 1);
	update_user_meta($user_id, 'ryit_user_life_assessment_total_average', $total_avg);
}


function ryit_get_javascript($context = false) {
	if(!$context) {
		return;
	}

	if($context == 'sidebar') :
		ob_start();
		?>
		<script type="text/javascript">   
			jQuery('document').ready(function($j) {
				$j(document).on('click', '#banner', function(e) {
					e.preventDefault();
					if($j('#ryit_popup').length <= 0) {
						$j('body').append('<div id="ryit_popup_overlay"></div><div id="ryit_popup"><div id="popup-banner"><img src="<?php echo get_field('ryit_triad_banner', $post_id); ?>" /></div><div id="popup-content"><h2><?php echo get_the_title($post_id); ?></h2><div id="triad-mission"><?php echo trim(get_field('ryit_triad_mission', $post_id)); ?></div><h3>Members</h3><?php echo $triad_member_html; ?><a href="#" class="close"><span class="fa fa-times"></span></a></div></div>');
					}
					else {
						$j('#ryit_popup').fadeIn(500);
						$j('#ryit_popup_overlay').fadeIn(500);
					}
					var scrollTop = $j(document).scrollTop();
					$j("#ryit_popup").css("top", scrollTop + 50);
				});

				$j(document).on('click', 'a.close', function(e) {
					e.preventDefault();
					$j('#ryit_popup').fadeOut(500);
					$j('#ryit_popup_overlay').fadeOut(500);
				});

				$j(document).on('mouseenter', '#user-badges li img', function(e) {
					$j(this).parent().addClass('hover');
				});
				
				$j(document).on('mouseleave', '#user-badges li img', function(e) {
					$j(this).parent().removeClass('hover');
				});
			});
		</script>
		<?php
		$echo = ob_get_clean();
	endif;



	if($context == 'user-profile') :
		ob_start();
	?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

			jQuery('document').ready(function($j) {
				
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


				$j('.acf-field.score-current input').val(<?php echo get_user_meta($user_id, 'ryit_user_life_assessment_mind_average', true); ?>);
 				$j(".acf-image-uploader p").each(function(){
					$j(this).replaceWith($j(this).html().replace("No image selected", ""));
				});

				$j(".acf-image-uploader .acf-button").text('Upload Goal Image');
			});

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
				$j('#ryit-menu select').val('-- Choose your week --');

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

				if(increment<=0) {
					$j('.acf-field.score-increase input').val('err');
					window.alert("Goal must be higher than current");
				}
				else {
					parent.find('.score-increase input').val(increment);
				}
			});

			$j(document).on('keydown', '.acf-field.score input', function(e) {
				e.preventDefault();
				
				var charnum = e.which;			
				//Backspace = 8, delete = 46
				var delete_keys = new Array(8,46);
				if(delete_keys.indexOf(charnum) != -1) {
					$j(this).val('');
					return;
				}

				var character = String.fromCharCode(e.which);
				
				if($j(this).val().length == 0) { //First letter being typed in
					var valid_numbers = new Array(0,1,2,3,4,5);
					if(valid_numbers.indexOf(Number(character)) != -1) {
						if(character == 5)  {
							$j(this).val(String.fromCharCode(e.which) + ".0");				
						}
						else {
							$j(this).val(String.fromCharCode(e.which) + ".");
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
							$j(this).val(string + String.fromCharCode(e.which));	

							//Update score increment
							var parent = $j(this).parents('.stretch-goal');
							parent.find('.score-increase input').val(parseFloat(parent.find('.score-goal input').val() - parent.find('.score-current input').val()).toFixed(1));
						}
						else {
							$j(this).val(string + String.fromCharCode(e.which));		
						}
					}
				}
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


	if($context == 'stretch-goals') :
		ob_start();
	?>
		<script type="text/javascript">
			jQuery('document').ready(function($j) {
				$j('.stretch-goal').on('click', function() {
					//Close all open stretch goals
					$j('#stretch-goals .stretch-goal').removeClass('open');
					$j('#stretch-goals .stretch-goal').addClass('closed');

					//Open new stretch goal
					$j(this).addClass('open');
					$j(this).removeClass('closed');
					$j(this).parent().addClass('open');
					$j(this).parent().removeClass('closed');


					//Fix image display
					/*
					var image_wrap = $j(this).find('.goal-image .image-wrap');
					var img_src = image_wrap.find('img').attr('src');
					console.log(img_src);
					var style = image_wrap.attr('style');
					var new_style = style + '; background-image: url(' + img_src + ');';
					image_wrap.removeAttr('style');
					image_wrap.attr('style', new_style);
					image_wrap.find('img').attr('src','');
					*/
				});

				$j('#stretch-goals a.close').on('click', function(e) {
					e.preventDefault();
					$j('#stretch-goals .stretch-goal').removeClass('open');
					$j('#stretch-goals .stretch-goal').addClass('closed');
					$j(this).parent().addClass('closed');
					$j(this).parent().removeClass('open');
				});
	
				$j(document).on('focus', '.start-date input', function(e) {
					e.preventDefault();
				});

				//BELOW: Control whether stretch goal has been fully defined before it can be initiated

				var is_goal_ready = is_goal_ready();
				var submit_button = $j('#form-stretch-goal-parent .acf-form-submit input');

				if(is_goal_ready === true) {
					submit_button.removeClass('inactive');
					submit_button.addClass('active');
				}
				else {
					submit_button.removeClass('active');
					submit_button.addClass('inactive');
				}

				submit_button.on('click', function(e) {
					if($j(this).hasClass('inactive')) {
						e.preventDefault();
					}
				});

				function is_goal_ready() {
					var completion_check = $j('#stretch-goals .completion-indicator');
					
					if(completion_check.length <= 4) {
						return false;
					}

					completion_check.each(function() {
						//console.log("CHECK STATUS");
						var check_status = $j(this).hasClass('complete');
						//console.log(check_status + " | ");
						if(check_status === false) {
							return false;
						}
					});

					return true;
				} 
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
					$j('body').addClass('loading');
					e.preventDefault();
					if(!$j(this).hasClass('switch-mode')) return;
					var mode = $j(this).attr('mode');
					$j('#ryit_popup').removeClass('hidden');
					$j('#ryit_popup_overlay').removeClass('hidden');

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
					      $j('#field-groups').animate({
					        opacity:0,
					        marginTop: "50px"
					      }, 400, function() {
					        $j('#profile #field-groups').html(response.data.output);
					        //$j('document').trigger('acf/setup_fields', $j('#profile .field-group .wp-editor-container'));
					        acf.do_action('append', $j('#popup-id'));
					        
					        $j('#ryit-menu').replaceWith(response.data.dropdown);
					        
					        if(mode == "goals") {
					          $j('.main').attr('active-section', 'vision-mission');
					        }
					        else if(mode == "ryit") {
					          $j('.main').attr('active-section', 'call-to-adventure');
					        }
					        
					        var stateObj = { foo: "bar" };
					        history.replaceState(stateObj, "Active field", "/user-profile");
					        
					        $j('#field-groups').animate({
					          opacity: 1,
					          marginTop: "0"
					        }, 400);
					      $j('body').removeClass('loading');
					      $j('#ryit_popup').addClass('hidden');
					      $j('#ryit_popup_overlay').addClass('hidden');
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
	$user_id = get_current_user_id();

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

// filter for every field
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
}

/************ E-MAIL FUNCTIONS *******************/

add_action('wp_ajax_email_ping_triad_member', 'ryit_email_ping_triad_member');
add_action('wp_ajax_nopriv_email_ping_triad_member', 'ryit_email_ping_triad_member');

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

function wpse27856_set_content_type() {
	return "text/html";
}
add_filter('wp_mail_content_type', 'wpse27856_set_content_type');

?>