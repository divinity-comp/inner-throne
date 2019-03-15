<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Change hex color more bright or more dark
 *
 * @param $hex
 * @param $steps
 *
 * @return string
 */
function gallery_img_adjust_brightness($hex, $steps) {
	// Steps should be between -255 and 255. Negative = darker, positive = lighter
	$steps = max(-255, min(255, $steps));

	// Normalize into a six character long hex string
	$hex = str_replace('#', '', $hex);
	if (strlen($hex) == 3) {
		$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
	}

	// Split into three parts: R, G and B
	$color_parts = str_split($hex, 2);
	$new_color = '';

	foreach ($color_parts as $color) {
		$color   = hexdec($color); // Convert to decimal
		$color   = max(0,min(255,$color + $steps)); // Adjust color
		$new_color .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
	}

	return $new_color;
}

/**
 * Get all default general options parameters in a single array
 *
 * @return array Array of all general options
 */
function gallery_img_get_default_options() {
	$gallery_default__params = array(
		'gallery_img_ht_view2_element_linkbutton_text'                  => 'View More',
		'gallery_img_ht_view2_element_show_linkbutton'                  => 'on',
		'gallery_img_ht_view2_element_linkbutton_color'                 => 'ffffff',
		'gallery_img_ht_view2_element_linkbutton_font_size'             => '14',
		'gallery_img_ht_view2_element_linkbutton_background_color'      => '2ea2cd',
		'gallery_img_ht_view2_show_popup_linkbutton'                    => 'on',
		'gallery_img_ht_view2_popup_linkbutton_text'                    => 'View More',
		'gallery_img_ht_view2_popup_linkbutton_background_hover_color'  => '0074a2',
		'gallery_img_ht_view2_popup_linkbutton_background_color'        => '2ea2cd',
		'gallery_img_ht_view2_popup_linkbutton_font_hover_color'        => 'ffffff',
		'gallery_img_ht_view2_popup_linkbutton_color'                   => 'ffffff',
		'gallery_img_ht_view2_popup_linkbutton_font_size'               => '14',
		'gallery_img_ht_view2_description_color'                        => '222222',
		'gallery_img_ht_view2_description_font_size'                    => '14',
		'gallery_img_ht_view2_show_description'                         => 'on',
		'gallery_img_ht_view2_thumbs_width'                             => '75',
		'gallery_img_ht_view2_thumbs_height'                            => '75',
		'gallery_img_ht_view2_thumbs_position'                          => 'before',
		'gallery_img_ht_view2_show_thumbs'                              => 'on',
		'gallery_img_ht_view2_popup_background_color'                   => 'FFFFFF',
		'gallery_img_ht_view2_popup_overlay_color'                      => '000000',
		'gallery_img_ht_view2_popup_overlay_transparency_color'         => '70',
		'gallery_img_ht_view2_popup_closebutton_style'                  => 'dark',
		'gallery_img_ht_view2_show_separator_lines'                     => 'on',
		'gallery_img_ht_view2_show_popup_title'                         => 'on',
		'gallery_img_ht_view2_element_title_font_size'                  => '18',
		'gallery_img_ht_view2_element_title_font_color'                 => '222222',
		'gallery_img_ht_view2_popup_title_font_size'                    => '18',
		'gallery_img_ht_view2_popup_title_font_color'                   => '222222',
		'gallery_img_ht_view2_element_overlay_color'                    => 'FFFFFF',
		'gallery_img_ht_view2_element_overlay_transparency'             => '70',
		'gallery_img_ht_view2_zoombutton_style'                         => 'light',
		'gallery_img_ht_view2_element_border_width'                     => '1',
		'gallery_img_ht_view2_element_border_color'                     => 'dedede',
		'gallery_img_ht_view2_element_background_color'                 => 'f9f9f9',
		'gallery_img_ht_view2_element_width'                            => '275',
		'gallery_img_ht_view2_element_height'                           => '160',
		'gallery_img_ht_view5_icons_style'                              => 'dark',
		'gallery_img_ht_view5_show_separator_lines'                     => 'on',
		'gallery_img_ht_view5_linkbutton_text'                          => 'View More',
		'gallery_img_ht_view5_show_linkbutton'                          => 'on',
		'gallery_img_ht_view5_linkbutton_background_hover_color'        => '0074a2',
		'gallery_img_ht_view5_linkbutton_background_color'              => '2ea2cd',
		'gallery_img_ht_view5_linkbutton_font_hover_color'              => 'ffffff',
		'gallery_img_ht_view5_linkbutton_color'                         => 'ffffff',
		'gallery_img_ht_view5_linkbutton_font_size'                     => '14',
		'gallery_img_ht_view5_description_color'                        => '555555',
		'gallery_img_ht_view5_description_font_size'                    => '14',
		'gallery_img_ht_view5_show_description'                         => 'on',
		'gallery_img_ht_view5_thumbs_width'                             => '75',
		'gallery_img_ht_view5_thumbs_height'                            => '75',
		'gallery_img_ht_view5_show_thumbs'                              => 'on',
		'gallery_img_ht_view5_title_font_size'                          => '16',
		'gallery_img_ht_view5_title_font_color'                         => '0074a2',
		'gallery_img_ht_view5_main_image_width'                         => '275',
		'gallery_img_ht_view5_slider_tabs_font_color'                   => 'd9d99',
		'gallery_img_ht_view5_slider_tabs_background_color'             => '555555',
		'gallery_img_ht_view5_slider_background_color'                  => 'f9f9f9',
		'gallery_img_ht_view6_title_font_size'                          => '16',
		'gallery_img_ht_view6_title_font_color'                         => '0074A2',
		'gallery_img_ht_view6_title_font_hover_color'                   => '2EA2CD',
		'gallery_img_ht_view6_title_background_color'                   => '000000',
		'gallery_img_ht_view6_title_background_transparency'            => '80',
		'gallery_img_ht_view6_border_radius'                            => '3',
		'gallery_img_ht_view6_border_width'                             => '0',
		'gallery_img_ht_view6_border_color'                             => 'eeeeee',
		'gallery_img_ht_view6_width'                                    => '275',
		'gallery_img_light_box_size'                                    => '17',
		'gallery_img_light_box_width'                                   => '500',
		'gallery_img_light_box_transition'                              => 'elastic',
		'gallery_img_light_box_speed'                                   => '800',
		'gallery_img_light_box_href'                                    => 'False',
		'gallery_img_light_box_title'                                   => 'false',
		'gallery_img_light_box_scalephotos'                             => 'true',
		'gallery_img_light_box_rel'                                     => 'false',
		'gallery_img_light_box_scrolling'                               => 'false',
		'gallery_img_light_box_opacity'                                 => '20',
		'gallery_img_light_box_open'                                    => 'false',
		'gallery_img_light_box_overlayclose'                            => 'true',
		'gallery_img_light_box_esckey'                                  => 'false',
		'gallery_img_light_box_arrowkey'                                => 'false',
		'gallery_img_light_box_loop'                                    => 'true',
		'gallery_img_light_box_data'                                    => 'false',
		'gallery_img_light_box_classname'                               => 'false',
		'gallery_img_light_box_fadeout'                                 => '300',
		'gallery_img_light_box_closebutton'                             => 'true',
		'gallery_img_light_box_current'                                 => 'image',
		'gallery_img_light_box_previous'                                => 'previous',
		'gallery_img_light_box_next'                                    => 'next',
		'gallery_img_light_box_close'                                   => 'close',
		'gallery_img_light_box_iframe'                                  => 'false',
		'gallery_img_light_box_inline'                                  => 'false',
		'gallery_img_light_box_html'                                    => 'false',
		'gallery_img_light_box_photo'                                   => 'false',
		'gallery_img_light_box_height'                                  => '500',
		'gallery_img_light_box_innerwidth'                              => 'false',
		'gallery_img_light_box_innerheight'                             => 'false',
		'gallery_img_light_box_initialwidth'                            => '300',
		'gallery_img_light_box_initialheight'                           => '100',
		'gallery_img_light_box_maxwidth'                                => '900',
		'gallery_img_light_box_maxheight'                               => '700',
		'gallery_img_light_box_slideshow'                               => 'false',
		'gallery_img_light_box_slideshowspeed'                          => '2500',
		'gallery_img_light_box_slideshowauto'                           => 'true',
		'gallery_img_light_box_slideshowstart'                          => 'start slideshow',
		'gallery_img_light_box_slideshowstop'                           => 'stop slideshow',
		'gallery_img_light_box_fixed'                                   => 'true',
		'gallery_img_light_box_top'                                     => 'false',
		'gallery_img_light_box_bottom'                                  => 'false',
		'gallery_img_light_box_left'                                    => 'false',
		'gallery_img_light_box_right'                                   => 'false',
		'gallery_img_light_box_reposition'                              => 'false',
		'gallery_img_light_box_retinaimage'                             => 'true',
		'gallery_img_light_box_retinaurl'                               => 'false',
		'gallery_img_light_box_retinasuffix'                            => '@2x.$1',
		'gallery_img_light_box_returnfocus'                             => 'true',
		'gallery_img_light_box_trapfocus'                               => 'true',
		'gallery_img_light_box_fastiframe'                              => 'true',
		'gallery_img_light_box_preloading'                              => 'true',
		'gallery_img_lightbox_open_position'                            => '5',
		'gallery_img_light_box_style'                                   => '1',
		'gallery_img_light_box_size_fix'                                => 'false',
		'gallery_img_slider_crop_image'                                 => 'resize',
		'gallery_img_slider_title_color'                                => '000000',
		'gallery_img_slider_title_font_size'                            => '13',
		'gallery_img_slider_description_color'                          => 'ffffff',
		'gallery_img_slider_description_font_size'                      => '12',
		'gallery_img_slider_title_position'                             => 'right-top',
		'gallery_img_slider_description_position'                       => 'right-bottom',
		'gallery_img_slider_title_border_size'                          => '0',
		'gallery_img_slider_title_border_color'                         => 'ffffff',
		'gallery_img_slider_title_border_radius'                        => '4',
		'gallery_img_slider_description_border_size'                    => '0',
		'gallery_img_slider_description_border_color'                   => 'ffffff',
		'gallery_img_slider_description_border_radius'                  => '0',
		'gallery_img_slider_slideshow_border_size'                      => '0',
		'gallery_img_slider_slideshow_border_color'                     => 'ffffff',
		'gallery_img_slider_slideshow_border_radius'                    => '0',
		'gallery_img_slider_navigation_type'                            => '1',
		'gallery_img_slider_navigation_position'                        => 'bottom',
		'gallery_img_slider_title_background_color'                     => 'ffffff',
		'gallery_img_slider_description_background_color'               => '000000',
		'gallery_img_slider_title_transparent'                          => 'on',
		'gallery_img_slider_description_transparent'                    => 'on',
		'gallery_img_slider_slider_background_color'                    => 'ffffff',
		'gallery_img_slider_dots_position'                              => 'top',
		'gallery_img_slider_active_dot_color'                           => 'ffffff',
		'gallery_img_slider_dots_color'                                 => '000000',
		'gallery_img_slider_description_width'                          => '70',
		'gallery_img_slider_description_height'                         => '50',
		'gallery_img_slider_description_background_transparency'        => '70',
		'gallery_img_slider_description_text_align'                     => 'justify',
		'gallery_img_slider_title_width'                                => '30',
		'gallery_img_slider_title_height'                               => '50',
		'gallery_img_slider_title_background_transparency'              => '70',
		'gallery_img_slider_title_text_align'                           => 'right',
		'gallery_img_slider_title_has_margin'                           => 'off',
		'gallery_img_slider_description_has_margin'                     => 'off',
		'gallery_img_slider_show_arrows'                                => 'on',
		'gallery_img_thumb_image_behavior'                              => 'on',
		'gallery_img_thumb_image_width'                                 => '240',
		'gallery_img_thumb_image_height'                                => '150',
		'gallery_img_thumb_image_border_width'                          => '1',
		'gallery_img_thumb_image_border_color'                          => '444444',
		'gallery_img_thumb_image_border_radius'                         => '5',
		'gallery_img_thumb_margin_image'                                => '1',
		'gallery_img_thumb_title_font_size'                             => '16',
		'gallery_img_thumb_title_font_color'                            => 'FFFFFF',
		'gallery_img_thumb_title_background_color'                      => 'CCCCCC',
		'gallery_img_thumb_title_background_transparency'               => '80',
		'gallery_img_thumb_box_padding'                                 => '28',
		'gallery_img_thumb_box_background'                              => '333333',
		'gallery_img_thumb_box_use_shadow'                              => 'on',
		'gallery_img_thumb_box_has_background'                          => 'on',
		'gallery_img_thumb_view_text'                                   => 'View Picture',
		'gallery_img_ht_view8_element_cssAnimation'                     => 'false',
		'gallery_img_ht_view8_element_height'                           => '120',
		'gallery_img_ht_view8_element_maxheight'                        => '155',
		'gallery_img_ht_view8_element_show_caption'                     => 'true',
		'gallery_img_ht_view8_element_padding'                          => '0',
		'gallery_img_ht_view8_element_border_radius'                    => '5',
		'gallery_img_ht_view8_icons_style'                              => 'dark',
		'gallery_img_ht_view8_element_title_font_size'                  => '13',
		'gallery_img_ht_view8_element_title_font_color'                 => '3AD6FC',
		'gallery_img_ht_view8_popup_background_color'                   => '000000',
		'gallery_img_ht_view8_popup_overlay_transparency_color'         => '0',
		'gallery_img_ht_view8_popup_closebutton_style'                  => 'dark',
		'gallery_img_ht_view8_element_title_overlay_transparency'       => '90',
		'gallery_img_ht_view8_element_size_fix'                         => 'false',
		'gallery_img_ht_view8_element_title_background_color'           => 'FF1C1C',
		'gallery_img_ht_view8_element_justify'                          => 'true',
		'gallery_img_ht_view8_element_randomize'                        => 'false',
		'gallery_img_ht_view8_element_animation_speed'                  => '2000',
		'gallery_img_ht_view2_content_in_center'                        => 'off',
		'gallery_img_ht_view6_content_in_center'                        => 'off',
		'gallery_img_ht_view2_popup_full_width'                         => 'on',
		'gallery_img_ht_view9_title_fontsize'                           => '18',
		'gallery_img_ht_view9_title_color'                              => 'FFFFFF',
		'gallery_img_ht_view9_desc_color'                               => '000000',
		'gallery_img_ht_view9_desc_fontsize'                            => '14',
		'gallery_img_ht_view9_element_title_show'                       => 'true',
		'gallery_img_ht_view9_element_desc_show'                        => 'true',
		'gallery_img_ht_view9_general_width'                            => '100',
		'gallery_img_view9_general_position'                            => 'center',
		'gallery_img_view9_title_textalign'                             => 'left',
		'gallery_img_view9_desc_textalign'                              => 'justify',
		'gallery_img_view9_image_position'                              => '2',
		'gallery_img_ht_view9_title_back_color'                         => '000000',
		'gallery_img_ht_view9_title_opacity'                            => '70',
		'gallery_img_ht_view9_desc_opacity'                             => '100',
		'gallery_img_ht_view9_desc_back_color'                          => 'FFFFFF',
		'gallery_img_ht_view9_general_space'                            => '0',
		'gallery_img_ht_view9_general_separator_size'                   => '0',
		'gallery_img_ht_view9_general_separator_color'                  => '010457',
		'gallery_img_view9_general_separator_style'                     => 'dotted',
		'gallery_img_ht_view9_paginator_fontsize'                       => '22',
		'gallery_img_ht_view9_paginator_color'                          => '1046B3',
		'gallery_img_ht_view9_paginator_icon_color'                     => '1046B3',
		'gallery_img_ht_view9_paginator_icon_size'                      => '18',
		'gallery_img_view9_paginator_position'                          => 'center',
		'gallery_img_video_view9_loadmore_position'                     => 'center',
		'gallery_img_video_ht_view9_loadmore_fontsize'                  => '19',
		'gallery_img_video_ht_view9_button_color'                       => '5CADFF',
		'gallery_img_video_ht_view9_loadmore_font_color'                => '000000',
		'gallery_img_video_ht_view9_loadmore_font_color'                => 'Load More',
		'gallery_img_loading_type'                                      => '2',
		'gallery_img_video_ht_view9_loadmore_text'                      => 'View More',
		'gallery_img_video_ht_view8_paginator_position'                 => 'center',
		'gallery_img_video_ht_view8_paginator_icon_size'                => '18',
		'gallery_img_video_ht_view8_paginator_icon_color'               => '26A6FC',
		'gallery_img_video_ht_view8_paginator_color'                    => '26A6FC',
		'gallery_img_video_ht_view8_paginator_fontsize'                 => '18',
		'gallery_img_video_ht_view8_loadmore_position'                  => 'center',
		'gallery_img_video_ht_view8_loadmore_fontsize'                  => '14',
		'gallery_img_video_ht_view8_button_color'                       => '26A6FC',
		'gallery_img_video_ht_view8_loadmore_font_color'                => 'FF1C1C',
		'gallery_img_video_ht_view8_loading_type'                       => '3',
		'gallery_img_video_ht_view8_loadmore_text'                      => 'View More',
		'gallery_img_video_ht_view7_paginator_fontsize'                 => '22',
		'gallery_img_video_ht_view7_paginator_color'                    => '0A0202',
		'gallery_img_video_ht_view7_paginator_icon_color'               => '333333',
		'gallery_img_video_ht_view7_paginator_icon_size'                => '22',
		'gallery_img_video_ht_view7_paginator_position'                 => 'center',
		'gallery_img_video_ht_view7_loadmore_position'                  => 'center',
		'gallery_img_video_ht_view7_loadmore_fontsize'                  => '19',
		'gallery_img_video_ht_view7_button_color'                       => '333333',
		'gallery_img_video_ht_view7_loadmore_font_color'                => 'CCCCCC',
		'gallery_img_video_ht_view7_loading_type'                       => '1',
		'gallery_img_video_ht_view7_loadmore_text'                      => 'View More',
		'gallery_img_video_ht_view4_paginator_fontsize'                 => '19',
		'gallery_img_video_ht_view4_paginator_color'                    => 'FF2C2C',
		'gallery_img_video_ht_view4_paginator_icon_color'               => 'B82020',
		'gallery_img_video_ht_view4_paginator_icon_size'                => '21',
		'gallery_img_video_ht_view4_paginator_position'                 => 'center',
		'gallery_img_video_ht_view4_loadmore_position'                  => 'center',
		'gallery_img_video_ht_view4_loadmore_fontsize'                  => '16',
		'gallery_img_video_ht_view4_button_color'                       => '5CADFF',
		'gallery_img_video_ht_view4_loadmore_font_color'                => 'FF0D0D',
		'gallery_img_video_ht_view4_loading_type'                       => '3',
		'gallery_img_video_ht_view4_loadmore_text'                      => 'View More',
		'gallery_img_video_ht_view1_paginator_fontsize'                 => '22',
		'gallery_img_video_ht_view1_paginator_color'                    => '222222',
		'gallery_img_video_ht_view1_paginator_icon_color'               => 'FF2C2C',
		'gallery_img_video_ht_view1_paginator_icon_size'                => '22',
		'gallery_img_video_ht_view1_paginator_position'                 => 'left',
		'gallery_img_video_ht_view1_loadmore_position'                  => 'center',
		'gallery_img_video_ht_view1_loadmore_fontsize'                  => '22',
		'gallery_img_video_ht_view1_button_color'                       => 'FF2C2C',
		'gallery_img_video_ht_view1_loadmore_font_color'                => 'FFFFFF',
		'gallery_img_video_ht_view1_loading_type'                       => '2',
		'gallery_img_video_ht_view1_loadmore_text'                      => 'Load More',
		'gallery_img_video_ht_view9_loadmore_font_color_hover'          => 'D9D9D9',
		'gallery_img_video_ht_view9_button_color_hover'                 => '8F827C',
		'gallery_img_video_ht_view8_loadmore_font_color_hover'          => 'FF4242',
		'gallery_img_video_ht_view8_button_color_hover'                 => '0FEFFF',
		'gallery_img_video_ht_view7_loadmore_font_color_hover'          => 'D9D9D9',
		'gallery_img_video_ht_view7_button_color_hover'                 => '8F827C',
		'gallery_img_video_ht_view4_loadmore_font_color_hover'          => 'FF4040',
		'gallery_img_video_ht_view4_button_color_hover'                 => '99C5FF',
		'gallery_img_video_ht_view1_loadmore_font_color_hover'          => 'F2F2F2',
		'gallery_img_video_ht_view1_button_color_hover'                 => '991A1A',
		'gallery_img_image_natural_size_thumbnail'                      => 'resize',
		'gallery_img_image_natural_size_contentpopup'                   => 'resize',
		'gallery_img_ht_popup_rating_count'                             => 'on',
		'gallery_img_ht_popup_likedislike_bg'                           => '7993A3',
		'gallery_img_ht_contentsl_rating_count'                         => 'on',
		'gallery_img_ht_popup_likedislike_bg_trans'                     => '0',
		'gallery_img_ht_popup_likedislike_thumb_color'                  => '2EC7E6',
		'gallery_img_ht_popup_likedislike_thumb_active_color'           => '2883C9',
		'gallery_img_ht_popup_likedislike_font_color'                   => '454545',
		'gallery_img_ht_popup_active_font_color'                        => '000000',
		'gallery_img_ht_contentsl_likedislike_bg'                       => '7993A3',
		'gallery_img_ht_contentsl_likedislike_bg_trans'                 => '0',
		'gallery_img_ht_contentsl_likedislike_thumb_color'              => '2EC7E6',
		'gallery_img_ht_contentsl_likedislike_thumb_active_color'       => '2883C9',
		'gallery_img_ht_contentsl_likedislike_font_color'               => '454545',
		'gallery_img_ht_contentsl_active_font_color'                    => '1C1C1C',
		'gallery_img_ht_lightbox_rating_count'                          => 'on',
		'gallery_img_ht_lightbox_likedislike_bg'                        => 'FFFFFF',
		'gallery_img_ht_lightbox_likedislike_bg_trans'                  => '20',
		'gallery_img_ht_lightbox_likedislike_thumb_color'               => '7A7A7A',
		'gallery_img_ht_lightbox_likedislike_thumb_active_color'        => 'E83D09',
		'gallery_img_ht_lightbox_likedislike_font_color'                => 'FFFFFF',
		'gallery_img_ht_lightbox_active_font_color'                     => 'FFFFFF',
		'gallery_img_ht_slider_rating_count'                            => 'on',
		'gallery_img_ht_slider_likedislike_bg'                          => 'FFFFFF',
		'gallery_img_ht_slider_likedislike_bg_trans'                    => '70',
		'gallery_img_ht_slider_likedislike_thumb_color'                 => '000000',
		'gallery_img_ht_slider_likedislike_thumb_active_color'          => 'FF3D3D',
		'gallery_img_ht_slider_likedislike_font_color'                  => '3D3D3D',
		'gallery_img_ht_slider_active_font_color'                       => '1C1C1C',
		'gallery_img_ht_thumb_rating_count'                             => 'on',
		'gallery_img_ht_thumb_likedislike_bg'                           => '63150C',
		'gallery_img_ht_thumb_likedislike_bg_trans'                     => '0',
		'gallery_img_ht_thumb_likedislike_thumb_color'                  => 'F7F7F7',
		'gallery_img_ht_thumb_likedislike_thumb_active_color'           => 'E65010',
		'gallery_img_ht_thumb_likedislike_font_color'                   => 'E6E6E6',
		'gallery_img_ht_thumb_active_font_color'                        => 'FFFFFF',
		'gallery_img_ht_just_rating_count'                              => 'on',
		'gallery_img_ht_just_likedislike_bg'                            => 'FFFFFF',
		'gallery_img_ht_just_likedislike_bg_trans'                      => '0',
		'gallery_img_ht_just_likedislike_thumb_color'                   => 'FFFFFF',
		'gallery_img_ht_just_likedislike_thumb_active_color'            => '0ECC5A',
		'gallery_img_ht_just_likedislike_font_color'                    => '030303',
		'gallery_img_ht_just_active_font_color'                         => 'EDEDED',
		'gallery_img_ht_blog_rating_count'                              => 'on',
		'gallery_img_ht_blog_likedislike_bg'                            => '0B0B63',
		'gallery_img_ht_blog_likedislike_bg_trans'                      => '0',
		'gallery_img_ht_blog_likedislike_thumb_color'                   => '8F827C',
		'gallery_img_ht_blog_likedislike_thumb_active_color'            => '5CADFF',
		'gallery_img_ht_blog_likedislike_font_color'                    => '4D4B49',
		'gallery_img_ht_blog_active_font_color'                         => '020300',
		'gallery_img_ht_popup_heart_likedislike_thumb_color'            => '84E0E3',
		'gallery_img_ht_popup_heart_likedislike_thumb_active_color'     => '46B4E3',
		'gallery_img_ht_contentsl_heart_likedislike_thumb_color'        => '84E0E3',
		'gallery_img_ht_contentsl_heart_likedislike_thumb_active_color' => '46B4E3',
		'gallery_img_ht_lightbox_heart_likedislike_thumb_color'         => 'B50000',
		'gallery_img_ht_lightbox_heart_likedislike_thumb_active_color'  => 'EB1221',
		'gallery_img_ht_slider_heart_likedislike_thumb_color'           => '8F8F8F',
		'gallery_img_ht_slider_heart_likedislike_thumb_active_color'    => 'FF2A12',
		'gallery_img_ht_thumb_heart_likedislike_thumb_color'            => 'FF0000',
		'gallery_img_ht_thumb_heart_likedislike_thumb_active_color'     => 'C21313',
		'gallery_img_ht_just_heart_likedislike_thumb_color'             => 'E0E0E0',
		'gallery_img_ht_just_heart_likedislike_thumb_active_color'      => 'F23D3D',
		'gallery_img_ht_blog_heart_likedislike_thumb_color'             => 'D63E48',
		'gallery_img_ht_blog_heart_likedislike_thumb_active_color'      => 'E00000',
		'gallery_img_admin_image_hover_preview'                         => 'on',
		'gallery_img_ht_view2_popup_mobile_position_at_top'             => 'on',
		'gallery_img_ht_view10_image_behaviour'                            => 'crop',
		'gallery_img_ht_view10_element_width'                              => '250',
		'gallery_img_ht_view10_element_height'                             => '250',
		'gallery_img_ht_view10_element_margin'                             => '10',
		'gallery_img_ht_view10_element_border_width'                       => '0',
		'gallery_img_ht_view10_element_border_color'                       => 'DEDEDE',
		'gallery_img_ht_view10_element_overlay_background_color_'          => '484848',
		'gallery_img_ht_view10_element_overlay_opacity'                    => '70',
		'gallery_img_ht_view10_element_hover_effect'                       => 'true',
		'gallery_img_ht_view10_hover_effect_delay'                         => '0',
		'gallery_img_ht_view10_hover_effect_inverse'                       => 'false',
		'gallery_img_ht_view10_expanding_speed'                            => '500',
		'gallery_img_ht_view10_expand_block_height'                        => '500',
		'gallery_img_ht_view10_element_title_font_size'                    => '16',
		'gallery_img_ht_view10_element_title_font_color'                   => 'FFFFFF',
		'gallery_img_ht_view10_element_title_align'                        => 'center',
		'gallery_img_ht_view10_element_title_border_width'                 => '1',
		'gallery_img_ht_view10_element_title_border_color'                 => 'FFFFFF',
		'gallery_img_ht_view10_element_title_margin_top'                   => '40',
		'gallery_img_ht_view10_element_title_padding_top_bottom'           => '10',
		'gallery_img_ht_view10_expand_block_background_color'              => '222222',
		'gallery_img_ht_view10_expand_block_opacity'                       => '100',
		'gallery_img_ht_view10_expand_block_title_color'                   => 'd6d6d6',
		'gallery_img_ht_view10_expand_block_title_font_size'               => '35',
		'gallery_img_ht_view10_expand_block_description_font_size'         => '13',
		'gallery_img_ht_view10_expand_block_description_font_color'        => '999',
		'gallery_img_ht_view10_expand_block_description_font_hover_color'  => '999',
		'gallery_img_ht_view10_expand_block_description_text_align'        => 'left',
		'gallery_img_ht_view10_expand_block_button_background_color'       => '454545',
		'gallery_img_ht_view10_expand_block_button_background_hover_color' => '454545',
		'gallery_img_ht_view10_expand_block_button_text_color'             => '9f9f9f',
		'gallery_img_ht_view10_expand_block_button_font_size'              => '11',
		'gallery_img_ht_view10_expand_block_button_text'                   => 'View More',
		'gallery_img_ht_view10_show_center'                   			   => 'on',
		'gallery_img_ht_view10_expand_width'                               => '100',
		'gallery_img_ht_view10_paginator_fontsize'                         => '22',
		'gallery_img_ht_view10_paginator_color'                            => '1046B3',
		'gallery_img_ht_view10_paginator_icon_color'                       => '1046B3',
		'gallery_img_ht_view10_paginator_icon_size'                        => '18',
		'gallery_img_ht_view10_paginator_position'                         => 'center',
		'gallery_img_lightbox_slideAnimationType'                         => 'effect_1',
		'gallery_img_lightbox_lightboxView'                               => 'view1',
		'gallery_img_lightbox_speed_new'                                  => '600',
		'gallery_img_lightbox_width_new'                                  => '100',
		'gallery_img_lightbox_height_new'                                 => '100',
		'gallery_img_lightbox_videoMaxWidth'                              => '790',
		'gallery_img_lightbox_overlayDuration'                            => '150',
		'gallery_img_lightbox_overlayClose_new'                           => 'true',
		'gallery_img_lightbox_loop_new'                                   => 'true',
		'gallery_img_lightbox_escKey_new'                                 => 'true',
		'gallery_img_lightbox_keyPress_new'                               => 'true',
		'gallery_img_lightbox_arrows'                                     => 'true',
		'gallery_img_lightbox_mouseWheel'                                 => 'true',
		'gallery_img_lightbox_download'                                   => 'false',
		'gallery_img_lightbox_showCounter'                                => 'true',
		'gallery_img_lightbox_nextHtml'                                   => '',     //not used
		'gallery_img_lightbox_prevHtml'                                   => '',     //not used
		'gallery_img_lightbox_sequence_info'                              => 'image',
		'gallery_img_lightbox_sequenceInfo'                               => 'of',
		'gallery_img_lightbox_slideshow_new'                              => 'true',
		'gallery_img_lightbox_slideshow_auto_new'                         => 'false',
		'gallery_img_lightbox_slideshow_speed_new'                        => '2500',
		'gallery_img_lightbox_slideshow_start_new'                        => '',     //not used
		'gallery_img_lightbox_slideshow_stop_new'                         => '',     //not used
		'gallery_img_lightbox_watermark'                                  => 'false',
		'gallery_img_lightbox_socialSharing'                              => 'true',
		'gallery_img_lightbox_facebookButton'                             => 'true',
		'gallery_img_lightbox_twitterButton'                              => 'true',
		'gallery_img_lightbox_googleplusButton'                           => 'true',
		'gallery_img_lightbox_pinterestButton'                            => 'false',
		'gallery_img_lightbox_linkedinButton'                             => 'false',
		'gallery_img_lightbox_tumblrButton'                               => 'false',
		'gallery_img_lightbox_redditButton'                               => 'false',
		'gallery_img_lightbox_bufferButton'                               => 'false',
		'gallery_img_lightbox_diggButton'                                 => 'false',
		'gallery_img_lightbox_vkButton'                                   => 'false',
		'gallery_img_lightbox_yummlyButton'                               => 'false',
		'gallery_img_lightbox_watermark_text'                             => 'WaterMark',
		'gallery_img_lightbox_watermark_textColor'                        => 'ffffff',
		'gallery_img_lightbox_watermark_textFontSize'                     => '30',
		'gallery_img_lightbox_watermark_containerBackground'              => '000000',
		'gallery_img_lightbox_watermark_containerOpacity'                 => '90',
		'gallery_img_lightbox_watermark_containerWidth'                   => '300',
		'gallery_img_lightbox_watermark_position_new'                     => '9',
		'gallery_img_lightbox_watermark_opacity'                          => '70',
		'gallery_img_lightbox_watermark_margin'                           => '10',
		'gallery_img_lightbox_watermark_img_src_new'                      => GALLERY_IMG_IMAGES_PATH . '/admin_images/No-image-found.jpg',
		'gallery_img_lightbox_type'                                       => 'new_type'
	);

	return $gallery_default__params;
}
/**
 * Get all general options parameters in a single array
 *
 * @return array Array of all general options
 */
function gallery_img_get_option() {
	$new_options            = array(
		'gallery_img_admin_image_hover_preview' => 'on',
		'gallery_img_light_box_size_fix'        => 'false',
		'gallery_img_light_box_width'           => '500',
		'gallery_img_light_box_height'          => '500',
		'gallery_img_light_box_maxwidth'        => '900',
		'gallery_img_light_box_maxheight'       => '700',
		'gallery_img_light_box_initialwidth'    => '300',
		'gallery_img_light_box_initialheight'   => '100',
		'gallery_img_version'                   => '2.0.2'
	);
	$gallery_img_get_option = array();
	foreach ( $new_options as $name => $new_option ) {
		$gallery_img_get_option[ $name ] = get_option( $name );
	}

	return $gallery_img_get_option;
}

function gallery_img_get_view_slag_by_id( $id ) {
	global $wpdb;
	$query = $wpdb->prepare( "SELECT huge_it_sl_effects from " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id=%d", $id );
	$view  = $wpdb->get_var( $query );
	switch ( $view ) {
		case 0:
			$slug = 'content-popup';
			break;
		case 1:
			$slug = 'content-slider';
			break;
		case 3:
			$slug = 'slider';
			break;
		case 4:
			$slug = 'thumbnails';
			break;
		case 5:
			$slug = 'lightbox-gallery';
			break;
		case 6:
			$slug = 'justified';
			break;
		case 7:
			$slug = 'blog-style-gallery';
			break;
		case 10:
			$slug = 'elastic-grid';
			break;
	}

	return $slug;
}

/**
 * Get attachment ID by image src
 *
 * @param $image_url
 *
 * @return mixed
 */
function gallery_img_get_image_id( $image_url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $wpdb->prefix . "posts WHERE guid='%s';", $image_url ) );
	if ( $attachment ) {
		return $attachment[0];
	}
}


/**
 * Get image url by image src, width, height
 *
 * @param $image_src
 * @param $image_sizes
 * @param $is_thumbnail
 *
 * @return false|string
 */
function gallery_img_get_image_by_sizes_and_src( $image_src, $image_sizes, $is_thumbnail ) {
	$is_attachment = gallery_img_get_image_id( $image_src );
	$is_readable   = is_readable( $image_src );
	if ( $is_readable ) {
		$img_sizes  = getimagesize( $image_src );
		$img_height = $img_sizes[1];
	} else {
		$img_height = null;
	}

	if ( is_string( $image_sizes ) ) {
		$image_sizes = $image_sizes;
		$img_width   = intval( $image_sizes );
	}
	if ( is_object( $image_sizes ) ) {
		// Closures are currently implemented as objects
		$image_sizes = array( $image_sizes, '' );
	}
	if ( ! $is_attachment ) {
		$image_url = $image_src;
	} else {
		$attachment_id     = gallery_img_get_image_id( $image_src );
		$natural_img_width = explode( ',', wp_get_attachment_image_sizes( $attachment_id, 'full' ) );
		$natural_img_width = $natural_img_width[1];
		$natural_img_width = str_replace( ' ', '', $natural_img_width );
		$natural_img_width = intval( str_replace( 'px', '', $natural_img_width ) );
		if ( $is_thumbnail ) {
			$image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
		} elseif ( $image_sizes[0] <= 300 || $image_sizes[0] == '' ) {
			if ( $img_height == null || $img_height >= $natural_img_width ) {
				$image_url = wp_get_attachment_image_url( $attachment_id, 'large' );
			} else {
				$image_url = wp_get_attachment_image_url( $attachment_id, 'medium' );
			}
		} elseif ( $image_sizes[0] <= 700 ) {
			$image_url = wp_get_attachment_image_url( $attachment_id, 'large' );
		} elseif ( $image_sizes[0] >= $natural_img_width ) {
			$image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
		} else {
			$image_url = wp_get_attachment_image_url( $attachment_id, $image_sizes );
		}
	}

	return $image_url;
}

/**
 * Get User IP
 * @return mixed
 */
function gallery_img_get_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$huge_it_ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$huge_it_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$huge_it_ip = $_SERVER['REMOTE_ADDR'];
	}

	return $huge_it_ip;
}