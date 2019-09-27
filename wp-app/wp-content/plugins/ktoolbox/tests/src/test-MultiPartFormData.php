<?php

namespace KToolboxTests;

/**
 * Class MultiPartFormData
 */
class MultiPartFormData extends \KToolboxTestCase {
	public function setUp() {
		parent::setUp();

		\WP_Mock::userFunction( 'wp_generate_password', [
			'args'   => \KToolbox\MultiPartFormData::BOUNDARY_LENGTH,
			'return' => '123',
		] );
	}

	public function test_get_boundary() {
		$test = new \KToolbox\MultiPartFormData();

		$this->assertEquals( $test->get_boundary(), wp_generate_password(
			\KToolbox\MultiPartFormData::BOUNDARY_LENGTH
		) );
	}

	public function test_create_header() {
		$test   = new \KToolbox\MultiPartFormData();
		$header = $test->create_header();

		$this->assertInternalType( 'array', $header );
		$this->assertEquals(
			$header['Content-Type'], 'multipart/form-data; boundary=' . $test->get_boundary()
		);
	}

	public function test_create_body_with_the_specified_fields() {
		$test   = new \KToolbox\MultiPartFormData();
		$fields = [
			'foo' => 'bar',
			'baz' => 'buzz'
		];

		$body = $test->create_body( $fields );

		$this->assertInternalType( 'string', $body );
		$this->assertStringStartsWith( '--' . $test->get_boundary(), $body );

		foreach ( $fields as $field_name => $field ) {
			$this->assertRegExp( '/name="' . $field_name . '"/', $body );
			$this->assertRegExp( "/$field/", $body );
		}
	}

	public function test_create_body_with_the_specified_file() {
		$files = [
			'image' => [
				'name'     => 'rsz_bob.jpg',
				'type'     => 'image/jpeg',
				'tmp_name' => '/tmp/phpOALeYv',
				'error'    => 0,
				'size'     => 5464,
			]
		];

		$test = \Mockery::mock( '\KToolbox\MultiPartFormData[get_file_contents]' )->makePartial();
		$test->shouldReceive( 'get_file_contents' )
		     ->withArgs( [ $files['image']['tmp_name'] ] )
		     ->times( count( $files ) )
		     ->andReturn( 'this.is.an.image' );

		$body = $test->create_body( [], $files );

		foreach ( $files as $name => $file ) {
			$this->assertRegExp( '/name="' . $name . '"/', $body );
			$this->assertRegExp( '/filename="' . $file['name'] . '"/', $body );
		}
	}
}