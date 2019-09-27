<?php

/**
 * Class ExampleDecorator
 */
class ExampleDecorator {

	/**
	 * @var \stdClass
	 */
	public $_obj;

	/**
	 * ExampleDecorator constructor.
	 *
	 * @param \stdClass $data
	 */
	public function __construct( \stdClass $data ) {
		$this->_obj = (object) $data;
	}

	/**
	 * @return string
	 */
	public function get_demo_prop() {
		return $this->_obj->demo_prop;
	}
}