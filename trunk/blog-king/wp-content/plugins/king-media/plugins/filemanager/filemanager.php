<?php
/*
Plugin Name:King_Filemanager
Plugin URI: http://www.blog.mediaprojekte.de
Description: A real Filemanager. Add buttons to the rich text editor for uploading, delete and inserting files.
Author: Georg Leciejewski
Version: 0.41
Author URI: http://www.blog.mediaprojekte.de
*/
/*  Copyright 2006  georg Leciejewski  (email :georg@mediaprojekte.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
define("KINGFILEMANAGERVERSION","041");

require_once(ABSPATH . 'wp-content/plugins/king-includes/library/king_widget_functions.php');

function add_filemanager_plugin($plugins)
{
	array_push($plugins, "filemanager");
    return $plugins;

}
function add_filemanager_button ($mce_buttons)
{
	array_push($mce_buttons, "filemanager");
    return $mce_buttons;

}
add_filter('mce_plugins','add_filemanager_plugin' );
add_filter('mce_buttons','add_filemanager_button');


//if in filemanager options
if(strstr($_SERVER['REQUEST_URI'], 'options-general.php') !== false){
	require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');
  		plugintoolkit(
			$plugin='filemanager',
			$array=array(
			'download_url' => 'Download URL  {textbox|80|}## This is where your download files are. Without trailing slash<br />http://myurl.de/wp-content/uploads',
			'document_root' => 'Document Root Path {textbox|80|}## Path to your download files. Without trailing slash<br /> /wwwrun/../wp-content/uploads',
			'allowed_ext' =>'Allow Files {textbox|80|}## File Extensions wich are allowed to upload. Seperated by comma. Ex.<br />html,doc,xls,txt,gif,jpeg,jpg,png,pdf,zip,swf,rar,tar,gz,mov,wmv,wav,rfp,psd,mp3,mp4,ogg,mmap,mmp,psd,odt,ods,odp',
			'deny_ext' =>'Deny Files {textbox|80|}## File Extensions wich are not allowed to upload. Seperated by comma. Ex.<br />php,php3,php4,phtml,shtml,cgi,pl',
			'dateformat' =>'Dateformat ## Datumsformat shown with the inserted file Ex." d.m.Y H:i"',
			'language' =>'Language {textbox|5|}## Language of filemanager. Currently supported: de, fr, pt_br, en',
			'max_file_size' =>'Max File Size ## Maximum allowed filesize in bytes. 2097152 = 2MB"',
			'delete' => 'delete'
			),
			$file='filemanager.php',
			$menu=array(
				'parent' => 'options-general.php' ,
				'access_level' => 'activate_plugins'
			)
		);
}

/**
* @desc Version Check Heading
*/
function king_filemanager_version() {
	king_version_head('King_Filemanager',KINGFILEMANAGERVERSION);
}
add_action('admin_head','king_filemanager_version');

?>