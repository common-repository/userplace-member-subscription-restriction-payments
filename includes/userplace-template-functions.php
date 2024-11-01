<?php

/**
 * Userplace Template
 *
 * Functions for the templating system.
 *
 * @package  Userplace\Functions
 * @version  1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get template part (for templates like the shop-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function userplace_get_template_part($slug, $name = '')
{
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/userplace/slug-name.php
	if ($name && !USERPLACE_TEMPLATE_DEBUG_MODE) {
		$template = locate_template(array("{$slug}-{$name}.php", Userplace()->template_path() . "{$slug}-{$name}.php"));
	}

	// Get default slug-name.php
	if (!$template && $name && file_exists(Userplace()->plugin_path() . "/templates/{$slug}-{$name}.php")) {
		$template = Userplace()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/userplace/slug.php
	if (!$template && !USERPLACE_TEMPLATE_DEBUG_MODE) {
		$template = locate_template(array("{$slug}.php", Userplace()->template_path() . "{$slug}.php"));
	}

	// Allow 3rd party plugin filter template file from their plugin
	if ((!$template && USERPLACE_TEMPLATE_DEBUG_MODE) || $template) {
		$template = apply_filters('userplace_get_template_part', $template, $slug, $name);
	}

	if ($template) {
		load_template($template, false);
	}
}

/**
 * Get other templates
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function userplace_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
	if ($args && is_array($args)) {
		extract($args);
	}

	$located = userplace_locate_template($template_name, $template_path, $default_path);

	if (!file_exists($located)) {
		_doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters('userplace_get_template', $located, $template_name, $args, $template_path, $default_path);

	do_action('userplace_before_template_part', $template_name, $template_path, $located, $args);

	include($located);

	do_action('userplace_after_template_part', $template_name, $template_path, $located, $args);
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function userplace_locate_template($template_name, $template_path = '', $default_path = '')
{
	if (!$template_path) {
		$template_path = Userplace()->template_path();
	}

	if (!$default_path) {
		$default_path = Userplace()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit($template_path) . $template_name,
			$template_name
		)
	);

	// Get default template
	if (!$template || USERPLACE_TEMPLATE_DEBUG_MODE) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters('userplace_locate_template', $template, $template_name, $template_path);
}


/**
 * Enables template debug mode
 */
function userplace_template_debug_mode()
{
	if (!defined('USERPLACE_TEMPLATE_DEBUG_MODE')) {
		$status_options = get_option('userplace_status_options', array());
		if (!empty($status_options['template_debug_mode']) && current_user_can('manage_options')) {
			define('USERPLACE_TEMPLATE_DEBUG_MODE', true);
		} else {
			define('USERPLACE_TEMPLATE_DEBUG_MODE', false);
		}
	}
}
add_action('after_setup_theme', 'userplace_template_debug_mode', 20);


add_action('template_redirect', function () {
	if (is_singular('collection')) {
		global $wp_query;
		$page = (int) $wp_query->get('page');
		if ($page > 1) {
			// convert 'page' to 'paged'
			$wp_query->set('page', 1);
			$wp_query->set('paged', $page);
		}
		// prevent redirect
		remove_action('template_redirect', 'redirect_canonical');
	}
}, 0); // on priority 0 to remove 'redirect_canonical' added with priority 10


/**
 * Get a slug identifying the current theme.
 *
 * @since 1.0.0
 * @return string
 */
function userplace_get_theme_slug_for_templates()
{
	return apply_filters('userplace_theme_slug_for_templates', get_option('template'));
}


/**
 * Global
 */

if (!function_exists('userplace_output_content_wrapper')) {

	/**
	 * Output the start of the page wrapper.
	 */
	function userplace_output_content_wrapper()
	{
		userplace_get_template('global/wrapper-start.php');
	}
}

if (!function_exists('userplace_output_content_wrapper_end')) {

	/**
	 * Output the end of the page wrapper.
	 */
	function userplace_output_content_wrapper_end()
	{
		userplace_get_template('global/wrapper-end.php');
	}
}


/**
 * Post Restriction Notice
 */
if (!function_exists('userplace_post_restriction_notices')) {

	/**
	 * Output the admin notices for post submission restriction
	 */
	function userplace_post_restriction_notices($notice_args)
	{
		userplace_get_template('notice/post-restriction-notice.php',  $notice_args);
	}
}
