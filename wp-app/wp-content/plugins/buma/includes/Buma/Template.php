<?php
/**
 * Template
 */

namespace Buma;

/**
 * Class Template
 *
 * @package Buma
 */
class Template {

	/**
	 * Protected class vars
	 *
	 * @var string $_default
	 * @var string $_slug
	 * @var string $_format
	 */
	protected
		$_default,
		$_format;

	/**
	 * Constructor
	 *
	 * @param string $default
	 * @param string $format
	 */
	public function __construct(  $default = 'single.php',  $format = 'single-%s.php' ) {
		$this->_default = $default;
		$this->_format  = $format;
	}

	/**
	 * Creates object and loads template
	 *
	 * @param $file
	 *
	 * @return string
	 */
	public static function load_default( string $file, $query_var ) {
		$obj = new self( $file );

		return $obj->load_template_part( $query_var );
	}

	/**
	 * Factory that creates a template-object from a format string
	 *
	 * @param string $format
	 *
	 * @return Template
	 */
	public static function create_from_format( string $format ) {
		$default = sprintf( $format, '' );

		return new self( $default, $format );
	}

	/**
	 * Locates template and returns the requested template with its absolute path or a fallback
	 *
	 * @param string $replacement
	 *
	 * @return string
	 */
	public function locate_template( string $replacement ) {
		$templates = [
			sprintf( $this->_format, $replacement ),
			$this->_default,
		];

		return locate_template( $templates );
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		$replacement = '';
		$abs_path    = $this->locate_template( $replacement );

		$search = get_stylesheet_directory();
		$path   = get_template_directory();
		if ( $path != $search ) {
			$search = [ $search, $path ];
		}

		$rel_path = str_replace( $search, '', $abs_path );

		return current( explode( '.', $rel_path ) );
	}

	/**
	 * Locates and loads a template
	 *
	 * @param mixed $query_var
	 *
	 * @return string
	 */
	public function load_template_part( $query_var = null ): string {
		set_query_var( 'Buma', $query_var );

		ob_start();
		get_template_part( $this->get_slug() );
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

}