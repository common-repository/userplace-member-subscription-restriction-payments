<?php include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_header.php'); ?>

<!-- HEADER -->
<?php include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_menu.php'); ?>

<?php
$user = wp_get_current_user();
if ($_POST) {
	foreach ($_POST as $meta_key => $meta_value) {
		if ($meta_key !== 'user_custom_gravatar' && $meta_key !== 'user_banner_image' && $meta_key !== 'user_working_location') {
			update_user_meta($user->ID, sanitize_key($meta_key), sanitize_text_field($meta_value));
		}
		if ($meta_key === 'user_working_location') {
			update_user_meta($user->ID, sanitize_key($meta_key), json_decode(stripslashes_deep(sanitize_text_field($meta_value)), true));
		}
	}
}

if ($_FILES) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$user_banner_image = wp_check_filetype(basename($_FILES['user_banner_image']['name']));
	if (!empty($user_banner_image['ext'])) {
		if (!empty($_FILES['user_banner_image']['tmp_name'])) {
			$user_banner_image = media_handle_upload('user_banner_image', 0);
			update_user_meta($user->ID, 'user_banner_image', sanitize_text_field($user_banner_image));
		}
	}

	$user_custom_gravatar = wp_check_filetype(basename($_FILES['user_custom_gravatar']['name']));
	if (!empty($user_custom_gravatar['ext'])) {
		if (!empty($_FILES['user_custom_gravatar']['tmp_name'])) {
			$user_custom_gravatar = media_handle_upload('user_custom_gravatar', 0);
			update_user_meta($user->ID, 'user_custom_gravatar', sanitize_text_field($user_custom_gravatar));
		}
	}
}

$user_banner_image = wp_get_attachment_image_url(get_user_meta($user->ID, 'user_banner_image', true), 'thumbnail');
$user_custom_gravatar = wp_get_attachment_image_url(get_user_meta($user->ID, 'user_custom_gravatar', true), 'thumbnail');

if (!$user_banner_image) {
	$user_banner_image = USERPLACE_IMG . '1x1.png';
}
if (!$user_custom_gravatar) {
	$user_custom_gravatar = USERPLACE_IMG . '1x1.png';
}
$first_name = get_user_meta($user->ID, 'first_name', true);
$last_name = get_user_meta($user->ID, 'last_name', true);
$user_url = get_user_meta($user->ID, 'user_url', true);
$user_description = get_user_meta($user->ID, 'user_description', true);
$user_facebook_uri = get_user_meta($user->ID, 'user_facebook_uri', true);
$user_twitter_uri = get_user_meta($user->ID, 'user_twitter_uri', true);
$user_reddit_uri = get_user_meta($user->ID, 'user_reddit_uri', true);
$user_instagram_uri = get_user_meta($user->ID, 'user_instagram_uri', true);
$user_instagram_uri = get_user_meta($user->ID, 'user_instagram_uri', true);
$user_instagram_uri = get_user_meta($user->ID, 'user_instagram_uri', true);
$user_pinterest_uri = get_user_meta($user->ID, 'user_pinterest_uri', true);
$user_linkedin_uri = get_user_meta($user->ID, 'user_linkedin_uri', true);
$user_working_location = get_user_meta($user->ID, 'user_working_location', true);

?>

<div class="userplaceSettingsBar">
	<div id="userplace_user_settings_form">
		<!-- main tab -->
		<form method="POST" class="userplace_user__tab" enctype='multipart/form-data'>
			<!-- tab header -->
			<ul class="userplace_user__tab-header">
				<li class="userplace_user__tab-header-list active">
					<a href="#basic-info"> <?php esc_html_e('Basic Info', 'userplace') ?> </a>
				</li>
				<li class="userplace_user__tab-header-list">
					<a href="#bio"> <?php esc_html_e('Bio', 'userplace') ?></a>
				</li>
				<li class="userplace_user__tab-header-list">
					<a href="#social"> <?php esc_html_e('Social', 'userplace') ?></a>
				</li>
				<li class="userplace_user__tab-header-list">
					<a href="#location"> <?php esc_html_e('Location', 'userplace') ?></a>
				</li>
			</ul>
			<!-- end tab header -->
			<!-- tab content -->
			<div class="userplace_user__tab-content" id="basic-info">
				<div class="form-group">
					<label for="user_custom_gravatar"> <?php esc_html_e('Avatar', 'userplace') ?></label>
					<img src="<?php echo esc_url($user_custom_gravatar) ?>" id="user_custom_gravatar" height="150" width="150" alt="<?php esc_attr_e('Cover Image', 'userplace') ?>">
					<input type="file" style="display:none;" id="user_custom_gravatar_upload" name="user_custom_gravatar">
				</div>
				<div class="form-group">
					<label for="user_banner_image"> <?php esc_html_e('Cover', 'userplace') ?></label>
					<img src="<?php echo esc_url($user_banner_image) ?>" id="user_banner_image" height="150" width="150" alt="<?php esc_attr_e('Banner Image', 'userplace') ?>">
					<input type="file" style="display:none;" id="user_banner_image_upload" name="user_banner_image">
				</div>
				<div class="form-group">
					<label for="first_name"> <?php esc_html_e('First name', 'userplace') ?></label>
					<input type="text" name="first_name" value="<?php echo esc_attr($first_name) ?> ">
				</div>
				<div class="form-group">
					<label for="avatar"> <?php esc_html_e('Last name', 'userplace') ?></label>
					<input type="text" name="last_name" value=" <?php echo esc_attr($last_name) ?>">
				</div>
			</div>
			<div class="userplace_user__tab-content" id="bio">
				<div class="form-group">
					<label for="user_url"> <?php esc_html_e('Website Url', 'userplace') ?></label>
					<input type="text" name="user_url" value="<?php echo esc_attr($user_url) ?>">
				</div>
				<div class="form-group">
					<label for="user_description"> <?php esc_html_e('Description About You', 'userplace') ?></label>
					<textarea name="user_description"><?php echo esc_html($user_description) ?></textarea>
				</div>
			</div>
			<div class="userplace_user__tab-content" id="social">
				<div class="form-group">
					<label for="user_facebook_uri"> <?php esc_html_e('Facebook URI', 'userplace') ?></label>
					<input type="text" name="user_facebook_uri" value="<?php echo esc_attr($user_facebook_uri) ?>">
				</div>
				<div class="form-group">
					<label for="user_facebook_uri"> <?php esc_html_e('Twitter URI', 'userplace') ?></label>
					<input type="text" name="user_twitter_uri" value="<?php echo esc_attr($user_twitter_uri) ?>">
				</div>
				<div class="form-group">
					<label for="user_facebook_uri"> <?php esc_html_e('Reddit URI', 'userplace') ?></label>
					<input type="text" name="user_reddit_uri" value="<?php echo esc_attr($user_reddit_uri) ?>">
				</div>
				<div class="form-group">
					<label for="user_facebook_uri"> <?php esc_html_e('Instagram URI', 'userplace') ?></label>
					<input type="text" name="user_instagram_uri" value="<?php echo esc_attr($user_instagram_uri) ?>">
				</div>
				<div class="form-group">
					<label for="user_facebook_uri"> <?php esc_html_e('Pinterest URI', 'userplace') ?></label>
					<input type="text" name="user_pinterest_uri" value="<?php echo esc_attr($user_pinterest_uri) ?>">
				</div>
				<div class="form-group">
					<label for="user_facebook_uri"> <?php esc_html_e('LinkedIn URI', 'userplace') ?></label>
					<input type="text" name="user_linkedin_uri" value="<?php echo esc_attr($user_linkedin_uri) ?>">
				</div>
			</div>
			<div class="userplace_user__tab-content" id="location">
				<div class="form-group form-group-icon">
					<i class="ion ion-md-search"></i>
					<input type="text" id="userplace_user_location_field">
				</div>
				<div class="map_canvas" style="width: 100%; height: 500px"></div>
				<div class="form-group form-group">
					<label for="map_country"> <?php esc_html_e('Country', 'userplace') ?></label>
					<input id="map_country" type="text" value="" disabled />
				</div>
				<div class="form-group form-group">
					<label for="map_city"> <?php esc_html_e('City', 'userplace') ?></label>
					<input id="map_city" type="text" value="" disabled />
				</div>
				<div class="form-group form-group">
					<label for="map_lat"> <?php esc_html_e('Latitude', 'userplace') ?></label>
					<input id="map_lat" type="text" value="" disabled />
				</div>
				<div class="form-group form-group">
					<label for="map_long"> <?php esc_html_e('Longitude', 'userplace') ?></label>
					<input id="map_lng" type="text" value="" disabled />
				</div>
				<input id="user_working_location" name="user_working_location" type="hidden" value="<?php echo str_replace('"', "'", esc_attr(json_encode($user_working_location))); ?>" />
			</div>
			<div class="form-group">
				<button type="submit"> <?php esc_html_e('Update Profile', 'userplace') ?></button>
			</div>
			<!-- end tab content -->
		</form>
		<!-- end main tab -->
	</div>
</div>
<!-- Footer -->
<?php
include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_footer.php');
