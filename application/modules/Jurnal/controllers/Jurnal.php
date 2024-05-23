<?php

/**
 *  -----------------------
 *	Daftar jurnal Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */
// 
defined('BASEPATH') or exit('No direct script access allowed');

class Jurnal extends CI_Controller
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
		$this->load->model('Model_jurnal', 'model_jurnal');
		# model daftar mobil cud
		$this->load->model('Model_jurnal_cud', 'model_jurnal_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function get_filter_daftar_jurnal()
	{
		$error = 0;
		$error_msg = '';
		# list akun
		$list_akun = $this->model_jurnal->get_list_akun();
		// list akun
		// print_r($list_akun);

		if (count($list_akun) == 0) {

			// echo "masuk List Akun<br>";
			$error = 1;
			$error_msg = 'List akun tidak ditemukan.';
		}
		# list periode
		$list_periode = $this->model_jurnal->get_list_periode();
		if (count($list_periode) == 0) {
			// echo "masuk List Periode<br>";
			$error = 1;
			$error_msg = 'List periode tidak ditemukan.';
		}

		if ($error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => $error_msg,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data jurnal berhasil ditemukan.',
				'list_akun' => $list_akun,
				'list_periode' => $list_periode,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function daftar_jurnal()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('tanggal',	'<b>Tanggal<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('akun',	'<b>Akun<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('periode',	'<b>Periode<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$param = array();
			$param['tanggal'] = $this->input->post('tanggal');
			$param['akun'] = $this->input->post('akun');
			$param['periode'] = $this->input->post('periode');

			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_jurnal->get_total_daftar_jurnal($param);
			$list 	= $this->model_jurnal->get_index_daftar_jurnal($perpage, $start_at, $param);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar jurnal tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar jurnal berhasil ditemukan.',
					'total' => $total,
					'data' => $list,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	function _ck_jurnal_id_exist($id)
	{
		if ($this->model_jurnal->check_jurnal_id_exist($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_jurnal_id_exist', 'ID jurnal tidak ditemukan.');
			return FALSE;
		}
	}

	function delete_jurnal()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Jurnal<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_jurnal_id_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_jurnal_cud->delete_jurnal($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data jurnal berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data jurnal gagal dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
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
