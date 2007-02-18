<?php
/*
Plugin Name: King Frontend
Version: 0.3
Plugin URI: http://www.website-king.de
Description: Plugins which affect the Frontend of your Website
Author: Georg Leciejewski
Author URI:http://www.website-king.de

*/

/**
* @desc initialize the admin plugin submenu
* @author Georg Leciejewski
*/
function king_frontend_adminmenu(){
	add_submenu_page('plugins.php', 'King Frontend', 'King Frontend', 'activate_plugins', 'king-frontend.php','king_frontend_loader' );
}
add_action('admin_menu','king_frontend_adminmenu');

/**
* @desc in first install puts an empty option to options table
* @author Georg Leciejewski
*/
function king_frontend_loader(){
	$current_king_plugin='frontend';
	include_once (ABSPATH.'/wp-admin/admin-king-plugins.php');
}
/**
* @desc in first install puts an empty option to options table
* @author Georg Leciejewski
*/
function king_frontend_init(){
	$current_king_plugin='frontend';
	$current=get_settings('active_'.$current_king_plugin.'_plugins');
	if(empty ($current)){
		add_option('active_'.$current_king_plugin.'_plugins', ' ');
	}

}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'king_frontend_init');
}

/**
* hook for frontend inclusion of search plugins
* copied from wp-settings.
* needs to be in every kineg meta plugin or more global so that all sub plugs will be included right.
*/
//
$current_king_plugin='frontend';

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