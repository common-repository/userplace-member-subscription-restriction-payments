<?php

/**
 * Plan Button
 *
 * Show the plan chooser button
 *
 * @author redq,inc
 * @category Shortcode
 * @package Userplace/Shortcodes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}


extract(shortcode_atts(array(
	'plan_id'               => '',
	'class'                 => '',
	'plan_title'            => esc_html__('Select Plan', 'userplace'),
	'switch_plan_title'     => esc_html__('Switch Plan', 'userplace'),
	'current_plan_title'    => esc_html__('Current Plan', 'userplace'),
), $atts));

$current_user   = wp_get_current_user();
$user_plan      = userplace_get_user_subscription_plan($current_user->ID);

$current_plan = false;

if (is_user_logged_in()) {
	$plan_title = $switch_plan_title;
	if ($plan_id == $user_plan) {
		$plan_title = $current_plan_title;
		$current_plan = true;
	}
}

if (!$current_plan) {
	$plan_url = site_url('subscription/pay?plan=' . $plan_id);
}
?>

<div class="userplace-pricing-plan-btn">
	<?php if ($current_plan) : ?>
		<button type="button" class="userplace-disabled-btn" disabled><?php echo esc_html($plan_title) ?></button>
	<?php else : ?>
		<a class="<?php echo esc_attr($class) ?>" href="<?php echo esc_url($plan_url) ?>"><?php echo esc_html($plan_title) ?></a>
	<?php endif ?>
</div>
