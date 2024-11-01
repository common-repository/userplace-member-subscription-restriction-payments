<div id="userplace_gateway_plan_id"></div>

<?php
/**
 * Localize the updated data from database
 */
$plan_builder = get_post_meta( $post->ID, '_userplace_plan_builder', true );
wp_localize_script( 'userplace_gateway_plan', 'USERPLACE_ADMIN',
	apply_filters('userplace_admin_generator_localize_args', array(
		'PLAN_BUILDER' => $plan_builder)
) );
?>
<input type="hidden" id="_userplace_plan_builder" name="_userplace_plan_builder" value="<?php echo esc_attr(isset($plan_builder) ? $plan_builder : '{}') ?>">
