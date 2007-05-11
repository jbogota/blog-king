<?php

require_once('../../../../../../wp-config.php');

//	prevent direct access from users not logged in
if ( (!empty($_COOKIE[USER_COOKIE]) && !wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true)) || (empty($_COOKIE[USER_COOKIE])) ) {
		nocache_headers();
		header('Location: ' . get_settings('siteurl') . '/wp-login.php');
		die();
}
nocache_headers();

$KMConfig = get_settings('king-filemanager') ;


$MY_DOCUMENT_ROOT 		= $KMConfig['document_root'].'/';//'/www/htdocs/../wp-content/upload';
$MY_BASE_URL 			= $KMConfig['download_url']; //'http://www.url.de/wp-content/upload';
$MY_URL_TO_OPEN_FILE 	= $KMConfig['download_url']; //'http://www.url.de/wp-content/upload';
$MY_ALLOW_EXTENSIONS 	= explode(',', $KMConfig['allowed_ext']);
$MY_DENY_EXTENSIONS  	= explode(',', $KMConfig['deny_ext']);
$MY_LIST_EXTENSIONS 	= explode(',', $KMConfig['allowed_ext']);
$MY_MAX_FILE_SIZE 		= $KMConfig['max_file_size'];
$MY_DATETIME_FORMAT 	= $KMConfig['dateformat'];
$MY_LANG 				= $KMConfig['language'];
$MY_CHARSET 			= get_settings('blog_charset'); //get blog charset
$MY_ALLOW_CREATE 		= true;
$MY_ALLOW_DELETE 		= true;
$MY_ALLOW_RENAME 		= true;
$MY_ALLOW_MOVE 			= true;
$MY_ALLOW_UPLOAD 		= true;

$MY_NAME 				= 'insertfiledialog';
$MY_PATH 				= '';
$MY_UP_PATH 			= '';
function parse_icon($ext) {
		switch (strtolower($ext)) {
				case 'doc': return 'doc_small.gif';
				case 'rtf': return 'doc_small.gif';
				case 'txt': return 'txt_small.gif';
				case 'xls': return 'xls_small.gif';
				case 'csv': return 'xls_small.gif';
				case 'ppt': return 'ppt_small.gif';
				case 'html': return 'html_small.gif';
				case 'htm': return 'html_small.gif';
				case 'php': return 'script_small.gif';
				case 'php3': return 'script_small.gif';
				case 'cgi': return 'script_small.gif';
				case 'pdf': return 'pdf_small.gif';
				case 'rar': return 'rar_small.gif';
				case 'zip': return 'zip_small.gif';
				case 'gz': return 'gz_small.gif';
				case 'jpg': return 'jpg_small.gif';
				case 'gif': return 'gif_small.gif';
				case 'png': return 'png_small.gif';
				case 'bmp': return 'image_small.gif';
				case 'exe': return 'binary_small.gif';
				case 'bin': return 'binary_small.gif';
				case 'avi': return 'mov_small.gif';
				case 'mpg': return 'mov_small.gif';
				case 'moc': return 'mov_small.gif';
				case 'asf': return 'mov_small.gif';
				case 'mp3': return 'sound_small.gif';
				case 'wav': return 'sound_small.gif';
				case 'org': return 'sound_small.gif';
				case 'swf': return 'flash_small.gif';
				case 'mmap': return 'mmap_small.gif';
				case 'mmp': return 'mmap_small.gif';
				case 'psd': return 'psd_small.gif';
				case 'eps': return 'eps_small.gif';

		default:
				return 'def_small.gif';
		}
}
?>