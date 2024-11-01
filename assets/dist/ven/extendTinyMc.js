jQuery(document).ready(function($) {
  var shortcodeList = [
    {
      type: 'button',
      name: 'restrict_content',
      text: 'Restrict Content',
      onClick: function(e) {
        selected = tinyMCE.activeEditor.selection.getContent();
        if (selected) {
          //If text is selected when button is clicked
          //Wrap shortcode around it.
          content =
            '[restrict_content restricted_plans_id="" html=""]' +
            selected +
            '[/restrict_content]';
        } else {
          content =
            '[restrict_content restricted_plans_id="" html=""] [/restrict_content]';
        }

        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'login',
      text: 'Login Form',
      onClick: function(e) {
        content = '[userplace_login_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'register',
      text: 'Register Form',
      onClick: function(e) {
        content = '[userplace_register_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'forgetpass',
      text: 'Forget Pass Form',
      onClick: function(e) {
        content = '[userplace_password_lost_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'password_reset_form',
      text: 'Password Reset Form',
      onClick: function(e) {
        content = '[password_reset_form]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'userplace_user_main_profile',
      text: 'User Main Profile Shortcode',
      onClick: function(e) {
        content = '[userplace_user_main_profile]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'billing_overview',
      text: 'Billing Overview',
      onClick: function(e) {
        content = '[billing_overview]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'userplace_invoices',
      text: 'Invoives',
      onClick: function(e) {
        content = '[userplace_invoices]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
    {
      type: 'button',
      name: 'userplace_list_cards',
      text: 'Card List',
      onClick: function(e) {
        content = '[userplace_list_cards]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    },
  ];
  if (typeof UFS_ADMIN !== 'undefined' && UFS_ADMIN.is_active) {
    shortcodeList.push({
      type: 'button',
      name: 'ufs_add_listing',
      text: 'Add Listing',
      onClick: function(e) {
        content = '[submission_form post_type=""]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    });
    shortcodeList.push({
      type: 'button',
      name: 'ufs_all_listings',
      text: 'All Listings',
      onClick: function(e) {
        content = '[listing_table post_type="" edit_page_slug=""]';
        tinymce.execCommand('mceInsertContent', false, content);
      },
    });
  }

  tinymce.create('tinymce.plugins.userplace_plugin', {
    init: function(ed, url) {
      // Register command for when button is clicked
      ed.addCommand('userplace_insert_shortcode', function() {
        ed.windowManager.open({
          title: 'Userplace',
          type: 'container',
          classes: 'userplace-shortcode',
          body: shortcodeList,
        });
      });

      // Register buttons - trigger above command when clicked
      ed.addButton('restrict_content_shortcode_button', {
        title: 'Userplace Shortcodes',
        cmd: 'userplace_insert_shortcode',
        image: url + '/lock.png',
      });
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
