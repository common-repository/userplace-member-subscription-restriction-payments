<?php

/**
 * Userplace Template Hooks
 *
 * Action/filter hooks used for Userplace functions/templates.
 *
 * @author    RedQ,Inc
 * @category  Core
 * @package   Userplace/Templates
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


/**
 * Content Wrappers.
 *
 * @see userplace_output_content_wrapper()
 * @see userplace_output_content_wrapper_end()
 */
add_action('userplace_before_main_content', 'userplace_output_content_wrapper', 10);
add_action('userplace_after_main_content', 'userplace_output_content_wrapper_end', 10);

/**
 * Post Restriction Admin Notices
 *
 * @see userplace_post_restriction_notices()
 */
add_action('userplace_post_restriction_notice_args', 'userplace_post_restriction_notices', 10, 1);
