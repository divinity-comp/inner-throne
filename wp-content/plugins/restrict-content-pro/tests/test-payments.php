<?php

class RCP_Payment_Tests extends WP_UnitTestCase {

	protected $_user_id = 1;
	protected $_payment_id = 0;

	function setUp() {

		parent::setUp();

		global $rcp_payments_db;

		$args = array(
			'user_id'      => 1,
			'amount'       => 10,
			'payment_type' => 'test'
		);

		$this->_payment_id = $rcp_payments_db->insert( $args );

	}

	function test_add_metadata() {

		global $rcp_payments_db;

		$this->assertFalse( $rcp_payments_db->add_meta( 0, '', '' ) );
		$this->assertFalse( $rcp_payments_db->add_meta( $this->_payment_id, '', '' ) );
		$this->assertNotEmpty( $rcp_payments_db->add_meta( $this->_payment_id, 'test_key', '' ) );
		$this->assertNotEmpty( $rcp_payments_db->add_meta( $this->_payment_id, 'test_key', '1' ) );
	}

	function test_update_metadata() {

		global $rcp_payments_db;

		$this->assertEmpty( $rcp_payments_db->update_meta( 0, '', '' ) );
		$this->assertEmpty( $rcp_payments_db->update_meta( $this->_payment_id, '', ''  ) );
		$this->assertNotEmpty( $rcp_payments_db->update_meta( $this->_payment_id, 'test_key_2' , '' ) );
		$this->assertNotEmpty( $rcp_payments_db->update_meta( $this->_payment_id, 'test_key_2', '1' ) );
	}

	function test_get_metadata() {

		global $rcp_payments_db;

		$this->assertEmpty( $rcp_payments_db->get_meta( $this->_payment_id ) );
		$this->assertEmpty( $rcp_payments_db->get_meta( $this->_payment_id, 'key_that_does_not_exist', true ) );
		$rcp_payments_db->update_meta( $this->_payment_id, 'test_key_2', '1' );
		$this->assertEquals( '1', $rcp_payments_db->get_meta( $this->_payment_id, 'test_key_2', true ) );
		$this->assertInternalType( 'array', $rcp_payments_db->get_meta( $this->_payment_id, 'test_key_2', false ) );
	}

	function test_delete_metadata() {

		global $rcp_payments_db;

		$rcp_payments_db->update_meta( $this->_payment_id, 'test_key', '1' );
		$this->assertTrue( $rcp_payments_db->delete_meta( $this->_payment_id, 'test_key' ) );
		$this->assertFalse( $rcp_payments_db->delete_meta( $this->_payment_id, 'key_that_does_not_exist' ) );
	}
}
