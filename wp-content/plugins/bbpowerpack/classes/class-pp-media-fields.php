<?php
/**
 * Handles logic media uploader field.
 *
 * @package BB_PowerPack
 * @since 1.0.0
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PP_Media_Fields' ) ) {
	/**
	 * @class PPFields
	 */
	class PP_Media_Fields {

		/**
		 * Holds the class object.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		public static $instance;

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( defined( 'DOING_AJAX' ) ) {
				add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_field_cta' ), 12, 2 );
				add_filter( 'attachment_fields_to_save', array( $this, 'attachment_field_cta_save' ), 12, 2 );
			}

			add_action( 'fl_page_data_add_properties', array( $this, 'add_field_connection' ) );
		}

		/**
		 * Add Custom Link field to media uploader.
		 *
		 * @param $form_fields array, fields to include in attachment form
		 * @param $post object, attachment record in database
		 * @return $form_fields, modified form fields
		 */
		public function attachment_field_cta( $form_fields, $post ) {
			$form_fields['pp-custom-link'] = array(
				'label' => __( 'Custom Link', 'bb-powerpack' ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, 'gallery_external_link', true ),
			);

			return $form_fields;
		}

		/**
		 * Save values of Custom Link field in media uploader.
		 *
		 * @param $post array, the post data for database
		 * @param $attachment array, attachment fields from $_POST form
		 * @return $post array, modified post data
		 */
		public function attachment_field_cta_save( $post, $attachment ) {
			if ( isset( $attachment['pp-custom-link'] ) ) {
				update_post_meta( $post['ID'], 'gallery_external_link', $attachment['pp-custom-link'] );
			}
			return $post;
		}

		public function add_field_connection() {
			FLPageData::add_post_property( 'pp_media_custom_link', array(
				'label'  => sprintf( __( 'Media - Custom URL (%s)', 'bb-powerpack' ), pp_get_admin_label() ),
				'group'  => 'posts',
				'type'   => 'url',
				'getter' => array( $this, 'get_custom_link' ),
			) );
		}

		public function get_custom_link() {
			$attachment_id = get_post_thumbnail_id();
			$link = '';

			if ( ! empty( $attachment_id ) ) {
				$link = get_post_meta( $attachment_id, 'gallery_external_link', true );
			}

			return $link;
		}

		/**
		 * Returns the singleton instance of the class.
		 *
		 * @since 1.0.0
		 *
		 * @return object
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PP_Media_Fields ) ) {
				self::$instance = new PP_Media_Fields();
			}

			return self::$instance;
		}

	}

	$pp_media_fields = PP_Media_Fields::get_instance();
} // End if().
