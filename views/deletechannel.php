<?php
	$delchannel_yes = cp_url('addons_modules/show_module_cp', array(
			'module'	=> 'title_master',
			'method'	=> 'delchannel',
			'cid'		=> $channel_id,
			'del'		=> 'yes',
		)
	);
	$delchannel_no = cp_url('addons_modules/show_module_cp', array(
			'module'	=> 'title_master',
		)
	);
?>

<p>Are you sure you want to delete the Title Master Settings for the <strong><?=$channel_name?></strong> channel? (Note: No Channel Entries will be affected by removing these settings.)</p>

<p>
	<a href="<?=$delchannel_no?>" class="submit button">No</a>
	<a href="<?=$delchannel_yes?>" class="submit button">Yes</a>
</p>
<p style="clear:left">&nbsp;</p>