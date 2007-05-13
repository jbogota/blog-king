<?php
/*
Plugin Name: King Search
Version: 0.6
Plugin URI: http://www.website-king.de
Description: A Bundle to improve the Search engine + results of Wordpress.
Author: Georg Leciejewski
Author URI:http://www.website-king.de
*/

require_once('admin.php');

//if the locatoin redirect underneath comes in we don?t want to loose the variable
if ( isset($_GET['current_king_plugin']) )
{
	$current_king_plugin = $_GET['current_king_plugin'];
}
$parent_file = 'plugins.php';
$submenu_file = 'king-'.$current_king_plugin.'.php';
//plugin de- and activation
if ( isset($_GET['action']) )
{
	check_admin_referer();
	$current_king_plugin = $_GET['current_king_plugin'];

	if ('activate' == $_GET['action'])
	{
		$current = get_settings('active_'.$current_king_plugin.'_plugins');
		if (!in_array($_GET['plugin'], $current))
		{
			$current[] = trim( $_GET['plugin'] );
			sort($current);
			update_option('active_'.$current_king_plugin.'_plugins', $current);
			include(ABSPATH . 'wp-content/plugins/king-'.$current_king_plugin.'/plugins/' . trim( $_GET['plugin'] ));
			do_action('activate_' . trim( $_GET['plugin'] ));
		}
		//	header('Location: admin-king-plugins.php?activate=true&current_king_plugin='.$current_king_plugin);
		header('Location: admin-king-plugins.php?activate=true&current_king_plugin='.$current_king_plugin );

	}
	elseif ('deactivate' == $_GET['action'])
	{
		$current = get_settings('active_'.$current_king_plugin.'_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_'.$current_king_plugin.'_plugins', $current);
		do_action('deactivate_' . trim( $_GET['plugin'] ));
		//header('Location: admin-king-plugins.php?deactivate=true&current_king_plugin='.$current_king_plugin);
		header('Location:admin-king-plugins.php?deactivate=true&current_king_plugin='.$current_king_plugin);

	}
	exit;
}

$title = __('Manage Plugins');
require_once('admin-header.php');

// Clean up options
// If any plugins don't exist, axe 'em

$check_plugins = get_settings('active_'.$current_king_plugin.'_plugins');

// Sanity check.  If the active plugin list is not an array, make it an
// empty array.
if ( !is_array($check_plugins) ) {
	$check_plugins = array();
	update_option('active_'.$current_king_plugin.'_plugins', $check_plugins);
}

// If a plugin file does not exist, remove it from the list of active
// plugins.
foreach ($check_plugins as $check_plugin) {
	if (!file_exists(ABSPATH . 'wp-content/plugins/king-'.$current_king_plugin.'/plugins/' . $check_plugin)) { ///plugins
			$current = get_settings('active_'.$current_king_plugin.'_plugins');
			$key = array_search($check_plugin, $current);
			if ( false !== $key && NULL !== $key ) {
				unset($current[$key]);
				update_option('active_'.$current_king_plugin.'_plugins', $current);
			}
	}
}
?>

<?php if (isset($_GET['activate'])) : ?>
<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p>
</div>
<?php endif; ?>
<?php if (isset($_GET['deactivate'])) : ?>
<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php echo strtoupper ($current_king_plugin); ?> - <?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

if ( get_settings('active_'.$current_king_plugin.'_plugins') )
	$current_plugins = get_settings('active_'.$current_king_plugin.'_plugins');

// get plugin list in sublocation
$plugins = king_get_plugins ($sub_location='king-'.$current_king_plugin.'/plugins/');


/**
* @desc get king sub plugins
* copied from admin-functions and enhanced by $sub_location
* needs to be here so that we avoid editing core files.
*/
function king_get_plugins($sub_location='') {
	global $wp_plugins;

	if (isset ($wp_plugins)) {
		return $wp_plugins;
	}

	$wp_plugins = array ();
	//added if clause
	if(!empty($sub_location)){
		$plugin_loc = 'wp-content/plugins/'.$sub_location;
	}else{
		$plugin_loc = 'wp-content/plugins';
	}
		$plugin_root = ABSPATH.$plugin_loc;

	// Files in wp-content/plugins directory
	$plugins_dir = @ dir($plugin_root);
	if ($plugins_dir) {
		while (($file = $plugins_dir->read()) !== false) {
			if (preg_match('|^\.+$|', $file))
				continue;
			if (is_dir($plugin_root.'/'.$file)) {
				$plugins_subdir = @ dir($plugin_root.'/'.$file);
				if ($plugins_subdir) {
					while (($subfile = $plugins_subdir->read()) !== false) {
						if (preg_match('|^\.+$|', $subfile))
							continue;
						if (preg_match('|\.php$|', $subfile))
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if (preg_match('|\.php$|', $file))
					$plugin_files[] = $file;
			}
		}
	}
	if (!$plugins_dir || !$plugin_files) {
		return $wp_plugins;
	}

	sort($plugin_files);

	foreach ($plugin_files as $plugin_file) {
		if ( !is_readable("$plugin_root/$plugin_file"))
			continue;

		$plugin_data = get_plugin_data("$plugin_root/$plugin_file");

		if (empty ($plugin_data['Name'])) {
			continue;
		}

		$wp_plugins[plugin_basename($plugin_file)] = $plugin_data;
	}

	return $wp_plugins;
}

/*******************************************************/

if (empty($plugins)) {
	echo '<p>';
	_e("Couldn't open plugins directory or there are no plugins available."); // TODO: make more helpful
	echo '</p>';
} else {
// output plugin list
?>
<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th><?php _e('Version'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th><?php _e('Action'); ?></th>
	</tr>
<?php
	$style = '';

	function sort_search_plugins($plug1, $plug2) {
		return strnatcasecmp($plug1['Name'], $plug2['Name']);
	}
	
	uksort($plugins, 'sort_search_plugins');

	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$action = "<a href='admin-king-plugins.php?action=deactivate&amp;plugin=$plugin_file&amp;current_king_plugin=$current_king_plugin' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			$style .= $style == 'alternate' ? ' active' : 'active';
		} else {
			$action = "<a href='admin-king-plugins.php?action=activate&amp;plugin=$plugin_file&amp;current_king_plugin=$current_king_plugin' title=' $plugin_file".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array()) ); ;
		if ($style != '') $style = 'class="' . $style . '"';
		echo "
	<tr $style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='desc'>{$plugin_data['Description']} <cite>".sprintf(__('By %s'), $plugin_data['Author']).".</cite></td>
		<td class='togl'>$action</td>
	</tr>";
	}
?>
</table>
<?php
}
?>
</div>
