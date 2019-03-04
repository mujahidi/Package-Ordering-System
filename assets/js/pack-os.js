var $jq = jQuery.noConflict();

(function() {

    var pos_summary = {
        'options'   : [],
        'packs'     : [],
        'addons'    : [],
        'total_price': 0
    };

    // Options Event. Display Packages
    $jq('.package-ordering-container').on('click', '.package-ordering-main-opts .pos-opt-item', function(){

        var $this = $jq(this);

        unset_pos_data( pos_summary );

        var $checkbox = $this.find('input.pos-opt-checkbox');
        
        $this.toggleClass('pos-opt-item-selected');

        if( !$checkbox.prop('checked') ) // if not checked
            $checkbox.prop('checked', true);
        else                             // if checked
            $checkbox.prop('checked', false);

            
        var opt_count = 0,
            data = {
                'action': 'pos_get_options_packages',
                '_nonce': pack_os_get_package.options_nonce,
                'pos_options[]': []
            };
        
        $jq('input.pos-opt-checkbox:checked').each(function(){

            var checked_opt = $jq(this).val();

            opt_count++;
            data['pos_options[]'].push(checked_opt);
        });
        
        if( opt_count > 0 ){
            $jq.ajax({
                url : pack_os_get_package.ajaxurl,
                data : data,
                type : 'GET',
                beforeSend : function ( xhr ) {
                    $jq($this).closest('.package-ordering-main-opts').append( pos_loader() );
                    hide_and_remove_packs();
                    hide_and_remove_addons();
                    $jq('.package-ordering-main-summary').fadeOut(250);
                    remove_order_summary( pos_summary );
                },
                success : function( data ){
                    $jq('.pos-loader').remove();
                    $jq('.package-ordering-packs').append(data);
                    $jq('.package-ordering-main-packs').fadeIn(250);
                    $jq('html, body').animate({
                        scrollTop: $jq('.package-ordering-main-packs').offset().top
                    }, 800);
                }
            });
        }else{
            hide_and_remove_packs();
            hide_and_remove_addons();
            unset_pos_data( pos_summary );
            $jq('.package-ordering-main-summary').fadeOut(250);
            remove_order_summary( pos_summary );
        }

    });

    // Packages Event. Display Addons
    $jq('.package-ordering-container').on('click', '.package-ordering-main-packs .pos-pack-button > button', function(){

        unset_pos_data( pos_summary );

        var $btn = $jq(this),
            parent_element = $jq(this).closest('.pos-pack-item'),
            $checkbox = parent_element.find('input.pos-pack-checkbox');

        parent_element.toggleClass('pos-pack-item-selected');

        if( !$checkbox.prop('checked') ) // if not checked
            $checkbox.prop('checked', true);
        else                             // if checked
            $checkbox.prop('checked', false);

            
        var pack_count = 0,
            data = {
                'action': 'pos_get_packages_addons',
                '_nonce': pack_os_get_addons.addons_nonce,
                'pos_packs[]': []
            };
        
        $jq('input.pos-pack-checkbox:checked').each(function(){

            var checked_packs = $jq(this).val(),
                opt_id = $jq(this).closest('.pos-pack-item').data('option');

            pack_count++;
            data['pos_packs[]'].push(checked_packs);
            pos_summary['packs'].push(checked_packs);
            if ( Object.values(pos_summary['options']).indexOf(opt_id) < 0 ) {
                pos_summary['options'].push(opt_id);
            }

        });
        
        if( pack_count > 0 ){
            $jq.ajax({
                url : pack_os_get_addons.ajaxurl,
                data : data,
                type : 'GET',
                beforeSend : function ( xhr ) {
                    $jq($btn).closest('.package-ordering-main-packs').append( pos_loader() );
                    hide_and_remove_addons();
                },
                success : function( data ){
                    console.log(data);
                    $jq('.pos-loader').remove();
                    $jq('.package-ordering-addons').append(data);

                    if( data.length ){
                        $jq('.package-ordering-main-addons').fadeIn(250);
                        $jq('html, body').animate({
                            scrollTop: $jq('.package-ordering-main-addons').offset().top - 50
                        }, 800);
                        $jq('.package-ordering-main-summary').fadeIn(250);
                    }else{
                        $jq('.package-ordering-main-summary').fadeIn(250);
                        $jq('html, body').animate({
                            scrollTop: $jq('.package-ordering-main-summary').offset().top - 50
                        }, 800);
                    }

                    add_order_summary( pos_summary );
                    $btn.text( $btn.text() == 'Order' ? 'Selected' : 'Order' ).toggleClass('pos-button-active');
                }
            });
        }else{
            hide_and_remove_addons();
            unset_pos_data( pos_summary );
            remove_order_summary( pos_summary );
            $btn.text( $btn.text() == 'Order' ? 'Selected' : 'Order' ).toggleClass('pos-button-active');
            $jq('.package-ordering-main-summary').fadeOut(250);
        }

    });

    // Addons Event. Display Summary
    $jq('.package-ordering-container').on('click', '.package-ordering-main-addons .pos-addon-button > button', function(){

        pos_summary['addons'] = [];

        var $btn = $jq(this),
            parent_element = $jq(this).closest('.pos-addon-item'),
            $checkbox = parent_element.find('input.pos-addon-checkbox');

        parent_element.toggleClass('pos-addon-item-selected');

        if( !$checkbox.prop('checked') ) // if not checked
            $checkbox.prop('checked', true);
        else                             // if checked
            $checkbox.prop('checked', false);

        $jq('input.pos-addon-checkbox:checked').each(function(){

            var checked_addons = $jq(this).val(),
                pack_id = $jq(this).closest('.pos-addon-item').data('package');

            pos_summary['addons'].push(checked_addons);
            if (Object.values(pos_summary).indexOf(pack_id) > -1) {
                pos_summary['packs'].push(pack_id);
            }
        });

        add_order_summary( pos_summary );

        $btn.text( $btn.text() == 'Add' ? 'Added' : 'Add' ).toggleClass('pos-button-active');

        $jq('html, body').animate({
            scrollTop: $jq('.package-ordering-main-summary').offset().top - 50
        }, 800);

    });

    // Skip to Order Summary
    $jq('.package-ordering-container').on('click', '.skip-addons', function(){
        $jq('html, body').animate({
            scrollTop: $jq('.package-ordering-main-summary').offset().top - 50
        }, 800);

        return false;
    });

    // Process Order
    $jq('#package-ordering-form-container').submit(function(){

        // Process Form
        var form_data = {
                'action': 'pos_process_order',
                '_nonce': pack_os_process_order._nonce,
                'order_data': pos_summary,
                'user_data': $jq(this).serializeArray()
        };
        $jq.ajax({
            url : pack_os_process_order.ajaxurl,
            data : form_data,
            dataType : 'json',
            type : 'POST',
            beforeSend : function ( xhr ) {
                // remove all existing notices
                $jq('.pos-notice').html('');
                $jq('.pos-form-button').prop('disabled', true).after( pos_loader() );
            },
            success : function( data ){

                if( data.result === 'error' ){ // if error
                    
                    var errorMsg = '<span>' + data.error + '</span>';

                    if( data.type === 'no-option' || data.type === 'invalid-option' ){
                        $jq('.pos-opts-notice').html( errorMsg );
                        $jq('html,body').animate({scrollTop: $jq('.pos-opts-notice').offset().top - 100},'slow');
                    }else if( data.type === 'no-pack' || data.type === 'invalid-pack' ){
                        $jq('.pos-packs-notice').html( errorMsg );
                        $jq('html,body').animate({scrollTop: $jq('.pos-packs-notice').offset().top - 100},'slow');
                    }else if( data.type === 'invalid-addon' ){
                        $jq('.pos-addons-notice').html( errorMsg );
                        $jq('html,body').animate({scrollTop: $jq('.pos-addons-notice').offset().top - 100},'slow');
                    }else if( data.type === 'empty-field' ){
                        $jq('.pos-summary-notice').html( errorMsg );
                        $jq('html,body').animate({scrollTop: $jq('.pos-summary-notice').offset().top - 100},'slow');
                    }else{
                        $jq('.pos-summary-notice').html( errorMsg );
                        $jq('html,body').animate({scrollTop: $jq('.pos-summary-notice').offset().top - 100},'slow');
                    }
                }else if( data.result === 'success' ){ // no error
                    $jq('.package-ordering-main-opts').remove();
                    $jq('.package-ordering-main-packs').remove();
                    $jq('.package-ordering-main-addons').remove();
                    $jq('.package-ordering-form').remove();

                    $jq('.pos-success-notice').html( '<span>' + data.msg + '</span>' );
                    $jq('html,body').animate({scrollTop: $jq('.pos-success-notice').offset().top - 100},'slow');
                }

                $jq('.pos-loader').remove();
                $jq('.pos-form-button').prop('disabled', false);

            }
        });

        return false;
    });
      
})( jQuery );


function hide_and_remove_packs(){
    $jq('.package-ordering-main-packs').fadeOut(250);
    $jq('.package-ordering-packs').find('.pos-pack-item').remove();
}

function hide_and_remove_addons(){
    $jq('.package-ordering-main-addons').fadeOut(250);
    $jq('.package-ordering-addons').find('.pos-addon-item').remove();
}

function add_order_summary( pos_summary ){

    remove_order_summary( pos_summary );

    Object.keys(pos_summary).forEach(function(item) {
        var pos_data = pos_summary[item];

        for( var key in pos_data ){
            if( item === 'options' ){
                var item_id = pos_data[key],
                    item_name = $jq('#pos-opt-item-id-' + item_id).data('name');
                    
                $jq('.pos-order-summary-items .pos-opt-summary-item .pos-summary-item-list').append('<li><h5 class="pos-summary-title">' + item_name + '</h5></li>');
            }else if( item === 'packs' ){
                var item_id = pos_data[key],
                    item_name = $jq('#pos-pack-item-id-' + item_id).data('name'),
                    raw_price = $jq('#pos-pack-item-id-' + item_id).data('price'),
                    item_price = pack_os_currency.prefix + parseFloat(raw_price).toFixed(2) + pack_os_currency.postfix;

                    pos_summary['total_price'] += parseFloat(raw_price);
                    
                    $jq('.pos-order-summary-items .pos-packs-summary-item .pos-summary-item-list').append('<li><h5 class="pos-summary-title">' + item_name + '</h5><span class="pos-summary-price">' + item_price + '</span></li>');
            }else if( item === 'addons' ){
                    var item_id = pos_data[key],
                    item_name = $jq('#pos-addon-item-id-' + item_id).data('name'),
                    raw_price = $jq('#pos-addon-item-id-' + item_id).data('price'),
                    item_price = pack_os_currency.prefix + parseFloat(raw_price).toFixed(2) + pack_os_currency.postfix;

                    pos_summary['total_price'] += parseFloat(raw_price);
                    
                $jq('.pos-order-summary-items .pos-addons-summary-item .pos-summary-item-list').append('<li><h5 class="pos-summary-title">' + item_name + '</h5><span class="pos-summary-price">' + item_price + '</span></li>');
            }
        }

    });

    if( pos_summary['addons'].length > 0){
        $jq('.package-ordering-container .package-ordering-summary .pos-addons-summary-item').show();
    }else{
        $jq('.package-ordering-container .package-ordering-summary .pos-addons-summary-item').hide();
    }

    var total_price = pack_os_currency.prefix + parseFloat(pos_summary['total_price']).toFixed(2) + pack_os_currency.postfix;

    $jq('.pos-order-summary-items .pos-total-summary-item .pos-price-summary').html( total_price );

}

function remove_order_summary( pos_summary ){

    pos_summary['total_price'] = 0;

    $jq('.package-ordering-main-summary .pos-order-summary-items .pos-summary-item-list').find('li').each(function(){
        $jq(this).remove();
    });

}

function unset_pos_data( pos_summary ){
    pos_summary['options'] = [];
    pos_summary['packs'] = [];
    pos_summary['addons'] = [];
    pos_summary['total_price'] = 0;
}

function pos_loader(){
    return '<div class="pos-loader"><span></span></div>';
}