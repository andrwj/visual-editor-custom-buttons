(function() {
    tinymce.create('tinymce.plugins.vecb_button10', {
        init : function(ed, url) {
            ed.addButton('vecb_button10', {
                title : '항목 표식',image : 'http://andrwj.net/wp-content/uploads/vecb/right-arrow.png',onclick : function() {
                     ed.selection.setContent('<span class="bullet-mark">&nbsp;</span>&nbsp;|');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
  tinymce.PluginManager.add('vecb_button10', tinymce.plugins.vecb_button10);
})();
