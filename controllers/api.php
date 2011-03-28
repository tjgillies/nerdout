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
			$username		= $user_check->username;
			$user_exists 	= TRUE;
			$user_message	= 'User Exists. ';
		}
		else
		{
			$process_image = FALSE;
		
			// If Twitter & has image
			if (($data->image) && ($data->module == 'twitter'))
			{
				// If non default image
				if (($data->image) && (!preg_match('/default_profile_/', $data->image))) $process_image = TRUE;				
				if ($process_image)
				{
			   		$image_full	= str_replace('_normal', '', $data->image); 
					$image_name	= $data->username.'.'.pathinfo($image_full, PATHINFO_EXTENSION);
			    }
			    else
			    {
			    	$image_name	= "";
			    }
			}
		
			// Add User
			$additional_data = array(
				'name'	=> $data->name,
				'image'	=> $image_name
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

				// User Data
				$username		= $data->username;
				$user_exists 	= TRUE;
				$user_message	= 'User Created. ';
				
	    		// Process Image	        	
				if ($process_image)
	    		{
	        		$this->load->model('image_model');
	
	        		// Snatch Twitter Image
	        		$image_save	= $image_name;
					$this->image_model->get_external_image($image_full, config_item('uploads_folder').$image_save);
	
					// Process New Images
					$image_size 	= getimagesize(config_item('uploads_folder').$image_save);
					$file_data		= array('file_name'	=> $image_save, 'image_width' => $image_size[0], 'image_height' => $image_size[1]);
					$image_sizes	= array('full', 'large', 'medium', 'small');
					$create_path	= config_item('users_images_folder').$user_id.'/';
	
					$this->image_model->make_images($file_data, 'users', $image_sizes, $create_path, TRUE);
	
					unlink(config_item('uploads_folder').$image_save);
				}				
			}
			else
			{
				$user_exists 	= FALSE;
				$user_message	= 'User Not Created. ';
			}
		}
		
		// If, Check, Add Place
		if ($data->location)
		{
			$title_url		= form_title_url($data->location->name, '');
			$check_place	= $this->social_igniter->get_content_title_url('place', $title_url);

			if (!$check_place)
			{
		    	$place_data = array(
		    		'site_id'			=> config_item('site_id'),
					'parent_id'			=> 0,
					'category_id'		=> 0,
					'module'			=> 'places',
					'type'				=> 'place',
					'source'			=> $data->source,
					'order'				=> 0,
		    		'user_id'			=> $user_id,
					'title'				=> $data->location->name,
					'title_url'			=> $title_url,
					'content'			=> '',
					'details'			=> '',
					'access'			=> 'E',
					'comments_allow'	=> 'Y',
					'geo_lat'			=> $data->geo_lat,
					'geo_long'			=> $data->geo_long,
					'viewed'			=> 'Y',
					'approval'			=> 'Y',
					'status'			=> 'P'  			
		    	);
	
				$add_place	= $this->social_igniter->add_content($place_data);
				$place_id	= $add_place['content']->content_id;
	
				// Add Place	
		    	if ($add_place)
			    {			
					$place_address_data = array(
						'content_id'	=> $place_id,
						'address'		=> $data->location->address,
						'district'		=> $data->location->district,
						'locality'		=> $data->location->locality,
						'region'		=> $data->location->region,
						'country'		=> $data->location->country,
						'postal'		=> $data->location->postal
					);
					
					$place_address = $this->social_tools->add_place($place_address_data);			
		        }
			}
			else
			{
				$place_id = $check_place->content_id;
			}
		}
		    									
		// Check Checkin / Insert
		$check_checkin = $this->social_igniter->get_content_title_url($data->type, $data->content_id);
		
		if ((!$check_checkin) && ($user_exists))
		{
		  	if ($result = $this->checkins_model->add_content_checkin($user_id, $data))
		    {
				// Increment Checkin Count
				$checkin_count	= $this->social_auth->get_user_meta_row($user_id, 'checkin_count');
				$checkin_new 	= 1 + $checkin_count->value;

				$this->social_auth->update_user_meta(1, $user_id, 'users', array('checkin_count' => $checkin_new));  
				
				
				// Checkin Activity
				$activity_info = array(
					'site_id'		=> config_item('site_id'),
					'user_id'		=> $user_id,
					'verb'			=> 'checkin',
					'module'		=> 'nerdout',
					'type'			=> 'checkin',
					'content_id'	=> 0 // To be 'event_id' once that feature is written
				);

				$activity_data = array(
					'title'		=> $data->location->name,
					'content'	=> '',
					'url'		=> base_url().'places/view/'.$place_id,
					'place_id'	=> $place_id,
					'geo_lat' 	=> $data->geo_lat,
					'geo_long'	=> $data->geo_long
				);
				
				// Add Activity
				$this->social_igniter->add_activity($activity_info, $activity_data);


		   
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
