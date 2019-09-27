<?php

namespace Buma\Cpt;

/**
 * Interface CptInterface
 * @package Buma\Cpt
 */
interface CptInterface {

	/**
	 * @return CptInterface
	 */
	public function register_post_type(): CptInterface;

}