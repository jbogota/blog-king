<?php
/*
	Plugin Name: Fold Page List
	Version: 1.1
	Plugin URI: http://www.webspaceworks.com/resources/cat/wp-plugins/30/
	Description: Provides PHP functions to display a folding page tree
	Author: Rob Schumann
	Author URI: http://www.webspaceworks.com/
*/
/*
	v1.1: New features/bugfix [11 December, 2005]
			Added new css class (current_page_ancestor) to allow for separate identification of current item from its ancestor trail.
			Adds new argument (true/false) to enable full unfolding (to a limit of 'depth') of a section. Default FALSE.
			Bugfix to internal function _wswwpx_page_get_child_ids to correctly return array of child ids

	Copyright (c) 2005  Rob Schumann  (email : robs_wp@webspaceworks.com)
	Released under the GPL license
	http://www.gnu.org/licenses/gpl.txt

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

function _wswwpx_page_get_parent_id ( $child = 0 ) {
	global $wpdb;
	// Make sure there is a child ID to process
	if ( $child > 0 ) {
		$result = $wpdb->get_var("
									SELECT post_parent
										FROM $wpdb->posts
										WHERE ID = $child");
	} else {
		// ... or set a zero result.
		$result = 0;
	}
	//
	return $result;
}
/*
 * page_get_child_ids
 *  - $parent the ID of the parent page
 *  - returns an array containing the IDs of the children
 *    of the parent page
 */
function _wswwpx_page_get_child_ids ( $parent = 0 ) {
	global $wpdb;
	if ( $parent > 0 ) {
		// Get the ID of the parent.

		$results = $wpdb->get_results("
	    							SELECT ID
	     								 FROM $wpdb->posts
	     								 WHERE post_parent = $parent", ARRAY_N );
 		if ($results) {
			foreach ($results AS $r) {
			 	foreach ($r AS $v) {
			 		$result[] = $v;
			 	}
			 }
		} else {
			$result = false;
		}
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
 * page_get_ancestor_ids
 *  - $child is the ID of a page
 *  - returns an array of IDs of all ancestors of the requested page
 *  - default sort order is top down.
 */
function _wswwpx_page_get_ancestor_ids ( $child = 0, $inclusive=true, $topdown=true ) {
 	if ( $child && $inclusive ) $ancestors[] = $child;
 	while ($parent = _wswwpx_page_get_parent_id ( $child ) ) {
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
  * page_get_descendant_ids
  *  - $parent is the ID of a page
  *  - $inclusive is a switch determining whether the parent ID is included in the returned array. Defaults TRUE
  *  - returns an array of IDs of all descendents of the requested page
  */
function _wswwpx_page_get_descendant_ids ( $parent = 0, $inclusive=true ) {
 	if ( $parent && $inclusive ) $descendants[] = $parent;
 	if ( $children = _wswwpx_page_get_child_ids ( $parent ) ) {
 		foreach ( $children as $child ) {
 			$descendants[] = $child;
 			$grandchildren = _wswwpx_page_get_child_ids ( $child );
 		}
 	}
 	//
 	return $descendants;
 }

/*
 * page_get_custom_linktitle
 *  - $child is the ID of a page
 *  - returns the ID of the parent of the given page
 */
function _wswwpx_page_get_custom_linktitle ( $page = 0 ) {
	global $wpdb;
	// Make sure there is a page ID to process
	if ( $page > 0 ) {
		$result = $wpdb->get_var("
									SELECT meta_value
										FROM $wpdb->postmeta
										WHERE post_id = $page AND meta_key = 'wswpg_linkttl'");
	} else {
		// ... or set a zero result.
		$result = false;
	}
	//
	return $result;
}

/*
 *
 *	The following are taken from WP itself, and modified.
 * Modifed versions of:
 *		wp_list_pages:   tree_list_pages
 *		_page_level_out: _tree_sublevels_out
 *
 *	Original comments from WP are left in place
 *
 */
function wswwpx_fold_page_list ($args = '', $fullunfold=false) {
	parse_str($args, $r);
	if (!isset($r['depth'])) $r['depth'] = 0;
	if (!isset($r['show_date'])) $r['show_date'] = '';
	if (!isset($r['child_of'])) $r['child_of'] = 0;
	if ( !isset($r['title_li']) ) $r['title_li'] = __('Pages');

	// Query pages.
	$pages = get_Pages($args);
	if ( $pages ) :

	if ( $r['title_li'] )
		echo '<li id="pagenav">' . $r['title_li'] . '<ul>';
	// Now loop over all pages that were selected
	$page_tree = Array();
	foreach($pages as $page) {
		// set the title for the current page
//
// 09-11-2005: Replaced to fix polyglot plugin by adding apply_filters
//	  		$page_tree[$page->ID]['title'] = $page->post_title;
		$page_tree[$page->ID]['title'] = apply_filters('the_title', $page->post_title);
		$page_tree[$page->ID]['name'] = $page->post_name;
		//
		//	Get custom link title for anchor 'title' attribute - Added 14 August, 2005
		//
		if ( $linkttl = _wswwpx_page_get_custom_linktitle ( $page->ID ) ) $page_tree[$page->ID]['linkttl'] = $linkttl;

		// set the selected date for the current page
		// depending on the query arguments this is either
		// the creation date or the modification date
		// as a unix timestamp. It will also always be in the
		// ts field.
		if (! empty($r['show_date'])) {
			if ('modified' == $r['show_date'])
				$page_tree[$page->ID]['ts'] = $page->time_modified;
			else
				$page_tree[$page->ID]['ts'] = $page->time_created;
		}

		// The tricky bit!!
		// Using the parent ID of the current page as the
		// array index we set the current page as a child of that page.
		// We can now start looping over the $page_tree array
		// with any ID which will output the page links from that ID downwards.
		$page_tree[$page->post_parent]['children'][] = $page->ID;
	}
	// Output of the pages starting with child_of as the root ID.
	// child_of defaults to 0 if not supplied in the query.
	//	** Modified: Will only expand to show sub-pages for the current page
	_wswwpx_tree_sublevels_out($r['child_of'],$page_tree, $r, 0, $fullunfold);
	if ( $r['title_li'] )
		echo '</ul></li>';
	endif;
}

function _wswwpx_tree_sublevels_out($parent, $page_tree, $args, $depth = 0, $fullunfold=false) {
	global $wp_query;
	
	$queried_obj = $wp_query->get_queried_object();
	
	if($depth) $indent = str_repeat("\t", $depth);
	$all_ancestors = _wswwpx_page_get_ancestor_ids($queried_obj->ID);
	$n = count($all_ancestors);
	$all_children  = _wswwpx_page_get_descendant_ids($all_ancestors[$n-1]);	// Get all children of the non-zero root parent of current page
	foreach($page_tree[$parent]['children'] as $page_id) {
		$cur_page = $page_tree[$page_id];
		$title = $cur_page['title'];
		//	14-August-2005
		//	Add check for linkttl, and revert to default behaviour if not available...
		// 
		if ( isset($cur_page['linkttl']) ) {
			$linkttl = $cur_page['linkttl'];
		} else {
			$linkttl = $title;
		}
		$queried_page = $queried_obj->ID;
		$css_class = 'page_item';
		if ( $page_id == $queried_obj->ID ) {
			$css_class .= ' current_page_item';
		} else if ( in_array($page_id, $all_ancestors) ) {
			$css_class .= ' current_page_ancestor';
		}
// 14-Aug-2005: Add custom title attribute on anchor tag.
//   		echo $indent . '<li class="' . $css_class . '"><a href="' . get_page_link($page_id) . '" title="' . wp_specialchars($title) . '">' . $title . '</a>';
		echo $indent . '<li class="' . $css_class . '"><a href="' . get_page_link($page_id) . '" title="' . wp_specialchars($linkttl) . '">' . $title . '</a>';

		if(isset($cur_page['ts'])) {
			$format = get_settings('date_format');
			if(isset($args['date_format'])) $format = $args['date_format'];
			echo " " . gmdate($format,$cur_page['ts']);
		}
		echo "\n";
		if (in_array($page_id, $all_ancestors) || (in_array($page_id, $all_children) && $fullunfold && $queried_page != 0 ) ) {
			if(isset($cur_page['children']) && is_array($cur_page['children'])) {
				$new_depth = $depth + 1;
				if(!$args['depth'] || $depth < ($args['depth']-1)) {
					echo "$indent<ul>\n";
					_wswwpx_tree_sublevels_out($page_id, $page_tree, $args, $new_depth, $fullunfold);
					echo "$indent</ul>\n";
				}
			}
		}
		echo "$indent</li>\n";
	}
}
?>