<?php
/**
 * UABB SVG Animator Module file
 *
 * @package UABB SVG Animator Module
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class UABBSvgAnimatorModule
 *
 * Renders an SVG icon or photo with stroke-drawing animation powered by CSS
 * transitions and the IntersectionObserver API.
 */
class UABBSvgAnimatorModule extends FLBuilderModule {

	/**
	 * In-memory cache: icon-class string → SVG markup.
	 * Avoids parsing the same 600 KB JSON file more than once per request.
	 *
	 * @var array<string,string>
	 */
	private static $icon_cache = array();

	// =========================================================================
	// Constructor + SVG upload hooks
	// =========================================================================

	/**
	 * Constructor.
	 *
	 * Registers the module and hooks that enable safe SVG uploads for this
	 * module's photo field.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'SVG Animator', 'uabb' ),
				'description'     => __( 'Animate SVG icons with stroke drawing effects.', 'uabb' ),
				'category'        => BB_Ultimate_Addon_Helper::module_cat( BB_Ultimate_Addon_Helper::$creative_modules ),
				'group'           => UABB_CAT,
				'dir'             => BB_ULTIMATE_ADDON_DIR . 'modules/uabb-svg-animator/',
				'url'             => BB_ULTIMATE_ADDON_URL . 'modules/uabb-svg-animator/',
				'partial_refresh' => true,
				'icon'            => 'svg-animator.svg',
			)
		);

		// Allow SVG files in the WordPress media uploader.
		// phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.upload_mimes
		add_filter( 'upload_mimes', array( $this, 'allow_svg_mime' ) );

		// PHP's finfo/mime_content_type often mis-identifies SVGs as text/plain.
		// This filter corrects the detected type so WP accepts the upload.
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_svg_filetype' ), 10, 4 );

		// Sanitize SVG content BEFORE WordPress moves the temp file into uploads/.
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_svg_on_upload' ) );

		// Show a correct thumbnail / dimensions for SVG in the media library.
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'svg_media_library_preview' ), 10, 3 );

		// BB core validates photo-field uploads against its own extension regex
		// (hook: fl_module_upload_regex). SVG is not included by default, so we
		// extend it here — the sanitize_svg_on_upload() hook still runs and
		// strips any malicious content before the file is saved.
		add_filter( 'fl_module_upload_regex', array( $this, 'allow_svg_in_photo_regex' ), 10, 4 );

		// Defense-in-depth: sanitize every SVG that reaches the media library,
		// regardless of upload path. wp_handle_upload_prefilter does NOT fire
		// for XML-RPC (wp.uploadFile → wp_upload_bits) or REST API uploads.
		// This hook covers all paths and removes malicious SVGs after save.
		add_action( 'add_attachment', array( $this, 'sanitize_svg_attachment' ) );

		// Initialize select2 on the icon select field in the BB editor.
		add_action( 'fl_builder_ui_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
	}

	/**
	 * Enqueue select2 initialization for the icon select field in the BB editor.
	 */
	public function enqueue_editor_scripts() {
		wp_add_inline_script(
			'fl-builder',
			'(function($){
				if (typeof FLBuilder !== "undefined" && FLBuilderConfig.select2Enabled) {
					FLBuilder.addHook("settings-form-init", function(){
						var $sel = $("select.uabb-svg-icon-select", window.parent.document);
						if ($sel.length && !$sel.data("select2")) {
							$sel.select2({width:"100%"});
						}
					});
				}
			})(jQuery);'
		);
	}

	/**
	 * Return the SVG markup for the module icon.
	 *
	 * Loads the icon from this module's icon/ directory.
	 *
	 * @param string $icon Icon filename.
	 * @return string SVG markup or empty string.
	 */
	public function get_icon( $icon = '' ) {
		if ( '' !== $icon && file_exists( BB_ULTIMATE_ADDON_DIR . 'modules/uabb-svg-animator/icon/' . $icon ) ) {
			return fl_builder_filesystem()->file_get_contents( BB_ULTIMATE_ADDON_DIR . 'modules/uabb-svg-animator/icon/' . $icon );
		}
		return '';
	}

	// =========================================================================
	// SVG upload support
	// =========================================================================

	/**
	 * Add SVG to WordPress's allowed upload MIME types.
	 *
	 * Scoped to BB editor upload requests only so that SVG uploads are not
	 * enabled across the entire WordPress installation. The sanitize_svg_on_upload()
	 * hook still fires for any SVG that passes, providing an additional layer
	 * of defence.
	 *
	 * @param array $mimes Existing allowed types.
	 * @return array
	 */
	public function allow_svg_mime( $mimes ) {
		// Only extend the MIME whitelist during a BB-initiated upload request.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$action = isset( $_POST['action'] ) ? sanitize_key( $_POST['action'] ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$is_bb_upload = (
			false !== strpos( $action, 'fl_builder' ) ||
			false !== strpos( $action, 'fl_upload' ) ||
			'async-upload' === $action
		);

		if ( $is_bb_upload ) {
			$mimes['svg'] = 'image/svg+xml';
		}

		return $mimes;
	}

	/**
	 * Correct the detected MIME type for SVG files.
	 *
	 * PHP's finfo extension often reports SVGs as "text/plain" or "text/html",
	 * which causes WordPress to reject them even when the extension is allowed.
	 * We override the result here for files with an .svg extension.
	 *
	 * @param array      $data     { ext, type, proper_filename }.
	 * @param string     $file     Full path to the temp file.
	 * @param string     $filename Original upload filename.
	 * @param array|null $mimes    Allowed MIME types.
	 * @return array
	 */
	public function fix_svg_filetype( $data, $file, $filename, $mimes ) {
		// Only correct the MIME type during a BB-initiated upload request.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$action = isset( $_POST['action'] ) ? sanitize_key( $_POST['action'] ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$is_bb_upload = (
			false !== strpos( $action, 'fl_builder' ) ||
			false !== strpos( $action, 'fl_upload' ) ||
			'async-upload' === $action
		);

		if ( ! $is_bb_upload ) {
			return $data;
		}

		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if ( 'svg' === $ext ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}

	/**
	 * Sanitize an SVG upload before it is moved into the uploads directory.
	 *
	 * Fires on `wp_handle_upload_prefilter`. The file still sits in PHP's
	 * temp directory at this point, so we can rewrite it safely. If
	 * sanitization fails we set an error message and WordPress aborts.
	 *
	 * @param array $file $_FILES entry: { name, type, tmp_name, error, size }.
	 * @return array
	 */
	public function sanitize_svg_on_upload( $file ) {
		$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		if ( 'svg' !== $ext ) {
			return $file; // Not an SVG — leave untouched.
		}

		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$raw = file_get_contents( $file['tmp_name'] );

		if ( false === $raw ) {
			$file['error'] = __( 'Could not read the SVG file for sanitization.', 'uabb' );
			return $file;
		}

		$sanitized = self::sanitize_svg( $raw );

		if ( empty( $sanitized ) ) {
			$file['error'] = __( 'The SVG file could not be sanitized and was rejected. Please ensure it is a valid SVG without embedded scripts.', 'uabb' );
			return $file;
		}

		// Write the sanitized content back to the temp file.
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
		file_put_contents( $file['tmp_name'], $sanitized );

		return $file;
	}

	/**
	 * Sanitize an SVG after it has been saved to the uploads directory.
	 *
	 * Fires on `add_attachment` which covers ALL upload paths — standard
	 * wp_handle_upload(), XML-RPC wp.uploadFile (wp_upload_bits), REST API,
	 * WP-CLI, and any third-party uploader. If sanitization fails or strips
	 * all content, the attachment is deleted to prevent a malicious file from
	 * remaining accessible on disk.
	 *
	 * @param int $attachment_id Newly created attachment post ID.
	 */
	public function sanitize_svg_attachment( $attachment_id ) {
		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return;
		}

		$file = get_attached_file( $attachment_id );

		if ( ! $file || ! file_exists( $file ) ) {
			return;
		}

		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$raw = file_get_contents( $file );

		if ( false === $raw ) {
			return;
		}

		$sanitized = self::sanitize_svg( $raw );

		if ( empty( $sanitized ) ) {
			// Could not sanitize — delete the attachment so the file is not
			// left on disk in an unsafe state.
			wp_delete_attachment( $attachment_id, true );
			return;
		}

		// Overwrite the saved file with the sanitized content.
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
		file_put_contents( $file, $sanitized );
	}

	/**
	 * Inject a preview URL + dimensions for SVG attachments in the media library.
	 *
	 * WordPress does not generate thumbnails for SVGs, so the media modal shows
	 * a blank box. This filter provides the full-size URL as the "sizes.full"
	 * entry so the library can display the SVG.
	 *
	 * @param array      $response   Attachment JS data.
	 * @param WP_Post    $attachment Attachment post object.
	 * @param array|bool $meta       Attachment metadata.
	 * @return array
	 */
	public function svg_media_library_preview( $response, $attachment, $meta ) {
		if ( 'image/svg+xml' !== $response['mime'] ) {
			return $response;
		}

		// Already has sizes (another plugin handled it).
		if ( ! empty( $response['sizes'] ) ) {
			return $response;
		}

		$svg_path   = get_attached_file( $attachment->ID );
		$dimensions = $this->get_svg_dimensions( $svg_path );

		$response['sizes'] = array(
			'full' => array(
				'url'         => $response['url'],
				'width'       => $dimensions ? $dimensions['width'] : 0,
				'height'      => $dimensions ? $dimensions['height'] : 0,
				'orientation' => ( $dimensions && $dimensions['width'] > $dimensions['height'] )
					? 'landscape'
					: 'portrait',
			),
		);

		return $response;
	}

	/**
	 * Parse width / height from an SVG file.
	 *
	 * Reads the <svg> root element's width/height attributes, falling back to
	 * the viewBox if the explicit attributes are absent.
	 *
	 * @param string $file Absolute path to the SVG file.
	 * @return array|null { width: float, height: float } or null on failure.
	 */
	private function get_svg_dimensions( $file ) {
		if ( ! $file || ! file_exists( $file ) ) {
			return null;
		}

		if ( ! class_exists( 'DOMDocument' ) ) {
			return null;
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		// Read at PHP layer (respects open_basedir) — safer than $dom->load( $file ).
		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$raw = file_get_contents( $file );
		if ( false !== $raw ) {
			$dom->loadXML( $raw, LIBXML_NONET | LIBXML_NOENT );
		}
		libxml_clear_errors();

		$svg = $dom->documentElement; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( ! $svg ) {
			return null;
		}

		$width  = (float) $svg->getAttribute( 'width' );
		$height = (float) $svg->getAttribute( 'height' );

		// Fallback to viewBox if explicit dimensions are missing.
		if ( ! $width || ! $height ) {
			$viewbox = $svg->getAttribute( 'viewBox' );
			if ( $viewbox ) {
				$parts = preg_split( '/[\s,]+/', trim( $viewbox ) );
				if ( 4 === count( $parts ) ) {
					$width  = (float) $parts[2];
					$height = (float) $parts[3];
				}
			}
		}

		return ( $width && $height )
			? array(
				'width'  => $width,
				'height' => $height,
			)
			: null;
	}

	/**
	 * Extend BB's photo-field upload extension whitelist to include SVG.
	 *
	 * BB core defines a strict regex for each upload type in
	 * FLBuilder::wp_handle_upload_prefilter_filter() and rejects anything that
	 * doesn't match with "not a valid photo extension." The `fl_module_upload_regex`
	 * filter lets us add SVG (and SVGZ) without patching BB core.
	 *
	 * Our `sanitize_svg_on_upload()` hook still fires afterwards, so any
	 * malicious SVG content is stripped before the file lands in uploads/.
	 *
	 * @param array  $regex Keyed regex patterns: photo|video|audio.
	 * @param string $type  Upload type (photo, video, audio).
	 * @param string $ext   File extension being checked.
	 * @param array  $file  $_FILES entry.
	 * @return array
	 */
	public function allow_svg_in_photo_regex( $regex, $type, $ext, $file ) {
		if ( 'photo' === $type && preg_match( '#^svgz?$#i', $ext ) ) {
			$regex['photo'] = str_replace( ')#i', '|svgz?)#i', $regex['photo'] );
		}
		return $regex;
	}

	// =========================================================================
	// Public render entry point
	// =========================================================================

	/**
	 * Render icon or photo — called from includes/frontend.php.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$image_type = ! empty( $this->settings->image_type ) ? $this->settings->image_type : 'icon';

		if ( 'photo' === $image_type ) {
			$this->render_photo();
		} else {
			$this->render_icon();
		}
	}

	// =========================================================================
	// Icon mode — FA class → inline SVG via bundled JSON path data
	// =========================================================================

	/**
	 * Render inline SVG for the selected Font Awesome icon.
	 *
	 * If the icon field was cleared, falls back to the default (rocket).
	 *
	 * @since 1.0.0
	 */
	public function render_icon() {
		$icon_class = ! empty( $this->settings->svg_icon )
			? trim( $this->settings->svg_icon )
			: 'fas fa-rocket';

		if ( empty( $icon_class ) ) {
			$icon_class = 'fas fa-rocket';
		}

		$svg = self::get_svg_by_icon( $icon_class );

		if ( ! empty( $svg ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized inside get_svg_by_icon().
			echo $svg;
		} else {
			// Fallback: render as icon font so the editor shows something.
			echo '<i class="' . esc_attr( $icon_class ) . ' uabb-svg-fallback-icon" aria-hidden="true"></i>';
		}
	}

	/**
	 * Convert a FontAwesome class string to a safe inline SVG element.
	 *
	 * Reads the path data for the icon from the bundled JSON files and
	 * constructs a minimal SVG with no fill, ready for stroke animation.
	 * Results are keyed by $icon_class in the static cache.
	 *
	 * @since 1.0.0
	 *
	 * @param string $icon_class FA class e.g. "fas fa-rocket".
	 * @return string Sanitized SVG markup, or empty string on failure.
	 */
	public static function get_svg_by_icon( $icon_class ) {
		if ( empty( $icon_class ) || ! is_string( $icon_class ) ) {
			return '';
		}

		if ( isset( self::$icon_cache[ $icon_class ] ) ) {
			return self::$icon_cache[ $icon_class ];
		}

		$parts = preg_split( '/\s+/', trim( $icon_class ) );
		if ( count( $parts ) < 2 ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		$prefix_map = array(
			'fas' => 'solid',
			'fab' => 'brands',
			'far' => 'regular',
		);

		$prefix    = $parts[0];
		$library   = isset( $prefix_map[ $prefix ] ) ? $prefix_map[ $prefix ] : 'solid';
		$icon_name = str_replace( 'fa-', '', $parts[1] );

		if ( ! preg_match( '/^[a-z0-9\-]+$/', $icon_name ) ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		$json_path = BB_ULTIMATE_ADDON_DIR . "modules/uabb-svg-animator/assets/lib/svg-icons/{$library}.json";
		if ( ! file_exists( $json_path ) ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$raw = file_get_contents( $json_path );
		if ( false === $raw ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		$icon_data = json_decode( $raw, true );
		if ( empty( $icon_data['icons'][ $icon_name ] ) ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		$info = $icon_data['icons'][ $icon_name ];

		if ( ! isset( $info[0], $info[1], $info[4] ) ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		$width  = absint( $info[0] );
		$height = absint( $info[1] );
		$path_d = $info[4];

		$paths_html = '';
		if ( is_array( $path_d ) ) {
			foreach ( $path_d as $single_path ) {
				if ( is_string( $single_path ) ) {
					$paths_html .= '<path fill="none" d="' . esc_attr( $single_path ) . '"></path>';
				}
			}
		} else {
			$paths_html = '<path fill="none" d="' . esc_attr( (string) $path_d ) . '"></path>';
		}

		if ( empty( $paths_html ) ) {
			self::$icon_cache[ $icon_class ] = '';
			return '';
		}

		$svg = sprintf(
			'<svg class="uabb-svg-icon uabb-svg-icon--%s" aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d">%s</svg>',
			esc_attr( $icon_name ),
			$width,
			$height,
			$paths_html
		);

		self::$icon_cache[ $icon_class ] = $svg;
		return $svg;
	}

	// =========================================================================
	// Photo mode — SVG from media library
	// =========================================================================

	/**
	 * Render the SVG from the media library.
	 *
	 * @since 1.0.0
	 */
	public function render_photo() {
		$this->render_photo_library();
	}

	/**
	 * Render an SVG from the media library.
	 *
	 * SVG attachments are sanitized and inlined so their paths are animatable.
	 * Non-SVG attachments show an editor placeholder prompting to upload an SVG.
	 */
	private function render_photo_library() {
		$attachment_id = ! empty( $this->settings->photo ) ? absint( $this->settings->photo ) : 0;

		if ( ! $attachment_id ) {
			if ( FLBuilderModel::is_builder_active() ) {
				$this->render_placeholder( __( 'Please select an SVG image in the module settings.', 'uabb' ) );
			}
			return;
		}

		$mime = get_post_mime_type( $attachment_id );

		if ( 'image/svg+xml' !== $mime ) {
			if ( FLBuilderModel::is_builder_active() ) {
				$this->render_placeholder( __( 'The selected file is not an SVG. Please upload an SVG image for stroke animation.', 'uabb' ) );
			}
			return;
		}

		$this->render_inline_svg_from_attachment( $attachment_id );
	}

	/**
	 * Read an SVG from the media library, sanitize it, and echo it inline.
	 *
	 * Sanitization runs even though the file was already sanitized on upload,
	 * providing defence-in-depth against any SVG that bypassed the upload hook.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 */
	private function render_inline_svg_from_attachment( $attachment_id ) {
		$file = get_attached_file( $attachment_id );

		if ( ! $file || ! file_exists( $file ) ) {
			$this->render_placeholder( __( 'SVG file not found.', 'uabb' ) );
			return;
		}

		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$raw_svg = file_get_contents( $file );

		if ( false === $raw_svg ) {
			$this->render_placeholder( __( 'Could not read SVG file.', 'uabb' ) );
			return;
		}

		$safe_svg = self::sanitize_svg( $raw_svg );

		if ( empty( $safe_svg ) ) {
			$this->render_placeholder( __( 'SVG could not be rendered safely.', 'uabb' ) );
			return;
		}

		// Inject CSS class into the root <svg> element using DOMDocument so that
		// existing class attributes are merged rather than duplicated.
		$class_dom = new DOMDocument();
		libxml_use_internal_errors( true );
		if ( $class_dom->loadXML( $safe_svg, LIBXML_NONET ) ) {
			$root = $class_dom->documentElement; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( $root ) {
				$existing = $root->getAttribute( 'class' );
				$classes  = 'uabb-svg-icon uabb-svg-photo-inline' . ( $existing ? ' ' . $existing : '' );
				$root->setAttribute( 'class', $classes );
				$out = $class_dom->saveXML( $root );
				if ( false !== $out ) {
					$safe_svg = $out;
				}
			}
		}
		libxml_clear_errors();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fully sanitized by sanitize_svg().
		echo $safe_svg;
	}

	/**
	 * Render an empty-state placeholder.
	 *
	 * @param string $message User-visible message.
	 */
	private function render_placeholder( $message ) {
		echo '<div class="uabb-svg-placeholder"><p>' . esc_html( $message ) . '</p></div>';
	}

	// =========================================================================
	// SVG sanitizer — used both at upload time and at render time
	// =========================================================================

	/**
	 * Sanitize raw SVG markup for safe inline output.
	 *
	 * Uses DOMDocument to remove:
	 *   - XML processing instructions (e.g. <?xml-stylesheet?>)
	 *   - <script>, <foreignObject>, <animate>, <set>, <animateTransform>,
	 *     <animateMotion>, <handler>, <listener> elements
	 *   - Dangerous CSS inside <style> blocks (@import, url(), expression())
	 *   - on* event-handler attributes — including namespaced variants (e.g. x:onclick)
	 *   - href / xlink:href attributes containing javascript:, vbscript:, or data: URIs
	 *
	 * Falls back to regex stripping when DOMDocument is unavailable.
	 * Returns an empty string if the content cannot be parsed as XML at all.
	 *
	 * @since 1.0.0
	 *
	 * @param string $svg_content Raw SVG string.
	 * @return string Sanitized SVG string, or empty string on failure.
	 */
	public static function sanitize_svg( $svg_content ) {
		if ( empty( $svg_content ) || ! is_string( $svg_content ) ) {
			return '';
		}

		if ( ! class_exists( 'DOMDocument' ) ) {
			return self::sanitize_svg_regex_fallback( $svg_content );
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );

		// Strip UTF-8 BOM if present.
		$svg_content = preg_replace( '/^\xEF\xBB\xBF/', '', $svg_content );

		// LIBXML_NONET prevents outbound network requests during parsing.
		// LIBXML_NOENT substitutes entity references inline (no external loading).
		$loaded = $dom->loadXML( $svg_content, LIBXML_NONET | LIBXML_NOENT );
		libxml_clear_errors();

		if ( ! $loaded ) {
			return '';
		}

		// --- Remove XML processing instructions (e.g. xml-stylesheet PIs) ---
		// These are not elements so getElementsByTagName() misses them.
		foreach ( iterator_to_array( $dom->childNodes ) as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( XML_PI_NODE === $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$dom->removeChild( $child );
			}
		}

		// --- Remove blocked tags entirely ------------------------------------
		$blocked_tags = array(
			'script',
			'foreignObject',
			'animate',
			'set',
			'animateTransform',
			'animateMotion',
			'handler',
			'listener',
		);
		foreach ( $blocked_tags as $tag ) {
			$nodes = $dom->getElementsByTagName( $tag );
			for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
				$node = $nodes->item( $i );
				if ( $node && $node->parentNode ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$node->parentNode->removeChild( $node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}
			}
		}

		// --- Sanitize <style> content — strip dangerous CSS rules -----------
		// Removes external resource loading and CSS-based execution vectors
		// while preserving class-based fills and structural styles.
		$style_nodes = $dom->getElementsByTagName( 'style' );
		for ( $i = $style_nodes->length - 1; $i >= 0; $i-- ) {
			$node = $style_nodes->item( $i );
			if ( ! $node ) {
				continue;
			}
			$css = $node->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			// Strip @import rules (can load external stylesheets / exfiltrate data).
			$css = preg_replace( '/@import\s[^;]+;?/i', '', $css );
			// Strip url() pointing outside the document; keep fragment-only refs.
			$css = preg_replace( '/url\s*\(\s*["\']?\s*(?!#)[^)]+\s*["\']?\s*\)/i', 'none', $css );
			// Strip expression() — IE CSS code execution.
			$css = preg_replace( '/expression\s*\([^)]*\)/i', 'none', $css );
			// Strip -moz-binding and behavior — Firefox/IE CSS execution.
			$css = preg_replace( '/(-moz-binding|behavior)\s*:[^;]+;?/i', '', $css );
			// Replace the text node with the sanitized CSS.
			while ( $node->firstChild ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$node->removeChild( $node->firstChild ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
			$node->appendChild( $dom->createTextNode( $css ) );
		}

		// --- Scrub dangerous attributes on every element --------------------
		// Copy the live NodeList to an array before iterating to guard against
		// mutations inside the loop invalidating the iterator.
		$all_elements = iterator_to_array( $dom->getElementsByTagName( '*' ) );
		foreach ( $all_elements as $element ) {
			$remove = array();

			foreach ( $element->attributes as $attr ) {
				$name = strtolower( $attr->name );
				// Use localName (without namespace prefix) to catch namespaced
				// event handlers such as x:onclick or xlink:onload.
				$local = strtolower( $attr->localName ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$value = strtolower( preg_replace( '/[\x00-\x20]/', '', $attr->value ) ); // strip whitespace/nulls.

				// Remove on* event handlers (catches x:onclick via localName).
				if ( 0 === strpos( $local, 'on' ) ) {
					$remove[] = $attr->name;
					continue;
				}

				// Remove href / src / action / xlink:href with dangerous URI schemes.
				// Block javascript:, vbscript:, and ALL data: URIs — including
				// data:image/svg+xml payloads that can carry embedded onload handlers.
				$link_attrs = array( 'href', 'src', 'action', 'xlink:href' );
				if ( in_array( $name, $link_attrs, true ) ) {
					if (
						0 === strpos( $value, 'javascript:' ) ||
						0 === strpos( $value, 'vbscript:' ) ||
						0 === strpos( $value, 'data:' )
					) {
						$remove[] = $attr->name;
					}
				}
			}

			foreach ( $remove as $attr_name ) {
				$element->removeAttribute( $attr_name );
			}
		}

		// --- Sanitize <use> and <image> external href/xlink:href ----------
		// Allow internal fragment references (#id) but remove anything that
		// points outside the current document (HTTP URLs, data: URIs, relative
		// paths to other uploaded files, etc.).
		foreach ( array( 'use', 'image' ) as $tag ) {
			$nodes = $dom->getElementsByTagName( $tag );
			for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
				$node = $nodes->item( $i );
				if ( ! $node ) {
					continue;
				}
				foreach ( array( 'href', 'xlink:href' ) as $attr_name ) {
					$val = $node->getAttribute( $attr_name );
					// Keep fragment-only (#id) refs; remove everything else.
					if ( '' !== $val && '#' !== $val[0] ) {
						$node->removeAttribute( $attr_name );
					}
				}
			}
		}

		$output = $dom->saveXML( $dom->documentElement ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return ( false !== $output ) ? $output : '';
	}

	/**
	 * Build the list of icons available in our bundled JSON files.
	 *
	 * Only icons that have actual SVG path data in our bundled JSON are
	 * included, so the select field never lets a user pick an icon we cannot
	 * animate. Results are cached in a transient to avoid re-parsing the JSON
	 * on every admin request.
	 *
	 * @return array  Flat options array: 'fas fa-rocket' => 'Rocket (Solid)'
	 */
	public static function get_available_icons() {
		$cache_key = 'uabb_svg_animator_icons_v1';
		$cached    = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$options   = array();
		$json_dir  = BB_ULTIMATE_ADDON_DIR . 'modules/uabb-svg-animator/assets/lib/svg-icons/';
		$style_map = array(
			'solid'   => array(
				'prefix' => 'fas',
				'label'  => 'Solid',
			),
			'regular' => array(
				'prefix' => 'far',
				'label'  => 'Regular',
			),
			'brands'  => array(
				'prefix' => 'fab',
				'label'  => 'Brands',
			),
		);

		foreach ( $style_map as $style => $meta ) {
			$file = $json_dir . $style . '.json';
			if ( ! file_exists( $file ) ) {
				continue;
			}

			// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
			$raw  = file_get_contents( $file );
			$data = json_decode( $raw, true );

			if ( empty( $data['icons'] ) || ! is_array( $data['icons'] ) ) {
				continue;
			}

			foreach ( array_keys( $data['icons'] ) as $icon_name ) {
				$key             = $meta['prefix'] . ' fa-' . $icon_name;
				$options[ $key ] = ucwords( str_replace( '-', ' ', $icon_name ) ) . ' (' . $meta['label'] . ')';
			}
		}

		asort( $options );
		set_transient( $cache_key, $options, WEEK_IN_SECONDS );

		return $options;
	}

	/**
	 * Normalize a color value stored by BB's color picker.
	 *
	 * BB stores colors as bare hex strings (e.g. "ff0000"), full hex with hash
	 * ("#ff0000"), or CSS functional notation ("rgb(255,0,0)", "rgba(…)",
	 * "hsl(…)") when alpha is enabled. This helper ensures we always get a
	 * valid CSS color string regardless of which format BB chose.
	 *
	 * @param string $raw     Raw value from BB settings.
	 * @param string $default Fallback when $raw is empty (should be a valid CSS color).
	 * @return string Valid CSS color string, or $default.
	 */
	public static function normalize_color( $raw, $default = '' ) {
		if ( empty( $raw ) ) {
			return $default;
		}
		$raw = trim( $raw );

		// Strict allowlist for CSS functional colors — validates the full value
		// to prevent injection like "rgb(0,0,0); background:url(evil)".
		if ( preg_match( '/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/i', $raw ) ) {
			return $raw;
		}
		if ( preg_match( '/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*[\d.]+\s*\)$/i', $raw ) ) {
			return $raw;
		}
		if ( preg_match( '/^hsl\(\s*\d{1,3}\s*,\s*[\d.]+%?\s*,\s*[\d.]+%?\s*\)$/i', $raw ) ) {
			return $raw;
		}
		if ( preg_match( '/^hsla\(\s*\d{1,3}\s*,\s*[\d.]+%?\s*,\s*[\d.]+%?\s*,\s*[\d.]+\s*\)$/i', $raw ) ) {
			return $raw;
		}

		// Hex color with # prefix — validate length and characters.
		if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $raw ) ) {
			return $raw;
		}

		// Bare hex string — validate with WP's own sanitizer then prepend #.
		$validated = sanitize_hex_color_no_hash( $raw );
		if ( null === $validated ) {
			return $default;
		}
		return '#' . $validated;
	}

	/**
	 * Regex-based SVG sanitizer used when DOMDocument is unavailable.
	 *
	 * Less robust than the DOM approach but covers the most common attack
	 * vectors as a last resort.
	 *
	 * @param string $svg Raw SVG markup.
	 * @return string Partially sanitized SVG.
	 */
	private static function sanitize_svg_regex_fallback( $svg ) {
		// Remove dangerous tags.
		$svg = preg_replace( '/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $svg );
		$svg = preg_replace( '/<foreignObject\b[^>]*>[\s\S]*?<\/foreignObject>/i', '', $svg );

		// Strip <style> blocks entirely — the regex path cannot safely parse CSS
		// to remove only dangerous rules, so removing all <style> is the safest option.
		$svg = preg_replace( '/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $svg );

		// Strip on* event-handler attributes.
		$svg = preg_replace( '/\s+on\w+\s*=\s*(["\'])[^"\']*\1/i', '', $svg );

		// Block dangerous URI schemes (javascript:, vbscript:, ALL data: URIs).
		$svg = preg_replace(
			'/\s+(?:href|xlink:href)\s*=\s*(["\'])\s*(?:javascript|vbscript|data):[^"\']*\1/i',
			'',
			$svg
		);

		// Strip external hrefs from <use> and <image> — keep only #fragment refs.
		$svg = preg_replace(
			'/(<(?:use|image)[^>]*)\s+(?:href|xlink:href)\s*=\s*(["\'])(?!#)[^"\']*\2/i',
			'$1',
			$svg
		);

		return $svg;
	}
}

// Load form settings for BB 2.2+.
if ( UABB_Compatibility::$version_bb_check ) {
	require_once BB_ULTIMATE_ADDON_DIR . 'modules/uabb-svg-animator/uabb-svg-animator-bb-2-2-compatibility.php';
}
