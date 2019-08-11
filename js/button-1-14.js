(function() {
  tinymce.create('tinymce.plugins.vecb_button14', {
    init : function(ed, url) {
      ed.addButton('vecb_button14', {
        title : 'Anchor',image : url+'/icons/anchor.png', onclick : function() {
              ed.selection.setContent('<a href="#id">' + ed.selection.getContent() + '</a>');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button14', tinymce.plugins.vecb_button14);
})();
