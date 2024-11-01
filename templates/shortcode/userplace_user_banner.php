<?php

extract(shortcode_atts(array(
	'banner_image'  => 'user_banner_image',
	'element_id'    => '',
	'element_class' => '',
), $atts));

global $userplace_user_id, $wpdb;

$id               = '';
$container_class  = 'rq-profile-page-banner ';
$banner_image     = get_user_meta($userplace_user_id, $banner_image, true);

if (!empty($banner_image[0]['url'])) {
	$banner_image = $banner_image[0]['url'];
}
if (!empty($element_id)) {
	$id = 'id="' . $id . '"';
}

if (!empty($element_class)) {
	$container_class .= $element_class;
}

$output = '';
$output .= '<div ' . esc_attr($id) . ' class="' . esc_attr(trim($container_class)) . '" style="background: linear-gradient(rgba(0, 0, 0, 0.65),rgba(0, 0, 0, 0.65)),url(' . esc_url($banner_image) . ') center center no-repeat; background-size: cover;">';
$output .= '</div>';

$allowed_html = [
	'div' 	=> [],
];

echo wp_kses($output, $allowed_html);
