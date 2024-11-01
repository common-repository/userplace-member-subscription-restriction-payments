<?php
/**
 * Localize the updated data from database
 */
 	use Userplace\Provider;
	$settings_array = new Provider();
	$settings_fields = $settings_array->payments_gateway_settings_array();
	$conditions = $settings_array->payment_settings_conditional_logic();
	$payments_settings = get_option('userplace_payment_settings', true );
	$settings_menu_fields = array(
		'stripe' 					=> esc_html__('Stripe', 'userplace'),
		'braintree' 				=> esc_html__('Braintree', 'userplace'),
	);
	wp_localize_script( 'userplace_payment_settings', 'USERPLACE_ADMIN',
		apply_filters('userplace_admin_generator_localize_args', array(
			'PAYMENT_SETTINGS' 	=> ( isset($payments_settings) && $payments_settings != 1 ) ? $payments_settings : '{}',
			'fields' 			=> apply_filters('userplace_payment_settings_fileds', $settings_fields),
			'conditions' 		=> $conditions,
			'SETTINGS_MENU' 	=> apply_filters('userplace_payment_settings_menu_fileds', $settings_menu_fields),
	) ));

	$userplace_settings_payment_update = '';
	$userplace_settings_payment_update = json_decode( get_option('userplace_settings' ), true );
	$userplace_settings_payment_update_msg = '';
	$userplace_settings_payment_update_msg = !empty($userplace_settings_payment_update['general_payment_update']) ? $userplace_settings_payment_update['general_payment_update'] : esc_html__('Settings Updated Successfully .', 'userplace');

?>
<div class="notification-container-settings dismiss">
<?php
		if ( !empty($userplace_settings_payment_update_msg)) {
			echo esc_html($userplace_settings_payment_update_msg);
		} else {
			echo esc_html__('Settings Updated Successfully .', 'userplace');
		} 
	?>
</div>
<div class="userplaceSettingsBar">
	<h1><?php esc_html_e('Userplace Settings', 'userplace') ?></h1>
	<div id="userplace_payment_settings"></div>
</div>

<input type="hidden" id="_userplace_payment_settings" name="_userplace_payment_settings" value="<?php echo esc_attr( ( isset($payments_settings) && $payments_settings != 1 ) ? $payments_settings : '{}') ?>">
