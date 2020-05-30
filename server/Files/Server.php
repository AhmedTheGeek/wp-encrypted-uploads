<?php

namespace ANCENC\Files;

use ANCENC\Admin\Settings;

class Server {
	private $file_manager;
	private $settings_manager;

	public function __construct( Manager $file_manager, Settings $settings_manager ) {
		$this->file_manager     = $file_manager;
		$this->settings_manager = $settings_manager;
	}

	public function is_octet_stream( $mime ) {
		return strpos( $mime, 'pdf' ) !== false || strpos( $mime, 'zip' );
	}

	public function handle_file_serving( $file ) {
		$file      = sanitize_text_field( $file );
		$file_path = str_replace( $this->file_manager->get_upload_dir(), '', $file );

		if ( $this->file_manager->file_exists( $file_path ) ) {
			$this->open_file( $this->file_manager->get_upload_path() . $file_path );
		}
	}

	public function will_force_download() {
		$force_download = $this->settings_manager->get_general_setting_option( 'force_download' );

		return $force_download === 'force_download';
	}

	public function can_download() {
		if ( is_user_logged_in() ) {
			$roles = wp_get_current_user()->roles;
			$roles = array_map( function ( $item ) {
				return ucfirst( $item );
			}, $roles );

			$enabled_roles = $this->settings_manager->get_general_setting_option( 'enabled_roles' );

			if ( ! is_array( $enabled_roles ) ) {
				return false;
			}

			$intersect = array_intersect( $roles, $enabled_roles );
			if ( ! empty( $intersect ) ) {
				return true;
			}
		}

		return false;
	}

	public function open_file( $file_path ) {
		if ( $this->can_download() ) {
			$crypto = new Crypto();

			try {
				$decrypted_file = $crypto->decrypt( $file_path );
			} catch ( \Exception $exception ) {
				http_response_code( 404 );
				exit();
			}

			$decrypted_file_path = stream_get_meta_data( $decrypted_file )['uri'];
			$mime                = mime_content_type( $decrypted_file_path );

			rewind( $decrypted_file );

			$filesize = filesize( $decrypted_file_path );

			header( 'Content-Description: File Transfer' );
			header( 'Connection: Keep-Alive' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Content-Length: ' . $filesize );

			if ( $this->is_octet_stream( $mime ) ) {
				header( 'Content-Type: application/octet-stream' );
			} else {
				header( 'Content-Type: image/png' );
			}

			if ( $this->will_force_download() ) {
				header( "Content-Disposition: attachment; filename= " . basename( $file_path ) );
			}

			$output_stream = fopen( 'php://output', 'wb' );

			stream_copy_to_stream( $decrypted_file, $output_stream );

			fclose( $decrypted_file );
			fclose( $output_stream );
			exit;
		} else {
			http_response_code( 403 );
			exit();
		}
	}

}