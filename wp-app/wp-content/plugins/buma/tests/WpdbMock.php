<?php

/**
 * Class WpdbMock
 * This is used as a mock of the global $wpdb var, which handles databases transactions. You should make mocks/partial mocks of this class in order to test database queries.
 */
class WpdbMock {

	/**
	 * @var string
	 */
	public $postmeta = 'postmeta';

	/**
	 * @param string $query
	 *
	 * @return array
	 */
	public function get_results(string $query): array {
		return [];
	}
}