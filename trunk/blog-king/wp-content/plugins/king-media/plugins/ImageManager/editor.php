<?php
/**
 * The PHP Image Editor user interface.
 * @author $Author: Wei Zhuo $
 * @version $Id: editor.php 26 2004-03-31 02:35:21Z Wei Zhuo $
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('classes/imagemanager.php');
require_once('classes/imageeditor.php');

$manager = new ImageManager($IMConfig);
$editor = new ImageEditor($manager);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title></title>
	<link href="assets/editor.css" rel="stylesheet" type="text/css" />	
<script type="text/javascript" src="assets/slider.js"></script>
<script type="text/javascript" src="assets/popup.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
	window.resizeTo(673, 531);
/*]]>*/
</script>
<script type="text/javascript" src="assets/editor.js"></script>
</head>

<body>
<div id="indicator">
<img src="img/spacer.gif" id="indicator_image" height="20" width="20" alt="" />
</div>
<div id="tools">
	<div id="tools_crop" style="display:none;">
		<div id="tool_inputs">
			<label for="cx"><?php _e("Start X:","ImageManager"); ?></label><input type="text" id="cx"  class="textInput" onchange="updateMarker('crop')"/>
			<label for="cy"><?php _e("Start Y:","ImageManager"); ?></label><input type="text" id="cy" class="textInput" onchange="updateMarker('crop')"/>
			<label for="cw"><?php _e("Width:","ImageManager"); ?></label><input type="text" id="cw" class="textInput" onchange="updateMarker('crop')"/>
			<label for="ch"><?php _e("Height:","ImageManager"); ?></label><input type="text" id="ch" class="textInput" onchange="updateMarker('crop')"/>
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
		</div>	
		<a href="javascript: editor.doSubmit('crop');" class="buttons" title="<?php _e("OK","ImageManager"); ?>"><img src="img/btn_ok.gif" height="30" width="30" alt="<?php _e("OK","ImageManager"); ?>" /></a>
		<a href="javascript: editor.reset();" class="buttons" title="<?php _e("Cancel","ImageManager"); ?>"><img src="img/btn_cancel.gif" height="30" width="30" alt="<?php _e("Cancel","ImageManager"); ?>" /></a>		
	</div>	
	<div id="tools_scale" style="display:none;">
		<div id="tool_inputs">
			<label for="sw"><?php _e("Width:","ImageManager"); ?></label><input type="text" id="sw" class="textInput" onchange="checkConstrains('width')"/>
			<a href="javascript:toggleConstraints();" title="<?php _e("Lock","ImageManager"); ?>"><img src="img/islocked2.gif" id="scaleConstImg" height="14" width="8" alt="<?php _e("Lock","ImageManager"); ?>" class="div" /></a><label for="sh"><?php _e("Height:","ImageManager"); ?></label>
			<input type="text" id="sh" class="textInput" onchange="checkConstrains('height')"/>
			<input type="checkbox" id="constProp" value="1" checked="checked" onclick="toggleConstraints()"/>
			<label for="constProp"><?php _e("Constrain Proportions","ImageManager"); ?></label>
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
		</div>	
		<a href="javascript: editor.doSubmit('scale');" class="buttons" title="<?php _e("OK","ImageManager"); ?>"><img src="img/btn_ok.gif" height="30" width="30" alt="<?php _e("OK","ImageManager"); ?>" /></a>
		<a href="javascript: editor.reset();" class="buttons" title="<?php _e("Cancel","ImageManager"); ?>"><img src="img/btn_cancel.gif" height="30" width="30" alt="<?php _e("Cancel","ImageManager"); ?>" /></a>		
	</div>	
	<div id="tools_rotate" style="display:none;">
		<div id="tool_inputs">
			<select id="flip" name="flip" style="margin-left: 10px; vertical-align: middle;">
              <option selected><?php _e("Flip Image","ImageManager"); ?></option>
              <option>-----------------</option>
              <option value="hoz"><?php _e("Flip Horizontal","ImageManager"); ?></option>
              <option value="ver"><?php _e("Flip Virtical","ImageManager"); ?></option>
         </select>
			<select name="rotate" onchange="rotatePreset(this)" style="margin-left: 20px; vertical-align: middle;">
              <option selected><?php _e("Rotate Image","ImageManager"); ?></option>
              <option>-----------------</option>

              <option value="180"><?php _e("Rotate 180 &deg;","ImageManager"); ?></option>
              <option value="90"><?php _e("Rotate 90 &deg; CW","ImageManager"); ?></option>
              <option value="-90"><?php _e("Rotate 90 &deg; CCW","ImageManager"); ?></option>
         </select>
			<label for="ra"><?php _e("Angle:","ImageManager"); ?></label><input type="text" id="ra" class="textInput" />
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
		</div>	
		<a href="javascript: editor.doSubmit('rotate');" class="buttons" title="<?php _e("OK","ImageManager"); ?>"><img src="img/btn_ok.gif" height="30" width="30" alt="<?php _e("OK","ImageManager"); ?>" /></a>
		<a href="javascript: editor.reset();" class="buttons" title="<?php _e("Cancel","ImageManager"); ?>"><img src="img/btn_cancel.gif" height="30" width="30" alt="<?php _e("Cancel","ImageManager"); ?>" /></a>		
	</div>		
	<div id="tools_measure" style="display:none;">
		<div id="tool_inputs">
			<label><?php _e("X:","ImageManager"); ?></label><input type="text" class="measureStats" id="sx" />
			<label><?php _e("Y:","ImageManager"); ?></label><input type="text" class="measureStats" id="sy" />
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
			<label><?php _e("W:","ImageManager"); ?></label><input type="text" class="measureStats" id="mw" />
			<label><?php _e("H:","ImageManager"); ?></label><input type="text" class="measureStats" id="mh" />
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
			<label><?php _e("A:","ImageManager"); ?></label><input type="text" class="measureStats" id="ma" />		
			<label><?php _e("D:","ImageManager"); ?></label><input type="text" class="measureStats" id="md" />		
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
			<button type="button" onclick="editor.reset();" >Clear</button>
		</div>	
	</div>
	<div id="tools_save" style="display:none;">
		<div id="tool_inputs">
			<label for="save_filename"><?php _e("Filename:","ImageManager"); ?></label><input type="text" id="save_filename" value="<?php echo $editor->getDefaultSaveFile();?>"/>
			<select name="format" id="save_format" style="margin-left: 10px; vertical-align: middle;" onchange="updateFormat(this)">
            <option value="" selected><?php _e("Image Format","ImageManager"); ?></option>
            <option value="">---------------------</option>
            <option value="jpeg,85"><?php _e("JPEG High","ImageManager"); ?></option>
            <option value="jpeg,60"><?php _e("JPEG Medium","ImageManager"); ?></option>
            <option value="jpeg,35"><?php _e("JPEG Low","ImageManager"); ?></option>
            <option value="png"><?php _e("PNG","ImageManager"); ?></option>
			<?php if($editor->isGDGIFAble() != -1) { ?>
            <option value="gif"><?php _e("GIF","ImageManager"); ?></option>
			<?php } ?>
         </select>
			<label><?php _e("Quality:","ImageManager"); ?></label>
			<table style="display: inline; vertical-align: middle;" cellpadding="0" cellspacing="0">
				<tr>
				<td>
					<div id="slidercasing"> 
				<div id="slidertrack" style="width:100px"><img src="img/spacer.gif" width="1" height="1" border="0" alt="<?php _e("track","ImageManager"); ?>"></div>
            <div id="sliderbar" style="left:85px" onmousedown="captureStart();"><img src="img/spacer.gif" width="1" height="1" border="0" alt="<?php _e("track","ImageManager"); ?>"></div>
			</div>	
				</td>
				</tr>
			</table>				
			<input type="text" id="quality" onchange="updateSlider(this.value)" style="width: 2em;" value="85"/>
			<img src="img/div.gif" height="30" width="2" class="div" alt="|" />
		</div>	
		<a href="javascript: editor.doSubmit('save');" class="buttons" title="<?php _e("OK","ImageManager"); ?>"><img src="img/btn_ok.gif" height="30" width="30" alt="<?php _e("OK","ImageManager"); ?>" /></a>
		<a href="javascript: editor.reset();" class="buttons" title="<?php _e("Cancel","ImageManager"); ?>"><img src="img/btn_cancel.gif" height="30" width="30" alt="<?php _e("Cancel","ImageManager"); ?>" /></a>		
	</div>	
</div>
<div id="toolbar">
<a href="javascript:toggle('crop')" id="icon_crop" title="<?php _e("Crop","ImageManager"); ?>"><img src="img/crop.gif" height="20" width="20" alt="<?php _e("Crop","ImageManager"); ?>" /><!--span><?php _e("Crop","ImageManager"); ?></span--></a>
<a href="javascript:toggle('scale')" id="icon_scale" title="<?php _e("Resize","ImageManager"); ?>"><img src="img/scale.gif" height="20" width="20" alt="<?php _e("Resize","ImageManager"); ?>" /><!--span><?php _e("Resize","ImageManager"); ?></span--></a>
<a href="javascript:toggle('rotate')" id="icon_rotate" title="Rotate"><img src="img/rotate.gif" height="20" width="20" alt="<?php _e("Rotate","ImageManager"); ?>" /><!--span><?php _e("Rotate","ImageManager"); ?></span--></a>
<a href="javascript:toggle('measure')" id="icon_measure" title="Measure"><img src="img/measure.gif" height="20" width="20" alt="<?php _e("Measure","ImageManager"); ?>" /><!--span><?php _e("Measure","ImageManager"); ?></span--></a>
<a href="javascript: toggleMarker();" title="Marker"><img id="markerImg" src="img/t_black.gif" height="20" width="20" alt="<?php _e("Marker","ImageManager"); ?>" /><!--span><?php _e("Marker","ImageManager"); ?></span--></a>
<a href="javascript:toggle('save')" id="icon_save" title="Save"><img src="img/save.gif" height="20" width="20" alt="<?php _e("Save","ImageManager"); ?>" /><!--span><?php _e("Save","ImageManager"); ?></span--></a>
</div>
<div id="contents">
<iframe src="editorframe.php?img=<?php if(isset($_GET['img'])) echo rawurlencode($_GET['img']); ?>" name="editor" id="editor"  scrolling="auto" title="<?php _e("Image Editor","ImageManager"); ?>" frameborder="0"></iframe>
</div>
<div id="bottom"></div>
</body>
</html>
