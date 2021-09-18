<?php 
namespace BackhoeBooking;
class Backhoe_Booking_Admin_Calendar {

    function add_admin_page() {
        add_menu_page('backhoe calander','Backhoe Rental Setting','manage_options','bachoecalendar',array($this,'backhoe_booking_setting'), 'dashicons-analytics', 59);
        add_submenu_page('bachoecalendar','Backhoe Rental Calander','Rental Calander','manage_options', 'backhoe-submenu-calander',array($this,'calendaradmin'));
       
    }
    /*
    *
    * Setting page
    *
    */
    function backhoe_booking_setting() {
        if (!empty($_POST)): 
        
        //-------------------Setting-------------
			
            update_option('default_set_price', esc_attr($_POST['default_set_price']));
            update_option('default_pricing_method', esc_attr($_POST['default_pricing_method']));
            if(isset($_POST['weekend_off'])) {
               update_option('weekend_off', esc_attr($_POST['weekend_off'])); 
            }else{
                update_option('weekend_off', "off"); 
            }
            
        ?>
			<div id="message" class="updated fade">
				<p> <?php echo esc_html__('Configuration updated!!','backhoebooking') ?></p>
			</div>
		<?php endif; ?>


		<div>
			<h2><?php echo esc_html__('Backhoe Booking','backhoebooking') ?></h2>
		</div>
		<div>
			<form  method="POST" action="">

               
                <div>
                    <label><?php echo esc_html__("Default Pricing method","backhoebooking") ?></label>
                    <select name="default_pricing_method">
                        <option value="price_per" <?php selected('price_per',get_option('default_pricing_method'),true) ?>>Price Per</option>
                        <option value="price_fixed" <?php selected('price_fixed',get_option('default_pricing_method'),true) ?>>Fixed Price </option>
                        <option value="price_structure" <?php selected('price_structure',get_option('default_pricing_method'),true) ?>>Pricing structure</option>
                    </select>
               
                </div>

                <div>
                    <label><?php echo esc_html__("Default set price","backhoebooking") ?></label>
                    <select name="default_set_price">
                        <option value="hourly_pricing" <?php selected('hourly_pricing',get_option('default_set_price'),true) ?>>Hourly</option>
                        <option value="daily_pricing" <?php selected('daily_pricing',get_option('default_set_price'),true) ?>>Daily</option>
                        <option value="weekly_pricing" <?php selected('weekly_pricing',get_option('default_set_price'),true) ?>>Weekly</option>
                    </select>
               
                </div>

                <div>
                    <label><?php echo esc_html__("Disable weekends","backhoebooking") ?></label>
                    <input type="checkbox" name="weekend_off" <?php checked('on',get_option('weekend_off'),true) ?> >
               
                </div>

                <p>
				<button type="submit" name="search" value="<?php echo esc_attr__('Save', 'backhoebooking'); ?>"class="button">
				    <span class="ti-save"></span><?php esc_html_e('Save', 'backhoebooking'); ?>
                </button>
                </p>
            </form>
        </div>
    <?php
    }

    /*
    *
    * Admin Calander
    *
    */
    function calendaradmin() {
        $calendar_data =array();
        global $wpdb;

        $args = [
            'post_type' => 'shop_order',
            'post_status' => 'any',
            'posts_per_page' => -1,
        ];

        $orders = get_posts($args);
        $fullcalendar = [];

        if (isset($orders) && !empty($orders)) {
            foreach ($orders as $o) {
                $order_id = $o->ID;
                $order = new \WC_Order($order_id);
                foreach ($order->get_items() as $item) {
                        $order_item_id = $item->get_id();
                        $product_id = $item->get_product_id();
                        $quantity = $item->get_quantity();
                        $order_item_details = $item->get_formatted_meta_data('');                    

                        $fullcalendar[$order_item_id]['post_status'] = $o->post_status;
                        $fullcalendar[$order_item_id]['title'] = get_the_title($product_id) . ' ×' . $quantity;
                        $fullcalendar[$order_item_id]['link'] = get_the_permalink($product_id);
                        $fullcalendar[$order_item_id]['id'] = $order_id;
                        $fullcalendar[$order_item_id]['url'] = admin_url('post.php?post=' . absint($order->get_id()) . '&action=edit');
                        foreach($order_item_details as $orderkey => $orderval) {
                            if ('From' === $orderval->key) {
                                $fullcalendar[$order_item_id]['start'] = date('Y-m-d',strtotime($orderval->value));
                                
                                }
                            if ('To' === $orderval->key) {
                                $fullcalendar[$order_item_id]['end'] = date('Y-m-d',strtotime($orderval->value)) ;
                               
                            }
                        }
                } 
            }

        }
        foreach($fullcalendar as $key=>$value) {
            if (array_key_exists('start', $value) && array_key_exists('end', $value)) {
                $calendar_data[$key] = $value;
            }            
        } ?>

        <div class="container">
            <div id="calendar"></div>
        </div>       
        <?php

    wp_register_script('backhoe_booking_admin_page', plugins_url( 'admin/js/scripts.js', __DIR__), ['jquery'], $ver = false, true);
    wp_enqueue_script('backhoe_booking_admin_page');

    $loc_data = [
    'calendar_data' => $calendar_data,
    ];

    wp_localize_script('backhoe_booking_admin_page', 'REDQRENTALFULLCALENDER', $loc_data);
    }
}