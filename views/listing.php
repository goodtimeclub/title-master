<?php 
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading('Channel','Delete');

foreach ($settings as $channel => $data)
{
	$delLink = $delChannelUrl . $data['channel_id'];
  
	$this->table->add_row('<a href="'.$settingsChannelUrl.$data['channel_id'].'"><strong>'.$data['channel'].'</strong></a>','<a  href="'.$delLink.'">Remove Channel</a>');			
}


echo $this->table->generate();



