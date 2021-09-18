<?php 
namespace BackhoeBooking;
class orderDate {
 
    public  function getorderdate($date1, $date2,$product_id) {
        global $wpdb;

        $args = [
            'post_type' => 'shop_order',
            'post_status' => 'any',
            'posts_per_page' => -1,
        ];

        $orders = get_posts($args);

        $fullcalendar = [];
       
        $product = wc_get_product($product_id);
        $quantity_stock = $product->get_stock_quantity();//(int) get_post_meta($product_id, '_backhoe_stock',true);
        $product_quantity = (isset($_POST['quantity'])) ? $_POST['quantity'] : 1;
        $pricing_type = get_post_meta($product_id, '_pricing_type',true);
       
       

        
        if($pricing_type == 'hourly_pricing' ) {
            $format = "m/d/Y h:m A";
        }else{
            $format = "m/d/Y";
        }
        if($product->get_manage_stock() == false) {
            return false;
        }
        
if (isset($orders) && !empty($orders)) {
    foreach ($orders as $o) {
        $order_id = $o->ID;
        $order = new \WC_Order($order_id);
        
        
        $existe = false;
        foreach ($order->get_items() as $item) {
           $order_quantity = $item['quantity'] ;
            $order_product_id = $item['product_id'];
               if($order_product_id === (int)$product_id) {
                $order_item_id = $item->get_id();
                $order_item_details = $item->get_formatted_meta_data('');
                $i = 0;
                foreach($order_item_details as $orderkey => $orderval) {
                 
                    if ('From' === $orderval->key) {
                        $fullcalendar['start'] = date($format,strtotime($orderval->value));
                        }
                    if ('To' === $orderval->key) {
                        $fullcalendar['end'] = date($format,strtotime($orderval->value)) ;
                    }
                   
                   if(isset($fullcalendar['start']) &&   isset($fullcalendar['end'])  ){
                       $datess = array();
                    
                        $currentt = strtotime($fullcalendar['start']);
                        $date22 = strtotime($fullcalendar['end']);
                       
                        $date1 =date($format,strtotime($date1));
                        $date2 = date($format,strtotime($date2));
                      
                        $stepVal = '+1 day';
                        while( $currentt <= $date22 ) {
                           $datess[] = date($format, $currentt);
                           $currentt = strtotime($stepVal, $currentt);
                        }
                      
                        
                        if((in_array( $date1,$datess)) || ( in_array( $date2,$datess)) ) {
                            $existe = true;  
                        }
                    }

                   
                  
                }
               
                if($existe == true && $quantity_stock < ($product_quantity + $order_quantity)) {
                   
                    $quantity_stock -= ($product_quantity + $order_quantity);
                }
           }
            
        }
       
            if($quantity_stock < 1) {
                return true;
            }
    }
    

}

         return false;
}
    function DateReservationDuration($date1, $date2,$product_id, $format = 'm/d/Y h:m A' ) {
   
      $fromdate =  $this->getorderdate($date1, $date2,$product_id);
            return $fromdate;
     }
  
    
}