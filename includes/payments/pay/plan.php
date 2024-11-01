<div class="up-userplace-plan-title">
	<h2><?php echo esc_html($plandata['name']); ?></h2>
</div>

<div class="up-userplace-plan-details">
	<div class="up-userplace-plan-row">
		<div class="up-userplace-pricing-total">
			<h2><?php echo '<span class="up-pricing-total-amount">' .esc_html($plandata['amount']).' '.esc_html($plandata['currency']).'</span> /'.esc_html($plandata['interval_count']). ' '.esc_html($plandata['interval']); ?></h2>
		</div>
	</div>
</div>