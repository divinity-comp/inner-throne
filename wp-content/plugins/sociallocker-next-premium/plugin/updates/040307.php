<?php

/**
 * Re-creating the leads table, adding the fields table for leads, adds the option 'opanda_catch_leads' for lockers.
 * 
 * @since 4.1.2
 */
class SocialLockerUpdate040307 extends Factory325_Update {

   public function install() {

        // updaing the database
        
        global $wpdb;
            
        $leads = "
            CREATE TABLE {$wpdb->prefix}opanda_leads (
              ID int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              lead_display_name varchar(255) DEFAULT NULL,
              lead_name varchar(100) DEFAULT NULL,
              lead_family varchar(100) DEFAULT NULL,
              lead_email varchar(50) NOT NULL,
              lead_date int(11) NOT NULL,
              lead_email_confirmed int(1) NOT NULL DEFAULT 0 COMMENT 'email',
              lead_subscription_confirmed int(1) NOT NULL DEFAULT 0 COMMENT 'subscription',
              lead_ip varchar(45) DEFAULT NULL,
              lead_item_id int(11) DEFAULT NULL,
              lead_post_id int(11) DEFAULT NULL,
              lead_item_title varchar(255) DEFAULT NULL,
              lead_post_title varchar(255) DEFAULT NULL,
              lead_referer text DEFAULT NULL,
              lead_confirmation_code varchar(32) DEFAULT NULL,
              lead_temp text DEFAULT NULL,
              PRIMARY KEY  (ID),
              UNIQUE KEY lead_email (lead_email)
            );";
            
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($leads);
    }
}