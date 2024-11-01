<?php

/**
 *
 */

namespace Userplace;

class Payment_Shortcode
{
	public function __construct()
	{
		add_shortcode('userplace_user_banner', array($this, 'user_banner'));
		add_shortcode('userplace_user_main_profile', array($this, 'user_main_profile'));
		add_shortcode('userplace_user_bio', array($this, 'user_bio'));
		add_shortcode('userplace_pricing_plan', array($this, 'pricing_plan'));
		add_shortcode('userplace_pricing_wrapper', array($this, 'pricing_wrapper'));
		add_shortcode('userplace_plan_button', array($this, 'plan_button'));
		add_shortcode('userplace_billing_details', array($this, 'billing_details'));
		add_shortcode('userplace_invoices', array($this, 'invoices'));
		add_shortcode('billing_overview', array($this, 'billing_overview'));
		add_shortcode('userplace_welcome_message', array($this, 'welcome_message'));
		add_shortcode('userplace_profile_widget', array($this, 'profile_widget'));
		add_shortcode('restrict_content', array($this, 'restrict_content'));
		add_shortcode('userplace_user_quick_contact', array($this, 'user_quick_contact'));
		add_shortcode('userplace_user_location_map', array($this, 'user_location_map'));
	}

	public function user_location_map($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = '/shortcode/userplace_user_location_map.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function restrict_content($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_restrict_content.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function user_quick_contact($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_user_quick_contact.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function profile_widget($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_profile_widget.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function welcome_message($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_welcome_message.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function billing_overview($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/billing_overview.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function invoices($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_invoices.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function billing_details($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_billing_details.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function pricing_wrapper($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_pricing_wrapper.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function plan_button($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_plan_button.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function pricing_plan($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_pricing_plan.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function user_bio($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_user_bio.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function user_main_profile($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		$template = 'shortcode/userplace_user_main_profile.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function user_banner($attributes, $content = null)
	{
		if (!$attributes) {
			$attributes = array();
		}
		ob_start();

		$template = 'shortcode/userplace_user_banner.php';

		userplace_get_template($template, array('atts' => $attributes, 'content' => $content));

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
