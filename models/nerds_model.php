<?php

class Nerds_model extends CI_Model
{
    
    function __construct()
    {
      parent::__construct();
    }

    function get_nerds_uber($limit=20)
    {
		$this->db->select('users.user_id, users.username, users.name, users.image, users.gravatar, users_meta.meta, users_meta.value');
		$this->db->from('users');
		$this->db->join('users_meta', 'users_meta.user_id = users.user_id');		
		$this->db->where('users_meta.meta', 'checkin_count');
		$this->db->order_by('users_meta.value', 'desc');
		$this->db->limit($limit);
		$result = $this->db->get();
		return $result->result();     
    }
    
}