<?php
/*
Plugin Name: King MetaTags
Version: 1.2
Plugin URI: http://www.website-king.de
Description: Add MetaTags to you site Header
Author: Georg Leciejewski
Author URI:http://www.website-king.de
*/

require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');
plugintoolkit(
	$plugin='metatags',
	$array=array(
	'robots' => 'robots ## Tell search-engines to crawl the site. In most cases to - index,follow -Options: all / index / noindex/ follow / nofollow /index,follow / noindex,follow / noindex,nofollow / index,nofollow',
	'revisit' => 'revisit ## How Often should Robots return. ex.:" 7 days "',
	'author' => 'author ## Shows the Author of the site',
	'publisher' => 'publisher ## This Tag shows the Publisher / ISP of the Website.',
	'copyright' => 'copyright ## Shows the legal responsability.',
	'allowsearch' => 'Allow Search ## Normally Yes.',
	'pragma' => 'Pragma ## Normally Yes.',
	'rating' => 'Rating ## Normally GENERAL .',
	'distribution' => 'Distribution ## Normally GLOBAL .',
	'classification' => 'Classification ##  .',
	'generator' => 'generator ## The software that generated this site. Could be Frontpage (puke) to trick the Visitors',
	'language' => 'language ##  What language are you writing in? Ex. "de" od most likely used "en"',
	'ICBM' => 'ICBM ## Geo-Tags where are you located! Only Dezimal:" 50.9544, 6.9186 " More info on http://www.multimap.com',
	'def_keywords' => 'Default Keywords {textbox|100|200}## Keywords that describe your Content. Will be used if an article does not have specific keywords.If an Article has Keywords they will be appended to those!',
	'def_description' => 'Default Description {textbox|100|200}## Description of your Content. Will be used if an article does not have a specific Description.',
	'delete' => 'delete',
	),
	$file='king-meta.php',
	$menu=array(
		'parent' => 'options-general.php' ,
		'access_level' => 'activate_plugins',
	),
	$newFiles=''
);
include (ABSPATH.'/wp-includes/pluggable.php');
if(!is_user_logged_in()){
	unset ($GLOBALS['metatags']);
}
/**
* get the Article Meta-Description and keywords
* @Author: Dirk Zimmermann / http://www.uberdose.com
* enhanced by Georg Leciejewski
*/
function meta_king_head_start(){
	global $single, $posts, $post_meta_cache, $xfish_ob_level, $keywords_out,$description_out;
	$meta_string = null;
	if (is_array($posts)) {
		foreach ($posts as $post) {
			if ($post) {
				$keywords_a = $keywords_i = null;
				$description_a = $description_i = null;
				$id = $post->ID;
				$meta = &$post_meta_cache[$id];
				if (is_array($meta)) {
					$keywords_a = $meta['keywords'];
					$description_a = $meta['description'];
					if (is_array($keywords_a)) {
						$keywords_i = $keywords_a[0];
					}
					if (is_array($description_a)) {
						$description_i = $description_a[0];
					}
				}
				if (!isset($description_i)) {
				// look for an excerpt?
				}
				if (isset($keywords_i)) {
					//$meta_string .= sprintf("", $keywords);
					if (isset($keywords)) {
					$keywords .= ',';
					}
				$keywords .= $keywords_i;
				}
				if (isset($description_i)) {
					//$meta_string .= sprintf("", $description);
					if (isset($description)) {
						$description .= ' ';
					}
					$description .= $description_i;
				}
			}
		}
	}

	if (isset($keywords)) {
		$keywords_out = getUniqueKeywords($keywords);
	}
	if (isset($description)) {
		$description_out .= sprintf("\n<meta name=\"description\" content=\"%s\" />",
		$description);
	}
}

function getUniqueKeywords($keywords)
{
	$keywords_ar = array_unique(explode(',', $keywords));
	return implode(',', $keywords_ar);
}

if (function_exists('add_action')) {
	add_action('wp_head', 'meta_king_head_start');
	add_action('wp_head', 'meta_king_head');

}

function meta_king_head() {
	global $metatags,$keywords_out,$description_out;
	$my_meta=get_option('king-metatags');

	if ($my_meta['robots'] !='') {
		print '<meta name="robots" content="'.$my_meta['robots'].'" />'."\n";
	}

	if ($my_meta['allowsearch'] !='') {
		print '<meta name="allow-search" content="'.$my_meta['allowsearch'].'" />'."\n";
	}
	if ($my_meta['pragma'] !='') {
		print ' <meta http-equiv="pragma" content="'.$my_meta['pragma'].'" />'."\n";
	}
	if ($my_meta['rating'] !='') {
		print '<meta name="rating" content="'.$my_meta['rating'].'" />'."\n";
	}
	if ($my_meta['distribution'] !='') {
		print '<meta name="distribution" content="'.$my_meta['distribution'].'" />'."\n";
	}
	if ($my_meta['classification'] !='') {
		print '<meta name="classification" content="'.$my_meta['classification'].'" />'."\n";
	}

	if ($my_meta['revisit'] !='') {
		print '<meta name="revisit" content="'.$my_meta['revisit'].'" />'."\n";
	}
	if ($my_meta['author'] !='') {
		print '<meta name="author" content="'.$my_meta['author'].'" />'."\n";
	}
	if ($my_meta['publisher'] !='') {
		print '<meta name="publisher" content="'.$my_meta['publisher'].'" />'."\n";
	}
	if ($my_meta['copyright'] !='') {
		print '<meta name="copyright" content="'.$my_meta['copyright'].'" />'."\n";
	}
	if ($metatags->option['generator'] !='') {
		print '<meta name="generator" content="'.$my_meta['generator'].'" />'."\n";
	}
	if ($my_meta['language'] !='') {
		print '<meta name="language" content="'.$my_meta['language'].'" />'."\n";
		print '<meta http-equiv="content-language" content="'.$my_meta['language'].'" />'."\n";
	}
	if ($my_meta['ICBM'] !='') {
		print '<meta name="ICBM" content="'.$my_meta['ICBM'].'" />'."\n";
	}
	if ($my_meta['def_keywords'] !='') {
		meta_king_head_start();
		print '<meta name="keywords" content="'.$my_meta['def_keywords'].' '.$keywords_out.'" />'."\n";
	}
	if ($my_meta['def_description'] !='' && $description_out =='') {

		print '<meta name="description" content="'.$my_meta['def_description']. $description.'" />'."\n";
	}elseif($description_out !=''){
		print $description_out;
	}
/*
    <meta name="rating" content="GENERAL">
    <meta name="distribution" content="GLOBAL">
    <meta name="classification" content="Accessibility">
*/


}

?>
