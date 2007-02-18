<?php
/*
Plugin Name: King Pages
Plugin URI: http://www.blog.mediaprojekte.de
Description: Make editing Pages easier
Version: 0.2 
Author: George Leciejewski
Author URI: http://www.blog.mediaprojekte.de
*/
/*
ToDo
- Ornder auf 777 schalten vor dem dateizugriff
-check ob neue version ?nderungen hat
- optionsfelder noch weiter zusammenfassen?
- optionsfelder pro benutzergruppe freigeben
*/
if (eregi('king-pages.php', $_SERVER['PHP_SELF'])) die('Are you trying to trick me?');

require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');

plugintoolkit(
	$plugin='KingPages',
	array(
	'showSimple' => 'Show Simple Options Field{radio|yes|yes|no|no} ## Show the simple Options right of the Page Editing',
	'showParent' =>'Page Parent Dropdown-Select{radio|yes|yes|no|no} ##',
	'showSort' =>' Page Sort field{radio|yes|yes|no|no} ##',
	'showURL' =>'Page URL field{radio|yes|yes|no|no} ##',
	'AdvOpt' => 'Advanced Fields {placeholder} ##',
	'showAdvanced' =>'Show Advanced Options Field{radio|yes|yes|no|no} ## Show the Advanced Options right of the Page Editing',
	'showPass' =>'Password Field{radio|yes|yes|no|no} ##',
	'showTemplate' =>'Template Dropdown-Select{radio|yes|yes|no|no} ##',
	'showAuthor' =>'Author Dropdown-Select{radio|yes|yes|no|no} ##',
	'showDiscuss' =>'Discussion Fields{radio|yes|yes|no|no} ##',
	'showCustom' =>'Show Custom Options Fields{radio|yes|yes|no|no} ## Show the Custom Options beneath the Page Editing',
	'showUpload' =>'Show Upload Options Fields{radio|yes|yes|no|no} ## Show the Upload Options beneath the Page Editing',
//	'debug' => 'debug',
	'delete' => 'delete',
	),
	$file='king-pages.php',
	$menu=array(
	'parent' => 'options-general.php' ,
	'access_level' => 'activate_plugins',
	)
	/*
	temp killed copy function,
	$newFiles=array(
	'newCoreFile' => 'edit-page-form.php' ,
	'coreFolder' => 'wp-admin',
	'newFolder' => 'wp-content/plugins/king-admin/plugins/changed_core_files',
	)
	*/
);


function king_page_show_simple() {
	global $KingPages,$post;
	if($KingPages->option['showSimple']=="yes"){
?>
	
	<fieldset id="pageparent" class="dbx-box">
	<h3 class="dbx-handle">Seiten Eigenschaften</h3> 
	<div class="dbx-content">
	<?php //neu in version 3510 juhu ?>
	<p><?php _e('Page Status') ?><br /> 
	<?php if ( current_user_can('publish_posts') ) : ?>
	<label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post->post_status, 'publish'); ?> /> <?php _e('Published') ?></label>
	<?php endif; ?>
	<label for="post_status_draft" class="selectit">
	<input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post->post_status, 'draft'); ?> />
	<?php _e('Draft') ?></label>
	<label for="post_status_private" class="selectit">
	<input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post->post_status, 'private'); ?> /> 
	<?php _e('Private') ?></label>
	</p>

		<?php if($KingPages->option['showParent']=="yes"){?>
		
		<p><?php _e('Page Parent') ?>: <select name="parent_id">
		<option value='0'><?php _e('Main Page (no parent)'); ?></option>
		<?php parent_dropdown($post->post_parent); ?>
		</select>
		</p>
		
		<?php	}	 ?>		
		<?php if($KingPages->option['showSort']=="yes"){?>
		
		<p><?php _e('Page Order'); ?><br />
		<input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $post->menu_order ?>" /> </p>
		
		<?php	}	 ?>		
		<?php if($KingPages->option['showURL']=="yes"){?>
		
		<p>Seiten URL: 
		<input name="post_name" type="text" size="13" id="post_name" value="<?php echo $post->post_name ?>" />
		</p>
		<?php	}	 ?>
	</div>
	</fieldset>
<?php		
	}	
}

function king_page_show_advanced() {
	global $KingPages, $post;
	if($KingPages->option['showAdvanced']=="yes"){
?>
	<fieldset id="slugdiv" class="dbx-box">
			<h3 class="dbx-handle">Erweiterte Eigenschaften</h3> 
			<div class="dbx-content">
			
		<?php if($KingPages->option['showPass']=="yes"){?>
				<p><?php _e('Password-Protect Post') ?>
					<input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post->post_password ?>" />
				</p>				
		<?php	}	 ?>
			
		<?php if($KingPages->option['showTemplate']=="yes"){?>
				
				<?php if ( 0 != count( get_page_templates() ) ) { ?>
		
			<?php _e('Page Template:') ?>
			<p><select name="page_template">
					<option value='default'><?php _e('Default Template'); ?></option>
					<?php page_template_dropdown($post->page_template); ?>
					</select></p>
			
			<?php }
			 
			}	 ?>
		<?php if($KingPages->option['showAuthor']=="yes"){
			global $current_user; 
		
			if ( $authors = get_editable_authors( $current_user->id ) ) : // TODO: ROLE SYSTEM ?>
				
			<?php _e('Post author'); ?>:
			<p>
				<select name="post_author_override" id="post_author_override">
				<?php 
					foreach ($authors as $o) :
					$o = get_userdata( $o->ID );
						if ( $post->post_author == $o->ID || ( empty($post_ID) && $user_ID == $o->ID ) ) $selected = 'selected="selected"';
						else $selected = '';
						echo "<option value='$o->ID' $selected>$o->display_name</option>";
				endforeach;
				?>
				</select>
				<?php endif; ?>
			</p>
		<?php	}	 ?>
		<?php if($KingPages->option['showDiscuss']=="yes"){?>
			
			<p><?php _e('Discussion') ?></p>
			<input name="advanced_view" type="hidden" value="1" />
			
			<label for="comment_status" class="selectit">
			<input name="comment_status" type="checkbox" id="comment_status" value="closed" <?php checked($post->comment_status, 'open'); ?> />
			<?php _e('Allow Comments') ?></label>
			
			<label for="ping_status" class="selectit">
			<input name="ping_status" type="checkbox" id="ping_status" value="closed" <?php checked($post->ping_status, 'open'); ?> /> 
			<?php _e('Allow Pings') ?></label>
		<?php	}	 ?>	
		</div>
		</fieldset>
	
	
<?php		
	}
}

function king_page_show_custom() {
	global $KingPages,$post;
	if($KingPages->option['showCustom']=="yes"){
	?>
	
	<div id="advancedstuff" class="dbx-group">
	<fieldset id="postcustom" class="dbx-box">
	<h3 class="dbx-handle"><?php _e('Custom Fields') ?></h3>
		<div id="postcustomstuff" class="dbx-content">
		<?php 
		if($metadata = has_meta($post_ID)) {

			list_meta($metadata); 

		}
			meta_form();
		?>
		</div>
	</fieldset>
</div>
<?php	
	}
}
function king_page_show_upload() {
	global $KingPages,$post;
	if($KingPages->option['showUpload']=="yes"){
		
		if (current_user_can('upload_files')) {
			$uploading_iframe_ID = (0 == $post_ID ? $temp_ID : $post_ID);
			$uploading_iframe_src = "inline-uploading.php?action=view&amp;post=$uploading_iframe_ID";
			$uploading_iframe_src = apply_filters('uploading_iframe_src', $uploading_iframe_src);
			if ( false != $uploading_iframe_src )
			echo '<iframe id="uploading" border="0" src="' . $uploading_iframe_src . '">' . __('This feature requires iframe support.') . '</iframe>';
		}
	}
}

?>