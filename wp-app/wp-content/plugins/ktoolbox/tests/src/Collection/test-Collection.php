<?php

namespace KToolboxTests;

/**
 * Class Collection
 * @package KToolboxTests
 */
class Collection extends \KToolboxTestCase {

	public function test_add_all() {
		$data = [ 1, 2, 3 ];
		$test = new \KToolbox\Collection\Collection( $data );
		$test->add( 'test', 4 );

		$expected = [ 1, 2, 3, 'test' => 4 ];

		$this->assertEquals( $expected, $test->all() );
	}

	public function test_count() {
		$data_1 = [ 1, 2, 3 ];
		$test_1 = new \KToolbox\Collection\Collection( $data_1 );

		$data_2 = (object) [ 'elem1' => 'val1', 'elem2' => 'val2' ];
		$test_2 = new \KToolbox\Collection\Collection( $data_2 );

		$this->assertEquals( 3, count( $test_1 ) );
		$this->assertEquals( 2, count( $test_2 ) );
	}

	public function test_each_string_no_effect() {
		$data   = [
			[ 'key1' => 'value1' ],
			[ 'key2' => 'value2' ],
		];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->each( 'nope' );

		$this->assertInstanceOf( \KToolbox\Collection\Collection::class, $result );
		$this->assertEquals( $test->all(), $result->all() );
	}

	public function test_each_string_collection_method() {
		$data   = [
			(object) [ 'key1' => 'value1' ],
			(object) [ 'key2' => 'value2' ],
		];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->each( 'add', [ 'key3', 'value3' ] );

		$expected_data = [
			(object) [ 'key1' => 'value1', 'key3' => 'value3' ],
			(object) [ 'key2' => 'value2', 'key3' => 'value3' ],
		];
		$expected      = new \KToolbox\Collection\Collection( $expected_data );
		$this->assertEquals( $expected->all(), $result->all() );
	}

	public function test_each_string_collection_method_with_arguments() {
		$data   = [
			new \KToolbox\Collection\Collection( [ 'key1' => 'value1', 'key4' => 'value4' ] ),
			new \KToolbox\Collection\Collection( [ 'key2' => 'value2', 'key4' => 'value4' ] ),
		];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->each( 'add', [ 'key3', 'value3' ] );

		$expected_data = [
			[ 'key1' => 'value1', 'key3' => 'value3', 'key4' => 'value4' ],
			[ 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4' ],
		];
		$expected      = new \KToolbox\Collection\Collection( $expected_data );
		$this->assertEquals( $expected->all(), $result->all() );
	}

	public function test_each_string_object_method() {
		$data   = [
			new \ExampleDecorator( (object) [ 'demo_prop' => 'Fiat Panda' ] ),
			new \ExampleDecorator( (object) [ 'demo_prop' => 'Alfa Romeo Giulietta' ] ),
			'a_string',
		];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->each( 'get_demo_prop' );

		$expected_data = [
			'Fiat Panda',
			'Alfa Romeo Giulietta',
			'a_string',
		];
		$expected      = new \KToolbox\Collection\Collection( $expected_data );
		$this->assertEquals( $expected->all(), $result->all() );
	}

	public function test_each_callable() {
		$data   = [ 1, 2, 3 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->each( function ( $item ) {
			return $item * 2;
		} );

		$this->assertInstanceOf( \KToolbox\Collection\Collection::class, $result );
		$this->assertEquals( [ 2, 4, 6 ], $result->all() );
	}

	public function test_exclude() {
		$data   = [ 'val1' => 1, 'val2' => 2, 'val3' => 3 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->except( [ 'val1', 'val2' ] );

		$this->assertEquals( [ 'val3' => 3 ], $result->all() );
	}

	public function test_filter() {
		$data = [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4 ];

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->filter( function ( $value, $key ) {
			return $value < 4 && $key !== 'c';
		}, ARRAY_FILTER_USE_BOTH );

		$expected = [ 'a' => 1, 'b' => 2 ];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_flatten() {
		$generic       = new \stdClass();
		$generic->prop = 'generic-value';
		$decorator     = new \ExampleDecorator( (object) [ 'demo_prop' => 'Fiat Panda' ] );

		$data = [
			[
				'item1' => $decorator,
				'item2' => [
					'item3' => 'value3',
				]
			],
			'item4' => 'value4',
			'value5',
			'value4',
			'item5' => 'generic-value',
			'item6' => new \ArrayableClass()
		];

		$test = new \KToolbox\Collection\Collection( $data );

		$this->assertEquals( [ $decorator, 'value3', 'value4', 'value5', 'value4', 'generic-value', 'value1', 'another_value' ], $test->flatten()->all() );
	}

	public function test_flatten_preserve_keys() {
		$generic       = new \stdClass();
		$generic->prop = 'generic-value';

		$data = [
			[
				'item1' => 'value1',
				'item2' => [
					'item4' => 'value3',
				]
			],
			'item4' => 'value4',
			'value5',
			'value4',
			'item5' => $generic, // consider this as 'item5' => ['prop' => generic-value']. 'item5' will be overwritten
			'item6' => $generic->prop,
			'item7' => new \ArrayableClass()
		];

		$test     = new \KToolbox\Collection\Collection( $data );
		$expected = [ 'item1' => 'value1', 'item4' => 'value4', 0 => 'value5', 1 => 'value4', 'prop' => 'generic-value', 'item6' => 'generic-value', 'elem1' => 'value1',  2 => 'another_value' ];

		$this->assertEquals( $expected, $test->flatten( true )->all() );
	}

	public function test_forget() {
		$test = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );
		$test->forget( 2 );

		$this->assertEquals( [ 1, 2 ], $test->all() );
	}

	public function test_intersect_no_callback() {
		$data_1 = [ 'val1' => 'aaa', 'val2' => 'bbb' ];
		$data_2 = [ 'val2' => 'bbb', 'val3' => 'ccc' ];

		$test   = new \KToolbox\Collection\Collection( $data_1 );
		$result = $test->intersect( new \KToolbox\Collection\Collection( $data_2 ) );

		$expected = [ 'val2' => 'bbb' ];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_intersect_callback() {
		$data_1 = [ 'val1' => 1, 'val2' => 2 ];
		$data_2 = [ 'val2' => 3, 'val3' => 3 ];

		$test   = new \KToolbox\Collection\Collection( $data_1 );
		$result = $test->intersect( new \KToolbox\Collection\Collection( $data_2 ), function ( $item_1, $item_2 ) {
			return $item_1 > $item_2;
		} );

		$expected = [ 'val2' => 2 ];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_join() {
		$data = [
			'arr' => [ 'val' => 'aaa' ],
			'obj' => (object) [ 'val' => 'bbb' ],
		];

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->join( 'added_value', 'added' );

		$expected = [
			'arr' => [ 'val' => 'aaa', 'added_value' => 'added' ],
			'obj' => (object) [ 'val' => 'bbb', 'added_value' => 'added' ],
		];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_join_callable() {
		$data = [
			'arr' => [ 'val' => 1 ],
			'obj' => (object) [ 'val' => 2 ],
		];

		$test     = new \KToolbox\Collection\Collection( $data );
		$callback = function ( $item, $prop ) {
			if ( is_object( $item ) ) {
				$mutated = $item->val * 2;
			} else {
				$mutated = $item['val'] * 2;
			}

			return $prop . '_' . $mutated;
		};
		$result   = $test->join( 'added_value', $callback );

		$expected = [
			'arr' => [ 'val' => 1, 'added_value' => 'added_value_2' ],
			'obj' => (object) [ 'val' => 2, 'added_value' => 'added_value_4' ],
		];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_join_where_simple() {
		$data = [
			'arr1' => [ 'val1' => 1, 'val2' => 'a', 'val3' => 0 ],
			'arr2' => [ 'val1' => 2, 'val2' => 'a', 'val3' => 0 ],
			'arr3' => [ 'val1' => 3, 'val2' => 'b', 'val3' => 0 ],
			'arr4' => [ 'val1' => 4, 'val2' => 'a', 'val3' => 0 ],
		];

		$test   = new \KToolbox\Collection\Collection( $data );
		$query  = [ [ 'val1', '==', 4 ] ];
		$result = $test->join_where( 'added_value', 'added', $query );

		$expected = [
			'arr1' => [ 'val1' => 1, 'val2' => 'a', 'val3' => 0 ],
			'arr2' => [ 'val1' => 2, 'val2' => 'a', 'val3' => 0 ],
			'arr3' => [ 'val1' => 3, 'val2' => 'b', 'val3' => 0 ],
			'arr4' => [ 'val1' => 4, 'val2' => 'a', 'val3' => 0, 'added_value' => 'added' ],
		];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_join_where_complex() {
		$data = [
			'arr1' => [ 'val1' => 1, 'val2' => 'a', 'val3' => 0 ],
			'arr2' => [ 'val1' => 2, 'val2' => 'a', 'val3' => 0 ],
			'arr3' => [ 'val1' => 3, 'val2' => 'b', 'val3' => 0 ],
			'arr4' => [ 'val1' => 4, 'val2' => 'a', 'val3' => 0 ],
		];

		$test   = new \KToolbox\Collection\Collection( $data );
		$query  = [ [ [ 'val3', '===', 0 ], 'AND', [ 'val2', '===', 'b' ] ], [ 'val1', '==', 4 ] ];
		$result = $test->join_where( 'added_value', 'added', $query );

		$expected = [
			'arr1' => [ 'val1' => 1, 'val2' => 'a', 'val3' => 0 ],
			'arr2' => [ 'val1' => 2, 'val2' => 'a', 'val3' => 0 ],
			'arr3' => [ 'val1' => 3, 'val2' => 'b', 'val3' => 0, 'added_value' => 'added' ],
			'arr4' => [ 'val1' => 4, 'val2' => 'a', 'val3' => 0, 'added_value' => 'added' ],
		];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_diff_no_callback() {
		$data = [ 'a' => 1, 'b' => 2, 'c' => 3 ];
		$diff = [ 'b' => 2, 'c' => 3 ];

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->diff( $diff );

		$this->assertEquals( [ 'a' => 1 ], $result->all() );
	}

	public function test_diff_callback() {
		$data = [ 0, 1, 2 ];
		$diff = new \KToolbox\Collection\Collection( [ - 1, 2, 3 ] );

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->diff( $diff, function ( $a, $b ) {
			return $a < $b;
		} );

		$this->assertEquals( [ 1, 2 ], array_values( $result->all() ) );
	}

	public function test_limit() {
		$data   = [ 1, 2, 3 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->limit( 2 );

		$this->assertEquals( [ 1, 2 ], $result->all() );
	}

	public function test_map() {
		$data = [ 1, 2, 3 ];

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->map( function ( $item ) {
			return $item * 2;
		} );

		$expected = [ 2, 4, 6 ];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_merge() {
		$data = [ 1, 2, 3 ];
		$test = new \KToolbox\Collection\Collection( $data );
		$test->merge( new\KToolbox\Collection\Collection( [ 4 ] ) );

		$this->assertEquals( [ 1, 2, 3, 4 ], $test->all() );
	}

	public function test_only() {
		$data = [
			'elem1' => [ 'param1' => [ 'val' => 1, 'other_val' => 'hello', 'skip' => 'aaa' ] ],
			'elem2' => [ 'param1' => [ 'val' => 1, 'other_val' => 'hello', 'skip' => 'aaa' ] ],
			'elem3' => (object) [ 'param1' => [ 'val' => 2, 'other_val' => 'hello', 'skip' => 'aaa' ] ],
		];

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->only( [ 'val', 'other_val' ], true );

		$expected = [ 1, 'hello', 2 ];

		$this->assertEquals( $expected, $result );
	}

	// Just test it returns a collection
	public function test_pick() {
		\WP_Mock::userFunction( 'wp_array_slice_assoc', [ 'return' => [], 'times' => 1 ] );
		$test = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );

		$this->assertInstanceOf( \KToolbox\Collection\Collection::class, $test->pick( [ 'something', 'something-else' ] ) );
	}

	public function test_push() {
		$data = [ 1, 2, 3 ];
		$test = new \KToolbox\Collection\Collection( $data );
		$test->push( 4 );

		$this->assertEquals( [ 1, 2, 3, 4 ], $test->all() );
	}

	public function test_skip() {
		$data   = [ 1, 2, 3 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->skip( 2 );

		$this->assertEquals( [ 3 ], $result->all() );
	}

	public function test_sort_asc() {
		$data = [ 'c', 'b', 'a' ];

		$test = new \KToolbox\Collection\Collection( $data );
		$test->sort( 'ASC' );

		$this->assertEquals( [ 'a', 'b', 'c' ], $test->all() );
	}

	public function test_sort_desc() {
		$data = [ 'a', 'b', 'c' ];

		$test = new \KToolbox\Collection\Collection( $data );
		$test->sort( 'DESC' );

		$this->assertEquals( [ 'c', 'b', 'a' ], $test->all() );
	}

	public function test_sort_callback() {
		$data = [
			[ 'val' => 1000 ],
			[ 'val' => 100 ],
			[ 'val' => 10 ],
			[ 'val' => 1 ],
		];

		$test = new \KToolbox\Collection\Collection( $data );
		$test->sort( function ( $a, $b ) {
			return $a['val'] <=> $b['val'];
		} );

		$expected = array_reverse( $data );

		$this->assertEquals( $expected, $test->all() );
	}

	public function test_split() {
		$data   = [ 1, 2, 3 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->split( 2, false );

		$this->assertEquals( [ [ 1, 2 ], [ 3 ] ], $result->all() );
	}

	public function test_split_preserve_keys() {
		$data   = [ 'key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3' ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->split( 2 );

		$expected = [
			[ 'key1' => 'val1', 'key2' => 'val2' ],
			[ 'key3' => 'val3' ],
		];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_where_simple() {
		$data = [
			'value1' => (object) [ 'param1' => 1 ],
			'value2' => (object) [ 'param1' => 2 ],
			'value3' => (object) [ 'param1' => 3 ],
		];

		$query  = [ 'param1', '<=', 2 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->where( $query );

		$expected = [
			'value1' => (object) [ 'param1' => 1 ],
			'value2' => (object) [ 'param1' => 2 ],
		];

		$this->assertEquals( $expected, $result->all() );
	}

	public function test_where_logical() {
		$data = [
			'value1' => (object) [ 'param1' => 1 ],
			'value2' => (object) [ 'param1' => 2 ],
			'value3' => (object) [ 'param1' => 3 ],
		];

		$query  = [ [ 'param1', '<=', 2 ], 'OR', [ 'param1', '=', 3 ] ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->where( $query );

		$this->assertEquals( $data, $result->all() );
	}

	public function test_where_complex() {
		$data = [
			'value1' => (object) [ 'param1' => 1 ],
			'value2' => (object) [ 'param1' => 2 ],
			'value3' => (object) [ 'param1' => 3 ],
		];

		$query  = [ [ [ 'param1', '=', 1 ], 'OR', [ 'param1', '=', 2 ] ], [ 'param1', '=', 3 ] ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->where( ...$query );

		$this->assertEquals( $data, $result->all() );
	}

	public function test_where_instance_of() {
		$data = [
			'arr' => [],
			'obj' => new \stdClass(),
		];

		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test->where_instance_of( \stdClass::class );

		$expected = $data['obj'];

		$this->assertSame( [ 'obj' => $expected ], $result->all() );
	}

	public function test_methods_chaining_with_closure() {
		$data   = [ 1, 2, 3, 4, 5, 5, 7, 8, 9 ];
		$test   = new \KToolbox\Collection\Collection( $data );
		$result = $test
			->split( 3, false )
			->skip( 1 )
			->limit( 1 )
			->each( function ( $chunk ) {
				return array_map( function ( $item ) {
					return $item * 2;
				}, $chunk );
			} )
			->flatten()
			->unique();

		$this->assertEquals( [ 8, 10 ], $result->all() );
	}

	public function test_iteration() {
		$data = [ 1, 2, 3 ];
		$test = new \KToolbox\Collection\Collection( $data );

		$result = [];
		foreach ( $test as $element ) {
			$result[] = $element;
		}

		$this->assertEquals( $data, $result );
	}

	public function test_property_access() {
		$data_1 = [ 'element' => 'value' ];
		$test_1 = new \KToolbox\Collection\Collection( $data_1 );

		$data_2          = new \stdClass();
		$data_2->element = 'value';
		$test_2          = new \KToolbox\Collection\Collection( $data_2 );

		$this->assertEquals( 'value', $test_1['element'] );
		$this->assertEquals( 'value', $test_2->element );
	}

	public function test_property_set() {
		$test_1            = new \KToolbox\Collection\Collection();
		$test_1->element   = 'value';
		$test_2            = new \KToolbox\Collection\Collection();
		$test_2['element'] = 'value';
		$test_3            = new \KToolbox\Collection\Collection();
		$test_3[]          = 'value';

		$this->assertEquals( [ 'element' => 'value' ], $test_1->all() );
		$this->assertEquals( [ 'element' => 'value' ], $test_2->all() );
		$this->assertEquals( [ 'value' ], $test_3->all() );
	}

	public function test_property_unset() {
		$test_1          = new \KToolbox\Collection\Collection();
		$test_1->element = 'value';
		unset( $test_1->element );

		$test_2            = new \KToolbox\Collection\Collection();
		$test_2['element'] = 'value';
		unset( $test_2['element'] );

		$this->assertEquals( [], $test_1->all() );
		$this->assertEquals( [], $test_2->all() );
	}

	public function test_to_json() {
		$data = [ 'elem1' => new \stdClass(), 'elem2', 'elem3' => 'value' ];
		$test = new \KToolbox\Collection\Collection( $data );

		$expected = '{"elem1":{},"0":"elem2","elem3":"value"}';

		$this->assertJsonStringEqualsJsonString( $expected, $test->to_json() );
	}

	public function test_ingest_arrayable() {
		$data = new \ArrayableClass();
		$test = new \KToolbox\Collection\Collection( $data );

		$this->assertEquals( [ 'another_value', 'elem1' => 'value1', 'elem2' => new \stdClass() ], $test->to_array() );
	}

	public function test_ingest_jsonable() {
		$data = new \JsonableClass();
		$test = new \KToolbox\Collection\Collection( $data );

		$this->assertJsonStringEqualsJsonString( $data->to_json(), $test->to_json() );
	}

	public function test_ingest_json_serializable() {
		$data = new \JsonSerializableClass();
		$test = new \KToolbox\Collection\Collection( $data );

		$this->assertJsonStringEqualsJsonString( json_encode( $data ), $test->to_json() );
	}

	public function test_ingest_traversable() {
		$data = new \TraversableClass();
		$test = new \KToolbox\Collection\Collection( $data );

		$this->assertEquals( [ 'item_1' => 'value1', 'item_2' => 'value2', 'item_3' => 'value3' ], $test->all() );
	}

	public function test_json_serialization_json_serializable() {
		$data = new \JsonSerializableClass();
		$test = new \KToolbox\Collection\Collection( [ $data ] );

		$this->assertJsonStringEqualsJsonString( '[' . json_encode( $data ) . ']', json_encode( $test ) );
	}

	public function test_json_serialization_jsonable() {
		$data = new \JsonableClass();
		$test = new \KToolbox\Collection\Collection( [ $data ] );

		$this->assertJsonStringEqualsJsonString( '[' . $data->to_json() . ']', json_encode( $test ) );
	}

	public function test_json_serialization_arrayable() {
		$data = new \ArrayableClass();
		$test = new \KToolbox\Collection\Collection( [ $data ] );

		$this->assertJsonStringEqualsJsonString( '[' . json_encode( $data->to_array() ) . ']', json_encode( $test ) );
	}

}
