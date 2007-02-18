<?php
require_once('config.inc.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php _e("New Folder","ImageManager")?></title>
 <style type="text/css">
 /*<![CDATA[*/
 html, body {  background-color: ButtonFace;  color: ButtonText; font: 11px Tahoma,Verdana,sans-serif; margin: 0; padding: 0;}
body { padding: 5px; }
 .title { background-color: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px; border-bottom: 1px  solid black; letter-spacing: 2px;}
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
.buttons { width: 70px; text-align: center; }
form { padding: 0px;  margin: 0;}
form .elements{
	padding: 10px; text-align: center;
}
 /*]]>*/
 </style>
<script type="text/javascript" src="assets/popup.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
	//window.resizeTo(300, 170);

function getRefToDivMod( divID, oDoc ) {
  if( !oDoc ) { oDoc = document; }
  if( document.layers ) {
    if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
      for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
        y = getRefToDivMod(divID,oDoc.layers[x].document); }
      return y; } }
  if( document.getElementById ) { return oDoc.getElementById(divID); }
  if( document.all ) { return oDoc.all[divID]; }
  return document[divID];
}	

function resizeWinTo( idOfDiv ) {	
  var oH = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
  var oW = oH.clip ? oH.clip.width : oH.offsetWidth;
  var oH = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
  var x = window; x.resizeTo( oW + 200, oH + 200 );
  var myW = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
  if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
  else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
  else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
  if( window.opera && !document.childNodes ) { myW += 16; }
  x.resizeTo( oW + ( ( oW + 200 ) - myW ), oH + ( (oH + 220 ) - myH ) );
  var scW = screen.availWidth ? screen.availWidth : screen.width;
  var scH = screen.availHeight ? screen.availHeight : screen.height;
  x.moveTo(Math.round((scW-oW)/2),Math.round((scH-oH)/2)); 
}	
	init = function () 
	{
		resizeWinTo('newfpop');
		__dlg_init();
		document.getElementById("f_foldername").focus();
	}

	function onCancel() 
	{
		__dlg_close(null);
		return false;
	}

	function onOK() 
	{
		 // pass data back to the calling window
	  var fields = ["f_foldername"];
	  var param = new Object();
	  for (var i in fields) {
		var id = fields[i];
		var el = document.getElementById(id);
		param[id] = el.value;
	  }
	  __dlg_close(param);
	  return false;
	}

	function addEvent(obj, evType, fn)
	{ 
		if (obj.addEventListener) { obj.addEventListener(evType, fn, true); return true; } 
		else if (obj.attachEvent) {  var r = obj.attachEvent("on"+evType, fn);  return r;  } 
		else {  return false; } 
	} 
	
	addEvent(window, 'load', init);
//-->
</script>
</head>
<body >
<div id="newfpop" style="position:absolute;left:0;top:0;margin:0;padding:0;">
<div class="title"><?php _e("New Folder","ImageManager")?></div>
<form action="">
<div class="elements">
	<label for="f_foldername"><?php _e("Folder Name:","ImageManager")?></label>
	<input type="text" id="f_foldername" size="40"/>
</div>
<div style="text-align: right;"> 
	  <hr />
	  <button type="button" class="buttons" onclick="return onOK();"><?php _e("OK","ImageManager")?></button>
	  <button type="button" class="buttons" onclick="return onCancel();"><?php _e("Cancel","ImageManager")?></button>
</div>
</div>
</form>
</body>
</html>