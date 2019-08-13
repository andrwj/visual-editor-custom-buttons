(function() {
                tinymce.create('tinymce.plugins.vecb_button18', {init : function(ed, url) {ed.addButton('vecb_button18', {title : 'Tracking Mark',image : 'http://andrwj.net/wp-content/uploads/vecb/tracking-40x40.png',onclick : function() {
                ed.selection.setContent('<span class="tracking-mark">↳</span> |');}});
            }, createControl : function(n, cm) {return null;},});
                tinymce.PluginManager.add('vecb_button18', tinymce.plugins.vecb_button18);
	    })();
