(function() {
  tinymce.create('tinymce.plugins.vecb_button15', {
    init : function(ed, url) {
      ed.addButton('vecb_button15', {
        title : 'Circle-Box',image : 'http://andrwj.net/wp-content/uploads/vecb/circle-box-red-40x40.png', onclick : function() {
              ed.selection.setContent('<span class="circle-box ctag-default">' + ed.selection.getContent() + '</span>');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button15', tinymce.plugins.vecb_button15);
})();
