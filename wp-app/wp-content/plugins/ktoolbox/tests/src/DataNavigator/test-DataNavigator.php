<?php

namespace KToolboxTests;

/**
 * Class DataNavigator
 * @package KToolboxTests
 */
class DataNavigator extends \KToolboxTestCase {

	public function test_invalid_separator() {
		$test   = new \KToolbox\DataNavigator\DataNavigator( [] );
		$result = $this->invokeProtectedMethod( $test, 'get_navigation_separator', [ 'data/element' ] );

		$this->assertEquals( 'data/element', $result );
	}

	public function test_dot_separator() {
		$test   = new \KToolbox\DataNavigator\DataNavigator( [] );
		$result = $this->invokeProtectedMethod( $test, 'get_navigation_separator', [ 'data.element' ] );

		$this->assertEquals( '.', $result );
	}

	public function test_arrow_separator() {
		$test   = new \KToolbox\DataNavigator\DataNavigator( [] );
		$result = $this->invokeProtectedMethod( $test, 'get_navigation_separator', [ 'data->element' ] );

		$this->assertEquals( '->', $result );
	}

	public function test_empty_separator() {
		$test     = new \KToolbox\DataNavigator\DataNavigator( [] );
		$result_1 = $this->invokeProtectedMethod( $test, 'get_navigation_separator', [ '**' ] );
		$result_2 = $this->invokeProtectedMethod( $test, 'get_navigation_separator', [ '*' ] );

		$this->assertEquals( '**', $result_1 );
		$this->assertEquals( '', $result_2 );
	}

	public function test_prioritize_dot_separator() {
		$test   = new \KToolbox\DataNavigator\DataNavigator( [ 'some_data' ] );
		$result = $this->invokeProtectedMethod( $test, 'get_navigation_separator', [ 'data.element->anotherValue' ] );

		$this->assertEquals( '.', $result );
	}

	public function test_get_invalid_key() {
		$test   = new \KToolbox\DataNavigator\DataNavigator( [ 'some_data' ] );
		$result = $test->get( 'key', false );

		$this->assertFalse( $result );
	}

	public function test_get_all_collection() {
		$element = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );
		$test    = new \KToolbox\DataNavigator\DataNavigator( $element );

		$this->assertEquals( [ 1, 2, 3 ], $test->get( '*' ) );
	}

	public function test_no_element() {
		$test = new \KToolbox\DataNavigator\DataNavigator( null );

		$this->assertNull( $test->get( 'something', 'not_null' ) );
	}

	public function test_navigate() {
		$data   = (object) [
			'element' => (object) [
				'sub_element' => [
					'value1',
					'value2'
				]
			]
		];
		$test   = new \KToolbox\DataNavigator\DataNavigator( $data );
		$result = $test->get( 'element.sub_element.1', false );

		$this->assertEquals( 'value2', $result );
	}

	public function test_navigate_error_key_does_not_exist() {
		$data   = [
			'element' => (object) [
				'sub_element' => [
					'value1',
					'value2'
				]
			]
		];
		$test   = new \KToolbox\DataNavigator\DataNavigator( $data );
		$result = $test->get( 'element.sub_element.zzz.1', false );

		$this->assertFalse( $result );
	}

	public function test_navigate_error_no_iterable() {
		$data   = [
			'element' => (object) [
				'sub_element' => [
					'value1' => [ 'a_value' ],
				]
			]
		];
		$test   = new \KToolbox\DataNavigator\DataNavigator( $data );
		$result = $test->get( 'element.sub_element.value1.0.aaaa.bbbbb', false );

		$this->assertFalse( $result );
	}

}
