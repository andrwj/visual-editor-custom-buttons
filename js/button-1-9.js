// JavaScript Document

function getBaseURL () {
   return location.protocol + '//' + location.hostname + 
      (location.port && ':' + location.port) + '/';
}

let is_prismjs_loaded = false;

(function() {
  tinymce.create('tinymce.plugins.vecb_button9', {
    init : function(ed, url) {
      ed.addButton('vecb_button9', {
        title : 'Apply PrismJS',image : url+'/icons/magic.png',onclick : function() {
          if(is_prismjs_loaded) return;

          const doc = ed.dom.doc;
          const head = doc.getElementsByTagName('head')[0];
          const base ='/wp-content/plugins/prismatic/lib/prism/';
					const lastWPstyleNode = [...head.childNodes]
					                          .filter(node => node.rel === 'stylesheet' && /wp\-custom\-style\.css/.test(node.href) )
																		.pop();
          const link = document.createElement("link");
          link.setAttribute("rel", "stylesheet");
          link.setAttribute("type", "text/css");
          link.setAttribute("href", `${base}/css/theme-funky.css`);
					// 이 위치에 넣지 않으면 Editor 안에서 <pre><code> 블록의 바탕색이 흰색으로 override 된다.
          head.insertBefore(link, lastWPstyleNode);

          ['prism-core.js', 'plugin-autoloader.js', 'plugin-toolbar.min.js',
           'plugin-normalize-whitespace.min.js', 'plugin-line-numbers.min.js', 'plugin-copy-to-clipboard.min.js'].forEach((lib) => {
             const script = doc.createElement('script');
             script.type = 'text/javascript';
             script.async = false;
             script.src = `${base}/js/${lib}`;
             head.appendChild(script);
           });
        }
      });
    },
    createControl : function(n, cm) {
      return null;
    },
  });
  tinymce.PluginManager.add('vecb_button9', tinymce.plugins.vecb_button9);
})();
