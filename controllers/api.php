<?php defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Messages API : Module : Social-Igniter
 *
 */
class Api extends Oauth_Controller
{
    function __construct()
    {
        parent::__construct();  
        
		$this->load->model('checkins_model');            
		$this->load->model('locations_model');
	}
    
    function checkins_get()
    {
    	$checkins = $this->checkins_model->get_checkins();
        
        if($checkins)
        {
            $features = array();
            
            foreach($checkins as $checkin)
            {
				$features[] = array(  
					'type' 			=> 'Feature',
					'geometry'		=> array(
					    'type' 			=> 'Point',
					    'coordinates'	=> array($checkin->geo_long, $checkin->geo_lat)
					)
				);
            }
            
            $data = array(
            	'type'		=> 'FeatureCollection',
            	'features'	=> $features
            );
            
            $message = array('status' => 'success', 'message' => 'Yay we found some checkins', 'checkins' => $data);
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Could not find any checkins');
        }
        
        $this->response(array($message), 200);   
    }

    function create_checkin_post()
    {
		$daemon = file_get_contents('php://input');
		$data	= json_decode($daemon);
		
		// Email
		$email	= $data->username.'@'.$data->module.'.com';

		// Site
		if ($data->module == 'twitter') $site_id = 2;
		else $site_id = 3;
		
		if ($connection_check = $this->social_auth->check_connection_user_id($data->remote_user_id, $data->module))
		{
			$user_check		= $this->social_auth->get_user('email', $email);		
			$user_id		= $user_check->user_id;
			$user_exists 	= TRUE;
			$user_message	= 'User Exists. ';
		}
		else
		{
			// Add User
			$additional_data = array(
				'name'	=> $data->name,
				'image'	=> $data->image
			);
		
			if ($user_id = $this->social_auth->social_register($data->username, $email, $additional_data))
			{			
				// Add Meta
				$user_meta_data = array(
					'url' 			=> $data->url, 
					'location' 		=> $data->user_location,
					'checkin_count' => 0
				);
				
				$this->social_auth->update_user_meta(config_item('site_id'), $user_id, 'users', $user_meta_data);
	
				// Add Connection
	       		$connection_data = array(
	       			'site_id'				=> $site_id,
	       			'user_id'				=> $user_id,
	       			'module'				=> $data->module,
	       			'type'					=> 'primary',
	       			'connection_user_id'	=> $data->remote_user_id,
	       			'connection_username'	=> $data->username,
	       			'auth_one'				=> '',
	       			'auth_two'				=> ''
	       		);
	 
				$this->social_auth->add_connection($connection_data);
				
				$user_exists 	= TRUE;
				$user_message	= 'User Created. ';
			}
			else
			{
				$user_exists 	= FALSE;
				$user_message	= 'User Not Created. ';
			}
		}
		    									
		// Insert
		$check_checkin = $this->social_igniter->get_content_title_url($data->type, $data->content_id);
		
		if ((!$check_checkin) && ($user_exists))
		{	
		  	if ($result = $this->checkins_model->add_checkin($user_id, $data))
		    {			
				$checkin_count	= $this->social_auth->get_user_meta_row($user_id, 'checkin_count');
				$checkin_new 	= 1 + $checkin_count->value;
				
				// Update Checkin Count
				$this->social_auth->update_user_meta(1, $user_id, 'users', array('checkin_count' => $checkin_new));  
		   
				// API Response
	        	$message = array('status' => 'success', 'message' => $user_message.'Awesome we added your checkin', 'data' => $result);
	        }
	        else
	        {
		        $message = array('status' => 'error', 'message' => $user_message.'Oops we were unable to add your checkin');
	        }
		}
		else
		{
			$message = array('status' => 'error', 'message' => $user_message.'That checkin already exists');		
		}

        $this->response($message, 200);
    }
      
    function destroy_delete()
    {		
    	if ( $this->social_tools->has_access_to_modify('message', $this->get('id')))
        {   
        	$this->social_tools->delete_message($this->get('id'));
        	        
        	$message = array('status' => 'success', 'message' => 'Message deleted');
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Could not delete that message');
        }

        $this->response($message, 200);        
    }
}
