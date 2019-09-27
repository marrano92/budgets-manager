<?php

namespace KToolbox;

/**
 * Class Url
 * @package KToolbox
 *
 * This class helps you analyzing the request url
 *
 * @codeCoverageIgnore
 */
class Url {

	/**
	 * Url
	 *
	 * @var string
	 */
	protected $url;

	public function __construct( $url, bool $full = false ) {
		$this->url = $full ? $url : home_url( $url );
	}

	/**
	 * Get parsed param from an URL
	 *
	 * Returns false for filtering with add_query_var
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function parse_param( string $key ) {
		$value = false;

		if ( strpos( $this->url, $key ) ) {
			parse_str( parse_url( $this->url, PHP_URL_QUERY ), $params );

			$value = $params[ $key ];
		}

		return $value;
	}

}