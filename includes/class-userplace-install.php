<?php

namespace Userplace;

class Install
{

	/**
	 * Hook in tabs.
	 */
	public static function init()
	{
		// add_action( 'init', array( __CLASS__, 'setup_pages' ), 5 );
	}

	/**
	 * Install Userplace.
	 */
	public static function install()
	{
		if (!is_blog_installed()) {
			return;
		}

		add_option('userplace_do_activation_redirect', true);

		self::setup_pages();
		self::create_templates();
		self::create_roles();
		self::create_all_custom_tables();
		self::create_default_plan();
		self::update_settings();
	}

	public static function update_settings()
	{
		update_option('webpack_public_path_url', USERPLACE_REUSE_VENDOR);
	}

	public static function create_default_plan()
	{
		// Information needed for creating the default plan
		$plan_definitions = array(
			'default-Plan' => array(
				'title'   => esc_html__('Default', 'userplace'),
				'meta'    => array(
					'_userplace_plan_restrictions' 			=> '{"enable":"true","default_plan":"true","userplace_coupon_post_id":"no_coupon","view_restricted_post_types":"","enable_single_post_restriction":"false","single_restriction_enable_post_types":"","post__enable":false,"post__allow_unlimited":false,"post__view_restriction_metabox":false,"post__max_posts":50,"post__max_terms_per_post":50}',
					'userplace_plan_role' 					=> 'userplace_member',
					'_userplace_plan_builder' 				=> '{"plan_id":"default"}',
					'enable' 								=> 'true',
					'default_plan' 							=> 'true',
					'userplace_coupon_post_id' 				=> 'no_coupon',
					'view_restricted_post_types' 			=> '',
					'enable_single_post_restriction' 		=> 'false',
					'single_restriction_enable_post_types' 	=> '',
					'post__enable' 							=> 'false',
					'post__allow_unlimited' 				=> 'false',
					'post__view_restriction_metabox' 		=> 'false',
					'post__max_posts'	 					=> '50',
					'post__max_terms_per_post' 				=> '50',
					'plan_id' 								=> 'default',
				),
			),
		);

		foreach ($plan_definitions as $slug => $plan) {
			// Check that the plan doesn't exist already
			$query = new \WP_Query('post_type=userplace_plan&name=' . $slug);
			if (!$query->have_posts()) {
				// Add the page using the data from the array above
				$post_id = wp_insert_post(
					array(
						'post_name'      => $slug,
						'post_title'     => $plan['title'],
						'post_status'    => 'publish',
						'post_type'      => 'userplace_plan',
						'ping_status'    => 'closed',
						'comment_status' => 'closed',
					)
				);

				// Insert meta information
				foreach ($plan['meta'] as $key => $value) {
					update_post_meta($post_id, $key, $value);
				}
			}
		}
	}

	public static function create_templates()
	{
		// Information needed for creating the plugin's templates
		$template_definitions = array(
			'user-template' => array(
				'title'   => esc_html__('User Template', 'userplace'),
				'content' => '[userplace_user_main_profile]',
				'meta'    => array(
					'_userplace_payment_template_data'        => '{"userplace_payment_template_select_type":"user"}',
					'userplace_payment_template_select_type'  => 'user',
				),
			),
			'console-template' => array(
				'title'   => esc_html__('Console Template', 'userplace'),
				'content' => '[billing_overview][userplace_invoices]',
				'meta'    => array(
					'_userplace_payment_template_data'        => '{"userplace_payment_template_select_type":"console"}',
					'userplace_payment_template_select_type'  => 'console',
				),
			),
		);

		foreach ($template_definitions as $slug => $template) {
			// Check that the template doesn't exist already
			$query = new \WP_Query('post_type=userplace_template&name=' . $slug);
			if (!$query->have_posts()) {
				// Add the page using the data from the array above
				$post_id = wp_insert_post(
					array(
						'post_content'   => $template['content'],
						'post_name'      => $slug,
						'post_title'     => $template['title'],
						'post_status'    => 'publish',
						'post_type'      => 'userplace_template',
						'ping_status'    => 'closed',
						'comment_status' => 'closed',
					)
				);

				// Insert meta information
				foreach ($template['meta'] as $key => $value) {
					update_post_meta($post_id, $key, $value);
				}
			}
		}
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function setup_pages()
	{
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			'member-login' => array(
				'title'             => esc_html__('Sign In', 'userplace'),
				'content'           => '[userplace_login_form]',
				'reuse_form_label'  => 'sign_in',
			),
			'member-register' => array(
				'title'   => esc_html__('Register', 'userplace'),
				'content' => '[userplace_register_form]',
				'reuse_form_label'  => 'register',
			),
			'member-password-lost' => array(
				'title'   => esc_html__('Forgot Your Password?', 'userplace'),
				'content' => '[userplace_password_lost_form]',
				'reuse_form_label'  => 'forgot_your_password',
			),
			'member-password-reset' => array(
				'title'   => esc_html__('Pick a New Password', 'userplace'),
				'content' => '[userplace_password_reset_form]',
				'reuse_form_label'  => 'pick_a_new_password',
			),
			'pricing-plan' => array(
				'title'   => esc_html__('Pricing Plan', 'userplace'),
				'content' => '[userplace_pricing_wrapper][userplace_pricing_plan title="Free" best_choice="" amount="$0" cycle="monthly" column="one"]<ul><li>Feature One</li><li>Feature Two</li><li>Feature Three</li><li>Feature Four</li><li>Cancel anytime</li></ul>[userplace_plan_button class="button" plan_id="default"][/userplace_pricing_plan][/userplace_pricing_wrapper]',
				'reuse_form_label'  => 'userplace_plan_page_url',
			),
		);

		$userplace_settings = json_decode(get_option('userplace_settings'), true);

		foreach ($page_definitions as $slug => $page) {
			global $wpdb;
			// Check that the page doesn't exist already
			$post_if = $wpdb->get_var("SELECT * FROM $wpdb->posts WHERE post_content LIKE '" . $page['content'] . "' AND post_type='page'");
			if (empty($post_if)) {
				// Add the page using the data from the array above
				$post_id = wp_insert_post(
					array(
						'post_content'   => $page['content'],
						'post_name'      => $slug,
						'post_title'     => $page['title'],
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'ping_status'    => 'closed',
						'comment_status' => 'closed',
					)
				);

				$userplace_settings[$page['reuse_form_label']] = "$post_id";
			}
		}

		if (!empty($userplace_settings)) {
			update_option('userplace_settings', json_encode($userplace_settings));
		}
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles()
	{
		global $wp_roles;

		if (!class_exists('WP_Roles')) {
			return;
		}

		if (!isset($wp_roles)) {
			$wp_roles = new \WP_Roles(); // @codingStandardsIgnoreLine
		}

		// Customer role.
		add_role(
			'userplace_member',
			esc_html__('uMember', 'userplace'),
			array(
				'read'          => true,
				'upload_files'  => true,
				'level_1'       => true,
			)
		);

		update_option('default_role', 'userplace_member');

		// Information needed for creating the default role
		$role_definitions = array(
			'userplace_member' => array(
				'title'   => esc_html__('uMember', 'userplace'),
				'meta'    => array(
					'_userplace_add_new_role_settings' => '{"user_role_capabilities":"upload_files,read,level_1"}',
				),
			),
		);

		foreach ($role_definitions as $slug => $role) {
			// Check that the role doesn't exist already
			$query = new \WP_Query('post_type=userplace_role&name=' . $slug);
			if (!$query->have_posts()) {
				// Add the page using the data from the array above
				$post_id = wp_insert_post(
					array(
						'post_name'      => $slug,
						'post_title'     => $role['title'],
						'post_status'    => 'publish',
						'post_type'      => 'userplace_role',
						'ping_status'    => 'closed',
						'comment_status' => 'closed',
					)
				);

				// Insert meta information
				foreach ($role['meta'] as $key => $value) {
					update_post_meta($post_id, $key, $value);
				}
			}
		}
	}

	public static function create_all_custom_tables()
	{
		global $wpdb;
		$collate = '';

		if ($wpdb->has_cap('collation')) {
			if (!empty($wpdb->charset)) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if (!empty($wpdb->collate)) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		$schema = "CREATE TABLE {$wpdb->prefix}userplace_cards (
      id bigint(200) NOT NULL auto_increment,
      card_id tinytext NOT NULL,
      user tinytext NOT NULL,
      last4 tinytext NOT NULL,
      card_name tinytext NOT NULL,
      card_brand tinytext NOT NULL,
      is_default boolean DEFAULT 0 NOT NULL,
      deleted boolean DEFAULT 0 NOT NULL,
      expired_at tinytext NULL,
      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY  (id)
    ) $collate;
    
    CREATE TABLE {$wpdb->prefix}userplace_invoices (
      id bigint(200) NOT NULL auto_increment,
			transaction_id tinytext NOT NULL,
      customer tinytext NOT NULL,
      amount tinytext NOT NULL,
			plan tinytext NOT NULL,
			last4 tinytext NULL,
			brand tinytext NULL,
      currency tinytext NOT NULL,
      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY  (id)
    ) $collate;
		
    CREATE TABLE {$wpdb->prefix}userplace_logs (
      id bigint(200) NOT NULL auto_increment,
			log_details text NULL,
      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY  (id)
    ) $collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($schema);
	}
}

Install::init();
