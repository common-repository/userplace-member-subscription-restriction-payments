<?php
/**
 * Pricing Plan
 *
 * Show the details of the plan a user have brought
 *
 * @author redqeteam
 * @category Theme
 * @package Userplace/Shortcodes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

extract( shortcode_atts( array(
    'title'                 => '',
    'best_choice'           => '',
    'amount'                => '',
    'cycle'                 => '',
    'column'                => 'three',
), $atts) );
?>
<div class="userplace-pricing-single column-<?php echo esc_attr($column) ?>">
    <h1 class="userplace-single-price-title"><?php echo esc_html($title.$best_choice) ?></h1>
    <span class="userplace-single-price-amount"><?php echo esc_html($amount); ?>
        <span class="userplace-single-sub-amount">/<?php echo esc_html($cycle) ?></span>
    </span>
    <?php echo do_shortcode( $content ); ?>
</div>
