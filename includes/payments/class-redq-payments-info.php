<?php

/**
 * Basic functionality provided for payments
 */

namespace Userplace;

use Userplace\Provider;
use DateTime;
use WP_Query;

trait Payment_Info
{

	public function get_payment_gateway()
	{
		$gateway = get_option('payment_gateway');
		return !empty($gateway) ? $gateway : 'false';
	}

	public function get_payment_gateway_credentials($gateway)
	{

		$payment_mode = get_option($gateway . '_payment_mode');
		if (empty($payment_mode)) return 'false';

		switch ($gateway) {
			case 'braintree':
				$args = ['merchant_id', 'api_key', 'api_secret'];
				return $this->get_gredentials($gateway, $payment_mode, $args);
				break;
			case 'stripe':
				$args = ['api_key', 'api_secret'];
				return $this->get_gredentials($gateway, $payment_mode, $args);
				break;
		}
	}


	public function get_user_info($meta_key)
	{
		$user_info = false;
		$user_id = get_current_user_id();
		if (is_user_logged_in()) {
			switch ($meta_key) {
				case 'email':
					$user = get_userdata($user_id);
					$user_info = $user->data->user_email;
					break;
				case 'username':
					$user = get_userdata($user_id);
					$user_info = $user->data->user_login;
					break;
				default:
					$user_info = get_user_meta($user_id, $meta_key, true);
					break;
			}
		}

		return $user_info ? $user_info : false;
	}

	private function get_gredentials($gateway, $payment_mode, $args)
	{
		$credentials = [];
		foreach ($args as $arg) {
			$credential = get_option($payment_mode . '_' . $gateway . '_' . $arg, true);
			$credentials[$arg] = isset($credential) && !empty($credential) ? $credential : 'false';
		}
		$credentials['payment_mode'] = $payment_mode;

		return $credentials;
	}

	public function getDetailsAboutPlan($plan_id, $post_type)
	{
		$provider = new Provider();
		return $provider->get_plan_restrictions($plan_id, $post_type);
	}

	// If user subscribe to this certain plan or not
	public function isUserSubscribed($userId, $planId)
	{
		$userplace_customer_id = get_user_meta($userId, 'userplace_customer_id', true);
		if (isset($userplace_customer_id) && $userplace_customer_id != '') {
			$user_Plan_id = get_user_meta($userId, 'userplace_customer_plan_id', true);
			if (isset($user_Plan_id) && $user_Plan_id == $planId) {
				return true;
			}
		}
		return false;
	}

	public function isUserOnTrail($userId)
	{
		return true;
	}
	// if user is into active subscription
	public function isUserActive($userId)
	{
		$user_subscription_status = get_user_meta($userId, 'userplace_status', true);
		if (isset($user_subscription_status) && strtolower($user_subscription_status) == 'active') {
			return true;
		}
		return false;
	}

	public function isSubscriptionAboutToEnd($userId)
	{
		return false;
	}

	public function getRestrictionDetails($userId, $plan_id, $postType = null)
	{
		global $wpdb;
		$provider = new Provider();
		if (isset($plan_id) && $plan_id !== '') {
			$restrictions = $provider->get_plan_restrictions($plan_id, $postType);
			foreach ($restrictions as $postType => $postTypeRestrictions) {

				$atts = array(
					'post_status'     => 'publish',
					'post_type'     => $postType,
					'author'     => $userId,
					'posts_per_page' => -1,
				);
				$posts = get_posts($atts);
				$number_of_post_by_this_user = count($posts);
				$restrictions[$postType]['used_quota'] = $number_of_post_by_this_user;
			}
			return $restrictions;
		}
		return false;
	}

	public function getUserSubscriptionPlan($user_id)
	{
		if ($this->isUserActive($user_id)) {
			$user_subscription_plan = get_user_meta($user_id, 'userplace_customer_plan_id', true);
			if (isset($user_subscription_plan) && $user_subscription_plan != '') {
				return $user_subscription_plan;
			}
		}

		$default_plan_object = userplace_get_default_plan();
		if ($default_plan_object) {
			$default_plan = $default_plan_object->plan_id;
			return $default_plan;
		}
	}

	public function getUserCardInfo($userId)
	{
		return array(
			'card_brand' => 'visa',
			'last_four' => '1111',
		);
	}

	public function getSubscriptionDayLeft($user_id)
	{
		if ($this->isUserActive($user_id)) {
			$subscription_expired_date = get_user_meta($user_id, 'userplace_expired_at', true);
			if (isset($subscription_expired_date) && $subscription_expired_date != '') {
				$today = time();
				$subscription_end_at = strtotime($subscription_expired_date);
				$time_left = $subscription_end_at - $today;
				if ($time_left > 0) {
					return round($time_left / 86400);
				}
			}
		}
		return 0;
	}

	public function calculate_is_user_permitted($form_data, $subscription_details, $listing_post_type)
	{
		$is_permitted = false;
		// Using dummy data the work is done here this have to do from fronend.
		// Have to send parent_id for child post type
		$form_data['parent_post_id'] = 3682;
		$listing_post_type = 'post';

		// userplace_is_child_post_type($listing_post_type);
		$query_data = userplace_is_child_post_type($listing_post_type);
		if (is_array($query_data) && !empty($query_data)) {
			if (isset($form_data['parent_post_id'])) {
				$parent_post_type = get_post_type($form_data['parent_post_id']);
			}
			if (isset($subscription_details[$parent_post_type])) {
				$number_of_listed_children = userplace_get_number_of_listed_children($form_data['parent_post_id'], $listing_post_type);
				if (isset($subscription_details[$parent_post_type]['maximum_child_' . $listing_post_type])) {
					if ($number_of_listed_children < $subscription_details[$parent_post_type]['maximum_child_' . $listing_post_type]) {
						$is_permitted = true;
					}
				}
			}
		} else {
			if (isset($subscription_details[$listing_post_type])) {
				if ($subscription_details[$listing_post_type]['max_posts'] > $subscription_details[$listing_post_type]['used_quota']) {
					if ($subscription_details[$listing_post_type]['max_attachments_per_post'] >= $form_data['noOfAttachments'] && $subscription_details[$listing_post_type]['max_terms_per_post'] >= $form_data['numberOfTerms']) {
						$is_permitted = true;
					}
				}
			}
		}
		return $is_permitted;
	}
}
