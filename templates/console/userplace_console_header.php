<?php ob_start(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="frontend-wp-toolbar">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1">
	<title><?php echo esc_html__('Console page', 'userplace'); ?></title>
	<?php wp_head(); ?>
</head>

<?php $admin_class = current_user_can('administrator') ? 'userplace-admin-class' : ''; ?>
<?php
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (is_plugin_active('js_composer/js_composer.php')) { ?>

	<body class="wpb-js-composer">
	<?php
} else { ?>

		<body>
		<?php } ?>

		<?php if (wp_is_mobile()) { ?>
			<section class="up-userplace-frontend--section up-console-page up-console-mobile-page hide-nav">
			<?php } else { ?>
				<section class="up-userplace-frontend--section up-console-page  <?php echo esc_attr($admin_class) ?>">
				<?php } ?>
				<div class="up-userplace-inner-wrapper">
					<!-- CONTENTS WRAPPER -->
					<div class="up-userplace-contents-wrapper">