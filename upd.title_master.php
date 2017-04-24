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
 * Title Master Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		BlendIMC
 * @link		
 */
require_once PATH_THIRD.'title_master/config.php'; 

class Title_master_upd {
	
	public $version = TITLEMASTER_VERSION;
	public $module_name = 'title_master';
	private $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();		
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'			=> 'Title_master',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);
		
		$this->EE->db->insert('modules', $mod_data);
		
		$this->EE->load->dbforge();
		/**
		 * In order to setup your custom tables, uncomment the line above, and 
		 * start adding them below!
		 */
		 $sql = "CREATE TABLE IF NOT EXISTS`exp_title_master` (
				  `channel_id` int(11) NOT NULL,
				  `site_id` int(11) NOT NULL,
				  `title_tmpl` text NOT NULL,
				  `url_title_tmpl` text  NOT NULL,
				  `update_url` int(1) NOT NULL,
				  `increment` int(11) NOT NULL DEFAULT '1',
				  `update_structure` int(1) NOT NULL,
				  `title_len` int(5) DEFAULT '100',
				  `url_title_len` int(5) DEFAULT '75'
				) ";
		$result = $this->EE->db->query($sql);

		$sql = "INSERT INTO exp_actions (action_id, 
			class, 
			method) 
			VALUES 
			('', 
			'Title_master', 
			'new_auto_channel')
		";
		$result = $this->EE->db->query($sql);
		
        $fields = array(
                'entry_id'=>  array('type' => 'int',
                                                'constraint'    =>      '6',
                                                'unsigned'      =>      TRUE),
                'increment'  =>      array('type' => 'int',
                                                'constraint'    =>      '6',
                                                'unsigned'      =>      TRUE)
                );

        $this->EE->dbforge->add_field($fields);
        $this->EE->dbforge->create_table('title_master_entries');
		
		return TRUE;
	}

		// ********************************************************************************* //

	/**
	 * Updates the module
	 *
	 * This function is checked on any visit to the module's control panel,
	 * and compares the current version number in the file to
	 * the recorded version in the database.
	 * This allows you to easily make database or
	 * other changes as new versions of the module come out.
	 *
	 * @access public
	 * @return Boolean FALSE if no update is necessary, TRUE if it is.
	 **/
	public function update($current = '')
	{		
		// Are they the same?
		if ($current >= $this->version)
		{
			return FALSE;
		}

		// Load dbforge
		$this->EE->load->dbforge();


		// Do they have the Increment Field added in 1.2?
    	if ($current < '1.2')
		{
			 // Add the link_channel_id Column
    		if ($this->EE->db->field_exists('increment', 'title_master') == FALSE)
			{
				$fields = array( 'increment'	=> array('type' => 'INT','constraint' =>'11','unsigned' => TRUE,'null'=>FALSE, 'default' => 1) );
				$this->EE->dbforge->add_column('title_master', $fields);
			}

                $fields = array(
                        'entry_id'=>  array(
                    						// 'name'=>'entry_id',
                    						'type' => 'int',
                                            'constraint'    =>      '6',
                                            'unsigned'      =>      TRUE),
                        					'increment'  =>      array(
                    						'name'=>'increment',
                    						'type' => 'int',
                                            'constraint'    =>      '6',
                                            'unsigned'      =>      TRUE)
                        );

                $this->EE->dbforge->add_field($fields);
                $this->EE->dbforge->create_table('title_master_entries');
		}

		if ($current < '1.3.2'){
			
			$fields = array(
                'title_tmpl' => array(
                     'name' => 'title_tmpl',
                     'type' => 'TEXT',
            	),
				'url_title_tmpl' => array(
                     'name' => 'url_title_tmpl',
                     'type' => 'TEXT',
            	)
			);

			$this->EE->dbforge->modify_column('title_master', $fields);

			//Add Update Structure Field
			$fields = array('update_structure' =>  array('type' => 'int','constraint' =>'1','unsigned'=>TRUE));
            $this->EE->dbforge->add_column('title_master',$fields);

		}

		if ($current < '1.4'){
			$fields = array(
				'title_len' =>  array(
					'type' => 'int','constraint' =>'5','default' => 100
				),
				'url_title_len' =>  array(
					'type' => 'int','constraint' =>'5','default' => 75
				)
			);
            $this->EE->dbforge->add_column('title_master',$fields);
		}


		if ($current < '1.5.8'){

				$fields = array(
                'title_tmpl' => array(
                     'name' => 'title_tmpl',
                     'type' => 'TEXT',
                     'null' => TRUE,
            	),
				'url_title_tmpl' => array(
                     'name' => 'url_title_tmpl',
                     'type' => 'TEXT',
                     'null' => TRUE,
            	),
            	'update_url' => array(
                     'name' => 'update_url',                    
                     'type' => 'INT',
                     'constraint' => 1,
                     'null' => TRUE,
                ),
                'update_structure' => array(
                     'name' => 'update_structure',                    
					 'type' => 'INT',
					 'constraint' => 1,
                     'null' => TRUE,
                ),                
			);

			$this->EE->dbforge->modify_column('title_master', $fields);


		}


		// Upgrade The Module
		$this->EE->db->set('module_version', $this->version);
		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->update('exp_modules');

		return TRUE;
	}


	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{
		$mod_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Title_master'
								))->row('module_id');
		
		$this->EE->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Title_master')
					 ->delete('modules');
		
		 $this->EE->load->dbforge();
		// Delete your custom tables & any ACT rows 
		// you have in the actions table
		 $sql = "DROP TABLE IF EXISTS exp_title_master";
		$result = $this->EE->db->query($sql);
		 $sql = "DROP TABLE IF EXISTS exp_title_master_entries";
		$result = $this->EE->db->query($sql);
		 $sql = "DELETE from exp_actions WHERE class = 'exp_title_master'";
		$result = $this->EE->db->query($sql);
		
		return TRUE;
	}

}
/* End of file upd.title_master.php */
/* Location: /system/expressionengine/third_party/title_master/upd.title_master.php */