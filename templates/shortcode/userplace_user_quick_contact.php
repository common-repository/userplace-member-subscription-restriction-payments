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
  'title'         => esc_html__('Quick Contact', 'userplace'),
  'element_id'    => '',
  'element_class' => '',
), $atts));
global $userplace_user_id, $wpdb;
$output             = '';
$id                 = '';
$container_class    = 'rq-listing-contact-widget listing-widget-padding ';

if (!empty($element_id)) {
  $id = 'id="' . $element_id . '"';
}
if (!empty($element_class)) {
  $container_class .= $element_class;
}

$user_email = get_the_author_meta('user_email', $userplace_user_id);

$output .= '<div ' . esc_attr($id) . ' class="' . esc_attr(trim($container_class)) . '">';
$output .= '<h4>' . esc_html($title) . '</h4>';
$output .= '<form id="listing-message-form" method="POST">';
$output .= '<fieldset>';
$output .= '<input type="text" required name="listingContactName" placeholder="' . esc_attr__('Name', 'userplace') . '">';
$output .= '<input type="hidden" name="listingContactAuthor" placeholder="' . esc_attr__('Author email', 'userplace') . '" value="' . esc_attr($user_email) . '">';
$output .= '<input type="email" required name="listingContactEmail" placeholder="' . esc_attr__('Email', 'userplace') . '">';
$output .= '</fieldset>';
$output .= '<textarea required name="listingContactMessage" cols="30" rows="10" placeholder="' . esc_attr__('Your Comment', 'userplace') . '"></textarea>';
$output .= '<button type="submit" class="rq-listing-btn">' . esc_html__('Submit', 'userplace') . '<i class="spinner fa fa-spinner fa-spin"></i></button>';
$output .= '</form>';
$output .= '<div id="listing-contact-error"><ul></ul></div>';
$output .= '</div>';

$allowed_html = [
  'div'   => [],
  'h4'    => [],
  'form'  => [],
  'button'  => [],
  'textarea'  => [],
  'input'  => [],
  'fieldset'  => [],
];

echo wp_kses($output, $allowed_html);
