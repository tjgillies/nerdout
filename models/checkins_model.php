<?php

class Checkins_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    function get_checkins()
    {
 		$this->db->select('checkins.status_id, checkins.user_id');
 		$this->db->from('checkins');    
 		$this->db->join('users', 'users.user_id = checkins.user_id');
 		$this->db->order_by('created_at', 'desc'); 
		$this->db->limit($limit);    
 		$result = $this->db->get();	
 		return $result->result();	      
    }
    
    function get_checkins_nearby($lat, $long)
    {
 		$this->db->select('checkins.status_id, checkins.user_id');
 		$this->db->from('checkins');    
 		$this->db->join('users', 'users.user_id = checkins.user_id');
  		$this->db->where('checkins.lat', $lat);
		$this->db->where('checkins.long', $long);
 		$this->db->order_by('created_at', 'desc'); 
		$this->db->limit($limit);    
 		$result = $this->db->get();	
 		return $result->result();	      
    }
    
    function add_checkin($user_id, $status_data)
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
    
    function delete_checkin($checkin_id) {
      return $this->db->delete('checkins', array('checkin_id' => $checkin_id));
    }

    function update_checkin($checkin_id, $data) {
      $this->db->where('checkin_id', $checkin_id);
      return $this->db->update('checkin', $data);
    }
}
