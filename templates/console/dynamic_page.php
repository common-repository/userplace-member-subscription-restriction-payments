<?php include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_header.php'); ?>
<?php include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_menu.php'); ?>

<?php
$node_name = get_query_var('node');
$args = array(
	'posts_per_page'   => -1,
	'post_type'     => 'userplace_console',
	'name'        => $node_name
);
$console_posts =  get_posts($args);
$post_data = array();
if (!empty($console_posts))
	$post_data = array(
		'title'     => $console_posts[0]->post_title,
		'shortcode'   => $console_posts[0]->post_content,
	);
?>
<?php if (isset($post_data['shortcode']) && !empty($post_data['shortcode'])) { ?>
	<div class="up-userplace-main-content">
	<?php echo do_shortcode($post_data['shortcode']);
}

include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_footer.php');
