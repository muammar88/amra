<?php

/**
 *  -----------------------
 *	Kas keluar masuk Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Kas_keluar_masuk extends CI_Controller
{

	private $company_code;
	private $company_id;
	private $akun;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		# model daftar bank
		$this->load->model('Model_kas_keluar_masuk', 'model_kas_keluar_masuk');
		# model daftar bank cud
		$this->load->model('Model_kas_keluar_masuk_cud', 'model_kas_keluar_masuk_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}


	function daftar_kas_keluar_masuk()
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
			$total 	= $this->model_kas_keluar_masuk->get_total_kas_keluar_masuk($search);
			$list 	= $this->model_kas_keluar_masuk->get_index_kas_keluar_masuk($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar kas keluar masuk tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar kas keluar masuk berhasil ditemukan.',
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

	function get_info_transaksi_keluar_masuk()
	{
		$error = 0;
		$error_msg = '';
		# list akun
		$list_akun = $this->model_kas_keluar_masuk->get_list_akun();
		if (count($list_akun) == 0) {
			$error = 1;
			$error_msg = 'List akun tidak ditemukan.';
		}
		# invoice
		$invoice = $this->text_ops->generated_invoice_kas();
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
				'error_msg' => 'Data akun berhasil ditemukan.',
				'data' => array(
					'list_akun' => $list_akun,
					'invoice' => $invoice
				),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function get_info_edit_transaksi_keluar_masuk()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Kas Keluar Masuk<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_kas_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$error = 0;
			$error_msg = '';
			# list akun
			$list_akun = $this->model_kas_keluar_masuk->get_list_akun();
			# feedBack
			$feedBack = $this->model_kas_keluar_masuk->get_info_kas_keluar_masuk($this->input->post('id'));
			# feedBack
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Info transaksi keluar masuk berhasil ditemukan.',
					'data' => array('list_akun' => $list_akun),
					'value' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info transaksi keluar masuk tidak ditemukan.',
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

	function _ck_id_kas_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_kas_keluar_masuk->check_kas_id_exist($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_kas_exist', 'ID kas tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_akun_exist($nomor_akun)
	{
		if (count($this->akun) > 0) {
			if (in_array($nomor_akun, $this->akun)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_akun_exist', 'Nomor akun tidak ditemukan.');
				return FALSE;
			}
		} else {
			$this->form_validation->set_message('_ck_akun_exist', 'Nomor akun tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_saldo_not_empty($saldo)
	{
		$saldo = $this->text_ops->hide_currency($saldo);
		if ($saldo > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function _ck_invoice($invoice)
	{
		if (!$this->model_kas_keluar_masuk->check_invoice_exist($invoice)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_invoice', 'Nomor invoice sudah terdefinisi.');
			return FALSE;
		}
	}

	function proses_addupdate_kas_keluar_masuk()
	{
		$this->akun = $this->model_kas_keluar_masuk->get_list_nomor_akun();
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Kas<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_kas_exist');
		if (!$this->input->post('id')) {
			$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice');
		}
		$this->form_validation->set_rules('tanggal_transaksi', '<b>Tanggal Transaksi<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('diterima_dibayar', '<b>Diterima Dibayar<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('ref', '<b>Ref<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('keterangan', '<b>Keterangan<b>', 'trim|required|xss_clean|min_length[1]');
		# akun debet
		foreach ($this->input->post('akun_debet') as $keyAkunDebet => $valAkunDebet) {
			$this->form_validation->set_rules("akun_debet[" . $keyAkunDebet . "]", "Akun Debet", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_akun_exist');
		}
		# akun kredit
		foreach ($this->input->post('akun_kredit') as $keyAkunKredit => $valAkunKredit) {
			$this->form_validation->set_rules("akun_kredit[" . $keyAkunKredit . "]", "Akun Kredit", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_akun_exist');
		}
		# saldo
		foreach ($this->input->post('saldo') as $keySaldo => $valSaldo) {
			$this->form_validation->set_rules("saldo[" . $keySaldo . "]", "Saldo", 'trim|required|xss_clean|min_length[1]|callback__ck_saldo_not_empty');
		}
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# receive data
			$data = array();
			$data['dibayar_diterima'] = $this->input->post('diterima_dibayar');
			# level_akun
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['last_update'] = date('Y-m-d H:i:s');
			# get akun debet, akun kredit dan saldo
			$akun_debet = $this->input->post('akun_debet');
			$akun_kredit = $this->input->post('akun_kredit');
			$saldo = $this->input->post('saldo');

			if ($this->input->post('id')) {
				$invoice = $this->model_kas_keluar_masuk->get_invoice_by_id($this->input->post('id'));
			} else {
				$invoice = $this->input->post('invoice');
			}
			# invoice
			$source = 'generaltransaksi:invoice:' . $invoice;
			# get last periode
			$last_periode = $this->model_kas_keluar_masuk->get_last_periode();
			# data jurnal
			$data_jurnal = array();
			foreach ($saldo as $key => $value) {
				$data_jurnal[] = array(
					'company_id' => $this->company_id,
					'source' => $source,
					'ref' => $this->input->post('ref'),
					'ket' => $this->input->post('keterangan'),
					'akun_debet' => $akun_debet[$key],
					'akun_kredit' => $akun_kredit[$key],
					'saldo' => $this->text_ops->hide_currency($value),
					'periode_id' => $last_periode,
					'input_date' => date('Y-m-d H:i:s'),
					'last_update'  => date('Y-m-d H:i:s')
				);
				if (substr($akun_debet[$key], 0, 1) == '1') {
					$data['status_kwitansi'] = 'masuk';
				}
				if (substr($akun_kredit[$key], 0, 1) == '1') {
					$data['status_kwitansi'] = 'keluar';
				}
			}
			# filter proses
			if ($this->input->post('id')) {
				# update process kas keluar masuk
				if (!$this->model_kas_keluar_masuk_cud->update_kas_keluar_masuk($this->input->post('id'), $data, $source, $data_jurnal)) {
					$this->session->set_userdata(
						array('cetak_invoice' => array(
							  'type' => 'cetak_kas_keluar_masuk',
							  'invoice' => $invoice
					)));
					$error = 1;
					$error_msg = 'Data kas gagal diperharui';
				}
			} else {
				# data kas
				$data['invoice'] = $this->input->post('invoice');
				$data['company_id'] = $this->company_id;
				$data['input_date'] = date('Y-m-d H:i:s');

				# insert process kas keluar masuk
				if (!$this->model_kas_keluar_masuk_cud->insert_kas_keluar_masuk($data, $data_jurnal)) {
					# set userdata
					$this->session->set_userdata(
						array('cetak_invoice' => array(
							  'type' => 'cetak_kas_keluar_masuk',
							  'invoice' => $invoice
					)));
					// 'nomor_registrasi' => $this->input->post('no_register'),
					// 'invoice' => $this->input->post('invoiceID')
					$error = 1;
					$error_msg = 'Data kas gagal disimpan';
				}
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data kas berhasil disimpan.',
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

	function delete_kas_keluar_masuk()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Kas Keluar Masuk<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_kas_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# retrive id
			$id = $this->input->post('id');
			# get invoice
			$invoice = $this->model_kas_keluar_masuk->get_invoice_by_id($id);
			# delete process
			if ($this->model_kas_keluar_masuk_cud->delete_kas_masuk_keluar($id, $invoice)) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete kas keluar masuk berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete kas keluar masuk gagal dilakukan.',
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

	function cetak_kas_keluar_masuk(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Kas Keluar Masuk<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_kas_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# retrive id
			$id = $this->input->post('id');
			# get invoice
			$invoice = $this->model_kas_keluar_masuk->get_invoice_by_id($id);
			# delete process
			if ( $invoice != '' ) {
				// set userdata
				$this->session->set_userdata(
					array('cetak_invoice' => array(
						  'type' => 'cetak_kas_keluar_masuk',
						  'invoice' => $invoice
				)));
				// return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses cetak kas keluar masuk berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses cetak kas keluar masuk gagal dilakukan.',
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
