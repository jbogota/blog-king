<?php
/*
Plugin Name: King_Search
Plugin URI: http://www.blog.mediaprojekte.de
Description: Adds option to search pages, attachments, drafts, comments and custom fields (metadata).
Version: 0.5
Author: Georg Leciejewski
Author URI: http://www.blog.mediaprojekte.de
*/

/*
	Rewritten admin interface and Options parts by Georg Leciejewski
	Big Portions © 2005-06, Daniel Cameron  (email : dancameron@gmail.com)
	Portions Copyright © 2006, Jan (email: jan at geheimwerk dot de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



add_action('admin_menu', 'king_search_adminmenu');

/**
*@desc log querys to see what is happening. needs to be enabeld by setting $logging to 1
*/

function king_search_log($msg) {
	$logging = 0; //log stuff to see what is happening
	if ($logging)
	{
		$fp = fopen("logfile.log","a+");
		$date = date("Y-m-d H:i:s ");
		$source = "search_everything_2 plugin: ";
		fwrite($fp, "\n\n".$date."\n".$source."\n".$msg);
		fclose($fp);
	}
	return true;
	}



/**
*@desc add filters based upon option settings
*/
$king_search_options = get_option('king_search');

if ($king_search_options['search_pages'])
{

	add_filter('posts_where', 'king_search_search_pages');
	king_search_log("searching pages");
}

if ($king_search_options['search_comments'])
{
	add_filter('posts_where', 'king_search_search_comments');
	add_filter('posts_join', 'king_search_comments_join');
	king_search_log("searching comments");
}

if ($king_search_options['search_drafts'])
{
	add_filter('posts_where', 'king_search_search_draft_posts');
	king_search_log("searching drafts");
}

if ($king_search_options['search_attachments'])
{
	add_filter('posts_where', 'king_search_search_attachments');
	king_search_log("searching attachments");
}

if ($king_search_options['search_metadata'])
{
	add_filter('posts_where', 'king_search_search_metadata');
	add_filter('posts_join', 'king_search_search_metadata_join');
	king_search_log("searching metadata");
}


/**
*@desc add pages to search query
*/
function king_search_search_pages($where)
{
	global $wp_query;
	if (!empty($wp_query->query_vars['s']))
	{
		$where = str_replace(' AND (post_status = "publish"', ' AND (post_status = "publish" or post_status = "static"', $where);
	}

	king_search_log("pages where: ".$where);
	return $where;
}

/**
*@desc add drafts to search query
*/
function king_search_search_draft_posts($where)
{
	global $wp_query;
	if (!empty($wp_query->query_vars['s']))
	{
		$where = str_replace(' AND (post_status = "publish"', ' AND (post_status = "publish" or post_status = "draft"', $where);
	}

	king_search_log("drafts where: ".$where);
	return $where;
}

/**
*@desc add atachments to search query
*/
function king_search_search_attachments($where)
{
	global $wp_query;
	if (!empty($wp_query->query_vars['s']))
	{
		$where = str_replace(' AND (post_status = "publish"', ' AND (post_status = "publish" or post_status = "attachment"', $where);
		$where = str_replace('AND post_status != "attachment"','',$where);
	}
	king_search_log("attachments where: ".$where);
	return $where;
}

/**
*@desc add comments to search query
*/
function king_search_search_comments($where)
{
	global $wp_query;
	if (!empty($wp_query->query_vars['s'])) {
		$where .= " OR (comment_content LIKE '%" . $wp_query->query_vars['s'] . "%') ";
	}
	king_search_log("comments where: ".$where);
	return $where;
}

//
/**
*@desc add join for searching comments  to search query
*/
function king_search_comments_join($join)
{
	global $wp_query, $wpdb;
	$options = get_option('king_search');
	if (!empty($wp_query->query_vars['s']))
	{
		if ($option['appvd_comments'])
		{
			$comment_approved = " AND comment_approved =  '1'";
  		}
  		else
  		{
			$comment_approved = '';
    	}

		$join .= "LEFT JOIN $wpdb->comments ON ( comment_post_ID = ID " . $comment_approved . ") ";
	}
	king_search_log("comments join: ".$join);
	return $join;
}

/**
*@desc add metadata search to query
*/
function king_search_search_metadata($where)
{
	global $wp_query;
	if (!empty($wp_query->query_vars['s']))
	{
		$where .= " OR meta_value LIKE '%" . $wp_query->query_vars['s'] . "%' ";
	}
	king_search_log("metadata where: ".$where);
	return $where;
}

/**
*@desc add join for searching metadata to query
*/
function king_search_search_metadata_join($join)
{
	global $wp_query, $wpdb;
	if (!empty($wp_query->query_vars['s']))
	{
		$join .= "LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
	}
	king_search_log("metadata join: ".$join);
	return $join;
}


/**
*@desc the Admin interface
*/
function king_search_admin_options()
{

	include_once (ABSPATH . 'wp-content/plugins/king-includes/library/form.php');

	if ( isset($_POST['king_search_options_save']) )
	{
		//form is submitted

		$newoptions['search_pages']			= isset($_POST["search_pages"]);
		$newoptions['search_comments']		= isset($_POST["search_comments"]);
		$newoptions['appvd_comments']		= isset($_POST["appvd_comments"]);
		$newoptions['search_drafts']		= isset($_POST["search_drafts"]);
		$newoptions['search_attachments']	= isset($_POST["search_attachments"]);
		$newoptions['search_metadata']		= isset($_POST["search_metadata"]);
		//save options
		update_option('king_search', $newoptions);
		echo '<div id="message" class="updated fade"><p>Options updated!</p></div>';

	}

	$options 			= get_option('king_search');
	$search_pages		= $options['search_pages']? 'checked="checked"' : '';
	$search_comments	= $options['search_comments']? 'checked="checked"' : '';
	$appvd_comments		= $options['appvd_comments']? 'checked="checked"' : '';
	$search_drafts		= $options['search_drafts']? 'checked="checked"' : '';
	$search_attachments	= $options['search_attachments']? 'checked="checked"' : '';
	$search_metadata	= $options['search_metadata']? 'checked="checked"' : '';
	$search_pages		= $options['search_pages']? 'checked="checked"' : '';

?>

	<div class="wrap">
		<h2><?php _e('King Search Options','kingplugin') ?> </h2>
		<form method="post" action="">

			<legend><?php _e('Define Search Options','kingplugin') ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
					<th width="33%" scope="row"><?php _e('Search in pages','kingplugin') ?></th>
					<td>
					<?php echo king_get_checkbox('search_pages',$search_pages); ?>
		          </td>
				</tr>
	            <tr valign="top">
					<th width="33%" scope="row"><?php _e('Search in all Comments:','kingplugin') ?></th>
					<td>
						<?php echo king_get_checkbox('search_comments',$search_comments); ?>
		            </td>
				</tr>
				<tr valign="top">
					<th width="33%" scope="row"><?php _e('Search only in approved Comments?','kingplugin') ?></th>
					<td>
						<?php echo king_get_checkbox('appvd_comments',$appvd_comments); ?>
		            </td>
				</tr>
	            <tr valign="top">
					<th width="33%" scope="row"><?php _e('Search in Drafts:','kingplugin') ?></th>
					<td>
						<?php echo king_get_checkbox('search_drafts',$search_drafts); ?>
		            </td>
				</tr>
				<tr valign="top">
					<th width="33%" scope="row"><?php _e('Search in Attachments','kingplugin') ?></th>
					<td>
						<?php echo king_get_checkbox('search_attachments',$search_attachments); ?>
		            </td>
				</tr>            <tr valign="top">
					<th width="33%" scope="row"><?php _e('Search in Custom Fields (Metadata)','kingplugin') ?></th>
					<td>
						<?php echo king_get_checkbox('search_metadata',$search_metadata); ?>
		            </td>
				</tr>

			</table>
			<p class="submit"><input type="submit" name="king_search_options_save" value="Save" /></p>
			</fieldset>
		</form>
	</div>

<?php
}
/**
*@desc admin menu hook
*/
function king_search_adminmenu()
{
	add_options_page('King Search', 'King Search','activate_plugins', 'king_search.php', 'king_search_admin_options');
}

?>
