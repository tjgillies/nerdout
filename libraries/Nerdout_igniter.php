<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Nerdout Igniter Library
*
* @package		Nerdout Igniter
* @link			http://nerdout.me
*
*/
class Nerdout_igniter
{

	function __construct()
	{
		$this->ci =& get_instance();

		// Load Configs
		$this->ci->load->config('nerdout');
		$this->ci->load->model('checkins_model');
		$this->ci->load->model('locations_model');
	}
	
	function add_checkin($source, $data)
	{		
		if ($source == 'twitter')
		{
		
		}
		elseif ($source == 'foursquare')
		{
			
		}
	
	}
	
}