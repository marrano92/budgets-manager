<?php

namespace KToolboxTests;

/**
 * Class MetaBox
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MetaBox extends \KToolboxTestCase {

	public function get_config() {
		$config = [
			'key' => [
				'id'       => 'box',
				'title'    => 'title',
				'page'     => 1,
				'context'  => 'some',
				'priority' => 'high',
				'fields'   => [
					'field' => [ 'id' => 17 ],
					'lawn'  => [ 'id' => 21 ]
				],
			]
		];

		return $config;
	}

	public function get_test( $config = null ) {
		\WP_Mock::passthruFunction( 'add_meta_box' );

		$config = $config ?? $this->get_config();
		$test   = new \KToolbox\MetaBox();

		$reflection          = new \ReflectionClass( $test );
		$reflection_property = $reflection->getProperty( '_config' );
		$reflection_property->setAccessible( true );
		$reflection_property->setValue( $test, $config );

		return $test;
	}

	public function test_add_config() {
		$test   = $this->get_test( [] );
		$config = $this->get_config();

		$test->add_config( $config );

		$this->assertAttributeEquals( $config, '_config', $test );

		$this->expectException( \InvalidArgumentException::class );
		$test->add_config( $config ); //rejects duplicates
	}

	public function test_has_config() {
		$test = $this->get_test();

		$this->assertTrue( $test->has_config( 'key' ) );
		$this->assertFalse( $test->has_config( 'missing' ) );
	}

	//Tests for show_box()
	//The regex will just check that the desired element it's echoed
	public function get_show_box_test_object( $config = [] ) {
		\WP_Mock::userFunction( 'wp_create_nonce', [
			'return' => 'class',
		] );
		\WP_Mock::passthruFunction( 'get_post_meta' );

		global $post;
		$post = $this->get_object( [ 'post_type' => 'custom', 'ID' => 17 ] );

		return $this->get_test( $config );
	}

	public function test_show_box_no_config() {
		$test = $this->get_show_box_test_object();

		$this->assertNull( $test->show_box() );
	}

	public function test_show_box_text() {
		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'text_field', 'id' => 1, 'type' => 'text', 'std' => 'std', 'desc' => 'desc', 'maxlength' => '35' ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<input type="text" name="1" id="1" value="17" size="30" style="width:97%" maxlength="35">/' );
		$test->show_box();
	}

	public function test_show_box_text_disabled_readonly() {
		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'text_field', 'id' => 1, 'type' => 'text', 'std' => 'std', 'desc' => 'desc', 'disabled' => true, 'readonly' => true ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<input type="text" name="1" id="1" value="17" size="30" style="width:97%" maxlength="" disabled readonly>/' );
		$test->show_box();
	}

	public function test_show_box_textarea() {
		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'textarea_field', 'id' => 1, 'type' => 'textarea', 'std' => 'std', 'desc' => 'desc' ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<textarea name="1" id="1" cols="30" rows="2" style="width:98%">/' );
		$test->show_box();
	}

	public function test_show_box_select() {
		\WP_Mock::userFunction( 'selected', [ 'return' => 'selected="selected"' ] );

		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'select_field', 'desc' => 'desc', 'id' => 1, 'type' => 'select', 'options' => [ '17' ] ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<select name="1" id="1">/' );
		$this->expectOutputRegex( '/<option selected="selected">17/' );
		$test->show_box();
	}

	public function test_show_box_select_multiple() {
		\WP_Mock::userFunction( 'selected', [ 'return' => 'selected="selected"' ] );
		\WP_Mock::userFunction( 'get_post_meta', [ 'return' => serialize( 'serialized' ) ] );

		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'select_field', 'multiple' => true, 'desc' => 'desc', 'id' => 1, 'type' => 'select', 'options' => [ '17', '18' ] ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<select name="1" id="1">/' );
		$this->expectOutputRegex( '/<option selected="selected">17/' );
		$this->expectOutputRegex( '/<option selected="selected">18/' );
		$test->show_box();
	}

	public function test_show_box_radio() {
		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'radio_field', 'id' => 1, 'type' => 'radio', 'options' => [ [ 'value' => 17, 'name' => 'testing' ] ] ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<input type="radio" name="1" value="17" checked="checked" /' );
		$test->show_box();
	}

	public function test_show_box_checkbox() {
		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'checkbox_field', 'id' => 1, 'type' => 'checkbox', 'desc' => 'desc' ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<input type="checkbox" name="1" id="1" checked="checked" /' );
		$test->show_box();
	}

	public function test_show_box_wp_editor() {
		\WP_Mock::passthruFunction( 'wp_editor' );

		$config = [ 'custom' => [ 'fields' => [ [ 'name' => 'wp_editor_field', 'id' => 1, 'type' => 'wp_editor', 'std' => 'std', 'desc' => 'desc' ] ] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputRegex( '/<span style="display:block; padding:0.5em 0 0;">desc/' );
		$test->show_box();
	}

	public function test_show_box_no_fields() {
		$config = [ 'custom' => [ 'fields' => [] ] ];

		$test = $this->get_show_box_test_object( $config );

		$this->expectOutputString( '<input type="hidden" name="cont_theme_meta_box_nonce" value="class" /><table class="form-table"></table>' );
		$test->show_box();
	}

	//Tests for save_data()
	public function test_save_data() {
		\Mockery::mock( 'overload:\KToolbox\FilterInput' )
		        ->shouldReceive( [
			        'has_var' => false,
			        'get'     => null,
		        ] );

		$test = $this->get_test();

		$this->assertFalse( $test->save_data( 7 ) );
	}

	public function test_save_data_verifies() {
		\Mockery::mock( 'overload:\KToolbox\FilterInput' )
		        ->shouldReceive( [
			        'has_var' => false,
			        'get'     => null,
		        ] );

		$test = $this->get_test();

		$this->assertFalse( $test->save_data( 7 ) );
	}

	public function test_save_data_type_page_user_cannot() {
		\WP_Mock::passthruFunction( 'wp_verify_nonce', true );
		\WP_Mock::passthruFunction( 'add_meta_box', [ 'times' => 1 ] );
		\WP_Mock::userFunction( 'current_user_can', [ 'return' => false, 'times' => 1 ] );

		\Mockery::mock( 'overload:\KToolbox\FilterInput' )
		        ->shouldReceive( [
			        'has_var' => true,
			        'get'     => 'page',
		        ] );

		$default        = $this->get_config();
		$config['page'] = $default['key'];

		$test = new \KToolbox\MetaBox();
		$test->add_config( $config );

		$this->assertFalse( $test->save_data( 7 ) );
	}

	public function test_save_data_user_cannot() {
		\WP_Mock::passthruFunction( 'wp_verify_nonce', true );
		\WP_Mock::passthruFunction( 'add_meta_box', [ 'times' => 1 ] );
		\WP_Mock::userFunction( 'current_user_can', [ 'return' => false, 'times' => 1 ] );

		\Mockery::mock( 'overload:\KToolbox\FilterInput' )
		        ->shouldReceive( [
			        'has_var' => true,
			        'get'     => 'post',
		        ] );

		$default        = $this->get_config();
		$config['post'] = $default['key'];

		$test = new \KToolbox\MetaBox();
		$test->add_config( $config );

		$this->assertFalse( $test->save_data( 7 ) );
	}

	public function test_save_data_user_can() {
		\WP_Mock::passthruFunction( 'wp_verify_nonce', true );
		\WP_Mock::passthruFunction( 'current_user_can', true );
		\WP_Mock::userFunction( 'get_post_meta', [
			'return_in_order' => [ '', '1' ],
		] );
		\WP_Mock::passthruFunction( 'update_post_meta' );
		\WP_Mock::passthruFunction( 'delete_post_meta' );

		\Mockery::mock( 'overload:\KToolbox\FilterInput' )
		        ->shouldReceive( [
			        'has_var' => true,
			        'get'     => 'page',
		        ] );

		$test = $this->get_test();

		$this->assertTrue( $test->save_data( 7 ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_save_data_defined_autosave() {
		define( 'DOING_AUTOSAVE', true );
		\Mockery::mock( 'overload:\KToolbox\FilterInput' )->shouldReceive( [ 'has_var' => true, 'get' => 'page', ] );
		\WP_Mock::passthruFunction( 'wp_verify_nonce', true );

		$test   = $this->get_test();
		$result = $test->save_data( 1 );
		$this->assertFalse( $result );
	}


	public function test_save_data_user_can_populated_config() {
		\WP_Mock::passthruFunction( 'wp_verify_nonce', true );
		\WP_Mock::passthruFunction( 'current_user_can', true );
		\WP_Mock::userFunction( 'get_post_meta', [ 'return_in_order' => [ '1', [], [ 'a' ] ], ] );
		//\WP_Mock::userFunction( 'apply_filters', [ 'return_in_order' => [ '', [], [ 'b' ] ], ] );
		\WP_Mock::onFilter( 'dkwp/metabox_save_field_value' )->with( '', '1', 1 )->reply( '' );
		\WP_Mock::onFilter( 'dkwp/metabox_save_field_value' )->with( '', [], 2 )->reply( [] );
		\WP_Mock::onFilter( 'dkwp/metabox_save_field_value' )->with( '', [ 'a' ], 3 )->reply( [ 'b' ] );
		\WP_Mock::passthruFunction( 'update_post_meta' );
		\WP_Mock::passthruFunction( 'delete_post_meta' );
		\Mockery::mock( 'overload:\KToolbox\FilterInput' )->shouldReceive( [ 'has_var' => true, 'get' => 'page', ] );

		$config = [
			'page' => [
				'fields' => [
					[ 'id' => 1, 'disabled' => true ],
					[
						'id'       => 2,
						'disabled' => false,
						'validate' => function () {
							return false;
						}
					],
					[ 'id' => 3, 'disabled' => false ],
				]
			]
		];
		$test   = $this->get_test( $config );

		$this->assertTrue( $test->save_data( 7 ) );
	}

	public function test_condition_true() {
		$config = [
			[
				'name'      => _x( 'Vehicle Image', 'Landing Page CPT admin', 'dkwp' ),
				'id'        => '_landing-editorial-vehicleimg',
				'type'      => 'text',
				'std'       => '',
				'title'     => _x( 'Vehicle Image', 'Landing Page CPT admin', 'dkwp' ),
				'desc'      => _x( 'Insert the CDN url of the vehicle image you want to show', 'Landing Page CPT admin', 'dkwp' ),
				'condition' => [ $this, '_return_true' ]
			],
		];

		foreach ( $config as $field_to_test ) {
			$test_config = [ 'custom' => [ 'fields' => [ $field_to_test ] ] ];
			$test        = $this->get_show_box_test_object( $test_config );
			$this->expectOutputRegex( '/<input type="text" name="' . $field_to_test['id'] . '" id="' . $field_to_test['id'] . '"/' );
			$test->show_box();
		}
	}

	public function test_condition_false() {
		$config = [
			[
				'name'      => _x( 'Vehicle Image', 'Landing Page CPT admin', 'dkwp' ),
				'id'        => '_landing-editorial-vehicleimg',
				'type'      => 'text',
				'std'       => '',
				'title'     => _x( 'Vehicle Image', 'Landing Page CPT admin', 'dkwp' ),
				'desc'      => _x( 'Insert the CDN url of the vehicle image you want to show', 'Landing Page CPT admin', 'dkwp' ),
				'condition' => [ $this, '_return_false' ]
			],
		];

		foreach ( $config as $field_to_test ) {
			$test_config = [ 'custom' => [ 'fields' => [ $field_to_test ] ] ];
			$test        = $this->get_show_box_test_object( $test_config );
			$this->expectOutputRegex( '/(?!<input type="text" name="' . $field_to_test['id'] . '" id="' . $field_to_test['id'] . '")/' );
			$test->show_box();
		}
	}

	public function test_get_fields() {
		\WP_Mock::userFunction( 'get_post_meta', [ 'return' => [ 'meta1' => [ 'value1', 'value2' ] ], 'times' => 1 ] );
		$expected = [ 'meta1' => implode( PHP_EOL, [ 'value1', 'value2' ] ) ];

		$test = new \KToolbox\MetaBox();

		$this->assertEquals( $expected, $test->get_fields( 1 ) );
	}
}