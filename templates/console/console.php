<?php

include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_header.php');
?>

<!-- HEADER -->
<?php include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_menu.php'); ?>
<!-- HEADER END -->

<!-- MAIN CONTENT -->
<div class="up-userplace-main-content console-page-main-content">
	<?php
	$is_console_page 	= get_query_var('console');
	if ($is_console_page == 'yes') {
		$query_args = array(
			'post_type' 		=> 'userplace_template',
			'post_per_page' => 1,
			'meta_key'      => 'userplace_payment_template_select_type',
			'meta_value'    => 'console',
		);

		$the_query = get_posts($query_args);
		if ($the_query) {
			$template = get_post($the_query[0]->ID);
			if ($template) {
				echo do_shortcode('[userplace_welcome_message]');
				do_action('userplace_console_widget_before');
				echo do_shortcode($template->post_content);
			}
		} else if (current_user_can('administrator')) {
			echo 'You Haven\'t Created for Console Page PLease create one from <a href="' . admin_url() . '/edit.php?post_type=userplace_template"> Here</a>';
		}
	}
	?>
	<!-- # Notifier Widget End # -->

</div>
<!-- MAIN CONTENT END -->
</div>
<!-- CONTENTS WRAPPER END -->

<?php
include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_footer.php');
