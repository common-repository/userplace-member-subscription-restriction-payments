<?php

namespace Userplace;

class Router
{
	/**
	 * Fire hooks
	 */
	public function __construct()
	{
		add_action('init', array($this, 'userplace_add_rule'));
		add_filter('query_vars', array($this, 'userplace_add_query_var'));
		add_filter('template_include', array($this, 'userplace_load_template'));
		// add_action('admin_init', array($this, 'userplace_redirect_admin'));
	}
	/**
	 * Add rewrite rules
	 *
	 * @return void
	 */
	public function userplace_add_rule()
	{
		global $wp_rewrite;
		do_action('userplace_new_rewrite_rules');
		add_rewrite_rule('console/node/([^/]+)', 'index.php?node=$matches[1]', 'top');
		add_rewrite_rule('webhooks', 'index.php?webhooks=yes', 'top');
		add_rewrite_rule('subscription/pay', 'index.php?subscription=yes', 'top');
		add_rewrite_rule('console/change-password', 'index.php?changepassword=yes', 'top');
		add_rewrite_rule('console/billing', 'index.php?billing=yes', 'top');
		add_rewrite_rule('console/user-settings', 'index.php?user-settings=yes', 'top');
		add_rewrite_rule('console', 'index.php?console=yes', 'top');
		add_rewrite_rule('user/([^/]+)', 'index.php?user=$matches[1]', 'top');
		$wp_rewrite->flush_rules();
	}
	/**
	 * Add Query parameter through url
	 *
	 * @param  array  $vars
	 * @return array
	 */
	public function userplace_add_query_var($vars)
	{
		$custom_vars = apply_filters(
			'userplace_query_vars',
			array(
				'console',
				'webhooks',
				'node',
				'billing',
				'user-settings',
				'user',
				'subscription',
				'changepassword',
			)
		);
		return array_merge($vars, $custom_vars);
	}
	/**
	 * Load template with respect to query var
	 *
	 * @param  string $template default template name
	 * @return string           template
	 */
	public function userplace_load_template($template)
	{

		$userplace_settings = json_decode(get_option('userplace_settings'), true);
		$sign_in_page         = (isset($userplace_settings['sign_in'])) ? get_post_field('post_name', $userplace_settings['sign_in']) : '';

		if (get_query_var('webhooks') && get_query_var('webhooks') == 'yes') {
			$template = USERPLACE_DIR . '/includes/payments/webhooks/webhooks.php';
			return $template;
		}

		if (get_query_var('node') && get_query_var('node') != 'yes') {
			if (is_user_logged_in()) {
				$template = USERPLACE_DIR . '/templates/console/dynamic_page.php';
				return $template;
			} else {
				exit(wp_redirect(site_url($sign_in_page)));
			}
		}

		if (get_query_var('console') && get_query_var('console') == 'yes') {
			if (is_user_logged_in()) {
				$user_id = get_current_user_id();
				$status_check = get_user_meta($user_id, 'userplace_user_activity', true);
				$user = wp_get_current_user();
				$template = USERPLACE_DIR . '/templates/console/console.php';
				return $template;
			} else {
				exit(wp_redirect(site_url($sign_in_page)));
			}
		}
		if (get_query_var('billing') && get_query_var('billing') == 'yes') {
			if (is_user_logged_in()) {
				$user_id = get_current_user_id();
				$template = USERPLACE_DIR . '/templates/console/billing.php';
				return $template;
			} else {
				exit(wp_redirect(site_url($sign_in_page)));
			}
		}

		if (get_query_var('changepassword') && get_query_var('changepassword') == 'yes') {
			if (is_user_logged_in()) {
				$user_id = get_current_user_id();
				$template = USERPLACE_DIR . '/templates/console/changepassword.php';
				return $template;
			} else {
				exit(wp_redirect(site_url($sign_in_page)));
			}
		}

		if (get_query_var('user-settings') && get_query_var('user-settings') == 'yes') {
			if (is_user_logged_in()) {
				$template = USERPLACE_DIR . '/templates/console/user-settings-userplacefw-page.php';
				return $template;
			} else {
				exit(wp_redirect(site_url($sign_in_page)));
			}
		}
		if (get_query_var('user') && get_query_var('user') != 'yes') {
			$template = USERPLACE_DIR . '/templates/user-page.php';
			return $template;
		}
		if (get_query_var('subscription') && get_query_var('subscription') == 'yes') {
			$template = USERPLACE_DIR . '/templates/handle-subscription-payment.php';
			return $template;
		}

		$template = apply_filters('userplace_load_router_template', $template, $sign_in_page);

		return $template;
	}

	/**
	 * login redirection
	 * @return [type] [description]
	 */
	public function userplace_redirect_admin()
	{
		if (!current_user_can('administrator')) {
			$has_admin_access = userplace_get_settings('userplace_access_to_admin_dashboard');
			if (!isset($has_admin_access) || $has_admin_access == '' || $has_admin_access == 'false') {
				wp_redirect(site_url() . '/console');
				exit;
			}
		}
	}
}
