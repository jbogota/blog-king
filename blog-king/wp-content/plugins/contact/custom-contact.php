<?php
/*
Plugin Name: Custom Contact
Plugin URI: http://www.xyooj.com
Description: Allow Custom/Extended Contact Us Page
Date: 2006, February, 9
Author: VaamYob
Author URI: http://www.xyooj.com
Version: 0.1
*/

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


require_once(ABSPATH . '/wp-content/plugins/contact/common.php');
define('CC_TEXT_FIELD',0);
define('CC_TEXTAREA_FIELD',1);
define('CC_SELECT_FIELD',2);
define('CC_CHECKBOX_FIELD',3);

add_action('wp_footer', 'custom_contact_wp_head');
add_action('admin_menu', 'custom_contact_admin_pages');
add_filter('the_content', 'custom_contact_content');

function custom_contact_wp_head($content_stuff) {

	$fields = custom_contact_get_field_defs();
?>
<script type="text/javascript">
	function customContactEmail() {
		var errMsg = "";
<?php

		foreach ($fields as $field) {
			if ($field->required == 1) {
?>
		var field = document.getElementById('field_<?php echo $field->id; ?>');

		if (field.value == null || field.value == '') {
			errMsg += "\n<?php echo $field->label ?>";
		}
			<?php
			}
		}
		?>
		if (errMsg == "") {
			var url = '<?php echo xyooj_get_plugins_url(); ?>/contact/custom-contact-email.php';
			var parms = new Array();
<?php
		$index = 0;
		foreach ($fields as $field) {
			$field_id = 'field_' . $field->id;
			echo "parms[$index] = new Array('$field_id', document.getElementById('$field_id').value);\n";
			$index++;
		}
?>
		        AJAXPost(url, parms, handleContactEmail);
		} else {
			alert("<?php echo get_option('custom_contact_required_msg'); ?>" + errMsg);
			return false;
		}
	}
	function handleContactEmail(code, message, data) {
		alert(message);
		if (code == 0) {
<?php
	foreach ($fields as $field) {
		$field_id = $field->id;
		echo "document.getElementById('field_$field_id').value = '';\n";
	}
?>
		}
	}
</script>
<?php
}
function custom_contact_admin_pages() {
	add_options_page('Kontakt', 'Kontakt', 8, 'contact/custom-contact-options.php');
}
// Initialize all variables
custom_contact_init();
$custom_contact_tbl_fields="";
function custom_contact_init(){
	global $table_prefix, $wpdb, $custom_contact_tbl_fields;

	$custom_contact_tbl_fields = $table_prefix.'custom_contact_fields';
	$custom_contact_success_msg = get_option('custom_contact_success_msg');
	if ($custom_contact_success_msg == null) {
		add_option('custom_contact_success_msg', 'Danke für Ihre Kontaktanfrage.');
	}
	$custom_contact_required_msg = get_option('custom_contact_required_msg');
	if ($custom_contact_required_msg == null) {
		add_option('custom_contact_required_msg', 'Ein erforderliches Feld ist leer:');
	}
	$custom_contact_email_subject = get_option('custom_contact_email_subject');
	if ($custom_contact_email_subject == null) {
		add_option('custom_contact_email_subject', 'Eine Nachricht für Sie');
	}
	if($wpdb->get_var("show tables like '$custom_contact_tbl_fields'") != $custom_contact_tbl_fields) {
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		$sql = "CREATE TABLE ".$custom_contact_tbl_fields."(
				`id` int(5) NOT NULL auto_increment,
				`label` varchar(64) NOT NULL default '',
				`required` tinyint(1) NOT NULL default '0',
				`input_type` tinyint(1) NOT NULL default '0',
				`text_max_chars` int(9) NOT NULL default '4096',
				`form_position` int(9) NOT NULL default '0',
				`options` text default '',
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM;";
		dbDelta($sql);
		$wpdb->query("INSERT INTO `$custom_contact_tbl_fields` (`label`, `required`, `input_type`, `form_position`, `text_max_chars`) VALUES ('Ihr Name', 1, " . CC_TEXT_FIELD . ", 0, 0)");
		$wpdb->query("INSERT INTO `$custom_contact_tbl_fields` (`label`, `required`, `input_type`, `form_position`, `text_max_chars`) VALUES ('Ihre Email-Adresse', 1, " . CC_TEXT_FIELD . ", 1, 0)");
		$wpdb->query("INSERT INTO `$custom_contact_tbl_fields` (`label`, `required`, `input_type`, `form_position`, `text_max_chars`) VALUES ('Ihre Website', 0, " . CC_TEXT_FIELD . ", 2, 0)");
		$wpdb->query("INSERT INTO `$custom_contact_tbl_fields` (`label`, `required`, `input_type`, `form_position`, `text_max_chars`) VALUES ('Ihre Nachricht', 1, " . CC_TEXTAREA_FIELD . ", 3, 0)");
	}

}

function custom_contact_content($content) {
	if(stristr($content, '<!--contact-form-->') === FALSE) {
		return $content;
	} else {
		$search_for = array('<!--contact-form-->');
		$replace_with = array(custom_contact_get_form());
		$retVal = str_replace($search_for,$replace_with, $content);
		return $retVal;
	}
}
function custom_contact_get_form() {
	$fields = custom_contact_get_field_defs();
	$html = '<div class="custom_contact_form">'."\n";
	foreach ($fields as $field) {
		if ($field->required == 1) {
			$required_subclass = '_required';
		} else {
			$required_subclass = '';
		}
		$html .='<div class="custom_contact_row">'."\n";
		if ($field->input_type != CC_CHECKBOX_FIELD) {
			$html .='<div class="custom_contact_label' . $required_subclass . '">';
			$html .= xyooj_unesc_quote($field->label);
			$html .='</div><!-- label -->';
		}
		$html .='<div class="custom_contact_data">';
		if ($field->input_type == CC_TEXT_FIELD) {
			$html .= '<input type="text" id="field_' . $field->id . '" name="field_' . $field->id . '" />';
		} else if ($field->input_type == CC_TEXTAREA_FIELD) {
			$html .= '<textarea id="field_' . $field->id . '" name="field_' . $field->id . '" rows="3" cols="50"></textarea>';
		} else if ($field->input_type == CC_CHECKBOX_FIELD) {
			$html .= '<input type="checkbox" value="Yes" id="field_' . $field->id . '" name="field_' . $field->id . '" />';
		} else if ($field->input_type == CC_SELECT_FIELD) {
			$html .= '<select id="field_' . $field->id . '" name="field_' . $field->id . '" >';
			$options = explode(',', xyooj_unesc_quote($field->options));
			foreach ($options as $option) {
				$html .= '<option value="' . $option . '">' . $option . '</option>';
			}
			$html .= '</select>';
		}
		$html .='</div><!-- data -->';
		if ($field->input_type == CC_CHECKBOX_FIELD) {
			$html .='<div class="custom_contact_label' . $required_subclass . '">';
			$html .= xyooj_unesc_quote($field->label);
			$html .='</div><!-- label -->';
		}
		$html .="\n</div><!-- row --><br/>\n";
	}
	$html .='<div class="custom_contact_buttons"><input type="button" value="Senden" onclick="customContactEmail(); return false;"/></div>'."\n";
	$html .='</div><!-- form -->';
	return $html;
}
function custom_contact_get_field_defs() {
	global $custom_contact_tbl_fields, $wpdb, $user_level;
	custom_contact_init();
	$fields = null;
	return $wpdb->get_results("SELECT * FROM $custom_contact_tbl_fields ORDER BY form_position");
}
?>