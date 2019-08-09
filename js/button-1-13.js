(function() {
  tinymce.create('tinymce.plugins.vecb_button13', {
    init : function(ed, url) {
      ed.addButton('vecb_button13', {
        title : 'Add Blank Line',image : 'http://andrwj.net/wp-content/uploads/vecb/enter-40x40.png', onclick : function() {
              ed.selection.setContent('<br/><p>&nbsp;</p>');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button13', tinymce.plugins.vecb_button13);
})();

