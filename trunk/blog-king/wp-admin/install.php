<?php
define('WP_INSTALLING', true);
if (!file_exists('../wp-config.php')) 
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");

require_once('../wp-config.php');
require_once('./upgrade-functions.php');

$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
$guessurl = str_replace('/wp-admin/install.php?step=2', '', $schema . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) );

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('WordPress &rsaquo; Installation'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style media="screen" type="text/css">
	<!--
	html {
		background: #eee;
	}
	body {
		background: #fff;
		color: #000;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 20%;
		margin-right: 20%;
		padding: .2em 2em;
	}
	
	h1 {
		color: #006;
		font-size: 18px;
		font-weight: lighter;
	}
	
	h2 {
		font-size: 16px;
	}
	
	p, li, dt {
		line-height: 140%;
		padding-bottom: 2px;
	}

	ul, ol {
		padding: 5px 5px 5px 20px;
	}
	#logo {
		margin-bottom: 2em;
	}
	.step a, .step input {
		font-size: 2em;
	}
	td input {
		font-size: 1.5em;
	}
	.step, th {
		text-align: right;
	}
	#footer {
		text-align: center; 
		border-top: 1px solid #ccc; 
		padding-top: 1em; 
		font-style: italic;
	}
	-->
	</style>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
// Let's check to make sure WP isn't already installed.
$wpdb->hide_errors();
$installed = $wpdb->get_results("SELECT * FROM $wpdb->users");
if ($installed) die('<h1>'.__('Already Installed').'</h1><p>'.__('You appear to have already installed WordPress. To reinstall please clear your old database tables first.').'</p></body></html>');
$wpdb->show_errors();

switch($step) {

	case 0:
?>
<p><?php printf(__('Welcome to WordPress installation. We&#8217;re now going to go through a few steps to get you up and running with the latest in personal publishing platforms. You may want to peruse the <a href="%s">ReadMe documentation</a> at your leisure.'), '../readme.html'); ?></p>
	<h2 class="step"><a href="install.php?step=1"><?php _e('First Step &raquo;'); ?></a></h2>
<?php
	break;

	case 1:
?>
<h1><?php _e('First Step'); ?></h1>
<p><?php _e("Before we begin we need a little bit of information. Don't worry, you can always change these later."); ?></p>

<form id="setup" method="post" action="install.php?step=2">
<table width="100%">
<tr>
<th width="33%"><?php _e('Weblog title:'); ?></th>
<td><input name="weblog_title" type="text" id="weblog_title" size="25" /></td>
</tr>
<tr>
<th><?php _e('Tagline:'); ?></th>
	<td><input name="blogdescription" type="text" id="blogdescription" size="25" /></td>
</tr>
<tr>
<th><?php _e('Default date format:'); ?></th>
	<td><input name="date_format" type="text" id="date_format" size="25" value="d. m. Y" />
	<?php _e('Output:') ?> <strong><?php echo mysql2date("d. m. Y" , current_time('mysql')); ?>
	</td>
</tr>
<tr>
<th><?php _e('Default time format:'); ?></th>
	<td><input name="time_format" type="text" id="time_format" size="25" value="G:i" />
	<?php _e('Output:') ?> <strong><?php echo gmdate("G:i", current_time('timestamp')); ?>
	</td>
</tr>
<th><?php _e('Your e-mail:'); ?></th>
	<td><input name="admin_email" type="text" id="admin_email" size="25" /></td>
</tr>
</table>
<p><em><?php _e('Double-check that email address before continuing.'); ?></em></p>
<h2 class="step">
<input type="hidden" name="moderation_keys" value="-online
4u
adipex
advicer
ambien
baccarrat
blackjack
bllogspot
booker
byob
car-rental-e-site
car-rentals-e-site
carisoprodol
casino
casinos
chatroom
cialis
credit-report-4u
cwas
cyclen
cyclobenzaprine
dating-e-site
day-trading
debt-consolidation-consultant
drug
discreetordering
duty-free
dutyfree
fioricet
flowers-leading-site
freenet-shopping
freenet
gambling
health-insurancedeals-4u
holdem
holdempoker
holdemsoftware
holdemtexasturbowilson
hotel-dealse-site
hotele-site
hotelse-site
incest
insurance-quotesdeals-4u
insurancedeals-4u
jrcreations
levitra
loan
macinstruct
mortgage-4-u
online-gambling
onlinegambling-4u
ottawavalleyag
ownsthis
palm-texas-holdem-game
paxil
penis
pharmacy
phentermine
poker
poker-chip
poze
rental-car-e-site
roulette 
shemale
slot-machine
slot
soma
taboo
teen
texas-holdem
thorcarlson
top-site
top-e-site
tramadol
trim-spa
ultram
valeofglamorganconservatives
viagra
vioxx
xanax
zolus" />
<input type="submit" name="Submit" value="<?php _e('Continue to Second Step &raquo;'); ?>" />
</h2>
</form>

<?php
	break;
	case 2:

// Fill in the data we gathered
$weblog_title = stripslashes($_POST['weblog_title']);

$blogdescription = stripslashes($_POST['blogdescription']);
$admin_email = stripslashes($_POST['admin_email']);
$date_format = stripslashes($_POST['date_format']);
$time_format = stripslashes($_POST['time_format']);
$moderation_keys = stripslashes($_POST['moderation_keys']);
// check e-mail address
if (empty($admin_email)) {
	die (__("<strong>ERROR</strong>: please type your e-mail address"));
} else if (!is_email($admin_email)) {
	die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));
}
	
?>
<h1><?php _e('Second Step'); ?></h1>
<p><?php _e('Now we&#8217;re going to create the database tables and fill them with some default data.'); ?></p>


<?php
flush();

// Set everything up
wp_cache_flush();
make_db_current_silent();
populate_options();
populate_roles();

update_option('blogname', $weblog_title);
update_option('blogdescription', $blogdescription);
update_option('date_format', $date_format);
update_option('time_format', $time_format);
update_option('admin_email', $admin_email);
update_option('moderation_keys', $moderation_keys);
// Now drop in some default links
$wpdb->query("INSERT INTO $wpdb->linkcategories (cat_id, cat_name) VALUES (1, '".$wpdb->escape(__('Blogroll'))."')");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://www.blog.mediaprojekte.de', 'MP:Blog', 1, 'www.blog.mediaprojekte.de/feed/', '');");

// Default category
$wpdb->query("INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename, category_count, category_description) VALUES ('0', '".$wpdb->escape(__('Uncategorized'))."', '".sanitize_title(__('Uncategorized'))."', '1', '')");

// First post
// Default comment
// First Page
$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_excerpt, post_title, post_category, post_name, post_modified, post_modified_gmt, post_status, to_ping, pinged, post_content_filtered) VALUES ('1', '$now', '$now_gmt', '".$wpdb->escape(__('This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress.'))."', '', '".$wpdb->escape(__('About'))."', '0', '".$wpdb->escape(__('about'))."', '$now', '$now_gmt', 'static', '', '', '')");
$wp_rewrite->flush_rules();

// Set up admin user
$random_password = substr(md5(uniqid(microtime())), 0, 6);
$display_name_array = explode('@', $admin_email);
$display_name = $display_name_array[0];
$wpdb->query("INSERT INTO $wpdb->users (ID, user_login, user_pass, user_email, user_registered, display_name, user_nicename) VALUES ( '1', 'admin', MD5('$random_password'), '$admin_email', NOW(), '$display_name', 'admin')");
$wpdb->query("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES ({$wpdb->insert_id}, '{$table_prefix}user_level', '10');");
$admin_caps = serialize(array('administrator' => true));
$wpdb->query("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES ({$wpdb->insert_id}, '{$table_prefix}capabilities', '{$admin_caps}');");

$message_headers = 'From: ' . $weblog_title . ' <wordpress@' . $_SERVER['SERVER_NAME'] . '>';
$message = sprintf(__("Willkommen bei Website King!

Du hast dir gerade eine neues Website King erfolgreich eingerichtet! 
Die Adresse dorthin lautet:

%1\$s


Jetzt kannst Du Dich dort auch mit den 
folgenden Daten als Administrator anmelden:

Username: admin
Password: %2\$s
Viel Spass noch mit Website King!

--Das Website King Team
http://www.website-king.de
"), $guessurl, $random_password);

@wp_mail($admin_email, __('Neue Website King Installation'), $message, $message_headers);

wp_cache_flush();
?>

<p><em><?php _e('Fertig!'); ?></em></p>

<p><?php printf(__('Now you can <a href="%1$s">log in</a> with the <strong>username</strong> "<code>admin</code>" and <strong>password</strong> "<code>%2$s</code>".'), '../wp-login.php', $random_password); ?></p>
<p><?php _e('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you. If you lose it, you will have to delete the tables from the database yourself, and re-install WordPress. So to review:'); ?>
</p>
<dl>
<dt><?php _e('Username'); ?></dt>
<dd><code>admin</code></dd>
<dt><?php _e('Password'); ?></dt>
<dd><code><?php echo $random_password; ?></code></dd>
	<dt><?php _e('Login address'); ?></dt>
<dd><a href="../wp-login.php">wp-login.php</a></dd>
</dl>
<p><?php _e('Were you expecting more steps? Sorry to disappoint. All done! :)'); ?></p>
<?php
	break;
}
?>
<p id="footer"><?php _e('<a href="http://www.website-king.de/">Website-King</a>, das einfache CMS.'); ?></p>
</body>
</html>