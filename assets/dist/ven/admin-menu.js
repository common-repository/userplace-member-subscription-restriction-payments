jQuery('.menu-item').on('click', function() {
  var self = jQuery(this);
  self.children('.menu-item-settings').children('.userplace-restricted-plans-areas').hide();

  // amare keu gali diba na. apatoto code kaj kore :D
  self.children('.menu-item-settings')
    .children('.rq-userplace-custom-input')
      .children('.description')
        .children('label')
          .children('.userplace-menu-restriction-radio').change(function() {
    var childSelf = jQuery(this);
    console.log(childSelf.attr('checked'));
    childSelf.closest('.rq-userplace-custom-field').next().hide();
    if( childSelf.attr('checked') == 'checked' && childSelf.val() == 'logged_in' ) {
      childSelf.closest('.rq-userplace-custom-field').next().show();
    }
  }).change();
});
