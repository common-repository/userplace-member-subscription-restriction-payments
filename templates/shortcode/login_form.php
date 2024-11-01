<?php
ob_start();
$userplace_settings_feedback_general_login = '';
$userplace_settings_feedback_general_login = json_decode(get_option('userplace_settings'), true);

$userplace_logged_out = '';
$userplace_force_login = '';
$userplace_lost_password_sent = '';
$userplace_password_updated = '';
$userplace_registered = '';
$userplace_forget_pass = '';

$userplace_logged_out = !empty($userplace_settings_feedback_general_login['signin_general_after_logged_out']) ? $userplace_settings_feedback_general_login['signin_general_after_logged_out'] : esc_html__('You have signed out. Would you like to sign in again?', 'userplace');
$userplace_force_login = !empty($userplace_settings_feedback_general_login['signin_general_after_force_login']) ? $userplace_settings_feedback_general_login['signin_general_after_force_login'] : esc_html__('You have to signin if you want to access this page.', 'userplace');
$userplace_lost_password_sent = !empty($userplace_settings_feedback_general_login['signin_general_lost_pass_sent']) ? $userplace_settings_feedback_general_login['signin_general_lost_pass_sent'] : esc_html__('Check your email for a link to reset your password.', 'userplace');
$userplace_password_updated = !empty($userplace_settings_feedback_general_login['signin_general_pass_update']) ? $userplace_settings_feedback_general_login['signin_general_pass_update'] : esc_html__('Your password has been updated. You can sign in now.', 'userplace');
$userplace_registered = !empty($userplace_settings_feedback_general_login['signin_general_currently_registered']) ? $userplace_settings_feedback_general_login['signin_general_currently_registered'] : esc_html__('You have successfully registered. Please check your email for setting up your password.', 'userplace');
$userplace_forget_pass = !empty($userplace_settings_feedback_general_login['signin_general_forget_pass']) ? $userplace_settings_feedback_general_login['signin_general_forget_pass'] : esc_html__('Forgot your password?', 'userplace');
?>

<div class="rq-userplace-login-form-container">
	<div class="rq-userplace-login-form">
		<?php if ($attributes['show_title']) : ?>
			<h2><?php esc_html_e('Sign In', 'userplace'); ?></h2>
		<?php endif; ?>

		<!-- Show errors if there are any -->
		<?php if (count($attributes['errors']) > 0) : ?>
			<?php foreach ($attributes['errors'] as $error) : ?>
				<p class="login-error">
					<?php echo esc_html($error); ?>
				</p>
			<?php endforeach; ?>
		<?php endif; ?>

		<!-- Show logged out message if user just logged out -->
		<?php if ($attributes['logged_out']) : ?>
			<p class="login-info">
				<?php
				if (!empty($userplace_logged_out)) {
					echo esc_html($userplace_logged_out);
				} else {
					esc_html_e('You have signed out. Would you like to sign in again?', 'userplace');
				}
				?>
			</p>
		<?php endif; ?>

		<!-- Show custom redirect message if user forced to login -->
		<?php if ($attributes['redirect_to']) : ?>
			<p class="login-info">
				<?php
				if (!empty($userplace_force_login)) {
					echo esc_html($userplace_force_login) . ' ' . $attributes['redirect_to'];
				} else {
					esc_html_e('You have signed out. Would you like to sign in again?', 'userplace');
				}
				?>
			</p>
		<?php endif; ?>

		<?php if ($attributes['lost_password_sent']) : ?>
			<p class="login-info">
				<?php
				if (!empty($userplace_lost_password_sent)) {
					echo esc_html($userplace_lost_password_sent);
				} else {
					esc_html_e('Check your email for a link to reset your password.', 'userplace');
				}
				?>
			</p>
		<?php endif; ?>

		<?php if ($attributes['password_updated']) : ?>
			<p class="login-info">
				<?php
				if (!empty($userplace_password_updated)) {
					echo esc_attr($userplace_password_updated);
				} else {
					esc_html_e('Your password has been updated. You can sign in now.', 'userplace');
				}

				?>
			</p>
		<?php endif; ?>

		<?php if ($attributes['registered']) : ?>
			<p class="login-info">
				<?php
				if (!empty($userplace_registered)) {
					echo esc_html($userplace_registered);
				} else {
					esc_html_e('You have successfully registered. Please check your email for setting up your password.', 'userplace');
				}
				?>
			</p>
		<?php endif; ?>

		<?php
		wp_login_form(
			apply_filters('userplace_login_from_render', array(
				'label_username' 	=> esc_html__('Username/Email Address', 'userplace'),
				'label_log_in' 		=> esc_html__('Sign In', 'userplace'),
				'redirect'	 		=> $attributes['redirect'],
				'value_username' 	=> ($attributes['registered']) ? $attributes['registered'] : '',
			))
		);
		?>
		<?php do_action('userplace_after_login'); ?>
		<a class="forgot-password" href="<?php echo wp_lostpassword_url(); ?>">
			<?php
			if (!empty($userplace_forget_pass)) {
				echo esc_html($userplace_forget_pass);
			} else {
				esc_html_e('Forgot your password?', 'userplace');
			}
			?>
		</a>
		<?php if (!$attributes['show_in_popup']) { ?>
			<p>
				<a class="new-account" href="<?php echo esc_url($attributes['register_page']); ?>">
					<?php
					esc_html_e("Don't have any account? Register Here.", 'userplace')
					?>
				</a>
			</p>
		<?php } ?>
	</div>
</div>

<?php ob_flush();
