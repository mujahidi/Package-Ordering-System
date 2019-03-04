<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/mujahidi/package-ordering-system
 * @since             1.0.0
 * @package           Package_Ordering_System
 *
 * @wordpress-plugin
 * Plugin Name:       Package Ordering System
 * Plugin URI:        https://github.com/mujahidi/package-ordering-system
 * Description:       A flexible but simple package ordering system for those who do not require e-commerce or billing functionality.
 * Version:           1.0.0
 * Author:            Mujahid Ishtiaq
 * Author URI:        https://github.com/mujahidi
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       package-os
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * 
 * @since			1.0.0
 */
define( 'PACK_OS_VERSION', '1.0.0' );

/**
 * 	Define Post Types & Taxonomy
 * 
 * 	@since			1.0.0
 */
define( 'PACK_OS_CPT', apply_filters( 'pack_os_post_type', 'package' ) );
define( 'PACK_OS_CT', apply_filters( 'pack_os_taxonomy', 'package-option' ) );
define( 'PACK_OS_ORDER_CPT', apply_filters( 'pack_os_order_post_type', 'package-order' ) );


/**
 * The code that runs during plugin activation.
 */
function activate_pack_os_plugin() {
	create_pack_os_post_types();
	create_pack_os_taxonomies();

	flush_rewrite_rules();
}
/**
 * The code that runs during plugin deactivation.
 */
function deactivate_pack_os_plugin() {
	unregister_post_type( PACK_OS_CPT );
	unregister_taxonomy( PACK_OS_CT );

	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_pack_os_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_pack_os_plugin' );


require plugin_dir_path( __FILE__ ) . 'includes/functions.php';	// functions and hooks
require plugin_dir_path( __FILE__ ) . 'includes/post-types.php'; // Registers post types & taxonomies required by the plugin
require plugin_dir_path( __FILE__ ) . 'includes/load-assets.php'; // Enqueue JS and CSS files
require plugin_dir_path( __FILE__ ) . 'cmb2/init.php'; // CMB2: https://github.com/CMB2/CMB2
require plugin_dir_path( __FILE__ ) . 'includes/meta-boxes.php'; // Register meta boxes using CMB2
require plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php'; // Register shortcode
require plugin_dir_path( __FILE__ ) . 'includes/ajax.php'; // Contains functions for jQuery/AJAX processing
