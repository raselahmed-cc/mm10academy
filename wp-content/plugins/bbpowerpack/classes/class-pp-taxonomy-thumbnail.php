<?php
/**
 * Handles logic for the term thumbnail.
 *
 * @package BB_PowerPack
 * @since 2.7.3
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BB_PowerPack_Taxonomy_Thumbnail.
 */
final class BB_PowerPack_Taxonomy_Thumbnail {
	/**
	 * Holds the value of setting field taxonomy_thumbnail_enable.
	 *
	 * @since 2.7.3
	 */
	static public $taxonomy_thumbnail_enable = 'disabled';

	/**
	 * Holds the value of setting field taxonomies.
	 *
	 * @since 2.7.3
	 */
	static public $taxonomies = array();

	/**
	 * Settings Tab constant.
	 */
	const SETTINGS_TAB = 'extensions';

	/**
	 * Initializing.
	 *
	 * @since 2.7.3
	 */
	static public function init() {

		add_action( 'pp_admin_settings_extensions_save', __CLASS__ . '::save_settings' );

		self::$taxonomy_thumbnail_enable = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_taxonomy_thumbnail_enable' );

		self::$taxonomies = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_taxonomy_thumbnail_taxonomies' );

		if ( 'disabled' !== self::$taxonomy_thumbnail_enable ) {
			add_action( 'admin_init', __CLASS__ . '::taxonomy_thumbnail_hooks' );
			add_action( 'admin_print_scripts', __CLASS__ . '::taxonomy_admin_scripts' );
			add_action( 'admin_print_styles', __CLASS__ . '::taxonomy_admin_styles' );
		}

		add_action( 'plugins_loaded', __CLASS__ . '::init_field_connections', 99 );
	}

	static public function init_field_connections() {
		if ( class_exists( 'FLPageData' ) ) {
			FLPageData::add_group( 'powerpack', array(
				'label' => pp_get_admin_label(),
			) );
		}

		add_action( 'fl_page_data_add_properties', __CLASS__ . '::add_field_connection' );
	}

	static public function taxonomy_admin_scripts() {
		global $current_screen;
		if ( $current_screen && ( $current_screen->base == 'edit-tags' || $current_screen->base == 'term' ) ) {
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script( 'pp-taxonomy-thumbnail-upload', BB_POWERPACK_URL . '/assets/js/pp-taxonomy-thumbnail.js', array( 'jquery' ), null, false );
		}
	}

	static public function taxonomy_admin_styles() {
		?>
		<style>
			.column-taxonomy_thumbnail {
				width: 80px;
			}
		</style>
		<?php
	}
	/**
	 * Taxonomy Lists.
	 *
	 * @access private
	 */
	static public function get_taxonomies_checklist() {
		$post_types    = array();
		$taxonomy_type = array();

		foreach ( FLBuilderLoop::post_types() as $slug => $type ) {
			$taxonomies = FLBuilderLoop::taxonomies( $slug );
			if ( ! empty( $taxonomies ) ) {
				//echo "<h4>".$type->label."</h4>";
			}
			foreach ( (array) $taxonomies as $taxonomy ) {
				if ( ! isset( $taxonomy->name ) ) {
					continue;
				}
				//print_r( $taxonomy->object_type);
				if ( ! isset( $taxonomy->label ) ) {
					continue;
				}

				if ( ! $taxonomy->public || ! $taxonomy->show_ui ) {
					continue;
				}

				$taxonomy_type[ $taxonomy->name ][] = $type->name;

				if ( isset( $taxonomy_type[ $taxonomy->name ] ) && count( $taxonomy_type[ $taxonomy->name ] ) > 1 ) {
					continue;
				}

				$id = 'bb-taxonomy-images-' . $taxonomy->name;

				$checked = '';
				if ( ! empty( self::$taxonomies ) && in_array( $taxonomy->name, (array) self::$taxonomies ) ) {
					$checked = ' checked="checked"';
				}

				echo "\n" . '<p><label for="' . esc_attr( $id ) . '">';
				echo '<input' . $checked . ' id="' . esc_attr( $id ) . '" class="dashicons" type="checkbox" name="bb_powerpack_taxonomy_thumbnail_taxonomies[]" value="' . esc_attr( $taxonomy->name ) . '" />';
				echo ' ' . esc_html( $taxonomy->label ) . '</label>';
				if ( isset( $taxonomy->object_type ) && count( $taxonomy->object_type ) > 1 ) {
					// translators: %s for tax label.
					echo sprintf( __( ' ( Used in %s )', 'bb-powerpack' ), implode( ', ', $taxonomy->object_type ) );
				}
				echo '</p>';
			}

			//print_r($taxonomy_type);
		}
	}

	/**
	 * Save settings.
	 *
	 * Saves setting fields value in options.
	 *
	 * @since 2.7.3
	 */
	static public function save_settings() {
		if ( ! isset( $_POST['bb_powerpack_taxonomy_thumbnail_enable'] ) ) {
			BB_PowerPack_Admin_Settings::update_option( 'bb_powerpack_taxonomy_thumbnail_enable', 'disabled' );
			return;
		}

		$enable     = sanitize_text_field( $_POST['bb_powerpack_taxonomy_thumbnail_enable'] );
		$taxonomies = array();
		if ( isset( $_POST['bb_powerpack_taxonomy_thumbnail_taxonomies'] ) && ! empty( $_POST['bb_powerpack_taxonomy_thumbnail_taxonomies'] ) ) {
			foreach ( $_POST['bb_powerpack_taxonomy_thumbnail_taxonomies'] as $taxonomy ) {
				$taxonomies[] = sanitize_text_field( $taxonomy );
			}
		}

		if ( 'yes' === $enable ) {
			$enable = 'enabled';
		} elseif ( 'no' === $enable ) {
			$enable = 'disabled';
		} else {
			$enable = 'disabled';
		}

		BB_PowerPack_Admin_Settings::update_option( 'bb_powerpack_taxonomy_thumbnail_enable', $enable );
		BB_PowerPack_Admin_Settings::update_option( 'bb_powerpack_taxonomy_thumbnail_taxonomies', $taxonomies );
		self::$taxonomy_thumbnail_enable = $enable;
		self::$taxonomies                = $taxonomies;
		// Clear BB's assets cache.
		if ( class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'delete_asset_cache_for_all_posts' ) ) {
			FLBuilderModel::delete_asset_cache_for_all_posts();
		}
	}

	/**
	 * Dynamically create hooks for each taxonomy for edit page.
	 *
	 * Adds hooks for each taxonomy that the user has selected
	 * via settings page. These hooks
	 * enable the image interface on wp-admin/edit-tags.php.
	 *
	 * @since 2.7.3
	 */
	static public function taxonomy_thumbnail_hooks() {
		if ( empty( self::$taxonomies ) ) {
			return;
		}
		$taxonomy_thumbnail_enable     = BB_PowerPack_Taxonomy_Thumbnail::$taxonomy_thumbnail_enable;
		$taxonomy_thumbnail_taxonomies = BB_PowerPack_Taxonomy_Thumbnail::$taxonomies;
		if ( 'enabled' === $taxonomy_thumbnail_enable ) {
			foreach ( self::$taxonomies as $taxonomy ) {
				add_filter( 'manage_' . $taxonomy . '_custom_column', __CLASS__ . '::taxonomy_thumbnail_taxonomy_rows', 15, 3 );
				add_filter( 'manage_edit-' . $taxonomy . '_columns', __CLASS__ . '::taxonomy_thumbnail_taxonomy_columns' );
				add_action( $taxonomy . '_edit_form_fields', __CLASS__ . '::taxonomy_thumbnail_edit_tag_form', 10, 2 );
				add_action( $taxonomy . '_add_form_fields', __CLASS__ . '::taxonomy_thumbnail_add_tag_form', 10 );
				add_action( 'edit_term', __CLASS__ . '::taxonomy_thumbnail_save_term', 10, 3 );
				add_action( 'create_term', __CLASS__ . '::taxonomy_thumbnail_save_term', 10, 3 );
			}
		}
	}

	/**
	 * Save Edited Term.
	 *
	 * @see taxonomy_thumbnail_hooks()
	 *
	 * @param array A list of columns.
	 * @return array List of columns with "Images" inserted after the checked.
	 * @since 2.7.3
	 */
	static public function taxonomy_thumbnail_save_term( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['taxonomy_thumbnail_id'] ) ) {
			update_term_meta( $term_id, 'taxonomy_thumbnail_id', sanitize_text_field( $_POST['taxonomy_thumbnail_id'] ) );
		}
	}


	/**
	 * Edit Term Columns.
	 *
	 * Insert a new column on wp-admin/edit-tags.php.
	 *
	 * @see taxonomy_thumbnail_hooks()
	 *
	 * @param array A list of columns.
	 * @return array List of columns with "Images" inserted after the checked.
	 * @since 2.7.3
	 */
	static public function taxonomy_thumbnail_taxonomy_columns( $original_columns ) {
		$new_columns = $original_columns;
		array_splice( $new_columns, 1 );
		$new_columns['taxonomy_thumbnail'] = esc_html__( 'Image', 'bb-powerpack' );
		return array_merge( $new_columns, $original_columns );
	}

	/**
	 * Edit Term Rows.
	 *
	 * Create image control for each term row of wp-admin/edit-tags.php.
	 *
	 * @see taxonomy_thumbnail_hooks()
	 *
	 * @param string    Row.
	 * @param string    Name of the current column.
	 * @param int   Term ID.
	 * @return    string    @see taxonomy_thumbnail_control_image()
	 * @since 2.7.3
	 */
	static public function taxonomy_thumbnail_taxonomy_rows( $row, $column_name, $term_id ) {
		if ( 'taxonomy_thumbnail' === $column_name ) {
			$html = '<div id="taxonomy_thumbnail_preview">';
			$taxonomy_thumbnail_id = '';
			$taxonomy_thumbnail_id = get_term_meta( $term_id, 'taxonomy_thumbnail_id', true );
			if ( '' !== $taxonomy_thumbnail_id ) {
				$obj_taxonomy_thumbnail = wp_get_attachment_image_src( $taxonomy_thumbnail_id, 'thumbnail' );
				if ( ! empty( $obj_taxonomy_thumbnail ) ) {
					$taxonomy_thumbnail_img_url = $obj_taxonomy_thumbnail[0];

					$html .= '<img id="taxonomy_thumbnail_preview_img" width="50" height="50" src="' . $taxonomy_thumbnail_img_url . '" >';
				}
			}
			$html .= '</div>';
			return $row . $html;
		}
		return $row;
	}

	/**
	 * Edit Term Control.
	 *
	 * Create image control for wp-admin/edit-tag-form.php.
	 * Hooked into the $taxonomy. '_edit_form_fields' action.
	 *
	 * @param stdClass  Term object.
	 * @param string    Taxonomy slug.
	 * @since 2.7.3
	 */
	static public function taxonomy_thumbnail_edit_tag_form( $term, $taxonomy ) {
		$taxonomy = get_taxonomy( $taxonomy );
		$name     = __( 'term', 'bb-powerpack' );
		if ( isset( $taxonomy->labels->singular_name ) )
			$name = strtolower( $taxonomy->labels->singular_name );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php print esc_html__( 'Featured Image', 'bb-powerpack' ); ?></label></th>
			<td>
				<div id="taxonomy_thumbnail_preview">
				<?php
				$taxonomy_thumbnail_id = '';
				$taxonomy_thumbnail_id = get_term_meta( $term->term_id, 'taxonomy_thumbnail_id', true );
				if ( '' !== $taxonomy_thumbnail_id ) {
					$obj_taxonomy_thumbnail = wp_get_attachment_image_src( $taxonomy_thumbnail_id, 'thumbnail' );
					if ( ! empty( $obj_taxonomy_thumbnail ) ) {
						$taxonomy_thumbnail_img_url = $obj_taxonomy_thumbnail[0];
						?>
						<img id="taxonomy_thumbnail_preview_img" width="150" height="150" src="<?php echo $taxonomy_thumbnail_img_url; ?>" ><br>
						<?php
					}
				}
				?>
				</div>
				<input id="taxonomy_thumbnail_id" type="hidden" name="taxonomy_thumbnail_id" value="<?php echo $taxonomy_thumbnail_id; ?>" />
				<input id="upload_taxonomy_thumbnail_button" type="button" class="button button-primary" value="<?php echo esc_html__( 'Upload', 'bb-powerpack' ); ?>" />
				<?php
				$delete_button_inline_css = 'display:none';
				if ( '' !== $taxonomy_thumbnail_id ) {
					$delete_button_inline_css = '';
				}
				?>
				<input style="<?php echo $delete_button_inline_css; ?>" id="delete_taxonomy_thumbnail_button" type="button" class="button button-danger" value="<?php echo esc_html__( 'Delete', 'bb-powerpack' ); ?>" />
				<div class="clear"></div>
				<?php
				// translators: %1$s for label.
				?>
				<span class="description"><?php printf( esc_html__( 'Add an image from media library to this %1$s.', 'bb-powerpack' ), esc_html( $name ) ); ?></span>
			</td>
		</tr>
		<?php
	}

	static public function taxonomy_thumbnail_add_tag_form( $taxonomy ) {
		$taxonomy = get_taxonomy( $taxonomy );
		$name     = __( 'term', 'bb-powerpack' );
		if ( isset( $taxonomy->labels->singular_name ) ) {
			$name = strtolower( $taxonomy->labels->singular_name );
		}
		?>
		<div class="form-field term-thumbnail-wrap">
			<label for="description"><?php print esc_html__( 'Featured Image', 'bb-powerpack' ); ?></label>
			<div id="taxonomy_thumbnail_preview">
			</div>
			<input id="taxonomy_thumbnail_id" type="hidden" name="taxonomy_thumbnail_id" value="" />
			<input id="upload_taxonomy_thumbnail_button" type="button" class="button button-primary" value="<?php echo esc_html__( 'Upload', 'bb-powerpack' ); ?>" />
			<?php
				$delete_button_inline_css = 'display:none';
			?>
			<input style="<?php echo $delete_button_inline_css; ?>" id="delete_taxonomy_thumbnail_button" type="button" class="button button-danger" value="<?php echo esc_html__( 'Delete', 'bb-powerpack' ); ?>" />
			<div class="clear"></div>
			<?php
				//translators: %1$s for label.
			?>
			<span class="description"><?php printf( esc_html__( 'Add an image from media library to this %1$s.', 'bb-powerpack' ), esc_html( $name ) ); ?></span>
		</div>
		<?php
	}

	static public function add_field_connection() {
		FLPageData::add_archive_property( 'pp_term_thumbnail_url', array(
			'label'  => sprintf( __( 'Term Thumbnail (%s)', 'bb-powerpack' ), pp_get_admin_label() ),
			'group'  => 'archives',
			'type'   => 'photo',
			'getter' => __CLASS__ . '::get_thumbnail_image_data',
		) );

		$fields = apply_filters( 'pp_term_thumbnail_url_connection_fields', array(
			'size'        => array(
				'type'    => 'photo-sizes',
				'label'   => __( 'Size', 'bb-powerpack' ),
				'default' => 'thumbnail',
			),
			'default_img' => array(
				'type'  => 'photo',
				'label' => __( 'Default Image', 'bb-powerpack' ),
			),
		) );

		FLPageData::add_archive_property_settings_fields( 'pp_term_thumbnail_url', $fields );

		// Term Thumbnail.
		FLPageData::add_archive_property( 'pp_term_thumbnail', array(
			'label'  => __( 'Term Thumbnail', 'bb-powerpack' ),
			'group'  => 'powerpack',
			'type'   => 'html',
			'getter' => __CLASS__ . '::get_thumbnail_image_data',
		) );

		FLPageData::add_archive_property_settings_fields( 'pp_term_thumbnail', array(
			'size'        => array(
				'type'    => 'photo-sizes',
				'label'   => __( 'Size', 'bb-powerpack' ),
				'default' => 'thumbnail',
			),
			'default_img' => array(
				'type'  => 'photo',
				'label' => __( 'Default Image', 'bb-powerpack' ),
			),
			'display' => array(
				'type'   => 'select',
				'label'  => __( 'Display as', 'bb-powerpack' ),
				'default' => 'tag',
				'options' => array(
					'tag'  => __( 'Tag', 'bb-powerpack' ),
					'url'  => __( 'URL', 'bb-powerpack' ),
				),
			),
		) );

		// Term Title.
		FLPageData::add_archive_property( 'pp_term_title', array(
			'label'  => __( 'Term Title', 'bb-powerpack' ),
			'group'  => 'powerpack',
			'type'   => 'html',
			'getter' => __CLASS__ . '::get_term_title',
		) );

		// Term Description.
		FLPageData::add_archive_property( 'pp_term_desc', array(
			'label'  => __( 'Term Description', 'bb-powerpack' ),
			'group'  => 'powerpack',
			'type'   => 'html',
			'getter' => __CLASS__ . '::get_term_description',
		) );

		// Term URL.
		FLPageData::add_archive_property( 'pp_term_url', array(
			'label'  => __( 'Term URL', 'bb-powerpack' ),
			'group'  => 'powerpack',
			'type'   => 'html',
			'getter' => __CLASS__ . '::get_term_url',
		) );

		// Term Posts Count.
		FLPageData::add_archive_property( 'pp_term_posts_count', array(
			'label'  => __( 'Term Posts Count', 'bb-powerpack' ),
			'group'  => 'powerpack',
			'type'   => 'html',
			'getter' => __CLASS__ . '::get_term_posts_count',
		) );

		FLPageData::add_archive_property_settings_fields( 'pp_term_posts_count', array(
			'label_singular' => array(
				'type'    => 'text',
				'label'   => __( 'Label Singular', 'bb-powerpack' ),
				'default' => 'post',
			),
			'label_plural' => array(
				'type'    => 'text',
				'label'   => __( 'Label Singular', 'bb-powerpack' ),
				'default' => 'posts',
			),
		) );

		// Term Meta.
		FLPageData::add_archive_property( 'pp_term_meta', array(
			'label'  => __( 'Term Meta', 'bb-powerpack' ),
			'group'  => 'powerpack',
			'type'   => 'html',
			'getter' => __CLASS__ . '::get_term_meta',
		) );

		FLPageData::add_archive_property_settings_fields( 'pp_term_meta', array(
			'meta_key' => array(
				'type'    => 'text',
				'label'   => __( 'Meta Key', 'bb-powerpack' ),
				'default' => '',
			),
		) );
	}

	static public function get_thumbnail_image_data( $settings ) {
		$term     = null;
		$settings = apply_filters( 'pp_term_thumbnail_url_connection_settings', $settings );

		if ( isset( $GLOBALS['pp_category'] ) ) {
			$term = $GLOBALS['pp_category'];
		} elseif ( isset( $settings->term_id ) && ! empty( $settings->term_id ) ) {
			$term_id = $settings->term_id;
			$term    = get_term( $term_id );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && isset( $queried_object->term_id ) ) {
				$term = $queried_object;
			}
		}

		if ( empty( $term ) || is_wp_error( $term ) ) {
			return;
		}

		$term_id       = $term->term_id;
		$meta_key      = 'product_cat' === $term->taxonomy ? 'thumbnail_id' : 'taxonomy_thumbnail_id';
		$thumbnail_id  = get_term_meta( $term_id, $meta_key, true );
		$thumbnail_url = '';

		if ( empty( $thumbnail_id ) ) {
			if ( isset( $settings->default_img_src ) ) {
				$thumbnail_url = $settings->default_img_src;
			}
		} else {
			$image = wp_get_attachment_image_src( $thumbnail_id, $settings->size );
			$thumbnail_url = ! empty( $image ) ? $image[0] : '';
		}

		if ( isset( $settings->display ) && 'tag' === $settings->display ) {
			if ( empty( $thumbnail_id ) && ! empty( $thumbnail_url ) ) {
				return sprintf( '<img src="%s" alt="%s" />', $thumbnail_url, htmlspecialchars( $term->name ) );
			}
			return wp_get_attachment_image( $thumbnail_id, $settings->size );
		}
		if ( isset( $settings->display ) && 'url' === $settings->display ) {
			return $thumbnail_url;
		}

		return array(
			'id'  => $thumbnail_id,
			'url' => $thumbnail_url,
		);
	}

	static public function get_term_title( $settings = null ) {
		$term = null;

		if ( isset( $GLOBALS['pp_category'] ) ) {
			$term = $GLOBALS['pp_category'];
		} elseif ( is_object( $settings ) && isset( $settings->term_id ) ) {
			$term = get_term_by( 'id', $settings->term_id );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && isset( $queried_object->term_id ) ) {
				$term = $queried_object;
			}
		}

		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			return $term->name;
		}
	}

	static public function get_term_description( $settings = null ) {
		$term = null;

		if ( isset( $GLOBALS['pp_category'] ) ) {
			$term = $GLOBALS['pp_category'];
		} elseif ( is_object( $settings ) && isset( $settings->term_id ) ) {
			$term = get_term( $settings->term_id );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && isset( $queried_object->term_id ) ) {
				$term = $queried_object;
			}
		}

		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			return get_term_field( 'description', $term );
		}
	}

	static public function get_term_url( $settings = null ) {
		$term = null;

		if ( isset( $GLOBALS['pp_category'] ) ) {
			$term = $GLOBALS['pp_category'];
		} elseif ( is_object( $settings ) && isset( $settings->term_id ) ) {
			$term = get_term( $settings->term_id );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && isset( $queried_object->term_id ) ) {
				$term = $queried_object;
			}
		}

		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			return get_term_link( $term->term_id );
		}
	}

	static public function get_term_posts_count( $settings = null ) {
		$term = null;

		if ( isset( $GLOBALS['pp_category'] ) ) {
			$term = $GLOBALS['pp_category'];
		} elseif ( is_object( $settings ) && isset( $settings->term_id ) ) {
			$term = get_term( $settings->term_id );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && isset( $queried_object->term_id ) ) {
				$term = $queried_object;
			}
		}

		if ( empty( $term ) || is_wp_error( $term ) ) {
			return;
		}

		$label_singular = is_object( $settings ) && isset( $settings->label_singular ) ? $settings->label_singular : '';
		$label_plural   = is_object( $settings ) && isset( $settings->label_plural ) ? $settings->label_plural : '';
		$count_text     = 1 === $term->count ? $label_singular : $label_plural;

		return sprintf( '%d %s', $term->count, $count_text );
	}

	static public function get_term_meta( $settings ) {
		$term = null;

		if ( isset( $GLOBALS['pp_category'] ) ) {
			$term = $GLOBALS['pp_category'];
		} elseif ( is_object( $settings ) && isset( $settings->term_id ) ) {
			$term = get_term( $settings->term_id );
		} else {
			$queried_object = get_queried_object();
			if ( is_object( $queried_object ) && isset( $queried_object->term_id ) ) {
				$term = $queried_object;
			}
		}

		if ( empty( $term ) || is_wp_error( $term ) ) {
			return;
		}

		return get_term_meta( $term->term_id, $settings->meta_key, true );
	}
}

// Initialize the class.
BB_PowerPack_Taxonomy_Thumbnail::init();
