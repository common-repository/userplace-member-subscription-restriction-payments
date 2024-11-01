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


global $userplace_user_id, $wpdb;
$username = get_query_var('user');


$get_user =  get_user_by('login', $username);
$userplace_user_id = $get_user->ID;
$user_personal_details = json_decode(get_user_meta($get_user->ID, 'userplace_user_settings_prevalue', true), true);

// User Profile Data
// $gravatar_image             = USERPLACE_IMG . 'gravatar/profile-photo.png';
// $first_name                 = isset($user_personal_details['first_name']) ? $user_personal_details['first_name'] : '';
// $last_name                  = isset($user_personal_details['last_name']) ? $user_personal_details['last_name'] : '';
// $displayName                  = $first_name  . ' ' . $last_name;
// $company_name               = isset($user_personal_details['user_company_name']) ? $user_personal_details['user_company_name'] : '';
// $user_url                        = isset($user_personal_details['user_url']) ? $user_personal_details['user_url'] : '';
// $userplace_user_banner      = !empty($user_personal_details['user_banner_image'][0]['url']) ? $user_personal_details['user_banner_image'][0]['url'] : USERPLACE_IMG . 'gravatar/cover.png';;
// $userplace_user_gravatar    = !empty($user_personal_details['user_custom_gravater'][0]['url']) ? $user_personal_details['user_custom_gravater'][0]['url'] : $gravatar_image;
// $userplace_user_designation = !empty($user_personal_details['user_designation']) ? $user_personal_details['user_designation'] : '';
// $userplace_user_description = !empty($user_personal_details['user_description']) ? $user_personal_details['user_description'] : '';
// $user_facebook_uri               = !empty($user_personal_details['user_facebook_uri']) ? $user_personal_details['user_facebook_uri'] : '';
// $user_twitter_uri                = !empty($user_personal_details['user_twitter_uri']) ? $user_personal_details['user_twitter_uri'] : '';
// $google_uri                 = !empty($user_personal_details['user_google_uri']) ? $user_personal_details['user_google_uri'] : '';
// $user_instagram_uri              = !empty($user_personal_details['user_instagram_uri']) ? $user_personal_details['user_instagram_uri'] : '';
// $user_gender                = !empty($user_personal_details['user_gender']) ? $user_personal_details['user_gender'] : '';
// $user_dob                   = !empty($user_personal_details['user_dob']) ? $user_personal_details['user_dob'] : '';
// $user_gender                = !empty($user_personal_details['user_gender']) ? $user_personal_details['user_gender'] : '';
// $user_timezone              = !empty($user_personal_details['user_timezone']) ? $user_personal_details['user_timezone'] : '';
// $userplace_timezones        = userplace_timezones();

$user_custom_gravatar = wp_get_attachment_image_url(get_user_meta($userplace_user_id, 'user_custom_gravatar', true), 'thumbnail');
if ($user_custom_gravatar) {
	$userplace_user_gravatar = $user_custom_gravatar;
} else {
	$userplace_user_gravatar = USERPLACE_IMG . 'gravatar/profile-photo.png';
}
$user_banner_image = wp_get_attachment_image_url(get_user_meta($userplace_user_id, 'user_banner_image', true), 'thumbnail');
if ($user_banner_image) {
	$userplace_user_banner = $user_banner_image;
} else {
	$userplace_user_banner = USERPLACE_IMG . 'gravatar/cover.png';
}
$first_name = get_user_meta($userplace_user_id, 'first_name', true);
$last_name = get_user_meta($userplace_user_id, 'last_name', true);
$user_url = get_user_meta($userplace_user_id, 'user_url', true);
$user_description = get_user_meta($userplace_user_id, 'user_description', true);
$user_facebook_uri = get_user_meta($userplace_user_id, 'user_facebook_uri', true);
$user_twitter_uri = get_user_meta($userplace_user_id, 'user_twitter_uri', true);
$user_reddit_uri = get_user_meta($userplace_user_id, 'user_reddit_uri', true);
$user_instagram_uri = get_user_meta($userplace_user_id, 'user_instagram_uri', true);
$user_pinterest_uri = get_user_meta($userplace_user_id, 'user_pinterest_uri', true);
$user_linkedin_uri = get_user_meta($userplace_user_id, 'user_linkedin_uri', true);
$displayName = $username;
if ($first_name || $last_name) {
	$displayName = $first_name . ' ' . $last_name;
}

$output           = '';
$container_class  = 'rq-userplace-main-profile ';
$id               = '';

if (!empty($element_id)) {
	$id = 'id="' . $element_id . '"';
}

if (!empty($element_class)) {
	$container_class .= $element_class;
}

$output .= '<div ' . $id . ' class="' . trim($container_class) . '">';

$userplace_addClass_for_profile = "withoutBannerImage";

if (!empty($userplace_user_banner) && !empty($userplace_user_gravatar)) {
	$userplace_addClass_for_profile = "withBannerImage";
}
$output .= '<div class="rq-userplace-main-profile-info ' . esc_attr($userplace_addClass_for_profile) . '">';
if (!empty($userplace_user_banner) && !empty($userplace_user_gravatar)) {
	$output .= '<div class="rq-userplace-userBanner-withAvatar" style="background-image: url(' . esc_url($userplace_user_banner) . '); background-position: center center; background-repeat: no-repeat; background-size: cover">';
	$output .= '</div>';
	$output .= '<div class="rq-userplace-bannertext-area">';
	$current_user = wp_get_current_user();

	$output .= '<div class="rq-userplace-user-profile-info">';
	$output .= '<div class="rq-userplace-image">';
	$output .= '<img class="rq-founder-img" src="' . esc_url($userplace_user_gravatar) . '" alt="' . esc_attr($displayName) . '">';
	$output .= '</div>';



	$output .= '<div class="rq-userplace-banner-data">';
	$output .= '<h4 class="rq-userplace-user-name"><a href="' . esc_url($user_url) . '">' . esc_attr($displayName) . '</a></h4>';
	if ($user_url != '') {
		$output .= '<a class="userplace-user-url" href="' . esc_url($user_url) . '"><i class="ion-link"></i>' . esc_url($user_url) . '</a>';
	}

	if (!empty($userplace_user_designation) && !empty($company_name)) {
		$output .= '<div class="rq-userplace-des-company">';
		$output .= '<i class="ion-briefcase"></i><span class="rq-userplace-user-designation">' . esc_html($userplace_user_designation) . '</span>';
		$output .= esc_html__(' at ', 'userplace');
		$output .= '<span class="rq-userplace-company-name">' . esc_html($company_name) . '</span>';
		$output .= '</div>';
	} elseif (!empty($userplace_user_designation) || !empty($company_name)) {
		$output .= '<div class="rq-userplace-des-company">';
		$output .= '<i class="ion-briefcase"></i><span class="rq-userplace-user-designation">' . esc_html($userplace_user_designation) . '</span>';
		$output .= '<span class="rq-userplace-company-name">' . esc_html($company_name) . '</span>';
		$output .= '</div>';
	} else {
		$output .= '';
	}

	$output .= '</div>';

	$output .= '</div>';
	$output .= '</div>';
} else {
	$output .= '<div class="rq-userplace-only-image">';
	$output .= '<img class="rq-founder-img" src="' . esc_url($userplace_user_gravatar) . '" alt="' . esc_attr($displayName) . '">';
	$output .= '</div>';

	$output .= '<div class="rq-userplace-userIntro">';
	$output .= '<h4 class="rq-userplace-user-name"><a href="' . esc_url($user_url) . '">' . esc_html($displayName) . '</a></h4>';
	if (!empty($userplace_user_designation) && !empty($company_name)) {
		$output .= '<div class="rq-userplace-des-company">';
		$output .= '<span class="rq-userplace-user-designation">' . esc_html($userplace_user_designation) . '</span>';
		$output .= esc_html__(' at ', 'userplace');
		$output .= '<span class="rq-userplace-company-name">' . esc_html($company_name) . '</span>';
		$output .= '</div>';
	} elseif (!empty($userplace_user_designation) || !empty($company_name)) {
		$output .= '<div class="rq-userplace-des-company">';
		$output .= '<span class="rq-userplace-user-designation">' . esc_html($userplace_user_designation) . '</span>';
		$output .= '<span class="rq-userplace-company-name">' . esc_html($company_name) . '</span>';
		$output .= '</div>';
	} else {
		$output .= '';
	}
	$output .= '</div>';
}

$output .= '<div class="rq-userplace-user-details">';


if (!empty($userplace_user_description) && isset($userplace_user_description)) {
	$output .= '<div class="rq-userplace-aboutMe-info rq-userplace-prfContent">';
	$output .= '<h3>' . esc_html__(' About Me ', 'userplace') . '</h3>';
	$output .= '<p class="rq-userplace-des-info">' . esc_html($userplace_user_description) . '</p>';
	$output .= '</div>';
};

if (!empty($user_gender)) {
	$output .= '<p class="userplace-profile-info"><strong>' . esc_html__('Gender : ', 'userplace') . '</strong>' . $user_gender . '</p>';
}
if (!empty($user_dob)) {
	$output .= '<p class="userplace-profile-info"><strong>' . esc_html__('Date of birth :  ', 'userplace') . '</strong>' . $user_dob . '</p>';
}
if (!empty($user_timezone)) {
	$output .= '<p class="userplace-profile-info"><strong>' .  esc_html__('Timezone : ', 'userplace') . '</strong>' . $userplace_timezones[$user_timezone] . '</p>';
}

if ($user_facebook_uri != '' || $user_twitter_uri != '' || $user_instagram_uri != '') {
	$output .= '<div class="rq-userplace-share-icon">';
	$output .= '<span class="rq-social-title">' . esc_html__('Social', 'userplace') . '</span>';
	$output .= '<ul class="social-page-nav">';
	$output .= (!empty($user_facebook_uri)) ? '<li><a href="' . esc_url($user_facebook_uri) . '" class="facebook"><i class="ion ion-logo-facebook"></i></a></li>' : '';
	$output .= (!empty($user_twitter_uri)) ? '<li><a href="' . esc_url($user_twitter_uri) . '" class="twitter"><i class="ion ion-logo-twitter"></i></a></li>' : '';
	$output .= (!empty($user_instagram_uri)) ? '<li><a href="' . esc_url($user_instagram_uri) . '" class="instagram"><i class="ion ion-logo-instagram"></i></a></li>' : '';
	$output .= '</ul>';
	$output .= '</div>';
}

$output .= '<div class="rq-userplace-share-button">';
if (is_user_logged_in()) {
	$following_user_id  = $userplace_user_id;
	$current_user_id    = get_current_user_id();
	$attrs              = '';
	if ($following_user_id == $current_user_id) {
		$attrs = 'disabled="disabled"';
	}

	$current_following_list = get_user_meta($current_user_id, 'following_list', true);
	$current_following_list = $current_following_list === '' ? [] : $current_following_list;
	if (in_array($following_user_id,  $current_following_list)) {
		$following = 'Following';
	} else {
		$following = 'Follow';
	}
}

$output .= '</div>';
$output .= '</div>';
$output .= do_shortcode('[userplace_user_location_map map_meta="user_working_location"]');
if ($userplace_payment_username = $current_user->user_login) {

	$output .= '<a class="userplace-profile-edit" href="' . site_url() . '/console/user-settings/">' . esc_html__('Edit', 'userplace') . '</a>';
}
$output .= '</div>';

$output .= '</div>';

$allowed_html = [
	'div' 	=> [],
	'img' 	=> [],
	'a'		=> [],
	'span'	=> [],
	'p'		=> [],
	'ul'	=> [],
	'li'	=> [],
	'h1'	=> [],
	'h2'	=> [],
	'h3'	=> [],
	'h4'	=> []
];

echo wp_kses($output, $allowed_html);
