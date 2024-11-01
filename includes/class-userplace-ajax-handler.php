<?php

/**
 * Handle AJAX Request
 */

namespace Userplace;

use Userplace\PayAsUGo;

class Ajax_Handler
{
	use Payment_Info;
	/**
	 * Action hook used by the AJAX class.
	 *
	 * @var string
	 */
	const ACTION = 'userplace_payment_ajax';

	/**
	 * Action argument used by the nonce validating the AJAX request.
	 *
	 * @var string
	 */
	const NONCE = 'userplace_payment_ajax_nonce';

	const ADONS = [
		'userplace-vc-addons/userplace-vc-addons.php',
		'userplace-google-reCAPTCHA/userplace-google-reCAPTCHA.php'
	];

	/**
	 * Register the AJAX handler class with all the appropriate WordPress hooks.
	 */
	public function __construct()
	{
		add_action('wp_ajax_' . self::ACTION, array($this, 'userplace_payment_handle_ajax'));
		add_action('wp_ajax_nopriv_' . self::ACTION, array($this, 'userplace_payment_handle_ajax'));

		add_action("wp_ajax_nopriv_userplace_send_listing_mail", array($this, 'userplace_send_listing_mail'));
		add_action("wp_ajax_userplace_send_listing_mail", array($this, 'userplace_send_listing_mail'));
		add_action("wp_ajax_userplace_user_follow", array($this, 'userplace_user_follow'));
		add_action("wp_ajax_userplace_user_follow_ajax", array($this, 'userplace_user_follow_ajax'));
		add_action("wp_ajax_userplace_install_adons", array($this, 'userplace_install_adons'));
		add_action("wp_ajax_userplace_activate_adons", array($this, 'userplace_activate_adons'));
		add_action("wp_ajax_userplace_deactivate_adons", array($this, 'userplace_deactivate_adons'));
	}

	public function userplace_activate_adons()
	{
		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonce'], self::NONCE)) {
			die('Busted!');
		}

		$plugin = sanitize_text_field($_POST['plugin']);
		if (!in_array($plugin, self::ADONS)) {
			return;
		}
		if (!current_user_can('activate_plugin')) {
			return;
		}
		$result = activate_plugin($plugin);

		if (is_wp_error($result)) {
			$errors[$plugin] = $result;
			if (!empty($errors))
				wp_send_json_error(new \WP_Error('plugins_invalid', esc_html__('One of the plugins is invalid.', 'userplace'), $errors));
		} else {

			wp_send_json_success(array('plugin' => $plugin));
		}

		wp_die();
	}

	public function userplace_deactivate_adons()
	{
		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonce'], self::NONCE)) {
			die('Busted!');
		}

		$plugin = sanitize_text_field($_POST['plugin']);
		if (!in_array($plugin, self::ADONS)) {
			return;
		}
		if (!current_user_can('activate_plugin')) {
			return;
		}
		$result = deactivate_plugins($plugin);

		if (is_wp_error($result)) {
			$errors[$plugin] = $result;
			if (!empty($errors))
				wp_send_json_error(new \WP_Error('plugins_invalid', esc_html__('One of the plugins is invalid.', 'userplace'), $errors));
		} else {

			wp_send_json_success(array('plugin' => $plugin));
		}

		wp_die();
	}

	public function userplace_install_adons()
	{
		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonce'], self::NONCE)) {
			die('Busted!');
		}
		include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
		include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');

		$api = new \stdClass();

		$api->name =  sanitize_text_field($_POST['name']);
		$api->slug =  sanitize_text_field($_POST['slug']);
		$api->version =  sanitize_text_field($_POST['version']);
		$api->download_link =  sanitize_text_field($_POST['link']);

		if (is_wp_error($api)) {
			$status['errorMessage'] = $api->get_error_message();
			wp_send_json_error($status);
		}

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader($skin);
		$result   = $upgrader->install($api->download_link);
		if (defined('WP_DEBUG') && WP_DEBUG) {
			$status['debug'] = $skin->get_upgrade_messages();
		}

		if (is_wp_error($result)) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
			wp_send_json_error($status);
		} elseif (is_wp_error($skin->result)) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();
			wp_send_json_error($status);
		} elseif ($skin->get_errors()->get_error_code()) {
			$status['errorMessage'] = $skin->get_error_messages();
			wp_send_json_error($status);
		} elseif (is_null($result)) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = esc_html__('Unable to connect to the filesystem. Please confirm your credentials.', 'userplace');

			// Pass through the error from WP_Filesystem if one was raised.
			if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				$status['errorMessage'] = esc_html($wp_filesystem->errors->get_error_message());
			}

			wp_send_json_error($status);
		}

		$install_status = install_plugin_install_status($api);
		$pagenow = isset($_POST['pagenow']) ? sanitize_key($_POST['pagenow']) : '';

		// If installation request is coming from import page, do not return network activation link.
		$plugins_url = ('import' === $pagenow) ? admin_url('plugins.php') : network_admin_url('plugins.php');

		if (current_user_can('activate_plugin', $install_status['file']) && is_plugin_inactive($install_status['file'])) {
			$status['activateUrl'] = add_query_arg(array(
				'_wpnonce' => wp_create_nonce('activate-plugin_' . $install_status['file']),
				'action'   => 'activate',
				'plugin'   => $install_status['file'],
			), $plugins_url);
		}

		if (is_multisite() && current_user_can('manage_network_plugins') && 'import' !== $pagenow) {
			$status['activateUrl'] = add_query_arg(array('networkwide' => 1), $status['activateUrl']);
		}

		wp_send_json_success($status);
	}

	public function userplace_user_follow()
	{

		if (!empty($_POST) && check_admin_referer('nonce_userplace_follow_button', 'userplace_follow_button')) {
			$posted = $_POST;
			unset($posted['submit']);
			unset($posted['userplace_follow_button']);
			unset($posted['_wp_http_referer']);

			$following_user_id 	= intval($_POST['following_user_id']);
			$current_user_id    = get_current_user_id();

			$this->userplace_follow_following($current_user_id, $following_user_id);

			$current_following_list = get_user_meta($current_user_id, 'following_list', true);
			$current_following_list = $current_following_list === '' ? [] : $current_following_list;
			if (in_array($following_user_id,  $current_following_list)) {
				$following = 'Following';
			} else {
				$following = 'Follow';
			}

			echo json_encode(array('status_code' => 200, 'button_text' => $following));
		}

		wp_die();
	}

	public function userplace_user_follow_ajax()
	{

		if (isset($_POST['nonce_userplace_follow_button']) || wp_verify_nonce($_POST['nonce_userplace_follow_button'], 'userplace_follow_button')) {
			$following_user_id 	= intval($_POST['following_user_id']);
			$current_user_id    = get_current_user_id();

			$this->userplace_follow_following($current_user_id, $following_user_id);

			$current_following_list = get_user_meta($current_user_id, 'following_list', true);
			$current_following_list = $current_following_list === '' ? [] : $current_following_list;
			if (in_array($following_user_id,  $current_following_list)) {
				$following = 'Following';
			} else {
				$following = 'Follow';
			}

			echo json_encode(array('status_code' => 200, 'button_text' => $following));
		}

		wp_die();
	}

	public function userplace_follow_following($current_user_id, $following_user_id)
	{
		$current_following_list = get_user_meta($current_user_id, 'following_list', true);

		$current_follower_list = get_user_meta($following_user_id, 'follower_list', true);
		$current_following_list = $current_following_list === '' ? [] : $current_following_list;
		$current_follower_list = $current_follower_list === '' ? [] : $current_follower_list;

		if (in_array($current_user_id, $current_follower_list) || in_array($following_user_id, $current_following_list)) {
			$following_key = array_search($following_user_id, $current_following_list);
			if (false !== $following_key) {
				unset($current_following_list[$following_key]);
			}
			$follower_key = array_search($current_user_id, $current_follower_list);
			if (false !== $follower_key) {
				unset($current_follower_list[$follower_key]);
			}
		} else {
			$current_following_list[] = $following_user_id;
			$current_follower_list[] = $current_user_id;
		}
		update_user_meta($current_user_id, 'following_list', $current_following_list);
		update_user_meta($following_user_id, 'follower_list', $current_follower_list);
	}

	/**
	 * Send email from quick contact
	 */
	public function userplace_send_listing_mail()
	{

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// check ajax refer
			check_ajax_referer('nonce-contact-form', 'security');
			$to      = sanitize_email($_POST["listingContactAuthor"]);
			$name    = sanitize_text_field($_POST["listingContactName"]);
			$from    = sanitize_email($_POST["listingContactEmail"]);
			$subject = get_bloginfo('name') . ': ' . esc_html__('Listing Contact', 'userplace');
			$message = sanitize_textarea_field($_POST["listingContactMessage"]);

			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
			$headers .= "From: " . $name . " <" . $from . ">" . "\r\n";

			// Send the test mail
			$result = wp_mail($to, $subject, $message, $headers);

			if ($result) {
				echo json_encode(array('message' => 'Thanks! Your email has been sent.', 'status_code' => 200));
			} else {
				echo json_encode(array('message' => 'Sorry! Something went wrong!', 'status_code' => 500));
			}

			wp_die();
		}
	}

	/**
	 * Handles the AJAX request for my plugin.
	 */
	public function userplace_payment_handle_ajax()
	{
		// Make sure we are getting a valid AJAX request
		check_ajax_referer(self::NONCE, 'nonce');
		$ajax_data = $_POST;
		unset($ajax_data['nonce']);
		unset($ajax_data['action']);
		switch ($ajax_data['action_type']) {
			case 'update_option':
				$this->update_option($ajax_data);
				break;
			case 'save_post':
				$this->save_post($ajax_data);
				break;
			case 'update_post':
				$this->update_post($ajax_data);
				break;
			case 'update_term':
				$this->update_term($ajax_data);
				break;
			case 'user_profile_update':
				$this->user_profile_update($ajax_data);
				break;
			case 'registration_user':
				$this->registration_user($ajax_data);
				break;
			case 'login_user':
				$this->login_user($ajax_data);
				break;
			case 'comment_status':
				$this->comment_status($ajax_data);
				break;
			case 'payment':
				$this->handle_payment($ajax_data);
				break;
			case 'fetch_post_data':
				$this->handle_fetch_post_data($ajax_data);
				break;
			case 'fetch_user_data':
				$this->handle_fetch_user_data();
				break;
			case 'delete_post':
				$this->delete_post($ajax_data);
				break;
			case 'reset_password':
				$this->reset_password($ajax_data);
				break;
			case 'set_new_password':
				$this->set_new_password($ajax_data);
				break;
			case 'change_password':
				$this->change_password($ajax_data);
				break;
			case 'user_personal_profile_update':
				$this->user_personal_profile_update($ajax_data);
				break;
			case 'go_as_you_pay':
				$this->go_as_you_pay($ajax_data);
				break;
			case 'change_post_status':
				$this->change_post_status($ajax_data);
				break;
			case 'request_listing_child_post':
				$this->request_listing_child_post($ajax_data);
				break;
			case 'request_more_posts':
				$this->request_more_posts($ajax_data);
				break;
			case 'delete_card':
				$this->delete_card($ajax_data);
				break;
			case 'make_default_card':
				$this->make_default_card($ajax_data);
				break;
		}

		die();
	}

	public function make_default_card($data)
	{
		if (isset($data['cardId']) && $data['cardId'] != '') {
			$default = userplace_make_default_card($data['cardId']);
			echo json_encode($default);
		} else {
			echo json_encode(array('deleted' => false));
		}
	}
	public function delete_card($data)
	{
		if (isset($data['cardId']) && $data['cardId'] != '') {
			$is_deleted = userplace_delete_card($data['cardId']);
			echo json_encode($is_deleted);
		} else {
			echo json_encode(array('deleted' => false));
		}
	}

	public function request_listing_child_post($ajax_data)
	{
		$parent_post_info = userplace_get_parent_post_thumb_info(intval($ajax_data['data']['id']));

		// below commented line is for sublisting data
		// $subListingData = userplace_get_sublisting_posts($ajax_data['data']['id']);


		$parent_post_info['subLisitngData'] = array();
		echo json_encode($parent_post_info);
	}

	public function request_more_posts($ajax_data)
	{
		$posts = userplace_get_user_posts(sanitize_text_field($ajax_data['data']['postType']), sanitize_text_field($ajax_data['data']['currentPosts']));
		foreach ($posts as $post) {
			$pre_value 			= get_post_meta($post->ID, 'pre_value', true);
			$post->pre_value 	= json_encode($pre_value);
		}
		echo json_encode($posts);
	}

	public function user_personal_profile_update($ajax_data)
	{
		if (!isset($ajax_data['data']['user_custom_gravater'])) {
			$ajax_data['data']['user_custom_gravater'] = '';
		}
		$user_id 	= $ajax_data['userId'];
		$metadata 	= $ajax_data['data'];
		if (is_array($metadata)) {
			foreach ($metadata as $meta_key => $meta_value) {
				update_user_meta($user_id, sanitize_key($meta_key), sanitize_text_field($meta_value));
			}
		}
	}

	public function change_post_status($ajax_data)
	{
		$update_post_data = array(
			'ID' => intval($ajax_data['data']['id']),
			'post_status' => sanitize_text_field($ajax_data['data']['status'])
		);

		$post_id = wp_update_post($update_post_data);
		if ($post_id) {
			echo json_encode(array('id' => $post_id));
		} else {
			echo json_encode(array('error' => 404));
		}
	}

	public function go_as_you_pay($ajax_data)
	{
		$url = 'https://userplace.dev/console/submission-restrictions/';
		// wp_redirect(esc_url( add_query_arg( 'variable_to_send', $ajax_data, $url ) ));
		wp_redirect($url);
		exit;
		// update_option('submission_prevalue', $ajax_data);
		$payAsUGo 		= new PayAsUGo();
		$post_type 		= isset($ajax_data['data']['postType']) ? sanitize_text_field($ajax_data['data']['postType']) : null;
		$listing_cost 	= 0;
		if ($post_type == null) {
			$results = array(
				'code' => 400,
				'message' => 'Something went wrong'
			);
			echo  json_encode($results);
			return;
		} else {
			$restrictions = $payAsUGo->get_pay_as_u_go_restrictions($post_type);
			if (isset($restrictions) && is_array($restrictions)) {
				$listing_cost += $restrictions['base_listing_rate'];
				$listing_cost += sanitize_text_field($ajax_data['data']['value']['attachments']) * $restrictions['rate_per_media'];
				$selected_terms = isset($ajax_data['data']['value']['selectedTerms']) ? $ajax_data['data']['value']['selectedTerms'] : array();
				foreach ($selected_terms as $key => $singleTerm) {
					if (array_key_exists($singleTerm, $restrictions['cost_for_specific_term'])) {
						$listing_cost += $restrictions['cost_for_specific_term'][$singleTerm];
					} else {
						$listing_cost += $restrictions['per_term_rate'];
					}
				}
			}
		}

		echo do_shortcode('[userplace_single_payment]');
	}

	/**
	 * Handle Saving Options Data
	 */
	private function update_option($options)
	{
		unset($options['action_type']);
		if (is_array($options)) {
			foreach ($options as $key => $option) {
				if (isset($option)) {
					$option = stripslashes_deep($option);
					update_option(sanitize_key($key), sanitize_text_field($option));
				}
			}
		}
	}

	/**
	 * Handle Saving Post Data
	 */
	private function save_post($post_data)
	{
		global $wpdb;
		unset($post_data['action_type']);

		$post 					= $post_data['data']['value'];
		$post_status 			= $post_data['data']['status'];
		$global_settings 		= $post_data['data']['settings'];
		$user_id 				= get_current_user_id();
		$restricted_data 		= isset($post_data['rulesComparedData']) ? $post_data['rulesComparedData'] : array();
		$can_user_submit_post 	= true;

		if (isset($can_user_submit_post) && $can_user_submit_post) {
			if (isset($global_settings['trigger']) && $global_settings['trigger'] === 'save_post') {
				if (is_array($global_settings['fieldSettings'])) {
					foreach ($global_settings['fieldSettings'] as $value) {
						if (isset($value['postDestination']) && $value['postDestination'] == 'taxonomies') {
							$fields_by_destinations['taxonomies'][] = $value;
						}
						if (isset($value['postDestination']) && $value['postDestination'] == 'post_keys') {
							$fields_by_destinations['post_keys'][] = $value;
						}
						if (isset($value['postDestination']) && $value['postDestination'] == 'meta_keys') {
							$fields_by_destinations['meta_keys'][] = $value;
						}
					}
				}
				if (isset($fields_by_destinations) && array_key_exists('post_keys', $fields_by_destinations)) {
					if (is_array($fields_by_destinations['post_keys'])) {
						foreach ($fields_by_destinations['post_keys'] as $post_keys) {
							$args[$post_keys['saveKey']] = $post[$post_keys['fieldKey']];
						}
					}
					$args['post_type'] 		= $global_settings['postType'];
					$args['post_author'] 	= $user_id;
					$args['post_status'] 	= $post_status;

					$post_id = wp_insert_post($args);
				}
				update_post_meta($post_id, 'pre_value', $post);
				update_post_meta($post_id, 'userplace_calculated_data_restrictions', $restricted_data);
				update_post_meta($post_id, 'is_pay_as_u_go', 'false');
				if (isset($fields_by_destinations) && array_key_exists('taxonomies', $fields_by_destinations)) {
					if (is_array($fields_by_destinations['taxonomies'])) {
						foreach ($fields_by_destinations['taxonomies'] as $taxonomies) {
							$term_lists = explode(',', $post[$taxonomies['fieldKey']]);
							wp_set_object_terms($post_id, $term_lists, $taxonomies['saveKey']);
						}
					}
				}
				$this->prepare_value_for_backend_render($post_id, $post, $global_settings);
				if (isset($fields_by_destinations) && array_key_exists('meta_keys', $fields_by_destinations)) {
					if (is_array($fields_by_destinations['meta_keys'])) {
						foreach ($fields_by_destinations['meta_keys'] as $meta_keys) {
							if (isset($meta_keys['type']) && $meta_keys['type'] == 'geobox') {
								userplace_reactive_lat_long($post_id, $post[$meta_keys['fieldKey']]);
							}
							if (isset($meta_keys['saveKey']) && $meta_keys['saveKey'] == '_thumbnail_id') {
								if (isset($post[$meta_keys['fieldKey']]) && !empty($post[$meta_keys['fieldKey']])) {
									update_post_meta($post_id, $meta_keys['saveKey'], $post[$meta_keys['fieldKey']][0]['id']);
								}
							} else {
								if (isset($meta_keys['saveKey']) && $meta_keys['saveKey'] == 'custom_type') {
									update_post_meta($post_id, $meta_keys['value'], $post[$meta_keys['fieldKey']]);
								} else {
									update_post_meta($post_id, $meta_keys['saveKey'], $post[$meta_keys['fieldKey']]);
								}
							}
						}
					}
				}
			}
			$post_object = get_post($post_id);
			$post_object->pre_value = json_encode($post);
			$post_object->restriction_redirect_url = site_url() . '/console/submission-restrictions/?listing_id=' . $post_id . '';
			echo json_encode($post_object);
		} else {
			echo json_encode(array('error' => 404));
		}
	}

	/**
	 * Handle Saving Term Data
	 */
	private function update_term($term_data)
	{
		unset($term_data['action_type']);
	}
	/**
	 * Handle User Registration
	 */
	private function registration_user($user_registration)
	{
		unset($user_registration['action_type']);

		$userdata = $user_registration['data']['value'];
		$global_settings = $user_registration['data']['settings'];
		if (is_array($global_settings['fieldSettings'])) {
			foreach ($global_settings['fieldSettings'] as $value) {
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_keys') {
					$fields_by_destinations['user_keys'][] = $value;
				}
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_meta_keys') {
					$fields_by_destinations['user_meta_keys'][] = $value;
				}
			}
		}
		$user = $this->make_post_destination_aray_by_key('user_keys', $fields_by_destinations, $userdata);
		$user_id = wp_insert_user($user);
		if (is_wp_error($user_id))
			echo	esc_html__('UserName or Email Already Exists or Empty', 'userplace');

		if (isset($fields_by_destinations) && array_key_exists('user_meta_keys', $fields_by_destinations)) {
			if (is_array($fields_by_destinations['user_meta_keys'])) {
				foreach ($fields_by_destinations['user_meta_keys'] as $meta_key) {
					if (isset($meta_key['type']) && ($meta_key['type'] == 'imageupload' || $meta_key['type'] == 'fileupload')) {
						foreach ($userdata[$meta_key['fieldKey']] as $key => $file) {
							$files[] = $file['id'];
						}
						$comma_seperated_files = implode(',', $files);
						if (isset($meta_key['saveKey']) && $meta_key['saveKey'] == 'custom_type') {
							$meta_array[$meta_key['value']] = $comma_seperated_files;
						} else {
							$meta_array[$meta_key['saveKey']] = $comma_seperated_files;
						}
					} else {
						if (isset($meta_key['saveKey']) && $meta_key['saveKey'] == 'custom_type') {
							$meta_array[$meta_key['value']] = $userdata[$meta_key['fieldKey']];
						} else {
							$meta_array[$meta_key['saveKey']] = $userdata[$meta_key['fieldKey']];
						}
					}
				}
			}
			userplace_add_bulk_meta('user', $user_id, $meta_array);
		}
		if (isset($user_id)) {
			wp_set_auth_cookie($user_id);
		}
	}
	/**
	 * Handle User Login
	 */
	private function login_user($user_login)
	{
		unset($user_login['action_type']);
		$userdata = $user_login['data']['value'];
		$global_settings = $user_login['data']['settings'];
		if (isset($global_settings['fieldSettings']) && is_array($global_settings['fieldSettings'])) {
			foreach ($global_settings['fieldSettings'] as $value) {
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_keys') {
					$fields_by_destinations['user_keys'][] = $value;
				}
			}
		}
		if (isset($fields_by_destinations) && array_key_exists('user_keys', $fields_by_destinations)) {
			if (isset($fields_by_destinations['user_keys']) && is_array($fields_by_destinations['user_keys'])) {
				foreach ($fields_by_destinations['user_keys'] as $post_keys) {
					$user_cred[$post_keys['saveKey']] = $userdata[$post_keys['fieldKey']];
				}
			}
		}
		$user = wp_signon($user_cred, false);
		if (is_wp_error($user))
			echo esc_html__('Invalid UserName or Password.', 'userplace');
	}
	/**
	 * Handle Comment Status
	 */
	private function comment_status($comment)
	{
		unset($comment['action_type']);
		wp_set_comment_status($comment['comment_id'], $comment['comment_status']);
	}

	private function handle_payment($payment_data)
	{
		unset($payment_data['action_type']);
	}

	private function handle_fetch_post_data($data)
	{
		$taxonomies = get_object_taxonomies($data['postType']);
		$post_keys = array(
			'title' 	=> 'post_title',
			'content' 	=> 'post_content',
			'excerpt' 	=> 'post_excerpt',
		);
		$meta_keys = $this->get_meta_keys($data['postType']);
		$post_data = array(
			'taxonomies' 	=> $taxonomies,
			'post_keys' 	=> $post_keys,
			'meta_keys' 	=> $meta_keys,
		);
		echo json_encode($post_data);
	}

	private function get_meta_keys($post_type)
	{
		global $wpdb;
		$query = $wpdb->prepare("SELECT DISTINCT pm.meta_key FROM {$wpdb->posts} post INNER JOIN
			{$wpdb->postmeta} pm ON post.ID = pm.post_id WHERE post.post_type='%s'", $post_type);
		$result = $wpdb->get_results($query, 'ARRAY_A');
		return $all_keys = $this->get_key_value($result);
	}

	private function get_key_value($result)
	{
		$keys = array();
		if (!empty($result) && is_array($result)) {
			foreach ($result as $res) {
				if (!in_array($res['meta_key'], $keys)) {
					$keys[] = $res['meta_key'];
				}
			}
		}
		$keys[] = '_thumbnail_id';
		return $keys;
	}
	private function handle_fetch_user_data()
	{
		global $wpdb;
		$query 				= "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta";
		$usermeta 			= $wpdb->get_results($query, 'ARRAY_A');
		$user_meta_keys 	= $this->get_key_value($usermeta);
		$user_meta_keys[] 	= 'user_custom_gravater';
		$user_meta_keys[] 	= 'user_first_name';
		$user_meta_keys[] 	= 'user_last_name';
		$user_keys 			= $this->get_user_keys();
		$user_keys[2] 		= 'user_password';
		$user_keys[] 		= 'remember';
		$user_data 			= array(
			'user_keys' 		=> $user_keys,
			'user_meta_keys' 	=> $user_meta_keys,
		);

		echo json_encode($user_data);
	}

	private function get_user_keys()
	{
		global $wpdb;
		return $user_keys = $wpdb->get_col(' DESC wp_users', 0);
	}

	public function update_post($post)
	{
		unset($post['action_type']);
		$post_id 				= $post['data']['id'];
		$post_values 			= $post['data']['value'];
		$post_status 			= $post['data']['status'];
		$global_settings 		= $post['data']['settings'];
		$user_id 				= get_current_user_id();
		$restricted_data 		= isset($post['rulesComparedData']) ? $post['rulesComparedData'] : array();
		$can_user_update_post 	= true;

		if (isset($can_user_update_post) && $can_user_update_post) {
			$this->save_ajax_post($post_id, $post_values, $post_status, $global_settings, $user_id, $restricted_data);
			$post_object 			= get_post($post_id);
			$post_object->pre_value = json_encode($post_values);
			$post_object->restriction_redirect_url = site_url() . '/console/submission-restrictions/?listing_id=' . $post_id . '';
			echo json_encode($post_object);
		} else {
			echo json_encode(array('error' => 404));
		}
	}

	private function make_post_destination_aray_by_key($key, $post_destinations, $data)
	{
		$args = array();
		if (isset($post_destinations) && array_key_exists($key, $post_destinations)) {
			if (isset($post_destinations[$key]) && is_array($post_destinations[$key])) {
				foreach ($post_destinations[$key] as $post_keys) {
					if (isset($post_keys['type']) && ($post_keys['type'] == 'imageupload' || $post_keys['type'] == 'fileupload')) {
						if (isset($userdata[$post_keys['fieldKey']]) && is_array($userdata[$post_keys['fieldKey']])) {
							foreach ($userdata[$post_keys['fieldKey']] as $key => $file) {
								$files[] = $file['id'];
							}
						}
						$comma_seperated_files = implode(',', $files);
						$args[$post_keys['saveKey']] = $comma_seperated_files;
					} else {
						if (isset($post_keys['saveKey']) && $post_keys['saveKey'] == 'user_password') {
							$post_keys['saveKey'] = 'user_pass';
						}
						$args[$post_keys['saveKey']] = $data[$post_keys['fieldKey']];
					}
				}
			}
			return $args;
		}
	}

	private function save_ajax_post($post_id, $post, $status, $global_settings, $user_id, $restricted_data)
	{
		global $wpdb;
		if (isset($global_settings['trigger']) && $global_settings['trigger'] === 'save_post') {
			if (isset($global_settings['fieldSettings']) && is_array($global_settings['fieldSettings'])) {
				foreach ($global_settings['fieldSettings'] as $value) {
					if (isset($value['postDestination']) && $value['postDestination'] == 'taxonomies') {
						$fields_by_destinations['taxonomies'][] = $value;
					}
					if (isset($value['postDestination']) && $value['postDestination'] == 'post_keys') {
						$fields_by_destinations['post_keys'][] = $value;
					}
					if (isset($value['postDestination']) && $value['postDestination'] == 'meta_keys') {
						$fields_by_destinations['meta_keys'][] = $value;
					}
				}
			}
			if (isset($fields_by_destinations) && array_key_exists('post_keys', $fields_by_destinations)) {
				if (isset($fields_by_destinations['post_keys']) && is_array($fields_by_destinations['post_keys'])) {
					foreach ($fields_by_destinations['post_keys'] as $post_keys) {
						$args[$post_keys['saveKey']] = $post[$post_keys['fieldKey']];
					}
				}
				$args['post_type'] 		= $global_settings['postType'];
				$args['post_author'] 	= $user_id;
				$args['post_status'] 	= $status;
				$args['ID'] 			= $post_id;
				wp_update_post($args);
			}
			update_post_meta($post_id, 'pre_value', $post);
			update_post_meta($post_id, 'userplace_calculated_data_restrictions', $restricted_data);
			update_post_meta($post_id, 'is_pay_as_u_go', 'false');
			if (isset($fields_by_destinations) && array_key_exists('taxonomies', $fields_by_destinations)) {
				if (isset($fields_by_destinations['taxonomies']) && is_array($fields_by_destinations['taxonomies'])) {
					foreach ($fields_by_destinations['taxonomies'] as $taxonomies) {
						$term_lists = explode(',', $post[$taxonomies['fieldKey']]);
						wp_set_object_terms($post_id, $term_lists, $taxonomies['saveKey']);
					}
				}
			}

			$this->prepare_value_for_backend_render($post_id, $post, $global_settings);
			if (isset($fields_by_destinations) && array_key_exists('meta_keys', $fields_by_destinations)) {
				if (isset($fields_by_destinations['meta_keys']) && is_array($fields_by_destinations['meta_keys'])) {
					foreach ($fields_by_destinations['meta_keys'] as $meta_keys) {
						if (isset($meta_keys['type']) && $meta_keys['type'] == 'geobox') {
							userplace_reactive_lat_long($post_id, $post[$meta_keys['fieldKey']]);
						}
						if (isset($meta_keys['saveKey']) && $meta_keys['saveKey'] == '_thumbnail_id') {
							if (!empty($post[$meta_keys['fieldKey']])) {
								update_post_meta($post_id, $meta_keys['saveKey'], $post[$meta_keys['fieldKey']][0]['id']);
							}
						} else {
							if ($meta_keys['saveKey'] == 'custom_type') {
								update_post_meta($post_id, $meta_keys['value'], $post[$meta_keys['fieldKey']]);
							} else {
								update_post_meta($post_id, $meta_keys['saveKey'], $post[$meta_keys['fieldKey']]);
							}
						}
					}
				}
			}
		}
	}

	private function delete_post($post_data)
	{
		unset($post_data['action_type']);
		$post_id = intval($post_data['data']['id']);
		if (isset($post_id)) {
			wp_delete_post($post_id);
		}
	}

	private function user_profile_update($user_data)
	{
		unset($user_data['action_type']);
		$userdata = $user_data['data']['value'];
		$global_settings = $user_data['data']['settings'];
		if (isset($global_settings['fieldSettings']) && is_array($global_settings['fieldSettings'])) {
			foreach ($global_settings['fieldSettings'] as $value) {
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_keys') {
					$fields_by_destinations['user_keys'][] = $value;
				}
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_meta_keys') {
					$fields_by_destinations['user_meta_keys'][] = $value;
				}
			}
		}
		$user = $this->make_post_destination_aray_by_key('user_keys', $fields_by_destinations, $userdata);
		$user['ID'] = userplace_get_current_user_id();
		if (isset($user) && !empty($user)) {
			wp_update_user($user);
		}
		if (isset($fields_by_destinations) && array_key_exists('user_meta_keys', $fields_by_destinations)) {
			if (isset($fields_by_destinations['user_meta_keys']) && is_array($fields_by_destinations['user_meta_keys'])) {
				foreach ($fields_by_destinations['user_meta_keys'] as $meta_key) {
					if (isset($meta_key['saveKey']) && $meta_key['saveKey'] == 'custom_type') {
						update_user_meta($user['ID'], sanitize_key($meta_key['value']), sanitize_text_field($userdata[$meta_key['fieldKey']]));
					} else {
						update_user_meta($user['ID'], sanitize_key($meta_key['saveKey']), sanitize_text_field($userdata[$meta_key['fieldKey']]));
					}
				}
			}
		}
	}

	private function reset_password($user_data)
	{
		unset($user_data['action_type']);
		$global_settings = $user_data['data']['settings'];
		$userdata = $user_data['data']['value'];
		if (isset($global_settings['fieldSettings']) && is_array($global_settings['fieldSettings'])) {
			foreach ($global_settings['fieldSettings'] as $value) {
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_keys') {
					$fields_by_destinations['user_keys'][] = $value;
				}
			}
		}
		if (isset($fields_by_destinations) && array_key_exists('user_keys', $fields_by_destinations)) {
			if (isset($fields_by_destinations['user_keys']) && is_array($fields_by_destinations['user_keys'])) {
				foreach ($fields_by_destinations['user_keys'] as $user_key) {
					if (isset($user_key['saveKey']) && $user_key['saveKey'] == 'user_email') {
						$user_email = $userdata[$user_key['fieldKey']];
					}
				}
			}
		}
		$user = get_user_by('email', $user_email);
		$errors = userplace_send_password_reset_link($user);
		if ($errors != 'true')
			echo esc_html($errors);
		else {
			echo esc_html('Mail Sent Successful', 'userplace');
		}
	}

	private function set_new_password($user_data)
	{
		$request_url = esc_url_raw($_SERVER["HTTP_REFERER"]);
		$data = parse_url(urldecode($request_url));
		parse_str($data['query'], $query);
		unset($user_data['action_type']);
		$global_settings = $user_data['data']['settings'];
		$userdata = $user_data['data']['value'];
		if (isset($global_settings['fieldSettings']) && is_array($global_settings['fieldSettings'])) {
			foreach ($global_settings['fieldSettings'] as $value) {
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_keys') {
					$fields_by_destinations['user_keys'][] = $value;
				}
			}
		}
		if (isset($fields_by_destinations) && array_key_exists('user_keys', $fields_by_destinations)) {
			if (isset($fields_by_destinations['user_keys']) && is_array($fields_by_destinations['user_keys'])) {
				foreach ($fields_by_destinations['user_keys'] as $user_key) {
					if (isset($user_key['saveKey']) && $user_key['saveKey'] == 'user_password') {
						$user_new_password = $userdata[$user_key['fieldKey']];
					}
				}
			}
		}
		$user = check_password_reset_key($query['key'], $query['login']);
		if (!$user || is_wp_error($user)) {
			if ($user && $user->get_error_code() === 'expired_key') {
				echo esc_html('Expired key', 'userplace');
			} else {
				echo esc_html('Invalid key', 'userplace');
			}
			exit;
		}

		reset_password($user, $user_new_password);
	}

	private function change_password($user_data)
	{
		unset($user_data['action_type']);
		$global_settings = $user_data['data']['settings'];
		$userdata = $user_data['data']['value'];
		if (isset($global_settings['fieldSettings']) && is_array($global_settings['fieldSettings'])) {
			foreach ($global_settings['fieldSettings'] as $value) {
				if (isset($value['postDestination']) && $value['postDestination'] == 'user_keys') {
					$fields_by_destinations['user_keys'][] = $value;
				}
			}
		}
		if (isset($fields_by_destinations) && array_key_exists('user_keys', $fields_by_destinations)) {
			if (isset($fields_by_destinations['user_keys']) && is_array($fields_by_destinations['user_keys'])) {
				foreach ($fields_by_destinations['user_keys'] as $user_key) {
					if (isset($user_key['saveKey']) && $user_key['saveKey'] == 'user_password') {
						$user_new_password = $userdata[$user_key['fieldKey']];
					}
				}
			}
		}
		$user_id = get_current_user_id();
		wp_set_password($user_new_password, $user_id);
	}

	private function prepare_value_for_backend_render($post_id, $post, $global_settings)
	{
		global $wpdb;
		// Find Out all the registered metabox for this post type
		$meta_query = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta}
			WHERE meta_key = %s and
			meta_value = %s", 'userplace_post_type_select', $global_settings['postType']);
		$metabox_list = $wpdb->get_results($meta_query, 'ARRAY_A');
		if (isset($metabox_list) && is_array($metabox_list)) {
			// Loop all metabox
			foreach ($metabox_list as $key => $meta) {
				$meta_box = get_post_meta($meta['post_id'], '_userplace_metabox_builder_output', true);
				if (isset($meta_box)) {
					$meta_box_object = json_decode($meta_box);
					//get all the fields for each metabox
					$meta_box_fileds = $meta_box_object->formBuilder->fields;
					if (isset($meta_box_fileds) && is_array($meta_box_fileds)) {
						// Loop all metabox fields and save the meta values to the meta_key so in backend the data could be re-rendered
						foreach ($meta_box_fileds as $key => $fields) {
							$backend_meta[$fields->id] = $post[$fields->id];
						}
					}
				}
				//Find Out the dynamic metabox value saving key
				$metabox_title 		= get_the_title($meta['post_id']);
				$generated_id 		= str_replace(' ', '_', strtolower($metabox_title));
				$dynamic_input_name = '_userplace_dynamic_meta_data_' . $generated_id;
				// save meta to the dynamic metakey for backend rerender
				update_post_meta($post_id, sanitize_key($dynamic_input_name), sanitize_text_field(addslashes(json_encode($backend_meta))));
			}
		}
	}
}
