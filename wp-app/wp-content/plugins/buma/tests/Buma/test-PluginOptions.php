<?php

namespace Buma\Tests\Buma;

use Buma\Locale;
use Buma\Tests\BUMA_Framework_TestCase;

/**
 * Class PluginOptions
 *
 * @package Buma\Tests\Buma
 */
class PluginOptions extends BUMA_Framework_TestCase {

	public function get_test() {
		\WP_Mock::userFunction( 'get_option', [ 'return' => [] ] );

		$locale = \Mockery::mock( Locale::class );

		return new \Buma\PluginOptions( $locale, 'option_name' );
	}

	public function test_create() {
		$locale = \Mockery::mock( Locale::class )
		                  ->shouldReceive( 'get_language' )->andReturn( 'it_IT' )
		                  ->getMock();
		$test   = $this->get_test();

		$this->assertInstanceOf( \Buma\PluginOptions::class, $test->create( $locale ) );
	}

	public function test_get() {
		\WP_Mock::userFunction( 'get_option', [ 'return' => [] ] );

		$locale = \Mockery::mock( Locale::class );

		$test = new \Buma\PluginOptions( $locale, 'option_name', [ 'option_1' => 'value_1' ] );

		$this->assertEquals( 'value_1', $test->get( 'option_1' ) );
	}

	public function test_get_not_exist_optionvalue() {
		\WP_Mock::userFunction( 'get_option', [ 'return' => [ 'option_1' => 'value_1' ] ] );

		$locale = \Mockery::mock( Locale::class );

		$test = new \Buma\PluginOptions( $locale, 'option_name' );

		$this->assertEquals( '', $test->get( 'new_option' ) );
	}
}