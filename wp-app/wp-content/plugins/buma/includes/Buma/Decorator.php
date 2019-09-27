<?php
/**
 * Decorator
 */

namespace Buma;

/**
 * Class Decorator
 * @package Dkwp
 */
class Decorator {

	/**
	 * Object to decorate
	 *
	 * @var object
	 */
	protected $_obj;

	/**
	 * Default return value
	 *
	 * @var string
	 */
	protected $_default;

	/**
	 * Decorator
	 *
	 * @param object $obj
	 */
	public function __construct( $obj ) {
		$this->_obj     = $obj;
		$this->_default = apply_filters( 'Buma/decorator_default', '-' );
	}

	/**
	 * Creates an decorator for a stdClass by passing an array
	 *
	 * @param array $arr
	 *
	 * @return $this
	 */
	public static function create( array $arr ) {
		return new static( (object) $arr );
	}

	/**
	 * Magic get
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->_obj->$key ?? null;
	}

	/**
	 * Magic isset
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->_obj->$key );
	}

	/**
	 * You can use every method of the decorated object
	 *
	 * @param string $method
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		if ( method_exists( $this->_obj, $method ) ) {
			return call_user_func_array( [ $this->_obj, $method ], $args );
		}
		else if ( 'the_' == substr( $method, 0, 4 ) ) {
			return print( $this->__get( substr( $method, 4 ) ) );
		}

		return null;
	}

	/**
	 * Gets a currency
	 *
	 * @param number
	 *
	 * @return string
	 */
	public static function get_currency( $value ): string {
		return sprintf( __( '€ %s', 'Buma' ), number_format_i18n( $value, 0 ) ) ;
	}

	/**
	 * @param string $key
	 * @param int $decimals
	 *
	 * @return string
	 */
	public function get_number_i18n( string $key, $decimals = 0 ): string {
		$value = $this->__get( $key );

		return $value ? number_format_i18n( $value, $decimals ) : '';
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_price_i18n( string $key ): string {
		$value = $this->__get( $key );

		return $value ? $this->get_currency( $value ) : '';
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_date_i18n( string $key ): string {
		$value = $this->__get( $key );

		if ( empty( $value ) ) {
			return '';
		}

		$date = new \DateTime( $value );

		return date_i18n( get_option( 'date_format' ), $date->getTimestamp() );
	}

	/**
	 * Gets a price range
	 *
	 * @param number $min
	 * @param number $max
	 *
	 * @return string
	 */
	public static function get_price_range( $min, $max ): string {
		return sprintf( _x( 'from %s to %s', 'Price Range', 'Buma' ), self::get_currency( $min ), self::get_currency( $max ) );
	}

	/**
	 * String conversion
	 * 
	 * @return string
	 */
	public function __toString() {
		return sprintf( '<pre>%s</pre>', print_r( $this->_obj, true ) );
	}

	/**
	 * You can get every element of the decorated object
	 *
	 * @param array $result
	 * @param array $keys
	 *
	 * @return mixed
	 */
	public function get( array $result = [ ], array $keys ) {
		foreach ( $keys as $key ) {
			$result[] = $this->__get( $key );
		}

		return $result;
	}

	/**
	 * @return Locale
	 */
	public function get_locale() {
		return Locale::create();
	}

	/**
	 * @return array
	 */
	public function get_object_vars() {
		return get_object_vars( $this->_obj );
	}

	/**
	 * Gets template partial
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function get_template_part( $filename ) {
		$template = Template::create_from_format( $filename );

		return $template->load_template_part( $this );
	}

}