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
 * Title Master Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		BlendIMC
 * @link		
 */

class Title_master {
	
	public $return_data;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
			$this->site_id = $this->EE->config->item('site_id');		
	}


	/**
	 * Get Templates for the Channel 
	 *	 
	 */
	function _get_tmpls_for_channel($channel_id){	
		$sql = "SELECT title_tmpl,url_title_tmpl FROM exp_title_master WHERE channel_id = '$channel_id'";
		$result = $this->EE->db->query($sql);
		if ($result->num_rows() > 0)
		{
		    foreach($result->result_array() as $row)
		    {
				$tmpls = $row;
		    }
		}else{
		  return FALSE;
		}
		
		//Check to make sure they are not both blank
		// if($tmpls['url_title_tmpl']  == ""){
		// 	if($tmpls['title_tmpl'] == ""){
		// 		$tmpls['url_title_tmpl'] = FALSE;
		// 		$tmpls['title_tmpl'] = FALSE;
		// 	}else{
		// 		$tmpls['url_title_tmpl'] = $tmpls['title_tmpl'];
		// 	}			
		// }
		
		return $tmpls;				
	}
	
	function _new_channel_url(){
			
		$new_id = $this->EE->db->where(array(
			'class' => 'Title_master',
			'method' => 'new_auto_channel'))
			->get('actions')
			->row('action_id');

	  	$newChannelUrl = BASE.AMP.'ACT='.$new_id;
	
		return $newChannelUrl;
		
	}	
	
	function _del_channel_url(){
			
		$del_id = $this->EE->db->where(array(
			'class' => 'Title_master',
			'method' => 'remove_auto_channel'))
			->get('actions')
			->row('action_id');
			
	  	$delChannelUrl = BASE.AMP.'ACT='.$del_id.AMP.'id=';
	
		return $delChannelUrl;
		
	}	
	

	// --------------------------------------------------------------------

	/**
	 * Update Entry - Updates entry with Template
	 *
	 */

	function _update_entry($entry_id,$title_tmpl,$url_title_tmpl,$channel_id){
		

		$title_data = array(); 
		$title_tmpl = ($title_tmpl ? $title_tmpl : '{title}');
		$url_title_tmpl = ($url_title_tmpl ? $url_title_tmpl : '{url_title}');

		
  		//Parse The New Title
  		$new_title = $this->_parse_tmpl($title_tmpl,$entry_id);	
  		if($new_title != ""){  			
  		    $title_len = $this->EE->db->query("SELECT title_len FROM exp_title_master WHERE channel_id = ? AND site_id = ?",array($channel_id,$this->site_id))->row('title_len');  					
  		    $new_title = substr($new_title,0,$title_len);
			$title_data['title'] = $new_title;
  		}

	    //Parse URL, Lowercase, Get Word Seperator, Replaces Spaces,  Strip Non Good Characters

		$new_url = trim(strtolower($this->_parse_tmpl($url_title_tmpl,$entry_id)));	
  		if($new_url != ""){

			$sep = ($this->EE->config->config['word_separator'] == 'underscore' ? "_" : "-");
  		    $new_url = preg_replace('/ /',$sep,$new_url);	
		  	$new_url = $this->_foreign_convert($new_url);
  		    $new_url = preg_replace('/[^a-zA-Z0-9_-]/','',$new_url);	
  		     while(strpos($new_url,$sep.$sep) > -1){
  		    		$new_url = preg_replace('/'.$sep.$sep.'/',$sep,$new_url);	
  		    }


  		    $url_title_len = $this->EE->db->query("SELECT url_title_len FROM exp_title_master WHERE channel_id = ? AND site_id = ?",array($channel_id,$this->site_id))->row('url_title_len');  					
  		    $new_url = substr($new_url,0,$url_title_len);
			  
    		 if(substr($new_url,-1) == $sep){	      		 	 
    			$new_url = substr($new_url, 0,-1);
    		 }  					
    		 
  			//Check to make sure no duplicate url
  			$sql = "SELECT url_title FROM exp_channel_titles WHERE url_title = '$new_url' AND channel_id = '$channel_id' AND entry_id != $entry_id";			
  			$result = $this->EE->db->query($sql);

  			if ($result->num_rows() > 0){

  				$current_url = $result->row('url_title');
  					//Get the Current Increment for this channel
  					$x = $this->EE->db->query("SELECT increment FROM exp_title_master WHERE channel_id = ? AND site_id = ?",array($channel_id,$this->site_id))->row('increment');  					
  					$try_url = $new_url.$x;
  					while($result->num_rows() > 0){//Add 1 and Verify that this new URL won't conflic with any existing entries			
  						$x++;
  						$try_url = $new_url.$x;	
  						$sql = "SELECT url_title FROM exp_channel_titles WHERE url_title = '$try_url'  AND channel_id = '$channel_id' AND entry_id != $entry_id";
  						$result = $this->EE->db->query($sql);
  						$current_url = $result->row('url_title');
  					}
  					$this->EE->db->query("UPDATE exp_title_master SET increment = ? WHERE channel_id = ? AND site_id = ?",array($x,$channel_id,$this->site_id));
  					$data = array(
	  					'entry_id' => $entry_id, 
	  					'increment'=>$x
	  					);
					$this->EE->db->insert('exp_title_master_entries', $data); 
  					
  					$new_url = $try_url;				

  			}

			$title_data['url_title'] = $new_url;

		}

		//Check to make sure that this channel needs to set the Structure URL
		if($this->isStructurePage($channel_id)){		
			$this->updateStructureUrl($entry_id,$title_data['url_title']);			
		}
		
	  	//Make Changes		
		$this->EE->db->where('entry_id', $entry_id);
		$this->EE->db->update('exp_channel_titles', $title_data);

		//return the title data in case anyone else wants to do something with it
		return $title_data;

	}
	

	// --------------------------------------------------------------------


	function isStructurePage($channel_id){		    
    
    $query = $this->EE->db->query('show tables like "exp_structure_channels"');
    if($query->num_rows() > 0){
      
      $query = $this->EE->db->query('select type from `exp_structure_channels` where channel_id = ?',$channel_id);
  
      if($query->num_rows() < 1){
      	return FALSE;
      }
      
      $type = $query->row()->type;
      
      if($type == 'page' OR $type == 'listing' ){

	    $result = $this->EE->db->query('SELECT update_structure FROM exp_title_master WHERE channel_id = ?',$channel_id);

		if($result->row()->update_structure == 1){				
        
        	return TRUE;        
        }
        
      }
      
    }
    
      return FALSE;
  }


  function updateStructureUrl($entry_id,$new_url){


  	//Get Site Pages
  	$query = $this->EE->db->query('select site_pages from `exp_sites` where site_id = ?',$this->site_id);
	$data = $query->row()->site_pages;

  	//Decode Data to be processed
	$decode = base64_decode($data);
	$pages = unserialize($decode);

	//Get the Url We Need
	$url = $pages[$this->site_id]['uris'][$entry_id];

	//Strip out 
	$segments = explode('/',$url);
	$last_segment = end($segments);
	if(empty($last_segment)){//Check if last segment is empty, which means it had trailing slash
		array_pop($segments);
		$add_slash = "/";
	}else{
		$add_slash = "";
	}

	$last = count($segments);
	$segments[$last-1] = $new_url;
	$new_url = implode('/',$segments) . $add_slash;
	// print_r($segments);
	// die();

	//Add the URL back into the Array
	$pages[$this->site_id]['uris'][$entry_id] = $new_url;

	//Encode the Data Back
	$ser = serialize($pages);
	$enc = base64_encode($ser);

	//Update Site Pages
 	$query = $this->EE->db->query('update `exp_sites` set site_pages = ?  where site_id = ?',array($enc,$this->site_id));

  }

	/**
	 * Update Entry - Updates entry with Template
	 *
	 */

	function _update_entry_b4_submit($data,$url_title_tmpl,$channel_id){

	$title_data = array(); 
	
	    
	    //Parse URL, Lowercase, Get Word Seperator, Replaces Spaces,  Strip Non Good Characters
        $new_url = trim(strtolower($this->_early_parse_tmpl($url_title_tmpl,$data)));	

 		if($new_url != ""){
  				$sep = $this->EE->config->config['word_separator'];
  				if($sep == 'underscore'){
  				  $sep = '_';
  				}else{
  				  $sep = '-';
  				}
  		    $new_url = preg_replace('/ /',$sep,$new_url);	
		  	$new_url = $this->_foreign_convert($new_url);
  		    $new_url = preg_replace('/[^a-zA-Z0-9_-]/','',$new_url);	
  		     while(strpos($new_url,$sep.$sep) > -1){
  		    		$new_url = preg_replace('/'.$sep.$sep.'/',$sep,$new_url);	
  		    }
			  
    		 if(substr($new_url,-1) == $sep){	  
    			 $new_url = substr($new_url, 0,1);
    		 }  					
		  		
		}
			
		//return the title data in case anyone else wants to do something with it
		return $new_url;
	
	}

	function _early_parse_tmpl($tmpl,$data){	
		
				$return = $tmpl;		
				$fields = array();
				$channel = $data['channel_id'];
				if(is_array($data['revision_post'])){//Better Workflow
					$post_data = $data['revision_post']; 
				}else{
					$post_data = $data; 
				}
				if($data['entry_id'] == 0){
					$increment = $this->EE->db->query("SELECT increment FROM exp_title_master WHERE channel_id = ?",$channel)->row('increment');
				}else{
					$increment = $this->_get_increment($data['entry_id']);
				}

				//Get the Field Data from the channel
				$sql = "select field_id,field_name,field_type 
				from exp_channel_fields as f
				join exp_channels as c on c.field_group = f.group_id
				where channel_id = $channel";
				$result = $this->EE->db->query($sql);


				if ($result->num_rows() > 0)
				{
				    foreach($result->result_array() as $row)
				    {						
					  $fields[$row['field_id']] = $row;
				    }
				}

				$channel_data = array();


				//Grab Data and Format it for inclusion in TMPL				
        		
				foreach($fields as $id => $field_data){				  
					//Strip Out HTML
					if(array_key_exists('field_id_' . $id ,$post_data) AND is_string( $post_data['field_id_' . $id])){
					    $value = $post_data['field_id_' . $id];
				    }else{
				      $value = '';
				    }
					$channel_data[$field_data['field_name']] = trim(strip_tags($value));
				}

					$channel_data['x'] = $increment;

				//Loop through TMPL to find Limits

				preg_match_all("/{([a-zA-Z0-9]*) (words|chars)=[\"']([0-9]*)[\"']}/", $tmpl, $matches);

		  if(!empty($matches[0])){

			    $limits = array();

			    foreach($matches[0] as $i => $v){
			      //Strip Out the Limit from the Template
			      $field = $matches[1][$i];
			      $tmpl = preg_replace($v,$field,$tmpl);      
			      $limits[$field] = array('type' => $matches[2][$i],'num'=>$matches[3][$i]);                  
			    }


			    foreach($limits as $field => $limit){

			        if($limit['type'] == 'words'){

			          $words = explode(' ',$channel_data[$field]);
			          $words = array_slice($words,0,$limit['num']);
			          $channel_data[$field] = implode(' ',$words);

			        }
			        if($limit['type'] == 'chars'){              
			          $channel_data[$field] = substr($channel_data[$field],0,$limit['num']);

			        }

			    }
		

		 }


				$this->EE->load->library('template', NULL, 'TMPL'); 

				$TMPL = new EE_Template();

				$return = $TMPL->parse_variables_row($tmpl, $channel_data);

				$return = $TMPL->advanced_conditionals($return);

			return $return;


		}

	
	

	function _parse_tmpl($tmpl,$entry_id) {		
		//Stantiate Variables
		$this->EE->load->library('typography');
		$this->EE->load->library('template');
		$this->EE->TMPL = new EE_Template;

		$status = $this->EE->db->select("status")->from('exp_channel_titles')->where('entry_id',$entry_id)->get()->row()->status;
	  	$increment = $this->_get_increment($entry_id);

		//Set Variables For Typography to minimal
		$prefs = array(
				'text_format'   => 'none',
				'html_format'   => 'none',
				'allow_img_url' => 'n',
 				'encode_email' => FALSE
				);
		
		$this->EE->typography->initialize($prefs);

		//Parse Incremental Value
		$row['x'] = $increment;
		$tmpl = $this->EE->TMPL->parse_variables_row($tmpl, $row);	

		//Set Template - Add a Not Status Check to do All Statuses		
		$raw_tmpl = '{exp:channel:entries entry_id="' . $entry_id . '" dynamic="no" status="' . $status . '" show_expired="yes" show_future_entries="yes"}' . $tmpl . '{/exp:channel:entries}';		

		$this->EE->TMPL->parse($raw_tmpl);
		$string = trim(strip_tags($this->EE->TMPL->parse_globals($this->EE->TMPL->final_template)));		
			
		return $string;
	}


	private function utf8_to_unicode_code($utf8_string)
	     {
	         $expanded = iconv("UTF-8", "UCS-4BE", $utf8_string);
	         return unpack("N*", $expanded);
	     }



	private function _foreign_convert($str){
		include(APPPATH.'config/foreign_chars.php');
		$chars = $this->utf8_to_unicode_code($str);
		$string = "";
		
		foreach($chars as $letter){
			if(isset($foreign_characters[$letter])){
				$string .= $foreign_characters[$letter];
			}else{
				$string .= chr($letter);
			}
		}

		return $string;

	}

	function _get_increment($entry_id) {
		//Check if Increment exits
		$result = $this->EE->db->select("increment")->from('exp_title_master_entries')->where('entry_id',$entry_id)->get();
		if($result->num_rows() > 0){
			return $result->row('increment');
		}else{
			$channel_id = $this->EE->db->query("SELECT channel_id FROM exp_channel_titles WHERE entry_id = ?",$entry_id)->row('channel_id');
			$result = $this->EE->db->query("SELECT increment FROM exp_title_master WHERE channel_id = ?",$channel_id);
			$currentIncrement =	$result->row('increment');
			//Increment the Increment since we are adding a new record			
			$data = array(
					'entry_id' => $entry_id, 
					'increment'=>$currentIncrement
					);			
			$this->EE->db->insert('exp_title_master_entries', $data); 
			$nextIncrement = $currentIncrement + 1;
			$data = array('increment' => $nextIncrement);
			$this->EE->db->update('exp_title_master', $data,array('channel_id' => $channel_id)); 

			return $currentIncrement;
		}
		

	}


	
	function _parse_tmpl_old($tmpl,$entry_id){

			$return = $tmpl;		
			$data = array();
			$fields = array();



		//Get all the Meta Data about the field
			$sql = "select * from exp_channel_titles
			join exp_channels on exp_channel_titles.channel_id = exp_channels.channel_id
		join exp_members on exp_members.member_id = exp_channel_titles.author_id
			 where entry_id = '$entry_id'";	
			$result = $this->EE->db->query($sql);

			if ($result->num_rows() > 0)
			{
			    foreach($result->result_array() as $row)
			    {
					$data = $row;
			    }
			}
      
			$channel = $data['channel_id'];

			//Get the Field Data from the channel
			$sql = "select field_id,field_name,field_type 
			from exp_channel_fields as f
			join exp_channels as c on c.field_group = f.group_id
			where channel_id = $channel";
			$result = $this->EE->db->query($sql);
			if ($result->num_rows() > 0)
			{
			    foreach($result->result_array() as $row)
			    {
				  $fields[$row['field_id']] = $row;
			    }
			}


			//Convert fields to Select String
			$field_idstring = "";
			foreach($fields as $id => $field){
				$field_idstring .= ",field_id_$id as `".$field['field_name']."`";
			}
			$field_idstring = substr($field_idstring,1);



			//Grab Data from Field for entry		
			$sql = "select $field_idstring from exp_channel_data where entry_id = '$entry_id'";

			$result = $this->EE->db->query($sql);

			$channel_data = array();
			if ($result->num_rows() > 0)
			{
			    foreach($result->result_array() as $row)
			    {
			 			$channel_data = $row;
			    }
			}

			//Grab Data and Format it for inclusion in TMPL				
			foreach($fields as $id => $field_data){				  
				extract($field_data);
				$value = $this->_parse_field($field_type,$channel_data[$field_name],$field_id,$entry_id);
				//Strip Out HTML
				$data[$field_name] = trim(strip_tags($value));
			}
			
			//Loop through TMPL to find Limits
						
			preg_match_all("/{([a-zA-Z0-9]*) (words|chars)=[\"']([0-9]*)[\"']}/", $tmpl, $matches);

      if(!empty($matches[0])){

        $limits = array();
      
        foreach($matches[0] as $i => $v){
          //Strip Out the Limit from the Template
          $field = $matches[1][$i];
          $tmpl = preg_replace($v,$field,$tmpl);      
          $limits[$field] = array('type' => $matches[2][$i],'num'=>$matches[3][$i]);                  
        }
      
      
        foreach($limits as $field => $limit){

            if($limit['type'] == 'words'){

              $words = explode(' ',$data[$field]);
              $words = array_slice($words,0,$limit['num']);
              $data[$field] = implode(' ',$words);

            }
            if($limit['type'] == 'chars'){              
              $data[$field] = substr($data[$field],0,$limit['num']);

            }
        
        }
      
      }
      
			$this->EE->load->library('template', NULL, 'TMPL'); 

			$TMPL = new EE_Template();

			$return = $TMPL->parse_variables_row($tmpl, $data);

			$return = $TMPL->advanced_conditionals($return);
			
		return $return;

	}
	
	function _parse_field($field,$value,$field_id,$entry_id){		
		switch($field){			
			case 'playa':
				return $this->_get_playa_data($value,$field_id,$entry_id);
			break;			
			case 'rel':
				return $this->_get_rel_data($value);
			break;
			default:
			 return $value;							
		}
		
	}

	function _get_rel_data($value){
		if($value > 0){
			$sql = "select title from exp_channel_titles as t
			join exp_relationships as r on t.entry_id = r.rel_child_id
			where rel_id = '$value' limit 1";
			$result = $this->EE->db->query($sql);
			if ($result->num_rows() > 0)
			{
				$relData = $result->row('title');
				return $relData;
			}
		}
			return '';
	}
	
	function _get_playa_data($value,$field_id,$entry_id){
	  
	  //Handle OLD PLaya Fields
		//Grab First Entry_id from the field
		if(preg_match("/[*[0-9]*]/",$value,$matches) > 0){
			$first = substr($matches[0],1,-1);
			
			//Grab Title from DB
			$sql = "SELECT title from exp_channel_titles where entry_id = '$first'";
			$result = $this->EE->db->query($sql);
			$playaData = $result->row('title');			
			
		}else{
		  
		  $sql = "SELECT title from exp_channel_titles as t
		  join exp_playa_relationships as p on p.child_entry_id = t.entry_id
		  where p.parent_field_id = '$field_id' AND p.parent_entry_id = '$entry_id' AND rel_order = 0";
      $result = $this->EE->db->query($sql);		  					        
		  //Handle New Playa Fields
    if ($result->num_rows() > 0){
		  $playaData = $result->row('title');		
		 }else{//IF still nothing, just return blank
       $playaData = '';
	   }		  			
	}
			
		
		//Return Title
		return $playaData;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get IDs - returns entry ids that need to be update 
	 *	 
	 */
	function _get_ids_from_channel($channel_id){		
		
		//Retrieve Entry Ids that are affected by Change
		$sql = "select entry_id from exp_channel_titles where channel_id = $channel_id";
		$result = $this->EE->db->query($sql);

		$ids = array();
		if ($result->num_rows() > 0)
		{
		    foreach($result->result_array() as $row)
		    {
				$ids[] = $row['entry_id']; 
		    }
		}
		
		return $ids;		
	}
	
	function _update_channel($channel_id,$url = FALSE){
		$ids = $this->_get_ids_from_channel($channel_id);
		//Get Template for Channel
		$tmpls = $this->_get_tmpls_for_channel($channel_id);
	
		//Pass Template to Parser
		foreach($ids as $id){
		  if($url){
			  $this->_update_entry($id,$tmpls['title_tmpl'],$tmpls['url_title_tmpl'],$channel_id);
		  }else{
		    $this->_update_entry($id,$tmpls['title_tmpl'],FALSE,$channel_id);
		  }
		}
	
	}
	
}
/* End of file mod.title_master.php */
/* Location: /system/expressionengine/third_party/title_master/mod.title_master.php */