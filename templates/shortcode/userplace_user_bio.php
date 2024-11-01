<?php

/**
 * Component Post Content Shortcode
 *
 * Show the post content for dynamic generated template
 *
 * @author redqteam
 * @category Theme
 * @package Userplace/Shortcodes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}


extract(shortcode_atts(array(
	'title'         => esc_html__('Your Title Here', 'userplace'),
	'element_id'    => '',
	'element_class' => '',
), $atts));
global $userplace_user_id, $wpdb;

$output             = '';
$id                 = '';
$container_class    = 'rq-listing-about listing-content-padding ';
$user_description   = get_user_meta($userplace_user_id, 'user_description', true);

if (!empty($element_class)) {
	$container_class .= $element_class;
}
if (!empty($element_id)) {
	$id = 'id="' . $element_id . '"';
}

$output .= '<div ' . $id . ' class="' . esc_attr(trim($container_class)) . '">';
$output .= '<h2 class="single-sub-title">' . esc_html($title) . '</h2>';
$output .= apply_filters('the_content', $user_description);
$output .= '</div>';

$allowed_html = [
	'div' 	=> [],
	'img' 	=> [],
	'a'		=> [],
	'span'	=> [],
	'p'		=> [],
	'h1'	=> [],
	'h2'	=> [],
	'h3'	=> [],
	'h4'	=> []
];

echo wp_kses($output, $allowed_html);
