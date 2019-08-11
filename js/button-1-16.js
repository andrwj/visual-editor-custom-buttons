(function() {
  tinymce.create('tinymce.plugins.vecb_button16', {
    init : function(ed, url) {
      ed.addButton('vecb_button16', {
        title : 'RoundBox',image : 'http://andrwj.net/wp-content/uploads/vecb/roundbox-40x40.png', onclick : function() {
              ed.selection.setContent('<span class="round-box">' + ed.selection.getContent() + '</span>');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button16', tinymce.plugins.vecb_button16);
})();
