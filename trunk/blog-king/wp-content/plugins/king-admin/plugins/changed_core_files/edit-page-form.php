<?php define("KING_EDITPAGE_VERSION", "0.1");?>
<div class="wrap">
<h2 id="write-post"><?php _e('Write Page'); ?><?php if ( 0 != $post_ID ) : ?>
<small class="quickjump"><a href="#preview-post"><?php _e('preview &darr;'); ?></a></small><?php endif; ?></h2>
<?php
if (0 == $post_ID) {
	$form_action = 'post';
	$temp_ID = -1 * time();
	$form_extra = "<input type='hidden' name='temp_ID' value='$temp_ID' />";
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' name='post_ID' value='$post_ID' />";
}

$sendto = $_SERVER['HTTP_REFERER'];

if ( 0 != $post_ID && $sendto == get_permalink($post_ID) )
 	$sendto = 'redo';
$sendto = wp_specialchars( $sendto );

?>

<form name="post" action="post.php" method="post" id="post">
<?php
if (isset($mode) && 'bookmarklet' == $mode) {
    echo '<input type="hidden" name="mode" value="bookmarklet" />';
}
?>
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action ?>' />
<?php echo $form_extra ?>
<input type="hidden" name="post_type" value="page" />

<script type="text/javascript">
<!--
function focusit() { // focus on first input field
	document.post.title.focus();
}
addLoadEvent(focusit);
//-->
</script>
<div id="poststuff">

<div id="moremeta">

	<div id="grabit" class="dbx-group">
 
<?php king_page_show_simple(); ?>
<?php king_page_show_advanced(); ?>

	</div>
</div>

<fieldset id="titlediv">
  <legend><?php _e('Page Title') ?></legend> 
  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $post->post_title; ?>" id="title" /></div>
</fieldset>
<fieldset id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>">
    <legend><?php _e('Page Content') ?></legend>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<?php the_quicktags(); ?>
<div><textarea title="true" rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content">
<?php echo user_can_richedit() ? wp_richedit_pre($post->post_content) : $post->post_content; ?></textarea></div>
</fieldset>
<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
<?php if ( user_can_richedit() ) : ?>
// This code is meant to allow tabbing from Title to Post (TinyMCE).
if ( tinyMCE.isMSIE )
	document.getElementById('title').onkeydown = function (e)
		{
			e = e ? e : window.event;
			if (e.keyCode == 9 && !e.shiftKey && !e.controlKey && !e.altKey) {
				var i = tinyMCE.selectedInstance;
				if(typeof i ==  'undefined')
					return true;
                                tinyMCE.execCommand("mceStartTyping");
				this.blur();
				i.contentWindow.focus();
				e.returnValue = false;
				return false;
			}
		}
else
	document.getElementById('title').onkeypress = function (e)
		{
			e = e ? e : window.event;
			if (e.keyCode == 9 && !e.shiftKey && !e.controlKey && !e.altKey) {
				var i = tinyMCE.selectedInstance;
				if(typeof i ==  'undefined')
					return true;
                                tinyMCE.execCommand("mceStartTyping");
				this.blur();
				i.contentWindow.focus();
				e.returnValue = false;
				return false;
			}
		}
<?php endif; ?>
//-->
</script>
<p class="submit">
<?php if ( $post_ID ) : ?>
<input name="save" type="submit" id="save" tabindex="5" value=" <?php _e('Save and Continue Editing'); ?> "/> 
<input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php $post_ID ? _e('Save') : _e('Create New Page') ?> &raquo;" /> 
<?php else : ?>
<input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php _e('Create New Page') ?> &raquo;" /> 
<?php endif; ?>
<input name="referredby" type="hidden" id="referredby" value="<?php echo $sendto; ?>" />
</p>

<?php
king_page_show_upload();
king_page_show_custom();
?>
<?php if ('edit' == $action) : ?>
		<input name="deletepost" class="delete" type="submit" id="deletepost" tabindex="10" value="<?php _e('Delete this page') ?>" <?php echo "onclick=\"return confirm('" . sprintf(__("You are about to delete this page \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $wpdb->escape($post->post_title) ) . "')\""; ?> />
<?php endif; ?>

<?php do_action('edit_page_form', ''); ?>
</form>
</div>
</div>