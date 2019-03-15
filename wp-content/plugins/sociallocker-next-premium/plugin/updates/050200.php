<?php

/**
 * Updates options.
 * @since 5.2.0
 */
class SocialLockerUpdate050200 extends Factory325_Update {

   public function install() {

        $termsEnabled = get_option('opanda_terms_enabled', 1);
        update_option('opanda_privacy_enabled', $termsEnabled ? 1 : 0);
    }
}