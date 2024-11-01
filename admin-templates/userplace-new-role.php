<?php
/**
 * Localize the updated data from database
 */
  global $post;
  use Userplace\Provider;
  $settings_array = new Provider();
  $fields = $settings_array->addRoleFields();
  $role_settings = get_post_meta($post->ID, '_userplace_add_new_role_settings', true );
  wp_localize_script( 'userplace_add_role', 'USERPLACE_ADMIN',
    apply_filters('userplace_admin_generator_localize_args', array(
      'ROLE_SETTINGS' => $role_settings,
      'fields' => apply_filters('admin_userplace_add_new_role_settings_fileds', $fields),
  ) ));
?>


<div id="userplace_add_new_role_settings"></div>

<input type="hidden" id="_userplace_add_new_role_settings" name="_userplace_add_new_role_settings" value="<?php echo esc_attr(isset($role_settings) ? $role_settings : '{}') ?>">