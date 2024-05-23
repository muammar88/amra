<?php

/**
 *  -----------------------
 *	Daftar bank Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_bank extends CI_Controller
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
		$this->load->model('Model_daftar_bank', 'model_daftar_bank');
		# model daftar bank cud
		$this->load->model('Model_daftar_bank_cud', 'model_daftar_bank_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	public function daftar_banks()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_daftar_bank->get_total_daftar_bank($search);
			$list 	= $this->model_daftar_bank->get_index_daftar_bank($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar bank tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar bank berhasil ditemukan.',
					'total' => $total,
					'data' => $list,
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


	function get_info_addupdate_bank()
	{
		$code = $this->random_code_ops->rand_bank_code();
		if ($code == '') {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Kode bank gagal di generated.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Kode bank berhasil di generated.',
				'data' => $code,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_kode_bank($kode_bank)
	{
		if ($this->input->post('id')) {
			if ($this->model_daftar_bank->check_exist_kode_bank($kode_bank, $this->input->post('id'))) {
				$this->form_validation->set_message('_ck_kode_bank', 'Kode bank sudah terdaftar pada bank yang lain. Silahkan gunakan  kode bank yang lain.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			if ($this->model_daftar_bank->check_exist_kode_bank($kode_bank)) {
				$this->form_validation->set_message('_ck_kode_bank', 'Kode bank sudah terdaftar pada bank yang lain. Silahkan gunakan  kode bank yang lain.');
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	function _ck_id_bank_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_daftar_bank->check_id_bank_exist($this->input->post('id'))) {
				return  TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_bank_exist', 'ID Bank tidak ditemukan.');
				return FALSE;
			}
		}
	}

	function proses_addupdate_bank()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bank<b>', 	'trim|xss_clean|numeric|min_length[1]|callback__ck_id_bank_exist');
		if (!$this->input->post('id')) {
			$this->form_validation->set_rules('kode_bank', '<b>Kode Bank<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_kode_bank');
		}
		$this->form_validation->set_rules('nama_bank',	'<b>Nama Bank<b>', 	'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			#  receive data
			$data = array();
			$data['mst_bank']['nama_bank'] = strtoupper($this->input->post('nama_bank'));
			$data['mst_bank']['last_update'] = date('Y-m-d');
			//$data_akun = array();
			if ($this->input->post('id')) {
				$kode_bank = $this->model_daftar_bank->get_kode_bank($this->input->post('id'));
				//  update data akun
				$data['akun']['nama_akun_secondary'] = strtoupper($this->input->post('nama_bank'));
				$feedBack = $this->model_daftar_bank_cud->update_bank($this->input->post('id'), $data, $kode_bank);
			} else {
				$data['mst_bank']['company_id'] = $this->company_id;
				$data['mst_bank']['kode_bank'] = $this->input->post('kode_bank');
				$data['mst_bank']['input_date'] = date('Y-m-d');
				// insert new akun
				$data['akun']['company_id'] = $this->company_id;
				$data['akun']['akun_primary_id'] = '1';
				$data['akun']['nomor_akun_secondary'] = $this->model_daftar_bank->generated_kode_akun_bank();
				$data['akun']['nama_akun_secondary'] = strtoupper($this->input->post('nama_bank'));
				$data['akun']['tipe_akun'] = 'bawaan';
				$data['akun']['path'] = 'bank:kodebank:' . $this->input->post('kode_bank');
				$feedBack = $this->model_daftar_bank_cud->insert_bank($data);
			}
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data bank berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data bank gagal disimpan.',
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


	function get_info_edit_bank()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bank<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_bank_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$feedBack = $this->model_daftar_bank->get_info_edit_bank($this->input->post('id'));

			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data bank berhasil ditemukan.',
					'data' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data bank gagal ditemukan.',
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

	// delete bank
	function delete_bank()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bank<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_bank_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// checking proses
			if ($this->model_daftar_bank->checking_akun_in_jurnal($this->input->post('id'))) {
				$error = 1;
				$error_msg = 'Anda tidak dapat menghapus bank ini,  karena akunnya masih terdapat didalam riwayat jurnal. Silahkan hapus riwayat tersebut terlebih dahulu untuk melanjutkan proses ini.';
			}
			// proses penghapusan
			if ($error == 0) {
				$kode_bank = $this->model_daftar_bank->get_kode_bank($this->input->post('id'));
				if (!$this->model_daftar_bank_cud->delete_bank($this->input->post('id'), $kode_bank)) {
					$error = 1;
					$error_msg = 'Proses delete bank gagal dilakukan';
				}
			}
			// filte feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data bank berhasil dihapus.',
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
