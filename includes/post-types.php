<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file includes functions to which register post types and taxonomies
 *
 * @since             1.0.0
 * @package           Package_Ordering_System
 */

/**
 * Register post types 'package' and 'package-order'
 * 
 * @since       1.0.0
 */
add_action( 'init', 'create_pack_os_post_types' );
if( !function_exists('create_pack_os_post_types')){
    function create_pack_os_post_types(){

        $pos_labels = array(
            'name'                  => _x( 'Packages', 'post type general name', 'package-os' ),
            'singular_name'         => _x( 'Package', 'post type singular name', 'package-os' ),
            'menu_name'             => _x( 'Packages', 'admin menu', 'package-os' ),
            'name_admin_bar'        => _x( 'Package', 'add new on admin bar', 'package-os' ),
            'add_new'               => _x( 'Add New', 'Package', 'package-os' ),
            'add_new_item'          => __( 'Add New Package', 'package-os' ),
            'new_item'              => __( 'New Package', 'package-os' ),
            'edit_item'             => __( 'Edit Package', 'package-os' ),
            'view_item'             => __( 'View Package', 'package-os' ),
            'all_items'             => __( 'All Packages', 'package-os' ),
            'search_items'          => __( 'Search Packages', 'package-os' ),
            'parent_item_colon'     => __( 'Parent Packages:', 'package-os' ),
            'not_found'             => __( 'No Packages found.', 'package-os' ),
            'not_found_in_trash'    => __( 'No Packages found in Trash.', 'package-os' )
        );

        $pos_order_labels = array(
            'name'                  => _x( 'Orders', 'post type general name', 'package-os' ),
            'singular_name'         => _x( 'Order', 'post type singular name', 'package-os' ),
            'menu_name'             => _x( 'Orders', 'admin menu', 'package-os' ),
            'name_admin_bar'        => _x( 'Order', 'add new on admin bar', 'package-os' ),
            'add_new'               => _x( 'Add New', 'Order', 'package-os' ),
            'add_new_item'          => __( 'Add New Order', 'package-os' ),
            'new_item'              => __( 'New Order', 'package-os' ),
            'edit_item'             => __( 'Edit Order', 'package-os' ),
            'view_item'             => __( 'View Order', 'package-os' ),
            'all_items'             => __( 'All Orders', 'package-os' ),
            'search_items'          => __( 'Search Orders', 'package-os' ),
            'parent_item_colon'     => __( 'Parent Orders:', 'package-os' ),
            'not_found'             => __( 'No Orders found.', 'package-os' ),
            'not_found_in_trash'    => __( 'No Orders found in Trash.', 'package-os' )
        );

        if ( function_exists( 'members_get_capabilities' ) ) {
	
			$pos_capabilities = array(
		
				'edit_post'          => 'pos_edit_package',
				'edit_posts'         => 'pos_edit_packages',
				'edit_others_posts'  => 'pos_edit_others_packages',
				'publish_posts'      => 'pos_publish_packages',
				'read_post'          => 'pos_read_package',
				'read_private_posts' => 'pos_read_private_packages',
				'delete_post'        => 'pos_delete_package',
				'delete_posts'       => 'pos_delete_packages'

            );
            
            $pos_order_capabilities = array(
        
				'edit_post'          => 'pos_edit_order',
				'edit_posts'         => 'pos_edit_orders',
				'edit_others_posts'  => 'pos_edit_others_orders',
				'publish_posts'      => 'pos_publish_orders',
				'read_post'          => 'pos_read_order',
				'read_private_posts' => 'pos_read_private_orders',
				'delete_post'        => 'pos_delete_order',
				'delete_posts'       => 'pos_delete_orders'

			);
			
			$pos_capabilitytype = PACK_OS_CPT;
			$pos_order_capabilitytype = PACK_OS_ORDER_CPT;
			
			$pos_mapmetacap = false;
			$pos_order_mapmetacap = false;
		
		} else {
		
			$pos_capabilities = array(
		
				'edit_post'          => 'edit_post',
				'edit_posts'         => 'edit_posts',
				'edit_others_posts'  => 'edit_others_posts',
				'publish_posts'      => 'publish_posts',
				'read_post'          => 'read_post',
				'read_private_posts' => 'read_private_posts',
				'delete_post'        => 'delete_post',
				'delete_posts'       => 'delete_posts'

            );
            
            $pos_order_capabilities = array(
		
				'edit_post'          => 'edit_post',
				'edit_posts'         => 'edit_posts',
				'edit_others_posts'  => 'edit_others_posts',
				'publish_posts'      => 'publish_posts',
				'read_post'          => 'read_post',
				'read_private_posts' => 'read_private_posts',
				'delete_post'        => 'delete_post',
				'delete_posts'       => 'delete_posts'

			);
			
			$pos_capabilitytype = 'post';
			$pos_order_capabilitytype = 'post';
			
			$pos_mapmetacap = true;
			$pos_order_mapmetacap = true;
		
		}
    
        $pos_args = array(
            'labels'                => $pos_labels,
            'public'                => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'query_var'             => false,
            'rewrite'               => false,
            'capability_type'       => $pos_capabilitytype,
			'capabilities'          => $pos_capabilities,
			'map_meta_cap'          => $pos_mapmetacap,
            'hierarchical'          => false,
            'menu_position'         => 50.22,
            'supports'              => array( 'title' ),
            'menu_icon'             => 'dashicons-cloud'
        );

        $pos_order_args = array(
            'labels'                => $pos_order_labels,
            'public'                => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'query_var'             => false,
            'rewrite'               => false,
            'capability_type'       => $pos_order_capabilitytype,
			'capabilities'          => $pos_order_capabilities,
			'map_meta_cap'          => $pos_order_mapmetacap,
            'hierarchical'          => false,
            'show_in_admin_bar'     => false,
            'show_in_menu'          => 'edit.php?post_type='.PACK_OS_CPT,
            'supports'              => array( 'title' ),
            'menu_icon'             => 'dashicons-cloud'
        );
    
        register_post_type( PACK_OS_CPT, $pos_args );
        register_post_type( PACK_OS_ORDER_CPT, $pos_order_args );

    }
}


/**
 * Register custom taxonomy 'package-option'
 * 
 * @since       1.0.0
 */
add_action( 'init', 'create_pack_os_taxonomies' );
if( !function_exists('create_pack_os_taxonomies')){
    function create_pack_os_taxonomies(){

        $pos_option_labels = array(
            'name'                          => _x( 'Options', 'taxonomy general name', 'package-os' ),
            'singular_name'                 => _x( 'Option', 'taxonomy singular name', 'package-os' ),
            'search_items'                  => __( 'Search Options', 'package-os' ),
            'all_items'                     => __( 'All Options', 'package-os' ),
            'parent_item'                   => __( 'Parent Option', 'package-os' ),
            'parent_item_colon'             => __( 'Parent Option:', 'package-os' ),
            'edit_item'                     => __( 'Edit Option', 'package-os' ),
            'update_item'                   => __( 'Update Option', 'package-os' ),
            'add_new_item'                  => __( 'Add New Option', 'package-os' ),
            'new_item_name'                 => __( 'New Option Name', 'package-os' ),
            'menu_name'                     => __( 'Options', 'package-os' ),
            'not_found'                     => __( 'No Options found.', 'package-os' ),
            'separate_items_with_commas'    => __( 'Separete options with commas', 'package-os' ),
            'choose_from_most_used'         => __( 'Choose from the most used options', 'package-os' ),
            'back_to_items'                 => __( 'Back to Options', 'package-os' ),
            'not_found'                     => __( 'No options found.', 'package-os' ),
        );

        if ( function_exists( 'members_get_capabilities' ) ) {
	
			$pos_tax_capabilities = array(
		
				'manage_terms' => 'pos_manage_options',
				'edit_terms'   => 'pos_manage_options',
				'delete_terms' => 'pos_manage_options',
				'assign_terms' => 'pos_edit_packages'

			);
		
		} else {
		
			$pos_tax_capabilities = array(
		
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_posts'

			);
		
		}
    
        $pos_option_args = array(
            'hierarchical'      => false,
            'labels'            => $pos_option_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'public'            => false,
            'query_var'         => false,
            'rewrite'           => false,
            'capabilities'      => $pos_tax_capabilities
        );
    
        register_taxonomy( PACK_OS_CT, array( PACK_OS_CPT ), $pos_option_args );

    }
}