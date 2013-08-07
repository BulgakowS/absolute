/**
 * $Id: editor_plugin_src.js 520 2008-01-07 16:30:32Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.SuperEmotionsPlugin', {
		init : function(ed, url) {
			var t = this;

			// Register commands
			ed.addCommand('mceSuperEmotion', function() {
				ed.windowManager.open({
					file : url + '/emotions.htm',
					width : 230 + parseInt(ed.getLang('emotions.delta_width', 0)),
					height : 180 + parseInt(ed.getLang('emotions.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register button
			ed.addButton('superemotions', {
				title : 'supermotions.emotions_desc',
				cmd : 'mceSuperEmotion',
				image : url + '/img/emotions.gif'
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_shcode(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_shcode(o.content);
			});

		},

		_do_shcode : function(co) {
			return co.replace(/\[superemotions([\s\S]+?)\]/g, function(a){
				var sfile = a.match(/file="([\s\S]+?)['"]/);
				var stitle = a.match(/title="([\s\S]+?)['"]/);
				
				var rfile = ' src="../wp-includes/images/smilies/' + sfile[1] + '"';
				var rtitle = ' title="' + stitle[1] + '"';
				var ralt = ' alt="' + stitle[1] + '"';
				var rclass = ' class="superemotionImg mceTemp"';
				var rborder = ' border=0';

				return '<img'+ rclass + rtitle + rfile + rborder + ralt + ' />';

			});
			
		},

		_get_shcode : function(co) {
			return co.replace(/<img class="superemotionImg([\s\S]+?)\/>/gi, function(a){
				var sfile = a.match(/\/smilies\/(\S+?)['"]/);
				var stitle = a.match(/title="([\s\S]+?)['"]/);
				
				var rfile = ' file="' + sfile[1] + '"';
				var rtitle = ' title="' + stitle[1] + '"';

				return '[superemotions'+ rfile + rtitle + ']';
			});
		},		

		getInfo : function() {
			return {
				longname : 'Super Emotions',
				author : 'Jess Planck',
				authorurl : 'http://funroe.net',
				infourl : 'http://factory.funroe.net',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('superemotions', tinymce.plugins.SuperEmotionsPlugin);
})();