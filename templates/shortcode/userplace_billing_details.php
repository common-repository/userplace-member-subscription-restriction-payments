<?php

/**
 * Welcome Message
 *
 * Show the Billing Details of a Customer
 *
 * @author redqeteam
 * @category Theme
 * @package Userplace/Shortcodes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}


if (shortcode_exists('userplace_billing')) {
	echo do_shortcode('[userplace_billing]');
} else {
	echo '<div class="up-userplace-notification-info">
						<h3 class="rqPageTitle">' . esc_html__('Billing Overview', 'userplace') . '</h3>
						<p class="up-notify">' . esc_html__(' You are not currently with any plan. ', 'userplace') . '</p>
					</div>';
}
