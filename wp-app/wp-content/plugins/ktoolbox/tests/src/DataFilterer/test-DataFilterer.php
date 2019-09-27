<?php

namespace KToolboxTests;

/**
 * Class DataFilterer
 * @package KToolboxTests
 */
class DataFilterer extends \KToolboxTestCase {

	public function test_comparison_1() {
		$query = [ 'prop1', '=', 'a' ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( $this->get_data(), [ $query ] );

		$expected = (object) [
			'data_1' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'a' ],
			'data_2' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'b' ],
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_2() {
		$query = [ 'prop1', '==', 'prop4' ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( (array) $this->get_data(), [ $query ] );

		$expected = [
			'data_1' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'a' ],
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_3() {
		$query = [ [ 'prop1', '=', 'prop4' ], 'OR', [ 'prop2', '===', 'prop4' ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( $this->get_data(), [ $query ] );

		$expected = (object) [
			'data_1' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'a' ],
			'data_2' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'b' ],
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_4() {
		$query = [ [ 'prop1', '<=', 1 ], 'OR', [ 'prop1', '>', 2 ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( (array) $this->get_data(), [ $query ] );

		$expected = [
			'data_3' => (object) [ 'prop1' => 1, 'prop2' => 2, 'prop3' => 3, 'prop4' => 4 ],
			'data_4' => (object) [ 'prop1' => 3, 'prop2' => 4, 'prop3' => 5, 'prop4' => 6 ],
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_5() {
		$query = [ [ 'prop1', '<=', 1 ], 'AND', [ 'prop1', '>', 2 ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( (array) $this->get_data(), [ $query ] );

		$expected = [];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_6() {
		$query = [ [ 'prop1', '===', 1 ], 'AND', [ 'prop4', '===', 4 ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( $this->get_data(), [ $query ] );

		$expected = (object) [
			'data_3' => (object) [ 'prop1' => 1, 'prop2' => 2, 'prop3' => 3, 'prop4' => 4 ],
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_7() {
		$query = [ [ 'prop1', '!==', 'a' ], 'AND', [ 'prop4', '!=', 'b' ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( (array) $this->get_data(), [ $query ] );

		$expected = [
			'data_3' => (object) [ 'prop1' => 1, 'prop2' => 2, 'prop3' => 3, 'prop4' => 4 ],
			'data_4' => (object) [ 'prop1' => 3, 'prop2' => 4, 'prop3' => 5, 'prop4' => 6 ],
			'i_am_skipped',
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_comparison_8() {
		$query = [ [ 'prop1', '>=', 1 ], 'OR', [ 'prop4', '!=', '1111' ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( $this->get_data(), [ $query ] );

		$this->assertEquals( $this->get_data(), $test->execute() );
	}

	public function test_comparison_9() {
		$query = [ 'prop1', '<', 100 ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( $this->get_data(), [ $query ] );

		$expected = (object) [
			'data_3' => (object) [ 'prop1' => 1, 'prop2' => 2, 'prop3' => 3, 'prop4' => 4 ],
			'data_4' => (object) [ 'prop1' => 3, 'prop2' => 4, 'prop3' => 5, 'prop4' => 6 ],
		];

		$this->assertEquals( $expected, $test->execute() );
	}

	public function test_invalid_logical_operator() {
		$query = [ [ 'prop1', '>=', 1 ], 'AAA', [ 'prop4', '!=', '1111' ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( (array) $this->get_data(), [ $query ] );

		$this->assertEquals( [], $test->execute() );
	}

	public function test_invalid_comparison_operator() {
		$query = [ [ 'prop1', '>qqq', 1 ], 'AND', [ 'prop4', 'www', '1111' ] ];
		$test  = new \KToolbox\DataFilterer\DataFilterer( $this->get_data(), [ $query ] );

		$this->assertEquals( (object) [], $test->execute() );
	}

	public function test_invalid_constructor_parameters() {
		$test = new \KToolbox\DataFilterer\DataFilterer( [], [] );

		$this->assertEquals( null, $test->execute() );
	}

	public function get_data() {
		return (object) [
			'data_1' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'a' ],
			'data_2' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'b' ],
			'data_3' => (object) [ 'prop1' => 1, 'prop2' => 2, 'prop3' => 3, 'prop4' => 4 ],
			'data_4' => (object) [ 'prop1' => 3, 'prop2' => 4, 'prop3' => 5, 'prop4' => 6 ],
			'i_am_skipped',
		];
	}

}
