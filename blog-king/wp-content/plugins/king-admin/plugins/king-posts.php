<?php
/*
Plugin Name: King Posts
Plugin URI: http://www.blog.mediaprojekte.de
Description: Make editing Post Page Cleaner
Version: 0.1
Author: George Leciejewski
Author URI: http://www.blog.mediaprojekte.de
*/
/*
ToDo
- trackback formfield wird nicht angezeigt
- Kategorien feld f?r neue kat. als option
- kat liste k?rzer
- die felder unter dem post mit reinnehmen
- checkboxen f?r die details bauen
- titelform inputfield hat keinen rahmen ? css problem
- was soll in welche  Einstellungen?
- brauchen wir noch expliziete rechte f?r einige felder?

*/
require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');

plugintoolkit('KingPosts',
	array(
	'showSimple' => 'Show Simple Sidebar{radio|yes|yes|no|no} ## Show the simple Options right of the Page Editing',
	'showCategory' => 'Category Selects{radio|yes|yes|no|no}## Show the Category Select Box',
	'showAjaxCat' => 'Ajax Category Box{radio|yes|yes|no|no} ## Show Ajax Category Box, to add new Categories',
	'showURL' => ' URL field{radio|yes|yes|no|no}## Show the URL field for page URL',
	'showDiscuss' => 'The Discuss Options{radio|yes|yes|no|no}## Comments Options',
	'showStatus' => 'Status Fields{radio|yes|yes|no|no}## Post Status fields',
	'spacer12' => '{placeholder} ##',
	'showAdvanced' =>'Show Advanced Sidebar {radio|yes|yes|no|no} ## Show the Advanced Options right of the Page Editing',
	'showPass' => 'Password Field{radio|yes|yes|no|no}## Show the Password Field',
	'showAuthors' => 'Authors Dropdown-Select{radio|yes|yes|no|no}## Show the Authors Dropdown-Select',
	'showTime' => 'Timestamp{radio|yes|yes|no|no}## Show Timestamp Selector',
	'showStatus' => 'Status Fields{radio|yes|yes|no|no}## Post Status fields',
	'showCustomSide' =>' Custom Options Fields{radio|yes|yes|no|no} ## Show the Custom sidebar 3rd party Components',
	'spacer13' => '{placeholder} ##',
	'showUpload' =>'Upload Field {radio|yes|yes|no|no} ## Show the Upload Options beneath the TextArea',
	'showExcerpt' =>'Excerpt Field {radio|yes|yes|no|no} ## Show the Excerpt beneath the TextArea',
	'showTrackback' =>'Trackback Field {radio|yes|yes|no|no} ## Show the Trackback beneath the TextArea',
	'showCustom' =>'Show Custom Options Fields{radio|yes|yes|no|no} ## Show the Custom Options beneath the TextArea',
	'dbx_post_advanced' =>'Advanced Options Fields{radio|yes|yes|no|no} ## Show the Advanced Options beneath the TextArea',
	'post_custom_old' =>'Old Custom Options Fields{radio|yes|yes|no|no} ## Show the Old Custom Options drektly beneath the TextArea. Non draggable',
	'delete' => 'delete',
	),
	'king-posts.php',
	array(
	'parent' => 'options-general.php' ,
	'access_level' => 'activate_plugins',
	),
	array(
	'newCoreFile' => 'edit-form-advanced.php' ,
	'coreFolder' => 'wp-admin',
	'newFolder' => 'wp-content/plugins/king-plugins/king-admin/core_files',
	)
);


function king_posts_show_simple() {
	global $KingPosts,$post, $current_user;
	if($KingPosts->option['showSimple']=="yes"){
?>
		<fieldset id="categorydiv" class="dbx-box">
			<h3 class="dbx-handle">Einstellungen</h3>
			<div class="dbx-content">
<?php if($KingPosts->option['showCategory']=="yes"){?>
				<?php _e('Categories') ?>
	<?php if($KingPosts->option['showAjaxCat']=="yes"){?>
				<p id="jaxcat"></p>
	<?php }?>
				<div id="categorychecklist"><?php dropdown_categories(get_settings('default_category')); ?></div>
<?php }  if($KingPosts->option['showDiscuss']=="yes"){?>
				 <p>
				 <?php _e('Discussion') ?>
				<input name="advanced_view" type="hidden" value="1" />
				<label for="comment_status" class="selectit">
				<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> />
				<?php _e('Allow Comments') ?></label>
				<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label>
				</p>
<?php } if($KingPosts->option['showStatus']=="yes"){?>
				<p>
				<?php _e('Post Status') ?>
				<?php if ( current_user_can('publish_posts') ) : ?>
				<label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post->post_status, 'publish'); ?> /> <?php _e('Published') ?></label>
				<?php endif; ?>
				<label for="post_status_draft" class="selectit"><input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post->post_status, 'draft'); ?> /> <?php _e('Draft') ?></label>
				<label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post->post_status, 'private'); ?> /> <?php _e('Private') ?></label>
				</p>
<?php } ?>
			</div>
		</fieldset>
<?php
	}
}

function king_posts_show_advanced() {
	global $KingPosts,$post, $current_user,$user_ID;
	if($KingPosts->option['showAdvanced']=="yes"){
?>
		<fieldset class="dbx-box">
		<h3 class="dbx-handle">Erweiterte Einstellungen</h3>
			<div class="dbx-content">
<?php if($KingPosts->option['showURL']=="yes"){?>
				<p>
				 <?php _e('Post slug') ?>
				<input name="post_name" type="text" size="20" id="post_name" value="<?php echo $post->post_name ?>" />
				</p>
<?php }
 if($KingPosts->option['showPass']=="yes"){?>
				<p>
				<?php _e('Password-Protect Post') ?>
				<input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post->post_password ?>" />
				</p>
<?php } if($KingPosts->option['showAuthors']=="yes"){?>
				<p>
				<?php _e('Post author'); ?>:
				<?php if ( $authors = get_editable_authors( $current_user->id ) ) : // TODO: ROLE SYSTEM ?>
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
<?php } if($KingPosts->option['showTime']=="yes"){?>
				<p>
				<?php if ( current_user_can('edit_posts') ) : ?>
				<?php _e('Post Timestamp');
					touch_time(($action == 'edit'));
					endif; ?>
				</p>
<?php } ?>
			</div>
		</fieldset>
<?php
	}//end if
}
function king_posts_show_sidecustom() {
	global $KingPosts;
	if($KingPosts->option['showCustomSide']=="yes"){
		do_action('dbx_post_sidebar');
	}
}

function king_posts_show_upload() {
	global $KingPosts,$post;
	if($KingPosts->option['showUpload']=="yes"){
		if (current_user_can('upload_files')) {
			$uploading_iframe_ID = (0 == $post_ID ? $temp_ID : $post_ID);
			$uploading_iframe_src = "inline-uploading.php?action=view&amp;post=$uploading_iframe_ID";
			$uploading_iframe_src = apply_filters('uploading_iframe_src', $uploading_iframe_src);
			if ( false != $uploading_iframe_src )
				echo '<iframe id="uploading" border="0" src="' . $uploading_iframe_src . '">' . __('This feature requires iframe support.') . '</iframe>';
		}
	}
}
function king_posts_show_excerpt() {
	global $KingPosts,$post;
	if($KingPosts->option['showExcerpt']=="yes"){?>
		<fieldset id="postexcerpt" class="dbx-box">
		<h3 class="dbx-handle"><?php _e('Optional Excerpt') ?></h3>
			<div class="dbx-content"><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt ?></textarea></div>
		</fieldset>
<?php	}
}
function king_posts_show_trackback() {
	global $KingPosts,$post;
	if($KingPosts->option['showTrackback']=="yes"){
		$form_trackback = '<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" value="'. str_replace("\n", ' ', $post->to_ping) .'" />';
		if ('' != $post->pinged) {
			$pings = '<p>'. __('Already pinged:') . '</p><ul>';
			$already_pinged = explode("\n", trim($post->pinged));
			foreach ($already_pinged as $pinged_url) {
				$pings .= "\n\t<li>$pinged_url</li>";
			}
		$pings .= '</ul>';
		}
		?>
		<fieldset class="dbx-box">
		<h3 class="dbx-handle"><?php _e('Trackbacks') ?></h3>
			<div class="dbx-content"><?php _e('Send trackbacks to'); ?>: <?php echo $form_trackback; ?> (<?php _e('Separate multiple URIs with spaces'); ?>)
			<?php
			if ( ! empty($pings) )
			echo $pings;
			?>
			</div>
		</fieldset><?php
	}
}
function king_posts_show_custom() {
	global $KingPosts,$post;
	if($KingPosts->option['showCustom']=="yes"){
	?>
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
<?php
	}
}
function king_posts_show_dbx_post() {
	global $KingPosts,$post;
	if($KingPosts->option['dbx_post_advanced']=="yes"){
   		do_action('dbx_post_advanced');
	}
}
function king_posts_show_bookmark() {
	global $KingPosts,$post;
	if($KingPosts->option['post_bookmarklet']=="yes"){

	}
}
function king_posts_show_custom_old() {
	global $KingPosts,$post;
	if($KingPosts->option['post_custom_old']=="yes"){
		do_action('edit_form_advanced');
	}
}
?>