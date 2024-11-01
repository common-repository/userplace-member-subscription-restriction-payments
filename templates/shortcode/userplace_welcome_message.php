<?php

/**
 * Welcome Message
 *
 * Show the confirmation message after successful payment & redirection
 *
 * @author redqeteam
 * @category Theme
 * @package Userplace/Shortcodes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

if (isset($_GET['welcome']) && $_GET['welcome'] == 'true') {
	$user_id = get_current_user_id();
	$price = get_user_meta($user_id, 'userplace_price', true);
	$currency = get_user_meta($user_id, 'userplace_currency', true);
	if ($user_id && isset($price) && $price != '') { ?>
		<div class="userplace-welcome">
			<p>
				<?php echo esc_html__('Congratz!! You have successfully subscribed.', 'userplace') ?>
				<span class="userplace-close-icon"><i class="icon icon ion-ios-close"></i><span>
			</p>
		</div>
<?php }
}
