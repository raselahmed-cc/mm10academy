<?php

if ( 'cli' !== PHP_SAPI ) {
    fwrite( STDERR, "This script must be run from the command line.\n" );
    exit( 1 );
}

require_once dirname( __DIR__ ) . '/wp-load.php';

$args = array_slice( $argv, 1 );
$output_path = dirname( __DIR__ ) . '/ops/beaver-layouts-export.json';

foreach ( $args as $arg ) {
    if ( 0 === strpos( $arg, '--output=' ) ) {
        $output_path = substr( $arg, 9 );
    }
}

$meta_keys = array(
    '_fl_builder_enabled',
    '_fl_builder_data',
    '_fl_builder_draft',
    '_fl_builder_data_settings',
);

$post_ids = get_posts(
    array(
        'post_type'      => 'any',
        'post_status'    => array( 'publish', 'draft', 'private', 'future', 'pending' ),
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'   => '_fl_builder_enabled',
                'value' => '1',
            ),
        ),
        'orderby'        => 'ID',
        'order'          => 'ASC',
    )
);

$records = array();
foreach ( $post_ids as $post_id ) {
    $post = get_post( $post_id );
    if ( ! ( $post instanceof WP_Post ) ) {
        continue;
    }

    $record = array(
        'ID'          => (int) $post->ID,
        'post_type'   => (string) $post->post_type,
        'post_name'   => (string) $post->post_name,
        'post_title'  => (string) $post->post_title,
        'post_status' => (string) $post->post_status,
        'meta'        => array(),
    );

    foreach ( $meta_keys as $meta_key ) {
        $record['meta'][ $meta_key ] = get_post_meta( $post->ID, $meta_key, true );
    }

    $records[] = $record;
}

$export = array(
    'generated_at_gmt' => gmdate( 'c' ),
    'site_url'         => home_url( '/' ),
    'show_on_front'    => get_option( 'show_on_front' ),
    'page_on_front'    => (int) get_option( 'page_on_front' ),
    'records'          => $records,
);

$json = wp_json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
if ( false === $json ) {
    fwrite( STDERR, "Failed to encode export payload.\n" );
    exit( 1 );
}

$dir = dirname( $output_path );
if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
    fwrite( STDERR, "Failed to create output directory: {$dir}\n" );
    exit( 1 );
}

if ( false === file_put_contents( $output_path, $json ) ) {
    fwrite( STDERR, "Failed to write export file: {$output_path}\n" );
    exit( 1 );
}

fwrite( STDOUT, "Exported " . count( $records ) . " Beaver Builder posts to {$output_path}\n" );