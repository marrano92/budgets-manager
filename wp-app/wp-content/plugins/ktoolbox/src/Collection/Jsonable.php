<?php

namespace KToolbox\Collection;

/**
 * Interface Jsonable
 * @package KToolbox\Collection
 */
interface Jsonable {

	/**
	 * Convert to json
	 *
	 * @param int $options
	 *
	 * @return string
	 */
	public function to_json(int $options = 0);
}