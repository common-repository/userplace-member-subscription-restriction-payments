<?php

if (!defined('ABSPATH')) {
	exit;
}
add_shortcode('ufs_listing_map', 'render_listing_map');

function render_listing_map($atts)
{
	$atts = shortcode_atts(array(
		'title'   => 'Location',
		'post_id' => false,
		'help_text' => esc_html__('"Listing Location" is a very important information section. This section setup the geolocation of your listing for the users to view the correct direction on the map.Please, Do not skip this field.', 'userplace')
	), $atts);
	$atts = apply_filters('ufs_listing_map_atts', $atts);
	extract($atts);
	/**
	 * @var $title
	 * @var $post_id
	 * @var $preValue
	 */

	$preValue = isset($_SESSION['preValue']) ? stripslashes_deep($_SESSION['preValue']['meta__location']) : false;
	if (!$preValue) {
		$preValue = get_post_meta($post_id, 'location', true);
	}
?>
	<div class='ufs_field_row ufs_location_field'>
		<div class="ufs_field_holder">
			<label for="listing_map"><?php echo esc_attr($title) ?></label>
			<input id="listing_map" type="text" placeholder="<?php esc_attr_e('Type in an address', 'userplace') ?>" size="90" />
			<input id="meta__location" name="meta__location" type="hidden" value="<?php echo str_replace('"', "'", esc_attr($preValue)); ?>" />
			<div class="map_canvas" style="width: 100%; height: 500px"></div>
			<div class="ufs_location_info">
				<div class="ufs_location_info_field">
					<label for="map_country"><?php esc_html_e('Country', 'userplace') ?></label>
					<input id="map_country" type="text" value="" disabled />
				</div>
				<div class="ufs_location_info_field">
					<label for="map_city"><?php esc_html_e('City', 'userplace') ?></label>
					<input id="map_city" type="text" value="" disabled />
				</div>
				<div class="ufs_location_info_field">
					<label for="map_lat"><?php esc_html_e('Latitude', 'userplace') ?></label>
					<input id="map_lat" type="text" value="" disabled />
				</div>
				<div class="ufs_location_info_field">
					<label for="map_long"><?php esc_html_e('Longitude', 'userplace') ?></label>
					<input id="map_lng" type="text" value="" disabled />
				</div>
			</div>
		</div>
	</div>
<?php
}
