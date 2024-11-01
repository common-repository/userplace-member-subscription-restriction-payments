<?php

/**
 * 
 */

namespace Userplace;

use Userplace\Post_Restrictions;

class Template_Loader
{
	use Payment_Info;
	function __construct()
	{
		add_filter('template_include', array($this, 'template_loader'), 99, 1);
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. userplace looks for theme
	 * overrides in /theme/userplace/ by default
	 *
	 * For beginners, it also looks for a userplace.php template first. If the user adds
	 * this to the theme (containing a userplace() inside) this will be used for all
	 * userplace templates.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader($template)
	{
		global $post;
		$find = array('userplace.php');
		$file = '';

		$user = wp_get_current_user();
		$role = (array) $user->roles;

		$user_id  = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		$restriction_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan);
		$restricted_post_types = isset($restriction_details['general']) && !empty($restriction_details['general']['view_restricted_post_types']) ? $restriction_details['general']['view_restricted_post_types'] : '';
		$post_restrictions = (isset($role[0]) && $role[0] === 'administrator') || $restricted_post_types === '' ? array() : explode(',', $restricted_post_types);
		if (is_single()) {
			$single_restrictions = json_decode(get_post_meta($post->ID, 'userplace_restrictions_settings', true), true);
			if (isset($single_restrictions['public_view']) && $single_restrictions['public_view'] == 'logged_in') {
				$single_restricted_plans = (isset($single_restrictions['restricted_plans'])) ? explode(',', $single_restrictions['restricted_plans']) : [];
				if (!is_user_logged_in() || in_array($user_subscribed_plan, $single_restricted_plans) && (isset($role[0]) && $role[0] !== 'administrator')) {
					$file   = 'single-template.php';
					$find[] = $file;
					$find[] = Userplace()->template_path() . $file;
					$template       = USERPLACE_TEMPLATE_PATH . $file;
				}
			}
		}

		foreach ($post_restrictions as $post_restriction) {
			if (is_single()) {
				if (get_post_type() == $post_restriction) {
					$file   = 'single-template.php';
					$find[] = $file;
					$find[] = Userplace()->template_path() . $file;
				}
			} elseif (is_archive()) {
				if (is_post_type_archive($post_restriction)) {
					$file   = 'archive-template.php';
					$find[] = $file;
					$find[] = Userplace()->template_path() . $file;
				}
			}
			if ($file) {
				$template       = locate_template(array_unique($find));
				if (!$template) {
					$template = USERPLACE_TEMPLATE_PATH . $file;
				}
			}
		}
		return $template;
	}
}
