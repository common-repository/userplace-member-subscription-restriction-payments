<?php

/**
 * The Template for displaying post restriction template
 *
 * @author    RedQ,Inc
 * @package   Userplace/Templates
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
  exit;
}

get_header();

/**
 * Hook: userplace_before_main_content.
 */
do_action('userplace_before_main_content');

?>

<div class="userplace-message-wrapper">
  <p><?php esc_html_e('Sorry, you are not allowed to view this page.', 'userplace') ?></p>
</div>

<?php
/**
 * Hook: userplace_after_main_content.
 */
do_action('userplace_after_main_content');

get_footer();
