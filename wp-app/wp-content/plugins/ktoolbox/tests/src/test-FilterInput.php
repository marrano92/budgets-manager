<?php

namespace KToolboxTests;

/**
 * Class FilterInput
 * @package KToolboxTests
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FilterInput extends \KToolboxTestCase {

	public function test_get_arr() {
		$data = [
			'key1' => 'value1',
		];

		$arr = [
			'key1' => 'value1',
		];

		$_REQUEST['a_request'] = $data;
		$_REQUEST['other_var'] = "I don't want to be part of all this.";
		$test_input_request    = \Mockery::mock( \KToolbox\FilterInput::class, [ INPUT_REQUEST, 'a_request' ] )->shouldAllowMockingProtectedMethods()->makePartial();
		$test_input_request->shouldReceive( 'has_var' )->once()->andReturnTrue();
		$test_input_request->set_filter( FILTER_SANITIZE_STRING )
		                   ->set_flags( FILTER_FLAG_NO_ENCODE_QUOTES );
		$this->assertEquals( $arr, $test_input_request->get_arr() );

		$_POST['a_request'] = $data;
		$_POST['other_var'] = "I don't want to be part of all this.";
		$test_input_post    = \Mockery::mock( \KToolbox\FilterInput::class, [ INPUT_POST, 'a_request' ] )->shouldAllowMockingProtectedMethods()->makePartial();
		$test_input_post->shouldReceive( 'has_var' )->once()->andReturnTrue();
		//$test_input_post->shouldReceive( 'get_variable_name_from_post' )->once()->andReturn( $arr );
		$test_input_post->set_filter( FILTER_SANITIZE_STRING )
		                ->set_flags( FILTER_FLAG_NO_ENCODE_QUOTES );
		$this->assertEquals( $arr, $test_input_post->get_arr() );

		$_GET['a_request'] = $data;
		$_GET['other_var'] = "I don't want to be part of all this.";
		$test_input_get    = \Mockery::mock( \KToolbox\FilterInput::class, [ INPUT_GET, 'a_request' ] )->shouldAllowMockingProtectedMethods()->makePartial();
		$test_input_get->shouldReceive( 'has_var' )->once()->andReturnTrue();
		//$test_input_get->shouldReceive( 'get_variable_name_from_get' )->once()->andReturn( $arr );
		$test_input_get->set_filter( FILTER_SANITIZE_STRING )
		               ->set_flags( FILTER_FLAG_NO_ENCODE_QUOTES );
		$this->assertEquals( $arr, $test_input_get->get_arr() );
	}

	public function test_get_array_no_var() {
		$test = new \KToolbox\FilterInput( INPUT_GET, 'a_request' );
		$this->assertEmpty( $test->get_arr( [] ) );
	}

	public function test_get_options() {
		$test = new \KToolbox\FilterInput( INPUT_GET, 'option' );
		$test->set_flags( INPUT_GET );
		$result = $test->get_options( 'default' );

		$this->assertEquals( [ 'options' => [ 'default' => 'default' ], 'flags' => INPUT_GET ], $result );
	}

	public function test_get_has_var() {
		$_REQUEST['option'] = 'value';
		$test               = new \KToolbox\FilterInput( INPUT_REQUEST, 'option' );
		$result             = $test->get( 'option' );

		$this->assertEquals( 'value', $result );
	}

	public function test_get_has_var_no_input_request() {
		$_GET['option'] = 'value';
		$test           = \Mockery::mock( \KToolbox\FilterInput::class, [ INPUT_GET, 'option' ] )->shouldAllowMockingProtectedMethods()->makePartial();
		$test->shouldReceive( 'has_var' )->once()->andReturnTrue();
		//$test               = new \KToolbox\FilterInput( INPUT_GET, 'option' );
		$result = $test->get( 'option' );

		$this->assertEquals( 'option', $result );
	}

	public function test_get_has_not_var() {
		$test = \Mockery::mock( \KToolbox\FilterInput::class, [ INPUT_GET, 'option' ] )->shouldAllowMockingProtectedMethods()->makePartial();
		$test->shouldReceive( 'has_var' )->once()->andReturnFalse();
		$result = $test->get( 'default' );

		$this->assertEquals( 'default', $result );
	}

}
