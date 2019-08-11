(function() {
  tinymce.create('tinymce.plugins.vecb_button17', {
    init : function(ed, url) {
      ed.addButton('vecb_button17', {
        title : 'SquareBox',image : url+'/icons/box.png', onclick : function() {
              ed.selection.setContent('<span class="square-box">' + ed.selection.getContent() + '</span>');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button17', tinymce.plugins.vecb_button17);
})();
