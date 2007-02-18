<?php
/*
Plugin Name: King Emails
Version: 0.1
Plugin URI: http://www.website-king.de
Description: better formating and control of sent emails. overwrites pluggable Email functions
Author: Georg Leciejewski
Author URI: http://www.blog.mediaprojekte.de
*/
/*
ToDo
- add options for showing or not showing stuff
- add options for texts
*/


require_once(ABSPATH.'wp-content/plugins/king-includes/library/class-plugintoolkit.php');

plugintoolkit(
	$plugin='KingMails',
	$array=array(
		//'from_name' => 'Email From Name {textbox|50|150} ## Name for the From field of the email.',
		'from_email' => 'From Email {textbox|50|150} ## Email Adress in the From field of the email.',

//	  	'debug' => 'debug',
		'delete' => 'delete',
	),
	$file='king-emails-admin.php',
	$menu=array(
		'parent' => 'plugins.php' ,
		'access_level' => 'activate_plugins',
	),
	$newFiles=''
);

if ( !function_exists('wp_mail') ) :
/**
*@desc overwritten mail function
*@author georg leciejewski
*
*/
function wp_mail($to, $subject, $message, $headers = '')
{
	$email_options = get_option('king-KingMails');

	if( $headers == '' ) {
		$headers = "MIME-Version: 1.0\n" .
			"From:".$email_options['from_email']."\n" .
			"Content-Type: text/plain; charset=". get_settings('blog_charset') . "\n";
	}

	return @mail($to, $subject, $message, $headers);
}
endif;

if ( ! function_exists('wp_notify_postauthor') ) :
/**
*@desc notify post author of comments ping and trakcbacks
*@author georg leciejewski
*
*/

function wp_notify_postauthor($comment_id, $comment_type='')
{
	global $wpdb;

	$comment = get_comment($comment_id);
	$post    = get_post($comment->comment_post_ID);
	$user    = get_userdata( $post->post_author );

	if ('' == $user->user_email) return false; // If there's no email to send the comment to

	$comment_author_domain = gethostbyaddr($comment->comment_author_IP);

	$blogname = get_settings('blogname');

	if ( empty( $comment_type ) ) $comment_type = 'comment';

	if ('comment' == $comment_type) {
		$notify_message  = sprintf( __('New comment on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\n";
		$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\n";
		$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\n";
		$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\n";
		$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\n";
		$notify_message .= __('Comment: ') . "\n" . $comment->comment_content . "\n\n";
		$notify_message .= __('You can see all comments on this post here: ') . "\n";
		$subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
	} elseif ('trackback' == $comment_type) {
		$notify_message  = sprintf( __('New trackback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\n";
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\n";
		$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\n";
		$notify_message .= __('Excerpt: ') . "\n" . $comment->comment_content . "\n\n";
		$notify_message .= __('You can see all trackbacks on this post here: ') . "\n";
		$subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
	} elseif ('pingback' == $comment_type) {
		$notify_message  = sprintf( __('New pingback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\n";
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\n";
		$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\n";
		$notify_message .= __('Excerpt: ') . "\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\n\n";
		$notify_message .= __('You can see all pingbacks on this post here: ') . "\n";
		$subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\n\n";
	$notify_message .= sprintf( __('To delete this comment, visit: %s'), get_settings('siteurl').'/wp-admin/post.php?action=confirmdeletecomment&p='.$comment->comment_post_ID."&comment=$comment_id" ) . "\n";

	$wp_email = 'wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));

	if ( '' == $comment->comment_author ) {
		$from = "From: \"$blogname\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: $comment->comment_author_email";
 	} else {
		$from = "From: \"$comment->comment_author\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
 	}

	$message_headers = "MIME-Version: 1.0\n"
		. "$from\n"
		. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";

	if ( isset($reply_to) )
		$message_headers .= $reply_to . "\n";

	$notify_message = apply_filters('comment_notification_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_notification_subject', $subject, $comment_id);
	$message_headers = apply_filters('comment_notification_headers', $message_headers, $comment_id);

	@wp_mail($user->user_email, $subject, $notify_message, $message_headers);

	return true;
}
endif;

if ( ! function_exists('wp_notify_moderator') ) :
/**
*@desc notify moderator of comments ping and trackbacks
*@author georg leciejewski
*
*/
function wp_notify_moderator($comment_id) {
	global $wpdb;

	if( get_settings( "moderation_notify" ) == 0 )
		return true;

	$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");

	$comment_author_domain = gethostbyaddr($comment->comment_author_IP);
	$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

	$notify_message  = sprintf( __('A new comment on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\n";
	$notify_message .= get_permalink($comment->comment_post_ID) . "\n\n";
	$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\n";
	$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\n";
	$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\n";
	$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\n";
	$notify_message .= __('Comment: ') . "\n" . $comment->comment_content . "\n\n";
	$notify_message .= sprintf( __('To approve this comment, visit: %s'),  get_settings('siteurl').'/wp-admin/post.php?action=mailapprovecomment&p='.$comment->comment_post_ID."&comment=$comment_id" ) . "\n";
	$notify_message .= sprintf( __('To delete this comment, visit: %s'), get_settings('siteurl').'/wp-admin/post.php?action=confirmdeletecomment&p='.$comment->comment_post_ID."&comment=$comment_id" ) . "\n";
	$notify_message .= sprintf( __('Currently %s comments are waiting for approval. Please visit the moderation panel:'), $comments_waiting ) . "\n";
	$notify_message .= get_settings('siteurl') . "/wp-admin/moderation.php\n\n\n";
	$notify_message .= "using overwritten function\n";

	$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), get_settings('blogname'), $post->post_title );
	$admin_email = get_settings('admin_email');

	$notify_message = apply_filters('comment_moderation_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_moderation_subject', $subject, $comment_id);

	@wp_mail($admin_email, $subject, $notify_message);

	return true;
}
endif;

if ( !function_exists('wp_new_user_notification') ) :
/**
*@desc notify new user
*@author georg leciejewski
*
*/
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	$message  = sprintf(__('New user registration on your blog %s:'), get_settings('blogname')) . "\n\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\n\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\n";

	@wp_mail(get_settings('admin_email'), sprintf(__('[%s] New User Registration'), get_settings('blogname')), $message);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\n";
	$message .= get_settings('siteurl') . "/wp-login.php\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_settings('blogname')), $message);

}
endif;

if(in){

}

?>