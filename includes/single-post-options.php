<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}
add_action( 'admin_menu', 'adsense_inpost_meta_box' );
add_action( 'admin_init', 'adsense_inpost_meta_box' );
function adsense_inpost_meta_box() {
	global $wpip_options;
	$post_types = adsense_inpost_ads_pro_get_cpts();
	$exclude_post_types = explode("\n", $wpip_options[ 'exclude_post_types' ] );
	foreach( $post_types as $post_type ) {
		if ( !in_array( $post_type, $exclude_post_types ) ){
			add_meta_box( 'wp-inpost-single-settings', __( 'Ad Settings', 'wp-inpost-ads' ), 'wp_inpost_ads_single_post_settings', $post_type, 'side', 'low' );
			add_action( 'save_post_' . $post_type, 'save_wp_inpost_ads_single_setting', 1, 2 );
		}
	}
}

function wp_inpost_ads_single_post_settings(){ 
	global $post, $inpost_ad_units;
	$paragraph1 = get_post_meta( $post->ID, 'wpip_individual_settings_override_paragraph1', true );
	$paragraph2 = get_post_meta( $post->ID, 'wpip_individual_settings_override_paragraph2', true );
	$paragraph3 = get_post_meta( $post->ID, 'wpip_individual_settings_override_paragraph3', true );
	$ad = get_post_meta( $post->ID, 'wpip_individual_settings_override_ad_unit', true );
	
	?>
	<div style="width:100%;">
		<label for="wpip_individual_settings_override_paragraph">
			<strong><?php _e('Enter first ad after which paragraph?','wp-inpost-ads'); ?></strong>
		</label><br />
		<input type="number" min="1" step="1" name="wpip_individual_settings_override_paragraph1" value="<?php echo $paragraph1; ?>" /><br />
		<label for="wpip_individual_settings_override_paragraph">
			<strong><?php _e('Enter second ad after which paragraph?','wp-inpost-ads'); ?></strong>
		</label><br />
		<input type="number" min="1" step="1" name="wpip_individual_settings_override_paragraph2" value="<?php echo $paragraph2; ?>" /><br />
		<label for="wpip_individual_settings_override_paragraph">
			<strong><?php _e('Enter third ad after which paragraph?','wp-inpost-ads'); ?></strong>
		</label><br />
		<input type="number" min="1" step="1" name="wpip_individual_settings_override_paragraph3" value="<?php echo $paragraph3; ?>" /><br />
		<label for="wpip_individual_settings_override_ad_unit">
			<strong><?php _e('Override Ad Unit','wp-inpost-ads'); ?></strong>
		</label><br />
		<select name="wpip_individual_settings_override_ad_unit">
			<option value="unit_auto_insert" <?php selected( 'unit_auto_insert', $ad ); ?> ><?php _e( 'Default Ad', 'wp-inpost-ads' ); ?></option>
			<option value="post_author" <?php selected( 'post_author', $ad ); ?> ><?php _e( 'Post Author Ad', 'wp-inpost-ads' ); ?></option>
			<option value="no_ad" <?php selected( 'no_ad', $ad ); ?> ><?php _e( 'No Ad', 'wp-inpost-ads' ); ?></option>
			<?php
			foreach( $inpost_ad_units as $key => $value ){ ?>
				<option value="<?php echo $key; ?>" <?php selected( $key, $ad ); ?> ><?php _e( 'Ad Unit ', 'wp-inpost-ads' ); echo $key + 1; ?></option>
				<?php
			}?>
		</select>
		<p>
			<strong><?php _e( 'Ad Preview. Actual ad may vary.', 'wp-inpost-ads' ); ?></strong>
		</p>
		<?php echo wpipa_preview_ad_unit_single_post(); ?>
	</div>
	<?php
}

function save_wp_inpost_ads_single_setting( $post_id ) {
	$fields = array( 'wpip_individual_settings_override_ad_unit', 'wpip_individual_settings_override_paragraph1', 'wpip_individual_settings_override_paragraph2', 'wpip_individual_settings_override_paragraph3' );
	foreach ($fields as $field) {
		if ( array_key_exists( $field, $_POST ) ) {
			$value = $_POST[$field];
			if ( in_array( $field, array( 'wpip_individual_settings_override_paragraph1', 'wpip_individual_settings_override_paragraph2', 'wpip_individual_settings_override_paragraph3' ) ) ) {
				$safeinput = preg_replace("/[^0-9\.]/", "", $value);
			}
			if ( $field == 'wpip_individual_settings_override_ad_unit' ) {
				$safeinput = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, $field, $safeinput );
		}
	}
}

// Display all custom post types
function adsense_inpost_ads_pro_get_cpts() {
	$args = array(
		'public'	=> true,
	);

	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'

	$post_types = get_post_types( $args, $output, $operator ); 

	return $post_types;
}

function wpipa_preview_ad_unit_single_post(){
	global $wpip_options, $inpost_ad_units;
	$post_id = get_the_ID();
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
	return $ad;
}
