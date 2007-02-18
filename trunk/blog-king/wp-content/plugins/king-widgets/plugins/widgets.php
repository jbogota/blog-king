<?php
/*
Plugin Name: New Sidebar Widgets eXtended
Plugin URI: http://website-king.de
Description: Adds "Sidebar Widgets" panel under Presentation menu. Now has shows title in widget if it has one
Author: georg leciejewski
Version: 0.1
Author URI: http://website-king.de
*/

/**********************
*@Todo
* - take out the ids from all fields
* - take out the cals king_p from all p´s
* - field width all over css
* - original widgets
*
*
*/

/***** Global Variables *****/

$registered_sidebars = array();
$registered_widgets = array();
$registered_widget_controls = array();
$registered_widget_styles = array();

/***** Public Functions *****/

/**
*@desc register available sidebars for admin display
*/
function register_sidebars($number = 1, $args = array())
{
	global $registered_sidebars;

	$number = (int) $number;

	if ( is_string($args) )
		parse_str($args, $args);

	$name = $args['name'] ? $args['name'] : __('Sidebar');

	$i = 1;
	while ( $i <= $number )
	{
		if ( isset($args['name']) && $number > 1 )
		{
			if ( !strstr($name, '%d') )
				$name = "$name %d";
			$args['name'] = sprintf($name, $i);
		}
		register_sidebar($args);
		++$i;
	}
}

/**
*@desc register a single sidebar by reading his settigs from the functions.php of the template
* 		of no settings are found take the default settings
*/
function register_sidebar($args = array())
{
	global $registered_sidebars;

	if ( is_string($args) )
		parse_str($args, $args);

	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), count($registered_sidebars) + 1 ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => "</li>\n",
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => "</h2>\n",
	);

	$sidebar = array_merge($defaults, $args);

	$index = sanitize_title($sidebar['name']);

	$registered_sidebars[$index] = $sidebar;

	return $index;
}

/**
*@desc remove a sidebar from the registered sidebars array
*/
function unregister_sidebar($name)
{
	global $registered_sidebars;

	$index = sanitize_title($name);

	unset( $registered_sidebars[$index] );
}

/**
*@desc register a single widget
*/
function register_sidebar_widget($name, $output_callback, $classname = '')
{
	global $registered_widgets;

	if ( is_array($name) )
	{
		$id = sanitize_title(sprintf($name[0], $name[2]));
		$name = sprintf(__($name[0], $name[1]), $name[2]);
	}
	else
	{
		$id = sanitize_title($name);
		$name = __($name);
	}

	if ( (empty($classname) || !is_string($classname)) && is_string($output_callback) )
	{
			$classname = $output_callback;
	}

	$widget = array(
		'id' => $id,
		'callback' => $output_callback,
		'classname' => $classname,
		'params' => array_slice(func_get_args(), 2)
	);

	if ( empty($output_callback) )
	{
		unset($registered_widgets[$name]);
	}
	elseif ( is_callable($output_callback) )
	{
		$registered_widgets[$name] = $widget;
	}
}

/**
*@desc remove a sidebar widget
*/
function unregister_sidebar_widget($name)
{
	return register_sidebar_widget($name, '');
}

/**
* @desc register the widget controll popup
* @param string $name				- widget name
* @param string $control_callback 	- the function to call after saving the widgte
* @param string $width				- DEPRECEATED but left in for compatibility issues
* @param string $height				- DEPRECEATED but left in for compatibility issues
*/
function register_widget_control($name, $control_callback, $width = 300, $height = 200)
{
	global $registered_widget_controls;

	if ( is_array($name) )
	{
		$id = sanitize_title(sprintf($name[0], $name[2]));
		$name = sprintf(__($name[0], $name[1]), $name[2]);
	}
	else
	{
		$id = sanitize_title($name);
		$name = __($name);
	}

	$width = (int) $width > 90 ? (int) $width + 60 : 360;
	$height = (int) $height > 60 ? (int) $height + 40 : 240;

	if ( empty($control_callback) )
	{
		unset($registered_widget_controls[$name]);
	}
	else
	{
		$registered_widget_controls[$name] = array(
			'callback' => $control_callback,
//			'width' => $width, not needed anymore
//			'height' => $height,
			'params' => array_slice(func_get_args(), 4)
		);
	}
}

function unregister_widget_control($name)
{
	return register_widget_control($name, '');
}
 /**
 *@desc get the sidebars
 */
function dynamic_sidebar($name = 1)
{
	global $registered_sidebars, $registered_widgets;

	if ( is_int($name) )
    {
		$name = "Sidebar $name";
    }

	$index = sanitize_title($name);

	$sidebars_widgets = get_option('sidebars_widgets');

	$sidebar = $registered_sidebars[$index];

	if ( empty($sidebar) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
    {
		return false;
    }

	$did_one = false;
	foreach ( $sidebars_widgets[$index] as $name )
	{
		$callback = $registered_widgets[$name]['callback'];

		$params = array_merge(array($sidebar), $registered_widgets[$name]['params']);
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $registered_widgets[$name]['id'], $registered_widgets[$name]['classname']);
		if ( is_callable($callback) )
		{
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}

/**
*@desc check if the widget is active
*/
function is_active_widget($callback)
{
	global $registered_widgets;

	$sidebars_widgets = get_option('sidebars_widgets');

	if ( is_array($sidebars_widgets) )
    {
        foreach ( $sidebars_widgets as $sidebar => $widgets )
        {
		    if ( is_array($widgets) )
            {
                foreach ( $widgets as $widget )
                {
			        if ( $registered_widgets[$widget]['callback'] == $callback )
                    {
                        return true;
                    }
                }
            }
        }
    }
	return false;
}


/**** Private Functions *****/
/**
*@desc prepare the admin menu link for adminpage
*/
function sidebar_admin_setup()
{
	global $registered_sidebars;
	if ( count($registered_sidebars) < 1 )
    {# no sidebars defined .. no menu link
		return;
    }
	$page = preg_replace('!^.*/wp-content/[^/]*plugins/!', '', __FILE__);
	add_submenu_page('themes.php', 'Sidebar Widgets', 'Sidebar Widgets', 5, $page, 'sidebar_admin_page');
	if ( $_GET['page'] == $page )
    {
		add_action('admin_head', 'sidebar_admin_head');
		do_action('sidebar_admin_setup');
	}
}

/**
*@desc add css includes and stuff to header
*/
function sidebar_admin_head()
{
	global $registered_widgets, $registered_sidebars, $registered_widget_controls;
	$width = 1 + 262 * ( 1 + count($registered_sidebars));
	?>
	<style type="text/css">
	#sbadmin { max-width: 100%; min-width: <?php echo $width; ?>px; -moz-user-select: none; -khtml-user-select: none;user-select: none;}

	</style>
	<?php
	do_action('sidebar_admin_head');
}

/**
*@desc call the options for the widget
*/
function show_options_popup($widget)
{
	 call_user_func_array($widget['callback'], $widget['params']);
}

/**
*@desc setup the admin area for controlling the widgets
*/
function sidebar_admin_page()
{
	global $registered_widgets, $registered_sidebars, $registered_widget_controls;

	if ( count($registered_sidebars) < 1 )
	{
	?>
		<div class="wrap">
		<h2><?php _e('About Dynamic Sidebars'); ?></h2>
		<p><?php _e("You can modify your theme's sidebar, rearranging and configuring widgets right in this screen! Well, you could if you had a compatible theme. You're seeing this message because your theme isn't ready for widgets. <a href='http://andy.wordpress.com/widgets/get-ready'>Get it ready!</a>"); ?></p>
		</div>
	<?php
		return;
	}

	$sidebars_widgets = get_option('sidebars_widgets');
	if ( empty($sidebars_widgets) )
	{
		$sidebars_widgets = get_widget_defaults();
	}

	if ( isset($_POST['action']) )
	{#save widgets
		check_admin_referer();
		switch ( $_POST['action'] )
		{
            case 'default' :
				$sidebars_widgets = get_widget_defaults();
				update_option('sidebars_widgets', $sidebars_widgets);
				break;
			case 'popup' :
                if ( !empty($_POST['widget']) )
				{
					show_options_popup($_POST['widget']);
				}
				break;

			case 'save_widget_order' :
				if ( !empty($_POST['widget_order']) )
				{
					parse_str($_POST['widget_order'], $sidebarwidgets);
					$sidebars_widgets = array();
			        foreach ( $sidebarwidgets as $key => $val )
					{
		                foreach ( $val as $key1 => $val1 )
						{
                			foreach ( $registered_widgets as $key2 => $val2 )
							{
								if ( $val1 == $val2['id'] )
								{
									$sidebars_widgets[$key][] = $key2;
								}
							}
						}
					}
					update_option('sidebars_widgets', $sidebars_widgets);
				}
				break;
		}
	}

	ksort($registered_widgets);

	$inactive_widgets = array();
	foreach ( $registered_widgets as $name => $callback )
	{
		$is_active = false;
		foreach ( $registered_sidebars as $index => $sidebar )
		{
			if ( is_array($sidebars_widgets[$index]) && in_array($name, $sidebars_widgets[$index]) )
			{
				$is_active = true;
				break;
			}
		}
		if ( ! $is_active )
			$inactive_widgets[] = $name;
	}

	?>
	<?php if ( $_POST['action'] ) { ?>
	<div class="fade updated" id="message">
		<p><?php printf(__('Sidebar Updated. <a href="%s">View site &raquo;</a>'), get_settings('home') . '/'); ?></p>
	</div>
	<?php } ?>

	<div class="wrap">
		<h2><?php _e('Sidebar Arrangement'); ?></h2>
		<p><?php _e("You can drag and drop widgets into your sidebar below."); ?></p>
		<form id="sbadmin" method="post" action="">

					<div class="dropzone">
						<h3><?php _e('Available Widgets'); ?></h3>
						<ul class="sortable"><?php foreach ( $inactive_widgets as $name ) widget_draggable($name); ?></ul>
					</div>

					<?php foreach ( $registered_widget_controls as $name => $control ) : ?>
						<div class="hidden" id="<?php echo sanitize_title($name); ?>control">
							<?php call_user_func_array($control['callback'], $control['params']); ?>
						</div>
					<?php endforeach; ?>

					<?php
					$i = 1;
					foreach ( $registered_sidebars as $index => $sidebar ) :
					?>
						<div class="dropzone">
							<h3><?php echo $sidebar['name']; ?></h3>
							<ul class="sortable" id="<?php echo $index; ?>">

								<?php
								if ( is_array($sidebars_widgets[$index]) )
								{
									 foreach ( $sidebars_widgets[$index] as $name )
									 {
										widget_draggable($name);
									 }
								}
								?>
							</ul>
						</div>
					<?php endforeach; ?>

			<p class="submit">
				<input type="hidden" name="widget_order" id="widget_order"  />
				<input type="hidden" name="action" id="action" value="save_widget_order" />
				<input type="submit" value="<?php _e('Save changes'); ?> &raquo;" />
			</p>
		</form>
	</div>
<?php
	do_action('sidebar_admin_page');
}

/**
*@desc generate the dragggable li for the widgets list in admin
*/
function widget_draggable($name)
{
	global $registered_widgets, $registered_widget_controls;
	$san_name = sanitize_title($name);

	if ( !isset($registered_widgets[$name]) )
	{
		return;
	}
	$poptitle = __('Configure');

	$popper = $registered_widget_controls[$name] ? '<a href="#" title="'.$san_name.'" class="opendiv">edit</a>': '';

	if(!empty($registered_widgets[$name]['params']))
	{
		$number		= $registered_widgets[$name]['params'][0]; 				# get the  widget number
		$options 	= get_option($registered_widgets[$name]['classname']); 	# get the  widget options
		$widgettitle= $options[$number]['title'] ; 							#get the widget title with numbering
	}
	else
	{ #no widget numbering
		$options =get_option($registered_widgets[$name]['classname']);
		$widgettitle=$options['title'] ;
	}

	if(!empty($widgettitle))
	{#show only if title is populated
		$show_title= '<small>( '.$widgettitle.' )</small>';
	}

	echo "<li class='module' id='$san_name'>$name $show_title  $popper</li>\n";
}

function get_widget_defaults()
{
	global $registered_sidebars;
	foreach ( $registered_sidebars as $index => $sidebar )
	{
		$defaults[$index] = array();
	}
	return $defaults;
}
add_action('admin_menu', 'sidebar_admin_setup');

?>
