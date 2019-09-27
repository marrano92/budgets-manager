<?php

/**
 * Class ArrayableClass
 */
class ArrayableClass implements \KToolbox\Collection\Arrayable {

	/**
	 * @return array
	 */
	public function to_array() {
		return [ 'elem1' => 'value1', 'elem2' => new \stdClass(), 'another_value' ];
	}
}