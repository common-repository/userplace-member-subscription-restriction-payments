<div id="userplace_plan_restrictions_for_post"></div>

<?php
use Userplace\Provider;
/**
 * Localize the updated data from database
 */
	$provider = new Provider();
	$restriction_fields = $provider->restrictions_provider_array();
	$restriction_menus = array('view_level_restriction' => 'View Restrictions');
	$restriction_menus = array_merge($restriction_menus, userplace_get_restricted_post_types());
 	$restrictions = get_post_meta( $post->ID, '_userplace_plan_restrictions', true );
	wp_localize_script( 'userplace_payment_plan_restrictions', 'USERPLACE_ADMIN_RESTRICTIONS',
		apply_filters('userplace_admin_plan_restrictions',
			array(
				'PLAN_RESTRICTIONS' => $restrictions,
				'fields' => $restriction_fields,
				'menus' => $restriction_menus,
			)
		)
	);
?>
<input type="hidden" id="_userplace_plan_restrictions" value="<?php echo esc_attr(isset($restrictions) ? $restrictions : '{}') ?>" name="_userplace_plan_restrictions">
