<?php

class RCP_Level_Tests extends WP_UnitTestCase {

	protected $_user_id = 1;
	protected $_level_id = 0;

	function setUp() {

		parent::setUp();

		global $rcp_levels_db;

		$args = array(
			'name'          => 'Gold',
			'description'   => 'The Gold Plan',
			'duration'      => '1',
			'duration_unit' => 'month',
			'price'         => '10',
			'fee'           => '0',
			'list_order'    => '0',
			'level' 	    => '5',
			'status'        => 'active',
			'role'          => 'subscriber'
		);

		$this->_level_id = $rcp_levels_db->insert( $args );

	}

	function test_add_metadata() {

		global $rcp_levels_db;

		$this->assertFalse( $rcp_levels_db->add_meta( 0, '', '' ) );
		$this->assertFalse( $rcp_levels_db->add_meta( $this->_level_id, '', '' ) );
		$this->assertNotEmpty( $rcp_levels_db->add_meta( $this->_level_id, 'test_key', '' ) );
		$this->assertNotEmpty( $rcp_levels_db->add_meta( $this->_level_id, 'test_key', '1' ) );
	}

	function test_update_metadata() {

		global $rcp_levels_db;

		$this->assertEmpty( $rcp_levels_db->update_meta( 0, '', '' ) );
		$this->assertEmpty( $rcp_levels_db->update_meta( $this->_level_id, '', ''  ) );
		$this->assertNotEmpty( $rcp_levels_db->update_meta( $this->_level_id, 'test_key_2' , '' ) );
		$this->assertNotEmpty( $rcp_levels_db->update_meta( $this->_level_id, 'test_key_2', '1' ) );
	}

	function test_get_metadata() {

		global $rcp_levels_db;

		$this->assertEmpty( $rcp_levels_db->get_meta( $this->_level_id ) );
		$this->assertEmpty( $rcp_levels_db->get_meta( $this->_level_id, 'key_that_does_not_exist', true ) );
		$rcp_levels_db->update_meta( $this->_level_id, 'test_key_2', '1' );
		$this->assertEquals( '1', $rcp_levels_db->get_meta( $this->_level_id, 'test_key_2', true ) );
		$this->assertInternalType( 'array', $rcp_levels_db->get_meta( $this->_level_id, 'test_key_2', false ) );
	}

	function test_delete_metadata() {

		global $rcp_levels_db;

		$rcp_levels_db->update_meta( $this->_level_id, 'test_key', '1' );
		$this->assertTrue( $rcp_levels_db->delete_meta( $this->_level_id, 'test_key' ) );
		$this->assertFalse( $rcp_levels_db->delete_meta( $this->_level_id, 'key_that_does_not_exist' ) );
	}
}
