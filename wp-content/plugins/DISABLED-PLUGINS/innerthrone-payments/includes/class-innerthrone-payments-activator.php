<?php

/**
 * Fired during plugin activation
 *
 * @link       http://adhipg.in/
 * @since      1.0.0
 *
 * @package    Innerthrone_Payments
 * @subpackage Innerthrone_Payments/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Innerthrone_Payments
 * @subpackage Innerthrone_Payments/includes
 * @author     Adhip Gupta <me@adhipg.in>
 */
class Innerthrone_Payments_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
    global $wpdb;
    $query = "
      CREATE TABLE IF NOT EXISTS {$wpdb->prefix}stripe_payments (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(127) NOT NULL,
        `amount` int(11) NOT NULL,
        `number_payments` int(3) NOT NULL DEFAULT 1,
        `created_on` datetime NOT NULL,
        `stripe_customer_id` VARCHAR(255) DEFAULT '',
        `stripe_charge_id` VARCHAR(255) DEFAULT '',
        `stripe_response` TEXT DEFAULT '',
        `user_id` INT(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
      );
    ";

    $wpdb->query($query);
	}

}
