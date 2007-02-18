<?php
/*
Plugin Name: King_Framework
Plugin URI: http://www.blog.mediaprojekte.de/
Description: You only need to activate this Plugin for remote version check of the Framework. Contains common Functions, Language, Javascripts used by all King Widgets + King Plugins.
Author: Georg Leciejewski
Version: 0.71
Author URI: http://www.blog.mediaprojekte.de
*/
/*  Copyright 2006  georg leciejewski  (email : georg@mediaprojekte.de)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
define("KINGFRAMEWORKVERSION", "072");
require_once(ABSPATH . 'wp-content/plugins/king-includes/library/king_widget_functions.php');

/**
* @desc Admin Menu
* @author georg leciejewski
*/
function king_framework_admin_menu()
{
  if(function_exists('add_options_page'))
  {//($page_title, $menu_title, $access_level, $file, $function = '')
    add_options_page('King Plugin Options','King Plugin Options', 'manage_options', basename(__FILE__),'king_framework_options' );
  }

	$king_options = get_option('king_framework');

	$GLOBALS['freakmode'] = $king_options['freakmode'];
}
add_action('admin_menu', 'king_framework_admin_menu');

/**
* @desc Admin Options Page
* @author georg leciejewski
*/
function king_framework_options()
{

	$options = $newoptions = get_option('king_framework');
	if ( $_POST["king_framework_submit"] )
	{
			//if defaults are choosen
		if ( isset($_POST["king_framework_defaults"]) )
		{
			//no defaults atm
		}
		else
		{// insert new form values

			$newoptions['widgets_number']	= (int)$_POST["king_framework_widgets_number"];
			$newoptions['freakmode']		= $_POST["king_framework_freakmode"];
		}
	}
	if ( $options != $newoptions )
	{
		$options = $newoptions;
		update_option('king_framework', $options);
	}


	$widgets_number = $options['widgets_number'];
	$freakmode = $options['freakmode'];

	#echo king_get_start_form('wrap','','','post');

	echo '<div class="wrap"><h2>'. __('King Framework', 'widgetKing') .'</h2>' ;
		_e('These Options are global for all King Widgets(if they support the functions). There will be more Options in the future.','widgetKing');
	echo ' <p> <a target="_blank" href="../wp-content/plugins/king-includes/changelog.txt">'.__('Check out the Changelog File','widgetKing').'</a></p> ';
	echo '</div>';
	#echo king_get_dump_options('widget_king_categories','','widget_king_categories');
 /*   echo king_get_textbox_p(array(
			'k_Label_Id_Name' 	=>"king_framework_widgets_number",
			'k_P_Class' 		=>"king_p",
			'k_Description' 	=> __('Widget Numbers', 'widgetKing'),
			'k_Label_Title' 	=> __('The title above your Login Widget', 'widgetKing'),
			'k_Value' 			=> $widgets_number,
			'k_Size' 			=>'2',
			'k_Max' 			=>'2'));

	_e('This many widgets will be available in the widgets dropdown in the Widget Admin Area','widgetKing');

    echo king_get_checkbox_p(array(
			'k_Label_Id_Name' 	=>"king_framework_sliding_js",
			'k_P_Class' 		=>"king_p",
			'k_Description' 	=>  __('Insert Javascripts for Widget Sliding into Head', 'widgetKing'),
			'k_Label_Title' 	=> __('Insert Javascript includes for Sliding(Open/Close)Widgets into Header of the Page.','widgetKing'),
			'k_Value' 			=>$sliding_js));
	_e('Insert Javascript includes for Sliding(Open/Close)Widgets into Header of the Page. Be aware that you need to adapt some of the widgets surrounding HTML Code, to be able to use the Slider on some Widgets','widgetKing');
    echo king_get_checkbox_p(array(
			'k_Label_Id_Name' 	=>"king_framework_freakmode",
			'k_P_Class' 		=>"king_p",
			'k_Description' 	=>  __('Go into FREAK-Mode', 'widgetKing'),
			'k_Label_Title' 	=> __('See more Options in the Plugins','widgetKing'),
			'k_Value' 			=>$freakmode));
	// problem weil versteckte felder in den widgets nicht gespeichert werden
	_e('Insert Javascript includes for Sliding(Open/Close)Widgets into Header of the Page. Be aware that you need to adapt some of the widgets surrounding HTML Code, to be able to use the Slider on some Widgets','widgetKing');
	echo king_get_start_p();
	 _e('Capability a User must have to see all adanced Widget Options ','widgetKing');
	echo king_get_capabilities_select('king_framework_freakmode', $freakmode, '');
	echo king_get_end_p();
	 _e('You can Use The Rolemanager to set up more Capabilites. f.ex. see advanced widgets or choose an existing Capability from the Dropdown.','widgetKing');

	echo king_get_start_p();
	echo king_get_submit('king_framework_submit','','king_framework_submit');
	echo king_get_end_p();
	echo king_get_end_form ();*/
}

/**
* @desc Version Check Heading
*/
function king_framework_version() {
	king_version_head('King_Framework',KINGFRAMEWORKVERSION);
}
add_action('admin_head','king_framework_version');

?>
