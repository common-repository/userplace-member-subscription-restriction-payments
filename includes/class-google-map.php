<?php

namespace Userplace;


class GoogleMapLoading
{
	public function __construct()
	{
		add_filter('userplace_settings_fields_array', array($this, 'add_google_map_field'));
	}

	public function add_google_map_field($fields)
	{
		$fields[] = array(
			'id' 		 => 'googlemap_api_key',
			'type' 		 => 'text',
			'label' 	 => esc_html__('Google Map API Key', 'userplace'),
			'menuId'	 => 'general',
			'param' 	 => 'googlemap_api_key',
			'multiple' 	 => false,
		);

		return $fields;
	}
}
