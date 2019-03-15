<?php
/*
Plugin original: https://wordpress.org/plugins/svn-updater/
By: Viktor Szépe (http://www.online1.hu/webdesign/)
GitHub: https://github.com/szepeviktor/svn-updater
v0.1.0
*/

if ( ! function_exists( 'add_filter' ) ) {
    ob_get_level() && ob_end_clean();
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

class bbptk_SVN_Updater {

    private $plugin;

    public function __construct() {

        add_action( 'update-custom_' . 'svn-update-plugin', array( $this, 'svn_update_plugin' ) );
    }

    public function svn_update_plugin() {

        global $title, $parent_file, $submenu_file;

        if ( ! current_user_can( 'update_plugins' ) )
            wp_die( __( 'You do not have sufficient permissions to update plugins for this site.' ) );

        check_admin_referer( 'svn_update_plugin' );

        $this->plugin = $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        if ( empty( $plugin ) ) {
            wp_die( 'Plugin name is missing.' );
        }

        add_filter( 'site_transient_update_plugins', array( $this, 'rewrite_update_plugins_url' ) );

        $title = __( 'Update Plugin' );
        $parent_file = 'plugins.php';
        $submenu_file = 'plugins.php';

        wp_enqueue_script( 'updates' );
        require_once( ABSPATH . 'wp-admin/admin-header.php' );

        $nonce = 'upgrade-plugin_' . $plugin;
        $url = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin );

        $upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin' ) ) );
        $upgrader->upgrade( $plugin );

        include( ABSPATH . 'wp-admin/admin-footer.php' );
    }

    public function rewrite_update_plugins_url( $transient ) {

        $trunk_zip_url_template = 'https://downloads.wordpress.org/plugin/%s.zip';

        if ( ! isset( $transient->response[ $this->plugin ] )
            && isset( $transient->no_update[ $this->plugin ] )
        ) {
            $transient->response[ $this->plugin ] = $transient->no_update[ $this->plugin ];
        }
        if ( isset( $transient->response[ $this->plugin ] ) ) {
            $slug = $transient->response[ $this->plugin ]->slug;
            $transient->response[ $this->plugin ]->package = sprintf( $trunk_zip_url_template, $slug );
        }

        return $transient;
    }
}

new bbptk_SVN_Updater();
