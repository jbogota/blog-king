<?php
/*
Plugin Name: King Search
Version: 0.5
Plugin URI: http://www.website-king.de
Description: A Bundle to improve the internal Search engine + results of Wordpress.
Author: Georg Leciejewski
Author URI:http://www.website-king.de
*/

/**
* TO DO
* - globalize / normalize include pathes
*
*/

/**
* @desc initialize the admin plugin submenu
* @author Georg Leciejewski
*/
function king_search_plugin_adminmenu(){
	add_submenu_page('plugins.php', 'King Search', 'King Search', 'activate_plugins', 'king-search.php','king_search_loader' );
}
add_action('admin_menu','king_search_plugin_adminmenu');

/**
* @desc used by admin menu to get king plugins admin and set current pluginname
* @author Georg Leciejewski
*/
function king_search_loader(){
	$current_king_plugin='search';
	//$current_king_plugin_file='search-plugins.php';
	include_once (ABSPATH.'/wp-admin/admin-king-plugins.php');

}
/**
* @desc in first install puts an empty option to options table
* @author Georg Leciejewski
*/
function king_search_init(){
	$current_king_plugin='search';
	$current=get_settings('active_'.$current_king_plugin.'_plugins');
	if(empty ($current)){
		add_option('active_'.$current_king_plugin.'_plugins', ' ');
	}

}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'king_search_init');
}

/**
*@desc hook for frontend inclusion of search plugins
* copied from wp-settings.
* needs to be in every king meta plugin or more global so that all sub plugs will be included right.
*/
$current_king_plugin='search';

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
