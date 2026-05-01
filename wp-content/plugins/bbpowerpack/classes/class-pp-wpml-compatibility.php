<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BB_PowerPack_WPML {
	static public function init() {
		add_filter( 'wpml_beaver_builder_modules_to_translate', __CLASS__ . '::translate_fields', 10, 1 );
	}

	static public function translate_fields( $modules ) {
		$config = array(
			'pp-advanced-accordion' => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Accordion',
			),
			'pp-advanced-menu'      => array(
				'fields' => array(
					array(
						'field'       => 'custom_menu_text',
						'type'        => __( 'Advanced Menu - Toggle Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-advanced-tabs'      => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Tabs',
			),
			'pp-business-hours'     => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Business_Hours',
			),
			'pp-notifications'      => array(
				'fields' => array(
					array(
						'field'       => 'notification_content',
						'type'        => __( 'Alert Box - Content', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
				),
			),
			'pp-animated-headlines' => array(
				'fields' => array(
					array(
						'field'       => 'before_text',
						'type'        => __( 'Animated Headlines - Before Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'highlighted_text',
						'type'        => __( 'Animated Headlines - Highlighted Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'rotating_text',
						'type'        => __( 'Animated Headlines - Rotating Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'after_text',
						'type'        => __( 'Animated Headlines - After Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-announcement-bar'   => array(
				'fields' => array(
					array(
						'field'       => 'announcement_content',
						'type'        => __( 'Annoucement Bar - Content', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'announcement_link_text',
						'type'        => __( 'Annoucement Bar - Link Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'announcement_link',
						'type'        => __( 'Annoucement Bar - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-contact-form'       => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Contact Form - Custom Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Contact Form - Custom Description', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'name_label',
						'type'        => __( 'Contact Form - Custom Label - Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'email_label',
						'type'        => __( 'Contact Form - Custom Label - Email', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'phone_label',
						'type'        => __( 'Contact Form - Custom Label - Phone', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'subject_label',
						'type'        => __( 'Contact Form - Custom Label - Subject', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'message_label',
						'type'        => __( 'Contact Form - Custom Label - Message', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'success_message',
						'type'        => __( 'Contact Form - Success Message', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'success_url',
						'type'        => __( 'Contact Form - Success URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'btn_text',
						'type'        => __( 'Contact Form - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-content-grid'       => array(
				'fields' => array(
					array(
						'field'       => 'more_link_text',
						'type'        => __( 'Content Grid - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'all_filter_label',
						'type'        => __( 'Content Grid - All Filter Label', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'meta_separator',
						'type'        => __( 'Content Grid - Meta Separator', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'no_results_message',
						'type'        => __( 'Content Grid - No Results Message', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-content-tiles'      => array(
				'fields' => array(
					array(
						'field'       => 'no_results_message',
						'type'        => __( 'Content Tiles - No Results Message', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-contact-form-7'     => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Contact Form 7 Styler - Custom Title', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Contact Form 7 Styler - Custom Description', 'bb-powerpack' ),
						'editor-type' => 'AREA',
					),
				),
			),
			'pp-dual-button'        => array(
				'fields' => array(
					array(
						'field'       => 'button_1_title',
						'type'        => __( 'Dual Button 1 - Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_1_link',
						'type'        => __( 'Dual Button 1 - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'title_attr_1',
						'type'        => __( 'Dual Button 1 - Attribute Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_2_title',
						'type'        => __( 'Dual Button 2 - Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_2_link',
						'type'        => __( 'Dual Button 2 - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'title_attr_2',
						'type'        => __( 'Dual Button 2 - Attribute Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-fancy-heading'      => array(
				'fields' => array(
					array(
						'field'       => 'heading_title',
						'type'        => __( 'Fancy Heading - Heading Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-flipbox'            => array(
				'fields' => array(
					array(
						'field'       => 'front_title',
						'type'        => __( 'FlipBox - Front Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'front_description',
						'type'        => __( 'FlipBox - Front Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'back_title',
						'type'        => __( 'FlipBox - Back Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'back_description',
						'type'        => __( 'FlipBox - Back Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'link_text',
						'type'        => __( 'FlipBox - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'link',
						'type'        => __( 'FlipBox - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-gravity-form'       => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Gravity Form Styler - Custom Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Gravity Form Styler - Custom Description', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
				),
			),
			'pp-highlight-box'      => array(
				'fields' => array(
					array(
						'field'       => 'box_content',
						'type'        => __( 'Highlight Box - Text', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'box_link',
						'type'        => __( 'Highlight Box - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-hover-cards'        => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Hover_Cards',
			),
			'pp-hover-cards-2'      => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Hover_Cards_2',
			),
			'pp-iconlist'           => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Icon_List',
			),
			'pp-image'              => array(
				'fields' => array(
					array(
						'field'       => 'photo_url',
						'type'        => __( 'Image - Photo URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'caption',
						'type'        => __( 'Image - Caption', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'link_url',
						'type'        => __( 'Image - Link URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-image-panels'       => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Image_Panels',
			),
			'pp-infobox'            => array(
				'fields' => array(
					array(
						'field'       => 'title_prefix',
						'type'        => __( 'InfoBox - Prefix', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'title',
						'type'        => __( 'InfoBox - Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'description',
						'type'        => __( 'InfoBox - Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'pp_infobox_read_more_text',
						'type'        => __( 'InfoBox - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'link',
						'type'        => __( 'InfoBox - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-infolist'           => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Infolist',
			),
			'pp-logos-grid'         => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Logos_Grid',
			),
			'pp-modal-box'          => array(
				'fields' => array(
					array(
						'field'       => 'modal_title',
						'type'        => __( 'Modal Box - Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'modal_type_video',
						'type'        => __( 'Modal Box - Embed Code / URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'modal_type_url',
						'type'        => __( 'Modal Box - URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'modal_type_content',
						'type'        => __( 'Modal Box - Content', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'modal_type_html',
						'type'        => __( 'Modal Box - Raw HTML', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Modal Box - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-pullquote'          => array(
				'fields' => array(
					array(
						'field'       => 'pullquote_content',
						'type'        => __( 'Pullquote - Quote', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'pullquote_title',
						'type'        => __( 'Pullquote - Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-restaurant-menu'    => array(
				'fields' => array(
					array(
						'field'       => 'menu_heading',
						'type'        => __( 'Restaurant Menu - Menu Heading', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'currency_symbol',
						'type'        => __( 'Restaurant Menu - Currency Symbol', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-info-banner'        => array(
				'fields' => array(
					array(
						'field'       => 'banner_title',
						'type'        => __( 'Smart Banner - Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'banner_description',
						'type'        => __( 'Smart Banner - Description', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Smart Banner - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_link',
						'type'        => __( 'Smart Banner - Button Link', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-smart-button'       => array(
				'fields' => array(
					array(
						'field'       => 'text',
						'type'        => __( 'Smart Button - Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'sub_text',
						'type'        => __( 'Smart Button - Sub Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'link',
						'type'        => __( 'Smart Button - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-heading'            => array(
				'fields' => array(
					array(
						'field'       => 'heading_title',
						'type'        => __( 'Smart Heading - Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'heading_title2',
						'type'        => __( 'Smart Heading - Secondary Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'heading_sub_title',
						'type'        => __( 'Smart Heading - Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'heading_link',
						'type'        => __( 'Smart Heading - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-subscribe-form'     => array(
				'fields' => array(
					array(
						'field'       => 'service_account',
						'type'        => __( 'Subscribe Form - Account Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'api_url',
						'type'        => __( 'Subscribe Form - API URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'api_key',
						'type'        => __( 'Subscribe Form - API Key', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'box_content',
						'type'        => __( 'Subscribe Form - Content', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'input_name_placeholder',
						'type'        => __( 'Subscribe Form - Name Field Placeholder Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'input_email_placeholder',
						'type'        => __( 'Subscribe Form - Email Field Placeholder Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'checkbox_field_text',
						'type'        => __( 'Subscribe Form - Checkbox Field Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'btn_text',
						'type'        => __( 'Subscribe Form - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'success_message',
						'type'        => __( 'Subscribe Form - Success Message', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'success_url',
						'type'        => __( 'Subscribe Form - Success URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-team'               => array(
				'fields' => array(
					array(
						'field'       => 'member_name',
						'type'        => __( 'Team - Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'member_designation',
						'type'        => __( 'Team - Designation', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'member_description',
						'type'        => __( 'Team - Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'link',
						'type'        => __( 'Team - LINK URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'email',
						'type'        => __( 'Team - Email', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'facebook_url',
						'type'        => __( 'Team - Facebook URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'twiiter_url',
						'type'        => __( 'Team - Twiiter URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'googleplus_url',
						'type'        => __( 'Team - Google Plus URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'pinterest_url',
						'type'        => __( 'Team - Pinterest URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'linkedin_url',
						'type'        => __( 'Team - Linkedin URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'youtube_url',
						'type'        => __( 'Team - Youtube URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'instagram_url',
						'type'        => __( 'Team - Instagram URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'vimeo_url',
						'type'        => __( 'Team - Vimeo URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'github_url',
						'type'        => __( 'Team - Github URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'dribbble_url',
						'type'        => __( 'Team - Dribbble URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'tumblr_url',
						'type'        => __( 'Team - Tumblr URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'flickr_url',
						'type'        => __( 'Team - Flickr URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'wordpress_url',
						'type'        => __( 'Team - WordPress URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-testimonials'       => array(
				'fields'            => array(
					array(
						'field'       => 'heading',
						'type'        => __( 'Testimonials - Heading', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_Testimonials',
			),
			'pp-timeline'           => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Timeline',
			),
			'pp-pricing-table'      => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Pricing_Table',
			),
			'pp-table'              => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Table',
			),
			'pp-restaurant-menu'    => array(
				'fields'            => array(
					array(
						'field'       => 'menu_heading',
						'type'        => __( 'Restaurant / Services Menu - Heading', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'currency_symbol',
						'type'        => __( 'Restaurant / Services Menu - Currency Symbol', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_Restaurant_Menu',
			),
			'pp-caldera-form'       => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Caledra Form Styler - Custom Title', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Caldera Form Styler - Custom Description', 'bb-powerpack' ),
						'editor-type' => 'AREA',
					),
				),
			),
			'pp-ninja-form'         => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Ninja Form Styler - Custom Title', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Ninja Form Styler - Custom Description', 'bb-powerpack' ),
						'editor-type' => 'AREA',
					),
				),
			),
			'pp-wpforms'            => array(
				'fields' => array(
					array(
						'field'       => 'select_form_field',
						'type'        => __( 'WPForms Styler - Form ID', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'custom_title',
						'type'        => __( 'WPForms Styler - Custom Title', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'WPForms Styler - Custom Description', 'bb-powerpack' ),
						'editor-type' => 'AREA',
					),
				),
			),
			'pp-file-download'      => array(
				'fields' => array(
					array(
						'field'       => 'file',
						'type'        => __( 'File Download - URL', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'text',
						'type'        => __( 'File Download - Button Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
				),
			),
			'pp-album'              => array(
				'fields' => array(
					array(
						'field'       => 'content_title',
						'type'        => __( 'Album - Content Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'content_subtitle',
						'type'        => __( 'Album - Content Subtitle', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'content_button_text',
						'type'        => __( 'Album - Content Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'cover_btn_text',
						'type'        => __( 'Album - Cover Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-author-box'         => array(
				'fields' => array(
					array(
						'field'       => 'author_name_text',
						'type'        => __( 'Author Box - Author Name Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'biography_text',
						'type'        => __( 'Author Box - Biography', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'link_url',
						'type'        => __( 'Author Box - URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Author Box - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'archive_url',
						'type'        => __( 'Author Box - Button URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-category-grid'      => array(
				'fields' => array(
					array(
						'field'       => 'category_count_text',
						'type'        => __( 'Category Grid - Counter Text ( Singular )', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'category_count_text_plural',
						'type'        => __( 'Category Grid - Counter Text ( Plural )', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'category_button_text',
						'type'        => __( 'Category Grid - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-countdown'          => array(
				'fields' => array(
					array(
						'field'       => 'expire_message',
						'type'        => __( 'Countdown - Expire Message', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'redirect_link',
						'type'        => __( 'Countdown - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'year_label_plural',
						'type'        => __( 'Countdown - Year Label in Plural', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'year_label_singular',
						'type'        => __( 'Countdown - Year Label in Singular', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'month_label_plural',
						'type'        => __( 'Countdown - Month Label in Plural', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'month_label_singular',
						'type'        => __( 'Countdown - Month Label in Singular', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'day_label_plural',
						'type'        => __( 'Countdown - Dday Label in Plural', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'day_label_singular',
						'type'        => __( 'Countdown - Day Label in Singular', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'hour_label_plural',
						'type'        => __( 'Countdown - Hour Label in Plural', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'hour_label_singular',
						'type'        => __( 'Countdown - Hour Label in Singular', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'minute_label_plural',
						'type'        => __( 'Countdown - Minute Label in Plural', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'minute_label_singular',
						'type'        => __( 'Countdown - Minute Label in Singular', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'second_label_plural',
						'type'        => __( 'Countdown - Second Label in Plural', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'second_label_singular',
						'type'        => __( 'Countdown - Second Label in Singular', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-coupon'             => array(
				'fields' => array(
					array(
						'field'       => 'discount',
						'type'        => __( 'Coupon - Discount', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'coupon_reveal',
						'type'        => __( 'Coupon - Reveal Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'coupon_code',
						'type'        => __( 'Coupon - Coupon Code', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'no_code_need',
						'type'        => __( 'Coupon - No Code Needed', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'title',
						'type'        => __( 'Coupon - Content Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'description',
						'type'        => __( 'Coupon - Content Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'link_url',
						'type'        => __( 'Coupon - Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'link_text',
						'type'        => __( 'Coupon - Link Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-custom-grid'        => array(
				'fields' => array(
					array(
						'field'       => 'no_results_message',
						'type'        => __( 'Custom Grid - No Results Message', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-devices'            => array(
				'fields' => array(
					array(
						'field'       => 'youtube_url',
						'type'        => __( 'Devices - Youtube URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'vimeo_url',
						'type'        => __( 'Devices - Vimeo URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'dailymotion_url',
						'type'        => __( 'Devices - Dailymotion URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'mp4_video_url',
						'type'        => __( 'Devices - MP4 Video URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'm4v_video_url',
						'type'        => __( 'Devices - M4V Video URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'ogg_video_url',
						'type'        => __( 'Devices - OGG Video URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'webm_video_url',
						'type'        => __( 'Devices - WEBM Video URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-facebook-button'    => array(
				'fields' => array(
					array(
						'field'       => 'url',
						'type'        => __( 'Facebook Button - Custom URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-facebook-comments'  => array(
				'fields' => array(
					array(
						'field'       => 'url',
						'type'        => __( 'Facebook Comments - Custom URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-facebook-embed'     => array(
				'fields' => array(
					array(
						'field'       => 'post_url',
						'type'        => __( 'Facebook Embed - Post URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'video_url',
						'type'        => __( 'Facebook Embed - Video URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'comment_url',
						'type'        => __( 'Facebook Embed - Comment URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-facebook-page'      => array(
				'fields' => array(
					array(
						'field'       => 'page_url',
						'type'        => __( 'Facebook Page - Page URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-fluent-form'        => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Fluent Form - Custom Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Fluent Form - Custom Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
				),
			),
			'pp-formidable-form'    => array(
				'fields' => array(
					array(
						'field'       => 'custom_title',
						'type'        => __( 'Formidable Form - Custom Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'custom_description',
						'type'        => __( 'Formidable Form - Custom Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
				),
			),
			'pp-image-comparison'   => array(
				'fields' => array(
					array(
						'field'       => 'before_img_label',
						'type'        => __( 'Image Comparison - Before Image Label', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'after_image',
						'type'        => __( 'Image Comparison - After Image Label', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-image-scroll'       => array(
				'fields' => array(
					array(
						'field'       => 'overlay_text',
						'type'        => __( 'Image Scroll - Overlay Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-instagram-feed'     => array(
				'fields' => array(
					array(
						'field'       => 'username',
						'type'        => __( 'Instagram Feed - Username', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'user_id',
						'type'        => __( 'Instagram Feed - User ID', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'client_id',
						'type'        => __( 'Instagram Feed - Client ID', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'access_token',
						'type'        => __( 'Instagram Feed - Access Token', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'tag_name',
						'type'        => __( 'Instagram Feed - Tag Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'insta_link_title',
						'type'        => __( 'Instagram Feed - Profile Link Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'insta_profile_url',
						'type'        => __( 'Instagram Feed - Instagram Profile URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-login-form'         => array(
				'fields' => array(
					array(
						'field'       => 'username_label',
						'type'        => __( 'Login Form - Username Label', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'username_placeholder',
						'type'        => __( 'Login Form - Username Placeholder', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'password_label',
						'type'        => __( 'Login Form - Password Label', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'password_placeholder',
						'type'        => __( 'Login Form - Password Placeholder', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Login Form - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'redirect_url',
						'type'        => __( 'Login Form - Redirect URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'redirect_logout_url',
						'type'        => __( 'Login Form - Redirect Logout URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'lost_password_text',
						'type'        => __( 'Login Form - Lost your password Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'remember_me_text',
						'type'        => __( 'Login Form - Remember Me Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-dotnav'             => array(
				'fields' => array(
					array(
						'field'       => 'row_ids',
						'type'        => __( 'Dot Nav - Row Ids', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
				),
			),
			'pp-search-form'        => array(
				'fields' => array(
					array(
						'field'       => 'placeholder',
						'type'        => __( 'Search Form - Placeholder', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Search Form - Button text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-star-rating'        => array(
				'fields' => array(
					array(
						'field'       => 'rating_title',
						'type'        => __( 'Star Rating - Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-twitter-buttons'    => array(
				'fields' => array(
					array(
						'field'       => 'profile',
						'type'        => __( 'Twitter Button - Profile URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'recipient_id',
						'type'        => __( 'Twitter Button - Recipient ID', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'default_text',
						'type'        => __( 'Twitter Button - Default Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'hashtag_url',
						'type'        => __( 'Twitter Button - Hashtag URL or #hashtag', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'via',
						'type'        => __( 'Twitter Button - Via (twitter handler)', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'share_text',
						'type'        => __( 'Twitter Button - Share Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'share_url',
						'type'        => __( 'Twitter Button - Share URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-twitter-grid'       => array(
				'fields' => array(
					array(
						'field'       => 'url',
						'type'        => __( 'Twitter Embedded Grid - Collection URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-twitter-timeline'   => array(
				'fields' => array(
					array(
						'field'       => 'username',
						'type'        => __( 'Twitter Embedded Timeline - Username', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-twitter-tweet'      => array(
				'fields' => array(
					array(
						'field'       => 'tweet_url',
						'type'        => __( 'Twitter Embedded Tweet - Tweet URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
			),
			'pp-video'              => array(
				'fields' => array(
					array(
						'field'       => 'youtube_url',
						'type'        => __( 'Video - Youtube URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'vimeo_url',
						'type'        => __( 'Video - Vimeo URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'dailymotion_url',
						'type'        => __( 'Video - Dailymotion URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'hosted_url',
						'type'        => __( 'Video - Hosted URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'external_url',
						'type'        => __( 'Video - External URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'video_title',
						'type'        => __( 'Video - Structured Data Video Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'video_desc',
						'type'        => __( 'Video - Structured Data Video Description', 'bb-powerpack' ),
						'editor_type' => 'AREA',
					),
				),
			),
			'pp-video-gallery'      => array(
				'fields'            => array(
					array(
						'field'       => 'filters_all_text',
						'type'        => __( 'Video Gallery - Filter All Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_Video_Gallery',
			),
			'pp-social-icons'       => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Social_Icon',
			),
			'pp-social-share'       => array(
				'fields'            => array(
					array(
						'field'       => 'share_url',
						'type'        => __( 'Social Share - Custom Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
				'integration-class' => 'WPML_PP_Social_Share',
			),
			'pp-sitemap'            => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Sitemap',
			),
			'pp-reviews'            => array(
				'fields'            => array(),
				'integration-class' => 'WPML_PP_Reviews',
			),
			'pp-card-slider'            => array(
				'fields'            => array(
					array(
						'field'       => 'not_found_msg',
						'type'        => __( 'Card Slider - Not Found Message', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_Card_Slider',
			),
			'pp-content-ticker'            => array(
				'fields'            => array(
					array(
						'field'       => 'not_found_msg',
						'type'        => __( 'Content Ticker - Not Found Message', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'heading_text',
						'type'        => __( 'Content Ticker - Heading Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'heading_link',
						'type'        => __( 'Content Ticker - Heading Link', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
				),
				'integration-class' => 'WPML_PP_Content_Ticker',
			),
			'pp-post-timeline'      => array(
				'fields' => array(
					array(
						'field'       => 'button_text',
						'type'        => __( 'Post Timeline - Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
			),
			'pp-faq'                => array(
				'fields'            => array(
					array(
						'field'       => 'acf_repeater_name',
						'type'        => __( 'FAQ - ACF Repeater Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'acf_repeater_label',
						'type'        => __( 'FAQ - ACF Repeater Question', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'acf_repeater_content',
						'type'        => __( 'FAQ - ACF Repeater Answer', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'acf_options_page_repeater_name',
						'type'        => __( 'FAQ - ACF Repeater Field Name', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'acf_options_page_repeater_label',
						'type'        => __( 'FAQ - ACF Repeater Sub Field Name (Question)', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'acf_options_page_repeater_content',
						'type'        => __( 'FAQ - ACF Repeater Sub Field Name (Answer)', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'faq_id_prefix',
						'type'        => __( 'FAQ - Custom ID Prefix', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_FAQ',
			),
			'pp-google-map'         => array(
				'fields'            => array(
					array(
						'field'       => 'map_style_code',
						'type'        => __( 'Google Map - Custom Style', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
				),
				'integration-class' => 'WPML_PP_Google_Map',
			),
			'pp-how-to'             => array(
				'fields'            => array(
					array(
						'field'       => 'title',
						'type'        => __( 'How To - Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'description',
						'type'        => __( 'How To - Description', 'bb-powerpack' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'total_time_text',
						'type'        => __( 'How To - Total Time Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'estimated_cost_text',
						'type'        => __( 'How To - Estimated Cost Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'currency_iso_code',
						'type'        => __( 'How To - Currency ISO Code', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'supply_title',
						'type'        => __( 'How To - Supply Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'tool_title',
						'type'        => __( 'How To - Tool Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'step_section_title',
						'type'        => __( 'How To - Step Title', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_How_To',
			),
			'pp-hotspot'            => array(
				'fields'            => array(
					array(
						'field'       => 'photo_url',
						'type'        => __( 'Hotspot - Photo URL', 'bb-powerpack' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'overlay_button',
						'type'        => __( 'Hotspot - Overlay Button Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'pre_text',
						'type'        => __( 'Hotspot - Previous Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'next_text',
						'type'        => __( 'Hotspot - Next Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'end_text',
						'type'        => __( 'Hotspot - End Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_Hotspot',
			),
			'pp-filterable-gallery' => array(
				'fields'            => array(
					array(
						'field'       => 'custom_all_text',
						'type'        => __( 'Filterable Gallery - Custom All Text', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'custom_id_prefix',
						'type'        => __( 'Filterable Gallery - Custom ID Prefix', 'bb-powerpack' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'WPML_PP_Filterable_Gallery',
			),
			'pp-login-form' => array(
				'fields' => array(
					array(
						'field'       => 'username_label',
						'type'        => __( 'Login Form - Username Label', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'username_placeholder',
						'type'        => __( 'Login Form - Username Placeholder', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'password_label',
						'type'        => __( 'Login Form - Password Label', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'password_placeholder',
						'type'        => __( 'Login Form - Password Placeholder', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Login Form - Button Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'redirect_url',
						'type'        => __( 'Login Form - Redirect URL', 'bb-powerpack' ),
						'editor-type' => 'LINK',
					),
					array(
						'field'       => 'redirect_logout_url',
						'type'        => __( 'Login Form - Redirect Logout URL', 'bb-powerpack' ),
						'editor-type' => 'LINK',
					),
					array(
						'field'       => 'lost_password_text',
						'type'        => __( 'Login Form - Lost Password Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'remember_me_text',
						'type'        => __( 'Login Form - Remember Me Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'separator_text',
						'type'        => __( 'Login Form - Separator Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
				)
			),
			'pp-registration-form' => array(
				'fields' => array(
					array(
						'field'       => 'btn_text',
						'type'        => __( 'Registration Form - Button Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'email_subject',
						'type'        => __( 'Registration Form - User Email Subject', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'email_content',
						'type'        => __( 'Registration Form - User Email Content', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'email_from',
						'type'        => __( 'Registration Form - User From Email', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'email_from_name',
						'type'        => __( 'Registration Form - User From Name', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'admin_email_subject',
						'type'        => __( 'Registration Form - Admin Email Subject', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'admin_email_content',
						'type'        => __( 'Registration Form - Admin Email Content', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'redirect_url',
						'type'        => __( 'Registration Form - Redirect URL', 'bb-powerpack' ),
						'editor-type' => 'LINK',
					),
					array(
						'field'       => 'success_message',
						'type'        => __( 'Registration Form - Success Message', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
				),
				'integration-class'     => 'WPML_PP_Registration_Form'
			),
			'pp-sliding-menus' => array(
				'fields' => array(
					array(
						'field'       => 'back_text',
						'type'        => __( 'Sliding Menu - Back Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
				),
			),
			'pp-toc' => array(
				'fields' => array(
					array(
						'field'       => 'heading_title',
						'type'        => __( 'Table of Contents - Title', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
				),
			),
			'pp-offcanvas-content' => array(
				'fields' => array(
					array(
						'field'       => 'button_text',
						'type'        => __( 'Off-Canvas Content - Button Text', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
					array(
						'field'       => 'burger_label',
						'type'        => __( 'Off-Canvas Content - Hamburger Label', 'bb-powerpack' ),
						'editor-type' => 'LINE',
					),
				),
				'integration-class'     => 'WPML_PP_Offcanvas_Content'
			),
        );

		$config = apply_filters( 'pp_wpml_translation_config', $config );

		foreach ( $config as $module_name => $module_fields ) {
			$module_fields['conditions'] = array( 'type' => $module_name );
			$modules[ $module_name ]     = $module_fields;
		}

		self::init_classes();

		return $modules;
	}

	static private function init_classes() {
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-accordion.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-business-hours.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-hover-cards-2.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-hover-cards.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-icon-list.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-image-panels.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-logos-grid.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-tabs.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-testimonials.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-registration-form.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-timeline.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-pricing-table.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-table.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-restaurant-menu.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-infolist.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-video-gallery.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-social-icons.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-social-share.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-sitemap.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-reviews.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-card-slider.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-content-ticker.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-faq.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-google-map.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-how-to.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-hotspot.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-filterable-gallery.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-offcanvas-content.php';
	}
}

BB_PowerPack_WPML::init();
