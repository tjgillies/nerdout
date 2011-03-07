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
          /* content.content_id, content.module, content.source, content.user_id, content.content, content.details, content.geo_lat, content.geo_long, content.geo_accuracy, users.user_id, users.username, users.name, users.image, users.gravatar */
            $features = array();
            foreach($checkins as $checkin) {
              $features[] = array(  
                'type' => 'Feature',
                /*'properties' => array(
                  'id' => $checkin->content_id,
                  'user_id' => $checkin->user_id,
                  'content' => $checkin->content,
                  'content_url' => $checkin->details
                ),*/
                'geometry' => array(
                  'type' => 'Point',
                  'coordinates' => array(
                    $checkin->geo_long, $checkin->geo_lat)
                )
              );
            }
            $data = array(
              'type' => 'FeatureCollection',
              'features' => $features
            );
            $message = array('status' => 'success', 'message' => 'Yay we found some checkins', 'checkins' => $data);
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Could not find any checkins');
        }
        
        $this->response(array($message), 200);   
    }

    function create_checkin_authd_post()
    {
/*    	user_id
		name
		username
		image
		url (profile url)
		location (house)
		source: daemon
		module: 4sq/twitter
		content:
		content_url:
		geo_lat
		geo_long
		geo_acc
		name address city state neighborhood
		timestamp
*/
		$daemon = file_get_contents('php://input');		
		$data	= json_decode($daemon);
		$email	= $data->username.'@'.$data->module.'.com';
		
		// Site
		if ($data->module == 'twitter') $site_id = 2;
		else $site_id = 3;
		
		$user_check			= $this->social_auth->get_user('email', $email);
		$connection_check	= $this->social_auth->check_connection_user_id($data->remote_user_id, $data->module);

		if ($user_check == true) && ($connection_check == true))
		{
			$user_id = $user_check->user_id;
		}
		else
		{
			// Add User
			$additional_data = array(
				'name'	=> $data->name,
				'image'	=> $data->image
			);
		
			$user_id = $this->social_auth->social_register($data->username, $email, $additional_data);
			
			// Add Meta
			$this->social_auth->update_user_meta($site_id, $user_id, 'users', array('url' => $data->url, 'location' => $location)));

			// Add Connection
       		$connection_data = array(
       			'site_id'				=> site_id,
       			'user_id'				=> $user_id,
       			'module'				=> $data->module,
       			'type'					=> 'scrapped',
       			'connection_user_id'	=> $data->user_id,
       			'connection_username'	=> $data->username,
       			'auth_one'				=> '',
       			'auth_two'				=> ''
       		);
 
			$this->social_auth->add_connection($connection_data);			
		}
		    									
		// Insert
		$result = $this->checkins_model->add_checkin($user_id, $data);

	  	if ($result)
	    {			
			$checkin_count	= $this->social_auth->get_user_meta_meta($user_id, 'checkin_count');
			$checkin_new 	= 1 + $checkin_count;
			
			$this->social_auth->update_user_meta(1, $user_id, 'users', array('checkin_count' => $checkin_new));  
	   
			// API Response
        	$message = array('status' => 'success', 'message' => 'Awesome we added your checkin', 'data' => $result['content']);
        }
        else
        {
	        $message = array('status' => 'error', 'message' => 'Oops we were unable to add your checkin');
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
