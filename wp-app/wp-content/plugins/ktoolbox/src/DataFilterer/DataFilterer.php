<?php

namespace KToolbox\DataFilterer;

use function foo\func;

/**
 * Class DataFilterer
 * @package KToolbox\DataFilterer
 */
class DataFilterer {

	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var array
	 */
	protected $query = [];

	/**
	 * @var array
	 */
	protected $results = [];

	/**
	 * DataFilterer constructor.
	 *
	 * @param array|\stdClass $data
	 * @param array $query
	 */
	public function __construct( $data = [], array $query = [] ) {
		$this->data  = $data;
		$this->query = $query;
	}

	/**
	 * Execute data filtering
	 *
	 * @return mixed
	 */
	public function execute() {
		if ( empty( $this->data ) || empty( $this->query ) ) {
			return null;
		}

		foreach ( $this->data as $key => $data ) {
			foreach ( $this->query as $options ) {
				if ( $this->is_logical_query( $options ) ) {
					$this->manage_logical_query( $key, $data, $options );
				} else {
					$this->execute_simple_query( $key, $data, $options );
				}
			}
		}

		$this->results = array_unique( $this->results );

		return $this->build_data();
	}

	/**
	 * Detect a query with AND or OR operator
	 *
	 * @param $options
	 *
	 * @return bool
	 */
	protected function is_logical_query( $options ): bool {
		return is_array( $options ) &&
		       is_array( $options[0] ) &&
		       is_string( $options[1] ) &&
		       is_array( $options[2] );
	}

	/**
	 * Prepare data for the logical comparison
	 *
	 * @param $key
	 * @param $data
	 * @param array $options
	 */
	protected function manage_logical_query( $key, $data, array $options ) {
		$results = [];
		if ( $this->no_value_errors( $options ) ) {
			for ( $i = 0; $i < 3; $i += 2 ) {
				$options[ $i ] = $this->convert_to_values( $data, $options[ $i ] );
				$results[]     = [ 'key' => $key, 'comparison' => $this->compare( $data, $options[ $i ][0], $options[ $i ][1], $options[ $i ][2] ) ];
				//$this->apply_logical_operator( $options[1], $results );
			}
		}
		$this->apply_logical_operator( $options[1], $results );
	}

	/**
	 * Execute a simple query
	 *
	 * @param $key
	 * @param $data
	 * @param $options
	 */
	protected function execute_simple_query( $key, $data, $options ) {
		$options = $this->convert_to_values( $data, $options );
		if ( $this->no_value_errors( $options ) ) {
			$comparison = $this->compare( $data, $options[0], $options[1], $options[2] );
			if ( $comparison['success'] ) {
				$this->results[] = serialize( [ 'key' => $key, 'comparison' => $comparison ] );
			}
		}
	}

	/**
	 * Convert paths to values. if needed
	 *
	 * @param $data
	 * @param array $options
	 *
	 * @return array
	 */
	protected function convert_to_values( $data, array $options ): array {
		if ( \KToolbox\DataNavigator\DataNavigator::is_navigable( $data, $options[0] ) ) {
			$options[0] = ( new \KToolbox\DataNavigator\DataNavigator( $data ) )->get( $options[0], '__data_navigator_error__' );
		} else {
			$options[0] = '__data_navigator_error__';
		}

		if ( \KToolbox\DataNavigator\DataNavigator::is_navigable( $data, $options[2] ) ) {
			$converted  = ( new \KToolbox\DataNavigator\DataNavigator( $data ) )->get( $options[2], '__data_navigator_error__' );
			$options[2] = '__data_navigator_error__' === $converted ? $options[2] : $converted;
		}

		return $options;
	}

	/**
	 * Detect values conversion errors
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	protected function no_value_errors( array $options ): bool {
		return '__data_navigator_error' !== $options[0] && '__data_navigator_error' !== $options[2];
	}

	/**
	 * Compare the given values with the given operator
	 *
	 * @param $data
	 * @param $first_value
	 * @param string $operator
	 * @param $second_value
	 *
	 * @return array
	 */
	protected function compare( $data, $first_value, string $operator, $second_value ) {
		$comparison = [ 'success' => false, 'data' => null ];
		if ( $this->apply_comparison_operator( $first_value, $operator, $second_value ) ) {
			$comparison = [ 'success' => true, 'data' => $data ];
		}

		return $comparison;
	}

	/**
	 * Apply the AND or OR operator
	 *
	 * @param string $operator
	 * @param array $data_sets
	 */
	protected function apply_logical_operator( string $operator, array $data_sets ) {
		switch ( $operator ) {
			case 'AND':
				if ( ! ( $data_sets[0]['comparison']['success'] && $data_sets[1]['comparison']['success'] ) ) {
					$data_sets = [];
				}
				break;

			case 'OR':
				$data_sets = array_filter( $data_sets, function ( $entry ) {
					return $entry['comparison']['success'];
				} );
				break;

			default:
				$data_sets = [];
				break;
		}

		foreach ( $data_sets as $data_entry ) {
			$this->results[] = serialize( $data_entry );
		}
	}

	/**
	 * Apply the comparison operator
	 *
	 * @param $first_value
	 * @param string $operator
	 * @param $second_value
	 *
	 * @return bool
	 */
	protected function apply_comparison_operator( $first_value, string $operator, $second_value ): bool {
		switch ( $operator ) {
			case '=':
			case'==':
				$result = $first_value == $second_value;
				break;
			case '===':
				$result = $first_value === $second_value;
				break;
			case '!=':
				$result = $first_value != $second_value;
				break;
			case '!==':
				$result = $first_value !== $second_value;
				break;
			case '>':
				$result = is_numeric( $first_value ) && is_numeric( $second_value ) && $first_value > $second_value;
				break;
			case '>=':
				$result = is_numeric( $first_value ) && is_numeric( $second_value ) && $first_value >= $second_value;
				break;
			case '<':
				$result = is_numeric( $first_value ) && is_numeric( $second_value ) && $first_value < $second_value;
				break;
			case '<=':
				$result = is_numeric( $first_value ) && is_numeric( $second_value ) && $first_value <= $second_value;
				break;
			default:
				$result = false;
				break;
		}

		return $result;
	}

	/**
	 * Build and return the results
	 *
	 * @return mixed
	 */
	protected function build_data() {
		$cast_object = $this->data instanceof \stdClass;
		$this->results = array_unique( $this->results );
		$data          = [];

		foreach ( $this->results as $result ) {
			$result                 = unserialize( $result );
			$data[ $result['key'] ] = $result['comparison']['data'];
		}

		return $cast_object ? (object) $data : $data;
	}
}