<?php
$userplace_settings_lost_pass_general = '';
$userplace_settings_lost_pass_general = json_decode(get_option('userplace_settings'), true);
$userplace_general_lost_pass_note = '';
$userplace_general_lost_pass_note = !empty($userplace_settings_lost_pass_general['signin_general_lost_empty_username']) ? $userplace_settings_lost_pass_general['signin_general_lost_empty_username'] : esc_html__('Note: Your password generation link will be sent to your email address automatically.', 'userplace');
?>
<div id="password-lost-form">
    <?php if ($attributes['show_title']) : ?>
        <h3><?php esc_html_e('Forgot Your Password?', 'userplace'); ?></h3>
    <?php endif; ?>

    <?php if (count($attributes['errors']) > 0) : ?>
        <?php foreach ($attributes['errors'] as $error) : ?>
            <p class="login-error">
                <?php echo esc_html($error); ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
    <p class="login-warning">
        <?php
        if (!empty($userplace_general_lost_pass_note)) {
            echo esc_attr($userplace_general_lost_pass_note);
        } else {
            esc_html_e(
                'Enter your email address & we will send you a link to pick a new password.',
                'userplace'
            );
        }
        ?>
    </p>

    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
        <p class="form-row">
            <input type="text" name="user_login" placeholder="<?php esc_attr_e('Enter Your Email/Username', 'userplace') ?>" id="user_login">
        </p>
        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button" value="<?php esc_attr_e('Reset Password', 'userplace'); ?>" />
        </p>
    </form>
</div>