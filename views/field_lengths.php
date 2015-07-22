<p>Use the following form to change the length of the DB Fields to allow longer Titles/URL Titles.</p>
<?php
echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=title_master'.AMP.'method=field_lengths');
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading('Field','Length');

foreach ($field_lengths as $field => $length)
{
	  $this->table->add_row("<strong>".lang($field).'</strong>','<input type="text" name="'. $field.'" value="'.$length.'" />');			
}

	  $this->table->add_row("<strong>Caution:</strong> Changing these values will alter the field length at the database level for all sites on this installation.",'<input type="submit" name="change_field_len" class="submit" value="Submit" />');			
echo $this->table->generate();
echo form_close();
?>



<?php $this->table->clear()?>
