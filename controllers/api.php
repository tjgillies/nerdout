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
          /* content.content_id, content.module, content.source, content.user_id, content.content, content.content_url, content.geo_lat, content.geo_long, content.geo_accuracy, users.user_id, users.username, users.name, users.image, users.gravatar */
            $message = array('status' => 'success', 'message' => 'Yay we found some checkins', 'data' => $checkins);
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Could not find any checkins');
        }
        
        $this->response($message, 200);   
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
		
		if ($user = $this->social_auth->get_user('email', $email))
		{
			$user_id = $user->user_id;
		}
		else
		{
			$additional_data = array(
				'name'	=> $data->name,
				'image'	=> $data->image
			);
		
			$user_id = $this->social_auth->social_register($data->username, $email, $additional_data);
		
			$this->social_auth->add_user_meta(array('url' => $data->url, 'location' => $location));
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
