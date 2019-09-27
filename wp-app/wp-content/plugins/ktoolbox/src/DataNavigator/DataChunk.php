<?php

namespace KToolbox\DataNavigator;

/**
 * Class DataChunk
 * @package KToolbox\DataNavigator
 */
class DataChunk {

	/**
	 * @var mixed
	 */
	public $start_data = null;

	/**
	 * @var mixed
	 */
	public $finish_data = null;

	/**
	 * @var bool
	 */
	public $error = false;

	/**
	 * @var bool
	 */
	public $end = false;

	/**
	 * DataChunk constructor.
	 *
	 * @param $data
	 */
	public function __construct( $data ) {
		$this->start_data  = $data;
		$this->finish_data = $data;
	}

	/**
	 * Dives into the given object/array
	 *
	 * @param string $next_step
	 *
	 * @return mixed
	 */
	public function go_to( string $next_step ) {
		if ( is_object( $this->start_data ) && property_exists( $this->start_data, $next_step ) ) {
			$this->finish_data = $this->start_data->$next_step;
		} elseif ( ( is_array( $this->start_data ) ) && array_key_exists( $next_step, $this->start_data ) ) {
			$this->finish_data = $this->start_data[ $next_step ];
		} else {
			$this->set_error();
		}

		$this->end = ( $this->error || ! $this->can_go_further() );
	}

	/**
	 * Check if the finish data is navigable
	 *
	 * @return bool
	 */
	public function can_go_further() {
		return is_object( $this->finish_data ) || is_array( $this->finish_data );
	}

	/**
	 * Prepare for the next iteration
	 *
	 * @return self
	 */
	public function reset() {
		$this->start_data = $this->finish_data;

		return $this;
	}

	/**
	 * Set the error to true
	 *
	 * @return void
	 */
	public function set_error() {
		$this->error = true;
	}

	/**
	 * Check if the object is empty
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return empty( $this->start_data );
	}

}