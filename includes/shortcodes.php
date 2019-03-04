<?php

/**
 * PACKAGE ORDERING SYSTEM
 *
 * This file includes functions to add plugin's shortcodes
 *
 * @since             1.0.0
 * @package           Package_Ordering_System
 */

/**
 * Add Shortcode '[package_ordering_system]'
 * 
 * @since       1.0.0
 */
add_shortcode( 'package_ordering_system', 'pos_main_shortcode' );
function pos_main_shortcode() {
    
    ob_start();

    // Let's first get package options
    $options = get_terms( array(
        'taxonomy'      => PACK_OS_CT,
    ) );

    if( !empty( $options ) && !is_wp_error( $options ) ){
?>
        <div class="package-ordering-container">
            <form class="package-ordering-form-container" id="package-ordering-form-container" method="post">
            <div class="package-ordering-main package-ordering-main-opts">
                    <h3 class="pos-section-title"><?php _e( 'Select Option(s)', 'package-os'); ?></h3>

                    <div class="pos-notice pos-opts-notice"></div>

                    <ul class="package-ordering-opts pos-list-reset">
                        <?php foreach( $options as $opt ){ ?>
                            <li class="pos-opt-item" id="pos-opt-item-id-<?php echo $opt->term_id; ?>" data-name="<?php echo $opt->name; ?>">
                                <div class="pos-opt-item-contain">
                                    <?php 
                                    if( get_term_meta( $opt->term_id, 'pos_opt_thumbnail_id', true ) ){ 
                                        $opt_thumbnail_id = get_term_meta( $opt->term_id, 'pos_opt_thumbnail_id', true );
                                    ?>
                                        <div class="post-opt-thumb">
                                            <?php echo wp_get_attachment_image( $opt_thumbnail_id, 'thumbnail' ); ?>
                                        </div>
                                    <?php } ?>
                                    <h4 class="pos-opt-name"><?php echo $opt->name; ?></h4>
                                    <input type="checkbox" class="pos-opt-checkbox" name="pos-options[]" value="<?php echo $opt->term_id; ?>" />
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="package-ordering-main package-ordering-main-packs">
                    <h3 class="pos-section-title"><?php _e( 'Select Package(s)', 'package-os'); ?></h3>

                    <div class="pos-notice pos-packs-notice"></div>

                    <ul class="package-ordering-packs pos-list-reset"></ul>
                </div>

                <div class="package-ordering-main package-ordering-main-addons">
                    <h3 class="pos-section-title">
                        <?php _e( 'Select Addon (optional)', 'package-os'); ?>
                        <a href="#" class="skip-addons"><?php _e( 'Skip', 'package-os'); ?></a>
                    </h3>

                    <div class="pos-notice pos-addons-notice"></div>

                    <ul class="package-ordering-addons pos-list-reset"></ul>
                    <a href="#" class="skip-addons"><?php _e( 'Skip', 'package-os'); ?></a>
                </div>

                <div class="package-ordering-main package-ordering-main-summary">
                    <h3 class="pos-section-title"><?php _e( 'Order Summary', 'package-os'); ?></h3>

                    <div class="pos-notice pos-success-notice"></div>

                    <ul class="package-ordering-summary">
                        <li class="pos-order-summary-items">
                            <div class="pos-opt-summary-item">
                                <h4 class="pos-summary-item-name"><?php _e( 'Options', 'package-os'); ?></h4>
                                <ol class="pos-opt-summary-list pos-summary-item-list"></ol>
                            </div>

                            <div class="pos-packs-summary-item">
                                <h4 class="pos-summary-item-name"><?php _e( 'Packages', 'package-os'); ?></h4>
                                <ol class="pos-packs-summary-list pos-summary-item-list"></ol>
                            </div>

                            <div class="pos-addons-summary-item">
                                <h4 class="pos-summary-item-name"><?php _e( 'Addons', 'package-os'); ?></h4>
                                <ol class="pos-addons-summary-list pos-summary-item-list"></ol>
                            </div>

                            <div class="pos-total-summary-item">
                                <h4 class="pos-summary-item-name pos-summary-total-price-name"><?php _e( 'Total', 'package-os'); ?></h4>
                                <h4 class="pos-price-summary"></h4>
                            </div>
                        </li>

                        <li class="package-ordering-form">

                            <div class="pos-notice pos-summary-notice"></div>

                            <div class="pos-form-field-container pos-form-field-first-name">
                                <label for="pos-form-field-first-name"><?php _e( 'First Name', 'package-os'); ?> <span class="pos-req">*</span></label>
                                <input type="text" class="pos-form-field" id="pos-form-field-first-name" name="pos_first_name" />
                            </div>

                            <div class="pos-form-field-container pos-form-field-last-name">
                                <label for="pos-form-field-last-name"><?php _e( 'Last Name', 'package-os'); ?> <span class="pos-req">*</span></label>
                                <input type="text" class="pos-form-field" id="pos-form-field-last-name" name="pos_last_name" />
                            </div>
                            
                            <div class="pos-form-field-container pos-form-ficompany-name">
                                <label for="pos-form-field-company-name"><?php _e( 'Company Name', 'package-os'); ?></label>
                                <input type="text" class="pos-form-field" id="pos-form-field-company-name" name="pos_company_name" />
                            </div>

                            <div class="pos-form-field-container pos-form-field-email">
                                <label for="pos-form-field-email"><?php _e( 'Email', 'package-os'); ?> <span class="pos-req">*</span></label>
                                <input type="email" class="pos-form-field" id="pos-form-field-email" name="pos_email" />
                            </div>

                            <div class="pos-form-field-container pos-form-field-phone">
                                <label for="pos-form-field-phone"><?php _e( 'Phone Number', 'package-os'); ?> <span class="pos-req">*</span></label>
                                <input type="text" class="pos-form-field" id="pos-form-field-phone" name="pos_phone" />
                            </div>

                            <div class="pos-form-field-container pos-form-field-order-notes">
                                <label for="pos-form-field-order-notes"><?php _e( 'Order Notes', 'package-os'); ?></label>
                                <textarea name="pos_order_notes" class="pos-form-field" id="pos-form-field-order-notes" rows="5"></textarea>
                            </div>

                            <div class="pos-form-button-container pos-button">
                                <button type="submit" class="pos-form-button"><?php _e( 'Place Order', 'package-os'); ?></button>
                            </div>
                        </li>
                    </ul>
                </div>

            </form>
        </div>
<?php
    }

    return ob_get_clean();
    
}