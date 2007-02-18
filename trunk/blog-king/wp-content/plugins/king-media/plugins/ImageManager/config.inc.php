<?php

require_once('../../../../../wp-config.php');


/*
	prevent direct access from users not logged in
*/
if ( (!empty($_COOKIE[USER_COOKIE]) && !wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true)) || (empty($_COOKIE[USER_COOKIE])) ) {
		nocache_headers();
	
		header('Location: ' . get_settings('siteurl') . '/wp-login.php');
		die();
}
nocache_headers();


load_plugin_textdomain('ImageManager','wp-content/plugins/king-media/plugins/ImageManager/lang');

$IMConfig = (get_settings('ps_imagemanager_options') != '') ? get_settings('ps_imagemanager_options') : array();
if (class_exists('RoleManager')) {
	$IMConfig['allow_new_dir'] = current_user_can('make_directory');
	$IMConfig['allow_upload'] = current_user_can('upload_files');
}

define('IMAGE_CLASS', $IMConfig['image_class']);
define('IMAGE_TRANSFORM_LIB_PATH', $IMConfig['image_transform_lib_path']);
?>