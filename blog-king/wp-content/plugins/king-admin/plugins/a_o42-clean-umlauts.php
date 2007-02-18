<?php
/*
Plugin Name: o42-clean-umlauts
Plugin URI: http://otaku42.de/2005/06/30/plugin-o42-clean-umlauts/
Description: Das Plugin konvertiert die deutschen Umlaute in den Beitragstiteln, Kommentaren und Feeds zu ASCII. - Aus &auml;,&uuml;,&ouml;,&szlig; wird ein ae, ue, oe und ss. auf der L&ouml;sung von <a href="http://www.papascott.de">Scott Hanson</a>. Das Plugin wirkt sich nur aus, wenn bei der Permalinstruktur "<em>Basierend auf Datum und Name</em>" aktiviert ist.
Version: 0.2.0
Author: Michael Renzmann
Author URI: http://otaku42.de/

This plugin is heavily based on the "German Permalinks" plugin from Scott Hanson.
See also: http://codex.wordpress.org/Plugins/GermanPermalinks
*/

// input
$o42_cu_chars['in'] = array(
    chr(196), chr(228), chr(214), chr(246), chr(220), chr(252), chr(223)
);
$o42_cu_chars['ecto'] = array(
    'Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'
);
$o42_cu_chars['utf8'] = array(
    utf8_encode('Ä'), utf8_encode('ä'), utf8_encode('Ö'), utf8_encode('ö'),
    utf8_encode('Ü'), utf8_encode('ü'), utf8_encode('ß')
);
$o42_cu_chars['perma'] = array(
    'Ae', 'ae', 'Oe', 'oe', 'Ue', 'ue', 'ss'
);

// output
$o42_cu_chars['post'] = array(
    'Ä', 'ä', 'Ö', 'ö', 'Uuml;', 'ü', 'ß'
);
$o42_cu_chars['feed'] = array(
    '&#196;', '&#228;', '&#214;', '&#246;', '&#220;', '&#252;', '&#223;'
);

function o42_cu_permalinks($title) {
    global $o42_cu_chars;
    
    if (seems_utf8($title)) {
	$invalid_latin_chars = array(chr(197).chr(146) => 'OE', chr(197).chr(147) => 'oe', chr(197).chr(160) => 'S', chr(197).chr(189) => 'Z', chr(197).chr(161) => 's', chr(197).chr(190) => 'z', chr(226).chr(130).chr(172) => 'E');
	$title = utf8_decode(strtr($title, $invalid_latin_chars));
    }
    
    $title = str_replace($o42_cu_chars['ecto'], $o42_cu_chars['perma'], $title);
    $title = str_replace($o42_cu_chars['in'], $o42_cu_chars['perma'], $title);
    $title = sanitize_title_with_dashes($title);
    return $title;
}

function o42_cu_content($content) {
    global $o42_cu_chars;

    if (strtoupper(get_option('blog_charset')) == 'UTF-8') {
	$content = str_replace($o42_cu_chars['utf8'], $o42_cu_chars['feed'], $content);
    }
    $content = str_replace($o42_cu_chars['ecto'], $o42_cu_chars['feed'], $content);
    $content = str_replace($o42_cu_chars['in'], $o42_cu_chars['feed'], $content);

    return $content;
}

/* enable cleaning of permalinks */
remove_filter('sanitize_title', 'sanitize_title_with_dashes');
add_filter('sanitize_title', 'o42_cu_permalinks');

/* enable cleaning of feeds and posts */
add_filter('the_excerpt', 'o42_cu_content');
add_filter('the_excerpt_rss', 'o42_cu_content');
add_filter('the_content', 'o42_cu_content');
add_filter('the_title_rss', 'o42_cu_content');
add_filter('the_title', 'o42_cu_content');
add_filter('comment_text_rss', 'o42_cu_content');
add_filter('comment_text', 'o42_cu_content');

?>
