(function() {
  tinymce.create('tinymce.plugins.vecb_button12', {
    init : function(ed, url) {
      ed.addButton('vecb_button12', {
        title : 'blockquote',image : 'http://andrwj.net/wp-content/uploads/vecb/blockquote-40x40.png', onclick : function() {
              ed.selection.setContent('<blockquote>' + ed.selection.getContent() + '</blockquote>');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button12', tinymce.plugins.vecb_button12);
})();

