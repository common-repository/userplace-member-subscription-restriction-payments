<form method="POST" id="userplace_stripe_form">
	<input type="hidden" name="userplace_change_payment_method" value="1">
	<script
		src="https://checkout.stripe.com/checkout.js" class="stripe-button"
		data-key="<?php echo esc_attr($stripePublicKey); ?>"
		data-panel-label="Submit"
		data-label="Change card"
		data-locale="auto">
	</script>
</form>
