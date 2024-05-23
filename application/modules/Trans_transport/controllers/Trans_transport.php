<?php

/**
 *  -----------------------
 *	Trans transport Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_transport extends CI_Controller
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
		$this->load->model('Model_trans_transport', 'model_trans_transport');
		# model daftar mobil cud
		$this->load->model('Model_trans_transport_cud', 'model_trans_transport_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}


	function daftar_transaksi_transport()
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
			$total = $this->model_trans_transport->get_total_daftar_transaksi_transport($search);
			$list = $this->model_trans_transport->get_index_daftar_transaksi_transport($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi transport tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi transport berhasil ditemukan.',
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

	function get_info_transaksi_transport()
	{
		$error = 0;
		$error_msg = '';
		# invoice
		$invoice = $this->random_code_ops->rand_invoice_transport();
		# list car
		$list_car = $this->model_trans_transport->get_list_car();
		if (count($list_car) == 0) {
			$error = 1;
			$error_msg = 'List mobil tidak ditemukan.';
		}
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
				'error_msg' => 'Data transaksi transport berhasil ditemukan.',
				'invoice' => $invoice,
				'list_car' => $list_car,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function  _ck_invoice_transport_exist($invoice)
	{
		if (!$this->model_trans_transport->check_invoice_transport_exist($invoice)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_invoice_transport_exist', 'Invoice transport sudah terdaftar.');
			return FALSE;
		}
	}

	function _ck_car_list($id, $car_list)
	{
		if (in_array($id, json_decode($car_list))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_car_list', 'ID Jenis Mobil tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_harga_paket($harga_paket)
	{
		if ($this->text_ops->hide_currency($harga_paket) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_paket', 'Harga paket tidak boleh nol.');
			return FALSE;
		}
	}

	function proses_addupdate_transport()
	{
		# get list car id
		$list_car_id = $this->model_trans_transport->get_list_id_car();

		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('invoice', '<b>Invoice Transaksi Hotel<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_transport_exist');
		$this->form_validation->set_rules('nama', '<b>Nama Pelanggan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas Pelanggan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('address', '<b>Alamat Pelanggan<b>', 'trim|required|xss_clean|min_length[1]');
		# Jenis Mobil
		foreach ($this->input->post('car_list') as $key => $value) {
			$this->form_validation->set_rules("car_list[" . $key . "]", "Jenis Mobil", 'trim|required|xss_clean|min_length[1]|callback__ck_car_list[' . json_encode($list_car_id) . ']');
		}
		# nomor plat
		foreach ($this->input->post('nomor_plat') as $key => $value) {
			$this->form_validation->set_rules("nomor_plat[" . $key . "]", "Nomor Plat", 'trim|required|xss_clean|min_length[1]');
		}
		# Harga per paket
		foreach ($this->input->post('price') as $key => $value) {
			$this->form_validation->set_rules("price[" . $key . "]", "Harga per Paket", 'trim|required|xss_clean|min_length[1]|callback__ck_harga_paket');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$jenis_mobil = $this->input->post('car_list');
			$nomor_plat = $this->input->post('nomor_plat');
			$harga_paket = $this->input->post('price');

			$data = array();
			$data['invoice'] = $this->input->post('invoice');
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['company_id'] = $this->company_id;
			$data['payer'] = $this->input->post('nama');
			$data['payer_identity'] = $this->input->post('nomor_identitas');
			$data['address'] = $this->input->post('address');
			$data['input_date'] = date('Y-m-d');
			$data['last_update'] = date('Y-m-d');

			$data_detail = array();
			foreach ($jenis_mobil as $key => $value) {
				$data_detail[] = array(
					'company_id' => $this->company_id,
					'car_id'  => $value,
					'car_number' => $nomor_plat[$key],
					'price' => $this->text_ops->hide_currency($harga_paket[$key]),
					'input_date' => date('Y-m-d'),
					'last_update' => date('Y-m-d')
				);
			}

			if (!$this->model_trans_transport_cud->insert_transaksi_transport($data, $data_detail)) {
				$error = 1;
				$error_msg = 'Data transaksi transport gagal disimpan';
			}
			# filter feedBack
			if ($error == 0) {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_transport',
					'invoice' => $this->input->post('invoice')
				)));

				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi hotel berhasil disimpan.',
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

	function _ck_id_transport_exist($id)
	{
		if ($this->model_trans_transport->check_id_transaction_transport_exist($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_id_transport_exist', 'ID Transaksi transport tidak ditemukan.');
			return FALSE;
		}
	}

	function delete_transport()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Transport<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_transport_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_trans_transport_cud->delete_transport($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi hotel berhasil disimpan.',
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

	function cetak_transaksi_transport()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Transport<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_transport_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$invoice = $this->model_trans_transport->get_invoice_by_id($this->input->post('id'));

			# filter feedBack
			if ($invoice != '') {
				# generated session cetak
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_transport',
					'invoice' => $invoice
				)));

				$return = array(
					'error'	=> false,
					'error_msg' => 'Sesi cetak kwitansi transaksi transport berhasil digenerated.',
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
}
