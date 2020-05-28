<?php

namespace ANCENC\Helpers;

class Activation {

	public function activation_hooks() {
		$this->create_custom_upload_directory();
	}

	public function create_custom_upload_directory() {
		$created = get_option( 'ancenc_custom_directory_created', false );

		if ( $created === false ) {
			$custom_name = ANCENC_DIR_PREFIX . String::random( 12 );
			if ( defined( 'WP_CONTENT_DIR' ) ) {
				$folder_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $custom_name;
			} else {
				$folder_path = ANCENC_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $custom_name;
			}
			$index_path = $folder_path . DIRECTORY_SEPARATOR . 'index.php';

			$mkdir      = mkdir( $folder_path, 0755 );
			$touch_file = touch( $index_path );

			if ( $mkdir ) {
				update_option( 'ancenc_custom_directory_created', true );
				update_option( 'ancenc_custom_directory', $custom_name, true );
			} else {
				Logger::error( __( "We weren't able to create the custom uploads directory, make sure wp-content has the correct permissions.", 'ancenc' ) );
			}

			if ( ! $touch_file ) {
				Logger::error( __( "We weren't able to create the index.php file in the custom uploads directory, make sure wp-content has the correct permissions.", 'ancenc' ) );
			}
		}
	}
}