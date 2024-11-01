<form method="POST" id="userplace_stripe_form">
	<input type="hidden" name="userplace_payment_form" value="1">
	<input type="hidden" name="userplace_payment_plan" value="<?php echo esc_attr(sanitize_text_field($_GET['plan'])); ?>">
	<script src="https://checkout.stripe.com/checkout.js" class="stripe-button" data-key="<?php echo esc_attr($stripePublicKey); ?>" data-name="userplace inc" data-description="Widget" data-image="https://stripe.com/img/documentation/checkout/marketplace.png" data-locale="auto">
	</script>
</form>