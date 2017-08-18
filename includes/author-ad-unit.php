<?php
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}
add_action( 'show_user_profile', 'wp_inpost_ads_author_ad' );
add_action( 'edit_user_profile', 'wp_inpost_ads_author_ad' );

function wp_inpost_ads_author_ad( $user ) { ?>

	<h3><?php _e( 'Author Ad Unit', 'wp-inpost-ads' ); ?></h3>

	<table class="form-table">

		<tr>
			<th width="5%"><label for="wp_inpost_ad"><?php _e( 'Ad Code', 'wp-inpost-ads' ); ?></label></th>

			<td width="20%">
				<script>
					jQuery(document).ready(function(){
						var authorAdInitial = document.getElementById('author-ad-unit').value
						jQuery("#show-author-ad-unit").html( authorAdInitial );
						jQuery("#author-ad-unit").change(function(){
							var authorAdUnit = document.getElementById('author-ad-unit').value
							jQuery("#show-author-ad-unit").html( authorAdUnit );
						})
					})
				</script>
				<textarea rows="5" cols="36" id="author-ad-unit" name="wp_inpost_ad"><?php echo esc_html( get_the_author_meta( 'wp_inpost_ad', $user->ID ) ); ?></textarea><br />
				<span class="description"><?php _e( 'Enter the ad code provided to you from your ad network.', 'wp-inpost-ads' ); ?></span>
			</td>
			<td width="75%">
				<span id="show-author-ad-unit"></span>
			</td>
		</tr>

	</table>
<?php }

add_action( 'personal_options_update', 'wp_inpost_ads_author_ad_save' );
add_action( 'edit_user_profile_update', 'wp_inpost_ads_author_ad_save' );

function wp_inpost_ads_author_ad_save( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'wp_inpost_ad' to the field ID. */
	update_user_meta( $user_id, 'wp_inpost_ad', $_POST['wp_inpost_ad'] );
}