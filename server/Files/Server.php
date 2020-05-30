<?php

namespace ANCENC\Files;

class Server {
	private $file_manager;

	public function __construct( Manager $file_manager ) {
		$this->file_manager = $file_manager;
	}

	public function handle_file_serving( $file ) {
		$file      = sanitize_text_field( $file );
		$file_path = str_replace( $this->file_manager->get_upload_dir(), '', $file );

		if ( $this->file_manager->file_exists( $file_path ) ) {
			$this->open_file( $this->file_manager->get_upload_path()  . $file_path );
		}
	}

	public function open_file( $file_path ) {
		if ( is_user_logged_in() ) {
			$file           = fopen( $file_path, 'r' );
			$crypto         = new Crypto();
			$encrypted_data = fread( $file, filesize( $file_path ) );
			$decrypted_data = $crypto->decrypt( $encrypted_data );
			$tmp_file       = tmpfile();
			fwrite( $tmp_file, $decrypted_data );
			$path = stream_get_meta_data( $tmp_file )['uri'];
			$mime = mime_content_type( $path );
			header( 'Content-Type: ' . mime_content_type( $path ) );
			header( 'Content-Length: ' . filesize( $path ) );
			fpassthru( $tmp_file );
			fclose( $file );
			fclose( $tmp_file );
			exit();
		} else {
			header( 'Content-Type: text/plain' );
			http_response_code( 403 );
			echo 'not authorized';
			exit();
		}
	}

}