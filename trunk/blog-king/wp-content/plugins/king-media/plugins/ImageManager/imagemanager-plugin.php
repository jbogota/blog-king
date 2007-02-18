<?php
/*
Plugin Name: ImageManager
Plugin URI: http://soderlind.no/ImageManager
Description: PHP ImageManager + Editor for WordPress. It will not be enabled until you have configured it via the <a href="options-general.php?page=imagemanager-plugin.php">Options &gt;&gt; ImageManager</a> menu. *** Requires Wordpress 2.0 *** Licensed under the <a href="http://www.opensource.org/licenses/mit-license.php">MIT License</a>.
Version: 2.4.1
Author: Per Soderlind
Author URI: http://soderlind.no
*/


define('IS_WPMU',false); //NOTE WPMU HASN'T BEEN TESTES YET, IF YOU DO AND IT WORKS, PLEASE TELL ME: per [at] sodelind [dot] no

if (IS_WPMU) {
	$ps_imagemanager_root = get_settings('siteurl') . '/wp-content/mu-plugins/ImageManager'; // urlpath(dirname(__FILE__));
	include_once(dirname(__FILE__) . "/configure/wpmu.php");
} else {
	$ps_imagemanager_root = get_settings('siteurl') . '/wp-content/plugins/king-media/plugins/ImageManager'; // urlpath(dirname(__FILE__));
	include_once(dirname(__FILE__) . "/configure/wp.php");
}



// code from TinyMCE external plugin example: http://an-archos.com/anarchy-media-player#download
function ps_imagemanager_mce_plugins($plugins) {
	array_push($plugins, "-ps_imagemanager_tinymceplugin");
	return $plugins;
}
//Append the external plugins to the top row of the RTE
function ps_imagemanager_mce_buttons($buttons) {
	array_push($buttons, "separator", "ps_imagemanager_tinymceplugin");
	return $buttons;
}
//This tells tinyMCE where the external plugin is located in your WP plugin directory, either mu-plugins or plugins. To add more just copy and paste the echo and change the path. I use a variable with get_settings('siteurl') to find the path in both Mu and WP 2.1 
function ps_imagemanager_external_plugins() {
	global $ps_imagemanager_root;
	//echo 'tinyMCE.loadPlugin("ps_imagemanager_tinymceplugin","' . get_settings('siteurl')  .'/wp-content/plugins/king-media/plugins/ImageManager/tinymceplugin/");' . "\n"; 
	echo 'tinyMCE.loadPlugin("ps_imagemanager_tinymceplugin","' . $ps_imagemanager_root . '/tinymceplugin/");' . "\n"; 
	return;
}

function ps_imagemanager_init() {
	global $buttonsnap, $ps_imagemanager_root /*, $wp_db_version*/;

	if (is_array(get_settings('ps_imagemanager_options')) ) {
		include_once(dirname(__FILE__) . "/lib/buttonsnap.php"); // need it for buttonsnap_settext

		if (function_exists('wp_print_scripts')  && 'true' == get_user_option('rich_editing')) {
//		if (4351 <= $wp_db_version  && 'true' == get_user_option('rich_editing')) {
			add_filter("mce_plugins", "ps_imagemanager_mce_plugins", 5);
			add_filter("mce_buttons", "ps_imagemanager_mce_buttons", 5);
			add_action('tinymce_before_init','ps_imagemanager_external_plugins');
			add_action('admin_footer', 'ps_imagemanager_add_quicktag');

			function ps_imagemanager_add_quicktag(){
					echo <<<EOT
					<script type="text/javascript">
						<!--
							// code from http://roel.meurders.nl/wordpress-plugins/wp-addquicktag-plugin-for-adding-quicktags/
							if(wpaqToolbar = document.getElementById("ed_toolbar")){
								var wpaqNr, wpaqBut, wpaqStart, wpaqEnd;
								wpaqStart = '';
								wpaqEnd = '';
								wpaqNr = edButtons.length;
								edButtons[wpaqNr] = new edButton('ed_ImageManager','Imagemanager','', '','',-1);
								var wpaqBut = wpaqToolbar.lastChild;
								while (wpaqBut.nodeType != 1){
									wpaqBut = wpaqBut.previousSibling;
								}
								wpaqBut = wpaqBut.cloneNode(true);
								wpaqToolbar.appendChild(wpaqBut);
								wpaqBut.value = 'ImageManager';
								wpaqBut.title = wpaqNr;
								// wpaqBut.onclick = function () {edInsertTag(edCanvas, parseInt(this.title));}
								wpaqBut.onclick = function () {
									imagemagager_use_edInsert = true; // don't use buttonsnap_settext
									openImageManager();
								}
								wpaqBut.id = "ed_ImageManager";
							}
						//-->
					</script>
EOT;

			}			
		} else {
			$button_image = $ps_imagemanager_root . '/gfx/images.png';
			buttonsnap_separator();
			buttonsnap_jsbutton($button_image, 'ImageManager', 'openImageManager();');
		}
	}
}


function ps_imagemanager_admin_head () {
	global $ps_imagemanager_root;

  if ((!IS_WPMU) && strpos($_SERVER['REQUEST_URI'], 'options-general.php') !== false) {
	  echo "<script type='text/javascript' src='" . $ps_imagemanager_root . "/configure/lib/prototype.lite.js'></script>\n";
	  echo "<script type='text/javascript' src='" . $ps_imagemanager_root . "/configure/lib/moo.fx.js'></script>\n";
	  echo "<script type='text/javascript' src='" . $ps_imagemanager_root . "/configure/lib/smoothscroll.js'></script>\n";
	}

	if ((!IS_WPMU) && (strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false) && (ps_imagemanager_remote_version_check() == 1)) {
		echo "<script type='text/javascript' src='" . $ps_imagemanager_root . "/lib/prototype-1.4.0.js'></script>\n";
		$alert = "\n";
		$alert .= "\n<script type='text/javascript'>";
		$alert .= "\n//<![CDATA[";
		
		$alert .= "\nfunction alertNewVersion() {";
		$alert .= "\n	pluginname = '" . ps_imagemanager_info('pluginname') . "';";
		$alert .= "\n	allNodes = document.getElementsByClassName('name');";
		$alert .= "\n	for(i = 0; i < allNodes.length; i++) {";
		$alert .= "\n			var regExp=/<\S[^>]*>/g;";
		$alert .= "\n	    temp = allNodes[i].innerHTML;";
		$alert .= "\n	    if (temp.replace(regExp,'') == pluginname) {";
		$alert .= "\n		    Element.setStyle(allNodes[i].getElementsByTagName('a')[0], {color: '#f00'});";
		$alert .= "\n		    new Insertion.After(allNodes[i].getElementsByTagName('strong')[0],'<br/><small>" .  __("new version available","ImageManager") . "</small>');";
		$alert .= "\n	  	}";
		$alert .= "\n	}";
		$alert .= "\n}";
		
		$alert .= "\naddLoadEvent(alertNewVersion);";
		
		$alert .= "\n//]]>";
		$alert .= "\n</script>";
		$alert .= "\n";
		echo $alert;
	}
	
  if (is_array(get_settings('ps_imagemanager_options')) && (strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'],'page.php') || strpos($_SERVER['REQUEST_URI'],'page-new.php') || strpos($_SERVER['REQUEST_URI'],'post-new.php'))) {

		echo "<script type='text/javascript' src='" . $ps_imagemanager_root . "/assets/dialog.js'></script>\n";
		echo "<script type='text/javascript' src='" . $ps_imagemanager_root . "/imestandalone.js'></script>\n";
		
		$imgoptions = get_settings('ps_imagemanager_options');
		$thumbdir  = $imgoptions['thumbnail_dir'];
		$thumbprefix = $imgoptions['thumbnail_prefix'];
				
		$prepend_thumb = (empty($thumbdir)) ? $thumbprefix : $thumbdir . '/' . $thumbprefix;
		echo <<<MANAGER
		<script type="text/javascript">
		//<![CDATA[		
			var imagemagager_use_edInsert = false;

			var manager = new ImageManager('$ps_imagemanager_root','en');
			ImageSelector =
			{
				update : function(params)
				{
					var str = "";
					var thumbwidth = $imgoptions[thumbnail_width];
					var thumbheight = $imgoptions[thumbnail_height];					
					
					if (params.f_url != null) {
					
						if (params.f_width > params.f_height) {
							thumbheight = Math.round(thumbwidth/params.f_width * params.f_height)
						} else if (params.f_height > params.f_width) {
							thumbwidth = Math.round(thumbheight/params.f_height * params.f_width)
						}
					
						switch (params.f_insert) {

							case '5': // Link to image
								str += '<a href="' + params.f_url + '"'; 
								str += ' rel="lightbox" ';								
								str += ' >';
								str += (params.f_alt) ? params.f_alt  : 'insert link text here';
								str += '</a>';
							break;

							case '4': // Thumbnail
								str += '<img src="' + params.f_thumb_url + '"';
								str += (params.f_alt) ? ' alt="' + params.f_alt +'"' : '';
								str += (params.f_alt) ? ' title="' + params.f_alt +'"' : '';
								str += (params.f_style) ? ' style="' + params.f_style +'"' : '';
								str += (params.f_class) ? ' class="' + params.f_class +'"' : '';								
								str += (params.f_align) ? ' align="' + params.f_align +'"' : '';
								str += ' width="' + thumbwidth +'"';
								str += ' height="' + thumbheight +'"';
								str += (params.f_horiz) ? ' hspace="' + params.f_horiz +'"' : '';
								str += (params.f_vert) ? ' vspace="' + params.f_vert +'"' : '';
								str += (params.f_border) ? ' border="' + params.f_border +'"' : '';
								str += ' />';
							break;

							case '3': // Thumbnail with link to Image
								str += '<a href="' + params.f_url + '"'; 
								str += ' rel="lightbox" ';								
								str += ' >';								
								str += '<img src="' + params.f_thumb_url + '"';
								str += (params.f_alt) ? ' alt="' + params.f_alt +'"' : '';
								str += (params.f_alt) ? ' title="' + params.f_alt +'"' : '';
								str += (params.f_style) ? ' style="' + params.f_style +'"' : '';
								str += (params.f_class) ? ' class="' + params.f_class +'"' : '';								
								str += (params.f_align) ? ' align="' + params.f_align +'"' : '';
								str += ' width="' + thumbwidth +'"';
								str += ' height="' + thumbheight +'"';
								str += (params.f_horiz) ? ' hspace="' + params.f_horiz +'"' : '';
								str += (params.f_vert) ? ' vspace="' + params.f_vert +'"' : '';
								str += (params.f_border) ? ' border="' + params.f_border +'"' : '';
								str += ' />';
								str += '</a>';
							break;
							
							case '2': // Thumbnail with PopUp
								str += '<a href="' + params.f_url + '"';
								str += " onclick=\"ps_imagemanager_popup(this.href,'" + params.f_alt + "','" + params.f_width + "','" + params.f_height + "');return false\" onfocus=\"this.blur()\"	";
								str += ' >';
								str += '<img src="' + params.f_thumb_url + '"';
								str += (params.f_alt) ? ' alt="' + params.f_alt +'"' : '';
								str += (params.f_alt) ? ' title="' + params.f_alt +'"' : '';
								str += (params.f_style) ? ' style="' + params.f_style +'"' : '';
								str += (params.f_class) ? ' class="' + params.f_class +'"' : '';								
								str += (params.f_align) ? ' align="' + params.f_align +'"' : '';
								str += ' width="' + thumbwidth +'"';
								str += ' height="' + thumbheight +'"';
								str += (params.f_horiz) ? ' hspace="' + params.f_horiz +'"' : '';
								str += (params.f_vert) ? ' vspace="' + params.f_vert +'"' : '';
								str += (params.f_border) ? ' border="' + params.f_border +'"' : '';
								str += ' />';
								str += '</a>';
							break;
						
							case '1': // Image
								str += '<img src="' + params.f_url + '"';
								str += (params.f_alt) ? ' alt="' + params.f_alt +'"' : '';
								str += (params.f_alt) ? ' title="' + params.f_alt +'"' : '';
								str += (params.f_style) ? ' style="' + params.f_style +'"' : '';
								str += (params.f_class) ? ' class="' + params.f_class +'"' : '';								
								str += (params.f_align) ? ' align="' + params.f_align +'"' : '';
								str += (params.f_width) ? ' width="' + params.f_width +'"' : '';
								str += (params.f_height) ? ' height="' + params.f_height +'"' : '';
								str += (params.f_horiz) ? ' hspace="' + params.f_horiz +'"' : '';
								str += (params.f_vert) ? ' vspace="' + params.f_vert +'"' : '';
								str += (params.f_border) ? ' border="' + params.f_border +'"' : '';
								str += ' />';
							break;
							
							default:
								str += '<img src="' + params.f_url + '"';
								str += (params.f_alt) ? ' alt="' + params.f_alt +'"' : '';
								str += (params.f_alt) ? ' title="' + params.f_alt +'"' : '';
								str += (params.f_style) ? ' style="' + params.f_style +'"' : '';
								str += (params.f_class) ? ' class="' + params.f_class +'"' : '';								
								str += (params.f_align) ? ' align="' + params.f_align +'"' : '';
								str += (params.f_width) ? ' width="' + params.f_width +'"' : '';
								str += (params.f_height) ? ' height="' + params.f_height +'"' : '';
								str += (params.f_horiz) ? ' hspace="' + params.f_horiz +'"' : '';
								str += (params.f_vert) ? ' vspace="' + params.f_vert +'"' : '';
								str += (params.f_border) ? ' border="' + params.f_border +'"' : '';
								str += ' />';
						}
						if (typeof('imagemagager_use_edInsert') != 'undefined' && imagemagager_use_edInsert) {
							edInsertContent(edCanvas, str);
							imagemagager_use_edInsert = false;
						} else {
							buttonsnap_settext(str);
						}	
							
					}
				},
				select : function()
				{
					manager.popManager(this);
				}
			};

			function openImageManager() {
				ImageSelector.select();
			}

		//]]>
		</script>

MANAGER;
		echo "\n";
	} // if (is_array(get_settings('ps_imagemanager_options')) && (strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'],'page-new.php')))
}


function ps_imagemanager_wp_head() {

echo<<<NEWWIN
		<script type="text/javascript">
		//<![CDATA[
		
			function basename (path) { return path.replace( /.*\//, "" ); }
	
			var winimg=null;
			function ps_imagemanager_popup(imgurl,title,w,h) {
				lpos=(screen.width)?(screen.width-w)/2:100;
				tpos=(screen.height)?(screen.height-h)/2:100;
				settings='width='+w+',height='+h+',top='+tpos+',left='+lpos+',scrollbars=no,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
				winimg=window.open('about:blank','imagemanagerpopup',settings);
	
				var doc = '';
				doc += '<html><head>';
				doc += '<title>' + title + ' - ' + basename(imgurl) + '(' + w + 'x' + h +')</title>';
				doc += '<style type="text/css"><!-- body { margin:0px; padding:0px; } --></style>';
				doc += '</head>';
				doc += '<body onload="self.focus();">';
				doc += '<img style="cursor:pointer;" src="' + imgurl + '" title="' + title + '" onclick="self.close();"/>';
				doc += '</body></html>';
				
				winimg.document.writeln(doc);
				winimg.document.close();
			}	
			
		//]]>
		</script>

NEWWIN;
}

function ps_imagemanager_add_capabilities($caps) {
	// remove old roles, if they exist
	unset($caps[array_search('ImageManager Upload', $caps)]);
	unset($caps[array_search('ImageManager MkDir', $caps)]);

	// add role
	$upload_files = array_search('upload_files', $caps); //Prior to PHP 4.2.0, array_search() returns NULL on failure instead of FALSE.
	if ($upload_files == FALSE || $upload_files == NULL) {
		$caps[] = 'upload_files';
	}
	$make_directory = array_search('make_directory', $caps);
	if ($make_directory == FALSE || $make_directory == NULL) {
		$caps[] = 'make_directory';
	}
	$edit_image = array_search('edit_image', $caps);
	if ($edit_image == FALSE || $edit_image == NULL) {
		$caps[] = 'edit_image';
	}	
	$delete_image = array_search('delete_image', $caps);
	if ($delete_image == FALSE || $delete_image == NULL) {
		$caps[] = 'delete_image';
	}
	return $caps;
}

function ps_imagemanager_disable_wpupload($uploading_iframe_src) {
	if (is_array(get_settings('ps_imagemanager_options')) && array_key_exists('wpupload_disabled', get_settings('ps_imagemanager_options')) ) {
		$atmp = get_settings('ps_imagemanager_options');
		if ($atmp['wpupload_disabled'] == 'true') {
			return "";
		} else {
			return $uploading_iframe_src;
		}
	} else {
		return $uploading_iframe_src;
	}
}


add_action('wp_head','ps_imagemanager_wp_head');
add_action('init', 'ps_imagemanager_init');
add_action('admin_head', 'ps_imagemanager_admin_head');
if (! IS_WPMU) {
	add_action('admin_menu', 'ps_imagemanager_admin');
	add_filter('uploading_iframe_src','ps_imagemanager_disable_wpupload');
}
add_filter('capabilities_list', 'ps_imagemanager_add_capabilities');

?>