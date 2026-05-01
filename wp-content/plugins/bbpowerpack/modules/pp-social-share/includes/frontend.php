<?php
/**
 *  Social Share Module front-end file
 *
 *  @package  Social Share Module
 */

global $post;

$main_el_class  = 'pp-social-share-content';
$main_el_class .= ' pp-share-buttons-view-' . esc_attr( $settings->view );
$main_el_class .= ' pp-share-buttons-skin-' . esc_attr( $settings->skin );
$main_el_class .= ' pp-share-buttons-shape-' . esc_attr( $settings->shape );

$main_el_class .= ' pp-social-share-col-' . absint( $settings->columns );
if ( $settings->columns_medium ) {
	$main_el_class .= ' pp-social-share-col-md-' . absint( $settings->columns_medium );
}
if ( $settings->columns_responsive ) {
	$main_el_class .= ' pp-social-share-col-sm-' . absint( $settings->columns_responsive );
}

$main_el_class .= ' pp-share-buttons-align-' . esc_attr( $settings->alignment );
if ( $settings->alignment_medium ) {
	$main_el_class .= ' pp-share-buttons-md-align-' . esc_attr( $settings->alignment_medium );
}
if ( $settings->alignment_responsive ) {
	$main_el_class .= ' pp-share-buttons-sm-align-' . esc_attr( $settings->alignment_responsive );
}

$main_el_class .= ' pp-share-buttons-color-' . esc_attr( $settings->color_source );

$button_class = 'pp-share-button';

$icon_type = ! isset( $settings->icon_type ) || 'font' == $settings->icon_type ? 'font' : 'svg';

$url = $module->get_current_url();
?>
<div class="<?php echo $main_el_class; ?>">
	<div class="pp-social-share-inner">
	<?php
	for ( $i = 0; $i < count( $settings->social_icons ); $i++ ) {

		if ( ! is_object( $settings->social_icons[ $i ] ) ) {
			continue;
		}

		$social_icon = $settings->social_icons[ $i ];
		$share_link  = 'javascript:void(0);';
		$title       = '';
		$icon_svg    = $module->get_social_icon_svg( $social_icon->social_share_type );

		if ( 'custom' == $settings->share_url_type ) {
			$url = esc_url( $settings->share_url );
		} else {
			$title = urlencode( get_the_title() );
		}

		switch ( $social_icon->social_share_type ) {
			case 'facebook':
				$share_link  = 'https://www.facebook.com/sharer.php?u=' . $url . '&title=' . $title;
				$share_title = __( 'Facebook', 'bb-powerpack' );
				$share_icon  = 'fab fa-facebook';
				break;
			case 'twitter':
				$share_link  = 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title;
				$share_title = __( 'X', 'bb-powerpack' );
				$share_icon  = 'fab fa-twitter';
				break;
			case 'pinterest':
				if ( '' == $social_icon->fallback_image ) {
					$pin_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
					$pin_url   = is_array( $pin_thumb ) ? $pin_thumb['0'] : '';
				} else {
					$pin_url = $social_icon->fallback_image_src;
				}
				$share_link  = 'https://pinterest.com/pin/create/bookmarklet/?media=' . esc_url( $pin_url ) . '&url=' . $url . '&description=' . $title;
				$share_title = __( 'Pinterest', 'bb-powerpack' );
				$share_icon  = 'fab fa-pinterest';
				break;
			case 'linkedin':
				$share_link  = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title;
				$share_title = __( 'Linkedin', 'bb-powerpack' );
				$share_icon  = 'fab fa-linkedin';
				break;
			case 'digg':
				$share_link  = 'https://digg.com/submit?url=' . $url;
				$share_title = __( 'Digg', 'bb-powerpack' );
				$share_icon  = 'fab fa-digg';
				break;
			case 'reddit':
				$share_link  = 'https://reddit.com/submit?url=' . $url . '&title=' . $title;
				$share_title = __( 'Reddit', 'bb-powerpack' );
				$share_icon  = 'fab fa-reddit';
				break;
			case 'stumbleupon':
				$share_link  = 'https://www.stumbleupon.com/submit?url=' . $url . '&title=' . $title;
				$share_title = __( 'Stumbleupon', 'bb-powerpack' );
				$share_icon  = 'fab fa-stumbleupon';
				break;
			case 'vk':
				$pin_thumb   = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$pin_url     = $pin_thumb['0'];
				$share_link  = 'https://vkontakte.ru/share.php?url=' . $url . '&title=' . $title . '&image=' . $pin_url;
				$share_title = __( 'VK', 'bb-powerpack' );
				$share_icon  = 'fab fa-vk';
				break;
			case 'odnoklassniki':
				$pin_thumb   = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$pin_url     = $pin_thumb['0'];
				$share_link  = 'https://connect.ok.ru/offer?url=' . $url . '&title=' . $title . '&imageUrl=' . $pin_url;
				$share_title = __( 'OK', 'bb-powerpack' );
				$share_icon  = 'fab fa-odnoklassniki';
				break;
			case 'bluesky':
				//$share_link  = 'https://bsky.app/post?url=' . $url . '&text=' . $title;
				$share_link  = 'https://bsky.app/intent/compose?&text=' . urlencode( urldecode( $title ) . ' ' . $url );
				$share_title = __( 'Bluesky', 'bb-powerpack' );
				$share_icon  = 'fab fa-bluesky';
				break;
			case 'delicious':
				$share_link  = 'https://del.icio.us/save?url=' . $url . '&title=' . $title;
				$share_title = __( 'Delicious', 'bb-powerpack' );
				$share_icon  = 'fab fa-delicious';
				break;
			case 'pocket':
				$share_link  = 'https://getpocket.com/edit?url=' . $url;
				$share_title = __( 'Pocket', 'bb-powerpack' );
				$share_icon  = 'fab fa-get-pocket';
				break;
			case 'whatsapp':
				$share_link  = 'https://api.whatsapp.com/send?text=' . $url;
				$share_title = __( 'WhatsApp', 'bb-powerpack' );
				$share_icon  = 'fab fa-whatsapp';
				break;
			case 'xing':
				$share_link  = 'https://www.xing.com/app/user?op=share&url=' . $url;
				$share_title = __( 'Xing', 'bb-powerpack' );
				$share_icon  = 'fab fa-xing';
				break;
			case 'print':
				$share_link  = 'javascript:print()';
				$share_title = __( 'Print', 'bb-powerpack' );
				$share_icon  = 'fa fab fa-print';
				break;
			case 'email':
				$share_link  = 'mailto:?body=' . $url;
				$share_title = __( 'Email', 'bb-powerpack' );
				$share_icon  = 'fas fa-envelope';
				break;
			case 'telegram':
				$share_link  = 'https://telegram.me/share/url?url=' . $url . '&text=' . $title;
				$share_title = __( 'Telegram', 'bb-powerpack' );
				$share_icon  = 'fab fa-telegram';
				break;
			case 'skype':
				$share_link  = 'https://web.skype.com/share?url=' . $url;
				$share_title = __( 'Skype', 'bb-powerpack' );
				$share_icon  = 'fab fa-skype';
				break;
			case 'fb-messenger':
				$share_link  = 'https://www.facebook.com/dialog/send?link=' . $url . '&app_id=' . BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_fb_app_id', true ) . '&redirect_uri=' . $url;
				$share_title = __( 'Messenger', 'bb-powerpack' );
				$share_icon  = 'fab fa-facebook-messenger';
				break;
			case 'buffer':
				$share_link  = 'https://bufferapp.com/add?url=' . $url;
				$share_title = __( 'Buffer', 'bb-powerpack' );
				$share_icon  = 'fab fa-buffer';
				break;
			default:
				break;
		}

		$social_icon             = $settings->social_icons[ $i ];
		$social_share_type_class = 'pp-share-button-' . esc_attr( $social_icon->social_share_type );
		?>
		<div class="pp-share-grid-item">
			<div class="<?php echo $button_class; ?> <?php echo $social_share_type_class; ?>">
				<?php $share_link = 'print' == $social_icon->social_share_type ? $share_link : esc_url( $share_link ); ?>
				<a class="pp-share-button-link" href="<?php echo $share_link; ?>">
					<?php if ( 'icon' === $settings->view || 'icon-text' === $settings->view ) { ?>
					<span class="pp-share-button-icon">
						<?php if ( '' !== $social_icon->custom_icon ) { ?>
							<i class="<?php echo $social_icon->custom_icon; ?>" aria-hidden="true"></i>
						<?php } elseif ( 'svg' === $icon_type ) { ?>
							<span class="icon-type-svg" aria-hidden="true">
							<?php echo $module->get_social_icon_svg( $social_icon->social_share_type ); ?>
							</span>
						<?php } elseif ( 'twitter' === $social_icon->social_share_type ) { ?>
							<i class="fab pp-x-icon">𝕏</i>
						<?php } else { ?>
							<i class="<?php echo $share_icon; ?>" aria-hidden="true"></i>
						<?php } ?>
						<span class="pp-screen-only">Share on <?php echo $share_title; ?></span>
					</span>
					<?php } ?>

					<?php if ( 'icon-text' === $settings->view || 'text' === $settings->view ) { ?>
						<div class="pp-share-button-text">
							<?php if ( '' !== $social_icon->text ) { ?>
								<span class="pp-share-button-title"><?php echo $social_icon->text; ?></span>
							<?php } else { ?>
								<span class="pp-share-button-title"><?php echo $share_title; ?></span>
							<?php } ?>
						</div>
					<?php } ?>
				</a>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
