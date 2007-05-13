<?php
/*
Plugin Name: King_Clouds
Plugin URI: http://www.blog.mediaprojekte.de/
Description: Template tags to display Clouds / weighted lists for categories, words, months 
Version: 0.5
Author: Georg Leciejewski
Author URI: http://www.blog.mediaprojekte.de/
*/


require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-kingcloud.php');
/**
* @desc weighted categories cloud with options for fontsize, color, after html, before html, exludecategories
* @param int $smallest	- fontsize of the smallest category
* @param int $largest	- fontsize of the largest category
* @param string $unit	- unit for the fontsize % / px /
* @param string $cold 	- fontcolor for the coldest cat
* @param string $hot 	- fontcolor for the hottest cat
* @param string $before	- html before each cat link
* @param string $after 	- html after each cat link
* @param string $exclude- categories to exclude as comma seperated list
* @author george leciejewski / Christoph Wimmer
*/
function categories_cloud($smallest=50, $largest=200, $unit="%", $cold="", $hot="", $before='', $after='', $exclude='')
{
	global $wpdb;
	$exclusions = '';
	if (!empty($exclude))
	{# set excluded category ids for sql
		$excats = preg_split('/[\s,]+/',$exclude);
		if (count($excats))
		{
			foreach ($excats as $excat)
			{
				$exclusions .= ' AND '.$wpdb->categories.'.cat_ID <> ' . intval($excat) . ' ';
			}
		}
	}
	# returns the categories as objects $result->post, id, name, nicename
	$results = $wpdb->get_results(
		'SELECT '
		.$wpdb->categories .'.cat_ID AS `id`,'
		.$wpdb->categories.'.cat_name AS `name`,'
		.$wpdb->categories.'.category_nicename AS `nicename`'
		.',COUNT('.$wpdb->post2cat.'.rel_id) AS `posts` FROM '. $wpdb->categories.', '.$wpdb->post2cat
		.' WHERE '. $wpdb->categories.'.cat_ID = '.$wpdb->post2cat.'.category_id'
		. $exclusions.' GROUP BY '.$wpdb->categories.'.cat_ID ORDER BY cat_name ASC');

	$content = array();
	foreach($results as $key => $val)
	{ # normalize the values to fit into KingCloud Class content var
		$content[$key]['url']	= get_category_link($val->id);
		$content[$key]['text']	= stripslashes($val->name);
		$content[$key]['title'] = $val->posts . ' Artikel';
		$content[$key]['count'] = $val->posts ;
	}
	$cloud = new KingCloud($content,$smallest, $largest, $unit, $cold, $hot, $before, $after);
	$cloud->output();
}


/**
* @desc Weighted Archives by Month with options for fontsize, color, after html, before html, exludecategories
* @param int $smallest	- fontsize of the smallest category
* @param int $largest	- fontsize of the largest category
* @param string $unit	- unit for the fontsize % / px /
* @param string $cold 	- fontcolor for the coldest cat
* @param string $hot 	- fontcolor for the hottest cat
* @param string $before	- html before each cat link
* @param string $after 	- html after each cat link
* @param string $exclude- categories to exclude as comma seperated list
* @author george leciejewski / Christoph Wimmer
*/

//function weighted_archives($smallest=10, $largest=36, $unit="em", $cold="00f", $hot="f00", $before='', $after='&nbsp')
function archives_cloud($smallest=50, $largest=200, $unit="%", $cold="", $hot="", $before='', $after='', $exclude='')
{
    global $month, $wpdb;

	$now = current_time('mysql');
	$results = $wpdb->get_results(
		'SELECT DISTINCT YEAR(post_date) AS `year`,'
		.' MONTH(post_date) AS `month`,'
		.' count(ID) AS `posts` '
		.' FROM '.$wpdb->posts.' WHERE post_date < "'.$now.'" AND post_status = "publish"'
		.' GROUP BY YEAR(post_date), MONTH(post_date)'.
		' ORDER BY post_date DESC');

    $content = array();
	foreach($results as $key => $val)
	{ # normalize the values to fit into KingCloud Class content var
		$content[$key]['url']	= get_month_link($val->year, $val->month);
		$content[$key]['text']	=  sprintf('%s %d', $month[zeroise($val->month,2)], $val->year);
		$content[$key]['title'] = $val->posts . ' Artikel';
		$content[$key]['count'] = $val->posts ;
	}
    $cloud = new KingCloud($content,$smallest, $largest, $unit, $cold, $hot, $before, $after);
	$cloud->output();
}


/**
* @desc weighted word cloud shows the most used words in your blog.
* @param int $mincount	- min count of the word before it appears in the cloud. default 25
* @param int $minlength	- min lenght of the word before it appears in the cloud. default 3
* @param int $smallest	- fontsize of the smallest category
* @param int $largest	- fontsize of the largest category
* @param string $unit	- unit for the fontsize % / px /
* @param string $cold 	- fontcolor for the coldest cat
* @param string $hot 	- fontcolor for the hottest cat
* @param string $before	- html before each cat link
* @param string $after 	- html after each cat link
* @param string $exclude- categories to exclude as comma seperated list
*/
function word_cloud($mincount=25, $minlength=3, $smallest=50, $largest=200, $unit="%", $cold="", $hot="", $before='', $after='')
{
	global $wpdb;

	$now = gmdate("Y-m-d H:i:s",time());
	$results = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_date < '$now'");

	$string = '';
	if ($results)
	{
		foreach ($results as $post)
		{
			$postwords = strip_tags($post->post_content);
        	$string .= $postwords;
    	}
	}

	$string = strtolower($string);

	#	Remove punctuation.
	$wordlist = preg_split('/\s*[\s+\.|\?|,|(|)|\-+|\'|\*|\"|=|;|%|\!|\[|\]|&#0215;|\$|\/|:|{|}]\s*/i', $string);

	#	Build an array of the unique words and number of times they occur.
	$allwords = array_count_values( $wordlist );

	#	Remove words that don't matter--"stop words."

	$overusedwords = array( '', 'a', 'am', 'an', 'the', 'and', 'of', 'i', 'to', 'is', 'in', 'for', 'as', 'that', 'on', 'at', 'this', 'my', 'was', 'our', 'it', 'its', 'you', 'we', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '10', 'about',  'actually', 'after', 'again', 'all', 'almost', 'along', 'also', 'always', 'another', 'any', 'anyone', 'anything', 'anyway', 'are', 'area', 'around',
	    'available', 'back', 'be', 'because', 'been', 'before', 'being', 'best', 'better', 'between', 'big', 'bit', 'both', 'but', 'by', 'c', 'came', 'can', 'capable', 'control', 'could', 'course', 'd', 'day', 'decided', 'did', 'didn', 'different', 'div', 'do', 'does', 'doesn', 'doing', 'down', 'drive', 'e', 'each', 'easy', 'else', 'end', 'enough', 'even', 'ever', 'every', 'example', 'few', 'find',
	     'first', 'found', 'from', 'get', 'go', 'going', 'good', 'got', 'had', 'hard', 'has', 'have', 'haven', 'he', 'her', 'hers', 'here', 'him', 'his', 'how', 'if',  'into', 'isn', 'just', 'kind', 'know', 'last', 'least', 'left', 'like', 'little', 'll', 'long', 'look', 'lot', 'm', 'made', 'make', 'many', 'may', 'maybe', 'me', 'might', 'more', 'most', 'much', 'my', 'name', 'nbsp', 'need', 'never',
	     'new', 'no', 'not', 'now', 'number', 'o', 'off', 'ok', 'okay', 'old', 'one', 'only', 'or', 'other', 'out', 'over', 'own', 'part', 'people', 'place', 'point', 'pretty', 'probably', 'problem', 'put', 'quite', 'quot', 'r', 're', 'really', 'results', 'right', 's', 'same', 'saw', 'say', 'see', 'set', 'several', 'she', 'should', 'since', 'size', 'small', 'so', 'some', 'someone', 'something', 'special',
	     'still', 'stuff', 'such', 'sure', 'system', 't', 'take', 'than', 'their', 'them', 'then', 'there', 'these', 'they', 'thing', 'things', 'think', 'those', 'though', 'through', 'time', 'today', 'together', 'too', 'took', 'two', 'up', 'us', 'use', 'used', 'using', 've', 'very', 'want', 'way', 'well', 'went', 'were', 'what', 'when', 'where', 'which', 'while', 'who', 'why', 'will', 'with','without',
	     'would', 'wouldn', 'x', 'y',  'add', 'added', 'com', 'contains', 'http', 'your','immer', 'kategorien', 'zwei', 'seite', 'seiten', 'heute', 'pdf', 'zeit', 'default', 'neue', 'check', 'auch', 'nicht','und','werden','durch','jetzt','seine','kleine','zeigt','der','die','das','alle','mit','uuml','auml','ouml','von','oder','mal','unter','szlig','des','ist','muss','vor','auf','den','kann','man','ganz',
	     'viele','ich','wenn','wer','wie','wieder','wird','zur','zum','aber','als','aus','amp','bei','ber','damit','dann','dazu','dem','diese','dieser','dieses','doch','einem','ein','eine','einen','einer','etwas','fuer','habe','haben','unterst','verf','ndash','hatte','ldquo',	'gibt','hat','hier','hierf','keine','nach','nnen','noch','nur','schon','sein','bitte','gute','kein','mdash','article','jeder',
	     'machen','post','pers','neu','soll', 'wir','ihr','sich','mehr','sich','sie','sind', '_request', 'acute', 'artikel', 'box', 'class', 'code', 'file', 'filename', 'files', 'finden', 'findet', 'focus', 'fragen', 'informationen', 'inhalte', 'level', 'line', 'link', 'links', 'page', 'product', 'slot', 'text', 'top', 'upload_file', 'url', 'users', 'version', 'www', 'recht', 'bis', 'diesem', 'einfach',
	     'features', 'funktionen', 'georg', 'image', 'kunden', 'lassen', 'leciejewski', 'mir', 'neuen', 'nun', 'ohne', 'site', 'sollte', 'wurde','alles', 'bietet', 'bundle', 'colum', 'column', 'columns', 'dass', 'dies', 'einige', 'folgenden', 'geben', 'gefunden', 'gemacht', 'gen', 'ihrer', 'jedoch  neues', 'rund', 'sch', 'search', 'sehen', 'sehr', 'seiner', 'seit', 'ver', 'via', 'vom', 'wohl', 'wurden',
	     'andere','bin','bisher','del','eigene','icio','jede','jedoch','llt','org','per','sowie','weiter','weitere','weiteren','zwar','anderen','dabei','sollten','neues','nutzen','eigentlich','array_keys','don','gut','in','_array','geht','glich','laut','meine','mich','net','schreiben','reg','steht','raquo','ren','seinem','nat','ssen','werde','wirklich','wollen','zumindest','zwischen','ben','bereits',
	     'diesen','direkt','eigenen','eines','erste','ersten','ffentlicht','ganze','genutzt','gerade','give','gro','hlen','ihre','ihren','insert','kleinen','kommen','kommt','lange','leider','letzten','macht','menge','oben','oft','paar','please','sicher','schnell','rlich','rsquo','sieht','ten','uft','war','welche','allem','allen','aller','allerdings','already','alt','alten','anzeigen','aufl','ausf','bald',
	     'basiert','bdquo','beiden','beim','bekommt','bereit','berpr','besch','besonders','bevor','bleiben','bieten','bleibt','bringt','bzw','chsten','che','chte','comming','daf','daher','dank','dar','daran','darauf','angezeigt','anderem','ans','anschauen','bringen','dargestellt','dein','deine','deiner','denen','denn','deren','deshalb','deutlich','dir','dort','eher','einf','einigen','erh','erkl','erm','erscheint','erst','erweiterte','etc','etwa','euch','falls'
	);

	foreach ($overusedwords as $word)
	{ # Remove the stop words from the list.
		unset( $allwords[$word] );
	}
	# Sort the keys alphabetically.
	ksort( $allwords );

    foreach ($allwords as $key => $val )
	{
        if ( strlen($key) < $minlength || $val < $mincount )
		{ # Remove short words or words below mincount
			unset( $allwords[$key] );
		}
	}
	$content = array();
	foreach ($allwords as $key => $val )
	{ # format the rest of the word to be shown in the cloud
        $content[$key]['url']	= get_bloginfo('wpurl').'/index.php?s=' . $key . '&amp;submit=ww';
		$content[$key]['text']	= $key;
		$content[$key]['title'] = 'Anzahl '.$val;
		$content[$key]['count'] = $val;
	}
    $cloud = new KingCloud($content,$smallest, $largest, $unit, $cold, $hot, $before, $after);
	$cloud->output();

}
?>