<?php
$item_classes = array(
	'pp-video-gallery-item'
);

if ( 'custom' === $video->overlay && ! empty( $video->custom_overlay ) ) {
	$item_classes[] = 'pp-video-has-overlay';
}
if ( 'carousel' === $layout ) {
	$item_classes[] = 'swiper-slide';
}

if ( $filters_enabled ) {
	$tags = $module->get_tags_array( $video );
	foreach ( array_keys( $tags ) as $tag_name ) {
		$item_classes[] = 'pp-filter-' . $tag_name;
	}
}
?>
<div class="<?php echo implode( ' ', $item_classes ); ?>" data-index="<?php echo $index; ?>">
	<div class="pp-video">
		<?php
		if ( 'above' === $settings->info_position ) {
			$module->render_video_info( $video );
		}
		?>
		<?php
			FLBuilder::render_module_html( 'pp-video', array(
				'video_type'		=> $video->video_type,
				'youtube_url'		=> $video->youtube_url,
				'vimeo_url'			=> $video->vimeo_url,
				'dailymotion_url'	=> $video->dailymotion_url,
				'wistia_url'	    => $video->wistia_url,
				'hosted_url'		=> $video->hosted_url,
				'external_url'		=> $video->external_url,
				'start_time'		=> $video->start_time,
				'end_time'			=> $video->end_time,
				'aspect_ratio'		=> $settings->aspect_ratio,
				'autoplay'			=> $settings->autoplay,
				'mute'				=> $settings->mute,
				'loop'				=> $settings->loop,
				'controls'			=> $settings->controls,
				'showinfo'			=> $settings->showinfo,
				'modestbranding'	=> $settings->modestbranding,
				'logo'				=> $settings->logo,
				'color'				=> $settings->color,
				'yt_privacy'		=> $settings->yt_privacy,
				'rel'				=> $settings->rel,
				'vimeo_title'		=> $settings->vimeo_title,
				'vimeo_portrait'	=> $settings->vimeo_portrait,
				'vimeo_byline'		=> $settings->vimeo_byline,
				'download_button'	=> $settings->download_button,
				'poster'			=> $settings->poster,
				'poster_src'		=> $settings->poster_src,
				'overlay'			=> $video->overlay,
				'custom_overlay'	=> $video->custom_overlay,
				'custom_overlay_src' => $video->custom_overlay_src,
				'play_icon'			=> $settings->play_icon,
				'lightbox'			=> $settings->lightbox,
				'schema_enabled'	=> $video->schema_enabled,
				'video_title'		=> $video->schema_video_title,
				'video_desc'		=> $video->schema_video_desc,
				'video_thumbnail'	=> $video->schema_video_thumbnail,
				'video_thumbnail_src' => isset( $video->schema_video_thumbnail_src ) ? $video->schema_video_thumbnail_src : '',
				'video_upload_date'	=> $video->schema_video_upload_date,
				'title_attr_text'	=> $video->video_title,
			) );
		?>
		<?php
		if ( 'below' === $settings->info_position ) {
			$module->render_video_info( $video );
		}
		?>
	</div>
</div>