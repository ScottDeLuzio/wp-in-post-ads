<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}
add_shortcode( 'inpostads', 'wp_in_post_ads_shortcode' );
function wp_in_post_ads_shortcode( $atts ){
	global $inpost_ad_units;
	$atts = shortcode_atts(
		array(
			'unit'	=> ''
		),
		$atts,
		'inpostads'
	);
	$unit = $atts[ 'unit' ] - 1; //0 index array need to subtract 1
	$ad = $inpost_ad_units[ $unit ];
	return $ad;
}

// Allow shortocdes to be used in widgets
add_filter( 'widget_text', 'do_shortcode' );