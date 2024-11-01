<div id="userplace_payasugo_id"></div>

<?php
use Userplace\Provider;
/**
 * Localize the updated data from database
 */
$plan_builder = get_post_meta( $post->ID, '_userplace_payasugo', true );
$provider = new Provider();
$payasugo_fields = $provider->payasugo_provider_array();
wp_localize_script( 'userplace_payasugo', 'USERPLACE_ADMIN',
	apply_filters('userplace_admin_generator_localize_args', array(
		'PLAN_BUILDER' => $plan_builder,
		'fields'       => $payasugo_fields
	)
) );
?>
<input type="hidden" id="_userplace_payasugo" name="_userplace_payasugo" value="<?php echo esc_attr(isset($plan_builder) ? $plan_builder : '{}') ?>">
