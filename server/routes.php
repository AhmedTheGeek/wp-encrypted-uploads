<?php

namespace ANCENC;

use ANCENC\Admin\Menu;
use ANCENC\Files\Manager;

add_action( 'admin_menu', function () {
	$menu = new Menu();
	$menu->register_menus();
} );

add_action( 'init', function () {
	$file_manager = new Manager();
	$file_manager->register_handlers();

	if ( isset( $_GET['ancenc_action'] ) && $_GET['ancenc_action'] == 'ancenc_get_file' ) {
		if ( isset( $_GET['ancenc_file'] ) ) {
			$dic    = DicLoader::get_instance()->get_dic();
			$server = $dic->make( 'ANCENC\Files\Server' );
			$server->handle_file_serving( $_GET['ancenc_file'] );
		}
	}
} );