<?php

namespace Buma\Cpt;

/**
 * Interface MetaBoxInterface
 *
 * @package Buma\Cpt
 */
interface MetaBoxInterface {

	/**
	 * @return MetaBoxInterface
	 */
	public function setup_metabox(): MetaBoxInterface;

}