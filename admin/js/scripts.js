(function( $ ) {
    'use strict';
    $( '.options_group.pricing' ).addClass( 'show_if_backhoe_bookin_product' ).show();

    var qtipDescription,events = [];
    var calendrData = REDQRENTALFULLCALENDER.calendar_data ? REDQRENTALFULLCALENDER.calendar_data : '';
    
    for(var key in calendrData) {
        events.push(calendrData[key]);
        
      }
      
    var calendar = $('#calendar').fullCalendar({
        editable:true,
        header:{
         left:'prev,next today',
         center:'title',
         right:'month'
        },
        locale:'en',
        firstDay:0,
        events: events,
       eventRender: function (event, element) {
            element.attr('href', event.url);
       },
       eventAfterRender: function (event, element, view) {
        if (event.post_status === 'wc-pending') {
          element.css('background-color', '#7266BA');
        }
        if (event.post_status === 'wc-processing') {
          element.css('background-color', '#23B7E5');
        }
        if (event.post_status === 'wc-on-hold') {
          element.css('background-color', '#FF7F00');
          element.css('color', '#000');
        }
        if (event.post_status === 'wc-completed') {
          element.css('background-color', '#27C24C');
        }
        if (event.post_status === 'wc-cancelled') {
          element.css('background-color', '#a00');
        }
        if (event.post_status === 'wc-refunded') {
          element.css('background-color', '#DDD');
        }
        if (event.post_status === 'wc-failed') {
          element.css('background-color', '#EE3939');
        }
      },
    });
 
})( jQuery );