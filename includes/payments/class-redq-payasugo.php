<?php

/**
 * Pay as u go in_admin_footer
 */
namespace Userplace;
class PayAsUGo {
	public function get_pay_as_u_go_restrictions($post_type) {
		global $wpdb;
		$query_placeholder = array(
			'userplace_payasugo',
			'publish'
		);
		$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s ", $query_placeholder);
		$all_payasugo = $wpdb->get_results($query);
		foreach ($all_payasugo as $key => $single_plan) {
			$restriction_post_type = get_post_meta( $single_plan->ID, 'pay_as_go_post_types', true );
			if (isset($restriction_post_type) && $restriction_post_type == $post_type) {
				$plan_restrictions = json_decode(get_post_meta($single_plan->ID, '_userplace_payasugo', true), true);
				if (isset($plan_restrictions['specific_term_cost']) && is_array($plan_restrictions['specific_term_cost'])) {
					foreach ($plan_restrictions['specific_term_cost'] as $key => $single_term_details) {
						$plan_restrictions['cost_for_specific_term'][$single_term_details['data'][0]['value']] = $single_term_details['data'][1]['value'];
					}
				}else{
					$plan_restrictions['cost_for_specific_term'] = array();
				}
				unset($plan_restrictions['specific_term_cost']);
				return $plan_restrictions;
			}
		}
		return false;
	}

	public function calculate_pay_as_you_go_amount($restrictions, $formData) {
		$listing_cost = 0;
		if(isset($restrictions) && is_array($restrictions)){
			$listing_cost += isset($restrictions['base_listing_rate']) ? $restrictions['base_listing_rate'] : 0;
			$listing_cost += (isset($formData['noOfAttachments']) ? $formData['noOfAttachments'] : 0 ) * (isset($restrictions['rate_per_media']) ? $restrictions['rate_per_media'] : 0 );
			$selected_terms = isset($formData['selectedTerms']) ? $formData['selectedTerms'] : array();
			foreach ($selected_terms as $key => $singleTerm) {
				if (array_key_exists($singleTerm, $restrictions['cost_for_specific_term'])) {
					$listing_cost += $restrictions['cost_for_specific_term'][$singleTerm];
				}else{
					$listing_cost += $restrictions['per_term_rate'];
				}
			}
		}

		return $listing_cost;
	}
}
