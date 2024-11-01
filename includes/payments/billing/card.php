<?php
if (isset($customer['currentPeriodEnd'])) {
	$date = date_format(\Carbon\Carbon::parse($customer['currentPeriodEnd'], 'UTC'), 'M j, Y h:m:s A');
}
?>

<div class="rqPaymentCard up-userplace-widgets">
	<h3 class="rqPageTitle"><?php echo esc_html__('Payment ', 'userplace'); ?></h3>
	<div class="up-userplace-widget-body">
		<div class="up-userplace-paymentCard">
			<div class="paymentCard-data">
				<p><strong><?php echo esc_html__('Card No ', 'userplace'); ?></strong><span><?php echo isset($card_brand) ? esc_html($card_brand . ' ' . '**** **** **** ' . $last4) : '<span class="userplace-dot">----</span>'; ?></span></p>
				<p><strong><?php echo esc_html__('Expiration ', 'userplace'); ?></strong><span> <?php echo isset($expired_at) ? esc_html($expired_at) : '<span class="userplace-dot">----</span>'; ?></span></p>
				<p><strong><?php echo esc_html__('Next payment ', 'userplace'); ?></strong><span> <?php echo isset($customer['currentPeriodEnd']) ? esc_html($date) : '<span class="userplace-dot">----</span>'; ?></span></p>
			</div>
			<?php
			if (isset($this->gateway) && $this->gateway === 'stripe') {
				$languages = array(
					'panelLabel' => esc_html__('Submit', 'userplace'),
					'label' => esc_html__('Update', 'userplace'),
					'locale' => 'auto',
				);
				if (isset($customer['currentPeriodEnd'])) {
					$this->billing->showCardChangeBox($languages);
				}
			}
			?>
		</div>
	</div>
</div>