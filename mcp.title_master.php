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
 * Title Master Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		BlendIMC
 * @link
 */

require_once PATH_THIRD.'title_master/mod.title_master.php';


class Title_master_mcp {

	public $return_data;

	private $_base_url;
	var $AT;
	private $site_id;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		define('CP_URL', ee()->config->item('cp_url'));
		define('CP_ADDON_URL', CP_URL.'/cp/addons_modules/show_module_cp?module=title_master');

		$this->_base_url = CP_URL.'/cp/addons_modules/show_module_cp?module=title_master';

		ee()->cp->set_right_nav(array(
			'module_home'	=> cp_url('addons_modules/show_module_cp', array(
				'module' => 'title_master',
				)
			),
			'field_length'	=> cp_url('addons_modules/show_module_cp', array(
				'module' => 'title_master',
				'method' => 'field_lengths',
				)
			),
			'add_channel'	=> cp_url('addons_modules/show_module_cp', array(
				'module' => 'title_master',
				'method' => 'addchannel',
				)
			)


			// Add more right nav items here.
		));

		$this->AT = new Title_master();
		$this->site_id = ee()->config->item('site_id');
	}

	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return	void
	 */
	public function index()
	{
		ee()->cp->load_package_js('jquery.simplemodal');
		$this->_set_cp_var('cp_page_title', lang('title_master_module_name'));

		ee()->load->library('table');
		$vars = array();

		$sql = "SELECT channel_title as channel,c.channel_id as channel_id
			 FROM exp_title_master as t
			 JOIN exp_channels as c on c.channel_id = t.channel_id
			 WHERE c.site_id = $this->site_id
			 ORDER BY  channel_title asc";
		$result = ee()->db->query($sql);
		$settings = array();

		if ($result->num_rows() > 0)
		{
		    foreach($result->result_array() as $row)
		    {
				$settings[$row['channel']] = $row;

		    }
		} else {
			//If no Channels, Redirect to the Add Channel Page
			ee()->functions->redirect(
				cp_url('addons_modules/show_module_cp', array(
					'module' => 'title_master',
					'method' => 'addchannel',
					)
				)
			);
		}
		$vars['settings'] = $settings;

		$vars['settingsChannelUrl'] = cp_url('addons_modules/show_module_cp', array(
			'module' => 'title_master',
			'method' => 'settings',
			)
		) .AMP.'cid=';
		$vars['newChannelUrl'] = $this->AT->_new_channel_url();
		$vars['delChannelUrl'] = cp_url('addons_modules/show_module_cp', array(
			'module' => 'title_master',
			'method' => 'delchannel',
			)
		) .AMP.'cid=';

		return ee()->load->view('listing', $vars, TRUE);
	}

	public function field_lengths()
	{
		ee()->view->cp_page_title = lang('field_length_page');

		$vars = array();

		$vars['field_lengths'] = $this->_get_field_legnths();

		ee()->load->helper('form');

		ee()->load->library('table');

		if(isset($_POST['change_field_len'])){
			$vars['field_lengths'] = $this->_update_field_lengths(ee()->input->post('title_len'),ee()->input->post('url_title_len'),TRUE);
		}

		return ee()->load->view('field_lengths', $vars, TRUE);
	}

	public function settings()
	{
		ee()->view->cp_page_title = lang('channel_settings_page');

		//Load Data for Form and Form Processing
		$vars = array();

		if(is_numeric($_GET['cid'])){
			$cid = $_GET['cid'];
		} else {
			$cid = 1;
		}

		$sql = "SELECT channel_title as channel,channel_name,c.channel_id,title_tmpl,url_title_tmpl,update_url,update_structure,increment,title_len,url_title_len
			FROM exp_title_master as t
			JOIN exp_channels as c on c.channel_id = t.channel_id
			WHERE c.channel_id = '$cid'";

		$result = ee()->db->query($sql);
		$settings = array();

		if ($result->num_rows() > 0)
		{
		    foreach($result->result_array() as $row)
		    {
		 	$cur_channel = $row['channel'];
				$settings[$row['channel']] = $row;

		    }
		}
		$vars['settings'] = $settings;
		$vars['cid'] = $cid;

		//Process Form
		if(isset($_POST['submit'])){
			$submit = $_POST['submit'];
			unset($_POST['submit']);
			$existing = $_POST['existing'];
			unset($_POST['existing']);

			foreach($_POST as $channel => $data){

				if(is_array($data)){

					ee()->db->where('channel_id', $data['channel_id']);
					ee()->db->update('title_master', $data);

					$this->_update_field_lengths($data['title_len'],$data['url_title_len']);

					switch ($existing) {
						case 'titles':
							$this->AT->_update_channel($data['channel_id']);
						break;
						case 'both':
							$this->AT->_update_channel($data['channel_id'],TRUE);
						break;
					}
				}
			}

			ee()->session->set_flashdata(
				'message_success',
				ee()->lang->line('preferences_updated')
			);

			//If Finished, Return to All Channels, or else just show form again
			if($submit == 'Submit and Finished'){
				ee()->functions->redirect(
						cp_url('addons_modules/show_module_cp', array(
							'module' => 'title_master'
						)
					)
				);
			} else {//Redirect so Flash Data Shows Success
				ee()->functions->redirect(
						cp_url('addons_modules/show_module_cp', array(
							'module' => 'title_master',
							'method' => 'settings',
							'cid' => $data['channel_id'],
						)
					)
				);
			}
		}

		//Display Form
		ee()->load->helper('form');
		ee()->load->library('table');

		//Get the Field Data from the channel
		$result = ee()->db->query("select field_id,field_name,field_type
			from exp_channel_fields as f
			join exp_channels as c on c.field_group = f.group_id
			where channel_id = ?",$cid);

		if ($result->num_rows() > 0)
		{
		    foreach($result->result_array() as $row)
		    {
				$vars['fields'][] = $row;
		    }
		}

		return ee()->load->view('settings', $vars, TRUE);
	}

	public function addchannel()
	{

		$cp_url = ee()->config->item('cp_url');

		ee()->load->library('table');
		$this->_set_cp_var('cp_page_title', lang('add_channel_page'));

		if(!isset($_GET['cid'])){//If no Id, Show channel Picker

			$vars = array();
			$sql = "SELECT channel_title,channel_id
				FROM exp_channels WHERE channel_id NOT IN (SELECT DISTINCT channel_id FROM exp_title_master) AND site_id = '$this->site_id' ORDER BY  channel_title asc";
			$channels = array();
			$result = ee()->db->query($sql);

			if ($result->num_rows() > 0)
			{
			    foreach($result->result_array() as $row)
			    {
					$channels[] = $row;
			    }
			}
			$vars['channels'] = $channels;
			$vars['newChannelUrl'] = cp_url('addons_modules/show_module_cp', array(
					'module' => 'title_master',
					'method' => 'addchannel',
				)
			);

			return ee()->load->view('channelpicker', $vars,TRUE);

		} else {//Else, Add Channel and return the Table Rows
			ee()->load->helper('form');
			ee()->load->library('table');
			ee()->lang->loadfile('title_master');
			$channel_id = ee()->input->get('cid');
			$vars = array();

			//Check to see if Channel Exists Already
			$sql = "SELECT channel_title as channel,c.channel_id,title_tmpl,url_title_tmpl
			FROM exp_title_master as t
			JOIN exp_channels as c on c.channel_id = t.channel_id
			WHERE c.channel_id = $channel_id";
			$result = ee()->db->query($sql);


			//If not Add Channel
			if ($result->num_rows() < 1){

				$sql = "INSERT INTO exp_title_master (site_id,channel_id) VALUES ($this->site_id,$channel_id)";
				$result = ee()->db->query($sql);


			}

			ee()->functions->redirect(
				cp_url('addons_modules/show_module_cp', array(
					'module' => 'title_master',
					'method' => 'settings',
					'cid' => $channel_id,
					)
				)
			);
		}
	}

	public function delchannel()
	{
		ee()->view->cp_page_title = lang('del_channel_page');
		$cp_url = ee()->config->item('cp_url');

		if(isset($_GET['del'])){
			if(is_numeric($_GET['cid'])){
				$cid = $_GET['cid'];
				$sql = "DELETE FROM exp_title_master WHERE channel_id = $cid";
				$result = ee()->db->query($sql);
			}
			ee()->functions->redirect(
					cp_url('addons_modules/show_module_cp', array(
						'module' => 'title_master',
					)
				)
			);
		} else {
			$vars['channel_id'] = mysql_real_escape_string($_GET['cid']);
			$sql = "SELECT channel_title FROM exp_channels WHERE channel_id = ".$vars['channel_id'];
			$result = ee()->db->query($sql);
			$vars['channel_name'] = $result->row('channel_title');
			
			$vars['url'] = CP_URL.'/cp/addons_modules/show_module_cp?module=title_master';
			return ee()->load->view('deletechannel', $vars,TRUE);
		}
   }

   private function _get_field_legnths()
   {
		$sql = "describe exp_channel_titles";
		$result = ee()->db->query($sql);

		foreach($result->result_array() as $row){
			if($row['Field'] == 'title'){
				$title_len = substr($row['Type'],strpos($row['Type'],'(')+1,-1);
			}
			if($row['Field'] == 'url_title'){
				$url_title_len = substr($row['Type'],strpos($row['Type'],'(')+1,-1);
			}
		}

		$data = array('title_len' => $title_len,'url_title_len'=>$url_title_len);
		return $data;
	}

	private function _update_field_lengths($title_len,$url_title_len,$force_update = FALSE)
	{
		$lengths = $this->_get_field_legnths();

		//Check the Need to update Field Langes
		$fields = array();

		if(($title_len > $lengths['title_len']) OR $force_update){
			$fields['title'] = array('name'=>'title','type' => 'VARCHAR','constraint' => $title_len ,'null'=> FALSE);
		}
		if(($url_title_len > $lengths['url_title_len']) OR $force_update){
			$fields['url_title'] = array('name'=>'url_title','type' => 'VARCHAR','constraint' => $url_title_len,'null'=> FALSE);
		}

		if($force_update){//If we are shrinking the fields, then we can't let any channels have titles longer than the DB Field Length. Only run on force update
			ee()->db->query("update exp_title_master set title_len = IF(title_len > $title_len,$title_len,title_len),url_title_len = IF(url_title_len > $url_title_len,$url_title_len,url_title_len)");
		}

		if(!empty($fields)){
					 ee()->load->dbforge();
			ee()->dbforge->modify_column('channel_titles', $fields);
		}

		return $this->_get_field_legnths();
	}

	/**
	 * Set cp var
	 *
	 * @access		private
	 * @param		string
	 * @param		string
	 * @return		void
	 */
	private function _set_cp_var($key, $val)
	{
		if (version_compare(APP_VER, '2.6.0', '<'))
		{
			ee()->cp->set_variable($key, $val);
		}
		else
		{
			ee()->view->$key = $val;
		}
	}
}
/* End of file mcp.title_master.php */
/* Location: /system/expressionengine/third_party/title_master/mcp.title_master.php */