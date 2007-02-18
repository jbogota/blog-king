<?php
/*
Plugin Name: King_Events
Version: 0.53
Plugin URI:http://www.website-king.de
Description: Creates iCal subscribable calendars (.ics/php files) from your posts. Adds Microformats, Google map, Google calendar,  Yahoo! calendar, 30Boxes Calendar, GoYellow Map, QYPE, Pazes, Second Live Map, ics and iCal to event posts.
Author: Georg Leciejewski and Ole Wiemeler
Author URI: http://www.website-king.de
*/

add_action('admin_menu','king_events_adminmenu');
add_action('simple_edit_form','king_events_form', 8);
add_action('dbx_post_advanced','king_events_form', 8);
add_action('save_post','king_events_save_post', 8);
add_filter('the_content','king_events_insert_links',10);

/**
* @desc output the form beneath the post edit field
*
* 	TODO
* 	- betterformating of fields
* 	- wiederholende Events
*/
function king_events_form()
{
	global $month, $postdata;
	//global $wpdb, $month, $postdata, $tablepostmeta;
	$king_event_options = get_option('king_events');
	!empty($king_event_options['show_sl']) ?  $show_sl = true : $show_sl='' ;
	if ($_GET['action'] == 'edit') {
		$edit			= 1;
		$post_id		= $_GET['post'];
		$king_event 	= get_post_meta($post_id,'king_events',true);
		$location_name 	= $king_event['location_name'];
		$location   	= $king_event['location'];
		$sl_location	= $king_event['sl_location'];

		$starttime  	= $king_event['starttime'];
		$endtime    	= $king_event['endtime'];
		$ad				= ($king_event['alldayevent'] == "on")	? 'checked="checked"' : '';
		$google_cal		= ($king_event['google_cal'] == "on")	? 'checked="checked"' : '';
		$google_map		= ($king_event['google_map'] == "on")	? 'checked="checked"' : '';
		$boxes_cal		= ($king_event['boxes_cal'] == "on")	? 'checked="checked"' : '';
		$goyellow		= ($king_event['goyellow'] == "on")		? 'checked="checked"' : '';
		$plazes			= ($king_event['plazes'] == "on")		? 'checked="checked"' : '';
		$plazes_pe		= ($king_event['plazes_pe'] == "on")	? 'checked="checked"' : '';
		$qype			= ($king_event['qype'] == "on")			? 'checked="checked"' : '';
		$yahoo_cal		= ($king_event['yahoo_cal'] == "on")	? 'checked="checked"' : '';
		$ical			= ($king_event['ical'] == "on") 		? 'checked="checked"' : '';
		$ics			= ($king_event['ics'] == "on") 			? 'checked="checked"' : '';
		$hcal			= ($king_event['hcal'] == "on") 		? 'checked="checked"' : '';
		$cal_info		= ($king_event['cal_info'] == "on")		? 'checked="checked"' : '';
		$sl				= ($king_event['sl'] == "on")			? 'checked="checked"' : '';
		$blogscout		= ($king_event['blogscout'] == "on")	? 'checked="checked"' : '';

		# repeat vals
		//$repeat			= $king_event['repeat'];
//		$repeatday		= $king_event['repeat_day'];
//		$repeat_month	= $king_event['repeat_month'];
	}
	//insert the default options only if not editing a post
	$google_cal = ($edit) ? $google_cal	: ( isset($king_event_options['google_cal'])?  'checked="checked"' : '' ) ;
	$google_map = ($edit) ? $google_map : ( isset($king_event_options['google_map'])?  'checked="checked"' : '' ) ;
	$boxes_cal 	= ($edit) ? $boxes_cal  : ( isset($king_event_options['boxes_cal'])	?  'checked="checked"' : '' ) ;
	$goyellow 	= ($edit) ? $goyellow	: ( isset($king_event_options['goyellow'])	?  'checked="checked"' : '' ) ;
	$plazes 	= ($edit) ? $plazes		: ( isset($king_event_options['plazes'])	?  'checked="checked"' : '' ) ;
	$plazes_pe 	= ($edit) ? $plazes_pe	: ( isset($king_event_options['plazes_pe']) ?  'checked="checked"' : '' ) ;
	$qype 		= ($edit) ? $qype		: ( isset($king_event_options['qype'])	    ?  'checked="checked"' : '' ) ;
	$yahoo_cal 	= ($edit) ? $yahoo_cal  : ( isset($king_event_options['yahoo_cal'])	?  'checked="checked"' : '' ) ;
	$ical		= ($edit) ? $ical  		: ( isset($king_event_options['ical'])		?  'checked="checked"' : '' ) ;
	$ics		= ($edit) ? $ics  		: ( isset($king_event_options['ics'])		?  'checked="checked"' : '' ) ;
	$hcal 		= ($edit) ? $hcal  		: ( isset($king_event_options['hcal'])		?  'checked="checked"' : '' ) ;
	$cal_info 	= ($edit) ? $cal_info	: ( isset($king_event_options['cal_info'])	?  'checked="checked"' : '' ) ;
	$sl		 	= ($edit) ? $sl			: ( isset($king_event_options['sl'])		?  'checked="checked"' : '' ) ;
	$blogscout	= ($edit) ? $blogscout	: ( isset($king_event_options['blogscout'])	?  'checked="checked"' : '' ) ;

	$time_adj = time() + (get_settings('gmt_offset') * 3600);
	//start time
	$sj = ($edit) ? mysql2date('d', $starttime) : gmdate('d', $time_adj);
	$sm = ($edit) ? mysql2date('m', $starttime) : gmdate('m', $time_adj);
	$sa = ($edit) ? mysql2date('Y', $starttime) : gmdate('Y', $time_adj);
	$sh = ($edit) ? mysql2date('H', $starttime) : gmdate('H', $time_adj);
	$sn = ($edit) ? mysql2date('i', $starttime) : gmdate('i', $time_adj);
	//end time
	$ej = ($edit) ? mysql2date('d', $endtime) : gmdate('d', $time_adj);
	$em = ($edit) ? mysql2date('m', $endtime) : gmdate('m', $time_adj);
	$ea = ($edit) ? mysql2date('Y', $endtime) : gmdate('Y', $time_adj);
	$eh = ($edit) ? mysql2date('H', $endtime) : gmdate('H', $time_adj);
	$en = ($edit) ? mysql2date('i', $endtime) : gmdate('i', $time_adj);
?>
<fieldset class="dbx-box">
    <div class="dbx-h-andle-wrapper">
		<h3 class="dbx-handle"><?php _e('Event Calendar','kingplugin'); ?></h3>
	</div>
	<div class="dbx-c-ontent-wrapper">
		<div class="dbx-content">
			<div id="section-1" class="fragment" style="width:550px;float:left;">
				<fieldset>
					<legend><?php _e('Location Name: K&ouml;lner Dom','kingplugin'); ?></legend>
					<input type="text" name="location_name" value="<?php echo $location_name; ?>" size="35" /><br />
				</fieldset>
				<fieldset>
					<legend><?php _e('Location: 50733 K&ouml;ln Holbeinstrasse 2','kingplugin'); ?></legend>
					<input type="text" name="location" value="<?php echo $location; ?>" size="35" />
					<input type="checkbox" name="delete" value="delete" /><?php _e('Delete Event','kingplugin'); ?><br />
				</fieldset>
				<?php if(!empty($show_sl)): ?>
					<fieldset>
						<legend><?php _e('Second Life: Plush Phi/53/14/22','kingplugin'); ?></legend>
						<input type="text" name="sl_location" value="<?php echo $sl_location; ?>" size="35" />
					</fieldset>
				<?php endif; ?>
				<fieldset style="width:75%">
					<legend><label for="starttime"><?php _e('Start Time','kingplugin'); ?></label>&nbsp;&nbsp; <small>Nov | 17 | 2006 @ 17:11</small></legend>
					<select name="sm">
					<?php
						for ($i=1; $i < 13; $i=$i+1)
						{
							echo "\t\t\t<option value=\"$i\"";
							if ($i == $sm)
							echo " selected='selected'";
							if ($i < 10)
							{
								$ii = "0".$i;
							}
							else
							{
								$ii = "$i";
							}
							echo ">".$month["$ii"]."</option>\n";
						}
					?>
					</select>
					<input type="text" name="sj" value="<?php echo $sj; ?>" size="2" maxlength="2" />
					<input type="text" name="sa" value="<?php echo $sa; ?>" size="4" maxlength="5" /> @
					<input type="text" name="sh" value="<?php echo $sh; ?>" size="2" maxlength="2" /> :
					<input type="text" name="sn" value="<?php echo $sn; ?>" size="2" maxlength="2" />
					<input type="checkbox" name="ad" <?php echo $ad; ?> /><?php _e('All Day Event','kingplugin'); // Ganzt&auml;tiger Termin ?><br />
				</fieldset>

				<fieldset>
					<legend><label for="starttime"><?php _e('End Time','kingplugin'); ?></label></legend>
					<select name="em">
					<?php
					for ($i=1; $i < 13; $i=$i+1)
					{
						echo "\t\t\t<option value=\"$i\"";
						if ($i == $em)
						echo " selected='selected'";
						if ($i < 10)
						{
							$ii = "0".$i;
						}
						else
						{
							$ii = "$i";
						}
						echo ">".$month["$ii"]."</option>\n";
					}
					?>
					</select>
					<input type="text" name="ej" value="<?php echo $ej; ?>" size="2" maxlength="2" />
					<input type="text" name="ea" value="<?php echo $ea; ?>" size="4" maxlength="5" /> @
					<input type="text" name="eh" value="<?php echo $eh; ?>" size="2" maxlength="2" /> :
					<input type="text" name="en" value="<?php echo $en; ?>" size="2" maxlength="2" /> <br />
				</fieldset>
			</div>

			<div id="section-3" class="fragment" style="float:right; width:200px;; margin: 0 10px 0;">
				<fieldset>
					<input type="checkbox" name="cal_info" <?php echo $cal_info; ?> /><?php _e('Insert Event Details above Post','kingplugin'); ?><br />
					<input type="checkbox" name="hcal" <?php echo $hcal; ?> /><?php _e('Insert hCalendar Microformat','kingplugin'); ?><br />
					<input type="checkbox" name="ical" <?php echo $ical; ?> /><?php _e('Insert iCal Subscribe Link','kingplugin'); ?><br />
					<input type="checkbox" name="ics" <?php echo $ics; ?> /><?php _e('Insert ics Download Link','kingplugin'); ?><br />
					<input type="checkbox" name="google_map" <?php echo $google_map; ?> /><?php _e('Insert Google Map Link','kingplugin'); ?><br />
					<input type="checkbox" name="goyellow" <?php echo $goyellow; ?> /><?php _e('Insert Link to GoYellow Map','kingplugin'); ?><br />
					<input type="checkbox" name="google_cal" <?php echo $google_cal; ?> /><?php _e('Insert Google Calendar Link','kingplugin'); ?><br />
					<input type="checkbox" name="yahoo_cal" <?php echo $yahoo_cal; ?> /><?php _e('Insert Yahoo Calendar Link','kingplugin'); ?><br />
					<input type="checkbox" name="boxes_cal" <?php echo $boxes_cal; ?> /><?php _e('Insert 30Boxes Calendar Link','kingplugin'); ?><br />
					<input type="checkbox" name="qype" <?php echo $qype; ?> /><?php _e('Insert Qype Link - Best of Town','kingplugin'); ?><br />
					<input type="checkbox" name="plazes" <?php echo $plazes; ?> /><?php _e('Insert Plazes Link - Plazes','kingplugin'); ?><br />
					<input type="checkbox" name="plazes_pe" <?php echo $plazes_pe; ?> /><?php _e('Insert Plazes Link - People','kingplugin'); ?><br />
					<input type="checkbox" name="sl" <?php echo $sl; ?> /><?php _e('Insert Second Life Map Link','kingplugin'); ?><br />
					<input type="checkbox" name="blogscout" <?php echo $blogscout; ?> /><?php _e('Insert Blogscout Link)','kingplugin'); ?>
				</fieldset>
			</div>
		</div>
	</div>
</fieldset>
	<?php
} //end edit form

/**
* @desc process the event content when saving the post
*/
function king_events_save_post($post_id)
{
	if (!$post_id)
	{
		$post_id = $_POST['post_ID'];
	}
	if ($_POST['location'])
	{ # only save if location is present

		if (!empty($_POST['delete']))
		{# delete meta vals
			delete_post_meta($post_id,'king_events');
		}
		else
		{ # save it all
			$king_events = Array();
			$king_events['location_name']	= $_POST['location_name'];
			$king_events['location']		= $_POST['location'];
			$king_events['sl_location']		= $_POST['sl_location'];
			$king_events['starttime']		= date("Y-m-d G:i:s", mktime($_POST['sh'], $_POST['sn'], 0, $_POST['sm'], $_POST['sj'], $_POST['sa']));
			$king_events['endtime']			= date("Y-m-d G:i:s", mktime($_POST['eh'], $_POST['en'], 0, $_POST['em'], $_POST['ej'], $_POST['ea']));

			$king_events['alldayevent']	= ($_POST['ad']) 			? 'on' : 'off';
			$king_events['google_map']	= ($_POST['google_map']) 	? 'on' : '';
			$king_events['goyellow']	= ($_POST['goyellow']) 		? 'on' : '';
			$king_events['qype']		= ($_POST['qype']) 			? 'on' : '';
			$king_events['plazes']		= ($_POST['plazes']) 		? 'on' : '';
			$king_events['plazes_pe']	= ($_POST['plazes_pe']) 	? 'on' : '';
			$king_events['google_cal']	= ($_POST['google_cal']) 	? 'on' : '';
			$king_events['boxes_cal']	= ($_POST['boxes_cal']) 	? 'on' : '';
			$king_events['yahoo_cal']	= ($_POST['yahoo_cal']) 	? 'on' : '';
			$king_events['ical']		= ($_POST['ical']) 			? 'on' : '';
			$king_events['ics']			= ($_POST['ics']) 			? 'on' : '';
			$king_events['hcal']		= ($_POST['hcal']) 			? 'on' : '';
			$king_events['cal_info']	= ($_POST['cal_info']) 		? 'on' : '';
			$king_events['sl']			= ($_POST['sl']) 			? 'on' : '';
			$king_events['blogscout']	= ($_POST['blogscout']) 	? 'on' : '';

			if (get_post_meta($post_id,'king_events'))
			{//already present meta values
				update_post_meta($post_id,'king_events',$king_events);
			}
			else
			{//new meta values
				add_post_meta($post_id,'king_events',$king_events);
			}
		}
	}
}

/**
* @desc used to strip the content before output in ical feed
*       TODO:
* 		- change formating of output ?? What is allowed in ical format
* 		- maybe set a switch for content type in icalfeed to excerpt or full post later
* 		- insert the mapping link
*/
function king_events_strip_content($post)
{
	$content = strip_tags($post);
	$content = str_replace("\n", " ", $content);
	$content = str_replace("\r", " ", $content);
	$content = str_replace("\0", " ", $content);
	return $content;
}

/**
* @desc insert the google maps link into content
* 		TODO:
* 		- make template paring for inserting links in admin
* 		- make global settings for plugin
* 		- insert in rss ?
* 		- check utw for more options
*/
function king_events_insert_links($thecontent = '')
{
	global $post_ID, $post;

	$post_id 	= $post ? $post->ID : $post_ID;
	$king_event = get_post_meta($post_id,'king_events',true);
	$options	= get_option('king_events');

	//event infos
	if($king_event['cal_info'] == 'on')
	{
		$thecontent = king_events_template($king_event,$options) . $thecontent;
	}
	//hCal microformat
	if($king_event['hcal'] == 'on')
	{
		$thecontent =$thecontent . king_events_hcal($king_event);
	}
	//iCal subscribe
	if($king_event['ical'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="ical" href="' . king_events_ical_link($king_event['location'], $options) . '" target="blank">Subscribe iCal</a></p>'."\n";
	}
	//google map link
	if($king_event['ics'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="ics" href="' . king_events_ics_link($king_event['location'], $options) . '" target="blank">Download ics</a></p>'."\n";
	}
	//google map link
	if($king_event['google_map'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="google_map" href="' . king_events_googlemap_link($king_event['location'], $options) . '" target="blank">Show on Google Map</a></p>'."\n";
	}
	//goyellow map link
	if($king_event['goyellow'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="goyellow" href="' . king_events_goyellow_link($king_event['location']) . '" target="blank">Show on GoYellow Map</a></p>'."\n";
	}
	//google calendar link
	if($king_event['google_cal'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="google_cal" href="' . king_events_googlecal_link($king_event) . '" target="blank">Add to Google Calendar</a></p>'."\n";
	}
	//yahoo calendar link
	if($king_event['yahoo_cal'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="yahoo_cal" href="' . king_events_yahoocal_link($king_event) . '" target="blank">Add to Yahoo Calendar</a></p>'."\n";
	}
	//30boxes calendar link
	if($king_event['boxes_cal'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="boxes_cal" href="' . king_events_boxescal_link($king_event) . '" target="blank">Add to 30Boxes Calendar</a></p>'."\n";
	}
	//outlook ics calendar link
	if($king_event['outlook'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="ics_link" href="' . king_events_outlook_link($king_event) . '" target="blank">Add to Outlook Calendar</a></p>'."\n";
	}
	//qype - best of Town link
	if($king_event['qype'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="qype" href="' . king_events_qype_link($king_event['location']) . '" target="blank">Show Best in Town (Qype)</a></p>'."\n";
	}
	//plazes - plazes link
	if($king_event['plazes'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="plazes" href="' . king_events_plazes_link($king_event['location']) . '" target="blank">Show Plazes (Plazes)</a></p>'."\n";
	}
	//plazes - people link
	if($king_event['plazes_pe'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="plazes_pe" href="' . king_events_plazespeople_link($king_event['location']) . '" target="blank">Show People (Plazes)</a></p>'."\n";
	}
	//SLurl link
	if($king_event['sl'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="sl" href="' . king_events_sl_link($king_event['sl_location']) . '" target="blank">Show on Second Life Map</a></p>'."\n";
	}
	//Blogscout link
	if($king_event['blogscout'] == 'on')
	{
		$thecontent =$thecontent . '<p class="icon"><a id="blogscout" href="' . king_events_blogscout_link($king_event['location']) . '" target="blank">Blogs &amp; Posts near location (Blogscout)</a></p>'."\n";
	}
	return $thecontent;
}

/**
* @desc create the google maps link
* @author georg leciejewski
* @todo - change charset to blog carset
* 		- get google links from events admin
* @param string $link - the event location
* @param array $options - the global event options array
* @return string $google_link  - the link to show the event on google map
*/
function king_events_googlemap_link($link, $options)
{
	$google_link = $options['google_map_url'];// 'http://maps.google.de/maps?q=';
	$link = king_events_to_string($link,'+');
	$google_link .= htmlentities($link,ENT_QUOTES,'UTF-8');
	return $google_link;
}
/**
* @desc create the goyellow map link
* @todo change if we have the location fields in a seperate table
* @author georg leciejewski
* @param array $event - the event information as an array
* @return string $goyellow_link  - the link to bookmark the event in his google calendar
*/
function king_events_goyellow_link($link)
{

	$goyellow_link = 'http://www.goyellow.de/map/';
	$link = explode(' ', $link);
	$string = $link[0]; //take the zip
	unset ($link[0]); //kill zip
	unset ($link[1]); //kill city
	$string .= $link[0] . '/'.   implode('-', $link); //put the street adress together with -
	$goyellow_link .= $string;
	return $goyellow_link;
}

/**
* @desc create the qype link
* @todo change if we have the location fields in a seperate table
* @author ole wiemeler
* @param array $event - the event information as an array
* @return string $qype_link  - the link to qype - the best of town
*/
function king_events_qype_link($link)
{
	$qype_link = 'http://www.qype.com/';
	$link = explode(' ', $link);
	$qype_link .= $link[0]; //just use the zip appended to the link
	return $qype_link;
}

/**
* @desc create the plazes link
* @author ole wiemeler
* @param string $link - the plazes in town
* @param array $options - the global event options array
* @return string $plazes_link  - the link to show plazes in town
*/
function king_events_plazes_link($link)
{
	$plazes_link = 'http://beta.plazes.com/plazes/in/';
	$link = explode(' ', $link);
	$plazes_link .= htmlentities($link[1],ENT_QUOTES,'UTF-8'); //just use the city
	return $plazes_link;
}

/**
* @desc create the plazes people link
* @author ole wiemeler
* @param string $link - plazes people in town
* @param array $options - the global event options array
* @return string $plazes_link  - the link to show plazes in town
*/
function king_events_plazespeople_link($link)
{
	$plazes_link 	= 'http://beta.plazes.com/people/in/';
	$link			= explode(' ', $link);
	$plazes_link .= htmlentities($link[1],ENT_QUOTES,'UTF-8'); //just use the city
	return $plazes_link;
}

/**
* @desc create a string with a given seperator
* @author georg leciejewski
* @param array $string - the event information as an array
* @param string $devider - the event devider for the string like + or /
* @return string $string  - the string put together with the given devider ex.: 50733+cologne+street+345
*/
function king_events_to_string($string, $devider){
	$string = explode(' ', $string);
	$string = implode($devider, $string);
	return $string;
}

/**
* @desc create the google calendar link
* @author georg leciejewski
* @param array $event - the event information as an array
* @return string $googlecal_link  - the link to bookmark the event in his google calendar
*/
function king_events_googlecal_link($event)
{
	global $post;
	$google_link =  'http://www.google.com/calendar/event?action=TEMPLATE';
	$google_link .= '&amp;text='.king_events_to_string($post->post_title,'+');
	$google_link .= '&amp;dates='.mysql2date('Ymd\THi\0\0',$event['starttime'], 0).'/'. mysql2date('Ymd\THi\0\0',$event['endtime'], 0);
	$google_link .= '&amp;location='.king_events_to_string ($event['location'], '+');
	$google_link .= '&amp;details='.king_events_to_string (king_events_strip_content($post->post_content), '+') ;
	$google_link .= '&amp;sprop='. get_permalink($post->ID);
	return $google_link;
}


/**
*@desc create the yahoo calendar link
* @param array $event - the event information as an array
* @return string $yahoocal_link  - the link to bookmark the event in yahoo calendar
*/
function king_events_yahoocal_link($event)
{
	global $post;
	$yahoo_link = 'http://calendar.yahoo.com/?v=60';
	$yahoo_link .= '&amp;Title='.$post->post_title;
	$yahoo_link .= '&amp;DUR=';                     //dauer muss noch berechnet werden
	$yahoo_link .= '&amp;ST='.mysql2date('Ymd\THi\0\0',$event['starttime'], 0); //20060925T200000
	$yahoo_link .= '&amp;in_loc='.$event['location'];
	$yahoo_link .= '&amp;DESC='.king_events_strip_content($post->post_content);
	$yahoo_link .= '&amp;URL='. get_permalink($post->ID);
	return $yahoo_link;
}

/**
*@desc create the 30boxes calendar link
* @author ole wiemeler
* @param array $event - the event information as an array
* @return string $boxes_link  - the link to bookmark the event in yahoo calendar
*/
function king_events_boxescal_link($event)
{
	global $post;
	//put those variables in the events admin config later
	$boxes_link = 'http://30boxes.com/add.php?ics=http://feeds.technorati.com/event';
	$boxes_link .= '/'. get_permalink($post->ID);
	return $boxes_link;
}


/**
*@desc create the hCalendar format
* @param array $event - the event information as an array
* @return string $hCalendar - insert hCalendar Mircroformts
*/
function king_events_hcal($event)
{
	global $post;
	$hcal = '<div class="vevent">'."\n";
	$hcal .= '<a class="url" href="'.get_permalink($post->ID).'" style="display:none">'.get_permalink($post->ID).'</a>'."\n";
	$hcal .= '<span class="summary" style="display:none">'.$post->post_title.'</span>'."\n";
	$hcal .= '<abbr class="dtstart" title="'.mysql2date('Ymd\THi\0\0',$event['starttime'], 0).'" style="display:none">'.mysql2date('d. m. Y - H:i',$event['starttime'], 0).'</abbr>'."\n";
	$hcal .= '<abbr class="dtend" title="'.mysql2date('Ymd\THi\0\0',$event['endtime'], 0).'" style="display:none">'.mysql2date('d. m. Y - H:i',$event['endtime'], 0).'</abbr>'."\n";
	$hcal .= '<span class="location" style="display:none">'.$event['location_name'] . ', ' .$event['location'].'</span>'."\n";
	$hcal .= '</div>'."\n";
	return $hcal;
}


/**
*@desc create the hCalendar format
*
* @param array $event - the event information as an array
* @return string $yahoocal_link  - the link to bookmark the event in yahoo calendar
*/
function king_events_template($event, $options)
{
	$starttime	= mysql2date( $options['dateformat'], $event['starttime'], 0);
	$endtime 	= mysql2date( $options['dateformat'], $event['endtime'], 0);
	$replace 	= array($starttime,$endtime,$event['location'],$event['location_name']);
	$search 	= array('%%starttime%%','%%endtime%%','%%location%%','%%location_name%%');
	$output 	= str_replace($search, $replace, $options['event_html']);
	return $output;
}


/**
*@desc create the ical subscribe
* @author ole wiemeler
* @param array $event - the event information as an array
* @return string $ical subscribe  - the link to subscribe ical
*/
function king_events_ical_link($event)

{
	global $post;
	//put those variables in the events admin config later
	$ical_link = 'webcal://feeds.technorati.com/event';
	$ical_link .= '/'. get_permalink($post->ID);
	return $ical_link;
}

/**
* @desc create the ics download
* @author ole wiemeler
* @param array $event - the event information as an array
* @return string $ical download  - the link to subscribe ical
*/
function king_events_ics_link($event)

{
	global $post;
	//put those variables in the events admin config later
	$ics_link = 'http://feeds.technorati.com/event';
	$ics_link .= '/'. get_permalink($post->ID);
	return $ics_link;
}


/**
*@desc create the Outlook link
*
* @param array $event - the event information as an array
* @return string $yahoocal_link  - the link to bookmark the event in yahoo calendar
*/
function king_events_outlook_link($event)
{
	global $post;
/*    BEGIN:VCALENDAR
	PRODID:-//LEEDS MUSIC SCENE//EN
	VERSION:2.0
	BEGIN:VEVENT
	SUMMARY:BAND @ VENUE
	PRIORITY:0
	CATEGORIES:GIG
	CLASS:PUBLIC
	DTSTART:STARTTIME
	DTEND:ENDTIME
	URL:LINK TO LMS GIG PAGE
	DESCRIPTION:FULL BAND LIST
	LOCATION:VENUE
	END:VEVENT
	END:VCALENDAR
	$ics_link = "BEGIN:VCALENDAR\n";
	$ics_link .= '&amp;Title='.$post->post_title;
	$ics_link .= '&amp;DUR=';                     //dauer muss noch berechnet werden
	$ics_link .= '&amp;ST='.mysql2date('Ymd\THi\0\0',$event['starttime'], 0); //20060925T200000
	$ics_link .= '&amp;in_loc='.$event['location'];
	$ics_link .= '&amp;DESC='.king_events_strip_content($post->post_content);
	$ics_link .= '&amp;URL='. get_permalink($post->ID);
	*/
	return $ics_link;
}

/**
* @desc create the Second Life  SLurl
* @author ole wiemeler
* @param string $link - sl map
* @param array $options - the global event options array
* @return string $sl_link  - the link to show sl event on map
*/
function king_events_sl_link($link)
{
	$sl_link = 'http://slurl.com/secondlife';
	$sl_link .= '/'. $link;
	return $sl_link;
}

/**
* @desc create the Blogscout link
* @todo change if we have the location fields in a seperate table
* @author ole wiemeler
* @param array $event - the event information as an array
* @return string $blogscout_link  - the link to Blogscout - Blogs at location
*/
function king_events_blogscout_link($link)
{
	$blogscout_link = 'http://blogscout.de/karte/umgebung/?plz=';
	$link = explode(' ', $link);
	$blogscout_link .= $link[0]; //just use the zip appended to the link
	$blogscout_link .= '&amp;umkreis=3';
	return $blogscout_link;
}

/**
* @desc Admin Menu hook
* @author Georg Leciejewski
*/
function king_events_adminmenu(){
	add_options_page('King Events Options', 'King Events', 'activate_plugins', 'king_events.php', 'king_events_admin_options');
}
/**
* @desc The Admin Page
* @author Georg Leciejewski
*/
function king_events_admin_options()
{
	include_once (ABSPATH . 'wp-content/plugins/king-includes/library/form.php');

	//see if we're handling a form submission.
	if($_POST['king_events_admin_options_save'])
	{// transfer new form values
		$newoptions['google_cal']	= isset($_POST["google_cal"]);
		$newoptions['google_map']	= isset($_POST["google_map"]);
		$newoptions['boxes_cal']	= isset($_POST["boxes_cal"]);
		$newoptions['goyellow']		= isset($_POST["goyellow"]);
		$newoptions['qype']			= isset($_POST["qype"]);
		$newoptions['plazes']		= isset($_POST["plazes"]);
		$newoptions['plazes_pe']	= isset($_POST["plazes_pe"]);
		$newoptions['yahoo_cal']	= isset($_POST["yahoo_cal"]);
		$newoptions['hcal']			= isset($_POST["hcal"]);
		$newoptions['ical'] 		= isset($_POST["ical"]);
		$newoptions['ics'] 			= isset($_POST["ics"]);
		$newoptions['cal_info']		= isset($_POST["cal_info"]);
		$newoptions['show_sl']		= isset($_POST["show_sl"]);
		$newoptions['sl']			= isset($_POST["sl"]);
		$newoptions['blogscout']	= isset($_POST["blogscout"]);
		$newoptions['event_html']	= stripslashes($_POST["event_html"]);
		$newoptions['dateformat']	= stripslashes($_POST["dateformat"]);
		$newoptions['google_map_url']= stripslashes($_POST["google_map_url"]);

		//save options
		update_option('king_events', $newoptions);
	}

	// Get our options
	$options = get_option('king_events');

	//prepare variables for admin form
	$google_cal		= $options['google_cal']? 'checked="checked"' : '';
	$google_map		= $options['google_map']? 'checked="checked"' : '';
	$boxes_cal		= $options['boxes_cal']	? 'checked="checked"' : '';
	$goyellow		= $options['goyellow']	? 'checked="checked"' : '';
	$qype			= $options['qype']		? 'checked="checked"' : '';
	$plazes			= $options['plazes']	? 'checked="checked"' : '';
	$plazes_pe		= $options['plazes_pe']	? 'checked="checked"' : '';
	$yahoo_cal		= $options['yahoo_cal']	? 'checked="checked"' : '';
	$hcal			= $options['hcal']		? 'checked="checked"' : '';
	$ical			= $options['ical']		? 'checked="checked"' : '';
	$ics			= $options['ics']		? 'checked="checked"' : '';
	$cal_info		= $options['cal_info']	? 'checked="checked"' : '';
	$show_sl		= $options['show_sl']	? 'checked="checked"' : '';
	$sl				= $options['sl']		? 'checked="checked"' : '';
	$blogscout		= $options['blogscout']	? 'checked="checked"' : '';
	$event_html 	= htmlspecialchars($options['event_html'], ENT_QUOTES);
	$dateformat		= $options['dateformat'];
	$google_map_url	= $options['google_map_url'];

	?>
	<div class="wrap">
	<h2><?php _e('King Events Options','kingplugin') ?> </h2>
	<form method="post" id="king_events_admin_options" action="">

		<legend><?php _e('Define Global King Events Options','kingplugin') ?></legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('the following:','kingplugin') ?></th>
				<td>
					<?php _e('Those Settings will set the defaults for new event posts. But this can also be edited in every single post.','kingplugin') ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Event Details above Post','kingplugin') ?></th>
				<td>
				<?php echo king_get_checkbox('cal_info',$cal_info); ?>
			</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('hCal Microformat in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('hcal',$hcal); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('iCal Subscribe Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('ical',$ical); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('ics Download Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('ics',$ics); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Google Calendar Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('google_cal',$google_cal); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('GoogleMap Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('google_map',$google_map); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('30boxes Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('boxes_cal',$boxes_cal); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('GoYellow Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('goyellow',$goyellow); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Yahoo Calendar Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('yahoo_cal',$yahoo_cal); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Qype Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('qype',$qype); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Plazes Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('plazes',$plazes); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Plazes People Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('plazes_pe',$plazes_pe); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Blogscout, german Blogs and Post at Location:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('plazes_pe',$plazes_pe); ?>
				</td>
			</tr>
            <tr valign="top">
				<th width="33%" scope="row"><?php _e('Show Second Live URL on Postingpage:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('show_sl',$show_sl); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('SL Link in Post:','kingplugin') ?></th>
				<td>
					<?php echo king_get_checkbox('sl',$sl); ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('HTML Template for Eventdetails in Post:','kingplugin') ?></th>
				<td >
					<?php echo king_get_textarea('event_html',$event_html,'','','40','4'); ?><br />
					<?php _e('Placeholders are:','kingplugin') ?><br />
					<strong>%%starttime%%</strong> ex: &lt;p&gt; Beginn: %%starttime%% &lt;/p&gt; <br />
					<strong>%%endtime%%</strong>  ex: &lt;p&gt; End: %%endtime%% &lt;/p&gt;<br />
					<strong>%%location_name%%, %%location%%</strong> ex: &lt;p&gt; Location: %%location_name%%, %%location%% &lt;/p&gt;
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Dateformat for Dates in HTML Template:','kingplugin') ?></th>
				<td>
					<?php echo king_get_textbox('dateformat',$dateformat); ?> <?php _e('Use phpTime Format: d m Y - H:i','kingplugin') ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('URL To Google Maps:','kingplugin') ?></th>
				<td>
					<?php echo king_get_textbox('google_map_url',$google_map_url,'','','40'); ?> <?php _e('Depending on your Country change URL Suffix to .de / .com : http://maps.google.com/maps?q=','kingplugin') ?>
				</td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row"><?php _e('Multiple ical feeds:','kingplugin') ?></th>
				<td>
					<?php echo king_get_textarea('ical_feeds',$ical_feeds,'','','40','5'); ?> <?php _e('Insert one Ical Feed URL in each Line','kingplugin') ?>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="king_events_admin_options_save" value="Save" /></p>
	</form>
	</div>
<?php
}
?>
