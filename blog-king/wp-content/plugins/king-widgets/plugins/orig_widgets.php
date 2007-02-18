<?php
/*
Plugin Name: Automattic Widgets
Plugin URI: http://svn.wp-plugins.org/widgets/trunk
Description: The widgets supplied with the original sidebars widgets release by Automattic.
Author: Automattic, Inc.
Version: 1.1
Author URI: http://automattic.com
*/
//////////////////////////////////////////////////////////// Standard Widgets

function widget_pages($args) {
	extract($args);
	$options = get_option('widget_pages');
	$title = empty($options['title']) ? __('Pages') : $options['title'];
	wp_list_pages("title_li=$before_title$title$after_title");
}

function widget_pages_control() {
	$options = $newoptions = get_option('widget_pages');
	if ( $_POST["pages-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["pages-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_pages', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="pages-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="pages-title" name="pages-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="pages-submit" name="pages-submit" value="1" />
<?php
}

function widget_links($args) {
	// This ONLY works with li/h2 sidebars.
	get_links_list();
}

function widget_search($args) {
	extract($args);
?>
		<?php echo $before_widget; ?>
			<form id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<div>
			<input type="text" name="s" id="s" size="15" /><br />
			<input type="submit" value="<?php _e('Search'); ?>" />
			</div>
			</form>
		<?php echo $after_widget; ?>
<?php
}

function widget_archives($args) {
	extract($args);
	$options = get_option('widget_archives');
	$title = empty($options['title']) ? __('Archives') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_get_archives('type=monthly'); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_archives_control() {
	$options = $newoptions = get_option('widget_archives');
	if ( $_POST["archives-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["archives-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_archives', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="archives-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="archives-title" name="archives-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="archives-submit" name="archives-submit" value="1" />
<?php
}

function widget_meta($args) {
	extract($args);
	$options = get_option('widget_meta');
	$title = empty($options['title']) ? __('Meta') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS 2.0'); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="http://wordpress.com/" title="<?php _e('Powered by Wordpress, state-of-the-art semantic personal publishing platform.'); ?>">WordPress.com</a></li>
			<?php wp_meta(); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}
function widget_meta_control() {
	$options = $newoptions = get_option('widget_meta');
	if ( $_POST["meta-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["meta-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_meta', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="meta-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="meta-title" name="meta-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="meta-submit" name="meta-submit" value="1" />
<?php
}

function widget_calendar($args) {
	extract($args);
	echo $before_widget;
	get_calendar();
	echo $after_widget;
}

function widget_text($args, $number = 1) {
	extract($args);
	$options = get_option('widget_text');
	$title = $options[$number]['title'];
	$text = $options[$number]['text'];
?>
		<?php echo $before_widget; ?>
			<?php $title ? print($before_title . $title . $after_title) : null; ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php echo $after_widget; ?>
<?php
}

function widget_text_control($number) {
	$options = $newoptions = get_option('widget_text');
	if ( $_POST["text-submit-$number"] ) {
		$newoptions[$number]['title'] = strip_tags(stripslashes($_POST["text-title-$number"]));
		$newoptions[$number]['text'] = stripslashes($_POST["text-text-$number"]);
		if ( !current_user_can('unfiltered_html') )
			$newoptions[$number]['text'] = stripslashes(wp_filter_post_kses($newoptions[$number]['text']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_text', $options);
	}
	$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
	$text = htmlspecialchars($options[$number]['text'], ENT_QUOTES);
?>
			<input style="width: 450px;" id="text-title-<?php echo "$number"; ?>" name="text-title-<?php echo "$number"; ?>" type="text" value="<?php echo $title; ?>" />
			<textarea style="width: 450px; height: 280px;" id="text-text-<?php echo "$number"; ?>" name="text-text-<?php echo "$number"; ?>"><?php echo $text; ?></textarea>
			<input type="hidden" id="text-submit-<?php echo "$number"; ?>" name="text-submit-<?php echo "$number"; ?>" value="1" />
<?php
}

function widget_text_setup() {
	$options = $newoptions = get_option('widget_text');
	if ( isset($_POST['text-number-submit']) ) {
		$number = (int) $_POST['text-number'];
		if ( $number > 9 ) $number = 9;
		if ( $number < 1 ) $number = 1;
		$newoptions['number'] = $number;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_text', $options);
		widget_text_register($options['number']);
	}
}

function widget_text_page() {
	$options = $newoptions = get_option('widget_text');
?>
	<div class="wrap">
		<form method="post" action="">
			<h2>Text Widgets</h2>
			<p style="line-height: 30px;"><?php _e('How many text widgets would you like?'); ?>
			<select id="text-number" name="text-number" value="<?php echo $options['number']; ?>">
<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
			</select>
			<span class="submit"><input type="submit" name="text-number-submit" id="text-number-submit" value="<?php _e('Save'); ?>" /></span></p>
		</form>
	</div>
<?php
}

function widget_text_register() {
	$options = get_option('widget_text');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = array('Text %s', null, $i);
		register_sidebar_widget($name, $i <= $number ? 'widget_text' : /* unregister */ '', $i);
		register_widget_control($name, $i <= $number ? 'widget_text_control' : /* unregister */ '', 460, 350, $i);
	}
	add_action('sidebar_admin_setup', 'widget_text_setup');
	add_action('sidebar_admin_page', 'widget_text_page');
}

function widget_categories($args) {
	extract($args);
	$options = get_option('widget_categories');
	$c = $options['count'] ? '1' : '0';
	$h = $options['hierarchical'] ? '1' : '0';
	$title = empty($options['title']) ? __('Categories') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_list_cats("sort_column=name&optioncount=$c&hierarchical=$h"); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_categories_control() {
	$options = $newoptions = get_option('widget_categories');
	if ( $_POST['categories-submit'] ) {
		$newoptions['count'] = isset($_POST['categories-count']);
		$newoptions['hierarchical'] = isset($_POST['categories-hierarchical']);
		$newoptions['title'] = strip_tags(stripslashes($_POST['categories-title']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_categories', $options);
	}
	$count = $options['count'] ? 'checked="checked"' : '';
	$hierarchical = $options['hierarchical'] ? 'checked="checked"' : '';
	$title = wp_specialchars($options['title']);
?>
			<p><label for="categories-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="categories-title" name="categories-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="categories-count">Show post counts <input class="checkbox" type="checkbox" <?php echo $count; ?> id="categories-count" name="categories-count" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="categories-hierarchical" style="text-align:right;">Show hierarchy <input class="checkbox" type="checkbox" <?php echo $hierarchical; ?> id="categories-hierarchical" name="categories-hierarchical" /></label></p>
			<input type="hidden" id="categories-submit" name="categories-submit" value="1" />
<?php
}

function widget_recent_entries($args) {
	extract($args);
	$title = __('Recent Posts');
	$r = new WP_Query('showposts=10');
	if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li><a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li>
			<?php endwhile; ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
	endif;
}

function widget_recent_comments($args) {
	global $wpdb, $comments, $comment;
	extract($args, EXTR_SKIP);
	$options = get_option('widget_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments') : $options['title'];
	$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 5");
?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul id="recentcomments"><?php
			if ( $comments ) : foreach ($comments as $comment) :
			echo  '<li class="recentcomments">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
			endforeach; endif;?></ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_recent_comments_control() {
	$options = $newoptions = get_option('widget_recent_comments');
	if ( $_POST["recent-comments-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["recent-comments-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_recent_comments', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="recent-comments-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="recent-comments-title" name="recent-comments-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="recent-comments-submit" name="recent-comments-submit" value="1" />
<?php
}

function widget_recent_comments_style() {
?>
<style type="text/css">.recentcomments a{display:inline !important;padding: 0 !important;margin: 0 !important;}</style>
<?php
}

function widget_recent_comments_register() {
	register_sidebar_widget('Recent Comments', 'widget_recent_comments');
	register_widget_control('Recent Comments', 'widget_recent_comments_control', 300, 90);

	if ( is_active_widget('widget_recent_comments') )
		add_action('wp_head', 'widget_recent_comments_style');
}

function widget_rss($args, $number = 1) {
	require_once(ABSPATH . WPINC . '/rss-functions.php');
	extract($args);
	$options = get_option('widget_rss');
	$num_items = (int) $options[$number]['items'];
	$show_summary = $options[$number]['show_summary'];
	if ( empty($num_items) || $num_items < 1 || $num_items > 10 ) $num_items = 10;
	$url = $options[$number]['url'];
	if ( empty($url) )
		return;
	while ( strstr($url, 'http') != $url )
		$url = substr($url, 1);
	$rss = fetch_rss($url);
	$link = wp_specialchars(strip_tags($rss->channel['link']), 1);
	while ( strstr($link, 'http') != $link )
		$link = substr($link, 1);
	$desc = wp_specialchars(strip_tags(html_entity_decode($rss->channel['description'], ENT_QUOTES)), 1);
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = htmlentities(strip_tags($rss->channel['title']));
	if ( empty($title) )
		$title = $desc;
	if ( empty($title) )
		$title = "Unknown Feed";
	$url = wp_specialchars(strip_tags($url), 1);
	if ( file_exists(dirname(__FILE__) . '/rss.png') )
		$icon = str_replace(ABSPATH, get_settings('siteurl').'/', dirname(__FILE__)) . '/rss.png';
	else
		$icon = get_settings('siteurl').'/wp-includes/images/rss.png';
	$title = "<a class='rsswidget' href='$url' title='Syndicate this content'><img width='14' height='14' src='$icon' alt='RSS' /></a> <a class='rsswidget' href='$link' title='$desc'>$title</a>";
?>
		<?php echo $before_widget; ?>
			<?php $title ? print($before_title . $title . $after_title) : null; ?>
			<ul>
<?php
	if ( $rss ) {
		$rss->items = array_slice($rss->items, 0, $num_items);
		foreach ($rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = wp_specialchars(strip_tags($item['link']), 1);
			$title = wp_specialchars(strip_tags($item['title']), 1);
			if ( empty($title) )
				$title = __('Untitled');
			if ( $show_summary ) {
				$desc = '';
				$summary = '<div class="rssSummary">' . $item['description'] . '</div>';
			} else {
				$desc = str_replace(array("\n", "\r"), ' ', wp_specialchars(strip_tags(html_entity_decode($item['description'], ENT_QUOTES)), 1));
				$summary = '';
			}
			echo "<li><a class='rsswidget' href='$link' title='$desc'>$title</a>$summary</li>";
		}
	} else {
		echo "<li>An error has occured; the feed is probably down. Try again later.</li>";
	}
?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_rss_control($number) {
	$options = $newoptions = get_option('widget_rss');
	if ( $_POST["rss-submit-$number"] ) {
		$newoptions[$number]['items'] = (int) $_POST["rss-items-$number"];
		$newoptions[$number]['url'] = strip_tags(stripslashes($_POST["rss-url-$number"]));
		$newoptions[$number]['title'] = trim(strip_tags(stripslashes($_POST["rss-title-$number"])));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_rss', $options);
	}
	$url = htmlspecialchars($options[$number]['url'], ENT_QUOTES);
	$items = (int) $options[$number]['items'];
	$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
	if ( empty($items) || $items < 1 ) $items = 10;
?>
			<p style="text-align:center;">Enter the RSS feed URL here:</p>
			<input style="width: 400px;" id="rss-url-<?php echo "$number"; ?>" name="rss-url-<?php echo "$number"; ?>" type="text" value="<?php echo $url; ?>" />
			<p style="text-align:center;">Give the feed a title (optional):</p>
			<input style="width: 400px;" id="rss-title-<?php echo "$number"; ?>" name="rss-title-<?php echo "$number"; ?>" type="text" value="<?php echo $title; ?>" />
			<p style="text-align:center; line-height: 30px;"><?php _e('How many items would you like to display?'); ?> <select id="rss-items-<?php echo $number; ?>" name="rss-items-<?php echo $number; ?>"><?php for ( $i = 1; $i <= 10; ++$i ) echo "<option value='$i' ".($items==$i ? "selected='selected'" : '').">$i</option>"; ?></select></p>
			<input type="hidden" id="rss-submit-<?php echo "$number"; ?>" name="rss-submit-<?php echo "$number"; ?>" value="1" />
<?php
}

function widget_rss_setup() {
	$options = $newoptions = get_option('widget_rss');
	if ( isset($_POST['rss-number-submit']) ) {
		$number = (int) $_POST['rss-number'];
		if ( $number > 9 ) $number = 9;
		if ( $number < 1 ) $number = 1;
		$newoptions['number'] = $number;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_rss', $options);
		widget_rss_register($options['number']);
	}
}

function widget_rss_page() {
	$options = $newoptions = get_option('widget_rss');
?>
	<div class="wrap">
		<form method="post" action="">
			<h2>RSS Feed Widgets</h2>
			<p style="line-height: 30px;"><?php _e('How many RSS widgets would you like?'); ?>
			<select id="rss-number" name="rss-number" value="<?php echo $options['number']; ?>">
<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
			</select>
			<span class="submit">
			<input type="submit" name="rss-number-submit" id="rss-number-submit" value="<?php _e('Save'); ?>" /></span></p>
		</form>
	</div>
<?php
}

function widget_rss_register() {
	$options = get_option('widget_rss');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = array('RSS %s', null, $i);
		register_sidebar_widget($name, $i <= $number ? 'widget_rss' : /* unregister */ '', $i);
		register_widget_control($name, $i <= $number ? 'widget_rss_control' : /* unregister */ '', 410, 200, $i);
	}
	add_action('sidebar_admin_setup', 'widget_rss_setup');
	add_action('sidebar_admin_page', 'widget_rss_page');

	if ( is_active_widget('widget_rss') )
		add_action('wp_head', 'widget_rss_head');
}

function widget_rss_head() {
?>
<style type="text/css">a.rsswidget{display:inline !important;}a.rsswidget img{background:orange;color:white;}</style>
<?php
}
/////////////////////////////////////////////////////////// Actions and Registrations

widget_text_register();
widget_rss_register();
widget_recent_comments_register();
register_sidebar_widget('Pages', 'widget_pages');
register_widget_control('Pages', 'widget_pages_control', 300, 90);
register_sidebar_widget('Calendar', 'widget_calendar');
register_sidebar_widget('Archives', 'widget_archives');
register_widget_control('Archives', 'widget_archives_control', 300, 90);
register_sidebar_widget('Links', 'widget_links');
register_sidebar_widget('Meta', 'widget_meta');
register_widget_control('Meta', 'widget_meta_control', 300, 90);
register_sidebar_widget('Search', 'widget_search');
register_sidebar_widget('Categories', 'widget_categories');
register_widget_control('Categories', 'widget_categories_control', 300, 150);
register_sidebar_widget('Recent Posts', 'widget_recent_entries');
?>
