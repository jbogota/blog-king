<?php
/*
Plugin Name: King Widgets
Version: 0.2
Plugin URI: http://www.website-king.de
Description: Great Widgets to be used in the sidebar or any other defined region in your template.
Author: Georg Leciejewski
Author URI:http://www.website-king.de
*/

/**
* @desc initialize the admin plugin submenu
* @author Georg Leciejewski
*/
function king_widgets_adminmenu(){
	add_submenu_page('plugins.php', 'King Widgets', 'King Widgets', 'activate_plugins', 'king-widgets.php','king_widgets_loader' );
}
add_action('admin_menu','king_widgets_adminmenu');

/**
* @desc used by admin menu to get king plugins admin and set current pluginname
* @author Georg Leciejewski
*/
function king_widgets_loader(){
	$current_king_plugin='widgets';
	//$current_king_plugin_file='search-plugins.php';
	include_once (ABSPATH.'/wp-admin/admin-king-plugins.php');

}
/**
* @desc in first install puts an empty option to options table
* @author Georg Leciejewski
*/
function king_widgets_init(){
	$current_king_plugin='widgets';
	$current=get_settings('active_'.$current_king_plugin.'_plugins');
	if(empty ($current)){
		add_option('active_'.$current_king_plugin.'_plugins', ' ');
	}

}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'king_widgets_init');
}

/**
*@desc hook for frontend inclusion of search plugins
* copied from wp-settings.
* needs to be in every king meta plugin or more global so that all sub plugs will be included right.
*/
$current_king_plugin='widgets';

if ( get_settings('active_'.$current_king_plugin.'_plugins') ) {
	$current_plugins = get_settings('active_'.$current_king_plugin.'_plugins');
	if ( is_array($current_plugins) ) {
		foreach ($current_plugins as $plugin) {
			if ('' != $plugin && file_exists(ABSPATH . 'wp-content/plugins/king-'.$current_king_plugin.'/plugins/' . $plugin))
				include_once(ABSPATH . 'wp-content/plugins/king-'.$current_king_plugin.'/plugins/' . $plugin);
		}
	}
}


?>