<?php

/**
 *
 */

namespace Userplace;

class Payment_Frontend_Scripts
{

	public function __construct()
	{
		add_action('wp_enqueue_scripts', array($this, 're_load_scripts'), 20);
		add_filter('userplace_admin_generator_localize_args', array($this, 'userplace_admin_generator_localize_args'));
	}

	public function re_load_scripts()
	{
		wp_register_style('user-place-login-registration', USERPLACE_JS_VENDOR . 'userplace-login-registration.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('user-place-login-registration');
		wp_enqueue_style('jquerymodalcss', USERPLACE_JS_VENDOR . 'jquery.modal.min.css', array(), false, 'all');
		wp_enqueue_script('jquerymodaljs', USERPLACE_JS_VENDOR . 'jquery.modal.min.js', array('jquery'), false, true);

		$query_var               = get_query_var('console', '');
		$query_var_node          = get_query_var('node', '');
		$query_var_billing       = get_query_var('billing', '');
		$user_settings           = get_query_var('user-settings', '');
		$all_reviews             = get_query_var('all_reviews', '');
		$query_var_user          = get_query_var('user', '');
		$subscription          	 = get_query_var('subscription', '');
		$view_claim          	 = get_query_var('view-claim', '');
		$new_claim          	 = get_query_var('new-claim', '');
		$changepassword          = get_query_var('changepassword', '');
		if ($changepassword || $view_claim || $new_claim || $query_var_user || $query_var === 'yes' || $query_var_billing === 'yes' || $subscription === 'yes' || $user_settings === 'yes' || $all_reviews === 'yes' || ($query_var_node && $query_var_node !== 'yes')) {
			if (!class_exists('Load_Google_Map')) {
				userplace_google_map_scripts();
			}
			wp_register_script('userplace-widget-media-upload', USERPLACE_JS_VENDOR . 'userplace-widget-media-upload.js', array(), false, true);
			wp_enqueue_script('userplace-widget-media-upload');
			wp_enqueue_script('media-upload');
			wp_enqueue_media();
			wp_register_script('frontend-submission-js', USERPLACE_JS_VENDOR . 'frontend-submission.js', array(), $ver = false, $media = 'all');
			wp_enqueue_script('frontend-submission-js');
			wp_register_script('userplace-js', USERPLACE_JS_VENDOR . 'userplace.js', array(), $ver = false, $media = 'all');
			wp_enqueue_script('userplace-js');
			wp_localize_script('userplace-js', 'USERPLACE_PAYMENT_AJAX_DATA', array(
				'action'     => 'userplace_payment_ajax',
				'nonce'      => wp_create_nonce('userplace_payment_ajax_nonce'),
				'admin_url'  => admin_url('admin-ajax.php'),
				'site_url'	 => site_url(),
				'image_path' => USERPLACE_IMG
			));
			wp_register_script('jquery-nicescroll-min', USERPLACE_JS_VENDOR . 'jquery.nicescroll.min.js', array(), $ver = false, $media = 'all');
			wp_enqueue_script('jquery-nicescroll-min');
			wp_register_style('icomoon-css', USERPLACE_JS_VENDOR . 'icomoon.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('icomoon-css');
			wp_register_style('flaticon-css', USERPLACE_JS_VENDOR . 'flaticon.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('flaticon-css');
			wp_register_style('ionicons-css', USERPLACE_JS_VENDOR . 'ionicons.min.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('ionicons-css');
			wp_register_style('font-awesome', USERPLACE_JS_VENDOR . 'font-awesome.min.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('font-awesome');
			wp_register_style('userplace-payment-helper-css', USERPLACE_CSS . 'userplace-payment-helper.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('userplace-payment-helper-css');
		}
		if ($user_settings == 'yes') {
			wp_enqueue_script('mapautocomplete', USERPLACE_JS_VENDOR . 'geocomplete.min.js', array('jquery'), false, true);
		}
	}

	public function redq_rb_load_reuse_form_scripts()
	{
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		if (!is_plugin_active('redq-reuse-form/redq-reuse-form.php')) {

			wp_register_style('reuse-form-two', USERPLACE_CSS . 'reuse-form-two.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('reuse-form-two');
			wp_register_style('reuse-form', USERPLACE_CSS . 'reuse-form.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('reuse-form');
			$reuse_form_scripts = new Reuse_Builder;
			$webpack_public_path = get_option('webpack_public_path_url', true);
			$reuse_form_scripts->load($webpack_public_path);
		}
	}

	public function enqueue_reuse_form_script()
	{
		wp_enqueue_style('icomoon-css');
		wp_enqueue_style('flaticon-css');
		wp_enqueue_style('ionicons-css');
		wp_enqueue_style('font-awesome');
		wp_enqueue_script('reuse-form-variable');
		wp_enqueue_script('react');
		wp_enqueue_script('react-dom');
		wp_enqueue_style('reuse-form-two');
		wp_enqueue_style('reuse-form');
		wp_enqueue_script('reuse_vendor');
		wp_enqueue_script('reusejs');
	}

	function userplace_admin_generator_localize_args($args)
	{
		$args['LANG']           =  Admin_Lacalize::admin_language();
		$args['ERROR_MESSAGE']  =  Admin_Lacalize::admin_error();
		return $args;
	}
}
