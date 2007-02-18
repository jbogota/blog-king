<?php
/*
Plugin Name: Breadcrumb Navigation XT
Plugin URI: http://www.sw-guide.de/projekte/wordpress-projekte/breadcrumb-nav-xt/
Description: Adds a breadcrumb navigation showing the visitor&#39;s path to their current location. For details on how to use this plugin visit <a href="http://www.sw-guide.de/projekte/wordpress-projekte/breadcrumb-nav-xt/">Breadcrumb Nav XT</a>.
Version: 1.1
Author: Michael Woehrer
Author URI: http://www.sw-guide.de
*/

/*

	© Copyright 2006  Michael Woehrer  (michael dot woehrer at gmail dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    --------------------------------------------------------------------------*/


class breadcrumb_navigation_xt {

/*==============================================================================
    					=== VARIABLES ===
  ============================================================================*/
	
	var $opt;	// array containing the options
	
	
/*==============================================================================
    					=== CONSTRUCTOR ===
  ============================================================================*/
	function breadcrumb_navigation_xt() {
		$this->opt = array(
			// URL for your blog's web site address that is used for the Weblog link. Also, use the URL if your blog may be
			// available at http://your-site.com/myweblog, and on http://your-site.com/ another wordpress page is being displayed.
				'url_blog' => '',
			// URL for the home link
				'url_home' => get_settings('home'),
			// Separator that is placed between each item in the breadcrumb navigation, but not placed before
			// the first and not after the last element. You also can use images here,
			// e.g. <img src="separator.gif" title="separator" width="10" height="8" />
				'separator' => ' / ',
			// Text displayed for the home link, if you don't want to call it home then just change this.
			// Also, it is being checked if the current page title = this variable. If yes, only the Home link is being displayed,
			// but not a weird "Home / Home" breadcrumb.	
				'title_home' => 'Home',
			// Text displayed for the weblog link.
				'title_blog' => 'Startseite',
			// Text displayed for the search page.
				'title_search' => 'Suche',
			// Prefix for a single blog article.
				'singleblogpost_prefix' => '',
			// Suffix for a single blog article.
				'singleblogpost_suffix' => '',
			// Prefix for a page.
				'page_prefix' => '',
			// Suffix for a page.
				'page_suffix' => '',
			// The prefix that is used for mouseover link (e.g.: "Browse to: Archive")
				'urltitle_prefix' => 'Gehe zu: ',
			// The suffix that is used for mouseover link
				'urltitle_suffix' => '',
			// Prefix for categories.
				'archive_category_prefix' => '',
			// Suffix for categories.
				'archive_category_suffix' => '&#39;',
			// Prefix for archive by year/month/day
				'archive_date_prefix' => '',
			// Suffix for archive by year/month/day
				'archive_date_suffix' => '',
			// Apply special title on 404 error page ? I do not use it since I want to display the latest 10 weblog articles in case of 404
				'use404' => true,
			// Text displayed for a 404 error page, , only being used if 'use404' => true
				'title_404' => '404',
			// Display current item as link?
				'link_current_item' => false,
			// URL title of current item, only being used if 'link_current_item' => true
				'current_item_urltitle' => 'Link of current page (click to refresh)',
			// Style or prefix being applied as prefix to current item. E.g. <span class="bc_current">
				'current_item_style_prefix' => '',
			// Style or prefix being applied as suffix to current item. E.g. </span>
				'current_item_style_suffix' => '',
			// Apply a link to HOME? If set to false, only plain text is being displayed.
				'home_link' => true,
			// Display HOME? If set to false, HOME is not being displayed. 
				'home_display' => true,

		);		
	} // END function breadcrumb (constructor)

/*==============================================================================
    				=== DISPLAY BREADCRUMB ===
  ============================================================================*/
	function display() {

		global $wpdb, $post;
	
		////////////////////////////////////////////////////////////////////////
		// Needed links
		////////////////////////////////////////////////////////////////////////
		/* -------- HOME LINK -------- */
		$bcn_homelink = '';
		if ($this->opt['home_display'] === true) {		// Hide HOME if it is disabled in the options
			if ($this->opt['home_link'] === true) {			// Link home or just display text
				$bcn_homelink = '<a href="' . $this->opt['url_home'] . '" title="' . $this->opt['urltitle_prefix'] . $this->opt['title_home'] . $this->opt['urltitle_suffix'] . '">' . $this->opt['title_home'] . '</a>';
			} else {
				$bcn_homelink = $this->opt['title_home'];			
			}
		}
	
		/* -------- BLOG LINK -------- */
		$bcn_bloglink = '<a href="' . get_bloginfo('url') . $this->opt['url_blog'] . '" title="' . $this->opt['urltitle_prefix'] . $this->opt['title_blog'] . $this->opt['urltitle_suffix'] . '">' . $this->opt['title_blog'] . '</a>';

		/* -------- CURRENT ITEM -------- */
		$curitem_urlprefix = '';
		$curitem_urlsuffix = '';
		if ($this->opt['link_current_item']) {
			$curitem_urlprefix = '<a title="' . $this->opt['current_item_urltitle'] . '" href="' . $_SERVER['REQUEST_URI'] . '">';
			$curitem_urlsuffix = '</a>';
		}
		
		////////////////////////////////////////////////////////////////////////
		// Get the different types
		////////////////////////////////////////////////////////////////////////
		if ( is_search() ) 								$swg_type = 'search';		// Search
		elseif ( is_page() ) 							$swg_type = 'page';			// Page
		elseif ( is_single() )							$swg_type = 'singlepost';	// Single post page
		elseif ( is_archive() )							$swg_type = 'blogarchive';	// Weblog Archive
		elseif ( is_404() and $this->opt['use404'])		$swg_type = '404';			// 404
		else											$swg_type = 'else';			// Everything else (should be weblog article list only)
	
	
		/* *************************************************
			Here we set the initial array $result_array. We use this for being able to apply styles, anchors etc. to each element
			Default is set to false.
		************************************************* */
		$result_array = array(
			'middle' => false,	// The part between "Home" and the last element of the breadcrumb.
			'last' => array(	// The last element of the breadcrumb
					'prefix' => false,	// prefix
					'title' => false,	// text
					'suffix' => false	// suffix
				  ) 
			);
	
	
		switch ($swg_type) {
	
		case 'page':
			////////////////////////////////////////////////////////////////////
			// Get Pages
			////////////////////////////////////////////////////////////////////

			$bcn_theparentid = $post->post_parent;	// id of the parent page
			
			$bcn_loopcount = 0;	// counter for the array
			while( 0 != $bcn_theparentid ) {
				// Get the row of the parent's page;
				// 	*** Regarding performance this is not a perfect solution so far since this query is in a loop ! ***
				//		However, the number of queries is reduced to the number of parents.
				$mylink = $wpdb->get_row("SELECT post_title, post_parent FROM $wpdb->posts WHERE ID = '$bcn_theparentid;'");
	
				// Title of parent into array incl. current permalink (via $bcn_theparentid, since we set this variable below we can use it here as current id!)
				$bcn_titlearray[$bcn_loopcount] = '<a href="' . get_permalink($bcn_theparentid) . '" title="' . $this->opt['urltitle_prefix'] . $mylink->post_title . $this->opt['urltitle_suffix'] . '">' . $mylink->post_title . '</a>';
	
				// New parent ID of parent
				$bcn_theparentid = $mylink->post_parent;
	
				$bcn_loopcount++;	
			}	// while
	
			if (is_array($bcn_titlearray)) {
				// Reverse the array since it is in a reverse order 
				$bcn_titlearray = array_reverse($bcn_titlearray);
		
				// Prepare the output by looping thru the array. We use $sep for not adding the separator before the first element
				$count = 0;
				foreach ($bcn_titlearray as $val) {
					$sep = '';
					if (0 != $count)
						$sep = $this->opt['separator'];

					$page_result = $page_result . $sep . $val;
					
					$count++;
				}
			}

			// Result			
			// If we have a front page named 'Home' (or similar), we do not want to display the Breadcrumb like this: Home / Home
			// Therefore do not display the Home Link if such certain page is being displayed.
			if( strtolower($post->post_title) != strtolower($this->opt['title_home']) ) {  // Check if we are not on home
				if ($page_result != '') $result_array['middle'] = $page_result;
				$result_array['last']['prefix'] = $this->opt['page_prefix'];
				$result_array['last']['title'] = $post->post_title;
				$result_array['last']['suffix'] = $this->opt['page_suffix'];
			}

	
			break; // end of case 'page'
	
		case 'search':
			////////////////////////////////////////////////////////////////////
			// Get Search
			////////////////////////////////////////////////////////////////////

			$result_array['last']['title'] = $this->opt['title_search'];
			
			break; // end of case 'search'
	
		case 'singlepost':
			////////////////////////////////////////////////////////////////////
			// Get single blog post
			////////////////////////////////////////////////////////////////////

			$result_array['middle'] = $bcn_bloglink;
			$result_array['last']['prefix'] = $this->opt['singleblogpost_prefix'];
			$result_array['last']['title'] = $post->post_title;
			$result_array['last']['suffix'] = $this->opt['singleblogpost_suffix'];
		
			break;
	
		case 'blogarchive':
			////////////////////////////////////////////////////////////////////
			// Get Blog archive
			////////////////////////////////////////////////////////////////////

			$result_array['middle'] = $bcn_bloglink;
	
			if (is_day()) {
				// -- Archive by day
				$result_array['last']['prefix'] = $this->opt['archive_date_prefix'];
				$result_array['last']['title'] = get_the_time('d') . '. ' . get_the_time('F') . ' ' . get_the_time('Y');
				$result_array['last']['suffix'] = $this->opt['archive_date_suffix'];

			} elseif (is_month()) {
				// -- Archive by month
				$result_array['last']['prefix'] = $this->opt['archive_date_prefix'];
				$result_array['last']['title'] = get_the_time('F') . ' ' . get_the_time('Y');
				$result_array['last']['suffix'] = $this->opt['archive_date_suffix'];
			} else if (is_year()) {
				// -- Archive by year
				$result_array['last']['prefix'] = $this->opt['archive_date_prefix'];
				$result_array['last']['title'] = get_the_time('Y');
				$result_array['last']['suffix'] = $this->opt['archive_date_suffix'];
			} else if (is_category()) {
				// -- Archive by category
				$result_array['last']['prefix'] = $this->opt['archive_category_prefix'];
				$result_array['last']['title'] = single_cat_title('', false);
				$result_array['last']['suffix'] = $this->opt['archive_category_suffix'];
			}
	
			break;
	
		case '404':
			////////////////////////////////////////////////////////////////////
			// Get 404 error page
			////////////////////////////////////////////////////////////////////

			$result_array['last']['title'] = $this->opt['title_404'];
		
			break;
	
		case 'else':
			////////////////////////////////////////////////////////////////////
			// Get weblog article list (which is very often the front page of the blog)
			////////////////////////////////////////////////////////////////////
		
			$result_array['last']['title'] = $this->opt['title_blog'];

		} // switch


		////////////////////////////////////////////////////////////////////////////
		// Echo the result
		////////////////////////////////////////////////////////////////////////////

		// MIDDLE PART

		//		The first separator between HOME and the first entry
		$first_sep = $this->opt['separator'];
		if ($this->opt['home_display'] !== true) $first_sep = ''; // remove first separator if HOME is disabled in the options 

		//		get middle part and add separator(s)
		$middle_part = '';		
		if ($result_array['middle'] === false) {
			// there is no middle part...
		
			if ($result_array['last']['title'] === false)
				$first_sep = ''; // we are on home.

		} else {
			// There is a middle part...
			$middle_part = $result_array['middle'] . $this->opt['separator'];
		}


		// LAST PART -- PREFIX
		$last_part = '';
		if ($result_array['last']['prefix'] !== false)
			$last_part .= $result_array['last']['prefix'];

		if ($result_array['last']['title'] !== false)
			$last_part .= $curitem_urlprefix . $result_array['last']['title'] . $curitem_urlsuffix;

		if ($result_array['last']['suffix'] !== false)
			$last_part .= $result_array['last']['suffix'];

		// ECHO		
		echo $bcn_homelink . $first_sep . $middle_part . $this->opt['current_item_style_prefix'] . $last_part . $this->opt['current_item_style_prefix'] ;


	} // END function display


} // END class breadcrumb_navigation_xt


?>