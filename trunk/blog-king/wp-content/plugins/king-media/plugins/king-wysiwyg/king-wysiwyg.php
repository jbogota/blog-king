<?php
/*
Plugin Name: King_WYSIWYG
Plugin URI:  http://blog.mediaprojekte.de
Description: Adds more styling options to the WYSIWYG post editor, updated for multi-line buttons.
Version: 0.3
Author: Georg Leciejewski
Author URI: http://blog.mediaprojekte.de
*/
define("KINGWYSIWYGVERSION","030");
require_once(ABSPATH . 'wp-content/plugins/king-includes/library/king_widget_functions.php');

if (isset($wp_version)) {
    add_filter("mce_plugins", "extended_editor_mce_plugins", 0);
    add_filter("mce_buttons", "extended_editor_mce_buttons", 0);
    add_filter("mce_buttons_2", "extended_editor_mce_buttons_2", 0);
    add_filter("mce_buttons_3", "extended_editor_mce_buttons_3", 0);
}

function extended_editor_mce_plugins($plugins) {
	//$KWConfig = get_settings('king-wysiwyg');
	//$myplugins = explode(',',$KWConfig['plugins']);
	//array_push($plugins, $KWConfig['plugins']);
    array_push($plugins, "table", "fullscreen", "searchreplace", "advhr", "advimage");
    return $plugins;
}


function extended_editor_mce_buttons($buttons) {
	$KWConfig = get_settings('king-wysiwyg');
	$buttons1 = explode(',',$KWConfig['buttons_1']);
	return $buttons1;
}

function extended_editor_mce_buttons_2($buttons) {
	$KWConfig = get_settings('king-wysiwyg');
    $buttons2 = explode(',',$KWConfig['buttons_2']);
	return $buttons2;
}

function extended_editor_mce_buttons_3($buttons) {
	$KWConfig = get_settings('king-wysiwyg');
	$buttons3 = explode(',',$KWConfig['buttons_3']);
	return $buttons3;
}

//if in filemanager options
if(strstr($_SERVER['REQUEST_URI'], 'options-general.php') !== false){
	require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');
  		plugintoolkit(
			$plugin='wysiwyg',
			$array=array(
			'plugins' =>'Plugins {textarea|5|45}## The functions on the first buttonrow. Available Plugins are.<br />table, fullscreen, searchreplace, advhr, advimage',
			'buttons_1' =>'Buttons 1 {textarea|5|45}##The functions on the first buttonrow<br />Available Buttons are:<br/>undo, redo, separator, cut, copy, paste, bold, italic, underline, strikethrough, bullist, numlist, separator, indent, outdent,justifyleft, justifycenter, justifyright, justifyfull, sub, sup, charmap, hr, advhr, link, unlink, anchor,code, cleanup, separator, search, replace,  wphelp, formatselect, fontselect, fontsizeselect, styleselect, forecolor, backcolor, removeformat,image, tablecontrols, fullscreen, wordpress',
			'buttons_2' =>'Buttons 2 {textarea|5|45}##The functions on the second buttonrow',
			'buttons_3' =>'Buttons 3 {textarea|5|45}##The functions on the third buttonrow',
			'delete' => 'delete'
			),
			$file='king-wysiwyg.php',
			$menu=array(
				'parent' => 'options-general.php' ,
				'access_level' => 'activate_plugins'
			)
		);
}

/**
* @desc Version Check Heading
*/
function king_wysiwyg_version() {
	king_version_head('King_WYSIWYG',KINGWYSIWYGVERSION);
}
add_action('admin_head','king_wysiwyg_version');

?>
