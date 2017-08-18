<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
	die( "Sorry, you are not allowed to access this page directly." );
}

//Need to add display as an allowed style for AdSense ads to be displayed correctly.
add_filter( 'safe_style_css', function( $styles ) {
	$styles[] = 'display';
	return $styles;
} );

function wpipa_filtered_allowed_html( $ad ){
	//Only allow certain HTML 
	$allowed_html = array(
		'a'			 => array(
			'href'						 => array(),
			'title'						 => array(),
			'class'						 => array(),
			'id'						 => array(),
			'target'					 => array(),
			'style'						 => array(),
		),
		'img'		 => array(
			'src'						 => array(),
			'alt'						 => array(),
			'title'						 => array(),
			'class'						 => array(),
			'id'						 => array(),
			'style'						 => array(),
			'width'						 => array(),
			'height'					 => array(),
		),
		'script'	 => array(
			'src'						 => array(),
			'async'						 => array(),
			'adsbygoogle'				 => array(),
			'push'						 => array(),
			'google_ad_client'			 => array(),
			'enable_page_level_ads'		 => array(),
		),
		'ins'		 => array(
			'class'						 => array(),
			'style'						 => array(),
			'data-ad-client'			 => array(),
			'data-ad-slot'				 => array(),
			'data-ad-format'			 => array(),
			'data-adsbygoogle-status'	 => array(),
		),
		'br'		 => array(),
		'em'		 => array(),
		'strong'	 => array(),
	);
	if ( has_filter( 'wpipa_filter_allowed_html' ) ){
		$allowed_html = apply_filters( 'wpipa_filter_allowed_html', $allowed_html );
	}

	return wp_kses( $ad, $allowed_html );
}