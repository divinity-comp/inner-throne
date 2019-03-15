<?php
/*
Plugin Name: ContentStudio
Description: ContentStudio provides you with powerful blogging & social media tools to keep your audience hooked by streamlining the process for you to discover and share engaging content on multiple blogging & social media networks
Version: 1.0.5
Author: ContentStudio
Author URI: http://contentstudio.io/
Plugin URI: http://contentstudio.io/
*/

// Check for existing class
if (! class_exists('contentstudio')) {
    class ContentStudio {

        protected $api_url = 'https://app.contentstudio.io/';
        protected $assets = 'https://contentstudio.io/img';
        protected $contentstudio_id = '';
        protected $blog_id = '';
        const INVALID_MESSAGE = 'Invalid API Key, please make sure you have correct API key added.';

        /**
         * Add calendar and settings link to the admin menu
         */

        public function __construct()
        {
            $this->hooks();
            register_activation_hook(__FILE__, array($this, 'activation'));
            if (is_admin()) {
                $this->register_admin_hooks();
            }
            $this->register_global_hooks();
            $this->check_plugin_update();

            // style registering
            wp_enqueue_style('contentstudio-dashboard', plugins_url('/contentstudio-plugin/_inc/main.css'), array(), '1.0.0');
        }

        public function check_plugin_update()
        {
            require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
            $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                $this->api_url . 'plugin.json',
                __FILE__,
                'contentstudio'
            );
        }


        public function add_menu()
        {
            add_menu_page('ContentStudio Publisher', 'ContentStudio', 'edit_posts', 'contentstudio_settings', array($this, 'connection_page'),
                $this->assets . '/favicon/favicon-16x16.png',
                '50.505');
            // Settings link to the plugin listing page.
        }

        public function register_global_hooks()
        {
            add_action('init', array($this, 'check_token'));
            add_action('init', array($this, 'set_token'));
            add_action('init', array($this, 'get_blog_authors'));
            add_action('init', array($this, 'get_blog_categories'));
            add_action('init', array($this, 'create_new_post'));
            add_action('init', array($this, 'is_installed'));
            add_action('init', array($this, 'unset_token'));
            add_action('init', array($this, 'is_upload_dir_exists'));
            add_action('wp_head', array($this, 'add_custom_stylesheet'));
            if (! function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

        }

        function add_custom_stylesheet()
        {
            global $post;
            if ($post) {
                if (isset($post->ID)) {
                    $meta_description = get_post_meta($post->ID, 'contentstudio_description');
                    if ($meta_description) {
                        echo '<meta name="description" content="' . $meta_description[0] . '" />' . "\n";
                    }
                }

            }
            echo '<script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script><style>
                .post_prev_block {
                    margin-bottom: 15px;
                    display: block;
                }
                .article__block {
                    text-align: left;
                }
                .article__block .inner:after {
                    clear: both;
                    content: "";
                    display: block;
                }
                .article__block .img_box_main {
                    float: left;
                    width: 185px;
                }
                .article__block .img_box_main .img_box {
                    overflow: hidden;
                    position: relative;
                    z-index: 1;
                }
                .article__block .img_box_main .img_box img {
                    max-width: 100%;
                }
                .article__block .img_box_main .img_box:hover .icon_control {
                    top: 0;
                }
                .article__block .img_box_main.left_side {
                    float: left;
                    margin-right: 15px;
                }
                .article__block .img_box_main.right_side {
                    float: right;
                    margin-left: 15px;
                }
                .article__block p,
                .article__block h1 {
                    word-wrap: break-word;
                    display: block;
                }
                .article__block a {
                    word-wrap: break-word;
                    color: #337ab7 !important;
                    font-size: 14px !important;
                }
                .article__block p {
                    font-size: 14px!important;
                    margin: 5px 0!important;
                }
                .article__block h1 {
                    margin-bottom: 5px;
                    font-size: 20px!important;
                    font-weight: 500!important;
                }
                .box_element_block .inner {
                    min-width: 420px;
                }
                .box_element_block .inner .iframe .iframe_inner {
                    position: relative;
                    padding-bottom: 56.25%;
                    height: 0;
                    display: block;
                }
                .box_element_block .inner .iframe .iframe_inner iframe {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    margin-bottom: 10px;
                }
                .box_element_block .inner .photo__block img {
                    max-width: 100%;
                    margin-bottom: 10px;
                }
                .box_element_block .inner .add_desc {
                    display: block;
                }
                .box_element_block .inner .add_desc p {
                    font-size: 14px;
                    word-wrap: break-word;
                }
                .box_element_block.text-left {
                    text-align: left;
                }
                .box_element_block.text-center {
                    text-align: center;
                }
                .box_element_block.text-right {
                    text-align: right;
                }
                </style>';
            echo '<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
            echo '<script src="//connect.facebook.net/en_US/sdk.js#xfbml=1&amp;version=v2.5" async></script>';

        }

        /**
         * Registers admin only hooks.
         */
        public function register_admin_hooks()
        {
            add_action('admin_menu', array($this, 'add_menu'));
            add_filter('plugin_action_links', array($this, 'plugin_settings_link'), 2, 2);


            // ajax requests
            add_action('wp_ajax_add_api_key', array($this, 'add_api_key'));
            // Add check for activation redirection
            add_action('admin_init', array($this, 'activation_redirect'));

            // load resources

        }

        public function hooks()
        {
            register_activation_hook(__FILE__, array($this, 'activation'));
            register_deactivation_hook(__FILE__, array($this, 'deactivation'));
        }

        // plugin activation, deactivation and uninstall hooks

        public function activation()
        {
            register_uninstall_hook(__FILE__, array('contentstudio', 'uninstall'));
            // Set redirection to true
            add_option('contentstudio_redirect', true);

        }

        // on plugin deactivation

        public function deactivation()
        {
            delete_option('contentstudio_redirect');
            delete_option('contentstudio_token');
        }

        // on plugin removal
        public function uninstall()
        {
            delete_option('contentstudio_redirect');
            delete_option('contentstudio_token');
        }


        /**
         * Checks to see if the plugin was just activated to redirect them to settings
         */
        public function activation_redirect()
        {


            if (get_option('contentstudio_redirect', false)) {
                // Redirect to settings page
                if (delete_option('contentstudio_redirect')) {
                    // If the plugin is being network activated on a multisite install
                    if (is_multisite() && is_network_admin()) {
                        $redirect_url = network_admin_url('plugins.php');
                    }
                    else {
                        $redirect_url = 'admin.php?page=contentstudio_settings';
                    }

                    if (wp_safe_redirect($redirect_url)) {
                        // NOTE: call to exit after wp_redirect is per WP Codex doc:
                        //       http://codex.wordpress.org/Function_Reference/wp_redirect#Usage
                        exit;
                    }
                }
            }
        }

        // filters plugins section

        public function plugin_settings_link($actions, $file)
        {
            if (false !== strpos($file, 'plugin')) {
                $url = "admin.php?page=contentstudio_settings";
                $actions['settings'] = '<a href="' . esc_url($url) . '">Settings</a>';
            }

            return $actions;
        }


        // ajax section

        public function add_api_key()
        {
            if (isset($_POST['data'])) {
                if (isset($_POST['data']['key'])) {
                    if (strlen($_POST['data']['key']) == 0) {
                        echo json_encode(['status' => false, 'message' => 'Please enter your API key']);
                        die();
                    }
                    $this->sanitize($_POST['data']['key']);

                    $response = json_decode($this->is_connected($_POST['data']['key']), true);
                    if ($response['status'] == false) {
                        echo json_encode($response);
                        die();
                    }
                    if ($response['status'] == true) {
                        // if successfully verified.
                        if (add_option('contentstudio_token', $_POST['data']['key']) == false) {
                            update_option('contentstudio_token', $_POST['data']['key']);
                        }

                        echo json_encode(['status' => true, 'message' => 'Your blog has been successfully connected with ContentStudio.']);
                        die();
                    }
                    else {
                        echo json_encode(['status' => false, 'message' => self::INVALID_MESSAGE]);
                        die();
                    }

                }
                else {
                    echo json_encode(['status' => false, 'message' => 'Please enter your API key']);
                    die();

                }
            }
            else {
                echo json_encode(['status' => false, 'message' => 'Please enter your API key']);
                die();
            }

        }

        public function is_installed()
        {
            if (isset($_REQUEST['cs_plugin_installed']) && $_REQUEST['cs_plugin_installed']) {
                echo json_encode(['status' => true, 'message' => 'ContentStudio plugin installed']);
                die();
            }

        }

        // check token direct ajax request.
        public function check_token()
        {
            if (isset($_REQUEST['token_validity']) && isset($_REQUEST['token'])) {
                $valid = $this->do_validate_token($_REQUEST['token']);

                // server side token validation required.

                if ($valid) {
                    echo json_encode(['status' => true, 'message' => 'Token validated successfully.']);
                    die();
                }
                else {
                    echo json_encode(['status' => false, 'message' => self::INVALID_MESSAGE]);
                    die();
                }


            }
        }


        // validate token from the server to local.
        public function do_validate_token($token)
        {
            $this->sanitize($token);
            if (get_option('contentstudio_token') == $token) {
                return true;
            }

            return false;
        }

        // check token direct ajax request.
        public function set_token()
        {
            if (isset($_REQUEST['set_token']) && isset($_REQUEST['token'])) {
                /*$valid = get_option('contentstudio_token');
                if (!$valid) {*/
                $this->sanitize($_REQUEST['token']);
                update_option('contentstudio_token', $_REQUEST['token']);

                echo json_encode(['status' => true, 'message' => 'Your token has been updated successfully!']);
                die();
                /*}
                else {
                    // TODO: need to brainstorm here.
                    echo json_encode(['status' => false, 'message' => 'Token already exists on your website.']);
                    die();
                }*/
            }
        }


        // unset token ajax request

        public function unset_token()
        {
            if (isset($_REQUEST['unset_token']) && isset($_REQUEST['token'])) {

                $valid = $this->do_validate_token($_REQUEST['token']);

                if ($valid) {
                    delete_option('contentstudio_token');
                    echo json_encode(['status' => true, 'message' => 'Your API key has been removed successfully!']);
                    die();
                }
                else {
                    // TODO: need to brainstorm here.
                    echo json_encode(['status' => false, 'message' => 'API key mismatch, please enter the valid API key.']);
                    die();
                }
            }
        }

        public function is_connected($token)
        {
            $payload = [
                'body' => array(
                    'token'       => $token,
                    "name"        => get_bloginfo("name"),
                    "description" => get_bloginfo("description"),
                    "wpurl"       => get_bloginfo("wpurl"),
                    "url"         => get_bloginfo("url"),
                )
            ];

            return wp_remote_post($this->api_url . 'blog/wordpress_plugin', $payload)['body'];
        }

        public function get_blog_authors()
        {
            if (isset($_REQUEST['token']) && isset($_REQUEST['authors'])) {
                $valid = $this->do_validate_token($_REQUEST['token']);
                if ($valid) {
                    $authors = get_users([['role__in' => ['editor', 'administrator', 'author']]]);
                    $return_authors = [];
                    foreach ($authors as $author) {
                        $return_authors[] = [
                            "display_name" => $author->data->display_name,
                            "user_id"      => $author->ID
                        ];
                    }
                    echo json_encode($return_authors);
                    die();
                }
                else {
                    echo json_encode(['status' => false, 'message' => self::INVALID_MESSAGE]);
                    die();
                }
            }

        }

        public function get_blog_categories()
        {
            if (isset($_REQUEST['token']) && isset($_REQUEST['categories'])) {
                $valid = $this->do_validate_token($_REQUEST['token']);
                if ($valid) {
                    $args = array(
                        "hide_empty" => 0,
                        "type"       => "post",
                        "orderby"    => "name",
                        "order"      => "ASC"
                    );
                    $categories = get_categories($args);
                    $return_categories = [];

                    foreach ($categories as $category) {
                        $return_categories[] = [
                            "name"    => $category->cat_name,
                            "term_id" => $category->term_id
                        ];

                    }
                    echo json_encode($return_categories);
                    die();
                }
                else {
                    echo json_encode(['status' => false, 'message' => self::INVALID_MESSAGE]);
                    die();
                }
            }

        }


        public function is_post_exists()
        {

        }

        public function is_upload_dir_exists()
        {
            if (isset($_REQUEST) && isset($_REQUEST['upload_dir_exists']) && isset($_REQUEST['token'])) {
                $valid = $this->do_validate_token($_REQUEST['token']);
                if ($valid) {
                    $base_dir = wp_upload_dir()['basedir'];
                    if (! is_dir($base_dir)) {
                        echo json_encode(['status' => true, 'message' => 'Your WordPress wp-content/uploads/ directory does not exist. Please create a directory first to enable featured images/media uploads.']);
                    }
                    else {
                        echo json_encode(['status' => false, 'message' => 'Directory already exists.']);
                    }
                    die();
                }
                else {
                    echo json_encode(['status' => false, 'message' => self::INVALID_MESSAGE]);
                    die();
                }
            }

        }


        public function create_new_post()
        {
            if (isset($_REQUEST) && isset($_REQUEST['create_post']) && isset($_REQUEST['token'])) {


                $valid = $this->do_validate_token($_REQUEST['token']);
                if ($valid) {
                    if (isset($_REQUEST['post'])) {


                        $post_title = $_REQUEST['post']['post_title'];
                        if (isset($post_title) && $post_title) {

                            global $wpdb;
                            $post_title = esc_sql($post_title);
                            $sql = $wpdb->prepare("select ID from " . $wpdb->posts . " where post_title='%s' AND  post_status = 'publish'", $post_title);
                            $get_posts_list = $wpdb->get_results($sql);
                            if (count($get_posts_list)) {
                                echo json_encode(['status' => false, 'message' => "Post already exists on your blog with title '$post_title'."]);
                                die();
                            }

                        }


                        $categories = explode(',', $_REQUEST['post']['post_category']);

                        $this->kses_remove_filters();

                        $post = wp_insert_post([
                            'post_title'    => $_REQUEST['post']['post_title'],
                            'post_author'   => $_REQUEST['post']['post_author'],
                            'post_content'  => $_REQUEST['post']['post_content'],
                            'post_status'   => $_REQUEST['post']['post_status'],
                            'post_category' => $categories,
                        ]);

                        if (! $post or $post == 0) {
                            $post = wp_insert_post([
                                'post_author'   => $_REQUEST['post']['post_author'],
                                'post_content'  => $_REQUEST['post']['post_content'],
                                'post_status'   => $_REQUEST['post']['post_status'],
                                'post_category' => $categories,
                            ]);
                            global $wpdb;
                            $wpdb->update($wpdb->posts, ['post_title' => (string)$post_title], ['ID' => $post]);
                        }

                        $get_post = get_post($post);

                        // setting up meta description

                        $meta_description = null;
                        if (isset($_REQUEST['post']['post_meta_description'])) {
                            $meta_description = esc_sql($_REQUEST['post']['post_meta_description']);

                        }
                        if ($meta_description) {
                            if (! get_post_meta($get_post->ID, 'contentstudio_description')) {
                                add_post_meta($get_post->ID, 'contentstudio_description', $meta_description, true);
                            }
                            else {
                                update_post_meta($get_post->ID, 'contentstudio_description', $meta_description);
                            }
                        }


                        if (isset($_REQUEST['post']['featured_image']) && $_REQUEST['post']['featured_image']) {
                            // perform http request to see the status code of the image.
                            $status_code = wp_remote_get($_REQUEST['post']['featured_image'])['response']['code'];

                            // if the status is valid process for upload.

                            if ($status_code == 301 || $status_code == 200) {
                                $img = $this->generate_image($_REQUEST['post']['featured_image'], $post);
                                if ($img['status']) {
                                    echo json_encode([
                                        'status' => true, 'post_id' => $get_post->ID, 'link' => $get_post->guid
                                    ]);
                                    die();
                                }
                                else {
                                    echo json_encode([
                                        'status'          => false,
                                        'warning_message' => $img['message'],
                                        'post_id'         => $get_post->ID,
                                        'link'            => $get_post->guid
                                    ]);
                                    die();
                                }
                            }
                            else {
                                echo json_encode([
                                    'status'          => false,
                                    'warning_message' => 'Post featured image seems to be down. Image HTTP status code is ' . $status_code,
                                    'post_id'         => $get_post->ID,
                                    'link'            => $get_post->guid
                                ]);
                                die();
                            }
                        }
                        else {
                            echo json_encode(['status' => true, 'post_id' => $get_post->ID, 'link' => $get_post->guid]);
                            die();
                        }


                    }
                    else {
                        echo json_encode(['status' => false, 'message' => 'Invalid API arguments provided.']);
                        die();

                    }
                }
                else {
                    echo json_encode(['status' => false, 'message' => self::INVALID_MESSAGE]);
                    die();
                }

            }
        }

        public function generate_image($image_url, $post_id)
        {

            $url_stripped = false;
            $url_content_fetch = $image_url;
            $pos = strpos($image_url, '?');
            if ($pos !== false) {
                $url_stripped = true;
                $image_url = substr($image_url, 0, $pos);
            }


            $pos = strpos($image_url, '#');
            if ($pos !== false) {
                $url_stripped = true;
                $image_url = substr($image_url, 0, $pos);
            }


            $upload_dir = wp_upload_dir();

            if (isset($upload_dir['error']) && $upload_dir['error']) {

                return ['status' => false, 'message' => $upload_dir['error']];
            }
            if ($url_stripped) {
                $image_data = file_get_contents($url_content_fetch);
            }
            else {
                $image_data = file_get_contents($image_url);
            }


//            $image_data = file_get_contents($image_url);
            if (strpos($image_url, 'contentstudioio.s3.amazonaws.com') !== false) {
                $filename = basename($image_url);

                $img_headers = wp_remote_get($image_url);
                $content_type = $img_headers['headers']['content-type'];
                if ($content_type == 'image/png') {
                    $filename .= '.png';
                }
                elseif ($content_type == 'image/jpg' || $content_type == 'image/jpeg') {
                    $filename .= '.jpg';
                }
                elseif ($content_type == 'image/gif') {
                    $filename .= '.gif';
                }
            }
            elseif (strpos($image_url, 'ytimg.com') !== false) {
                $split = explode('/', $image_url);
                $filename = $split[4] . '_' . basename($image_url);
            }
            else {
                $filename = basename($image_url);
            }

            if (wp_mkdir_p($upload_dir['path'])) $file = $upload_dir['path'] . '/' . $filename;
            else                                    $file = $upload_dir['basedir'] . '/' . $filename;
            $resp = file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null);

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name($filename),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $file, $post_id);

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            $res1 = wp_update_attachment_metadata($attach_id, $attach_data);
            $res2 = set_post_thumbnail($post_id, $attach_id);
            if ($res2) {
                return ['status' => true];
            }
            else {
                return ['status' => false, 'message' => 'An Unknown error occurred while uploading media file.'];
            }

        }


        // callbacks section for page rendering

        public function connection_page()
        {
            if (! current_user_can('edit_posts')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            $token = get_option('contentstudio_token');
            $response = json_decode($this->is_connected($token), true);
            $response['reconnect'] = false;
            if (isset($_GET['reconnect']) && $_GET['reconnect'] == 'true') {
                $response['reconnect'] = true;
            }

            $response['security_plugins'] = $this->installed_security_plugins();
            // Save the data to the error log so you can see what the array format is like.


            $this->load_resources();

            include(sprintf("%s/page.php", dirname(__FILE__)));
        }

        // analyzing the security plugins on the user website.
        function installed_security_plugins()
        {
            $activated_plugins = get_option('active_plugins');
            $response = [
                'wordfence'                       => $this->is_plugin_activated($activated_plugins, 'wordfence/wordfence.php'),
                'jetpack'                         => $this->is_plugin_activated($activated_plugins, 'jetpack/jetpack.php'),
                '6scan'                           => $this->is_plugin_activated($activated_plugins, '6scan-protection/6scan.php'),
                'wp_security_scan'                => $this->is_plugin_activated($activated_plugins, 'wp-security-scan/index.php'),
                'wp_all_in_one_wp_security'       => $this->is_plugin_activated($activated_plugins, 'all-in-one-wp-security-and-firewall/wp-security.php'),
                'bulletproof_security'            => $this->is_plugin_activated($activated_plugins, 'bulletproof-security/bulletproof-security.php'),
                'better_wp_security'              => $this->is_plugin_activated($activated_plugins, 'better-wp-security/better-wp-security.php'),
                'limit_login_attempts_reloaded'   => $this->is_plugin_activated($activated_plugins, 'limit-login-attempts-reloaded/limit-login-attempts-reloaded.php'),
                'limit_login_attempts'            => $this->is_plugin_activated($activated_plugins, 'limit-login-attempts/limit-login-attempts.php'),
                'lockdown_wp_admin'               => $this->is_plugin_activated($activated_plugins, 'lockdown-wp-admin/lockdown-wp-admin.php'),
                'miniorange_limit_login_attempts' => $this->is_plugin_activated($activated_plugins, 'miniorange-limit-login-attempts/mo_limit_login_widget.php'),
                'wp_cerber'                       => $this->is_plugin_activated($activated_plugins, 'wp-cerber/wp-cerber.php'),
                'wp_limit_login_attempts'         => $this->is_plugin_activated($activated_plugins, 'wp-limit-login-attempts/wp-limit-login-attempts.php'),
                'sucuri_scanner'                  => $this->is_plugin_activated($activated_plugins, 'sucuri-scanner/sucuri.php'),
                //                'limit_login_attempts_reloaded' => $this->is_plugin_activated($all_plugins, 'limit-login-attempts-reloaded/limit-login-attempts-reloaded.php'),
            ];

            return $response;
        }

        function is_plugin_activated($plugins_list, $file_name)
        {
            if (in_array($file_name, $plugins_list)) {
                return true;
            }

            return false;
        }

        // style loading

        function load_resources()
        {
            wp_enqueue_style('contentstudio.css', plugin_dir_url(__FILE__) . '_inc/contentstudio.css', [], 0.01, false);
            wp_enqueue_script('notify.min.js', plugin_dir_url(__FILE__) . '_inc/notify.min.js', array('jquery'), 0.01, false);
            wp_enqueue_script('helper.js', plugin_dir_url(__FILE__) . '_inc/helper.js', array('jquery'), 0.01, false);
        }


        // helpers methods

        // http methods

        public function prepare_request($url, $body)
        {
            $params = array(
                'method' => 'POST',
                'body'   => $this->array_decode_entities($body),
            );

            return $this->perform_request($this->api_url . $url, $params);
        }

        public function perform_request($url, $params = null)
        {
            $http = new WP_Http;

            $out = $this->perform_http_request($http, $url, false, $params);

            if (is_wp_error($out)) {
                $out = $this->perform_http_request($http, $url, true, $params);
            }

            return $out;
        }

        public function perform_http_request($http, $url, $skip_ssl_verify = false, $params = null)
        {

            if (isset($skip_ssl_verify) && (true === $skip_ssl_verify)) {
                // For the CURL SSL verifying, some websites does not have the valid SSL certificates.
                add_filter('https_ssl_verify', '__return_false');
                add_filter('https_local_ssl_verify', '__return_false');
            }

            if (isset($params)) {
                /** @noinspection PhpUndefinedMethodInspection */
                return $http->request($url, $params);
            }
            else {
                /** @noinspection PhpUndefinedMethodInspection */
                return $http->request($url);
            }
        }


        // sanitizing data

        public function array_decode_entities($array)
        {
            $new_array = array();

            foreach ($array as $key => $string) {
                if (is_string($string)) {
                    $new_array[$key] = html_entity_decode($string, ENT_QUOTES);
                }
                else {
                    $new_array[$key] = $string;
                }
            }

            return $new_array;
        }

        public function sanitize(&$param = '')
        {
            if (is_string($param)) {
                $param = esc_sql($param);
                $param = esc_html($param);
            }
        }

        function kses_remove_filters()
        {
            // Post filtering
            remove_filter('content_save_pre', 'wp_filter_post_kses');
            remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
            remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
        }


    }

    return new ContentStudio();

}
