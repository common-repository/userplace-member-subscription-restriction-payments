<?php

namespace Userplace;

class ViewHelper
{
	use Payment_Info;


	public function is_user_capable_of_view_comment($post_type, $comment_template){
		if (current_user_can( 'administrator' )) {
			return $comment_template;
		}
		$user_id = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		$plan_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan);
		$comment_restricted_post_types = isset($plan_details['general']['comment_restricted_post_type']) ? explode(',', $plan_details['general']['comment_restricted_post_type']) : [];
		if(in_array($post_type, $comment_restricted_post_types)){
			return USERPLACE_TEMPLATE_PATH. 'comments.php';
		} else{
			return $comment_template;
		}
	}
}
