<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file includes all the functions for AJAX processing
 *
 * @since             1.0.0
 * @package           Package_Ordering_System
 */

/**
 * Get Option IDs and return Packages in HTML format to JS
 * 
 * @since       1.0.0
 */
function pos_get_options_packages_ajax_handler(){
    
    $nonce = $_REQUEST['_nonce'];
	if ( wp_verify_nonce( $nonce, 'pos_get_options_packages' ) ) {
	    // nonce is valid
        
        if ( wp_doing_ajax() ){

            $packages_list = array();

            $options = $_GET['pos_options'];

            foreach( $options as $opt ){

                $opt_id = (int) esc_attr( $opt );

                $get_packages_args = array(
                    'post_type'         => PACK_OS_CPT,
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                    'tax_query'          => array(
                        array(
                            'taxonomy'  => PACK_OS_CT,
                            'field'     => 'term_id',
                            'terms'     => $opt_id,
                        )
                    )
                );
            
                $get_packages = new WP_QUERY( $get_packages_args );
                if( $get_packages->have_posts() ){
                    while( $get_packages->have_posts() ): $get_packages->the_post(); 

                        // if package is already in the packages array, skip loop
                        if( in_array( get_the_ID(), $packages_list ) ) continue;

                        $pos_options = get_option('pack_os_settings');

                        $features = get_post_meta( get_the_ID(), 'pos_features', true );
            
                        $raw_price = get_post_meta( get_the_ID(), 'pos_price', true);
                        $price = explode( ".", $raw_price );
                    ?>
                        <li class="pos-pack-item" id="pos-pack-item-id-<?php echo get_the_ID(); ?>" data-name="<?php echo get_the_title(); ?>" data-price="<?php echo $raw_price;  ?>" data-option="<?php echo $opt_id; ?>">
            
                            <h4 class="pos-pack-name"><?php the_title(); ?></h4>

                            <div class="pos-pack-price">
                                <span class="pos-pack-price-currency"><?php echo $pos_options['currency_prefix']; ?></span>
                                <span class="pos-pack-price-unit"><?php echo $price[0]; ?></span>
                                <span class="pos-pack-price-decimal"><?php echo $price[1]; ?></span>
                                <span class="pos-pack-price-after"><?php echo $pos_options['currency_postfix']; ?></span>
                            </div>

                            <?php if( $features && count($features) > 0 ){ ?>
                            <ul class="pos-pack-features pos-list-reset">
                                <?php foreach( $features as $feature ){ ?>
                                    <li><?php echo $feature; ?></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
            
                            <div class="pos-pack-button pos-button">
                                <button type="button"><?php _e( 'Order', 'package-os'); ?></button>
                            </div>
                            <input type="checkbox" class="pos-pack-checkbox" name="package" value="<?php echo get_the_ID(); ?>" />
                        </li>
                    <?php 
                        $packages_list[] = get_the_ID();
                    endwhile; wp_reset_postdata();
                }
            }

        }
        
    }

    die; // here we exit the script
}
add_action('wp_ajax_pos_get_options_packages', 'pos_get_options_packages_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_pos_get_options_packages', 'pos_get_options_packages_ajax_handler'); // wp_ajax_nopriv_{action}

/**
 * Get Package IDs and return Addons in HTML format to JS
 * 
 * @since       1.0.0
 */
function pos_get_packages_addons_ajax_handler(){
    
    $nonce = $_REQUEST['_nonce'];

	if ( wp_verify_nonce( $nonce, 'pos_get_packages_addons' ) ) {
	    // nonce is valid
        
        if ( wp_doing_ajax() ){

            
            $packages = $_GET['pos_packs'];

            foreach( $packages as $pack ){

                $pack_id = (int) esc_attr( $pack );

                $get_addons_args = array(
                    'post_type'         => PACK_OS_CPT,
                    'post_status'       => 'publish',
                    'p'                 => $pack_id
                );

                $get_addons = new WP_QUERY( $get_addons_args );
                if( $get_addons->have_posts() ){
                    while( $get_addons->have_posts() ): $get_addons->the_post(); 
                        $addons = get_post_meta( get_the_ID(), 'pos_addons', true );

                        $pos_options = get_option('pack_os_settings');

                        foreach( $addons as $addon ){
                            $pos_options = get_option('pack_os_settings');

                            $addon_id = str_replace(' ','-',$addon['name'])."___".get_the_ID();
                            $price = explode( ".", $addon['price'] );
                        ?>
                            <li class="pos-addon-item" id="pos-addon-item-id-<?php echo $addon_id; ?>" data-name="<?php echo $addon['name']; ?>" data-price="<?php echo $addon['price']; ?>" data-package="<?php echo get_the_ID(); ?>">
                                
                                <div class="pos-addon-content">
                                    <h4 class="pos-addon-name"><?php echo $addon['name']; ?></h4>
                                    <div class="pos-addon-desc">
                                        <?php echo $addon['description']; ?>
                                    </div>
                                </div>
                                
                                <div class="pos-addon-price">
                                    <span class="pos-addon-price-currency"><?php echo $pos_options['currency_prefix']; ?></span>
                                    <span class="pos-addon-price-unit"><?php echo $price[0]; ?></span>
                                    <span class="pos-addon-price-decimal"><?php echo $price[1]; ?></span>
                                    <span class="pos-addon-price-after"><?php echo $pos_options['currency_postfix']; ?></span>
                                </div>

                                <div class="pos-addon-button pos-button">
                                    <button type="button"><?php _e( 'Add', 'package-os'); ?></button>
                                </div>

                                <input type="checkbox" class="pos-addon-checkbox" name="addon" value="<?php echo $addon_id; ?>" />
                            </li>
                        <?php
                        }
                    
                    endwhile; wp_reset_postdata();
                }
            }

        }
        
    }

    die; // here we exit the script
}
add_action('wp_ajax_pos_get_packages_addons', 'pos_get_packages_addons_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_pos_get_packages_addons', 'pos_get_packages_addons_ajax_handler'); // wp_ajax_nopriv_{action}

/**
 * Responsible for processing the data
 * 
 * @since       1.0.0
 */
function pos_process_order_ajax_handler(){
    
    $nonce = $_POST['_nonce'];
	if ( wp_verify_nonce( $nonce, 'pos_process_order_'.date('Ymd') ) ) {
	    // nonce is valid
        
        if ( wp_doing_ajax() ){

            $result = $pos_opts = $pos_packs = $pos_addons = $user_data_array = array();

            if( isset($_POST['order_data']) && count($_POST['order_data']) > 0 ){

                if( $_POST['order_data']['options'] < 1 ){ // No Options

                    pos_json_error( 'error', 'no-option', 'You must select at least one Option' );

                }elseif( $_POST['order_data']['packs'] < 1 ){ // No Packages

                    pos_json_error( 'error', 'no-pack', 'You must select at least one Package' );

                }else{ // Found Options and Packages
                    foreach( $_POST['order_data'] as $key => $order_data ){

                        if( $key == 'options' ){
                            foreach ($order_data as $term_id) {
                                $options = get_term_by('id', (int) esc_attr($term_id), PACK_OS_CT);
                                if ( empty( $options ) ){ // if not a valid term
                                    
                                    pos_json_error( 'error', 'invalid-option', 'You must select a valid Option' );

                                } else {
                                    $term = get_term( $term_id, PACK_OS_CT );
                                    $pos_opts[$term->term_id] = $term->name;
                                }
                            }
                        }elseif( $key == 'packs' ){
                            foreach ($order_data as $item_id) {
                                $post_id = (int) esc_attr( $item_id );
                                if( FALSE == get_post_status( $post_id ) ){ // if not a valid post

                                    pos_json_error( 'error', 'invalid-pack', 'You must select a valid Package' );

                                }else {
                                    $package_price = get_post_meta( $post_id, 'pos_price', true );
                                    if( $package_price )
                                        $pos_packs[$post_id] = $package_price;
                                }
                            }
                        }elseif( $key == 'addons' ){
                            foreach ($order_data as $item) {
                                $addon = explode( '___', esc_attr($item) );
                                $addon_title = str_replace("-"," ",$addon[0]);
                                $post_id = $addon[1];

                                $addons = get_post_meta( $post_id, 'pos_addons', true);
                                
                                if( !in_array( $post_id, $_POST['order_data']['packs'] ) && !in_array($addon_title, array_column($addons, 'name')) ){ // if not a valid addon
                                    
                                    pos_json_error( 'error', 'invalid-addon', 'You must select a valid Addon' );

                                }else{
                                    foreach( $addons as $addon ){
                                        if( $addon['name'] == $addon_title && $addon['price'] ){
                                            $pos_addons[$addon['name']] = $addon['price'];
                                        }
                                    }
                                }
                            }
                        }

                    }
                }

                if( $_POST['user_data'] && is_array( $_POST['user_data'] ) ){
                    foreach( $_POST['user_data'] as $user_data ){
                        
                        if( $user_data['name'] == "pos_first_name" && $user_data['value'] == '' ){ // First Name
                            
                            pos_json_error( 'error', 'empty-field', 'You can not leave First Name field blank' );

                        }elseif( $user_data['name'] == "pos_last_name" && $user_data['value'] == '' ){ // Last Name
                            
                            pos_json_error( 'error', 'empty-field', 'You can not leave Last Name field blank' );
                            
                        }elseif( $user_data['name'] == "pos_email" && $user_data['value'] == '' ){ // Email
                            
                            pos_json_error( 'error', 'empty-field', 'You can not leave Email field blank' );

                        }elseif( $user_data['name'] == "pos_email" && !is_email($user_data['value']) ){ // Invalid Email

                            pos_json_error( 'error', 'empty-field', 'You must enter a valid Email' );

                        }elseif( $user_data['name'] == "pos_phone" && $user_data['value'] == '' ){ // Phone

                            pos_json_error( 'error', 'empty-field', 'You can not leave Phone field blank' );

                        }elseif( $user_data['name'] == "pos_phone" && !preg_match( '%^[+]?[0-9()/ -]*$%', $user_data['value'] ) ){ // Invalid Phone
                            
                            pos_json_error( 'error', 'empty-field', 'You must enter a valid Phone Number' );

                        }else{
                            $user_data_array[ $user_data['name'] ] = esc_html($user_data['value']);
                        }

                    }
                }

                $order_number = 0;
                // fetch last order #
                global $wpdb;
                $order_number_field = '_pos_order_number';
                $order_number = $wpdb->get_var( $wpdb->prepare( "
                    SELECT 
                    IF( 
                        MAX( CAST( meta_value as UNSIGNED) ) IS NULL, 
                        1, 
                        MAX( CAST( meta_value as UNSIGNED ) ) + 1
                    ) 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key='%s'
                ", $order_number_field ) );

                // lets create a new order post
                $order_post_args = array(
                    'post_title'        => 'Order # '.$order_number,
                    'post_status'       => 'publish',
                    'post_type'         => PACK_OS_ORDER_CPT
                );

                $order_id = wp_insert_post( $order_post_args );

                if( !is_wp_error($order_id) ){
                    // order number meta
                    update_post_meta( $order_id, $order_number_field, $order_number );
                    // options meta
                    if( !empty( $pos_opts ) && count( $pos_opts ) > 0 ){
                            add_post_meta( $order_id, 'pos_order_options', maybe_unserialize($pos_opts) );
                    }

                    // packages meta
                    if( !empty( $pos_packs ) && count( $pos_packs ) > 0 ){
                            add_post_meta( $order_id, 'pos_order_packs', maybe_serialize( $pos_packs ) );
                    }

                    // addons meta
                    if( !empty( $pos_addons ) && count( $pos_addons ) > 0 ){
                            add_post_meta( $order_id, 'pos_order_addons', maybe_serialize( $pos_addons ) );
                    }

                    foreach( $user_data_array as $ud => $uv ){
                        if( $ud == 'pos_first_name' ) add_post_meta( $order_id, 'pos_order_first_name', $uv );
                        if( $ud == 'pos_last_name' ) add_post_meta( $order_id, 'pos_order_last_name', $uv );
                        if( $ud == 'pos_company_name' ) add_post_meta( $order_id, 'pos_order_company_name', $uv );
                        if( $ud == 'pos_email' ) add_post_meta( $order_id, 'pos_order_email', $uv );
                        if( $ud == 'pos_phone' ) add_post_meta( $order_id, 'pos_order_phone', $uv );
                        if( $ud == 'pos_order_notes' ) add_post_meta( $order_id, 'pos_order_notes', $uv );
                    }

                    echo json_encode( array(
                        'result'       => 'success',
                        'msg'          => esc_html__( 'Thank you! We have received your order.', 'package-os' )
                    ) );
                    die;

                }else{
                    pos_json_error( 'error', 'order_error', $order_id->get_error_message() );
                }

            }

        }
        
    }else{
        pos_json_error( 'error', 'nonce', 'There was some error. Please try again' );
    }

    die; // here we exit the script
}
add_action('wp_ajax_pos_process_order', 'pos_process_order_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_pos_process_order', 'pos_process_order_ajax_handler'); // wp_ajax_nopriv_{action}