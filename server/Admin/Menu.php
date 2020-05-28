<?php

namespace ANCENC\Admin;

use ANCENC\UI\Renderer;

class Menu {

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
		$renderer = new Renderer();
		$renderer->render('menu', array(
			'name' => 'ahmed'
		));
	}

}