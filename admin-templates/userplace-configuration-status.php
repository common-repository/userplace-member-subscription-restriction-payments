<?php
$is_webhook_configured = false;
$is_payment_configured = false;
$is_plan_configured = false;
$structure = get_option('permalink_structure');
class Payment
{
	use Userplace\Payment_Info;
}

// Fallback
$api_key = 'false';
$api_secret = 'false';

$gateway_info = new Payment();
$gateway = $gateway_info->get_payment_gateway();
if ($gateway != 'false') {
	$gateway_data = $gateway_info->get_payment_gateway_credentials($gateway);
	if ($gateway_data !== 'false') {
		extract($gateway_data);
	}
}
if ($api_key != 'false' && $api_secret != 'false') {
	$is_payment_configured = true;
}

$webhooks = get_option('is_userplace_webhook_configured');

if ($webhooks != '') {
	$is_webhook_configured = true;
}

$plans = get_posts(array(
	'post_type' => 'userplace_plan',
	'post_status' => 'publish'
));

if (count($plans) > 1) {
	$is_plan_configured = true;
}

$userplace_settings = json_decode(get_option('userplace_settings'), true);

$sign_in_page 						= false;
$register 								= false;
$forgot_your_password 		= false;
$pick_a_new_password 			= false;
$userplace_plan_page_url 	= false;

if (isset($userplace_settings['sign_in']) && !empty($userplace_settings['sign_in'])) {
	if (get_post_status($userplace_settings['sign_in']) == 'publish') {
		$sign_in_page = true;
	}
}

if (isset($userplace_settings['register']) && !empty($userplace_settings['register'])) {
	if (get_post_status($userplace_settings['register']) == 'publish') {
		$register = true;
	}
}

if (isset($userplace_settings['forgot_your_password']) && !empty($userplace_settings['forgot_your_password'])) {
	if (get_post_status($userplace_settings['forgot_your_password']) == 'publish') {
		$forgot_your_password = true;
	}
}

if (isset($userplace_settings['pick_a_new_password']) && !empty($userplace_settings['pick_a_new_password'])) {
	if (get_post_status($userplace_settings['pick_a_new_password']) == 'publish') {
		$pick_a_new_password = true;
	}
}

if (isset($userplace_settings['userplace_plan_page_url']) && !empty($userplace_settings['userplace_plan_page_url'])) {
	if (get_post_status($userplace_settings['userplace_plan_page_url']) == 'publish') {
		$userplace_plan_page_url = true;
	}
}


?>

<style>

</style>

<div class="up-settings-main-wrapper">
	<div class="adminContainer up-welcome-msg">
		<h1><?php esc_html_e('Userplace Configuration Status', 'userplace') ?></h1>
	</div>

	<div class="reactive-trick reactive-wrap up-status-wrap">
		<h2 class="up-welcome-heading"><?php esc_html_e('Basic Structure Setup', 'userplace') ?></h2>
		<div class="up-structure-setup">
			<div class="permalinks-status up-status-div">
				<h3><?php esc_html_e('Permalink Structure Change', 'userplace') ?></h3>
				<?php if ($structure != '') { ?>
					<p class="premalinks-done up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php	} else { ?>
					<p class="up-settings-error"><?php esc_html_e('Not Configured!!', 'userplace') ?></p>
					<p><?php esc_html_e('You have to change permalinks structure to anything rather than Plain for Userplace to work properly. Change your sites permalinks structure from', 'userplace') ?> <a href="<?php echo admin_url('options-permalink.php') ?>"><?php esc_html_e('here', 'userplace') ?></a></p>
				<?php } ?>
			</div>
			<div class="payment-gateway-setup up-status-div">
				<h3><?php esc_html_e('Payment Gateway Setup', 'userplace') ?></h3>
				<?php if ($is_payment_configured) { ?>
					<p class="payment-gateway-setup-status up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php	} else { ?>
					<p class="up-settings-error"><?php esc_html_e('Not Configured!!', 'userplace') ?></p>
					<p><?php esc_html_e('Please configure your payment gateway properly from', 'userplace') ?> <a href="<?php echo admin_url('admin.php?page=userplace_payment_settings') ?>" target="_blank"><?php esc_html_e('here', 'userplace') ?></a>. <?php esc_html_e('For better understanding how to configure payment gateway you can follow', 'userplace') ?> <a href="https://www.youtube.com/watch?v=PQd5F3JBOoU" target="_blank">this</a> <?php esc_html_e('url.', 'userplace') ?></p>
				<?php } ?>
			</div>
			<div class="payment-webhook-setup up-status-div">
				<h3><?php esc_html_e('Webhook Setup', 'userplace') ?></h3>
				<?php if ($is_webhook_configured) { ?>
					<p class="payment-webhook-status up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php	} else { ?>
					<p class="up-settings-error"><?php esc_html_e('Not Configured!!', 'userplace') ?></p>
					<p><?php esc_html_e('Please follow', 'userplace') ?> <a href="https://www.youtube.com/watch?v=VbPBeZAJ3Vg" target="_blank"><?php esc_html_e('this', 'userplace') ?></a> <?php esc_html_e('url to know how you can configure webhook. Webhook must be configured to generate invoices in your site which can be downloaded as pdf. After setting up webhook, please send a test webhook to complete this process.', 'userplace') ?></p>
				<?php } ?>
			</div>
			<div class="payment-plan-setup up-status-div">
				<h3><?php esc_html_e('Membership Plan Setup', 'userplace') ?></h3>
				<?php if ($is_plan_configured) { ?>
					<p class="payment-webhook-status up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php	} else { ?>
					<p class="up-settings-error"><?php esc_html_e('Not Configured!!', 'userplace') ?></p>
					<p><?php esc_html_e('Please go to', 'userplace') ?> <a href="<?php echo admin_url('edit.php?post_type=userplace_plan'); ?>" target="_blank"><?php esc_html_e('this', 'userplace') ?></a> <?php esc_html_e('url to create a Membership Plan. For better guidance please follow', 'userplace') ?> <a href="https://www.youtube.com/watch?v=-KEtlrExZcc" target="_blank"><?php esc_html_e('this', 'userplace') ?> </a><?php esc_html_e(' video tutorial.', 'userplace') ?></p>
				<?php } ?>
			</div>
		</div>

		<div class="up-pages-setup">
			<h2 class="up-welcome-heading page-setup"><?php esc_html_e('Basic Pages Setup', 'userplace') ?></h2>
			<div class="up-status-div">
				<h3><?php esc_html_e('Signin Page', 'userplace') ?></h3>
				<?php if ($sign_in_page) { ?>
					<p class="pages-setup-done up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php } else { ?>
					<p class="up-settings-error"><?php esc_html_e('Does not exists!', 'userplace') ?></p>
				<?php } ?>
			</div>
			<div class="up-status-div">
				<h3><?php esc_html_e('Register Page', 'userplace') ?></h3>
				<?php if ($register) { ?>
					<p class="pages-setup-done up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php } else { ?>
					<p class="up-settings-error"><?php esc_html_e('Does not exists!', 'userplace') ?></p>
				<?php } ?>
			</div>
			<div class="up-status-div">
				<h3><?php esc_html_e('Forgot Password Page', 'userplace') ?></h3>
				<?php if ($forgot_your_password) { ?>
					<p class="pages-setup-done up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php } else { ?>
					<p class="up-settings-error"><?php esc_html_e('Does not exists!', 'userplace') ?></p>
				<?php } ?>
			</div>
			<div class="up-status-div">
				<h3><?php esc_html_e('Reset Password Page', 'userplace') ?></h3>
				<?php if ($pick_a_new_password) { ?>
					<p class="pages-setup-done up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php } else { ?>
					<p class="up-settings-error"><?php esc_html_e('Does not exists!', 'userplace') ?></p>
				<?php } ?>
			</div>
			<div class="up-status-div">
				<h3><?php esc_html_e('Pricing Plan Page', 'userplace') ?></h3>
				<?php if ($userplace_plan_page_url) { ?>
					<p class="pages-setup-done up-settings-done"><?php esc_html_e('Done!', 'userplace') ?></p>
				<?php } else { ?>
					<p class="up-settings-error"><?php esc_html_e('Does not exists!', 'userplace') ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
</div>