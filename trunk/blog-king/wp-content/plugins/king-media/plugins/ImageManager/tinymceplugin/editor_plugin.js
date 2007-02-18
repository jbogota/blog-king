tinyMCE.importPluginLanguagePack('ps_imagemanager_tinymceplugin','en,tr,de,sv,zh_cn,cs,fa,fr_ca,fr,pl,pt_br,nl,he,nb,ru,ru_KOI8-R,ru_UTF-8,nn,cy,es,is,zh_tw,zh_tw_utf8,sk,da');
//This var is called at bottom by tinyMCE.addPlugin
var TinyMCE_ExternalPluginPlugin = {
	//Woteva info you need
	getInfo : function() {
		return {
			longname : 'ImageManager TinyMCE Plugin',
			author : 'Per Soderlind',
			authorurl : 'http://www.soderlind.no',
			infourl : 'http://www.soderlind.no/ImageManager',
			version : "1.0"
		};
	},
	//This adds the button image itself and its command
	getControlHTML : function(cn) {
		switch (cn) {
			case "ps_imagemanager_tinymceplugin":
				return tinyMCE.getButtonHTML(cn, 'ImageManager', '{$pluginurl}/images/images.png', 'ps_imagemanager');
		}
		return "";
	},
	//This executes the button call to an external javascript
	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "ps_imagemanager":
				openImageManager();
			return true;
		}
		return false;
	}
};
//Registers the plugin name and the functions above with tinyMCE. 'externalplugin' is the plugin name called in mybuttons.php
tinyMCE.addPlugin("ps_imagemanager_tinymceplugin", TinyMCE_ExternalPluginPlugin);