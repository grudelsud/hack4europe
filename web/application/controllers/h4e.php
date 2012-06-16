<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class H4e extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		// set default options & output template
		$this->data['logged_user'] = 1;
		$this->data['logged_admin'] = false;
		$this->data['template'] = 'home';
	}
	
	function index()
	{
		$this->load->view('h4e_template', $this->data);
	}
}

/* end of hack4europe */