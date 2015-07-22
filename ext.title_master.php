<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Title Master Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		BlendIMC
 * @link		
 */


require_once PATH_THIRD.'title_master/config.php'; 
require_once PATH_THIRD.'title_master/mod.title_master.php'; 


class Title_master_ext {
	
	public $settings 		= array();
	public $description		= 'Fills in the Title Automatically';
	public $docs_url		= '';
	public $name			= 'Title Master';
	public $settings_exist	= 'n';
	public $version			= TITLEMASTER_VERSION;
	var $AT;
	
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
		$this->AT = new Title_master();		
		
	}
	

	// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'updateTitle',
			'hook'		=> 'entry_submission_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);	
		
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'updateMetaTitle',
			'hook'		=> 'entry_submission_ready',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);
					
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * updateTitle
	 *
	 * @param 
	 * @return 
	 */
	public function updateTitle($entry_id,$meta,$data)
	{

		//Get Channel
		$channel_id = $meta['channel_id'];
	
		//Get Template for Channel
		$tmpls = $this->AT->_get_tmpls_for_channel($channel_id);		

		if($tmpls){
			
			$sql = "SELECT update_url FROM exp_title_master WHERE channel_id = '$channel_id'";
			$result = $this->EE->db->query($sql);					
			$update_url = $result->row('update_url');
						

			$sep = ($this->EE->config->config['word_separator'] == 'underscore' ? "_" : "-");

			if(!($data['entry_id'] == 0 OR $update_url == '1' OR (strpos($meta['url_title'],'autofill' . $sep . 'title') > -1))){				

				$tmpls['url_title_tmpl'] = FALSE;		    	
			  //$this->AT->_update_entry($entry_id,$tmpls['title_tmpl'],FALSE,$channel_id);				
			
		  	}

		  	$titles = $this->AT->_update_entry($entry_id,$tmpls['title_tmpl'],$tmpls['url_title_tmpl'],$channel_id);  							
		  
  		}	
	
		 
	}

	// ----------------------------------------------------------------------
	
	/**
	 * updateTitle
	 *
	 * @param 
	 * @return 
	 */
	public function updateMetaTitle($meta, $data, $autosave)
	{				


		if(!$autosave){								
						
	      if(!isset($data['XID'])){//If its set, were dealing with a Safe Cracker form, and we don't want to run this
									
				//Get Channel
				$channel_id = $meta['channel_id'];

				//Get Template for Channel
				$tmpls = $this->AT->_get_tmpls_for_channel($channel_id);

			    if(!empty($tmpls['url_title_tmpl']) AND $tmpls['url_title_tmpl'] != '{url_title}'){

					$sql = "SELECT update_url FROM exp_title_master WHERE channel_id = '$channel_id'";
					$result = $this->EE->db->query($sql);		

					$update_url = $result->row('update_url');

					if($data['entry_id'] == 0 OR $update_url == '1'){

						$new_url = $this->AT->_update_entry_b4_submit($data,$tmpls['url_title_tmpl'],$channel_id);  		

						$this->EE->api_channel_entries->data['revision_post']['url_title'] = $new_url;
						$this->EE->api_channel_entries->meta['url_title'] = $new_url;
								
					}			
						  
				}else{//Update URL Title for longer Titles

					$new_url = $data['revision_post']['url_title'];
					$this->EE->api_channel_entries->meta['url_title'] = $new_url;


				}
			
			}
				
		}
		

	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
	


	
}

/* End of file ext.title_master.php */
/* Location: /system/expressionengine/third_party/title_master/ext.title_master.php */