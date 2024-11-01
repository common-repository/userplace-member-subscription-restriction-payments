<?php

/**
 * @param  [user id]
 * @return [array of posts that is created by the user]
 */

class UserplaceGateway
{
	use Userplace\Payment_Info;
}

add_filter('wp_nav_menu_args', 'userplace_edit_nav_menu');

function userplace_edit_nav_menu($args)
{
	if ($args['menu_class'] === 'listbook-mobile-menu') {
		$args['walker'] = new Userplace_Mobile_Nav_Walker();
	} else {
		$args['walker'] = new Userplace_Nav_Walker();
	}
	return $args;
}

function userplace_google_map_scripts()
{
	$googlemap_settings = userplace_get_settings('googlemap_api_key');
	if (isset($googlemap_settings) && $googlemap_settings != '') {
		wp_register_script('google-map-api', '//maps.googleapis.com/maps/api/js?key=' . $googlemap_settings . '&libraries=places,geometry&language=en-US', true, false);
		wp_enqueue_script('google-map-api');
	}
}

function userplace_process_login_user_option($restricted_plans, $current_user_plan)
{
	if (isset($restricted_plans) && in_array($current_user_plan, $restricted_plans)) {
		return false;
	}
	return true;
}

function userplace_insert_data_to_custom_db($table_name, $data, $format)
{
	global $wpdb;
	$wpdb->insert($wpdb->prefix . $table_name, $data, $format);
}

function is_userplace_configured_properly()
{
	$is_webhook_configured = false;
	$is_payment_configured = false;
	$is_plan_configured = false;
	$structure = get_option('permalink_structure');


	// Fallback
	$api_key = 'false';
	$api_secret = 'false';

	$gateway_info = new UserplaceGateway();
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


	if ($structure != '' && $is_webhook_configured && $is_payment_configured && $is_plan_configured && $sign_in_page && $register && $forgot_your_password && $userplace_plan_page_url && $pick_a_new_password) {
		return true;
	}
	return false;
}

function userplace_get_all_cards($user_id)
{
	global $wpdb;
	$query = $wpdb->prepare("SELECT * from {$wpdb->prefix}userplace_cards where user = %s AND deleted != %d ORDER BY id DESC", $user_id, 1);
	$results  = $wpdb->get_results($query, 'ARRAY_A');
	return $results;
}
function userplace_get_customer_default_card($user_id)
{
	global $wpdb;
	$query = $wpdb->prepare("SELECT * from {$wpdb->prefix}userplace_cards where user = %s AND deleted != %d AND is_default = %d", $user_id, 1, 1);
	$results  = $wpdb->get_results($query, 'ARRAY_A');
	return $results;
}
function userplace_get_all_invoices($customer, $limit = 10)
{
	global $wpdb;
	$query = $wpdb->prepare("SELECT * from {$wpdb->prefix}userplace_invoices where customer = %s ORDER BY id DESC LIMIT {$limit}", $customer);
	$results  = $wpdb->get_results($query);
	return $results;
}

function userplace_save_cards_to_db($card, $default_card = null)
{
	$processed_card_info = [];
	$processed_card_info['card_id'] = sanitize_text_field($card->id);
	$processed_card_info['card_name'] = sanitize_text_field($card->name);
	$processed_card_info['card_brand'] = sanitize_text_field($card->brand);
	$processed_card_info['user'] = sanitize_text_field($card->customer);
	$processed_card_info['last4'] = sanitize_text_field($card->last4);
	$processed_card_info['expired_at'] = sanitize_text_field($card->exp_month . '/' . $card->exp_year);
	$format = array(
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
	);
	if (($default_card != null && $default_card['default_source']['id'] === $card->id) || (isset($card->is_default) && $card->is_default == 1)) {
		$processed_card_info['is_default'] = 1;
		$format[] = '%d';
	}
	userplace_insert_data_to_custom_db('userplace_cards', $processed_card_info, $format);
}
function userplace_update_cards_to_db($card, $default_card = null)
{
	global $wpdb;
	$processed_card_info = [];
	$processed_card_info['card_id'] = sanitize_text_field($card->id);
	$processed_card_info['card_name'] = sanitize_text_field($card->name);
	$processed_card_info['card_brand'] = sanitize_text_field($card->brand);
	$processed_card_info['user'] = sanitize_text_field($card->customer);
	$processed_card_info['last4'] = sanitize_text_field($card->last4);
	$processed_card_info['expired_at'] = sanitize_text_field($card->exp_month . '/' . $card->exp_year);
	$processed_card_info['is_default'] = 1;
	$format = array(
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%d'
	);
	$where = array(
		'user' => $card->customer,
		'is_default' => 1
	);
	$where_format = array('%s', '%d');
	$wpdb->update($wpdb->prefix . 'userplace_cards', $processed_card_info, $where, $format, $where_format);
}

function userplace_store_logs($log_details = '')
{
	$format = array(
		'%s',
	);
	$processed_card_info['log_details'] = $log_details;
	userplace_insert_data_to_custom_db('userplace_logs', $processed_card_info, $format);
}

function userplace_save_invoices_to_db($invoice_object)
{

	$invoice = [];
	$invoice['customer'] = sanitize_text_field($invoice_object['customerId']);
	$invoice['amount'] = sanitize_text_field($invoice_object['deductedAmount']);
	$invoice['currency'] = sanitize_text_field($invoice_object['currency']);
	$invoice['last4'] = sanitize_text_field($invoice_object['last4']);
	$invoice['brand'] = sanitize_text_field($invoice_object['brand']);
	$invoice['plan'] = sanitize_text_field($invoice_object['planName']);
	$invoice['transaction_id'] = sanitize_text_field($invoice_object['transactionId']);
	$format = array(
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
	);
	userplace_insert_data_to_custom_db('userplace_invoices', $invoice, $format);
}

function userplace_delete_card($card_id)
{
	try {
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}userplace_cards WHERE id = %d", $card_id);
		$card = $wpdb->get_results($query, "ARRAY_A");
		if (isset($card[0])) {
			$payment_init = userplace_get_payment_info();
			$delete_card_info = $payment_init->billing->deleteCard($card[0]['card_id'], $card[0]['user']);
		}
		if ($delete_card_info->deleted) {
			$wpdb->update(
				$wpdb->prefix . 'userplace_cards',
				array('deleted' => 1),
				array('id' => $card_id),
				array('%d'),
				array('%d')
			);
			return array('success' => 1);
		}
		return array('success' => false);
	} catch (\Exception $e) {
		userplace_store_logs($e->getMessage());
	}
}
function userplace_make_default_card($card_id)
{
	try {
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}userplace_cards WHERE id = %d", $card_id);
		$card = $wpdb->get_results($query, "ARRAY_A");
		if (isset($card[0])) {
			$payment_init = userplace_get_payment_info();
			$default_card_info = $payment_init->billing->makeDefault($card[0]['card_id'], $card[0]['user']);
		}
		if ($default_card_info) {
			$wpdb->update(
				$wpdb->prefix . 'userplace_cards',
				array('is_default' => 0),
				array('user' => $card[0]['user'], 'is_default' => 1),
				array('%d'),
				array('%s', '%d')
			);
			$wpdb->update(
				$wpdb->prefix . 'userplace_cards',
				array('is_default' => 1),
				array('id' => $card_id),
				array('%d'),
				array('%d')
			);
			return array('success' => 1);
		}
		return array('success' => false);
	} catch (\Exception $e) {
		userplace_store_logs($e->getMessage());
	}
}

function userplace_get_payment_info()
{
	try {
		$payment_init = new Userplace\Payment_Init();
		$payment_init->gateway 			   = $payment_init->get_payment_gateway();
		$payment_init->credentials 	   = $payment_init->get_payment_gateway_credentials($payment_init->gateway);
		$gateWayClass = "RedQ\\Payment\\" . ucfirst($payment_init->gateway) . 'Gateway';
		$payment_init->billing = new RedQ\Payment\Billing(new $gateWayClass($payment_init->credentials));
		return $payment_init;
	} catch (\Exception $e) {
		userplace_store_logs($e->getMessage());
	}
}



function userplace_is_user_capable_to_view($current_user_plan, $restricted_plans)
{
	return userplace_process_login_user_option($restricted_plans, $current_user_plan);
}

function userplace_get_all_plan()
{
	$all_plan =  get_posts(array(
		'post_type' 	=> 'userplace_plan',
		'post_status' => 'publish'
	));

	$all_plan_name = array('all_membership' => 'All Membership');

	foreach ($all_plan as $key => $plan) {
		$all_plan_name[$plan->ID] = $plan->post_title;
	}
	return apply_filters('userplace_all_plan_list', $all_plan_name);
}

function userplace_get_coupon_id($coupon_post_id)
{
	$coupon_id = null;
	$coupon_id = get_post_meta($coupon_post_id, 'userplace_coupon_id', true);
	return $coupon_id;
}

function userplace_get_all_coupons()
{
	$coupon_post_object = get_posts(array(
		'post_type'      => 'userplace_coupon',
		'posts_per_page' => -1,
		'post_status'    => 'publish'
	));

	$all_coupons = array('no_coupon' => esc_html__('No Coupon Applied', 'userplace'));

	if (isset($coupon_post_object) && is_array($coupon_post_object) && !empty($coupon_post_object)) {
		foreach ($coupon_post_object as $key => $coupon) {
			$all_coupons[$coupon->ID] = $coupon->post_title;
		}
	}
	return $all_coupons;
}

function userplace_get_all_active_widgets()
{
	$all_widgets = get_option('sidebars_widgets');
	$processed_widgets = array();
	$processed_widget_child = array();
	foreach ($all_widgets as $key => $widget) {
		if ($key != 'wp_inactive_widgets' && is_array($widget) && !empty($widget)) {
			foreach ($widget as $child_key => $value) {
				$processed_widget_child[$value] = $value;
			}
		}
	}
	return $processed_widget_child;
}

function userplace_get_restricted_post_types()
{
	$providers =  new Userplace\Admin_Lacalize();
	$all_post_types =  $providers->get_all_posts();
	$restricted_post_types = userplace_get_settings('userplace_submission_restricted_post_types');
	if (!isset($restricted_post_types) || $restricted_post_types == '') {
		return $all_post_types;
	}
	$restricted_post_type_array = explode(',', $restricted_post_types);
	$processed_post_types = array();
	if (is_array($restricted_post_type_array)) {
		foreach ($restricted_post_type_array as $key => $post_type) {
			$processed_post_types[$post_type] = $all_post_types[$post_type];
		}
	}
	return $processed_post_types;
}

function userplace_get_all_meta_keys()
{
	$all_post_types = array();
	$all_post_types = userplace_get_restricted_post_types();
	$post_types = array_keys($all_post_types);
	global $wpdb;
	$all_keys = array();
	$post_type_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));
	$query = $wpdb->prepare("SELECT DISTINCT pm.meta_key FROM {$wpdb->posts} post INNER JOIN
	{$wpdb->postmeta} pm ON post.ID = pm.post_id WHERE post.post_type IN ($post_type_placeholder)", $post_types);
	$result = $wpdb->get_results($query, 'ARRAY_A');
	if (!empty($result)) {
		foreach ($result as $res) {
			if (!in_array($res['meta_key'], $all_keys)) {
				$all_keys[$res['meta_key']] = $res['meta_key'];
			}
		}
	}
	return $all_keys;
}

function userplace_get_processed_bundle_settings_data($restrictions = array())
{
	foreach ($restrictions as $restriction) {
		$post_type = false;
		foreach ($restriction['data'] as $data) {
			if ($data['id'] === 'post_type') {
				$post_type = $data['value'];
				$restricted_posts[$post_type]['post_type'] = $data['value'];
			} else if ($data['id'] === 'public_view') {
				$restricted_posts[$post_type]['public_view'] = $data['value'];
			} else if ($data['id'] === 'restricted_roles') {
				$restricted_posts[$post_type]['restricted_roles'] = ($data['value']) ? explode(',', $data['value']) : array();
			}
		}
	}
	return $restricted_posts;
}

if (!function_exists('userplace_get_user_posts')) {
	function userplace_get_user_posts($post_type = 'post', $offset = 0, $user_id = '')
	{
		if (empty($user_id)) {
			global $current_user;
			$user_id = $current_user->ID;
		}
		$user_posts = array();
		$post_args = array(
			'author'            =>  $user_id,
			'orderby'           =>  'post_date',
			'order'             =>  'DESC',
			'post_type'         => $post_type,
			'post_status'       => 'any',
			'posts_per_page'    => 2,
			'offset'    				=> $offset
		);
		$user_posts = get_posts($post_args);
		return $user_posts;
	}
}
if (!function_exists('userplace_redirect')) {
	function userplace_redirect($url)
	{
		$string = '<script type="text/javascript">';
		$string .= 'window.location = "' . $url . '"';
		$string .= '</script>';
		echo apply_filters('userplace_redirect_direct', $string);
	}
}
if (!function_exists('userplace_get_all_capabilities')) {
	function userplace_get_all_capabilities()
	{
		global $wp_roles;
		$all_capabilities = array();
		foreach ($wp_roles->roles as $key => $role) {
			$all_capabilities  = array_merge($all_capabilities, $role['capabilities']);
		}
		$processed_cap = array();
		foreach ($all_capabilities as $single_cap => $value) {
			$processed_cap[$single_cap] = $single_cap;
		}
		return $processed_cap;
	}
}
if (!function_exists('userplace_get_all_roles')) {
	function userplace_get_all_roles()
	{
		global $wp_roles;
		$all_roles = array();
		foreach ($wp_roles->roles as $key => $role) {
			$all_roles[$key]  = $key;
		}
		return $all_roles;
	}
}
if (!function_exists('userplace_get_all_pages')) {
	function userplace_get_all_pages($type = 'id')
	{
		$all_pages = get_posts(array('post_type' => 'page', 'status' => 'publish', 'posts_per_page' => -1));
		$formatted_pages = array();
		foreach ($all_pages as $page) {
			if ($type === 'slug') {
				$formatted_pages[$page->post_name] = $page->post_title;
			} else {
				$formatted_pages[$page->ID] = $page->post_title;
			}
		}
		return $formatted_pages;
	}
}
if (!function_exists('userplace_get_settings')) {
	function userplace_get_settings($settings_key)
	{
		$all_settings = json_decode(get_option('userplace_settings', true), true);
		if (!isset($all_settings[$settings_key])) return false;
		if ($all_settings[$settings_key] == '') return false;
		return $all_settings[$settings_key];
	}
}



/**
 * @param  [user id]
 * @return [array of posts that is created by the user]
 */
if (!function_exists('userplace_get_user_all_posts')) {
	function userplace_get_user_all_posts($user_id = '')
	{
		global $wpdb;
		if (empty($user_id)) {
			global $current_user;
			$user_id = $current_user->ID;
		}
		$placeholder_array = array(
			'attachment',
			'userplace_faq',
			'page',
			'userplace_template',
			'userplace_component',
			'userplace_taxonomy',
			'userplace_term_metabox',
			'userplace_metabox',
			'userplace_form_builder',
			'userplace_plan',
			'userplace_rb_post',
			'userplace_post_type',
			'userplace_post_type',
			'nav_menu_item',
			'userplace_console',
			'revision',
			'userplace_contact_form',
			'reactive_search',
			'pricetable',
			'rebuilder',
			'wpcf7',
			'reactive_grid',
			'reactive_builder'
		);
		$placeholder = implode(', ', array_fill(0, count($placeholder_array), '%s'));
		$placeholder_array[] = $user_id;
		$placeholder_array[] = 'publish';
		$user_posts = array();
		$query = $wpdb->prepare("SELECT * FROM $wpdb->posts
							WHERE post_type NOT IN ($placeholder) AND
							post_author = %d AND
							post_status = %s
							ORDER BY post_date DESC", $placeholder_array);
		$user_posts = $wpdb->get_results($query);
		return $user_posts;
	}
}

/**
 * @param  [array of all the posts by a user]
 * @param  [user id]
 * @return [total number of comments receive by a user]
 */
if (!function_exists('userplace_get_user_posts_comment_count')) {
	function userplace_get_user_posts_comment_count($current_user_posts, $current_user_id)
	{
		global $wpdb;
		$total_comments = 0;
		foreach ($current_user_posts as $post) {
			$post_id = $post->ID;
			$query = $wpdb->prepare(
				"SELECT * FROM $wpdb->comments
								WHERE comment_post_ID = %d and
								user_id != %d
								ORDER BY comment_date DESC",
				$post_id,
				$current_user_id
			);
			$comments = $wpdb->get_results($query);
			$total_comments += count($comments);
		}
		return $total_comments;
	}
}

/**
 * @param  [post type index number]
 * @return [color string]
 */
if (!function_exists('userplace_get_post_color_category')) {
	function userplace_get_post_color_category($key)
	{
		$post_color_categories = array('business', 'restaurant', 'job');
		$color = $post_color_categories[$key % 3];
		return $color;
	}
}


/**
 * [userplace_get_term_by_taxonomy description]
 * @param  [string] $taxonomy [taxonomy name]
 * @return [array]           [array of terms]
 */
if (!function_exists('userplace_get_term_by_taxonomy')) {
	function userplace_get_term_by_taxonomy($taxonomy, $post_type = '')
	{
		$user_id = get_current_user_id();
		$user_plan_id = get_user_meta($user_id, 'userplace_customer_plan_id', true);
		$providers = new \Userplace\Provider();
		$restrictions = $providers->get_plan_restrictions($user_plan_id, $post_type);
		$restricted_terms = '';

		$all_terms = array();
		$restricted_terms_array = array();
		if (isset($restrictions['restricted_terms']) && $restrictions['restricted_terms'] != '') {
			$restricted_terms_array = explode(',', $restrictions['restricted_terms']);
		}
		$terms = get_terms($taxonomy, array('hide_empty' => false));
		foreach ($terms as $term) {
			if (!in_array($term->slug, $restricted_terms_array)) {
				$all_terms[$term->slug] = $term->name;
			}
		}
		return $all_terms;
	}
}


if (!function_exists('userplace_get_post_terms')) {
	function userplace_get_post_terms($post_id, $taxonomies)
	{
		$temp = array();
		foreach ($taxonomies as $taxonomy) {
			$terms = wp_get_post_terms($post_id, $taxonomy);
			$temp[$taxonomy] = array();
			foreach ($terms as $term) {
				$slug = apply_filters('editable_slug', $term->slug);
				$temp[$taxonomy][] = esc_attr($slug);
			}
		}
		return $temp;
	}
}
if (!function_exists('userplace_get_term_hierarchicaly_by_taxonomy')) {
	function userplace_get_term_hierarchicaly_by_taxonomy($taxonomy)
	{
		$categories = get_terms($taxonomy, array('hide_empty' => false));
		$categoryHierarchy = array();
		userplace_sort_terms_hierarchicaly($categories, $categoryHierarchy);

		return $categoryHierarchy;
	}
}
if (!function_exists('userplace_sort_terms_hierarchicaly')) {
	function userplace_sort_terms_hierarchicaly(array &$cats, array &$into, $parentId = 0)
	{
		foreach ($cats as $i => $cat) {
			if ($cat->parent == $parentId) {
				$into[] = $cat;
				unset($cats[$i]);
			}
		}
		foreach ($into as $topCat) {
			$topCat->children = array();
			userplace_sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id);
		}
	}
}
if (!function_exists('userplace_get_current_user_id')) {
	function userplace_get_current_user_id()
	{
		global $current_user;
		$user_id = $current_user->ID;
		return $user_id;
	}
}
if (!function_exists('userplace_add_bulk_meta')) {
	function userplace_add_bulk_meta($meta_type, $post_id, $metadata)
	{
		global $wpdb;
		$meta_values = array();
		foreach ($metadata as $key => $value) {
			$meta_values[] = $wpdb->prepare('(NULL, %s, %s, %s)', $post_id, $key, $value);
		}
		$values = implode(', ', $meta_values);
		switch ($meta_type) {
			case 'post':
				$wpdb->query("INSERT INTO $wpdb->postmeta (meta_id, post_id, meta_key, meta_value) VALUES $values");
				break;

			case 'user':
				$wpdb->query("INSERT INTO $wpdb->usermeta (umeta_id, user_id, meta_key, meta_value) VALUES $values");
				break;
		}
	}
}
if (!function_exists('userplace_send_password_reset_link')) {
	function userplace_send_password_reset_link($user_data)
	{
		$errors = new WP_Error();
		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @since 2.1.0
		 * @since 4.4.0 Added the `$errors` parameter.
		 *
		 * @param WP_Error $errors A WP_Error object containing any errors generated
		 *                         by using invalid credentials.
		 */
		do_action('lostpassword_post', $errors);

		if ($errors->get_error_code())
			return $errors;

		if (!$user_data) {
			$errors->add('invalidcombo', esc_html__('<strong>ERROR</strong>: Invalid username or email.', 'userplace'));
			return $errors;
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key = get_password_reset_key($user_data);

		if (is_wp_error($key)) {
			return $key;
		}

		$message = esc_html__('Someone has requested a password reset for the following account:', 'userplace') . "\r\n\r\n";
		$message .= network_home_url('/') . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'userplace') . "\r\n\r\n";
		$message .= esc_html__('To reset your password, visit the following address:', 'userplace') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

		if (is_multisite()) {
			$blogname = get_network()->site_name;
		} else {
			/*
			* The blogname option is escaped with esc_html on the way into the database
			* in sanitize_option we want to reverse this for the plain text arena of emails.
			*/
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		}

		/* translators: Password reset email subject. 1: Site name */
		$title = sprintf(__('[%s] Password Reset'), $blogname);

		/**
		 * Filters the subject of the password reset email.
		 *
		 * @since 1.0.0
		 * @since 1.0.0 Added the `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $title      Default email title.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		 */
		$title = apply_filters('retrieve_password_title', $title, $user_login, $user_data);

		/**
		 * Filters the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $message    Default mail message.
		 * @param string  $key        The activation key.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		 */
		$message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);
		if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message))
			return esc_html__('The email could not be sent.', 'userplace') . "<br />\n" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'userplace');
		return 'true';
	}
}

if (!function_exists('userplace_process_user_string_data')) {
	function userplace_process_user_string_data($data)
	{
		$userplace_user_id = get_current_user_id();
		$data = explode(':', $data);
		switch ($data[0]) {
			case 'meta_key':
				$user_info = get_user_meta($userplace_user_id, $data[1], true);
				break;
			case 'user_key':
				$user =  get_user_by('id', $userplace_user_id);
				$user_info = $user->data->{$data[1]};
				break;
		}
		return $user_info;
	}
}
if (!function_exists('userplace_reactive_lat_long')) {
	function userplace_reactive_lat_long($post_id, $data)
	{
		global $wpdb;
		$check_link = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}re_lat_lng WHERE id = '" . $post_id . "'");
		if (isset($data['zipcode'])) {
			$zipcode = $data['zipcode'];
		}
		if (isset($data['zip_code'])) {
			$zipcode = $data['zip_code'];
		}
		if ($check_link != null) {
			if (isset($data['lat']) && $data['lng'])
				$wpdb->update(
					$wpdb->prefix . 're_lat_lng',
					array(
						'lat' => $data['lat'],
						'lng' => $data['lng'],
						'state' => $data['state_long'],
						'city' => $data['city'],
						'country' => $data['country_long'],
						'country_short_name' => $data['country_short'],
						'state_short_name' => $data['country_long'],
						'zipcode' => $zipcode,
						'formatted_address' => $data['formattedAddress'],
					),
					array('id' => $post_id),
					array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
				);
		} else {
			if (isset($data['lat']) && $data['lng'])
				$wpdb->insert(
					$wpdb->prefix . 're_lat_lng',
					array(
						'id' => $post_id,
						'lat' => $data['lat'],
						'lng' => $data['lng'],
						'state' => $data['state_long'],
						'city' => $data['city'],
						'country' => $data['country_long'],
						'country_short_name' => $data['country_short'],
						'state_short_name' => $data['country_long'],
						'zipcode' => $zipcode,
						'formatted_address' => $data['formattedAddress'],
					),
					array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
				);
		}
	}
}

if (!function_exists('userplace_get_all_terms')) {
	function userplace_get_all_terms()
	{
		global $wpdb;
		$all_terms = [];
		$results = $wpdb->get_results("SELECT DISTINCT name, slug from {$wpdb->terms}", 'ARRAY_A');
		foreach ($results as $key => $result) {
			$all_terms[$result['slug']] = $result['name'];
		}
		return $all_terms;
	}
}


if (!function_exists('userplace_get_terms')) :
	function userplace_get_terms($taxonomies)
	{
		global $wpdb;
		if (empty($taxonomies)) $taxonomies = array('category');
		$taxonomy_placeholder = implode(', ', array_fill(0, count($taxonomies), '%s'));

		$query = $wpdb->prepare("SELECT term_taxonomy_id as term_id, name, slug, parent, taxonomy, description, count, GROUP_CONCAT(meta_key SEPARATOR ':::') as allMetaKey, GROUP_CONCAT(meta_value SEPARATOR ':::') as allMetaValue FROM {$wpdb->terms}
        LEFT JOIN {$wpdb->term_taxonomy}
        ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_taxonomy_id
        LEFT JOIN {$wpdb->termmeta} ON
        {$wpdb->termmeta}.term_id = {$wpdb->terms}.term_id
        WHERE {$wpdb->term_taxonomy}.taxonomy IN ($taxonomy_placeholder)
        GROUP BY {$wpdb->term_taxonomy}.term_taxonomy_id", $taxonomies);
		$results = $wpdb->get_results($query, 'ARRAY_A');

		$data = array();

		foreach ($results as $result) {
			$meta = array();
			$allMetaKey = explode(':::', $result['allMetaKey']);
			$allMetaValue = explode(':::', $result['allMetaValue']);

			if (!empty($allMetaKey) && !empty($allMetaValue)) {
				$meta = array_combine($allMetaKey, $allMetaValue);
			}

			$obj                    = new stdClass();
			$obj->term_id           = $result['term_id'];
			$obj->name              = $result['name'];
			$obj->slug              = $result['slug'];
			$obj->taxonomy          = $result['taxonomy'];
			$obj->description       = $result['description'];
			$obj->count             = $result['count'];
			$obj->parent            = $result['parent'];
			$obj->meta             	= $meta;
			$data[]               	= $obj;
		}

		return $data;
	}
endif;

/**
 * Sublisting helper functioins
 */
if (!function_exists('userplace_get_parent_post_type')) :
	function userplace_get_parent_post_type($child_post_type)
	{
		global $wpdb;
		$child_post_type = '%' . $child_post_type . '%';
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}userplace_parent_child_post_types WHERE child_post_types LIKE %s", $child_post_type);
		$results = $wpdb->get_results($query);
		if (is_array($results) && !empty($results)) {
			return $results[0]->parent_post_type;
		}
		return 'false';
	}
endif;
if (!function_exists('userplace_get_all_child_post_type')) :
	function userplace_get_all_child_post_type($parent_post_type)
	{
		global $wpdb;
		$parent_post_type = '%' . $parent_post_type . '%';
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}userplace_parent_child_post_types WHERE parent_post_type LIKE %s", $parent_post_type);
		$results = $wpdb->get_results($query);
		if (is_array($results) && !empty($results)) {
			return $results[0]->child_post_types;
		}
		return 'false';
	}
endif;
if (!function_exists('userplace_is_child_post_type')) :
	function userplace_is_child_post_type($post_type)
	{
		global $wpdb;
		$post_type = '%' . $post_type . '%';
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}userplace_parent_child_post_types WHERE child_post_types LIKE %s", $post_type);
		$results = $wpdb->get_results($query);

		if (is_array($results) && !empty($results)) {
			return $results;
		}
		return 'false';
	}
endif;
if (!function_exists('userplace_get_number_of_listed_children')) :
	function userplace_get_number_of_listed_children($parent_post_id, $post_type)
	{
		global $wpdb;
		$data_array[] = $parent_post_id;
		$data_array[] = $post_type;
		$data_array[] = '%publish%';
		$query = $wpdb->prepare("SELECT COUNT(*) as total_listing FROM {$wpdb->posts} WHERE post_parent = %d AND post_type = %s AND post_status LIKE %s", $data_array);
		$results = $wpdb->get_results($query);
		if (is_array($results) && !empty($results)) {
			return isset($results[0]->total_listing) ? $results[0]->total_listing : 0;
		}
		return 'false';
	}
endif;

if (!function_exists('userplace_get_sublisting_posts')) :
	function userplace_get_sublisting_posts($parent_id)
	{
		global $wpdb;
		$query = $wpdb->prepare("SELECT DISTINCT {$wpdb->posts}.*, {$wpdb->postmeta}.meta_value as pre_value FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->postmeta}
				ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND meta_key = 'pre_value'
				WHERE post_parent = %d
				AND post_status != %s
				", $parent_id, 'trash');
		$results = $wpdb->get_results($query);
		return $results;
	}
endif;
if (!function_exists('userplace_get_parent_post_thumb_info')) :
	function userplace_get_parent_post_thumb_info($parent_id)
	{
		$thumb_url = get_the_post_thumbnail_url($parent_id);
		$post_link = get_permalink($parent_id);
		return array(
			'thumb_url' => $thumb_url,
			'post_url' => $post_link
		);
	}

endif;

if (!function_exists('userplace_get_default_plan')) :
	function userplace_get_default_plan()
	{
		$args = array(
			'posts_per_page'   => -1,
			'meta_key'         => 'default_plan',
			'meta_value'       => 'true',
			'post_type'        => 'userplace_plan',
		);
		$default_plan = get_posts($args);

		if (!empty($default_plan)) {
			$default_plans = $default_plan[0];
			$default_plans->plan_id = get_post_meta($default_plans->ID, 'plan_id', true);
			return $default_plans;
		}

		return;
	}

endif;

if (!function_exists('userplace_get_all_plans')) :
	function userplace_get_all_plans()
	{
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'userplace_plan',
		);
		$plans = array();
		$all_plans = get_posts($args);

		if (!empty($all_plans)) {
			foreach ($all_plans as $plan) {
				$plans[get_post_meta($plan->ID, 'plan_id', true)] = $plan->post_title;
			}
			return $plans;
		}

		return;
	}

endif;


function userplace_subscription_active($userId)
{
	$user_subscription_status = get_user_meta($userId, 'userplace_status', true);
	if (isset($user_subscription_status) && strtolower($user_subscription_status) == 'active') {
		return true;
	}
	return false;
}

function userplace_get_user_subscription_plan($user_id)
{
	if (userplace_subscription_active($user_id)) {
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
