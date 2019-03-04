<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file includes functions to enqueue plugin styles and scripts
 *
 * @since             1.0.0
 * @package           Package_Ordering_System
 */

/**
 * Enqueue font-end styles and scripts
 * 
 * @since       1.0.0
 */
add_action( 'wp_enqueue_scripts', 'pack_os_scripts' );
function pack_os_scripts() {

    global $post;

    // determine whether the page's content contains "[package_ordering_system]" shortcode
    if ( is_a( $post, 'WP_Post' ) && has_shortcode($post->post_content, 'package_ordering_system') ) {

        $pos_options = get_option('pack_os_settings');
        
        wp_enqueue_style( 'package-ordering-system', plugins_url('assets/css/pack-os.css', dirname(__FILE__)), '', PACK_OS_VERSION );

        $user_defined_css = "";
        
        if( isset($pos_options['primary_color']) ){
            $user_defined_css .= "
                .package-ordering-container *{ color: {$pos_options['primary_color']}; }
                .package-ordering-container .package-ordering-opts .pos-opt-item.pos-opt-item-selected, 
                .package-ordering-container .package-ordering-packs .pos-pack-item.pos-pack-item-selected, 
                .package-ordering-container .package-ordering-addons .pos-addon-item.pos-addon-item-selected{
                    border-color: {$pos_options['primary_color']};
                }
                .pos-loader, .pos-loader span{ border-top-color: {$pos_options['primary_color']}; }
            ";
        }

        if( isset($pos_options['secondary_color']) ){
            $user_defined_css .= "
                .package-ordering-container .package-ordering-opts > li,
                .package-ordering-container .package-ordering-packs > li,
                .package-ordering-container .package-ordering-addons > li,
                .package-ordering-container .package-ordering-summary > li,
                .package-ordering-container .package-ordering-summary .pos-summary-item-name,
                .package-ordering-container .package-ordering-summary .pos-total-summary-item,
                .package-ordering-container .package-ordering-form .pos-form-field{ 
                    border-color: {$pos_options['secondary_color']}; 
                }
            ";
        }

        if( isset($pos_options['button_color']) ){
            $user_defined_css .= "
                .package-ordering-container .pos-button button{ 
                    border-color: {$pos_options['button_color']}; 
                    color: {$pos_options['button_color']};
                }
            ";
        }

        if( isset($pos_options['button_font_color']) ){
            $user_defined_css .= "
                .package-ordering-container .pos-button button{ 
                    color: {$pos_options['button_font_color']};
                }
            ";
        }

        if( isset($pos_options['button_hover_font_color']) ){
            $user_defined_css .= "
                .package-ordering-container .pos-button button:hover, .package-ordering-container .pos-button button.pos-button-active{
                    background: {$pos_options['button_color']};
                    color: {$pos_options['button_hover_font_color']};
                }
            ";
        }

        wp_add_inline_style( 'package-ordering-system', $user_defined_css );

        if ( ! wp_script_is( 'jquery', 'enqueued' )) {
            wp_enqueue_script( 'jquery' );
        }

        wp_register_script ( 'package-ordering-system-script', plugins_url('assets/js/pack-os.js', dirname(__FILE__)), array('jquery'), PACK_OS_VERSION, true );

        wp_localize_script( 'package-ordering-system-script', 'pack_os_currency', array(
            'prefix'    => $pos_options['currency_prefix'],
            'postfix'    => $pos_options['currency_postfix']
        ) );
        wp_localize_script( 'package-ordering-system-script', 'pack_os_get_package', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'options_nonce' => wp_create_nonce( 'pos_get_options_packages' ),
        ) );
        wp_localize_script( 'package-ordering-system-script', 'pack_os_get_addons', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'addons_nonce' => wp_create_nonce( 'pos_get_packages_addons' ),
        ) );

        wp_localize_script( 'package-ordering-system-script', 'pack_os_process_order', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            '_nonce' => wp_create_nonce( 'pos_process_order_'.date('Ymd') ),
        ) );

        wp_enqueue_script ( 'package-ordering-system-script' );

    }
    
}

/**
 * Enqueue admin styles and scripts
 * 
 * @since       1.0.0
 */
add_action( 'admin_enqueue_scripts', 'pack_os_admin_scripts' );
function pack_os_admin_scripts() {

    // get current admin screen, or null
    $screen = get_current_screen();
    // verify admin screen object
    if (is_object($screen)) {
        // enqueue only for specific post types
        if (in_array($screen->post_type, [PACK_OS_ORDER_CPT])) {
            
            wp_enqueue_style( 'package-ordering-system-admin', plugins_url('assets/css/pack-os-admin.css', dirname(__FILE__)), '', PACK_OS_VERSION );
            
        }
    }

}