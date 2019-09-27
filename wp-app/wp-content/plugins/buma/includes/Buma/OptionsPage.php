<?php

namespace Buma;

use Buma\OptionsPage\Tab;

class OptionsPage {

	/**
	 * Protected vars
	 *
	 * @var string
	 * @var PluginOptions
	 * @var array
	 */
	protected
		$title,
		$options,
		$tabs;

	/**
	 * Constructor
	 *
	 * @param PluginOptions $options
	 * @param string $title
	 * @param Proxy $endpoint
	 * @param array $tabs
	 */
	public function __construct( PluginOptions $options, string $title, array $tabs ) {
		$this->title   = $title;
		$this->options = $options;

		$this->load_tabs( $tabs );
	}

	/**
	 * Magic getter
	 *
	 * @param $key
	 *
	 * @return mixed
	 *
	 * @codeCoverageIgnore
	 */
	public function __get( $key ) {
		return $this->$key ?? null;
	}

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @param PluginOptions $options
	 *
	 * @return OptionsPage
	 */
	public static function init( PluginOptions $options ) {
		$title = _x( 'Buma Options', 'BumaOptions', 'Buma' );

		$tabs = [
			'General',
		];

		$obj        = new self( $options, $title, $tabs );
		$capability = 'administrator';

		add_options_page( $obj->title, $obj->title, $capability, __CLASS__, [ $obj, 'render' ] );
		add_action( 'admin_init', [ $obj, 'register' ] );

		return $obj;
	}

	/**
	 * Registers callback
	 *
	 * @codeCoverageIgnore
	 */
	public function register() {
		$key = $this->options->get_name();

		/**
		 * @var Tab $tab
		 */
		$tab = $this->get_active_tab();

		register_setting( $key, $key, [ $this, 'validate' ] );

		$tab->init_section();
	}

	/**
	 * Validates the input
	 *
	 * @param array $arr
	 *
	 * @return array
	 *
	 * @codeCoverageIgnore
	 */
	function validate( $arr ) {

		flush_rewrite_rules();

		return $arr;
	}

	/**
	 * Load tabs
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	protected function load_tabs( $classes ) {
		if ( is_null( $this->tabs ) ) {
			$this->tabs = [];

			foreach ( $classes as $class ) {
				/**
				 * @var Tab $class
				 */
				$class = Tab::class . '\\' . $class;

				if ( class_exists( $class ) ) {
					$tab                          = $class::init( $this->options, __CLASS__ );
					$this->tabs[ $tab->get_id() ] = $tab;
				}
			}
		}

		return $this->tabs;
	}

	public function render() {
		$active_tab = $this->get_selected_tab();

		echo '<div class="wrap">', "\n", '<div class="icon32" id="icon-options-general"><br/>', "</div>\n";
		printf( "<h2>%s</h2>\n", $this->title );
		echo '<form action="options.php?tab=' . $active_tab . '" method="post">', "\n";

		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->get_tabs() as $tab ) {
			/**
			 * @var Tab $tab
			 */
			printf(
				'<a href="%s" class="nav-tab%s">%s</a>',
				esc_url( add_query_arg( [ 'tab' => $tab->get_id() ] ) ),
				( $tab->get_id() == $active_tab ? ' nav-tab-active' : '' ),
				$tab->get_name()
			);
		}
		echo "</h2>\n";

		settings_fields( $this->options->get_name() );
		do_settings_sections( __CLASS__ );

		submit_button();

		echo "</form>\n</div>\n";
	}

	/**
	 * Gets the active tab
	 *
	 * @return string
	 */
	public function get_selected_tab() {
		$tab = current( $this->get_tabs() );

		$input = new FilterInput( INPUT_GET, 'tab' );
		if ( $input->has_var() ) {
			$tab_name = $input->get();

			if ( $this->tab_exists( $tab_name ) ) {
				return $tab_name;
			}
		}

		return $tab->get_id();
	}

	/**
	 * Get tabs
	 *
	 * @return array
	 */
	public function get_tabs() {
		return ( ! is_null( $this->tabs ) ) ? $this->tabs : [];
	}

	/**
	 * Gets the active tab
	 *
	 * @return Tab/bool
	 *
	 * @codeCoverageIgnore
	 */
	public function get_active_tab() {
		return $this->tabs[ $this->get_selected_tab() ] ?? false;
	}

	/**
	 * Check tab value in input (GET) exists amongst tabs the current user has access to
	 *
	 * @param $tab_name
	 *
	 * @return bool
	 */
	public function tab_exists( $tab_name ) {
		if ( empty( $tab_name ) ) {
			return false;
		}
		$tabs = $this->get_tabs();
		foreach ( $tabs as $tab ) {
			if ( $tab_name === $tab->get_id() ) {
				return true;
			}
		}

		return false;
	}
}