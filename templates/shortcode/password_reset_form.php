<div id="password-reset-form">
    <?php if ($attributes['show_title']) : ?>
        <h3><?php esc_html_e('Pick a New Password', 'userplace'); ?></h3>
    <?php endif; ?>

    <form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($attributes['login']); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?php echo esc_attr($attributes['key']); ?>" />

        <?php if (count($attributes['errors']) > 0) : ?>
            <?php foreach ($attributes['errors'] as $error) : ?>
                <p class="login-error">
                    <?php echo esc_html($error); ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <p>
            <label for="pass1"><?php esc_html_e('Set password', 'userplace') ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass2"><?php esc_html_e('Confirm password', 'userplace') ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
        </p>

        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button" class="button" value="<?php esc_attr_e('Reset Password', 'userplace'); ?>" />
        </p>
    </form>
</div>