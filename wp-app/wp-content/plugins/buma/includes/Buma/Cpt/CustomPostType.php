<?php

namespace Buma\Cpt;

use Buma\QueryVars;

class CustomPostType {

	/**
	 * @return static
	 */
	public static function init() {
		$obj = new static();

		if ( $obj instanceof CptInterface ) {
			$obj->register_post_type();
		}

		if ( $obj instanceof MetaBoxInterface ) {
			add_action( 'admin_menu', [ $obj, 'setup_metabox' ] );
		}

		return $obj->register_hooks();
	}

	/**
	 * @return static
	 */
	public function register_hooks() {
		return $this;
	}

	/**
	 * Adds the promo_source query var.
	 *
	 * This is used to differentiate the source when accessing the custom post type from the rewrite rule
	 * /promo-[source]/slug
	 *
	 * @param $vars
	 *
	 * @return array
	 */
	public function query_vars( $vars ) {
		$arr = [
			'promo_source',
		];

		return QueryVars::add( $vars, $arr );
	}
}