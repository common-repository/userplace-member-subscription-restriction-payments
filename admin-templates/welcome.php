<div class="userplace-welcome-wrapper">
  <div class="userplace-welcome-banner">
    <img src="<?php print USERPLACE_IMG ?>welcome/banner.svg" alt="">
    <div class="userplace-welcome-text">
      <h3><?php esc_html_e('Welcome to the Userplace Membership Plugin', 'userplace') ?></h3>
      <p><?php esc_html_e("It’ll help you to monetize your site, you will able to create plans, subscriptions and restrictions that applies into view level and submission level.", 'userplace') ?></p>
      <div class="userplace-welcome-btn">
        <a href="https://redq.gitbooks.io/userplace/content/" target="_blank" class="userplace-readmore-btn"><?php esc_html_e('Documentation', 'userplace') ?></a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=userplace_settings')) ?>" class="userplace-settings-btn"><?php esc_html_e('Settings', 'userplace') ?></a>
      </div>
    </div>
  </div>
</div>
<!-- end of userplace-welcome-wrapper -->