<?php
/**
 * Partners Banner Partner Tests.
 *
 * @since   1.0.0
 * @package Partners_Banner
 */
class PB_Partner_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'PB_Partner') );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'PB_Partner', partners_banner()->partner' );
	}

	/**
	 * Test to make sure the CPT now exists.
	 *
	 * @since  1.0.0
	 */
	function test_cpt_exists() {
		$this->assertTrue( post_type_exists( 'pb-partner' ) );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
