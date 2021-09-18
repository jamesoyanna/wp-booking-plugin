<?php

namespace BackhoeBooking;

class Backhoe_Booking_Add_To_Cart {

    // filter "woocommerce_add_cart_item_data"
    function add_date_to_cart_item($cart_item_data, $product_id, $variation_id) {

        $from = filter_input(INPUT_POST, 'start_date');
        $to = filter_input(INPUT_POST, 'end_date');

        $start_date = $from;
        $end_date = $to;
        $product = wc_get_product($product_id);
        $price = $product->get_price();

        $totalPrice = Backhoe_Booking_CalcDuration::calcPice($product_id, $start_date, $end_date, $price);

        if (empty($from)) {
            return $cart_item_data;
        }

        $cart_item_data['start_date'] = $from;
        $cart_item_data['end_date'] = $to;
        $cart_item_data['total_price'] = $totalPrice;

        return $cart_item_data;
    }

    // action "woocommerce_before_calculate_totals"
    function before_calculate_totals($cart_obj) {

        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        // Iterate through each cart item
        foreach ($cart_obj->get_cart() as $key => $value) {
            if (isset($value['total_price'])) {
                $price = $value['total_price'];
                $value['data']->set_price($price);
            }
        }
    }

    // filter "woocommerce_get_item_data"
    function display_date_to_cart($item_data, $cart_item) {

        $start_date = $end_date = "";
        if (isset($cart_item['start_date'])) {
            $start_date =  wc_clean($cart_item['start_date']);
        }
        if (isset($cart_item['end_date'])) {
            $end_date =  wc_clean($cart_item['end_date']);
        }

        $total_days = Backhoe_Booking_CalcDuration::calcDay($cart_item['product_id'], $start_date, $end_date);

        if (empty($cart_item['start_date'])) {
            return $item_data;
        }

        $item_data[] = array(
            'key'     => esc_html__('Pickup date', 'backhoebooking'),
            'value'   => $start_date,
            'display' => '',
        );
        $item_data[] = array(
            'key'     => esc_html__('Return date', 'backhoebooking'),
            'value'   => $end_date,
            'display' => '',
        );
        $item_data[] = array(
            'key'     => esc_html__('Duration', 'backhoebooking'),
            'value'   => wc_clean($total_days),
            'display' => '',
        );

        return $item_data;
    }

    // filter "woocommerce_checkout_create_order_line_item"
    function add_date_info_to_order_items($item, $cart_item_key, $values, $order) {

        if (empty($values['start_date'])) {
            return;
        }
        $item->add_meta_data(esc_html__('From', 'backhoebooking'), $values['start_date']);
        $item->add_meta_data(esc_html__('To', 'backhoebooking'), $values['end_date']);
    }


    function add_to_cart_button($button, $product) {
        
        if (is_product_category() || is_shop()) {
            if ($product->get_type() == 'backhoe_bookin_product') {
                $button_text = esc_html__("Rent Now", "backhoebooking");
                $button_link = $product->get_permalink();
                $button = '<a class="button" href="' . $button_link . '">' . $button_text . '</a>';
            }
        }
        return $button;
    }
}
