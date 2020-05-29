<?php

namespace ANCENC\Admin;

class Settings {

	private $option_prefix = 'ancenc_';
	private $autoload_options = [
		'settings' => [
			'name'    => 'enabled_types',
			'default' => []
		]
	];

	private $allowed_sections = [
		'general'
	];

	public $object = [];

	public function register_filters() {
		add_filter( 'ancenc_settings_checked_for_section', array( &$this, 'setting_checked' ) );
	}

	public function load_section_settings( $section ) {
		return $this->get_option( 'settings_' . $section );
	}

	public function setting_checked( $query ) {
		$settings = $this->load_section_settings( $query['section'] );
		if ( $settings !== false ) {
			if ( isset( $query['name'] ) && is_array( $settings[ $query['name'] ] ) ) {
				return in_array( $query['value'], $settings[ $query['name'] ]);
			}
		}

		return false;
	}

	public function register_ajax_actions() {
		add_action( 'wp_ajax_ancenc_update_settings', array( &$this, 'update_settings_ajax' ) );
	}

	public function update_settings_ajax() {
		parse_str( $_POST['data'], $output );

		if ( isset( $output['settings_section'] ) && wp_verify_nonce( $_POST['nonce'], 'ancenc_update_settings' ) ) {
			$update = $this->update_settings_object( $output );
			if ( $update ) {
				wp_send_json_success( [
					'message' => 'Settings updated'
				] );
				wp_die();
			}
		}

		wp_send_json_error( [
			'message' => 'Invalid update request'
		] );
		wp_die();
	}

	public function update_settings_object( $new ) {
		if ( in_array( $new['settings_section'], $this->allowed_sections ) ) {
			$update_object = array_map( 'esc_sql', $new );
			$section       = $update_object['settings_section'];
			unset( $update_object['settings_section'] );
			$this->set_option( 'settings_' . $section, $update_object, true );

			return true;
		}

		return false;
	}

	public function set_option( $name, $value, $autoload = false ) {
		update_option( $this->option_prefix . $name, $value, $autoload );
	}

	public function available_settings() {
		return [
			[
				'title'       => 'Enabled File Types',
				'description' => "Encryption is only enabled for the checked file types, it's recommended that you don't enable encryption for public images, as this may significantly affect the website performance.",
				'options'     => [
					[
						'type'       => 'checkbox',
						'name'       => 'enabled_types',
						'single'     => false,
						'disabled'   => false,
						'value'      => null,
						'label'      => null,
						'check_type' => true,
						'children'   => [
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'images',
								'label'    => 'Images (any file with an image mime type (jpg, png, gif, ...)',
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'images'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'zip',
								'label'    => 'Zip files',
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'zip'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'pdf',
								'label'    => 'PDF files',
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'pdf'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'audio',
								'label'    => 'Audio Files (mp3, aac, wav, ogg)',
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'audio'
								] )
							]
						]
					]
				]
			],
			[
				'title'       => 'Upload Path',
				'description' => "The custom path where WP Encrypted Uploads stores the encrypted files.",
				'options'     => [
					[
						'type'       => 'text',
						'name'       => 'upload_path',
						'single'     => true,
						'disabled'   => true,
						'value'      => apply_filters( 'ancenc_get_upload_path', '' ),
						'label'      => null,
						'check_type' => false,
						'size'       => 100
					]
				]
			]
		];
	}

	public function get_option( $name, $default = false ) {
		return get_option( $this->option_prefix . $name, $default );
	}

	public function autoload_options() {
		foreach ( $this->autoload_options as $option ) {
			$this->object[ $option['name'] ] = get_option( $this->option_prefix . $option['name'], $option['default'] );
		}
	}

	public function settings_page_nonce() {
		return wp_create_nonce( 'ancenc_update_settings' );
	}
}
