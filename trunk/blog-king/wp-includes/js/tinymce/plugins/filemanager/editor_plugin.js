var TinyMCE_filemanager = {

    getInfo : function() {
		return {
			longname : 'King Filemanager',
			author : 'Georg Leciejewski',
			authorurl : 'http://www.blog.mediaprojekte.de',
			infourl : 'http://www.blog.mediaprojekte.de',
			version : "1.0"
		};
	},

	getControlHTML : function(control_name) {
			switch (control_name) {
				case "filemanager":
					return tinyMCE.getButtonHTML(control_name, 'lang_insert_filemanager', '{$pluginurl}/images/filemanager.gif', 'mceFileManager',true);
				/*	return '<img id="{$editor_id}_filemanager" src="{$pluginurl}/images/filemanager.gif" title="{$lang_insert_filemanager}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceFileManager\', true);">';*/
		}
		return "";
	},

	// Executes the mceFileManager command.
	execCommand : function(editor_id, element, command, user_interface, value) {

	    // Handle commands
		switch (command) {
			case "mceFileManager":
	            var template = new Array();
	                if (user_interface){

	                    template['file'] = '../../plugins/filemanager/InsertFile/insert_file.php'; // Relative to theme location
	                    template['width'] = 650;
	                    template['height'] = 450;

        				var href = "", src = "", alt = "", name = "", title="", size = "", date = "", action = "insert";

	                if (tinyMCE.selectedElement != null && tinyMCE.selectedElement.nodeName.toLowerCase() == "a") {
    					tinyMCE.linkElement = tinyMCE.selectedElement;
	                }
	                if (tinyMCE.selectedElement != null && tinyMCE.selectedElement.nodeName.toLowerCase() == "img") {
    					tinyMCE.imgElement = tinyMCE.selectedElement;
	                }

	                //Handle existing file link
	                if (tinyMCE.linkElement && tinyMCE.linkElement.getAttribute('name') == "file"){

	                    href = tinyMCE.linkElement.getAttribute('href');
	                    title = tinyMCE.linkElement.getAttribute('title');

	                    child = ( tinyMCE.linkElement.childNodes );
        				for ( var i = 0; i < child.length; i++ ) {
        					if ( child[i].tagName == 'IMG' ) {
        						src = child[i].src;
	                        }
	                        if ( child[i].tagName == 'SPAN' ) {
	                            size_date = child[i].id;
	                            size_date_arr = size_date.split(',');
	                            size = size_date_arr[0];
	                            date = size_date_arr[1];
	                        }
	                    }
	                    action = "update";
	           }
	        }else{
	            if (tinyMCE.linkElement)
	            {
	                href = value['href'];
	                caption = value['caption'];
	                a_title = value['a_title'];

	                i_src = value['i_src'];
	                i_alt = value['i_alt'];
	                f_date = value['f_date'];
	                f_size = value['f_size'];

	                icon = value['icon'];
	                date = value['date'];
	                size = value['size'];

	                var size_date_str = '';
	                    if (size && !date){
	                        size_date_str += ''+f_size+',null';
	                    }
	                    if (size && date){
	                        size_date_str += ''+f_size+','+f_date+'';
	                    }
	                    if (date && !size){
	                        size_date_str += 'null,'+f_date+'';
	                    }

	                html = '';
	                html += '<a href="'+href+'" title="'+a_title+'">';
	                if (icon == true){
	                    html += '<img src="' +i_src+ '" border="0" alt="'+i_alt+'" />&nbsp;';
	                }
	                html +=''+caption+'';
	                if (date || size){
	                    html += '&nbsp;(<span style="font-size:80%" id="'+size_date_str+'">';
	                }
	                if (size){
	                  html += ''+f_size+'';
	                }
	                if (date && size){
	                  html+= '&nbsp;';
	                }
	                if (date){
	                  html += ''+f_date+'';
	                }
	                if (date || size){
	                    html += ')</span>';
	                }
	                html += '</a>';

	                tinyMCE.execCommand("mceReplaceContent",false,html);
	            }

	            if (!tinyMCE.linkElement)
	            {
	                href = value['href'];
	                caption = value['caption'];
	                a_title = value['a_title'];

	                i_src = value['i_src'];
	                i_alt = value['i_alt'];
	                f_date = value['f_date'];
	                f_size = value['f_size'];

	                icon = value['icon'];
	                date = value['date'];
	                size = value['size'];

	                var size_date_str = '';
	                    if (size && !date){
	                        size_date_str += ''+f_size+',null';
	                    }
	                    if (size && date){
	                        size_date_str += ''+f_size+','+f_date+'';
	                    }
	                    if (date && !size){
	                        size_date_str += 'null,'+f_date+'';
	                    }

	                html = '';
	                html += '<a href="'+href+'" title="'+a_title+'">';
	                if (icon == true){
	                    html += '<img src="' +i_src+ '" border="0" alt="'+i_alt+'" />&nbsp;';
	                }
	                html +=''+caption+'';
	                if (date || size){
	                    html += '&nbsp;(<span style="font-size:80%" id="'+size_date_str +'">';
	                }
	                if (size){
	                  html += ''+f_size+'';
	                }
	                if (date && size){
	                  html+= '&nbsp;';
	                }
	                if (date){
	                  html += ''+f_date+'';
	                }
	                if (date || size){
	                    html += ')</span>';
	                }
	                html += '</a>';

	                tinyMCE.execCommand("mceInsertContent",false,html);
	            }
	          return true;
	        }
	        tinyMCE.openWindow(template, {editor_id : editor_id, href : href, src : src, alt : alt, name : name, title : title, size : size, date : date, action : action});
	        return true;
	    }
	   // Pass to next handler in chain
	   return false;
	}
};
// Adds the plugin class to the list of available TinyMCE plugins
tinyMCE.addPlugin("filemanager", TinyMCE_filemanager);
// Import theme specific language pack
tinyMCE.importPluginLanguagePack('filemanager', 'en,de,pl');