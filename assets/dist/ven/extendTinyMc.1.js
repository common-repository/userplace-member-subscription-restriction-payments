jQuery(document).ready(function($) {
  tinymce.create('tinymce.plugins.userplace_plugin', {
    init: function(ed, url) {
      // Register command for when button is clicked
      ed.addCommand('userplace_insert_shortcode', function() {
        selected = tinyMCE.activeEditor.selection.getContent();

        if (selected) {
          //If text is selected when button is clicked
          //Wrap shortcode around it.
          content =
            '[restrict_content restricted_plans=""]' +
            selected +
            '[/restrict_content]';
        } else {
          content =
            '[restrict_content restricted_plans=""] [/restrict_content]';
        }

        tinymce.execCommand('mceInsertContent', false, content);
      });
      // Register command for when button is clicked
      ed.addCommand('userplace_insert_login_button_shortcode', function() {
        content = '[userplace_login_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      ed.addCommand('login_button_shortcode', function() {
        content = '[userplace_login_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      ed.addCommand('register_form', function() {
        content = '[userplace_register_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      ed.addCommand('forget_pass_form', function() {
        content = '[userplace_password_lost_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      ed.addCommand('password_reset_form', function() {
        content = '[userplace_password_reset_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      ed.addCommand('userplace_user_main_profile', function() {
        content = '[userplace_user_main_profile]';
        tinymce.execCommand('mceInsertContent', false, content);
      });

      // Register buttons - trigger above command when clicked
      ed.addButton('restrict_content_shortcode_button', {
        icon: false,
        text: 'Restrict Content',
        cmd: 'userplace_insert_shortcode',
      });

      ed.addButton('login_button_shortcode', {
        icon: false,
        text: 'Login Form',
        cmd: 'login_button_shortcode',
      });
      ed.addButton('register_form', {
        icon: false,
        text: 'Register Form Shortcode',
        cmd: 'register_form',
      });

      ed.addButton('forget_pass_form', {
        icon: false,
        text: 'Forget Password',
        cmd: 'forget_pass_form',
      });
      ed.addButton('password_reset_form', {
        icon: false,
        text: 'Password Reset',
        cmd: 'password_reset_form',
      });
      ed.addButton('userplace_user_main_profile', {
        icon: false,
        text: 'Userplace Main Profile',
        cmd: 'userplace_user_main_profile',
      });
      // if (UFS_AJAX_DATA) {
      ed.addButton('ufs_add_listing', {
        icon: false,
        text: 'Add Listing',
        cmd: 'ufs_add_listing',
      });
      ed.addCommand('ufs_add_listing', function() {
        content = '[submission_form post_type=""]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      ed.addButton('ufs_all_listings', {
        icon: false,
        text: 'All Listings',
        cmd: 'ufs_all_listings',
      });
      ed.addCommand('ufs_all_listings', function() {
        content = '[listing_table post_type="" edit_page_slug=""]';
        tinymce.execCommand('mceInsertContent', false, content);
      });
      // }
    },
  });

  // Register our TinyMCE plugin
  // first parameter is the button ID1
  // second parameter must match the first parameter of the tinymce.create() function above
  tinymce.PluginManager.add(
    'restrict_content_shortcode_button',
    tinymce.plugins.userplace_plugin
  );
});
