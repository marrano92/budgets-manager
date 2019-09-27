<?php
/**
 * Registry
 */

namespace Buma;

/**
 * Class Registry
 * @package Buma
 */
class Registry {

	/**
	 * Container
	 * @var array
	 */
	protected $registered = [];

	/**
	 * Singleton Factory
	 *
	 * @codeCoverageIgnore
	 */
	public static function create() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Set
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return Registry
	 */
	public function set( $key, $value ) {
		$this->registered[ $key ] = $value;

		return $this;
	}

	/**
	 * Getter
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = false ) {
		return $this->registered[ $key ] ?? $default;
	}

	/**
	 * Isset
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return isset( $this->registered[ $key ] );
	}

}