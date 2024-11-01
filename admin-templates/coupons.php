<div id="userplace_coupon_id"></div>

<?php
use Userplace\Provider;
/**
 * Localize the updated data from database
 */
$coupon_settings = get_post_meta( $post->ID, '_userplace_coupon', true );
$provider = new Provider();
$coupon_fields = $provider->coupon_provider_array();
wp_localize_script( 'userplace_coupon', 'USERPLACE_ADMIN',
	apply_filters('userplace_admin_generator_localize_args', array(
		'COUPON_SETTINGS' => $coupon_settings,
		'fields'       => $coupon_fields
	)
) );
?>
<input type="hidden" id="_userplace_coupon" name="_userplace_coupon" value="<?php echo esc_attr(isset($coupon_settings) ? $coupon_settings : '{}') ?>">
