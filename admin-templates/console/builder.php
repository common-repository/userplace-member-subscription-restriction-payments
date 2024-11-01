<div id="userplace_console_builder"></div>

<input type="hidden" id="userplace_console_builder_data" name="_userplace_console_builder">

<?php
/**
 * Localize the updated data from database
 */

$args = array(
  'posts_per_page'   => -1,
  'post_type'        => 'userplace_console',
);

$provider = new Userplace\Provider();
$console_menus = get_posts($args);
$console_menu = array();
foreach ($console_menus as $menu) {
  $console_menu[$menu->ID] = $menu->post_name;
}

wp_localize_script(
  'userplace_console_menu_settings',
  'USERPLACE_ADMIN',
  apply_filters(
    'userplace_admin_generator_localize_args',
    array(
      'CONSOLE_BUILDER' => get_post_meta($post->ID, '_userplace_console_builder', true),
      'CONSOLE_MENU'    => $console_menu,
      'FIELDS'          => $provider->console_menu_settings(),
    )
  )
);
