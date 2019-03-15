<?php

class RCP_Discount_Tests extends WP_UnitTestCase {

	/**
	 * Database query class.
	 *
	 * @var RCP_Discounts
	 */
	protected $db;

	/**
	 * ID of the discount code.
	 *
	 * @var int
	 */
	protected $discount_id;

	public function setUp() {
		parent::setUp();

		$this->db = new RCP_Discounts;

		$args = array(
			'name'       => 'Test Code',
			'code'       => 'test',
			'status'     => 'active',
			'amount'     => '10',
			'expiration' => '2024-10-10 12:12:50',
			'max_uses'   => 2
		);

		$this->discount_id = $this->db->insert( $args );

	}

	function test_format_amount() {
		$formatted_amount = $this->db->format_amount( '10', '%' );
		$this->assertEquals( '10', $formatted_amount );
	}

	function test_format_amount_decimal() {
		$formatted_amount = $this->db->format_amount( '10.25', '%' );
		$this->assertTrue( is_wp_error( $formatted_amount ) );

		$formatted_amount = $this->db->format_amount( '10.25', 'flat' );
		$this->assertEquals( '10.25', $formatted_amount );
	}

	function test_has_discounts() {
		$this->assertTrue( rcp_has_discounts() );
	}

	function test_insert_discount() {

		$args = array(
			'name'   => 'Test Code 2',
			'code'   => 'test2',
			'status' => 'active',
			'amount' => '10',
		);
		$discount_id = $this->db->insert( $args );

		$this->assertGreaterThan( 1, $discount_id );

	}

	function test_update_discount() {

		$updated = $this->db->update( $this->discount_id, array( 'name' => 'Updated Code', 'amount' => '10' ) );
		$this->assertTrue( $updated );

		$discount = $this->db->get_discount( $this->discount_id );
		$this->assertEquals( 'Updated Code', $discount->name );

	}

	function test_get_discount() {

		$discount = $this->db->get_discount( $this->discount_id );

		$this->assertNotEmpty( $discount );
		$this->assertEquals( 'Test Code', $discount->name );
		$this->assertEquals( 'test', $discount->code );
		$this->assertEquals( 'active', $discount->status );
		$this->assertEquals( '10', $discount->amount );

	}

	function test_get_by() {

		$discount = $this->db->get_by( 'code', 'test' );

		$this->assertNotEmpty( $discount );
		$this->assertEquals( 'Test Code', $discount->name );
		$this->assertEquals( 'test', $discount->code );
		$this->assertEquals( 'active', $discount->status );
		$this->assertEquals( '10', $discount->amount );

	}

	function test_get_status() {
		$this->assertEquals( 'active', $this->db->get_status( $this->discount_id ) );
	}

	function test_get_amount() {
		$this->assertEquals( '10', $this->db->get_amount( $this->discount_id ) );
	}

	function test_get_uses() {
		$this->assertEquals( 0, $this->db->get_uses( $this->discount_id ) );
		$this->db->increase_uses( $this->discount_id );
		$this->assertEquals( 1, $this->db->get_uses( $this->discount_id ) );
	}

	function test_get_max_uses() {
		$this->assertEquals( 2, $this->db->get_max_uses( $this->discount_id ) );
	}

	function test_get_subscription_id() {
		$this->assertEquals( 0, $this->db->get_subscription_id( $this->discount_id ) );
	}

	function test_has_subscription_id() {
		$this->assertFalse( $this->db->has_subscription_id( $this->discount_id ) );
	}

	/**
	 * A new discount code should not have any membership level ids assigned by default.
	 *
	 * @covers RCP_Discounts::get_membership_level_ids()
	 * @since 3.0
	 */
	function test_get_membership_level_ids() {
		$this->assertEmpty( $this->db->get_membership_level_ids( $this->discount_id ) );
	}

	/**
	 * A new discount code should not have any membership level ids assigned by default.
	 *
	 * @covers RCP_Discounts::has_membership_level_ids()
	 * @since 3.0
	 */
	function test_has_membership_level_ids() {
		$this->assertFalse( $this->db->has_membership_level_ids( $this->discount_id ) );
	}

	function test_get_expiration() {
		$this->assertEquals( '2024-10-10 12:12:50', $this->db->get_expiration( $this->discount_id ) );
	}

	function test_get_type() {
		$this->assertEquals( '%', $this->db->get_type( $this->discount_id ) );
	}

	function test_delete() {

		$this->db->delete( $this->discount_id );
		$discount = $this->db->get_discount( $this->discount_id );
		$this->assertEmpty( $discount );
	}

	function test_is_maxed_out() {

		$this->assertFalse( $this->db->is_maxed_out( $this->discount_id ) );
		$this->db->increase_uses( $this->discount_id );
		$this->db->increase_uses( $this->discount_id );
		$this->assertTrue( $this->db->is_maxed_out( $this->discount_id ) );

	}

	function test_is_expired() {

		$this->assertFalse( $this->db->is_expired( $this->discount_id ) );

		$updated = $this->db->update( $this->discount_id, array( 'expiration' => '2012-10-10 00:00:00' ) );

		$this->assertTrue( $this->db->is_expired( $this->discount_id ) );
	}

	function test_user_has_used() {

		$this->assertFalse( $this->db->user_has_used( 1, 'test' ) );

		$this->db->add_to_user( 1, 'test' );

		$this->assertTrue( $this->db->user_has_used( 1, 'test' ) );
	}

	function test_format_discount() {

		$this->assertEquals( '&#36;10.00', $this->db->format_discount( 10, 'flat' ) );
		$this->assertEquals( '10%', $this->db->format_discount( 10, '%' ) );
	}

	function test_calc_discounted_price() {

		$this->assertEquals( 90, $this->db->calc_discounted_price( 100, 10, '%' ) );
		$this->assertEquals( 450, $this->db->calc_discounted_price( 500, 10, '%' ) );

		$this->assertEquals( 90, $this->db->calc_discounted_price( 100, 10, 'flat' ) );
	}

	function test_calc_discounted_price_with_high_price_and_flat_discount() {

		$this->assertEquals( 1979, $this->db->calc_discounted_price( 1999, 20, 'flat' ) );

	}

	function test_calc_discounted_price_with_high_price_and_percentage_discount() {

		$this->assertEquals( 1599.2, $this->db->calc_discounted_price( 1999, 20, '%' ) );

	}

}

