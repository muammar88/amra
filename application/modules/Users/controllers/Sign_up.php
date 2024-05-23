<?php

/**
 *  -----------------------
 *	Sign Up Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Sign_up extends CI_Controller
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_sign_up', 'model_sign_up');
		$this->load->model('Model_sign_up_cud', 'model_sign_up_cud');

		ini_set('date.timezone', 'Asia/Jakarta');
	}

	/**
	 * Index Sign Up Controllers
	 */
	public function index()
	{
		// define property setting
		$this->index_loader->settingProperty(array('title'));
		// get setting data
		$this->index_loader->Setting();
		// add js files
		$this->index_loader->addData(array('js' => 'Public/sign_up'));
		// get setting values
		$data = $this->index_loader->Response();
		// sign up templating
		$this->templating->sign_up_templating($data);
	}

	function _ck_verfied_code_exist($verified_code)
	{
		if ($verified_code != '') {
			if ($this->model_sign_up->verified_code_exist($verified_code)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_verfied_code_exist', 'Kode verifikasi salah.');
				return FALSE;
			}
		} else {
			$this->form_validation->set_message('_ck_verfied_code_exist', 'Kode verifikasi tidak ditemukan.');
			return FALSE;
		}
	}
	// 6Le9JbwUAAAAAH98M0oGrXamN3LRpHVz1haRvYAr
	function verified()
	{
		// disable csrf for temporary
		$this->config->set_item('csrf_protection', FALSE);
		if ($this->input->get('verified_code') and $this->input->get('verified_code') != '') {
			$return = array();
			$this->form_validation->set_data($_GET);
			$this->form_validation->set_rules('verified_code',	'<b>Code Verifikasi<b>',  'trim|required|xss_clean|min_length[1]|callback__ck_verfied_code_exist');
			/*
				Validation process
			*/
			if ($this->form_validation->run()) {
				# get company id
				$GetData = $this->model_sign_up->get_company_id_by_verified_code($this->input->get('verified_code'));
				if ($GetData['verified'] != 'verified') {
					# define data
					$data = array();
					$data['verified'] = 'verified';
					$data['verified_time'] = date('Y-m-d H:i:s');
					# insert new company
					$feedBack = $this->model_sign_up_cud->update_verified($GetData['company_id'], $data);
					# filter feedBack
					if ($feedBack) {
						redirect('Users/Sign_in', 'refresh');
					} else {
						show_error('Proses update status verifikasi gagal dilakukan.', '404', $heading = 'Error');
					}
				} else {
					redirect('Users/Sign_in', 'refresh');
				}
			} else {
				if (validation_errors()) {
					show_error(validation_errors('<span class="error">', '</span>'), '404', $heading = 'Error');
				}
			}
		} else {
			show_error('Kode verifikasi tidak ditemukan', '404', $heading = 'Error');
		}
	}

	function _ckEmailSignUpExist($email)
	{
		$feedBack = $this->model_sign_up->email_checking($email);
		if ($feedBack) {
			$this->form_validation->set_message('_ckEmailSignUpExist', 'Email tidak tersedia, silahkan pilih email yang lain.');
			return  FALSE;
		} else {
			return TRUE;
		}
	}

	# check nomor whatsapp
	public function _ckNomorWhatsapp($nomor_whatsapp)
	{
		if ( $this->model_sign_up->check_nomor_whatsapp( $nomor_whatsapp ) ) {
			$this->form_validation->set_message('_ckNomorWhatsapp', 'Nomor whatsapp sudah terdaftar.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ck_validation_recaptcha($response)
	{
		$verify = json_decode($this->curl_get_file_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $this->config->item('google_recaptcha_secret_key') . '&response=' . $response));
		if ($verify->success == true) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_validation_recaptcha', 'Anda tidak terverifikasi.');
			return FALSE;
		}
	}

	function curl_get_file_contents($URL)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);

		if ($contents) return $contents;
		else return FALSE;
	}

	/**
	 * Index Sign Up Process Controllers
	 */
	public function sign_up_process()
	{
		# define csrf to false
		$this->config->set_item('csrf_protection', FALSE);
		$return = array();
		$error = 0;
		$error_mess = '';
		$this->form_validation->set_rules('company_name', '<b>Nama Perusahaan<b>', 'trim|required|xss_clean|required|min_length[1]');
		$this->form_validation->set_rules('company_phone',	'<b>Telpon Perusahaan<b>',	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('company_whatsapp',	'<b>Nomor Whatsapp<b>',	'trim|required|xss_clean|min_length[1]|callback__ckNomorWhatsapp');
		$this->form_validation->set_rules('email', '<b>Email<b>', 'trim|required|xss_clean|min_length[1]|valid_email|callback__ckEmailSignUpExist');
		$this->form_validation->set_rules('duration', '<b>Duration<b>', 'trim|required|xss_clean|min_length[1]|numeric|in_list[1,3,6,12]');
		$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|required|xss_clean|min_length[1]|max_length[256]|alpha_numeric');
		$this->form_validation->set_rules('password_conf',	'<b>Konfirmasi Password<b>', 'trim|xss_clean|min_length[1]|max_length[256]|alpha_numeric|matches[password]');
		$this->form_validation->set_rules('g-recaptcha-response', '<b>Captcha<b>', 'trim|required|xss_clean|required|min_length[1]|callback__ck_validation_recaptcha');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# define data
			$data = array();
			$data['name'] = $this->input->post('company_name');
			$data['code'] = $this->model_sign_up->generated_company_code();
			$data['company_type'] = 'limited';
			$data['whatsapp_number'] = $this->input->post('company_whatsapp');
			$data['telp'] = $this->input->post('company_phone');
			$data['email'] = $this->input->post('email');
			$data['verified_code'] = $this->text_ops->generated_verified_code();
			$data['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			$data['input_date'] = date('Y-m-d');
			$data['last_update'] = date('Y-m-d');

			$duration = $this->input->post('duration');
			$start_date = date('Y-m-d');
			$end_date = date('Y-m-d', strtotime('+'.$duration.' month'));

			$data_subscription = array();
			$data_subscription['payment_status'] = 'process';
			$data_subscription['duration'] = $duration;
			$data_subscription['pay_per_month'] = 200000;
			$data_subscription['total'] = 200000 * $duration;
			$data_subscription['start_date_subscribtion'] = $start_date;
			$data_subscription['end_date_subscribtion'] = $end_date;
			$data_subscription['transaction_date'] = date('Y-m-d H:i:s');
			$data_subscription['last_update'] = date('Y-m-d H:i:s');

			# insert new company
			$feedBack = $this->model_sign_up_cud->insert_new_company($data, $data_subscription);
			# filter feedBack
			if ($feedBack) {
				$return = array(
					'error' => false,
					'error_msg' => 'Proses pendaftaran akun baru berhasil dilakukan.',
					'data' => $data['code'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses pendaftaran akun baru gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
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
