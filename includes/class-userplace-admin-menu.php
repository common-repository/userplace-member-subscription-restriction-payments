<?php

/**
 *
 */

namespace Userplace;

class Admin_Menu
{

	public function __construct()
	{
		add_action('admin_menu', array($this, 'userplace_register_menu'), 9);
		add_action('admin_init', array($this, 'remove_duplicate_submenu'));
	}

	function remove_duplicate_submenu()
	{
		global $submenu;
		if (isset($submenu['userplace'], $submenu['userplace'][0], $submenu['userplace'][0][0]))
			$submenu['userplace'][0][0]  = esc_html__('Welcome', 'userplace');
	}

	public function userplace_register_menu()
	{

		add_menu_page(
			$page_title 	= esc_html__('Userplace', 'userplace'),
			$menu_title		= esc_html__('Userplace', 'userplace'),
			$capability 	= 'manage_options',
			$menu_slug 		= 'userplace',
			$function 		= array($this, 'userplace_welcome'),
			$icon_url 		= 'dashicons-screenoptions',
			$position		= 59
		);

		add_submenu_page(
			$parent_slug 	= 'userplace',
			$page_title 	= esc_html__('Settings', 'userplace'),
			$menu_title 	= esc_html__('Settings', 'userplace'),
			$capability 	= 'manage_options',
			$menu_slug 		= 'userplace_settings',
			$function 		= array($this, 'userplace_settings')
		);

		add_submenu_page(
			$parent_slug 	= 'userplace',
			$page_title 	= esc_html__('Payments', 'userplace'),
			$menu_title 	= esc_html__('Payments', 'userplace'),
			$capability 	= 'manage_options',
			$menu_slug 		= 'userplace_payment_settings',
			$function 		= array($this, 'userplace_payment_settings')
		);

		add_submenu_page(
			$parent_slug 	= 'userplace',
			$page_title 	= esc_html__('System Status', 'userplace'),
			$menu_title 	= esc_html__('System Status', 'userplace'),
			$capability 	= 'manage_options',
			$menu_slug 		= 'userplace_system_status',
			$function 		= array($this, 'userplace_system_status')
		);
		add_submenu_page(
			$parent_slug 	= 'userplace',
			$page_title 	= esc_html__('Configuration Status', 'userplace'),
			$menu_title 	= esc_html__('Configuration Status', 'userplace'),
			$capability 	= 'manage_options',
			$menu_slug 		= 'userplace_configuration_status',
			$function 		= array($this, 'userplace_configuration_status')
		);
	}
	public function userplace_welcome()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'userplace'));
		}
		include_once(USERPLACE_DIR . '/admin-templates/welcome.php');
	}

	public function userplace_settings()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'userplace'));
		}
		include_once(USERPLACE_DIR . '/admin-templates/settings.php');
	}
	public function userplace_payment_settings()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'userplace'));
		}
		include_once(USERPLACE_DIR . '/admin-templates/payment_settings.php');
	}
	public function userplace_system_status()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'userplace'));
		}
		include_once(USERPLACE_DIR . '/admin-templates/userplace-system-status.php');
	}
	public function userplace_configuration_status()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'userplace'));
		}
		include_once(USERPLACE_DIR . '/admin-templates/userplace-configuration-status.php');
	}
}
