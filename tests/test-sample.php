<?php

include '../piousbox_wp_plugin.php';
/**
 * Class PiousboxWpPluginTest
 *
 * @package Piousbox_wp_plugin
 */

class PiousboxWpPluginTest extends WP_UnitTestCase {

	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}

	public function test_foobar_func() {
		$result = foobar_func();
		$expected = "foo and bar";
		$this->assertEquals( $result, $expected );
	}

}


