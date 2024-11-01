<?php


namespace Userplace;


class Integrations
{

	public function __construct()
	{
		add_action('listbook_topbar_console_menu', array($this, 'listbook_topbar_console_menu'), 10, 4);
	}

	public function listbook_topbar_console_menu($topbarJoinUs, $topbarAddListing, $className, $joinUsText)
	{
?>
		<div class="<?php echo esc_attr($className) ?>">
			<?php if ($topbarJoinUs !== 'off' && !is_user_logged_in()) { ?>
				<a class="listbook-join-us-btn <?php do_action('listbook_auth_trigger') ?> " href="#">
					<i class="ion ion-md-person-add"></i>
					<?php echo esc_html($joinUsText) ?>
				</a>
			<?php } ?>
			<?php if ($topbarJoinUs !== 'off' && is_user_logged_in()) { ?>
				<a class="listbook-join-us-btn" href="<?php echo esc_url(site_url() . '/console/') ?>">
					<i class="ion ion-md-person"></i>
					<?php echo esc_html__('Console', 'userplace') ?>
				</a>
			<?php } ?>
			<?php if ($topbarAddListing !== 'off') : ?>
				<?php do_action('userplace_add_listing_topbar_menu') ?>
			<?php endif; ?>
		</div>
<?php
	}
}
