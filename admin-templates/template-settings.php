<div id="userplace_payment_template_metabox"></div>

<?php
/**
 * Localize the updated data from database
 */
wp_localize_script( 'userplace_payment_template_settings', 'USERPLACE_ADMIN', apply_filters('userplace_payment_admin_generator_localize_args', array( 'UPDATED_TEMPLATE' => get_post_meta( $post->ID, '_userplace_payment_template_data', true ) )
) );

?>
<input type="hidden" id="_userplace_payment_template_data" name="_userplace_payment_template_data">
