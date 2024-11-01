<?php

/**
 * Localize the updated data from database
 */

use Userplace\Provider;

$settings_array = new Provider();
$settings_fields = $settings_array->payments_settings_array();
$payments_settings = get_option('userplace_settings', true);
$settings_menu_fields = array(
	'feedback' 		=> esc_html__('General Messages', 'userplace'),
	'login'			=> esc_html__('Feedback : Sign In', 'userplace'),
	'signup' 		=> esc_html__('Feedback : Sign Up', 'userplace'),
	'lost_pass' 	=> esc_html__('Feedback : Lost Password', 'userplace'),
	'reset_pass' 	=> esc_html__('Feedback : Reset Password', 'userplace'),
	'change_pass' 	=> esc_html__('Feedback : Change Password', 'userplace'),
);
wp_localize_script(
	'userplace_settings',
	'USERPLACE_ADMIN',
	apply_filters('userplace_admin_generator_localize_args', array(
		'PAYMENT_SETTINGS' 	=> (isset($payments_settings) && $payments_settings != 1) ? $payments_settings : '{}',
		'fields' 			=> apply_filters('userplace_settings_fields', $settings_fields),
		'SETTINGS_MENU' 	=> apply_filters('userplace_settings_menu_fields', $settings_menu_fields),
	))
);

$userplace_settings_general_settings_update = '';
$userplace_settings_general_settings_update = json_decode(get_option('userplace_settings'), true);
$userplace_settings_general_settings_update_msg = '';
$userplace_settings_general_settings_update_msg = !empty($userplace_settings_general_settings_update['general_settings_update']) ? $userplace_settings_general_settings_update['general_settings_update'] : esc_html__('Settings Updated Successfully .', 'userplace');

?>
<div class="notification-container-settings dismiss">
	<?php
	if (!empty($userplace_settings_general_settings_update_msg)) {
		echo esc_html($userplace_settings_general_settings_update_msg);
	} else {
		echo esc_html__('Settings Updated Successfully .', 'userplace');
	}
	?>
</div>
<div class="userplaceSettingsBar">
	<h1><?php esc_html_e('Userplace Settings', 'userplace') ?></h1>
	<div id="userplace_settings"></div>
</div>

<input type="hidden" id="_userplace_settings" name="_userplace_settings" value="<?php echo esc_attr((isset($payments_settings) && $payments_settings != 1) ? $payments_settings : '{}') ?>">
