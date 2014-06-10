(function($) {
  var unbind_sharedaddy_email = function(e) {
    $('a.share-email', $('.sharedaddy ul')).off('click');
  };
  // These event bindings need to match sharing.js, which triggers the binding
  // against ".sharedaddy ul a.share-email".
  // @see sharing.js
  $(document).on('ready', unbind_sharedaddy_email);
  $(document.body).on('post-load', unbind_sharedaddy_email);
})(jQuery);
