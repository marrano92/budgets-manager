<?php

namespace Buma;

use \Buma\FilterInput;

/**
 * Class QueryVars
 * @package Buma
 */
class QueryVars {

	protected $vars, $groups;

	/**
	 * Constructor
	 */
	public function __construct( PluginOptions $options ) {
		$this->vars   = (array) apply_filters( 'drivek_query_vars_blacklist', $options->get( 'blacklist_vars' ) );
		$this->groups = (array) apply_filters( 'drivek_query_groups_blacklist', $options->get( 'blacklist_groups' ) );
	}

	/**
	 * @param array $hash
	 * @param array $vars
	 *
	 * @return array
	 */
	public function filter_vars( array $hash, array $vars ): array {
		return array_filter( $hash, function ( $key ) use ( $vars ) {
			$needle = strtolower( $key );

			return ! in_array( $needle, $vars );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * @param array $hash
	 * @param $groups
	 *
	 * @return array
	 */
	public function filter_groups( array $hash, array $groups ): array {
		return array_filter( $hash, function ( $key ) use ( $groups ) {
			foreach ( $groups as $item ) {
				if ( $item == substr( $key, 0, strlen( $item ) ) ) {
					return false;
				}
			}

			return true;
		}, ARRAY_FILTER_USE_KEY );
	}


	/**
	 * @param array $hash
	 *
	 * @return array
	 */
	public function clean( array $hash ): array {
		$hash = $this->filter_groups( $hash, $this->groups );

		return $this->filter_vars( $hash, $this->vars );
	}

	/**
	 * @param array $vars
	 * @param array $arr
	 *
	 * @return array
	 */
	public static function add( array $vars, array $arr ): array {
		$uri  = FilterInput::get_request_uri();
		$path = parse_url( $uri, PHP_URL_PATH );

		if ( '/' === $path ) {
			return $vars;
		}

		if ( count( $arr ) > 0 ) {
			array_push( $vars, ... $arr );
		}

		return array_unique( $vars );
	}

}