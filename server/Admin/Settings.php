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

	public $object = [];

	public function set_option( $name, $value, $autoload = false ) {
		update_option( $this->option_prefix . $name, $value, $autoload );
	}

	public function autoload_options() {
		foreach ( $this->autoload_options as $option ) {
			$this->object[ $option['name'] ] = get_option( $this->option_prefix . $option['name'], $option['default'] );
		}
	}
}
