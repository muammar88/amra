<?php

/**
 *  -----------------------
 *	Sign In Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Sign_in extends CI_Controller
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_sign_in', 'model_sign_in');
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	/**
	 * Index Sign In Controllers
	 */
	public function index()
	{
		// is login library
		$this->auth_library->Is_login();
		// define property setting
		$this->index_loader->settingProperty(array('title'));
		// get setting data
		$this->index_loader->Setting();
		// get logo data
		if ($this->input->get('code')) {
			$code = htmlspecialchars($this->input->get('code'), ENT_QUOTES, 'UTF-8');
			// echo $code;
			$this->index_loader->addData(array('company_logo' => $this->index_loader->CompanyData($code), 'code' => $code));
		}
		// add js files
		$this->index_loader->addData(array('js' => 'Public/sign_in'));
		// get setting values
		$data = $this->index_loader->Response();
		// generate sign in templating
		$this->templating->sign_in_templating($data);
	}

	// kode perusahaan
	function _ckKodePerusahaan()
	{
		$company_code = $this->input->post('kode_perusahaan');
		$feedBack = $this->model_sign_in->checkExistCompanyCode($company_code);
		if ($feedBack) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckKodePerusahaan', 'Kode perusahaan tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_token_is_valid($token)
	{
		if ($token == '123456') {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_token_is_valid', 'Token tidak ditemukan.');
			return FALSE;

			// $email = $this->input->post('email');
			// $feedBack = $this->model_sign_in->check_token_is_valid($token, $email);
			// if( $feedBack['error'] == false ){
			// 	return TRUE;
			// }else{
			// 	$this->form_validation->set_message('_ck_token_is_valid', $feedBack['error_msg']);
			// 	return FALSE;
			// }
		}
	}

	function _ck_token_staff($token)
	{
		$kode_perusahaan = $this->input->post('kode_perusahaan');
		$nomor_whatsapp = $this->input->post('nomor_whatsapp');
		$check  = $this->model_sign_in->check_token_is_real($token, $kode_perusahaan, $nomor_whatsapp);
		if ($check['error'] == true) {
			$this->form_validation->set_message('_ck_token_staff', $check['error_msg']);
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ck_company_code_to($code)
	{
		if ($code != '') {
			if (!$this->model_sign_in->check_company_code_exist($code)) {
				$this->form_validation->set_message('_ck_company_code_to', 'Code Perusahaan Tidak ditemukan.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	/**
	 * Sign In Process Controllers
	 */
	public function sign_in_process()
	{
		# disable csrf protection
		$this->config->set_item('csrf_protection', FALSE);
		$return = array();
		$error = 0;
		$error_msg = '';
		// sign_in_process
		$this->form_validation->set_rules('code',	'<b>Company Code<b>',  'trim|xss_clean|min_length[1]|max_length[256]|callback__ck_company_code_to');
		$this->form_validation->set_rules('level_akun',	'<b>Level Akun<b>',  'trim|required|xss_clean|required|min_length[1]|max_length[256]|in_list[administrator,staff]');
		if ($this->input->post('level_akun') == 'administrator') {
			$this->form_validation->set_rules('email', '<b>Email<b>', 'trim|required|xss_clean|min_length[1]|valid_email|max_length[256]');
			if (!$this->model_sign_in->check_verification($this->input->post('email'))) {
				// $this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|max_length[6]|callback__ck_token_is_valid');
			}
		} elseif ($this->input->post('level_akun') == 'staff') {
			if (!$this->input->post('code')) {
				$this->form_validation->set_rules('kode_perusahaan', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|max_length[256]|callback__ckKodePerusahaan');
			}
			// $this->form_validation->set_rules('token', '<b>Token OTP<b>', 'trim|required|xss_clean|min_length[1]|max_length[256]|numeric|callback__ck_token_staff');
			$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|max_length[256]|numeric');
		}
		$this->form_validation->set_rules('password', '<b>Password<b>',  'trim|required|xss_clean|min_length[1]|max_length[256]|alpha_numeric');
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {
			# receive post
			$code = '';
			if ($this->input->post('code')) {
				$code = $this->input->post('code');
			}
			$level_akun = $this->input->post('level_akun'); 
			$userNameArray = array();

			
			if ($level_akun == 'administrator') {
				$email = $this->input->post('email');
				$userNameArray['email'] = $this->input->post('email');

				if ( ! $this->model_sign_in->check_verification($this->input->post('email'))) {
					// echo "ya";
					
					$info_subscribtion_duration = $this->model_sign_in->get_info_subscribtion_duration($this->input->post('email'));

					// print_r( $info_subscribtion_duration );

					if( count ( $info_subscribtion_duration ) > 0 ){
						echo "1";
						$data_company = array();
						$data_company['start_date_subscribtion'] = $info_subscribtion_duration['start_date_subscribtion'];
						$data_company['end_date_subscribtion'] = $info_subscribtion_duration['end_date_subscribtion'];
						$data_company['verified'] = 'verified';
						$data_company['verified_time'] = date('Y-m-d H:i:s');
						# verifikasi akun
						$this->model_sign_in_cud->update_verified($data_company, $this->input->post('email'));
					}else{
						// echo "2";
						$error = 1;
						$error_msg = 'Silahkan melakukan proses pembayaran terlebih dahulu sebelum melakukan verifikasi.';
					}
				}
				// else{
				// 	echo "gak";
				// }

				// die();
			} elseif ($level_akun == 'staff') {
				if ( $this->input->post('code') ) {
					$personal_id = $this->model_sign_in->get_personal_id($this->input->post('nomor_whatsapp'), $this->input->post('code'));
				} else {
					$personal_id = $this->model_sign_in->get_personal_id($this->input->post('nomor_whatsapp'), $this->input->post('kode_perusahaan'));
				}
				$userNameArray['personal_id'] = $personal_id;
				# remove otp
				$this->model_sign_in_cud->remove_otp($personal_id);
			}
			# error filterisasi
			if( $error == 0 ) {
				$password = $this->input->post('password');
				# aunthentication
				$feedBack = $this->model_sign_in->username_password_authentication($level_akun, $userNameArray, $password, $code);

				$return_data = array();
				# filter
				if ($feedBack['error'] == 1) {
					if ($feedBack['active'] == false) {
						$return_data = array('code' => $feedBack['company_code'], 'subscribtion' => false);
						$error = 0;
					} else {
						$error = 1;
						$error_msg .= $feedBack['error_msg'];
					}
				} else {
					if ($feedBack['feedBack']['Is_login'] == false) {
						$error = 1;
						$error_msg .= $feedBack['error_msg'];
					} else {
						//print_r($userNameArray);
						//print_r($userNameArray);
						# aunthentication
						

						$this->session->set_userdata(array($this->config->item('apps_name') => $feedBack['feedBack']));
						$feedBack = $this->model_sign_in_cud->insert_log_login($level_akun, $userNameArray, $code);	
					}
				}
			}
			# filter feedBack
			if ($error == 0) {
				
				# return
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses login berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
				if (count($return_data) > 0) {
					$return['return_data'] = $return_data;
				} else {
					$return['company_code']  = $this->session->userdata($this->config->item('apps_name'))['company_code'];
				}
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

	function _ck_email_company($email)
	{
		if (!$this->model_sign_in->check_email_exist($email)) {
			$this->form_validation->set_message('_ck_email_company', 'Email tidak terdaftar dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function check_verification()
	{
		# disable csrf protection
		$this->config->set_item('csrf_protection', FALSE);
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('email', '<b>Email<b>', 'trim|required|xss_clean|required|min_length[1]|valid_email|max_length[256]|callback__ck_email_company');
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_sign_in->check_verification($this->input->post('email'))) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Akun sudah terverifikasi.',
					'verifikasi' => true,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Akun belum terverifikasi',
					'verifikasi' => false,
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

	function _ck_company_code($code)
	{
		if ($this->model_sign_in->check_kode_perusahaan_exist($code)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_company_code', 'Kode perusahaan tidak terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	function prepare_renew()
	{
		$this->config->set_item('csrf_protection', FALSE);
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|max_length[256]|callback__ck_company_code');
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_sign_in_cud->update_payment_process($this->input->post('code'))) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Failed',
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

	// get otp
	function get_otp()
	{
		$this->load->library('smsgateway');
		# disable csrf protection
		$this->config->set_item('csrf_protection', FALSE);
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('email', '<b>Email<b>', 'trim|required|xss_clean|min_length[1]|valid_email|max_length[256]|callback__ck_email_company');
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {
			# api key 
         	$this->whatsapp_ops->define_api();
         	# get info device 
         	$this->whatsapp_ops->define_system_device_key();
			# get whatsapp number
         	$this->whatsapp_ops->define_system_whatsapp_number();
         	# define whatsapp message
            $this->whatsapp_ops->message = "Nomor OTP anda adalah : ". $this->model_sign_in->gen_otp($this->input->post('email'));
            # get destination number
            $this->whatsapp_ops->destination_number = $this->model_sign_in->get_wa($this->input->post('email'));
            # sending proses
            $this->whatsapp_ops->send_message();
            # check response status
            if( $this->whatsapp_ops->status_response() == 'ok' ) {
            	$return = array(
					'error'		=> false,
					'error_msg' => 'OTP Berhasil dikirim.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
            }else{
            	$return = array(
					'error'	=> true,
					'error_msg' => $this->whatsapp_ops->status_response() ,
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

	# kode perusahaan
	function _ck_kode_perusahaan($kode_perusahaan)
	{
		if ($this->model_sign_in->check_kode_perusahaan($kode_perusahaan)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_kode_perusahaan', 'Kode perusahaan tidak terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	function _ck_nomor_whatsapp($nomor_whatsapp)
	{
		$kode_perusahaan = $this->input->post('kode_perusahaan');
		if ($this->model_sign_in->check_nomor_whatsapp_by_code($nomor_whatsapp, $kode_perusahaan)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_nomor_whatsapp', 'Nomor whatsapp tidak terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	# 
	function get_otp_staff()
	{
		# disable csrf protection
		$this->config->set_item('csrf_protection', FALSE);
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('kode_perusahaan', '<b>Kode Perusahaan<b>', 'trim|xss_clean|required|min_length[1]|max_length[256]|callback__ck_kode_perusahaan');
		$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|max_length[256]|callback__ck_nomor_whatsapp');
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {

			$personal_id = $this->model_sign_in->get_personal_id($this->input->post('nomor_whatsapp'), $this->input->post('kode_perusahaan'));
			# api key 
         	$this->whatsapp_ops->define_api();
         	# get info device 
         	$this->whatsapp_ops->define_system_device_key();
			# get whatsapp number
         	$this->whatsapp_ops->define_system_whatsapp_number();
         	# define whatsapp message
            $this->whatsapp_ops->message = "Nomor OTP anda adalah : ". $this->model_sign_in->gen_otp_staff( $personal_id );
            # get destination number
            $this->whatsapp_ops->destination_number = $this->input->post('nomor_whatsapp');
            # sending proses
            $this->whatsapp_ops->send_message();
            # check response status
            if( $this->whatsapp_ops->status_response() == 'ok' ) {
            	$return = array(
					'error'		=> false,
					'error_msg' => 'OTP Berhasil dikirim.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
            }else{
            	$return = array(
					'error'	=> true,
					'error_msg' => $this->whatsapp_ops->status_response() ,
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

	/**
	 * Logout Login Session
	 */
	function logout()
	{
		$this->session->sess_destroy();
		echo json_encode(array('error' => False));
	}
}
