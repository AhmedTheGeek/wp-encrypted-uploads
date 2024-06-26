<?php

namespace ANCENC\Helpers;

class Str {
	public static function random( $n ) {
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ( $i = 0; $i < $n; $i ++ ) {
			$index        = rand( 0, strlen( $characters ) - 1 );
			$randomString .= $characters[ $index ];
		}

		return $randomString;
	}
}