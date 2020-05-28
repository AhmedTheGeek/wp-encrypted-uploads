<?php

namespace ANCENC\Admin;

use ANCENC\PublicDependencies\Style;
use ANCENC\UI\Renderer;

class Menu {
	private $public_css_deps;

	public function __construct( Style $public_css_deps ) {
		$this->public_css_deps = $public_css_deps;
	}

	public function register_menus() {
		add_menu_page(
			__( 'WP Encrypted', 'ancenc' ),
			__( 'WP Encrypted', 'ancenc' ),
			'manage_options',
			'ancenc',
			array( &$this, 'render_menu' ),
			'',
			6
		);
	}

	public function render_menu() {
		$this->public_css_deps->load_admin_dependencies();

		$renderer         = new Renderer();
		$settings_manager = new Settings();
		$settings_manager->autoload_options();

		$settings         = $settings_manager->object;

		$renderer->render( 'menu', array(
			'name'     => 'ahmed',
			'available_file_types' => array_values(wp_get_mime_types())
		) );
	}

}