<?php

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