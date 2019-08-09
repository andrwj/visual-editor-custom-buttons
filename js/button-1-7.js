(function() {
    tinymce.create('tinymce.plugins.vecb_button7', {
        init : function(ed, url) {
            ed.addButton('vecb_button7', {
                title : 'ctag-box: 컨텐트 내에 태그 넣기',image : url+'/icons/tags.png',onclick : function() {
                     ed.selection.setContent('<span class="ctag-box ctag-default">' + ed.selection.getContent() + '</span>');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
  tinymce.PluginManager.add('vecb_button7', tinymce.plugins.vecb_button7);
})();
