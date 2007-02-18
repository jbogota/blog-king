<?php

/*
Plugin Name: Search Excerpt
Plugin URI: http://scott.yang.id.au/2005/08/search-excerpt-wordpress-plugin/
Description: Modify <code>the_excerpt()</code> template code during search to return snippets containing the search phrase. Snippet extraction code stolen from <a href="http://drupal.org/">Drupal</a>'s search module.
Version: 1.0
Author: Scott Yang
Author URI: http://scott.yang.id.au/

*/

function ylsy_search_excerpt($text) {
    static $filter_deactivated = false;
    global $more;
    global $wp_query;

    // If we are not in a search - simply return the text unmodified.
    if (!is_search())
        return $text;

    // Deactivating some of the excerpt text.
    if (!$filter_deactivated) {
        remove_filter('the_excerpt', 'wpautop');
        $filter_deactivated = true;
    }

    // Get the whole document, not just the teaser.
    $more = 1;
    $result = get_the_content();

    return _search_excerpt($wp_query->query_vars['s'], $result);
}

function _truncate_utf8($string, $len, $wordsafe=false) {
    $slen = strlen($string);
    if ($slen <= $len)
        return $string;
    if ($wordsafe)
        while (($string[--$len] != ' ') && ($len > 0)) {};
    if ((ord($string[$len]) < 0x80) || (ord($string[$len]) >= 0xC0))
        return substr($string, 0, $len);
    while (ord($string[--$len]) < 0xC0) {};
    return substr($string, 0, $len);
}

function _search_excerpt($keys, $text) {
    $keys = _search_keywords_split($keys);
    $text = strip_tags($text);

    for ($i = 0; $i < sizeof($keys); $i ++)
        $keys[$i] = preg_quote($keys[$i], '/');

    $workkeys = $keys;

    // Extract a fragment per keyword for at most 4 keywords.
    // First we collect ranges of text around each keyword, starting/ending
    // at spaces.
    // If the sum of all fragments is too short, we look for second occurrences.
    $ranges = array();
    $included = array();
    $length = 0;
    while ($length < 256 && count($workkeys)) {
        foreach ($workkeys as $k => $key) {
            if (strlen($key) == 0) {
                unset($workkeys[$k]);
                continue;
            }
            if ($length >= 256) {
                break;
            }
            // Remember occurrence of key so we can skip over it if more occurrences
            // are desired.
            if (!isset($included[$key])) {
                $included[$key] = 0;
            }
            if (preg_match('/'.$key.'/iu', $text, $match, PREG_OFFSET_CAPTURE, $included[$key])) {
                $p = $match[0][1];
                if (($q = strpos($text, ' ', max(0, $p - 60))) !== false) {
                    $end = substr($text, $p, 80);
                    if (($s = strrpos($end, ' ')) !== false) {
                        $ranges[$q] = $p + $s;
                        $length += $p + $s - $q;
                        $included[$key] = $p + 1;
                    } else {
                        unset($workkeys[$k]);
                    }
                } else {
                    unset($workkeys[$k]);
                }
            } else {
                unset($workkeys[$k]);
            }
        }
    }

    // If we didn't find anything, return the beginning.
    if (sizeof($ranges) == 0)
        return _truncate_utf8($text, 256) . '&nbsp;...';

    // Sort the text ranges by starting position.
    ksort($ranges);

    // Now we collapse overlapping text ranges into one. The sorting makes it O(n).
    $newranges = array();
    foreach ($ranges as $from2 => $to2) {
        if (!isset($from1)) {
            $from1 = $from2;
            $to1 = $to2;
            continue;
        }
        if ($from2 <= $to1) {
            $to1 = max($to1, $to2);
        } else {
            $newranges[$from1] = $to1;
            $from1 = $from2;
            $to1 = $to2;
        }
    }
    $newranges[$from1] = $to1;

    // Fetch text
    $out = array();
    foreach ($newranges as $from => $to)
        $out[] = substr($text, $from, $to - $from);

    $text = (isset($newranges[0]) ? '' : '...&nbsp;').
        implode('&nbsp;...&nbsp;', $out).'&nbsp;...';
    $text = preg_replace('/('.implode('|', $keys) .')/iu', '<strong>\0</strong>', $text);
    return "<p>$text</p>";
}

function _search_keywords_split($text) {
    static $last = null;
    static $lastsplit = null;

    if ($last == $text)
        return $lastsplit;

    // The dot, underscore and dash are simply removed. This allows meaningful
    // search behaviour with acronyms and URLs.
    $text = preg_replace('/[._-]+/', '', $text);

    // Process words
    $words = explode(' ', $text);

    // Save last keyword result
    $last = $text;
    $lastsplit = $words;

    return $words;
}

add_filter('get_the_excerpt', 'ylsy_search_excerpt');

?>