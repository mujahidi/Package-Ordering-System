<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file includes functions to add meta boxes to post types,
 * taxonomies and plugin settings page
 *
 * @since             1.0.0
 * @package           Package_Ordering_System
 */

/**
 * Adds metabox to taxonomy terms
 * 
 * @since       1.0.0
 */
add_action( 'cmb2_admin_init', 'pack_os_register_tax_options_metabox' );
function pack_os_register_tax_options_metabox() {
	$prefix = 'pos_opt_';

	/**
	 * Metabox to add fields to Options Taxonomy
	 */
	$cmb_term = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'object_types'     => array( 'term' ), 		// Tells CMB2 to use term_meta vs post_meta
		'taxonomies'       => array( PACK_OS_CT ),	// Tells CMB2 which taxonomies should have these fields
		'new_term_section' => true, 				// Will display in the "Add New Category" section
	) );

	$cmb_term->add_field( array(
		'name' 			=> esc_html__( 'Thumbnail', 'package-os' ),
		'id'   			=> $prefix . 'thumbnail',
		'type' 			=> 'file',
		'options' 		=> array(
			'add_upload_file_text' => esc_html__( 'Upload Thumbnail', 'package-os' ),
		),
	) );

}


/**
 * Adds metaboxes to 'package' post type
 * 
 * @since       1.0.0
 */
add_action( 'cmb2_admin_init', 'pack_os_register_package_metabox' );
function pack_os_register_package_metabox() {

	$prefix = 'pos_';

	$cmb_package = new_cmb2_box( array(
		'id'            	=> $prefix . 'metabox',
		'remove_box_wrap' 	=> true,
		'title'         	=> esc_html__( 'Package Information & Addons', 'package-os' ),
		'object_types'  	=> array( PACK_OS_CPT ), // Post type
		'context'    		=> 'normal',
	) );

	$pos_options = get_option('pack_os_settings');

	/**
	 * Repeatable Field Groups for Features
	 */
	$features_prefix = 'pos_features';

	$cmb_package->add_field( array(
		'name'       => esc_html__( 'Features', 'package-os' ),
		'id'         => $features_prefix,
		'type'       => 'text',
		'repeatable' => true,
		'text'		 => array(
			'add_row_text'	=> esc_html__( 'Add Feature', 'package-os' ),
		),
	) );

	/**
	 * 	Price field
	 */

	$cmb_package->add_field( array(
		'name' 				=> esc_html__( 'Package Price', 'package-os' ),
		'id' 				=> $prefix . 'price',
		'type' 				=> 'text_money',
		'before_field' 		=> $pos_options['currency_prefix'],
		'after_field' 		=> $pos_options['currency_postfix'],
		'attributes' 		=> array(
			'type' 			=> 'number',
			'pattern' 		=> '\d*',
			'min'			=> 0
		),
	) );


	/**
	 * Repeatable Field Groups for Addons
	 */
	$addon_prefix = 'pos_addons';

	$group_addons_id = $cmb_package->add_field( array(
		'id'          => $addon_prefix,
		'type'        => 'group',
		'description' => esc_html__( 'Please add addons related to this package below', 'package-os' ),
		'options'     => array(
			'group_title'    => esc_html__( 'Addon # {#}', 'package-os' ), // {#} gets replaced by row number
			'add_button'     => esc_html__( 'Add Another Addon', 'package-os' ),
			'remove_button'  => esc_html__( 'Remove Addon', 'package-os' ),
			'sortable'       => true,
			'closed'      => true, // true to have the groups closed by default
			'remove_confirm' => esc_html__( 'Are you sure you want to remove this addon?', 'package-os' ), // Performs confirmation before removing group.
		),
	) );

	/**
	 * Group fields works the same, except ids only need
	 * to be unique to the group. Prefix is not needed.
	 *
	 * The parent field's id needs to be passed as the first argument.
	 */
	$cmb_package->add_group_field( $group_addons_id, array(
		'name'       => esc_html__( 'Name', 'package-os' ),
		'id'         => 'name',
		'type'       => 'text',
	) );

	$cmb_package->add_group_field( $group_addons_id, array(
		'name' 				=> esc_html__( 'Price', 'package-os' ),
		'id' 				=> 'price',
		'type' 				=> 'text_money',
		'before_field' 		=> $pos_options['currency_prefix'],
		'after_field' 		=> $pos_options['currency_postfix'],
		'attributes' 		=> array(
			'type' 			=> 'number',
			'pattern' 		=> '\d*',
			'min'			=> 0
		),
	) );

	$cmb_package->add_group_field( $group_addons_id, array(
		'name'    => esc_html__( 'Description', 'package-os' ),
		'id'      => 'description',
		'type'    => 'wysiwyg',
		'options' => array(
			'media_buttons'		=> false
		),
	) );

}

/**
 * Adds metaboxes to 'package-order' post type for Order Information
 * 
 * @since       1.0.0
 */
function pos_order_metaboxes_init(){
	add_meta_box(
		'pos_order_metabox',        // Unique ID
		'Order Information',  		// Box title
		'pos_order_metaboxes_html',    // Content callback, must be of type callable
		PACK_OS_ORDER_CPT                     // Post type
	);
}
add_action('add_meta_boxes', 'pos_order_metaboxes_init');

/**
 * Callback function for 'package-order' post type
 * Displays Order Information section
 * 
 * @since       1.0.0
 */
function pos_order_metaboxes_html($post) {

	// pos options
	$pos_options = get_option('pack_os_settings');

	// order info
	$order_opts = maybe_unserialize( get_post_meta( $post->ID, 'pos_order_options', true ) );
	$order_packs = maybe_unserialize( get_post_meta( $post->ID, 'pos_order_packs', true ) );
	$order_addons = maybe_unserialize( get_post_meta( $post->ID, 'pos_order_addons', true ) );

	$total_price = 0;

?>
	<div class="package-ordering-summary-admin-container">
		<ul class="package-ordering-summary-admin-list">
			<li class="pos-order-summary-items">

				<?php if( !empty( $order_opts ) && count( $order_opts ) > 0 ){ ?>
				<div class="pos-opt-summary-item">
					<h4 class="pos-summary-item-name"><?php _e( 'Options', 'package-os' ); ?></h4>
					<ol class="pos-opt-summary-list pos-summary-item-list">
						<?php foreach( $order_opts as $opt_id => $opt_name ){ ?>
							<li><h5 class="pos-summary-title"><?php echo $opt_name; ?></h5></li>
						<?php } ?>
					</ol>
				</div>
				<?php } ?>

				<?php if( !empty( $order_packs ) && count( $order_packs ) > 0 ){ ?>
				<div class="pos-packs-summary-item">
					<h4 class="pos-summary-item-name"><?php _e( 'Packages', 'package-os' ); ?></h4>
					<ol class="pos-packs-summary-list pos-summary-item-list">
						<?php 
						foreach( $order_packs as $pack_id => $pack_price ){ 
							$total_price += $pack_price;
						?>
							<li>
								<h5 class="pos-summary-title">
									<?php if( FALSE == get_post_status( $pack_id ) ){ ?>
										<?php echo get_the_title( $pack_id ); ?>
									<?php }else{ ?>
										<a href="<?php echo get_edit_post_link( $pack_id ); ?>" target="_blank">
											<?php echo get_the_title( $pack_id ); ?>
										</a>
									<?php } ?>
								</h5>
								<span class="pos-summary-price"><?php echo $pos_options['currency_prefix'].$pack_price.$pos_options['currency_postfix']; ?></span>
							</li>
						<?php } ?>
					</ol>
				</div>
				<?php } ?>

				<?php if( !empty( $order_addons ) && count( $order_addons ) > 0 ){ ?>
				<div class="pos-addons-summary-item">
					<h4 class="pos-summary-item-name"><?php _e( 'Addons', 'package-os' ); ?></h4>
					<ol class="pos-addons-summary-list pos-summary-item-list">
						<?php 
						foreach( $order_addons as $addon_name => $addon_price ){ 
							$total_price += $addon_price;	
						?>
							<li>
								<h5 class="pos-summary-title"><?php echo $addon_name; ?></h5>
								<span class="pos-summary-price"><?php echo $pos_options['currency_prefix'].$addon_price.$pos_options['currency_postfix']; ?></span>
							</li>
						<?php } ?>
					</ol>
				</div>
				<?php } ?>

				<div class="pos-total-summary-item">
					<h4 class="pos-summary-item-name pos-summary-total-price-name"><?php _e( 'Total', 'package-os' ); ?></h4>
					<h4 class="pos-price-summary"><?php echo $pos_options['currency_prefix'] . number_format($total_price, 2) . $pos_options['currency_postfix']; ?></h4>
				</div>
			</li>
		</ul>
	</div>
<?php
}

/**
 * Adds metaboxes to 'package' post type for User Information
 * 
 * @since       1.0.0
 */
add_action( 'cmb2_admin_init', 'pack_os_register_order_metabox' );
function pack_os_register_order_metabox() {

	$prefix = 'pos_order_';

	$cmb_order = new_cmb2_box( array(
		'id'            	=> $prefix . 'user_metabox',
		'remove_box_wrap' 	=> true,
		'title'         	=> esc_html__( 'User Information', 'package-os' ),
		'object_types'  	=> array( PACK_OS_ORDER_CPT ), // Post type
		'context'    		=> 'normal',
	) );

	$cmb_order->add_field( array(
		'name' 				=> esc_html__( 'First Name', 'package-os' ),
		'id' 				=> $prefix . 'first_name',
		'type' 				=> 'text_medium',
	) );
	$cmb_order->add_field( array(
		'name' 				=> esc_html__( 'Last Name', 'package-os' ),
		'id' 				=> $prefix . 'last_name',
		'type' 				=> 'text_medium',
	) );
	$cmb_order->add_field( array(
		'name' 				=> esc_html__( 'Company Name', 'package-os' ),
		'id' 				=> $prefix . 'company_name',
		'type' 				=> 'text_medium',
	) );
	$cmb_order->add_field( array(
		'name' 				=> esc_html__( 'Email', 'package-os' ),
		'id' 				=> $prefix . 'email',
		'type' 				=> 'text_email',
	) );
	$cmb_order->add_field( array(
		'name' 				=> esc_html__( 'Phone Number', 'package-os' ),
		'id' 				=> $prefix . 'phone',
		'type' 				=> 'text_medium',
	) );
	$cmb_order->add_field( array(
		'name' 				=> esc_html__( 'Order Notes', 'package-os' ),
		'id' 				=> $prefix . 'notes',
		'type' 				=> 'textarea_small',
	) );

	
	$cmb_order2 = new_cmb2_box( array(
		'id'            	=> 'pos_order_admin_metabox',
		'remove_box_wrap' 	=> true,
		'title'         	=> esc_html__( 'Admin Order Notes', 'package-os' ),
		'object_types'  	=> array( PACK_OS_ORDER_CPT ), // Post type
		'context'    		=> 'side',
	) );

	$cmb_order2->add_field( array(
		'name' 				=> '',
		'description'		=> esc_html__( 'Add your notes related to this order in the above textarea', 'package-os'),
		'id' 				=> $prefix . 'admin_order_notes',
		'type' 				=> 'textarea_small',
	) );

}

/**
 * Adds metaboxes to plugin's settings page and adds a sub-menu item.
 * 
 * @since       1.0.0
 */
add_action( 'cmb2_admin_init', 'pack_os_register_settings_metabox' );
function pack_os_register_settings_metabox() {
	/**
	 * Registers options page menu item and form.
	 */
	$cmb_settings = new_cmb2_box( array(
		'id'           => 'pack_os_settings_page',
		'title'        => esc_html__( 'Package Ordering System Settings', 'package-os' ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'pack_os_settings',
		'menu_title'      => esc_html__( 'Settings', 'package-os' ),
		'parent_slug'     => 'edit.php?post_type='.PACK_OS_CPT,
		'capability'      => 'manage_options',
	) );

	$cmb_settings->add_field( array(
		'name'     => esc_html__( 'Currency', 'package-os' ),
		'id'       => 'currency_title',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Currency Prefix', 'package-os' ),
		'desc'    => esc_html__( 'e.g. $, &pound;, &yen; etc', 'package-os' ),
		'default' => '$',
		'id'      => 'currency_prefix',
		'type'    => 'text_small'
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Currency Postfix', 'package-os' ),
		'desc'    => esc_html__( 'e.g. USD, CAD etc', 'package-os' ),
		'id'      => 'currency_postfix',
		'type'    => 'text_small'
	) );

	$cmb_settings->add_field( array(
		'name'     => esc_html__( 'Colors', 'package-os' ),
		'id'       => 'colors_title',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Primary Color', 'package-os' ),
		'id'      => 'primary_color',
		'type'    => 'colorpicker',
		'default' => '#333',
		'options' => array(
			'alpha' => true, // Make this a rgba color picker.
		),
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Secondary Color', 'package-os' ),
		'id'      => 'secondary_color',
		'type'    => 'colorpicker',
		'default' => '#E2E6E7',
		'options' => array(
			'alpha' => true, // Make this a rgba color picker.
		),
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Button Color', 'package-os' ),
		'id'      => 'button_color',
		'type'    => 'colorpicker',
		'default' => '#333',
		'options' => array(
			'alpha' => true, // Make this a rgba color picker.
		),
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Button Font Color', 'package-os' ),
		'id'      => 'button_font_color',
		'type'    => 'colorpicker',
		'default' => '#333',
		'options' => array(
			'alpha' => true, // Make this a rgba color picker.
		),
	) );

	$cmb_settings->add_field( array(
		'name'    => esc_html__( 'Button Hover Font Color', 'package-os' ),
		'id'      => 'button_hover_font_color',
		'type'    => 'colorpicker',
		'default' => '#fff',
		'options' => array(
			'alpha' => true, // Make this a rgba color picker.
		),
	) );

	$cmb_settings->add_field( array(
		'name'     => esc_html__( 'Shortcode', 'package-os' ),
		'id'       => 'shortcode_title',
		'type'     => 'title',
		'on_front' => false,
		'render_row_cb' => 'pos_render_shortcode_cb',
	) );

}

/**
 * Adds metabox to Settings page for shortcode information
 * 
 * @since       1.0.0
 */
function pos_render_shortcode_cb( $field_args, $field ) {
	$label       = $field->args( 'name' );
	?>
	<div class="cmb-row cmb-type-title cmb2-id-shortcode-title" data-fieldtype="title">
		<div class="cmb-td">
			<h3 class="cmb2-metabox-title" id="shortcode-title" data-hash="51i3i82v0cn0"><?php echo $label; ?></h3>
			<p class="cmb2-metabox-description"><?php _e('Use the following shortcode to add to your Post or Page content', 'package-os'); ?></p>
			<p><code>[package_ordering_system]</code></p>
		</div>
	</div>
	<?php
}