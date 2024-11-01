<?php
$userplace_settings_feedback_general_signup = '';
$userplace_settings_feedback_general_signup = json_decode(get_option('userplace_settings'), true);
$userplace_general_signup_note = '';
$userplace_general_signup_note = !empty($userplace_settings_feedback_general_signup['signin_general_signup_note']) ? $userplace_settings_feedback_general_signup['signin_general_signup_note'] : esc_html__('Note: Your password generation link will be sent to your email address automatically.', 'userplace');
?>
<div class="rq-userplace-register-form-container">
	<div class="rq-userplace-register-form">
		<div id="register-form">
			<?php if ($attributes['show_title']) : ?>
				<h3><?php esc_html_e('Register', 'userplace'); ?></h3>
			<?php endif; ?>

			<?php if (count($attributes['errors']) > 0) : ?>
				<?php foreach ($attributes['errors'] as $error) : ?>
					<p class="login-error">
						<?php echo esc_html($error); ?>
					</p>
				<?php endforeach; ?>
			<?php endif; ?>

			<form id="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
				<p class="form-row">
					<label for="username"><?php esc_html_e('Username', 'userplace'); ?> <strong>*</strong></label>
					<input type="text" name="username" id="username" required="">
				</p>
				<p class="form-row">
					<label for="register-email"><?php esc_html_e('Email', 'userplace'); ?> <strong>*</strong></label>
					<input type="text" name="email" id="register-email" required="">
				</p>

				<p class="form-row">
					<label for="first-name"><?php esc_html_e('First name', 'userplace'); ?></label>
					<input type="text" name="first_name" id="first-name">
				</p>

				<p class="form-row">
					<label for="last-name"><?php esc_html_e('Last name', 'userplace'); ?></label>
					<input type="text" name="last_name" id="last-name">
				</p>

				<p class="form-row noted-text">
					<?php
					if (!empty($userplace_general_signup_note)) {
						echo esc_html($userplace_general_signup_note);
					} else {
						esc_html_e('Note: Your password generation link will be sent to your email address automatically.', 'userplace');
					}
					?>
				</p>

				<?php do_action('userplace_register_form_before_submit_button', $attributes); ?>

				<p class="signup-submit">
					<input type="submit" name="submit" class="register-button" value="<?php esc_html_e('Register', 'userplace'); ?>" />
				</p>
			</form>
			<?php do_action('userplace_after_register'); ?>
			<?php if (!$attributes['show_in_popup']) { ?>
				<p>
					<a class="new-account" href="<?php echo esc_url($attributes['login_page']); ?>">
						<?php
						esc_html_e("Already have an account? Login Here.", 'userplace')
						?>
					</a>
				</p>
			<?php } ?>
		</div>
	</div>
</div>