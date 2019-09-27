<?php

namespace Buma\Result;


/**
 * Interface ApiResultInterface
 *
 * @package Dkwp\Result
 *
 */
interface PostResultInterface {

	/**
	 * Does the request
	 *
	 * @return PostResultInterface
	 */
	public function make_request(): PostResultInterface;

	/**
	 * Prepares the content object
	 *
	 * @return \stdClass
	 */
	public function build_content(): \stdClass;

}