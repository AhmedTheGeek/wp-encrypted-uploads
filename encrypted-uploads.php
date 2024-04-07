<?php
/*
Plugin Name: WP Encrypted Uploads
Plugin URI: https://wordpress.org/plugins/wp-encrypted-uploads/
Description: Keep your uploaded files safe by encrypting them and setting specific permission for every role.
Author: Anciented
Version: 1.0
Author URI: https://anciented.io
*/

use ANCENC\Helpers\Activation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANCENC_VER', '1.0.0' );

define( 'ANCENC_PATH', __DIR__ );

define( 'ANCENC_URL', plugin_dir_url( __FILE__ ) );

define( 'ANCENC_DIR_PREFIX', 'wp_ancenc' );

if ( ! defined( 'ANCENC_BUNDLED_SCRIPT_PATH' ) ) {
	define( 'ANCENC_BUNDLED_SCRIPT_PATH', ANCENC_URL . 'public/js' );
	define( 'ANCENC_BUNDLED_STYLE_PATH', ANCENC_URL . 'public/css' );
}

require_once ANCENC_PATH . '/vendor/autoload.php';
/* Register Autoloader */
require_once ANCENC_PATH . '/psr4-autoload.php';
/* Bootstrap The Plugin */
require_once ANCENC_PATH . '/server/bootstrap.php';

$activation_helper = new Activation();
register_activation_hook( __FILE__, array( $activation_helper, 'activation_hooks' ) );