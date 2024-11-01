<?php
$userplace_settings_general_change_pass = '';
$userplace_settings_general_change_pass = json_decode(get_option('userplace_settings'), true);
$userplace_general_change_pass = '';
$userplace_general_change_pass = !empty($userplace_settings_general_change_pass['signin_general_change_pass_message']) ? $userplace_settings_general_change_pass['signin_general_change_pass_message'] : esc_html__('Your password has been changed. You can sign in now with your new password.', 'userplace');
?>

<div id="password-change-form">
    <?php if ($attributes['show_title']) : ?>
        <h3><?php esc_html_e('Pick a New Password', 'userplace'); ?></h3>
    <?php endif; ?>

    <form name="changepassform" id="changepassform" action="<?php echo site_url('wp-login.php?action=changepass'); ?>" method="post" autocomplete="off">
        <?php if (count($attributes['errors']) > 0) : ?>
            <?php foreach ($attributes['errors'] as $error) : ?>
                <p class="login-error">
                    <?php echo esc_html($error); ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($attributes['password_updated']) : ?>
            <p class="login-info">
                <?php
                if (!empty($userplace_general_change_pass)) {
                    echo esc_attr($userplace_general_change_pass);
                } else {
                    esc_html_e('Your password has been changed. You can sign in now with your new password.', 'userplace');
                }
                ?>
            </p>
        <?php endif; ?>

        <p>
            <label for="pass1"><?php esc_html_e('Old password', 'userplace') ?></label>
            <input type="password" name="pass0" id="pass0" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass1"><?php esc_html_e('New password', 'userplace') ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass2"><?php esc_html_e('Repeat new password', 'userplace') ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
        </p>

        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button" class="button" value="<?php esc_attr_e('Change Password', 'userplace'); ?>" />
        </p>
    </form>
</div>