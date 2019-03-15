<?php
/**
 * Memberships List Table
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro
 * @license   GPL2+
 * @since     3.0
 */

namespace RCP\Admin;

/**
 * Class Memberships_Table
 *
 * @since   3.0
 * @package RCP\Admin
 */
class Memberships_Table extends List_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.0
	 * @see   WP_List_Table::__construct()
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Membership', 'rcp' ),
			'plural'   => __( 'Memberships', 'rcp' ),
			'ajax'     => false,
		) );

		$this->process_bulk_action();
		$this->get_counts();
	}

	/**
	 * Get the base URL for the memberships list table.
	 *
	 * @since 3.0
	 * @return string Base URL.
	 */
	public function get_base_url() {
		return rcp_get_memberships_admin_page();
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @since 3.0
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'customer'        => __( 'Customer', 'rcp' ),
			'object_id'       => __( 'Membership', 'rcp' ),
			'status'          => __( 'Status', 'rcp' ),
			'auto_renew'      => __( 'Recurring', 'rcp' ),
			'created_date'    => __( 'Created', 'rcp' ),
			'expiration_date' => __( 'Expiration', 'rcp' ),
		);

		/*
		 * Backwards compatibility: add an "extra" column if someone is hooking into the old action to add
		 * their own column. Everything gets bundled into one column because this is the only way we can realistically
		 * do it.
		 */
		if ( has_action( 'rcp_members_page_table_header' ) ) {
			$columns['custom'] = __( 'Extra', 'rcp' );
		}

		$columns = apply_filters( 'rcp_memberships_list_table_columns', $columns );

		return $columns;
	}

	/**
	 * Retrieve the sortable columns.
	 *
	 * @since 3.0
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'object_id'       => array( 'object_id', false ),
			'status'          => array( 'status', false ),
			'auto_renew'      => array( 'auto_renew', false ),
			'created_date'    => array( 'created_date', false ),
			'expiration_date' => array( 'expiration_date', false )
		);
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 3.0
	 * @return string
	 */
	protected function get_primary_column_name() {
		return 'customer';
	}

	/**
	 * This function renders any other columns in the list table.
	 *
	 * @param \RCP_Membership $membership  Membership object.
	 * @param string          $column_name The name of the column
	 *
	 * @since 3.0
	 * @return string Column Name
	 */
	public function column_default( $membership, $column_name ) {

		$value = '';

		/*
		 * Backwards compatibility: show content of custom columns from old action hook.
		 */
		if ( 'custom' == $column_name && has_action( 'rcp_members_page_table_column' ) ) {
			ob_start();
			do_action( 'rcp_members_page_table_column', $membership->get_customer()->get_user_id() );
			$column_content = ob_get_clean();

			$value = wp_strip_all_tags( $column_content );
		}

		/**
		 * Filters the column value.
		 *
		 * @param string          $value      Column value.
		 * @param \RCP_Membership $membership Membership object.
		 *
		 * @since 3.0
		 */
		$value = apply_filters( 'rcp_memberships_list_table_column_' . $column_name, $value, $membership );

		return $value;

	}

	/**
	 * Render the checkbox column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_cb( $membership ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'membership_id',
			$membership->get_id()
		);
	}

	/**
	 * Render the "Customer" column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_customer( $membership ) {

		$membership_id       = $membership->get_id();
		$user_id             = $membership->get_customer()->get_user_id();
		$user                = get_userdata( $user_id );
		$display_name        = ! empty( $user->display_name ) ? $user->display_name : $user->user_login;
		$edit_membership_url = rcp_get_memberships_admin_page( array(
			'membership_id' => absint( $membership_id ),
			'view'          => 'edit'
		) );
		$edit_customer_url   = rcp_get_customers_admin_page( array(
			'customer_id' => $membership->get_customer()->get_id(),
			'view'        => 'edit'
		) );
		$cancel_url          = wp_nonce_url( add_query_arg( array(
			'rcp-action'    => 'cancel_membership',
			'membership_id' => $membership_id
		), $this->get_base_url() ), 'rcp-cancel-nonce' );

		$actions = array(
			'edit_membership' => '<a href="' . esc_url( $edit_membership_url ) . '">' . __( 'Edit Membership', 'rcp' ) . '</a>',
			'edit_customer'   => '<a href="' . esc_url( $edit_customer_url ) . '">' . __( 'Edit Customer', 'rcp' ) . '</a>',
			'membership_id'   => '<span class="rcp-id-col">' . sprintf( __( 'ID: %d', 'rcp' ), $membership_id ) . '</span>'
		);

		if ( $membership->can_cancel() ) {
			$actions['cancel'] = '<a href="' . esc_url( $cancel_url ) . '">' . __( 'Cancel', 'rcp' ) . '</a>';
		}

		ob_start();
		/**
		 * @deprecated 3.0 Use `rcp_memberships_list_table_row_actions` instead.
		 */
		do_action( 'rcp_member_row_actions', $membership->get_customer()->get_user_id() );
		$custom_row_actions = ob_get_clean();
		if ( $custom_row_actions ) {
			$actions['custom_row_actions'] = $custom_row_actions;
		}

		/**
		 * Filters the row actions.
		 *
		 * @param array           $actions    Default actions.
		 * @param \RCP_Membership $membership Membership object.
		 *
		 * @since 3.0
		 */
		$actions = apply_filters( 'rcp_memberships_list_table_row_actions', $actions, $membership );

		return '<strong><a class="row-title" href="' . esc_url( $edit_membership_url ) . '">' . esc_html( $display_name ) . '</a></strong>' . $this->row_actions( $actions );

	}

	/**
	 * Render the "Membership" column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_object_id( $membership ) {
		return $membership->get_membership_level_name();
	}

	/**
	 * Render the "Status" column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_status( $membership ) {
		return rcp_print_membership_status( $membership->get_id(), false );
	}

	/**
	 * Render the "Recurring" column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_auto_renew( $membership ) {
		return $membership->is_recurring() ? __( 'Yes', 'rcp' ) : __( 'No', 'rcp' );
	}

	/**
	 * Render the "Created" column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_created_date( $membership ) {
		return $membership->get_created_date();
	}

	/**
	 * Render the "Expiration" column.
	 *
	 * @param \RCP_Membership $membership
	 *
	 * @since 3.0
	 * @return string
	 */
	public function column_expiration_date( $membership ) {
		return $membership->get_expiration_date();
	}

	/**
	 * Message to be displayed when there are no memberships.
	 *
	 * @since 3.0
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No memberships found.', 'rcp' );
	}

	/**
	 * Retrieve the bulk actions.
	 *
	 * @since 3.0
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'activate' => __( 'Activate', 'rcp' ),
			'expire'   => __( 'Expire', 'rcp' ),
			'cancel'   => __( 'Cancel', 'rcp' ),
			'delete'   => __( 'Permanently Delete', 'rcp' )
		);
	}

	/**
	 * Process bulk actions.
	 *
	 * @since 3.0
	 * @return void
	 */
	public function process_bulk_action() {

		// Bail if a nonce was not supplied.
		if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'rcp-bulk-memberships' ) ) {
			return;
		}

		$ids = wp_parse_id_list( (array) $this->get_request_var( 'membership_id', false ) );

		// Bail if no IDs
		if ( empty( $ids ) ) {
			return;
		}

		foreach ( $ids as $id ) {
			$membership = rcp_get_membership( absint( $id ) );

			if ( empty( $membership ) ) {
				continue;
			}

			switch ( $this->current_action() ) {
				case 'activate':
					$membership->activate();
					break;

				case 'expire':
					$membership->expire();
					break;

				case 'cancel':
					if ( $membership->can_cancel() ) {
						$membership->cancel_payment_profile();
					} else {
						$membership->cancel();
					}
					break;

				case 'delete':
					$membership->disable(); // we don't truly delete
					break;
			}
		}

	}

	/**
	 * Retrieve the membership counts.
	 *
	 * @since 3.0
	 * @return void
	 */
	public function get_counts() {
		$this->counts = rcp_get_membership_counts();
	}

	/**
	 * Retrieve memberships data.
	 *
	 * @since 3.0
	 * @return array
	 */
	public function memberships_data() {
		$search_type = $this->get_request_var( 'search_type', '' );

		$args = array(
			'number'    => $this->per_page,
			'offset'    => $this->get_offset(),
			'orderby'   => sanitize_text_field( $this->get_request_var( 'orderby', 'date_modified' ) ),
			'order'     => sanitize_text_field( $this->get_request_var( 'order', 'DESC' ) ),
			'object_id' => sanitize_text_field( $this->get_request_var( 'object_id', '' ) ),
			'status'    => $this->get_status(),
		);

		/*
		 * Filter by customer ID
		 */
		$customer_id = $this->get_request_var( 'customer_id' );
		if ( ! empty( $customer_id ) ) {
			$args['customer_id'] = absint( $customer_id );
		}

		$search = $this->get_search();
		if ( ! empty( $search ) ) {
			if ( 'user' == $search_type ) {
				/*
				 * Search by user account
				 * This process sucks because our query class doesn't do joins.
				 */

				// First we have to search for user accounts.
				$user_ids = get_users( array(
					'number' => -1,
					'search' => '*' . $search . '*',
					'fields' => 'ids'
				) );

				// No user results - bail.
				if ( empty( $user_ids ) ) {
					return array();
				}

				// Now get all customers based on these accounts.
				$customer_ids = rcp_get_customers( array(
					'number'      => 999,
					'user_id__in' => $user_ids,
					'fields'      => 'ID'
				) );

				// No customer results - bail.
				if ( empty( $customer_ids ) ) {
					return array();
				}

				// Finally, include these customer IDs in the memberships query.
				$args['customer_id__in'] = $customer_ids;

			} else {
				/*
				 * Search by any membership field.
				 */
				$args['search'] = $search;
			}
		}

		return rcp_get_memberships( $args );
	}

	/**
	 * Setup the final data for the table.
	 *
	 * @since 3.0
	 * @return void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->memberships_data();

		$status = $this->get_status( 'total' );

		// Setup pagination
		$this->set_pagination_args( array(
			'total_items' => $this->counts[$status],
			'per_page'    => $this->per_page,
			'total_pages' => ceil( $this->counts[$status] / $this->per_page )
		) );
	}

	/**
	 * Display extra table nav. This includes the membership level filter.
	 *
	 * @param string $which
	 *
	 * @since 3.0
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' !== $which ) {
			return;
		}

		$level_id = $this->get_request_var( 'object_id', '' );
		$levels   = rcp_get_subscription_levels( 'all' );

		if ( empty( $levels ) ) {
			return;
		}
		?>
		<div class="alignleft actions">
			<label for="rcp-memberships-level-filter" class="screen-reader-text"><?php _e( 'Filter by membership level', 'rcp' ); ?></label>
			<select id="rcp-memberships-level-filter" name="object_id">
				<option value="" <?php selected( $level_id, '' ); ?>><?php _e( 'All Membership Levels', 'rcp' ); ?></option>
				<?php foreach ( $levels as $level ) : ?>
					<option value="<?php echo esc_attr( $level->id ); ?>" <?php selected( $level_id, $level->id ); ?>><?php echo esc_html( $level->name ); ?></option>
				<?php endforeach; ?>
			</select>

			<?php submit_button( __( 'Filter' ), '', 'filter_action', false ); ?>
		</div>
		<?php

	}

	/**
	 * Show the search field.
	 *
	 * @param string $text     Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @since 3.0
	 */
	public function search_box( $text, $input_id ) {

		// Bail if no items and no search
		if ( ! $this->get_search() && ! $this->has_items() ) {
			return;
		}

		$orderby     = $this->get_request_var( 'orderby' );
		$order       = $this->get_request_var( 'order' );
		$search_type = $this->get_request_var( 'search_type', 'user' );
		$input_id    = $input_id . '-search-input';

		if ( ! empty( $orderby ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $orderby ) . '" />';
		}

		if ( ! empty( $order ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $order ) . '" />';
		}

		?>

		<p class="search-box">
			<label class="screen-reader-text" for="rcp-memberships-search-type"><?php esc_html_e( 'Choose a field to search', 'rcp' ); ?></label>
			<select id="rcp-memberships-search-type" name="search_type" style="float:left;">
				<option value="user" <?php selected( $search_type, 'user' ); ?>><?php _e( 'User Account (name, email, login)', 'rcp' ); ?></option>
				<option value="gateway_id" <?php selected( $search_type, 'gateway_id' ); ?>><?php _e( 'Gateway Subscription ID', 'rcp' ); ?></option>
			</select>
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( esc_html( $text ), 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>

		<?php
	}

}