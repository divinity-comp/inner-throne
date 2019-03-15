<?php

class RCP_Member_Tests extends WP_UnitTestCase {

	/**
	 * User object.
	 *
	 * @var WP_User
	 */
	protected $user;

	/**
	 * Customer object.
	 *
	 * @var RCP_Customer
	 */
	protected $customer;

	/**
	 * Membership object.
	 *
	 * @var RCP_Membership
	 */
	protected $membership;

	/**
	 * Deprecated member object.
	 *
	 * @var RCP_Member
	 */
	protected $member;

	/**
	 * ID of membership level #1.
	 *
	 * @var int
	 */
	protected $level_id;

	/**
	 * ID of membership level #2.
	 *
	 * @var int
	 */
	protected $level_id_2;

	/**
	 * ID of membership level #3.
	 *
	 * @var int
	 */
	protected $level_id_3;

	/**
	 * ID of free membership level.
	 *
	 * @var int
	 */
	protected $free_level;

	/**
	 * ID of membership level that grants "editor" role.
	 *
	 * @var int
	 */
	protected $level_editor;

	/**
	 * ID of membership level that's set up as a payment plan.
	 *
	 * @var int
	 */
	protected $level_payment_plan;

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Term #1.
	 *
	 * @var array
	 */
	protected $term;

	/**
	 * Term #2.
	 *
	 * @var array
	 */
	protected $term_2;

	/**
	 * Setup
	 */
	public function setUp() {
		parent::setUp();

		// Create user and customer record.

		$user = wp_insert_user( array(
			'user_login' => 'test',
			'user_pass'  => 'pass',
			'first_name' => 'Tester',
			'user_email' => 'test@test.com'
		) );

		$this->member = new RCP_Member( $user );
		$this->user   = get_userdata( $user );

		$customer_id = rcp_add_customer( array(
			'user_id' => $user
		) );

		$this->customer = rcp_get_customer( $customer_id );

		// Create membership levels.

		$levels = new RCP_Levels;

		$this->level_id = $levels->insert( array(
			'name'          => 'Gold',
			'duration'      => 1,
			'duration_unit' => 'month',
			'level'         => 1,
			'status'        => 'active',
			'price'         => 10
		) );

		$this->level_id_2 = $levels->insert( array(
			'name'          => 'Silver',
			'duration'      => 1,
			'duration_unit' => 'month',
			'status'        => 'active',
			'level'         => 3
		) );

		$this->level_id_3 = $levels->insert( array(
			'name'          => 'Bronze',
			'duration'      => 1,
			'duration_unit' => 'day',
			'status'        => 'active',
			'level'         => 3
		) );

		$this->free_level = $levels->insert( array(
			'name'          => 'Free',
			'duration'      => 0,
			'duration_unit' => 'day',
			'status'        => 'active',
			'price'         => 0
		) );

		$this->level_editor = $levels->insert( array(
			'name'          => 'Editor',
			'duration'      => 1,
			'duration_unit' => 'month',
			'status'        => 'active',
			'role'          => 'editor'
		) );

		$this->level_payment_plan = $levels->insert( array(
			'name'                => 'Payment Plan',
			'duration'            => 1,
			'duration_unit'       => 'day',
			'status'              => 'active',
			'maximum_renewals'    => 2,
			'after_final_payment' => 'lifetime'
		) );

		// Add membership to level #1 to customer.
		$membership_id    = $this->customer->add_membership( array(
			'object_id'        => $this->level_id,
			'status'           => 'active',
			'initial_amount'   => 10.00,
			'recurring_amount' => 10.00
		) );
		$this->membership = rcp_get_membership( $membership_id );

		// Create post.

		$this->post_id = wp_insert_post( array(
			'post_title'  => 'Test',
			'post_status' => 'publish',
		) );

		// Set up a restricted taxonomy term for use in some tests.
		$this->term = wp_insert_term( 'test', 'category' );
		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id ) ) );

		$this->term_2 = wp_insert_term( 'test2', 'category' );
		update_term_meta( $this->term_2['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id_2 ) ) );

	}

	/**
	 * Customer object should be an instance of `RCP_Customer`.
	 *
	 * @covers RCP_Customer
	 * @since  3.0
	 */
	public function test_customer_object() {
		$this->assertInstanceOf( 'RCP_Customer', $this->customer );
	}

	/**
	 * Membership object should be an instance of `RCP_Membership`.
	 *
	 * @covers RCP_Membership
	 * @since  3.0
	 */
	public function test_membership_object() {
		$this->assertInstanceOf( 'RCP_Membership', $this->membership );
	}

	/**
	 * @covers RCP_Membership::get_customer()
	 * @since  3.0
	 */
	public function test_get_customer_from_membership() {
		$this->assertInstanceOf( 'RCP_Customer', $this->membership->get_customer() );
	}

	/**
	 * @covers RCP_Customer::get_user_id()
	 * @since  3.0
	 */
	public function test_get_user_id_from_customer() {
		$this->assertEquals( $this->user->ID, $this->customer->get_user_id() );
	}

	/**
	 * Test setting membership status.
	 *
	 * @covers RCP_Membership::set_status()
	 */
	public function test_set_status() {

		$this->membership->set_status( 'active' );
		$this->assertEquals( 'active', $this->membership->get_status() );

		$this->membership->set_status( 'pending' );
		$this->assertEquals( 'pending', $this->membership->get_status() );

		$this->membership->set_status( 'cancelled' );
		$this->assertEquals( 'cancelled', $this->membership->get_status() );

		$this->membership->set_status( 'expired' );
		$this->assertEquals( 'expired', $this->membership->get_status() );

	}

	/**
	 * Test getting status.
	 *
	 * @covers RCP_Membership::get_status()
	 * @covers ::rcp_get_status()
	 */
	public function test_get_status() {

		$this->assertEquals( 'active', $this->membership->get_status() );
		$this->assertEquals( 'active', rcp_get_status( $this->user->ID ) );

	}

	/**
	 * Test getting the expiration date.
	 *
	 * @covers RCP_Membership::get_expiration_date()
	 */
	public function test_get_expiration_date() {

		$this->assertInternalType( 'string', $this->membership->get_expiration_date() );

		$this->membership->set_expiration_date( 'none' );

		$this->assertEquals( 'none', $this->membership->get_expiration_date() );

		$this->membership->set_expiration_date( '2025-01-01 00:00:00' );

		$this->assertEquals( date_i18n( get_option( 'date_format' ), strtotime( '2025-01-01 00:00:00' ) ), $this->membership->get_expiration_date() );
		$this->assertEquals( '2025-01-01 00:00:00', $this->membership->get_expiration_date( false ) );
	}

	/**
	 * Test getting the expiration timestamp.
	 *
	 * @covers RCP_Membership::get_expiration_time()
	 */
	public function test_get_expiration_time() {

		$this->membership->set_expiration_date( 'none' );
		$this->assertFalse( $this->membership->get_expiration_time() );

		$this->membership->set_expiration_date( date( 'Y-n-d 23:59:59' ) );
		$this->assertInternalType( 'int', $this->membership->get_expiration_time() );

	}

	/**
	 * Test setting the expiration date.
	 *
	 * @covers RCP_Membership::set_expiration_date()
	 */
	function test_set_expiration_date() {

		$this->membership->set_expiration_date( '2025-01-01 00:00:00' );

		$this->assertEquals( date_i18n( get_option( 'date_format' ), strtotime( '2025-01-01 00:00:00' ) ), $this->membership->get_expiration_date() );
		$this->assertEquals( '2025-01-01 00:00:00', $this->membership->get_expiration_date( false ) );

	}

	/**
	 * Test expiration date calculations.
	 *
	 * @covers RCP_Membership::calculate_expiration()
	 */
	function test_calculate_expiration() {

		$expiration = $this->membership->calculate_expiration( true );

		$this->membership->set_expiration_date( $expiration );
		$this->assertEquals( $expiration, $this->membership->get_expiration_date( false ) );
		$this->assertEquals( date( 'Y-n-d', strtotime( '+1 month' ) ), date( 'Y-n-d', $this->membership->get_expiration_time() ) );

		// Now manually set expiration to last day of the month to force a date "walk".
		// See https://github.com/pippinsplugins/restrict-content-pro/issues/239

		$this->membership->set_expiration_date( date( 'Y-n-d 23:59:59', strtotime( 'October 31, 2019' ) ) );
		$this->membership->set_status( 'active' );

		$expiration = $this->membership->calculate_expiration();
		$this->membership->set_expiration_date( $expiration );
		$this->assertEquals( '2019-12-01 23:59:59', date( 'Y-n-d H:i:s', $this->membership->get_expiration_time() ) );

		// Now test a one-day subscription
		$this->membership->set_object_id( $this->level_id_3 );

		$expiration = rcp_calculate_subscription_expiration( $this->level_id_3, false );
		$this->membership->set_expiration_date( $expiration );
		$this->assertEquals( date( 'Y-n-d 23:59:59', strtotime( '+1 day' ) ), date( 'Y-n-d H:i:s', $this->membership->get_expiration_time() ) );

	}

	/**
	 * Test expired memberships.
	 */
	function test_is_expired() {

		$this->membership->set_status( 'active' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 month' ) ) );

		$this->assertFalse( $this->membership->is_expired() );

		$this->membership->set_expiration_date( '2014-01-01 00:00:00' );
		$this->assertTrue( $this->membership->is_expired() );

		$this->membership->set_expiration_date( '2025-01-01 00:00:00' );
		$this->assertFalse( $this->membership->is_expired() );

	}

	/**
	 * Test recurring status.
	 *
	 * @covers RCP_Membership::is_recurring()
	 */
	function test_is_recurring() {

		$this->assertFalse( $this->membership->is_recurring() );

		$this->membership->set_recurring( true );

		$this->assertTrue( $this->membership->is_recurring() );

		$this->membership->set_recurring( false );

		$this->assertFalse( $this->membership->is_recurring() );

		$this->membership->set_recurring( 1 );

		$this->assertTrue( $this->membership->is_recurring() );

		$this->membership->set_recurring( 0 );

		$this->assertFalse( $this->membership->is_recurring() );

		$this->membership->set_recurring( 1 );
		$this->membership->cancel();

		$this->assertFalse( $this->membership->is_recurring() );

	}

	/**
	 * Test renewal.
	 *
	 * @covers RCP_Membership::renew()
	 */
	function test_renew() {

		$this->membership->set_expiration_date( '2014-01-01 00:00:00' );
		$this->assertTrue( $this->membership->is_expired() );

		// Should be false when no subscription ID is set
		$this->membership->set_object_id( 0 );
		$this->assertFalse( $this->membership->renew() );

		$this->membership->set_object_id( $this->level_id );
		$this->membership->renew();

		$this->assertFalse( $this->membership->is_expired() );
		$this->assertEquals( date_i18n( get_option( 'date_format' ), strtotime( '+1 month' ) ), $this->membership->get_expiration_date() );

	}

	/**
	 * Test cancellation.
	 *
	 * @covers RCP_Membership::cancel()
	 */
	function test_cancel() {

		$this->membership->set_status( 'active' );
		$this->membership->set_recurring( true );
		$this->membership->cancel();

		// Should still be active since the expiration date is in the future
		$this->assertTrue( $this->membership->is_active() );
		$this->assertFalse( $this->membership->is_recurring() );
		$this->assertEquals( 'cancelled', $this->membership->get_status() );

	}

	/**
	 * Test trialling status.
	 *
	 * @covers RCP_Membership::is_trialing()
	 */
	function test_is_trialing() {

		$this->assertFalse( $this->membership->is_trialing() );

		$this->membership->set_status( 'active' );

		$this->membership->update( array(
			'trial_end_date' => date( 'Y-n-d 23:59:59', strtotime( '+1 month' ) )
		) );

		$this->assertTrue( $this->membership->is_trialing() );

		$this->membership->cancel();
		$this->assertTrue( $this->membership->is_trialing() );

		$this->membership->set_status( 'expired' );
		$this->assertFalse( $this->membership->is_trialing() );

		$this->membership->set_status( 'pending' );
		$this->assertFalse( $this->membership->is_trialing() );


	}

	/**
	 * Test whether a customer has trialed.
	 *
	 * @covers RCP_Customer::has_trialed()
	 */
	function test_has_trialed() {

		$this->assertFalse( $this->customer->has_trialed() );

		$this->membership->update( array(
			'trial_end_date' => date( 'Y-n-d 23:59:59', strtotime( '+1 month' ) )
		) );

		$this->assertTrue( $this->customer->has_trialed() );

	}

	/**
	 * Test user role being added and removed based on membership status/actions.
	 */
	public function test_membership_role() {

		$user_id = $this->customer->get_user_id();

		$this->assertFalse( in_array( 'editor', $this->user->roles ) );

		// Give the user a new membership to the editor level. Editor role should be applied.
		$membership_id = $this->customer->add_membership( array(
			'object_id' => $this->level_editor,
			'status'    => 'active'
		) );
		$membership    = rcp_get_membership( $membership_id );

		$this->user = get_userdata( $user_id );

		$this->assertTrue( in_array( 'editor', $this->user->roles ) );

		// When membership expires, role should be removed.
		$membership->set_status( 'expired' );

		$this->user = get_userdata( $user_id );

		$this->assertFalse( in_array( 'editor', $this->user->roles ) );

		// When membership is renewed, role should be re-added.
		$membership->renew();

		$this->user = get_userdata( $user_id );

		$this->assertTrue( in_array( 'editor', $this->user->roles ) );

	}

	/**
	 * A user with no membership at all should be able to access an unrestricted post.
	 *
	 * @covers RCP_Customer::can_access()
	 */
	function test_can_access_unrestricted_post() {

		rcp_delete_membership( $this->membership->get_id() );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
	}

	/**
	 * Test ability to access restricted posts.
	 *
	 * @covers RCP_Customer::can_access()
	 */
	function test_can_access() {

		// Give the customer a membership to level #1.
		$membership_id    = $this->customer->add_membership( array(
			'object_id' => $this->level_id,
			'status'    => 'active'
		) );
		$this->membership = rcp_get_membership( $membership_id );

		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );

		update_post_meta( $this->post_id, 'rcp_access_level', 4 );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );

		update_post_meta( $this->post_id, 'rcp_access_level', 1 );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );

		$this->membership->set_status( 'cancelled' );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );

		$this->membership->set_status( 'expired' );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );

		$this->membership->renew();

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );

		$this->membership->set_status( 'active' );

		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id ) );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );

		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );

		// Change membership to level #2.
		$this->membership->update( array( 'object_id' => $this->level_id_2 ) );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
	}

	/**
	 * Ensure that pending members cannot access paid posts.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_pending_member() {

		$this->membership->set_status( 'pending' );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', 'any-paid' );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Pending members cannot access posts restricted to "any level".
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_content_restricted_to_any_level_as_pending_member() {

		$this->membership->set_status( 'pending' );

		delete_post_meta( $this->post_id, '_is_paid' );
		update_post_meta( $this->post_id, 'rcp_subscription_level', 'any' );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Expired members cannot access paid content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_expired_member() {

		$this->membership->set_status( 'expired' );
		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', 'any-paid' );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Cancelled members can access paid content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_active_content_as_cancelled_member() {

		$this->membership->set_status( 'cancelled' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 week' ) ) );
		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', 'any-paid' );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Free members cannot access paid content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_free_member() {

		$membership_id    = $this->customer->add_membership( array(
			'status'    => 'active',
			'object_id' => $this->free_level
		) );
		$this->membership = rcp_get_membership( $membership_id );

		$this->member->set_status( 'free' );
		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', 'any-paid' );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Paid members can access paid content set to "any paid" level.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_active_content_as_active_member() {

		$this->membership->set_status( 'active' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 week' ) ) );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', 'any-paid' );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Paid members can access paid content restricted to specific membership levels.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_active_content_as_active_member_with_subscription_level() {

		$this->membership->set_status( 'active' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 week' ) ) );
		$this->membership->set_object_id( $this->level_id );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Cancelled member that still has time left can access paid content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_active_content_as_cancelled_member_with_subscription_level() {

		$this->membership->set_status( 'cancelled' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 week' ) ) );
		$this->membership->set_object_id( $this->level_id );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Expired members cannot access paid content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_expired_member_with_subscription_level() {

		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '-1 week' ) ) );
		$this->membership->set_status( 'expired' );
		$this->membership->set_object_id( $this->level_id );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Pending members cannot access restricted content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_pending_member_with_subscription_level() {

		$this->membership->set_status( 'pending' );
		$this->membership->set_object_id( $this->level_id );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Free membership cannot access paid content.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_free_member_with_subscription_level() {

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id );
		$this->membership->update( array(
			'initial_amount'   => 0.00,
			'recurring_amount' => 0.00
		) );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->free_level, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * An active member on level #3 should not be able to access content restricted to levels #1 and #2.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_active_member_without_subscription_level() {

		$this->membership->set_status( 'active' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 week' ) ) );
		$this->membership->set_object_id( $this->level_id_3 );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * A cancelled member on level #3 should not be able to access content restricted to levels #1 and #2.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_cancelled_member_without_subscription_level() {

		$this->membership->set_status( 'cancelled' );
		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '+1 week' ) ) );
		$this->membership->set_object_id( $this->level_id_3 );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * An expired member on level #3 should not be able to access content restricted to levels #1 and #2.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_expired_member_without_subscription_level() {

		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '-1 week' ) ) );
		$this->membership->set_status( 'expired' );
		$this->membership->set_object_id( $this->level_id_3 );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * A pending member on level #3 should not be able to access content restricted to levels #1 and #2.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_pending_member_without_subscription_level() {

		$this->membership->set_status( 'pending' );
		$this->membership->set_object_id( $this->level_id_3 );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * A free member on level #3 should not be able to access content restricted to levels #1 and #2.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_active_content_as_free_member_without_subscription_level() {

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id_3 );
		$this->membership->update( array(
			'initial_amount'   => 0.00,
			'recurring_amount' => 0.00
		) );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * User should lose access to restricted post when membership is disabled.
	 *
	 * @covers RCP_Membership::disable()
	 * @since  3.0
	 */
	function test_cannot_access_active_content_with_disabled_membership() {

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id );

		update_post_meta( $this->post_id, '_is_paid', true );
		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		$this->assertTrue( $this->membership->can_access( ( $this->post_id ) ) );

		$this->membership->disable();

		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Post is restricted to levels #1 and #2, but terms are restricted to level #3. Active user on level #1 should be
	 * able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_post_restricted_at_post_level_and_taxonomy_term_but_with_different_settings() {

		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id, $this->level_id_2 ) );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id_3 ) ) );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * Post is restricted to level #2.
	 * Term on post is restricted to level #1.
	 *
	 * User on level #1 should be able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_post_restricted_at_post_level_and_taxonomy_term_but_with_different_settings_matches_term() {

		delete_post_meta( $this->post_id, 'rcp_subscription_level' );

		update_post_meta( $this->post_id, 'rcp_subscription_level', array( $this->level_id_2 ) );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id ) ) );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * User can access a post restricted by taxonomy term.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_post_restricted_by_taxonomy_term() {

		delete_post_meta( $this->post_id, 'rcp_subscription_level' );
		delete_post_meta( $this->post_id, 'rcp_access_level' );
		delete_post_meta( $this->post_id, '_is_paid' );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Post has no restrictions. Post has a term restricted to level #1. Active user on level #3 should not be able to
	 * access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_post_restricted_by_taxonomy_term_with_incorrect_subscription_level() {

		delete_post_meta( $this->post_id, 'rcp_subscription_level' );
		delete_post_meta( $this->post_id, 'rcp_access_level' );
		delete_post_meta( $this->post_id, '_is_paid' );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id ) ) );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id_3 );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Post has no restrictions. Post has a term restricted to level #1. Cancelled user on level #3 should not be able
	 * to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_post_restricted_by_taxonomy_term_with_member_status_cancelled() {

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_status( 'cancelled' );
		$this->membership->set_object_id( $this->level_id );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Post has no restrictions. Post has a term restricted to level #1. Cancelled user on level #3 who is past their
	 * expiration date should not be able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_post_restricted_by_taxonomy_term_with_member_status_cancelled_but_expired() {

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_expiration_date( date( 'Y-n-d H:i:s', strtotime( '-1 week' ) ) );
		$this->membership->set_status( 'cancelled' );
		$this->membership->set_object_id( $this->level_id );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Post has no restrictions. Post has a term restricted to level #1. Expired user on level #3 should not be able to
	 * access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_post_restricted_by_taxonomy_term_with_member_status_expired() {

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_status( 'expired' );
		$this->membership->set_object_id( $this->level_id );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Taxonomy is restricted to paid users. A paid member should be able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_post_restricted_by_taxonomy_term_with_paid_only_flag() {

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'paid_only' => true ) );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Taxonomy is restricted to paid users. A free member should not be able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_post_restricted_by_taxonomy_term_with_paid_only_flag_with_free_subscription_level() {

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'paid_only' => true ) );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->free_level );
		$this->membership->update( array(
			'initial_amount'   => 0.00,
			'recurring_amount' => 0.00
		) );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Taxonomy is restricted to level #1. User on free level should not be able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_cannot_access_post_restricted_to_paid_level_on_taxonomy_term_without_paid_only_flag_with_member_status_free() {

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id ) ) );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->free_level );

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );
		$this->assertFalse( $this->membership->can_access( $this->post_id ) );

	}

	/**
	 * User with no membership should not be able to access post with restricted taxonomy.
	 *
	 * @covers RCP_Customer::can_access()
	 */
	function test_cannot_access_post_restricted_by_taxonomy_term_if_user_has_no_membership() {

		update_term_meta( $this->term['term_id'], 'rcp_restricted_meta', array( 'subscriptions' => array( $this->level_id ) ) );

		wp_set_post_terms( $this->post_id, $this->term, 'category' );

		$this->customer->disable_memberships();

		$this->assertFalse( $this->customer->can_access( $this->post_id ) );

	}

	/**
	 * Taxonomy is restricted to levels #1 and #2. User on level #2 should be able to access.
	 *
	 * @covers RCP_Customer::can_access()
	 * @covers RCP_Membership::can_access()
	 */
	function test_can_access_post_restricted_by_multiple_taxonomy_terms_where_only_one_matches() {

		wp_set_post_terms( $this->post_id, array( $this->term, $this->term_2 ), 'category' );

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_id_2 );

		$this->assertTrue( $this->customer->can_access( $this->post_id ) );
		$this->assertTrue( $this->membership->can_access( $this->post_id ) );
	}

	/**
	 * Test payment plan completion. After 2 renewals the plan should complete and user's expiration should be updated
	 * to never.
	 *
	 * @covers RCP_Membership::complete_payment_plan()
	 * @covers RCP_Membership::at_maximum_renewals()
	 * @covers RCP_Membership::is_payment_plan_complete()
	 *                                                   
	 * @since  3.0
	 */
	public function test_payment_plan_completion() {

		$this->membership->set_status( 'active' );
		$this->membership->set_object_id( $this->level_payment_plan );
		$this->membership->update( array( 'maximum_renewals' => 2 ) );
		$expiration = $this->membership->calculate_expiration( true );
		$this->membership->set_expiration_date( $expiration );
		$this->membership->increment_times_billed(); // 1/3

		$this->assertEquals( 1, $this->membership->get_times_billed() );
		$this->assertEquals( 2, $this->membership->get_maximum_renewals() );
		$this->assertTrue( $this->membership->has_payment_plan() );
		$this->assertFalse( $this->membership->at_maximum_renewals() );
		$this->assertFalse( $this->membership->is_payment_plan_complete() );
		$this->assertEquals( null, $this->membership->get_payment_plan_completed_date() );

		$this->membership->renew();
		$this->membership->increment_times_billed(); // 2/3

		$this->membership->renew();
		$this->membership->increment_times_billed(); // 3/3

		$this->assertEquals( 3, $this->membership->get_times_billed() );
		$this->assertTrue( $this->membership->at_maximum_renewals() );
		$this->assertTrue( $this->membership->is_payment_plan_complete() );
		$this->assertEquals( 'none', $this->membership->get_expiration_date() );

	}

	/**
	 * A level without a payment plan should not be marked as having one.
	 *
	 * @covers RCP_Membership::has_payment_plan()
	 * @covers RCP_Membership::is_payment_plan_complete()
	 * @covers RCP_Membership::at_maximum_renewals()
	 *
	 * @since  3.0
	 */
	public function test_level_without_payment_plan() {

		$this->membership->set_object_id( $this->level_id );

		$this->assertFalse( $this->membership->has_payment_plan() );
		$this->assertFalse( $this->membership->is_payment_plan_complete() );
		$this->assertFalse( $this->membership->at_maximum_renewals() );

	}

	/**
	 * @covers ::rcp_add_user_to_subscription()
	 * @since 3.0
	 */
	public function test_backwards_compat_rcp_add_user_to_subscription() {

		$user_id = wp_insert_user( array(
			'user_login' => 'test_2',
			'user_pass'  => 'pass',
			'first_name' => 'Tester 2',
			'user_email' => 'test2@test.com'
		) );

		$expiration = rcp_calculate_subscription_expiration( $this->level_id, false );

		rcp_add_user_to_subscription( $user_id, array(
			'status'          => 'active',
			'subscription_id' => $this->level_id,
			'expiration'      => $expiration,
			'recurring'       => false
		) );

		$customer = rcp_get_customer_by_user_id( $user_id );

		$this->assertInstanceOf( 'RCP_Customer', $customer );

		$membership = rcp_get_customer_single_membership( $customer->get_id() );

		$this->assertInstanceOf( 'RCP_Membership', $membership );

		$this->assertEquals( 'active', $membership->get_status() );
		$this->assertEquals( $expiration, $membership->get_expiration_date( false ) );
		$this->assertEquals( $this->level_id, $membership->get_object_id() );
		$this->assertFalse( $membership->is_recurring() );

	}

	/**
	 * @covers RCP_Member::set_status()
	 * @covers RCP_Member::get_status()
	 */
	public function test_backwards_compat_rcp_member_class_set_status() {

		$this->member->set_status( 'pending' );
		$this->assertEquals( 'pending', $this->member->get_status() );

		$customer   = rcp_get_customer_by_user_id( $this->member->ID );
		$membership = rcp_get_customer_single_membership( $customer->get_id() );

		$this->assertEquals( 'pending', $membership->get_status() );

	}

	/**
	 * @covers RCP_Member::set_expiration_date()
	 * @covers RCP_Member::get_expiration_date()
	 */
	public function test_backwards_compat_rcp_member_class_set_expiration_date() {

		$expiration_date = date( 'Y-m-d 23:59:59', strtotime( '+1 month' ) );

		$this->member->set_expiration_date( $expiration_date );
		$this->assertEquals( $expiration_date, $this->member->get_expiration_date( false ) );

		$customer   = rcp_get_customer_by_user_id( $this->member->ID );
		$membership = rcp_get_customer_single_membership( $customer->get_id() );

		$this->assertEquals( $expiration_date, $membership->get_expiration_date( false ) );

	}

	/**
	 * @covers ::rcp_get_expiration_date()
	 */
	public function test_backwards_compat_rcp_get_expiration_date() {

		$expiration_date = rcp_get_expiration_date( $this->member->ID );

		$this->assertEquals( $expiration_date, $this->membership->get_expiration_date( true ) );

	}

}