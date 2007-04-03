<?php
/***********************************************************************
** Title.........:    Insert File Dialog, File Manager
** Version.......:    1.1
** Authors.......:	georg leciejewski (georg@mediaprojekte.de)
* 					Al Rashid <alrashid@klokan.sk>
**                   Xiang Wei ZHUO <wei@zhuo.org>
** Filename......:    insert_file.php
***********************************************************************/

require("config.inc.php");
//include ("langs/de.php");
if(file_exists('../langs/'.$MY_LANG.'.php'))
{
	include ('../langs/'.$MY_LANG.'.php');
}
else
{
	include ('../langs/en.php');
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
		<head>
		<title><?php echo _filemanager_insertfile ?></title>
		<?php
		echo '<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />'."\n";
		echo '<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />'."\n";
		echo '<META HTTP-EQUIV="Expires" CONTENT="Fri, Oct 24 1976 00:00:00 GMT" />'."\n";
		echo '<meta http-equiv="content-language" content="'.$MY_LANG.'" />'."\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.$MY_CHARSET.'" />'."\n";
		?>
		<script type="text/javascript" src="js/popup.js"></script>
        <script type="text/javascript" src="js/dialog.js"></script>
        <script language="javascript" src="../../../tiny_mce_popup.js"></script>
		<style type="text/css">
				body { padding: 5px; }
				table {font: 11px Tahoma,Verdana,sans-serif;}
				form p {margin-top: 5px;margin-bottom: 5px;}
				fieldset { padding: 0px 10px 5px 5px; }
				select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
				button { width: 70px; }
				.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
				border-bottom: 1px solid black; letter-spacing: 2px;}
				form { padding: 0px; margin: 0px; }
				a { padding: 2px; border: 1px solid ButtonFace;}
				a img { border: 0px; vertical-align:bottom; }
				a:hover { border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight; }
		</style>
		<script language="JavaScript" type="text/javascript">
		/*<![CDATA[*/
				var preview_window = null;
				var resize_iframe_constant = 150;
				<?php
				if (is_array($MY_DENY_EXTENSIONS)) {
						echo 'var DenyExtensions = [';
						foreach($MY_DENY_EXTENSIONS as $value) echo '"'.$value.'", ';
						echo '""];
						';
				}
				if (is_array($MY_ALLOW_EXTENSIONS)) {
						echo 'var AllowExtensions = [';
						foreach($MY_ALLOW_EXTENSIONS as $value) echo '"'.$value.'", ';
						echo '""];
						';
				}
				?>

				function Init() {
				var formObj = document.forms[0];
                var editor_url   = tinyMCE.baseURL;
                var plugin_url = "/plugins/filemanager/InsertFile/";
				var icon_url = "img/ext/";
				document.getElementById('f_url').value = tinyMCE.getWindowArg('href');
				document.getElementById('f_caption').value = tinyMCE.getWindowArg('title');

				//Get icon based on filename
				var file = tinyMCE.getWindowArg('href');
				var file_name = file.replace(editor_url+plugin_url+icon_url, '', 'gi');
				var file_ext = getExtension(file_name);
				var icon_type = file_ext+'_small.gif';
				f_icon_value = icon_url+icon_type;

				formObj.f_icon.value = f_icon_value;

				var icon_src = tinyMCE.getWindowArg('src');
				if (icon_src)
				{
					formObj.f_addicon.checked = true;
					formObj.f_icon.value = f_icon_value;
				}
					var file_date = tinyMCE.getWindowArg('date');
					var file_size = tinyMCE.getWindowArg('size');

					if (file_size && file_size != 'null')
					{
						formObj.f_addsize.checked = true;
						formObj.f_size.value = file_size;
					}else{
						formObj.f_size.value = tinyMCE.getWindowArg('size');
					}
					if (file_date && file_date != 'null')
					{
						formObj.f_adddate.checked = true;
						formObj.f_date.value = file_date;
					}else{
						formObj.f_date.value = tinyMCE.getWindowArg('date');
					}
				formObj.insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('action'), 'Insert', true);
				};

				function onOK() {
						if (window.opener) {
						var formObj = document.forms[0];
						if (formObj.f_url.value == '' || formObj.f_caption.value == ''){
							alert("You must select a file (or enter a URL) and a Caption, or Cancel.");
							return false;
						}
						if (formObj.f_addsize.checked == true && (formObj.f_size.value == '' || formObj.f_size.value == 'null')){
						alert('Please reselect the file to insert the Size and/or Date value');
						return false;
						}
						if (formObj.f_adddate.checked == true && (formObj.f_date.value == '' || formObj.f_date.value == 'null')){
						alert('Please reselect the file to insert the Size and/or Date value');
						return false;
						}
				var formObj = document.forms[0];
        		var editor_url   = tinyMCE.baseURL;
                var plugin_url = "/plugins/filemanager/InsertFile/";

				var f_url = formObj.f_url.value;
				var f_caption = formObj.f_caption.value;
				var f_icon = formObj.f_icon.value;
				var f_icon_url = editor_url+plugin_url+f_icon;
				var f_size = formObj.f_size.value;
				var f_date = formObj.f_date.value;

				var args = Array();
					args['href'] = f_url;
					args['a_title'] = f_caption;
					args['caption'] = f_caption;
					args['i_alt'] = f_caption;
					args['i_src'] = f_icon_url;
					args['f_size'] = f_size;
					args['f_date'] = f_date;

					args['icon'] = false;
					args['size'] = false;
					args['date'] = false;

				if (formObj.f_addicon.checked==true) {
					args['icon'] = true;
				}else{
					args['icon'] = false;
				}
				if (formObj.f_addsize.checked==true) {
				args['size'] = true;
				}else{
					args['size'] = false;
				}
				if (formObj.f_adddate.checked==true) {
					args['date'] = true;
				}else{
					args['date'] = false;
				}
				tinyMCE.execCommand("mceFileManager",false,args);
				top.close();
				}

		};

				function onCancel() {
				top.close();
				return false;
				};

				function changeDir(selection) {
						changeLoadingStatus('load');
						var newDir = selection.options[selection.selectedIndex].value;
						var postForm2 = fileManager.document.getElementById('form2');
						postForm2.elements["action"].value="changeDir";
						postForm2.elements["path"].value=newDir;
						postForm2.submit();
				}

				function goUpDir() {
						var selection = document.forms[0].path;
						var dir = selection.options[selection.selectedIndex].value;
						if(dir != '/'){
							changeLoadingStatus('load');
								var postForm2 = fileManager.document.getElementById('form2');
								postForm2.elements["action"].value="changeDir";
								postForm2.elements["path"].value=postForm2.elements["uppath"].value;
								postForm2.submit();
						}
				}

				function newFolder() {
						var selection = document.forms[0].path;
						var path = selection.options[selection.selectedIndex].value;
						var folder = prompt('<?php echo _filemanager_newfolder ?>','');
						if (folder) {
							changeLoadingStatus('load');
								var postForm2 = fileManager.document.getElementById('form2');
								postForm2.elements["action"].value="createFolder";
								postForm2.elements["file"].value=folder;
								postForm2.submit();
						}
						return false
				}

				function deleteFile() {
						var folderItems = fileManager.sta.getSelectedItems();
						var folderItemsLength = folderItems.length;
						var fileItems = fileManager.stb.getSelectedItems();
						var fileItemsLength = fileItems.length;
						var message = "<?php echo _filemanager_delete ?>";
			if ((folderItemsLength == 0) && (fileItemsLength == 0)) return false;
						if (folderItemsLength > 0) {
								message = message + " " + folderItemsLength + " " + "<?php echo _filemanager_folders ?>";
						}
						if (fileItemsLength > 0) {
								message = message + " " + fileItemsLength + " " + "<?php echo _filemanager_files ?>";
						}
						if (confirm(message+" ?")) {
								var postForm2 = fileManager.document.getElementById('form2');
								for (var i=0; i<folderItemsLength; i++) {
										var strId = folderItems[i].getAttribute("id").toString();
										var trId = parseInt(strId.substring(1, strId.length));
										var i_field = fileManager.document.createElement('INPUT');
										i_field.type = 'hidden';
										i_field.name = 'folders[' + i.toString() + ']';
										i_field.value = fileManager.folderJSArray[trId][1];
										postForm2.appendChild(i_field);
								}
								for (var i=0; i<fileItemsLength; i++) {
										var strId = fileItems[i].getAttribute("id").toString();
										var trId = parseInt(strId.substring(1, strId.length));
										var i_field = fileManager.document.createElement('INPUT');
										i_field.type = 'hidden';
										i_field.name = 'files[' + i.toString() + ']';
										i_field.value = fileManager.fileJSArray[trId][1];
										postForm2.appendChild(i_field);
								}
								changeLoadingStatus('load');
								postForm2.elements["action"].value="delete";
								postForm2.submit();
						}
				}

				function renameFile() {
						var folderItems = fileManager.sta.getSelectedItems();
						var folderItemsLength = folderItems.length;
						var fileItems = fileManager.stb.getSelectedItems();
						var fileItemsLength = fileItems.length;
						var postForm2 = fileManager.document.getElementById('form2');
						if ((folderItemsLength == 0) && (fileItemsLength == 0)) return false;
						if (!confirm('<?php echo _filemanager_renamewarning ?>')) return false;
						for (var i=0; i<folderItemsLength; i++) {
								var strId = folderItems[i].getAttribute("id").toString();
								var trId = parseInt(strId.substring(1, strId.length));
				var newname = prompt('<?php echo _filemanager_renamefolder ?>', fileManager.folderJSArray[trId][1]);
								if (!newname) continue;
								if (!newname == fileManager.folderJSArray[trId][1]) continue;
								var i_field = fileManager.document.createElement('INPUT');
								i_field.type = 'hidden';
								i_field.name = 'folders[' + i.toString() + '][oldname]';
								i_field.value = fileManager.folderJSArray[trId][1];
								postForm2.appendChild(i_field);
								var ii_field = fileManager.document.createElement('INPUT');
								ii_field.type = 'hidden';
								ii_field.name = 'folders[' + i.toString() + '][newname]';
								ii_field.value = newname;
								postForm2.appendChild(ii_field);
						}
						for (var i=0; i<fileItemsLength; i++) {
								var strId = fileItems[i].getAttribute("id").toString();
								var trId = parseInt(strId.substring(1, strId.length));
								var        newname = getNewFileName(fileManager.fileJSArray[trId][1]);
								if (!newname) continue;
								if (newname == fileManager.fileJSArray[trId][1]) continue;
								var i_field = fileManager.document.createElement('INPUT');
								i_field.type = 'hidden';
								i_field.name = 'files[' + i.toString() + '][oldname]';
								i_field.value = fileManager.fileJSArray[trId][1];
								postForm2.appendChild(i_field);
								var ii_field = fileManager.document.createElement('INPUT');
								ii_field.type = 'hidden';
								ii_field.name = 'files[' + i.toString() + '][newname]';
								ii_field.value = newname;
								postForm2.appendChild(ii_field);
						}
						changeLoadingStatus('load');
						postForm2.elements["action"].value="rename";
						postForm2.submit();
				}

				function moveFile() {
						var folderItems = fileManager.sta.getSelectedItems();
						var folderItemsLength = folderItems.length;
						var fileItems = fileManager.stb.getSelectedItems();
						var fileItemsLength = fileItems.length;
						var postForm2 = fileManager.document.getElementById('form2');
						if ((folderItemsLength == 0) && (fileItemsLength == 0)) return false;
						if (!confirm('<?php echo _filemanager_renamewarning ?>')) return false;
						var postForm2 = fileManager.document.getElementById('form2');
						Dialog("move.php", function(param) {
								if (!param) // user must have pressed Cancel
										return false;
								else {
									postForm2.elements["newpath"].value=param['newpath'];
									moveFiles();
								}
						}, null);
				}

		function moveFiles() {
						var folderItems = fileManager.sta.getSelectedItems();
						var folderItemsLength = folderItems.length;
						var fileItems = fileManager.stb.getSelectedItems();
						var fileItemsLength = fileItems.length;
						var postForm2 = fileManager.document.getElementById('form2');
						for (var i=0; i<folderItemsLength; i++) {
								var strId = folderItems[i].getAttribute("id").toString();
								var trId = parseInt(strId.substring(1, strId.length));
								var i_field = fileManager.document.createElement('INPUT');
								i_field.type = 'hidden';
								i_field.name = 'folders[' + i.toString() + ']';
								i_field.value = fileManager.folderJSArray[trId][1];
								postForm2.appendChild(i_field);
						}
						for (var i=0; i<fileItemsLength; i++) {
								var strId = fileItems[i].getAttribute("id").toString();
								var trId = parseInt(strId.substring(1, strId.length));
								var i_field = fileManager.document.createElement('INPUT');
								i_field.type = 'hidden';
								i_field.name = 'files[' + i.toString() + ']';
								i_field.value = fileManager.fileJSArray[trId][1];
								postForm2.appendChild(i_field);
						}
						changeLoadingStatus('load');
						postForm2.elements["action"].value="move";
						postForm2.submit();
				}

				function openFile() {
						var urlPrefix = "<?php echo $MY_URL_TO_OPEN_FILE; ?>";
						var myPath = fileManager.document.getElementById('form2').elements["path"].value;
						var folderItems = fileManager.sta.getSelectedItems();
						var folderItemsLength = folderItems.length;
						var fileItems = fileManager.stb.getSelectedItems();
						var fileItemsLength = fileItems.length;

						for (var i=0; i<folderItemsLength; i++) {
								var strId = folderItems[i].getAttribute("id").toString();
								var trId = parseInt(strId.substring(1, strId.length));
							window.open(urlPrefix+myPath+fileManager.folderJSArray[trId][1],'','');
						}
						for (var i=0; i<fileItemsLength; i++) {
								var strId = fileItems[i].getAttribute("id").toString();
								var trId = parseInt(strId.substring(1, strId.length));
								window.open(urlPrefix+myPath+fileManager.fileJSArray[trId][1],'','');
						}
				}

				function doUpload() {
						var isOK = 1;
						var fileObj = document.forms[0].uploadFile;
						if (fileObj == null) return false;

						newname = fileObj.value;
						isOK = checkExtension(newname);
						if (isOK == -2) {
								alert('<?php echo _filemanager_extnotallowed ?>');
								return false;
						}
						if (isOK == -1) {
								alert('<?php echo _filemanager_extmissing ?>');
								return false;
						}
						changeLoadingStatus('upload');
				}
				function getExtension(name) {
						var regexp = /\/|\\/;
						var parts = name.split(regexp);
						var filename = parts[parts.length-1].split(".");
						if (filename.length <= 1) {
								return(-1);
						}
						var ext = filename[filename.length-1].toLowerCase();
						return ext;
				}

				function checkExtension(name) {
						var regexp = /\/|\\/;
						var parts = name.split(regexp);
						var filename = parts[parts.length-1].split(".");
						if (filename.length <= 1) {
								return(-1);
						}
						var ext = filename[filename.length-1].toLowerCase();

						for (i=0; i<DenyExtensions.length; i++) {
								if (ext == DenyExtensions[i]) return(-2);
						}
						for (i=0; i<AllowExtensions.length; i++) {
								if (ext == AllowExtensions[i])        return(1);
						}
						return(-2);
				}
				function getNewFileName(name) {
						var isOK = 1;
						var newname='';
						do {
								newname = prompt('<?php echo _filemanager_renamefile ?>', name);
								if (!newname) return false;
								isOK = checkExtension(newname);
								if (isOK == -2) alert('<?php echo _filemanager_extnotallowed ?>');
								if (isOK == -1) alert('<?php echo _filemanager_extmissing ?>');
						} while (isOK != 1);
						return(newname);
				}

				function selectFolder() {
						Dialog("move.php", function(param) {
								if (!param) // user must have pressed Cancel
										return false;
								else {
										var postForm2 = fileManager.document.getElementById('form2');
										postForm2.elements["newpath"].value=param['newpath'];
								}
						}, null);

				}

				function refreshPath(){
						var selection = document.forms[0].path;
						changeDir(selection);
				}

				function winH() {
				if (window.innerHeight)
					return window.innerHeight;
				else if
				(document.documentElement &&
				document.documentElement.clientHeight)
					return document.documentElement.clientHeight;
				else if
				(document.body && document.body.clientHeight)
					return document.body.clientHeight;
				else
					return null;
				}

				function resize_iframe() {
						document.getElementById("fileManager").height=winH()-resize_iframe_constant;//resize the iframe according to the size of the window
				}

				function MM_findObj(n, d) { //v4.01
				var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
					d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
				if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
				for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
				if(!x && d.getElementById) x=d.getElementById(n); return x;
				}

				function MM_showHideLayers() { //v6.0
				var i,p,v,obj,args=MM_showHideLayers.arguments;
				for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
					if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
					obj.visibility=v; }
				}

				function changeLoadingStatus(state) {
						var statusText = null;
						if(state == 'load') {
								statusText = '<?php echo _filemanager_loading ?> ';
						}
						else if(state == 'upload') {
								statusText = '<?php echo _filemanager_uploading ?>';
						}
						if(statusText != null) {
								var obj = MM_findObj('loadingStatus');
								if (obj != null && obj.innerHTML != null)
										obj.innerHTML = statusText;
								MM_showHideLayers('loading','','show')
						}
				}
				function openHelp() {
				window.open('docs/<?php echo $help_lang; ?>/index.htm', 'Help', 'menubar=no,toolbar=no,scrollbars=yes,left=20,top=20,width=700,height=400');
				}

		/*]]>*/
		</script>
</head>
<body onload="Init();">
	<form action="files.php?dialogname=<?php echo $MY_NAME; ?>" name="form1" method="post" target="fileManager" enctype="multipart/form-data">
		<div id="loading" style="position:absolute; left:200px; top:130px; width:184px; height:48px; z-index:1" class="statusLayer">
			<div id= "loadingStatus" align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif; z-index:2;">
				<?php echo _filemanager_loading ?>
			</div>
		</div>
		<fieldset>
			<legend>
				<?php echo _filemanager_filemanager;?>
				<?php echo '<span style="font-size:x-small; "> - '._filemanager_ctrlshift.'</span>';?>
			</legend>
			<div style="margin:5px;">
				<label for="path">
						<?php echo _filemanager_directory ?>
				</label>
				<select name="path" id="path" style="width:20em" onChange="changeDir(this)">
						<option value="/">/</option>
				</select>
				<?php
				echo '<a href="#" onClick="javascript:goUpDir();"><img src="img/btn_up.gif" width="18" height="18" border="0" alt="" title="'._filemanager_up.'" /></a>';
				if (/*($fm_new_folder_auth || $isAdmin) && */$MY_ALLOW_CREATE) {
						echo '<a href="#" onClick="javascript:newFolder();"><img src="img/btn_create.gif"  width="18" height="18" border="0" alt="" title="'._filemanager_newfolder.'" /></a>';
				}
				if (/*($fm_delete_auth || $isAdmin) && */$MY_ALLOW_DELETE) {
						echo '<a href="#" onClick="javascript:deleteFile();"><img src="img/btn_delete.gif" width="18" height="18" border="0" alt="" title="'._filemanager_delete.'" /></a>';
				}
				if (/*($fm_rename_auth || $isAdmin) && */$MY_ALLOW_RENAME) {
						echo '<a href="#" onClick="javascript:renameFile();"><img src="img/btn_rename.gif" width="18" height="18" border="0" alt="" title="'._filemanager_rename.'" /></a>';
				}
				if (/*($fm_move_auth || $isAdmin) && */$MY_ALLOW_MOVE) {
						echo '<a href="#" onClick="javascript:moveFile();"><img src="img/btn_move.gif" width="18" height="18" border="0" alt="" title="'._filemanager_move.'" /></a>';
				}
				echo '<a href="#" onClick="javascript:openFile();"><img src="img/btn_open.gif"  width="18" height="18" border="0" alt="" title="'._filemanager_openfile.'" /></a>';
				echo '<a href="#" onclick="javascript:openHelp();"><img src="img/ext/def_small.gif" border="0" alt="'._filemanager_help.'"  title="'._filemanager_help.'" /></a>';
			?>
				<input id="sortby" type="hidden" value="0" />
			</div>

			<div style="margin:5px;">
				<iframe src="files.php?dialogname=<?php echo $MY_NAME; ?>&amp;refresh=1" name="fileManager" id="fileManager" background="Window" marginwidth="0" marginheight="0" valign="top" scrolling="no" frameborder="0" hspace="0" vspace="0" width="600px" height="250px" style="background-color: Window; margin:0px; padding:0px; border:0px; vertical-align:top;"></iframe>
			</div>

			<table border="0" align="center" cellpadding="2" cellspacing="2" summary="file properties">
				<tr>
					<td nowrap align="right"><label for="f_url"><?php echo _filemanager_url ?> </label></td>
					<td><input name="url" id="f_url" type="text" style="width:20em" size="30" /></td>
					<td nowrap align="right"><label for="f_caption"><?php echo _filemanager_caption ?> </label></td>
					<td><input name="caption" id="f_caption" type="text" style="width:20em" size="30" /></td>
				</tr>
			</table>
			<table border="0" align="center" cellpadding="2" cellspacing="2"  summary="file insert properties">
				<tr>
					<td>
						<input id="f_addicon" value="f_addicon" type="checkbox" />
						<input id="f_icon" value="f_icon" type="hidden" />
					</td>
					<td>
						<label for="f_addicon"><?php echo _filemanager_file_type ?> </label>
					</td>
					<td>
						<input id="f_addsize" value="f_addsize" type="checkbox" />
						<input id="f_size" value="f_size" type="hidden" />
					</td>
					<td>
						<label for="f_addsize"><?php echo _filemanager_file_size ?> </label>
					</td>
					<td>
						<input id="f_adddate" value="f_adddate" type="checkbox" />
						<input id="f_date" value="f_date" type="hidden" />
					</td>
					<td>
						<label for="f_adddate"><?php echo _filemanager_file_date ?> </label>
					</td>
				</tr>
			</table>
			<div style="text-align:center; padding:2px;">
				<?php
				if (/*($fm_upload_auth || $isAdmin) && */$MY_ALLOW_UPLOAD) {
				?>
					<label for="uploadFile">
					<?php echo _filemanager_upload ?>
					</label>
					<input name="uploadFile" type="file" id="uploadFile" size="72" />
				<input type="submit" style="width:5em" value="<?php echo _filemanager_upload ?>" onClick="javascript:return doUpload();" />
				<?php
				}
				?>
			</div>
		</fieldset>

		<div style="text-align: right; margin-top:5px;">
			<input type="button" name="refresh" value="<?php echo _filemanager_refresh ?>" onclick="return refreshPath();" />
			<input type="button" name="cancel" value="<?php echo _filemanager_cancel ?>" onclick="return onCancel();" />
			<input type="reset" name="reset" value="<?php echo _filemanager_reset ?>" />
			<input type="button" name="insert" id="insert" value="<?php echo _filemanager_insertfile ?>" onclick="return onOK();" />
		</div>
		<div style="position:absolute; bottom:-5px; right:-3px;">
			<img src="img/btn_Corner.gif" width="14" height="14" border="0" alt="" />
		</div>
	</form>
</body>
</html>