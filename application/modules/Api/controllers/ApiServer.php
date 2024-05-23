<?php

/**
 *  -----------------------
 *	Api Server Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class ApiServer extends CI_Controller
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_api', 'model_api');
		$this->load->model('ModelRead/Model_general', 'model_general');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function index()
	{
		// echo "masuk";
		//$this->config->set_item('csrf_protection', FALSE);
		if ($this->input->get('p') == 'homeData') {
			$this->_homeData();
		} elseif ($this->input->get('p') == 'saveDataAkun') {
			$this->_saveDataAkun();
		} elseif ($this->input->get('p') == 'getListTambahSaldo') {
			$this->_getListTambahSaldo();
		} elseif ($this->input->get('p') == 'getTiketTambahSaldo') {
			$this->_getTiketTambahSaldo();
		} elseif ($this->input->get('p') == 'deleteRequestTambahSaldo') {
			$this->_deleteRequestTambahSaldo();
		} elseif ($this->input->get('p') == 'wasSend') {
			$this->_wasSend();
		}
	}

	// code perusahaan
	function _ck_code_perusahaan_exist($codeCompany)
	{
		if (!$this->model_api->check_company_code($codeCompany)) {
			$this->form_validation->set_message('_ck_code_perusahaan_exist', 'Kode perusahaan tidak ditemukan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ck_token($token)
	{
		$kode_perusahaan = $this->input->post('company_code');
		if (!$this->model_api->validation_token($kode_perusahaan, $token)) {
			$this->form_validation->set_message('_ck_token', 'Kode perusahaan tidak ditemukan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ck_password_baru($password_baru)
	{
		if ($this->input->post('password_baru') and $this->input->post('password_baru') != '') {
			if ($this->input->post('password_lama') == '') {
				$this->form_validation->set_message('_ck_password_baru', 'Jika ingin merubah password. Anda wajib mengisi password lama.');
				return FALSE;
			} elseif ($this->input->post('password_baru') == $this->input->post('password_lama')) {
				$this->form_validation->set_message('_ck_password_baru', 'Password baru tidak boleh sama dengan password lama.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_password_lama()
	{
		if ($this->input->post('password_lama')) {
			if (!$this->model_api->check_password_lama($this->input->post('password_lama'))) {
				$this->form_validation->set_message('_ck_password_lama', 'Password lama tidak valid.');
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	function _ck_nominal($nominal)
	{
		$c_nominal  = $this->text_ops->hide_currency($nominal);
		if ($c_nominal == 0) {
			$this->form_validation->set_message('_ck_nominal', 'Nominal tambah saldo tidak boleh nol.');
			return FALSE;
		} else {
			$keperluan = $this->input->post('keperluan');
			if ($keperluan == 'Tabungan Umrah') {
				$company_code = $this->input->post('company_code');
				$token = $this->input->post('token');
				$personal_id = $this->model_api->get_personal_id_by_token($company_code, $token);
				if ($this->model_general->get_pool_id($personal_id) == 0) {
					$jamaah_id = $this->model_general->get_jamaah_id_by_personal_id($personal_id);
					if ($jamaah_id != 0) {
						$fee_info = $this->model_general->fee_keagenan_deposit_paket($jamaah_id);
						if (count($fee_info) > 0) {
							$total_fee = 0;
							foreach ($fee_info as $key => $value) {
								$total_fee = $total_fee + $value['fee'];
							}
							if ($c_nominal >= $total_fee) {
								return TRUE;
							} else {
								$this->form_validation->set_message('_ck_nominal', 'Nominal Deposit Tabungan Umrah Pertama tidak boleh lebih kecil dari Rp ' . number_format($total_fee) . ',-.');
								return FALSE;
							}
						} else {
							return TRUE;
						}
					} else {
						$this->form_validation->set_message('_ck_nominal', 'Anda tidak terdaftar sebagai jamaah. Silahkan lakukan proses pendaftaran terlebih dahulu pada petugas untuk memulai proses Tabungan Umrah Anda.');
						return FALSE;
					}
				} else {
					return TRUE;
				}
			} else {
				return TRUE;
			}
		}
	}

	function _ck_bank_pembayaran($nama_bank)
	{
		if (!$this->model_api->check_nama_bank_exist($nama_bank)) {
			$this->form_validation->set_message('_ck_bank_pembayaran', 'Nama bank yang dipilih tidak ditemukan dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ck_trx_number($trx_number)
	{
		$token = $this->input->post('token');
		if (!$this->model_api->check_transaction_number_axist($trx_number, $token)) {
			$this->form_validation->set_message('_ck_trx_number', 'Nomor transaksi tidak ditemukan dipangkalan data.');
			return FALSE;
		} else {
			return  TRUE;
		}
	}

	function _wasSend()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_token');
		$this->form_validation->set_rules('trx_number', '<b>Transaksi Number<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_trx_number');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			# get company id
			$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			# retrive post data
			$company_code = $this->input->post('company_code');
			$token = $this->input->post('token');
			$trx_number = $this->input->post('trx_number');
			# get personal id
			$personal_id = $this->model_api->get_personal_id_by_token($company_code, $token);
			# define data
			$data = array();
			$data['sending_payment_status'] = 'sudah_dikirim';
			$data['last_update'] = date('Y-m-d H:i:s');
			# insert process
			if (!$this->model_api->sudahdikirim($data, $trx_number, $personal_id)) {
				$error = 1;
				$error_msg = 'Tiket gagal dihapus';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					'token' => $this->input->post('token')
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $this->input->post('token')
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg'    => validation_errors(),
					'token' => $this->input->post('token')
				);
			}
		}
		echo json_encode($return);
	}

	function _deleteRequestTambahSaldo()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_token');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			# get company id
			$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			# retrive post data
			$company_code = $this->input->post('company_code');
			$token = $this->input->post('token');
			# get personal id
			$personal_id = $this->model_api->get_personal_id_by_token($company_code, $token);
			$request_exist = $this->model_api->check_request_proses_axist($personal_id, $company_id);
			if( $request_exist == true ) {
				# insert process
				if (!$this->model_api->deleteRequestTiket($personal_id, $company_id)) {
					$error = 1;
					$error_msg = 'Tiket gagal dihapus';
				}
			}else{
				$error = 1;
				$error_msg = 'Tiket yang dihapus hanya tiket yang belum disetujui.';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					'token' => $this->input->post('token')
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $this->input->post('token')
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg'    => validation_errors(),
					'token' => $this->input->post('token')
				);
			}
		}
		echo json_encode($return);
	}

	function _getTiketTambahSaldo()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_token');
		$this->form_validation->set_rules('nominal', '<b>Nominal<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nominal');
		$this->form_validation->set_rules('bank_pembayaran', '<b>Bank Pembayaran<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank_pembayaran');
		$this->form_validation->set_rules('keperluan', '<b>Keperluan<b>', 'trim|required|xss_clean|min_length[1]|in_list[Deposit,Tabungan Umrah]');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			# get company id
			$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			# retrive post data
			$company_code = $this->input->post('company_code');
			$token = $this->input->post('token');
			$nominal = $this->text_ops->hide_currency($this->input->post('nominal'));
			$nama_bank = $this->input->post('bank_pembayaran');
			$keperluan = $this->input->post('keperluan');
			# get bank id
			$bank_info = $this->model_api->get_bank_info($nama_bank);
			$personal_id = $this->model_api->get_personal_id_by_token($company_code, $token);
			# data
			$data = array();
			$data['transaction_number'] = $this->model_api->gen_transction_number();
			$data['company_id'] = $company_id;
			$data['personal_id'] = $personal_id;
			$data['amount'] = $this->text_ops->hide_currency($this->input->post('nominal'));
			$data['amount_code'] = $this->model_api->gen_amount_code($personal_id);
			$data['activity_type'] = $keperluan == 'Tabungan Umrah' ? 'deposit_paket' : 'deposit';
			$data['payment_source'] = 'transfer';
			$data['bank_id'] = $bank_info['bank_id'];
			$data['bank_account'] = $bank_info['account_number'];
			$data['account_name'] = $bank_info['account_name'];
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			# insert process
			if (!$this->model_api->insert_member_transaction_request($data)) {
				$error = 1;
				$error_msg = 'Tiket gagal dibuat';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					'token' => $this->input->post('token')
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $this->input->post('token')
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg'    => strip_tags(validation_errors()),
					'token' => $this->input->post('token')
				);
			}
		}
		echo json_encode($return);
	}

	function _getListTambahSaldo()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_token');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			$token = $this->input->post('token');
			$list_bank = $this->model_api->get_rek_info();
			$data = array();
			$data['list_bank'] = $list_bank;
			if (count($list_bank) == 0) {
				$error = 1;
				$error_msg = 'Daftar Bank Tidak Ditemukan.';
			}
			$list_tambah_saldo = array();
			if ($error == 0) {
				$list_tambah_saldo = $this->model_api->get_list_tambah_saldo($token);
				if (count($list_tambah_saldo) > 0) {
					$data['list_tambah_saldo'] = $list_tambah_saldo;
				}
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					'data' => $data
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $this->input->post('token')
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg'    => validation_errors(),
					'token' => $this->input->post('token')
				);
			}
		}
		echo json_encode($return);
	}

	# save data akun
	function _saveDataAkun()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_token');
		$this->form_validation->set_rules('csrf_code', '<b>CSRF Code<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('csrf_name', '<b>CSRF Name<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_lengkap', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tempat_lahir', '<b>Tempat Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_lahir', '<b>Tanggal Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('password_lama', '<b>Password Lama<b>', 'trim|xss_clean|min_length[1]|callback__ck_password_lama');
		$this->form_validation->set_rules('password_baru', '<b>Password Baru<b>', 'trim|xss_clean|min_length[1]|callback__ck_password_baru');
		/*
		 Validation process
	 */
		if ($this->form_validation->run()) {
			# personal id
			$personal_id = $this->model_api->get_personal_id_by_token($this->input->post('company_code'), $this->input->post('token'));
			# data
			$data = array();
			$data['fullname'] = $this->input->post('nama_lengkap');
			$data['identity_number'] = $this->input->post('nomor_identitas');
			$data['birth_place'] = $this->input->post('tempat_lahir');
			$data['birth_date'] = $this->input->post('tanggal_lahir');
			if ($this->input->post('password_baru')) {
				$data['password'] = password_hash($this->input->post('password_baru') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			#  update process
			if (!$this->model_api->update_data_akun($personal_id, $data)) {
				$error = 1;
				$error_msg = 'Proses update data akun gagal dilakukan.';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses update data akun berhasil dilakukan.',
					'token' => $this->input->post('token'),
					'csrf_code' => $this->input->post('csrf_code'),
					'csrf_name' => $this->input->post('csrf_name'),
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $this->input->post('token')
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					'token' => $this->input->post('token')
				);
			}
		}
		echo json_encode($return);
	}

	# update home
	function _homeData()
	{
		$return 	= array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_token');
		/*
        Validation process
     */
		if ($this->form_validation->run()) {
			# token
			$token = $this->input->post('token');
			# get headline
			$headline = $this->model_api->get_headline();
			# get info paket
			$info_paket = $this->model_api->get_info_paket();
			# get info akun
			$info_akun = $this->model_api->get_info_akun();
			# saldo
			$saldo = 'Rp ' . number_format($this->model_api->saldo_akun());

			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					'token' => $token,
					'csrf_name' => $this->security->get_csrf_token_name(),
					'csrf_code' => $this->security->get_csrf_hash(),
					'data' => array(
						'fullname' => $info_akun['fullname'],
						'identity_number' => $info_akun['identity_number'],
						'nomor_whatsapp' => $info_akun['nomor_whatsapp'],
						'birth_place' => $info_akun['birth_place'],
						'birth_date' => $info_akun['birth_date'],
						'saldo' =>  $saldo,
						'headline' => $headline,
						'list_paket' => $info_paket
					)
				);
				// $this->output->set_header('Cookie: PHPSESSID='.$this->session->session_id.'; ci_session='.$this->session->session_id.';');
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $token,
					'csrf_name' => $this->security->get_csrf_token_name(),
					'csrf_code' => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'csrf_name' => $this->security->get_csrf_token_name(),
					'csrf_code' => $this->security->get_csrf_hash(),
					'error_msg'    => validation_errors(),
					'token' => $this->input->post('token')
				);
			}
		}
		echo json_encode($return);
	}
}
