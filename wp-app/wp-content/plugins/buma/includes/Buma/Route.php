<?php

namespace Buma;

/**
 * Class Route
 * @package Buma
 */
abstract class Route {

	/**
	 * Protected class vars
	 *
	 * @var array $_request
	 * @var string $_last_modification
	 * @var string $_template
	 * @var string $_slug
	 */
	protected
		$_request = [],
		$_template,
		$_slug;

	/**
	 * Constructor
	 *
	 * It will only add a query vars filter if an endpoint is given
	 *
	 * @param Proxy $endpoints
	 * @param array $request
	 */
	public function __construct( array $request = [] ) {
			add_filter( 'query_vars', [ $this, 'query_vars' ] );

			$action = sprintf( 'dk/%s::the_uri', $this->sanitize_classname( get_called_class() ) );
			if ( ! has_action( $action, [ $this, 'the_uri' ] ) ) {
				add_action( $action, [ $this, 'the_uri' ] );
			}

			$this->_request   = $request;

	}

	/**
	 * Init
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $request
	 *
	 * @return Route
	 */
	public static function init(  array $request = [] ): self {
		$obj = new static( $request );

		$obj->add_hooks();

		return $obj;
	}

	/**
	 * Add hooks
	 *
	 * @codeCoverageIgnore
	 */
	public function add_hooks() {
		add_filter( 'wp_title', [ $this, 'wp_title' ], 99 );
		add_filter( 'pre_get_document_title', [ $this, 'wp_title' ], 99 );
		add_filter( 'wpseo_title ', [ $this, 'wp_title' ] );
		add_filter( 'wpseo_opengraph_title', [ $this, 'wp_title' ] );
		add_filter( 'wpseo_twitter_title', [ $this, 'wp_title' ] );
		add_filter( 'dkshare_title', [ $this, 'wp_title' ] );

		add_filter( 'wpseo_canonical', [ $this, 'wp_canonical' ] );
		add_filter( 'wpseo_opengraph_url', [ $this, 'wp_canonical' ] );
		add_filter( 'dkshare_url', [ $this, 'wp_canonical' ] );

		add_filter( 'dkshare_id', [ $this, 'wp_md5_id' ] );

		add_filter( 'wpseo_metadesc', [ $this, 'wp_description' ] );
		add_filter( 'wpseo_opengraph_desc', [ $this, 'wp_description' ] );
		add_filter( 'wpseo_twitter_description', [ $this, 'wp_description' ] );

		add_action( 'wpseo_opengraph', [ $this, 'wp_og_image' ] );
		add_action( 'wpseo_pre_analysis_post_content', [ $this, 'wp_seo_content' ] );
		add_filter( 'wpseo_next_rel_link', '__return_false' );
		add_filter( 'wpseo_metakey', '__return_false' );

		add_filter( 'the_content', [ $this, 'the_content' ] );
		add_filter( 'template_include', [ $this, 'template_include' ], 99 );

		add_action( 'admin_bar_menu', [ $this, 'admin_bar_menu' ], 999 );
		add_action( 'wp', [ $this, 'wp' ], 1 );
		add_filter( 'post_link', [ $this, 'post_link' ], 1 );

		add_filter( 'amp_frontend_show_canonical', [ $this, 'amp_frontend_show_canonical' ] );
	}

	/**
	 * Set request
	 *
	 * @param array $request
	 */
	public function set_request( array $request ) {
		$this->_request = $request;
	}

	/**
	 * Strategy
	 *
	 * @return ApiResult
	 */
	abstract public function get_data();

	/**
	 * Adds rewrite rule
	 *
	 * @param int $position
	 *
	 * @return void
	 */
	abstract public function add_rewrite_rule( int $position );


	/**
	 * Action: prints structured data into the head of a page
	 */
	abstract public function wp_structured_data();

	/**
	 * Filter: wp_title
	 *
	 * @return string
	 */
	abstract public function wp_title(): string;

	/**
	 * Filter: Changes the meta description here
	 *
	 * @return string
	 */
	abstract public function wp_description(): string;

	/**
	 * Filter: the_content
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	abstract public function the_content( string $content ): string;

	/**
	 * Adds analytics
	 *
	 * Override this with your code
	 */
	public function add_analytics() {
		// Default: do nothing
	}

	/**
	 * @param array $vars
	 *
	 * @return array
	 */
	public function query_vars( array $vars ): array {
		return $vars;
	}

	/**
	 * Filter: gets canonical url
	 *
	 * @return string
	 */
	public function wp_canonical(): string {
		return $this->build_uri( [] );
	}

	/**
	 * Action: prints Open Graph image
	 *
	 * Hint: use something like
	 *
	 * $GLOBALS['wpseo_og']->image_output( 'http://img.jpg' );
	 * @codeCoverageIgnore
	 */
	public function wp_og_image() {
		// Default: do nothing
	}

	/**
	 * Return empty content to avoid that the SEO plugin catches images
	 * and put them into query_og:image
	 */
	public function wp_seo_content() {
		return '';
	}

	/**
	 * Gets template for this route
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public function template_include( string $template ): string {
		$class = $this->sanitize_classname( get_called_class() );

		$obj = new Template( $template );

		return $obj->locate_template( $class );
	}

	/**
	 * Returns sanitized class name without namespace
	 *
	 * @param string $class
	 *
	 * @return string
	 */
	public function sanitize_classname( $class ) {
		$parts = explode( '\\', $class );

		return sanitize_title( end( $parts ) );
	}

	/**
	 * Gets the Locale object
	 *
	 * @return Locale
	 */
	public function get_locale(): Locale {
		return $this->get_options()->get_locale();
	}

	/**
	 * Builds the URI for you
	 *
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function build_uri( array $params = [] ) {
		return apply_filters( __METHOD__, home_url( sprintf( '/%s/', $this->flatten( [], $params ) ) ) );
	}

	/**
	 * Echoes the output of build_uri
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $params
	 */
	public function the_uri( array $params = [] ) {
		echo $this->build_uri( $params );
	}

	/**
	 * Flattens the arr and the vars from params
	 *
	 * @param array $arr
	 * @param array $params
	 *
	 * @return string
	 */
	public function flatten( array $arr, array $params ) {
		foreach ( $this->query_vars( [] ) as $var ) {
			$item = $params[ $var ] ?? get_query_var( $var );
			if ( ! empty( $item ) ) {
				$arr[] = rawurlencode( $item );
			}
		}

		return implode( '/', $arr );
	}

	/**
	 * Safe way to get a query var
	 *
	 * @param string $query_var
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_query_var( $query_var, $default = '' ) {
		return $this->_request[ $query_var ] ?? get_query_var( $query_var, $default );
	}

	/**
	 * Sets 404
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	public function set_404( $data ) {
		global $wp_query;

		// @codeCoverageIgnoreStart
		if ( ! is_null( $wp_query ) ) {
			$wp_query->set_404();

			status_header( 404 );
			nocache_headers();
		}
		// @codeCoverageIgnoreEnd

		$this->_template = 'parts/404.php';

		return $data;
	}



	/**
	 * Remove not needed items from the top bar
	 *
	 * @codeCoverageIgnore
	 *
	 * @param $wp_admin_bar
	 */
	public function admin_bar_menu( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'customize' );
		$wp_admin_bar->remove_node( 'edit' );
	}

	/**
	 * Create a fake post on the route's back.
	 *
	 * This will let the amp plugin works correctly and
	 * avoid warnings when no posts are found.
	 *
	 * @codeCoverageIgnore
	 *
	 * @see https://barn2.co.uk/create-fake-wordpress-post-fly/
	 */
	public function wp() {
		global $wp, $wp_query;

		$post_id                 = - 99; // negative ID, to avoid clash with a valid post
		$post                    = new \stdClass();
		$post->ID                = $post_id;
		$post->post_author       = 1;
		$post->post_date         = current_time( 'mysql' );
		$post->post_date_gmt     = current_time( 'mysql', 1 );
		$post->post_modified     = current_time( 'mysql' );
		$post->post_modified_gmt = current_time( 'mysql', 1 );
		$post->post_title        = $this->wp_title();
		$post->post_content      = $this->the_content( '' );
		$post->post_status       = 'publish';
		$post->comment_status    = 'closed';
		$post->ping_status       = 'closed';
		$post->post_name         = 'route-page-' . uniqid(); // append random number to avoid clash
		$post->post_type         = 'post';
		$post->filter            = 'raw'; // important

		// Convert to WP_Post object
		$wp_post = new \WP_Post( $post );

		wp_cache_add( $post_id, $wp_post, 'posts', 1 );

		// Update the main query
		$wp_query->post                 = $wp_post;
		$wp_query->posts                = array( $wp_post );
		$wp_query->queried_object       = $wp_post;
		$wp_query->queried_object_id    = $post_id;
		$wp_query->found_posts          = 1;
		$wp_query->post_count           = 1;
		$wp_query->max_num_pages        = 1;
		$wp_query->is_page              = false;
		$wp_query->is_singular          = true;
		$wp_query->is_single            = true;
		$wp_query->is_attachment        = false;
		$wp_query->is_archive           = false;
		$wp_query->is_category          = false;
		$wp_query->is_tag               = false;
		$wp_query->is_tax               = false;
		$wp_query->is_author            = false;
		$wp_query->is_date              = false;
		$wp_query->is_year              = false;
		$wp_query->is_month             = false;
		$wp_query->is_day               = false;
		$wp_query->is_time              = false;
		$wp_query->is_search            = false;
		$wp_query->is_feed              = false;
		$wp_query->is_comment_feed      = false;
		$wp_query->is_trackback         = false;
		$wp_query->is_home              = false;
		$wp_query->is_embed             = false;
		$wp_query->is_paged             = false;
		$wp_query->is_admin             = false;
		$wp_query->is_preview           = false;
		$wp_query->is_robots            = false;
		$wp_query->is_posts_page        = false;
		$wp_query->is_post_type_archive = false;

		$GLOBALS['wp_query'] = $wp_query;
		$wp->register_globals();
	}

	/**
	 * Fix virtual posts / routes post link
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function post_link( $url ) {
		$re = '/.+\/route-page-.+\/-99\/$/';

		if ( preg_match( $re, $url ) || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ) {
			return $this->wp_canonical();
		}

		return $url;
	}

	/**
	 * Get slug
	 * @return string
	 */
	public function get_slug() {
		return $this->_slug ?? '';
	}
}
