<?php

/**
 *
 */

namespace Userplace;

use Userplace\Admin_Lacalize;

class Provider
{
	public function __construct()
	{
		$provider = new Admin_Lacalize();
		$this->post_types = $provider->get_all_posts();
	}

	public function coupon_provider_array()
	{
		$all_plan = userplace_get_all_plan();
		return apply_filters('userplace_coupons_fields_array', array(
			array(
				'id'            => 'userplace_coupon_id',
				'label'         => esc_html__('Coupons/Discounts Id', 'userplace'),
				'type'          => 'text',
				'placeholder'   => esc_html__('Place coupon id created in your dashboard', 'userplace'),
			),
		));
	}
	public function console_menu_settings()
	{
		return apply_filters('userplace_console_menu_settings_fileds_array', array(
			array(
				'id'    => 'reuse_button_iconpicker',
				'type'  => 'iconpicker',
				'label' => 'Choose Console Menu Icon',
				'value' =>  'ion ion-ios-add-circle-outline',
			),
			array(
				'id'        => 'form_type',
				'type'      => 'select',
				'label'     => 'Form Type',
				'multiple'  => 'false',
				'clearable' => 'false',
				'subtitle'  => 'Choose the form type',
				'options'   => array(
					'parent' => 'Show in sidebar menu',
				),
				'value'     => 'parent',
			),
			array(
				'id'          => 'restricted_plans',
				'type'        => 'checkbox',
				'label'       => esc_html__('Restricted Plans', 'userplace'),
				'param'       => 'restricted_plans',
				'multiple'    => false,
				'options'     => userplace_get_all_plans(),
			),
		));
	}

	public function single_post_restrictions_fields()
	{
		return apply_filters('userplace_single_post_restrictions_fields_array', array(
			array(
				'id' 		       => 'public_view',
				'type' 		   	 => 'radio',
				'label' 	   	 => esc_html__('Public View', 'userplace'),
				'param' 	   	 => 'public_view',
				'multiple' 	 	 => false,
				'options'			 => array(
					'everyone'		=> esc_html__('Everyone', 'userplace'),
					'logged_in'		=> esc_html__('Logged In Users', 'userplace'),
					'logged_out'	=> esc_html__('Looged Out Users', 'userplace'),
				),
				'value'        => 'everyone'
			),

			array(
				'id'           => 'restricted_plans',
				'type'         => 'checkbox',
				'label'        => esc_html__('Restricted Plans', 'userplace'),
				'param'        => 'restricted_roles',
				'multiple'     => false,
				'options'      => userplace_get_all_plans(),
			),
		));
	}

	public function single_restriction_conditions()
	{
		return $allLogicBlock = [
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758343162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'public_view',
							'secondOperand'  => [
								'type'       => 'value',
								'value'      => 'everyone',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField' => [
					[
						'action'      => 'hide',
						'id'          => 148787613315,
						'fieldID'     => 'restricted_plans',
					],
				],
			],
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758213162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'public_view',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'logged_in',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField'   => [
					[
						'action'  => 'show',
						'id'      => 148787613315,
						'fieldID' => 'restricted_plans',
					],
				],
			],
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758213162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'public_view',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'logged_out',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField'   => [
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'restricted_plans',
					],
				],
			],
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758213162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'public_view',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'undefined',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField'   => [
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'restricted_plans',
					],
				],
			],
		];
	}




	public function addRoleFields()
	{
		$all_capabilities = userplace_get_all_capabilities();
		return $fields = array(
			array(
				'id'        => 'user_role_capabilities',
				'label'     => 'Select Capabilities...',
				'type'      => 'checkbox',
				'multiple'  => 'true',
				'columns'   => 3,
				'step'      => 10,
				'options'   => $all_capabilities,
				'value'     => 'upload_files,read,level_1'
			),
		);
	}

	public function userSettingsFields()
	{

		$fields = array(
			array(
				'id'        => 'user_custom_gravater',
				'label'     => esc_html__('Gravater', 'userplace'),
				'type'      => 'imageupload',
				'menuId'	 		=> 'general',
				'parentId'  => [],
				'subtitle'  => '',
				'multiple'  => 'false',
				'hidden'    => 'false',
			),
			array(
				'id'        => 'user_banner_image',
				'label'     => esc_html__('Banner Image', 'userplace'),
				'type'      => 'imageupload',
				'menuId'	 		=> 'general',
				'parentId'  => [],
				'subtitle'  => '',
				'multiple'  => 'false',
				'hidden'    => 'false',
			),
			array(
				'id'            => 'first_name',
				'label'         => esc_html__('First name', 'userplace'),
				'type'          => 'text',
				'menuId'	 		=> 'general',
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'last_name',
				'label'         => esc_html__('Last Name', 'userplace'),
				'type'          => 'text',
				'menuId'	 		=> 'general',
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_gender',
				'type'          => 'radio',
				'label'         => esc_html__('Gender', 'userplace'),
				'subtitle'      => esc_html__('Select your gender', 'userplace'),
				'menuId'	 		=> 'general',
				'selectionType' => '', //'showMore' for more button && 'showAllButton' for show all
				'step'          => 5, // number of columns twhich ill show 1st
				'column'        => 1, // number of columns to show (default 1)
				'options'       => array(
					'male'        => esc_html__('Male', 'userplace'),
					'female'      => esc_html__('Female', 'userplace'),
					'other'       => esc_html__('Other', 'userplace'),
				),
				'value'         => '',
			),
			array(
				'id'            => 'user_dob',
				'type'          => 'inputMask',
				'menuId'	 		=> 'general',
				'label'         => esc_html__('Date of birth', 'userplace'),
				'subtitle'      => esc_html__('Insert date of birth ( YYYY-MM-DD )', 'userplace'),
				'placeholder'   => 'YYYY-MM-DD',
				'mask'   => '9999-99-99',
			),
			array(
				'id'            => 'user_url',
				'type'          => 'text',
				'parentId'      => [],
				'label'         => esc_html__('Website URI', 'userplace'),
				'subtitle'      => '',
				'menuId' 				=> 'bio',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_description',
				'label'         => esc_html__('Description About You', 'userplace'),
				'type'          => 'textarea',
				'menuId' 				=> 'bio',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_designation',
				'label'         => esc_html__('Designation', 'userplace'),
				'type'          => 'text',
				'menuId' 				=> 'bio',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_facebook_uri',
				'label'         => esc_html__('Facebook URI', 'userplace'),
				'type'          => 'text',
				'menuId' 				=> 'social',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_twitter_uri',
				'label'         => esc_html__('Twitter URI', 'userplace'),
				'type'          => 'text',
				'menuId' 				=> 'social',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_google_uri',
				'label'         => esc_html__('Google+ URI', 'userplace'),
				'type'          => 'text',
				'menuId' 				=> 'social',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_instagram_uri',
				'label'         => esc_html__('Instagram URI', 'userplace'),
				'type'          => 'text',
				'menuId' 				=> 'social',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_company_name',
				'label'         => esc_html__('Company Name', 'userplace'),
				'type'          => 'text',
				'menuId' 				=> 'social',
				'parentId'      => [],
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			),
			array(
				'id'            => 'user_timezone',
				'type'          => 'select',
				'label'         => esc_html__('Timezone', 'userplace'),
				'menuId' 				=> 'bio',
				'multiple'      => 'false',
				'clearable'     => 'false',
				'subtitle'      => esc_html__('Select your timezone', 'userplace'),
				// 'options'       => $userplace_timezones,
				'options'       => userplace_timezones(),
				'value'         => '',
			),
		);
		$googlemap_settings = get_option('googlemap_settings', true);
		$map_api_key = isset($googlemap_settings['googlemap_api_key']) ? $googlemap_settings['googlemap_api_key'] : '';
		$googleMap_exists = class_exists('Load_Google_Map');
		if ($map_api_key && $googleMap_exists) {
			$fields[] = array(
				'id'            => 'user_working_location',
				'label'         => esc_html__('Work Location', 'userplace'),
				'type'          => 'geobox',
				'parentId'      => [],
				'menuId'		=> 'location',
				'subtitle'      => '',
				'placeholder'   => '',
				'delimiter'     => '',
				'hidden'        => 'false',
			);
		} else if (current_user_can('administrator')) {
			$fields[] = array(
				'id'            => 'no_map_api_key',
				'label'         => esc_html__('Please Set Google Map API Key from Settings', 'userplace'),
				'type'          => 'label',
				'menuId' 		=> 'location',
				'label_type'	=> 'h3',
			);
		}
		$fields[] = array(
			'id'                => 'update_profile',
			'type'              => 'compoundbutton',
			'parentId'          => [],
			'label'             => esc_html__('Update Profile', 'userplace'),
			'getallData'        => 'true',
			'getFormData'       => 'true',
			'className'         => 'reuseFlatButton',
			'fullWidthControl'  => '',
			'hidden'            => 'false',
		);
		return  apply_filters('userplace_user_settings_fields_array', $fields);
	}
	public function payasugo_provider_array()
	{
		$terms = userplace_get_all_terms();
		$fields = array(
			array(
				'id'            => 'pay_as_go_post_types',
				'type'          => 'select',
				'label'         => esc_html__('Select Posttypes for this Plan', 'userplace'),
				'param'         => 'pay_as_go_post_types',
				'options'       => $this->post_types
			),
			array(
				'id'            => 'base_listing_rate',
				'type'          => 'text',
				'placeholder'   => esc_html__('Base Listing Rate in $', 'userplace'),
				'label'         => esc_html__('Base Listing Rate', 'userplace'),
				'param'         => 'base_listing_rate',
			),
			array(
				'id' 		     => 'pay_as_go_currency',
				'type' 		     => 'text',
				'placeholder'	 => esc_html__('Enter Currency Symbol (ex. $)', 'userplace'),
				'label' 	     => esc_html__('Enter Currency Symbol', 'userplace'),
				'param' 	     => 'pay_as_go_currency',
			),
			array(
				'id' 		     => 'rate_per_media',
				'type' 		     => 'text',
				'placeholder'	 => esc_html__(
					'Per Media Rate in $', 'userplace'),
				'label' 	     => esc_html__('Rate Per Media', 'userplace'),
				'param' 	     => 'rate_per_media',
			),
			array(
				'id' 		 => 'expired_in_days',
				'type' 		 => 'minmaxbutton',
				'label' 	 => esc_html__('Expired in Days', 'userplace'),
				'param' 	 => 'expired_in_days',
				'value' 	 => 30,
				'step' 		 => 1,
				'min' 		 => 1,
			),
			array(
				'id' 		=> 'on_expired_post_status',
				'type' 		=> 'select',
				'label' 	=> esc_html__(' On Subscription Expired Listing Status', 'userplace'),
				'param' 	=> 'on_expired_post_status',
				'options' 	=> array(
					'draft'    => 'Draft',
					'publish'  => 'Publish'
				)
			),
			array(
				'id' 		=> 'specific_term_cost',
				'type' 		=> 'bundle',
				'label' 	=> esc_html__('Term Specific Cost', 'userplace'),
				'param' 	=> 'specific_term_cost',
				'fields' 	=> array(
					array(
						'id' 		=> 'payasugo_term',
						'type' 		=> 'select',
						'label' 	=> esc_html__(' Select Term', 'userplace'),
						'param' 	=> 'payasugo_term',
						'options' 	=> $terms
					),
					array(
						'id'     => 'cost_of_term',
						'type'   => 'text',
						'label'  => esc_html__('Cost of this Term', 'userplace'),
						'param'  => 'cost_of_term',
					),
				),
			),
			array(
				'id' 		     => 'per_term_rate',
				'type' 		     => 'text',
				'placeholder'	 => 'Rate Per Term in $',
				'label' 	     => esc_html__('Rate Per Term', 'userplace'),
				'param' 	     => 'per_term_rate',
			),
		);
		return apply_filters('userplace_payasugo_settings_fields_array', $fields);
	}


	public function userplace_get_restricted_post_types()
	{
		$all_post_types           = $this->post_types;
		$restricted_post_types    = userplace_get_settings('userplace_submission_restricted_post_types');
		if (!isset($restricted_post_types) || $restricted_post_types == '') {
			return $all_post_types;
		}
		$restricted_post_type_array = explode(',', $restricted_post_types);
		$processed_post_types = array();
		if (is_array($restricted_post_type_array)) {
			foreach ($restricted_post_type_array as $key => $post_type) {
				$processed_post_types[$post_type] = $all_post_types[$post_type];
			}
		}
		return $processed_post_types;
	}

	public function restrictions_provider_array()
	{
		global $post;

		$post_types   = userplace_get_restricted_post_types();
		$all_metakeys = userplace_get_all_meta_keys();
		$terms = userplace_get_all_terms();
		$fields[] = array(
			'id' 		 			=> 'general_settings_label',
			'type' 		 		=> 'label',
			'menuId'	 		=> 'general',
			'label' 	 		=> esc_html__('General Settings', 'userplace'),
			'label_type' 	=> 'h3'
		);
		$fields[] = array(
			'id' 		    => 'enable',
			'type' 		  => 'switch',
			'menuId'	  => 'general',
			'label' 	  => esc_html__('Enable/Disable This Plan', 'userplace'),
			'param' 	  => 'enable',
			'value' 	  => false,
		);

		$default_plan = userplace_get_default_plan();

		$default_plan_label = esc_html__('Make This a Default Plan', 'userplace');


		if (!empty($default_plan)) {
			if ($post->ID == $default_plan->ID) {
				$fields[] = array(
					'id'                            => 'single_restriction_enable_helps',
					'type'                          => 'basicHtml',
					'menuId'                        => 'general',
					'dangerouslySetInnerHTML'       => '<div class="doc_block_design">This is your default plan, you can disable this. But you must need a default paln.</div>'
				);
			} else {
				$fields[] = array(
					'id'                            => 'single_restriction_enable_helps',
					'type'                          => 'basicHtml',
					'menuId'                        => 'general',
					'dangerouslySetInnerHTML'       => '<div class="doc_block_design">You have already a Default plan. You want to Make This a Default Plan? This will override the others.</div>'
				);
			}
		}

		$fields[] = array(
			'id'        => 'default_plan',
			'type'      => 'switch',
			'menuId'    => 'general',
			'label'     => $default_plan_label,
			'param'     => 'default_plan',
			'value'     => false,
		);
		$fields[] = array(
			'id' 		    => 'userplace_price',
			'type' 		  => 'text',
			'menuId'	  => 'general',
			'label' 	  => esc_html__('Plan Price', 'userplace'),
			'subtitle' => esc_html__('This price will be same as your payment gateway plan price .', 'userplace'),
			'param' 	  => 'userplace_price',
		);
		$fields[] = array(
			'id'       => 'userplace_plan_role',
			'type'     => 'select',
			'menuId'   => 'general',
			'label'    => esc_html__('Select Role For this plan', 'userplace'),
			'subtitle' => esc_html__('This will set as user role after purchasing the plan. Please select the role wisely. You can just keep userplace_member. You can also change the role capabilities or create a new one from role menu.', 'userplace'),
			'param'    => 'userplace_plan_role',
			'options'  => userplace_get_all_roles(),
			'value'	 => 'userplace_member',
		);
		$fields[] = array(
			'id'       => 'userplace_coupon_post_id',
			'type'     => 'select',
			'menuId'   => 'general',
			'label'    => esc_html__('Select Coupon for this plan', 'userplace'),
			'subtitle' => esc_html__('Please create coupon from the coupon menu, and add it into the plan.', 'userplace'),
			'param'    => 'userplace_coupon_post_id',
			'options'  => userplace_get_all_coupons(),
			'value'		 => 'no_coupon',
		);
		$fields[] =  array(
			'id' 		 			=> 'view_level_restrict_label',
			'type' 		 		=> 'label',
			'menuId'	 		=> 'view_level_restriction',
			'label' 	 		=> esc_html__('Apply View Level Restrictions', 'userplace'),
			'label_type' 	=> 'h3'
		);

		$fields[] = array(
			'id'        => 'view_restricted_post_types',
			'type' 		  => 'select',
			'label' 	  => esc_html__('GLOBAL: Post Restriction', 'userplace'),
			'param' 	  => 'view_restricted_post_types',
			'subtitle' => esc_html__('Please select post type, where you want to add restriction capabilities', 'userplace'),
			'multiple' 	=> 'true',
			'menuId'	 	=> 'view_level_restriction',
			'options'   => Admin_Lacalize::get_all_posts()
		);
		$fields[] = array(
			'id' 		 		=> 'enable_single_post_restriction',
			'type' 		 	=> 'switch',
			'label' 	 	=> esc_html__(' Enable Single Post Restriction', 'userplace'),
			'subtitle'		=> esc_html__('This will only work if you do not have global post type restriction.', 'userpalce'),
			'menuId'	 	=> 'view_level_restriction',
			'param' 	 	=> 'enable_single_post_restriction',
		);
		$fields[] =	array(
			'id' 		 												=> 'single_restriction_enable_helps',
			'type' 		 											=> 'basicHtml',
			'menuId'	 											=> 'view_level_restriction',
			'dangerouslySetInnerHTML'    		=> '<div class="doc_block_design">' . esc_html__("This portion gives user of this plan to restrict other user to view your listing.", "userpalce") . ' </div>'
		);
		$fields[] =	array(
			'id' 		 			=> 'single_restriction_enable_post_types',
			'type' 		 		=> 'select',
			'label' 	 		=> esc_html__(' Select Single Restricted Post Types', 'userplace'),
			'subtitle' 	  => esc_html__('Member under this plan will be able to restrict this post for any user.(only his own listings)', 'userplace'),
			'menuId'	 		=> 'view_level_restriction',
			'param' 	 		=> 'restriction_enable_post_types',
			'multiple' 	 	=> 'true',
			'options'    	=> array_merge(array('page' => 'Page'), Admin_Lacalize::get_all_posts())
		);
		$fields[] = array(
			'id'        => 'comment_restricted_post_type',
			'type' 		  => 'select',
			'label' 	 		=> esc_html__('Comment Restrictions', 'userplace'),
			'subtitle' 	  => esc_html__('Member under this plan will not have any access to the comment section of these post types', 'userplace'),
			'param' 	  => 'comment_restricted_post_type',
			'multiple' 	=> 'true',
			'menuId'	 	=> 'view_level_restriction',
			'options'   => Admin_Lacalize::get_all_posts()
		);

		$fields[] = array(
			'id'        => 'restricted_widgets',
			'type' 		  => 'select',
			'label' 	 		=> esc_html__('Widgets Restrictions', 'userplace'),
			'subtitle' 	  => esc_html__('Member under this plan will not have any access to these widgets', 'userplace'),
			'param' 	  => 'restricted_widgets',
			'multiple' 	=> 'true',
			'menuId'	 	=> 'view_level_restriction',
			'options'   => userplace_get_all_active_widgets()
		);

		foreach ($post_types as $key => $post_type) {
			$fields[] =  array(
				'id' 		 			=> 'view_level_restrict_label',
				'type' 		 		=> 'label',
				'menuId' 	 => $key,
				'label' 	 		=> esc_html__('Submission Restrictions', 'userplace'),
				'label_type' 	=> 'h3'
			);
			$fields[] = array(
				'id' 		   => $key . '__enable',
				'type' 		 => 'switch',
				'menuId' 	 => $key,
				'label' 	 => esc_html__(' Enable/Disable', 'userplace'),
				'param' 	 => $key . '__enable',
				'value' 	 => false,
			);
			$fields[] = array(
				'id' 		   => $key . '__allow_unlimited',
				'type' 		 => 'switch',
				'menuId' 	 => $key,
				'label' 	 => esc_html__(' Allow Unlimited ' . ucwords($key), 'userplace'),
				'subtitle' 	  => esc_html__('Member under this plan will able to create unlimited ' . $key, 'userplace'),
				'param' 	 => $key . '__allow_unlimited',
				'value' 	 => false,
			);
			$fields[] = array(
				'id' 		   => $key . '__view_restriction_metabox',
				'type' 		 => 'switch',
				'menuId' 	 => $key,
				'label' 	 => esc_html__(' Show View Resctriction Metabox', 'userplace'),
				'subtitle' 	  => esc_html__('Member under this plan will able to limit view level restriction of his ' . $key . ' Like he will able to specify that member under which plan can see his ' . $key, 'userplace'),
				'param' 	 => $key . '__view_restriction_metabox',
				'value' 	 => false,
			);
			$fields[] = array(
				'id' 		   => $key . '__max_posts',
				'type' 		 => 'minmaxbutton',
				'menuId' 	 => $key,
				'label' 	 => esc_html__(' Max ' . ucwords($key) . ' Allowed', 'userplace'),
				'subtitle' 	  => esc_html__('Member under this plan will able to create maximum number of ' . $key . ' given below.', 'userplace'),
				'param' 	 => $key . '__max_posts',
				'value' 	 => 50,
				'step' 		 => 1,
				'min' 		 => 1,
			);
			$fields[] = array(
				'id' 		  => $key . '__max_terms_per_post',
				'type' 		=> 'minmaxbutton',
				'menuId' 	=> $key,
				'label' 	=> esc_html__(' Max Terms per ' . ucwords($key), 'userplace'),
				'subtitle' 	  => esc_html__('Member under this plan will able to select maximum number of terms for ' . $key . ' given below.', 'userplace'),
				'param' 	=> $key . '__max_terms_per_post',
				'value' 	=> 50,
				'step' 		=> 1,
				'min' 		=> 1,
			);
			$fields[] = array(
				'id' 		   => $key . '__restricted_metakeys',
				'type' 		 => 'select',
				'menuId' 	 => $key,
				'label' 	 =>  esc_html__(' Restricted Metakeys', 'redq_payments'),
				'subtitle' 	  => esc_html__('Member under this plan will not be able to create any ' . $key . ' with the metakeys selected below.', 'userplace'),
				'param' 	 => $key . '__restricted_metakeys',
				'multiple' => 'true',
				'options'  => $all_metakeys,
			);
			// $fields[] = array(
			// 	'id' 		=> $key.'_max_attachments_per_post',
			// 	'type' 		=> 'minmaxbutton', 
			// 	'menuId' 	=> $key,
			// 	'label' 	=> $post_type . esc_html__(' Max Attachemts per Post', 'redq_payments'),
			// 	'param' 	=> $key.'_max_attachments_per_post',
			// 	'value' 	=> 50,
			// 	'step' 		=> 1,
			// 	'min' 		=> 1,
			// );
			$fields[] = array(
				'id' 		    => $key . '__restricted_terms',
				'type' 		  => 'select',
				'multiple' 	=> 'true',
				'menuId' 	  => $key,
				'label' 	  =>  esc_html__(' Restricted Terms', 'userplace'),
				'subtitle' 	  => esc_html__('Member under this plan will not be able to create any ' . $key . ' with the terms selected below.', 'userplace'),
				'param' 	  => $key . '__restricted_terms',
				'options' 	=> $terms
			);
			$fields[] = array(
				'id' 		     => $key . '__expired_post_status',
				'type' 		   => 'select',
				'multiple' 	 => 'true',
				'menuId' 	   => $key,
				'label' 	   =>  esc_html__(' On Subscription Expired ' . ucwords($key) . ' Status', 'userplace'),
				'param' 	   => $key . '__expired_post_status',
				'options' 	 => array(
					'draft'    => 'Draft',
					'publish'  => 'Publish'
				)
			);
			// 	$child_post_types_string = userplace_get_all_child_post_type('company');
			// 	if($child_post_types_string !== 'false'){
			// 		$child_post_types = explode(',', $child_post_types_string);
			// 		foreach ($child_post_types as $child_key => $single_child_post_type) {
			// 			$fields[] = array(
			// 				'id' 		   => $key.'_maximum_child_'. $single_child_post_type,
			// 				'type' 		 => 'minmaxbutton', 
			// 				'menuId'	 => $key,
			// 				'label' 	 => esc_html__('Number of '.ucwords(str_replace("_", " ", $single_child_post_type)). ' Per '. $post_type, 'userplace'),
			// 				'param' 	 => 'maximum_child_'. $single_child_post_type,
			// 				'value' 	=> 50,
			// 				'step' 		=> 1,
			// 				'min' 		=> 1,
			// 			);
			// 		}
			// 	}
		}
		return apply_filters('userplace_restrictions_fields_array', $fields);
	}

	public function payments_gateway_settings_array()
	{
		return apply_filters('userplace_payment_settings_fields', array(
			array(
				'id' 		         => 'payment_gateway',
				'type' 		       => 'select',
				'label' 	       => esc_html__('Please select a payment gateway', 'userplace'),
				'menuId'	       => 'general',
				'param' 	       => 'payment_gateway',
				'options' 	     => array(
					'stripe'       => esc_html__('Stripe', 'userplace'),
					'braintree'    => esc_html__('Braintree', 'userplace'),
				),
			),
			array(
				'id'                  			=> 'userplace_webhooks',
				'type'                  		=> 'basicHtml',
				'menuId'             				=> 'general',
				'dangerouslySetInnerHTML'  	=> '<p>Please use this url as your webhooks url <b>' . site_url() . '/webhooks</b></p>',
			),
			array(
				'id' 		     => 'stripe_payment_mode',
				'type' 		   => 'select',
				'label' 	   => esc_html__('Set Payment Mode', 'userplace'),
				'menuId'	   => 'stripe',
				'param' 	   => 'stripe_payment_mode',
				'options' 	 => array(
					'live'     => esc_html__('Live', 'userplace'),
					'test'     => esc_html__('Test', 'userplace'),
				),
			),
			array(
				'id'                  => 'stripe_settings_label',
				'type'                  => 'basicHtml',
				'menuId'             => 'stripe',
				'dangerouslySetInnerHTML'              => '<p>Please follow the <a href="https://stripe.com/docs/keys" target="_blank">Documentation</a>.</p>',
			),
			array(
				'id' 		=> 'live_stripe_api_key',
				'type' 		=> 'text',
				'label' 	=> esc_html__('Live Stripe API Key', 'userplace'),
				'menuId'	=> 'stripe',
				'param' 	=> 'live_stripe_api_key',
				'multiple' 	=> false,
			),
			array(
				'id' 		 => 'live_stripe_api_secret',
				'type' 		 => 'text',
				'label' 	 => esc_html__('Live Stripe Secret Key', 'userplace'),
				'menuId'	 => 'stripe',
				'param' 	 => 'live_stripe_api_secret',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'test_stripe_api_key',
				'type' 		 => 'text',
				'label' 	 => esc_html__('Test Stripe API Key', 'userplace'),
				'menuId'	 => 'stripe',
				'param' 	 => 'test_stripe_api_key',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'test_stripe_api_secret',
				'type' 		 => 'text',
				'label' 	 => esc_html__('Test Stripe Secret Key', 'userplace'),
				'menuId'	 => 'stripe',
				'param' 	 => 'test_stripe_api_secret',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'braintree_payment_mode',
				'type' 		 => 'select',
				'label' 	 => esc_html__('Set Payment Mode', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'braintree_payment_mode',
				'options' 	 => array(
					'sandbox'      => 'SandBox',
					'production'   => 'Production',
				),
			),
			array(
				'id'                  => 'stripe_settings_label',
				'type'                  => 'basicHtml',
				'menuId'             => 'braintree',
				'dangerouslySetInnerHTML'              => '<p>Please follow the <a href="https://developers.braintreepayments.com/start/go-live/php" target="_blank">Documentation</a>.</p>',
			),
			array(
				'id' 		 => 'sandbox_braintree_merchant_id',
				'type' 		 => 'text',
				'label' 	 => esc_html__('SandBox Braintree Merchant ID', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'sandbox_braintree_merchant_id',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'sandbox_braintree_api_key',
				'type' 		 => 'text',
				'label' 	 => esc_html__('SandBox Braintree API Key', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'sandbox_braintree_api_key',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'sandbox_braintree_api_secret',
				'type' 		 => 'textarea',
				'label' 	 => esc_html__('SandBox Braintree API Secret', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'sandbox_braintree_api_secret',
				'multiple'	 => false,
			),
			array(
				'id' 		 => 'production_braintree_merchant_id',
				'type' 		 => 'text',
				'label' 	 => esc_html__('Production Braintree Merchant ID', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'production_braintree_merchant_id',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'production_braintree_api_key',
				'type' 		 => 'text',
				'label' 	 => esc_html__('Production Braintree API Key', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'production_braintree_api_key',
				'multiple' 	 => false,
			),
			array(
				'id' 		 => 'production_braintree_api_secret',
				'type' 		 => 'textarea',
				'label' 	 => esc_html__('Production Braintree API Secret', 'userplace'),
				'menuId'	 => 'braintree',
				'param' 	 => 'production_braintree_api_secret',
				'multiple' 	 => false,
			),
		));
	}
	public function payments_settings_array()
	{
		$userplace_settings_array = array(
			array(
				'id' 		 		=> 'general_settings_label',
				'type' 		 		=> 'label',
				'menuId'	 		=> 'general',
				'label' 	 		=> esc_html__('Please select appropriate pages', 'userplace'),
				'label_type' 	=> 'h3'
			),
			array(
				'id'          => 'sign_in',
				'type'        => 'select',
				'label'       => esc_html__('Sign In Page', 'userplace'),
				'menuId'      => 'general',
				'param'       => 'sign_in',
				'multiple'    => false,
				'placeholder' => esc_html__('Select a page...', 'userplace'),
				'options'     => userplace_get_all_pages(),
			),
			array(
				'id'          => 'register',
				'type'        => 'select',
				'label'       => esc_html__('Register Page', 'userplace'),
				'placeholder' => esc_html__('Select a page...', 'userplace'),
				'menuId'      => 'general',
				'param'       => 'register',
				'multiple'    => false,
				'options'     => userplace_get_all_pages(),
			),
			array(
				'id'          => 'forgot_your_password',
				'type'        => 'select',
				'label'       => esc_html__('Forgot Your Password?', 'userplace'),
				'placeholder' => esc_html__('Select a page...', 'userplace'),
				'menuId'      => 'general',
				'param'       => 'forgot_your_password',
				'multiple'    => false,
				'options'     => userplace_get_all_pages(),
			),
			array(
				'id'          => 'pick_a_new_password',
				'type'        => 'select',
				'label'       => esc_html__('Pick a New Password', 'userplace'),
				'placeholder' => esc_html__('Select a page...', 'userplace'),
				'menuId'      => 'general',
				'subtitle'	  => esc_html__('This page will appear after the forget password call.', 'userplace'),
				'param'       => 'pick_a_new_password',
				'multiple'    => false,
				'options'     => userplace_get_all_pages(),
			),
			array(
				'id'          => 'userplace_plan_page_url',
				'type'        => 'select',
				'label'       => esc_html__('Pricing Plan Page (Optional)', 'userplace'),
				'placeholder' => esc_html__('Select a page...', 'userplace'),
				'subtitle'	  => esc_html__('Please select your pricing plan page. It will be your default pricing page.', 'userplace'),
				'menuId'      => 'general',
				'param'       => 'userplace_plan_page_url',
				'multiple'    => false,
				'options'     => userplace_get_all_pages(),
			),
		);

		return apply_filters('userplace_settings_fields_array', $userplace_settings_array);
	}

	public function payment_settings_conditional_logic()
	{
		return $allLogicBlock = [
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758343162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'stripe_payment_mode',
							'secondOperand'  => [
								'type'       => 'value',
								'value'      => 'test',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField' => [
					[
						'action'      => 'hide',
						'id'          => 148787613315,
						'fieldID'     => 'live_stripe_api_key',
					],
					[
						'action'      => 'hide',
						'id'          => 148787613315,
						'fieldID'     => 'live_stripe_api_secret',
					],
					[
						'action'      => 'show',
						'id'          => 148787613315,
						'fieldID'     => 'test_stripe_api_key',
					],
					[
						'action'      => 'show',
						'id'          => 148787613315,
						'fieldID'     => 'test_stripe_api_secret',
					],
				],
			],
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758213162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'stripe_payment_mode',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'live',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField'   => [
					[
						'action'  => 'show',
						'id'      => 148787613315,
						'fieldID' => 'live_stripe_api_key',
					],
					[
						'action'  => 'show',
						'id'      => 148787613315,
						'fieldID' => 'live_stripe_api_secret',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'test_stripe_api_key',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'test_stripe_api_secret',
					],
				],
			],
			[
				'name'                      => 'condition101',
				'id'                        => 322283156285,
				'logicBlock'                => [
					[
						'id'      => 1373758313562312,
						'key'     => 'field',
						'value'   => [
							'fieldID'        => 'stripe_payment_mode',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'undefined',
							],
							'operator'       => 'equal_to',
						],
						'childresult'       => false,
					],
				],
				'effectField'               => [
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'live_stripe_api_key',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'live_stripe_api_secret',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'test_stripe_api_key',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'test_stripe_api_secret',
					],
				],
			],
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758343162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'braintree_payment_mode',
							'secondOperand'  => [
								'type'       => 'value',
								'value'      => 'sandbox',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField' => [
					[
						'action'      => 'hide',
						'id'          => 148787613315,
						'fieldID'     => 'production_braintree_api_secret',
					],
					[
						'action'      => 'hide',
						'id'          => 148787613315,
						'fieldID'     => 'production_braintree_api_key',
					],
					[
						'action'      => 'hide',
						'id'          => 148787613315,
						'fieldID'     => 'production_braintree_merchant_id',
					],
					[
						'action'      => 'show',
						'id'          => 148787613315,
						'fieldID'     => 'sandbox_braintree_api_secret',
					],
					[
						'action'      => 'show',
						'id'          => 148787613315,
						'fieldID'     => 'sandbox_braintree_api_key',
					],
					[
						'action'      => 'show',
						'id'          => 148787613315,
						'fieldID'     => 'sandbox_braintree_merchant_id',
					],
				],
			],
			[
				'name'          => 'condition101',
				'id'            => 322283156285,
				'logicBlock'    => [
					[
						'id'              => 1373758213162312,
						'key'             => 'field',
						'value'           => [
							'fieldID'        => 'braintree_payment_mode',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'production',
							],
							'operator'       => 'equal_to',
						],
						'childresult'     => false,
					],
				],
				'effectField'   => [
					[
						'action'  => 'show',
						'id'      => 148787613315,
						'fieldID' => 'production_braintree_api_secret',
					],
					[
						'action'  => 'show',
						'id'      => 148787613315,
						'fieldID' => 'production_braintree_api_key',
					],
					[
						'action'  => 'show',
						'id'      => 148787613315,
						'fieldID' => 'production_braintree_merchant_id',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'sandbox_braintree_api_secret',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'sandbox_braintree_api_key',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'sandbox_braintree_merchant_id',
					],
				],
			],
			[
				'name'                      => 'condition101',
				'id'                        => 322283156285,
				'logicBlock'                => [
					[
						'id'      => 1373758313562312,
						'key'     => 'field',
						'value'   => [
							'fieldID'        => 'braintree_payment_mode',
							'secondOperand'  => [
								'type'  => 'value',
								'value' => 'undefined',
							],
							'operator'       => 'equal_to',
						],
						'childresult'       => false,
					],
				],
				'effectField'               => [
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'production_braintree_api_secret',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'production_braintree_api_key',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'production_braintree_merchant_id',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'sandbox_braintree_api_secret',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'sandbox_braintree_api_key',
					],
					[
						'action'  => 'hide',
						'id'      => 148787613315,
						'fieldID' => 'sandbox_braintree_merchant_id',
					],
				],
			],
		];
	}


	public function get_all_plan_restrictions()
	{
		$all_plans = get_posts(array(
			'post_per_page'  => -1,
			'post_type'      => 'userplace_plan',
			'post_status' 	 => 'publish'
		));

		$all_plan_restrictions = array();
		foreach ($all_plans as $key => $single_plan) {
			$single_plan_id = get_post_meta($single_plan->ID, 'plan_id', true);
			if (isset($single_plan_id) && $single_plan_id != '') {
				$plan_restrictions = json_decode(get_post_meta($single_plan->ID, '_userplace_plan_restrictions', true), true);
				if ($plan_restrictions['enable'] != '1' && $plan_restrictions['enable'] == 'true') {
					$all_plan_restrictions[$single_plan_id]['price'] = isset($plan_restrictions['userplace_price']) ? $plan_restrictions['userplace_price'] : 'Set Plan Price';
					foreach ($plan_restrictions as $key => $single_plan_restriction) {
						if ($key != 'enable') {
							$post_type  = strstr($key, '__', true);
							if (isset($plan_restrictions[$post_type . '__enable']) && $plan_restrictions[$post_type . '__enable'] == 'true') {
								$restriction_key    = str_replace($post_type . '__', '', $key);
								$all_plan_restrictions[$single_plan_id][$post_type][$restriction_key]   = $single_plan_restriction;
							}
						}
					}
				}
			}
		}
		return $all_plan_restrictions;
	}
	public function get_plan_restrictions($plan_id, $post_type = null)
	{
		$all_plans = get_posts(array(
			'post_per_page'   => -1,
			'post_type' 	    => 'userplace_plan',
			'post_status' 	  => 'publish'
		));
		$post_types = $this->post_types;

		$restrictions_by_post_type 	= [];
		$over_all_restrictions 		= [];
		foreach ($all_plans as $key => $single_plan) {
			$single_plan_id = get_post_meta($single_plan->ID, 'plan_id', true);
			if (isset($single_plan_id) && $single_plan_id != '' && $single_plan_id === $plan_id) {
				$plan_restrictions = json_decode(get_post_meta($single_plan->ID, '_userplace_plan_restrictions', true), true);
				// return $plan_restrictions;
				if ($plan_restrictions['enable'] != '1' && $plan_restrictions['enable'] == 'true') {
					foreach ($plan_restrictions as $key => $single_plan_restriction) {
						if ($key != 'enable') {
							$post_type 													= strstr($key, '__', true);
							if (isset($plan_restrictions[$post_type . '__enable']) && $plan_restrictions[$post_type . '__enable'] == 'true') {
								$restriction_key 											= str_replace($post_type . '__', '', $key);
								$restrictions_by_post_type[$post_type][$restriction_key] 	= $single_plan_restriction;
							}
						}
					}
					$restrictions_by_post_type['general']['userplace_price']       = isset($plan_restrictions['userplace_price']) ? $plan_restrictions['userplace_price'] : '';
					$restrictions_by_post_type['general']['userplace_plan_role']   = isset($plan_restrictions['userplace_plan_role']) ? $plan_restrictions['userplace_plan_role'] : '';
					$restrictions_by_post_type['general']['view_restricted_post_types']   = isset($plan_restrictions['view_restricted_post_types']) ? $plan_restrictions['view_restricted_post_types'] : '';
					$restrictions_by_post_type['general']['comment_restricted_post_type']   = isset($plan_restrictions['comment_restricted_post_type']) ? $plan_restrictions['comment_restricted_post_type'] : '';
					$restrictions_by_post_type['general']['restricted_widgets']   = isset($plan_restrictions['restricted_widgets']) ? $plan_restrictions['restricted_widgets'] : '';
					$restrictions_by_post_type['general']['enable_single_post_restriction']   = isset($plan_restrictions['enable_single_post_restriction']) ? $plan_restrictions['enable_single_post_restriction'] : '';
					$restrictions_by_post_type['general']['single_restriction_enable_post_types']   = isset($plan_restrictions['single_restriction_enable_post_types']) ? $plan_restrictions['single_restriction_enable_post_types'] : '';
					$restrictions_by_post_type['general']['userplace_coupon_post_id']   = isset($plan_restrictions['userplace_coupon_post_id']) ? $plan_restrictions['userplace_coupon_post_id'] : '';
				}
			}
		}
		return apply_filters('userplace_current_user_plan_restriction_details', $restrictions_by_post_type);
	}
}
