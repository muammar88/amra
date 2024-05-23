<?php

/**
 *  -----------------------
 *	Renew_subscribtion Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Renew_subscribtion extends CI_Controller
{
	private $company_code;
	// private $company_id;
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_users', 'model_users');
		$this->load->model('Model_users_cud', 'model_users_cud');
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		// $this->company_id =
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

   function index(){
		if( $this->input->get('code') ) {
		  	# get info
		  	$data = $this->model_users->check_renew_subscribtion( $this->input->get('code') );
		  # filter
		  if( count( $data ) > 0 ) {
				$data['title'] = 'AMRA :: Aplikasi Manajemen Travel Haji dan Umrah';
				$data['code'] = $this->input->get('code');
			  	$this->templating->renew_templating($data);
		  }else{
			  redirect('Users/Sign_in', 'refresh');
		  }
		}else{
			redirect('Users/Sign_in', 'refresh');
		}
   }

	function _ck_code_renew_exist( $code ){
		if( ! $this->model_users->check_company_code_renew_exist( $code ) ) {
			$this->form_validation->set_message('_ck_code_renew_exist', 'Code perusahaan tidak ditemukan dipangkalan data.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	# renew
	function renew(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('code',	'<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_renew_exist');
		$this->form_validation->set_rules('duration', '<b>Durasi<b>', 'trim|required|xss_clean|min_length[1]|in_list[1,3,6,12]');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			# code
			$code = $this->input->post('code');
			$duration = $this->input->post('duration');
			# get info
			$info = $this->model_users->get_all_info_company( $code );
			if( $info['end_date_subscribtion'] >= date('Y-m-d')){
				$date = strtotime($info['end_date_subscribtion']);
				$date = strtotime("+1 day", $date);
				$start_date = date('Y-m-d', $date);
				$date_2 = strtotime($start_date);
				$date_2 = strtotime("+".$duration." month", $date_2);
				$end_date = date('Y-m-d', $date_2);
			}else{
				$start_date = date('Y-m-d');
				$date_2 = strtotime($start_date);
				$date_2 = strtotime("+".$duration." month", $date_2);
				$end_date = date('Y-m-d', $date_2);
			}

			$data = array();
			$data['company_id'] = $info['id'];
			$data['payment_status'] = 'process';
			$data['duration'] = $duration;
			$data['pay_per_month'] = 200000;
			$data['total'] = 200000 * $duration;
			$data['start_date_subscribtion'] = $start_date;
			$data['end_date_subscribtion'] = $end_date;
			$data['transaction_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			# filter insert to database
			if( ! $this->model_users_cud->insert_renew_subscribtion( $data ) ) {
				$error = 1;
				$error_msg = 'Proses renew gagal dilakukan.';
			}
			# filter error
			if ( $error == 1 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Token berhasil digenerated.',
					'data' => $code,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

}
