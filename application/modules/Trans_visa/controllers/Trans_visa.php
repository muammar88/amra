<?php

/**
 *  -----------------------
 *	Daftar transaksi visa Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_visa extends CI_Controller
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
		$this->load->model('Model_daftar_transaksi_visa', 'model_daftar_transaksi_visa');
		# model daftar mobil cud
		$this->load->model('Model_daftar_transaksi_visa_cud', 'model_daftar_transaksi_visa_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_transaksi_visa()
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
			$total = $this->model_daftar_transaksi_visa->get_total_daftar_transaksi_visa($search);
			$list = $this->model_daftar_transaksi_visa->get_index_daftar_transaksi_visa($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi visa tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi visa berhasil ditemukan.',
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

	function get_info_transaksi_visa()
	{
		$error = 0;
		$error_msg = '';
		# invoice
		$invoice = $this->random_code_ops->rand_invoice_visa();
		# list request type
		$list_request_type = $this->model_daftar_transaksi_visa->get_request_type();
		if (count($list_request_type) == 0) {
			$error = 1;
			$error_msg = 'List tipe permohonan tidak ditemukan.';
		}
		# list city
		$list_city = $this->model_daftar_transaksi_visa->get_list_city();
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
				'error_msg' => 'Data transaksi visa berhasil ditemukan.',
				'invoice' => $invoice,
				'request_type' => $list_request_type,
				'list_city' => $list_city,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_transaksi_visa_id_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_daftar_transaksi_visa->check_transaksi_visa_id_exist($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_transaksi_visa_id_exist', 'ID Transaksi visa tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_tipe_permohonan($id, $list_tipe_permohonan)
	{
		if (in_array($id,  json_decode($list_tipe_permohonan))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_tipe_permohonan', 'ID Tipe Permohonan tidak ditemukan.');
			return FALSE;
		}
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

	function _ck_harga_paket($harga_paket)
	{
		if ($this->text_ops->hide_currency($harga_paket) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_paket', 'Harga paket tidak boleh nol.');
			return FALSE;
		}
	}

	function _ck_invoice_visa_exist($invoice)
	{
		if (!$this->model_daftar_transaksi_visa->check_invoice_exist($invoice)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_invoice_visa_exist', 'Invoice sudah terdaftar didalam database.');
			return FALSE;
		}
	}

	function proses_addupdate_visa()
	{
		# get list request type
		$list_request_type_id = $this->model_daftar_transaksi_visa->get_list_id_request_type();
		# get list city id
		$list_city_id = $this->model_daftar_transaksi_visa->get_list_id_city();

		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Transaksi Visa<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_transaksi_visa_id_exist');
		$this->form_validation->set_rules('pembayar', '<b>Pembayar Visa<b>', 'trim|required|xss_clean|numeric|min_length[1]');

		# invoice
		if (!$this->input->post('id')) {
			$this->form_validation->set_rules('invoice', '<b>Invoice Transaksi Visa<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_visa_exist');
		}
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
		# gender
		foreach ($this->input->post('gender') as $key => $value) {
			$this->form_validation->set_rules("gender[" . $key . "]", "Jenis Kelamin Pelanggan", 'trim|required|xss_clean|min_length[1]|in_list[laki-laki,perempuan]');
		}
		# tempat_lahir
		foreach ($this->input->post('tempat_lahir') as $key => $value) {
			$this->form_validation->set_rules("tempat_lahir[" . $key . "]", "Tempat Lahir Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# tanggal_lahir
		foreach ($this->input->post('tanggal_lahir') as $key => $value) {
			$this->form_validation->set_rules("tanggal_lahir[" . $key . "]", "Tanggal Lahir Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# kewarganegaraan
		foreach ($this->input->post('kewarganegaraan') as $key => $value) {
			$this->form_validation->set_rules("kewarganegaraan[" . $key . "]", "Kewarganegaraan Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# jenis_permohonan
		foreach ($this->input->post('jenis_permohonan') as $key => $value) {
			$this->form_validation->set_rules("jenis_permohonan[" . $key . "]", "Jenis Permohonan Visa Pelanggan", 'trim|required|xss_clean|min_length[1]|callback__ck_tipe_permohonan[' . json_encode($list_request_type_id) . ']');
		}
		# tanggal_permohonan
		foreach ($this->input->post('tanggal_permohonan') as $key => $value) {
			$this->form_validation->set_rules("tanggal_permohonan[" . $key . "]", "Tanggal Permohonan Visa Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# no_passport
		foreach ($this->input->post('no_passport') as $key => $value) {
			$this->form_validation->set_rules("no_passport[" . $key . "]", "Nomor Passport Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# release_date_passport
		foreach ($this->input->post('release_date_passport') as $key => $value) {
			$this->form_validation->set_rules("release_date_passport[" . $key . "]", "Tanggal Dikeluarkan Passport Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# release_place_passport
		foreach ($this->input->post('release_place_passport') as $key => $value) {
			$this->form_validation->set_rules("release_place_passport[" . $key . "]", "Tempat Dikeluarkan Passport Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# passport_valid_date
		foreach ($this->input->post('passport_valid_date') as $key => $value) {
			$this->form_validation->set_rules("passport_valid_date[" . $key . "]", "Tanggal Berlaku Passport Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# profession_idn
		foreach ($this->input->post('profession_idn') as $key => $value) {
			$this->form_validation->set_rules("profession_idn[" . $key . "]", "Pekerjaan Pelanggan Diindonesia", 'trim|required|xss_clean|min_length[1]');
		}
		# profession_ln
		foreach ($this->input->post('profession_ln') as $key => $value) {
			$this->form_validation->set_rules("profession_ln[" . $key . "]", "Pekerjaan Pelanggan Diluar negeri", 'trim|required|xss_clean|min_length[1]');
		}
		# profession_address
		foreach ($this->input->post('profession_address') as $key => $value) {
			$this->form_validation->set_rules("profession_address[" . $key . "]", "Alamat Pekerjaan Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# pos code
		foreach ($this->input->post('pos_code') as $key => $value) {
			$this->form_validation->set_rules("pos_code[" . $key . "]", "Kode Pos Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# city
		foreach ($this->input->post('city') as $key => $value) {
			$this->form_validation->set_rules("city[" . $key . "]", "Kota Alamat Pelanggan", 'trim|required|xss_clean|min_length[1]|callback__ck_city[' . json_encode($list_city_id) . ']');
		}
		# country
		foreach ($this->input->post('country') as $key => $value) {
			$this->form_validation->set_rules("country[" . $key . "]", "Negara Asal Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# telephone
		foreach ($this->input->post('telephone') as $key => $value) {
			$this->form_validation->set_rules("telephone[" . $key . "]", "Nomor Telephone Pelanggan", 'trim|required|xss_clean|min_length[1]');
		}
		# harga_paket
		foreach ($this->input->post('harga_paket') as $key => $value) {
			$this->form_validation->set_rules("harga_paket[" . $key . "]", "Harga Paket Pelanggan", 'trim|required|xss_clean|min_length[1]|callback__ck_harga_paket');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# nama pelanggan
			$nama_pelanggan = $this->input->post('nama');
			# nomor identitas
			$nomor_identitas = $this->input->post('nomor_identitas');
			# gender
			$gender = $this->input->post('gender');
			# tempat_lahir
			$tempat_lahir = $this->input->post('tempat_lahir');
			# tanggal_lahir
			$tanggal_lahir = $this->input->post('tanggal_lahir');
			# kewarganegaraan
			$kewarganegaraan = $this->input->post('kewarganegaraan');
			# jenis_permohonan
			$jenis_permohonan = $this->input->post('jenis_permohonan');
			# tanggal_permohonan
			$tanggal_permohonan = $this->input->post('tanggal_permohonan');
			# no_passport
			$no_passport = $this->input->post('no_passport');
			# release_date_passport
			$release_date_passport = $this->input->post('release_date_passport');
			# release_place_passport
			$release_place_passport = $this->input->post('release_place_passport');
			# passport_valid_date
			$passport_valid_date = $this->input->post('passport_valid_date');
			# profession_idn
			$profession_idn = $this->input->post('profession_idn');
			# profession_ln
			$profession_ln = $this->input->post('profession_ln');
			# profession_address
			$profession_address = $this->input->post('profession_address');
			# pos code
			$pos_code = $this->input->post('pos_code');
			# city
			$city = $this->input->post('city');
			# country
			$country = $this->input->post('country');
			# telephone
			$telephone = $this->input->post('telephone');
			# harga_paket
			$harga_paket = $this->input->post('harga_paket');
			# pembayar
			$pembayar = $this->input->post('pembayar');
			# pembayar_hidden
			$pembayar_hidden = $this->input->post('pembayar_hidden');
			# pembayar
			$pembayar = $this->input->post('pembayar');

			#  receive data
			$data = array();
			$data['last_update'] = date('Y-m-d');

			$data_detail = array();
			if ($this->input->post('id')) {
				foreach ($nama_pelanggan as $key => $value) {
					$data_detail[] = array(
						'transaction_visa_id' => $this->input->post('id'),
						'company_id' => $this->company_id,
						'request_id' => $jenis_permohonan[$key],
						'request_date' => $tanggal_permohonan[$key],
						'name' => $nama_pelanggan[$key],
						'identity_number' => $nomor_identitas[$key],
						'gender' => $gender[$key],
						'birth_place' => $tempat_lahir[$key],
						'birth_date' => $tanggal_lahir[$key],
						'citizenship' => $kewarganegaraan[$key],
						'passport_number' => $no_passport[$key],
						'date_issued' => $release_date_passport[$key],
						'place_of_release' => $release_place_passport[$key],
						'valid_until' => $passport_valid_date[$key],
						'profession_idn' => $profession_idn[$key],
						'profession_foreign' => $profession_ln[$key],
						'profession_address' => $profession_address[$key],
						'profession_pos_code' => $pos_code[$key],
						'profession_city' => $city[$key],
						'profession_country' => $country[$key],
						'profession_telephone' => $telephone[$key],
						'price' => $this->text_ops->hide_currency($harga_paket[$key]),
						'input_date' => date('Y-m-d'),
						'last_update' => date('Y-m-d')
					);
					if ($pembayar_hidden[$key] == $pembayar) {
						$data['payer'] = $nama_pelanggan[$key];
						$data['payer_identity'] = $nomor_identitas[$key];
					}
				}
				if (!$this->model_daftar_transaksi_visa_cud->update_transaksi_visa($this->input->post('id'), $data, $data_detail)) {
					$error = 1;
					$error_msg = 'Data transaksi visa gagal diperharui';
				}
			} else {
				foreach ($nama_pelanggan as $key => $value) {
					$data_detail[] = array(
						'company_id' => $this->company_id,
						'request_id' => $jenis_permohonan[$key],
						'request_date' => $tanggal_permohonan[$key],
						'name' => $nama_pelanggan[$key],
						'identity_number' => $nomor_identitas[$key],
						'gender' => $gender[$key],
						'birth_place' => $tempat_lahir[$key],
						'birth_date' => $tanggal_lahir[$key],
						'citizenship' => $kewarganegaraan[$key],
						'passport_number' => $no_passport[$key],
						'date_issued' => $release_date_passport[$key],
						'place_of_release' => $release_place_passport[$key],
						'valid_until' => $passport_valid_date[$key],
						'profession_idn' => $profession_idn[$key],
						'profession_foreign' => $profession_ln[$key],
						'profession_address' => $profession_address[$key],
						'profession_pos_code' => $pos_code[$key],
						'profession_city' => $city[$key],
						'profession_country' => $country[$key],
						'profession_telephone' => $telephone[$key],
						'price' => $this->text_ops->hide_currency($harga_paket[$key]),
						'input_date' => date('Y-m-d'),
						'last_update' => date('Y-m-d')
					);
					if ($pembayar_hidden[$key] == $pembayar) {
						$data['payer'] = $nama_pelanggan[$key];
						$data['payer_identity'] = $nomor_identitas[$key];
					}
				}

				if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
					$data['receiver'] = "Administrator";
				} else {
					$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				}
				$data['company_id'] = $this->company_id;
				$data['invoice'] = $this->input->post('invoice');
				$data['input_date'] = date('Y-m-d');

				if (!$this->model_daftar_transaksi_visa_cud->insert_transaksi_visa($data, $data_detail)) {
					$error = 1;
					$error_msg = 'Data transaksi visa gagal disimpan';
				}
			}
			# filter feedBack
			if ($error == 0) {

				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_visa',
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


	function delete_transaksi_visa()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Transaksi Visa<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_transaksi_visa_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_daftar_transaksi_visa_cud->delete_transaksi_visa($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi visa berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi visa gagal dihapus.',
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

	function cetak_transaksi_visa()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Transaksi Visa<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_transaksi_visa_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$invoice = $this->model_daftar_transaksi_visa->get_invoice($this->input->post('id'));
			if ($invoice == '') {
				$error = 1;
				$error_msg = 'Nomor invoice tidak ditemukan.';
			} else {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_visa',
					'invoice' => $invoice
				)));
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Sesi cetak kwitansi transaksi visa berhasil digenerated.',
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
