<?php

namespace ANCENC\Files;

use ANCENC\Admin\Settings;
use ANCENC\Helpers\Str;

class Manager {

	private $upload_path;
	private $upload_dir;
	private $settings_manager;

	public function __construct( Settings $settings ) {
		$this->settings_manager = $settings;

		$this->upload_dir  = get_option( 'ancenc_custom_directory', 'wp_ancenc' );
		$this->upload_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->upload_dir;

		add_filter( 'ancenc_get_upload_dir', array( &$this, 'get_upload_dir' ) );
		add_filter( 'ancenc_get_upload_path', array( &$this, 'get_upload_path' ) );
		add_filter( 'ancenc_can_handle_type', array( &$this, 'can_handle_type' ) );
	}

	public function can_handle_type( $file ) {
		$file_path = $file['file'];
		$settings  = $this->settings_manager->get_option( 'settings_general' );
		$filename  = $this->get_file_name( $file_path );
		$check     = wp_check_filetype_and_ext( $file_path, $filename );

		if ( $check['type'] !== false ) {
			$mime = explode( '/', $check['type'] );
			if ( count( $mime ) > 0 && in_array( $mime[0], $settings['enabled_types'] ) ) {
				return true;
			}
		}

		if ( $check['ext'] !== false ) {
			return in_array( $check['ext'], $settings['enabled_types'] );
		}

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
		return basename( $path );
	}

	public function handle_uploaded_file( $file ) {

		if ( $this->can_handle_type( $file ) ) {
			$file['file'] = $this->move_uploaded_file( $file['file'] );
			//encrypt file
			$this->rewrite_encrypted_file( $file['file'] );
		}

		return $file;
	}

	public function modify_attachment_url( $url, $id = null ) {
		$start_position = strpos( $url, ANCENC_DIR_PREFIX );
		$path           = substr( $url, $start_position );
		if ( $start_position !== false ) {
			return content_url( $path );
		}

		return $url;
	}

	private function get_file_ext( $filename ) {
		$exploded_name = explode( '.', $filename );

		return end( $exploded_name );
	}

	private function move_uploaded_file( $path ) {
		$filename     = $this->get_file_name( $path );
		$ext          = $this->get_file_ext( $filename );
		$dated_path   = $this->get_dated_path();
		$new_filename = $filename;

		if ( ! file_exists( $dated_path ) ) {
			mkdir( $dated_path, 0755, true );
			touch( $dated_path . DIRECTORY_SEPARATOR . 'index.php' );
		}

		if ( file_exists( $dated_path . DIRECTORY_SEPARATOR . $new_filename ) ) {
			$new_filename = Str::random( 16 ) . '.' . $ext;
		}

		$new_path = $dated_path . DIRECTORY_SEPARATOR . $new_filename;

		rename( $path, $new_path );

		return $new_path;
	}

	public function rewrite_encrypted_file( $path ) {
		$crypto         = new Crypto();
		$file = $crypto->encrypt( $path );
		$tmp_path = stream_get_meta_data( $file )['uri'];
		unlink($path);
		copy($tmp_path, $path);
		fclose($file);
		return true;
	}

	private function get_dated_path() {
		return $this->upload_path . DIRECTORY_SEPARATOR . date( 'Y' ) . DIRECTORY_SEPARATOR . date( 'm' );
	}

}