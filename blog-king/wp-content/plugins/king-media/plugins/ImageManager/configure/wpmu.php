<?php
// increase this number if you force an update to all user, note the user saved options will be overwritten
$IMConfig['configversion'] = 1; 

// 
$IMConfig['base_dir'] =  str_replace( "\\", "/",ABSPATH)  . UPLOADS; // UPLOADS is defiend in wp-settings.php
$atmp = parse_url(get_settings('siteurl'));
$IMConfig['base_url'] = str_replace( "\\", "/",$atmp['path']) . '/' . UPLOADS ;


$IMConfig['image_class'] = (function_exists("gd_info")) ? 'GD' : '';


$IMConfig['image_transform_lib_path'] = '/usr/local/bin/';
$IMConfig['safe_mode'] = (ini_get('safe_mode') == "1" || strtolower(ini_get('safe_mode')) == "on") ? true : false;

//Let the user create a subdir
$IMConfig['allow_new_dir'] = true;

//Let the user create upload files
$IMConfig['allow_upload'] = true;

//default thumbnail
$IMConfig['default_thumbnail'] = $ps_imagemanager_root .  '/img/default.gif';

$IMConfig['thumbnail_width'] = 96;
$IMConfig['thumbnail_height'] = 96;
$IMConfig['thumbnail_prefix'] = '.';
$IMConfig['thumbnail_dir'] = 	'.thumbs';
$IMConfig['validate_images'] = true;
$IMConfig['tmp_prefix'] = 	'.editor_';
/*Insert image defaults*/
$IMConfig['style']    = 	'';
$IMConfig['class']    = 	'';
$IMConfig['vspace']   = 	'';
$IMConfig['hspace']   = 	'';
$IMConfig['border']   = 	'0';
$IMConfig['align']    = 	'';
$IMConfig['insertas'] = 	'2';

if (is_array(get_settings('ps_imagemanager_options')) ) {
	$imgoptions = get_settings('ps_imagemanager_options');
	if (intval($IMConfig['configversion']) > intval($imgoptions['configversion'])) {
		update_option('ps_imagemanager_options',$IMConfig);
	}
} else {
	update_option('ps_imagemanager_options',$IMConfig);
}	  
?>