(function() {
    tinymce.create('tinymce.plugins.vecb_button5', {
        init : function(ed, url) {
            ed.addButton('vecb_button5', {
                title : 'inline-code: PrismJS<default:empty> 인라인 코드 넣기',image : 'http://andrwj.net/wp-content/uploads/vecb/Code.png',onclick : function() {
                     ed.selection.setContent('<code class="language-">' + ed.selection.getContent() + '</code>');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
  tinymce.PluginManager.add('vecb_button5', tinymce.plugins.vecb_button5);
})();

