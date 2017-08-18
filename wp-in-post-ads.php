<?php
   /*
   Plugin Name: WP In-Post Ads
   Plugin URI: https://wpinpostads.com/
   Description: A plugin to automatically insert ads in your posts.
   Version: 1.0.6
   Author: Scott DeLuzio
   Author URI: https://scottdeluzio.com
   Text Domain: wp-inpost-ads
   */
   
	/* Copyright 2017 Scott DeLuzio */
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
/*
 * Includes for WP In-Post Ads
 */
if ( ! defined( 'WP_INPOST_ADS' ) ) {
  define( 'WP_INPOST_ADS', __FILE__ );
}
if( ! defined( 'WP_INPOST_ADS_PLUGIN_DIR' ) ) {
  define( 'WP_INPOST_ADS_PLUGIN_DIR', dirname( __FILE__ ) );
}
if( ! defined( 'WP_INPOST_ADS_PLUGIN_URL' ) ) {
  define( 'WP_INPOST_ADS_PLUGIN_URL', plugins_url( '', __FILE__ ) );
}
if( ! defined( 'WP_INPOST_ADS_PLUGIN_BASENAME' ) ) {
  define( 'WP_INPOST_ADS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

$aip_options      = get_option( 'aip_settings' );
$wpip_options     = get_option( 'wpip_settings' );
$inpost_ad_units  = get_option( 'inpost_ad_units' );

/* Load Text Domain */
add_action('plugins_loaded', 'adsense_inpost_ads_pro_plugin_init');
function adsense_inpost_ads_pro_plugin_init() {
  load_plugin_textdomain( 'wp-inpost-ads', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/upgrader.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/allowed-html.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/settings.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/license.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/shortcode.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/author-ad-unit.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/legacy-shortcodes.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/auto-insert-ad-unit.php' );
include( WP_INPOST_ADS_PLUGIN_DIR . '/includes/single-post-options.php' );