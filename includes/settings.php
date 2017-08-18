<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
	die( "Sorry, you are not allowed to access this page directly." );
}
/* Settings Page */

// Remove free version's ad blocks as they were already converted to pro version. Any edits will not be reflected in new ads.
remove_action( 'aip_settings', 'adsense_ad_blocks' );

// Hook for adding admin menus
add_action( 'admin_menu', 'wp_inpost_ads_add_pages' );

// action function for above hook
function wp_inpost_ads_add_pages() {
	// Add a new submenu under Settings:
	add_options_page('In-Post Options','WP In-Post Ads', 'manage_options', 'wp-inpost-ads', 'wp_inpost_ads_settings_page');
}

// Scripts for settings page
add_action( 'admin_enqueue_scripts', 'wpipa_load_admin_scripts' );
function wpipa_load_admin_scripts(){
	wp_register_script( 'wpipa-admin-scripts', WP_INPOST_ADS_PLUGIN_URL . '/assets/js/admin-scripts.js', array( 'jquery', 'jquery-ui-tooltip' ) );
	wp_enqueue_script( 'wpipa-admin-scripts' );
	wp_register_style( 'wpipa-admin', WP_INPOST_ADS_PLUGIN_URL . '/assets/css/wpipa-admin.css' );
	wp_enqueue_style( 'wpipa-admin' );
}

// Display the page content for the ads submenu
function wp_inpost_ads_settings_page() {
	global $wpip_options; ?>
	<div class="wrap">
		<h2><?php _e( 'In-Post Ads Options', 'wp-inpost-ads' ); ?></h2>
		<?php do_action( 'wp_inpost_ads_license' ); ?>
		<form method='post' action='options.php'>
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'wp_inpost_ads_settings_group' ); ?>
			<table class="form-table">
				<tbody>
					<?php do_action( 'wp_inpost_ads_settings' ); ?>
					<tr valign="top">
						<td colspan="2"><input type="hidden" name="action" value="update" /><?php submit_button(); ?></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div> 
	<?php
}
 
add_action( 'wp_inpost_ads_settings', 'wp_inpost_settings', 1 );
function wp_inpost_settings(){
	global $wpip_options;
	?>
	<tr valign="top">
		<th colspan="3">
			<h3><?php _e( 'Default Settings', 'wp-inpost-ads' ); ?></h3>
		</th>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e( 'Enter first ad after which paragraph?', 'wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title="<?php _e( 'Enter the paragraph number for the ad to be placed after. This setting can be overridden on individual posts.', 'wp-inpost-ads' ); ?>"></span>
		</th>
		<td>
			<input type="number" min="1" step="1" name="wpip_settings[paragraph]" value="<?php echo $wpip_options[ 'paragraph' ]; ?>">
		</td>
		<td>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e( 'Enter second ad after which paragraph?', 'wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title="<?php _e( 'Enter the paragraph number for the ad to be placed after. This setting can be overridden on individual posts.', 'wp-inpost-ads' ); ?>"></span>
		</th>
		<td>
			<input type="number" min="1" step="1" name="wpip_settings[paragraph2]" value="<?php echo $wpip_options[ 'paragraph2' ]; ?>">
		</td>
		<td>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e( 'Enter third ad after which paragraph?', 'wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title="<?php _e( 'Enter the paragraph number for the ad to be placed after. This setting can be overridden on individual posts.', 'wp-inpost-ads' ); ?>"></span>
		</th>
		<td>
			<input type="number" min="1" step="1" name="wpip_settings[paragraph3]" value="<?php echo $wpip_options[ 'paragraph3' ]; ?>">
		</td>
		<td>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e( 'Default ad unit', 'wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title="<?php _e( 'This ad unit is inserted automatically after the paragraph(s) set above. This setting can be overridden on individual posts.', 'wp-inpost-ads' ); ?>"></span>
		</th>
		<td>
			<script>
				jQuery(document).ready(function(){
					var defaultAdInitial = document.getElementById('default-ad-unit').value
					jQuery("#show-default-ad-unit").html( defaultAdInitial );
					jQuery("#default-ad-unit").change(function(){
						var defaultAdUnit = document.getElementById('default-ad-unit').value
						jQuery("#show-default-ad-unit").html( defaultAdUnit );
					})
				})
			</script>
			<textarea rows="5" cols="36" id="default-ad-unit" name="wpip_settings[unit_auto_insert]"><?php echo wpipa_filtered_allowed_html( $wpip_options[ 'unit_auto_insert' ] ); ?></textarea>
		</td>
		<td width="75%" style="">
			<span id="show-default-ad-unit"></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e( 'Exclude certain post types?', 'wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title="<?php _e( 'Enter post type slug that should not have an ad inserted automatically (i.e. products or books). One per line.', 'wp-inpost-ads' ); ?>"></span>
		</th>
		<td>
			<textarea rows="5" cols="36" name="wpip_settings[exclude_post_types]"><?php echo $wpip_options[ 'exclude_post_types' ]; ?></textarea>
		</td>
		<td>
		</td>
	</tr> 
	<?php
}

add_action( 'wp_inpost_ads_settings', 'wp_inpost_ad_blocks', 2 );
function wp_inpost_ad_blocks(){ 
	global $inpost_ad_units; ?>
	<tr valign="top">
		<th colspan="3">
			<h3><?php _e( 'Additional Ad Units', 'wp-inpost-ads' ); ?></h3>
		</th>
	</tr>
	<tr valign="top">
		<th colspan="3">
			<?php _e( 'You can create additional ad units if you want to use different ads on select posts.', 'wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title='<?php _e( 'Override the default ad unit set above with a new ad unit set below. Optionally use any ad unit below with a shortcode anywhere your site accepts shortcodes using [inpostads unit="X"] replace X with the number after Ad Unit.', 'wp-inpost-ads' ); ?>'></span>
		</th>
	</tr>
	<?php
	if ( $inpost_ad_units ){
		foreach( $inpost_ad_units as $key => $ad ){ ?>
			<script>
				jQuery(document).ready(function(){
					var adUnitInitial = document.getElementById('ad-unit-<?php echo $key + 1; ?>').value
					jQuery("#show-ad-unit-<?php echo $key + 1; ?>").html( adUnitInitial );
					jQuery("#ad-unit-<?php echo $key + 1; ?>").change(function(){
						var adUnit = document.getElementById('ad-unit-<?php echo $key + 1; ?>').value
						jQuery("#show-ad-unit-<?php echo $key + 1; ?>").html( adUnit );
					})
				})
			</script>
			<tr valign="top">
				<th scope="row"><?php _e( 'Ad Unit ', 'wp-inpost-ads' ); echo $key + 1; ?></th>
				<td>
					<textarea rows="5" cols="36" id="ad-unit-<?php echo $key + 1; ?>" name="inpost_ad_units[]"><?php echo wpipa_filtered_allowed_html( $ad ); ?></textarea>
				</td>
				<td width="75%">
					<span id="show-ad-unit-<?php echo $key + 1; ?>"></span>
				</td>
			</tr>
			<?php
		}
	} ?>
	<tr valign="top">
		<th scope="row"><?php _e( 'Enter the new ad code provided to you from your ad network.', 'wp-inpost-ads' ); ?></th>
		<td>
			<textarea rows="5" cols="36" name="inpost_ad_units[]"></textarea>
		</td>
		<td>
		</td>
	</tr>
	<?php
}

function wp_inpost_ads_register_settings() {
	// creates our settings in the options table
	register_setting( 'wp_inpost_ads_settings_group', 'wpip_settings' );
	register_setting( 'wp_inpost_ads_settings_group', 'inpost_ad_units', 'wp_inpost_ads_validation' );
}
add_action( 'admin_init', 'wp_inpost_ads_register_settings' );

function wp_inpost_ads_validation( $input ) {
	foreach ( $input as $key => $value ){
		if ( '' == $value ){
			unset ( $input[ $key ] );
		}
	}
	return $input;
}