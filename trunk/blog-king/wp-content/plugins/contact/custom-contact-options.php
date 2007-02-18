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

require_once('../wp-content/plugins/contact/custom-contact.php');

custom_contact_init();

// Button: "Delete Field"
if(isset($_POST['delete'])){
	custom_contact_delete_field();
}
// Button: "Add Field"
else if ($_POST['action']=='add') {
	custom_contact_add_field();
}
// Button: "Update Field"
else if( $_POST['action']=='update'){
	custom_contact_update_field();
}
else if ($_POST['action']=='update_options'){
	custom_contact_update_options();
}



custom_contact_build_admin_form();

function custom_contact_update_options() {
	global $custom_contact_tbl_fields, $wpdb, $user_level;

	get_currentuserinfo();

	if ($user_level < 10) {
		die ( __('Are You Cheatin&#8217; uh?') );
	}
	update_option('custom_contact_success_msg', $_POST['custom_contact_success_msg']);
	update_option('custom_contact_required_msg', $_POST['custom_contact_required_msg']);
	update_option('custom_contact_email_subject', $_POST['custom_contact_email_subject']);

	echo "<div style='background-color: rgb(207, 235, 247);' id='message' class='updated fade'><p><strong>";
	_e('Options Updated Successfully','Localization name');
	echo "</strong></p></div>";
}
function custom_contact_add_field(){
	global $custom_contact_tbl_fields, $wpdb, $user_level;

	get_currentuserinfo();

	if ($user_level < 10) {
		die ( __('Are You Cheatin&#8217; uh?') );
	}

	$sql = "INSERT INTO $custom_contact_tbl_fields (label, required, input_type, form_position, text_max_chars, options) values (";
	$sql .= "'".xyooj_esc_quote($_POST['label'])."', ";
	$sql .= ($_POST['required']=='true'?1:0).", ";
	$sql .= ($_POST['input_type']).", ";
	$sql .= ($_POST['form_position']).", ";
	//$sql .= ($_POST['text_max_chars']).", ";
	$sql .= "0, ";
	$sql .= "'" . xyooj_esc_quote($_POST['options'])."') ";

	if ($wpdb->query($sql)==1) {
		echo "<div style='background-color: rgb(207, 235, 247);' id='message' class='updated fade'><p><strong>";
		_e('Field Added Successfully','Localization name');
		echo "</strong></p></div>";
	}

}

function custom_contact_update_field(){
	global $custom_contact_tbl_fields, $wpdb, $user_level;

	get_currentuserinfo();

	if ($user_level < 10) {
		die ( __('Are You Cheatin&#8217; uh?') );
	}

	$sql = "UPDATE $custom_contact_tbl_fields SET ";
	$sql .= "label = '".xyooj_esc_quote($_POST['label'])."', ";
	$sql .= "required = ".($_POST['required'] == 'true' ? 1:0).", ";
	$sql .= "input_type = ".($_POST['input_type']).", ";
	$sql .= "form_position = ".($_POST['form_position']).", ";
	//$sql .= "text_max_chars = ".($_POST['text_max_chars']).", ";
	$sql .= "text_max_chars = 0, ";
	$sql .= "options = '".(xyooj_esc_quote($_POST['options']))."' ";
	$sql .= "WHERE id = ".($_POST['id'])." ";

	if ($wpdb->query($sql)==1) {
		echo "<div style='background-color: rgb(207, 235, 247);' id='message' class='updated fade'><p><strong>";
		_e('Field Updated Successfully','Localization name');
		echo "</strong></p></div>";
	}

}

function custom_contact_delete_field(){
	global $custom_contact_tbl_fields, $wpdb, $user_level;

	get_currentuserinfo();

	if ($user_level < 10) {
		die ( __('Are You Cheatin&#8217; uh?') );
	}

	$sql = "DELETE FROM $custom_contact_tbl_fields WHERE id = ".$_POST['id'];

	if ($wpdb->query($sql)==1) {
		echo "<div style='background-color: rgb(207, 235, 247);' id='message' class='updated fade'><p><strong>";
		_e('Field Deleted Successfully','Localization name');
		echo "</strong></p></div>";
	}
}

// ===================== Admin form ====================

function custom_contact_build_admin_form(){
?>
		<div class="wrap">
			<h2>Options</h2>
			<form name="doptions" method="post">
				<input type="hidden" name="action" value="update_options" />
				<fieldset name="new" class="options">
					<table summary="Field" class="editform" cellpadding="5" cellspacing="2" style="width:100%;vertical-align:top;">
						<tr>
							<th scope="row">Successs Response: </th>
							<td><input name="custom_contact_success_msg" max_length="64" value="<?php echo get_option('custom_contact_success_msg'); ?>" size="40" class="code" type="text" />
							</td>
						</tr>
						<tr>
							<th scope="row">Email Subject: </th>
							<td><input name="custom_contact_email_subject" max_length="64" value="<?php echo get_option('custom_contact_email_subject'); ?>" size="40" class="code" type="text" />
							</td>
						</tr>
						<tr>
							<th scope="row">Missing Required Field Message: </th>
							<td><input name="custom_contact_required_msg" max_length="64" value="<?php echo get_option('custom_contact_required_msg'); ?>" size="40" class="code" type="text" />
							</td>
						</tr>
					</table>
				</fieldset>
				<div class="submit">
					<input type="submit" name="update_options" value="Update Options" title="Submit and saves changes" />
				</div>
			</form>
			<h2>Add Field</h2>
			<?php custom_contact_build_field_form(null, "add") ?>
			<h2>Modify Fields</h2>
			<?php
			
			$fields = custom_contact_get_field_defs();
			if ($fields != null) {
				foreach ($fields as $field) {
					custom_contact_build_field_form($field, "update");
				}
			}
			?>
		</div>
<?php
}
function custom_contact_build_field_form($custom_contact_field, $custom_contact_action) {
?>
				<form name="custom_contact_options" method="post">
				<input type="hidden" name="action" value="<?php echo $custom_contact_action ?>" />
				<input type="hidden" name="id" value="<?php echo $custom_contact_field->id ?>" />

				<fieldset name="new" class="options">
					<table summary="Field" class="editform" cellpadding="5" cellspacing="2" style="width:100%;vertical-align:top;">
						<tr>
							<th scope="row">Field Label: </th>
							<td><input name="label" max_length="64" value="<?php if($custom_contact_field != null) echo xyooj_unesc_quote($custom_contact_field->label); ?>" size="40" class="code" type="text" />
							</td>
						</tr>
						<tr>
							<th scope="row">Required: </th>
							<td>
								<input type="checkbox" name="required" value="true" <?php if($custom_contact_field != null && $custom_contact_field->required == 1) echo "checked='checked'"; ?> /> 
							</td>
						</tr>
						<tr>
							<th scope="row">Input Type: </th>
							<td>
								<select name="input_type">
									<option value="0" <?php if ($custom_contact_field != null && $custom_contact_field->input_type == '0') echo 'selected="selected"'; ?>>Text Box</option>
									<option value="1" <?php if ($custom_contact_field != null && $custom_contact_field->input_type == '1') echo 'selected="selected"'; ?>>Text Area</option>
									<option value="2" <?php if ($custom_contact_field != null && $custom_contact_field->input_type == '2') echo 'selected="selected"'; ?>>Select/Option</option>
									<option value="3" <?php if ($custom_contact_field != null && $custom_contact_field->input_type == '3') echo 'selected="selected"'; ?>>Checkbox</option>
								</select>
								Options: <input type="text" name="options" value="<?php if ($custom_contact_field != null) echo $custom_contact_field->options ; ?>"/> comma separated, i.e. Sales,Marketing,Tech Support,Press Contact,Other 
							</td>
						</tr>
						<tr>
							<th scope="row">Form Position: </th>
							<td>
								<input type="text" name="form_position" value="<?php if($custom_contact_field == null) {echo "0";} else {echo $custom_contact_field->form_position;}?>" size="4" maxlength="4" class="code" />
							</td>
						</tr>
						<!--tr>
							<th scope="row">Text Max Num Characters: </th>
							<td>
								<input type="text" name="text_max_chars" value="<?php if($custom_contact_field == null) {echo "4096";} else {echo $custom_contact_field->text_max_chars;}?>" size="9" maxlength="4" class="code" /> 
							</td>
						</tr-->
					</table>
				</fieldset>
				<div class="submit">
				<?php if ($custom_contact_field != null) { ?>
					<input type="submit" name="delete" value="<?php	_e('Delete Field', 'Localization name');?>" title="Delete Field" onclick="javascript: return confirm('Are you sure ?');"  />&nbsp;&nbsp;&nbsp;
				<?php } ?>
					<input type="submit" name="update_options" value="<?php	_e($custom_contact_action=='add'?'Add Field':'Modify Field', 'Localization name')?>" title="Submit and saves changes" />
				</div>

			</form>
			<?php if ($custom_contact_field != null) echo "<hr/>"; ?>

<?php
}
?>