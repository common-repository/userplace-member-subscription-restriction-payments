<?php

/**
 * Component Post Content Shortcode
 *
 * Show the post content for dynamic generated template
 *
 * @author redqteam
 * @category Theme
 * @package Userplace/Shortcodes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Payment_info_trait')) {
	class Payment_info_trait
	{
		use Userplace\Payment_Info;
	}
}

$user = wp_get_current_user();
extract(shortcode_atts(array(
	'restricted_plans_id'   => '',
	'html'									=> esc_html__('The content is restricted. Please upgrade your plan to view this.', 'userplace')
), $atts));
$payment = new Payment_info_trait();
$current_user_plan = $payment->getUserSubscriptionPlan($user->ID);

$restricted_plans_id_array = $restricted_plans_id !== '' ? explode(',', $restricted_plans_id) : array();
$message = '';
if (userplace_is_user_capable_to_view($current_user_plan, $restricted_plans_id_array) || current_user_can('administrator')) {
	echo do_shortcode($content);
} else {
	if (empty($html)) {
		$message .= '<strong>';
		$message .= esc_html__('The content is restricted. Please upgrade your plan to view this.', 'userplace');
		$message .= '</strong>';
	} else {
		$message = html_entity_decode($html);
	}
	$message = apply_filters('userplace_restrict_content_message',  $message);
	echo do_shortcode($message);
}
