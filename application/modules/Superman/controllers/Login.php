<?php

/**
 *  -----------------------
 *	Login Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

	private $company_code;
	private $company_id;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_superman', 'model_superman');
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function index(){
		// is login library
		$this->auth_library->Is_login();
		// define property setting
		$this->index_loader->settingProperty(array('title'));
		// get setting data
		$this->index_loader->Setting();
		// add js files
		$this->index_loader->addData(array('js' => 'Superman/superman_sign_in'));
		// get setting values
		$data = $this->index_loader->Response();
		// generate sign in templating
		$this->templating->superman_sign_in_templating($data);
	}

	// superman login process
	function login_process(){
		$return = array();
		$error = 0;
		$error_msg = '';
		// sign_in_process
		$this->form_validation->set_rules('username', '<b>Username<b>',  'trim|required|xss_clean|min_length[1]|max_length[256]');
		$this->form_validation->set_rules('password', '<b>Password<b>',  'trim|required|xss_clean|min_length[1]|max_length[256]|alpha_numeric');
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {
			// username
			$username = $this->input->post('username');
			// password
			$password = $this->input->post('password');
			// check
			$check  = $this->model_superman->check_username_superman( $username );
			// check username
			if( count( $check ) > 0 ) {
				// check password verify
				if ( password_verify( $password . '_' . $this->systems->getSalt(), $check['password'] ) ) {
    				// set userdata              
	                $this->session->set_userdata(array('superman' => array('is_superman_alive' => true,
                  														   'fullname' => $check['fullname'], 
                  														   'username' => $check['username'], 
                  														   'module_access' => $this->model_superman->get_module_access())));
               }else{
                  $error = 1;
                  $error_msg = '1Proses menjadi <b>SUPERMAN</b> gagal dilakukan.';
               }
			}else{
				$error = 1;
				$error_msg = '2Proses menjadi <b>SUPERMAN</b> gagal dilakukan.';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses perubahan berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors('<span class="error">', '</span>'),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}
}