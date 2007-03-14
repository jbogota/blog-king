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
    add_filter("mce_plugins", "extended_editor_mce_plugins",0);
    add_filter("mce_buttons", "extended_editor_mce_buttons", 0);
    add_filter("mce_buttons_2", "extended_editor_mce_buttons_2", 0);
    add_filter("mce_buttons_3", "extended_editor_mce_buttons_3", 0);
}

function extended_editor_mce_plugins($plugins) {

	#print_r($plugins);
	$KWConfig = get_settings('king-wysiwyg');
	$plugins = array();

	if(strpos($KWConfig['plugins'],','))
	{#more than one plugin found
		$plugins = explode(',',$KWConfig['plugins']);
	}
	else
	{
		$plugins[] =$KWConfig['plugins'];
	}

	if(!empty($plugins))
	{
	    foreach($plugins as $plugin)
		{

			array_push($plugins, trim($plugin));
		}

	}
    return $plugins;
}


function extended_editor_mce_buttons($mce_buttons)
{
	$KWConfig = get_settings('king-wysiwyg');
	$buttons = array();
	#need to insert our new buttons before the hidden wp stuff
	$wp_adv_start = array_search('wp_adv_start',$mce_buttons);
	$wp_adv_end = array_search('wp_adv_end',$mce_buttons);
	$adv_buttons = array_slice($mce_buttons,$wp_adv_start,$wp_adv_end);
	#remove adv buttons from array
	array_splice($mce_buttons,$wp_adv_start,count($adv_buttons));

    if(strpos($KWConfig['buttons_1'],','))
	{#more than one plugin found
		$buttons = explode(',',$KWConfig['buttons_1']);
	}
	else
	{
		$buttons[] =$KWConfig['buttons_1'];
	}
    if(!empty($buttons))
	{
		foreach($buttons as $button)
		{
			array_push($mce_buttons, trim($button));
		}
	}
	#merge buttons back together
	#$mce_buttons = array_merge($mce_buttons,$adv_buttons);


    return $mce_buttons;
}

function extended_editor_mce_buttons_2($mce_buttons_2) {
	$KWConfig = get_settings('king-wysiwyg');
    $buttons2 = explode(',',$KWConfig['buttons_2']);
	return $buttons2;
}

function extended_editor_mce_buttons_3($mce_buttons_3) {
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
			'plugins' =>'Plugins {textarea|5|45}## The functions on the first buttonrow. Available Plugins are.<br />table, searchreplace, advhr, advimage',
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

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2//EN">

<html>
<head>
  <meta name="generator" content=
  "HTML Tidy for Windows (vers 14 February 2006), see www.w3.org">

  <title></title>
</head>

<body>
</body>
</html>
