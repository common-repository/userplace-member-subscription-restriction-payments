<?php

/**
 * Localize the updated data from database
 */
global $post;

use Userplace\Provider;

$settings_array = new Provider();
$fields = $settings_array->single_post_restrictions_fields();
$field_conditions = $settings_array->single_restriction_conditions();
$restrictions_settings = get_post_meta($post->ID, 'userplace_restrictions_settings', true);
wp_localize_script(
  'userplace_restrictions_settings',
  'USERPLACE_GLOBAL',
  apply_filters('userplace_global_generator_localize_args', array(
    'RESTRICTIONS_SETTINGS' => $restrictions_settings,
    'fields' => apply_filters('admin__userplace_restrictions_settings_fileds', $fields),
    'conditions' => $field_conditions
  ))
);
?>

<div id="_userplace_restrictions_settings"></div>

<input type="hidden" id="__userplace_restrictions_settings" name="userplace_restrictions_settings" value="<?php echo esc_attr(isset($restrictions_settings) ? $restrictions_settings : '{}') ?>">