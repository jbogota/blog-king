<?php
/*
Plugin Name: Event Calendar
Version: 3.0.4
Plugin URI: http://blog.firetree.net/2005/07/18/eventcalendar-30/
Description: Manage future events as an online calendar. Display upcoming events in a dynamic calendar, on a listings page, or as a list in the sidebar. You can subscribe to the calendar from iCal (OSX) or Sunbird. Change settings on the Event Calendar Options screen.
Author: Alex Tingle
Author URI: http://blog.firetree.net/
*/

/*
Copyright (c) 2005, 2006 Alex Tingle.  $Revision: 1.34.4.6 $

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

load_plugin_textdomain('ec3');

/** Singleton class. Manages EC3 options. Global options that are guaranteed to
 *  exist (start of week, siteurl) are not managed by this class. */
class ec3_Options
{
  // Some global variables.
  var $version='3.0.4';
  var $is_listing=0;
  var $call_count=0;
  
  /** May be set TRUE by a template before the call to wp_head().
    * Turns off CSS in header. */
  var $nocss=true;

  // Which category is used for events? DEFAULT=0 */
  var $event_category;
  /** Show only events in calendar. DEFAULT=false */
  var $show_only_events;
  /** Number to months displayed by get_calendar(). DEFAULT=1 */
  var $num_months;
  /** Should day names be abbreviated to 1 or 3 letters? DEFAULT=1 */
  var $day_length;
  /** Hide the 'EC' logo on calendar displays? DEFAULT=0 */
  var $hide_logo;
  /** Use advanced post behaviour? DEFAULT=0 */
  var $advanced;
  /** Put navigation links below the calendar? DEFAULT=0 */
  var $nav_below;
  /** Disable popups? DEFAULT=0 */
  var $disable_popups;

  function ec3_Options()
  {
    $this->read_event_category();
    $this->read_show_only_events();
    $this->read_num_months();
    $this->read_day_length();
    $this->read_hide_logo();
    $this->read_advanced();
    $this->read_nav_below();
    $this->read_disable_popups();
  }
  
  // READ functions
  function read_event_category()
  {
    $this->event_category=intval( get_settings('ec3_event_category') );
    if($this->event_category==0)
        $this->event_category=1;
  }
  function read_show_only_events()
  {
    $this->show_only_events=intval(get_settings('ec3_show_only_events'));
  }
  function read_num_months()
  {
    $this->num_months =abs(intval(get_settings('ec3_num_months')));
    if(!$this->num_months)
        $this->num_months=1;
  }
  function read_day_length()
  {
    $this->day_length=intval(get_settings('ec3_day_length'));
    if($this->day_length==0)
        $this->day_length=1;
  }
  function read_hide_logo()
  {
    $this->hide_logo=intval(get_settings('ec3_hide_logo'));
  }
  function read_advanced()
  {
    $this->advanced=intval(get_settings('ec3_advanced'));
  }
  function read_nav_below()
  {
    $this->nav_below=intval(get_settings('ec3_nav_below'));
  }
  function read_disable_popups()
  {
    $this->disable_popups=intval(get_settings('ec3_disable_popups'));
  }
  
  // SET functions
  function set_event_category($val)
  {
    if($this->event_category!=$val)
    {
      update_option('ec3_event_category',$val);
      $this->read_event_category();
    }
  }
  function set_show_only_events($val)
  {
    if($this->show_only_events!=$val)
    {
      update_option('ec3_show_only_events',$val);
      $this->read_show_only_events();
    }
  }
  function set_num_months($val)
  {
    if($this->num_months!=$val)
    {
      update_option('ec3_num_months',$val);
      $this->read_num_months();
    }
  }
  function set_day_length($val)
  {
    if($this->day_length!=$val)
    {
      update_option('ec3_day_length',$val);
      $this->read_day_length();
    }
  }
  function set_hide_logo($val)
  {
    if($this->hide_logo!=$val)
    {
      update_option('ec3_hide_logo',$val);
      $this->read_hide_logo();
    }
  }
  function set_advanced($val)
  {
    if($this->advanced!=$val)
    {
      update_option('ec3_advanced',$val);
      $this->read_advanced();
    }
  }
  function set_nav_below($val)
  {
    if($this->nav_below!=$val)
    {
      update_option('ec3_nav_below',$val);
      $this->read_nav_below();
    }
  }
  function set_disable_popups($val)
  {
    if($this->disable_popups!=$val)
    {
      update_option('ec3_disable_popups',$val);
      $this->read_disable_popups();
    }
  }
} // end class ec3_Options


/** Singleton instance of ec3_Options. */
$ec3=new ec3_Options();


function ec3_action_wp_head()
{
  global $ec3,$month,$month_abbrev;
  $myfiles=get_settings('siteurl').'/wp-content/plugins/king-frontend/plugins/eventcalendar3/';
?>
<link rel="stylesheet" href="<?php echo $myfiles; ?>ec3.css" type="text/css" media="screen" />
	<!-- Added by EventCalendar plugin. Version <?php echo $ec3->version; ?> -->
	<script type='text/javascript' src='<?php echo $myfiles; ?>xmlhttprequest.js'></script>
	<script type='text/javascript' src='<?php echo $myfiles; ?>ec3.js'></script>
	<script type='text/javascript'><!--
	ec3.start_of_week=<?php echo intval( get_settings('start_of_week') ); ?>;
	ec3.month_of_year=new Array('<?php echo implode("','",$month); ?>');
	ec3.month_abbrev=new Array('<?php echo implode("','",$month_abbrev); ?>');
	ec3.siteurl='<?php echo get_settings('siteurl'); ?>';
	ec3.home='<?php echo get_settings('home'); ?>';
	ec3.hide_logo=<?php echo $ec3->hide_logo; ?>;
	ec3.viewpostsfor="<?php echo __('View posts for %1$s %2$s'); ?>";
	// --></script>

<?php if(!$ec3->nocss): ?>
<style type='text/css' media='screen'>
@import url(<?php echo $myfiles; ?>ec3.css);
.ec3_ec {
 background-image:url(<?php echo $myfiles; ?>ec.png) !IMPORTANT;
 background-image:none;
 filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $myfiles; ?>ec.png');
}
<?php   if(!$ec3->disable_popups): ?>
#ec3_shadow0 {
 background-image:url(<?php echo $myfiles; ?>shadow0.png) !IMPORTANT;
 background-image:none;
}
#ec3_shadow0 div {
 filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $myfiles; ?>shadow0.png',sizingMethod='scale');
}
#ec3_shadow1 {
 background-image:url(<?php echo $myfiles; ?>shadow1.png) !IMPORTANT;
 background-image:none;
 filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $myfiles; ?>shadow1.png',sizingMethod='crop');
}
#ec3_shadow2 {
 background-image:url(<?php echo $myfiles; ?>shadow2.png) !IMPORTANT;
 background-image:none;
}
#ec3_shadow2 div {
 filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $myfiles; ?>shadow2.png',sizingMethod='scale');
}
<?php   endif; ?>
</style>

<?php endif;
}

$ec3_today_id='ec3_'.gmdate('Y_n_j',get_settings('gmt_offset')*3600+time());

/** Calendar class. Encapsulates all functionality concerning the calendar - 
 *  how many days in each month, leap years, days of the week, locale
 *  names etc. */
class ec3_Date
{
  var $year_num =0;
  var $month_num=0;
  var $day_num  =0;
  var $_unixdate      =0;
  var $_days_in_month =0;

  function ec3_Date($year_num=0,$month_num=0,$day_num=0)
  {
    if(0==$year_num)
    {
      $this->from_current_page();
    }
    else
    {
      $this->year_num =$year_num;
      $this->month_num=$month_num;
      $this->day_num  =$day_num;
    }
  }

  /** Utility function. Calculates the value of month/year for the current
   *  page. Code block from wp-includes/template-functions-general.php
   *  (get_calendar function). */
  function from_current_page()
  {
    global
      $m,
      $monthnum,
      $wpdb,
      $year;

    if (isset($_GET['w'])) {
        $w = ''.intval($_GET['w']);
    }

    // Let's figure out when we are
    if (!empty($monthnum) && !empty($year)) {
        $thismonth = ''.zeroise(intval($monthnum), 2);
        $thisyear = ''.intval($year);
    } elseif (!empty($w)) {
        // We need to get the month from MySQL
        $thisyear = ''.intval(substr($m, 0, 4));
        $d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
        $thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
    } elseif (!empty($m)) {
//        $calendar = substr($m, 0, 6);
        $thisyear = ''.intval(substr($m, 0, 4));
        if (strlen($m) < 6) {
            $thismonth = '01';
        } else {
            $thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
        }
    } else {
        $thisyear = gmdate('Y', current_time('timestamp') + get_settings('gmt_offset') * 3600);
        $thismonth = gmdate('m', current_time('timestamp') + get_settings('gmt_offset') * 3600);
    }
    
    $this->year_num =intval($thisyear);
    $this->month_num=intval($thismonth);
    $this->day_num  =1;
  }
  
  /** Month arithmetic. Returns a new date object. */
  function plus_months($month_count)
  {
    $result=new ec3_Date($this->year_num,$this->month_num,$this->day_num);
    $result->month_num += $month_count;
    if($month_count>0)
    {
      while($result->month_num>12)
      {
        $result->month_num -= 12;
        $result->year_num++;
      }
    }
    else
    {
      while($result->month_num<1)
      {
        $result->month_num += 12;
        $result->year_num--;
      }
    }
    return $result;
  }
  /** Convenience function for accessing plus_months(). */
  function prev_month() { return $this->plus_months(-1); }
  function next_month() { return $this->plus_months( 1); }
  
  /** Modifies the current object to be one day in the future. */
  function increment_day()
  {
    $this->day_num++;
    if($this->day_num > $this->days_in_month())
    {
      $this->day_num=1;
      $this->month_num++;
      if($this->month_num>12)
      {
        $this->month_num=1;
        $this->year_num++;
      }
      $this->_days_in_month=0;
    }
    $this->_unixdate=0;
  }
  
  function month_id() // e.g. ec3_2005_06
  {
    return 'ec3_' . $this->year_num . '_' . $this->month_num;
  }
  function day_id()  // e.g. ec3_2005_06_25
  {
    $result='ec3_'.$this->year_num.'_'.$this->month_num.'_'.$this->day_num;
    global $ec3_today_id;
    if($result==$ec3_today_id)
      return 'today';
    else
      return $result;
  }
  function day_link()
  {
    global $ec3;
    if($ec3->show_only_events)
    {
      return get_settings('home') . '/?m='
       . $this->year_num
       . zeroise($this->month_num, 2)
       . zeroise($this->day_num, 2)
       . "&amp;cat=" . $ec3->event_category;
    }
    else
      return get_day_link($this->year_num,$this->month_num,$this->day_num);
  }
  function month_name() // e.g. June
  {
    global $month;
    return $month[zeroise($this->month_num,2)];
  }
  function month_abbrev() // e.g. Jun
  {
    global $month_abbrev;
    return $month_abbrev[ $this->month_name() ];
  }
  function month_link()
  {
    global $ec3;
    if($ec3->show_only_events)
    {
      return get_settings('home') . '/?m='
       . $this->year_num
       . zeroise($this->month_num, 2)
       . "&amp;cat=" . $ec3->event_category;
    }
    else
      return get_month_link($this->year_num,$this->month_num);
  }
  function days_in_month()
  {
    if(0==$this->_days_in_month)
      $this->_days_in_month=intval(date('t', $this->to_unixdate()));
    return $this->_days_in_month;
  }
  function week_day()
  {
    return intval(date('w', $this->to_unixdate()));
  }
  function to_unixdate()
  {
    if(0==intval($this->_unixdate))
    {
      $this->_unixdate =
        mktime(0,0,0, $this->month_num,$this->day_num,$this->year_num);
    }
    return $this->_unixdate;
  }
} // end class ec3_Date


/** Calculate the header (days of week). */
function ec3_util_thead()
{
  global
    $ec3,
    $weekday,
    $weekday_abbrev,
    $weekday_initial;

  $result="<thead><tr>\n";

  $start_of_week =intval( get_settings('start_of_week') );
  for($i=0; $i<7; $i++)
  {
    $full_day_name=$weekday[ ($i+$start_of_week) % 7 ];
    if(3==$ec3->day_length)
        $display_day_name=$weekday_abbrev[$full_day_name];
    elseif($ec3->day_length<3)
        $display_day_name=$weekday_initial[$full_day_name];
    else
        $display_day_name=$full_day_name;
    $result.="\t<th abbr='$full_day_name' scope='col' title='$full_day_name'>"
           . "$display_day_name</th>\n";
  }

  $result.="</tr></thead>\n";
  return $result;
}


/** Echos the event calendar navigation controls. */
function ec3_get_calendar_nav($date,$num_months)
{
  global $ec3;
  echo "<table class='nav' summary='Calendar'><tbody><tr>\n";

  // Previous
  $prev=$date->prev_month();
  echo "\t<td id='prev'><a id='ec3_prev' href='" . $prev->month_link() . "'"
     . '>&laquo;&nbsp;' . $prev->month_abbrev() . "</a></td>\n";

  echo "\t<td><img id='ec3_spinner' style='display:none' src='" 
     . get_settings('siteurl') . "/wp-content/plugins/king-frontend/plugins/eventcalendar3/wait.gif'"
     . " alt='spinner' /></td>\n";

  // Next
  $next=$date->plus_months($num_months);
  echo "\t<td id='next'><a id='ec3_next' href='" . $next->month_link() . "'"
     . '>' . $next->month_abbrev() . "&nbsp;&raquo;</a></td>\n";

  echo "</tr></tbody></table>\n";
}


/** Represents all posts from a particular day.
 *  Generated by ec3_util_calendar_days(). */
class ec3_Day
{
  var $is_event =False;
  var $titles   =array();
  function ec3_Day(){}
  function add_post($title,$time,$is_event)
  {
    $safe_title=
      str_replace(
        array(',','@'),
        ' ',
        htmlspecialchars(
          stripslashes($title),
          ENT_QUOTES,
          get_settings('blog_charset')
        )
      );
    if($is_event)
    {
      $safe_title.=' @'.$time;
      $this->is_event=True;
    }
    $this->titles[]=$safe_title;
  }
  function get_titles()
  {
    return implode(', ',$this->titles);
  }
}


/** Generates an array of all 'ec3_Day's between the start of
 *  begin_month & end_month.
 *  month_id is in the form: ec3_<year_num>_<month_num> */
function ec3_util_calendar_days($begin_month_id,$end_month_id)
{
  $begin_date=substr($begin_month_id,4) . '_1';
  $end_date  =substr($end_month_id,4)   . '_1';

  global $ec3, $tablepost2cat, $tableposts, $wpdb;

  // Which posts are we interested in?
  if($ec3->show_only_events)
  {
    // Category ID number for event posts.
    $where_post = "category_id = $ec3->event_category";
  }
  else 
  {
    $now = gmdate('Y-m-d H:i:59');
    $where_post = "(post_date_gmt<='$now' OR category_id=$ec3->event_category)";
  }

  $calendar_entries = $wpdb->get_results(
    "SELECT DISTINCT
         post_title,
         post_date,
         DAYOFMONTH(post_date) AS day,
         MONTH(post_date) AS month,
         YEAR(post_date) AS year,
         (category_id = $ec3->event_category) AS is_event
       FROM $tableposts,$tablepost2cat
       WHERE post_date >= '$begin_date'
         AND post_date <  '$end_date'
         AND post_status = 'publish'
         AND id = post_id
         AND $where_post
       ORDER BY post_date ASC"
  );

  $calendar_days = array(); // result
  if($calendar_entries)
  {
    $time_format=get_settings('time_format');
    foreach($calendar_entries as $ent)
    {
      $date=new ec3_Date($ent->year,$ent->month,$ent->day);
      $day_id=$date->day_id();
      if(empty($calendar_days[$day_id]))
          $calendar_days[$day_id] = new ec3_Day();
      $calendar_days[$day_id]->add_post(
        "$ent->post_title",
        mysql2date($time_format,$ent->post_date),
        $ent->is_event
      );
    }
  }
  return $calendar_days;
}

/** Echos one event calendar month table. */
function ec3_get_calendar_month($date,$calendar_days,$thead)
{
  global $ec3;
  //
  // Table start.
  $title=
    sprintf(__('View posts for %1$s %2$s'),$date->month_name(),$date->year_num);
  echo "<table id='" . $date->month_id() . "' summary='Calendar'>\n<caption>"
    . '<a href="' . $date->month_link() . '" title="' . $title . '">'
    . $date->month_name() . ' ' . $date->year_num . "</a></caption>\n";
  echo $thead;

  //
  // Table body
  echo "<tbody>\n\t<tr>";

  $days_in_month =$date->days_in_month();
  $week_day=( $date->week_day() + 7 - intval(get_settings('start_of_week')) ) % 7;
  $col =0;
  
  while(True)
  {
    if($col>6)
    {
      echo "</tr>\n\t<tr>";
      $col=0;
    }
    if($col<$week_day)
    {
      // insert padding
      $pad=$week_day-$col;
      echo "<td colspan='$pad' class='pad'>&nbsp;</td>";
      $col=$week_day;
    }
    // insert day
    $day_id = $date->day_id();
    echo "<td id='$day_id'";

    if(array_key_exists($day_id,$calendar_days))
    {
      echo ' class="ec3_postday';
      if($calendar_days[$day_id]->is_event)
          echo ' ec3_eventday';
      echo '">';
      echo '<a href="' . $date->day_link()
         . '" title="' . $calendar_days[$day_id]->get_titles() . '"';
      if($calendar_days[$day_id]->is_event)
          echo ' class="eventday"';
      echo ">$date->day_num</a>";
    }
    else
    {
      echo '>' . $date->day_num;
    }

    echo '</td>';

    $col++;
    $date->increment_day();
    if(1==$date->day_num)
        break;
    $week_day=($week_day+1) % 7;
  }
  // insert padding
  $pad=7-$col;
  if($pad>1)
      echo "<td colspan='$pad' class='pad' style='vertical-align:bottom'>"
      . "<a href='http://blog.firetree.net/?ec3_version=$ec3->version'"
      . " title='Event Calendar $ec3->version'"
      . ($ec3->hide_logo? " style='display:none'>": ">")
      . "<span class='ec3_ec'><span>EC</span></span></a></td>";
  elseif($pad)
      echo "<td colspan='$pad' class='pad'>&nbsp;</td>";

  echo "</tr>\n</tbody>\n</table>";
}


/** Template function. Call this from your template to insert the
 *  Event Calendar. */
function ec3_get_calendar()
{
  // Can't cope with more than one calendar on the same page. Everything has
  // a unique ID, so it can't be duplicated.
  // Simple fix for problem: Just ignore all calls after the first.
  $ec3->call_count++;
  if($ec3->call_count>1)
  {
    echo "<!-- You can only have one Event Calendar on each page. -->\n";
    return;
  }

  echo "<div id='wp-calendar'>\n";

  $this_month = new ec3_Date();

  global $ec3;

  // Display navigation panel.
  if(!$ec3->nav_below)
    ec3_get_calendar_nav($this_month,$ec3->num_months);
  
  // Get entries
  $end_month=$this_month->plus_months($ec3->num_months);
  $calendar_days =
    ec3_util_calendar_days(
      $this_month->month_id(),
      $end_month->month_id()
    );

  // Display months.
  $thead=ec3_util_thead();
  for($i=0; $i<$ec3->num_months; $i++)
  {
    $next_month=$this_month->next_month();
    ec3_get_calendar_month($this_month,$calendar_days,$thead);
    $this_month=$next_month;
  }

  // Display navigation panel.
  if($ec3->nav_below)
    ec3_get_calendar_nav(new ec3_Date(),$ec3->num_months);

  echo "</div>\n";

  if(!$ec3->disable_popups)
    echo "\t<script type='text/javascript' src='".get_settings('siteurl')
    . "/wp-content/plugins/king-frontend/plugins/eventcalendar3/popup.js'></script>\n";
}


/** Substitutes placeholders like '%key%' in $format with 'value' from $data
 *  array. */
function ec3_format_str($format,$data)
{
  foreach($data as $k=>$v)
      $format=str_replace("%$k%",$v,$format);
  return $format;
}


/** Template function. Call this from your template to insert a list of
 *  forthcoming events. Available template variables are:
 *   - template_month: %MONTH%
 *   - template_day: %MONTH% %DATE% %SINCE% (only with Time Since plugin)
 *   - template_event: %DATE% %TIME% %LINK% %TITLE% %AUTHOR%
 */
function ec3_get_events(
  $limit,
  $template_event='<a href="%LINK%">%TITLE% (%TIME%)</a>',
  $template_day='%DATE%:',
  $date_format='j F',
  $template_month='',
  $month_format='F Y')
{
  global $ec3,$wpdb,$tableposts,$tablepost2cat,$tableusers,$wp_version;
  
  if(!$date_format)
      $date_format=get_settings('date_format');

  // Support older versions of Wordpress.
  if(ereg('^1[.]',$wp_version))
      $user_nicename='user_nickname';
  else
      $user_nicename='user_nicename';

  // Start at midnight to show all of today's events
  $now = gmdate('Y-m-d 00:00:00');
  $calendar_entries = $wpdb->get_results(
    "SELECT
         p.id AS id,
         post_title,
         post_date,
         u.$user_nicename AS author
       FROM $tableposts p, $tablepost2cat p2c, $tableusers u
       WHERE post_status = 'publish'
         AND post_date_gmt > '$now'
         AND p.id = post_id
         AND p.post_author = u.id
         AND category_id = $ec3->event_category
       ORDER BY post_date
       LIMIT $limit"
  );

  echo "<ul class='event_list'>";
  echo "<!-- Generated by Event Calendar v$ec3->version -->\n";
  if(count($calendar_entries))
  {
    $time_format=get_settings('time_format');
    $current_date=false;
    $current_month=false;
    $data=array();
    $time_now=time()+(60*60*get_settings("gmt_offset"));
    foreach($calendar_entries as $entry)
    {
      $data['MONTH']=mysql2date($month_format,$entry->post_date);
      if((!$current_month || $current_month!=$data['MONTH']) && $template_month)
      {
        if($current_month)
            echo "</ul></li>\n";
        echo "<li class='event_list_month'>"
        .    ec3_format_str($template_month,$data)."\n<ul>\n";
        $current_month=$data['MONTH'];
      }

      // To use %SINCE%, you need Dunstan's 'Time Since' plugin.
      if(function_exists('time_since'))
        $data['SINCE']=time_since($time_now,abs(strtotime($entry->post_date)));

      $data['DATE']=mysql2date($date_format,$entry->post_date);
      if((!$current_date || $current_date!=$data['DATE']) && $template_day)
      {
        if($current_month || $current_date)
            echo "</ul></li>\n";
        echo "<li class='event_list_day'>"
        .    ec3_format_str($template_day,$data)."\n<ul>\n";
        $current_date=$data['DATE'];
      }
      $data['TIME']  =mysql2date($time_format,$entry->post_date);
      $data['TITLE'] =$entry->post_title;
      $data['LINK']  =get_permalink($entry->id);
      $data['AUTHOR']=$entry->author;
      echo " <li>".ec3_format_str($template_event,$data)."</li>\n";
    }
    if($template_day || $template_month)
        echo "</ul></li>\n";
  }
  else
  {
    echo "<li>".__('No events.','ec3')."</li>\n";
  }
  echo "</ul>\n";
}


/** DO NOT USE. This function is for backwards compatibility. */
function ec2_get_calendar()
{
  ec3_get_calendar();
}
/** DO NOT USE. This function is for backwards compatibility. */
function ec2_get_events($limit, $date_format)
{
  ec3_get_events(
    $limit,
    '<a href="%LINK%">%TITLE% (%TIME%)</a>',
    '%DATE%:',
    $date_format
  );
}


function ec3_action_admin_menu()
{
  if(function_exists('add_options_page'))
  {
    add_options_page(
      'Event Calendar Options',
      'EventCalendar',
      6,
      basename(__FILE__),
      'ec3_options_subpanel'
    );
  }
}


function ec3_options_subpanel()
{
  global $ec3;

  if(isset($_POST['info_update']))
  {
    echo '<div class="updated"><p><strong>';
    if(isset($_POST['ec3_event_category']))
        $ec3->set_event_category( intval($_POST['ec3_event_category']) );
    if(isset($_POST['ec3_num_months']))
        $ec3->set_num_months( intval($_POST['ec3_num_months']) );
    if(isset($_POST['ec3_show_only_events']))
        $ec3->set_show_only_events( intval($_POST['ec3_show_only_events']) );
    if(isset($_POST['ec3_day_length']))
        $ec3->set_day_length( intval($_POST['ec3_day_length']) );
    if(isset($_POST['ec3_hide_logo']))
        $ec3->set_hide_logo( intval($_POST['ec3_hide_logo']) );
    if(isset($_POST['ec3_advanced']))
        $ec3->set_advanced( intval($_POST['ec3_advanced']) );
    if(isset($_POST['ec3_nav_below']))
        $ec3->set_nav_below( intval($_POST['ec3_nav_below']) );
    if(isset($_POST['ec3_disable_popups']))
        $ec3->set_disable_popups( intval($_POST['ec3_disable_popups']) );
    _e('Options set.','ec3');
    echo '</strong></p></div>';
  }
  ?>

 <div class=wrap>
  <form method="post">
   <h2><?php _e('Event Calendar Options','ec3'); ?></h2>
   <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 

    <tr valign="middle"> 
     <th width="33%" scope="row"><?php _e('Event category','ec3'); ?>:</th> 
     <td>
      <select name="ec3_event_category">
      <?php wp_dropdown_cats( 0, $ec3->event_category ); ?>
      </select>
     </td> 
    </tr> 

    <tr valign="middle">
     <th width="33%" scope="row"><?php _e('Show events as blog entries','ec3'); ?>:</th> 
     <td>
      <select name="ec3_advanced">
       <option value='0'<?php if(!$ec3->advanced) echo " selected='selected'" ?> >
        <?php _e('Events are Normal Posts','ec3'); ?>
       </option>
       <option value='1'<?php if($ec3->advanced) echo " selected='selected'" ?> >
        <?php _e('Keep Events Separate','ec3'); ?>
       </option>
      </select>
     </td> 
    </tr> 
    <tr valign="middle"><th width="33%" scope="row"></th> 
     <td><em>
      <?php _e('Keep Events Separate: the Event Category page shows future events, in date order. Events do not appear on front page.','ec3'); ?>
     </em></td> 
    </tr> 

   </table>

   <fieldset class="options"><legend><?php _e('Calendar Display','ec3'); ?></legend> 

   <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 

    <tr valign="middle"> 
     <th width="33%" scope="row"><?php _e('Number of months','ec3'); ?>:</th> 
     <td>
      <input type="text" name="ec3_num_months" value="<?php echo $ec3->num_months; ?>" />
     </td> 
    </tr> 

    <tr valign="middle"> 
     <th width="33%" scope="row"><?php _e('Show all categories in calendar','ec3'); ?>:</th> 
     <td>
      <select name="ec3_show_only_events">
       <option value='1'<?php if($ec3->show_only_events) echo " selected='selected'" ?> >
        <?php _e('Only Show Events','ec3'); ?>
       </option>
       <option value='0'<?php if(!$ec3->show_only_events) echo " selected='selected'" ?> >
        <?php _e('Show All Posts','ec3'); ?>
       </option>
      </select>
     </td> 
    </tr> 

    <tr valign="middle"> 
     <th width="33%" scope="row"><?php _e('Show day names as','ec3'); ?>:</th> 
     <td>
      <select name="ec3_day_length">
       <option value='1'<?php if($ec3->day_length<3) echo " selected='selected'" ?> >
        <?php _e('Single Letter','ec3'); ?>
       </option>
       <option value='3'<?php if(3==$ec3->day_length) echo " selected='selected'" ?> >
        <?php _e('3-Letter Abbreviation','ec3'); ?>
       </option>
       <option value='9'<?php if($ec3->day_length>3) echo " selected='selected'" ?> >
        <?php _e('Full Day Name','ec3'); ?>
       </option>
      </select>
     </td> 
    </tr> 

    <tr valign="middle"> 
     <th width="33%" scope="row"><?php _e('Show Event Calendar logo','ec3'); ?>:</th> 
     <td>
      <select name="ec3_hide_logo">
       <option value='0'<?php if(!$ec3->hide_logo) echo " selected='selected'" ?> >
        <?php _e('Show Logo','ec3'); ?>
       </option>
       <option value='1'<?php if($ec3->hide_logo) echo " selected='selected'" ?> >
        <?php _e('Hide Logo','ec3'); ?>
       </option>
      </select>
     </td> 
    </tr> 

    <tr valign="middle"> 
     <th width="33%" scope="row"><?php _e('Position of navigation links','ec3'); ?>:</th> 
     <td>
      <select name="ec3_nav_below">
       <option value='0'<?php if(!$ec3->nav_below) echo " selected='selected'" ?> >
        <?php _e('Above Calendar','ec3'); ?>
       </option>
       <option value='1'<?php if($ec3->nav_below) echo " selected='selected'" ?> >
        <?php _e('Below Calendar','ec3'); ?>
       </option>
      </select>
     </td> 
    </tr> 
    <tr valign="middle"><th width="33%" scope="row"></th>
     <td><em>
      <?php _e('The navigation links are more usable when they are above the calendar, but you might prefer them below it for aesthetic reasons.','ec3'); ?>
     </em></td> 
    </tr> 

    <tr valign="middle">
     <th width="33%" scope="row"><?php _e('Popup event lists','ec3'); ?>:</th> 
     <td>
      <select name="ec3_disable_popups">
       <option value='0'<?php if(!$ec3->disable_popups) echo " selected='selected'" ?> >
        <?php _e('Show Popups','ec3'); ?>
       </option>
       <option value='1'<?php if($ec3->disable_popups) echo " selected='selected'" ?> >
        <?php _e('Hide Popups','ec3'); ?>
       </option>
      </select>
     </td> 
    </tr> 
    <tr valign="middle"><th width="33%" scope="row"></th> 
     <td><em>
      <?php _e('You might want to disable popups if you use Nicetitles.','ec3'); ?>
     </em></td> 
    </tr> 

   </table>
   </fieldset>

   <p class="submit"><input type="submit" name="info_update" value="<?php
    _e('Update options','ec3')
    ?> &raquo;" /></p>
  </form>

  <h3>EXAMPLE SIDEBAR CODE:</h3>

  <pre><code>        &lt;li&gt;
           &lt;?php ec3_get_calendar(); ?&gt;
        &lt;/li&gt;
        &lt;li&gt;&lt;?php _e('Events'); ?&gt;
           &lt;?php ec3_get_events(5); ?&gt;
        &lt;/li&gt;</code></pre>

 </div> <?php
}


/** Eliminate date restrictions if the query is day- or category- specific. */
function ec3_filter_where($where)
{
  global $ec3,$wp_query,$tablepost2cat,$wpdb;
  $result=$where;

  if($wp_query->is_page)
      return $result;

  if(!function_exists('get_category')) // This error is so common...
      die("ERROR! Function get_category() is not defined."
       . " Upgrade to the latest version of Wordpress!");

  $event_cat_record = &get_category($ec3->event_category);
  $event_cat_nicename = $event_cat_record->category_nicename;
  $ec3->is_listing =
    (preg_match("/\bcategory_id\s*=\s*'?$ec3->event_category'?\b/",$where) ||
     preg_match("/\bcategory_nicename\s*=\s*'$event_cat_nicename'/",$where));

  if($ec3->is_listing && $ec3->advanced && !$wp_query->is_date)
  {
    // reverse date restriction for event category listing
    $now = gmdate('Y-m-d 00:00:00');
    $result=preg_replace(
      "/\bAND\s+post_date_gmt\s*<=\s*'([-0-9]+) [:0-9]+'/",
      "AND post_date_gmt > '\$1 00:00:00'",$where
    );
  }
  elseif($wp_query->is_date || $wp_query->is_single ||
         $ec3->is_listing || $ec3->advanced)
  {
    // (This should be a sub-select, but older versions of MySQL can't cope.)
    $post_list=$wpdb->get_results(
      "SELECT DISTINCT(post_id) FROM $tablepost2cat " .
      "WHERE category_id=$ec3->event_category" );
    if(count($post_list))
    {
      $post_list_str='-1'; // start with a dummy value
      foreach($post_list as $p)
          $post_list_str.=",$p->post_id";
      if($wp_query->is_date || $wp_query->is_single || $ec3->is_listing)
          // remove date restriction for events
          $result=preg_replace(
            "/AND +(post_date_gmt *<= *'[-: 0-9]+') +/",
            'AND ($1 OR id IN ('.$post_list_str.') ) ',$where
          );
      else // ($ec3->advanced)
          // remove events from blog.
          $result=preg_replace(
            "/AND +(post_date_gmt *<= *'[-: 0-9]+') +/",
            'AND ($1 AND id NOT IN ('.$post_list_str.') ) ',$where
          );
    } // end if(count($post_list))
  }
  return $result;
}


/** Change the order of event listings (only advanced mode). */
function ec3_filter_posts_orderby($orderby)
{
  global $ec3;
  if($ec3->is_listing)
      $orderby=preg_replace("/\bDESC\b/",'',$orderby);
  return $orderby;
}


/** If the parameter ec3_xml is set, then brutally hijack the page and replace
 *  it with XML calendar data. This is used by XmlHttpRequests from the 
 *  active calendar JavaScript. */
function ec3_filter_query_vars_xml($wpvarstoreset)
{
  if(isset($_GET['ec3_xml']))
  {
    $components=explode('_',$_GET['ec3_xml']);
    if(count($components)==2)
    {
      $date=new ec3_Date($components[0],$components[1]);
      $end=$date->next_month();
      $calendar_days=ec3_util_calendar_days($date->month_id(),$end->month_id());
      @header('Content-type: text/xml');
      echo '<?xml version="1.0" encoding="'.get_settings('blog_charset')
      .    '" standalone="yes"?>';
      echo "<calendar><month id='".$date->month_id()."'>\n";
      foreach($calendar_days as $day_id=>$day)
      {
        if('today'==$day_id)
          $dc=explode('_', gmdate(':_Y_m_d') );
        else
          $dc=explode('_',$day_id);
        if(count($dc)==4)
        {
          $date->day_num=$dc[3];
          $titles=$day->get_titles();
          echo "<day id='$day_id' is_event='$day->is_event'"
          .    " titles='$titles' link='" . $date->day_link() . "'/>\n";
        }
      }
      echo "</month></calendar>\n";
      exit(0);
    }
  }
  // else...
  return $wpvarstoreset;
}


/** If the parameter ec3_vcal is set, then brutally hijack the page and replace
 *  it with vCalendar data.
 * (Includes fixes contributed by Matthias Tarasiewicz.)*/
function ec3_filter_query_vars_vcal($wpvarstoreset)
{
  if(isset($_GET['ec3_vcal']))
  {
    //
    // Generate the vCalendar
    
    $default_duration=3; // hours

    $name=preg_replace('/[^0-9a-zA-Z]/','',get_bloginfo_rss('name'));

    header("Content-Type: text/x-vCalendar");
    header("Content-Disposition: inline; filename=$name.vcs");
    header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    echo "BEGIN:VCALENDAR\r\n";
    echo "VERSION:2.0\r\n";
    echo "X-WR-CALNAME:$name\r\n";

    global $ec3,$tableposts,$tablepost2cat,$tableusers,$wpdb,$wp_version;

    // Support older versions of Wordpress.
    if(ereg('^1[.]',$wp_version))
        $user_nicename='user_nickname';
    else
        $user_nicename='user_nicename';

    $calendar_entries = $wpdb->get_results(
      "SELECT
           p.id as id,
           post_title,
           post_excerpt,
           DATE_FORMAT(post_date_gmt,'%Y%m%dT%H%i%sZ') as dt_start,
           $user_nicename
         FROM $tableposts p, $tablepost2cat p2c
         LEFT JOIN $tableusers u
           ON u.ID=p.post_author
         WHERE post_status = 'publish'
           AND p.id = post_id
           AND category_id = $ec3->event_category"
    );

    foreach($calendar_entries as $entry)
    {
      global $id;
      $id=$entry->id;

      $meta=get_post_custom($entry->id);
      $duration=$default_duration;
      if(is_array($meta) && array_key_exists('duration',$meta))
      {
        $values=$meta['duration'];
        if(count($values))
           $duration=end($values);
      }

      echo "BEGIN:VEVENT\r\n";
      echo "SUMMARY:$entry->post_title\r\n";
      echo "URL;VALUE=URI:". get_permalink($entry->id) . "\r\n";
      if(strlen($entry->post_excerpt)>0)
      {
        // I can't get iCal to understand vCalendar encoding.
        // So just strip out newlines here:
        echo "DESCRIPTION:"
         . preg_replace('/[\r\n]+/',' ',$entry->post_excerpt) . "\r\n";
      }
      if($duration)
      {
        echo "DURATION:PT$duration"."H\r\n";
      }
      echo "DTSTART:$entry->dt_start\r\n";
      echo "DTEND:$entry->dt_start\r\n";
      echo "END:VEVENT\r\n";
    }

    echo "END:VCALENDAR\r\n";


    exit(0);
  }
  // else...
  return $wpvarstoreset;
}


//
// Hook in...

add_action('wp_head',      'ec3_action_wp_head');
add_action('admin_menu',   'ec3_action_admin_menu');
add_filter('posts_where',  'ec3_filter_where',11);
add_filter('query_vars',   'ec3_filter_query_vars_xml');
add_filter('query_vars',   'ec3_filter_query_vars_vcal');

if($ec3->advanced)
  add_filter('posts_orderby','ec3_filter_posts_orderby');

?>