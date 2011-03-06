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
		type:
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
		
		//$user = $this->social_igniter->get_user('email', $user_email);
	
    	$content_data = array(
    		'site_id'			=> 1,
			'parent_id'			=> 0,
			'category_id'		=> 0,
			'module'			=> $data->module,
			'type'				=> 'checkin',
			'source'			=> $data->souce,
			'order'				=> 0,
    		'user_id'			=> $this->oauth_user_id,
			'title'				=> '',
			'title_url'			=> '',
			'content'			=> $data->content,
			'details'			=> $data->content_url,
			'access'			=> '',
			'comments_allow'	=> 'Y',
			'geo_lat'			=> $data->geo_lat,
			'geo_long'			=> $data->geo_long,
			'geo_accuracy'		=> $data->geo_accuracy,
			'viewed'			=> 'Y',
			'approval'			=> 'Y',
			'status'			=> 'P'  			
    	);
    									
		// Insert
		$result = $this->social_igniter->add_content($content_data);
		     		
	    if ($result)
	    {			
			// Process Content Meta
			//$meta_data = array('excerpt' => $this->input->post('excerpt'));
			//$content_meta = $this->social_igniter->add_meta(config_item('site_id'), $result['content']->content_id, $meta_data);
	   
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