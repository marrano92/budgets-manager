<?php

namespace KToolbox;

use \WP_CLI;

/**
 * Class BaseCommand
 * @package KToolbox
 *
 * @codeCoverageIgnore
 */
abstract class BaseCommand {

	/**
	 * Setup the command name
	 *
	 * @return string
	 */
	public abstract function get_name(): string;

	/**
	 * Setup the command arguments.
	 *
	 * @return array
	 */
	public abstract function get_arguments(): array;

	/**
	 * Command execution
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public abstract function __invoke( array $args );

	/**
	 * Logs a message
	 *
	 * @param array $messages
	 * @param string $severity = 'log'
	 *
	 * @return void
	 */
	protected function console_log( array $messages, string $severity = 'log' ) {
		foreach ( $messages as $string ) {
			WP_CLI::$severity( $string );
		}
	}

	/**
	 * Exits with a log
	 *
	 * @param array $messages
	 *
	 * @return void
	 */
	protected function exit_with_error( array $messages ) {
		$this->console_log( [ 'Something went wrong.' ], 'error' );
		foreach ( $messages as $string ) {
			$this->console_log( $string );
		}
		exit();
	}
}