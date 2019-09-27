<?php

namespace Buma\Cpt;

use Buma\BuilderInterface;

/**
 * Class CptBuilder
 *
 * @package Buma\Cpt
 */
class Builder implements BuilderInterface {

	/**
	 * @codeCoverageIgnore
	 */
	public static function init() {
			Menus::init();
			Plates::init();
			Rooms::init();
	}

}