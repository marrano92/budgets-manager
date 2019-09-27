<?php

namespace KToolbox\DataNavigator;

use \KToolbox\DataNavigator\DataChunk;

/**
 * Class DataNavigator
 * @package KToolbox\DataNavigator
 */
class DataNavigator {

	/**
	 * @var DataChunk
	 */
	protected $element;

	/**
	 * DataNavigator constructor.
	 *
	 * @param $element
	 */
	public function __construct( $element = null ) {
		$this->element = new DataChunk( $element );
	}

	/**
	 * Access a nested path in the element using a dot or arrow notation
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( string $key, $default = null ) {
		if ( $this->element->is_empty() ) {
			return null;
		}

		$separator = $this->get_navigation_separator( $key );

		if ( $key === $separator ) {
			$path = [ $key ];
		} else {
			$path = '' === $separator ? str_split( $key ) : explode( $separator, $key );
		}

		while ( ! is_null( $segment = array_shift( $path ) ) ) {
			if ( '*' === $segment && $this->element->finish_data instanceof \KToolbox\Collection\Collection ) {
				return $this->element->finish_data->all();
			}

			$this->element->go_to( $segment );

			if ( ! $this->element->can_go_further() && count( $path ) > 0 ) {
				$this->element->set_error();
			}
			if ( $this->element->end || $this->element->error ) {
				break;
			}

			$this->element->reset();
		}

		return ! $this->element->error ? $this->element->finish_data : $default;
	}

	public static function is_navigable( $data, $step ) {
		$obj = new self( $data );

		return $obj->element->can_go_further() && ( null !== $obj->get_navigation_separator( $step ) );
	}

	/**
	 * Guess the separator for an object navigation. If an asterisk
	 * is passed, the separator will be an empty string so the explode
	 * above will be safer to execute.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	protected function get_navigation_separator( string $key ) {
		$separator = null;
		if ( strpos( $key, '.' ) !== false ) {
			$separator = '.';
		} elseif ( strpos( $key, '->' ) !== false ) {
			$separator = '->';
		} elseif ( '*' === $key ) {
			$separator = '';
		} elseif ( null != $key ) {
			$separator = $key;
		}

		return $separator;
	}
}