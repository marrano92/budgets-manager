<?php

/**
 * Class TraversableClass
 */
class TraversableClass implements \IteratorAggregate {

	/**
	 * @var string
	 */
	public $item_1 = 'value1';

	/**
	 * @var string
	 */
	public $item_2 = 'value2';

	/**
	 * @var string
	 */
	public $item_3 = 'value3';

	/**
	 * @return ArrayIterator
	 */
	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator( $this );
	}
}