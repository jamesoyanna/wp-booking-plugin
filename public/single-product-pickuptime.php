<?php
namespace BackhoeBooking;
class Backhoe_Booking_Pickup_Time {

    function add_html_time() {
        global $post, $product;
        if ('backhoe_bookin_product' == $product->get_type()) {
            global $product;
            wc_update_product_stock( $product, 99, 'set' );
            if(get_post_meta(get_the_ID(),'devia_pricing_methode',true) == "price_fixed" ) {
                $maxday = get_post_meta(get_the_ID(),'devia_max_day',true);
            }else{
                $maxday = 9999;
            }
            ?>
            <div class="barb-form-wrapper">
                <div class="barb-pickup-date" data-maxday = "<?php echo esc_attr($maxday)  ?>" data-weekends = "<?php echo esc_attr(get_option('weekend_off'))  ?>">
                    <label for="start_date"><?php esc_html__('Pickup date', 'backhoebooking'); ?></label>
                    <input type="text" name="start_date" id="backhoe_fdate" autocomplete="off" placeholder="<?php esc_html__('Pickup date', 'backhoebooking'); ?>" />
                </div>
                <div>
                    <label for="end_date"><?php esc_html__('Return date', 'backhoebooking'); ?></label>
                    <input type="text" name="end_date" id="backhoe_tdate" autocomplete="off" placeholder="<?php esc_html__('Return date', 'backhoebooking'); ?>" />
                    <input type="hidden" class="backhoe_product_id" value="<?php echo $product->get_id(); ?>">
                    <input type="hidden" class="backhoe_product_type" value="<?php echo $product->get_type(); ?>">
                    <input type="hidden" class="backhoe_price" value="<?php echo $product->get_price(); ?>">
                </div>
            </div>
            <div class="backhoe_duration_and_price "></div>
            
        <?php
        
        }
    }

    function add_to_cart() {
        global $product;
        // Make sure it's our custom product type
        if ('backhoe_bookin_product' == $product->get_type()) { ?>
            <form class="backhoe_cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                <?php do_action('woocommerce_before_add_to_cart_button'); ?>
                <div class="backhoe_add_tocart">
                    <div class="quantity">
                        <label for="quantity" class="screen-reader-text"><?php esc_html__('Quantity', 'backhoebooking'); ?></label>
                        <input type="number" aria-labelledby="" inputmode="numeric" pattern="[0-9]*" size="4" title="Qty" value="1" name="quantity" max="" min="1" step="1" class="input-text qty text">
                    </div>
                    <button id="wd-add-to-cart" type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="button "><?php echo esc_html__('Rent Now', 'backhoebooking'); ?></button>
                </div>
                <?php do_action('woocommerce_after_add_to_cart_button'); ?>
            </form>
            <?php do_action('woocommerce_after_add_to_cart_form');
        }
    }


    public function get_price_html( $price, $product) {
        $pricePer = get_post_meta(get_the_ID(),'_pricing_type',true);
        $html = "";
        switch ($pricePer) {
            case 'hourly_pricing':
                $html = $price."<small>/".esc_html__(' Hour','backhoebooking')."</small>" ;
            break;
            case 'daily_pricing':
                $html = $price."<small>/".esc_html__(' Day','backhoebooking')."</small>" ;
            break;
            case 'weekly_pricing':
             $html = $price."<small>/".esc_html__(' Week','backhoebooking')."</small>" ;
            break;
            
            }
            if ('backhoe_bookin_product' == $product->get_type()) { 
                return $html;
            }
        return $price;
    }


    function check_product_availability() {

        global $product;
        $id = sanitize_text_field(absint($_POST['product_id']));
        $curr = get_woocommerce_currency_symbol();
        $price = "";
        $start = sanitize_text_field($_POST['start']);
        $end = sanitize_text_field($_POST['end']);
        $producte_id = sanitize_text_field($_POST['product_id']);
        $start_date = explode('-',trim($start));
        $end_date = $end;

        require BACKHOE_BOOKING_PATH . 'includes/class/orderDate.class.php';
        $orderDate = new orderDate();
        $isreserved = $orderDate->DateReservationDuration($start_date[0], $end, $producte_id);

        if ( $isreserved ) {
            echo $output = '<div class="callout alert"><i class="fas fa-exclamation-triangle"></i>'
                    . esc_html( __('This date is already reserved, please choose another date or contact us.', 'backhoebooking'))
                .'</div>';
        } else {
        
            $total_days = Backhoe_Booking_CalcDuration::calcDay($producte_id,$start_date[0],$end_date);    
            $totalPrice = Backhoe_Booking_CalcDuration::calcPice($producte_id,$start_date[0],$end_date,$_POST['price']); 
            $output = "";                    
            $output .=   '<div class="callout success"><i class="fas fa-check"></i>';
            $output .=   '<div>' . esc_html( 'Duration: ', 'backhoebooking') . $total_days . '<br/>';
            $output .=    esc_html('Subtotal: ', 'backhoebooking') . wc_price($totalPrice) . '</div>';
            $output .=   '</div>';
        
            echo  $output;
        }

        die();
    }
    
}