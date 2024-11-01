<?php
/**
 * Save MetaBox
 */

namespace Userplace;

class SaveMeta {
	use Payment_Info;
	public function __construct() {
		add_action( 'save_post', array( $this, 'save_metabox' ), 9, 2 );
		add_action( 'save_post', array( $this, 'save_role' ), 9, 2 );
		add_action( 'trashed_post', array( $this, 'trashed_post' ) );
	}

	public function save_metabox( $post_id, $post ){
		$args = array(
			array(
				'post_id' 				=> $post_id,
				'post_type' 			=> 'userplace_plan',
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'_userplace_plan_builder',
					'_userplace_plan_restrictions',
				),
			),
			array(
				'post_id' 				=> $post_id,
				'post_type' 			=> 'userplace_payasugo',
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'_userplace_payasugo',
				),
			),
			array(
				'post_id' 				=> $post_id,
				'post_type' 			=> 'userplace_template',
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'_userplace_payment_template_data',
				),
			),
			array(
				'post_id' 				=> $post_id,
				'post_type' 			=> 'userplace_role',
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'_userplace_add_new_role_settings',
				),
			),
			array(
				'post_id' 				=> $post_id,
				'post_type' 			=> 'userplace_console',
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'_userplace_console_builder',
				),
			),
			array(
				'post_id' 				=> $post_id,
				'post_type' 			=> 'userplace_coupon',
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'_userplace_coupon',
				),
			),
		);
		
		
		$user_id  = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		$restriction_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan);
		if (isset($restriction_details['general']['enable_single_post_restriction']) && $restriction_details['general']['enable_single_post_restriction'] === 'true') {
			$restricted_post_types = isset($restriction_details['general']['single_restriction_enable_post_types']) && $restriction_details['general']['single_restriction_enable_post_types'] != '' ? explode(',', $restriction_details['general']['single_restriction_enable_post_types']) : array();
			foreach ($restricted_post_types as $key => $single_post_type) {
				$args[] = array(
					'post_id' 				=> $post_id,
					'post_type' 			=> $single_post_type,
					'has_individual' 	=> true,
					'meta_fields' 		=> array(
						'userplace_restrictions_settings',
					),
				);
			}
		}else if (current_user_can( 'administrator' )){
			$args[] = array(
				'post_id' 				=> $post_id,
				'post_type' 			=> $post->post_type,
				'has_individual' 	=> true,
				'meta_fields' 		=> array(
					'userplace_restrictions_settings',
				),
			);
		}
		$args = apply_filters( 'userplace_save_custom_meta_args', $args );
		new Generate_Metabox_Saver( array_merge( $args ) );
	}

	public function save_role( $post_id, $post ) {
		$default_roles = array(
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		);

		if( in_array( $post->post_title, $default_roles ) ) {
			return;
		}

		if( get_post_type() != 'userplace_role')
			return;

		// Clean previous data
		remove_role($post->post_name);
		
		$capabilities = json_decode( get_post_meta( $post_id, '_userplace_add_new_role_settings', true ), true );
		add_role(
	    $post->post_name,
	    esc_html__( $post->post_title ),
	    array(
        'read'	=> true,  // true allows this capability
	    )
		);

		$update_cap = get_role($post->post_name);
   	
   	// add $cap capability to this role object
   	if( !empty( $capabilities ) ) {
   		foreach ($capabilities as $capability) {
	   		$new_caps = explode(',', $capability);
	   		foreach ($new_caps as $cap) {
	   			$update_cap->add_cap($cap);
	   		}
	   	}
   	}
	}

	function trashed_post($post_id) {
    $post 					= get_post( $post_id );
    $post_type 			= get_post_type( $post_id );
    $default_roles 	= array(
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		);
    if( $post_type == 'userplace_role' && !in_array( $post->post_name, $default_roles ) ) {
      //check if role exist before removing it
			if( get_role($post->post_name) ){
			  remove_role( $post->post_name );
			}
    }
  }
}
