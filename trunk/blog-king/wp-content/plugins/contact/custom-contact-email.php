<?php
/*  
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

require_once('../../../wp-blog-header.php');
require_once('custom-contact.php');

custom_contact_init();
header("Content-type: text/xml;");
$fields = custom_contact_get_field_defs();
$email_body = "";
foreach ($fields as $field) {
	$field_value = $_REQUEST['field_' . $field->id];
	if ($field->required == 1 && $field_value == null) {
		return_required_msg();
		return;
	}
	$email_body .= xyooj_unesc_quote($field->label) . ": " . $field_value . "\n";
}

$email_subject = get_option('custom_contact_email_subject');
wp_mail(get_settings('admin_email'), "$email_subject", $email_body);
return_success_msg();

function return_success_msg() {
	$msg = get_option('custom_contact_success_msg');
	echo "<response><code>0</code><message>$msg</message></response>";
}
function return_required_msg() {
	$msg = get_option('custom_contact_required_msg');
	echo "<response><code>-1</code><message>$msg</message></response>";
}
?>
