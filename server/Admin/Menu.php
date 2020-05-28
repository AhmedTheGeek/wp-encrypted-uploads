<?php

namespace ANCENC\Admin;

use ANCENC\UI\Renderer;

class Menu {

	public function register_menus() {
		add_menu_page(
			__( 'Encrypted Uploads', 'ancenc' ),
			__( 'Encrypted Uploads', 'ancenc' ),
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