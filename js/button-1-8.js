(function() {
    tinymce.create('tinymce.plugins.vecb_button8', {
        init : function(ed, url) {
            ed.addButton('vecb_button8', {
                title : '민감한 컨텐트 숨기기',image : url+'/icons/user_lock.png',onclick : function() {
                     ed.selection.setContent('[private role="administrator"]' + ed.selection.getContent() + '[/private]');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
  tinymce.PluginManager.add('vecb_button8', tinymce.plugins.vecb_button8);
})();
