<?php

namespace ANCENC\Admin;

use ANCENC\PublicDependencies\Javascript;
use ANCENC\PublicDependencies\Style;
use ANCENC\UI\Renderer;

class Menu {
	private $public_css_deps;
	private $public_js_deps;

	public function __construct( Style $public_css_deps, Javascript $public_js_deps ) {
		$this->public_css_deps = $public_css_deps;
		$this->public_js_deps = $public_js_deps;

		add_action('admin_enqueue_scripts', [&$this, 'enqueue_admin_scripts']);
	}

	public function register_menus() {
		add_menu_page(
			__( 'WP Encrypted', 'ancenc' ),
			__( 'WP Encrypted', 'ancenc' ),
			'manage_options',
			'ancenc',
			array( &$this, 'render_menu' ),
			'dashicons-lock',
			6
		);
	}

	public function enqueue_admin_scripts() {
		$this->public_js_deps->load_admin_dependencies();
	}

	public function render_menu() {

		$this->public_css_deps->load_admin_dependencies();


		$renderer         = new Renderer();
		$settings_manager = new Settings();
		$settings_manager->autoload_options();

		$settings = $settings_manager->object;

		$renderer->render( 'menu', array(
			'available_settings' => $settings_manager->available_settings(),
			'update_nonce' => $settings_manager->settings_page_nonce()
		));
	}

}