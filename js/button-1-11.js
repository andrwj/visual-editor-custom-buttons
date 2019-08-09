(function() {
  tinymce.create('tinymce.plugins.vecb_button11', {
    init : function(ed, url) {
      ed.addButton('vecb_button11', {
        title : '들여쓰기 표식',image : url+'/icons/intro_header.png', onclick : function() {
              ed.selection.setContent('<span class="pre-paragraph">' + ed.selection.getContent() + '&nbsp;</span>-|');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button11', tinymce.plugins.vecb_button11);
})();