<?php
$plan_page_id = userplace_get_settings('userplace_plan_page_url');

$plan_page_url = get_permalink($plan_page_id);

?>

<div class="rqBillingOverview up-userplace-widgets">
	<h3 class="rqPageTitle"><?php echo esc_html__('Billing Overview ', 'userplace'); ?></h3>

	<div class="up-userplace-widget-body">
		<div class="rqBillingInfoBlock">
			<p><strong><?php echo esc_html__('Current plan ', 'userplace'); ?></strong>
				<span><?php echo isset($customer['planName']) ? esc_html($customer['planName']) : '<span class="userplace-dot">----</span>'; ?></span>
			</p>

			<p><strong><?php echo esc_html__('Amount ', 'userplace'); ?></strong>
				<span>
					<?php
					if (isset($customer['planAmount'])) {
						$interval = is_int($customer['planInterval']) ? $customer['planInterval'] . ' Month' : $customer['planInterval'];
					}
					echo isset($customer['planAmount']) ? esc_html($customer['planAmount'] . ' ' . strtoupper($customer['planCurrency']) . '/' . $interval) : '<span class="userplace-dot">----</span>';
					?>
				</span>
			</p>

			<p><strong><?php echo esc_html__('Status ', 'userplace'); ?></strong>
				<span class="<?php echo isset($customer['status']) ? esc_attr(strtolower($customer['status'])) : ''; ?>"><?php echo isset($customer['status']) ? esc_html($customer['status']) : '<span class="userplace-dot">----</span>'; ?></span>
			</p>
		</div>
	</div>

	<div class="rqUserPlaceWidgetFooter">
		<a href="<?php echo esc_url($plan_page_url)  ?>" class="up-userplace-btn">
			<?php if (isset($customer['planName'])) { ?>
				<?php echo esc_html__('Change', 'userplace'); ?></a>
	<?php } else { ?>
		<?php echo esc_html__('Buy Plan', 'userplace'); ?></a>
	<?php } ?>
	</div>
</div>