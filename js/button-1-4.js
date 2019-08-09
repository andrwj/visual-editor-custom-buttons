(function() {
    tinymce.create('tinymce.plugins.vecb_button4', {
        init : function(ed, url) {
            ed.addButton('vecb_button4', {
                title : 'Clear:Both',image : 'http://andrwj.net/wp-content/uploads/vecb/ClearBoth.png',onclick : function() {
                     ed.selection.setContent('<p style="clear:both; overflow:hidden;"></p>');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('vecb_button4', tinymce.plugins.vecb_button4);
})();
