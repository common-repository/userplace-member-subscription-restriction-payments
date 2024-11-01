<?php
get_header('userplace', array('title' => esc_html__('Pricing Plan', 'userplace')));

if (is_user_logged_in() && !current_user_can('administrator')) {
	$plan = isset($_GET['plan']) ? sanitize_text_field($_GET['plan']) : null;
	if ($plan != null) {
		echo do_shortcode('[userplace_payment plan="' . $plan . '"]');
	}
} else if (current_user_can('administrator')) {
	echo '<div class="userplace-admin-notice"><p class="login-warning" >' . esc_html__('You are the admin of the site so you can not buy plans. For testing purpose, please register as a new user.', 'userplace') . '</p></div>';
} else {
	echo '<div class="userplace-plan-login-wrapper entry-content"><div class="userplace-plan-wrap"><p class="userplace-plan-message login-warning">' . esc_html__('For choosing any plan, please login first', 'userplace') . '</p></div>';
	echo do_shortcode('[userplace_login_form]');
	echo '</div>';
}

get_footer('userplace');
