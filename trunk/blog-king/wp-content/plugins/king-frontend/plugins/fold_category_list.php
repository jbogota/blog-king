<?php
/*
	Plugin Name: Fold Category List
	Version: 1.0b4
	Plugin URI: http://www.webspaceworks.com/resources/cat/wp-plugins/31/
	Description: Provides PHP functions to display a folding category tree
	Author: Rob Schumann
	Author URI: http://www.webspaceworks.com/
*/
/*
	v1.0b4: Feature enhancement [28 January, 2006]
		Added wswwpx_category_description and the ability to specify a truncated description for use in category link title text (tooltips)
	v1.0b3: Bug fix & enhancement [5 January, 2006]
		Internal changes for better compatibility with WP Core, especially for WP2.0 & revised permalinks system
		Added _wswwpx_category_get_name function to obtain the name (title) for a specific category upon request.


	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/



/*
 * _wswwpx_category_get_id
 *	 Converts from cat_name input to ID output
 *  - $cat is the identifier for a category.
 *			If a non-numeric string, find the corresponding ID for the category.
 *			If it's numeric, we keep it as the wanted value
 *			If neither criterion is met, return as zero
 *  - returns the ID of the given category
 */
function _wswwpx_category_get_id ( $cat = '' ) {
	global $wpdb;
	// Make sure there is a cat identifier to process
	if ( !is_numeric($cat) && strlen($cat) > 0 ) {
		//
		//	This next bit to prevent SQL insertion attacks through the argument list.
		//		Breaks on the first semi-colon encountered and discards the trailing part.
		//		Then strips any trailing '/' character
		//
		$cats = explode(';', $cat, 2);
		$cats = explode('/', $cats[0]);
		$n = count($cats);
		$cats = array_reverse(array_slice($cats, 0, $n-1));
		$result = $wpdb->get_var("
									SELECT cat_ID
										FROM $wpdb->categories
										WHERE category_nicename = '{$cats[0]}'");
	} else if (is_numeric($cat)) {
		// ... Keep an existing numeric value
		$result = $cat;
	} else {
		// ... or set a zero result.
		$result = 0;
	}
	//
	return $result;
}

/*
 * _wswwpx_category_get_parent_id
 *  - $child is the ID of a category
 *  - returns the ID of the parent of the given category
 */
function _wswwpx_category_get_parent_id ( $child = 0 ) {
	global $wpdb;
	// Make sure there is a child ID to process
	if ( is_numeric($child) && $child > 0 ) {
		$result = $wpdb->get_var("
									SELECT category_parent
										FROM $wpdb->categories
										WHERE cat_ID = $child");
	} else {
		// ... or set a zero result.
		$result = 0;
	}
	//
	return $result;
}
/*
 * _wswwpx_category_get_name
 *  - $cid is the ID of a category
 *  - returns the name of the given category
 */
function _wswwpx_category_get_name ( $cid = 0 ) {
	global $wpdb;
	// Make sure there is a child ID to process
	if ( is_numeric($cid) && $cid > 0 ) {
		$result = $wpdb->get_var("
									SELECT cat_name
										FROM $wpdb->categories
										WHERE cat_ID = $cid");
	} else {
		// ... or set a null result.
		$result = NULL;
	}
	//
	return $result;
}

/*
 * get_ancestor_ids
 *  - $child is the ID of a category
 *  - returns an array of IDs of all ancestors of the requested category
 *  - default sort order is top down.
 */

function _wswwpx_category_get_ancestor_ids ( $child = 0, $inclusive=true, $topdown=true ) {
	//
	//	Make sure we are dealing with a $child that is a numeric ID ID and not a string cat_name
	//	Convert as necessary
	//
	$child = _wswwpx_category_get_id ($child);
	//
	//	And start processing
	//
	if ( $child && $inclusive ) $ancestors[] = $child;
 	while ($parent = _wswwpx_category_get_parent_id ( $child ) ) {
 		$ancestors[] = $parent;
 		$child = $parent;
 	}
 	//	If there are ancestors, test for resorting, and apply
 	if ($ancestors && $topdown) krsort($ancestors);
	if ( !$ancestors ) $ancestors[] = 0;
 	//
 	return $ancestors;
 }

/*
 * _wswwpx_category_get_child_ids
 *  - $parent is the ID of the parent category
 *  - returns an associative array containing the IDs of the children
 *    of the parent category
 */
function _wswwpx_category_get_child_ids ( $parent = 0 ) {
	global $wpdb;
	if ( is_numeric($parent) && $parent > 0 ) {
		// Get the ID of the parent.

		$result = $wpdb->get_results("
	    							SELECT cat_ID
	     								 FROM $wpdb->categories
	     								 WHERE category_parent = $parent");
	} else {
		// ... or set a zero result.
		$pages = get_pages();
		foreach ($pages AS $page) {
			$result[]=$page->ID;
		}
//		$result = 0;
	}
	//
	return $result;
}

 /*
  * _wswwpx_category_get_descendant_ids
  *  - $parent is the ID of a category
  *  - $inclusive is a switch determining whether the parent ID is included in the returned array. Defaults TRUE
  *  - returns an array of IDs of all descendents of the requested category
  */
function _wswwpx_category_get_descendant_ids ( $parent = 0, $inclusive=true ) {
 	if ( $parent && $inclusive ) $descendants[] = $parent;
 	if ( $children = _wswwpx_category_get_child_ids ( $parent ) ) {
 		foreach ( $children as $child ) {
 			$descendants[] = $child;
 			$grandchildren = _wswwpx_category_get_child_ids ( $child );
 		}
 	}
 	//
 	return $descendants;
 }

/*	F R O N T   E N D   Functions
 *-----------------------------------------------------------------------
 *	The following are taken from WP itself, and modified.
 * Modifed versions of:
 *		wp_list_cats: wswwpx_fold_category_list
 *		list_cats:    wswwpx_list_cats
 *
 *	Original comments from WP are left in place
 *
 */
   function wswwpx_fold_category_list ($args = '') {
   	parse_str($args, $r);
   	if (!isset($r['optionall'])) $r['optionall'] = 0;
       if (!isset($r['all'])) $r['all'] = 'All';
   	if (!isset($r['sort_column'])) $r['sort_column'] = 'ID';
   	if (!isset($r['sort_order'])) $r['sort_order'] = 'asc';
   	if (!isset($r['file'])) $r['file'] = '';
   	if (!isset($r['list'])) $r['list'] = true;
   	if (!isset($r['optiondates'])) $r['optiondates'] = 0;
   	if (!isset($r['optioncount'])) $r['optioncount'] = 0;
   	if (!isset($r['hide_empty'])) $r['hide_empty'] = 1;
   	if (!isset($r['use_desc_for_title'])) $r['use_desc_for_title'] = 1;
   	if (!isset($r['children'])) $r['children'] = true;
   	if (!isset($r['child_of'])) $r['child_of'] = 0;
   	if (!isset($r['categories'])) $r['categories'] = 0;
   	if (!isset($r['recurse'])) $r['recurse'] = 0;
   	if (!isset($r['feed'])) $r['feed'] = '';
   	if (!isset($r['feed_image'])) $r['feed_image'] = '';
   	if (!isset($r['exclude'])) $r['exclude'] = '';
   	if (!isset($r['hierarchical'])) $r['hierarchical'] = true;
// WSW Extras
   	if (isset($r['cut_desc'])) $cut_desc = $r['cut_desc'];

   	wswwpx_list_cats($r['optionall'], $r['all'], $r['sort_column'], $r['sort_order'], $r['file'],	$r['list'], $r['optiondates'], $r['optioncount'], $r['hide_empty'], $r['use_desc_for_title'], $r['children'], $r['child_of'], $r['categories'], $r['recurse'], $r['feed'], $r['feed_image'], $r['exclude'], $r['hierarchical'], $cut_desc);
   }


   function wswwpx_list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=FALSE, $child_of=0, $categories=0, $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=FALSE, $cut_desc='') {

   	global $wpdb, $category_posts, $wp_query,$myLastCat;
$myLastCat=$current_cat;
//	Added for folding functionality... fix for permalink compatibility and modified after further suggestion from Laurence O.
		if (is_category()) {
			$current_cat = $wp_query->get_queried_object_id();
			$all_ancestors = _wswwpx_category_get_ancestor_ids($current_cat);
		}elseif(is_single()) {
			//
			//	Default to zero for all other cases.
			//$current_cat = $myLastCat;
		   //	$category->cat_ID = $myLastCat;
		$all_ancestors = _wswwpx_category_get_ancestor_ids($myLastCat);

		}else {
			//
			//	Default to zero for all other cases.
			//$current_cat = $myLastCat;
		   //	$category->cat_ID = $myLastCat;
		$all_ancestors[] = 0;

		}
/*//	Old version... replaced by above for greater compatibility with WP CORE
		if (isset($_GET['category_name'])) {
			$all_ancestors = _wswwpx_category_get_ancestor_ids($_GET['category_name']);
		} else if (isset($_GET['cat'])) {
			$all_ancestors = _wswwpx_category_get_ancestor_ids($_GET['cat']);
		} else {
			//
			//	Default to zero for all other cases.
			//
			$all_ancestors[] = 0;
		} */
//	End add
   	// Optiondates now works
   	if ('' == $file) {
   		$file = get_settings('home') . '/';
   	}

   	$exclusions = '';
   	if (!empty($exclude)) {
   		$excats = preg_split('/[\s,]+/',$exclude);
   		if (count($excats)) {
   			foreach ($excats as $excat) {
   				$exclusions .= ' AND cat_ID <> ' . intval($excat) . ' ';
   			}
   		}
   	}

   	$exclusions = apply_filters('list_cats_exclusions', $exclusions);

   	if (intval($categories)==0){
   		$sort_column = 'cat_'.$sort_column;

   		$query  = "
   			SELECT cat_ID, cat_name, category_nicename, category_description, category_parent
   			FROM $wpdb->categories
   			WHERE cat_ID > 0 $exclusions
   			ORDER BY $sort_column $sort_order";

   		$categories = $wpdb->get_results($query);
   	}
   	if (!count($category_posts)) {
   		$now = current_time('mysql', 1);
   		$cat_counts = $wpdb->get_results("	SELECT cat_ID,
   		COUNT($wpdb->post2cat.post_id) AS cat_count
   		FROM $wpdb->categories 
   		INNER JOIN $wpdb->post2cat ON (cat_ID = category_id)
   		INNER JOIN $wpdb->posts ON (ID = post_id)
   		WHERE post_status = 'publish'
   		AND post_date_gmt < '$now' $exclusions
   		GROUP BY category_id");
           if (! empty($cat_counts)) {
               foreach ($cat_counts as $cat_count) {
                   if (1 != intval($hide_empty) || $cat_count > 0) {
                       $category_posts["$cat_count->cat_ID"] = $cat_count->cat_count;
                   }
               }
           }
   	}
   	
   	if ( $optiondates ) {
   		$cat_dates = $wpdb->get_results("	SELECT category_id,
   		UNIX_TIMESTAMP( MAX(post_date) ) AS ts
   		FROM $wpdb->posts, $wpdb->post2cat
   		WHERE post_status = 'publish' AND post_id = ID $exclusions
   		GROUP BY category_id");
   		foreach ($cat_dates as $cat_date) {
   			$category_timestamp["$cat_date->category_id"] = $cat_date->ts;
   		}
   	}
   	
   	$num_found=0;
   	$thelist = "";
   	
   	foreach ($categories as $category) {
   		if ((intval($hide_empty) == 0 || isset($category_posts["$category->cat_ID"])) && (!$hierarchical || $category->category_parent == $child_of) ) {
   			$num_found++;
   			$link = '<a href="'.get_category_link($category->cat_ID).'" ';
   			if ($use_desc_for_title == 0 || empty($category->category_description)) {
   				$link .= 'title="'. sprintf(__("View all posts filed under %s"), wp_specialchars($category->cat_name)) . '"';
   			} else {
//
//	WSW change to allow for truncated descriptions within link titles.
//
//   				$link .= 'title="' . wp_specialchars(apply_filters('category_description',$category->category_description,$category)) . '"';
					$link .= 'title="' . wp_specialchars(wswwpx_category_description($category, $cut_desc, 0)) . '"';
   			}
   			$link .= '>';
   			$link .= apply_filters('list_cats', $category->cat_name, $category).'</a>';

   			if ( (! empty($feed_image)) || (! empty($feed)) ) {
   				
   				$link .= ' ';

   				if (empty($feed_image)) {
   					$link .= '(';
   				}

   				$link .= '<a href="' . get_category_rss_link(0, $category->cat_ID, $category->category_nicename)  . '"';

   				if ( !empty($feed) ) {
   					$title =  ' title="' . $feed . '"';
   					$alt = ' alt="' . $feed . '"';
   					$name = $feed;
   					$link .= $title;
   				}

   				$link .= '>';

   				if (! empty($feed_image)) {
   					$link .= "<img src='$feed_image' $alt$title" . ' />';
   				} else {
   					$link .= $name;
   				}
   				
   				$link .= '</a>';

   				if (empty($feed_image)) {
   					$link .= ')';
   				}
   			}

   			if (intval($optioncount) == 1) {
   				$link .= ' ('.intval($category_posts["$category->cat_ID"]).')';
   			}
   			if ( $optiondates ) {
   				if ( $optiondates == 1 ) $optiondates = 'Y-m-d';
   				$link .= ' ' . gmdate($optiondates, $category_timestamp["$category->cat_ID"]);
   			}
   			if ($list) {
				$css_class = 'page_item';

/////////////
//if(!empty ($childs))  $category_posts["$category->cat_ID"]
				if ($current_cat == $category->cat_ID) {
					$css_class .= ' current_page_item';

			  	   //	setcookie("wpLastCategory", $current_cat);
				} else if (in_array($category->cat_ID, $all_ancestors) ) {
					$css_class .= ' current_page_ancestor';
				}

//////////////
			$thelist .= "<li class=\"".$css_class."\" >$link"; //added class by Georg Leciejewski

			} else {
   				$thelist .= "$link<br />\n";
   			}

//	Extra 'if' added for folding functionality recursion um children anzuhängen
   			if (in_array($category->cat_ID, $all_ancestors)) {

	   			if ($hierarchical && $children) $thelist .= wswwpx_list_cats($optionall, $all, $sort_column, $sort_order, $file, $list, $optiondates, $optioncount, $hide_empty, $use_desc_for_title, $hierarchical, $category->cat_ID, $categories, 1, $feed, $feed_image, $exclude, $hierarchical);
			
				}
//	End add
   			if ($list) $thelist .= "</li>\n";
   		}
   	}

   	if (!$num_found && !$child_of){
   		if ($list) {
   			$before = '<li>';
   			$after = '</li>';
   		}
   		echo $before . __("No categories") . $after . "\n";
   		return;
   	}
   	if ($list && $child_of && $num_found && $recurse) {
	
   		$pre = "\n<ul>\n"; // changed from childeren to class current_page_item georg leciejewski
   		$post = "</ul>\n";
   	} else {
   		$pre = $post = '';
   	}
   	$thelist = $pre . $thelist . $post;
   	if ($recurse) {
   		return $thelist;
   	}
   	echo apply_filters('list_cats', $thelist);
   }
   //
   //	Function to optionally cut the category description into two pieces, at a specified cut-mark, and to return the specified part.
   //
   function wswwpx_category_description($category = 0, $cut_at='', $fetch=0) {
   	global $cat;
   	if (!$category) $category = $cat;
   	if (is_numeric($category))$category = & get_category($category);

   	if ( strlen($cut_at)>0 ) {
   		$desc = explode($cut_at, apply_filters('category_description', $category->category_description, $category->cat_ID), 2);
   		$desc = $desc[$fetch];
   	} else {
   		$desc = apply_filters('category_description', $category->category_description, $category->cat_ID);
   	}
   	return $desc;
   }

?>