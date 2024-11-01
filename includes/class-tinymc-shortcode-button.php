<?php

namespace Userplace;

class ExtendTinyMC
{
	public function __construct() {
		add_action('init', array($this, 'userplace_shortcode_button_init'));
	}

	public function userplace_shortcode_button_init() {
		//Abort early if the user will never see TinyMCE
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			return;

		//Add a callback to regiser our tinymce plugin
		add_filter("mce_external_plugins", array($this, "userplace_register_tinymce_plugin"));

		// Add a callback to add our button to the TinyMCE toolbar
		add_filter('mce_buttons', array($this, 'userplace_add_tinymce_button'));
	}

	//This callback registers our plug-in
	public function userplace_register_tinymce_plugin($plugin_array) {
		$plugin_array['restrict_content_shortcode_button'] = USERPLACE_JS_VENDOR.'extendTinyMc.js';
		return $plugin_array;
	}

	//This callback adds our button to the toolbar
	function userplace_add_tinymce_button($buttons) {
		//Add the button ID to the $button array
		$buttons[] = "restrict_content_shortcode_button";
		// $buttons[] = "login_button_shortcode";
		// $buttons[] = "register_form";
		// $buttons[] = "forget_pass_form";
		// $buttons[] = "password_reset_form";
		// $buttons[] = "userplace_user_main_profile";
		return $buttons;
	}

}
