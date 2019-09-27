<?php

/**
 * Class JsonableClass
 */
class JsonableClass implements \KToolbox\Collection\Jsonable {

	/**
	 * @param int $options
	 *
	 * @return string
	 */
	public function to_json( int $options = 0 ) {
		return json_encode( [ 'elem1' => 'value1', 'elem2' => new \stdClass(), 'another_value' ], $options );
	}
}