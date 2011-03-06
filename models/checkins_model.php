<?php

class Checkins_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_checkins($limit=8)
    {
 		$this->db->select('checkins.status_id, checkins.user_id');
 		$this->db->from('checkins');    
 		$this->db->join('users', 'users.user_id = checkins.user_id'); 				
 		$this->db->order_by('created_at', 'desc'); 
		$this->db->limit($limit);    
 		$result = $this->db->get();	
 		return $result->result();	      
    }
    
    function add_content($user_id, $status_data)
    {
 		$data = array(
			'user_id' 	 			=> $user_id,
			'source'				=> $status_data['source'],
			'text'  	 			=> $status_data['text'],
			'lat'		 			=> $status_data['lat'],
			'long'					=> $status_data['long'],
			'created_at' 			=> unix_to_mysql(now())
		);	
		$insert 	= $this->db->insert('checkins', $data);
		$status_id 	= $this->db->insert_id();
		return $this->db->get_where('checkins', array('item_id' => $status_id))->row();	
    }    
    
}