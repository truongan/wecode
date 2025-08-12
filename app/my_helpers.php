<?php
namespace App;
class my_helpers{

	public static function ip_in_range( $ip, $range ) {
		if ( strpos( $range, '/' ) == false ) {
			$range .= '/32';
		}
		// $range is in IP/CIDR format eg 127.0.0.1/24
		list( $range, $netmask ) = explode( '/', $range, 2 );
		$range_decimal = ip2long( $range );
		$ip_decimal = ip2long( $ip );
		$wildcard_decimal = pow( 2,  32 - $netmask  ) - 1;
		$netmask_decimal = ~ $wildcard_decimal;
		// echo ("{$ip_decimal} & {$netmask_decimal}) == ( {$range_decimal} & {$netmask_decimal} )\n");
		// echo ($ip_decimal & $netmask_decimal);
		// echo ("\n");
		// echo (( $range_decimal & $netmask_decimal ));
		return   ($ip_decimal & $netmask_decimal) == ( $range_decimal & $netmask_decimal )  ;
	}

}
