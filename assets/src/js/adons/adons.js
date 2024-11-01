jQuery('.userplace-install-now').on('click', function(e) {
  e.preventDefault();
  var self = jQuery(this);
  self.text(USERPLACE_PAYMENT_AJAX_DATA.INSTALLING);
  jQuery
    .ajax({
      url: USERPLACE_PAYMENT_AJAX_DATA.admin_url,
      method: 'post',
      data: {
        action: 'userplace_install_adons',
        link: self.data('link'),
        name: self.data('name'),
        slug: self.data('slug'),
        version: self.data('version'),
        nonce: USERPLACE_PAYMENT_AJAX_DATA.nonce
      },
    })
    .done(function(response) {
      if( response.success == true) {
        self.hide();
        self.parent().html('<a class="up-userplace-btn userplace-activate-now button button-primary" href="#" data-plugin='+self.data('slug')+'>'+USERPLACE_PAYMENT_AJAX_DATA.ACTIVATE+'</a>');
        activate_plugin();
        deactivate_plugin();
      }
    });
});

function activate_plugin() {
  jQuery('.userplace-activate-now').on('click', function(e) {
    e.preventDefault();
    var self = jQuery(this);
    var plugin = jQuery(this).data('plugin');
    self.text(USERPLACE_PAYMENT_AJAX_DATA.ACTIVATING);
    jQuery
      .ajax({
        url: USERPLACE_PAYMENT_AJAX_DATA.admin_url,
        method: 'post',
        data: {
          action: 'userplace_activate_adons',
          plugin: plugin,
          nonce: USERPLACE_PAYMENT_AJAX_DATA.nonce
        },
      })
      .done(function(response) {
        console.log(response);
        if( response.success == true) {
          self.hide();
          self.parent().html('<a class="userplace-deactivate-now button" href="#" data-plugin="'+plugin+'">'+USERPLACE_PAYMENT_AJAX_DATA.DEACTIVATE+'</a>');
          deactivate_plugin();
        }
      });
  });
}

function deactivate_plugin() {
  jQuery('.userplace-deactivate-now').on('click', function(e) {
    e.preventDefault();
    var self = jQuery(this);
    var plugin = jQuery(this).data('plugin');
    self.text(USERPLACE_PAYMENT_AJAX_DATA.DEACTIVATING);
    jQuery
      .ajax({
        url: USERPLACE_PAYMENT_AJAX_DATA.admin_url,
        method: 'post',
        data: {
          action: 'userplace_deactivate_adons',
          plugin: plugin,
          nonce: USERPLACE_PAYMENT_AJAX_DATA.nonce
        },
      })
      .done(function(response) {
        console.log(response);
        if( response.success == true) {
          self.hide();
          self.parent().html('<a class="userplace-activate-now button button-primary" href="#" data-plugin="'+plugin+'">'+USERPLACE_PAYMENT_AJAX_DATA.ACTIVATE+'</a>');
          activate_plugin();
        }
      });
  });
}

activate_plugin();
deactivate_plugin();
