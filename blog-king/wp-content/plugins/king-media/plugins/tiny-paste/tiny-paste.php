<?php
/*
Plugin Name: TinyMCE Text Paste
Plugin URI: http://www.ndsinternet.com/blog/archives/2006/08/wordpress-plain-text-paste-plugin
Description: Add buttons to the rich text editor for pasting in HTML as plain text. Adapted to fit into Website King
Author: Georg Leciejewski
Version: 0.3
Author URI: http://22eleven.com/
*/

/*  Copyright 2006  Peter Baumgartner  (email : pete@ndsinternet.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//load TinyMCE paste plugin http://tinymce.moxiecode.com/tinymce/docs/plugin_paste.html
function tinymce_paste() {
	$pluginFile = realpath("../../../wp-content/plugins/king-media/plugins/tiny-paste/paste/editor_plugin.js");
	$languageFile = realpath("../../../wp-content/plugins/king-media/plugins/tiny-paste/paste/langs/" . $locale . ".js");
	if (!file_exists($languageFile))
		$languageFile = realpath("../../../wp-content/plugins/king-media/plugins/tiny-paste/paste/langs/en.js");
	
	if ($pluginFile)
		echo file_get_contents($pluginFile);
	
	if ($languageFile)
		echo wp_translate_tinymce_lang(file_get_contents($languageFile));
?>
	initArray.plugins = initArray.plugins + ", paste";
	initArray.theme_advanced_buttons1 = initArray.theme_advanced_buttons1 + ", separator, pastetext, pasteword, selectall";
	initArray.paste_create_paragraphs = false;
	initArray.paste_create_linebreaks = true;
	initArray.paste_use_dialog = true;
	initArray.paste_auto_cleanup_on_paste = true;
	initArray.paste_convert_middot_lists = false;
	initArray.paste_unindented_list_class = "";
	initArray.paste_convert_headers_to_strong = true;

<?php
}

add_action('tinymce_before_init', 'tinymce_paste');

?>
