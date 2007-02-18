<?php
/*
Plugin Name: King Post Restrictions
Plugin URI: http://website-king.de
Description: Show private posts to other users if at least one of the user's capabilities meets one of the post's capabilities. original Version by <a href="http://soeren-weber.net/post/2006/01/01/121/" target="_blank">S&ouml;ren Weber</a> <br>
Enhacements: - Select where the restriction Box is shown , -select which Capabilities should be shown - show set restriction in list - killed migrations options
Version: 1.3
Author: Georg Leciejewski
Author URI: http://website-king.de

This work is derived from Filipe Fortes Post Levels plugin (http://fortes.com/)

Known Issues:
 - Private and protected posts don't show up in the next/previous post links
 - Until Ticket #2183 isn't fixed, the template tags get_post_restrictions
     and get_post_restrictions_list won't work as expected

 ToDo:
 - kill the global $g_postrestrictions_cap_names don´t know why we would need it since all
 allowed are listet in the options noch an zwei stellen da
 - maybe rebuild with plugintoolkit for the backoffice options .. might be a problem
 - insert new cap like in rolemanager ?
 - put the box on the edit pages site
*/
if (eregi('king-restrictions.php', $_SERVER['PHP_SELF'])) die('Are you trying to trick me?');
define('POSTRESTRICTIONS_VERSION', '1.3');
define('POSTRESTRICTIONS_PLUGIN_ID', 'post_restrictions');
define('POSTRESTRICTIONS_META_KEY', 'post_restrictions');

define('POSTRESTRICTIONS_POSTLEVELS_POST_META_KEY', 'post_level');
define('POSTRESTRICTIONS_POSTLEVELS_POST_META_KEY_OPTION', 'postlevels_post_key');
define('POSTRESTRICTIONS_POSTLEVELS_USER_META_KEY_OPTION', 'postlevels_user_key');
define('POSTRESTRICTIONS_POSTLEVELS_ADMIN_ROLE', 'post_level_admin');


function postrestrictions_enforce_constraints()
{
	// just do some clean up to be sure our metas are consistent;
	// after migration is is mandatory but we do it here anyway
	global $wpdb;

	// selecting duplicates with equal value
	$query = "
		SELECT `post_id`, `meta_value`, count(*) AS `count`
		FROM `{$wpdb->postmeta}`
		WHERE `meta_key` = '". POSTRESTRICTIONS_META_KEY. "'
		GROUP BY `post_id`, `meta_value`
		HAVING `count` > 1
	";
	$s = $wpdb->get_results($query);
	if (!is_array($s))
		$s = array();

	// deleting duplicates with equal value
	foreach($s as $i)
	{
		$query = "
			DELETE
			FROM `{$wpdb->postmeta}`
			WHERE `meta_key` = '". POSTRESTRICTIONS_META_KEY. "'
			AND `post_id` = {$i->post_id}
			AND `meta_value` = '{$i->meta_value}'
			LIMIT ". strval($i->count - 1). "
		";
		$wpdb->query($query);
	}
}

// ----------------------------------------------------------------------------
// admin filter
// ----------------------------------------------------------------------------

// Post Restrictions configuration page
function postrestrictions_config()
{
	global $wpdb,$wp_roles;
	global $g_postrestrictions_options;
    // new by georg leciejewski get all names again ... it´s double right now but i think the global call is not so good and might not be needed
    foreach($wp_roles->role_objects as $key => $role) {
		foreach($role->capabilities as $capability => $grant)
			$all_cap_names[$capability] = $capability;
	}
	sort($all_cap_names);
    // end by georg leciejewski
	$feed_options = array('full_content', 'excerpt_only', 'title_only');
	$showOnPostEdit_options = array('sidebar', 'beneath_Post', 'beneath_Post_dragable');

	// executing form actions
	if (isset($_POST['submit'])) {
		check_admin_referer();
		// new by georg Leciejewski to only allow certain cap names to be shown
		//$my_allowed_cap_names = $_POST["show_postrestrictions"];
		$g_postrestrictions_options = array(
			'feed_privacy' => (in_array($_POST['post_restrictions_feed_privacy'], $feed_options)) ?
				$_POST['post_restrictions_feed_privacy'] : 'title_only',
			'rewrite_feed_link' => isset($_POST['post_restrictions_rewrite_feed_link']),
			'my_allowed_cap_names'=>$_POST["show_postrestrictions"],      // new by georg leciejewski
			'showOnPostEdit'=>$_POST["showOnPostEdit"]
		);
		update_option(POSTRESTRICTIONS_PLUGIN_ID, $g_postrestrictions_options);
		echo '<div id="message" class="updated fade"><p><strong>' . __('Options saved.', POSTRESTRICTIONS_PLUGIN_ID) . "</strong></p></div>\n";
	}

	postrestrictions_enforce_constraints();
?>
<div class="wrap">
	<h2><?php _e('Post Restrictions Options', POSTRESTRICTIONS_PLUGIN_ID); ?></h2>
	<p><?php _e("Post Restrictions shows private posts to other users if at least one of the user's capabilities meets one of the post's capabilities.", POSTRESTRICTIONS_PLUGIN_ID); ?></p>
	<form action="" method="post" id="post_restrictions_configuration">
		<table width="100%" cellspacing="2" cellpadding="5" class="editform" summary="options">
		<?php /* START new by georg leciejewski   */ ?>
			<tr valign="top">
				<th scope="row"><?php _e('Show only those Capabilities:', POSTRESTRICTIONS_PLUGIN_ID); ?></th>
				<td>
				<?php   // $my_allowed_cap_names == $g_postrestrictions_options['my_allowed_cap_names'];
				foreach($all_cap_names as $capability) {
						$capability_name = ucwords(str_replace('_', ' ', $capability));
						echo '<label style="display:block;float:left;width:15em;">';
						echo '<input type="checkbox" name="show_postrestrictions[]" value="'. $capability. '"';

						if (is_array($g_postrestrictions_options['my_allowed_cap_names']) && in_array($capability, $g_postrestrictions_options['my_allowed_cap_names'])) {

						echo ' checked="checked" /><strong> '. $capability_name. '</strong>';
						}else{
							echo ' /> '. $capability_name;
						}
						echo '</label>'. "\n";
						}  ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Where to show the Restrictions Box:', POSTRESTRICTIONS_PLUGIN_ID); ?></th>
				<td>
					<label for="showOnPostEdit">
					 <select name="showOnPostEdit" id="showOnPostEdit"><?php
						foreach ($showOnPostEdit_options as $option)
						{
							echo "	<option value='$option'";
							if ($option == $g_postrestrictions_options['showOnPostEdit'])
								echo " selected='yes'";
							echo ">$option</option>\n";
						}
?>
					</select>
						<?php _e('Where do you want the box to appear on the Post Edit Page', POSTRESTRICTIONS_PLUGIN_ID); ?>

					</label>
				</td>
			</tr>
			<?php /* END new by georg leciejewski   */ ?>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Feed Privacy:', POSTRESTRICTIONS_PLUGIN_ID); ?></th>
				<td>
					<select name="post_restrictions_feed_privacy" id="post_restrictions_feed_privacy">
<?php
						foreach ($feed_options as $option)
						{
							echo "						<option value='$option'";
							if ($option == $g_postrestrictions_options['feed_privacy'])
								echo " selected='yes'";
							echo ">$option</option>\n";
						}
?>
					</select>
					<br />
					<?php _e('For users reading feeds via HTTP authentication, this decides how much content should be shown', POSTRESTRICTIONS_PLUGIN_ID); ?>

				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Rewrite feed links:', POSTRESTRICTIONS_PLUGIN_ID); ?></th>
				<td>
					<label for="post_restrictions_rewrite_feed_link">
						<input type="checkbox" name="post_restrictions_rewrite_feed_link" id="post_restrictions_rewrite_feed_link" value="true" <?php if ($g_postrestrictions_options['rewrite_feed_link']) : ?>checked="checked" <?php endif; ?>/>
						<?php _e('For logged in users, automatically appends <code>http_auth=yes</code> to the query string of feed links to support HTTP Authentication', POSTRESTRICTIONS_PLUGIN_ID); ?>

					</label>
				</td>
			</tr>

		</table>

		<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Update Options') ?> &raquo;" />
		</p>
	</form>
</div>
<?php
	echo $html;
}

// Called when a post is saved, updates the post restriction
function postrestrictions_post_save($post_ID)
{
	global $current_user, $wpdb;
	global $g_postrestrictions_options;

	// we don't update but recreate the whole postmetas because of lazyness
	$old_meta = get_post_meta($post_ID, POSTRESTRICTIONS_META_KEY, false);
	$query = "
		DELETE FROM `{$wpdb->postmeta}`
		WHERE `post_id` = '{$post_ID}'
		AND `meta_key` = '". POSTRESTRICTIONS_META_KEY. "'
	";
	$wpdb->query($query);
/* until we can not avoid user to delete metas, we can not restrict this
	foreach($old_meta as $capability)
	{
		if (!$current_user->has_cap($capability))
			add_post_meta($post_ID, POSTRESTRICTIONS_META_KEY, $capability);
	}
*/
	foreach($g_postrestrictions_options['my_allowed_cap_names'] as $capability)
	{
		if ($_POST["post_restrictions_$capability"] /* && $current_user->has_cap($capability) */)
		{
			add_post_meta($post_ID, POSTRESTRICTIONS_META_KEY, $capability);
		}
	}
}

// Called before post status is updated on save. Marks
// post as private if a post restriction is set
function postrestrictions_status_save($post_status)
{
	global $current_user;
	global $g_postrestrictions_options;

	if ($post_status == 'publish')
	{
/* until we can not avoid user to delete metas, we can not restrict this
		$old_meta = get_post_meta($post_ID, POSTRESTRICTIONS_META_KEY, false);
		foreach($old_meta as $capability)
		{
			if (!$current_user->has_cap($capability))
				return 'private';
		}
*/
		foreach($g_postrestrictions_options['my_allowed_cap_names'] as $capability)
		{
			if ($_POST["post_restrictions_$capability"] /* && $current_user->has_cap($capability) */)
				return 'private';
		}
	}
	return $post_status;
}

// Called during the edit form, outputs the Post Restrictions drop down
function postrestrictions_do_form()
{
	global $post, $current_user,$g_postrestrictions_options;
    if($g_postrestrictions_options['showOnPostEdit']=="beneath_Post"||$g_postrestrictions_options['showOnPostEdit']=="beneath_Post_dragable")
    {
		echo '<fieldset id="post_restrictions" class="dbx-box">'."\n";
		echo '<h3 class="dbx-handle">'. __('Post Restrictions', POSTRESTRICTIONS_PLUGIN_ID). '</h3>'."\n";
		echo '<div class="dbx-content">'."\n";
		echo '<h4 style="font-weight:normal;">'. __('Mark one or more capability to restrict access to this post. The post will then automatically marked as private and will not be published to the public:', POSTRESTRICTIONS_PLUGIN_ID). '</h4>'."\n";

		$restrictions = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);

    		foreach($g_postrestrictions_options['my_allowed_cap_names'] as $capability) {   //georg leciejewski  changed for each to get the stored option for cap names

				$capability_name = ucwords(str_replace('_', ' ', $capability));
				echo '<label style="display:block;float:left;width:15em;">';
				echo '<input type="checkbox" name="post_restrictions_'. $capability. '" id="post_restrictions_'. $capability. '"';

				if (is_array($restrictions) && in_array($capability, $restrictions))
					echo ' checked="checked" /><strong> '. $capability_name. '</strong>';
				else
					echo ' /> '. $capability_name;
				echo '</label>'. "\n";
			}
    		 echo "</div>\n</fieldset>\n";

	}elseif ($g_postrestrictions_options['showOnPostEdit']=="sidebar"){
			echo '<fieldset id="post_restrictions" class="dbx-box">'."\n";
			echo '<h3 class="dbx-handle">'. __('Post Restrictions', POSTRESTRICTIONS_PLUGIN_ID). '</h3>'."\n";
			echo '<div class="dbx-content">'."\n";
			echo '<p style="font-weight:normal;">'. __('Mark one or more capability to restrict access to this post. The post will then automatically marked as private and will not be published to the public:', POSTRESTRICTIONS_PLUGIN_ID). '</p>'."\n";

			$restrictions = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);

    		foreach($g_postrestrictions_options['my_allowed_cap_names'] as $capability) {   //georg leciejewski  changed for each to get the stored option for cap names

				$capability_name = ucwords(str_replace('_', ' ', $capability));
			echo '<label for="post_restrictions_'. $capability. '" class="selectit">';
			echo '<input type="checkbox" name="post_restrictions_'. $capability. '" id="post_restrictions_'. $capability. '"';

			if (is_array($restrictions) && in_array($capability, $restrictions))
				echo ' checked="checked" /><strong> '. $capability_name. '</strong>';
			else
				echo ' /> '. $capability_name;
			echo '</label>'. "\n";
			}
	 echo "</div>\n</fieldset>\n";

		} //georg leciejewski
}//end postrestrictions_do_form

// Adds a post restriction column to the manage post list
function postrestrictions_add_column($columns)
{
	$columns[POSTRESTRICTIONS_PLUGIN_ID] = __('Restriction', POSTRESTRICTIONS_PLUGIN_ID);
	return $columns;
}

// Outputs the post restriction for each post in the manage post list
function postrestrictions_do_column($column_name)
{
	global $post;

	if ($column_name != POSTRESTRICTIONS_PLUGIN_ID)
		return false;

	$meta = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);

	if ($post->post_status != 'private'){
		_e('Public', POSTRESTRICTIONS_PLUGIN_ID);
	}else if (!empty($meta)) {
		//_e('<em>Protected</em>', POSTRESTRICTIONS_PLUGIN_ID);
        foreach ($meta as $value){   //new by georg leciejewski
		echo '<small>'.$value.'</small><br />';
		}
	}else {
		_e('<strong>Private</strong>', POSTRESTRICTIONS_PLUGIN_ID);
    }
	return true;
}

// ----------------------------------------------------------------------------
// runtime filter
// ----------------------------------------------------------------------------

// Enable users with the right restriction to read a private post
// This is required because single post viewing has special logic
// $allcaps = Capabilities the user currently has
// $caps = Primitive capabilities being tested / requested
// $args = array with:
// $args[0] = original meta capability requested
// $args[1] = user being tested
// $args[2] = post id to view
// See code for assumptions
function postrestrictions_must_fullfil($allcaps, $caps, $args)
{
	// This handler is only set up to deal with the read_private_posts
	// capability. Ignore all other calls into here.
	if (!in_array('read_private_posts', $caps))
		return $allcaps;

	// As of WP 2.0, read_private_posts is only requested when viewing
	// a single post. When this happens, the args[] array has three values,
	// as shown above. If WP changes the args[], this plugin will break

	$capabilities = array();
	$restrictions = get_post_meta($args[2], POSTRESTRICTIONS_META_KEY, false);
	$intersect = array_intersect(array_keys($allcaps), $restrictions);

	$capabilities['read_private_posts'] = !empty($intersect);
	return $capabilities;
}

// Left join with the custom fields table so we can filter
// based on restriction in the where clause
function postrestrictions_posts_join($join)
{
	// We add a left join here on the postmeta table --
	// this could conceivably break if another plugin
	// manipulates the join
	global $wpdb;
	$s = " LEFT JOIN {$wpdb->postmeta} AS postrestrictions_meta ON {$wpdb->posts}.ID = postrestrictions_meta.post_id
		AND postrestrictions_meta.meta_key = '". POSTRESTRICTIONS_META_KEY. "' ". $join;
	return $s;
}

// Change where clause to accept published posts and private posts
// with appropriate capability
function postrestrictions_posts_where($where)
{
	// This could break if another plugin manipulates the where
	// clause, specifically the post_status part
	global $wpdb, $current_user;
	$user_capabilities = "'". implode("', '", array_keys($current_user->allcaps)). "'";
	return str_replace('post_status = "publish"', '(post_status = "publish" OR (post_status = "private" ' .
		"AND (postrestrictions_meta.meta_key = '". POSTRESTRICTIONS_META_KEY. "' " .
			"AND postrestrictions_meta.meta_value IN ($user_capabilities) )))", $where);
}

// Called when using HTTP auth -- changes the post content
function postrestrictions_the_content_rss($content)
{
  global $post;
  global $g_postrestrictions_options;

  if ($post->post_status != 'private')
    return $content;

  switch ($g_postrestrictions_options['feed_privacy'])
  {
    case 'excerpt_only':
      return apply_filters('the_excerpt_rss', get_the_excerpt(true));

    case 'title_only':
    default:
      $permalink = get_permalink($post->ID);
      return __("View the content of this <a href='$permalink'>protected post</a>", POSTRESTRICTIONS_PLUGIN_ID);
  }
}

// Called when using HTTP auth -- changes the post excerpt
function postrestrictions_the_excerpt_rss($excerpt)
{
  global $post;
  global $g_postrestrictions_options;

  if ($post->post_status != 'private')
    return $excerpt;

  switch ($g_postrestrictions_options['feed_privacy'])
  {
    case 'excerpt_only':
      return $excerpt;

    case 'title_only':
    default:
      $permalink = get_permalink($post->ID);
      return __("View the content of this <a href='$permalink'>protected post</a>", POSTRESTRICTIONS_PLUGIN_ID);
  }
}

// Rewrites RSS feed links to support http authentication
// if the user is logged in
function postrestrictions_feed_link($output)
{
  $delim = (strpos('?', $output) === false) ? '?' : '&';
  return $output . $delim . 'http_auth=yes';
}

// ----------------------------------------------------------------------------
// setup
// ----------------------------------------------------------------------------

// See if we're doing HTTP Auth (http_auth=yes is in the query string)
function postrestrictions_enable_http_auth()
{
	if ($_GET['http_auth'] == 'yes')
	{
		// Perform HTTP user authentication against the WP user system
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']))
		{
			header('WWW-Authenticate: Basic realm="' . get_bloginfo('name') . '"');
			header('HTTP/1.0 401 Unauthorized');
			die(__('You must be logged in to view this site', POSTRESTRICTIONS_PLUGIN_ID));
		}
		else
		{
			// Override WP's get_currentuserinfo in order to do the login
			// via HTTP auth. This code is copied from WP2.0's get_currentuserinfo
			// I wish there were a more targeted way to override this
			function get_currentuserinfo()
			{
				global $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity, $current_user;

				// Use HTTP auth instead of cookies
				if(!wp_login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
				{
					header('WWW-Authenticate: Basic realm="' . get_bloginfo('name') . '"');
					header('HTTP/1.0 401 Unauthorized');
					die(__('Incorrect credentials', POSTRESTRICTIONS_PLUGIN_ID));
				}

				$user_login  = $_SERVER['PHP_AUTH_USER'];
				$userdata    = get_userdatabylogin($user_login);
				$user_level  = $userdata->user_level;
				$user_ID     = $userdata->ID;
				$user_email  = $userdata->user_email;
				$user_url    = $userdata->user_url;
				$user_pass_md5 = md5($userdata->user_pass);
				$user_identity = $userdata->display_name;

				if (empty($current_user))
					$current_user = new WP_User($user_ID);
			}
		}
	}
}

// Creates the database-stored options for the plugins that customize
// the plugin's behavior
function postrestrictions_setup_options()
{
	global $wp_roles;
	global $g_postrestrictions_options, $g_postrestrictions_cap_names;

	$g_postrestrictions_options = get_option(POSTRESTRICTIONS_PLUGIN_ID);
	if (!count($g_postrestrictions_options))
	{
		$g_postrestrictions_options = array(
			'feed_privacy' => 'title_only',
			'rewrite_feed_link' => true
		);

		add_option(POSTRESTRICTIONS_PLUGIN_ID, $g_postrestrictions_options);
	}
	//not needed anymore ??
    if($wp_roles){
	// Get role list
		foreach($wp_roles->role_objects as $key => $role)
		{
			foreach($role->capabilities as $capability => $grant)
				$g_postrestrictions_cap_names[$capability] = $capability;
		}
		sort($g_postrestrictions_cap_names);
	} //end if $wp_roles
}

// Sets up the postrestrictions admin menu
function postrestrictions_admin_setup()
{
	global $g_postrestrictions_options;
	if (function_exists('add_submenu_page'))
	{
		add_submenu_page('options-general.php', __('Post Restrictions', POSTRESTRICTIONS_PLUGIN_ID),
			__('Post Restrictions', POSTRESTRICTIONS_PLUGIN_ID),
			8, __FILE__, 'postrestrictions_config');
	}

	add_filter('manage_posts_columns', 'postrestrictions_add_column');
	add_filter('manage_posts_custom_column', 'postrestrictions_do_column');

	add_filter('simple_edit_form', 'postrestrictions_do_form');
//add_filter('edit_form_advanced', 'postrestrictions_do_form');

	//add_filter('dbx_post_advanced', 'postrestrictions_do_form'); // new hook in wp2
	if($g_postrestrictions_options['showOnPostEdit']=="sidebar"){
	  add_filter('dbx_post_sidebar', 'postrestrictions_do_form'); // new hook in wp2
	}
	if($g_postrestrictions_options['showOnPostEdit']=="beneath_Post"){
	  add_filter('edit_form_advanced', 'postrestrictions_do_form'); // new hook in wp2
	}
	if($g_postrestrictions_options['showOnPostEdit']=="beneath_Post_dragable"){
	  add_filter('dbx_post_advanced', 'postrestrictions_do_form'); // new hook in wp2
	}
	add_filter('status_save_pre', 'postrestrictions_status_save');
	add_filter('save_post', 'postrestrictions_post_save');
}

// Initialize plugin variables and functions, called during the
// 'init' action by WordPress
function postrestrictions_setup()
{
	// Setup options stored in database and global variables
	postrestrictions_setup_options();

	// Defer admin menu setup to only run if we're in admin section
	add_action('admin_menu', 'postrestrictions_admin_setup');

	// Don't bother with anything more unless someone is logged in
	if (is_user_logged_in())
	{
		// Make sure private pages aren't cached publicly
		header('Cache-Control: private');
		header('Pragma: no-cache');

		// Setup filters
		add_filter('posts_join', 'postrestrictions_posts_join');
		add_filter('posts_where', 'postrestrictions_posts_where');
		add_filter('user_has_cap', 'postrestrictions_must_fullfil', 10, 3);
		if ($g_postrestrictions_options['rewrite_feed_link'] == 1)
			add_filter('feed_link', 'postrestrictions_feed_link');

		// Only filter RSS content if we're using HTTP auth
		if ($_GET['http_auth'] == 'yes'
			&& $g_postrestrictions_options['feed_privacy'] != 'full_content')
		{
			// Don't bother setting up filters if we're not gonna change the content
			add_filter('the_content_rss', 'postrestrictions_the_content_rss');
			// WP 2.0 bug? the_content_rss never gets called
			add_filter('the_content', 'postrestrictions_the_content_rss');
			add_filter('the_excerpt_rss', 'postrestrictions_the_excerpt_rss');
		}

		// In case WP ever defines this function for us, check
		// if it exists first
		if (!function_exists('is_private'))
		{
			// Whether the current post is private
			function is_private()
			{
				global $post;
				$meta = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);
				return ($post->post_status == 'private' && empty($meta));
			}
		}
		// Whether the current post is protected, means is marked private but has
		// post restrictions defined
		function is_protected()
		{
			global $post;
			$meta = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);
			return ($post->post_status == 'private' && !empty($meta));
		}
		// Array with restrictions for this post
		function get_post_restrictions()
		{
			global $post;
			$meta = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);
			return $meta;
		}
		function get_post_restrictions_list($display = true)
		{
			global $post;
			$meta = get_post_meta($post->ID, POSTRESTRICTIONS_META_KEY, false);
			$list = ucwords(str_replace('_', ' ', implode(", ", $meta)));
			if (!$display)
				return $list;
			echo $list;
		}
	}
	else
	{
		// Not logged in, define fast functions that don't use the DB
		// in case they're used in the templates
		if (!function_exists('is_private'))
		{
			function is_private()
			{
				return false;
			}
		}
		function is_protected()
		{
			return false;
		}
		function get_post_restrictions()
		{
			return array();
		}
		function get_post_restrictions_list($display = true)
		{
			return '';
		}
	}
}

// ----------------------------------------------------------------------------

global $g_postrestrictions_cap_names;
$g_postrestrictions_cap_names = array();    //is this needed here anymore?
global $g_postrestrictions_options;
$g_postrestrictions_options = array();

// Just in case someone's loaded up the page standalone for whatever reason,
// make sure it doesn't crash in an ugly way
if (!function_exists('add_filter'))
  die(__("This page must be loaded as part of WordPress", POSTRESTRICTIONS_PLUGIN_ID));

global $wp_version;
if (substr($wp_version, 0, 2) == "1.")
  die(__("This plugin requires at least WordPress 2.0", POSTRESTRICTIONS_PLUGIN_ID));

postrestrictions_enable_http_auth();

add_filter('init', 'postrestrictions_setup');
?>