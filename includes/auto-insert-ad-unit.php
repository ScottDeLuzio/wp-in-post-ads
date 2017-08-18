<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
	die( "Sorry, you are not allowed to access this page directly." );
}
add_filter( 'the_content', 'adsense_auto_insert_ad' );
function adsense_auto_insert_ad( $content ) {
	global $wpip_options, $inpost_ad_units;
	
	// Check if content has any existing ad units from free plugin AdSense-A AdSense-B or AdSense-C shortcodes
	$is_shortcode_a = has_shortcode( $content, 'AdSense-A' );
	$is_shortcode_b = has_shortcode( $content, 'AdSense-B' );
	$is_shortcode_c = has_shortcode( $content, 'AdSense-C' );

	if ( true == $is_shortcode_a || true == $is_shortcode_b || true == $is_shortcode_c ){
		// We have an ad unit, bail and return content as-is.
		return $content;
	}

	// Make sure we're not on the admin side and only on single posts (any post type).
	if ( is_singular() && ! is_admin() ) {
		// Check if we need to exclude auto-inserting ads in the post if we're on an excluded post type.
		$exclude_post_types = explode("\n", $wpip_options[ 'exclude_post_types' ] );
		if ( is_singular( $exclude_post_types ) ) {
			return $content;
		}
		// Otherwise run the auto-insert function.
		$post_id = get_the_ID();
		$overridePar1 = get_post_meta( $post_id, 'wpip_individual_settings_override_paragraph1', true );
		if ( $overridePar1 ){
			$paragraph_id1 = $overridePar1;
		} else {
			$paragraph_id1 = $wpip_options[ 'paragraph' ];
		}
		$overridePar2 = get_post_meta( $post_id, 'wpip_individual_settings_override_paragraph2', true );
		if ( $overridePar2 ){
			$paragraph_id2 = $overridePar2;
		} else {
			$paragraph_id2 = $wpip_options[ 'paragraph2' ];
		}
		$overridePar3 = get_post_meta( $post_id, 'wpip_individual_settings_override_paragraph3', true );
		if ( $overridePar3 ){
			$paragraph_id3 = $overridePar3;
		} else {
			$paragraph_id3 = $wpip_options[ 'paragraph3' ];
		}
		$paragraph_id = array(
			$paragraph_id1,
			$paragraph_id2,
			$paragraph_id3
		);

		$overrideAd = get_post_meta( $post_id, 'wpip_individual_settings_override_ad_unit', true );
		if ( '' != $overrideAd ){
			switch ( $overrideAd ) {
				case 'unit_auto_insert':
					$ad = $wpip_options[ 'unit_auto_insert' ];
					break;
				case 'post_author':
					$ad = get_the_author_meta( 'wp_inpost_ad' );
					break;
				case 'no_ad':
					$ad = '';
					break;
				default:
					$ad = $inpost_ad_units[ $overrideAd ];
					break;
			}
		} else {
			$ad = $wpip_options[ 'unit_auto_insert' ];
		}
		$position = 'after';
		if( has_filter( 'wp_inpost_ads_before_after_filter' ) ){
			$position = apply_filters( 'wp_inpost_ads_before_after_filter', $position );
			$position = in_array( $position, array( 'before', 'after' ) ) ? $position : 'after';
		}
		return wp_inpost_ads_display_auto( $ad, $paragraph_id, $content, $position );
	}
	// Fallback just return the content.
	return $content;
}

function wp_inpost_ads_display_auto( $ad, $paragraph_id, $content, $position ){
	$tag = 'after' == $position ? '</p>' : '<p>';
	if( has_filter( 'wp_inpost_ads_tag' ) ){
		$tag = apply_filters( 'wp_inpost_ads_tag', $tag );
	}

	$paragraphs = explode( $tag, $content );
	
	$del_val = '';

	if( ( $key = array_search( $del_val, $paragraph_id ) ) !== false ) {
		unset( $paragraph_id[$key]);
	}

	//var_dump($paragraph_id);
	//var_dump($paragraphs);

	foreach ( $paragraphs as $index => $paragraph ) {
		switch ( $position ) {
			case 'before':
				/**
				 * Check if the closing tag exists in the string.
				 * If it does, we know there was an opening tag stripped in the
				 * $paragraphs = explode( $tag, $content ) above.
				 * We now need to add that opening tag back and put and check 
				 * if we need to add the ad before this section.
				 */
				
				//create the closing tag based on the opening tag we were passed from above.
				$closetag = substr_replace( $tag, '/', 1, 0 );
				
				//check if the closing tag exists in $paragraph
				if( strpos( $paragraph, $closetag ) ){
					//check if this section needs an ad unit
					if( in_array( $index, $paragraph_id ) ){
						$paragraphs[$index] = wpipa_filtered_allowed_html( $ad ) . $tag . $paragraph;
					} else {
						//if not, just add the opening tag back
						$paragraphs[$index] = $tag . $paragraph;
					}
				}
				break;
			
			default:
				if ( trim( $paragraph ) ) {
					$paragraphs[$index] .= $tag;
				}
				if ( in_array( $index + 1, $paragraph_id ) ) {
					$paragraphs[$index] .= wpipa_filtered_allowed_html( $ad );
				}
				break;
		}
	}
	
	return implode( '', $paragraphs );
}