<?php
/*
Plugin Name: King Media
Plugin URI: http://www.blog.mediaprojekte.de
Description: Plugins for processing media-types like images / files / music / video
Version: 0.2
Author: George Leciejewski
Author URI: http://www.blog.mediaprojekte.de
*/

/**
* @desc initialize the admin plugin submenu
* @author Georg Leciejewski
*/
function king_media_adminmenu(){
	add_submenu_page('plugins.php', 'King Media', 'King Media', 'activate_plugins', 'king-media.php','king_media_loader' );
}
add_action('admin_menu','king_media_adminmenu');

/**
* @desc used by admin menu to get king plugins admin and set current pluginname
* @author Georg Leciejewski
*/
function king_media_loader(){
	$current_king_plugin='media';
	include_once (ABSPATH.'/wp-admin/admin-king-plugins.php');
}

/**
* @desc in first install puts an empty option to options table
* @author Georg Leciejewski
*/
function king_media_init(){
	$current_king_plugin='media';
	$current=get_settings('active_'.$current_king_plugin.'_plugins');
	if(empty ($current)){
		add_option('active_'.$current_king_plugin.'_plugins', ' ');
	}

}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'king_media_init');
}

/**
*hook for frontend inclusion of search plugins
* copied from wp-settings.
* needs to be in every kineg meta plugin or more global so that all sub plugs will be included right.
*/
$current_king_plugin='media';
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