<?php


namespace Buma\Route;

use Buma\{Result\PostResult\RoomSearchResult, Result\PostResultInterface, Route, Template};

class RoomSearch extends Route {

	/**
	 * Protected class var
	 *
	 * @var string $_template
	 */
	protected
		$_template = 'parts/search.php';

	/**
	 * Child constructor calls parent's constructor
	 *
	 * It will only add the ajax actions if an endpoint is given
	 *
	 * @param array $request
	 */
	public function __construct(  array $request = [] ) {
		parent::__construct( $request );

		$this->_slug = _x( 'search', 'Route URI', 'dkwp' );
	}

	public function add_rewrite_rule( int $position ){
		$rewrite = sprintf( 'index.php?Buma_route=%s', $position );
		$uri     = apply_filters( __METHOD__, sprintf( '^%s', $this->_slug ) );

		add_rewrite_rule( $uri, $rewrite, 'top' );
	}

	/**
	 * Action: prints structured data into the head of a page
	 */
	public function wp_structured_data(){
		return '';
	}

	/**
	 * Filter: wp_title
	 *
	 * @return string
	 */
	public function wp_title(): string{
		return 'title';
	}

	/**
	 * Filter: Changes the meta description here
	 *
	 * @return string
	 */
	public function wp_description(): string {
		return 'description';
	}

	/**
	 * Gets data
	 *
	 * @return PostResultInterface
	 *
	 * @throws \Exception
	 */
	public function get_data(): PostResultInterface {
		static $data = null;

		if ( is_null( $data ) ) {
			$data = new RoomSearchResult( options_factory(), $this );

			$data->make_request();
		}

		return $data;
	}

	/**
	 * Filter: the_content
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function the_content( string $content ): string {
		$data     = $this->get_data();
		$result   = $data->build_content();

		$template = new Template( $this->_template );

		return $template->load_template_part( $result );
	}

}