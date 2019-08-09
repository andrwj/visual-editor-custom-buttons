(function() {
    tinymce.create('tinymce.plugins.vecb_button6', {
        init : function(ed, url) {
            ed.addButton('vecb_button6', {
                title : 'code-block: PrismJS <default:bash> 코드 블럭 넣기',image : 'http://andrwj.net/wp-content/uploads/vecb/codeblock-logo-40x40.png',onclick : function() {
                     ed.selection.setContent('<pre><code class="language-bash">' + ed.selection.getContent() + '</code></pre>');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('vecb_button6', tinymce.plugins.vecb_button6);
})();
