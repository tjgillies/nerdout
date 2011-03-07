<?php
class Nerdout extends Site_Controller
{
    function __construct()
    {
        parent::__construct();       

		$this->load->config('nerdout');
		
		$this->load->model('nerds_model');
	}
	
	function index()
	{
		$this->data['page_title'] = 'Nerdout';
		$this->render('landing');	
	}

	function view() 
	{		
		// Basic Content Redirect	
		$this->render();
	}
	
	function city()
	{
		$uber_nerds		 = $this->nerds_model->get_nerds_uber();		
		$uber_nerds_view =  '';
		
		foreach ($uber_nerds as $nerd)
		{
			$this->data['name']				= $nerd->username;
			$this->data['profile_link']		= base_url().'nerd/'.$nerd->username;
			$this->data['profile_avatar']	= str_replace('_normal', '', $nerd->image); 
			$this->data['checkin_count'] 	= $nerd->value;
		
			$uber_nerds_view .= $this->load->view('partials/widget_wide_uber_nerds', $this->data, true);		
		}
			
		$this->data['widget_wide_uber_nerds'] = $uber_nerds_view;	
			
		$this->render('city');
	}
	
}
