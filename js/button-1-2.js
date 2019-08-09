(function() {
    tinymce.create('tinymce.plugins.vecb_button2', {
        init : function(ed, url) {
            ed.addButton('vecb_button2', {
                title : 'TypeWriter_Normal',image : 'http://andrwj.net/wp-content/uploads/vecb/Typewriter-L.png',onclick : function() {
                     ed.selection.setContent('<span class="typewriter-normal">' + ed.selection.getContent() + '</span>');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
  tinymce.PluginManager.add('vecb_button2', tinymce.plugins.vecb_button2);
})();
