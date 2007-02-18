<?php
/*
Plugin Name: Content Widgets
Plugin URI: http://www.blog.mediaprojekte.de/
Description: Adds "Content Widgets" panel under Presentation menu.
Author: Georg Leciejewski
Version: 0.1
Author URI: http://www.blog.mediaprojekte.de/
*/


//////////////////////////////////////////////////////////// Global Variables

$registered_pages = array();
$registered_c_widgets = array();
$registered_c_widget_controls = array();
$registered_c_widget_styles = array();


//////////////////////////////////////////////////////////// Public Functions

function register_pages($number = 1, $args = array()) {
	global $registered_pages;

	$number = (int) $number;

	if ( is_string($args) )
		parse_str($args, $args);

	$name = $args['name'] ? $args['name'] : __('page');

	$i = 1;
	while ( $i <= $number ) {
		if ( isset($args['name']) && $number > 1 ) {
			if ( !strstr($name, '%d') )
				$name = "$name %d";
			$args['name'] = sprintf($name, $i);
		}
		register_page($args);
		++$i;
	}
}

function register_page($args = array()) {
	global $registered_pages;

	if ( is_string($args) )
		parse_str($args, $args);

	$defaults = array(
		'name' => sprintf(__('page %d'), count($registered_pages) + 1 ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => "</li>\n",
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => "</h2>\n",
	);

	$page = array_merge($defaults, $args);

	$index = sanitize_title($page['name']);

	$registered_pages[$index] = $page;

	return $index;
}

function unregister_page($name) {
	global $registered_pages;

	$index = sanitize_title($name);

	unset( $registered_pages[$index] );
}

function register_page_widget($name, $output_callback, $classname = '') {
	global $registered_c_widgets;

	if ( is_array($name) ) {
		$id = sanitize_title(sprintf($name[0], $name[2]));
		$name = sprintf(__($name[0], $name[1]), $name[2]);
	} else {
		$id = sanitize_title($name);
		$name = __($name);
	}

	if ( (empty($classname) || !is_string($classname)) && is_string($output_callback) )
			$classname = $output_callback;
	
	$widget = array(
		'id' => $id,
		'callback' => $output_callback,
		'classname' => $classname,
		'params' => array_slice(func_get_args(), 2)
	);

	if ( empty($output_callback) )
		unset($registered_c_widgets[$name]);
	elseif ( is_callable($output_callback) )
		$registered_c_widgets[$name] = $widget;
}

function unregister_page_widget($name) {
	return register_page_widget($name, '');
}

function  register_c_widget_control($name, $control_callback, $width = 300, $height = 200) {
	global $registered_c_widget_controls;

	if ( is_array($name) ) {
		$id = sanitize_title(sprintf($name[0], $name[2]));
		$name = sprintf(__($name[0], $name[1]), $name[2]);
	} else {
		$id = sanitize_title($name);
		$name = __($name);
	}

	$width = (int) $width > 90 ? (int) $width + 60 : 360;
	$height = (int) $height > 60 ? (int) $height + 40 : 240;

	if ( empty($control_callback) )
		unset($registered_c_widget_controls[$name]);
	else
		$registered_c_widget_controls[$name] = array(
			'callback' => $control_callback,
			'width' => $width,
			'height' => $height,
			'params' => array_slice(func_get_args(), 4)
		);
}

function unregister_c_widget_control($name) {
	return register_widget_control($name, '');
}
/**
*@desc output of the widgets in the frontend
*/
function dynamic_page($name = 1) {

	global $registered_pages, $registered_c_widgets;

	if ( is_int($name) )
		$name = "page $name";

	$index = sanitize_title($name);

	$content_widgets = get_option('content_widgets');

	$page = $registered_pages[$index];

	if ( empty($page) || !is_array($content_widgets[$index]) || empty($content_widgets[$index]) )
		return false;

	$did_one = false;
    ob_start();
	    foreach ( $content_widgets[$index] as $name ) {
		    $callback = $registered_c_widgets[$name]['callback'];

		    $params = array_merge(array($page), $registered_c_widgets[$name]['params']);
		    $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $registered_c_widgets[$name]['id'], $registered_c_widgets[$name]['classname']);
		    if ( is_callable($callback) ) {
			    call_user_func_array($callback, $params);
			    $did_one = true;
		    }
	    }
    $out = ob_get_contents();
    ob_end_clean();
    echo $out;

	return $did_one;
}

function is_active_c_widget($callback) {
	global $registered_c_widgets;

	$content_widgets = get_option('content_widgets');

	if ( is_array($content_widgets) ) foreach ( $content_widgets as $page => $widgets )
		if ( is_array($widgets) ) foreach ( $widgets as $widget )
			if ( $registered_c_widgets[$widget]['callback'] == $callback )
				return true;

	return false;
}


//////////////////////////////////////////////////////////// Private Functions

function page_admin_setup() {
	global $registered_pages;
	if ( count($registered_pages) < 1 )
		return;
	$page = preg_replace('!^.*/wp-content/[^/]*plugins/!', '', __FILE__);
	add_submenu_page('themes.php', 'Content Widgets', 'Content Widgets', 5, $page, 'page_admin_page');
	if ( $_GET['page'] == $page ) {
		add_action('admin_head', 'page_admin_head');
		do_action('page_admin_setup');
	}
}

function page_admin_head() {
	global $registered_c_widgets, $registered_pages, $registered_c_widget_controls;

	if ( file_exists(dirname(__FILE__).'/scriptaculous/scriptaculous.js') )
		$scriptdir = str_replace(ABSPATH, get_settings('siteurl').'/', dirname(__FILE__)) . '/scriptaculous';
	else
		$scriptdir = get_settings('siteurl') . '/wp-includes/js/scriptaculous';

	$width = 1 + 262 * ( 1 + count($registered_pages));
	$height = 35 * count($registered_c_widgets);
	?>
	<style type="text/css">
	body {
		height: 100%;
	}
	#sbadmin {
		width: <?php echo $width; ?>px;
		-moz-user-select: none;
		-khtml-user-select: none;
		user-select: none;
	}
	#sbadmin .submit {
	}
	#sbreset {
		float: left;
		margin: 1px 0;
	}
	.dropzone {
		float: left;
		margin-right: 10px;
		padding: 5px;
		border: 1px solid #bbb;
		background-color: #f0f8ff;
	}
	.dropzone h3 {
		text-align: center;
		color: #333;
	}
	.dropzone ul {
		list-style-type: none;
		width: 180px;
		height: <?php echo $height; ?>px;
		float: left;
		margin: 0;
		padding: 0;
	}
	.module {
		width: 179px;
		padding: 0;
		margin: 5px 0;
		cursor: move;
		display: block;
		border: 1px solid #ccc;
		background-color: #fbfbfb;
		text-align: left;
		line-height: 25px;
	}
	.handle {
		display: block;
		width: 126px;
		padding: 0 10px;
		border-top: 1px solid #f2f2f2;
		border-right: 1px solid #e8e8e8;
		border-bottom: 1px solid #e8e8e8;
		border-left: 1px solid #f2f2f2;
	}
	.popper {
		margin: 0;
		display: inline;
		position: absolute;
		top: 3px;
		right: 3px;
		overflow: hidden;
		text-align: center;
		height: 16px;
		font-size: 18px;
		line-height: 14px;
		cursor: pointer;
		padding: 0 3px 1px;
		border-top: 4px solid #6da6d1;
		background: url( images/fade-butt.png ) -5px 0px;
	}
	* html .popper {
		padding: 1px 6px 0;
		font-size: 16px;
	}
	* html .module {
		position: absolute;
		position: relative;
	}
	#sbadmin p.submit {
		padding-right: 10px;
		clear: left;
	}
	.placematt {
		position: absolute;
		cursor: default;
		margin: 10px 0 0;
		padding: 0;
		width: 179px;
		background-color: #ffe;
	}
	* html .placematt {
		margin-top: 5px;
	}
	.placematt h4 {
		text-align: center;
		margin-bottom: 5px;
	}
	.placematt span {
		padding: 0 10px 10px;
		text-align: justify;
	}
	#controls {
		height: 0px;
	}
	.control {
		position: absolute;
		display: block;
		background: #f9fcfe;
		padding: 0;
	}
	.controlhandle {
		cursor: move;
		background-color: #6da6d1;
		border-bottom: 2px solid #448abd;
		color: #333;
		display: block;
		margin: 0 0 5px;
		padding: 4px;
		font-size: 120%;
	}
	.controlcloser {
		cursor: pointer;
		font-size: 120%;
		display: block;
		position: absolute;
		top: 2px;
		right: 8px;
		padding: 0 3px;
		font-weight: bold;
	}
	.controlform {
		margin: 20px 30px;
	}
	.controlform p {
		text-align: center;
	}
	.control .checkbox {
		border: none;
		background: transparent;
	}
	.hidden {
		display: none;
	}
	#shadow {
		background: black;
		display: none;
		position: absolute;
		top: 0px;
		left: 0px;
		width: 100%;
	}
	</style>

	<script language="JavaScript" type="text/javascript" src="<?php echo $scriptdir; ?>/prototype.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $scriptdir; ?>/scriptaculous.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $scriptdir; ?>/dragdrop.js"></script>
	<script type="text/javascript">
		// <![CDATA[
		var cols = new Array;
<?php $i = 0; foreach ( array_merge(array('palette'=>array()), $registered_pages) as $index => $page ) : ?>
			cols[<?php echo $i++; ?>] = '<?php echo $index; ?>';
<?php endforeach; ?>
		var widgets = new Array;
<?php $i = 0; foreach ( $registered_c_widgets as $name => $widget ) : $san_name = sanitize_title($name); ?>
			widgets[<?php echo $i++; ?>] = '<?php echo $san_name; ?>';
<?php endforeach; ?>
		var controldims = new Array;
<?php foreach ( $registered_c_widget_controls as $name => $control ) : ?>
			controldims['<?php echo sanitize_title($name); ?>control'] = new Array;
			controldims['<?php echo sanitize_title($name); ?>control']['width'] = <?php echo (int) $control['width']; ?>;
			controldims['<?php echo sanitize_title($name); ?>control']['height'] = <?php echo (int) $control['height']; ?>;
<?php endforeach; ?>
		function initWidgets() {
<?php foreach ( $registered_c_widget_controls as $name => $control ) : $san_name = sanitize_title($name); ?>
			$('<?php echo $san_name; ?>popper').onclick = function() {popControl('<?php echo $san_name; ?>control');};
			$('<?php echo $san_name; ?>closer').onclick = function() {unpopControl('<?php echo $san_name; ?>control');};
			new Draggable('<?php echo sanitize_title($name); ?>control', {revert:false,handle:'controlhandle',starteffect:function(){},endeffect:function(){},change:function(o){dragChange(o);}});
			if ( true && window.opera )
				$('<?php echo sanitize_title($name); ?>control').style.border = '1px solid #bbb';
<?php endforeach; ?>
			if ( true && window.opera )
				$('shadow').style.background = 'transparent';
			new Effect.Opacity('shadow', {to:0.0});
			widgets.map(function(o) {o='widgetprefix-'+o; Position.absolutize(o); Position.relativize(o);} );
		}
		function resetDroppableHeights() {
			var max = 6;
			cols.map(function(o) {var c = $(o).childNodes.length; if ( c > max ) max = c;} );
			var height = 35 * ( max + 1);
			cols.map(function(o) {h = (($(o).childNodes.length + 1) * 35); $(o).style.height = (h > 280 ? h : 280) + 'px';} );
		}
		function maxHeight(elm) {
			htmlheight = document.body.parentNode.clientHeight;
			bodyheight = document.body.clientHeight;
			var height = htmlheight > bodyheight ? htmlheight : bodyheight;
			$(elm).style.height = height + 'px';
		}
		function dragChange(o) {
			el = o.element ? o.element : $(o);
			var p = Position.page(el);
			var left = p[0];
			var top = p[1];
			var right = $('shadow').offsetWidth - (el.offsetWidth + left);
			var bottom = $('shadow').offsetHeight - (el.offsetHeight + top);
			if ( left < 1 ) el.style.left = 0;
			if ( top < 1 ) el.style.top = 0;
			if ( right < 1 ) el.style.left = (left + right) + 'px';
			if ( bottom < 1 ) el.style.top = (top + bottom) + 'px';
		}
		function popControl(elm) {
			el = $(elm);
			el.style.width = controldims[elm]['width'] + 'px';
			el.style.height = controldims[elm]['height'] + 'px';
			var x = ( document.body.clientWidth - controldims[elm]['width'] ) / 2;
			var y = ( document.body.parentNode.clientHeight - controldims[elm]['height'] ) / 2;
			el.style.position = 'absolute';
			el.style.left = '' + x + 'px';
			el.style.top = '' + y + 'px';
			el.style.zIndex = 1000;
			el.className='control';
			$('shadow').onclick = function() {unpopControl(elm);};
	        window.onresize = function(){maxHeight('shadow');dragChange(elm);};
			popShadow();
		}
		function popShadow() {
			maxHeight('shadow');
			var shadow = $('shadow');
			shadow.style.zIndex = 999;
			shadow.style.display = 'block';
	        new Effect.Opacity('shadow', {duration:0.5, from:0.0, to:0.2});
		}
		function unpopShadow() {
	        new Effect.Opacity('shadow', {to:0.0});
			$('shadow').style.display = 'none';
		}
		function unpopControl(el) {
			$(el).className='hidden';
			unpopShadow();
		}
		function serializeAll() {
<?php foreach ( $registered_pages as $index => $page ) : ?>
			$('<?php echo $index; ?>order').value = Sortable.serialize('<?php echo $index; ?>');
<?php endforeach; ?>
		}
		function updateAll(el) {
			resetDroppableHeights();
			cols.map(function(o){
				if ( o == 'palette' ) return;
				var pm = $(o+'placematt');
				if ( $(o).childNodes.length == 0 ) {
					pm.style.display = 'block';
					Position.absolutize(o+'placematt');
				} else {
					pm.style.display = 'none';
				}
			});
		}
		function noSelection(event) {
			if ( document.selection ) {
				var range = document.selection.createRange();
				range.collapse(false);
				range.select();
				return false;
			}
		}
		addLoadEvent(updateAll);
		addLoadEvent(initWidgets);
		// ]]>
	</script>
	<?php
	do_action('page_admin_head');
}

function page_admin_page() {
	global $registered_c_widgets, $registered_pages, $registered_c_widget_controls;

	if ( count($registered_pages) < 1 ) {
?>
	<div class="wrap">
	<h2><?php _e('About Dynamic Content'); ?></h2>
	<p><?php _e("You can modify your theme's page, rearranging and configuring widgets right in this screen! Well, you could if you had a compatible theme. You're seeing this message because your theme isn't ready for widgets. <a href='http://andy.wordpress.com/widgets/get-ready'>Get it ready!</a>"); ?></p>
	</div>
<?php
		return;
	}
	$content_widgets = get_option('content_widgets');
	if ( empty($content_widgets) ) {
		$content_widgets = get_c_widget_defaults();
	}

	if ( isset($_POST['action']) ) {
		check_admin_referer();
		switch ( $_POST['action'] ) {
			case 'default' :
				$content_widgets = get_c_widget_defaults();
				update_option('content_widgets', $content_widgets);
				break;
			case 'save_widget_order' :
				$content_widgets = array();
				foreach ( $registered_pages as $index => $page ) {
					$postindex = $index . 'order';
					parse_str($_POST[$postindex], $order);
					$new_order = $order[$index];
					if ( is_array($new_order) )
						foreach ( $new_order as $sanitized_name )
							foreach ( $registered_c_widgets as $name => $callback )
								if ( $sanitized_name == sanitize_title($name) )
									$content_widgets[$index][] = $name;
				}
				update_option('content_widgets', $content_widgets);
				break;
		}
	}

	ksort($registered_c_widgets);

	$inactive_widgets = array();
	foreach ( $registered_c_widgets as $name => $callback ) {
		$is_active = false;
		foreach ( $registered_pages as $index => $page ) {
			if ( is_array($content_widgets[$index]) && in_array($name, $content_widgets[$index]) ) {
				$is_active = true;
				break;
			}
		}
		if ( ! $is_active )
			$inactive_widgets[] = $name;
	}


	$containers = array('palette');
	foreach ( $registered_pages as $index => $page )
		$containers[] = $index;
	$c_string = '';
	foreach ( $containers as $container )
		$c_string .= "\"$container\",";
	$c_string = substr($c_string, 0, -1);
	?>
	<?php if ( $_POST['action'] ) { ?>
	<div class="fade updated" id="message">
	<p><?php printf(__('page Updated. <a href="%s">View site &raquo;</a>'), get_settings('home') . '/'); ?></p>
	</div>
	<?php } ?>
	<div class="wrap">
	<h2><?php _e('page Arrangement'); ?></h2>
	<p><?php _e("You can drag and drop widgets into your page below."); ?></p>
	<form id="sbadmin" method="POST" onsubmit="serializeAll()">
	<p>
	<div>
	<div class="dropzone">
		<h3><?php _e('Available Widgets'); ?></h3>
		<ul id="palette"><?php foreach ( $inactive_widgets as $name ) c_widget_draggable($name); ?></ul>
	</div>
<?php $i = 1; foreach ( $registered_pages as $index => $page ) : ?>
	<input type="hidden" id="<?php echo $index; ?>order" name="<?php echo $index; ?>order" value="" />
	<div class="dropzone">
		<h3><?php echo $page['name']; ?></h3>
		<div id="<?php echo $index; ?>placematt" class="module placematt"><span class="handle"><h4><?php _e('Default page'); ?></h4><?php _e('Your theme will display its usual page when this box is empty. Dragging widgets into this box will replace the usual page with your customized page.'); ?></span></div>
		<ul id="<?php echo $index; ?>"><?php if ( is_array($content_widgets[$index]) ) foreach ( $content_widgets[$index] as $name ) c_widget_draggable($name); ?></ul>
	</div>
<?php endforeach; ?>
	<br class="clear" />
	</div>

	<script type="text/javascript">
	// <![CDATA[
<?php foreach ( $containers as $container ) : ?>
	Sortable.create("<?php echo $container; ?>",
	{dropOnEmpty:true,containment:[<?php echo $c_string; ?>],handle:'handle',constraint:false,onUpdate:updateAll,format:/^widgetprefix-(.*)$/});
<?php endforeach; ?>
	// ]]>
	</script>
	</p>
	<p class="submit">
	<input type="hidden" name="action" id="action" value="save_widget_order" />
	<input type="submit" value="<?php _e('Save changes'); ?> &raquo;" />
	</p>
	<div id="controls">
<?php foreach ( $registered_c_widget_controls as $name => $control ) : ?>
		<div class="hidden" id="<?php echo sanitize_title($name); ?>control">
			<span class="controlhandle"><?php echo $name; ?></span>
			<span id="<?php echo sanitize_title($name); ?>closer" class="controlcloser">&#215;</span>
			<div class="controlform">
<?php call_user_func_array($control['callback'], $control['params']); ?>
			</div>
		</div>
<?php endforeach; ?>
	</div>
	</form>
	<br class="clear" />
	</div>
	<div id="shadow"> </div>
<?php
	do_action('page_admin_page');
}

function c_widget_draggable($name) {
	global $registered_c_widgets, $registered_c_widget_controls;
	$san_name = sanitize_title($name);
	if ( !isset($registered_c_widgets[$name]) )
		return;
	$poptitle = __('Configure');
	$popper = $registered_c_widget_controls[$name] ? " <div class='popper' id='{$san_name}popper' title='$poptitle'>&#8801;</div>" : '';
// start widget title enhacements by georg leciejewski
	if(!empty($registered_c_widgets[$name]['params'])){
		$number = $registered_c_widgets[$name]['params'][0]; 				// get the  widget number
		$options =get_option($registered_c_widgets[$name]['classname']); 	// get the  widget options
		$widgettitle=$options[$number]['title'] ; 						// get the widget title with numbering
	}else{ //no widget numbering
		$options =get_option($registered_c_widgets[$name]['classname']);
		$widgettitle=$options['title'] ;
	}
	//show only if title is populated
	if(!empty($widgettitle)){
		$show_title= '<small>( '.$widgettitle.' )</small>';
	}
// end widget title enhacements by georg leciejewski
	echo "<li class='module' id='widgetprefix-$san_name'><span class='handle'>$name $show_title  $popper</span></li>";
}

function get_c_widget_defaults() {
	global $registered_pages;
	foreach ( $registered_pages as $index => $page )
		$defaults[$index] = array();
	return $defaults;
}
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////// Standard Widgets
/**
	* @desc Output of the loop start
	* @author Georg Leciejewski
	*/
	function c_widget_start_loop($number = 1) {
		$options = get_option('c_widget_start_loop');
		$before_widget	= stripslashes($options[$number]['before_widget']);

		$start_if = ' if (have_posts()) : ';
		$start_while =' while (have_posts()) : the_post(); ';
		eval ($start_if);
 		echo $before_widget."\n";
		eval ($start_while);
		echo "\n";
	}
	/**
	* @desc Output of plugin?s editform in the adminarea
	* @author Georg Leciejewski
	*/
	function c_widget_start_loop_control($number) {
		// Get our options and see if we're handling a form submission.
		$options = $newoptions = get_option('c_widget_start_loop');

		if ( $_POST["c_widget_start_loop_submit_$number"] )
		{
			$newoptions[$number]['before_widget']	= html_entity_decode($_POST["c_widget_start_loop_before_$number"]);
		  }
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('c_widget_start_loop', $options);
		}
		$before_widget		= stripslashes(htmlentities($options[$number]['before_widget']));

		//before widget
		echo king_get_textbox_p(array(
				'Label_Id_Name' =>"c_widget_start_loop_before_$number",
				'Description' 	=> __('HTML before the Loop', 'widgetContent'),
				'Label_Title' 	=>  __('HTML before the real Loop. But inside have_posts() Posts. Usefull to set the overalle Heading. f.ex. in Search -> Searches found:', 'widgetContent'),
				'Value' 			=>$before_widget,
				'Size' 			=>'50',
				'Max' 			=>'300'));


		echo king_get_hidden("c_widget_start_loop_submit_$number",'1',"c_widget_start_loop_submit_$number");
	}

	/**
	* @desc takes the call from the number of boxes form and initiates new instances
	* @author Georg Leciejewski
	*/
	function c_widget_start_loop_setup() {
		$options = $newoptions = get_option('c_widget_start_loop');

		if ( isset($_POST['c_widget_start_loop_number_submit']) ) {
			$number = (int) $_POST['c_widget_start_loop_number'];
			if ( $number > 9 ) $number = 9;
			if ( $number < 1 ) $number = 1;
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('c_widget_start_loop', $options);
			c_widget_start_loop_register($options['number']);
		}
	}
	/**
	* @desc Admin Form to select number of titles
	* @author Georg Leciejewski
	*/
	function c_widget_start_loop_page() {

		$options = $newoptions = get_option('c_widget_start_loop');
		echo king_get_start_form('wrap','','',$k_Method='post');
		?>
		<h2><?php _e('Start Loop Widgets', 'widgetContent'); ?></h2>
		<?php
		echo '<p>';
		_e('How many Start Loops would you like? ', 'widgetContent');
		echo king_get_select("c_widget_start_loop_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9',), 'c_widget_start_loop_number' );
		echo king_get_submit('c_widget_start_loop_number_submit','','c_widget_start_loop_number_submit');
		echo king_get_end_p();
		echo king_get_end_form ();
	}

	/**
	* @desc Calls all other functions in this file initializing them
	* @author Georg Leciejewski
	*/
	function c_widget_start_loop_register()
	{
		$options = get_option('c_widget_start_loop');
		$number = $options['number'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++) {
			$name = array('Start Loop %s', null, $i);
			register_page_widget($name, $i <= $number ? 'c_widget_start_loop' : '', $i);
			register_c_widget_control($name, $i <= $number ? 'c_widget_start_loop_control' : '', 450, 200, $i);
		}
		add_action('page_admin_setup', 'c_widget_start_loop_setup');
		add_action('page_admin_page', 'c_widget_start_loop_page');
	}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
/**
* @desc Output of  the endwhile
* @author Georg Leciejewski
*/
function c_widget_end_while($number = 1) {
    $end_loop="<?php endwhile; ?>	<?php else : ?> <?php endif; ?>";

	eval('?>'.$end_loop);
}

/**
* @desc takes the call from the number of boxes form and initiates new instances
* @author Georg Leciejewski
*/
function c_widget_end_while_setup() {
	$options = $newoptions = get_option('c_widget_end_while');

	if ( isset($_POST['c_widget_end_while_number_submit']) ) {
		$number = (int) $_POST['c_widget_end_while_number'];
		if ( $number > 9 ) $number = 9;
		if ( $number < 1 ) $number = 1;
		$newoptions['number'] = $number;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('c_widget_end_while', $options);
		c_widget_end_while_register($options['number']);
	}
}
/**
* @desc Admin Form to select number of titles
* @author Georg Leciejewski
*/
function c_widget_end_while_page() {

	$options = $newoptions = get_option('c_widget_end_while');
	echo king_get_start_form('wrap','','',$k_Method='post');
	?>
	<h2><?php _e('While Posts Widgets', 'widgetContent'); ?></h2>
	<?php
	echo '<p>';
	_e('How many `While Have Post´ Boxes would you like? ', 'widgetContent');
	echo king_get_select("c_widget_end_while_number", $options['number'], array('1', '2', '3', '4', '5', '6', '7', '8', '9',), 'c_widget_end_while_number' );
	echo king_get_submit('c_widget_end_while_number_submit','','c_widget_end_while_number_submit');
	echo king_get_end_p();
	echo king_get_end_form ();
}

/**
* @desc Calls all other functions in this file initializing them
* @author Georg Leciejewski
*/
function c_widget_end_while_register()
{
	$options = get_option('c_widget_end_while');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = array('End While Posts %s', null, $i);
		register_page_widget($name, $i <= $number ? 'c_widget_end_while' : '', $i);
	}
	add_action('page_admin_setup', 'c_widget_end_while_setup');
	add_action('page_admin_page', 'c_widget_end_while_page');
}


//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////


function c_widget_comments($args) {

}
function c_widget_previousnext($args) {

}

function c_widget_tags($args) {

}
function c_widget_html($args) {

}




/////////////////////////////////////////////////////////// Actions and Registrations
include_once (ABSPATH . 'wp-content/plugins/king-includes/library/form.php');
include_once ('c_widget_title.php');
include_once ('c_widget_date.php');
include_once ('c_widget_author.php');
include_once ('c_widget_excerpt.php');
include_once ('c_widget_content.php');
include_once ('c_widget_category.php');
include_once ('c_widget_edit.php');
add_action('admin_menu', 'page_admin_setup');

//c_widget_start_loop_register(); ich kann irgendwie den while loop ncht richtig rausschreiben ??? deswegen vorerst nur alles inside des loops
//c_widget_end_while_register();
c_widget_title_register();
c_widget_date_register();
c_widget_author_register();
c_widget_excerpt_register();
c_widget_content_register();
c_widget_category_register();
c_widget_edit_register();
register_page_widget('Category', 'c_widget_category');


register_page_widget('Comments', 'c_widget_comments');
register_page_widget('Prev-Next Navi', 'c_widget_previousnext');

register_page_widget('Tags', 'c_widget_tags');
register_page_widget('HTML between', 'c_widget_html');
register_page_widget('Edit Link', 'c_widget_edit');
//register_page_widget('Start Loop', 'c_widget_start_loop');

//register_page_widget('End Loop Content', 'c_widget_end_loop_content');
//register_page_widget('End Loop', 'c_widget_end_while');
?>
