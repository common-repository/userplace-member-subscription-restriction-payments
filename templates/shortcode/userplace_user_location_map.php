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
	'marker'        => '',
	'map_meta'      => '',
	'title'         => esc_html__('Position', 'userplace'),
	'height'        => '280px',
	'css'           => '',
	'element_id'    => '',
	'element_class' => '',
), $atts));

global $userplace_user_id, $wpdb;
$output             = '';
$id                 = '';
$container_class    = 'rq-listing-map-widget listing-widget-padding-map ';
$custom_css = '';
if (!empty($element_id)) {
	$id = 'id="' . $element_id . '"';
}
if (!empty($element_class)) {
	$container_class .= $element_class;
}

if (empty($marker)) {
	$marker_url = esc_url('http://maps.google.com/mapfiles/ms/icons/red-dot.png');
} else {
	$marker_url = wp_get_attachment_url($marker);
}

if (!empty($css)) {
	if (function_exists('vc_shortcode_custom_css_class')) {
		$custom_css = vc_shortcode_custom_css_class($css);
	}
	$container_class .= ' ' . $custom_css;
}

$map_object   = get_user_meta($userplace_user_id, $map_meta, true);

if (isset($map_object['lat']) && isset($map_object['lng'])) {
	$lat          =  $map_object['lat'];
	$lng          =  $map_object['lng'];
	$formattedAddress = $map_object['formattedAddress']; ?>

	<div <?php echo esc_attr($id) ?> class="<?php echo esc_attr($container_class) ?>">
		<div class="rq-contact-us-map">
			<div id="listingMap" style="height:<?php echo esc_attr($height) ?>"></div>
		</div>
	</div>

<?php
	// add inline scripts
	wp_add_inline_script(
		'userplace-js',
		"
      var myLatLng = {lat: " . esc_html($lat) . ", lng: " . esc_html($lng) . "};

      var map = new google.maps.Map(document.getElementById('listingMap'), {
        zoom: 15,
        center: myLatLng,
        scrollwheel: false,
      });

      var icon = '" . esc_url($marker_url) . "';
      var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        icon: icon,
        title: '" . esc_html($formattedAddress) . "'
      });"
	);
}
