<?php

namespace Buma;

/**
 * Class PluginOptions
 * @package Buma
 */
class PluginOptions {

	/**
	 * Protected class vars
	 *
	 * @var string $options_name
	 * @var array $options_value
	 * @var array $default_values
	 */
	protected
		$option_name,
		$option_value,
		$default_values;

	/**
	 * Private class var
	 *
	 * @var Locale
	 */
	private $_locale;


	/**
	 * Constructor
	 *
	 * @param Locale $locale
	 * @param string $option_name
	 * @param array $default_values
	 */
	public function __construct( Locale $locale, $option_name, $default_values = [] ) {
		$this->_locale = $locale;

		$this->option_name    = $option_name;
		$this->option_value   = get_option( $this->option_name );
		$this->default_values = $default_values;
	}

	/**
	 * Does this key already have a filter function?
	 *
	 * @codeCoverageIgnore
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function has_filter( $key ) {
		return has_filter( "hb/Buma_{$key}" );
	}

	/**
	 * Factory
	 *
	 *
	 * @param Locale $locale
	 * @param string $options_name
	 * @param array $default_values
	 *
	 * @return PluginOptions
	 */
	public static function create( Locale $locale, $options_name = 'Buma_options', $default_values = [] ) {
		$registry = Registry::create();
		$object   = $registry->get( __CLASS__ );

		if ( ! $object ) {
			if ( empty( $default_values ) ) {

				$default_values = [
					'plugin_lang'               => $locale->get_language(),
					'results_per_page'       => '12',
					'white_list' => ['price', 'size', 'guest', 'order']
				];

			}

			$object = new self( $locale, $options_name, $default_values );

			$registry->set( __CLASS__, $object );

			add_action( 'Buma/set_plugin_option', [ $object, 'set' ], 10, 2 );
			add_filter( 'Buma_plugin_options', [ $object, 'get' ], 10, 2 );
		}

		return $object;
	}

	/**
	 * Gets an item with a default as fallback
	 *
	 * @param $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = '' ) {
		if ( '' === $default && isset( $this->default_values[ $key ] ) ) {
			$default = $this->default_values[ $key ];
		}
		$value = ! empty( $this->option_value[ $key ] ) ? $this->option_value[ $key ] : $default;

		return $value;
	}

	/**
	 * Magic getter
	 *
	 * @param $key
	 *
	 * @return mixed
	 *
	 * @codeCoverageIgnore
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Gets the name of the option
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	public function get_name() {
		return $this->option_name;
	}
}