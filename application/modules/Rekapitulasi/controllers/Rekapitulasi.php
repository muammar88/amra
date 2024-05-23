<?php

/**
 *  -----------------------
 *	Rekapitulasi Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Rekapitulasi extends CI_Controller
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
		$this->load->model('Model_rekapitulasi', 'model_rekapitulasi');
		# model daftar mobil cud
		$this->load->model('Model_rekapitulasi_cud', 'model_rekapitulasi_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_rekapitulasi()
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
			$total = $this->model_rekapitulasi->get_total_daftar_rekapitulasi($search);
			$list = $this->model_rekapitulasi->get_index_daftar_rekapitulasi($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar rekapitulasi tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar rekapitulasi berhasil ditemukan.',
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

	function get_info_list_tiket()
	{
		$error = 0;
		$error_msg = '';
		# invoice
		$invoice = $this->random_code_ops->rand_recapitulation();
		// # total
		// $total = $this->model_rekapitulasi->get_total_daftar_tiket('');
		// $list = $this->model_rekapitulasi->get_index_daftar_tiket(5, 0, '');
		# filter error
		if ($error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => $error_msg,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data nomor invoice rekapitulasi berhasil digenerated.',
				'invoice' => $invoice,
				// 'total' => $total,
				// 'list' => $list,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function daftar_tiket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('listRekap',	'<b>List Rekap<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$list_rekap = $this->model_rekapitulasi->get_was_rekap();

			if ($this->input->post('listRekap') != '') {
				$decode = json_decode($this->input->post('listRekap'));
				foreach ($decode as $key => $value) {
					$list_rekap[] = $value;
				}
			}

			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total = $this->model_rekapitulasi->get_total_daftar_tiket($search, $list_rekap);
			$list = $this->model_rekapitulasi->get_index_daftar_tiket($perpage, $start_at, $search, $list_rekap);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi tiket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi tiket berhasil ditemukan.',
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

	function _ck_invoice_rekapitulasi($invoice)
	{
		if ($this->model_rekapitulasi->check_invoice_rekapitulasi($invoice)) {
			$this->form_validation->set_message('_ck_invoice_rekapitulasi', 'Nomor invoice sudah terdaftar didalam database.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function proses_addupdate_rekapitulasi()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('invoice', '<b>Nomor Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_rekapitulasi');
		$this->form_validation->set_rules('nama', '<b>Id Transaksi Visa<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('address', '<b>Pembayar Visa<b>', 'trim|required|xss_clean|min_length[1]');
		# Pembayaran hidden
		foreach ($this->input->post('tiket_transaction_id') as $key => $value) {
			$this->form_validation->set_rules("tiket_transaction_id[" . $key . "]", "Pembayaran Hidden", 'trim|required|xss_clean|min_length[1]|numeric');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# retrive post
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['recapitulation_number'] = $this->input->post('invoice');
			$data['receiver'] = $this->input->post('nama');
			$data['receiver_address'] = $this->input->post('address');
			$data['input_date'] = date('Y-m-d');
			$data['last_update'] = date('Y-m-d');

			# tiket transaction id
			$tiket_transaction_id = $this->input->post('tiket_transaction_id');

			# check tiket transaction id exist
			if ($this->model_rekapitulasi->check_tiket_transaction_id($tiket_transaction_id)) {
				$error = true;
				$error_msg = 'tiket id sudah direkap pada nomor rekap yang lain.';
			}
			# insert process
			if (!$this->model_rekapitulasi_cud->insert_rekapitulasi($data, $tiket_transaction_id)) {
				$error = true;
				$error_msg = 'Proses rekapitulasi gagal dilakukan.';
			}
			# filter feedBack
			if ($error == 0) {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_rekap',
					'invoice' => $this->input->post('invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi visa berhasil disimpan.',
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
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	function _ck_id_rekapitulasi($id)
	{
		if ($this->model_rekapitulasi->check_id_rekapitulasi_exist($id)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function delete_rekapitulasi()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Rekapitulasi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_rekapitulasi');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_rekapitulasi_cud->delete_rekapitulasi($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data rekapitulasi berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data rekapitulasi gagal dihapus.',
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

	function cetak_rekapitulasi()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Rekapitulasi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_rekapitulasi');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get invoice
			$invoice = $this->model_rekapitulasi->get_invoice_rekapitulasi($this->input->post('id'));
			# filter feedBack
			if ($invoice != '') {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_rekap',
					'invoice' => $invoice
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data invoice rekapitulasi berhasil ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data rekapitulasi gagal dihapus.',
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
