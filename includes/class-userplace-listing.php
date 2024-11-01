<?php

/**
 *
 */

namespace Userplace;

class Listing
{

	use Payment_Info;

	public function __construct()
	{
		add_action('init', array($this, 'register_post_type'));
		add_action('add_meta_boxes', array($this, 'add_metabox'));

		// Add a post display state for special Userplace pages.
		add_filter('display_post_states', array($this, 'add_display_post_states'), 10, 2);
	}

	public function register_post_type()
	{
		new Generate_Post_Type(
			apply_filters('userplace_add_new_post_types', array(
				array(
					"name" 				=> "userplace_plan",
					"excludeFromSearch" 		    => true,
					"showInMenu" 	=> 'userplace',
					"showInRest" 	=> true,
					"showName" 		=> esc_html__("Membership Plan", 'userplace'),
					"label" 		=> array(
						'all_items' => esc_html__("Membership Plan", "userplace"),
					),
					'supports' 		=> array(
						'title' 		=> true,
						'editor' 		=> false,
					),
				),
				array(
					"name" 			=> "userplace_template",
					"showInMenu"	=> 'userplace',
					"showName" 		=> esc_html__("Template", "userplace"),
					"label" 		=> array(
						'all_items' => esc_html__("Templates", "userplace"),
					),
					'supports' 		=> array(
						'title' 	=> true,
						'editor'	=> true,
					),
					"publiclyQueryable" 	=> false,
					"hasArchive" 		    => false,
					"excludeFromSearch" 	=> true,
					"hierarchical" 		  	=> false,
				),
				array(
					"name" 				    => "userplace_role",
					"showInMenu" 		 	=> 'userplace',
					"showName" 			    => esc_html__("Role", "userplace"),
					"label" 			    => array(
						'all_items' 	    => esc_html__("Roles", "userplace"),
					),
					'supports' 			    => array(
						'title' 		      => true,
						'editor' 		      => false,
					),
					"publiclyQueryable" => false,
					"hasArchive" 		    => false,
					"hierarchical" 		  => false,
					"excludeFromSearch" 		    => true,
				),
				array(
					"name" 				 => "userplace_console",
					"showInMenu" 		 => 'userplace',
					"excludeFromSearch"  => true,
					"showName" 			 => esc_html__("Console Menu", "userplace"),
					"label" 			 => array(
						'all_items' 	 => esc_html__("Console Menu", 'userplace'),
					),
					'supports' 			 => array(
						'title' 		   => true,
						'editor' 		   => true,
					),
				),
				array(
					"name" 				 => "userplace_coupon",
					"excludeFromSearch" 		    => true,
					"showInMenu" 		 => 'userplace',
					"showName" 			 => esc_html__("Coupons", "userplace"),
					"label" 			 => array(
						'all_items' 	 => esc_html__("Coupons", 'userplace'),
					),
					'supports' 			 => array(
						'title' 		   => true,
						'editor' 		   => true,
					),
				),
			))
		);
	}

	public function add_metabox($post_type)
	{
		add_meta_box(
			'userplace_plan_page_url',
			esc_html__('Plan Page URL', 'userplace'),
			array($this, 'plan_page_url_render'),
			'userplace_plan',
			'normal',
			'high'
		);

		$args = apply_filters('userplace_add_new_metabox', array(
			array(
				'id' 						=> 'userplace_plan_builder',
				'name' 					=> esc_html__('Plan Builder', 'userplace'),
				'post_type' 		=> 'userplace_plan',
				'priority' 			=> 'high',
				'position' 			=> 'normal',
				'template_path' => '/plan/gateway-id.php',
			),
			array(
				'id' 						=> 'userplace_plan_restrictions',
				'name' 					=> esc_html__('Apply Restrictions', 'userplace'),
				'post_type' 		=> 'userplace_plan',
				'priority' 			=> 'high',
				'position' 			=> 'normal',
				'template_path' => '/plan/restrictions.php',
			),
			array(
				'id' 			      => 'userplace_pasyasugo',
				'name' 			    => esc_html__('Plans Pricing Details', 'userplace'),
				'post_type' 	  => 'userplace_payasugo',
				'priority' 		  => 'high',
				'position' 		  => 'normal',
				'template_path' => '/plan/payasugo.php',
			),
			array(
				'id' 			      => 'userplace_role_capabilities',
				'name' 			    => esc_html__('Role Capabilities', 'userplace'),
				'post_type' 	  => 'userplace_role',
				'priority' 		  => 'high',
				'position' 		  => 'normal',
				'template_path' => '/userplace-new-role.php',
			),
			array(
				'id' 			      => 'scholar_template_settings',
				'name' 			    => esc_html__('Template Settings', 'scholarwp'),
				'post_type' 	  => 'userplace_template',
				'priority' 		  => 'high',
				'position' 		  => 'normal',
				'template_path' => '/template-settings.php',
			),
			array(
				'id' 			      => 'userplace_console_settings', //must be different and unique from template id
				'name' 			    => esc_html__('Console Menu Settings', 'userplace'),
				'post_type' 	  => 'userplace_console',
				'priority' 			=> 'high',
				'position' 			=> 'normal',
				'template_path' => '/console/builder.php',
			),
			array(
				'id' 			      => 'userplace_coupon_settings', //must be different and unique from template id
				'name' 			    => esc_html__('Coupon Settings', 'userplace'),
				'post_type' 	  => 'userplace_coupon',
				'priority' 			=> 'high',
				'position' 			=> 'normal',
				'template_path' => '/coupons.php',
			),
		));
		$user_id  = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		$restriction_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan);
		$info = get_current_screen();
		$current_screen = null;
		if ($info->base == 'post' || $info->base == 'term' || $info->base == 'edit-tags')
			$current_screen = $info->post_type;
		elseif ($info->post_type == null)
			$current_screen = $info->base; // take the base when it's a page or options
		$provider = new Admin_Lacalize;
		$post_types = $provider->get_all_posts();
		if (isset($restriction_details['general']['enable_single_post_restriction']) && $restriction_details['general']['enable_single_post_restriction'] === 'true') {
			$restricted_post_types = isset($restriction_details['general']['single_restriction_enable_post_types']) && $restriction_details['general']['single_restriction_enable_post_types'] != '' ? explode(',', $restriction_details['general']['single_restriction_enable_post_types']) : array();
			if (in_array($post_type, $restricted_post_types)) {
				$args[] = array(
					'id' 						=> 'single_post_restriction',
					'name' 					=> esc_html__('Restrict This', 'userplace'),
					'post_type' 		=> $post_type,
					'priority' 			=> 'high',
					'position' 			=> 'side',
					'template_path' => '/single-post-restrictions.php',
				);
			}
		} else if (current_user_can('administrator') && array_key_exists($current_screen,  $post_types)) {
			$args[] = array(
				'id' 						=> 'single_post_restriction',
				'name' 					=> esc_html__('Restrict This', 'userplace'),
				'post_type' 		=> $post_type,
				'priority' 			=> 'high',
				'position' 			=> 'side',
				'template_path' => '/single-post-restrictions.php',
			);
		}
		new Generate_MetaBox($args);
	}

	public function plan_page_url_render($post)
	{
		$plan = get_post_meta($post->ID, 'plan_id', true);
		$url = home_url() . '/subscription/pay?plan=' . $plan;
?>
		<h4><?php esc_html_e('Please copy this Url After saving the plan.', 'userplace') ?></h4>
		<pre class="scwp-snippet"><div class="scwp-clippy-icon" data-clipboard-snippet=""><img class="clippy" width="13" src="<?php print USERPLACE_IMG ?>clippy.svg" alt="Copy to clipboard"></div><code class="js hljs javascript"><?php echo esc_url($url) ?></code></pre>
<?php
	}

	/**
	 * Add a post display state for special Userplace pages in the page list table.
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states($post_states, $post)
	{
		$default_plan = userplace_get_default_plan();
		if (!empty($default_plan->plan_id) && $default_plan->ID === $post->ID) {
			$post_states['userplace_plan_for_default'] = esc_html__('Default Plan', 'userplace');
		}
		if ('member-login' === $post->post_name) {
			$post_states['userplace_page_for_sign_in'] = esc_html__('Sign In Page', 'userplace');
		}

		return $post_states;
	}
}
