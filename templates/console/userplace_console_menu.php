<?php
$payment_info = new Userplace\Template_Loader();

global $userplace_option_data;
$user          = wp_get_current_user();
$first_name    = get_user_meta($user->ID, 'first_name', true);
$last_name     = get_user_meta($user->ID, 'last_name', true);
$username      = $user->user_login;
$first_name    = get_user_meta($user->ID, 'first_name', true);
$last_name     = get_user_meta($user->ID, 'last_name', true);

$user_custom_gravatar = wp_get_attachment_image_url(get_user_meta($user->ID, 'user_custom_gravatar', true), 'thumbnail');
if ($user_custom_gravatar) {
	$custom_avatar_url = $user_custom_gravatar;
} else {
	$custom_avatar_url = USERPLACE_IMG . 'gravatar/profile-photo.png';
}
$profile_image = $custom_avatar_url;
$redirect = (isset($userplace_option_data['userplace_select_login_page']) && !empty($userplace_option_data['userplace_select_login_page']) ? $userplace_option_data['userplace_select_login_page'] : '');
if ($redirect != '') {
	$redirect_page = get_page_link($redirect);
} else {
	$redirect_page =  '';
}
$node = get_query_var('node', false);
?>

<!-- LEFTSIDE NAVIGATION -->
<nav class="up-userplace-leftside-nav">
	<div class="up-userplace-back2home">
		<a href="<?php echo esc_url(site_url()); ?>">
			<i class="ion ion-ios-arrow-round-back"></i>
			<span><?php echo esc_html__('Back to Home', 'userplace'); ?></span>
		</a>
		<div class="hamburger-box toggle-nav">
			<div class="hamburger-inner"></div>
		</div>
	</div>

	<?php
	$posts = array();
	$args = array(
		'posts_per_page'   => -1,
		'orderby' => 'title',
		'order'            => 'ASC',
		'post_type'        => 'userplace_console',
		'post_status'      => 'publish',
	);
	$posts = get_posts($args);
	$sidebar_console_menus = apply_filters('userplace_sidebar_console_menus', array(
		array(
			'url' => 'console',
			'icon' => 'ion ion-ios-stats',
			'name' => esc_html__('Console', 'userplace'),
		),
	));
	?>
	<ul class="up-userplace-nav-menu">
		<?php foreach ($sidebar_console_menus as $key => $menu) { ?>
			<?php $is_active = !$node ? 'active-page' : ''; ?>
			<li>
				<a href="<?php echo esc_url(site_url($menu['url'])); ?>" title="<?php echo esc_attr($menu['name']); ?>" class="<?php echo esc_attr($is_active); ?>">
					<i class="<?php echo isset($menu['icon']) ? $menu['icon'] : 'ion-ionic'; ?>"></i>
					<span><?php echo esc_html($menu['name']); ?></span>
				</a>
			</li>
		<?php } ?>
		<?php
		foreach ($posts as $post) {
			$console_meta = json_decode(get_post_meta($post->ID, '_userplace_console_builder', true), true);
			$console_menu_icon = 'ion-ios-arrow-right';
			if (!empty($console_meta)) {
				$console_meta_value = json_decode(stripslashes_deep(get_post_meta($post->ID, '_userplace_console_builder', true)), true);
				if (isset($console_meta_value['reuse_button_iconpicker']) && !empty($console_meta_value['reuse_button_iconpicker'])) {
					$console_menu_icon = $console_meta_value['reuse_button_iconpicker'];
				}
			}
		?>
			<?php
			$restricted_plans = (isset($console_meta['restricted_plans'])) ? explode(',', $console_meta['restricted_plans']) : [];
			$current_user       = wp_get_current_user();
			$user_id  = get_current_user_id();
			$user_subscribed_plan = $payment_info->getUserSubscriptionPlan($user_id);
			$is_active = $post->post_name === $node ? 'active-page' : '';
			if ($console_meta['form_type'] == 'parent' && ($current_user->roles[0] === 'administrator' || !in_array($user_subscribed_plan, $restricted_plans))) : ?>
				<li>
					<a href="<?php echo esc_url(get_site_url() . '/console/node/' . $post->post_name . '/'); ?>" title="<?php echo esc_attr($post->post_title); ?>" class="<?php echo esc_attr($is_active); ?>">
						<i class="<?php echo esc_attr($console_menu_icon) ?>"></i>
						<span><?php echo esc_attr($post->post_title); ?></span>
					</a>
				</li>
			<?php endif ?>
		<?php
		}
		?>
	</ul>
</nav>
<!-- LEFTSIDE NAVIGATION END -->
<!-- HEADER -->

<?php
$dropdown_menus = array(
	array(
		'url' => 'console/user-settings',
		'icon' => 'ion ion-md-create',
		'text' => esc_html__('Edit Profile', 'userplace'),
	),
	array(
		'url' => 'console/billing',
		'icon' => 'ion ion-ios-wallet',
		'text' => esc_html__('Billing', 'userplace'),
	),
	array(
		'url' => 'console/change-password',
		'icon' => 'ion ion-md-lock',
		'text' => esc_html__('Change Password', 'userplace'),
	),
);
$dropdown_menus = apply_filters('console_dropdown_menus', $dropdown_menus);
$displayName = $username;
if ($first_name || $last_name) {
	$displayName = $first_name . ' ' . $last_name;
}
?>
<header class="up-userplace-header-wrapper">
	<button type="button" class="toggle-nav">
		<span class="up-nav-bar"></span>
		<span class="up-nav-bar"></span>
		<span class="up-nav-bar"></span>
	</button>
	<div class="up-userplace-header-right">
		<?php do_action('userplace_openform_add_listing_button'); ?>
		<ul class="up-userplace-header-content">
			<li class="up-userplace-usericon up-dropdown--btn">
				<span class="up-userplace-userimg-wrapper">
					<img src="<?php echo isset($profile_image) && !empty($profile_image) ? $profile_image : '' ?>" alt="<?php esc_attr_e('User', 'userplace') ?>" height="36" width="36">
				</span>
				<ul class="up-userplace--dropdown">
					<li class="up-userplace-userinfo">
						<span class="up-userplace-userimg-wrapper">
							<img src="<?php echo isset($profile_image) && !empty($profile_image) ? esc_url($profile_image) : '' ?>" alt="<?php esc_attr_e('User', 'userplace') ?>" height="36" width="36">
						</span>
						<h3><?php echo esc_html($displayName) ?></h3>
						<span><?php echo esc_html($user->user_email) ?></span>
					</li>
					<li>
						<a href="<?php echo home_url() . '/user/' . $username ?>"><i class="ion ion-ios-person"></i><?php echo esc_html__('View Profile', 'userplace'); ?></a>
					</li>
					<?php foreach ($dropdown_menus as $key => $menu) { ?>
						<li>
							<a href="<?php echo home_url($menu['url']); ?>"><i class="<?php echo esc_attr($menu['icon']) ?>"></i><?php echo esc_html($menu['text']); ?></a>
						</li>
					<?php } ?>
					<?php do_action('userplace_console_menu_before_logout') ?>
					<li>
						<a href="<?php echo wp_logout_url($redirect_page); ?>"><i class="ion ion-ios-log-out"></i><?php echo esc_html__('Logout', 'userplace'); ?></a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</header>
<!-- HEADER END -->