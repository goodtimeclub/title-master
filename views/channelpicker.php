<?php
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading('Channel');

foreach($channels as $channel){
	$link = $newChannelUrl.'&cid='.$channel['channel_id'];
	$this->table->add_row('<a href="'.$link.'">'.$channel['channel_title'].'</a>');	
}
echo $this->table->generate();


?>
