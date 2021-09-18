<?php

namespace BackhoeBooking;
class Backhoe_Booking {  
    

    /**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      hooks_load    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
    
    function __construct()
    {
        
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->public_hooks();
        $this->run();
    }
    /*
    *   Include the following files that make up the plugin
    */
    private function load_dependencies() {
        //--- back-end-end ---
        require BACKHOE_BOOKING_PATH . 'admin/class.meta-box.php';
        require BACKHOE_BOOKING_PATH . 'admin/class.admin.calendar.php';
        require BACKHOE_BOOKING_PATH . 'admin/class.post.type.php';
       
        //------- includes ---
        require BACKHOE_BOOKING_PATH . 'includes/class/calc.rental.duration.class.php';
        //--- front-end ---
        require BACKHOE_BOOKING_PATH . 'includes/class/hooks.class.php';
        require BACKHOE_BOOKING_PATH . 'public/single-product-pickuptime.php';
        require BACKHOE_BOOKING_PATH . 'public/cat.php';
        $this->loader = new hooks_load();
    }

    /**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	function define_admin_hooks() {
        require_once BACKHOE_BOOKING_PATH . 'includes/base/Activate.php';
		$backhoe_booking_activate = new Backhoe_Booking_Activate();  
        add_action( 'admin_notices', array( $backhoe_booking_activate, 'render_missing_woocommerce_notice' ) );
	}

    /**
     * Register all of the hooks related to the public & admin functionality
	 *  of the plugin.
     */
    function public_hooks () {
    
        // calendar admin page
        $add_to_cart = new Backhoe_Booking_Admin_Calendar();
        $this->loader->add_action('admin_menu',$add_to_cart,'add_admin_page');
        
        // Post Type
        $post_type = new backhoe_booking_post_type();
        $this->loader->add_action( 'init', $post_type, 'post_type_pricing_structure'  );
        $this->loader->add_action( 'admin_init', $post_type, 'pricing_structure_add_meta_boxes'  );
        $this->loader->add_action( 'save_post', $post_type, 'pricing_structure_meta_box_save'  );
        
       

        //-- add metabox
        $add_metabox = new Backhoe_Booking_Add_Metabox();
        $this->loader->add_filter('product_type_selector',               $add_metabox,'product_type');
        $this->loader->add_filter( 'woocommerce_product_class',          $add_metabox,'woocommerce_product_class', 10, 2 );       
        $this->loader->add_action( 'woocommerce_product_options_pricing',$add_metabox,'inventory_fields' );        
        $this->loader->add_action( 'woocommerce_process_product_meta',   $add_metabox,'backhoe_save_fields' ); 

        // pickup time in single product
        $pickuptime = new Backhoe_Booking_Pickup_Time();        
        $this->loader->add_filter( 'woocommerce_get_price_html',          $pickuptime, 'get_price_html', 99, 4 );
        $this->loader->add_action( 'woocommerce_single_product_summary',  $pickuptime, 'add_to_cart', 60 );
        $this->loader->add_filter('woocommerce_before_add_to_cart_button',$pickuptime, 'add_html_time');
        $this->loader->add_action('wp_ajax_backhoe_booking_check_availability',       $pickuptime, 'check_product_availability');
        $this->loader->add_action('wp_ajax_nopriv_backhoe_booking_check_availability',$pickuptime, 'check_product_availability');

        // calculate price in cart page
        $add_to_cart = new Backhoe_Booking_Add_To_Cart();       
        $this->loader->add_filter( 'woocommerce_loop_add_to_cart_link',   $add_to_cart, 'add_to_cart_button', 10, 2 );        
        $this->loader->add_filter( 'woocommerce_add_cart_item_data',      $add_to_cart, 'add_date_to_cart_item', 10, 3 );
        $this->loader->add_action( 'woocommerce_before_calculate_totals', $add_to_cart, 'before_calculate_totals', 99 );
        $this->loader->add_filter( 'woocommerce_get_item_data',           $add_to_cart, 'display_date_to_cart', 10, 2 );
        $this->loader->add_action( 'woocommerce_checkout_create_order_line_item',$add_to_cart, 'add_date_info_to_order_items', 10, 4 );

        
    }
    
    /**
     * register style and js
     *    
    */
    function register() {
        
        add_action('wp_enqueue_scripts',    array($this,'enqueue_js_css'));
        add_action('admin_enqueue_scripts', array($this,'admin_script_css'));
    }


    /**
     * Load css and scripts
     */
    function enqueue_js_css() {
        
        wp_enqueue_style( 'daterangepickers',    plugins_url("public/css/daterangepicker.css",__DIR__));
        wp_enqueue_style( 'backhoe_booking_app', plugins_url("public/css/app.css",__DIR__));
       
        wp_enqueue_script( 'datetimepicker',     plugins_url("public/js/moment.min.js",__DIR__),array('jquery','jquery-ui-core'),'',true);
        wp_enqueue_script( 'daterangepicker',    plugins_url("public/js/daterangepicker.min.js",__DIR__),array('jquery','jquery-ui-core'),'',true);
        wp_enqueue_script( 'backhoe_booking_default_js', plugins_url("public/js/scripts.js",__DIR__),array('jquery'),'',true);

        // localize the Filter Models script with new data
        $ajax_link = array('template_url' => admin_url( 'admin-ajax.php' ));
        wp_localize_script('backhoe_booking_default_js', 'urltheme', $ajax_link);

    }

    /**
     * Load admin scripts
     */
    function admin_script_css() {

        wp_enqueue_style( 'fullcalendar',    plugins_url("public/css/fullcalendar.css",__DIR__));
        wp_enqueue_style( 'backhoe_booking_admin', plugins_url("admin/css/admin.css",__DIR__));

        wp_enqueue_script( 'productscript',  plugins_url("admin/js/productscript.js",__DIR__),array('jquery'),'',true);        
        wp_enqueue_script( 'datetimepicker', plugins_url("admin/js/moment.min.js",__DIR__),array('jquery','jquery-ui-core'),'',true);
        wp_enqueue_script( 'fullcalendar',   plugins_url("admin/js/fullcalendar.min.js",__DIR__),array('jquery','jquery-ui-core'),'',true);
    }

    /**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}
  
}

add_action( 'init', 'BackhoeBooking\\backhoe_booking_create_custom_product_type' );
 
function backhoe_booking_create_custom_product_type(){
    //use My\Full\NSname;
    if (class_exists('WooCommerce')) {
        class WC_Product_Custom extends \WC_Product_Simple
        {
            public function get_type()
            {
                return 'backhoe_bookin_product';
            }
        }
    }
}
 

