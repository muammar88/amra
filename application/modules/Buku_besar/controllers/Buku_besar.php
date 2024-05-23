<?php

/**
 *  -----------------------
 *	Slider Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Buku_besar extends CI_Controller
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
		$this->load->model('Model_buku_besar', 'model_buku_besar');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function get_filter_buku_besar()
	{
		$error = 0;
		$error_msg = '';
		# list akun
		$list_akun = $this->model_buku_besar->get_list_akun();
		if (count($list_akun) == 0) {
			$error = 1;
			$error_msg = 'List akun tidak ditemukan.';
		}
		# list periode
		$list_periode = $this->model_buku_besar->get_list_periode();
		if (count($list_periode) == 0) {
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
				'error_msg' => 'Data buku besar berhasil ditemukan.',
				'list_akun' => $list_akun,
				'list_periode' => $list_periode,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_akun_id_exist($akun_id)
	{
		if ($akun_id != 0) {
			if ($this->model_buku_besar->check_akun_id_exist($akun_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_akun_id_exist', 'ID akun tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_periode_exist($id)
	{
		if( $id != 0 ) {
			if ( $this->model_buku_besar->check_periode_exist($id) ) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_periode_exist', 'ID Periode tidak ditemukan.');
				return FALSE;
			}
		}else{
			return TRUE;
		}
	}

	# daftar buku besar
	function daftar_buku_besar()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('akun',	'<b>Akun<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_akun_id_exist');
		$this->form_validation->set_rules('periode',	'<b>Periode<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_periode_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$param = array();
			$param['akun'] = $this->input->post('akun');
			$param['periode'] = $this->input->post('periode');
			$list = $this->model_buku_besar->get_index_daftar_buku_besar($param);
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar buku besar tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar buku besar berhasil ditemukan.',
					'data' => $list['list'],
					'saldo_akhir' => $list['saldo_akhir'],
					'total_debet' => $list['total_debet'],
					'total_kredit' => $list['total_kredit'],
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

	# download excel
	function download_excel_buku_besar()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('akun',	'<b>Akun<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_akun_id_exist');
		$this->form_validation->set_rules('periode',	'<b>Periode<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_periode_exist');
		/*
		  Validation process
	  */
		if ($this->form_validation->run()) {
			# set session
			$this->session->set_userdata(array('download_to_excel' => array(
				'type' => 'download_buku_besar',
				'akun' => $this->input->post('akun'),
				'periode' => $this->input->post('periode')
			)));
			if (!$this->session->userdata('download_to_excel')) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar data buku besar tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar data buku besar berhasil ditemukan.',
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
