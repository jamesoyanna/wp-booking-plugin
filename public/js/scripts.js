(function( $ ) {
    'use strict';
    
   

    
    var currentDate = moment().format("MM/DD/YYYY");
    var tomorrowDate = moment().add(1, 'days').format("MM/DD/YYYY");
    // creat cookie
    function cratecookie(name,re_date) {
        document.cookie = name+'='+re_date + "; path=/";
    }
    // get cookie
    function getCookie(c_name) {
        
        if (document.cookie.length > 0) {
          var c_start = document.cookie.indexOf(c_name + "=");
          if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            var c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
              c_end = document.cookie.length;
            }
            console.log("date:" + document.cookie.substring(c_start, c_end));
            return unescape(document.cookie.substring(c_start, c_end));
          }
        }
        return "";
      }
      // delete cookie
      function deletecookie(name) {
        cratecookie(name, '');
      }
    var maxday = $(".barb-pickup-date").data( "maxday");
    if(maxday != 0) {
        maxday = maxday;
    }else {
        maxday = 9999;
    }

    var disableweekends = $(".barb-pickup-date").data( "weekends");

    $('input[name="start_date"],input[name="end_date"]').daterangepicker({
        autoApply: true,
        autoUpdateInput: false,
       isInvalidDate: function(date) {
        if (date.day() == 0 || date.day() == 6)
          return disableweekends;
        return false;
      },
        maxSpan: {
            day: maxday,
        },
        locale: {
              format: 'MM/DD/YYYY hh:mm A',
        },
       
        timePicker: true,
        minDate: currentDate
    });
 
           
        var backhoe_fdate = $('#backhoe_fdate').val();
        var backhoe_tdate = $('#backhoe_tdate').val();
        if (backhoe_fdate != '' || backhoe_tdate != '') {
            $('#wd-add-to-cart').attr('disabled', false);
        } else {
            $('#wd-add-to-cart').attr('disabled', true);
        }
        $('#backhoe_fdate,#backhoe_tdate').on('keyup', function () {
            if (backhoe_fdate != '' && backhoe_tdate != '') {
                $('#wd-add-to-cart').attr('disabled', false);
            } else {
                $('#wd-add-to-cart').attr('disabled', true);
            }
        });

        function enable_disable_btn_booking() {
            var backhoe_fdate = $('#backhoe_fdate').val();
            var backhoe_tdate = $('#backhoe_tdate').val();
            if (backhoe_fdate != '' || backhoe_tdate != '') {
                $('#wd-add-to-cart').attr('disabled', false);
            } else {
                $('#wd-add-to-cart').attr('disabled', true);
                    
            }
        }
    $('input[name="start_date"], input[name="end_date"]').val('');
    var ajaxurl = urltheme.template_url;
    var product_type=$('.backhoe_product_type').val();

    $('input[name="start_date"], input[name="end_date"]').on('apply.daterangepicker', function(ev, picker) {

        $('input[name="start_date"]').val(picker.startDate.format('MM/DD/YYYY hh:mm A'));
        $('input[name="end_date"]').val( picker.endDate.format('MM/DD/YYYY hh:mm A'));

        enable_disable_btn_booking();

       
                        var fromd=	$('#backhoe_fdate').val();
                        
                        var tod=$('#backhoe_tdate').val();
                        deletecookie('rental_start_date_cookie');
                        deletecookie('rental_end_date_cookie');
                        cratecookie('rental_start_date_cookie',fromd);
                        cratecookie('rental_end_date_cookie',tod);

                        var variation_id=''//jQuery('.variation_id').val();
                        
                        var quantity=$('.qty').val();
                        
                        var product_id=$('.backhoe_product_id').val();
                        
                        var product_price=$('.backhoe_price').val();
                        $(".backhoe_duration_and_price").html('<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');

                        jQuery.post(
                            
                            ajaxurl, 
                            {
                                'action': 'backhoe_booking_check_availability',
                                'product_id':product_id,
                                'variation_id': variation_id,  
                                'start':fromd,
                                'end':tod,
                                'quantity':quantity,
                                'price':product_price
                            }, 
                            
                            function(response){
                                console.log(response);
                                $('.backhoe_duration_and_price').html(response);
                            },
                            
                        );
                    
     }); 
    $('input[name="quantity"]').on('change', function(){
       
                        var fromd=	$('#backhoe_fdate').val();
                        
                        var tod=$('#backhoe_tdate').val();
                        
                        var variation_id=''//jQuery('.variation_id').val();
                        
                        var quantity=$('.qty').val();
                        
                        var product_id=$('.backhoe_product_id').val();
                        
                        var product_price=$('.backhoe_price').val();
                        $(".backhoe_duration_and_price").html('<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');

                        jQuery.post(
                            
                            ajaxurl, 
                            {
                                'action': 'backhoe_booking_check_availability',
                                'product_id':product_id,
                                'variation_id': variation_id,  
                                'start':fromd,
                                'end':tod,
                                'quantity':quantity,
                                'price':product_price
                            }, 
                            
                            function(response){
                                console.log(response);
                                $('.backhoe_duration_and_price').html(response);
                            },
                            
                        );
   });
    
   
   $('input[name="start_date"]').val( currentDate );
   $('input[name="end_date"]').val( tomorrowDate );
   // calcul total if cookie existe 
   if(getCookie('rental_start_date_cookie') != '') {
        $('input[name="start_date"]').val(getCookie('rental_start_date_cookie'));
        $('input[name="end_date"]').val( getCookie('rental_end_date_cookie'));
    }
    enable_disable_btn_booking();
    var fromd=	$('#backhoe_fdate').val();
                        
    var tod=$('#backhoe_tdate').val();
    
    var variation_id=''//jQuery('.variation_id').val();
    
    var quantity=$('.qty').val();
    
    var product_id=$('.backhoe_product_id').val();
    
    var product_price=$('.backhoe_price').val();
    $(".backhoe_duration_and_price").html('<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');

    jQuery.post(
        
        ajaxurl, 
        {
            'action': 'backhoe_booking_check_availability',
            'product_id':product_id,
            'variation_id': variation_id,  
            'start':fromd,
            'end':tod,
            'quantity':quantity,
            'price':product_price
        }, 
        
        function(response){
            console.log(response);
            $('.backhoe_duration_and_price').html(response);
        },
        
    );
   
  // save ookie from calander home page
  $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
    
        var cookie_date = $('input[name="daterange"]').val().split('-');
        var cookie_start = cookie_date[0];
        var cookie_end = cookie_date[1];

        deletecookie('rental_start_date_cookie');
        deletecookie('rental_end_date_cookie');
        cratecookie('rental_start_date_cookie',cookie_start);
        cratecookie('rental_end_date_cookie',cookie_end);
     
  })
                

})( jQuery );
