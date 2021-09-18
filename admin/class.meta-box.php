<?php
namespace BackhoeBooking;
class Backhoe_Booking_Add_Metabox {
		
    function product_type($product_types) {

        $product_types['backhoe_bookin_product'] = "Bookable rental product";
        return $product_types;
    }

    /* 
    * Load New Product Type Class
    */ 
    function woocommerce_product_class( $classname, $product_type ) {
        
        if ( $product_type == 'backhoe_bookin_product' ) {
            $classname = 'BackhoeBooking\\WC_Product_Custom';
        }
        return $classname;
    }
   

    public function inventory_fields() {

        $default_pricing_methode = get_post_meta(get_the_ID(),'devia_pricing_methode',true);
        if($default_pricing_methode == '') $default_pricing_methode = get_option('default_pricing_method');

        woocommerce_wp_radio(
            array(
                'id'            => 'devia_pricing_methode',
                'wrapper_class' => 'hide_if_simple',
                'label'         => esc_html__('Pricing method', 'backhoebooking'),
                'description'   => esc_html__( 'Pricing method', 'backhoebooking').'<br>',
                'desc_tip'      => true,
                'options'       => array(
                    'price_per'       => esc_html__('Price per...', 'backhoebooking'),
                    'price_fixed'     => esc_html__('Fixed price', 'backhoebooking'),
                    'price_structure' => esc_html__('Pricing structure', 'backhoebooking'),
                ),
                'value' => $default_pricing_methode,
            ));
     
            $default_set_price = get_post_meta(get_the_ID(),'_pricing_type',true);
            if($default_set_price == '') $default_set_price = get_option('default_set_price');
        woocommerce_wp_select(array(
            'id' => '_pricing_type', 
            'label' => esc_html__('Set Price Per', 'backhoebooking'), 
            'wrapper_class' => 'hide_if_simple hide',
            'options' => array(
                'daily_pricing'  => esc_html__('Daily', 'backhoebooking'),
                'hourly_pricing' => esc_html__('Hourly', 'backhoebooking'),
                'weekly_pricing' => esc_html__('Weekly', 'backhoebooking'),           
            ),
            "value" => $default_set_price
            ));

            $apricing_structur_post_type = get_posts(array('post_type' => 'pricing_structure'));
            $posts_array = array();
            foreach ($apricing_structur_post_type as $key => $post) {
                $posts_array[$post->ID] = $post->post_title;
            }

            if(!empty($posts_array)) {
               woocommerce_wp_select(array(
                'id' => 'structur_pricing', 
                'label' => esc_html__('Custom Price', 'backhoebooking'), 
                'wrapper_class' => 'hide_if_simple hide',
                'options' => $posts_array,
               // "value" => $default_set_price
                )); 
            }else{
                ?>
                <p class="form-field structur_pricing_field hide_if_simple">
                <label for="devia_custom_price">Custom Price</label>
                <a href="<?php echo home_url( 'wp-admin/edit.php?post_type=pricing_structure' ); ?>" ><?php echo esc_html__("Add Pricing Structure","backhoebooking") ?></a>
                </p>
                <?php
            }
            

        
            woocommerce_wp_text_input(
                array(
                  'id'          => 'devia_max_day',
                  'label'       => esc_html__('Max day','backhoebooking'),
                  'placeholder' => esc_html__('Max day','backhoebooking'),
                  'desc_tip'    => 'true',
                  'wrapper_class' => 'hide_if_simple hide',
                  'type'              => 'number',
                  
                )
            );

       
       
    }

    public function backhoe_save_fields($post_id) {
        
        $product = wc_get_product($post_id);

        $pricing_methode = isset($_POST['devia_pricing_methode']) ? $_POST['devia_pricing_methode'] : 'price_per';
        $product->update_meta_data('devia_pricing_methode', sanitize_text_field($pricing_methode));
        $product->save();

        $devia_max_day = isset($_POST['devia_max_day']) ? $_POST['devia_max_day'] : 1;
        $product->update_meta_data('devia_max_day', sanitize_text_field($devia_max_day));
        $product->save();

        
        $devia_structur_pricing = isset($_POST['structur_pricing']) ? $_POST['structur_pricing'] : 1;
        $product->update_meta_data('structur_pricing', sanitize_text_field($devia_structur_pricing));
        $product->save();

        
        

        $num_package = isset($_POST['_pricing_type']) ? $_POST['_pricing_type'] : '';
        $product->update_meta_data('_pricing_type', sanitize_text_field($num_package));
        $product->save();
    }

} 