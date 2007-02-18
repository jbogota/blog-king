<?php
/*
Plugin Name: Admin CSS
Plugin URI: http://www.website-king.de
Description: Improvemts of the Adminarea. Choose different CSS Layouts
Author: Georg Leciejewski
Version: 0.2
Author URI: http://www.website-king.de
*/
require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');
plugintoolkit(
	$plugin='admin_css',
	array(
		'css' => 'Admin CSS{radio|bluebrown2.css|bluebrown2.css|bluewhite.css|bluewhite.css|bluegreen.css|bluegreen.css|blueblue.css|blueblue.css} ## ',
		//'debug' => 'debug',
		'delete' => 'delete',
	),
	$file='king-css.php',
	$menu=array(
		'parent' => 'options-general.php' ,
		'access_level' => 'activate_plugins',
	),
	$newFiles=''
);

function king_admin_css() {
global $admin_css;
	echo '<link rel="stylesheet" type="text/css" href="' . get_settings('siteurl') . '/wp-content/plugins/king-admin/plugins/admin-css/' . $admin_css->option['css'] . '" />';
}

add_action('admin_head', 'king_admin_css');

?>