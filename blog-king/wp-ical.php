<?php
if (empty($wp))
{
    require_once('wp-config.php');
}
if ($_GET[cat])
{
    $cat = $wpdb->get_row("SELECT wcat.* FROM $wpdb->categories wcat WHERE wcat.cat_ID = '$_GET[cat]'", OBJECT);
}
if ($cat)
{
    $posts = $wpdb->get_results("SELECT wposts.* FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta, $wpdb->post2cat wpost2cat WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = 'location' AND wposts.post_status = 'publish' AND wposts.ID = wpost2cat.post_id AND wpost2cat.category_id = '$cat->cat_ID'", OBJECT);
}
else
{
    $posts = $wpdb->get_results("SELECT wposts.* FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = 'king_events' AND wposts.post_status = 'publish'", OBJECT);
}

?>
BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:<?php bloginfo('name'); ?><?php if ($cat) {echo ": ".$cat->cat_name;} ?><?php echo "\n"; ?>
X-WR-TIMEZONE:<?php $timezone = get_option('iCalTimezone'); echo $timezone; ?><?php echo "\n"; ?>
CALSCALE:GREGORIAN
X-WR-CALDESC:<?php if (!$cat) { bloginfo("description"); } else { echo wp_ical_strip_content(category_description($cat->cat_ID)); } ?><?php echo "\n"; ?>
<?php
if ($posts) {
	foreach ($posts as $post) {
    	$king_event = get_post_meta($post->ID,'king_events',true);
?>
BEGIN:VEVENT
<?php if ($king_event['alldayevent'] == "on") {   ?>
DTSTART:<?php echo mysql2date('Ymd', $king_event['starttime'], 0); ?><?php echo "\n"; ?>
DTEND:<?php echo mysql2date('Ymd', $king_event['endtime'], 0); ?><?php echo "\n"; ?>
<?php } else { ?>
DTSTART;TZID=<?php echo $timezone; ?>:<?php echo mysql2date('Ymd\THi\0\0',$king_event['starttime'], 0); ?><?php echo "\n"; ?>
DTEND;TZID=<?php echo $timezone; ?>:<?php echo mysql2date('Ymd\THi\0\0',$king_event['endtime'], 0); ?><?php echo "\n"; ?><?php } ?>
SUMMARY:<?php the_title_rss(); ?><?php echo "\n"; ?>
URL;VALUE=URI:<?php echo get_permalink($post->ID); ?><?php echo "\n"; ?>
DESCRIPTION:<?php  echo king_events_strip_content($post->post_content); ?><?php echo "\n"; ?>
LOCATION:<?php echo $king_event['location']; ?><?php echo "\n"; ?>
END:VEVENT
<?php }
} ?>
END:VCALENDAR
