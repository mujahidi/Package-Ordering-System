<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file includes general functions and hooks for the plugin
 *
 * @since             1.0.0
 * @package           Package_Ordering_System
 */

/**
 * Sends back error in JSON format to JS and exits the wp_ajax script
 * 
 * @since       1.0.0
 */
function pos_json_error( $result, $type, $msg, $die = true ){

    $json_result = array( 
        'result' => $result, 
        'type' => $type, 
        'error' => esc_html__( $msg, 'package-os' )
    );

    echo json_encode( $json_result );

    if( $die ) die;

}

/**
 * Add custom columns to Package CPT on the manage packages screen
 * 
 * @since       1.0.0
 */
add_filter( 'manage_'.PACK_OS_CPT.'_posts_columns', 'pos_filter_package_columns' );
function pos_filter_package_columns( $columns ) {

    $columns = array(
        'cb' => $columns['cb'],
        'title' => __( 'Title', 'package-os' ),
        'price' => __( 'Price', 'package-os' ),
        'addons' => __( 'Addons', 'package-os' ),
        'taxonomy-package-option' => __( 'Options', 'package-os' ),
        'date' => __( 'Date', 'package-os' ),
    );

    return $columns;

}

/**
 * Add custom columns to Package Order CPT on the manage orders screen
 * 
 * @since       1.0.0
 */
add_filter( 'manage_'.PACK_OS_ORDER_CPT.'_posts_columns', 'pos_filter_package_order_columns' );
function pos_filter_package_order_columns( $columns ) {

    $columns = array(
        'cb' => $columns['cb'],
        'title' => __( 'Title', 'package-os' ),
        'total_price' => __( 'Total Price', 'package-os' ),
        'date' => __( 'Date', 'package-os' ),
    );

    return $columns;

}

/**
 * Populating columns to Package CPT on the manage packages screen
 * 
 * @since       1.0.0
 */
add_action( 'manage_'.PACK_OS_CPT.'_posts_custom_column', 'pos_package_column', 10, 2);
function pos_package_column( $column, $post_id ) {

    $pos_options = get_option('pack_os_settings');
    $price = get_post_meta( $post_id, 'pos_price', true );
    $addons = get_post_meta( $post_id, 'pos_addons', true );

    if ( 'price' === $column && $price ) {
        echo $pos_options['currency_prefix'].$price.$pos_options['currency_postfix'];
    }

    if( 'addons' === $column && $addons ){
        echo count( $addons );
    }

}

/**
 * Populating columns to Package CPT on the manage packages screen
 * 
 * @since       1.0.0
 */
add_action( 'manage_'.PACK_OS_ORDER_CPT.'_posts_custom_column', 'pos_package_order_column', 10, 2);
function pos_package_order_column( $column, $post_id ) {

    $pos_options = get_option('pack_os_settings');
    $order_packs = maybe_unserialize( get_post_meta( $post_id, 'pos_order_packs', true ) );
    $order_addons = maybe_unserialize( get_post_meta( $post_id, 'pos_order_addons', true ) );

    $packs_price = $addons_price = 0;

    if( is_array( $order_packs ) )
        $packs_price = array_sum( $order_packs );

    if( is_array( $order_addons ) )
        $addons_price = array_sum( $order_addons );

    $total_price = $packs_price + $addons_price;

    if ( 'total_price' === $column ) {
        echo $pos_options['currency_prefix'].number_format($total_price, 2).$pos_options['currency_postfix'];
    }

}

/**
 * Adding 'price' column to the list of sortable columns on the manage packages screen
 * 
 * @since       1.0.0
 */
add_filter( 'manage_edit-'.PACK_OS_CPT.'_sortable_columns', 'pos_sortable_columns');
function pos_sortable_columns( $columns ) {
    $columns['price'] = 'price';
    return $columns;
}

/**
 * Making Columns Sortable to Package CPT on the manage packages screen
 * 
 * @since       1.0.0
 */
add_action( 'pre_get_posts', 'pos_packages_orderby' );
function pos_packages_orderby( $query ) {
    if( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( 'price' === $query->get( 'orderby') ) {
        $query->set( 'orderby', 'meta_value' );
        $query->set( 'meta_key', 'pos_price' );
        $query->set( 'meta_type', 'numeric' );
    }
}