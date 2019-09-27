<?php

namespace KToolbox;

/**
 * Class MultiPartFormData
 * @package KToolbox
 *
 * This class helps you build a multipart form data-like request body
 */
class MultiPartFormData {

	const BOUNDARY_LENGTH = 24;
	const CONTENT_DISPOSITION = 'Content-Disposition: form-data;';
	const CONTENT_TYPE = 'Content-Type';
	const BREAK = "\r\n";

	/**
	 * @var string
	 */
	private $boundary;

	/**
	 * MultiPartFormData constructor.
	 */
	public function __construct() {
		$this->boundary = wp_generate_password( self::BOUNDARY_LENGTH );
	}

	/**
	 * @return string
	 */
	public function create_content_type(): string {
		return 'multipart/form-data; boundary=' . $this->get_boundary();
	}

	/**
	 * @return array
	 */
	public function create_header(): array {
		return [
			'Content-Type' => $this->create_content_type()
		];
	}

	/**
	 * @return string
	 */
	public function get_boundary() {
		return $this->boundary;
	}

	/**
	 * @param array $fields
	 * @param array $files
	 *
	 * @return string
	 */
	public function create_body( array $fields = [], array $files = [] ): string {
		$payload = $this->create_body_from_fields( $fields );
		$payload .= $this->create_body_from_files( $files );

		return $payload . '--' . $this->get_boundary() . '--';
	}

	/**
	 * This is for testing purposes
	 *
	 * @codeCoverageIgnore
	 *
	 * @param $filename
	 *
	 * @return bool|string
	 */
	public function get_file_contents( $filename ) {
		return file_get_contents( $filename );
	}

	/**
	 * @param array $fields
	 *
	 * @return string
	 */
	private function create_body_from_fields( array $fields = [] ): string {
		$payload = '';

		foreach ( $fields as $name => $value ) {
			$payload .= '--' . $this->get_boundary();
			$payload .= self::BREAK;
			$payload .= self::CONTENT_DISPOSITION . ' name="' . $name . '"' . self::BREAK . self::BREAK;
			$payload .= $value;
			$payload .= self::BREAK;
		}

		return $payload;
	}

	/**
	 * @param array $files
	 *
	 * @return string
	 */
	private function create_body_from_files( array $files = [] ): string {
		$payload = '';

		foreach ( $files as $name => $file ) {
			if ( ! empty( $file ) ) {
				$payload .= '--' . $this->get_boundary();
				$payload .= self::BREAK;
				$payload .= self::CONTENT_DISPOSITION . ' name="' . $name . '"; filename="' . $file['name'] . '"' . self::BREAK;
				$payload .= 'Content-Type: ' . $file['type'] . self::BREAK;
				$payload .= self::BREAK;
				$payload .= $this->get_file_contents( $file['tmp_name'] );
				$payload .= self::BREAK;
			}
		}

		return $payload;
	}
}