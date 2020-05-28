<?php

namespace ANCENC\PublicDependencies;

class Javascript {
	public function register_assets() {
		wp_register_script( 'AS11_shortcode', AS11_URL . 'public/js/shortcode.js', [ 'jquery' ], AS11_VER );
		wp_register_script( 'AS11_admin', AS11_URL . 'public/js/admin.js', [ 'jquery' ], AS11_VER );

		wp_localize_script( 'AS11_admin', 'AS11',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'AS11_shortcode', 'AS11',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	public function load_admin_dependencies() {
		wp_enqueue_script( 'AS11_admin' );
	}

	public function load_frontend_dependencies() {
		wp_enqueue_script( 'AS11_shortcode' );
	}
}
