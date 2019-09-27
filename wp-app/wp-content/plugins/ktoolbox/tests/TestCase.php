<?php

use \PHPUnit\Framework\TestCase;

/**
 * Class KToolboxTestCase
 */
class KToolboxTestCase extends TestCase {

	public function setUp() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown() {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	protected function get_object( array $from_array ) {
		return json_decode( json_encode( $from_array ) );
	}

	public function _return_true() {
		return true;
	}

	public function _return_false() {
		return false;
	}

	/**
	 * Lets us access a protected property

	 * @param $test
	 * @param $property_name
	 *
	 * @return ReflectionProperty
	 * @throws ReflectionException
	 */
	protected static function accessProperty( $test, $property_name ) {
		$reflection          = new ReflectionClass( $test );
		$reflection_property = $reflection->getProperty( $property_name );
		$reflection_property->setAccessible( true );

		return $reflection_property;
	}

	/**
	 * Sets a protected property and returns the object
	 *
	 * @param $test
	 * @param $property_name
	 * @param $value
	 *
	 * @return object $test
	 * @throws ReflectionException
	 */
	public static function setProtectedProperty( $test, $property_name, $value ) {
		$reflection_property = self::accessProperty( $test, $property_name );

		$reflection_property->setValue( $test, $value );

		return $test;
	}

	/**
	 * Returns a protected property's value
	 *
	 * @param $test
	 * @param $property_name
	 *
	 * @return mixed
	 * @throws ReflectionException
	 */
	public static function getProtectedProperty( $test, $property_name ) {
		$reflection_property = self::accessProperty( $test, $property_name );

		return $reflection_property->getValue( $test );
	}

	/**
	 * Gets a protected method
	 *
	 * @param $test
	 * @param $method_name
	 *
	 * @return ReflectionMethod
	 * @throws ReflectionException
	 */
	protected static function getProtectedMethod( $test, $method_name ) {
		$reflection = new ReflectionClass( $test );
		$method = $reflection->getMethod( $method_name );
		$method->setAccessible( true );

		return $method;
	}

	/**
	 * Invokes the method with given args, behaves like the real method
	 *
	 * @param $test
	 * @param $method_name
	 * @param array $args
	 *
	 * @return mixed
	 * @throws ReflectionException
	 */
	public static function invokeProtectedMethod( $test, $method_name, array $args = [] ) {
		$method = self::getProtectedMethod( $test, $method_name );

		return $method->invokeArgs( $test, $args );
	}

}