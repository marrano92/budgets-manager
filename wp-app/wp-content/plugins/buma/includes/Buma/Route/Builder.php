<?php

namespace Buma\Route;

use Buma\BuilderInterface;
use Buma\Rewrites;

/**
 * Class RouteBuilder
 *
 * @package Buma\Route
 */
class Builder implements BuilderInterface {

	/**
	 * Route init
	 *
	 * @return void
	 */
	public static function init() {
		$options  = options_factory();
		$rewrites = Rewrites::init( $options );

		$rewrites->add( new RoomSearch() );

		add_filter( 'hotel_route', function() {
			return \Buma\Registry::create()->get( 'route' );
		} );
	}

}