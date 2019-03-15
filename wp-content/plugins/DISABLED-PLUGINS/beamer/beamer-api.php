<?php

	/* ----------------------------------------------------------------------------------------------------

		BEAMER API
		Connects to the API

	---------------------------------------------------------------------------------------------------- */

	// Include metabox options and styles
	include('beamer-api-metabox.php');

	// Get the beamer ID by post ID
	function bmr_api_get_id( $id ){
		$beamer_id = get_post_meta($id, 'bmr_id', true);
		if( isset( $beamer_id ) ){
			return  $beamer_id;
		}else{
			return null;
		}
	}

	// Check if post has beamer ID
	function bmr_api_has_id( $id ){
		$check = bmr_api_get_id( $id );
		if($check != null && $check > 0){
			return true;
		}else{
			return false;
		}
	}

	// Get the API key
	function bmr_api_get_key(){
		if( bmr_get_setting('api_key') != '' ){
			return bmr_get_setting('api_key');
		}else{
			return null;
		}
	}

	// Trim content
	function bmr_api_trim_content( $obj, $num = 160 ){
		$result = wp_trim_words($obj, $num);
		return $result;
	}

	// Protect fields
	function bmr_protect_fields( $protected, $meta_key ) {
		$secured = array(
			'bmr_ignore_this_post',
			'bmr_category',
			'bmr_link_text',
			'bmr_feedback',
			'bmr_reactions',
		);
	    if( in_array( $meta_key, $secured ) ) {
			return true;
	    }
		return $protected;
	}
	//add_filter( 'is_protected_meta', 'bmr_protect_fields', 10, 2 );


	// CALL API ---------------------------------------------------------------------------
	function bmr_api_call($post_ID, $post_after, $post_before){
		$beamer_id = bmr_api_has_id($post_ID) ? bmr_api_get_id($post_ID) : 0;
		$edit_check = array('draft', 'pending', 'publish', 'future');

		if( $post_after->post_status != 'auto-draft' ){
			if( $post_after->post_status == 'trash' && bmr_get_meta( 'bmr_ignore' ) == null && bmr_api_has_id($post_ID) ){
				// DELETE
				$api_key = bmr_api_get_key();
				$api_url = bmr_api_url('posts', $beamer_id);

				// JSON here
				$ch = curl_init($api_url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Beamer-Api-Key: ' . $api_key)
				);
				$result = curl_exec($ch);
			}elseif( $post_after->post_status == 'publish' OR $post_after->post_status == 'future' ){
				// POST
				$api_key = bmr_api_get_key();
				$api_url = bmr_api_has_id($post_ID) ? bmr_api_url('posts', $beamer_id) : bmr_api_url('posts');

				// Set date
				$date = get_the_date( 'o-m-d', $post_ID ).'T'.get_the_date( 'H:i:s', $post_ID );

				// Set content
				if( $post_after->post_excerpt != '' ){
					// Look for the excerpt
					$body = $post_after->post_excerpt;
				}else{
					 if( strpos( $post_after->post_content, '<!--more-->' ) ){
						// Look for read more tag
						$content = $post_after->post_content;
						$content_extended = get_extended( $content );
						$content_iframe_filter = preg_replace('/<iframe.*?\/iframe>/i', '', $content_extended['main']);
						$content_script_filter = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content_iframe_filter);
						$body = $content_script_filter;
					 }else{
					 	// Create a custom exerpt
						$content = $post_after->post_content;
						$content_iframe_filter = preg_replace('/<iframe.*?\/iframe>/i', '', $content);
						$content_script_filter = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content_iframe_filter);
						$limit = bmr_get_setting('api_excerpt');
						$body = $limit ? bmr_api_trim_content( $content_script_filter, $limit ) : bmr_api_trim_content( $content_script_filter );
					 }
				}

				// Set Featured Image
				if( has_post_thumbnail($post_ID) ){
					$thumbnail = get_the_post_thumbnail_url($post_ID, 'full');
				}

				$content = $thumbnail ? $thumbnail.' '.$body : $body;

				// Set category
				$category = bmr_get_meta('bmr_category');

				// Set Read More
				if( bmr_get_meta('bmr_link_text') ){
					// Manual
					$readmore = bmr_get_meta('bmr_link_text');
				}elseif( bmr_get_setting('api_readmore') ){
					// Default
					$readmore = bmr_get_setting('api_readmore');
				}

				// Set feedback
				if( bmr_get_meta( 'bmr_feedback' ) === 'off' ){
					$feedback = false;
				}else{
					$feedback = true;
				}

				// Set reactions
				if( bmr_get_meta( 'bmr_reactions' ) === 'off' ){
					$react = false;
				}else{
					$react = true;
				}

				// Create data array
				$data = array(
					'title' => array( $post_after->post_title ),
					'content' => array( $content ),
					'category' => $category,
					'publish' => true,
					'linkUrl' => array( $post_after->guid ),
					'linkText' => array( $readmore ?: 'Read more' ),
					'date' => $date,
					'enableFeedback' => $feedback,
					'enableReactions' => $react,
					'autoOpen' => false,
					'language' => array('EN')
				);

				// Set request
				$request = bmr_api_has_id($post_ID) ? 'PUT' : 'POST';

				// Check if ignore
				if( bmr_get_meta( 'bmr_ignore' ) == null ){
					// JSON here
					$data_string = json_encode($data);
					$ch = curl_init($api_url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Beamer-Api-Key: ' . $api_key)
					);
					$result = curl_exec($ch);
					$decoded = json_decode($result, true);
				}

				// Update post meta with the Beamer custom fields
				$prefix = 'bmr_';
				$beamer_meta = array(
					$prefix.'title' => $post_after->post_title,
					$prefix.'content' => $content,
					$prefix.'publish' => true,
					$prefix.'linkUrl' => $post_after->guid,
					$prefix.'date' => $date
				);
				if( !bmr_api_has_id($post_ID) ){
					$beamer_meta[$prefix.'id'] = $decoded['id'];
				}
				foreach($beamer_meta as $key => $var){
					update_post_meta($post_ID, $key, $var);
				}
			}
		}
	}
	add_action( 'post_updated', 'bmr_api_call', 10, 3 );

?>