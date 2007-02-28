<?php
/* @(#)$KimmoSuominen: timezone.php,v 1.13 2006/01/08 21:53:26 kim Exp $
 *
 * Copyright (c) 2005 Kimmo Suominen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer
 *    in the documentation and/or other materials provided with the
 *    distribution.
 * 3. The name of the author may not be used to endorse or promote
 *    products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
 * IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
 * IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/*
Plugin Name: Time Zone
Plugin URI: http://kimmo.suominen.com/sw/timezone/
Description: Automatische Umstellung von Sommerzeit auf Winterzeit. Einstellungen k&ouml;nnen unter: Optionen &raquo; Time Zone ge&auml;ndert werden.
Author: Kimmo Suominen
Version: 2.1
Author URI: http://kimmo.suominen.com/
*/

if (is_plugin_page()):
    $wppTimeZone->options_page();
else:

class TimeZone
{
    function TimeZone()
    {
		if (!ini_get('safe_mode')) {
		    $tz = get_option('timezone_tz');
		    if (!empty($tz)) putenv("TZ=$tz");
		}
		add_action('admin_menu', array(&$this, 'admin_menu'));
    }

    function admin_menu()
    {
		add_options_page( __('Time Zone Options'),__('Time Zone'), 5, basename(__FILE__), array(&$this,'options_page'));
    }

    function options_page()
    {
		if (isset($_POST['Submit'])):
		    update_option('timezone_tz', $_POST['timezone_tz']);
		    // There is no function to remove TZ from environment
		    // so we only output the save success notification.
			?>
			<div class="updated">
			<p><strong><?php _e('Einstellungen gespeichert.') ?></strong></p>
			</div>
			<?php
				else:
				    $t = time();
			?>
			<div class="wrap">
			<h2><?php echo __('Zeitzonen Einstellung'); ?></h2>
			<form name="timezone" method="post" action="">
			    <input type="hidden" name="action" value="update" />
			    <input type="hidden" name="page_options" value="'timezone_tz'" />
			    <fieldset class="options">
				<legend><?php echo _e('Bisheriger Wert'); ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				    <tr valign="baseline">
					<th scope="row"><?php _e('Name der Zeitzone:') ?></th>
					<td><?php echo strftime('%Z', $t); ?></td>
				    </tr>
				    <tr valign="baseline">
					<th scope="row"><?php _e('Verschiebung der Zeitzone:') ?></th>
					<td><?php echo strftime('%z', $t); ?></td>
				    </tr>
				    <tr valign="baseline">
					<th scope="row"><?php _e('Datum und Zeit:') ?></th>
					<td><?php echo date('r (T)'); ?></td>
				    </tr>
				</table>
			    </fieldset>
			    <fieldset class="options">
				<legend><?php _e('Automatische Wordpress Einstellung') ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				    <tr valign="baseline">
					<th scope="row"><?php
					    _e('Differenz:') ?></th>
					<td><?php echo $this->option_gmt_offset(); ?> Stunden</td>
				    </tr>
				</table>
			    </fieldset>
			<?php if (!ini_get('safe_mode')): ?>
			    <fieldset class="options">
				<legend><?php _e('Von Ihnen gew&#228;hlte Zeitzone') ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				    <tr valign="baseline">
					<th scope="row"><?php _e('Zeitzone (TZ)') ?>:</th>
					<td><input name="timezone_tz" type="text" id="timezone_tz"
					    value="<?php form_option('timezone_tz'); ?>" size="40" />
					</td>
				    </tr>
				</table>
			    </fieldset>
			<?php endif; ?>
			    <p class="submit">
				<input type="submit" name="Submit"
				    value="<?php _e('Aktualisieren') ?> &raquo;" />
			    </p>
			</form>
			</div>
			<?php
		endif;
    }

    // Get the GMT offset from TZ

    function option_gmt_offset()
    {
	$offset = strftime('%z', time());
	if (substr($offset, 0, 1) == '-') {
	    $dir = -1;
	} else {
	    $dir = 1;
	}
	$hr = intval(substr($offset, -4, 2));
	$mn = intval(substr($offset, -2, 2));
	return $dir * ($hr + ($mn / 60));
    }
}

$wppTimeZone =& new TimeZone;

endif; // is_plugin_page()

?>
