(function() {
                tinymce.create('tinymce.plugins.vecb_button19', {init : function(ed, url) {ed.addButton('vecb_button19', {title : 'Sequence Arrow',image : 'http://andrwj.net/wp-content/uploads/vecb/sequence-arrow-40x40.png',onclick : function() {
                ed.selection.setContent('<span class="sequence-arrow">→</span> ');}});
            }, createControl : function(n, cm) {return null;},});
                tinymce.PluginManager.add('vecb_button19', tinymce.plugins.vecb_button19);
	    })();
