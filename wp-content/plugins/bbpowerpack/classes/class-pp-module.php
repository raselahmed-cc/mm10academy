<?php
/**
 * Handles logic for modules.
 *
 * @package BB_PowerPack
 * @since 2.6.10
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PPModuleExtend.
 */
final class PPModuleExtend {
	private static $_faq_schema_rendered = false;

	/**
	 * @since 2.7.0
	 * @return void
	 */
	static public function init() {
		// Filters.
		if ( class_exists( 'FLThemeBuilderLoader' ) ) {
			add_filter( 'fl_builder_register_settings_form',   	    __CLASS__ . '::post_grid_settings', 10, 2 );
			add_filter( 'fl_builder_register_settings_form',   	    __CLASS__ . '::category_grid_settings', 10, 2 );
			add_filter( 'fl_builder_render_css',               	    __CLASS__ . '::grid_css', 10, 2 );
			add_filter( 'pp_cg_module_layout_path', 			    __CLASS__ . '::post_grid_layout_path', 10, 3 );
			add_filter( 'pp_category_grid_layout_path', 		    __CLASS__ . '::category_grid_layout_path', 10, 3 );
			add_filter( 'pp_post_custom_layout_html', 			    __CLASS__ . '::custom_html_parse_shortcodes', 1 );
			add_filter( 'pp_category_custom_layout_html', 		    __CLASS__ . '::custom_html_parse_shortcodes', 1 );

			add_action( 'pp_post_custom_layout_before_content',     __CLASS__ . '::post_custom_layout_before_content' );
			add_action( 'pp_post_custom_layout_after_content',      __CLASS__ . '::post_custom_layout_after_content' );
			add_action( 'pp_category_custom_layout_before_content', __CLASS__ . '::category_custom_layout_before_content' );
			add_action( 'pp_category_custom_layout_after_content',  __CLASS__ . '::category_custom_layout_after_content' );
		}

		if ( function_exists( 'pods_beaver_loop_settings_before_form' ) ) {
			add_action( 'fl_page_data_add_properties', function() {
				add_action( 'pp_module_after_ui_setting_fields', 'pods_beaver_loop_settings_before_form', 10, 1 );
			}, 10 );
		}

		add_action( 'pp_cg_before_posts', __CLASS__ . '::posts_grid_before_posts', 10, 2 );
		add_action( 'pp_cg_after_posts', __CLASS__ . '::posts_grid_after_posts', 10, 2 );

		add_action( 'wp_head', __CLASS__ . '::render_faq_schema' );
		add_action( 'wp_footer', __CLASS__ . '::force_render_faq_schema' );
	}

	static public function get_faq_schema( $items, $settings ) {
		// @codingStandardsIgnoreStart.
		$schema_data = array(
			"@context" => "https://schema.org",
			"@type" => "FAQPage",
			"mainEntity" => array(),
		);

		if ( empty( $items ) ) {
			return;
		}

		for ( $i = 0; $i < count( $items ); $i++ ) {
			if ( ! is_object( $items[ $i ] ) ) {
				continue;
			}

			$item = (object) array(
				"@type" => "Question",
				"name" => $items[ $i ]->question,
				"acceptedAnswer" => (object) array(
					"@type" => "Answer",
					"text" => $items[ $i ]->answer,
				),
			);

			$schema_data['mainEntity'][] = $item;
		}
		// @codingStandardsIgnoreEnd.

		$schema_data = apply_filters( 'pp_faq_schema_markup', $schema_data, $settings );

		global $pp_faq_schema_items;

		if ( ! is_array( $pp_faq_schema_items ) ) {
			$pp_faq_schema_items = array();
		}

		$pp_faq_schema_items[] = $schema_data['mainEntity'];

		return $schema_data;
	}

	static public function render_faq_schema( $force = false ) {
		if ( ! is_callable( 'FLBuilderModel::get_nodes' ) ) {
			return;
		}

		// @codingStandardsIgnoreStart.
		$schema_data = array(
			"@context" => "https://schema.org",
			"@type" => "FAQPage",
			"mainEntity" => array(),
		);
		// @codingStandardsIgnoreEnd.

		if ( ! $force ) {
			$nodes = FLBuilderModel::get_nodes();
			$modules = array();
			$lookup = array( 'pp-faq', 'pp-advanced-accordion' );
			$schema = false;

			foreach ( $nodes as $node ) {
				if ( ! is_object( $node ) ) {
					continue;
				}

				if ( 'module' == $node->type ) {
					if ( in_array( $node->settings->type, $lookup ) ) {
						$modules[] = $node;
					}
				}

				if ( 'module' != $node->type && isset( $node->template_id ) ) {
					$template_id = $node->template_id;
					$template_node_id = $node->template_node_id;
					$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
					$data     = FLBuilderModel::get_layout_data( 'published', $post_id );

					foreach ( $data as $global_node ) {
						if ( 'module' == $global_node->type && in_array( $global_node->settings->type, $lookup ) ) {
							$modules[] = $global_node;
						}
					}
				}
			} // End foreach().

			if ( empty( $modules ) ) {
				return;
			}

			foreach ( $modules as $node ) {
				$settings = $node->settings;

				// if ( isset( $settings->enable_schema ) && 'no' == $settings->enable_schema ) {
				// 	self::$_faq_schema_rendered = true;
				// 	continue;
				// }

				if ( ! is_callable( 'FLBuilderModel::get_module' ) ) {
					continue;
				}

				$module = FLBuilderModel::get_module( $node );

				if ( ! is_object( $module ) ) {
					continue;
				}

				$items = array();

				if ( method_exists( $module, 'get_schema_items' ) ) {
					$items = $module->get_schema_items();
				}

				if ( ! is_array( $items ) || empty( $items ) ) {
					continue;
				}

				for ( $i = 0; $i < count( $items ); $i++ ) {
					if ( ! is_object( $items[ $i ] ) ) {
						continue;
					}

					// @codingStandardsIgnoreStart.
					$item = (object) array(
						"@type" => "Question",
						"name" => $items[ $i ]->question,
						"acceptedAnswer" => (object) array(
							"@type" => "Answer",
							"text" => $items[ $i ]->answer,
						),
					);
					// @codingStandardsIgnoreEnd.

					$schema_data['mainEntity'][] = $item;
				}
			} // End foreach().
		} else {
			global $pp_faq_schema_items;

			$schema_data['mainEntity'] = $pp_faq_schema_items;
		} // End if().

		if ( ! empty( $schema_data['mainEntity'] ) ) {
			$schema_data = apply_filters( 'pp_faq_schema_markup', $schema_data );
			echo '<script type="application/ld+json">';
			echo json_encode( $schema_data );
			echo '</script>';

			self::$_faq_schema_rendered = true;
		}
	}

	/**
	 * Renders FAQ schema when module is rendered through
	 * shortcode inside other module. 
	 *
	 * @return void
	 */
	static public function force_render_faq_schema() {
		/**
		 * Hook to determine whether the schema should render
		 * forcefully or not.
		 *
		 * @param bool
		 */
		if ( ! self::$_faq_schema_rendered || apply_filters( 'pp_faq_schema_force_render', false ) ) {
			self::render_faq_schema( true );
		}
	}

	/**
	 * Adds the custom code settings for custom post
	 * module layouts.
	 *
	 * @since 1.0
	 * @param array $form	Module setting form fields array.
	 * @param string $slug	Module slug.
	 * @return array
	 */
	static public function post_grid_settings( $form, $slug ) {
		if ( 'pp-content-grid' != $slug ) {
			return $form;
		}

		$form['layout']['sections']['general']['fields']['post_grid_style_select']['options']['custom'] = __( 'Custom', 'bb-powerpack' );
		$form['layout']['sections']['general']['fields']['post_grid_style_select']['toggle']['custom'] = array(
			'fields' => array( 'custom_layout' ),
		);

		$fields = $form['layout']['sections']['general']['fields'];
		$custom_layout = array(
			'type'          => 'form',
			'label'         => __( 'Custom Layout', 'bb-powerpack' ),
			'form'          => 'pp_post_custom_layout',
			'preview_text'  => null,
			'multiple'		=> false,
		);

		$position = array_search( 'match_height', array_keys( $fields ) );
		$fields = array_merge(
			array_slice( $fields, 0, $position ),
			array(
				'custom_layout' => $custom_layout,
			),
			array_slice( $fields, $position )
		);

		$form['layout']['sections']['general']['fields'] = $fields;

		FLBuilder::register_settings_form( 'pp_post_custom_layout', array(
			'title' => __( 'Customize Layout', 'bb-powerpack' ),
			'tabs'  => array(
				'html'          => array(
					'title'         => __( 'HTML', 'bb-powerpack' ),
					'sections'      => array(
						'html'          => array(
							'title'         => '',
							'fields'        => array(
								'html'          => array(
									'type'          => 'code',
									'editor'        => 'html',
									'label'         => '',
									'rows'          => '18',
									'default'       => self::get_preset_data( 'html' ),
									'preview'           => array(
										'type'              => 'none',
									),
									'connections'       => array( 'html', 'string', 'url' ),
								),
							),
						),
					),
				),
				'css'           => array(
					'title'         => __( 'CSS', 'bb-powerpack' ),
					'sections'      => array(
						'css'           => array(
							'title'         => '',
							'fields'        => array(
								'css'           => array(
									'type'          => 'code',
									'editor'        => 'css',
									'label'         => '',
									'rows'          => '18',
									'default'       => self::get_preset_data( 'css' ),
									'preview'           => array(
										'type'              => 'none',
									),
								),
							),
						),
					),
				),
			),
		));

		return $form;
	}

	static public function category_grid_settings( $form, $slug ) {
		if ( 'pp-category-grid' !== $slug ) {
			return $form;
		}

		$structure_fields = $form['structure']['sections']['structure']['fields'];

		$layout_fields = array(
			'layout' => array(
				'type'    => 'select',
				'label'   => __( 'Layout', 'bb-powerpack' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'bb-powerpack' ),
					'custom'  => __( 'Custom', 'bb-powerpack' ),
				),
				'toggle' => array(
					'default' => array(
						'sections' => array( 'content_setting' ),
						'fields' => array( 'category_title_tag' ),
					),
					'custom' => array(
						'fields' => array( 'custom_layout' ),
					),
				),
			),
			'custom_layout' => array(
				'type'          => 'form',
				'label'         => __( 'Custom Layout', 'bb-powerpack' ),
				'form'          => 'pp_category_custom_layout',
				'preview_text'  => null,
				'multiple'		=> false,
			),
		);
		
		$form['structure']['sections']['structure']['fields'] = array_merge( $layout_fields, $structure_fields );

		FLBuilder::register_settings_form( 'pp_category_custom_layout', array(
			'title' => __( 'Customize Layout', 'bb-powerpack' ),
			'tabs'  => array(
				'html'          => array(
					'title'         => __( 'HTML', 'bb-powerpack' ),
					'sections'      => array(
						'html'          => array(
							'title'         => '',
							'fields'        => array(
								'html'          => array(
									'type'          => 'code',
									'editor'        => 'html',
									'label'         => '',
									'rows'          => '18',
									'default'       => file_get_contents( BB_POWERPACK_DIR . 'includes/category-module-layout-html.php' ),
									'preview'       => array(
										'type'          => 'none',
									),
									'connections' => array( 'html', 'string', 'url' ),
								),
							),
						),
					),
				),
				'css' => array(
					'title' => __( 'CSS', 'bb-powerpack' ),
					'sections' => array(
						'css'           => array(
							'title'         => '',
							'fields'        => array(
								'css'       => array(
									'type'     => 'code',
									'editor'   => 'css',
									'label'    => '',
									'rows'     => '18',
									'default'  => file_get_contents( BB_POWERPACK_DIR . 'includes/category-module-layout-css.php' ),
									'preview'  => array(
										'type'  => 'none',
									),
								),
							),
						),
					),
				),
			),
		));

		return $form;
	}

	/**
	 * Get content from Post module's HTML and CSS files.
	 *
	 * @since 1.0
	 * @param string $type	html or css.
	 * @return string
	 */
	static public function get_preset_data( $type ) {
		if ( ! in_array( $type, array( 'html', 'css' ) ) ) {
			return;
		}

		$file = BB_POWERPACK_DIR . 'includes/post-module-layout-' . $type . '.php';

		if ( file_exists( $file ) ) {
			return file_get_contents( $file );
		}
	}

	/**
	 * Renders custom CSS for the post grid module.
	 *
	 * @since 1.0
	 * @param string $css
	 * @param array  $nodes
	 * @return string
	 */
	static public function grid_css( $css, $nodes ) {
		if ( isset( $_GET['fl_builder'], $_GET['safemode'] ) && is_user_logged_in() ) {
			return $css;
		}

		$valid_modules = array(
			'pp-content-grid',
			'pp-custom-grid',
			'pp-category-grid'
		);

		if ( ! class_exists( 'powerpack_lessc' ) ) {
			require_once BB_POWERPACK_DIR . 'classes/class-lessc.php';
		}

		foreach ( $nodes['modules'] as $module ) {

			if ( ! is_object( $module ) ) {
				continue;
			}

			if ( ! in_array( $module->settings->type, $valid_modules ) ) {
				continue;
			}

			$settings   = $module->settings;
			$module_css = '';

			if ( 'pp-content-grid' == $settings->type ) {
				if ( 'custom' != $settings->post_grid_style_select ) {
					continue;
				}

				$module_css = $settings->custom_layout->css;
				$module_css = is_object( $module_css ) && isset( $module_css->css ) ? $module_css->css : $module_css;
			}

			if ( 'pp-custom-grid' == $settings->type ) {
				if ( ! isset( $settings->preset ) || empty( $settings->preset ) ) {
					continue;
				}

				$preset = $settings->preset;
				$preset_form = $settings->{$preset . '_preset'};

				if ( ! isset( $preset_form->css ) ) {
					continue;
				}

				$module_css = $preset_form->css;
			}

			if ( 'pp-category-grid' == $settings->type ) {
				if ( ! isset( $settings->layout ) || 'custom' !== $settings->layout ) {
					continue;
				}

				$module_css = $settings->custom_layout->css;
				$module_css = is_object( $module_css ) && isset( $module_css->css ) ? $module_css->css : $module_css;
			}

			try {
				$less    = new powerpack_lessc;
				$custom  = '.fl-node-' . $module->node . ' { ';
				$custom .= $module_css;
				$custom .= ' }';
				if ( method_exists( 'FLBuilder', 'maybe_do_shortcode' ) ) {
					$custom = FLBuilder::maybe_do_shortcode( $custom );
				}
				$css .= @$less->compile( $custom ); // @codingStandardsIgnoreLine
			} catch ( Exception $e ) {
				@error_log( 'bb-powerpack: ' . $e ); // @codingStandardsIgnoreLine
				$css .= $module_css;
			}
		} // End foreach().

		unset( $valid_modules );

		return $css;
	}

	static public function post_grid_layout_path( $path, $layout, $settings ) {
		if ( 'custom' == $settings->post_grid_style_select ) {
			$path = BB_POWERPACK_DIR . 'includes/post-module-layout.php';
		}

		return $path;
	}

	static public function posts_grid_before_posts( $settings, $query ) {
		if ( is_callable( 'FLThemeBuilderFieldConnections::posts_grid_before_posts' ) ) {
			FLThemeBuilderFieldConnections::posts_grid_before_posts( $settings, $query );
		}
		// Adds WooCommerce result count and ordering.
		if ( is_callable( 'FLThemeBuilderWooCommerceArchive::posts_module_before_posts' ) ) {
			if ( ! isset( $settings->woo_ordering ) ) {
				$settings->woo_ordering = 'hide';
			}
			FLThemeBuilderWooCommerceArchive::posts_module_before_posts( $settings, $query );
		}

		// Render layout shortcode assets.
		if ( 'custom' == $settings->post_grid_style_select ) {
			$custom_layout = (object) $settings->custom_layout;
			$custom_layout_html = is_object( $custom_layout->html ) && isset( $custom_layout->html->html ) ? $custom_layout->html->html : $custom_layout->html;

			preg_match( '#(?<=fl_builder_insert_layout).*[id|slug]=[\'"]?([0-9a-z-]+)#', $custom_layout_html, $matches );

			if ( isset( $matches[1] ) && $matches[1] ) {
				pp_enqueue_layout_assets( $matches[1] );
			}
		}
		if ( 'custom' == $settings->show_content && ! empty( $settings->custom_content ) ) {
			preg_match( '#(?<=fl_builder_insert_layout).*[id|slug]=[\'"]?([0-9a-z-]+)#', $settings->custom_content, $matches );

			if ( isset( $matches[1] ) && $matches[1] ) {
				pp_enqueue_layout_assets( $matches[1] );
			}
		}
	}

	static public function posts_grid_after_posts( $settings, $query ) {
		if ( is_callable( 'FLThemeBuilderFieldConnections::posts_grid_after_posts' ) ) {
			FLThemeBuilderFieldConnections::posts_grid_after_posts( $settings, $query );
		}
	}

	static public function category_grid_layout_path( $path, $category, $settings ) {
		if ( 'custom' == $settings->layout ) {
			$path = BB_POWERPACK_DIR . 'includes/category-module-layout.php';
		}

		return $path;
	}

	static public function custom_html_parse_shortcodes( $content ) {
		return FLThemeBuilderFieldConnections::parse_shortcodes(
			$content,
			array(
				'wpbb-acf-flex',
				'wpbb-acf-repeater',
			)
		);
	}

	static public function post_custom_layout_before_content() {
		add_filter( 'wp_get_attachment_image_attributes', __CLASS__ . '::set_post_image_class', 10, 3 );
	}

	static public function post_custom_layout_after_content() {
		remove_filter( 'wp_get_attachment_image_attributes', __CLASS__ . '::set_post_image_class', 10, 3 );
	}

	static public function set_post_image_class( $attrs, $attachment, $size ) {
		$class = $attrs['class'];
		$attrs['class'] = $class . ' pp-post-img';

		return $attrs;
	}

	static public function category_custom_layout_before_content() {
		add_filter( 'wp_get_attachment_image_attributes', __CLASS__ . '::set_category_image_class', 10, 3 );
	}

	static public function category_custom_layout_after_content() {
		remove_filter( 'wp_get_attachment_image_attributes', __CLASS__ . '::set_category_image_class', 10, 3 );
	}

	static public function set_category_image_class( $attrs, $attachment, $size ) {
		$class = $attrs['class'];
		$attrs['class'] = $class . ' category-img';

		return $attrs;
	}
}

PPModuleExtend::init();
