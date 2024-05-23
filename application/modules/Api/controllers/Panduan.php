<?php

/**
 *  -----------------------
 *	Slider Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Panduan extends CI_Controller
{

	private $company_code;
	private $company_id;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_api', 'model_api');
		# checking is not Login
		// $this->auth_library->Is_not_login();
		# get company id
		// $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		// $this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

   function _ck_path_existen($part){
		// echo $part;
      if( ! $this->model_api->check_path_panduan($part) ){
         $this->form_validation->set_message('_ck_path_existen', 'Path panduan tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

	// code perusahaan
	function _ck_code_perusahaan_exist($codeCompany){
		if( !$this->model_api->check_company_code($codeCompany) ){
			$this->form_validation->set_message('_ck_code_perusahaan_exist', 'Kode perusahaan tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

   # view panduan
   function view_panduan(){
      $return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code',	'<b>Kode Perusahaan<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('part',	'<b>Part<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_path_existen');
		/*
        Validation process
     */
		if ($this->form_validation->run()) {
         # feedBack
         $feedBack = $this->model_api->get_panduan( $this->input->post('part') );
         # total
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
               'data' => $feedBack,
					// $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Failed',
					// $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					// $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   }

}
