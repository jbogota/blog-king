<?php define("KING_EDITPOST_VERSION", "0.1");?>
<?php
$messages[1] = __('Post updated');
$messages[2] = __('Custom field updated');
$messages[3] = __('Custom field deleted.');
?>
<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>
<form name="post" action="post.php" method="post" id="post">
<?php if ( (isset($mode) && 'bookmarklet' == $mode) || isset($_GET['popupurl']) ): ?>
<input type="hidden" name="mode" value="bookmarklet" />
<?php endif; ?>
<div class="wrap">
<h2 id="write-post"><?php _e('Write Post'); ?><?php if ( 0 != $post_ID ) : ?>
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
$form_pingback = '<input type="hidden" name="post_pingback" value="' . get_option('default_pingback_flag') . '" id="post_pingback" />';
$form_prevstatus = '<input type="hidden" name="prev_status" value="' . $post->post_status . '" />';
$saveasdraft = '<input name="save" type="submit" id="save" tabindex="3" value="' . __('Save and Continue Editing') . '" />';
if (empty($post->post_status)) $post->post_status = 'draft';
?>

<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value="<?php echo $form_action ?>" />
<input type="hidden" name="post_author" value="<?php echo $post->post_author ?>" />
<input type="hidden" name="post_type" value="post" />

<?php echo $form_extra ?>
<?php if (isset($_GET['message']) && 2 > $_GET['message']) : ?>
<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
addLoadEvent(focusit);
</script>
<?php endif; ?>
<div id="poststuff">
	<div id="moremeta">
		<div id="grabit" class="dbx-group">
		<?php
		king_posts_show_simple();
		king_posts_show_advanced();
		king_posts_show_sidecustom();
		?>
		</div>
	</div>
	<fieldset id="titlediv">
	  <legend><?php _e('Title') ?></legend>
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $post->post_title; ?>" id="title" /></div>
	</fieldset>
	<fieldset id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>">
		<legend><?php _e('Post') ?></legend>
	<?php
	 $rows = get_settings('default_post_edit_rows');
	 if (($rows < 3) || ($rows > 100)) {
	     $rows = 12;
	 }
	?>
	<?php the_quicktags(); ?>

		<textarea <?php if ( user_can_richedit() ) echo 'title="true" '; ?>rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="2" id="content"><?php echo user_can_richedit() ? wp_richedit_pre($post->post_content) : $post->post_content; ?></textarea>

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
<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>
<p class="submit"><?php echo $saveasdraft; ?> <input type="submit" name="submit" value="<?php _e('Save') ?>" style="font-weight: bold;" tabindex="4" /> 
<?php 
if ('publish' != $post->post_status || 0 == $post_ID) {
?>
<?php if ( current_user_can('publish_posts') ) : ?>
	<input name="publish" type="submit" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" /> 
<?php endif;
}
?>
<input name="referredby" type="hidden" id="referredby" value="<?php
if ( !empty($_REQUEST['popupurl']) )
	echo wp_specialchars($_REQUEST['popupurl']);
else if ( url_to_postid($_SERVER['HTTP_REFERER']) == $post_ID )
	echo 'redo';
else
	echo wp_specialchars($_SERVER['HTTP_REFERER']);
?>" /></p>
<?php
// needs a wrapper
king_posts_show_custom_old();
king_posts_show_upload();
?>
	<div id="advancedstuff" class="dbx-group" >
		<?php
		king_posts_show_excerpt();
		king_posts_show_trackback();
		king_posts_show_custom();
		king_posts_show_dbx_post();
		?>
	</div>
<?php if ('edit' == $action) : ?>
<input name="deletepost" class="button" type="submit" id="deletepost" tabindex="10" value="<?php _e('Delete this post') ?>" <?php echo "onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), addslashes($post->post_title) ) . "')\""; ?> />
<?php endif; ?>
</div>
</div>
</form>