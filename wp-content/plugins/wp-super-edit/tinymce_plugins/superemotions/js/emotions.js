tinyMCEPopup.requireLangPack();

var SuperEmotionsDialog = {
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
	},

	insert : function(file, title) {
		var ed = tinyMCEPopup.editor, dom = ed.dom;

		tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
			'src' : '../wp-includes/images/smilies/' + file,
			'class' : 'superemotionImg mceTemp',
			'alt' : ed.getLang(title),
			'title' : ed.getLang(title),
			'border' : 0
		}));
		
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(SuperEmotionsDialog.init, SuperEmotionsDialog);
