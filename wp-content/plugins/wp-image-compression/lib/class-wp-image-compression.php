<?php
// require_once(__DIR__ . '../vendor/autoload.php');
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

require_once dirname(dirname(__FILE__)) . '/publitio_api.php';

// Please update xxxx with your key and yyyy with your secret

/** Load all of the necessary class files for the plugin */
use lsolesen\pel\Pel;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;


class Wp_Image_compression {

	private $id;
	private $compression_settings = array();
	private $thumbs_data = array();
	private $backup_before_compression = 0;

	function __construct() {

		if (!class_exists('Optimisationio_Dashboard')) {
			require_once 'class-optimisationio-dashboard.php';
		}

		Optimisationio_Dashboard::init();
        

		$this->compression_settings = unserialize(get_option('_wpimage_options'));

		$this->backup_before_compression = isset($this->compression_settings['backup_before_compression']) ? $this->compression_settings['backup_before_compression'] : false;

		add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts'));
		add_action('wp_ajax_wpimage_request', array(&$this, 'wpimage_media_library_ajax_callback'));
		add_action('manage_media_custom_column', array(&$this, 'fill_media_columns'), 10, 2);
		add_filter('manage_media_columns', array(&$this, 'add_media_columns'));

		add_action('add_attachment', array(&$this, 'wpimage_media_uploader_callback'));

		add_action('wp_loaded', array($this, 'exe_before_wpheader'));

		if (!is_admin()) {
			//lazyl
			global $wpImagelazySizesDefaults;

			$settings = get_option('_wpimage_lazyload_options', $wpImagelazySizesDefaults);

			if (isset($settings) && $settings != '') {
				$settings = unserialize($settings);
			}

			if (isset($settings['enable_lazyload']) && $settings['enable_lazyload'] == 'true') {
				add_action('wp_enqueue_scripts', array(&$this, 'add_lazyloading_styles'), 1);
				add_action('wp_enqueue_scripts', array(&$this, 'add_lazyloading_scripts'), 200);
				// Run this later, so other content filters have run, including image_add_wh on WP.com
				add_filter('the_content', array(&$this, 'filter_images'), 200);
				add_filter('post_thumbnail_html', array(&$this, 'filter_images'), 200);
				add_filter('widget_text', array(&$this, 'filter_images'), 200);
				if ($settings['lazyload_iframe'] != 'false') {
					add_filter('oembed_result', array(&$this, 'filter_iframes'), 200);
					add_filter('embed_oembed_html', array(&$this, 'filter_iframes'), 200);
				}
				add_filter('get_avatar', array(&$this, 'filter_avatar'), 200);
			}
		}
	}

	private function get_js_config() {
		global $wpImagelazySizesDefaults;
		$settings = unserialize(get_option('_wpimage_lazyload_options', $wpImagelazySizesDefaults));
		return array(
			'expand' => $settings['lazyload_expand'],
			'preloadAfterLoad' => $settings['lazyload_preloadAfterLoad'],
		);
	}

	private function _add_class($htmlString = '', $newClass) {

		$pattern = '/class="([^"]*)"/';

		// Class attribute set.
		if (preg_match($pattern, $htmlString, $matches)) {
			$definedClasses = explode(' ', $matches[1]);
			if (!in_array($newClass, $definedClasses, true)) {
				$definedClasses[] = $newClass;
				$htmlString = str_replace(
					$matches[0],
					sprintf('class="%s"', implode(' ', $definedClasses)),
					$htmlString
				);
			}
			// Class attribute not set.
		} else {
			$htmlString = preg_replace('/(\<.+\s)/', sprintf('$1class="%s" ', $newClass), $htmlString);
		}

		return $htmlString;
	}

	/*
		     *  Adds wpimage fields and settings to Settings->Media settings page
	*/
	public function add_lazyloading_styles() {
		wp_enqueue_style('wpimage_lazyloading', plugins_url(ltrim('css/lazysizes.min.css', '/'), dirname(__FILE__)), array(), "1.1");
	}

	public function add_lazyloading_scripts() {
		global $wpImagelazySizesDefaults;
		$settings = unserialize(get_option('_wpimage_lazyload_options', $wpImagelazySizesDefaults));
		wp_enqueue_script('wpimage_lazyloading', plugins_url(ltrim('build/wp-lazysizes.min.js'), dirname(__FILE__)), array(), "1.1", false);

		if ($settings['lazyload_optimumx'] !== 'false') {
			wp_enqueue_script(
				'lazysizesoptimumx',
				plugins_url(ltrim('js/lazysizes/plugins/optimumx/ls.optimumx.min.js', '/'), dirname(__FILE__)),
				array(),
				"1.1",
				false
			);
		}

		wp_localize_script('wpimage_lazyloading', 'lazySizesConfig', $this->get_js_config());
	}

	public function filter_images($content, $type = 'ratio') {
		global $wpImagelazySizesDefaults;
		$settings = unserialize(get_option('_wpimage_lazyload_options', $wpImagelazySizesDefaults));
		if (is_feed()
			|| intval(get_query_var('print')) == 1
			|| intval(get_query_var('printpage')) == 1
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
			return $content;
		}

		$respReplace = 'data-sizes="auto" data-srcset=';

		if ($settings['lazyload_optimumx'] != 'false') {
			$respReplace = 'data-optimumx="' . $settings['lazyload_optimumx'] . '" ' . $respReplace;
		}

		$matches = array();
		$skip_images_regex = '/class=".*lazyload.*"/';
		$placeholder_image = apply_filters(
			'lazysizes_placeholder_image',
			'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='
		);
		preg_match_all('/<img\s+.*?>/', $content, $matches);

		$search = array();
		$replace = array();

		foreach ($matches[0] as $imgHTML) {

			// Don't to the replacement if a skip class is provided and the image has the class.
			if (!(preg_match($skip_images_regex, $imgHTML))) {

				$replaceHTML = preg_replace('/<img(.*?)src=/is', '<img$1src="' . $placeholder_image . '" data-src=', $imgHTML);
				$replaceHTML = preg_replace('/<img(.*?)srcset=/is', '<img$1srcset="" data-srcset=', $replaceHTML);

				$replaceHTML = $this->_add_class($replaceHTML, 'lazyload');

				$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';

				if ($type == 'ratio' && $settings['lazyload_intrinsicRatio'] != 'false') {
					if (preg_match('/width=["|\']*(\d+)["|\']*/', $imgHTML, $width) == 1
						&& preg_match('/height=["|\']*(\d+)["|\']*/', $imgHTML, $height) == 1) {

						$ratioBox = '<span class="intrinsic-ratio-box';
						if (preg_match('/(align(none|left|right|center))/', $imgHTML, $align_class) == 1) {
							$ratioBox .= ' ' . $align_class[0];
							$replaceHTML = str_replace($align_class[0], '', $replaceHTML);
						}
						if ($settings['lazyload_intrinsicRatio'] == 'animated') {
							$ratioBox .= ' lazyload" data-expand="-1';
						}

						$ratioBox .= '" style="max-width: ' . $width[1] . 'px; max-height: ' . $height[1] . 'px;';

						$ratioBox .= '"><span class="intrinsic-ratio-helper" style="padding-bottom: ';
						$replaceHTML = $ratioBox . (($height[1] / $width[1]) * 100) . '%;"></span>'
							. $replaceHTML . '</span>';
					}
				}

				array_push($search, $imgHTML);
				array_push($replace, $replaceHTML);
			}
		}
		$search = array_unique($search);
		$replace = array_unique($replace);
		$content = str_replace($search, $replace, $content);

		return $content;
	}

	public function filter_avatar($content) {
		return $this->filter_images($content, 'noratio');
	}

	public function filter_iframes($html) {
		return false === strpos($html, 'iframe') ? $html : $this->_add_class($html, 'lazyload');
	}

	public function admin_scripts($hook) {

		$plugin_dir = dirname(__FILE__);

		if ('options-media.php' === $hook || 'upload.php' === $hook) {

			wp_enqueue_style('wpimage_admin_style', plugins_url('css/admin.css', $plugin_dir));
			wp_enqueue_style('tipsy-style', plugins_url('css/tipsy.css', $plugin_dir));
			wp_enqueue_style('modal-style', plugins_url('css/jquery.modal.css', $plugin_dir));

			wp_enqueue_style('typeahead', plugins_url('css/jquery.typeahead.min.css', $plugin_dir));

			wp_enqueue_script('jquery');
			wp_enqueue_script('tipsy-js', plugins_url('js/jquery.tipsy.js', $plugin_dir), array('jquery'));
			wp_enqueue_script('async-js', plugins_url('js/async.js', $plugin_dir));
			wp_enqueue_script('modal-js', plugins_url('js/jquery.modal.min.js', $plugin_dir), array('jquery'));
			wp_enqueue_script('ajax-script', plugins_url('js/ajax.js', $plugin_dir), array('jquery'));

			wp_enqueue_script('typeahead', plugins_url('js/jquery.typeahead.min.js', $plugin_dir), array('jquery'));

			wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
		}

	}

	public function get_api_status() {
		return true;
	}

	/**
	 *  Handles optimizing already-uploaded images in the  Media Library
	 */
	public function wpimage_media_library_ajax_callback() {  //runs on Optimize This Image btn click..........
		$post_req = $_POST; // Input var okay.
		$image_id = (int) $post_req['id'];
		$callback_type = 'button_click';
		$this->publitio_image_compression($image_id, $callback_type);
	}

	public function wpimage_media_uploader_callback($image_id) {  //automatically runs when a new file is uploaded.........
		$callback_type = 'image_upload';
		$this->publitio_image_compression($image_id, $callback_type);
	}

	public function publitio_image_compression ($image_id, $callback_type) {
		// Publitio Image Conversion start......................................................................................

		//get the setting from compress images tab in optimisation page
		$image_quality = get_option('wpimages_quality_auto');
		$bmp_to_jpg = get_option('wpimages_bmp_to_jpg');
		$png_to_jpg = get_option('wpimages_png_to_jpg');
		$use_our_image_cdn = get_option('wpimages_use_our_image_cdn');
		// debug($image_quality); debug($bmp_to_jpg); debug($png_to_jpg); debug($use_our_image_cdn);die();

		if ($image_quality == "auto:best") {
			$quality = "q_100";
		} else if ($image_quality == "auto:good") {
			$quality = "q_50";
		} else if ($image_quality == "auto:eco") {
			$quality = "q_10";
		} else if ($image_quality == "auto:low") {
			$quality = "q_1";
		}

		if ($use_our_image_cdn == 1) {
			$image_path = get_attached_file($image_id);

			$filename = basename($image_path);
			$this_img_extension = "";
			$extensions = array('.jpg', '.JPG', '.png', '.PNG', '.jpeg', '.JPEG');
			foreach ($extensions as $key => $extension) {
				if (strpos($filename, $extension) !== false) {
					$this_img_extension = $extension;
					$img_name = str_replace($extension, "", $filename);
				}
			}
			$image_extension = str_replace(".", "", $this_img_extension);

			$originalSize = filesize(get_attached_file($image_id));

			$publitio_api = new PublitioAPI('hxr7aqQDXG6WyLMApSjc', 'SmSt4vSBRtBW2m0kLAx5HsPikzhNwLuj');

			//first we will check if this image is already uploaded or not
			$reload = 0;
			// list all files to check for this recently uploaded file by file name
			$get_all_files = $publitio_api->call("/files/list", "GET", array('offset' => '0', 'limit' => '5'));
			$all_files = json_decode($get_all_files);

			//first of all find any files present in our publitio db, that were not deleted and delete them
			/*$allFiles = $all_files->files;
			foreach ($allFiles as $key => $file) {
			    $publito_img_id = $file->id;
			    $res = $publitio_api->call("/files/delete/".$publito_img_id, "DELETE");
			    debug($res); 
			 die('sad');
			}*/

			//and now,first upload the original file from our media library to be called later in other format
			$response = $publitio_api->upload_file($image_path, "file", array('name' => $filename, 'public_id' => $img_name, 'position' => 'top-right', 'padding' => '20'));
			$uploaded_file = json_decode($response);

			if ($uploaded_file->success == 1) {

				$publito_img_id = $uploaded_file->id;
				$publito_img_extension = $uploaded_file->extension;
				$publito_img_src = $uploaded_file->url_preview;
				$publito_img_download_src = $uploaded_file->url_download;
				//now get the desired format image from url of this original format uploaded image
				if ($png_to_jpg == '1' && $publito_img_extension == 'png') {
					$new_format = ".jpg";
				}
				if ($bmp_to_jpg == '1' && $publito_img_extension == 'bmp') {
					$new_format = ".jpg";
				}
				if ($publito_img_extension == 'jpg') {// even if uploaded format is jpg, we will need to compress it further below 
					$new_format = ".jpg";
				}
				if ($png_to_jpg == '0') {// even if uploaded format is jpg, we will need to compress it further below 
					$new_format = $this_img_extension;
				}
				if ($bmp_to_jpg == '0') {// even if uploaded format is jpg, we will need to compress it further below 
					$new_format = $this_img_extension;
				}

				if ($new_format !== null) {// can be same if permissions to convert to jpg are not set, but to compress image size
					if ($image_quality == "manual" || $image_quality == "auto") {
						$new_image_src = "https://media.publit.io/file/". $publito_img_id . "" . $new_format;
					} else {
						$new_image_src = "https://media.publit.io/file/".$quality."/" . $publito_img_id . "" . $new_format;
					}
					
					$new_image = '<img src="' . $new_image_src . '" width="200px">';
					$new_image_name = $img_name . "" . $new_format;
	                  
	                //here we will just replace the original image contents with compressed size image contents from publitio  
					$newSize = file_put_contents($image_path,file_get_contents($new_image_src));

					//at last delete this uploaded file with publitio image id
					// $del_response = $publitio_api->call("/files/delete/" . $publito_img_id, "DELETE");

					$savings_percentage = (((int) $originalSize - (int) $newSize) / (int) $originalSize) * 100;
					$saved_bytes = (int) $originalSize - (int) $newSize;

					$kv = array(
						'success' => true,
						'original_size' => self::pretty_kb($originalSize),
						'compressed_size' => self::pretty_kb($newSize),
						'saved_bytes' => self::pretty_kb($saved_bytes),
						'savings_percent' => round($savings_percentage, 2) . '%',
						'backup_before_compression' => "",
					);
                    
                    //now we will see if new format image extension is same as that of optimized image or not, if formats are same, we dont need to change this file and its thumbnails extensions as below, but if not, we will change this file extension, create its new format extensions, and delete its old format extensions  
					if ($this_img_extension !== $new_format && $callback_type == 'button_click') { 
		        
							//now change image name to new file extension in meta data and uploads folder
							//first we will change image meta data old extension name to our new image format extension name
							$var = get_post_meta($image_id);
							$string1 = $var['_wp_attached_file'][0];
							$string2 = $var['_wp_attachment_metadata'][0];
							
			                $new_format_name = str_replace(".","", $new_format);

							$newstring1 = str_replace($this_img_extension,$new_format, $string1);
							$newstring11 = str_replace("image/".$image_extension,"image/".$new_format_name, $newstring1);

							$newstring2 = str_replace($this_img_extension,$new_format, $string2);
							$newstring21 = str_replace("image/".$image_extension,"image/".$new_format_name, $newstring2);

							update_post_meta( $image_id, '_wp_attached_file', $newstring11);
							update_post_meta( $image_id, '_wp_attachment_metadata', unserialize($newstring21));

                            //now we will unlink the old thumbnail images for this image , and generate new format thumbnails// 
							
							$directory = str_replace("/".$filename,"", $image_path);
								$images = glob($directory . "/*".$this_img_extension);

								foreach($images as $old_image_extension_path)  //these are old image extensions
								{
                                    //if it is the main image in old format(old extension name),here rename it to new file extension and make its thumbnail images in this new format(new extension name),and unlink all old format thumbnail images in else condition below
									if (strpos($old_image_extension_path, $img_name."".$this_img_extension) !== false) {  // rename only main image 
										$new_image_path = str_replace($this_img_extension,$new_format, $old_image_extension_path); 
											rename($old_image_extension_path,$new_image_path); 
										
											//now, generate new thumbnail images with new format for this new image
									        $path_for_extension = str_replace($img_name.''.$new_format,"", $new_image_path); 
											
											// process image
											//get sizes for this image
											$var = get_post_meta($image_id);
											$image_sizes = unserialize($var['_wp_attachment_metadata'][0]);
											foreach ($image_sizes['sizes'] as $key => $image_size) {
													$image_sizes_array[] = ['w'=>$image_size['width'],'h'=>$image_size['height']];
											}

											debug($image_sizes_array); die();
											// $image_sizes_array = [['w'=>'100','h'=>'100'],['w'=>'150','h'=>'150'],['w'=>'300','h'=>'200'],['w'=>'300','h'=>'300'],['w'=>'600','h'=>'450'],['w'=>'768','h'=>'620']];
											foreach ($image_sizes_array as $key => $value) {
												$image = "";
												if ( ! is_wp_error( $image ) ) {
													$image = wp_get_image_editor( $new_image_path );
													$image->resize( $value['w'], $value['h'], true );  // new image dimension
													// $image->save( '/home/techsprinters/web/dmo.website/public_html/wp/wp-content/uploads/2018/10/camera-'.$value['w'].'x'.$value['h'].'.jpg' );

													//new image extensions created below as save method
													$image->save( $path_for_extension.''.$img_name.'-'.$value['w'].'x'.$value['h'].''.$new_format );
												}
											}

            							} else {
											unlink($old_image_extension_path);  //unlink old thumbnail images
										}
										
								}
					}	

					//now when the file is just uploaded, we will generate its thumbnails in new format right here, as till now, its thumbnails are not already generated, so we cant remove its old thumbnails anyway,
					if ($callback_type == 'image_upload') {
					    $new_thumbnails = wp_generate_attachment_metadata( $image_id, $image_path ); //get the dimensions in which this uploaded image can be generated

					    foreach ($new_thumbnails['sizes'] as $key => $image_size) {
								$image_sizes_array[] = ['w'=>$image_size['width'],'h'=>$image_size['height']];
						}

						foreach ($image_sizes_array as $key => $value) {
							$image = "";
							if ( ! is_wp_error( $image ) ) {
								$image = wp_get_image_editor( $image_path );
								$image->resize( $value['w'], $value['h'], true );  // new image dimension
								//new image extensions are created below as save method with different dimensions
								$image->save( $image_path.'-'.$value['w'].'x'.$value['h'].''.$new_format );
							}
						}

					}

					// Store compressed info to DB to show how much size is compressed
					update_post_meta($image_id, '_wpimage_size', $kv);

					$reload = 1;
				}
			}

		} else {
			return false;
		}

		if ($reload == 1 && $callback_type == 'button_click') {
			echo json_encode(array('reload' => 'reload'));
			die();
		}

		// Publitio Image Conversion ends......................................................................................
	}	


	public function show_credentials_validity() {

		$settings = $this->compression_settings;
		$status = $this->get_api_status();
		$url = admin_url() . 'images/';

		if ($status !== false && isset($status['active'])) {
			$url .= 'yes.png';
			echo '<p class = "apiStatus">Your credentials are valid <span class = "apiValid" style = "background:url(' . "'$url') no-repeat 0 0" . '"></span></p>';
		} else {
			$url .= 'no.png';
			echo '<p class = "apiStatus">There is a problem with your credentials <span class = "apiInvalid" style = "background:url(' . "'$url') no-repeat 0 0" . '"></span></p>';
		}
	}

	public function validate_options($input) {

		$valid = array();
		$error = '';
		$valid['api_lossy'] = $input['api_lossy'];

		if (!function_exists('curl_exec')) {
			$error = 'cURL not available. Wp image compression requires cURL in order to communicate with wp-image.co.uk servers. <br /> Please ask your system administrator or host to install PHP cURL, or contact support@wp-image.co.uk for advice';
		} else {
			$status = $this->get_api_status();

			if ($status !== false) {

				if (isset($status['active'])) {
					if ($status['plan_name'] === 'Developers') {
						$error = 'Developer API credentials cannot be used with this plugin.';
					} else {
						$valid['api_key'] = $input['api_key'];
						$valid['api_secret'] = $input['api_secret'];
					}
				} else {
					$error = 'There is a problem with your credentials. Please check them from your wp-image.co.uk account.';
				}
			} else {
				$error = 'Please enter a valid wp-image.co.uk API key and secret';
			}
		}

		if (!empty($error)) {
			add_settings_error(
				'media',
				'api_key_error',
				$error,
				'error'
			);
		}
		return $valid;
	}

	public function show_lossy() {

		$options = get_option('_wpimage_options');
		$value = isset($options['api_lossy']) ? $options['api_lossy'] : 'lossy';

		$html = '<input type="radio" id="wpicompressor_lossy" name="_wpimage_options[api_lossy]" value="lossy"' . checked('lossy', $value, false) . '/>';
		$html .= '<label for="wpicompressor_lossy">Lossy</label>';

		$html .= '<input style="margin-left:10px;" type="radio" id="wpimage_lossless" name="_wpimage_options[api_lossy]" value="lossless"' . checked('lossless', $value, false) . '/>';
		$html .= '<label for="wpimage_lossless">Lossless</label>';

		echo $html;
	}

	public function add_media_columns($columns) {
		// $columns['quality'] = 'Quality';
		$columns['original_size'] = 'Original Size';
		$columns['geo'] = 'Geo';
		$columns['compressed_size'] = 'Compressed Size';
		return $columns;
	}

	public function fill_media_columns($column_name, $id) {

		$original_size = @filesize(get_attached_file($id));
		$original_size = self::pretty_kb($original_size);

		$options = get_option('_wpimage_options');
		$type = isset($options['api_lossy']) ? $options['api_lossy'] : 'lossy';

		if (0 === strcmp($column_name, 'original_size')) {
			if (wp_attachment_is_image($id)) {
				$meta = get_post_meta($id, '_wpimage_size', true);
				echo isset($meta['original_size']) ? $meta['original_size'] : $original_size;
			} else {
				echo $original_size;
			}
		} else if (0 === strcmp($column_name, 'compressed_size')) {

			if (wp_attachment_is_image($id)) {

				$meta = get_post_meta($id, '_wpimage_size', true);

				$backup_file_exists = false;
				if (isset($meta['meta'])) {

					$uploads = wp_upload_dir();
					$image_in_backup = $uploads['basedir'] . '/optimisationio_media_backup/';

					if (isset($meta['meta']['file'])) {
						$image_in_backup .= basename($meta['meta']['file']);
						$backup_file_exists = file_exists($image_in_backup);
					} else {

						$wp_meta = wp_get_attachment_metadata($id);

						if (isset($wp_meta['file'])) {
							$image_in_backup .= basename($wp_meta['file']);
							$backup_file_exists = file_exists($image_in_backup);
						}
					}
				}

				$image_url = wp_get_attachment_url($id);
				$filename = basename($image_url);

				if (isset($meta['compressed_size'])) {

					$compressed_size = $meta['compressed_size'];

					if ($meta['savings_percent'] >= 0) {
						$savings_percentage = $meta['savings_percent'];
						echo '<strong>' . $compressed_size . '</strong><br/><small>Savings:&nbsp;' . $savings_percentage . '</small>';

						$thumbs_data = get_post_meta($id, '_compressed_thumbs', true);
						
						if (!empty($thumbs_data)) {
							$thumbs_count = count($thumbs_data);
							echo '<small>' . $thumbs_count . ' thumbs optimized' . '</small>';
						}
					} else {
						echo '<br/><small>No further optimization required</small>';
					}

					if (!empty($meta['no_savings'])) {
						echo '<div class="noSavings"><strong>No savings found</strong><br /></div>';
					} else if (isset($meta['error'])) {
						$error = $meta['error'];
						echo '<div class="wpimageErrorWrap"><a class="wpimageError" title="' . $error . '">Failed! Hover here</a></div>';
					}
				}

				if (!isset($meta['compressed_size']) || $backup_file_exists) {
					// echo $id;
					echo '<div class="buttonWrap">';
					echo $backup_file_exists ? '<br/>' : '';
					echo '<button data-setting="' . $type . '" type="button" class="wpimage_req" data-id="' . $id . '" id="wpimageid-' . $id . '" data-filename="' . $filename . '" data-url="' . $image_url . '" data-optimised="' . ($backup_file_exists ? 1 : 0) . '">';
					echo $backup_file_exists ? 'Re-Optimize This Image' : 'Optimize This Image';
					echo '</button>';
					echo '<span class="wpimageSpinner"></span>';
					echo '</div>';
				}

			} else {
				echo 'n/a';
			}
		} else if (0 === strcmp($column_name, 'geo')) {   // problem with this condition
			if (wp_attachment_is_image($id)) {

				$meta = get_post_meta($id, '_wpimage_size', true);

				$backup_file_exists = false;
				if (isset($meta['meta'])) {

					$uploads = wp_upload_dir();
					$image_in_backup = $uploads['basedir'] . '/optimisationio_media_backup/';

					if (isset($meta['meta']['file'])) {
						$image_in_backup .= basename($meta['meta']['file']);
						$backup_file_exists = file_exists($image_in_backup);
					} else {

						$wp_meta = wp_get_attachment_metadata($id);

						if (isset($wp_meta['file'])) {
							$image_in_backup .= basename($wp_meta['file']);
							$backup_file_exists = file_exists($image_in_backup);
						}
					}
				}

				$image_url = wp_get_attachment_url($id);
				$filename = basename($image_url);

				$image_path = get_attached_file($id);
				// if (exif_imagetype($image_path) != IMAGETYPE_JPEG) {
				// 	return "";
				// }
				//.check if image already has meta data,
				$upload_dir = wp_upload_dir();
				$image_basedir = $upload_dir['basedir'];
				$array = explode("uploads", $image_url);

				if (file_exists($image_path)) {
					// if (function_exists('exif_read_data')) {
					// 	$exif = exif_read_data($image_path, 0, true); //array reverse so that we get correct image title
					// 	$exif = array_reverse($exif);
					// 	foreach ($exif as $key => $section) {
					// 		foreach ($section as $name => $val) {
					// 			// print_r("$name: $val<br />\n") ;
					// 			if ($name == "UserComment") {
					// 				$image_title = $val;
					// 			}
					// 			if ($name == "ImageDescription") {
					// 				$image_desc = $val;
					// 			}
					// 		}
					// 	}
					// }
					global $wpdb;
					$image_addr = $wpdb->get_row("select * from " . $wpdb->prefix . "image_compression_details where image_id='" . $id . "'", ARRAY_A);
					$address = $image_addr['address'];
					if (!empty($address)) {
						echo '<button style="padding-bottom: 2px;" onclick="' . $this->add_geo_data($id/*, $image_title, $image_desc, $address*/) . '" class="wpimage_req_btn" id="modal-link' . $id . '" data-id="' . $id . '" ><img style="width: 10px;" src="' . esc_url(plugin_dir_url(dirname(__FILE__)) . 'images/map-pin.png') . '" alt="" /></button>';
					} else if (empty($address)) {
						$image_title = "";
						$image_desc = "";
						$address = "";
						echo '<button onclick="' . $this->add_geo_data($id/*, $image_title, $image_desc, $address*/) . '" class="wpimage_req_btn" id="modal-link' . $id . '" data-id="' . $id . '" > GEO TAG</button>';
					} else {
						//if there is no efix data yet linked to image
						$image_title = "";
						$image_desc = "";
						$address = "";
						echo '<button onclick="' . $this->add_geo_data($id/*, $image_title, $image_desc, $address*/) . '" class="wpimage_req_btn" id="modal-link' . $id . '" data-id="' . $id . '" > GEO TAG</button>';
					}
				} else {
					echo "NF";
				}

			} else {
				echo 'n/a';
			}
		} else if (0 === strcmp($column_name, 'quality')) {

			if (wp_attachment_is_image($id)) {

				$image_url = wp_get_attachment_url($id);
				$filename = basename($image_url);
				$image_path = get_attached_file($id);
			}

		}
	}

	public function gps2Num($coordPart) {
		$parts = explode('/', $coordPart);
		if (count($parts) <= 0) {
			return 0;
		}

		if (count($parts) == 1) {
			return $parts[0];
		}

		return floatval($parts[0]) / floatval($parts[1]);
	}

	public function getAddress($latitude, $longitude) {
		if (!empty($latitude) && !empty($longitude)) {
			//Send request and receive json data by address
			$geocodeFromLatLong = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latitude) . ',' . trim($longitude) . '&sensor=false');
			$output = json_decode($geocodeFromLatLong);
			$status = $output->status;
			//Get address from json data
			$address = ($status == "OK") ? $output->results[1]->formatted_address : '';
			//Return address of the given latitude and longitude
			if (!empty($address)) {
				return $address;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function add_geo_data($id/*, $image_title, $image_desc, $address*/) {
		echo '<div class="modal-wrapper">';
		echo '<div class="modal" id="modal' . $id . '">';
		echo '<div style="margin-left:95%"><button class="close-modal" id="closemodal' . $id . '" >X</button></div>';
		echo '<div class="modal-content"></div>';
		echo '</div>';
		echo '</div>';

		echo '<div class="show-in-modal" id="show-in-modal' . $id . '">';

		echo '</div>';
		/*echo '<input class="image_title" hidden value="' . stripslashes($image_title) . '">';
		echo '<textarea class="image_description" hidden>' . stripslashes($image_desc) . '</textarea>';*/

		?>
    
		<script>
		jQuery(document).ready(function($) {

		  var imageid = '<?php echo $id; ?>';
		  
		  $('#closemodal'+imageid).click(function(e) {  // Close modal
		    e.preventDefault();
		    $("#modal"+imageid).toggleClass('show');
		  });

		  var $window = $(window);  // Detect windows width function

		  function checkWidth() {
		    var windowsize = $window.width();
		    if (windowsize > 767) {
		      // if the window is greater than 767px wide then do below. we don't want the modal to show on mobile devices and instead the link will be followed.

		      $("#modal-link"+imageid).click(function(e) {

		        // var image_title = jQuery(this).siblings('.image_title').val();
		        // var image_desc = jQuery(this).siblings('.image_description').text();
		        var url = "<?php echo esc_url(admin_url('admin.php?page=optimisationio-dashboard')); ?>";
		        //we will fetch image geo data from our database, not by checking image meta data
				$.ajax({
				  type: "POST",
				  dataType:'json',
				  url: url,
				  data: {get_image_geo_data : imageid},
				  success: function(data){
				  	
				     if (data == null) {  // newly uploaded image, geo data will get inserted on form submit for this image
	 					$image_title = $image_description = $image_address = $image_latitude = $image_longitude = '';
				     } else {
                        $image_title = data.file_title;
	                    $image_description = data.file_description;
	                    $image_address = data.address;
	                    $image_latitude = data.latitude;
	                    $image_longitude = data.longitude;
				     }

			        var modalContent = $(".modal-content");
			        var post_link =  '<form action="<?php echo esc_url(admin_url('admin.php?page=optimisationio-dashboard')); ?>" method="post">'+
			                        '<input type="hidden" name="imageid" value="<?php echo $id ?>">'+
			                        '<div class="form">'+
			                            '<div class="input-group">'+
			                                '<label for="">Title/ Custom Text:</label>'+
			                                '<input name="image_title" value="'+$image_title+'" type="text" required=""/>'+
			                            '</div>'+
			                            '<div class="input-group">'+
			                                '<label for="">Image Description:</label>'+
			                                '<textarea name="image_desc" class="text-large" rows="5">'+$image_description+'</textarea>'+
			                            '</div>'+
			                            '<div class="input-group">'+
			                                '<label for="">Image Location:</label>'+

			                                '<div class="typeahead__container">'+
			                                    '<div class="typeahead__field">'+
			                                        '<div class="typeahead__query">'+
			                                            '<input class="js-typeahead-country_v1  image_location" value="'+$image_address+'" id="image_location'+imageid+'" name="image_location" type="search" placeholder="Search"  autocomplete="off">'+
			                                        '</div>'+
			                                    '</div>'+
			                                '</div>'+

			                                 '<input name="image_lat" class="image_lat" id="image_lat'+imageid+'" value="'+$image_latitude+'" hidden>'+
			                                 '<input name="image_lng" class="image_lng" id="image_lng'+imageid+'" value="'+$image_longitude+'" hidden>'+

			                            '</div>'+
			                            '<div class="input-group">'+
			                                '<button type="submit" > Update Image </button>'+
			                            '</div>'+
			                        '</div>'+
			                    '</form>';
			        e.preventDefault(); // prevent link from being followed

			        $("#modal"+imageid).addClass('show', 1000, "easeOutSine");
			        modalContent.empty();
			        modalContent.append(post_link);


		 			$.typeahead({
		                input: '.image_location',
		                minLength: 2,
		                // order: "desc",
		                dynamic: true,
		                filter: false,
		                delay: 1000,
		                // cache: true,
		                source: {
		                    location: {
		                        ajax: function (query) {
		                            return {
		                                type: "POST",
		                                url: ajaxurl,
		                                 // url: "http://data.campaigns.io/location/search/{{query}}",
		                                 // dataType: 'jsonp',
		                                 path: "data.location",
		                                 data: {
		                                    action: 'get_location',
		                                    query: "{{query}}",
		                                  }
		                             }
		                        }
		                    },
		                },
		                callback: {
		                    onClick: function (node, a, item, event) {
		                        $(".image_lat").val(item.latitude);
		                        $(".image_lng").val(item.longitude);
		                    },
		                    onCancel (node, event) {
		                        $(".image_lat").val("");
		                        $(".image_lng").val("");
		                    }
		                }
		            });

			        $(".image_location").keyup(function() {
			            var term = jQuery(this).val();
			            if( !term ) {
			                $(".image_lat").val("");
			                $(".image_lng").val("");
			            }

			        });

				  }
				});
		        
		       return false;


		      });
		    }
		  };

		  checkWidth(); // excute function to check width on load
		  $(window).resize(checkWidth); // execute function to check width on resize
		});

		</script>
<?php

	}


	public function save_meta_data($imageId, $image_url, $filename, $title, $description, $longitude, $latitude) {
	// image gps info

		$upload_dir = wp_upload_dir();
		$image_basedir = $upload_dir['basedir'];
		$array = explode("uploads", $image_url);
		$image_path = $image_basedir . "" . $array[1];
		$input = $image_path;
		$output = $image_path;
		$comment = $title;
		$model = "";
		$altitude = '';
		$date_time = date("Y-m-d H:i:s");
		$this->addGpsInfo($input, $output, $description, $comment, $model, $longitude, $latitude, $altitude, $date_time);
		$sizes = get_intermediate_image_sizes();
		foreach ($sizes as $key => $size) {
			$thumb = wp_get_attachment_image_src($imageId, $size);
			$this_image_url = $thumb[0];
			$array = explode("uploads", $this_image_url);
			$this_image_path = $image_basedir . "" . $array[1];
			$input = $this_image_path;
			$output = $this_image_path;
			if (file_exists($input)) {
				$this->addGpsInfo($input, $output, $description, $comment, $model, $longitude, $latitude, $altitude, $date_time);
			} else {
				echo "<br> Size No file " . $input;
			}
		}

	}

	public function addGpsInfo($input, $output, $description, $comment, $model, $longitude, $latitude, $altitude, $date_time) {

		$jpeg = new PelJpeg($input);
		$exif = new PelExif();
		$jpeg->setExif($exif);
		$tiff = new PelTiff();
		$exif->setTiff($tiff);
		$ifd0 = new PelIfd(PelIfd::IFD0);
		$tiff->setIfd($ifd0);
		$gps_ifd = new PelIfd(PelIfd::GPS);
		$ifd0->addSubIfd($gps_ifd);
		$exif_ifd = new PelIfd(PelIfd::EXIF);
		$exif_ifd->addEntry(new PelEntryUserComment($comment));
		$ifd0->addSubIfd($exif_ifd);

		$inter_ifd = new PelIfd(PelIfd::INTEROPERABILITY);
		$ifd0->addSubIfd($inter_ifd);

		$ifd0->addEntry(new PelEntryAscii(PelTag::MODEL, $model));
		$ifd0->addEntry(new PelEntryAscii(PelTag::DATE_TIME, $date_time));
		$ifd0->addEntry(new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $description));

		$gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_VERSION_ID, 2, 2, 0, 0));

		list($hours, $minutes, $seconds) = $this->convertDecimalToDMS($latitude);

		$latitude_ref = ($latitude < 0) ? 'S' : 'N';

		$gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LATITUDE_REF, $latitude_ref));
		$gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LATITUDE, $hours, $minutes, $seconds));

		/* The longitude works like the latitude. */
		list($hours, $minutes, $seconds) = $this->convertDecimalToDMS($longitude);
		$longitude_ref = ($longitude < 0) ? 'W' : 'E';

		$gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LONGITUDE_REF, $longitude_ref));
		$gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LONGITUDE, $hours, $minutes, $seconds));
		if ($altitude) {

			$gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_ALTITUDE, array(
				abs($altitude),
				1,
			)));
		}
		$gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_ALTITUDE_REF, (int) ($altitude < 0)));

		/* Finally we store the data in the output file. */
		file_put_contents($output, $jpeg->getBytes());
	}
	public function convertDecimalToDMS($degree) {
		if ($degree > 180 || $degree < -180) {
			return null;
		}
		$degree = abs($degree);
		$seconds = $degree * 3600; // Total number of seconds.
		$degrees = floor($degree); // Number of whole degrees.
		$seconds -= $degrees * 3600; // Subtract the number of seconds// taken by the degrees.
		$minutes = floor($seconds / 60); // Number of whole minutes.
		$seconds -= $minutes * 60; // Subtract the number of seconds // taken by the minutes.
		$seconds = round($seconds * 100, 0); // Round seconds with a 1/100th  // second precision.

		return array(
			array($degrees, 1),
			array($minutes, 1),
			array($seconds, 100),
		);
	}


	public function exe_before_wpheader() {

		if (is_admin() && current_user_can('manage_options')) {

			$server = $_SERVER; // Input var okay.
			$post_req = 'post' === sanitize_key($server['REQUEST_METHOD']) ? $_POST : array(); // Input var okay.
            

            if (isset($_POST['get_image_geo_data'])) {
		      	
		      	global $wpdb;
		      	$imageId = $_POST['get_image_geo_data'];
				$image = $wpdb->get_row("select * from " . $wpdb->prefix . "image_compression_details where image_id='" . $imageId . "'", ARRAY_A);
				echo json_encode($image);
				exit;
		    }

			//GEO TAG btn
			if (isset($_POST['image_title'])) {

				$imageId = $_POST['imageid'];
				$image_url = wp_get_attachment_url($imageId);
				$filename = basename($image_url);
				$title = $_POST['image_title'];
				$description = $_POST['image_desc'];
				$thisaddress = $_POST['image_location'];
				if (empty($thisaddress)) {
					$latitude = '0';  //new inserted data
					$longitude = '0';
				} else {
					$latitude = $_POST['image_lat'];  //new inserted data
					$longitude = $_POST['image_lng'];
				}
				

				// save address in database so that address can be fetched easily on editing image
				global $wpdb;
				$image = $wpdb->get_row("select * from " . $wpdb->prefix . "image_compression_details where image_id='" . $imageId . "'", ARRAY_A);

				//update image
				if (!empty($image)) {
					//update address to fetch on update from image_compression table
					$data['address'] = $thisaddress;
					$data['file_title'] = $title;
					$data['file_description'] = $description;
					$data['latitude'] = $latitude;
					$data['longitude'] = $longitude;

					$wpdb->update($wpdb->prefix . "image_compression_details", $data, array('image_id' => $imageId));

					$this->save_meta_data($imageId, $image_url, $filename, $title, $description, $longitude, $latitude);
				
				} else {   //insert image

						$latitude = $_POST['image_lat'];
						$longitude = $_POST['image_lng'];

						//save address to fetch on update from image_compression table
						$file_data['image_id'] = $imageId;
						$file_data['address'] = $thisaddress;
						$file_data['file_title'] = $title;
						$file_data['file_description'] = $description;
						$file_data['latitude'] = $latitude;
						$file_data['longitude'] = $longitude;
						$wpdb->insert($wpdb->prefix . "image_compression_details", $file_data);

						$this->save_meta_data($imageId, $image_url, $filename, $title, $description, $longitude, $latitude);
					

				}
				wp_redirect(get_site_url() . "/wp-admin/upload.php");
				die("---");

			}

			if (isset($post_req['optimisation_save_image_compression_settings'])) {
				// NOTE:Save ONLY on form submit. Deny access from another call (eg. AJAX).

				if (isset($post_req['optimisationio_image_compression_settings']) && wp_verify_nonce($post_req['optimisationio_image_compression_settings'], 'optimisationio-image-compression-settings')) {

					// Image Compression settings.
					update_option('wpimages_max_height', isset($post_req['wpimages_max_height']) ? $post_req['wpimages_max_height'] : WPIMAGE_DEFAULT_MAX_HEIGHT);
					update_option('wpimages_max_width', isset($post_req['wpimages_max_width']) ? $post_req['wpimages_max_width'] : WPIMAGE_DEFAULT_MAX_WIDTH);
					update_option('wpimages_max_height_library', isset($post_req['wpimages_max_height_library']) ? $post_req['wpimages_max_height_library'] : WPIMAGE_DEFAULT_MAX_WIDTH);
					update_option('wpimages_max_width_library', isset($post_req['wpimages_max_width_library']) ? $post_req['wpimages_max_width_library'] : WPIMAGE_DEFAULT_MAX_WIDTH);
					update_option('wpimages_max_height_other', isset($post_req['wpimages_max_height_other']) ? $post_req['wpimages_max_height_other'] : WPIMAGE_DEFAULT_MAX_WIDTH);
					update_option('wpimages_max_width_other', isset($post_req['wpimages_max_width_other']) ? $post_req['wpimages_max_width_other'] : WPIMAGE_DEFAULT_MAX_WIDTH);
					update_option('wpimages_bmp_to_jpg', isset($post_req['wpimages_bmp_to_jpg']) && 1 === (int) $post_req['wpimages_bmp_to_jpg'] ? 1 : 0);
					update_option('wpimages_png_to_jpg', isset($post_req['wpimages_png_to_jpg']) && 1 === (int) $post_req['wpimages_png_to_jpg'] ? 1 : 0);
					/*update_option('wpimages_use_our_image_cdn', isset($post_req['wpimages_use_our_image_cdn']) && 1 === (int) $post_req['wpimages_use_our_image_cdn'] ? 1 : 0);*/
					update_option('wpimages_quality', isset($post_req['wpimages_quality']) ? $post_req['wpimages_quality'] : WPIMAGE_DEFAULT_QUALITY);
					update_option('wpimages_quality_auto', isset($post_req['wpimages_quality_auto']) ? $post_req['wpimages_quality_auto'] : 'auto');

					// Lazy Load settings.
					$lazy_load_values = serialize($post_req['_wpimage_lazyload']);
					update_option('_wpimage_lazyload_options', $lazy_load_values);
				}
			}

		}
	}

	public static function pretty_kb($bytes) {
		return wpimages_pretty_kb($bytes);
	}

	public static function addon_settings() {
		include dirname(dirname(__FILE__)) . '/views/dashboard-settings.php';
	}

	public static function cloudinary_settings($include_upgrade = true) {
		include dirname(dirname(__FILE__)) . '/views/api-settings.php';     
	}
}



add_action('wp_ajax_get_location', 'get_location');
function get_location() {
	global $wpdb; // this is how you get access to the database

	$term = $_POST['query'];

    // $url = "http://location.test:8090/location/search/" . $term;
	$url = "http://data.campaigns.io/location/search/" . $term;

	$response = wp_remote_get($url);
	// print_r($response); die();
	if (is_array($response)) {
		$header = $response['headers']; // array of http header lines
		$body = $response['body']; // use the content
	}
	echo $body;

	wp_die(); // this is required to terminate immediately and return a proper response
}



