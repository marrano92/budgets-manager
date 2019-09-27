<?php


namespace Buma;


class Locale {

	/**
	 * @var string
	 */
	protected $locale;

	/**
	 * Locale constructor
	 */
	public function __construct() {
		$this->locale = get_locale();
	}

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return Locale
	 */
	public static function create(): self {
		$registry = Registry::create();
		$object   = $registry->get( __CLASS__ );

		if ( ! $object ) {
			$object = new self();

			add_filter( 'Buma/get_language', [ $object, 'get_language' ] );
			add_filter( 'Buma/get_country', [ $object, 'get_country' ] );

			$registry->set( __CLASS__, $object );
		}

		return $object;
	}

	/**
	 * Gets locale language for default values
	 *
	 * @return string
	 */
	public function get_language(): string {
		return '_' == $this->locale[2] ? substr( get_locale(), 0, 2 ) : $this->locale;
	}

	/**
	 * Gets the country from the locale
	 *
	 * @return string
	 */
	public function get_country(): string {
		return '_' == $this->locale[2] ? strtoupper( substr( $this->locale, - 2 ) ) : $this->locale;
	}

}