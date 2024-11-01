<?php

/**
 * Comment Restrictions
 */

namespace Userplace;

class CommentSinglePostRestriction
{
	use Payment_Info;

	private $restrictions_error;

	function __construct()
	{
		add_filter('comments_template', array($this, 'restrict_comment_template'), 10, 1);
		add_filter('post_updated_messages', array($this, 'hide_post_update_notice'), 10, 1);
		add_action('sidebars_widgets', array($this, 'restrict_widgets'), 10, 1);
		add_action('save_post', array($this, 'restrict_submission'), 15, 3);
		add_action('admin_notices', array($this, 'admin_notice_on_restriction'), 10, 2);
	}

	public function hide_post_update_notice($notices)
	{
		if (!isset($_GET['userplace_restrict_post'])) {
			return $notices;
		}
		if (!isset($_GET['post'])) {
			return $notices;
		}
		$post_id = intval($_GET['post']);
		$post = get_post($post_id);
		if ($post->post_status === 'publish') {
			return $notices;
		}
		$user_id  = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		if ($user_subscribed_plan) {
			$restriction_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan, $post->post_type);
			if (isset($restriction_details[$post->post_type]) && is_array($restriction_details[$post->post_type])) {
				unset($notices[$post->post_type]);
			}
		}
		return $notices;;
	}

	public function admin_notice_on_restriction($data)
	{
		if (!isset($_GET['userplace_restrict_post'])) {
			return;
		}

		if (!isset($_GET['post'])) {
			return $data;
		}
		$post_id = intval($_GET['post']);
		if (!$post_id) {
			return;
		}
		$post 					= get_post($post_id);
		$user_id  				= get_current_user_id();
		$user_subscribed_plan 	= $this->getUserSubscriptionPlan($user_id);
		if ($user_subscribed_plan) {
			$restriction_details  = $this->getRestrictionDetails($user_id, $user_subscribed_plan, $post->post_type);
			$get_all_taxonomies = get_post_taxonomies($post);
			$not_allowed_term = [];
			$not_allowed_metas = [];
			$total_number_of_terms = 0;
			$restricted_terms = (isset($restriction_details[$post->post_type]['restricted_terms'])) ? explode(',', $restriction_details[$post->post_type]['restricted_terms']) : array();
			foreach ($get_all_taxonomies as $key => $taxonomy) {
				$terms = wp_get_post_terms($post->ID, $taxonomy);
				$total_number_of_terms += count($terms);
				if (isset($restricted_terms) && is_array($restricted_terms) && !empty($restricted_terms)) {
					foreach ($terms as $key => $term_option) {
						if (in_array($term_option->slug, $restricted_terms)) {
							$not_allowed_term[] = $term_option->name;
						}
					}
				}
			}
			$restricted_metas = [];
			if (isset($restriction_details[$post->post_type]['restricted_metakeys'])) {
				$restricted_metas = explode(',', $restriction_details[$post->post_type]['restricted_metakeys']);
			}

			foreach ($restricted_metas as $key => $meta) {
				$meta_value = get_post_meta($post_id, $meta, true);
				if (isset($meta_value) && $meta_value != '') {
					$not_allowed_metas[] = $meta;
				}
			}
		}

		/**
		 * Hook: userplace_post_restriction_notice_args.
		 */
		$notice_args = apply_filters('userplace_post_restriction_notice_args', array(
			'post'                  => $post,
			'restriction_details'   => $restriction_details,
			'not_allowed_term'      => $not_allowed_term,
			'total_number_of_terms' => $total_number_of_terms,
			'not_allowed_metas' 	=> $not_allowed_metas
		));

		/**
		 * Hook: userplace_post_restriction_notices.
		 */
		do_action('userplace_post_restriction_notices', $notice_args);
	}

	public function userplace_add_notice_query_var($location)
	{
		remove_filter('redirect_post_location', array($this, 'userplace_add_notice_query_var'), 99);
		return add_query_arg(array('userplace_restrict_post' => 'yes'), $location);
	}



	public function restrict_submission($id, $post, $update)
	{
		$restricted_metas = array();
		$restricted_terms = array();
		if (!current_user_can('administrator')) {
			// $restricted_metas = [];
			if ($post->post_status === 'publish') {
				update_post_meta($id, 'is_pay_as_u_go', 'false'); // set meta for not pay as you go
				remove_action('save_post', array($this, 'restrict_submission'), 15, 3); // remove the action for avoiding infinite loop
				$user_id  = get_current_user_id();
				$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
				if ($user_subscribed_plan) {
					$restriction_details    = $this->getRestrictionDetails($user_id, $user_subscribed_plan, $post->post_type);
					$get_all_taxonomies     = get_post_taxonomies($post);
					$total_number_of_terms  = 0;
					$is_all_term_allowed    = true;
					$is_all_meta_allowed    = true;
					if (isset($restriction_details[$post->post_type]['restricted_terms'])) {
						$restricted_terms = explode(',', $restriction_details[$post->post_type]['restricted_terms']);
					}
					if (isset($restriction_details[$post->post_type]['restricted_metakeys'])) {
						$restricted_metas = explode(',', $restriction_details[$post->post_type]['restricted_metakeys']);
					}

					foreach ($restricted_metas as $key => $meta) {
						$meta_value = get_post_meta($post->ID, $meta, true);
						if (isset($meta_value) && $meta_value != '') {
							$is_all_meta_allowed = false;
							break;
						}
					}

					foreach ($get_all_taxonomies as $key => $taxonomy) {
						$terms = wp_get_post_terms($post->ID, $taxonomy);
						$total_number_of_terms += count($terms);
						if ($is_all_term_allowed && isset($restricted_terms) && is_array($restricted_terms) && !empty($restricted_terms)) {
							foreach ($terms as $key => $term_option) {
								if (in_array($term_option->slug, $restricted_terms)) {
									$is_all_term_allowed = false;
									break;
								}
							}
						}
					}
					if (!isset($restriction_details[$post->post_type])) {
						wp_update_post(array('ID' => $id, 'post_status' => 'draft'));
						add_filter('redirect_post_location', array($this, 'userplace_add_notice_query_var'), 99);
						do_action('userplace_post_restriction_notice_args', array(
							'post'                  => $post,
							'restriction_details'   => $restriction_details,
							'not_allowed_term'      => $restricted_terms,
							'total_number_of_terms' => $total_number_of_terms,
							'not_allowed_metas' => $restricted_metas,
							'submission_allowed' => false
						));
					}
					if (isset($restriction_details[$post->post_type]) && is_array($restriction_details[$post->post_type])) {
						if ($restriction_details[$post->post_type]['allow_unlimited'] != 'true') {
							if ($restriction_details[$post->post_type]['max_posts'] < $restriction_details[$post->post_type]['used_quota'] || !$is_all_meta_allowed || !$is_all_term_allowed || $total_number_of_terms > $restriction_details[$post->post_type]['max_terms_per_post']) {
								wp_update_post(array('ID' => $id, 'post_status' => 'draft'));
								add_filter('redirect_post_location', array($this, 'userplace_add_notice_query_var'), 99);
								do_action('userplace_post_restriction_notice_args', array(
									'post'                  => $post,
									'restriction_details'   => $restriction_details,
									'not_allowed_term'      => $restricted_terms,
									'total_number_of_terms' => $total_number_of_terms,
									'not_allowed_metas' => $restricted_metas
								));
							}
						}
					}
				}
				add_action('save_post', array($this, 'restrict_submission'), 15, 3);
			}
		}
	}

	public function restrict_widgets($all_widget_list)
	{
		$user_id = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		$plan_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan);
		$restricted_widgets = isset($plan_details['general']['restricted_widgets']) ? explode(',', $plan_details['general']['restricted_widgets']) : [];
		$all_widget_list = $this->hide_restricted_widgets($all_widget_list, $restricted_widgets);
		return $all_widget_list;
	}

	public function restrict_comment_template($comment_template)
	{
		global $post;
		$helper = new ViewHelper;
		if (!(is_singular() && (have_comments() || 'open' == $post->comment_status))) {
			return;
		}
		return $comment_template = $helper->is_user_capable_of_view_comment($post->post_type, $comment_template);
	}


	public function hide_restricted_widgets($widget_list, $restricted_widgets)
	{
		if (current_user_can('administrator')) {
			return $widget_list;
		}
		if (!is_admin()) {
			foreach ($widget_list as $key => $value) {
				foreach ($value as $child_key => $child_value) {
					if (in_array($child_value, $restricted_widgets)) {
						unset($widget_list[$key][$child_key]);
					}
				}
			}
		}
		return $widget_list;
	}
}
