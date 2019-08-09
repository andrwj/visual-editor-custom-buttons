(function() {

  tinymce.create('tinymce.plugins.vecb_button3', {
    init : function(ed, url) {
      ed.addButton('vecb_button3', {
        title : 'TypeWriter_Bold', image : 'http://andrwj.net/wp-content/uploads/vecb/Typewriter-M.png',onclick : function() {
          ed.selection.setContent('<span class="typewriter-bold">' + ed.selection.getContent() + '</span>');
        }
      });
    },
    createControl : function(n, cm) {
      return null;
    },
  });

  tinymce.PluginManager.add('vecb_button3', tinymce.plugins.vecb_button3);
})();
