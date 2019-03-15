<?php
/*
 * FooBox Admin Menu class
 */

if ( ! class_exists( 'FooBox_Admin_Menu' ) ) {

	class FooBox_Admin_Menu {

		function __construct() {
			add_action( 'admin_menu', array( $this, 'register_menu_items' ) );
		}

		/**
		 * @todo add context to the translations
		 */
		function register_menu_items() {

			add_menu_page(
				__( 'Getting Started With FooBox', 'foobox-image-lightbox' ),
				__( 'FooBox', 'foobox-image-lightbox' ),
				'manage_options',
				FOOBOX_BASE_SLUG,
				array( $this, 'render_getting_started' ),
				'dashicons-grid-view'
			);

			add_submenu_page(
				FOOBOX_BASE_SLUG,
				__( 'Getting Started With FooBox', 'foobox-image-lightbox' ),
				__( 'Getting Started', 'foobox-image-lightbox' ),
				'manage_options',
				FOOBOX_BASE_SLUG,
				array( $this, 'render_getting_started' )
			);

			add_submenu_page(
				FOOBOX_BASE_SLUG,
				__( 'FooBox Settings', 'foobox-image-lightbox' ),
				__( 'Settings', 'foobox-image-lightbox' ),
				'manage_options',
				FOOBOX_BASE_PAGE_SLUG_SETTINGS,
				array( $this, 'render_settings' )
			);

			//allow things to add their own menu items afterwards
			foobox_action_admin_menu_after();
		}

		function render_settings() {
			foobox_action_admin_menu_render_settings();
		}

		function render_getting_started() {
			require_once FOOBOX_BASE_PATH . 'includes/admin/view-getting-started.php';
		}
	}
}
