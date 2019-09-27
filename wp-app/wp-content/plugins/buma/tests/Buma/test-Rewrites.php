<?php

namespace Buma\Tests\Buma;

use Buma\Tests\BUMA_Framework_TestCase;

/**
 * Class Rewrites
 */
class Rewrites extends BUMA_Framework_TestCase {

	public function get_test() {
		\WP_Mock::passthruFunction('add_filter');

		$plugin_option = $this->get_options();

		return new \Buma\Rewrites( $plugin_option );
	}

	public function test_init(){
		$test = $this->get_test();
		$plugin_option = $this->get_options();

		$this->assertInstanceOf(\Buma\Rewrites::class, $test->init($plugin_option) );
	}
}