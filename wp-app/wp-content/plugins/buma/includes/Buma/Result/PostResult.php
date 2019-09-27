<?php
/**
 * ApiResult
 */

namespace Buma\Result;

use Buma\FilterInput;
use Buma\PluginOptions;
use Buma\Route;
use Buma\Decorator\{DecoratorRooms};

/**
 * Class PostResult
 * @package Dkwp
 *
 * @method int get_numresult
 * @method array get_applied_filters
 * @method array get_clusters
 * @method array get_compares
 * @method array get_equipments
 * @method array get_facets
 * @method array get_headtohead_suggestions
 * @method array get_makes
 * @method array get_request
 * @method array get_smart_suggestions
 * @method array get_submodel_suggestions
 * @method array get_text
 * @method array get_trimlevels
 * @method array get_versions_hash
 * @method array get_models_hash
 * @method Makes get_makes_post_object
 */
class PostResult {
	/**
	 * Protected class vars
	 *
	 * @var array $_arr
	 */
	protected $_arr = [];

	/**
	 * @var PluginOptions
	 */
	protected $_options;

	/**
	 * @var Route
	 */
	protected $_route;

	/**
	 * @var array $_args
	 */
	protected $_args = [];

	/**
	 * ApiResult constructor.
	 *
	 * @param PluginOptions $options
	 * @param Route $route
	 */
	public function __construct( PluginOptions $options, Route $route = null ) {
		$this->_options = $options;
		$this->_route   = $route;
	}

	/**
	 * Let's handle non-existent get_-methods
	 *
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	final public function __call( string $method, array $args ) {
		$result = null;

		if ( 'get_' == substr( $method, 0, 4 ) ) {
			$default = ( ! empty( $args ) ? $args[0] : null );
			$result  = $this->get( substr( $method, 4 ), $default );

			/**
			 * Name of the filter is dynamic ... will something like Dkwp\Result\ApiResult::get_facets
			 */
			$result = apply_filters( sprintf( '%s::%s', __CLASS__, $method ), $result );
		}

		return $result;
	}

	/**
	 * Sets args for request
	 *
	 * @param array $args
	 *
	 * @return static
	 */
	public function set_args( array $args ) {
		$this->_args = $args;

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
	protected function get( $key, $default = null ) {
		return $this->_arr[ $key ] ?? $default;
	}

	/**
	 * Gets an item from _GET
	 *
	 * @param $key
	 * @param mixed $default
	 * @param int $filter
	 *
	 * @return mixed
	 */
	public function get_param( $key, $default = null, $filter = FILTER_DEFAULT ) {
		return ( new FilterInput( INPUT_GET, $key ) )->set_filter( $filter )->get( $default );
	}

	/**
	 * Gets args for request
	 *
	 * @return array
	 */
	public function get_args() {
		return $this->_args;
	}

	/**
	 * Setter
	 *
	 * @param $value
	 *
	 * @return $this
	 */
	protected function set( $value ) {
		$key = debug_backtrace()[1]['function'];

		if ( 'set_' == substr( $key, 0, 4 ) ) {
			$key = substr( $key, 4 );
		}

		$this->_arr[ $key ] = $value;

		return $this;
	}

	/**
	 * Decorates array
	 *
	 * @param array $arr
	 * @param string $decorator
	 *
	 * @return array
	 */
	protected function decorate_array( array $arr, $decorator = 'Buma\Decorator\DecoratorRooms' ) {
		$temp = [];

		foreach ( $arr as $item ) {
			$temp[] = new $decorator( $item );
		}

		return $temp;
	}


	/**
	 * Sets clusters
	 *
	 * @param $arr
	 *
	 * @return static
	 */
	public function set_rooms( $arr ): self {
		return $this->set( $this->decorate_array( $arr, 'Buma\Decorator\DecoratorRooms' ) );
	}

}