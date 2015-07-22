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
 * Title Master Accessory
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Accessory
 * @author		BlendIMC
 * @link		
 */
 require_once PATH_THIRD.'title_master/config.php'; 

 
class Title_master_acc {
	
	public $name			= 'Title Master';
	public $id				= 'title_master';
	public $version = TITLEMASTER_VERSION;
	public $description		= 'Hides Title';
	public $sections		= array();
	
	/**
	 * Set Sections
	 */
	public function set_sections()
	{
		$EE =& get_instance();
		
		
		$this->sections['Hide title'] = $EE->load->view('accessory_hide_title', '', TRUE);
		
	}
	
	// ----------------------------------------------------------------
	
}
 
/* End of file acc.title_master.php */
/* Location: /system/expressionengine/third_party/title_master/acc.title_master.php */