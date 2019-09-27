<?php

namespace Buma\OptionsPage;

use Buma\PluginOptions;

abstract class Tab {

	/**
	 * Tab id
	 *
	 * @var string $id
	 */
	protected $id;

	/**
	 * @var PluginOptions $options
	 */
	protected $options;


	public function __construct( PluginOptions $options, string $page ) {
		$this->options  = $options;

		$this->set_page( $page );
	}

	/**
	 * @codeCoverageIgnore
	 *
	 * @param PluginOptions $options
	 * @param string $page
	 *
	 * @return Tab
	 */
	public static function init( PluginOptions $options, string $page ) {
		$obj = new static( $options, $page );

		return $obj;
	}

	/**
	 * Get tab name/label
	 *
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Set page
	 *
	 * @param $page
	 */
	public function set_page( $page ) {
		$this->page = $page;
	}

	/**
	 * Get page
	 *
	 * @return string
	 */
	public function get_page() {
		return $this->page;
	}

	/**
	 * Get the tab id
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Start the tab section
	 *
	 * @codeCoverageIgnore
	 */
	public function init_section() {
		add_settings_section(
			$this->get_id(),
			'',
			[ $this, 'section' ],
			$this->get_page()
		);
	}

	/**
	 * Renders form-element (text-input)
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $key Name and ID of the form-element
	 * @param string $class CSS class(es)
	 *
	 * @return string
	 */
	public function render_input( $key, $class = 'regular-text all-options' ) {

		return sprintf(
			'<input id="%1$s" name="%2$s[%1$s]" value="%3$s" class="%4$s" />',
			$key,
			$this->options->get_name(),
			esc_attr( $this->options->$key ),
			$class
		);
	}

	/**
	 * Renders form-element (checkbox-input)
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $key Name and ID of the form-element
	 * @param string $class CSS class(es)
	 *
	 * @return string
	 */
	public function render_checkbox( $key, $class = '' ) {

		return sprintf(
			'<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1" class="%3$s"%4$s />',
			$key,
			$this->options->get_name(),
			$class,
			checked( 1, $this->options->$key, false )
		);
	}

	/**
	 * Extend it to add fields
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	abstract public function section();
}