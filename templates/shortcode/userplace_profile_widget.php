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
	'gravatar_image'      => 'user_custom_gravater',
	'first_name'          => 'meta_key:first_name',
	'last_name'           => 'meta_key:last_name',
	'address'             => 'meta_key:address',
	'url'                 => 'meta_key:user_url',
	'user_designation'    => 'meta_key:user_designation',
	'facebook_uri'        => 'meta_key:user_facebook_uri',
	'twitter_uri'         => 'meta_key:user_twitter_uri',
	'google_uri'          => 'meta_key:user_google_uri',
	'instagram_uri'       => 'meta_key:user_instagram_uri',
	'company_name'        => 'meta_key:user_company_name',
	'element_id'          => '',
	'element_class'       => '',
), $atts));

// User Profile Data
global $userplace_user_id, $wpdb;
$user_personal_details = json_decode(get_user_meta(get_current_user_id(), 'userplace_user_settings_prevalue', true), true);
$gravatar_image = get_user_meta($userplace_user_id, $gravatar_image, true);

if (!empty($gravatar_image[0]['url'])) {
	$gravatar_image = $gravatar_image[0]['url'];
} else {
	$gravatar_image = USERPLACE_IMG . 'gravatar/user-png.jpg';
}

$first_name       = userplace_process_user_string_data($first_name);
$last_name        = userplace_process_user_string_data($last_name);
$address          = userplace_process_user_string_data($address);
$url              = userplace_process_user_string_data($url);
$user_designation = userplace_process_user_string_data($user_designation);
$facebook_uri     = userplace_process_user_string_data($facebook_uri);
$twitter_uri      = userplace_process_user_string_data($twitter_uri);
$google_uri       = userplace_process_user_string_data($google_uri);
$instagram_uri    = userplace_process_user_string_data($instagram_uri);
$company_name     = userplace_process_user_string_data($company_name);

$full_name = $first_name . ' ' . $last_name;

$userplace_user_gravatar = !empty($user_personal_details['user_custom_gravater'][0]['url']) ? $user_personal_details['user_custom_gravater'][0]['url'] : $gravatar_image;
$userplace_user_designation = !empty($user_personal_details['user_designation']) ? $user_personal_details['user_designation'] : '';
$userplace_user_company_name = isset($user_personal_details['user_company_name']) ? $user_personal_details['user_company_name'] : '';
$userplace_user_description = !empty($user_personal_details['user_description']) ? $user_personal_details['user_description'] : '';
$userplace_user_address = isset($user_personal_details['address']) ? $user_personal_details['address'] : '';
$userplace_user_dob = isset($user_personal_details['user_dob']) ? $user_personal_details['user_dob'] : '';
$userplace_user_url = site_url() . '/user/' . wp_get_current_user()->user_login;

if ($full_name == ' ') {
	$full_name = get_query_var('user', '');
}

$output = '';

$container_class  = 'rq-userplace-main-profile ';
$id               = '';

if (!empty($element_id)) {
	$id = 'id="' . $element_id . '"';
}

if (!empty($element_class)) {
	$container_class .= $element_class;
}

$output .= '<div class="rqUserplaceProfileWidget up-userplace-widgets">';

$output .= '<h3 class="rqPageTitle">';
$output .= esc_html__(' User Profile ', 'userplace');
$output .= '</h3>';

$output .= '<div class="up-userplace-widget-body">';
$output .= '<div class="up-userplace-prfolieWidget-info">';
$output .= '<div class="up-user-information">';
$output .= '<h4>' . esc_html($full_name) . '</h4>';
$output .= '<div class="up-user-profile-details">';
if (!empty($userplace_user_designation) && !empty($userplace_user_company_name)) {
	$output .= '<p class="rq-userplace-company">';
	$output .= '<span class="up-label">' . esc_html__(' Job ', 'userplace') . '</span><span class="up-data"><span class="rq-userplace-user-designation">' . esc_html($userplace_user_designation) . '</span>';
	$output .= esc_html__(' at ', 'userplace');
	$output .= '<span class="rq-userplace-company-name">' . esc_html($userplace_user_company_name) . '</span></span>';
	$output .= '</p>';
} elseif (!empty($userplace_user_designation) || !empty($userplace_user_company_name)) {
	$output .= '<p class="rq-userplace-company">';
	$output .= '<span class="up-label">' . esc_html__(' Job ', 'userplace') . '</span><span class="up-data"><span class="rq-userplace-user-designation">' . esc_attr($userplace_user_designation) . '</span>';
	$output .= '<span class="rq-userplace-company-name">' . esc_html($userplace_user_company_name) . '</span></span>';
	$output .= '</p>';
} else {
	$output .= '';
}

if (!empty($userplace_user_dob) && isset($userplace_user_dob)) {
	$output .= '<p class="rq-userplace-user-dob">';
	$output .= '<span class="up-label">' . esc_html__(' Date of Birth ', 'userplace') . '</span>';
	$output .= '<span class="up-data">' . esc_html($userplace_user_dob) . '</span>';
	$output .= '</p>';
}

if (!empty($userplace_user_address) && isset($userplace_user_address)) {
	$output .= '<p class="rq-userplace-user-address">';
	$output .= '<span class="up-label">' . esc_html__(' Address ', 'userplace') . '</span>';
	$output .= '<span class="up-data">' . esc_html($userplace_user_address) . '</span>';
	$output .= '</p>';
}
$output .= '</div>';
$output .= '</div>';

$output .= '<div class="up-user-fw-info">';
$output .= '<div class="up-view-details-area">';
if (!empty($userplace_user_url) && isset($userplace_user_url)) {
	$output .= '<a class="up-userplace-btn" href="' . esc_url($userplace_user_url) . '">' . esc_html__(' View Details ', 'userplace') . '</a>';
}
$output .= '</div>';
$output .= '<div class="rq-userplace-image">';
$output .= '<img class="rq-founder-img" src="' . esc_url($userplace_user_gravatar) . '" alt="' . esc_attr($full_name) . '">';
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';
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
