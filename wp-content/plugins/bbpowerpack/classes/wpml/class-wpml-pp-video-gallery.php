<?php

class WPML_PP_Video_Gallery extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->videos;
	}

	public function get_fields() {
		return array( 'video_title', 'youtube_url', 'vimeo_url', 'dailymotion_url', 'hosted_url', 'external_url', 'filter_tags', 'schema_video_title', 'schema_video_desc' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'video_title':
				return esc_html__( 'Video Gallery - Video Title', 'bb-powerpack' );

			case 'youtube_url':
				return esc_html__( 'Video Gallery - Youtube URL', 'bb-powerpack' );

			case 'vimeo_url':
				return esc_html__( 'Video Gallery - Vimeo URL', 'bb-powerpack' );

			case 'dailymotion_url':
				return esc_html__( 'Video Gallery - Dailymotion URL', 'bb-powerpack' );

			case 'hosted_url':
				return esc_html__( 'Video Gallery - Hosted URL', 'bb-powerpack' );

			case 'external_url':
				return esc_html__( 'Video Gallery - External URL', 'bb-powerpack' );

			case 'filter_tags':
				return esc_html__( 'Video Gallery - Filter Tags', 'bb-powerpack' );

			case 'schema_video_title':
				return esc_html__( 'Video Gallery - Schema Video Title', 'bb-powerpack' );

			case 'schema_video_desc':
				return esc_html__( 'Video Gallery - Schema Video Description', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'video_title':
				return 'LINE';

			case 'youtube_url':
				return 'LINK';

			case 'vimeo_url':
				return 'LINK';

			case 'dailymotion_url':
				return 'LINK';

			case 'hosted_url':
				return 'LINK';

			case 'external_url':
				return 'LINK';

			case 'filter_tags':
				return 'LINE';

			case 'schema_video_title':
				return 'LINE';

			case 'schema_video_desc':
				return 'TEXTAREA';

			default:
				return '';
		}
	}

}
