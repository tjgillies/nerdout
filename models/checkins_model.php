<?php

class Checkins_model extends CI_Model
{
    
    function __construct()
    {
      parent::__construct();
    }

    function get_checkins($limit=8)
    {
 		  $this->db->select('content.content_id, content.module, content.source, content.user_id, content.content, content.details, content.geo_lat, content.geo_long, content.geo_accuracy, users.user_id, users.username, users.name, users.image, users.gravatar');
 		  $this->db->from('content');
 		  $this->db->join('users', 'users.user_id = content.user_id');
 		  $this->db->order_by('content.created_at', 'desc');
      $this->db->where('content.type', 'checkin');
		  $this->db->limit($limit);
 		  $result = $this->db->get();
 		  return $result->result();     
    }
    
    function get_checkins_nearby($lat, $long, $limit=8)
    {
   		$this->db->select('content.content_id, content.module, content.source, content.user_id, content.content, content.details, content.geo_lat, content.geo_long, content.geo_accuracy, users.user_id, users.username, users.name, users.image, users.gravatar');
   		$this->db->from('content');    
   		$this->db->join('users', 'users.user_id = checkins.user_id');
      $this->db->where('content.type', 'checkin');
  		$this->db->where('content.geo_lat', $lat);
  		$this->db->where('content.geo_long', $long);
   		$this->db->order_by('content.created_at', 'desc'); 
  		$this->db->limit($limit);    
   		$result = $this->db->get();	
   		return $result->result();	      
    }
    
    function add_checkin($user_id, $data)
    {
    	$content_data = array(
    		'site_id'			    => 1,
  			'parent_id'		  	=> 0,
  			'category_id'		  => 0,
  			'module'		    	=> $data->module,
  			'type'			  	  => 'checkin',
  			'source'	    		=> $data->source,
  			'order'			    	=> 0,
    		'user_id'		    	=> $user_id,
  			'title'			    	=> '',
  			'title_url'	  		=> '',
  			'content'		    	=> $data->content,
  			'details'	    		=> $data->content_url,
  			'access'		    	=> '',
  			'comments_allow'	=> 'Y',
  			'geo_lat'		    	=> $data->geo_lat,
  			'geo_long'	  		=> $data->geo_long,
  			'geo_accuracy'		=> $data->geo_accuracy,
  			'viewed'		    	=> 'Y',
  			'approval'	  		=> 'Y',
  			'status'		    	=> 'P',
        'created_at'      => now()
    	);
  		$insert 	= $this->db->insert('content', $content_data);
  		$status_id 	= $this->db->insert_id();
  		return $this->db->get_where('content', array('content_id' => $status_id))->row();	
    }    
    
    function delete_checkin($checkin_id) {
      $this->db->where('type', 'checkin');
      return $this->db->delete('content', array('content_id' => $checkin_id));
    }

    function update_checkin($checkin_id, $data) {
      $content_data = array(
    		'site_id'			    => 1,
  			'parent_id'		  	=> 0,
  			'category_id'		  => 0,
  			'module'		    	=> $data->module,
  			'type'			  	  => 'checkin',
  			'source'	    		=> $data->source,
  			'order'			    	=> 0,
    		'user_id'		    	=> $data->user_id,
  			'title'			    	=> '',
  			'title_url'	  		=> '',
  			'content'		    	=> $data->content,
  			'details'	    		=> $data->content_url,
  			'access'		    	=> '',
  			'comments_allow'	=> 'Y',
  			'geo_lat'		    	=> $data->geo_lat,
  			'geo_long'	  		=> $data->geo_long,
  			'geo_accuracy'		=> $data->geo_accuracy,
  			'viewed'		    	=> 'Y',
  			'approval'	  		=> 'Y',
  			'status'		    	=> 'P',
        'updated_at'      => now()
    	);
      $this->db->where('type', 'checkin');
      $this->db->where('content_id', $checkin_id);
      return $this->db->update('content', $data);
    }
}
