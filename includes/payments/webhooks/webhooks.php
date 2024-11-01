<?php

$webhooksEventData = new \Userplace\Payment_Init();
do_action('userplace_webhooks');
$data = $webhooksEventData->handleAnyWebhooks();
update_option('is_userplace_webhook_configured', true);
if ($data['type'] == 'nothing') return;
switch ($data['type']) {
	case 'subscription_expired':
		if ($data['customer_id'] == null) return;
		$users = get_users(
			array(
				'meta_key' => 'userplace_customer_id',
				'meta_value' => $data['customer_id']
			)
		);
		if (is_array($users) && isset($users[0])) {
			global $wpdb;
			$user_id = $users[0]->ID;
			update_user_meta($user_id, 'userplace_status', 'expired');

			$query = $wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->postmeta} as meta1 ON {$wpdb->posts}.ID = meta1.post_id
				WHERE post_author = %d AND post_status = %s AND (meta1.meta_key = %s AND meta1.meta_value <> %s)", $user_id, 'publish', 'is_pay_as_u_go', 'true');
			$results = array_flatten($wpdb->get_results($query, 'ARRAY_A'));
			foreach ($results as $key => $post_id) {
				$postarr = array(
					'ID' => $post_id,
					'post_status' => 'draft'
				);
				$updated_post_id = wp_update_post($postarr);
			}
		}
		break;
	case 'payment_succeeded':
		$users = get_users(array(
			'meta_key' => 'userplace_customer_id',
			'meta_value' => $data['customer_id'],
		));

		if (count($users)) {
			update_user_meta($users[0]->ID, 'userplace_expired_at', $data['currentPeriodEnd']);
		}

		userplace_save_invoices_to_db($data);
		break;

	default:
		# code...
		break;
}
