<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
	die( "Sorry, you are not allowed to access this page directly." );
}
/* Set shortcodes so we don't have to go back and find all the old shortcodes. */
function wp_inpost_ads_a() {
	global $inpost_ad_units;
	if(!isset($adsense_a)) {
		$adsense_a = do_shortcode( $inpost_ad_units[0] );
	}
	return $adsense_a;
}
add_shortcode('AdSense-A', 'wp_inpost_ads_a');

function wp_inpost_ads_b() {
	global $inpost_ad_units;
	if(!isset($adsense_b)) {
		$adsense_b = do_shortcode( $inpost_ad_units[1] );
	}
	return $adsense_b;
}
add_shortcode('AdSense-B', 'wp_inpost_ads_b');

function wp_inpost_ads_c() {
	global $inpost_ad_units;
	if(!isset($adsense_c)) {
		$adsense_c = do_shortcode( $inpost_ad_units[2] );
	}
	return $adsense_c;
}
add_shortcode('AdSense-C', 'wp_inpost_ads_c');