<?php
/**
 * WooCommerce Integration
 *
 * @package     Restrict Content Pro
 * @subpackage  Integrations/WooCommerce
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
 */

class RCP_WooCommerce {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.2
	 */
	public function __construct() {

		if( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'data_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'data_display' ) );
		add_action( 'save_post_product', array( $this, 'save_meta' ) );

		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 999999, 2 );
		add_filter( 'woocommerce_product_is_visible', array( $this, 'is_visible' ), 999999, 2 );
		add_filter( 'wc_get_template_part', array( $this, 'hide_template' ), 999999, 3 );
	}

	/**
	 * Register the product settings tab
	 *
	 * @param array $tabs
	 *
	 * @access  public
	 * @since   2.2
	 * @return  array
	 */
	public function data_tab( $tabs ) {

		$tabs['access'] = array(
			'label'  => __( 'Access Control', 'rcp' ),
			'target' => 'rcp_access_control',
			'class'  => array(),
		);

		return $tabs;

	}

	/**
	 * Display product settings
	 *
	 * @access  public
	 * @since   2.2
	 * @return  void
	 */
	public function data_display() {
        ?>
		<div id="rcp_access_control" class="panel woocommerce_options_panel">

			<div class="options_group">
				<p><?php _e( 'Restrict purchasing of this product to:', 'rcp' ); ?></p>
				<?php

				woocommerce_wp_checkbox( array(
					'id'      => '_rcp_woo_active_to_purchase',
					'label'   => __( 'Active subscribers only?', 'rcp' ),
					'cbvalue' => 1
				) );

				$levels = (array) get_post_meta( get_the_ID(), '_rcp_woo_subscription_levels_to_purchase', true );
				foreach ( rcp_get_subscription_levels( 'all' ) as $level ) {
					woocommerce_wp_checkbox( array(
						'name'    => '_rcp_woo_subscription_levels_to_purchase[]',
						'id'      => '_rcp_woo_subscription_level_' . $level->id,
						'label'   => $level->name,
						'value'   => in_array( $level->id, $levels ) ? $level->id : 0,
						'cbvalue' => $level->id
					) );
				}

				woocommerce_wp_select( array(
					'id'      => '_rcp_woo_access_level_to_purchase',
					'label'   => __( 'Access level required?', 'rcp' ),
					'options' => rcp_get_access_levels()
				) );
				?>
			</div>

			<div class="options_group">
				<p><?php _e( 'Restrict viewing of this product to:', 'rcp' ); ?></p>
				<?php

				woocommerce_wp_checkbox( array(
					'id'      => '_rcp_woo_active_to_view',
					'label'   => __( 'Active subscribers only?', 'rcp' ),
					'cbvalue' => 1
				) );

				$levels = (array) get_post_meta( get_the_ID(), '_rcp_woo_subscription_levels_to_view', true );
				foreach ( rcp_get_subscription_levels( 'all' ) as $level ) {
					woocommerce_wp_checkbox( array(
						'name'    => '_rcp_woo_subscription_levels_to_view[]',
						'id'      => '_rcp_woo_subscription_level_to_view_' . $level->id,
						'label'   => $level->name,
						'value'   => in_array( $level->id, $levels ) ? $level->id : 0,
						'cbvalue' => $level->id
					) );
				}

				woocommerce_wp_select( array(
					'id'      => '_rcp_woo_access_level_to_view',
					'label'   => __( 'Access level required?', 'rcp' ),
					'options' => rcp_get_access_levels()
				) );
				?>
			</div>
			<input type="hidden" name="rcp_woocommerce_product_meta_box_nonce" value="<?php echo wp_create_nonce( 'rcp_woocommerce_product_meta_box_nonce' ); ?>" />
		</div>
		<?php
	}

	/**
	 * Saves product access settings
	 *
	 * @param int $post_id ID of the post being saved.
	 *
	 * @access  public
	 * @since   2.2
	 * @return  int|void
	 */
	public function save_meta( $post_id = 0 ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! isset( $_POST['rcp_woocommerce_product_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_woocommerce_product_meta_box_nonce'], 'rcp_woocommerce_product_meta_box_nonce' ) ) {
			return;
		}

		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Check user permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if( isset( $_POST['_rcp_woo_active_to_purchase'] ) ) {

			update_post_meta( $post_id, '_rcp_woo_active_to_purchase', 1 );

		} else {

			delete_post_meta( $post_id, '_rcp_woo_active_to_purchase' );

		}

		if( isset( $_POST['_rcp_woo_access_level_to_purchase'] ) ) {

			update_post_meta( $post_id, '_rcp_woo_access_level_to_purchase', sanitize_text_field( $_POST['_rcp_woo_access_level_to_purchase'] ) );

		} else {

			delete_post_meta( $post_id, '_rcp_woo_access_level_to_purchase' );

		}

		if( isset( $_POST['_rcp_woo_subscription_levels_to_purchase'] ) ) {

			update_post_meta( $post_id, '_rcp_woo_subscription_levels_to_purchase', array_map( 'absint', $_POST['_rcp_woo_subscription_levels_to_purchase'] ) );

		} else {

			delete_post_meta( $post_id, '_rcp_woo_subscription_levels_to_purchase' );

		}

		if( isset( $_POST['_rcp_woo_active_to_view'] ) ) {

			update_post_meta( $post_id, '_rcp_woo_active_to_view', 1 );

		} else {

			delete_post_meta( $post_id, '_rcp_woo_active_to_view' );

		}

		if( isset( $_POST['_rcp_woo_access_level_to_view'] ) ) {

			update_post_meta( $post_id, '_rcp_woo_access_level_to_view', sanitize_text_field( $_POST['_rcp_woo_access_level_to_view'] ) );

		} else {

			delete_post_meta( $post_id, '_rcp_woo_access_level_to_view' );

		}

		if( isset( $_POST['_rcp_woo_subscription_levels_to_view'] ) ) {

			update_post_meta( $post_id, '_rcp_woo_subscription_levels_to_view', array_map( 'absint', $_POST['_rcp_woo_subscription_levels_to_view'] ) );

		} else {

			delete_post_meta( $post_id, '_rcp_woo_subscription_levels_to_view' );

		}

	}

	/**
	 * Restrict the ability to purchase a product
	 *
	 * @param bool       $ret
	 * @param WC_Product $product
	 *
	 * @access  public
	 * @since   2.2
	 * @return  bool
	 */
	public function is_purchasable( $ret, $product ) {

		if( $ret ) {

			$customer     = rcp_get_customer(); // current customer
			$has_access   = true;
			$active_only  = get_post_meta( $product->get_id(), '_rcp_woo_active_to_purchase', true );
			$levels       = (array) get_post_meta( $product->get_id(), '_rcp_woo_subscription_levels_to_purchase', true );
			$access_level = get_post_meta( $product->get_id(), '_rcp_woo_access_level_to_purchase', true );

			if( $active_only ) {

				if( empty( $customer ) || ! $customer->has_active_membership() ) {
					$has_access = false;
				}

			}

			if( is_array( $levels ) && ! empty( $levels[0] ) ) {

				if( empty( $customer ) || ! count( array_intersect( rcp_get_customer_membership_level_ids( $customer->get_id() ), $levels ) ) ) {
					$has_access = false;
				}

			}

			if( $access_level ) {

				if ( empty( $customer ) || ! $customer->has_access_level( $access_level  ) ) {
					$has_access = false;
				}

			}

			$ret = $has_access;

		}

		return $ret;
	}

	/**
	 * Restrict the visibility of a product
	 *
	 * @param bool $ret
	 * @param int $product_id
	 *
	 * @access  public
	 * @since   2.2
	 * @return  bool
	 */
	public function is_visible( $ret, $product_id ) {

		if( ! $ret ) {
			return $ret;
		}

		if ( current_user_can( 'edit_post', $product_id ) ) {
			return true;
		}

		$customer     = rcp_get_customer(); // current customer
		$active_only  = get_post_meta( $product_id, '_rcp_woo_active_to_view', true );
		$levels       = (array) get_post_meta( $product_id, '_rcp_woo_subscription_levels_to_view', true );
		$access_level = get_post_meta( $product_id, '_rcp_woo_access_level_to_view', true );

		if( $active_only ) {

			if( empty( $customer ) || ! $customer->has_active_membership() ) {
				$ret = false;
			}

		}

		if( is_array( $levels ) && ! empty( $levels[0] ) ) {

			if( empty( $customer ) || ! count( array_intersect( rcp_get_customer_membership_level_ids( $customer->get_id() ), $levels ) ) ) {
				$ret = false;
			}

		}

		if( $access_level ) {

			if ( empty( $customer ) || ! $customer->has_access_level( $access_level ) ) {
				$ret = false;
			}

		}

		if ( true === rcp_is_post_taxonomy_restricted( $product_id, 'product_cat' ) ) {
			$ret = false;
		}

		if ( true === rcp_is_post_taxonomy_restricted( $product_id, 'product_tag' ) ) {
			$ret = false;
		}

		return $ret;
	}

	/**
	 * Loads the restricted content template if required.
	 *
	 * @param string $template
	 * @param string $slug
	 * @param string $name
	 *
	 * @access  public
	 * @since   2.5
	 * @return  string
	 */
	public function hide_template( $template, $slug, $name ) {

		$product_id = get_the_ID();

		if ( ! is_singular( 'product' ) ) {
			return $template;
		}

		if( 'content-single-product' !== $slug . '-' . $name ) {
			return $template;
		}

		if ( current_user_can( 'edit_post', $product_id ) ) {
			return $template;
		}

		$customer = rcp_get_customer(); // current customer


		$active_only    = get_post_meta( $product_id, '_rcp_woo_active_to_view', true );
		$levels         = get_post_meta( $product_id, '_rcp_woo_subscription_levels_to_view', true );
		$access_level   = get_post_meta( $product_id, '_rcp_woo_access_level_to_view', true );

		$product_cat    = rcp_is_post_taxonomy_restricted( $product_id, 'product_cat' );
		$product_tag    = rcp_is_post_taxonomy_restricted( $product_id, 'product_tag' );

		/**
		 * rcp_is_post_taxonomy_restricted() returns:
		 * - true when restrictions are found for the current user
		 * - false when restrictions are not found for the current user
		 * - -1 when no terms are assigned, for which we don't care.
		 * We're normalizing the value here. If the value is false,
		 * the user has already passed the restriction checks.
		 */
		$cat_restricted = true === $product_cat ? true : false;
		$tag_restricted = true === $product_tag ? true : false;

		// Return early if no restrictions
		if ( ! $active_only && empty( $levels[0] ) && ! $access_level && ! $cat_restricted && ! $tag_restricted ) {
			return $template;
		}

		$visible = ( $cat_restricted || $tag_restricted ) ? false : true;

		// Active subscription setting
		if ( $active_only && ( empty( $customer ) || ! $customer->has_active_membership() ) ) {
			$visible = false;
		}

		// Membership level setting
		if ( is_array( $levels ) && ( empty( $customer ) || ! count( array_intersect( rcp_get_customer_membership_level_ids( $customer->get_id() ), $levels ) ) ) ) {
			$visible = false;
		}

		// User level setting
		if ( $access_level && ( empty( $customer ) || ! $customer->has_access_level( $access_level ) ) ) {
			$visible = false;
		}

		if ( $visible ) {
			return $template;
		}

		return rcp_get_template_part( 'woocommerce', 'single-no-access', false );
	}

}
new RCP_WooCommerce;