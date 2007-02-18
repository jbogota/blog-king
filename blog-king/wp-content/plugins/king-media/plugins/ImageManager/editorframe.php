<?php

/**
 * The frame that contains the image to be edited.
 * @author $Author: Wei Zhuo $
 * @version $Id: editorFrame.php 26 2004-03-31 02:35:21Z Wei Zhuo $
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('classes/imagemanager.php');
require_once('classes/imageeditor.php');

$manager = new ImageManager($IMConfig);
$editor = new ImageEditor($manager);
$imageInfo = $editor->processImage();

?>

<html>
<head>
	<title></title>
<link href="assets/editorframe.css" rel="stylesheet" type="text/css" />	
<script type="text/javascript" src="assets/wz_jsgraphics.js"></script>
<script type="text/javascript" src="assets/editorcontent.js"></script>
<script type="text/javascript">
	
var mode = "<?php echo $editor->getAction(); ?>" //crop, scale, measure

var currentImageFile = "<?php if(count($imageInfo)>0) echo rawurlencode($imageInfo['file']); ?>";

<?php if ($editor->isFileSaved() == 1) { ?>
	alert('<?php _e("File saved.","ImageManager"); ?>');
<?php } else if ($editor->isFileSaved() == -1) { ?>
	alert('<?php _e("File was not saved","ImageManager"); ?>');
<?php } ?>

</script>
<script type="text/javascript">
	 <?php require	"assets/editorframe_js.php"; ?>
</script>

</head>

<body>
<div id="status"></div>
<div id="ant" class="selection" style="visibility:hidden"><img src="img/spacer.gif" width="0" height="0" border="0" alt="" id="cropContent"></div>
<?php if ($editor->isGDEditable() == -1) { ?>
	<div style="text-align:center; padding:10px;"><span class="error"><?php _e("GIF format is not supported, image editing not supported.","ImageManager"); ?></span></div>
<?php } ?>
<table height="100%" width="100%">
	<tr>
		<td>
<?php if(count($imageInfo) > 0 && is_file($imageInfo['fullpath'])) { ?>
	<span id="imgCanvas" class="crop"><img src="<?php echo $imageInfo['src']; ?>" <?php echo $imageInfo['dimensions']; ?> alt="" id="theImage" name="theImage"></span>
<?php } else { ?>
	<span class="error"><?php _e("No Image Available","ImageManager"); ?></span>
<?php } ?>
		</td>
	</tr>
</table>
</body>
</html>
