<?php

namespace BackhoeBooking;

/**
 * @since             1.0.0
 * @package           backhoe_booking
 *
 * @wordpress-plugin
 * Plugin Name: Backhoe Advanced Rental & Booking
 * Plugin URI: http://www.themeforest.net/user/Mymoun
 * Description: The most advanced Equipment Rental & Booking Plugin for WooCommerce.
 * Author: Mymoun
 * Author URI: http://www.themeforest.net/user/Mymoun
 * Text Domain: backhoebooking
 * Domain Path: /languages/
 * Version: 1.1.1
 */
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 
 * Rename this for your plugin and update it as you release new versions.
 */
define('BACKHOE_BOOKING_VERSION', '1.0.0');


// Define CONSTANTS
define('BACKHOE_BOOKING_PATH', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/base/Activate.php
 */
function Backhoe_Booking_Activate() {
	require_once BACKHOE_BOOKING_PATH . 'includes/base/Activate.php';
	Backhoe_Booking_Activate::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/base/Deactivate.php
 */
function Backhoe_Booking_deactivate() {
	require_once BACKHOE_BOOKING_PATH . 'includes/base/Deactivate.php';
	Backhoe_Booking_Deactivate::deactivate();
}
/*
* activation
*/
register_activation_hook(__FILE__, 'BackhoeBooking\\Backhoe_Booking_Activate');
/*
* deactivate
*/
register_activation_hook(__FILE__,  'BackhoeBooking\\Backhoe_Booking_deactivate');

require BACKHOE_BOOKING_PATH . 'includes/backhoe_booking.php';

/*
* run class
*/
function Backhoe_Booking_Run() {

	$Backhoe_Booking = new Backhoe_Booking();
	$Backhoe_Booking->register();
}
Backhoe_Booking_Run();
