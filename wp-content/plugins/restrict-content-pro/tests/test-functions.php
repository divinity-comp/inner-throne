<?php

class RCP_Function_Tests extends WP_UnitTestCase {

	function setUp() {

		parent::setUp();

	}

	function test_rcp_is_sandbox() {

		$this->assertFalse( rcp_is_sandbox() );

		add_filter( 'rcp_is_sandbox', '__return_true' );

		$this->assertTrue( rcp_is_sandbox() );

	}

}
