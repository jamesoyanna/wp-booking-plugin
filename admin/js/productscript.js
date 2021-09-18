(function( $ ) {
    'use strict';
    $( '.options_group.pricing' ).addClass( 'show_if_backhoe_bookin_product' ).show();

    $('.inventory_options').addClass('show_if_backhoe_bookin_product').show();
    $('#inventory_product_data ._manage_stock_field').addClass('show_if_backhoe_bookin_product').show();
    $('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_backhoe_bookin_product').show();
    $('#inventory_product_data ._sold_individually_field').addClass('show_if_backhoe_bookin_product').show();
    //-------------------------
    if ( $(".devia_pricing_methode_field input[type='radio']:checked").val() === "price_per" ){
        $('._pricing_type_field').removeClass( 'hide');
    }else{
        $('._pricing_type_field').addClass( 'hide');
    }

    $(".devia_pricing_methode_field input[type='radio']").on( 'change', function(){
        let value = $(this).val();
        console.log(value);
        if (value === "price_per"){
            $('._pricing_type_field').removeClass( 'hide');
        }else{
            $('._pricing_type_field').addClass( 'hide');
        }
    });
//--------------------------------
    if ( $(".devia_pricing_methode_field input[type='radio']:checked").val() === "price_fixed" ){
        $('.devia_max_day_field').removeClass( 'hide');
    }else{
        $('.devia_max_day_field').addClass( 'hide');
    }

    $(".devia_pricing_methode_field input[type='radio']").on( 'change', function(){
        let value = $(this).val();
        console.log(value);
        if (value === "price_fixed"){
            $('.devia_max_day_field').removeClass( 'hide');
        }else{
            $('.devia_max_day_field').addClass( 'hide');
        }
    });
//----------------------------------

    if ( $(".devia_pricing_methode_field input[type='radio']:checked").val() === "price_structure" ){
        $('.structur_pricing_field').removeClass( 'hide');
    }else{
        $('.structur_pricing_field').addClass( 'hide');
    }

    $(".devia_pricing_methode_field input[type='radio']").on( 'change', function(){
        let value = $(this).val();
        console.log(value);
        if (value === "price_structure"){
            $('.structur_pricing_field').removeClass( 'hide');
        }else{
            $('.structur_pricing_field').addClass( 'hide');
        }
    });

})( jQuery );