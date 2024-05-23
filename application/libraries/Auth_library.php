<?php

/**
 *  -----------------------
 *	Superman Authentication library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Auth_library
{

	function __construct()
	{
		$this->CI = &get_instance();
	}

	/**
	 * Checking is Login
	 * @return  Redirect Action to Users Controllers
	 */
	function Is_login()
	{
		if ($this->CI->session->userdata($this->CI->config->item('apps_name')) and ($this->CI->session->userdata($this->CI->config->item('apps_name'))['Is_login'] or $this->CI->session->userdata($this->CI->config->item('apps_name'))['Is_login'] == true)) {
			# redirect to users page
			redirect('Users', 'refresh');
		}
	}

	/**
	 * Checking is not login
	 * @return  Redirect Action to Sign in page Controllers
	 */
	function Is_not_login()
	{
		if (!$this->CI->session->userdata($this->CI->config->item('apps_name')) or $this->CI->session->userdata($this->CI->config->item('apps_name'))['Is_login'] != true) {
			# destroy sessions
			$this->CI->session->sess_destroy();
			# redirect to sign in page
			redirect('Users/Sign_in', 'refresh');
		}
	}

	/**
	 * Checking is superman not login
	 * @return  Redirect Action to Sign in page Controllers
	 */
	function Is_superman_not_login(){
		if (!$this->CI->session->userdata('superman') or $this->CI->session->userdata('superman')['is_superman_alive'] != true) {
			# destroy sessions
			$this->CI->session->sess_destroy();
			# redirect to sign in page
			redirect('Superman/Login', 'refresh');
		}
	}

	/**
	 * Checking is company code exist
	 * @return  Redirect Action to Sign in page Controllers
	 */
	function is_company_code_exist()
	{
		if (!$this->CI->input->get('company_code') or $this->CI->input->get('company_code') != $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_code']) {
			# destroy sessions
			$this->CI->session->sess_destroy();
			# redirect to sign in page
			redirect('Users/Sign_in', 'refresh');
		}
	}
}
