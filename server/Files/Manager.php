<?php

namespace ANCENC\Files;

use ANCENC\Helpers\String;

class Manager {

	private $upload_path;
	private $upload_dir;

	public function __construct() {
		$this->upload_dir  = get_option( 'ancenc_custom_directory', 'wp_ancenc' );
		$this->upload_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->upload_dir;

		add_action( 'ancenc_get_upload_dir', array( &$this, 'get_upload_dir' ) );
		add_action( 'ancenc_get_upload_path', array( &$this, 'get_upload_path' ) );
		add_action( 'ancenc_can_handle_type', array( &$this, 'can_handle_type' ) );
	}

	public function can_handle_type($type) {
		return false;
	}

	public function get_upload_dir( $dir = '' ) {
		return $this->upload_dir;
	}

	public function get_upload_path( $path = '' ) {
		return $this->upload_path;
	}

	public function file_exists( $filename ) {
		return file_exists( $this->upload_path . DIRECTORY_SEPARATOR . $filename );
	}

	public function register_handlers() {
		add_filter( 'wp_get_attachment_url', array( &$this, 'modify_attachment_url' ) );
		add_filter( 'wp_handle_upload', array( &$this, 'handle_uploaded_file' ) );
	}

	public function get_file_name( $path ) {
		$exploded_path = explode( DIRECTORY_SEPARATOR, $path );
		if ( count( $exploded_path ) > 0 ) {
			return end( $exploded_path );
		}

		return null;
	}

	public function handle_uploaded_file( $file ) {
		$file['file'] = $this->move_uploaded_file( $file['file'] );

		return $file;
	}

	public function modify_attachment_url( $url, $id = null ) {
		$start_position = strpos( $url, ANCENC_DIR_PREFIX );
		$path           = substr( $url, $start_position );

		return content_url( $path );
	}

	private function move_uploaded_file( $path ) {
		$filename      = $this->get_file_name( $path );
		$exploded_name = explode( '.', $filename );
		$ext           = end( $exploded_name );
		$dated_path    = $this->get_dated_path();
		$new_filename  = $filename;

		if ( ! file_exists( $dated_path ) ) {
			mkdir( $dated_path, 0755, true );
			touch( $dated_path . DIRECTORY_SEPARATOR . 'index.php' );
		}

		if ( file_exists( $dated_path . DIRECTORY_SEPARATOR . $new_filename ) ) {
			$new_filename = String::random( 16 ) . '.' . $ext;
		}

		$new_path = $dated_path . DIRECTORY_SEPARATOR . $new_filename;

		rename( $path, $new_path );

		return $new_path;
	}

	private function get_dated_path() {
		return $this->upload_path . DIRECTORY_SEPARATOR . date( 'Y' ) . DIRECTORY_SEPARATOR . date( 'm' );
	}

}