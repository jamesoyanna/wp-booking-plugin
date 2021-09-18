<?php
namespace BackhoeBooking;
/**
 * 
 *
 * @package    backhoe_booking
 * 
 */
 class Backhoe_Booking_Activate {

        /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.6.0';

    function __construct () {
       // if ( class_exists( 'WooCommerce' ) ) {
                /* Add admin notice */
                add_action( 'admin_notices', array($this,'backhoe_booking_admin_notice_example_notice' ) );
        //}
    }
    public static function activate() {
   
    
      $self = new self();
      if ( ! $self->is_supported_php() ) {
          require_once WC_ABSPATH . 'includes/wc-notice-functions.php';
          wc_print_notice( sprintf( __( 'The Minimum PHP Version Requirement for <b>Backhoe Booking</b> is %s. You are Running PHP %s', 'backhoebooking' ), $self->min_php, phpversion(), 'error' ) );
          exit;
      }

      if ( ! class_exists( 'WooCommerce' ) ) {
         set_transient( 'backhoe_booking_wc_missing_notice', true );
     }
    }

    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php() {
        if ( version_compare( PHP_VERSION, $this->min_php, '<=' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Missing woocomerce notice
     *
     * @since 2.9.16
     *
     * @return void
     */
    public function render_missing_woocommerce_notice() {
      if ( ! get_transient( 'backhoe_booking_wc_missing_notice' ) ) {
          return;
      }

      if ( class_exists( 'WooCommerce' ) ) {
          return delete_transient( 'backhoe_booking_wc_missing_notice' );
      }

      $plugin_url = self_admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' );
      $message    = sprintf( esc_html__( 'Backhoe Booking requires WooCommerce to be installed and active. You can activate %s here.', 'backhoebooking' ), '<a href="' . $plugin_url . '">WooCommerce</a>' );

      echo wp_kses_post( sprintf( '<div class="error"><p><strong>%1$s</strong></p></div>', $message ) );
  }

 
 
 
    /**
     * Admin Notice on Activation.
     * @since 0.1.0
     */
    function backhoe_booking_admin_notice_example_notice(){
    
        /* Check transient, if available display notice */
        
            ?>
            <div class="updated notice is-dismissible ">
                <p><?php echo esc_html__("Thank you for using Backhoe Booking plugin!","backhoebooking"); ?> <strong><?php echo esc_html__("You are awesome","backhoebooking") ?></strong>.</p>
                <button class="button"><a href="<?php echo home_url() ?>/wp-admin/admin.php?page=bachoecalendar"><?php echo esc_html__("Backhoe Rental Setting","backhoebooking"); ?></a></button>
            </div>
            <?php
        
        
    }
 }