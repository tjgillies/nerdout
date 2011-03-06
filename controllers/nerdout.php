<?php
class Nerdout extends Site_Controller
{
    function __construct()
    {
        parent::__construct();       

		$this->load->config('nerdout');
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
		$this->render('city');
	}
	
}
