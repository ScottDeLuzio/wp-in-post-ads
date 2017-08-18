<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}
add_action( 'init', 'update_inpost_ads_pro_options', 1 );
function update_inpost_ads_pro_options(){
	$a = get_option('oizuled-adsense-unit-a');
	$b = get_option('oizuled-adsense-unit-b');
	$c = get_option('oizuled-adsense-unit-c');

	$notoptions = wp_cache_get( 'notoptions', 'options' );

	if ( isset( $notoptions[ 'oizuled-adsense-unit-a' ] ) && isset( $notoptions[ 'oizuled-adsense-unit-b' ] ) && isset( $notoptions[ 'oizuled-adsense-unit-c' ] ) ){
		return;
	} else {
		$updated = array(
			'unit_a' => $a,
			'unit_b' => $b,
			'unit_c' => $c
		);

		update_option( 'aip_settings', $updated );

		delete_option( 'oizuled-adsense-unit-a' );
		delete_option( 'oizuled-adsense-unit-b' );
		delete_option( 'oizuled-adsense-unit-c' );
	}	
}

add_action( 'init', 'update_ad_units', 2 );
function update_ad_units(){
	$upgraded = get_option( 'aip_upgraded' );
	if( $upgraded ){
		return;
	} else {
		$aip_options = get_option( 'aip_settings' );
		// Get ad units if entered in free version
		$update_a = '';
		$update_b = '';
		$update_c = '';
		if ( '' != $aip_options[ 'unit_a' ] ){
			$update_a = $aip_options[ 'unit_a' ];
		}
		if ( '' != $aip_options[ 'unit_b' ] ){
			$update_b = $aip_options[ 'unit_b' ];
		}
		if ( '' != $aip_options[ 'unit_c' ] ){
			$update_c = $aip_options[ 'unit_c' ];
		}
		$inpost_units = array(
			$update_a,
			$update_b,
			$update_c
		);
		update_option( 'inpost_ad_units', $inpost_units );
		update_option( 'aip_upgraded', 'true' );
		delete_option( 'aip_settings' );
	}
}