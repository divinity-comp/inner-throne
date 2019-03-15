<?php
	/* ----------------------------------------------------------------------------------------------------

		BEAMER SETTINGS
		Handles all the settings in the Beamer options page

	---------------------------------------------------------------------------------------------------- */

	// BEAMER SETTINGS CLASS
	class BeamerSettings {
		private $beamer_settings_options;
		// Construct
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'beamer_settings_add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'beamer_settings_page_init' ) );
		}

		// Add settings page
		public function beamer_settings_add_plugin_page() {
			add_options_page(
				'Beamer Settings', // page_title
				'Beamer Settings', // menu_title
				'manage_options', // capability
				'beamer-settings', // menu_slug
				array( $this, 'beamer_settings_create_admin_page' ) // function
			);
		}

		// Create settings page
		public function beamer_settings_create_admin_page() {
			$this->beamer_settings_options = get_option( 'beamer_settings_option_name' );
			include('beamer-settings-panel.php');
		}

		// Add setting page elements
		public function beamer_settings_page_init() {
			// Register settings
			register_setting(
				'beamer_settings_option_name', // option_group
				'beamer_settings_option_name', // option_name
				array( $this, 'beamer_settings_sanitize' ) // sanitize_callback
			);
			// Settings sections -------------------------------------------------------
				// Add general settings section
				add_settings_section(
					'beamer_settings_setting_section', // id
					'General Settings', // title
					array( $this, 'beamer_settings_section_info' ), // callback
					'beamer-settings-admin' // page
				);
				// Add advanced settings section
				add_settings_section(
					'beamer_settings_advanced_section', // id
					'Advanced Options', // title
					array( $this, 'beamer_settings_advanced_section_info' ), // callback
					'beamer-settings-admin' // page
				);
				// Add user settings section
				add_settings_section(
					'beamer_settings_user_section', // id
					'User Options', // title
					array( $this, 'beamer_settings_user_section_info' ), // callback
					'beamer-settings-admin' // page
				);
				// Add master settings section
				add_settings_section(
					'beamer_settings_master_section', // id
					'Filter Options', // title
					array( $this, 'beamer_settings_master_section_info' ), // callback
					'beamer-settings-admin' // page
				);
				// Add master settings section
				add_settings_section(
					'beamer_settings_api_section', // id
					'Beamer API <span class="betatag">BETA</span>', // title
					array( $this, 'beamer_settings_api_section_info' ), // callback
					'beamer-settings-admin' // page
				);
			// Settings fields -------------------------------------------------------
				// Field: product-id
				add_settings_field(
					'product_id', // id
					'Product ID', // title
					array( $this, 'product_id_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: selector
				add_settings_field(
					'selector', // id
					'Selector', // title
					array( $this, 'selector_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);

				// Field: display
				add_settings_field(
					'display', // id
					'Display', // title
					array( $this, 'display_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: top
				add_settings_field(
					'top', // id
					'Top', // title
					array( $this, 'top_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: right
				add_settings_field(
					'right', // id
					'Right', // title
					array( $this, 'right_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: bottom
				add_settings_field(
					'bottom', // id
					'Bottom', // title
					array( $this, 'bottom_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: left
				add_settings_field(
					'left', // id
					'Left', // title
					array( $this, 'left_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: button_position
				add_settings_field(
					'button_position', // id
					'Button Position', // title
					array( $this, 'button_position_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: button_default
				add_settings_field(
					'button_default', // id
					'Default Button', // title
					array( $this, 'button_default_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_setting_section' // section
				);
				// Field: language (advanced)
				add_settings_field(
					'language', // id
					'Language', // title
					array( $this, 'language_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_advanced_section' // section
				);
				// Field: filters (advanced)
				add_settings_field(
					'filters', // id
					'Filter', // title
					array( $this, 'filters_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_advanced_section' // section
				);
				// Field: lazy (advanced; checkbox)
				add_settings_field(
					'lazy', // id
					'Lazy', // title
					array( $this, 'lazy_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_advanced_section' // section
				);
				// Field: alert (advanced; checkbox)
				add_settings_field(
					'alert', // id
					'Alert', // title
					array( $this, 'alert_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_advanced_section' // section
				);
				// Field: callback (advanced)
				add_settings_field(
					'callback', // id
					'Callback', // title
					array( $this, 'callback_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_advanced_section' // section
				);
				// Field: user (user; checkbox)
				add_settings_field(
					'user', // id
					'Catch user data', // title
					array( $this, 'user_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_user_section' // section
				);

				// Field: mobile (master; checkbox)
				add_settings_field(
					'mobile', // id
					'Disable for mobile', // title
					array( $this, 'mobile_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: filter front (master; checkbox)
				add_settings_field(
					'nofront', // id
					'Disable in the Front Page', // title
					array( $this, 'nofront_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: filter posts (master; checkbox)
				add_settings_field(
					'noposts', // id
					'Disable for Posts', // title
					array( $this, 'noposts_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: filter pages (master; checkbox)
				add_settings_field(
					'nopages', // id
					'Disable for Pages', // title
					array( $this, 'nopages_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: filter archive (master; checkbox)
				add_settings_field(
					'noarchive', // id
					'Disable for Archives', // title
					array( $this, 'noarchive_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: filter id (master)
				add_settings_field(
					'noid', // id
					'Filter by ID', // title
					array( $this, 'noid_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: logged (master; checkbox)
				add_settings_field(
					'logged', // id
					'Logged users only', // title
					array( $this, 'logged_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				// Field: master (master; checkbox)
				add_settings_field(
					'master', // id
					'Master Switch', // title
					array( $this, 'master_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_master_section' // section
				);
				if(bmr_is_curl()):
				// Field: API set (api; checkbox)
				add_settings_field(
					'api_set', // id
					'Connect to API', // title
					array( $this, 'api_set_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_api_section' // section
				);
				// Field: API key (api)
				add_settings_field(
					'api_key', // id
					'API Key', // title
					array( $this, 'api_key_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_api_section' // section
				);
				// Field: API excerpt (api)
				add_settings_field(
					'api_excerpt', // id
					'Default excerpt length', // title
					array( $this, 'api_excerpt_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_api_section' // section
				);
				// Field: API readmore (api)
				add_settings_field(
					'api_readmore', // id
					'Default "Read More" text', // title
					array( $this, 'api_readmore_callback' ), // callback
					'beamer-settings-admin', // page
					'beamer_settings_api_section' // section
				);
				endif;
		}

		// Sanitize fields
		public function beamer_settings_sanitize($input) {
			$sanitary_values = array();
			if ( isset( $input['product_id'] ) ) {
				$sanitary_values['product_id'] = sanitize_text_field( $input['product_id'] );
			}
			if ( isset( $input['selector'] ) ) {
				$sanitary_values['selector'] = sanitize_text_field( $input['selector'] );
			}
			// Advenced
			if ( isset( $input['display'] ) ) {
				$sanitary_values['display'] = $input['display'];
			}
			if ( isset( $input['top'] ) ) {
				$sanitary_values['top'] = sanitize_text_field( $input['top'] );
			}
			if ( isset( $input['right'] ) ) {
				$sanitary_values['right'] = sanitize_text_field( $input['right'] );
			}
			if ( isset( $input['bottom'] ) ) {
				$sanitary_values['bottom'] = sanitize_text_field( $input['bottom'] );
			}
			if ( isset( $input['left'] ) ) {
				$sanitary_values['left'] = sanitize_text_field( $input['left'] );
			}
			if ( isset( $input['button_position'] ) ) {
				$sanitary_values['button_position'] = $input['button_position'];
			}
			if ( isset( $input['button_default'] ) ) {
				$sanitary_values['button_default'] = $input['button_default'];
			}
			if ( isset( $input['language'] ) ) {
				$sanitary_values['language'] = sanitize_text_field( $input['language'] );
			}
			if ( isset( $input['filters'] ) ) {
				$sanitary_values['filters'] = sanitize_text_field( $input['filters'] );
			}
			if ( isset( $input['lazy'] ) ) {
				$sanitary_values['lazy'] = $input['lazy'];
			}
			if ( isset( $input['alert'] ) ) {
				$sanitary_values['alert'] = $input['alert'];
			}
			if ( isset( $input['callback'] ) ) {
				$sanitary_values['callback'] = sanitize_text_field( $input['callback'] );
			}
			// User
			if ( isset( $input['user'] ) ) {
				$sanitary_values['user'] = $input['user'];
			}
			// Master
			if ( isset( $input['mobile'] ) ) {
				$sanitary_values['mobile'] = $input['mobile'];
			}
			if ( isset( $input['nofront'] ) ) {
				$sanitary_values['nofront'] = $input['nofront'];
			}
			if ( isset( $input['noposts'] ) ) {
				$sanitary_values['noposts'] = $input['noposts'];
			}
			if ( isset( $input['nopages'] ) ) {
				$sanitary_values['nopages'] = $input['nopages'];
			}
			if ( isset( $input['noarchive'] ) ) {
				$sanitary_values['noarchive'] = $input['noarchive'];
			}
			if ( isset( $input['noid'] ) ) {
				$sanitary_values['noid'] = sanitize_text_field( $input['noid'] );
			}
			if ( isset( $input['logged'] ) ) {
				$sanitary_values['logged'] = $input['logged'];
			}
			if ( isset( $input['master'] ) ) {
				$sanitary_values['master'] = $input['master'];
			}
			if ( isset( $input['api_set'] ) ) {
				$sanitary_values['api_set'] = $input['api_set'];
			}
			if ( isset( $input['api_key'] ) ) {
				$sanitary_values['api_key'] = sanitize_text_field( $input['api_key'] );
			}
			if ( isset( $input['api_thumb'] ) ) {
				$sanitary_values['api_thumb'] = $input['api_thumb'];
			}
			if ( isset( $input['api_excerpt'] ) ) {
				$sanitary_values['api_excerpt'] = sanitize_text_field( $input['api_excerpt'] );
			}
			if ( isset( $input['api_readmore'] ) ) {
				$sanitary_values['api_readmore'] = sanitize_text_field( $input['api_readmore'] );
			}
			return $sanitary_values;
		}

			// Beamer Sections Info
			public function beamer_settings_section_info() {
				echo('<div class="bmrNotice">Your <strong>product ID</strong> is the code that appears at the top of the screen in your <a href="'.bmr_url('home', 'app', false).'" target="_blank" rel="nofollow">Beamer Dashboard</a></div><div>To set your <b>Beamer embed</b> just add your product-id. You can customize your embed with the advanced parameters. For more information please read our <a href="'.bmr_url('docs', 'www').'" target="_blank">Documentation.</a></div>');
			}

			// Beamer Sections Info
			public function beamer_settings_advanced_section_info() {
				echo('<div>Customize the <b>Beamer embed</b>. For more information on each parameter and customization option please read our <a href="'.bmr_url('docs', 'www').'" target="_blank">Documentation.</a></div>');
			}

			// Beamer User Info
			public function beamer_settings_user_section_info() {
				echo('<div><b>Beamer</b> can track the users info (name, surname and email) as long as they are logged in their Wordpress accounts (recommended only for Wordpress sites that have subscribers).</div>');
			}

			// Beamer Master Info
			public function beamer_settings_master_section_info() {
				echo('<div><b>Beamer</b> can be disabled in some devices or pages using general and specific filters.</div>');
			}

			// Beamer API Info
			public function beamer_settings_api_section_info() {
				if(bmr_is_curl()):
					echo('<div class="bmrNotice warning">This feature is currently in its <strong>beta phase.</strong> Please help us improve it by testing it and sending us feedback</div><div>The <b>Beamer API</b> will connect to your Wordpress site. Each post you publish in Wordpress will be also published in your Beamer feed. You can change the specific settings for each post and pick what to add and what to ignore during editing.</div>');
				else:
					echo('<div class="bmrNotice wrong">This feature requires the <strong>cURL library.</strong> If you\'re seeing this message it means that the library is not installed on your server. Please consult with your hosting provider.</div>');
				endif;
			}

		// Callbacks
			// Product ID
			public function product_id_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[product_id]" id="bmr-product_id" value="%s"><div class="bmrTip">This code identifies your product in Beamer. <span>Required</span></div>',
					isset( $this->beamer_settings_options['product_id'] ) ? esc_attr( $this->beamer_settings_options['product_id']) : ''
				);
			}
			// Selector
			public function selector_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[selector]" id="bmr-selector" value="%s"><div class="bmrTip">HTML id for the DOM element to be used as a trigger to show the panel. <span>Optional</span></div>',
					isset( $this->beamer_settings_options['selector'] ) ? esc_attr( $this->beamer_settings_options['selector']) : ''
				);
			}
			// Button Position
			public function display_callback() {
				?> <select name="beamer_settings_option_name[display]" id="bmr-display">
					<?php $selected = (isset( $this->beamer_settings_options['display'] ) && $this->beamer_settings_options['display'] === 'right') ? 'selected' : '' ; ?>
					<option value="right" <?php echo $selected; ?>>Right</option>
					<?php $selected = (isset( $this->beamer_settings_options['display'] ) && $this->beamer_settings_options['display'] === 'left') ? 'selected' : '' ; ?>
					<option value="left" <?php echo $selected; ?>>Left</option>
				</select> <div class="bmrTip">Side on which the Beamer panel will be shown in your site. <span>Optional</span></div> <?php
			}
			// Top
			public function top_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[top]" id="bmr-top" value="%s" placeholder="0"> <div class="bmrTip">Top position offset for the notification bubble.</div>',
					isset( $this->beamer_settings_options['top'] ) ? esc_attr( $this->beamer_settings_options['top']) : ''
				);
			}
			// Right
			public function right_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[right]" id="bmr-right" value="%s" placeholder="0"> <div class="bmrTip">Right position offset for the notification bubble. <span>Optional</span></div>',
					isset( $this->beamer_settings_options['right'] ) ? esc_attr( $this->beamer_settings_options['right']) : ''
				);
			}
			// Bottom
			public function bottom_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[bottom]" id="bmr-bottom" value="%s" placeholder="0"> <div class="bmrTip">Bottom position offset for the notification bubble. <span>Optional</span></div>',
					isset( $this->beamer_settings_options['bottom'] ) ? esc_attr( $this->beamer_settings_options['bottom']) : ''
				);
			}
			// Left
			public function left_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[left]" id="bmr-left" value="%s" placeholder="0"> <div class="bmrTip">Left position offset for the notification bubble. <span>Optional</span></div>',
					isset( $this->beamer_settings_options['left'] ) ? esc_attr( $this->beamer_settings_options['left']) : ''
				);
			}
			// Button Position
			public function button_position_callback() {
				?> <select name="beamer_settings_option_name[button_position]" id="bmr-button_position">
					<?php $selected = (isset( $this->beamer_settings_options['button_position'] ) && $this->beamer_settings_options['button_position'] === 'bottom-right') ? 'selected' : '' ; ?>
					<option value="bottom-right" <?php echo $selected; ?>>Bottom Right</option>
					<?php $selected = (isset( $this->beamer_settings_options['button_position'] ) && $this->beamer_settings_options['button_position'] === 'bottom-left') ? 'selected' : '' ; ?>
					<option value="bottom-left" <?php echo $selected; ?>>Bottom Left</option>
					<?php $selected = (isset( $this->beamer_settings_options['button_position'] ) && $this->beamer_settings_options['button_position'] === 'top-left') ? 'selected' : '' ; ?>
					<option value="top-left" <?php echo $selected; ?>>Top Left</option>
					<?php $selected = (isset( $this->beamer_settings_options['button_position'] ) && $this->beamer_settings_options['button_position'] === 'top-right') ? 'selected' : '' ; ?>
					<option value="top-right" <?php echo $selected; ?>>Top Right</option>
				</select> <div class="bmrTip">Position for the notification button (which opens the Beamer panel) that shows up when the selector parameter is not set. </div> <?php
			}
			// Button Default
			public function button_default_callback() {
				?> <select name="beamer_settings_option_name[button_default]" id="bmr-button_default">
					<?php $selected = (isset( $this->beamer_settings_options['button_default'] ) && $this->beamer_settings_options['button_default'] === 'on') ? 'selected' : '' ; ?>
					<option value="on" <?php echo $selected; ?>>ON</option>
					<?php $selected = (isset( $this->beamer_settings_options['button_default'] ) && $this->beamer_settings_options['button_default'] === 'off') ? 'selected' : '' ; ?>
					<option value="off" <?php echo $selected; ?>>OFF</option>
				</select> <div class="bmrTip">If this option is <b>turned off</b> the default button will not show up, even if there's no selector or trigger present.</div> <?php
			}
			// Language
			public function language_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[language]" id="bmr-language" value="%s" placeholder="EN"> <div class="bmrTip">Retrieve only posts that have a translation in this language. <span>Optional</span></div>',
					isset( $this->beamer_settings_options['language'] ) ? esc_attr( $this->beamer_settings_options['language']) : ''
				);
			}
			// Filters
			public function filters_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[filters]" id="bmr-filters" value="%s"> <div class="bmrTip">Retrieve only posts with a segment filter that matches or includes this value. <span>Optional</span></div>',
					isset( $this->beamer_settings_options['filters'] ) ? esc_attr( $this->beamer_settings_options['filters']) : ''
				);
			}
			// Lazy
			public function lazy_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[lazy]" id="bmr-lazy" value="lazy" %s> <label for="lazy">If <b>checked</b>, the Beamer plugin won’t be initialized until the method Beamer.init is called.</label>',
					( isset( $this->beamer_settings_options['lazy'] ) && $this->beamer_settings_options['lazy'] === 'lazy' ) ? 'checked' : ''
				);
			}
			// Alert
			public function alert_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[alert]" id="bmr-alert" value="alert" %s> <label for="alert">If <b>checked</b>, the selector parameter will be ignored and it won\'t open the panel when clicked (only with the methods Beamer.show and Beamer.hide</label>',
					( isset( $this->beamer_settings_options['alert'] ) && $this->beamer_settings_options['alert'] === 'alert' ) ? 'checked' : ''
				);
			}
			// Callbacks
			public function callback_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[callback]" id="bmr-callback" value="%s"> <div class="bmrTip">Function to be called once the plugin is initialized. Learn more in our <a href="https://www.getbeamer.com/docs/" target="_blank">documentation page</a></div>',
					isset( $this->beamer_settings_options['callback'] ) ? esc_attr( $this->beamer_settings_options['callback']) : ''
				);
			}
			// User
			public function user_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[user]" id="bmr-user" value="user" %s> <label for="user">If <b>checked</b>, the Beamer plugin will register the user\'s name, surname and email as long as they are logged to be shown in your accounts statistics</label>',
					( isset( $this->beamer_settings_options['user'] ) && $this->beamer_settings_options['user'] === 'user' ) ? 'checked' : ''
				);
			}
			// Mobile
			public function mobile_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[mobile]" id="bmr-mobile" value="mobile" %s> <label for="mobile">If <b>checked</b>, the Beamer plugin will not be called on any mobile device</label>',
					( isset( $this->beamer_settings_options['mobile'] ) && $this->beamer_settings_options['mobile'] === 'mobile' ) ? 'checked' : ''
				);
			}
			// No Front
			public function nofront_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[nofront]" id="bmr-nofront" value="nofront" %s> <label for="nofront">If <b>checked</b>, the Beamer plugin will not be called on the front page</label>',
					( isset( $this->beamer_settings_options['nofront'] ) && $this->beamer_settings_options['nofront'] === 'nofront' ) ? 'checked' : ''
				);
			}
			// No Posts
			public function noposts_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[noposts]" id="bmr-noposts" value="noposts" %s> <label for="noposts">If <b>checked</b>, the Beamer plugin will not be called on any Post</label>',
					( isset( $this->beamer_settings_options['noposts'] ) && $this->beamer_settings_options['noposts'] === 'noposts' ) ? 'checked' : ''
				);
			}
			// No Pages
			public function nopages_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[nopages]" id="bmr-nopages" value="nopages" %s> <label for="nopages">If <b>checked</b>, the Beamer plugin will not be called on any Pages</label>',
					( isset( $this->beamer_settings_options['nopages'] ) && $this->beamer_settings_options['nopages'] === 'nopages' ) ? 'checked' : ''
				);
			}
			// No Archive
			public function noarchive_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[noarchive]" id="bmr-noarchive" value="noarchive" %s> <label for="noarchive">If <b>checked</b>, the Beamer plugin will not be called on any Archive, Category or Tag</label>',
					( isset( $this->beamer_settings_options['noarchive'] ) && $this->beamer_settings_options['noarchive'] === 'noarchive' ) ? 'checked' : ''
				);
			}
			// No ID
			public function noid_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[noid]" id="bmr-noid" value="%s"> <div class="bmrTip">Add IDs separated by commas. Beamer will be deactivated for all pages or posts that have those IDs</div>',
					isset( $this->beamer_settings_options['noid'] ) ? esc_attr( $this->beamer_settings_options['noid']) : ''
				);
			}
			// Logged
			public function logged_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[logged]" id="bmr-logged" value="logged" %s> <label for="logged">Beamer will be shown only for logged in users if this is <b>checked</b></label>',
					( isset( $this->beamer_settings_options['logged'] ) && $this->beamer_settings_options['logged'] === 'logged' ) ? 'checked' : ''
				);
			}
			// Master
			public function master_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[master]" id="bmr-master" value="master" %s> <label for="master" style="color:#fd5c63;">Beamer will be disabled completely if this is <b>checked</b> (in all devices)</label>',
					( isset( $this->beamer_settings_options['master'] ) && $this->beamer_settings_options['master'] === 'master' ) ? 'checked' : ''
				);
			}
			// API Set
			public function api_set_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[api_set]" id="bmr-api_set" value="api_set" %s> <label for="api_set">If <b>checked</b>, the Beamer plugin will connect to the Beamer API and your new posts will be also published in the Beamer feed.</label>',
					( isset( $this->beamer_settings_options['api_set'] ) && $this->beamer_settings_options['api_set'] === 'api_set' ) ? 'checked' : ''
				);
			}
			// API Key
			public function api_key_callback() {
				printf(
					'<input class="regular-text" type="text" name="beamer_settings_option_name[api_key]" id="bmr-api_key" value="%s"><div class="bmrTip">This secret code identifies your calls to Beamer. You can get your API key in your <a href="'.bmr_url('settings', 'app', false).'" target="_blank" rel="nofollow">Beamer Dashboard > Settings > API</a> <span>Required</span></div>',
					isset( $this->beamer_settings_options['api_key'] ) ? esc_attr( $this->beamer_settings_options['api_key']) : ''
				);
			}
			// API Set
			public function api_thumb_callback() {
				printf(
					'<input type="checkbox" name="beamer_settings_option_name[api_thumb]" id="bmr-api_thumb" value="api_thumb" %s> <label for="api_thumb">If <b>checked</b>, the Beamer plugin will not use the Featured Image of your post (if it exists) and display only the post content.</label>',
					( isset( $this->beamer_settings_options['api_thumb'] ) && $this->beamer_settings_options['api_thumb'] === 'api_thumb' ) ? 'checked' : ''
				);
			}
			// API Excerpt
			public function api_excerpt_callback() {
				printf(
					'<input class="regular-text" type="number" placeholder="160" name="beamer_settings_option_name[api_excerpt]" id="bmr-api_excerpt" value="%s" style="width:100px;"><div class="bmrTip">The default maximum length of the text that will be shared with your posts (You can pick full length in each post\'s Advanced Options). <span>Optional</span></div>',
					isset( $this->beamer_settings_options['api_excerpt'] ) ? esc_attr( $this->beamer_settings_options['api_excerpt']) : ''
				);
			}
			// API Read More
			public function api_readmore_callback() {
				printf(
					'<input class="regular-text" type="text" placeholder="Read more" name="beamer_settings_option_name[api_readmore]" id="bmr-api_readmore" value="%s"><div class="bmrTip">The default text of the link that will be shared with your posts (You can change the specific text in each post\'s Advanced Options). <span>Optional</span></div>',
					isset( $this->beamer_settings_options['api_readmore'] ) ? esc_attr( $this->beamer_settings_options['api_readmore']) : ''
				);
			}
	}

	if ( is_admin() )
		$beamer_settings = new BeamerSettings();