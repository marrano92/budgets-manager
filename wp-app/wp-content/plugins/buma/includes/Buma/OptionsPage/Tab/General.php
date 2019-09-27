<?php

namespace Buma\OptionsPage\Tab;

use Buma\OptionsPage\Tab;

class General extends Tab {

	protected $id = 'main_section';

	/**
	 * Gets name for tab
	 *
	 * @return string
	 */
	public function get_name(): string {
		return _x( 'General Settings', 'OptionsPage', 'dkwp' );
	}

	/**
	 * Extend it to add fields
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function section() {
		printf( '<p>%s</p>', _x( 'Please check twice what you set here!', 'OptionsPage', 'dkwp' ) );

		add_settings_field(
			'slide_home',
			_x( 'Visibility slide in home', 'OptionsPage', 'Buma' ),
			[ $this, 'slide_home' ],
			$this->get_page(),
			$this->get_id()
		);
	}

	public function slide_home(){
		echo $this->render_checkbox( 'slide_home' );
	}
}