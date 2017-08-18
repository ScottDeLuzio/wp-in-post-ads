<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/* Start Updater */
if (!defined('WP_INPOST_ADS_STORE_URL')){
	define( 'WP_INPOST_ADS_STORE_URL', 'https://wpinpostads.com' );
}
// the name of your product. This should match the download name in EDD exactly
define( 'WP_INPOST_ADS_NAME', 'WP In-Post Ads' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'WPIPA_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function wp_inpost_ads_license_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'wp_inpost_ads_license_key' ) );

	// setup the updater
	$edd_updater = new WPIPA_SL_Plugin_Updater( WP_INPOST_ADS_STORE_URL, __FILE__, array(
			'version' 	=> '1.0.6', 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => WP_INPOST_ADS_NAME, 	// name of this plugin
			'author' 	=> 'Scott DeLuzio'  // author of this plugin
		)
	);

}
add_action( 'admin_init', 'wp_inpost_ads_license_updater', 0 );

function wp_inpost_ads_register_option() {
	// creates our settings in the options table
	register_setting('wp_inpost_ads_license_group', 'wp_inpost_ads_license_key', 'wp_inpost_ads_sanitize_license' );
}
add_action('admin_init', 'wp_inpost_ads_register_option');

function wp_inpost_ads_sanitize_license( $new ) {
	$old = get_option( 'wp_inpost_ads_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'wp_inpost_ads_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}
function wp_inpost_ads_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['wp_inpost_ads_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'wp_inpost_ads_license_nonce', 'wp_inpost_ads_license_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'wp_inpost_ads_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( WP_INPOST_ADS_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( WP_INPOST_ADS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'wp_inpost_ads_license_status', $license_data->license );
		update_option( 'wp_inpost_ads_license_expires', $license_data->expires );

	}
}
add_action('admin_init', 'wp_inpost_ads_activate_license');

function wp_inpost_ads_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['wp_inpost_ads_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'wp_inpost_ads_license_nonce', 'wp_inpost_ads_license_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'wp_inpost_ads_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( WP_INPOST_ADS_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( WP_INPOST_ADS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'wp_inpost_ads_license_status' );

	}
}
add_action('admin_init', 'wp_inpost_ads_deactivate_license');
/* End Updater */

// Add license key settings field
function wp_inpost_ads_license_field() {
	$wp_inpost_ads_key = get_option( 'wp_inpost_ads_license_key' );
	$wp_inpost_ads_status = get_option( 'wp_inpost_ads_license_status' );
	$date_format = get_option( 'date_format' );
	$wp_inpost_ads_expires = strtotime( get_option( 'wp_inpost_ads_license_expires' ) );
	$formatted_date = date( $date_format, $wp_inpost_ads_expires );
	$hide_save = false;
	?>
	<div class="wrap">
		<h3><?php _e( 'License Key', 'wp-inpost-ads' ); ?></h3>
		<form method="post" action="options.php">
		<?php settings_fields('wp_inpost_ads_license_group'); ?>
			<table>
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'License Key','wp-inpost-ads' ); ?><span class="wpipa-help-tip dashicons dashicons-editor-help" title="<?php _e( 'You received this in your purchase confirmation email. You can also retrieve it from your account https://wpinpostads.com/your-account', 'wp-inpost-ads'); ?>"></span>
						</th>
						<td>
							<input id="wp_inpost_ads_license_key" name="wp_inpost_ads_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $wp_inpost_ads_key ); ?>" />
							<label class="description" for="wp_inpost_ads_license_key"><?php _e('Enter your license key','wp-inpost-ads'); ?></label>
						</td>
					</tr>
					<?php if( false !== $wp_inpost_ads_key ) { ?>
					<tr valign="top">
						<th scope="row" valign="top">
						</th>
						<td>
							<?php if( $wp_inpost_ads_status !== false && $wp_inpost_ads_status == 'valid' ) { 
								$hide_save = 'true'; ?>
								<?php wp_nonce_field( 'wp_inpost_ads_license_nonce', 'wp_inpost_ads_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="wp_inpost_ads_deactivate" value="<?php _e('Deactivate License','wp-inpost-ads'); ?>"/><br />
								<span style="color:green;font-weight:700;"><?php _e( 'Your license is ', 'wp-inpost-ads' ); echo $wp_inpost_ads_status . '.'; ?>
								<em><?php _e( 'Expires on ', 'wp-inpost-ads' ); echo $formatted_date; ?></em></span>
							<?php } else {
								wp_nonce_field( 'wp_inpost_ads_license_nonce', 'wp_inpost_ads_license_nonce' );
								if( $wp_inpost_ads_status != ''){ ?>
									<span style="color:red;"><?php _e( 'Your license is ', 'wp-inpost-ads' ); echo $wp_inpost_ads_status; ?></span>
								<?php } ?>
								<input type="submit" class="button-secondary" name="wp_inpost_ads_activate" value="<?php _e('Activate License','wp-inpost-ads'); ?>"/>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php 
		if (!$hide_save) { 
			submit_button('Save License'); 
		}
		?>
		</form>
	</div>
	<?php
}
add_action( 'wp_inpost_ads_license', 'wp_inpost_ads_license_field' );