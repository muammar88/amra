<?php

/**
 *  -----------------------
 *	Daftar transaksi hotel Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_hotel extends CI_Controller
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
		$this->load->model('Model_daftar_transaksi_hotel', 'model_daftar_transaksi_hotel');
		# model daftar mobil cud
		$this->load->model('Model_daftar_transaksi_hotel_cud', 'model_daftar_transaksi_hotel_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# daftar transaksi hotel
	function daftar_transaksi_hotel()
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
			$total = $this->model_daftar_transaksi_hotel->get_total_daftar_transaksi_hotel($search);
			$list = $this->model_daftar_transaksi_hotel->get_index_daftar_transaksi_hotel($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi hotel tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi hotel berhasil ditemukan.',
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

	# get info transaksi hotel
	function get_info_transaksi_hotel()
	{
		$error = 0;
		$error_msg = '';
		# invoice
		$invoice = $this->random_code_ops->rand_invoice_hotel();
		# list hotel
		$list_hotel = $this->model_daftar_transaksi_hotel->get_list_hotel();
		if (count($list_hotel) == 0) {
			$error = 1;
			$error_msg = 'List hotel tidak ditemukan.';
		}
		# list city
		$list_city = $this->model_daftar_transaksi_hotel->get_list_city();
		if (count($list_city) == 0) {
			$error = 1;
			$error_msg = 'List kota tidak ditemukan.';
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
				'error_msg' => 'Data transaksi hotel berhasil ditemukan.',
				'invoice' => $invoice,
				'list_hotel' => $list_hotel,
				'list_city' => $list_city,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_city($id, $list_city_id)
	{
		if (in_array($id, json_decode($list_city_id))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_city', 'ID Kota tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_hotel($id, $list_hotel_id)
	{
		if (in_array($id, json_decode($list_hotel_id))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_hotel', 'ID Hotel tidak ditemukan.');
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

	function _ck_invoice_hotel_exist($invoice)
	{
		if (!$this->model_daftar_transaksi_hotel->check_invoice_hotel_exist($invoice)) {
			return true;
		} else {
			$this->form_validation->set_message('_ck_invoice_hotel_exist', 'Invoice hotel sudah terdaftar dipangkalan data.');
			return false;
		}
	}

	function _ck_transaksi_hotel_id_exist($id)
	{
		if ($this->model_daftar_transaksi_hotel->check_transaksi_hotel_id_exist($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_transaksi_hotel_id_exist', 'Hotel id tidak ditemukan.');
			return FALSE;
		}
	}

	# get proses add update hotel
	function proses_addupdate_hotel()
	{
		# get list hotel id
		$list_hotel_id = $this->model_daftar_transaksi_hotel->get_list_id_hotel();
		# get list city id
		$list_city_id = $this->model_daftar_transaksi_hotel->get_list_id_city();

		$return = array();
		$error = 0;
		$error_msg = '';
		// $this->form_validation->set_rules('id','<b>Id Transaksi Hotel<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_transaksi_hotel_id_exist');
		$this->form_validation->set_rules('pembayar', '<b>Pembayar Hotel<b>', 'trim|required|xss_clean|numeric|min_length[1]');
		# invoice
		// if( ! $this->input->post('id') ){
		$this->form_validation->set_rules('invoice', '<b>Invoice Transaksi Hotel<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_hotel_exist');
		// }
		# Pembayaran hidden
		foreach ($this->input->post('pembayar_hidden') as $key => $value) {
			$this->form_validation->set_rules("pembayar_hidden[" . $key . "]", "Pembayaran Hidden", 'trim|required|xss_clean|min_length[1]|numeric');
		}
		# nama pelanggan
		foreach ($this->input->post('nama') as $key => $value) {
			$this->form_validation->set_rules("nama[" . $key . "]", "Nama Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# nomor identitas
		foreach ($this->input->post('nomor_identitas') as $key => $value) {
			$this->form_validation->set_rules("nomor_identitas[" . $key . "]", "Nomor Identitas Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# city
		foreach ($this->input->post('city') as $key => $value) {
			$this->form_validation->set_rules("city[" . $key . "]", "Nama Kota Pelanggan", 'trim|required|xss_clean|min_length[1]|callback__ck_city[' . json_encode($list_city_id) . ']');
		}
		# tempat_lahir
		foreach ($this->input->post('tempat_lahir') as $key => $value) {
			$this->form_validation->set_rules("tempat_lahir[" . $key . "]", "Tempat Lahir Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# tempat_lahir
		foreach ($this->input->post('tanggal_lahir') as $key => $value) {
			$this->form_validation->set_rules("tanggal_lahir[" . $key . "]", "Tanggal Lahir Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# hotel
		foreach ($this->input->post('hotel') as $key => $value) {
			$this->form_validation->set_rules("hotel[" . $key . "]", "Nama Hotel yang Dipilih", 'trim|required|xss_clean|min_length[1]|callback__ck_hotel[' . json_encode($list_hotel_id) . ']');
		}
		# check_in_date
		foreach ($this->input->post('check_in_date') as $key => $value) {
			$this->form_validation->set_rules("check_in_date[" . $key . "]", "Tanggal Check In Hotel", 'trim|required|xss_clean|min_length[1]');
		}
		# check_out_date
		foreach ($this->input->post('check_out_date') as $key => $value) {
			$this->form_validation->set_rules("check_out_date[" . $key . "]", "Tanggal Check OUT Hotel", 'trim|required|xss_clean|min_length[1]');
		}
		# Harga per paket
		foreach ($this->input->post('price') as $key => $value) {
			$this->form_validation->set_rules("price[" . $key . "]", "Harga per Paket", 'trim|required|xss_clean|min_length[1]|callback__ck_harga_paket');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$pembayar = 	$this->input->post('pembayar');
			$invoice = $this->input->post('invoice');
			$pembayar_hidden = $this->input->post('pembayar_hidden');
			$nama = $this->input->post('nama');
			$nomor_identitas = $this->input->post('nomor_identitas');
			$city = $this->input->post('city');
			$tempat_lahir = $this->input->post('tempat_lahir');
			$tanggal_lahir = $this->input->post('tanggal_lahir');
			$hotel = $this->input->post('hotel');
			$check_in_date = $this->input->post('check_in_date');
			$check_out_date = $this->input->post('check_out_date');
			$harga_per_paket = $this->input->post('price');

			$data = array();
			$data['invoice'] = $this->input->post('invoice');
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['company_id'] = $this->company_id;
			$data['last_update'] = date('Y-m-d');
			$data['input_date'] = date('Y-m-d');

			$data_detail = array();
			foreach ($nama as $key => $value) {
				$data_detail[] = array(
					'company_id' => $this->company_id,
					'name' => $value,
					'birth_place' => $tempat_lahir[$key],
					'birth_date' => $tanggal_lahir[$key],
					'identity_number' => $nomor_identitas[$key],
					'hotel_id' => $hotel[$key],
					'city_id' => $city[$key],
					'price' => $this->text_ops->hide_currency($harga_per_paket[$key]),
					'check_in_date' => $check_in_date[$key],
					'check_out_date' => $check_out_date[$key],
					'input_date' => date('Y-m-d'),
					'last_update' => date('Y-m-d')
				);
				if ($pembayar_hidden[$key] == $pembayar) {
					$data['payer'] = $nama[$key];
					$data['payer_identity'] = $nomor_identitas[$key];
				}
			}

			if (!$this->model_daftar_transaksi_hotel_cud->insert_transaksi_hotel($data, $data_detail)) {
				$error = 1;
				$error_msg = 'Data transaksi hotel gagal disimpan';
			}
			# filter feedBack
			if ($error == 0) {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_hotel',
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


	function delete_hotel()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Transaksi Hotel<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_transaksi_hotel_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_daftar_transaksi_hotel_cud->delete_hotel($this->input->post('id'))) {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_hotel',
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


	function cetak_kwitansi_hotel()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Transaksi Visa<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_transaksi_hotel_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$invoice = $this->model_daftar_transaksi_hotel->get_invoice($this->input->post('id'));
			if ($invoice == '') {
				$error = 1;
				$error_msg = 'Nomor invoice tidak ditemukan.';
			} else {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_hotel',
					'invoice' => $invoice
				)));
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Sesi cetak kwitansi transaksi hotel berhasil digenerated.',
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
