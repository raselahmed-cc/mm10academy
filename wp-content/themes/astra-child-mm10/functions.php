<?php
/**
 * Astra Child Theme - MM10 Academy
 *
 * @package Astra-Child-MM10
 */

// Enqueue parent and child theme styles.
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style(
        'mm10-premium-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&family=Playfair+Display:wght@500;600;700&display=swap',
        array(),
        null
    );
    wp_enqueue_style( 'astra-child-style', get_stylesheet_uri(), array( 'astra-parent-style' ), wp_get_theme()->get( 'Version' ) );
}, 15 );

// =========================================================================
// PERFORMANCE: Remove unused CSS/JS on pages that don't need them
// =========================================================================
add_action( 'wp_enqueue_scripts', function() {

    // Remove block library CSS (not using Gutenberg blocks on frontend).
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );

    // Remove dashicons for non-logged-in users.
    if ( ! is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
    }

    // Remove SportsPress CSS/JS on pages that don't use it.
    if ( ! is_singular( array( 'sp_team', 'sp_player', 'sp_event', 'sp_calendar', 'sp_table', 'sp_list' ) )
         && ! is_post_type_archive( array( 'sp_team', 'sp_player', 'sp_event' ) ) ) {
        wp_dequeue_style( 'sportspress-general' );
        wp_dequeue_style( 'sportspress-icons' );
        wp_dequeue_style( 'sportspress-roboto' );
        wp_dequeue_style( 'sportspress-style' );
        wp_dequeue_style( 'sportspress-style-ltr' );
        wp_dequeue_script( 'sportspress' );
        wp_dequeue_script( 'jquery-datatables' );
    }

    // Remove Thrive Ovation CSS/JS on non-testimonials pages.
    // Remove Thrive Ovation CSS/JS on pages that don't use testimonials.
    // Keep on: homepage (front page has TVO shortcode in BB) + testimonials page.
    if ( ! is_front_page() && ! is_page( 'testimonials' ) ) {
        wp_dequeue_style( 'tve_style_family_tve_flt' );
        wp_dequeue_style( 'tvo-frontend' );
        wp_dequeue_style( 'tvo-display-testimonials' );
        wp_dequeue_script( 'tve_frontend' );
        wp_dequeue_script( 'tve-dash-frontend' );
        wp_dequeue_script( 'display-testimonials-tcb' );

        // Thrive registers dynamic handles — dequeue by pattern.
        global $wp_styles, $wp_scripts;
        if ( $wp_styles ) {
            foreach ( $wp_styles->queue as $h ) {
                if ( strpos( $h, 'thrlider' ) !== false || strpos( $h, 'tvo' ) !== false ) {
                    wp_dequeue_style( $h );
                }
            }
        }
        if ( $wp_scripts ) {
            foreach ( $wp_scripts->queue as $h ) {
                if ( strpos( $h, 'thrlider' ) !== false || strpos( $h, 'tvo' ) !== false ) {
                    wp_dequeue_script( $h );
                }
            }
        }
    }

    // Remove jQuery Masonry if not needed on this page.
    if ( ! is_home() && ! is_archive() ) {
        wp_dequeue_script( 'jquery-masonry' );
        wp_dequeue_script( 'masonry' );
    }

    // Remove jQuery UI autocomplete (rarely needed on frontend).
    wp_dequeue_script( 'jquery-ui-autocomplete' );
    wp_dequeue_script( 'jquery-ui-menu' );

}, 999 );

function mm10_get_beaver_builder_node_count( $post_id ) {
    $post_id = (int) $post_id;
    if ( $post_id <= 0 ) {
        return 0;
    }

    $data = get_post_meta( $post_id, '_fl_builder_data', true );

    return is_array( $data ) ? count( $data ) : 0;
}

function mm10_bb_seed_recursive_replace_urls( $value, $from, $to ) {
    if ( '' === $from || $from === $to ) {
        return $value;
    }

    if ( is_string( $value ) ) {
        return str_replace( $from, $to, $value );
    }

    if ( is_array( $value ) ) {
        foreach ( $value as $key => $item ) {
            $value[ $key ] = mm10_bb_seed_recursive_replace_urls( $item, $from, $to );
        }
        return $value;
    }

    if ( is_object( $value ) ) {
        foreach ( $value as $key => $item ) {
            $value->{$key} = mm10_bb_seed_recursive_replace_urls( $item, $from, $to );
        }
        return $value;
    }

    return $value;
}

function mm10_bb_seed_array_to_mixed_object_tree( $value ) {
    if ( is_object( $value ) ) {
        foreach ( $value as $key => $item ) {
            $value->{$key} = mm10_bb_seed_array_to_mixed_object_tree( $item );
        }
        return $value;
    }

    if ( ! is_array( $value ) ) {
        return $value;
    }

    $keys    = array_keys( $value );
    $is_list = ( $keys === range( 0, count( $value ) - 1 ) );

    if ( $is_list ) {
        foreach ( $value as $index => $item ) {
            $value[ $index ] = mm10_bb_seed_array_to_mixed_object_tree( $item );
        }
        return $value;
    }

    $object = new stdClass();
    foreach ( $value as $key => $item ) {
        $object->{$key} = mm10_bb_seed_array_to_mixed_object_tree( $item );
    }

    return $object;
}

function mm10_bb_seed_normalize_meta_value( $meta_key, $value ) {
    if ( '_fl_builder_enabled' === $meta_key ) {
        return (string) $value;
    }

    if ( '_fl_builder_data' === $meta_key || '_fl_builder_draft' === $meta_key ) {
        if ( ! is_array( $value ) ) {
            return $value;
        }
        // Shallow cast only: node becomes stdClass, node->settings becomes stdClass.
        // Fields INSIDE settings (connections, typography, data, etc.) stay as PHP arrays.
        foreach ( $value as $node_id => $node_value ) {
            if ( is_array( $node_value ) ) {
                $node_value = (object) $node_value;
            }
            if ( isset( $node_value->settings ) && is_array( $node_value->settings ) ) {
                $node_value->settings = (object) $node_value->settings;
            }
            $value[ $node_id ] = $node_value;
        }
        return $value;
    }

    if ( '_fl_builder_data_settings' === $meta_key ) {
        return mm10_bb_seed_array_to_mixed_object_tree( $value );
    }

    return $value;
}

function mm10_bb_seed_find_target_post( array $record ) {
    $source_id = isset( $record['ID'] ) ? (int) $record['ID'] : 0;
    if ( $source_id > 0 ) {
        $target_post = get_post( $source_id );
        if ( $target_post instanceof WP_Post ) {
            return $target_post;
        }
    }

    if ( empty( $record['post_name'] ) || empty( $record['post_type'] ) ) {
        return null;
    }

    $target_post = get_page_by_path( (string) $record['post_name'], OBJECT, (string) $record['post_type'] );

    return $target_post instanceof WP_Post ? $target_post : null;
}

function mm10_apply_beaver_seed_once() {
    $seed_version = '2026-05-03-v3';
    if ( $seed_version === (string) get_option( 'mm10_bb_seed_version', '' ) ) {
        return;
    }

    $seed_file = ABSPATH . 'ops/beaver-layouts-seed.json';
    if ( ! is_file( $seed_file ) || ! is_readable( $seed_file ) ) {
        return;
    }

    $payload = json_decode( (string) file_get_contents( $seed_file ), true );
    if ( ! is_array( $payload ) || empty( $payload['records'] ) || ! is_array( $payload['records'] ) ) {
        return;
    }

    $meta_keys = array(
        '_fl_builder_enabled',
        '_fl_builder_data',
        '_fl_builder_draft',
        '_fl_builder_data_settings',
    );

    $source_url = isset( $payload['site_url'] ) ? rtrim( (string) $payload['site_url'], '/' ) : '';
    $target_url = rtrim( home_url( '/' ), '/' );
    $updated    = 0;
    $matched    = 0;

    foreach ( $payload['records'] as $record ) {
        if ( ! is_array( $record ) ) {
            continue;
        }

        $target_post = mm10_bb_seed_find_target_post( $record );
        if ( ! ( $target_post instanceof WP_Post ) ) {
            continue;
        }

        $matched++;

        foreach ( $meta_keys as $meta_key ) {
            if ( ! array_key_exists( $meta_key, $record['meta'] ?? array() ) ) {
                continue;
            }

            $new_value = mm10_bb_seed_recursive_replace_urls( $record['meta'][ $meta_key ], $source_url, $target_url );
            $new_value = mm10_bb_seed_normalize_meta_value( $meta_key, $new_value );
            update_post_meta( $target_post->ID, $meta_key, $new_value );
            $updated++;
        }

        clean_post_cache( $target_post->ID );
    }

    if ( 0 === $matched || 0 === $updated ) {
        return;
    }

    if ( ! empty( $payload['show_on_front'] ) ) {
        update_option( 'show_on_front', (string) $payload['show_on_front'] );
    }

    if ( ! empty( $payload['page_on_front'] ) ) {
        $front_page_id = (int) $payload['page_on_front'];
        if ( $front_page_id > 0 ) {
            update_option( 'page_on_front', $front_page_id );
        }
    }

    if ( class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'delete_asset_cache_for_all_posts' ) ) {
        FLBuilderModel::delete_asset_cache_for_all_posts();
    }

    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }

    update_option( 'mm10_bb_seed_version', '2026-05-03-v3', false );
    update_option( 'mm10_bb_seed_applied_at', gmdate( 'c' ), false );
}

add_action( 'init', 'mm10_apply_beaver_seed_once', 4 );

function mm10_get_preferred_homepage_id() {
    $candidates = array();

    $home_page = get_page_by_path( 'home', OBJECT, 'page' );
    if ( $home_page instanceof WP_Post ) {
        $candidates[] = (int) $home_page->ID;
    }

    $homepage_page = get_page_by_path( 'homepage', OBJECT, 'page' );
    if ( $homepage_page instanceof WP_Post ) {
        $candidates[] = (int) $homepage_page->ID;
    }

    $candidates[] = 15;
    $candidates   = array_values( array_unique( array_filter( array_map( 'intval', $candidates ) ) ) );

    foreach ( $candidates as $candidate_id ) {
        $candidate = get_post( $candidate_id );
        if ( ! ( $candidate instanceof WP_Post ) || 'page' !== $candidate->post_type || 'publish' !== $candidate->post_status ) {
            continue;
        }

        if ( mm10_get_beaver_builder_node_count( $candidate_id ) > 1 ) {
            return $candidate_id;
        }
    }

    return 0;
}

add_action( 'init', function() {
    if ( 'page' !== get_option( 'show_on_front' ) ) {
        return;
    }

    $current_front_page_id = (int) get_option( 'page_on_front' );
    if ( $current_front_page_id > 0 && mm10_get_beaver_builder_node_count( $current_front_page_id ) > 1 ) {
        return;
    }

    $preferred_homepage_id = mm10_get_preferred_homepage_id();
    if ( $preferred_homepage_id <= 0 || $preferred_homepage_id === $current_front_page_id ) {
        return;
    }

    update_option( 'page_on_front', $preferred_homepage_id );

    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
}, 5 );

function mm10_is_beaver_builder_page() {
    if ( is_admin() || ! is_singular() ) {
        return false;
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id ) {
        return false;
    }

    return '1' === (string) get_post_meta( $post_id, '_fl_builder_enabled', true );
}

add_action( 'wp_enqueue_scripts', function() {
    if ( ! mm10_is_beaver_builder_page() ) {
        return;
    }

    foreach ( array( 'fl-builder-css', 'fl-slideshow', 'font-awesome-5', 'font-awesome' ) as $handle ) {
        if ( wp_style_is( $handle, 'registered' ) ) {
            wp_enqueue_style( $handle );
        }
    }
}, 1000 );

function mm10_page_has_sportspress_shortcode() {
    if ( ! is_singular() ) {
        return false;
    }

    $post = get_post();
    if ( ! $post || empty( $post->post_content ) ) {
        return false;
    }

    $tokens = array(
        'player_list',
        'player_details',
        'player_statistics',
        'event_list',
        'event_details',
        'league_table',
        'staff',
        'teams',
        'sp_',
    );

    foreach ( $tokens as $token ) {
        if ( false !== strpos( $post->post_content, '[' . $token ) ) {
            return true;
        }
    }

    return false;
}

function mm10_should_load_sportspress_ui() {
    if ( ! class_exists( 'SportsPress' ) ) {
        return false;
    }

    if ( is_singular( array( 'sp_team', 'sp_player', 'sp_event', 'sp_calendar', 'sp_table', 'sp_list', 'sp_staff' ) ) ) {
        return true;
    }

    if ( is_post_type_archive( array( 'sp_team', 'sp_player', 'sp_event', 'sp_staff' ) ) ) {
        return true;
    }

    if ( is_tax( array( 'sp_league', 'sp_season', 'sp_team', 'sp_position', 'sp_venue' ) ) ) {
        return true;
    }

    return mm10_page_has_sportspress_shortcode();
}

add_action( 'wp_enqueue_scripts', function() {
    if ( ! mm10_should_load_sportspress_ui() ) {
        return;
    }

    wp_enqueue_style(
        'mm10-sportspress-ui',
        get_stylesheet_directory_uri() . '/assets/css/mm10-sportspress-ui.css',
        array( 'astra-child-style' ),
        wp_get_theme()->get( 'Version' )
    );

    wp_enqueue_script(
        'mm10-sportspress-player-list',
        get_stylesheet_directory_uri() . '/assets/js/mm10-sportspress-player-list.js',
        array(),
        wp_get_theme()->get( 'Version' ),
        true
    );

    wp_localize_script(
        'mm10-sportspress-player-list',
        'mm10SportsPressList',
        array(
            'apiBase' => esc_url_raw( rest_url( 'wp/v2' ) ),
        )
    );
}, 1100 );

add_action( 'admin_enqueue_scripts', function() {
    if ( ! class_exists( 'SportsPress' ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }

    $is_sportspress_screen = false;

    $post_type = isset( $screen->post_type ) ? (string) $screen->post_type : '';
    if ( '' !== $post_type && 0 === strpos( $post_type, 'sp_' ) ) {
        $is_sportspress_screen = true;
    }

    $taxonomy = isset( $screen->taxonomy ) ? (string) $screen->taxonomy : '';
    if ( '' !== $taxonomy && 0 === strpos( $taxonomy, 'sp_' ) ) {
        $is_sportspress_screen = true;
    }

    $screen_id = isset( $screen->id ) ? (string) $screen->id : '';
    if ( false !== strpos( $screen_id, 'sportspress' ) ) {
        $is_sportspress_screen = true;
    }

    if ( ! $is_sportspress_screen ) {
        return;
    }

    wp_enqueue_style(
        'mm10-sportspress-admin-ui',
        get_stylesheet_directory_uri() . '/assets/css/mm10-sportspress-admin.css',
        array(),
        wp_get_theme()->get( 'Version' )
    );
}, 20 );

// Also dequeue Thrive scripts in footer (they enqueue late).
add_action( 'wp_footer', function() {
    if ( is_front_page() || is_page( 'testimonials' ) ) {
        return;
    }
    global $wp_scripts;
    if ( $wp_scripts ) {
        foreach ( $wp_scripts->queue as $h ) {
            if ( strpos( $h, 'thrlider' ) !== false || strpos( $h, 'tvo' ) !== false || strpos( $h, 'tve' ) !== false ) {
                wp_dequeue_script( $h );
            }
        }
    }
}, 1 );

// =========================================================================
// PERFORMANCE: Lazy-load images and add loading="lazy" to iframes
// =========================================================================
add_filter( 'the_content', function( $content ) {
    // Add loading="lazy" to iframes (YouTube embeds, etc.)
    $content = preg_replace(
        '/<iframe(?![^>]*loading=)([^>]*)>/i',
        '<iframe loading="lazy"$1>',
        $content
    );
    return $content;
}, 20 );

// =========================================================================
// PERFORMANCE: Preconnect to external resources
// =========================================================================
add_action( 'wp_head', function() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

// =========================================================================
// PERFORMANCE: Disable emoji scripts (saves ~10KB)
// =========================================================================
add_action( 'init', function() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
});

// =========================================================================
// PERFORMANCE: Remove unnecessary WP head items
// =========================================================================
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// =========================================================================
// PERFORMANCE: Disable self-pingbacks
// =========================================================================
add_action( 'pre_ping', function( &$links ) {
    $home = home_url();
    foreach ( $links as $l => $link ) {
        if ( strpos( $link, $home ) === 0 ) {
            unset( $links[ $l ] );
        }
    }
});

// =========================================================================
// WOOCOMMERCE: Theme Support + Core Configuration
// =========================================================================
add_action( 'after_setup_theme', function() {
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 600,
        'gallery_thumbnail_image_width' => 300,
        'single_image_width' => 800,
    ));
});

function mm10_env( $key, $default = '' ) {
    $value = getenv( $key );
    if ( false !== $value && '' !== $value ) {
        return (string) $value;
    }

    if ( isset( $_ENV[ $key ] ) && '' !== $_ENV[ $key ] ) {
        return (string) $_ENV[ $key ];
    }

    if ( isset( $_SERVER[ $key ] ) && '' !== $_SERVER[ $key ] ) {
        return (string) $_SERVER[ $key ];
    }

    return (string) $default;
}

function mm10_is_local_environment() {
    $home_url = home_url();
    $env      = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';

    return in_array( $env, array( 'local', 'development' ), true ) || false !== strpos( $home_url, '.local' );
}

// Production SMTP bootstrap for GoSMTP (env-driven, no secrets in code).
add_action( 'init', function() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( ! is_plugin_active( 'gosmtp/gosmtp.php' ) ) {
        return;
    }

    $smtp_host = mm10_env( 'MM10_SMTP_HOST' );
    $smtp_user = mm10_env( 'MM10_SMTP_USER' );
    $smtp_pass = mm10_env( 'MM10_SMTP_PASS' );

    // Don't overwrite plugin settings unless required env vars are provided.
    if ( empty( $smtp_host ) || empty( $smtp_user ) || empty( $smtp_pass ) ) {
        return;
    }

    $smtp_port       = mm10_env( 'MM10_SMTP_PORT', '587' );
    $smtp_encryption = strtolower( mm10_env( 'MM10_SMTP_ENCRYPTION', 'tls' ) );
    $from_email      = mm10_env( 'MM10_SMTP_FROM_EMAIL', $smtp_user );
    $from_name       = mm10_env( 'MM10_SMTP_FROM_NAME', get_bloginfo( 'name' ) );

    if ( ! in_array( $smtp_encryption, array( 'none', 'ssl', 'tls' ), true ) ) {
        $smtp_encryption = 'tls';
    }

    $options = get_option( 'gosmtp_options', array() );
    if ( ! is_array( $options ) ) {
        $options = array();
    }

    if ( empty( $options['mailer'] ) || ! is_array( $options['mailer'] ) ) {
        $options['mailer'] = array();
    }

    $options['mailer'][0] = array_merge(
        isset( $options['mailer'][0] ) && is_array( $options['mailer'][0] ) ? $options['mailer'][0] : array(),
        array(
            'mail_type'                  => 'smtp',
            'smtp_host'                  => $smtp_host,
            'smtp_port'                  => $smtp_port,
            'encryption'                 => $smtp_encryption,
            'smtp_auth'                  => 'Yes',
            'smtp_username'              => $smtp_user,
            'smtp_password'              => $smtp_pass,
            'disable_ssl_verification'   => '',
            'from_email'                 => $from_email,
            'from_name'                  => $from_name,
            'force_from_email'           => '1',
            'force_from_name'            => '1',
        )
    );

    // Keep force-from at root for compatibility with GoSMTP loader conn_id=0 behavior.
    $options['from_email']       = $from_email;
    $options['from_name']        = $from_name;
    $options['force_from_email'] = '1';
    $options['force_from_name']  = '1';

    $serialized = md5( wp_json_encode( $options ) );
    if ( get_option( 'mm10_gosmtp_config_hash', '' ) === $serialized ) {
        return;
    }

    update_option( 'gosmtp_options', $options, false );
    update_option( 'mm10_gosmtp_config_hash', $serialized, false );
}, 15 );

// Admin notice for production readiness when SMTP is not configured.
add_action( 'admin_notices', function() {
    if ( ! current_user_can( 'manage_options' ) || mm10_is_local_environment() ) {
        return;
    }

    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( ! is_plugin_active( 'gosmtp/gosmtp.php' ) ) {
        return;
    }

    $options      = get_option( 'gosmtp_options', array() );
    $manual_ready = ! empty( $options['mailer'][0]['mail_type'] ) && 'smtp' === $options['mailer'][0]['mail_type'] && ! empty( $options['mailer'][0]['smtp_host'] ) && ! empty( $options['mailer'][0]['smtp_username'] );
    $env_ready    = '' !== mm10_env( 'MM10_SMTP_HOST' ) && '' !== mm10_env( 'MM10_SMTP_USER' ) && '' !== mm10_env( 'MM10_SMTP_PASS' );

    if ( $manual_ready || $env_ready ) {
        return;
    }

    echo '<div class="notice notice-warning"><p><strong>MM10 SMTP:</strong> Production SMTP is not configured yet. Set GoSMTP manually or provide env vars MM10_SMTP_HOST, MM10_SMTP_USER, MM10_SMTP_PASS before go-live.</p></div>';
} );

// Optional one-time local admin bootstrap (env-driven only).
add_action( 'init', function() {
    if ( '1' === get_option( 'mm10_admin_seed_done', '0' ) ) {
        return;
    }

    if ( ! mm10_is_local_environment() ) {
        return;
    }

    if ( '1' !== mm10_env( 'MM10_LOCAL_ADMIN_SEED', '0' ) ) {
        return;
    }

    $username = mm10_env( 'MM10_LOCAL_ADMIN_USER' );
    $password = mm10_env( 'MM10_LOCAL_ADMIN_PASS' );
    $email    = mm10_env( 'MM10_LOCAL_ADMIN_EMAIL' );

    if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
        return;
    }

    $user = get_user_by( 'login', $username );

    if ( $user instanceof WP_User ) {
        wp_set_password( $password, $user->ID );
        if ( ! in_array( 'administrator', (array) $user->roles, true ) ) {
            $user->set_role( 'administrator' );
        }
        update_option( 'mm10_admin_seed_done', '1', false );
        return;
    }

    if ( email_exists( $email ) ) {
        $email = 'admin+' . time() . '@mm10academy.local';
    }

    $user_id = wp_create_user( $username, $password, $email );
    if ( is_wp_error( $user_id ) ) {
        return;
    }

    $new_user = new WP_User( $user_id );
    $new_user->set_role( 'administrator' );
    update_option( 'mm10_admin_seed_done', '1', false );
}, 5 );

// =========================================================================
// MOBILE MENU: Enterprise navigation (mobile only)
// Keeps desktop menu untouched and replaces crowded mobile menu with key links.
// =========================================================================
function mm10_get_enterprise_mobile_menu_items() {
    $account_url = wc_get_page_permalink( 'myaccount' );

    return array(
        array( 'label' => 'Home', 'url' => home_url( '/' ) ),
        array( 'label' => 'About Us', 'url' => home_url( '/about-us/' ) ),
        array( 'label' => 'Lessons', 'url' => get_permalink( wc_get_page_id( 'shop' ) ) ),
        array( 'label' => 'Spain Trip', 'url' => home_url( '/spain-trip/' ) ),
        array( 'label' => 'Testimonials', 'url' => home_url( '/testimonials/' ) ),
        array( 'label' => 'Blog', 'url' => home_url( '/blog/' ) ),
        array( 'label' => 'Contact Us', 'url' => home_url( '/contact/' ) ),
        array(
            'label' => 'My Account',
            'url'   => $account_url,
            'class' => 'mm10-mobile-cta menu-item-has-children',
            'children' => array(
                array( 'label' => 'Membership History', 'url' => wc_get_endpoint_url( 'membership-history', '', $account_url ) ),
                array( 'label' => 'Training Schedule', 'url' => wc_get_endpoint_url( 'schedule', '', $account_url ) ),
                array( 'label' => 'FAQ Center', 'url' => wc_get_endpoint_url( 'faq', '', $account_url ) ),
                array( 'label' => 'WhatsApp Support', 'url' => 'https://wa.me/60132061010?text=' . rawurlencode( 'Hi MM10 Academy, I need quick help.' ), 'class' => 'mm10-mobile-wa', 'new_tab' => true ),
            ),
        ),
    );
}

function mm10_get_auth_endpoint_url( $endpoint ) {
    return trailingslashit( wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( 'myaccount' ) ) );
}

function mm10_is_auth_request_path( $endpoint ) {
    $request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
    $request_path = trim( (string) $request_path, '/' );
    if ( '' === $request_path ) {
        return false;
    }

    $home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
    if ( '' !== $home_path && 0 === strpos( $request_path, $home_path . '/' ) ) {
        $request_path = substr( $request_path, strlen( $home_path ) + 1 );
    }

    $myaccount_path = trim( (string) wp_parse_url( wc_get_page_permalink( 'myaccount' ), PHP_URL_PATH ), '/' );
    if ( '' === $myaccount_path ) {
        return false;
    }

    return $request_path === trim( $myaccount_path . '/' . trim( $endpoint, '/' ), '/' );
}

function mm10_render_mobile_menu_items( $menu_items, $is_sub_menu = false ) {
    $output = '';

    foreach ( $menu_items as $item ) {
        if ( empty( $item['url'] ) || empty( $item['label'] ) ) {
            continue;
        }

        $li_class = 'menu-item';
        if ( ! empty( $item['class'] ) ) {
            $li_class .= ' ' . sanitize_html_class( $item['class'] );
        }
        if ( ! empty( $item['children'] ) ) {
            $li_class .= ' menu-item-has-children';
        }

        $target = ! empty( $item['new_tab'] ) ? ' target="_blank" rel="noopener"' : '';

        $output .= '<li class="' . esc_attr( trim( $li_class ) ) . '">';
        $output .= '<a class="menu-link" href="' . esc_url( $item['url'] ) . '"' . $target . '>' . esc_html( $item['label'] ) . '</a>';

        if ( ! empty( $item['children'] ) && is_array( $item['children'] ) ) {
            $output .= '<ul class="sub-menu">';
            $output .= mm10_render_mobile_menu_items( $item['children'], true );
            $output .= '</ul>';
        }

        $output .= '</li>';
    }

    return $output;
}

// Ensure Lessons exists as an actual Primary menu item (visible in Appearance > Menus).
add_action( 'init', function() {
    if ( '1' === get_option( 'mm10_lessons_menu_synced', '0' ) ) {
        return;
    }

    $locations = get_nav_menu_locations();
    if ( empty( $locations['primary'] ) ) {
        return;
    }

    $menu_id = (int) $locations['primary'];
    if ( $menu_id <= 0 ) {
        return;
    }

    $shop_id  = wc_get_page_id( 'shop' );
    $shop_url = get_permalink( $shop_id );
    if ( empty( $shop_url ) ) {
        return;
    }

    $existing_items = wp_get_nav_menu_items( $menu_id );
    if ( is_array( $existing_items ) ) {
        foreach ( $existing_items as $item ) {
            if ( ! empty( $item->title ) && 'lessons' === strtolower( trim( $item->title ) ) ) {
                update_option( 'mm10_lessons_menu_synced', '1', false );
                return;
            }
            if ( ! empty( $item->url ) && untrailingslashit( $item->url ) === untrailingslashit( $shop_url ) ) {
                update_option( 'mm10_lessons_menu_synced', '1', false );
                return;
            }
        }
    }

    wp_update_nav_menu_item( $menu_id, 0, array(
        'menu-item-title'  => 'Lessons',
        'menu-item-url'    => $shop_url,
        'menu-item-status' => 'publish',
        'menu-item-type'   => 'custom',
    ) );

    update_option( 'mm10_lessons_menu_synced', '1', false );
}, 20 );

// Sync Primary Menu to Mobile Menu on first setup (optional).
add_action( 'init', function() {
    if ( '1' === get_option( 'mm10_mobile_menu_synced', '0' ) ) {
        return;
    }

    $locations = get_nav_menu_locations();
    if ( empty( $locations['primary'] ) ) {
        return;
    }

    $primary_menu_id = (int) $locations['primary'];
    if ( $primary_menu_id <= 0 ) {
        return;
    }

    // Create a "Mobile Menu" if it doesn't exist.
    $mobile_menu = wp_get_nav_menu_object( 'Mobile Menu' );
    if ( ! $mobile_menu ) {
        $mobile_menu_id = wp_create_nav_menu( 'Mobile Menu' );
        if ( ! is_wp_error( $mobile_menu_id ) ) {
            // Copy all items from primary menu to mobile menu.
            $primary_items = wp_get_nav_menu_items( $primary_menu_id );
            if ( is_array( $primary_items ) && ! empty( $primary_items ) ) {
                foreach ( $primary_items as $item ) {
                    if ( 0 === (int) $item->menu_item_parent ) {
                        // Top-level items only; ignore children for now.
                        wp_update_nav_menu_item( $mobile_menu_id, 0, array(
                            'menu-item-title'  => $item->title,
                            'menu-item-url'    => $item->url,
                            'menu-item-status' => 'publish',
                            'menu-item-type'   => $item->type,
                            'menu-item-object-id' => $item->object_id,
                        ) );
                    }
                }
            }

            // Assign it to the mobile menu location.
            if ( ! empty( $locations ) ) {
                // Get all current menu assignments.
                $locations_array = array_map( 'intval', $locations );
                // Check if parent Astra theme supports 'mobile_menu' or 'ast-hf-mobile-menu' location.
                $registered_locations = get_registered_nav_menus();
                if ( ! empty( $registered_locations ) && ( isset( $registered_locations['mobile_menu'] ) || isset( $registered_locations['ast-hf-mobile-menu'] ) ) ) {
                    $mobile_location_key = isset( $registered_locations['mobile_menu'] ) ? 'mobile_menu' : 'ast-hf-mobile-menu';
                    $locations_array[ $mobile_location_key ] = $mobile_menu_id;
                    set_theme_mod( 'nav_menu_locations', $locations_array );
                }
            }
        }
    }

    update_option( 'mm10_mobile_menu_synced', '1', false );
}, 20 );

// Add featured desktop header actions for key conversion paths.
add_filter( 'wp_nav_menu_items', function( $items, $args ) {
    if ( is_admin() || wp_doing_ajax() ) {
        return $items;
    }

    $location = isset( $args->theme_location ) ? (string) $args->theme_location : '';
    $menu_id  = isset( $args->menu_id ) ? (string) $args->menu_id : '';

    // Only enrich desktop primary header navigation.
    if ( 'primary' !== $location || 'ast-hf-mobile-menu' === $menu_id ) {
        return $items;
    }

    $account_url  = wc_get_page_permalink( 'myaccount' );
    $whatsapp_url = 'https://wa.me/60132061010?text=' . rawurlencode( 'Hi MM10 Academy, I need quick support.' );

    $items .= '<li class="menu-item mm10-menu-featured mm10-menu-featured--account"><a class="menu-link" href="' . esc_url( $account_url ) . '">My Account</a></li>';
    $items .= '<li class="menu-item mm10-menu-featured mm10-menu-featured--wa"><a class="menu-link" href="' . esc_url( $whatsapp_url ) . '" target="_blank" rel="noopener">WhatsApp</a></li>';

    return $items;
}, 25, 2 );

// Remove default WooCommerce sidebar on shop pages.
add_action( 'wp', function() {
    if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
    }
});

// Force Astra no-sidebar + plain-container on shop/category pages.
// Uses Astra's final store-specific filters so our values stick.
add_filter( 'astra_get_store_sidebar_layout', function( $layout ) {
    if ( is_shop() || is_product_taxonomy() ) {
        return 'no-sidebar';
    }
    return $layout;
});
add_filter( 'astra_get_store_content_layout', function( $layout ) {
    if ( is_shop() || is_product_taxonomy() ) {
        return 'plain-container';
    }
    return $layout;
});

// =========================================================================
// WOOCOMMERCE: Redirect to checkout after adding virtual product to cart
// =========================================================================
add_filter( 'woocommerce_add_to_cart_redirect', function( $url ) {
    if ( ! isset( $_REQUEST['add-to-cart'] ) ) {
        return $url;
    }
    $product_id = absint( $_REQUEST['add-to-cart'] );
    $product    = wc_get_product( $product_id );
    if ( $product && $product->is_virtual() ) {
        return wc_get_checkout_url();
    }
    return $url;
});

// =========================================================================
// WOOCOMMERCE: Remove shipping fields (we collect player/parent info instead)
// =========================================================================
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_address_1'] );
    unset( $fields['billing']['billing_address_2'] );
    unset( $fields['billing']['billing_city'] );
    unset( $fields['billing']['billing_postcode'] );
    unset( $fields['billing']['billing_country'] );
    unset( $fields['billing']['billing_state'] );
    unset( $fields['order']['order_comments'] );
    return $fields;
});

// =========================================================================
// WOOCOMMERCE: Player & Parent Details — checkout fields for lessons
// =========================================================================
add_action( 'woocommerce_after_checkout_billing_form', function( $checkout ) {
    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return;
    }

    echo '<div id="mm10_player_details"><h3>' . esc_html__( 'Player Details', 'astra-child-mm10' ) . '</h3>';

    woocommerce_form_field( 'player_name', array(
        'type'     => 'text',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( 'Player Name', 'astra-child-mm10' ),
        'required' => true,
    ), $checkout->get_value( 'player_name' ) );

    woocommerce_form_field( 'player_age', array(
        'type'     => 'number',
        'class'    => array( 'form-row-first' ),
        'label'    => __( 'Age', 'astra-child-mm10' ),
        'required' => true,
        'custom_attributes' => array( 'min' => '3', 'max' => '18' ),
    ), $checkout->get_value( 'player_age' ) );

    woocommerce_form_field( 'player_gender', array(
        'type'     => 'select',
        'class'    => array( 'form-row-last' ),
        'label'    => __( 'Gender', 'astra-child-mm10' ),
        'required' => true,
        'options'  => array(
            '' => __( 'Select gender', 'astra-child-mm10' ),
            'male'   => __( 'Male', 'astra-child-mm10' ),
            'female' => __( 'Female', 'astra-child-mm10' ),
        ),
    ), $checkout->get_value( 'player_gender' ) );

    woocommerce_form_field( 'player_dob', array(
        'type'     => 'date',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( 'Date of birth', 'astra-child-mm10' ),
        'required' => true,
    ), $checkout->get_value( 'player_dob' ) );

    woocommerce_form_field( 'player_phone', array(
        'type'     => 'tel',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( 'Player Phone No.', 'astra-child-mm10' ),
        'required' => false,
    ), $checkout->get_value( 'player_phone' ) );

    woocommerce_form_field( 'player_medical', array(
        'type'     => 'textarea',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( 'Medical Condition', 'astra-child-mm10' ),
        'required' => false,
    ), $checkout->get_value( 'player_medical' ) );

    woocommerce_form_field( 'player_address', array(
        'type'     => 'textarea',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( 'Home address', 'astra-child-mm10' ),
        'required' => false,
    ), $checkout->get_value( 'player_address' ) );

    echo '</div>';

    echo '<div id="mm10_parent_details"><h3>' . esc_html__( "Parent's / Guardian's Details", 'astra-child-mm10' ) . '</h3>';

    woocommerce_form_field( 'parent_name', array(
        'type'     => 'text',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( "Parent's/Guardian's Name", 'astra-child-mm10' ),
        'required' => true,
    ), $checkout->get_value( 'parent_name' ) );

    woocommerce_form_field( 'parent_mobile', array(
        'type'     => 'tel',
        'class'    => array( 'form-row-wide' ),
        'label'    => __( "Parent's/Guardian's Mobile No", 'astra-child-mm10' ),
        'required' => true,
    ), $checkout->get_value( 'parent_mobile' ) );

    echo '</div>';
});

// Validate player & parent details at checkout.
add_action( 'woocommerce_checkout_process', function() {
    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return;
    }
    if ( empty( $_POST['player_name'] ) ) {
        wc_add_notice( __( 'Please enter the player name.', 'astra-child-mm10' ), 'error' );
    }
    if ( empty( $_POST['player_age'] ) ) {
        wc_add_notice( __( 'Please enter the player age.', 'astra-child-mm10' ), 'error' );
    }
    if ( empty( $_POST['player_gender'] ) ) {
        wc_add_notice( __( 'Please select the player gender.', 'astra-child-mm10' ), 'error' );
    }
    if ( empty( $_POST['player_dob'] ) ) {
        wc_add_notice( __( 'Please enter the date of birth.', 'astra-child-mm10' ), 'error' );
    }
    if ( empty( $_POST['parent_name'] ) ) {
        wc_add_notice( __( "Please enter the parent's / guardian's name.", 'astra-child-mm10' ), 'error' );
    }
    if ( empty( $_POST['parent_mobile'] ) ) {
        wc_add_notice( __( "Please enter the parent's / guardian's mobile number.", 'astra-child-mm10' ), 'error' );
    }
});

// Save player & parent details to order meta.
add_action( 'woocommerce_checkout_update_order_meta', function( $order_id ) {
    $text_fields = array( 'player_name', 'player_age', 'player_gender', 'player_dob', 'player_phone', 'parent_name', 'parent_mobile' );
    foreach ( $text_fields as $field ) {
        if ( ! empty( $_POST[ $field ] ) ) {
            update_post_meta( $order_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }
    $textarea_fields = array( 'player_medical', 'player_address' );
    foreach ( $textarea_fields as $field ) {
        if ( ! empty( $_POST[ $field ] ) ) {
            update_post_meta( $order_id, '_' . $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }
});

// Display player & parent details in WP Admin order screen.
add_action( 'woocommerce_admin_order_data_after_billing_address', function( $order ) {
    $fields = array(
        '_player_name'   => 'Player Name',
        '_player_age'    => 'Age',
        '_player_gender' => 'Gender',
        '_player_dob'    => 'Date of Birth',
        '_player_phone'  => 'Player Phone',
        '_player_medical' => 'Medical Info',
        '_player_address' => 'Home Address',
        '_parent_name'   => "Parent's Name",
        '_parent_mobile' => "Parent's Mobile",
    );
    $has_data = false;
    foreach ( $fields as $key => $label ) {
        $val = get_post_meta( $order->get_id(), $key, true );
        if ( $val ) {
            if ( ! $has_data ) {
                echo '<h3 style="margin-top:20px;">Player & Parent Details</h3>';
                $has_data = true;
            }
            echo '<p><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $val ) . '</p>';
        }
    }
});

// Include player & parent details in order emails.
add_filter( 'woocommerce_email_order_meta_fields', function( $fields, $sent_to_admin, $order ) {
    $meta = array(
        'player_name'   => 'Player Name',
        'player_age'    => 'Age',
        'player_gender' => 'Gender',
        'player_dob'    => 'Date of Birth',
        'parent_name'   => "Parent's Name",
        'parent_mobile' => "Parent's Mobile",
    );
    foreach ( $meta as $key => $label ) {
        $val = get_post_meta( $order->get_id(), '_' . $key, true );
        if ( $val ) {
            $fields[ $key ] = array( 'label' => $label, 'value' => $val );
        }
    }
    return $fields;
}, 10, 3 );

// =========================================================================
// WOOCOMMERCE: Custom Email Instructions for Offline (BACS) Payment
// =========================================================================
add_action( 'woocommerce_email_before_order_table', function( $order, $sent_to_admin, $plain_text, $email ) {
    if ( $email->id !== 'customer_on_hold_order' ) {
        return;
    }
    if ( $order->get_payment_method() !== 'bacs' ) {
        return;
    }

    if ( $plain_text ) {
        echo "\n\nHOW TO PAY\n";
        echo "1. Transfer the total amount to the bank details below.\n";
        echo '2. Use your Order Number (#' . esc_html( $order->get_order_number() ) . ") as the payment reference.\n";
        echo "3. Send a screenshot of your payment confirmation to payments@mm10academy.com\n";
        echo "4. Your membership will be activated within 24 hours of confirmation.\n\n";
    } else {
        echo '<div style="background:#f0f7ff;border-left:4px solid #007bff;padding:15px 20px;margin:20px 0;border-radius:4px;">';
        echo '<h3 style="margin-top:0;color:#007bff;">How to Complete Your Payment</h3>';
        echo '<ol style="margin:0;padding-left:20px;">';
        echo '<li>Transfer the total amount to the bank account shown below.</li>';
        echo '<li>Use your <strong>Order #' . esc_html( $order->get_order_number() ) . '</strong> as the payment reference.</li>';
        echo '<li>Send a screenshot of your payment to <a href="mailto:payments@mm10academy.com">payments@mm10academy.com</a></li>';
        echo '<li>Your membership will be activated within 24 hours of confirmation.</li>';
        echo '</ol></div>';
    }
}, 10, 4 );

// =========================================================================
// WOOCOMMERCE: Custom Thank You Page for BACS Orders
// =========================================================================
add_action( 'woocommerce_thankyou_bacs', function( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }
    ?>
    <section class="mm10-thankyou-spotlight" aria-label="Payment next steps">
        <h2 class="mm10-thankyou-spotlight__title">Thank You! You&apos;re Almost There</h2>
        <p class="mm10-thankyou-spotlight__lead">
            Your order <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong> has been received.
        </p>
        <div class="mm10-thankyou-spotlight__card">
            <h3 class="mm10-thankyou-spotlight__subtitle">Next Steps</h3>
            <ol class="mm10-thankyou-spotlight__steps">
                <li>Transfer <strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong> to our bank account</li>
                <li>Use reference: <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong></li>
                <li>Email your payment screenshot to <a href="mailto:payments@mm10academy.com">payments@mm10academy.com</a></li>
            </ol>
        </div>
        <p class="mm10-thankyou-spotlight__footnote">
            We'll confirm your membership within 24 hours of receiving payment.
            <br>Questions? Contact us at <a href="mailto:info@mm10academy.com">info@mm10academy.com</a>
        </p>
    </section>
    <?php
}, 20 );

// Global thank-you actions (all payment methods).
add_action( 'woocommerce_thankyou', function( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    echo '<div class="mm10-thankyou-actions" aria-label="Order actions">';
    echo '<a class="mm10-thankyou-btn mm10-thankyou-btn--home" href="' . esc_url( home_url( '/' ) ) . '">Back to Home</a>';

    if ( is_user_logged_in() ) {
        echo '<a class="mm10-thankyou-btn mm10-thankyou-btn--account" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">My Account</a>';
    }

    echo '</div>';
}, 35 );

// =========================================================================
// WOOCOMMERCE: Auto-cancel unpaid BACS orders after 14 days
// =========================================================================
add_filter( 'woocommerce_cancel_unpaid_order', '__return_true' );
add_filter( 'wc_order_is_pending_statuses', function( $statuses ) {
    $statuses[] = 'on-hold';
    return $statuses;
});

// =========================================================================
// MY ACCOUNT PORTAL — Premium Dashboard
// =========================================================================
add_action( 'woocommerce_account_dashboard', function() {
    $user       = wp_get_current_user();
    $first_name = $user->first_name ?: $user->display_name;
    $initial    = mb_strtoupper( mb_substr( $first_name, 0, 1 ) );
    $all_orders = wc_get_orders( array( 'customer_id' => $user->ID, 'limit' => -1 ) );
    $active     = wc_get_orders( array( 'customer_id' => $user->ID, 'status' => array( 'completed', 'processing' ), 'limit' => 1 ) );
    $has_active = ! empty( $active );
    $order_count = count( $all_orders );
    $member_since = date_i18n( 'M Y', strtotime( $user->user_registered ) );
    // Latest order summary.
    $latest = ! empty( $all_orders ) ? $all_orders[0] : null;
    $latest_program = $latest ? implode( ', ', array_map( fn($i) => $i->get_name(), $latest->get_items() ) ) : '';
    ?>
    <div class="mm10-dashboard-welcome">
        <div class="mm10-welcome-avatar"><?php echo esc_html( $initial ); ?></div>
        <div class="mm10-welcome-text">
            <h2>Welcome Back, <?php echo esc_html( $first_name ); ?>!</h2>
            <p>Member since <?php echo esc_html( $member_since ); ?> &nbsp;&bull;&nbsp; <?php echo esc_html( $order_count ); ?> order<?php echo $order_count !== 1 ? 's' : ''; ?></p>
            <?php if ( $has_active ) : ?>
                <span class="mm10-status-badge mm10-status-active">Membership Active</span>
            <?php else : ?>
                <span class="mm10-status-badge mm10-status-inactive">No Active Membership</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( $latest ) : ?>
    <div class="mm10-latest-order-bar">
        <span class="mm10-lob-label">Latest order:</span>
        <span class="mm10-lob-name"><?php echo esc_html( $latest_program ); ?></span>
        <mark class="order-status status-<?php echo esc_attr( $latest->get_status() ); ?>"><?php echo esc_html( wc_get_order_status_name( $latest->get_status() ) ); ?></mark>
        <a href="<?php echo esc_url( $latest->get_view_order_url() ); ?>" class="mm10-lob-link">View &rarr;</a>
    </div>
    <?php endif; ?>

    <div class="mm10-dashboard-grid">
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'membership-history' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-star-filled"></span></div>
            <h3>Membership History</h3>
            <p>All programs and enrollments</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-list-view"></span></div>
            <h3>Orders</h3>
            <p>Receipts and invoices</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'child-profile' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-groups"></span></div>
            <h3>Child Profile</h3>
            <p>Manage your child's details</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'attendance' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-yes-alt"></span></div>
            <h3>Attendance</h3>
            <p>Session attendance records</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'schedule' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-calendar-alt"></span></div>
            <h3>Schedule</h3>
            <p>Training sessions and fixtures</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'progress-reports' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-chart-bar"></span></div>
            <h3>Progress Reports</h3>
            <p>Skill assessments by term</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-admin-users"></span></div>
            <h3>Account Details</h3>
            <p>Update your information</p>
        </a>
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'support' ) ); ?>" class="mm10-dash-card">
            <div class="mm10-dash-card-icon"><span class="dashicons dashicons-email-alt"></span></div>
            <h3>Support</h3>
            <p>Get in touch with us</p>
        </a>
    </div>
    <?php
});

// Load dashicons on My Account page for dashboard cards.
add_action( 'wp_enqueue_scripts', function() {
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_script(
            'mm10-account-router',
            get_stylesheet_directory_uri() . '/assets/js/mm10-account-router.js',
            array(),
            wp_get_theme()->get( 'Version' ),
            true
        );
        wp_enqueue_script(
            'mm10-account-player-modal',
            get_stylesheet_directory_uri() . '/assets/js/mm10-account-player-modal.js',
            array(),
            wp_get_theme()->get( 'Version' ),
            true
        );
        wp_localize_script(
            'mm10-account-router',
            'mm10AccountRouter',
            array(
                'fragmentParam' => 'mm10_account_fragment',
                'nonceParam'    => 'mm10_account_fragment_nonce',
                'nonce'         => wp_create_nonce( 'mm10_account_fragment' ),
                'requestHeader' => 'X-MM10-Account-Fragment',
            )
        );
    }
}, 20 );

// Lightweight fragment response for My Account endpoint transitions.
add_action( 'template_redirect', function() {
    if ( ! is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    $fragment_param = isset( $_GET['mm10_account_fragment'] ) ? sanitize_text_field( wp_unslash( $_GET['mm10_account_fragment'] ) ) : '';
    $header_param   = isset( $_SERVER['HTTP_X_MM10_ACCOUNT_FRAGMENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_MM10_ACCOUNT_FRAGMENT'] ) ) : '';

    if ( '1' !== $fragment_param && '1' !== $header_param ) {
        return;
    }

    $nonce = isset( $_GET['mm10_account_fragment_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['mm10_account_fragment_nonce'] ) ) : '';
    if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'mm10_account_fragment' ) ) {
        status_header( 403 );
        nocache_headers();
        exit;
    }

    if ( ! function_exists( 'woocommerce_account_navigation' ) ) {
        status_header( 404 );
        nocache_headers();
        exit;
    }

    nocache_headers();
    header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );

    echo '<div id="mm10-account-fragment" data-title="' . esc_attr( wp_get_document_title() ) . '">';
    woocommerce_account_navigation();
    echo '<div class="woocommerce-MyAccount-content">';
    do_action( 'woocommerce_account_content' );
    echo '</div>';
    echo '</div>';
    exit;
}, 1 );

// Premium action bar for the single-order view inside My Account.
add_action( 'woocommerce_view_order', function( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $placed_on = $order->get_date_created() ? wc_format_datetime( $order->get_date_created(), 'j M Y' ) : '';
    $status    = wc_get_order_status_name( $order->get_status() );
    $total     = $order->get_formatted_order_total();
    ?>
    <div class="mm10-order-toolbar">
        <div class="mm10-order-toolbar__meta">
            <span class="mm10-order-toolbar__eyebrow">Order Summary</span>
            <div class="mm10-order-toolbar__title-row">
                <div>
                    <h2 class="mm10-order-toolbar__title">Order #<?php echo esc_html( $order->get_order_number() ); ?></h2>
                    <p class="mm10-order-toolbar__subtitle">MM10 Academy receipt and payment summary</p>
                </div>
                <div class="mm10-order-toolbar__brand print-only">MM10 Academy</div>
            </div>
            <div class="mm10-order-toolbar__chips">
                <?php if ( $placed_on ) : ?>
                    <span class="mm10-order-chip">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        Placed <?php echo esc_html( $placed_on ); ?>
                    </span>
                <?php endif; ?>
                <span class="mm10-order-chip">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php echo wp_kses_post( $total ); ?>
                </span>
                <mark class="order-status status-<?php echo esc_attr( $order->get_status() ); ?>"><?php echo esc_html( $status ); ?></mark>
            </div>
        </div>
        <button type="button" class="button mm10-order-print no-print" onclick="window.print(); return false;">
            <span class="dashicons dashicons-printer"></span>
            Print Order
        </button>
    </div>
    <?php
}, 5 );

// =========================================================================
// SPORTSPRESS ENTERPRISE INTEGRATION
// All via hooks/shortcodes — no template copies, update-safe.
// =========================================================================

// --- [mm10_latest_results] — last N results with scores ---
add_shortcode( 'mm10_latest_results', function( $atts ) {
    if ( ! class_exists( 'SportsPress' ) ) return '';
    $atts = shortcode_atts( array( 'limit' => 5 ), $atts );
    $events = get_posts( array(
        'post_type'      => 'sp_event',
        'posts_per_page' => intval( $atts['limit'] ),
        'meta_query'     => array( array( 'key' => 'sp_status', 'value' => 'results', 'compare' => '=' ) ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
    if ( empty( $events ) ) return '<p class="mm10-no-results">No results yet — season coming soon!</p>';
    $out = '<div class="mm10-results-list">';
    foreach ( $events as $e ) {
        $date  = get_the_date( 'j M Y', $e );
        $teams = get_post_meta( $e->ID, 'sp_team', true );
        $out  .= '<div class="mm10-result-item">';
        $out  .= '<span class="mm10-result-date">' . esc_html( $date ) . '</span>';
        $out  .= '<a href="' . esc_url( get_permalink( $e ) ) . '" class="mm10-result-title">' . esc_html( get_the_title( $e ) ) . '</a>';
        $out  .= '<span class="mm10-result-arrow">&#8594;</span>';
        $out  .= '</div>';
    }
    return $out . '</div>';
} );

// --- [mm10_fixtures] — upcoming events ---
add_shortcode( 'mm10_fixtures', function( $atts ) {
    if ( ! class_exists( 'SportsPress' ) ) return '';
    $atts   = shortcode_atts( array( 'limit' => 5, 'title' => 'Upcoming Fixtures' ), $atts );
    $events = get_posts( array(
        'post_type'      => 'sp_event',
        'posts_per_page' => intval( $atts['limit'] ),
        'meta_query'     => array( array( 'key' => 'sp_status', 'value' => 'fixture', 'compare' => '=' ) ),
        'orderby'        => 'date',
        'order'          => 'ASC',
        'date_query'     => array( array( 'after' => 'yesterday' ) ),
    ) );
    if ( empty( $events ) ) return '<div class="mm10-fixtures-empty"><p>No upcoming fixtures scheduled yet.</p></div>';
    $out = '<div class="mm10-fixtures-widget">';
    if ( $atts['title'] ) $out .= '<h3 class="mm10-widget-heading">' . esc_html( $atts['title'] ) . '</h3>';
    $out .= '<div class="mm10-fixture-list">';
    foreach ( $events as $e ) {
        $date  = get_the_date( 'D, j M Y', $e );
        $time  = get_the_date( 'H:i', $e );
        $venue = get_post_meta( $e->ID, 'sp_venue', true );
        $out  .= '<div class="mm10-fixture-row">';
        $out  .= '<div class="mm10-fixture-date"><span class="mm10-fix-day">' . esc_html( $date ) . '</span>';
        if ( $time ) $out .= '<span class="mm10-fix-time">' . esc_html( $time ) . '</span>';
        $out  .= '</div>';
        $out  .= '<a href="' . esc_url( get_permalink( $e ) ) . '" class="mm10-fixture-name">' . esc_html( get_the_title( $e ) ) . '</a>';
        $out  .= '</div>';
    }
    return $out . '</div></div>';
} );

// --- [mm10_standings] — league table ---
add_shortcode( 'mm10_standings', function( $atts ) {
    if ( ! class_exists( 'SportsPress' ) ) return '';
    $atts = shortcode_atts( array( 'league' => '', 'season' => '', 'title' => 'Standings' ), $atts );
    $tables = get_posts( array(
        'post_type'      => 'sp_table',
        'posts_per_page' => 1,
        'tax_query'      => array_filter( array(
            $atts['league'] ? array( 'taxonomy' => 'sp_league', 'field' => 'slug', 'terms' => $atts['league'] ) : null,
            $atts['season'] ? array( 'taxonomy' => 'sp_season', 'field' => 'slug', 'terms' => $atts['season'] ) : null,
        ) ),
    ) );
    if ( empty( $tables ) ) return '<p class="mm10-no-results">Standings coming soon.</p>';
    return '<div class="mm10-standings-wrap">'
        . ( $atts['title'] ? '<h3 class="mm10-widget-heading">' . esc_html( $atts['title'] ) . '</h3>' : '' )
        . do_shortcode( '[sp_league_table id="' . $tables[0]->ID . '"]' )
        . '</div>';
} );

// --- [mm10_teams] — age-group team cards ---
add_shortcode( 'mm10_teams', function( $atts ) {
    if ( ! class_exists( 'SportsPress' ) ) return '';
    $atts  = shortcode_atts( array( 'limit' => 12, 'title' => 'Our Teams' ), $atts );
    $teams = get_posts( array( 'post_type' => 'sp_team', 'posts_per_page' => intval( $atts['limit'] ), 'orderby' => 'title', 'order' => 'ASC' ) );
    if ( empty( $teams ) ) return '<p class="mm10-no-results">No teams found.</p>';
    $out  = '<div class="mm10-teams-wrap">';
    if ( $atts['title'] ) $out .= '<h3 class="mm10-widget-heading">' . esc_html( $atts['title'] ) . '</h3>';
    $out .= '<div class="mm10-teams-grid">';
    foreach ( $teams as $t ) {
        $thumb = has_post_thumbnail( $t->ID ) ? get_the_post_thumbnail_url( $t->ID, 'thumbnail' ) : '';
        $leagues = wp_get_post_terms( $t->ID, 'sp_league', array( 'fields' => 'names' ) );
        $league  = ! empty( $leagues ) ? $leagues[0] : '';
        $out .= '<a href="' . esc_url( get_permalink( $t ) ) . '" class="mm10-team-card">';
        if ( $thumb ) $out .= '<div class="mm10-team-badge"><img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $t->post_title ) . '"></div>';
        else $out .= '<div class="mm10-team-badge mm10-team-badge--no-img"><span class="dashicons dashicons-shield"></span></div>';
        $out .= '<div class="mm10-team-info"><h4>' . esc_html( $t->post_title ) . '</h4>';
        if ( $league ) $out .= '<span>' . esc_html( $league ) . '</span>';
        $out .= '</div></a>';
    }
    return $out . '</div></div>';
} );

// --- [mm10_players] — player roster card grid ---
add_shortcode( 'mm10_players', function( $atts ) {
    if ( ! class_exists( 'SportsPress' ) ) return '';
    $atts    = shortcode_atts( array( 'team' => '', 'limit' => 20, 'title' => 'Players' ), $atts );
    $args    = array(
        'post_type'      => 'sp_player',
        'posts_per_page' => intval( $atts['limit'] ),
        'meta_key'       => 'sp_number',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    );
    if ( $atts['team'] ) {
        $team_post = get_page_by_path( $atts['team'], OBJECT, 'sp_team' );
        if ( $team_post ) {
            $args['meta_query'] = array( array( 'key' => 'sp_team', 'value' => $team_post->ID, 'compare' => 'LIKE' ) );
        }
    }
    $players = get_posts( $args );
    if ( empty( $players ) ) return '<p class="mm10-no-results">No players found.</p>';
    $out = '<div class="mm10-players-wrap">';
    if ( $atts['title'] ) $out .= '<h3 class="mm10-widget-heading">' . esc_html( $atts['title'] ) . '</h3>';
    $out .= '<div class="mm10-players-grid">';
    foreach ( $players as $p ) {
        $thumb    = has_post_thumbnail( $p->ID ) ? get_the_post_thumbnail_url( $p->ID, 'medium' ) : '';
        $position_terms = wp_get_post_terms( $p->ID, 'sp_position', array( 'fields' => 'names' ) );
        $position = ! empty( $position_terms ) ? $position_terms[0] : get_post_meta( $p->ID, 'sp_position', true );
        $number   = get_post_meta( $p->ID, 'sp_number', true );
        $nationality_code = get_post_meta( $p->ID, 'sp_nationality', true );
        $nationalities = array(
            'irn' => 'Iran',
            'jpn' => 'Japan',
            'lva' => 'Latvia',
            'mys' => 'Malaysia',
            'sgp' => 'Singapore',
        );
        $nationality = isset( $nationalities[ $nationality_code ] ) ? $nationalities[ $nationality_code ] : '';
        $out .= '<a href="' . esc_url( get_permalink( $p ) ) . '" class="mm10-player-card">';
        $out .= '<div class="mm10-player-photo">';
        if ( $thumb ) $out .= '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $p->post_title ) . '">';
        else $out .= '<span class="dashicons dashicons-admin-users"></span>';
        if ( $number ) $out .= '<span class="mm10-player-number">' . esc_html( $number ) . '</span>';
        $out .= '</div>';
        $out .= '<div class="mm10-player-info"><h4>' . esc_html( $p->post_title ) . '</h4>';
        if ( $position ) $out .= '<span class="mm10-player-pos">' . esc_html( $position ) . '</span>';
        if ( $nationality ) $out .= '<span class="mm10-player-country">' . esc_html( $nationality ) . '</span>';
        $out .= '</div></a>';
    }
    return $out . '</div></div>';
} );

// --- [mm10_promise_players] — reusable Promise Group roster block ---
// Usage example: [mm10_promise_players team="mm10-academy" limit="5" title="" class="my-custom-class"]
add_shortcode( 'mm10_promise_players', function( $atts ) {
    $atts = shortcode_atts(
        array(
            'team'  => 'mm10-academy',
            'limit' => 5,
            'title' => '',
            'class' => '',
        ),
        $atts,
        'mm10_promise_players'
    );

    $classes = array( 'mm10-promise-sportspress-roster' );
    if ( ! empty( $atts['class'] ) ) {
        $extra_classes = preg_split( '/\s+/', trim( $atts['class'] ) );
        foreach ( $extra_classes as $extra_class ) {
            $safe_class = sanitize_html_class( $extra_class );
            if ( $safe_class ) {
                $classes[] = $safe_class;
            }
        }
    }

    $shortcode = sprintf(
        '[mm10_players team="%s" limit="%d" title="%s"]',
        esc_attr( $atts['team'] ),
        intval( $atts['limit'] ),
        esc_attr( $atts['title'] )
    );

    return '<div class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '">'
        . do_shortcode( $shortcode )
        . '</div>';
} );

// Fill the Promise Group homepage slot with SportsPress-backed player cards.
add_filter( 'the_content', function( $content ) {
    if ( is_admin() || ! ( is_front_page() || is_page( 'home' ) ) ) {
        return $content;
    }

    if ( false !== strpos( $content, 'mm10-promise-sportspress-roster' ) || false === strpos( $content, 'fyex0ts6zm8p' ) ) {
        return $content;
    }

    $players = do_shortcode( '[mm10_promise_players team="mm10-academy" limit="5" title=""]' );

    // Match the target module even if spacing or class ordering differs between environments.
    $pattern     = '/(<div\s+class="[^"]*\bfl-module\b[^"]*\bfl-module-box\b[^"]*\bfl-node-fyex0ts6zm8p\b[^"]*"\s+data-node="fyex0ts6zm8p">)/i';
    $replacement = '$1' . $players;
    $updated     = preg_replace( $pattern, $replacement, $content, 1, $count );

    if ( 1 !== (int) $count || null === $updated ) {
        return $content;
    }

    return $updated;
}, 12 );

// --- [mm10_coaches] — staff cards ---
add_shortcode( 'mm10_coaches', function( $atts ) {
    if ( ! class_exists( 'SportsPress' ) ) return '';
    $atts    = shortcode_atts( array( 'limit' => 10, 'title' => 'Coaching Staff' ), $atts );
    $coaches = get_posts( array( 'post_type' => 'sp_staff', 'posts_per_page' => intval( $atts['limit'] ), 'orderby' => 'menu_order', 'order' => 'ASC' ) );
    if ( empty( $coaches ) ) return '<p class="mm10-no-results">No coaches found.</p>';
    $out  = '<div class="mm10-coaches-wrap">';
    if ( $atts['title'] ) $out .= '<h3 class="mm10-widget-heading">' . esc_html( $atts['title'] ) . '</h3>';
    $out .= '<div class="mm10-coaches-grid">';
    foreach ( $coaches as $c ) {
        $thumb = has_post_thumbnail( $c->ID ) ? get_the_post_thumbnail_url( $c->ID, 'medium' ) : '';
        $role  = get_post_meta( $c->ID, 'sp_role', true );
        $out  .= '<div class="mm10-coach-card">';
        $out  .= '<div class="mm10-coach-photo">';
        if ( $thumb ) $out .= '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $c->post_title ) . '">';
        else $out .= '<span class="dashicons dashicons-businessperson"></span>';
        $out  .= '</div>';
        $out  .= '<div class="mm10-coach-info"><h4>' . esc_html( $c->post_title ) . '</h4>';
        if ( $role ) $out .= '<span class="mm10-coach-role">' . esc_html( $role ) . '</span>';
        $out  .= '<a href="' . esc_url( get_permalink( $c ) ) . '" class="mm10-coach-link">View Profile</a>';
        $out  .= '</div></div>';
    }
    return $out . '</div></div>';
} );

// =========================================================================
// MY ACCOUNT PORTAL — Custom Endpoints
// Update-safe: uses WC endpoint API + hooks only, no template overrides.
// =========================================================================

// 1. Register rewrite endpoints.
function mm10_get_account_endpoints() {
    return array(
        'signin',
        'signup',
        'membership-history',
        'child-profile',
        'attendance',
        'schedule',
        'progress-reports',
        'faq',
        'support',
    );
}

function mm10_register_account_endpoints() {
    foreach ( mm10_get_account_endpoints() as $endpoint ) {
        add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
    }
}
add_action( 'init', 'mm10_register_account_endpoints' );

// Ensure endpoints are available even when rewrite rules were not manually flushed.
function mm10_maybe_flush_account_endpoints() {
    $version      = '3';
    $stored_value = get_option( 'mm10_account_endpoints_flush_version', '' );

    if ( $stored_value === $version ) {
        return;
    }

    mm10_register_account_endpoints();
    flush_rewrite_rules( false );
    update_option( 'mm10_account_endpoints_flush_version', $version, false );
}
add_action( 'init', 'mm10_maybe_flush_account_endpoints', 20 );

// Force refresh on theme switch so rewrites are always valid.
add_action( 'after_switch_theme', function() {
    delete_option( 'mm10_account_endpoints_flush_version' );
    mm10_register_account_endpoints();
    flush_rewrite_rules( false );
    update_option( 'mm10_account_endpoints_flush_version', '3', false );
} );

// Split guest auth flow into clean pages: /my-account/signin/ and /my-account/signup/
add_action( 'template_redirect', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    if ( is_wc_endpoint_url( 'lost-password' ) || is_wc_endpoint_url( 'reset-password' ) ) {
        return;
    }

    $signin_url  = mm10_get_auth_endpoint_url( 'signin' );
    $signup_url  = mm10_get_auth_endpoint_url( 'signup' );
    $can_signup  = 'yes' === get_option( 'woocommerce_enable_myaccount_registration', 'yes' );
    $is_signin   = is_wc_endpoint_url( 'signin' ) || mm10_is_auth_request_path( 'signin' );
    $is_signup   = is_wc_endpoint_url( 'signup' ) || mm10_is_auth_request_path( 'signup' );

    if ( isset( $_GET['action'] ) ) {
        $action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
        if ( 'register' === $action && $can_signup ) {
            wp_safe_redirect( $signup_url, 302 );
            exit;
        }
        if ( 'login' === $action ) {
            wp_safe_redirect( $signin_url, 302 );
            exit;
        }
    }

    if ( $is_signup && ! $can_signup ) {
        wp_safe_redirect( $signin_url, 302 );
        exit;
    }

    if ( ! $is_signin && ! $is_signup ) {
        wp_safe_redirect( $signin_url, 302 );
        exit;
    }
} );

add_filter( 'body_class', function( $classes ) {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return $classes;
    }

    if ( is_wc_endpoint_url( 'lost-password' ) || is_wc_endpoint_url( 'reset-password' ) ) {
        $classes[] = 'mm10-auth-lost';
    } elseif ( is_wc_endpoint_url( 'signup' ) || mm10_is_auth_request_path( 'signup' ) ) {
        $classes[] = 'mm10-auth-register';
    } else {
        $classes[] = 'mm10-auth-login';
    }

    return $classes;
} );

add_action( 'woocommerce_before_customer_login_form', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    $signin_url  = mm10_get_auth_endpoint_url( 'signin' );
    $signup_url  = mm10_get_auth_endpoint_url( 'signup' );
    $is_signup   = is_wc_endpoint_url( 'signup' ) || mm10_is_auth_request_path( 'signup' );
    $can_signup  = 'yes' === get_option( 'woocommerce_enable_myaccount_registration', 'yes' );

    echo '<div class="mm10-auth-switcher">';
    echo '<a href="' . esc_url( $signin_url ) . '" class="mm10-auth-switcher__tab' . ( $is_signup ? '' : ' is-active' ) . '">Login</a>';
    if ( $can_signup ) {
        echo '<a href="' . esc_url( $signup_url ) . '" class="mm10-auth-switcher__tab' . ( $is_signup ? ' is-active' : '' ) . '">Register</a>';
    }
    echo '</div>';
}, 5 );

add_action( 'woocommerce_login_form_end', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    $can_signup = 'yes' === get_option( 'woocommerce_enable_myaccount_registration', 'yes' );
    $is_signin  = is_wc_endpoint_url( 'signin' ) || mm10_is_auth_request_path( 'signin' );

    if ( ! $can_signup || ! $is_signin ) {
        return;
    }

    $signup_url = mm10_get_auth_endpoint_url( 'signup' );
    echo '<p class="mm10-login-register-cta">Don\'t have an account? <a href="' . esc_url( $signup_url ) . '">Register here</a></p>';
}, 20 );

add_action( 'woocommerce_register_form_end', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    $is_signup = is_wc_endpoint_url( 'signup' ) || mm10_is_auth_request_path( 'signup' );
    if ( ! $is_signup ) {
        return;
    }

    $signin_url = mm10_get_auth_endpoint_url( 'signin' );
    echo '<p class="mm10-register-login-cta">Already have an account? <a href="' . esc_url( $signin_url ) . '">Login here</a></p>';
}, 20 );

add_action( 'woocommerce_lostpassword_form', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    $signin_url = mm10_get_auth_endpoint_url( 'signin' );
    echo '<p class="mm10-lost-back-cta">Remembered your password? <a href="' . esc_url( $signin_url ) . '">Back to login</a></p>';
}, 30 );

add_action( 'woocommerce_after_lost_password_confirmation_message', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    echo '<p class="mm10-lost-home-cta"><a href="' . esc_url( home_url( '/' ) ) . '" class="button">Back to Home</a></p>';
}, 30 );

// Show auth validation errors under exact fields on My Account login/register forms.
add_action( 'wp_footer', function() {
    if ( is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }
    ?>
    <script>
    (function() {
        var wrappers = document.querySelectorAll('.woocommerce-notices-wrapper .woocommerce-error li');
        if (!wrappers.length) {
            return;
        }

        var mappedCount = 0;

        function addInlineError(input, message) {
            if (!input || !message) {
                return;
            }

            var row = input.closest('.form-row') || input.parentElement;
            if (!row) {
                return;
            }

            if (row.querySelector('.mm10-inline-error')) {
                return;
            }

            input.classList.add('mm10-invalid-field');

            var error = document.createElement('div');
            error.className = 'mm10-inline-error';
            error.textContent = message;
            row.appendChild(error);
            mappedCount++;
        }

        wrappers.forEach(function(item) {
            var clone = item.cloneNode(true);
            clone.querySelectorAll('a').forEach(function(link){ link.remove(); });
            var text = (clone.textContent || '').trim().replace(/\s+/g, ' ');
            var lower = text.toLowerCase();

            if (lower.indexOf('username') !== -1 || lower.indexOf('email address') !== -1 || lower.indexOf('invalid username') !== -1 || lower.indexOf('unknown email') !== -1) {
                addInlineError(document.getElementById('username'), text);
                return;
            }

            if (lower.indexOf('password') !== -1) {
                addInlineError(document.getElementById('password'), text);
                return;
            }

            if (lower.indexOf('email') !== -1) {
                addInlineError(document.getElementById('reg_email'), text);
                return;
            }
        });

        if (mappedCount > 0) {
            var noticeWrap = document.querySelector('.woocommerce-notices-wrapper');
            if (noticeWrap) {
                noticeWrap.classList.add('mm10-notices-inline-mapped');
            }
        }
    })();
    </script>
    <?php
}, 99 );

// 2. Add items to My Account navigation.
add_filter( 'woocommerce_account_menu_items', function( $items ) {
    // Build ordered nav: Dashboard first, then custom, then default, then logout.
    $logout = isset( $items['customer-logout'] ) ? $items['customer-logout'] : __( 'Logout', 'woocommerce' );
    unset( $items['customer-logout'] );

    $custom = array(
        'membership-history' => __( 'Membership History', 'astra-child-mm10' ),
        'orders'             => isset( $items['orders'] ) ? $items['orders'] : __( 'Orders', 'woocommerce' ),
        'child-profile'      => __( "Child Profile", 'astra-child-mm10' ),
        'attendance'         => __( 'Attendance', 'astra-child-mm10' ),
        'schedule'           => __( 'Training Schedule', 'astra-child-mm10' ),
        'progress-reports'   => __( 'Progress Reports', 'astra-child-mm10' ),
        'edit-account'       => isset( $items['edit-account'] ) ? $items['edit-account'] : __( 'Account Details', 'woocommerce' ),
        'support'            => __( 'Support', 'astra-child-mm10' ),
        'customer-logout'    => $logout,
    );
    unset( $items['orders'], $items['edit-account'] );
    return array_merge( array( 'dashboard' => $items['dashboard'] ?? __( 'Dashboard', 'woocommerce' ) ), $custom );
} );

// 3. Endpoint content callbacks.
add_action( 'woocommerce_account_membership-history_endpoint', function() {
    $user   = wp_get_current_user();
    $orders = wc_get_orders( array(
        'customer_id' => $user->ID,
        'limit'       => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ) );
    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-star-filled"></span> Membership History</h2>';
    if ( empty( $orders ) ) {
        echo '<div class="mm10-empty-state"><span class="dashicons dashicons-info"></span><p>No memberships yet. <a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '">Browse programs</a></p></div>';
    } else {
        echo '<table class="mm10-account-table"><thead><tr><th>Order</th><th>Date</th><th>Program</th><th>Status</th><th>Total</th></tr></thead><tbody>';
        foreach ( $orders as $order ) {
            $status_class = 'status-' . $order->get_status();
            echo '<tr>';
            echo '<td><a href="' . esc_url( $order->get_view_order_url() ) . '">#' . esc_html( $order->get_order_number() ) . '</a></td>';
            echo '<td>' . esc_html( wc_format_datetime( $order->get_date_created() ) ) . '</td>';
            $items_str = implode( ', ', array_map( fn($i) => $i->get_name(), $order->get_items() ) );
            echo '<td>' . esc_html( $items_str ) . '</td>';
            echo '<td><mark class="order-status ' . esc_attr( $status_class ) . '">' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</mark></td>';
            echo '<td>' . wp_kses_post( $order->get_formatted_order_total() ) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    echo '</div>';
} );

// =========================================================================
// CHILD PROFILES: Helper functions
// =========================================================================
function mm10_get_child_profiles( $user_id ) {
    $profiles = get_user_meta( $user_id, 'mm10_child_profiles', true );
    return is_array( $profiles ) ? $profiles : array();
}

function mm10_get_child_profile_age( $dob ) {
    if ( empty( $dob ) ) {
        return '';
    }

    $dob_date = DateTime::createFromFormat( 'Y-m-d', (string) $dob );
    if ( ! $dob_date ) {
        return '';
    }

    return (string) $dob_date->diff( new DateTime() )->y;
}

function mm10_get_child_profile_orders( $user_id, $profile_name ) {
    if ( empty( $profile_name ) ) {
        return array();
    }

    $orders = wc_get_orders( array(
        'customer_id' => (int) $user_id,
        'limit'       => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ) );

    $matched = array();
    foreach ( $orders as $order ) {
        $player_name = (string) get_post_meta( $order->get_id(), '_player_name', true );
        if ( '' === $player_name ) {
            continue;
        }

        if ( 0 !== strcasecmp( trim( $player_name ), trim( (string) $profile_name ) ) ) {
            continue;
        }

        $matched[] = $order;
    }

    return $matched;
}

function mm10_get_sportspress_players() {
    if ( ! post_type_exists( 'sp_player' ) ) {
        return array();
    }

    $players = get_posts( array(
        'post_type'      => 'sp_player',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    return is_array( $players ) ? $players : array();
}

function mm10_get_sportspress_player_details( $player_id ) {
    $player_id = absint( $player_id );
    if ( ! $player_id || ! post_type_exists( 'sp_player' ) ) {
        return array();
    }

    $details = array();

    $number = (string) get_post_meta( $player_id, 'sp_number', true );
    if ( '' !== $number ) {
        $details['Squad No'] = $number;
    }

    $positions = wp_get_post_terms( $player_id, 'sp_position', array( 'fields' => 'names' ) );
    if ( ! is_wp_error( $positions ) && ! empty( $positions ) ) {
        $details['Position'] = implode( ', ', array_map( 'sanitize_text_field', $positions ) );
    }

    $team_ids = array_map( 'absint', (array) get_post_meta( $player_id, 'sp_current_team', false ) );
    $team_ids = array_filter( $team_ids );
    if ( ! empty( $team_ids ) ) {
        $teams = array();
        foreach ( $team_ids as $team_id ) {
            $team_title = get_the_title( $team_id );
            if ( '' !== (string) $team_title ) {
                $teams[] = (string) $team_title;
            }
        }
        if ( ! empty( $teams ) ) {
            $details['Team'] = implode( ', ', $teams );
        }
    }

    $nationalities = (array) get_post_meta( $player_id, 'sp_nationality', false );
    $nationalities = array_filter( array_map( 'sanitize_text_field', $nationalities ) );
    if ( ! empty( $nationalities ) ) {
        $details['Nationality'] = implode( ', ', $nationalities );
    }

    $post_obj = get_post( $player_id );
    if ( $post_obj instanceof WP_Post && ! empty( $post_obj->post_modified ) ) {
        $details['Updated'] = date_i18n( 'j M Y', strtotime( (string) $post_obj->post_modified ) );
    }

    return $details;
}

function mm10_get_player_attendance_summary( $player_id ) {
    $summary = array(
        'events_total' => 0,
        'events_done'  => 0,
        'next_event'   => null,
    );

    if ( ! $player_id || ! post_type_exists( 'sp_event' ) ) {
        return $summary;
    }

    $events = get_posts( array(
        'post_type'      => 'sp_event',
        'posts_per_page' => 150,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => array( 'publish', 'future' ),
    ) );

    $now_ts = current_time( 'timestamp' );
    foreach ( $events as $event ) {
        $event_players = array_map( 'intval', (array) get_post_meta( $event->ID, 'sp_player', false ) );
        if ( ! in_array( (int) $player_id, $event_players, true ) ) {
            continue;
        }

        $summary['events_total']++;
        $event_ts = (int) get_post_time( 'U', true, $event );

        if ( $event_ts && $event_ts <= $now_ts ) {
            $summary['events_done']++;
        }

        if ( $event_ts && $event_ts > $now_ts && null === $summary['next_event'] ) {
            $summary['next_event'] = $event;
        }
    }

    return $summary;
}

function mm10_get_player_attendance_rows( $player_id, $limit = 30 ) {
    if ( ! $player_id || ! post_type_exists( 'sp_event' ) ) {
        return array();
    }

    $events = get_posts( array(
        'post_type'      => 'sp_event',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => array( 'publish', 'future' ),
    ) );

    $rows   = array();
    $now_ts = current_time( 'timestamp' );

    foreach ( $events as $event ) {
        $event_players = array_map( 'intval', (array) get_post_meta( $event->ID, 'sp_player', false ) );
        if ( ! in_array( (int) $player_id, $event_players, true ) ) {
            continue;
        }

        $event_ts = (int) get_post_time( 'U', true, $event );
        $rows[]   = array(
            'id'     => (int) $event->ID,
            'title'  => get_the_title( $event ),
            'date'   => get_the_date( 'D, j M Y', $event ),
            'time'   => get_the_date( 'H:i', $event ),
            'status' => ( $event_ts && $event_ts > $now_ts ) ? 'upcoming' : 'completed',
            'url'    => get_permalink( $event ),
        );
    }

    if ( $limit > 0 && count( $rows ) > $limit ) {
        $rows = array_slice( $rows, 0, $limit );
    }

    return $rows;
}

function mm10_find_stat_total( $totals, $candidates ) {
    foreach ( $candidates as $key ) {
        if ( isset( $totals[ $key ] ) ) {
            return (float) $totals[ $key ];
        }
    }
    return 0.0;
}

function mm10_collect_numeric_stat_totals( $data, &$totals ) {
    if ( ! is_array( $data ) ) {
        return;
    }

    foreach ( $data as $key => $value ) {
        if ( is_array( $value ) ) {
            mm10_collect_numeric_stat_totals( $value, $totals );
            continue;
        }

        if ( ! is_string( $key ) ) {
            continue;
        }

        if ( ! is_numeric( $value ) ) {
            continue;
        }

        $stat_key = strtolower( trim( $key ) );
        if ( '' === $stat_key ) {
            continue;
        }

        if ( ! isset( $totals[ $stat_key ] ) ) {
            $totals[ $stat_key ] = 0.0;
        }
        $totals[ $stat_key ] += (float) $value;
    }
}

function mm10_format_stat_label( $key ) {
    $label = str_replace( array( '-', '_' ), ' ', strtolower( (string) $key ) );
    return ucwords( $label );
}

function mm10_get_player_progress_payload( $player_id ) {
    $payload = array(
        'kpi' => array(
            'appearances'     => 0,
            'goals'           => 0,
            'assists'         => 0,
            'discipline'      => 0,
            'attendance_rate' => 0,
            'index'           => 0,
            'grade'           => 'Foundation',
        ),
        'metrics'   => array(),
        'totals'    => array(),
        'generated' => current_time( 'mysql' ),
    );

    $player_id = absint( $player_id );
    if ( ! $player_id ) {
        return $payload;
    }

    $metrics_raw = get_post_meta( $player_id, 'sp_metrics', true );
    if ( is_array( $metrics_raw ) ) {
        foreach ( $metrics_raw as $key => $value ) {
            if ( '' === (string) $value ) {
                continue;
            }
            $payload['metrics'][] = array(
                'label' => mm10_format_stat_label( $key ),
                'value' => is_numeric( $value ) ? (string) (float) $value : (string) $value,
            );
        }
    }

    $stats_raw = get_post_meta( $player_id, 'sp_statistics', true );
    $totals    = array();
    if ( is_array( $stats_raw ) ) {
        mm10_collect_numeric_stat_totals( $stats_raw, $totals );
    }

    arsort( $totals );
    $payload['totals'] = $totals;

    $appearances = mm10_find_stat_total( $totals, array( 'appearances', 'apps', 'matches', 'eventsplayed', 'played' ) );
    $goals       = mm10_find_stat_total( $totals, array( 'goals', 'goal' ) );
    $assists     = mm10_find_stat_total( $totals, array( 'assists', 'assist' ) );
    $yellow      = mm10_find_stat_total( $totals, array( 'yellowcards', 'yellow_cards', 'yellow card' ) );
    $red         = mm10_find_stat_total( $totals, array( 'redcards', 'red_cards', 'red card' ) );

    $attendance = mm10_get_player_attendance_summary( $player_id );
    $attendance_rate = 0.0;
    if ( (int) $attendance['events_total'] > 0 ) {
        $attendance_rate = ( (float) $attendance['events_done'] / (float) $attendance['events_total'] ) * 100.0;
    }

    $score = 0.0;
    $score += min( 30.0, $appearances * 1.5 );
    $score += min( 35.0, ( $goals * 4.0 ) + ( $assists * 3.0 ) );
    $score += min( 25.0, $attendance_rate * 0.25 );
    $score -= min( 15.0, ( $yellow * 0.8 ) + ( $red * 2.5 ) );
    if ( $score < 0 ) {
        $score = 0.0;
    }
    if ( $score > 100 ) {
        $score = 100.0;
    }

    $grade = 'Foundation';
    if ( $score >= 80 ) {
        $grade = 'Elite';
    } elseif ( $score >= 60 ) {
        $grade = 'Advanced';
    } elseif ( $score >= 40 ) {
        $grade = 'Developing';
    }

    $payload['kpi'] = array(
        'appearances'     => (int) round( $appearances ),
        'goals'           => (int) round( $goals ),
        'assists'         => (int) round( $assists ),
        'discipline'      => (int) round( $yellow + $red ),
        'attendance_rate' => (int) round( $attendance_rate ),
        'index'           => (int) round( $score ),
        'grade'           => $grade,
    );

    return $payload;
}

function mm10_send_child_profile_notification( $user_id, $action, $profile ) {
    $user = get_user_by( 'id', (int) $user_id );
    if ( ! $user instanceof WP_User || empty( $user->user_email ) ) {
        return;
    }

    $profile_name = isset( $profile['name'] ) ? (string) $profile['name'] : 'Child';
    $action_label = 'updated';
    if ( 'created' === $action ) {
        $action_label = 'created';
    } elseif ( 'deleted' === $action ) {
        $action_label = 'deleted';
    }

    $subject = sprintf( 'MM10 Child Profile %s: %s', ucfirst( $action_label ), $profile_name );
    $message = array();
    $message[] = 'Hello ' . ( $user->first_name ? $user->first_name : $user->display_name ) . ',';
    $message[] = '';
    $message[] = 'Your child profile has been ' . $action_label . '.';
    $message[] = 'Name: ' . $profile_name;
    if ( ! empty( $profile['gender'] ) ) {
        $message[] = 'Gender: ' . ucfirst( (string) $profile['gender'] );
    }
    if ( ! empty( $profile['dob'] ) ) {
        $message[] = 'Date of Birth: ' . (string) $profile['dob'];
    }
    $message[] = '';
    $message[] = 'Manage profiles: ' . wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) );

    wp_mail( $user->user_email, $subject, implode( "\n", $message ) );
}

function mm10_save_child_profile( $user_id, $profile_id, $profile_data ) {
    $profiles = mm10_get_child_profiles( $user_id );
    $is_new   = empty( $profile_id ) || ! isset( $profiles[ $profile_id ] );

    if ( empty( $profile_id ) ) {
        $profile_id = 'profile_' . time() . '_' . rand( 100, 999 );
    }

    $profiles[ $profile_id ] = array_merge(
        isset( $profiles[ $profile_id ] ) ? $profiles[ $profile_id ] : array(),
        $profile_data,
        array( 'updated' => current_time( 'mysql' ) )
    );
    if ( ! isset( $profiles[ $profile_id ]['created'] ) ) {
        $profiles[ $profile_id ]['created'] = current_time( 'mysql' );
    }

    update_user_meta( $user_id, 'mm10_child_profiles', $profiles );
    mm10_send_child_profile_notification( $user_id, $is_new ? 'created' : 'updated', $profiles[ $profile_id ] );

    return $profile_id;
}

function mm10_delete_child_profile( $user_id, $profile_id ) {
    $profiles = mm10_get_child_profiles( $user_id );
    $profile  = isset( $profiles[ $profile_id ] ) ? $profiles[ $profile_id ] : array();

    unset( $profiles[ $profile_id ] );
    update_user_meta( $user_id, 'mm10_child_profiles', $profiles );

    if ( ! empty( $profile ) ) {
        mm10_send_child_profile_notification( $user_id, 'deleted', $profile );
    }
}

// =========================================================================
// CHILD PROFILES: Form submission handler
// =========================================================================
add_action( 'wp', function() {
    if ( ! mm10_is_auth_request_path( 'child-profile' ) ) {
        return;
    }
    if ( ! is_user_logged_in() ) {
        return;
    }

    $user_id = get_current_user_id();
    $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
    $profile_id = isset( $_GET['profile_id'] ) ? sanitize_text_field( $_GET['profile_id'] ) : '';

    // Handle delete
    if ( 'delete' === $action && $profile_id && isset( $_GET['_wpnonce'] ) ) {
        if ( wp_verify_nonce( $_GET['_wpnonce'], 'mm10_delete_profile_' . $profile_id ) ) {
            mm10_delete_child_profile( $user_id, $profile_id );
            wp_redirect( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) );
            exit;
        }
    }

    // Handle save (POST)
    if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['mm10_save_profile'] ) ) {
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'mm10_child_profile_nonce' ) ) {
            wp_die( 'Security check failed' );
        }

        $profile_id = isset( $_POST['profile_id'] ) ? sanitize_text_field( $_POST['profile_id'] ) : '';
        $existing   = $profile_id ? mm10_get_child_profiles( $user_id ) : array();
        $existing_profile = ( $profile_id && isset( $existing[ $profile_id ] ) ) ? $existing[ $profile_id ] : array();

        $profile_data = array(
            'name'      => isset( $_POST['child_name'] ) ? sanitize_text_field( $_POST['child_name'] ) : '',
            'gender'    => isset( $_POST['child_gender'] ) ? sanitize_text_field( $_POST['child_gender'] ) : '',
            'dob'       => isset( $_POST['child_dob'] ) ? sanitize_text_field( $_POST['child_dob'] ) : '',
            'age_group' => isset( $_POST['child_age_group'] ) ? sanitize_text_field( $_POST['child_age_group'] ) : '',
            'sp_player_id' => isset( $_POST['sp_player_id'] ) ? absint( $_POST['sp_player_id'] ) : 0,
        );

        // Keep previous photo by default.
        if ( ! empty( $existing_profile['photo_id'] ) ) {
            $profile_data['photo_id'] = absint( $existing_profile['photo_id'] );
        }

        if ( ! empty( $_POST['remove_photo'] ) ) {
            $profile_data['photo_id'] = 0;
        }

        if ( isset( $_FILES['child_photo'] ) && ! empty( $_FILES['child_photo']['name'] ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $attachment_id = media_handle_upload( 'child_photo', 0 );
            if ( is_wp_error( $attachment_id ) ) {
                wc_add_notice( __( 'Photo upload failed. Please try again.', 'astra-child-mm10' ), 'error' );
                return;
            }
            $profile_data['photo_id'] = (int) $attachment_id;
        }

        if ( empty( $profile_data['name'] ) || empty( $profile_data['dob'] ) || empty( $profile_data['gender'] ) ) {
            wp_die( 'Please fill in all required fields.' );
        }

        mm10_save_child_profile( $user_id, $profile_id, $profile_data );
        wp_redirect( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) );
        exit;
    }
} );

// =========================================================================
// CHILD PROFILES: Display endpoint
// =========================================================================
add_action( 'woocommerce_account_child-profile_endpoint', function() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    $user_id = get_current_user_id();
    $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
    $profile_id = isset( $_GET['profile_id'] ) ? sanitize_text_field( $_GET['profile_id'] ) : '';
    $profiles = mm10_get_child_profiles( $user_id );
    $current_profile = ( $profile_id && isset( $profiles[ $profile_id ] ) ) ? $profiles[ $profile_id ] : null;
    $sports_players = mm10_get_sportspress_players();

    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-groups"></span> Child Profile</h2>';

    if ( 'edit' === $action || 'add' === $action ) {
        // Edit/Add form
        echo '<div class="mm10-profile-form-wrap">';
        echo '<form method="post" enctype="multipart/form-data" class="mm10-child-profile-form">';
        wp_nonce_field( 'mm10_child_profile_nonce' );
        echo '<input type="hidden" name="profile_id" value="' . esc_attr( $profile_id ) . '">';
        echo '<input type="hidden" name="mm10_save_profile" value="1">';

        echo '<div class="mm10-form-group">';
        echo '<label for="child_name">Child\'s Name *</label>';
        echo '<input type="text" id="child_name" name="child_name" value="' . esc_attr( $current_profile['name'] ?? '' ) . '" required>';
        echo '</div>';

        echo '<div class="mm10-form-row">';
        echo '<div class="mm10-form-group">';
        echo '<label for="child_gender">Gender *</label>';
        echo '<select id="child_gender" name="child_gender" required>';
        echo '<option value="">-- Select --</option>';
        $gender = $current_profile['gender'] ?? '';
        echo '<option value="male" ' . selected( $gender, 'male', false ) . '>Male</option>';
        echo '<option value="female" ' . selected( $gender, 'female', false ) . '>Female</option>';
        echo '<option value="other" ' . selected( $gender, 'other', false ) . '>Other</option>';
        echo '</select>';
        echo '</div>';

        echo '<div class="mm10-form-group">';
        echo '<label for="child_dob">Date of Birth *</label>';
        echo '<input type="date" id="child_dob" name="child_dob" value="' . esc_attr( $current_profile['dob'] ?? '' ) . '" required>';
        echo '</div>';
        echo '</div>';

        echo '<div class="mm10-form-group">';
        echo '<label for="child_age_group">Age Group / Level</label>';
        echo '<select id="child_age_group" name="child_age_group">';
        echo '<option value="">-- Not Set --</option>';
        $age_group = $current_profile['age_group'] ?? '';
        $groups = array( 'u6' => 'Under 6', 'u8' => 'Under 8', 'u10' => 'Under 10', 'u12' => 'Under 12', 'u14' => 'Under 14', 'u16' => 'Under 16', 'u18' => 'Under 18', 'adult' => 'Adult' );
        foreach ( $groups as $key => $label ) {
            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $age_group, $key, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<div class="mm10-form-group">';
        echo '<label for="sp_player_id">Linked SportsPress Player</label>';
        echo '<select id="sp_player_id" name="sp_player_id">';
        echo '<option value="0">-- Not Linked --</option>';
        $linked_player_id = absint( $current_profile['sp_player_id'] ?? 0 );
        foreach ( $sports_players as $sp_player ) {
            echo '<option value="' . esc_attr( $sp_player->ID ) . '" ' . selected( $linked_player_id, (int) $sp_player->ID, false ) . '>' . esc_html( $sp_player->post_title ) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        $existing_photo_id = absint( $current_profile['photo_id'] ?? 0 );
        if ( $existing_photo_id ) {
            echo '<div class="mm10-form-group">';
            echo '<label>Current Photo</label>';
            echo '<div class="mm10-profile-photo-preview">' . wp_get_attachment_image( $existing_photo_id, 'thumbnail' ) . '</div>';
            echo '<label class="mm10-inline-check"><input type="checkbox" name="remove_photo" value="1"> Remove current photo</label>';
            echo '</div>';
        }

        echo '<div class="mm10-form-group">';
        echo '<label for="child_photo">Child Photo</label>';
        echo '<input type="file" id="child_photo" name="child_photo" accept="image/*">';
        echo '<small class="mm10-help-text">Upload JPG/PNG/WebP image for child profile.</small>';
        echo '</div>';

        echo '<div class="mm10-form-actions">';
        echo '<button type="submit" class="mm10-btn mm10-btn--primary">Save Profile</button>';
        echo '<a href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) ) . '" class="mm10-btn mm10-btn--secondary">Cancel</a>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    } else {
        // List view
        echo '<div class="mm10-profiles-header">';
        echo '<p class="mm10-profiles-count">You have ' . count( $profiles ) . ' child profile' . ( count( $profiles ) !== 1 ? 's' : '' ) . '</p>';
        echo '<a href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) . '?action=add' ) . '" class="mm10-btn mm10-btn--primary">+ Add New Child</a>';
        echo '</div>';

        if ( empty( $profiles ) ) {
            echo '<div class="mm10-empty-state">';
            echo '<span class="dashicons dashicons-groups"></span>';
            echo '<h3>No Child Profiles Yet</h3>';
            echo '<p>Add your child\'s profile to track their training progress and manage their enrollment.</p>';
            echo '</div>';
        } else {
            echo '<div class="mm10-profiles-grid">';
            foreach ( $profiles as $pid => $profile ) {
                $name = $profile['name'] ?? 'Unnamed';
                $dob = $profile['dob'] ?? '';
                $age_group = $profile['age_group'] ?? 'Not set';
                $photo_id = absint( $profile['photo_id'] ?? 0 );
                $linked_player_id = absint( $profile['sp_player_id'] ?? 0 );
                $profile_orders = mm10_get_child_profile_orders( $user_id, $name );
                $attendance = mm10_get_player_attendance_summary( $linked_player_id );
                $player_details = mm10_get_sportspress_player_details( $linked_player_id );
                $age_group_label = array( 'u6' => 'U6', 'u8' => 'U8', 'u10' => 'U10', 'u12' => 'U12', 'u14' => 'U14', 'u16' => 'U16', 'u18' => 'U18', 'adult' => 'Adult' );
                $age_display = $age_group_label[ $age_group ] ?? 'Not Set';
                $age = mm10_get_child_profile_age( $dob );
                $gender_value = isset( $profile['gender'] ) ? ucfirst( (string) $profile['gender'] ) : 'Not Set';

                echo '<div class="mm10-profile-card">';
                echo '<div class="mm10-profile-card-header">';
                echo '<div class="mm10-profile-identity">';
                if ( $photo_id ) {
                    echo '<div class="mm10-profile-avatar">' . wp_get_attachment_image( $photo_id, 'thumbnail' ) . '</div>';
                } else {
                    echo '<div class="mm10-profile-avatar mm10-profile-avatar--placeholder"><span class="dashicons dashicons-admin-users"></span></div>';
                }
                echo '<div class="mm10-profile-name-wrap">';
                echo '<h3 class="mm10-profile-name">' . esc_html( $name ) . '</h3>';
                echo '</div>';
                echo '</div>';
                echo '<span class="mm10-profile-badge">' . esc_html( $age_display ) . '</span>';
                echo '</div>';
                echo '<div class="mm10-profile-card-content">';
                echo '<div class="mm10-profile-facts">';
                echo '<div class="mm10-profile-fact"><span>Age</span><strong>' . ( '' !== $age ? esc_html( $age ) . ' years' : 'Not set' ) . '</strong></div>';
                echo '<div class="mm10-profile-fact"><span>Gender</span><strong>' . esc_html( $gender_value ) . '</strong></div>';
                echo '</div>';

                if ( $linked_player_id ) {
                    echo '<p class="mm10-profile-link-row"><strong>SportsPress:</strong> <a href="' . esc_url( get_permalink( $linked_player_id ) ) . '" class="mm10-player-modal-trigger" data-player-name="' . esc_attr( $name ) . '">View Player Profile</a></p>';
                    echo '<div class="mm10-profile-chip-wrap">';
                    echo '<span class="mm10-profile-chip">Sessions: ' . esc_html( (string) $attendance['events_total'] ) . '</span>';
                    echo '<span class="mm10-profile-chip">Completed: ' . esc_html( (string) $attendance['events_done'] ) . '</span>';
                    echo '</div>';

                    if ( ! empty( $attendance['next_event'] ) ) {
                        echo '<p><strong>Next Session:</strong> ' . esc_html( get_the_title( $attendance['next_event'] ) ) . '</p>';
                    }

                    if ( ! empty( $player_details ) ) {
                        echo '<div class="mm10-player-details">';
                        echo '<h4>Player Details</h4>';
                        echo '<ul class="mm10-player-details-list">';
                        foreach ( $player_details as $label => $value ) {
                            if ( '' === trim( (string) $value ) ) {
                                continue;
                            }
                            echo '<li><span class="mm10-player-details-label">' . esc_html( $label ) . '</span><span class="mm10-player-details-value">' . esc_html( (string) $value ) . '</span></li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                    }

                    if ( shortcode_exists( 'player_statistics' ) ) {
                        if ( (int) $attendance['events_done'] > 0 || (int) $attendance['events_total'] > 0 ) {
                            echo '<details class="mm10-profile-stats-collapse">';
                            echo '<summary>View Performance Statistics</summary>';
                            echo '<div class="mm10-profile-stats">' . do_shortcode( '[player_statistics id="' . (int) $linked_player_id . '"]' ) . '</div>';
                            echo '</details>';
                        } else {
                            echo '<p class="mm10-muted">Performance statistics will appear once session data is recorded.</p>';
                        }
                    }
                }

                echo '<div class="mm10-profile-enrollments">';
                echo '<h4>Enrollment History</h4>';
                if ( empty( $profile_orders ) ) {
                    echo '<p class="mm10-muted">No enrollments linked yet.</p>';
                } else {
                    echo '<ul class="mm10-enrollment-list">';
                    foreach ( $profile_orders as $order ) {
                        $items_str = implode( ', ', array_map( fn( $i ) => $i->get_name(), $order->get_items() ) );
                        echo '<li>';
                        echo '<a href="' . esc_url( $order->get_view_order_url() ) . '">#' . esc_html( $order->get_order_number() ) . '</a>';
                        echo ' - ' . esc_html( $items_str );
                        echo ' <mark class="order-status status-' . esc_attr( $order->get_status() ) . '">' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</mark>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
                echo '</div>';

                echo '</div>';
                echo '<div class="mm10-profile-card-actions">';
                echo '<a href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) . '?action=edit&profile_id=' . esc_attr( $pid ) ) . '" class="mm10-profile-action mm10-profile-action--edit">Edit</a>';
                $delete_url = add_query_arg( array( 'action' => 'delete', 'profile_id' => $pid, '_wpnonce' => wp_create_nonce( 'mm10_delete_profile_' . $pid ) ), wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) );
                echo '<a href="' . esc_url( $delete_url ) . '" class="mm10-profile-action mm10-profile-action--delete" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
    }

    echo '</div>';
} );

// =========================================================================
// CHILD PROFILES: Admin dashboard page
// =========================================================================
add_action( 'admin_menu', function() {
    add_users_page(
        __( 'Child Profiles', 'astra-child-mm10' ),
        __( 'Child Profiles', 'astra-child-mm10' ),
        'list_users',
        'mm10-child-profiles-admin',
        function() {
            if ( ! current_user_can( 'list_users' ) ) {
                return;
            }

            $users = get_users( array(
                'fields' => array( 'ID', 'display_name', 'user_email' ),
            ) );

            $rows_html       = array();
            $family_count    = 0;
            $total_profiles  = 0;
            $linked_profiles = 0;
            $photo_profiles  = 0;
            $age_group_label = array( 'u6' => 'U6', 'u8' => 'U8', 'u10' => 'U10', 'u12' => 'U12', 'u14' => 'U14', 'u16' => 'U16', 'u18' => 'U18', 'adult' => 'Adult' );

            foreach ( $users as $user ) {
                $profiles = mm10_get_child_profiles( (int) $user->ID );
                if ( empty( $profiles ) ) {
                    continue;
                }

                $family_count++;

                foreach ( $profiles as $profile ) {
                    $total_profiles++;
                    $linked_player_id = absint( $profile['sp_player_id'] ?? 0 );
                    $photo_id         = absint( $profile['photo_id'] ?? 0 );
                    $age              = mm10_get_child_profile_age( (string) ( $profile['dob'] ?? '' ) );
                    $gender_raw       = strtolower( (string) ( $profile['gender'] ?? '' ) );
                    $gender_class     = in_array( $gender_raw, array( 'male', 'female', 'other' ), true ) ? $gender_raw : 'unknown';
                    $gender_label     = '' !== $gender_raw ? ucfirst( $gender_raw ) : 'Not set';
                    $age_group_value  = strtolower( (string) ( $profile['age_group'] ?? '' ) );
                    $age_group_clean  = preg_replace( '/[^a-z0-9\-]/', '', $age_group_value );
                    $age_group_text   = $age_group_label[ $age_group_value ] ?? ( '' !== $age_group_value ? strtoupper( $age_group_value ) : 'Not set' );
                    $updated_value    = (string) ( $profile['updated'] ?? '' );

                    if ( $linked_player_id ) {
                        $linked_profiles++;
                    }
                    if ( $photo_id ) {
                        $photo_profiles++;
                    }

                    $row  = '<tr>';
                    $row .= '<td><strong>' . esc_html( (string) $user->display_name ) . '</strong></td>';
                    $row .= '<td><a href="mailto:' . esc_attr( (string) $user->user_email ) . '">' . esc_html( (string) $user->user_email ) . '</a></td>';
                    $row .= '<td>' . esc_html( (string) ( $profile['name'] ?? '' ) ) . '</td>';
                    $row .= '<td>' . esc_html( '' !== $age ? $age . ' yrs' : 'Not set' ) . '</td>';
                    $row .= '<td><span class="mm10-admin-pill mm10-admin-pill--gender mm10-admin-pill--' . esc_attr( $gender_class ) . '">' . esc_html( $gender_label ) . '</span></td>';
                    $row .= '<td><span class="mm10-admin-pill mm10-admin-pill--age mm10-admin-pill--age-' . esc_attr( $age_group_clean ) . '">' . esc_html( $age_group_text ) . '</span></td>';

                    if ( $linked_player_id ) {
                        $edit_link = get_edit_post_link( $linked_player_id );
                        $row .= '<td>';
                        if ( $edit_link ) {
                            $row .= '<a href="' . esc_url( $edit_link ) . '">#' . esc_html( (string) $linked_player_id ) . '</a>';
                        } else {
                            $row .= '#' . esc_html( (string) $linked_player_id );
                        }
                        $row .= '</td>';
                    } else {
                        $row .= '<td><span class="mm10-admin-muted">Not linked</span></td>';
                    }

                    $row .= '<td>' . esc_html( '' !== $updated_value ? $updated_value : '-' ) . '</td>';
                    $row .= '</tr>';
                    $rows_html[] = $row;
                }
            }

            echo '<div class="wrap mm10-admin-child-profiles">';
            echo '<h1>MM10 Child Profiles</h1>';
            echo '<div class="mm10-admin-overview">';
            echo '<div class="mm10-admin-overview-card"><span>Total Profiles</span><strong>' . esc_html( (string) $total_profiles ) . '</strong></div>';
            echo '<div class="mm10-admin-overview-card"><span>Families</span><strong>' . esc_html( (string) $family_count ) . '</strong></div>';
            echo '<div class="mm10-admin-overview-card"><span>SportsPress Linked</span><strong>' . esc_html( (string) $linked_profiles ) . '</strong></div>';
            echo '<div class="mm10-admin-overview-card"><span>With Photo</span><strong>' . esc_html( (string) $photo_profiles ) . '</strong></div>';
            echo '</div>';
            echo '<table class="widefat striped mm10-admin-child-table">';
            echo '<thead><tr><th>User</th><th>Email</th><th>Child</th><th>Age</th><th>Gender</th><th>Age Group</th><th>SportsPress</th><th>Updated</th></tr></thead><tbody>';

            if ( empty( $rows_html ) ) {
                echo '<tr><td colspan="8">No child profiles found.</td></tr>';
            } else {
                echo implode( '', $rows_html );
            }

            echo '</tbody></table>';
            echo '</div>';
        }
    );
} );

add_action( 'admin_head-users_page_mm10-child-profiles-admin', function() {
    echo '<style>';
    echo '.mm10-admin-child-profiles{max-width:1220px}.mm10-admin-overview{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:14px 0 18px}.mm10-admin-overview-card{background:#fff;border:1px solid #dbe7f5;border-radius:10px;padding:12px 14px;box-shadow:0 1px 3px rgba(15,23,42,.05)}.mm10-admin-overview-card span{display:block;font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:#4b6785}.mm10-admin-overview-card strong{display:block;margin-top:6px;font-size:22px;line-height:1;color:#102a43}.mm10-admin-child-table thead th{background:#f4f8fd;font-weight:700}.mm10-admin-child-table td{vertical-align:middle}.mm10-admin-pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;line-height:1.4}.mm10-admin-pill--gender.mm10-admin-pill--male{background:#e8f1ff;color:#1455c2}.mm10-admin-pill--gender.mm10-admin-pill--female{background:#fce7f3;color:#9d174d}.mm10-admin-pill--gender.mm10-admin-pill--other,.mm10-admin-pill--gender.mm10-admin-pill--unknown{background:#eef2ff;color:#4338ca}.mm10-admin-pill--age{background:#ecfdf3;color:#166534}.mm10-admin-muted{color:#64748b}.mm10-admin-child-table a{font-weight:600}.mm10-admin-child-table tbody tr:hover td{background:#f8fbff}@media (max-width:1024px){.mm10-admin-overview{grid-template-columns:repeat(2,minmax(0,1fr))}}';
    echo '</style>';
} );

add_action( 'woocommerce_account_attendance_endpoint', function() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    $user_id  = get_current_user_id();
    $profiles = mm10_get_child_profiles( $user_id );

    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-yes-alt"></span> Attendance</h2>';

    if ( empty( $profiles ) ) {
        echo '<div class="mm10-empty-state">';
        echo '<span class="dashicons dashicons-groups"></span>';
        echo '<h3>No Child Profiles Found</h3>';
        echo '<p>Add a child profile first to view attendance tracking.</p>';
        echo '<p><a class="mm10-btn mm10-btn--primary" href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">Go To Child Profile</a></p>';
        echo '</div></div>';
        return;
    }

    $selected_profile_id = isset( $_GET['child'] ) ? sanitize_text_field( $_GET['child'] ) : '';
    if ( ! $selected_profile_id || ! isset( $profiles[ $selected_profile_id ] ) ) {
        $keys = array_keys( $profiles );
        $selected_profile_id = (string) $keys[0];
    }

    $selected_profile = $profiles[ $selected_profile_id ];
    $selected_name    = (string) ( $selected_profile['name'] ?? '' );
    $linked_player_id = absint( $selected_profile['sp_player_id'] ?? 0 );

    // Fallback: auto-link by SportsPress player title if not explicitly linked.
    if ( ! $linked_player_id && '' !== $selected_name && post_type_exists( 'sp_player' ) ) {
        $found = get_posts( array(
            'post_type'      => 'sp_player',
            'title'          => $selected_name,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );
        if ( ! empty( $found[0] ) ) {
            $linked_player_id = (int) $found[0]->ID;
        }
    }

    echo '<div class="mm10-attendance-switcher">';
    foreach ( $profiles as $pid => $profile ) {
        $is_active = ( $pid === $selected_profile_id );
        $url = add_query_arg( array( 'child' => $pid ), wc_get_endpoint_url( 'attendance', '', wc_get_page_permalink( 'myaccount' ) ) );
        echo '<a class="mm10-attendance-tab' . ( $is_active ? ' is-active' : '' ) . '" href="' . esc_url( $url ) . '">';
        echo esc_html( (string) ( $profile['name'] ?? 'Child' ) );
        echo '</a>';
    }
    echo '</div>';

    if ( ! $linked_player_id ) {
        echo '<div class="mm10-coming-soon">';
        echo '<div class="mm10-coming-soon-icon"><span class="dashicons dashicons-warning"></span></div>';
        echo '<h3>SportsPress Player Not Linked</h3>';
        echo '<p>Link this child to a SportsPress player in Child Profile to enable full attendance tracking.</p>';
        echo '<p><a class="mm10-btn mm10-btn--primary" href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">Open Child Profile</a></p>';
        echo '</div></div>';
        return;
    }

    $summary = mm10_get_player_attendance_summary( $linked_player_id );
    $rows    = mm10_get_player_attendance_rows( $linked_player_id, 30 );

    $completed = 0;
    $upcoming  = 0;
    foreach ( $rows as $row ) {
        if ( 'upcoming' === $row['status'] ) {
            $upcoming++;
        } else {
            $completed++;
        }
    }

    echo '<div class="mm10-attendance-cards">';
    echo '<div class="mm10-attendance-card"><h4>Total Sessions</h4><strong>' . esc_html( (string) count( $rows ) ) . '</strong></div>';
    echo '<div class="mm10-attendance-card"><h4>Completed</h4><strong>' . esc_html( (string) $completed ) . '</strong></div>';
    echo '<div class="mm10-attendance-card"><h4>Upcoming</h4><strong>' . esc_html( (string) $upcoming ) . '</strong></div>';
    echo '<div class="mm10-attendance-card"><h4>Linked Player</h4><strong>#' . esc_html( (string) $linked_player_id ) . '</strong></div>';
    echo '</div>';

    if ( ! empty( $summary['next_event'] ) ) {
        echo '<div class="mm10-attendance-next">';
        echo '<span class="dashicons dashicons-calendar-alt"></span>';
        echo '<span><strong>Next Session:</strong> ' . esc_html( get_the_title( $summary['next_event'] ) ) . ' (' . esc_html( get_the_date( 'D, j M Y', $summary['next_event'] ) ) . ')</span>';
        echo '</div>';
    }

    if ( empty( $rows ) ) {
        echo '<div class="mm10-empty-state">';
        echo '<span class="dashicons dashicons-yes-alt"></span>';
        echo '<h3>No Sessions Found</h3>';
        echo '<p>No SportsPress sessions are linked to this player yet.</p>';
        echo '</div>';
    } else {
        echo '<div class="mm10-attendance-table-wrap">';
        echo '<table class="mm10-account-table mm10-attendance-table">';
        echo '<thead><tr><th>Date</th><th>Session</th><th>Time</th><th>Status</th></tr></thead><tbody>';
        foreach ( $rows as $row ) {
            $status_label = ( 'upcoming' === $row['status'] ) ? 'Upcoming' : 'Completed';
            echo '<tr>';
            echo '<td>' . esc_html( $row['date'] ) . '</td>';
            echo '<td><a href="' . esc_url( $row['url'] ) . '">' . esc_html( $row['title'] ) . '</a></td>';
            echo '<td>' . esc_html( $row['time'] ) . '</td>';
            echo '<td><span class="mm10-attendance-status mm10-attendance-status--' . esc_attr( $row['status'] ) . '">' . esc_html( $status_label ) . '</span></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }

    echo '<p class="mm10-attendance-note">Attendance is generated from SportsPress player-to-session assignments.</p>';
    echo '</div>';
} );

add_action( 'woocommerce_account_schedule_endpoint', function() {
    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-calendar-alt"></span> Training Schedule</h2>';
    if ( class_exists( 'SportsPress' ) && shortcode_exists( 'mm10_fixtures' ) ) {
        echo do_shortcode( '[mm10_fixtures limit="10" title="Upcoming Training Sessions"]' );
    } elseif ( class_exists( 'SportsPress' ) ) {
        $events = get_posts( array(
            'post_type'      => 'sp_event',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'ASC',
            'date_query'     => array( array( 'after' => 'yesterday' ) ),
        ) );

        if ( empty( $events ) ) {
            echo '<div class="mm10-coming-soon"><div class="mm10-coming-soon-icon"><span class="dashicons dashicons-calendar-alt"></span></div><h3>No Upcoming Sessions Yet</h3><p>Your training schedule will appear here once sessions are published.</p></div>';
        } else {
            echo '<div class="mm10-fixtures-widget"><h3 class="mm10-widget-heading">Upcoming Training Sessions</h3><div class="mm10-fixture-list">';
            foreach ( $events as $event ) {
                $date = get_the_date( 'D, j M Y', $event );
                $time = get_the_date( 'H:i', $event );
                echo '<div class="mm10-fixture-row">';
                echo '<div class="mm10-fixture-date"><span class="mm10-fix-day">' . esc_html( $date ) . '</span>';
                if ( $time ) {
                    echo '<span class="mm10-fix-time">' . esc_html( $time ) . '</span>';
                }
                echo '</div>';
                echo '<a href="' . esc_url( get_permalink( $event ) ) . '" class="mm10-fixture-name">' . esc_html( get_the_title( $event ) ) . '</a>';
                echo '</div>';
            }
            echo '</div></div>';
        }
    } else {
        echo '<div class="mm10-coming-soon"><div class="mm10-coming-soon-icon"><span class="dashicons dashicons-calendar-alt"></span></div><h3>Schedule Coming Soon</h3><p>Your training schedule will appear here once sessions are published.</p></div>';
    }
    echo '</div>';
} );

add_action( 'woocommerce_account_progress-reports_endpoint', function() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    $user_id  = get_current_user_id();
    $profiles = mm10_get_child_profiles( $user_id );

    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-chart-bar"></span> Progress Reports</h2>';

    if ( empty( $profiles ) ) {
        echo '<div class="mm10-empty-state">';
        echo '<span class="dashicons dashicons-groups"></span>';
        echo '<h3>No Child Profiles Found</h3>';
        echo '<p>Create a child profile first to generate progress analytics.</p>';
        echo '<p><a class="mm10-btn mm10-btn--primary" href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">Go To Child Profile</a></p>';
        echo '</div></div>';
        return;
    }

    $selected_profile_id = isset( $_GET['child'] ) ? sanitize_text_field( $_GET['child'] ) : '';
    if ( ! $selected_profile_id || ! isset( $profiles[ $selected_profile_id ] ) ) {
        $keys = array_keys( $profiles );
        $selected_profile_id = (string) $keys[0];
    }

    $selected_profile = $profiles[ $selected_profile_id ];
    $selected_name    = (string) ( $selected_profile['name'] ?? '' );
    $linked_player_id = absint( $selected_profile['sp_player_id'] ?? 0 );

    if ( ! $linked_player_id && '' !== $selected_name && post_type_exists( 'sp_player' ) ) {
        $found = get_posts( array(
            'post_type'      => 'sp_player',
            'title'          => $selected_name,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );
        if ( ! empty( $found[0] ) ) {
            $linked_player_id = (int) $found[0]->ID;
        }
    }

    echo '<div class="mm10-progress-switcher">';
    foreach ( $profiles as $pid => $profile ) {
        $is_active = ( $pid === $selected_profile_id );
        $url = add_query_arg( array( 'child' => $pid ), wc_get_endpoint_url( 'progress-reports', '', wc_get_page_permalink( 'myaccount' ) ) );
        echo '<a class="mm10-progress-tab' . ( $is_active ? ' is-active' : '' ) . '" href="' . esc_url( $url ) . '">' . esc_html( (string) ( $profile['name'] ?? 'Child' ) ) . '</a>';
    }
    echo '</div>';

    if ( ! $linked_player_id ) {
        echo '<div class="mm10-coming-soon">';
        echo '<div class="mm10-coming-soon-icon"><span class="dashicons dashicons-chart-bar"></span></div>';
        echo '<h3>Player Link Required</h3>';
        echo '<p>Link this child profile to a SportsPress player to unlock progress analytics.</p>';
        echo '<p><a class="mm10-btn mm10-btn--primary" href="' . esc_url( wc_get_endpoint_url( 'child-profile', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">Open Child Profile</a></p>';
        echo '</div></div>';
        return;
    }

    $payload = mm10_get_player_progress_payload( $linked_player_id );
    $kpi     = $payload['kpi'];
    $details = mm10_get_sportspress_player_details( $linked_player_id );

    echo '<div class="mm10-progress-meta">';
    echo '<span>Report For: <strong>' . esc_html( $selected_name ) . '</strong></span>';
    echo '<span>Player ID: <strong>#' . esc_html( (string) $linked_player_id ) . '</strong></span>';
    echo '<span>Generated: <strong>' . esc_html( date_i18n( 'j M Y H:i', strtotime( (string) $payload['generated'] ) ) ) . '</strong></span>';
    echo '</div>';

    echo '<div class="mm10-progress-kpis">';
    echo '<div class="mm10-progress-kpi"><h4>Progress Index</h4><strong>' . esc_html( (string) $kpi['index'] ) . '%</strong></div>';
    echo '<div class="mm10-progress-kpi"><h4>Grade</h4><strong class="mm10-grade mm10-grade--' . esc_attr( strtolower( (string) $kpi['grade'] ) ) . '">' . esc_html( (string) $kpi['grade'] ) . '</strong></div>';
    echo '<div class="mm10-progress-kpi"><h4>Attendance</h4><strong>' . esc_html( (string) $kpi['attendance_rate'] ) . '%</strong></div>';
    echo '<div class="mm10-progress-kpi"><h4>Appearances</h4><strong>' . esc_html( (string) $kpi['appearances'] ) . '</strong></div>';
    echo '<div class="mm10-progress-kpi"><h4>Goals</h4><strong>' . esc_html( (string) $kpi['goals'] ) . '</strong></div>';
    echo '<div class="mm10-progress-kpi"><h4>Assists</h4><strong>' . esc_html( (string) $kpi['assists'] ) . '</strong></div>';
    echo '<div class="mm10-progress-kpi"><h4>Discipline</h4><strong>' . esc_html( (string) $kpi['discipline'] ) . '</strong></div>';
    echo '</div>';

    if ( ! empty( $details ) ) {
        echo '<div class="mm10-progress-panel">';
        echo '<h3 class="mm10-progress-panel-title">Player Snapshot</h3>';
        echo '<div class="mm10-progress-detail-grid">';
        foreach ( $details as $label => $value ) {
            if ( '' === trim( (string) $value ) ) {
                continue;
            }
            echo '<div class="mm10-progress-detail-item"><span>' . esc_html( (string) $label ) . '</span><strong>' . esc_html( (string) $value ) . '</strong></div>';
        }
        echo '</div>';
        echo '</div>';
    }

    echo '<div class="mm10-progress-panel">';
    echo '<h3 class="mm10-progress-panel-title">Top Performance Metrics</h3>';
    if ( empty( $payload['totals'] ) ) {
        echo '<p class="mm10-muted">No SportsPress statistics have been recorded yet for this player.</p>';
    } else {
        echo '<div class="mm10-progress-table-wrap">';
        echo '<table class="mm10-account-table mm10-progress-table">';
        echo '<thead><tr><th>Metric</th><th>Total</th></tr></thead><tbody>';
        $rank = 0;
        foreach ( $payload['totals'] as $metric => $value ) {
            $rank++;
            if ( $rank > 12 ) {
                break;
            }
            echo '<tr><td>' . esc_html( mm10_format_stat_label( $metric ) ) . '</td><td>' . esc_html( (string) round( (float) $value, 2 ) ) . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
    echo '</div>';

    if ( ! empty( $payload['metrics'] ) ) {
        echo '<div class="mm10-progress-panel">';
        echo '<h3 class="mm10-progress-panel-title">Coach Metrics</h3>';
        echo '<div class="mm10-progress-metric-chips">';
        foreach ( $payload['metrics'] as $metric ) {
            echo '<span class="mm10-progress-chip">' . esc_html( (string) $metric['label'] ) . ': <strong>' . esc_html( (string) $metric['value'] ) . '</strong></span>';
        }
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
} );

add_action( 'woocommerce_account_faq_endpoint', function() {
    $support_url = wc_get_endpoint_url( 'support', '', wc_get_page_permalink( 'myaccount' ) );
    $shop_url    = get_permalink( wc_get_page_id( 'shop' ) );
    $wa_url      = 'https://wa.me/60132061010?text=' . rawurlencode( 'Hi MM10 Academy, I need quick help.' );

    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-editor-help"></span> FAQ</h2>';
    echo '<div class="mm10-faq-list">';
    echo '<details class="mm10-faq-item" open><summary>How do I register for a training program?</summary><p>Go to <a href="' . esc_url( $shop_url ) . '">Programs</a>, choose the package, and complete checkout. You will receive your order confirmation instantly.</p></details>';
    echo '<details class="mm10-faq-item"><summary>How long does membership activation take?</summary><p>Membership is usually activated within 24 hours after payment verification.</p></details>';
    echo '<details class="mm10-faq-item"><summary>Where can I see my orders and payment status?</summary><p>Open <strong>My Account &gt; Orders</strong> to view order details, totals, and current payment status.</p></details>';
    echo '<details class="mm10-faq-item"><summary>How do I check training schedules?</summary><p>Open <strong>My Account &gt; Training Schedule</strong> to view upcoming sessions and event details.</p></details>';
    echo '<details class="mm10-faq-item"><summary>Can I contact MM10 Academy immediately?</summary><p>Yes. Use our WhatsApp support for instant assistance during operating hours.</p></details>';
    echo '<details class="mm10-faq-item"><summary>How can I update my account details?</summary><p>Open <strong>My Account &gt; Account details</strong> to edit your profile information.</p></details>';
    echo '</div>';
    echo '<div class="mm10-faq-actions">';
    echo '<a href="' . esc_url( $wa_url ) . '" class="mm10-faq-btn mm10-faq-btn--whatsapp" target="_blank" rel="noopener">WhatsApp Support</a>';
    echo '<a href="' . esc_url( $support_url ) . '" class="mm10-faq-btn">Go To Support</a>';
    echo '</div></div>';
} );

add_action( 'woocommerce_account_support_endpoint', function() {
    $default_whatsapp = '60132061010';
    $raw_whatsapp = apply_filters( 'mm10_whatsapp_number', $default_whatsapp );
    $wa_number    = preg_replace( '/\D+/', '', (string) $raw_whatsapp );
    $wa_message   = rawurlencode( 'Hi MM10 Academy, I need support with my account.' );
    $wa_url       = $wa_number ? 'https://wa.me/' . $wa_number . '?text=' . $wa_message : 'https://wa.me/?text=' . $wa_message;

    $faq_url = wc_get_endpoint_url( 'faq', '', wc_get_page_permalink( 'myaccount' ) );

    echo '<div class="mm10-endpoint-wrap">';
    echo '<h2 class="mm10-endpoint-title"><span class="dashicons dashicons-email-alt"></span> Support</h2>';
    echo '<div class="mm10-support-grid">';
    echo '<a href="' . esc_url( home_url( '/contact/' ) ) . '" class="mm10-support-card">';
    echo '<span class="dashicons dashicons-email-alt"></span><h3>Contact Us</h3><p>Send us a message and we\'ll reply within 24 hours.</p></a>';
    echo '<a href="mailto:info@mm10academy.com" class="mm10-support-card">';
    echo '<span class="dashicons dashicons-phone"></span><h3>Email Directly</h3><p>info@mm10academy.com</p></a>';
    echo '<a href="' . esc_url( $faq_url ) . '" class="mm10-support-card">';
    echo '<span class="dashicons dashicons-editor-help"></span><h3>FAQ Center</h3><p>Find quick answers for MM10 Academy.</p></a>';
    echo '<a href="' . esc_url( $wa_url ) . '" class="mm10-support-card mm10-support-card--whatsapp" target="_blank" rel="noopener">';
    echo '<span class="dashicons dashicons-format-chat"></span><h3>WhatsApp Now</h3><p>Instant connect for quick support.</p></a>';
    echo '</div></div>';
} );

// Redirect legacy /faq/ requests to My Account FAQ endpoint.
add_action( 'template_redirect', function() {
    if ( ! is_404() ) {
        return;
    }

    $request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
    $request_path = trim( (string) $request_path, '/' );

    if ( '' === $request_path ) {
        return;
    }

    $home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
    if ( '' !== $home_path && 0 === strpos( $request_path, $home_path . '/' ) ) {
        $request_path = substr( $request_path, strlen( $home_path ) + 1 );
    }

    if ( 'faq' !== $request_path ) {
        return;
    }

    $faq_endpoint_url = wc_get_endpoint_url( 'faq', '', wc_get_page_permalink( 'myaccount' ) );
    wp_safe_redirect( $faq_endpoint_url, 301 );
    exit;
} );

// =========================================================================
// SPORTSPRESS: Homepage Results Shortcode [mm10_latest_results]

// =========================================================================
// WOOCOMMERCE: Premium Shop Page — Hero + Divider + Filter Bar
// Hooked on astra_content_before = OUTSIDE the ast-container div,
// so these elements span the full viewport width.
// =========================================================================
add_action( 'astra_content_before', function() {
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }
    ?>
    <div class="mm10-shop-hero">
        <div class="mm10-shop-hero-inner">
            <div class="mm10-hero-text">
                <h1>Train Like a <span>Champion</span></h1>
                <p>UEFA &amp; Coerver certified coaching programs for young players aged 4–18. Choose your path and start your football journey today.</p>
                <div class="mm10-shop-stats">
                    <div class="mm10-shop-stat">
                        <strong>500+</strong>
                        <span>Players Trained</span>
                    </div>
                    <div class="mm10-shop-stat">
                        <strong>UEFA</strong>
                        <span>Certified Coaches</span>
                    </div>
                    <div class="mm10-shop-stat">
                        <strong>12+</strong>
                        <span>Years Experience</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mm10-shop-hero-divider"></div>
    <?php

    // Category filter bar.
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_option( 'default_product_cat' ) ),
        'orderby'    => 'count',
        'order'      => 'DESC',
    ) );

    $current_cat = is_product_category() ? get_queried_object_id() : 0;
    $shop_url    = get_permalink( wc_get_page_id( 'shop' ) );

    echo '<div class="mm10-shop-filters">';
    echo '<div class="mm10-filters-left">';
    echo '<a href="' . esc_url( $shop_url ) . '" class="mm10-filter-btn' . ( ! $current_cat ? ' active' : '' ) . '">All Programs</a>';
    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        foreach ( $categories as $cat ) {
            $active = ( $current_cat === $cat->term_id ) ? ' active' : '';
            echo '<a href="' . esc_url( get_term_link( $cat ) ) . '" class="mm10-filter-btn' . $active . '">' . esc_html( $cat->name ) . '</a>';
        }
    }
    echo '</div>';
    // Sorting dropdown inside filter bar.
    echo '<div class="mm10-filters-right">';
    woocommerce_catalog_ordering();
    echo '</div>';
    echo '</div>';
});

// Remove the default WooCommerce sorting from shop loop (we moved it to filter bar).
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

// Grid wrapper stays inside Astra container (where products live).
add_action( 'woocommerce_before_shop_loop', function() {
    echo '<div class="mm10-shop-grid-wrap">';
}, 15 );
add_action( 'woocommerce_after_shop_loop', function() {
    echo '</div>'; // Close .mm10-shop-grid-wrap
}, 5 );

// =========================================================================
// WOOCOMMERCE: Premium Shop Page — Product Card Enhancements
// =========================================================================

// Tell Astra to skip its own shop product card structure (title, price,
// add-to-cart, thumbnail wrap). We rebuild everything ourselves below.
add_filter( 'astra_woo_shop_product_structure_override', '__return_true' );

// Remove default WooCommerce shop loop elements and rebuild.
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 15 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

// Custom product card output.
add_action( 'woocommerce_before_shop_loop_item', function() {
    global $product;

    // Category badge.
    $cats = get_the_terms( $product->get_id(), 'product_cat' );
    $cat_name = '';
    if ( $cats && ! is_wp_error( $cats ) ) {
        foreach ( $cats as $cat ) {
            if ( $cat->slug !== 'uncategorized' ) {
                $cat_name = $cat->name;
                break;
            }
        }
    }

    // Product image link + overlay.
    echo '<a href="' . esc_url( get_the_permalink() ) . '" class="mm10-product-image-link">';
    // Ribbon for popular/best-value products.
    $sku = $product->get_sku();
    $ribbon = '';
    if ( $sku === 'MM10-HF-12S' ) {
        $ribbon = 'Most Popular';
    } elseif ( $sku === 'MM10-HF-3SIB' ) {
        $ribbon = 'Best Value';
    } elseif ( $sku === 'MM10-DROP' ) {
        $ribbon = 'Try First';
    }

    if ( $cat_name || $ribbon ) {
        echo '<div class="mm10-product-badge-row">';
        if ( $cat_name ) {
            echo '<span class="product-category-badge">' . esc_html( $cat_name ) . '</span>';
        }
        if ( $ribbon ) {
            echo '<span class="mm10-product-ribbon">' . esc_html( $ribbon ) . '</span>';
        }
        echo '</div>';
    }

    // Product thumbnail.
    echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' );
    echo '</a>';

    // Start product info area.
    echo '<div class="mm10-product-info">';
}, 5 );

// Product title.
add_action( 'woocommerce_shop_loop_item_title', function() {
    echo '<a href="' . esc_url( get_the_permalink() ) . '">';
    echo '<h2 class="woocommerce-loop-product__title">' . get_the_title() . '</h2>';
    echo '</a>';
}, 10 );

// Product meta (duration, ages).
add_action( 'woocommerce_after_shop_loop_item_title', function() {
    global $product;
    $sku = $product->get_sku();

    $meta = array();
    // Map SKUs to meta info.
    $program_meta = array(
        'MM10-HF-12S'  => array( 'duration' => '12 weeks', 'age' => 'Ages 4–10' ),
        'MM10-HF-4S'   => array( 'duration' => '4 weeks', 'age' => 'Ages 4–10' ),
        'MM10-PP-U10'  => array( 'duration' => '12 weeks', 'age' => 'Under 10' ),
        'MM10-HF-2SIB' => array( 'duration' => '12 weeks', 'age' => '2 Siblings' ),
        'MM10-HF-3SIB' => array( 'duration' => '12 weeks', 'age' => '3 Siblings' ),
        'MM10-DROP'    => array( 'duration' => 'Per session', 'age' => 'Ages 4–10' ),
        'MM10-REG'     => array( 'duration' => 'One-time', 'age' => 'Full Kit' ),
        'MM10-PVT'     => array( 'duration' => 'Per session', 'age' => 'All Ages' ),
    );

    if ( isset( $program_meta[ $sku ] ) ) {
        $meta = $program_meta[ $sku ];
    }

    if ( $meta ) {
        echo '<div class="mm10-product-meta">';
        if ( ! empty( $meta['duration'] ) ) {
            echo '<span class="mm10-product-meta-item"><span class="dashicons dashicons-clock"></span>' . esc_html( $meta['duration'] ) . '</span>';
        }
        if ( ! empty( $meta['age'] ) ) {
            echo '<span class="mm10-product-meta-item"><span class="dashicons dashicons-groups"></span>' . esc_html( $meta['age'] ) . '</span>';
        }
        echo '</div>';
    }
}, 5 );

// Price.
add_action( 'woocommerce_after_shop_loop_item_title', function() {
    global $product;
    echo '<div class="price">';
    if ( $product->is_type( 'variable' ) ) {
        echo '<span class="price-prefix">From</span> ';
    }
    echo $product->get_price_html();
    echo '</div>';
}, 10 );

// Close product info div + add to cart button.
add_action( 'woocommerce_after_shop_loop_item', function() {
    echo '</div>'; // Close .mm10-product-info
    woocommerce_template_loop_add_to_cart();
}, 10 );

// Load dashicons on shop pages for product meta icons.
add_action( 'wp_enqueue_scripts', function() {
    if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_category() ) ) {
        wp_enqueue_style( 'dashicons' );
    }
}, 20 );

// Change "Select options" text to "View Options" for variable products.
add_filter( 'woocommerce_product_add_to_cart_text', function( $text, $product ) {
    if ( $product->is_type( 'variable' ) ) {
        return __( 'Select Package', 'astra-child-mm10' );
    }
    return $text;
}, 10, 2 );

// Set shop products per page (show all 8).
add_filter( 'loop_shop_per_page', function() {
    return 12;
});

// Set shop columns.
add_filter( 'loop_shop_columns', function() {
    return 4;
});

/* ==========================================================================
   SINGLE PRODUCT — Premium enhancements
   ========================================================================== */

// Load dashicons on single product pages too.
add_action( 'wp_enqueue_scripts', function() {
    if ( function_exists( 'is_product' ) && is_product() ) {
        wp_enqueue_style( 'dashicons' );
    }
}, 21 );

// Remove breadcrumb on single product pages.
add_action( 'wp', function() {
    if ( function_exists( 'is_product' ) && is_product() ) {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
        add_filter( 'woocommerce_get_breadcrumb', '__return_empty_array' );
    }
});

// Variation table border reset is handled in style.css to avoid first-paint flicker.

// Trust badges below add-to-cart.
add_action( 'woocommerce_single_product_summary', function() {
    echo '<div class="mm10-trust-badges">';
    echo '<div class="mm10-trust-badge"><span class="dashicons dashicons-shield"></span>Secure Payment</div>';
    echo '<div class="mm10-trust-badge"><span class="dashicons dashicons-yes-alt"></span>Instant Confirmation</div>';
    echo '<div class="mm10-trust-badge"><span class="dashicons dashicons-groups"></span>Licensed Coaches</div>';
    echo '</div>';
}, 35 );

// Ensure single product uses plain container (no sidebar).
add_filter( 'astra_get_content_layout', function( $layout ) {
    if ( function_exists( 'is_product' ) && is_product() ) {
        return 'plain-container';
    }
    return $layout;
}, 99 );

// Remove upsells ("You may also like...") from single product page.
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

// Change "Related products" heading.
add_filter( 'woocommerce_product_related_products_heading', function() {
    return __( 'More Lessons You May Choose', 'astra-child-mm10' );
});

// Redirect to cart page after successful "Add to Cart".
add_filter( 'woocommerce_add_to_cart_redirect', function( $url ) {
    if ( function_exists( 'wc_get_cart_url' ) ) {
        return wc_get_cart_url();
    }
    return $url;
}, 99 );

// Remove cross-sells ("You May Be Interested In...") from cart page.
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/* ==========================================================================
   SHORTCODE: Google Review CTA card
   Usage: [mm10_google_review_cta]
   ========================================================================== */
add_shortcode( 'mm10_google_review_cta', function( $atts ) {
    $default_reviews_url = 'https://www.google.com/search?q=MM10+Football+Academy+Petaling+Jaya+reviews';

    $atts = shortcode_atts(
        array(
            'rating'      => '4.9/5 Parent Rating on Google',
            'copy'        => 'Trusted by families in Petaling Jaya and across Klang Valley.',
            'reviews_url' => $default_reviews_url,
            'trial_url'   => home_url( '/contact-us/' ),
        ),
        $atts,
        'mm10_google_review_cta'
    );

    $reviews_url = trim( $atts['reviews_url'] );
    if ( '' === $reviews_url || false !== strpos( $reviews_url, '...' ) ) {
        $reviews_url = $default_reviews_url;
    }

    ob_start();
    ?>
    <section class="mm10-review-section" aria-label="Google Reviews and Call to Action">
        <div class="mm10-hero-review-card">
            <div class="mm10-hero-review-stars" aria-label="Five star rating">★★★★★</div>
            <div class="mm10-hero-review-score"><?php echo esc_html( $atts['rating'] ); ?></div>
            <div class="mm10-hero-review-copy"><?php echo esc_html( $atts['copy'] ); ?></div>
            <div class="mm10-hero-review-actions">
                <a class="mm10-hero-review-btn mm10-hero-review-btn--ghost" href="<?php echo esc_url( $reviews_url ); ?>" target="_blank" rel="noopener">
                    <span class="mm10-google-mark" aria-hidden="true">
                        <svg viewBox="0 0 48 48" focusable="false" role="img">
                            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20c10 0 19-7.3 19-20 0-1.3-.1-2.4-.4-3.5z"/>
                            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 16.2 4 9.5 8.5 6.3 14.7z"/>
                            <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.6 5.1C9.3 39.6 16 44 24 44z"/>
                            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.1 5.6l6.2 5.2C37 39.1 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/>
                        </svg>
                    </span>
                    <span>Read Reviews</span>
                </a>
                <a class="mm10-hero-review-btn mm10-hero-review-btn--solid" href="<?php echo esc_url( $atts['trial_url'] ); ?>">Book Free Trial</a>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
} );
