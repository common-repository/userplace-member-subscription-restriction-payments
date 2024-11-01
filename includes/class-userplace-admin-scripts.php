<?php

/**
 *
 */

namespace Userplace;

use Userplace\Admin_Lacalize;

class Admin_Scripts
{

	use Payment_Info;

	protected $custom_scripts = array(
		array(
			'post_type'		=> 'userplace_plan',
			'js_file_name'	=> 'userplace_gateway_plan',
		),
		array(
			'post_type'		=> 'userplace_plan',
			'js_file_name'	=> 'userplace_payment_plan_restrictions',
		),
		array(
			'post_type'		=> 'userplace_page_userplace_payment_settings',
			'js_file_name'	=> 'userplace_payment_settings',
		),
		array(
			'post_type'		=> 'userplace_page_userplace_settings',
			'js_file_name'	=> 'userplace_settings',
		),
		array(
			'post_type'		=> 'userplace_payasugo',
			'js_file_name'	=> 'userplace_payasugo',
		),
		array(
			'post_type'		=> 'userplace_template',
			'js_file_name'	=> 'userplace_payment_template_settings',
		),
		array(
			'post_type'		=> 'userplace_role',
			'js_file_name'	=> 'userplace_add_role',
		),
		array(
			'post_type'		=> 'userplace_console',
			'js_file_name'	=> 'userplace_console_menu_settings',
		),
		array(
			'post_type'		=> 'userplace_coupon',
			'js_file_name'	=> 'userplace_coupon',
		),
		array(
			'post_type'		=> 'nav-menus.php',
			'js_file_name'	=> 'userplace_adons',
		),
	);

	protected $restricted_post_types = array(
		null,
		'userplace_console',
		'userplace_plan',
		'userplace_template',
		'userplace_post_type',
		'userplace_form_builder',
		'userplace_taxonomy',
		// 'userplace_term_metabox',
		'userplace_metabox',
		'listing_page_userplace_settings',
		'reactive_grid',
	);

	public function __construct()
	{
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_filter('userplace_admin_generator_localize_args', array($this, 'userplace_admin_generator_localize_args'), 10, 1);
	}

	public function admin_enqueue_scripts($hook)
	{

		wp_register_script('reuse-form-variable', USERPLACE_JS_VENDOR . 'reuse-form-variable.js', array(), $ver = true, true);
		wp_enqueue_script('reuse-form-variable');

		wp_register_script('userplace-variable', USERPLACE_JS_VENDOR . 'userplace-variable.js', array(), $ver = false, false);
		wp_enqueue_script('userplace-variable');
		wp_register_script('react', USERPLACE_JS_VENDOR . 'react.min.js', array(), $ver = true, true);
		wp_enqueue_script('react');
		wp_register_script('react-dom', USERPLACE_JS_VENDOR . 'react-dom.min.js', array(), $ver = true, true);
		wp_enqueue_script('react-dom');
		wp_register_script('highlight-pack-js', USERPLACE_JS_VENDOR . 'highlight.pack.min.js', array('jquery'), $ver = true, true);
		wp_enqueue_script('highlight-pack-js');
		wp_register_script('clipboardjs', USERPLACE_JS_VENDOR . 'clipboard.min.js', array('jquery'), $ver = true, true);
		wp_enqueue_script('clipboardjs');
		wp_register_style('plan-url-copy-css', USERPLACE_JS_VENDOR . 'plan-url-copy.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('plan-url-copy-css');
		wp_register_script('plan-url-copy-js', USERPLACE_JS_VENDOR . 'plan-url-copy.js', array('jquery'), false, true);
		wp_enqueue_script('plan-url-copy-js');

		wp_register_script('userplace_admin_menu', USERPLACE_JS_VENDOR . 'admin-menu.js', array('jquery'), false, true);
		wp_enqueue_script('userplace_admin_menu');
		$this->redq_rb_load_reuse_form_scripts();
		$this->load_backend_scripts(USERPLACE_JS);
	}

	public function load_backend_scripts($publicPath)
	{
		wp_register_style('userplace-payment-helper', USERPLACE_CSS . 'userplace-payment-helper.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('userplace-payment-helper');
		wp_register_style('icomoon-css', USERPLACE_JS_VENDOR . 'icomoon.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('icomoon-css');
		wp_register_style('flaticon-css', USERPLACE_JS_VENDOR . 'flaticon.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('flaticon-css');
		wp_register_style('ionicons-css', USERPLACE_JS_VENDOR . 'ionicons.min.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('ionicons-css');
		wp_register_style('font-awesome', USERPLACE_JS_VENDOR . 'font-awesome.min.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('font-awesome');
		// wp_register_style('ionicons-min-css', USERPLACE_JS_VENDOR.'ionicons.min.css', array(), $ver = false, $media = 'all');
		// wp_enqueue_style('ionicons-min-css');
		// All other assets
		$admin_scripts = json_decode(file_get_contents(USERPLACE_FILE . "/resource/admin-assets.json"), true);

		$all_scripts = array();
		$all_scripts = $this->current_scripts();
		foreach ($admin_scripts as $filename => $file) {
			if (in_array($filename, $all_scripts)) {
				wp_register_script($filename, $publicPath . $file['js'], array('jquery', 'underscore', 'wp-color-picker'), $ver = false, true);
				wp_enqueue_script($filename);

				wp_localize_script($filename, 'RE_ICON', array('icon_provider' => apply_filters('reuse_icon_picker',  array()))); // For reuse form

				wp_localize_script($filename, 'USERPLACE_PAYMENT_AJAX_DATA', array(
					'action'	 		=> 'userplace_payment_ajax',
					'nonce' 			=> wp_create_nonce('userplace_payment_ajax_nonce'),
					'admin_url' 		=> admin_url('admin-ajax.php'),
					'ACTIVATE'			=> esc_html__('Activate', 'userplace'),
					'DEACTIVATE'		=> esc_html__('Deactivate', 'userplace'),
					'INSTALLING'		=> esc_html__('Installing...', 'userplace'),
					'DEACTIVATING'		=> esc_html__('Deactivating...', 'userplace'),
					'ACTIVATING'		=> esc_html__('Activating...', 'userplace'),
				));
			}
		}
		$provider = new Admin_Lacalize;
		$info = get_current_screen();
		$current_screen = null;
		if ($info->base == 'post' || $info->base == 'term' || $info->base == 'edit-tags')
			$current_screen = $info->post_type;
		elseif ($info->post_type == null)
			$current_screen = $info->base;
		$post_types = $provider->get_all_posts();
		if (current_user_can('administrator') && array_key_exists($current_screen,  $post_types)) {
			wp_register_script('userplace_restrictions_settings', USERPLACE_JS . $admin_scripts['userplace_restrictions_settings']['js'], array('jquery'), false, true);
			wp_enqueue_script('userplace_restrictions_settings');
		}
	}

	// dynamically load
	public function current_scripts()
	{
		$info = get_current_screen();
		$current_screen = null;
		if ($info->base == 'post' || $info->base == 'term' || $info->base == 'edit-tags')
			$current_screen = $info->post_type;
		elseif ($info->post_type == null)
			$current_screen = $info->base; // take the base when it's a page or options
		$all_scripts = [];
		$custom_scripts = apply_filters('userplace_script_loading_array', $this->custom_scripts);
		$user_id  = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		$restriction_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan);
		if (isset($restriction_details['general']['enable_single_post_restriction']) && $restriction_details['general']['enable_single_post_restriction'] === 'true') {
			$restricted_post_types = isset($restriction_details['general']['single_restriction_enable_post_types']) && $restriction_details['general']['single_restriction_enable_post_types'] != '' ? explode(',', $restriction_details['general']['single_restriction_enable_post_types']) : array();
			foreach ($restricted_post_types as $key => $single_post_type) {
				$custom_scripts[] = array('post_type' => $single_post_type, 'js_file_name' => 'userplace_restrictions_settings');
			}
		}
		foreach ($custom_scripts as $script_name) {
			if ($current_screen == $script_name['post_type']) {
				array_push($all_scripts, $script_name['js_file_name']);
			}
		}
		return $all_scripts;
	}

	public function add_custom_attribute($tag, $handle)
	{
		$all = array_merge($this->custom_scripts, $this->reuse_scripts);
		foreach ($all as $script) {
			if ($script === $handle) {
				return str_replace(' src', ' defer="defer" src', $tag);
			}
		}
		if ($handle === 'reuse_vendor') {
			return str_replace(' src', ' defer="defer" src', $tag);
		}
		// if needed add async in here as defer
		return $tag;
	}

	public function redq_rb_load_reuse_form_scripts()
	{
		if (!is_plugin_active('redq-reuse-form/redq-reuse-form.php')) {

			wp_register_style('reuse-form-two', USERPLACE_CSS . 'reuse-form-two.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('reuse-form-two');
			wp_register_style('reuse-form', USERPLACE_CSS . 'reuse-form.css', array(), $ver = false, $media = 'all');
			wp_enqueue_style('reuse-form');
			$reuse_form_scripts 	= new Reuse_Builder;
			$webpack_public_path 	= get_option('webpack_public_path_url', true);
			$reuse_form_scripts->load($webpack_public_path);
		}
	}

	function userplace_admin_generator_localize_args($args)
	{
		$args['postTypes'] 				=  Admin_Lacalize::get_all_posts();
		$args['taxonomies'] 			=  Admin_Lacalize::get_all_taxonomies();
		$args['LANG'] 					=  Admin_Lacalize::admin_language();
		$args['ERROR_MESSAGE'] 			=  Admin_Lacalize::admin_error();
		$args['_WEBPACK_PUBLIC_PATH_'] 	=  USERPLACE_JS;

		return $args;
	}
}
