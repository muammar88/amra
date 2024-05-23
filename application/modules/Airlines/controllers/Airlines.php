<?php

/**
 *  -----------------------
 *	Airlines Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Airlines extends CI_Controller
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
		$this->load->model('Model_airlines', 'model_airlines');
		# model trans tiket cud
		$this->load->model('Model_airlines_cud', 'model_airlines_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_airlines()
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
			$total 	= $this->model_airlines->get_total_daftar_airlines($search);
			$list 	= $this->model_airlines->get_index_daftar_airlines($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar airlines tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar airlines berhasil ditemukan.',
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

	function _ck_id_airlines_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_airlines->check_id_airlines_exist($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_airlines_exist', 'ID Airlines tidak ditemukan.');
				return FALSE;
			}
		}
	}

	function proses_addupdate_airlines()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Airlines<b>', 	'trim|xss_clean|numeric|min_length[1]|callback__ck_id_airlines_exist');
		$this->form_validation->set_rules('nama_airlines',	'<b>Nama Airlines<b>', 	'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			#  receive data
			$data = array();
			$data['mst_airlines']['airlines_name'] = strtoupper($this->input->post('nama_airlines'));
			$data['mst_airlines']['last_update'] = date('Y-m-d');
			# filter
			if ($this->input->post('id')) {
				// akun deposit
				$data['akun']['deposit'] = array('nama_akun_secondary' => 'DEPOSIT ' . strtoupper($this->input->post('nama_airlines')));
				// akun pendapatan
				$data['akun']['pendapatan'] = array('nama_akun_secondary' => 'PENDAPATAN ' . strtoupper($this->input->post('nama_airlines')));
				// akun hpp
				$data['akun']['hpp'] = array('nama_akun_secondary' => 'HPP ' . strtoupper($this->input->post('nama_airlines')));
				// feedBack
				$feedBack = $this->model_airlines_cud->update_airlines($this->input->post('id'), $data);
			} else {
				$data['mst_airlines']['company_id'] = $this->company_id;
				$data['mst_airlines']['input_date'] = date('Y-m-d');
				// akun deposit
				$data['akun']['deposit'] = array(
					'akun_primary_id' => '1',
					'company_id' => $this->company_id,
					'nomor_akun_secondary' => $this->model_airlines->generated_nomor_akun_airlines_deposit(),
					'nama_akun_secondary' => 'DEPOSIT ' . strtoupper($this->input->post('nama_airlines')),
					'tipe_akun' => 'bawaan',
					'path' => 'airlines:deposit:'
				);
				// akun pendapatan
				$data['akun']['pendapatan'] = array(
					'akun_primary_id' => '4',
					'company_id' => $this->company_id,
					'nomor_akun_secondary' => $this->model_airlines->generated_nomor_akun_airlines_pendapatan(),
					'nama_akun_secondary' => 'PENDAPATAN ' . strtoupper($this->input->post('nama_airlines')),
					'tipe_akun' => 'bawaan',
					'path' => 'airlines:pendapatan:'
				);
				// akun hpp
				$data['akun']['hpp'] = array(
					'akun_primary_id' => '5',
					'company_id' => $this->company_id,
					'nomor_akun_secondary' => $this->model_airlines->generated_nomor_akun_airlines_hpp(),
					'nama_akun_secondary' => 'HPP ' . strtoupper($this->input->post('nama_airlines')),
					'tipe_akun' => 'bawaan',
					'path' => 'airlines:hpp:'
				);
				$feedBack = $this->model_airlines_cud->insert_airlines($data);
			}
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data airlines berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data airlines gagal disimpan.',
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

	function get_info_edit_airlines()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Airlines<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_airlines_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$feedBack = $this->model_airlines->get_info_edit_airlines($this->input->post('id'));

			if ( count($feedBack) > 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data airlines berhasil ditemukan.',
					'data' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data airlines gagal ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if ( validation_errors() ) {
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

	// delete airline
	function delete_airlines()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Airlines<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_airlines_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// checking proses
			// if ($this->model_airlines->checking_akun_airlines_in_jurnal($this->input->post('id'))) {
			// 	$error = 1;
			// 	$error_msg = 'Anda tidak dapat menghapus airlines ini,  karena akunnya masih terdapat didalam riwayat jurnal. Silahkan hapus riwayat tersebut terlebih dahulu untuk melanjutkan proses ini.';
			// }
			// proses penghapusan
			if ($error == 0) {
				if (!$this->model_airlines_cud->delete_airlines($this->input->post('id'))) {
					$error = 1;
					$error_msg = 'Proses delete airlines gagal dilakukan';
				}
			}
			// filte feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data airlines berhasil dihapus.',
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
