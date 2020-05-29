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
			$this->open_file( $this->file_manager->get_upload_path() . DIRECTORY_SEPARATOR . $file_path );
		}
	}

	public function open_file( $file_path ) {
		if ( is_user_logged_in() ) {
			$file = fopen( $file_path, 'r' );
			header( 'Content-Type: ' . mime_content_type( $file_path ) );
			header( 'Content-Length: ' . filesize( $file_path ) );

			fpassthru( $file );
			fclose($file);
			exit();
		} else {
			header( 'Content-Type: text/plain' );
			http_response_code( 403 );
			echo 'not authorized';
			exit();
		}
	}

}