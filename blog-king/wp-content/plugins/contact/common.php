<?php
add_action('wp_head', 'xyooj_wp_head');
function xyooj_wp_head($head_stuff) {
	?>
<script type="text/javascript" src="<?php echo xyooj_get_plugins_url(); ?>/contact/ajax.js"></script>
<?php
}


function xyooj_esc_html($text) {
	$text = str_replace("&", '&amp;', $text);
	$text = str_replace("<", '&lt;', $text);
	$text = str_replace(">", '&gt;', $text);
	return $text;
}
function xyooj_unesc_quote($text, $for_html = false) {
	if ($for_html == true) {
		$text = str_replace('&quot;','"',$text);
		$text = str_replace('&apos;', "'",$text);
	} else {
		//$text = str_replace('&quot;','"',$text);
		$text = str_replace('&apos;', "'",$text);
	}
	return $text;
}
function xyooj_esc_quote($text) {
	$text = str_replace('"',"&quot;",$text);
	$text = str_replace("'","&apos;",$text);
	return $text;
}
function xyooj_get_users() {
	global $wpdb;
	$sql = "SELECT * FROM $wpdb->users ORDER BY display_name";
	return $wpdb->get_results($sql);
}
function xyooj_get_categories() {
	global $wpdb;
	$sql = "SELECT * FROM $wpdb->categories ORDER BY cat_name";
	return $wpdb->get_results($sql);
}
function xyooj_get_wp_url() {
	return get_bloginfo('wpurl');
}
function xyooj_get_plugins_url() {
	return xyooj_get_wp_url().'/wp-content/plugins';
}
?>
