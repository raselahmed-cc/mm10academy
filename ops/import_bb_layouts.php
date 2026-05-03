<?php

if ( 'cli' !== PHP_SAPI ) {
    fwrite( STDERR, "This script must be run from the command line.\n" );
    exit( 1 );
}

require_once dirname( __DIR__ ) . '/wp-load.php';

$args = array_slice( $argv, 1 );
$input_path = '';
$apply      = false;

foreach ( $args as $arg ) {
    if ( 0 === strpos( $arg, '--input=' ) ) {
        $input_path = substr( $arg, 8 );
    } elseif ( '--apply' === $arg ) {
        $apply = true;
    }
}

if ( '' === $input_path ) {
    fwrite( STDERR, "Usage: php ops/import_bb_layouts.php --input=/path/to/beaver-layouts-export.json [--apply]\n" );
    exit( 1 );
}

if ( ! is_file( $input_path ) || ! is_readable( $input_path ) ) {
    fwrite( STDERR, "Input file not found or not readable: {$input_path}\n" );
    exit( 1 );
}

$payload = json_decode( (string) file_get_contents( $input_path ), true );
if ( ! is_array( $payload ) || ! isset( $payload['records'] ) || ! is_array( $payload['records'] ) ) {
    fwrite( STDERR, "Invalid Beaver layout export payload.\n" );
    exit( 1 );
}

$meta_keys = array(
    '_fl_builder_enabled',
    '_fl_builder_data',
    '_fl_builder_draft',
    '_fl_builder_data_settings',
);

function mm10_find_import_target_post( array $record ) {
    $post_id = isset( $record['ID'] ) ? (int) $record['ID'] : 0;
    if ( $post_id > 0 ) {
        $post = get_post( $post_id );
        if ( $post instanceof WP_Post ) {
            return $post;
        }
    }

    if ( empty( $record['post_name'] ) || empty( $record['post_type'] ) ) {
        return null;
    }

    $post = get_page_by_path( (string) $record['post_name'], OBJECT, (string) $record['post_type'] );

    return $post instanceof WP_Post ? $post : null;
}

$report = array();
foreach ( $payload['records'] as $record ) {
    if ( ! is_array( $record ) ) {
        continue;
    }

    $target_post = mm10_find_import_target_post( $record );
    $row = array(
        'source_id' => isset( $record['ID'] ) ? (int) $record['ID'] : 0,
        'target_id' => $target_post instanceof WP_Post ? (int) $target_post->ID : 0,
        'title'     => isset( $record['post_title'] ) ? (string) $record['post_title'] : '',
        'status'    => $target_post instanceof WP_Post ? 'matched' : 'missing',
    );

    if ( ! $apply || ! ( $target_post instanceof WP_Post ) ) {
        $report[] = $row;
        continue;
    }

    foreach ( $meta_keys as $meta_key ) {
        if ( ! array_key_exists( $meta_key, $record['meta'] ?? array() ) ) {
            continue;
        }

        $backup_key = sprintf( '_mm10_backup_%s_%s', trim( $meta_key, '_' ), gmdate( 'Ymd_His' ) );
        $current_value = get_post_meta( $target_post->ID, $meta_key, true );
        if ( '' !== $current_value && array() !== $current_value ) {
            add_post_meta( $target_post->ID, $backup_key, $current_value );
        }

        update_post_meta( $target_post->ID, $meta_key, $record['meta'][ $meta_key ] );
    }

    clean_post_cache( $target_post->ID );
    $row['status'] = 'updated';
    $report[] = $row;
}

if ( $apply && class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'delete_asset_cache_for_all_posts' ) ) {
    FLBuilderModel::delete_asset_cache_for_all_posts();
}

if ( $apply && function_exists( 'wp_cache_flush' ) ) {
    wp_cache_flush();
}

fwrite( STDOUT, ( $apply ? 'Applied' : 'Dry-run checked' ) . ' ' . count( $report ) . " Beaver Builder records\n" );
foreach ( $report as $row ) {
    fwrite(
        STDOUT,
        sprintf(
            "- source:%d target:%d status:%s title:%s\n",
            $row['source_id'],
            $row['target_id'],
            $row['status'],
            $row['title']
        )
    );
}