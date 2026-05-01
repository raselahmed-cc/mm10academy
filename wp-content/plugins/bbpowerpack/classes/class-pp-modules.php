<?php
/**
 * Modules Manager.
 */
final class BB_PowerPack_Modules {
	protected static $_modules = array();
	protected static $_categorized_modules = array();
	protected static $_bb_enabled_modules = array();
	protected static $_enabled_modules = null;
	protected static $_enabled_categories = null;
	protected static $_is_admin = false;
	protected static $global_styles = array();

	public static function init() {
		self::$_is_admin = is_admin();

		if ( ! is_array( self::$_enabled_modules ) ) {
			self::$_enabled_modules = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_modules', true );
		}

		if ( ! is_array( self::$_enabled_categories ) ) {
			self::$_enabled_categories = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_module_categories', true );
		}

		if ( isset( $_GET['fl_builder'] ) ) {
			add_filter( 'fl_builder_enable_small_data_mode', '__return_false', 13 );
		}
		add_filter( 'fl_builder_enabled_modules',     __CLASS__ . '::filter_enabled_modules' );
		add_action( 'fl_builder_admin_settings_save', __CLASS__ . '::on_bb_settings_save' );
		add_filter( 'fl_module_dependencies',         __CLASS__ . '::module_dependencies' );
		add_action( 'admin_footer',                   __CLASS__ . '::admin_footer' );
	}

	public static function is_bb_module_settings_page() {
		return self::$_is_admin && isset( $_GET['page'] ) && 'fl-builder-settings' === $_GET['page'];
	}

	public static function register_module( $class, $form ) {
		$instance = new $class();
		
		self::$_modules[ $instance->slug ] = array(
			'name'			=> $instance->name,
			'slug'			=> $instance->slug,
			'description' 	=> $instance->description,
			'category'		=> $instance->category,
			'dir'			=> BB_POWERPACK_DIR . 'modules/' . $instance->slug . '/',
			'enabled'		=> $instance->enabled,
		);

		self::set_global_styles_fields( $instance, $form );

		if ( self::is_bb_module_settings_page() ) {
			FLBuilder::register_module( $class, $form );
			return;
		}

		$load_modules_in_admin = apply_filters( 'pp_load_modules_in_admin', true );
		
		if ( ! $load_modules_in_admin && self::$_is_admin ) {
			return;
		}

		if ( is_array( self::$_enabled_modules ) && ! in_array( $instance->slug, self::$_enabled_modules ) ) {
			return;
		}
		if ( ! $instance->enabled ) {
			return;
		}

		FLBuilder::register_module( $class, $form );
	}

	public static function set_global_styles_fields( $instance, $form ) {
		foreach ( $form as $tab_key => $tab ) {
			if ( ! isset( $tab['sections'] ) ) {
				continue;
			}
			foreach ( $tab['sections'] as $section_key => $section ) {
				if ( ! isset( $section['fields'] ) ) {
					continue;
				}
				foreach ( $section['fields'] as $field_key => $field ) {
					if ( isset( $field['is_global'] ) && $field['is_global'] ) {
						if ( ! isset( self::$global_styles[ $instance->slug ] ) ) {
							self::$global_styles[ $instance->slug ] = array(
								'name'   => '',
								'fields' => array()
							);
						}
						self::$global_styles[ $instance->slug ]['name']   = $instance->name;
						self::$global_styles[ $instance->slug ]['fields'][ $field_key ] = $field;
					}
				}
			}
		}
	}

	public static function get_global_styles_fields() {
		return self::$global_styles;
	}

	public static function get_modules() {
		uasort( self::$_modules, function( $a, $b ) {
			return strcmp( $a['name'], $b['name']) ;
		} );

		return self::$_modules;
	}

	public static function get_categorized_modules() {
		if ( ! empty( self::$_categorized_modules ) ) {
			return self::$_categorized_modules;
		}

		$modules = self::get_modules();
		$categorized_modules = array();

		foreach ( $modules as $slug => $module ) {
			$category = sanitize_title( $module['category'] );
			$categorized_modules[ $category ]['category'] = $module['category'];
			$categorized_modules[ $category ]['modules'][ $slug ] = $module;
		}

		ksort( $categorized_modules );

		self::$_categorized_modules = $categorized_modules;

		return $categorized_modules;
	}

	public static function get_enabled_modules() {
		if ( ! is_array( self::$_enabled_modules ) ) {
			$enabled_modules = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_modules', true );
			
			if ( ! is_array( $enabled_modules ) ) {
				self::$_enabled_modules = array_keys( self::get_modules() );
			} else {
				self::$_enabled_modules = $enabled_modules;
			}
		}

		return self::$_enabled_modules;
	}

	public static function get_enabled_categories() {
		if ( ! is_array( self::$_enabled_categories ) ) {
			return array_keys( self::get_categorized_modules() );
		}

		return self::$_enabled_categories;
	}

	public static function get_used_modules() {
		$used_modules = array();

		$args = array(
			'post_type'      => FLBuilderModel::get_post_types(),
			'post_status'    => 'publish',
			'meta_key'       => '_fl_builder_enabled',
			'meta_value'     => '1',
			'posts_per_page' => -1,
		);

		$query           = new WP_Query( $args );
		$data['enabled'] = count( $query->posts );

		/**
		* Using the array of pages/posts using builder get a list of all used modules
		*/
		if ( is_array( $query->posts ) && ! empty( $query->posts ) ) {
			foreach ( $query->posts as $post ) {
				$meta = get_post_meta( $post->ID, '_fl_builder_data', true );
				foreach ( (array) $meta as $node_id => $node ) {
					if ( @isset( $node->type ) && 'module' === $node->type ) { // @codingStandardsIgnoreLine
						if ( ! isset( self::$_modules[ $node->settings->type ] ) ) {
							continue;
						}

						if ( ! isset( $used_modules[ $node->settings->type ][ $post->post_type ] ) ) {
							$used_modules[ $node->settings->type ][ $post->post_type ] = array();
						}

						if ( ! isset( $used_modules[ $node->settings->type ][ $post->post_type ][ $post->ID ] ) ) {
							$used_modules[ $node->settings->type ][ $post->post_type ][ $post->ID ] = 1;
						} else {
							$used_modules[ $node->settings->type ][ $post->post_type ][ $post->ID ] ++;
						}


						if ( ! isset( $used_modules[ $node->settings->type ][ $post->post_type ]['total'] ) ) {
							$used_modules[ $node->settings->type ][ $post->post_type ]['total'] = 1;
						} else {
							$used_modules[ $node->settings->type ][ $post->post_type ]['total'] ++;
						}
					}
				}
			}
		}

		return $used_modules;
	}

	public static function filter_enabled_modules( $modules ) {
		if ( ! self::is_bb_module_settings_page() ) {
			return $modules;
		}

		$enabled_modules = self::get_enabled_modules();
		$all_modules = array_keys( self::get_modules() );
		$union = array_merge( $enabled_modules, $all_modules );
		$intersect = array_intersect( $enabled_modules, $all_modules );
		$disabled_modules = array_diff( $union, $intersect );

		foreach ( $enabled_modules as $module ) {
			$key = array_search( $module, $modules );
			if ( false === $key ) {
				$modules[] = $module;
			}
		}

		if ( ! empty( $disabled_modules ) ) {
			foreach ( $disabled_modules as $module ) {
				$key = array_search( $module, $modules );
				if ( false !== $key ) {
					unset( $modules[ $key ] );
				}
			}
		} else {
			foreach ( $enabled_modules as $module ) {
				$key = array_search( $module, $modules );
				if ( false === $key ) {
					$modules[] = $module;
				}
			}
		}

		return $modules;
	}

	public static function on_bb_settings_save() {
		$modules = array();

		if ( isset( $_POST['fl-modules'] ) && is_array( $_POST['fl-modules'] ) ) {
			$modules = array_map( 'sanitize_text_field', $_POST['fl-modules'] );
		}

		if ( ! empty( $modules ) ) {
			self::$_bb_enabled_modules = $modules;

			$pp_modules = array_filter( $modules, function( $module ) {
				return isset( self::$_modules[ $module ] );
			} );

			if ( ! is_array( $pp_modules ) ) {
				$pp_modules = array();
			}
			
			BB_PowerPack_Admin_Settings::update_option( 'bb_powerpack_modules', $pp_modules, false );
			self::$_enabled_modules = $pp_modules;
		}
	}

	public static function module_dependencies( $deps ) {
		$deps['pp-advanced-menu'] = array(
			'pp-search-form'
		);

		$deps['pp-video-gallery'] = array(
			'pp-video'
		);

		$deps['pp-content-grid'] = array(
			'photo'
		);

		$deps['pp-file-download'] = array(
			'pp-smart-button'
		);

		return $deps;
	}

	public static function admin_footer() {
		if ( is_admin() && isset( $_GET['page'] ) && 'fl-builder-settings' === $_GET['page'] ) {
			$admin_label = pp_get_admin_label();
			?>
			<script>
				;(function($) {
					window.pp_modules = <?php echo json_encode( array_keys( self::$_modules ) ); ?>;
					$('input.fl-module-cb').each(function() {
						if ( $.inArray( $(this).val(), pp_modules ) !== -1 ) {
							$(this).parent().attr( 'title', '<?php echo sprintf( esc_html__( 'You can manage this module in %s settings.', 'bb-powerpack' ), $admin_label ); ?>' );
						}
					});
				})(jQuery);
			</script>
			<?php
		}
	}

	public static function get_module_dependency( $slug = false ) {
		$deps = array(
			'pp-video'	=> array(
				'pp-video-gallery'
			),
			'pp-smart-button' => array(
				'pp-file-download'
			),
			'pp-advanced-menu' => array(
				'pp-search-form'
			)
		);

		if ( empty( $slug ) ) {
			return $deps;
		}
		if ( isset( $deps[ $slug ] ) ) {
			$deps[ $slug ];
		}

		return $slug;
	}
}

BB_PowerPack_Modules::init();