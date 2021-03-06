<?php
/**
 * A class for the page providing the basic settings.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The page Basic Settings.
 * 
 * @since 1.0.0
 */
class OPanda_TermsSettings extends OPanda_Settings  {
 
    public $id = 'terms';
    
    /**
     * Sets notices.
     * 
     * @since 1.0.0
     * @return void
     */
    public function init() {
        
        if ( isset( $_GET['onp_table_cleared'] )) {
            $this->success = __('The data has been successfully cleared.', 'bizpanda');
        }
    }
    
    /**
     * Shows the header html of the settings screen.
     * 
     * @since 1.0.0
     * @return void
     */
    public function header() {
        ?>
        <p><?php _e('Configure here Terms of Use and Privacy Policy for locker on your website. It\'s not mandatory, but improves transparency and conversions.', 'optionpanda') ?></p>
        <?php
    }
    
    /**
     * Returns options for the Basic Settings screen. 
     * 
     * @since 1.0.0
     * @return void
     */
    public function getOptions() {
        global $optinpanda;

        $options = array();
        
        $pages = get_pages();
        $result = array();
        
        foreach( $pages as $page ) {
            $result[] = array($page->ID, $page->post_title . ' [ID=' . $page->ID . ']');
        }

        $defaultTermsOfUse = file_get_contents( OPANDA_BIZPANDA_DIR . '/content/terms-of-use.html' );
        $defaultPrivacy = file_get_contents( OPANDA_BIZPANDA_DIR . '/content/privacy-policy.html' ); 

        $options[] = array(
            'type' => 'separator'
        );
        
        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'terms_enabled',
            'title'     => __('Enable Terms of Use', 'bizpanda'),
            'hint'      => __('Set On to show the link to Terms of Use of your website below the Sign-In/Email lockers.', 'bizpanda'),
            'default'   => true
        );
        
        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'privacy_enabled',
            'title'     => __('Enable Privacy Policies', 'bizpanda'),
            'hint'      => __('Set On to show the link to Privacy Policies of your website below the Sign-In/Email lockers.', 'bizpanda'),
            'default'   => true
        );

        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'terms_use_pages',
            'data'      => $result,
            'title'     => __('Use Existing Pages', 'bizpanda'),
            'hint'      => __('Set On, if your website already contains pages for "Terms of Use" and "Privacy Policies" and you want to use them.', 'bizpanda'),
            'default'   => false
        ); 

        $options[] = array(
            'type' => 'separator'
        );
        
        $noPagesWrap = array(
            'type'      => 'div',
            'id'        => 'opanda-nopages-options',
            'items'     => array(

                array(
                    'type'      => 'div',
                    'id'        => 'no-page-opanda-terms-enabled-options',
                    'items'     => array(
                        array(
                            'type'      => 'wp-editor',
                            'name'      => 'terms_of_use_text',
                            'title'     => __('Terms of Use', 'bizpanda'),
                            'hint'      => __('The text of Terms of Use. The link to this text will be shown below the lockers.', 'bizpanda'),
                            'tinymce'   => array(
                                'height' => 250,
                                'content_css' => OPANDA_BIZPANDA_URL . '/assets/admin/css/tinymce.010000.css'
                            ),
                            'default'   => $defaultTermsOfUse
                        ),
                        array(
                            'type' => 'separator'
                        )
                    )
                ),
                array(
                    'type'      => 'div',
                    'id'        => 'no-page-opanda-privacy-enabled-options',
                    'items'     => array(
                        array(
                            'type'      => 'wp-editor',
                            'name'      => 'privacy_policy_text',
                            'title'     => __('Privacy Policy', 'bizpanda'),
                            'hint'      => __('The text of Privacy Policy.  The link to this text will be shown below the lockers.', 'bizpanda'),
                            'tinymce'   => array(
                                'height' => 250,
                                'content_css' => OPANDA_BIZPANDA_URL . '/assets/admin/css/tinymce.010000.css'
                            ),
                            'default'   => $defaultPrivacy
                        ),
                        array(
                            'type' => 'separator'
                        )
                    )
                )
            )
        );

        $pagesWrap = array(
            'type'      => 'div',
            'id'        => 'opanda-pages-options',
            'items'     => array(

                array(
                    'type'      => 'div',
                    'id'        => 'page-opanda-terms-enabled-options',
                    'items'     => array(
                        array(
                            'type'      => 'dropdown',
                            'name'      => 'terms_of_use_page',
                            'data'      => $result,
                            'title'     => __('Terms of Use', 'bizpanda'),
                            'hint'      => __('Select a page which contains the "Terms of Use" for the lockers or/and your website.', 'bizpanda')
                        ),
                        array(
                            'type' => 'separator'
                        )
                    )
                ),
                
                array(
                    'type'      => 'div',
                    'id'        => 'page-opanda-privacy-enabled-options',
                    'items'     => array(
                        array(
                            'type'      => 'dropdown',
                            'name'      => 'privacy_policy_page',
                            'data'      => $result,
                            'title'     => __('Privacy Policy', 'bizpanda'),
                            'hint'      => __('Select a page which contains the "Privacy Policy" for the lockers or/and your website.', 'bizpanda')
                        ),
                        array(
                            'type' => 'separator'
                        )
                    )
                ),
            )
        );
        
        $options[] = $noPagesWrap;
        $options[] = $pagesWrap;
        
        return $options;
    }
}

