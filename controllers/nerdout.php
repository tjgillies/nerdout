<?php
class Nerdout extends Site_Controller
{
    function __construct()
    {
        parent::__construct();       

		$this->load->config('nerdout');
		
		$this->load->model('nerds_model');
		
		$this->data['widgets_content_wide']	= '';
	}
	
	function index()
	{
		$this->render('landing');	
	}

	function view() 
	{		
		// Basic Content Redirect	
		$this->render();
	}
	
	function city()
	{
		$uber_nerds		 	= $this->nerds_model->get_nerds_uber();		
		$uber_nerds_view 	=  '';
		$this_city			= ucwords($this->uri->segment(2));
		
		if ($uber_nerds)
		{
			foreach ($uber_nerds as $nerd)
			{
				$this->data['profile_name']		= $nerd->name;
				$this->data['profile_link']		= base_url().'profile/'.$nerd->username;
				$this->data['profile_avatar']	= $this->social_igniter->profile_image($nerd->user_id, $nerd->image, $nerd->gravatar, 'medium'); 
				$this->data['checkin_count'] 	= $nerd->value;
				$this->data['checkins']			= $this->social_igniter->get_timeline_user_view($nerd->user_id, 'type', 'checkin', 4);

				$uber_nerds_view .= $this->load->view('partials/widget_content_wide_uber', $this->data, true);
			}
		}
		else
		{
			$uber_nerds_view = '<h3>No Uber Nerds in this city</h3>';
		}
			
		$this->data['widgets_content_wide_uber']= $uber_nerds_view;
		$this->data['widgets_content_wide']	   .= $this->load->view('partials/widget_content_wide_all', $this->data, true);
		
		$this->data['this_city']				= $this_city;
		$this->data['site_title']				= 'Nerdouts in '.$this_city;	
			
		$this->render('site_wide');
	}
	
}
