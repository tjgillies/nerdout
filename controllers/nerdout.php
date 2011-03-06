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
		$this->render();	
	}

	function view() 
	{		
		// Basic Content Redirect	
		$this->render();
	}
	
}
