<?php

/**
 * Class JsonSerializableClass
 */
class JsonSerializableClass implements \JsonSerializable {

	/**
	 * @var array
	 */
	public $items = [ 'a' => 'b', 'c' => 'd', 'e' => 'f' ];

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->items;
	}
}