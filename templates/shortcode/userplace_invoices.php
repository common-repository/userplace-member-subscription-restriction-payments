<?php

/**
 * Welcome Message
 *
 * Show the invoices of a customer
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
	echo do_shortcode('[userplace_billing preview_mode="invoice"]');
} else {
	echo '<div class="up-userplace-notification-info">
						<h3 class="rqPageTitle">' . esc_html__('Invoices', 'userplace') . '</h3>
						<p class="up-notify">' . esc_html__(' No Invoices Yet!', 'userplace') . '</p>
					</div>';
}
