<?php

/**
 *  -----------------------
 *	Trans_tiket Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_tiket extends CI_Controller
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
		$this->load->model('Model_trans_tiket', 'model_trans_tiket');
		# model trans tiket cud
		$this->load->model('Model_trans_tiket_cud', 'model_trans_tiket_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function _ck_otoritas(){
      	if($this->session->userdata($this->config->item('apps_name'))['level_akun'] != 'administrator'){
        	$this->form_validation->set_message('_ck_otoritas', 'Anda Tidak Berhak Untuk Melakukan Proses Hapus.');
         	return FALSE;
      	}else{
         	return TRUE;
      	}
   	}

	function daftar_all_daftar_transaksi_tiket()
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

			$total 	= $this->model_trans_tiket->get_total_trans_tiket($search);
			$list 	= $this->model_trans_tiket->get_index_trans_tiket($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi tiket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi tiket berhasil ditemukan.',
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


	function get_info_tiket_transaction()
	{
		$error = 0;
		# generated invoice
		$no_invoice = $this->text_ops->generated_invoice_tiket();
		# generated no register
		$no_register = $this->text_ops->generated_register_tiket();
		# get list airlines
		$airlines = $this->model_trans_tiket->get_list_airlines();
		# filter airlines
		if( count( $airlines ) == 0 ) {
			$error = 1;
			$error_msg = 'Daftar airlines belum didefinisikan. Untuk melanjutkan, silahkan definisi terlebih dahulu daftar airlines.';
		}
		# filter
		if ($error != 0) {
			$return = array(
				'error'	=> true,
				'error_msg' => $error_msg,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data transaksi berhasil ditemukan.',
				'data' => array(
					'no_register' => $no_register,
					'no_invoice' => $no_invoice,
					'airlines' => $airlines
				),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_bayar_tiket_transaksi($dibayar)
	{
		if ($this->text_ops->hide_currency($dibayar) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_bayar_tiket_transaksi', 'Jumlah pembayaran harus lebih besar dari nol.');
			return FALSE;
		}
	}

	function _ck_pax($pax)
	{
		if ($pax > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_pax', 'Jumlah pax harus lebih besar dari nol.');
			return FALSE;
		}
	}

	function _ck_airlines($airlines)
	{
		if ($this->model_trans_tiket->check_airlines_exist($airlines)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_airlines', 'ID maskapai tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_kodebooking($kode_booking)
	{
		if ($kode_booking != '') {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_kodebooking', 'Kode booking tidak boleh kosong.');
			return FALSE;
		}
	}

	function _ck_harga_travel($harga_travel)
	{
		if ($this->text_ops->hide_currency($harga_travel) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_travel', 'Harga travel tidak boleh nol.');
			return FALSE;
		}
	}

	function _ck_harga_kostumer($harga_kostumer)
	{
		if ($this->text_ops->hide_currency($harga_kostumer) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_kostumer', 'Harga kostumer tidak boleh nol.');
			return FALSE;
		}
	}

	function _ck_nomor_register_tiket($nomor_register)
	{
		if (!$this->model_trans_tiket->check_nomor_register_exist($nomor_register)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_nomor_register_tiket', 'Nomor register sudah terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	function _ck_nomor_invoice_tiket($nomor_invoice)
	{
		if (!$this->model_trans_tiket->check_nomor_invoice_exist($nomor_invoice)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_nomor_invoice_tiket', 'Nomor invoice sudah terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	function tiketing_prosess()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('no_register', '<b>Nomor Register<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_register_tiket');
		$this->form_validation->set_rules('no_invoice', '<b>Nomor Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_invoice_tiket');
		$this->form_validation->set_rules('nama_pelanggan',	'<b>Nama Pelanggan<b>', 	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas',	'<b>Nomor Identitas<b>', 	'trim|required|xss_clean|numeric|min_length[1]');
		$this->form_validation->set_rules('dibayar',	'<b>Dibayar<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_bayar_tiket_transaksi');
		# pax
		foreach ($this->input->post('pax') as $keyPax => $valPax) {
			$this->form_validation->set_rules("pax[" . $keyPax . "]", "Pax", 'trim|xss_clean|min_length[1]|numeric|callback__ck_pax');
		}
		# airlines
		foreach ($this->input->post('airlines') as $keyAirlines => $valAirlines) {
			$this->form_validation->set_rules("airlines[" . $keyAirlines . "]", "Airlines", 'trim|xss_clean|min_length[1]|numeric|callback__ck_airlines');
		}
		# kode booking
		foreach ($this->input->post('kode_booking') as $keyKodeBooking => $valKodeBooking) {
			$this->form_validation->set_rules("kode_booking[" . $keyKodeBooking . "]", "Kode Booking", 'trim|xss_clean|min_length[1]|callback__ck_kodebooking');
		}
		# tanggal keberangkatan
		foreach ($this->input->post('departure_date') as $keyDepartureDate => $valDepartureDate) {
			$this->form_validation->set_rules("departure_date[" . $keyDepartureDate . "]", "Tanggal Keberangkatan", 'trim|xss_clean|min_length[1]');
		}
		# harga travel
		foreach ($this->input->post('harga_travel') as $keyHargaTravel => $valHargaTravel) {
			$this->form_validation->set_rules("harga_travel[" . $keyHargaTravel . "]", "Harga Travel", 'trim|xss_clean|min_length[1]|callback__ck_harga_travel');
		}
		# harga costumer
		foreach ($this->input->post('harga_costumer') as $keyHargaKostumer => $valHargaKostumer) {
			$this->form_validation->set_rules("harga_costumer[" . $keyHargaKostumer . "]", "Harga Kostumer", 'trim|xss_clean|min_length[1]|callback__ck_harga_kostumer');
		}
		/*
         Validation process
      */
		if ($this->form_validation->run()) {

			// info pembayaran
			$no_register = $this->input->post('no_register');
			$nama_pelanggan = $this->input->post('nama_pelanggan');
			$nomor_identitas = $this->input->post('nomor_identitas');
			$dibayar = $this->text_ops->hide_currency($this->input->post('dibayar'));

			// info tiket
			$pax = $this->input->post('pax');
			$airlines = $this->input->post('airlines');
			$kode_booking = $this->input->post('kode_booking');
			$departure_date = $this->input->post('departure_date');
			$harga_travel = $this->input->post('harga_travel');
			$harga_costumer = $this->input->post('harga_costumer');

			# data transaction
			$data_transaction_detail = array();
			$data_jurnal = array();
			$total_transaksi = 0;

			$list_airlines = array();
			$jurnal_airlines = array();
			$tot_pax = 0;
			foreach ($pax as $keyPax => $valuePax) {
				$total_transaksi = $total_transaksi + ($valuePax * $this->text_ops->hide_currency($harga_costumer[$keyPax]));
				$data_transaction_detail[] = array(
					'company_id' => $this->company_id,
					'pax' => $valuePax,
					'code_booking' => $kode_booking[$keyPax],
					'airlines_id' => $airlines[$keyPax],
					'departure_date' => $departure_date[$keyPax],
					'travel_price' => $this->text_ops->hide_currency($harga_travel[$keyPax]),
					'costumer_price' => $this->text_ops->hide_currency($harga_costumer[$keyPax]),
					'input_date' => date('Y-m-d'),
					'last_update' => date('Y-m-d')
				);

				$jurnal_airlines[] = array(
					'airlines_id' => $airlines[$keyPax],
					'travel_price' => $this->text_ops->hide_currency($harga_travel[$keyPax]),
					'costumer_price' => $this->text_ops->hide_currency($harga_costumer[$keyPax]),
					'pax' => $valuePax,
					'kode_booking' => $kode_booking[$keyPax]
				);
				$list_airlines[] = $airlines[$keyPax];
			}

			$data_transaction = array();
			$data_transaction['company_id'] = $this->company_id;
			$data_transaction['no_register'] = $this->input->post('no_register');
			$data_transaction['total_transaksi'] = $total_transaksi;
			$data_transaction['input_date'] = date('Y-m-d H:i:s');
			$data_transaction['last_update'] = date('Y-m-d H:i:s');

			# data history transaction
			$data_transaction_history = array();
			$data_transaction_history['company_id'] = $this->company_id;
			$data_transaction_history['costumer_name'] = $nama_pelanggan;
			$data_transaction_history['costumer_identity'] = $nomor_identitas;
			$data_transaction_history['invoice'] = $this->input->post('no_invoice');
			$data_transaction_history['biaya'] = $dibayar;
			$data_transaction_history['ket'] = 'cash';
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data_transaction_history['receiver'] = "Administrator";
			} else {
				$data_transaction_history['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data_transaction_history['input_date'] = date('Y-m-d H:i:s');
			$data_transaction_history['last_update'] = date('Y-m-d H:i:s');

			# get data jurnal
			$data_jurnal = $this->get_data_jurnal($no_register, $jurnal_airlines, $dibayar, $list_airlines);

			// insert process
			if ($this->model_trans_tiket_cud->insert_trans_tiket($data_transaction, $data_transaction_detail, $data_transaction_history, $data_jurnal)) {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'trans_tiket',
					'no_register' => $no_register,
					'invoice' => $this->input->post('no_invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi tiket berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi tiket gagal disimpan.',
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

	function get_data_jurnal($no_register, $jurnal_airlines, $dibayar, $list_airlines)
	{
		// preparing jurnal data
		$last_periode = $this->model_trans_tiket->get_last_periode();
		$get_list_name_airlines = $this->model_trans_tiket->get_list_name_airlines_by_array($list_airlines);
		// insert jurnal
		$data_jurnal = array();
		$sisa_dibayar = $dibayar;

		foreach ($jurnal_airlines as $keyAir => $valueAir) {
			$akun = $this->model_trans_tiket->get_akun_number(array(
				'kas',
				'piutang',
				'airlines:deposit:' . $valueAir['airlines_id'],
				'airlines:pendapatan:' . $valueAir['airlines_id'],
				'airlines:hpp:' . $valueAir['airlines_id']
			));

			$sisa_bayar_sebelumnya = $sisa_dibayar;
			$sisa_dibayar = $sisa_dibayar - ($valueAir['pax'] * $valueAir['costumer_price']);
			if ($sisa_dibayar < 0) {
				# piutang
				$data_jurnal[] = array(
					'company_id' => $this->company_id,
					'source' => 'tiket:noreg:' . $no_register,
					'ref' => 'No Register :' . $no_register . '<br> Kode Booking :' . $valueAir['kode_booking'],
					'ket' => 'Tiket ' . $no_register . ' ' . $get_list_name_airlines[$valueAir['airlines_id']] . ' ' . $valueAir['kode_booking'],
					'akun_debet' => $akun['piutang'],
					'akun_kredit' => $akun['airlines:pendapatan:' . $valueAir['airlines_id']],
					'saldo' => ($sisa_bayar_sebelumnya < 0 ? ($valueAir['pax'] * $valueAir['costumer_price']) : abs($sisa_dibayar)),
					'periode_id' => $last_periode,
					'input_date' => date('Y-m-d H:i:s'),
					'last_update' => date('Y-m-d H:i:s')
				);
				# pendapatan
				$data_jurnal[] = array(
					'company_id' => $this->company_id,
					'source' => 'tiket:noreg:' . $no_register,
					'ref' => 'No Register :' . $no_register . '<br> Kode Booking :' . $valueAir['kode_booking'],
					'ket' => 'Tiket ' . $no_register . ' ' . $get_list_name_airlines[$valueAir['airlines_id']] . ' ' . $valueAir['kode_booking'],
					'akun_debet' => $akun['kas'],
					'akun_kredit' => $akun['airlines:pendapatan:' . $valueAir['airlines_id']],
					// 'akun_debet' => '',
					// 'akun_kredit' => '',
					'saldo' => ($sisa_bayar_sebelumnya < 0 ? 0 : $sisa_bayar_sebelumnya),
					'periode_id' => $last_periode,
					'input_date' => date('Y-m-d H:i:s'),
					'last_update' => date('Y-m-d H:i:s')
				);
			} else {
				# pendapatan
				$data_jurnal[] = array(
					'company_id' => $this->company_id,
					'source' => 'tiket:noreg:' . $no_register,
					'ref' => 'No Register :' . $no_register . '<br> Kode Booking :' . $valueAir['kode_booking'],
					'ket' => 'Tiket ' . $no_register . ' ' . $get_list_name_airlines[$valueAir['airlines_id']] . ' ' . $valueAir['kode_booking'],
					'akun_debet' => $akun['kas'],
					'akun_kredit' => $akun['airlines:pendapatan:' . $valueAir['airlines_id']],
					// 'akun_debet' => '',
					// 'akun_kredit' => '',
					'saldo' => ($valueAir['pax'] * $valueAir['costumer_price']),
					'periode_id' => $last_periode,
					'input_date' => date('Y-m-d H:i:s'),
					'last_update' => date('Y-m-d H:i:s')
				);
			}
			# hpp
			$data_jurnal[] = array(
				'company_id' => $this->company_id,
				'source' => 'tiket:noreg:' . $no_register,
				'ref' => 'No Register :' . $no_register . '<br> Kode Booking :' . $valueAir['kode_booking'],
				'ket' => 'Tiket ' . $no_register . ' ' . $get_list_name_airlines[$valueAir['airlines_id']] . ' ' . $valueAir['kode_booking'],
				'akun_debet' => $akun['airlines:hpp:' . $valueAir['airlines_id']],
				'akun_kredit' => $akun['airlines:deposit:' . $valueAir['airlines_id']],
				// 'akun_debet' => '',
				// 'akun_kredit' => '',
				'saldo' => ($valueAir['pax'] * $valueAir['travel_price']),
				'periode_id' => $last_periode,
				'input_date' => date('Y-m-d H:i:s'),
				'last_update' => date('Y-m-d H:i:s')
			);
		}
		return $data_jurnal;
	}

	function _ck_tiket_transaction_id($id)
	{
		if ($this->model_trans_tiket->check_tiket_transaction_id_exist($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_tiket_transaction_id', 'Tiket transaksi id tidak ditemukan di dalam pangkalan data.');
			return FALSE;
		}
	}

	function info_bayar_tiket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_tiket_transaction_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# generated invoice
			$invoice = $this->text_ops->generated_invoice_tiket();
			# get riwayat total pembayaran dan total tiket
			$feedBack = $this->model_trans_tiket->get_riwayat_pembayaran_tiket($this->input->post('id'));
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data riwayat pembayaran tiket berhasil ditemukan.',
					'data' => $feedBack,
					'invoice' => $invoice,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data riwayat pembayaran tiket tidak ditemukan.',
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

	function _ck_dibayar($dibayar)
	{
		$feedBack = $this->model_trans_tiket->check_pembayaran($this->text_ops->hide_currency($dibayar), $this->input->post('id'));
		if ($feedBack['error'] == false) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_dibayar', $feedBack['error_msg']);
			return FALSE;
		}
	}

	function proses_pembayaran_tiket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_tiket_transaction_id');
		$this->form_validation->set_rules('invoice', '<b>Invoice Transaksi Pembayaran Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_nomor_invoice_tiket');
		$this->form_validation->set_rules('nama_pelanggan', '<b>Nama Pelanggan<b>', 	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('dibayar', '<b>Dibayar<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_dibayar');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['tiket_transaction_id'] = $this->input->post('id');
			$data['costumer_name'] = $this->input->post('nama_pelanggan');
			$data['costumer_identity'] = $this->input->post('nomor_identitas');
			$data['invoice'] = $this->input->post('invoice');
			$data['biaya'] = $this->text_ops->hide_currency($this->input->post('dibayar'));
			$data['ket'] = 'cash';
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');

			$last_periode = $this->model_trans_tiket->get_last_periode();
			$akun = $this->model_trans_tiket->get_akun_number(array('kas', 'piutang'));
			// data jurnal
			$data_jurnal = array();
			$data_jurnal['company_id'] = $this->company_id;
			$data_jurnal['source'] = 'tiket:noreg:' . $this->model_trans_tiket->get_no_reg_tiket($this->input->post('id'));
			$data_jurnal['ket'] = 'Tiket ' . $this->input->post('invoice');
			$data_jurnal['akun_debet'] = $akun['kas'];
			$data_jurnal['akun_kredit'] = $akun['piutang'];
			$data_jurnal['saldo'] = $this->text_ops->hide_currency($this->input->post('dibayar'));
			$data_jurnal['periode_id'] = $last_periode;
			$data_jurnal['input_date'] = date('Y-m-d H:i:s');
			$data_jurnal['last_update'] = date('Y-m-d H:i:s');

			if ($this->model_trans_tiket_cud->insert_pembayaran_tiket($data, $data_jurnal)) {
				// generated session
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi pembayaran tiket berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi pembayaran tiket gagal disimpan.',
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

	function info_detail_riwayat_pembayaran_tiket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_tiket_transaction_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			#  get data
			$feedBack = $this->model_trans_tiket->riwayat_pembayaran_tiket($this->input->post('id'));
			# filter
			if (count($feedBack) > 0) {
				// generated session
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data riwayat pembayaran tiket berhasil ditemukan.',
					'data' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data riwayat pembayara tiket gagal ditemukan.',
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

	function delete_transaksi_tiket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_otoritas|callback__ck_tiket_transaction_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$no_reg = $this->model_trans_tiket->get_no_reg_tiket($this->input->post('id'));
			# get detail
			$tiket_transaction_detail_id = $this->model_trans_tiket->get_tiket_transaction_detail_id($this->input->post('id'));

			# filter
			if ($this->model_trans_tiket_cud->delete_transaksi_tiket($this->input->post('id'), $no_reg, $tiket_transaction_detail_id)) {
				// generated session
				$return = array(
					'error'	=> false,
					'error_msg' => 'Transaksi tiket berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi tiket gagal dihapus.',
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

	function get_info_reschedule_tiket_transaction()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_tiket_transaction_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$feedBack = array();
			$feedBack['pembayaran'] = $this->model_trans_tiket->get_sudah_bayar_tiket($this->input->post('id'));
			# generated invoice
			$feedBack['invoice'] = $this->random_code_ops->rand_tiket_history();
			# get info reschedule tiket transaction
			$feedBack['riwayat_pembayaran'] = $this->model_trans_tiket->get_info_reschedule_tiket_transaction($this->input->post('id'));
			# filter
			if ($feedBack) {
				// generated session
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info detail transaksi tiket berhasil ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
					'data' => $feedBack
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info detail transaksi tiket gagal dilakukan.',
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

	function _ck_tiket_transactin_detail_id($id)
	{
		if ($this->model_trans_tiket->check_tiket_transaction_detail_id_exit($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_tiket_transactin_detail_id', 'ID Tiket transaction detail tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_price_not_null($price, $label_name)
	{
		if ($price != '') {
			$price = $this->text_ops->hide_currency($price);
			if ($price <= 0) {
				$this->form_validation->set_message('_ck_price_not_null', $label_name . ' Tidak boleh lebih kecil atau sama dengan dari NOL.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			$this->form_validation->set_message('_ck_price_not_null', $label_name . ' Tidak boleh kosong!!!.');
			return FALSE;
		}
	}

	# reschedule
	function reschedule_tiketing_prosess()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_tiket_transaction_id');
		$this->form_validation->set_rules('nama_pelanggan', '<b>Nama Pelanggan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_invoice', '<b>Nomor Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_invoice_tiket');
		# code booking
		foreach ($this->input->post('code_booking') as $keyId => $valId) {
			$this->form_validation->set_rules("code_booking[" . $keyId . "]", "Kode Booking", 'trim|required|xss_clean|min_length[1]');
		}
		# tiket transaction detail id
		foreach ($this->input->post('tiket_transaction_detail_id') as $keyId => $valId) {
			$this->form_validation->set_rules("tiket_transaction_detail_id[" . $keyId . "]", "ID Tiket Transaction Detail", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_tiket_transactin_detail_id');
		}
		# departure dates
		foreach ($this->input->post('departure_date') as $keyDepartureDate => $valDepartureDate) {
			$this->form_validation->set_rules("departure_date[" . $keyDepartureDate . "]", "Tanggal Keberangkatan", 'trim|required|xss_clean|min_length[1]');
		}
		# harga_travel
		foreach ($this->input->post('harga_travel') as $keyHargaTravel => $valHargaTravel) {
			$this->form_validation->set_rules("harga_travel[" . $keyHargaTravel . "]", "Harga Travel", 'trim|required|xss_clean|min_length[1]|callback__ck_price_not_null["Harga Travel"]');
		}
		# harga_travel
		foreach ($this->input->post('harga_costumer') as $keyHargaCostumer => $valHargaCostumer) {
			$this->form_validation->set_rules("harga_costumer[" . $keyHargaCostumer . "]", "Harga Kostumer", 'trim|required|xss_clean|min_length[1]|callback__ck_price_not_null["Harga Kostumer"]');
		}
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# get total pembayaran
			$total_pembayaran = $this->model_trans_tiket->get_total_pembayaran($this->input->post('id'));
			# get detail trasaction
			$db_transaction_detail = $this->model_trans_tiket->get_db_transaction_detail($this->input->post('id'));
			# retrive post
			$tt_detail_id = $this->input->post('tiket_transaction_detail_id');
			$code_booking = $this->input->post('code_booking');
			$departure_date = $this->input->post('departure_date');
			$harga_travel = $this->input->post('harga_travel');
			$harga_costumer = $this->input->post('harga_costumer');
			$total_transaksi = 0;
			$old_total_transaksi = 0;
			$no_register = '';
			$update_t_t_detail = array();
			$jurnal_airlines = array();
			$list_airlines = array();



			foreach ($tt_detail_id as $keyid => $id) {
				if (isset($db_transaction_detail[$id])) {
					$harga_travel_new = $this->text_ops->hide_currency($harga_travel[$keyid]);
					$harga_costumer_new = $this->text_ops->hide_currency($harga_costumer[$keyid]);

					$perubahan = 0;
					if ($db_transaction_detail[$id]['departure_date'] != $departure_date[$keyid]) {
						$perubahan = 1;
					}
					if ($db_transaction_detail[$id]['code_booking'] != $code_booking[$keyid]) {
						$perubahan = 1;
					}
					if ($db_transaction_detail[$id]['travel_price'] != $harga_travel_new) {
						$perubahan = 1;
					}
					if ($db_transaction_detail[$id]['costumer_price'] != $harga_costumer_new) {
						$perubahan = 1;
					}
					if ($perubahan == 1) {
						$update_t_t_detail[] = array(
							'tiket_transaction_detail_id' => $id,
							'old_departure_date' => $db_transaction_detail[$id]['departure_date'],
							'new_departure_date' => $departure_date[$keyid],
							'old_travel_price' => $db_transaction_detail[$id]['travel_price'],
							'new_travel_price' => $harga_travel_new,
							'old_costumer_price' => $db_transaction_detail[$id]['costumer_price'],
							'new_costumer_price' => $harga_costumer_new,
							'old_code_booking' => $db_transaction_detail[$id]['code_booking'],
							'new_code_booking' => $code_booking[$keyid],
							'airlines_id' => $db_transaction_detail[$id]['airlines_id']
						);
						$jurnal_airlines[] =   array(
							'airlines_id' => $db_transaction_detail[$id]['airlines_id'],
							'travel_price' => $harga_travel_new,
							'costumer_price' => $harga_costumer_new,
							'pax' => $db_transaction_detail[$id]['pax'],
							'kode_booking' => $code_booking[$keyid]
						);
						$total_transaksi = $total_transaksi + ($db_transaction_detail[$id]['pax'] * $harga_costumer_new);
					} else {
						$total_transaksi = $total_transaksi + ($db_transaction_detail[$id]['pax'] * $db_transaction_detail[$id]['costumer_price']);
						$jurnal_airlines[] =   array(
							'airlines_id' => $db_transaction_detail[$id]['airlines_id'],
							'travel_price' => $db_transaction_detail[$id]['old_travel_price'],
							'costumer_price' => $db_transaction_detail[$id]['old_costumer_price'],
							'pax' => $db_transaction_detail[$id]['pax'],
							'kode_booking' => $db_transaction_detail[$id]['code_booking']
						);
					}
					$list_airlines[] = $db_transaction_detail[$id]['airlines_id'];
					$old_total_transaksi = $old_total_transaksi + ($db_transaction_detail[$id]['pax'] * $db_transaction_detail[$id]['costumer_price']);
					$no_register = $db_transaction_detail[$id]['no_register'];
				}
			}
			# update
			if (count($update_t_t_detail) > 0) {
				# data tiket transaction
				$data_tiket_transaction = array();
				$data_tiket_transaction['total_transaksi'] = $total_transaksi;
				$data_tiket_transaction['id'] = $this->input->post('id');
				$data_tiket_transaction['no_register'] = $no_register;
				# data riwayat reschedule tiket
				$data_reschedule_tiket = array();
				$data_reschedule_tiket['tiket_transaction_id'] = $this->input->post('id');
				$data_reschedule_tiket['invoice'] = $this->input->post('no_invoice');
				$data_reschedule_tiket['old_total_transaksi'] = $old_total_transaksi;
				$data_reschedule_tiket['company_id'] = $this->company_id;
				$data_reschedule_tiket['new_total_transaksi'] = $total_transaksi;
				$data_reschedule_tiket['costumer_name'] = $this->input->post('nama_pelanggan');
				$data_reschedule_tiket['costumer_identity'] = $this->input->post('nomor_identitas');
				# get receiver
				if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
					$data_reschedule_tiket['receiver'] = "Administrator";
				} else {
					$data_reschedule_tiket['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				}
				$data_reschedule_tiket['input_date'] = date('Y-m-d');
				# define tiket transaction history
				$data_tiket_transaction_history = array();
				$data_tiket_transaction_history['company_id'] = $this->company_id;
				$data_tiket_transaction_history['tiket_transaction_id'] = $this->input->post('id');
				$data_tiket_transaction_history['costumer_name'] = $this->input->post('nama_pelanggan');
				$data_tiket_transaction_history['costumer_identity'] = $this->input->post('nomor_identitas');
				$data_tiket_transaction_history['invoice'] = $this->input->post('no_invoice');
				if ($total_pembayaran >= $total_transaksi) {
					$data_tiket_transaction_history['biaya'] = $total_pembayaran;
				} else {
					$data_tiket_transaction_history['biaya'] = $total_pembayaran;
				}
				$data_tiket_transaction_history['ket'] = 'cash';
				# get receiver name
				if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
					$data_tiket_transaction_history['receiver'] = 'Administrator';
				} else {
					$data_tiket_transaction_history['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				}
				# get data jurnal
				$data_jurnal = $this->get_data_jurnal($no_register, $jurnal_airlines, $total_pembayaran, $list_airlines);
				# update process
				if (!$this->model_trans_tiket_cud->update_schedule($update_t_t_detail, $data_tiket_transaction, $data_tiket_transaction_history, $data_reschedule_tiket, $data_jurnal)) {
					$error = 1;
					$error_msg = 'Proses reschedule tiket gagal dilakukan';
				}
			} else {
				$error = 1;
				$error_msg = 'Data detail transaksi tidak di update karena tidak terdapat perubahan.';
			}
			# filter
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				# generated session
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'reschedule',
					'reschedule_id' => $this->model_trans_tiket_cud->reschedule_id()
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses reschedule tiket berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
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

	# get info refund tiket
	function get_info_refund_tiket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_tiket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$feedBack = $this->model_trans_tiket->get_info_refund_tiket_transaction($this->input->post('id'));
			# filter
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info detail transaksi tiket berhasil ditemukan.',
					'data' => $feedBack,
					'invoice' => $this->text_ops->generated_invoice_tiket(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// generated session
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info detail transaksi tiket gagal ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
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

	# jurnal refund
	function _jurnal_refund($no_register, $tiket_refund, $refund,  $airlines_id)
	{
		$param_akun = array('kas', 'piutang');
		// menentukan parameter keterangan untuk mengambil nomor akun
		foreach ($airlines_id as $key => $value) {
			$param_akun[] = 'airlines:deposit:' . $value;
			$param_akun[] = 'airlines:pendapatan:' . $value;
			$param_akun[] = 'airlines:hpp:' . $value;
		}
		# mengambil nomor akun berdasarkan param
		$akun = $this->model_trans_tiket->get_akun_number($param_akun);
		$last_periode = $this->model_trans_tiket->get_last_periode();
		$data_jurnal = array();

		foreach ($tiket_refund as $key => $value) {

			$this->db->select('akun_debet, akun_kredit, saldo')
				->from('jurnal')
				->where('company_id', $this->company_id)
				->where('source', 'tiket:noreg:' . $no_register)
				->like('ket', 'Tiket ' . $no_register . ' ' . $value['airlines_name'] . ' ' . $value['code_booking']);
			$kredit = 0;
			$debet = 0;
			$q = $this->db->get();
			if ($q->num_rows() > 0) {
				foreach ($q->result() as $row) {
					if ($row->akun_debet == $akun['piutang']) : $debet = $debet + $row->saldo;
					endif;
					if ($row->akun_kredit == $akun['piutang']) : $kredit = $kredit + $row->saldo;
					endif;
				}
			}
			$sisa_hutang = $debet - $kredit;
			// input hutang jurnal
			if ($sisa_hutang > 0) {
				$data_jurnal[] = array(
					'company_id' => $this->company_id,
					'source' => 'tiket:noreg:' . $no_register,
					'ket' => 'Tiket ' . $no_register . ' ' . $value['airlines_name'] . ' ' . $value['code_booking'],
					'akun_debet' => $akun['kas'],
					'akun_kredit' => $akun['piutang'],
					'saldo' => $sisa_hutang,
					'periode_id' => $last_periode,
					'input_date' => date('Y-m-d H:i:s'),
					'last_update' => date('Y-m-d H:i:s')
				);
			}
			// pendapatan
			$data_jurnal[] = array(
				'company_id' => $this->company_id,
				'source' => 'tiket:noreg:' . $no_register,
				'ket' => 'Tiket ' . $no_register . ' ' . $value['airlines_name'] . ' ' . $value['code_booking'],
				'akun_debet' => 'airlines:pendapatan:' . $key,
				'akun_kredit' => $akun['kas'],
				'saldo' => $this->text_ops->hide_currency($refund[$value['id']]),
				'periode_id' => $last_periode,
				'input_date' => date('Y-m-d H:i:s'),
				'last_update' => date('Y-m-d H:i:s')
			);
			// penjualan
			$data_jurnal[] = array(
				'company_id' => $this->company_id,
				'source' => 'tiket:noreg:' . $no_register,
				'ket' => 'Tiket ' . $no_register . ' ' . $value['airlines_name'] . ' ' . $value['code_booking'],
				'akun_debet' => $akun['airlines:hpp:' . $value['airlines_id']],
				'akun_kredit' => $akun['airlines:deposit:' . $value['airlines_id']],
				'saldo' => $this->text_ops->hide_currency($refund[$value['id']]),
				'periode_id' => $last_periode,
				'input_date' => date('Y-m-d H:i:s'),
				'last_update' => date('Y-m-d H:i:s')
			);
		}
		return $data_jurnal;
	}

	# refund tiketing process
	function refund_tiketing_prosess()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('tiket_transaction_id', '<b>ID Transaksi Tiket<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_tiket_transaction_id');
		$this->form_validation->set_rules('nama_pelanggan', '<b>Nama Pelanggan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_invoice', '<b>Nomor Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_invoice_tiket');
		# refund
		foreach ($this->input->post('refund') as $key => $val) {
			$this->form_validation->set_rules("refund[" . $key . "]", "Refund", 'trim|xss_clean|min_length[1]');
		}
		# fee
		foreach ($this->input->post('fee') as $key => $val) {
			$this->form_validation->set_rules("fee[" . $key . "]", "Fee", 'trim|xss_clean|min_length[1]');
		}
		# id detail
		foreach ($this->input->post('id') as $key => $val) {
			$this->form_validation->set_rules("id[" . $key . "]", "ID", 'trim|xss_clean|min_length[1]|callback__ck_tiket_transactin_detail_id');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get tiket transaction id
			$tiket_transaction_id = $this->input->post('tiket_transaction_id');
			$ori_refund = $this->input->post('refund');
			$fee = $this->input->post('fee');
			$id = $this->input->post('id');
			$refund = array();
			foreach ($ori_refund as $key => $value) {
				if ($value != '' or $this->text_ops->hide_currency($value) != 0) {
					$refund[$key] = $value;
				}
			}
			# count refund
			if (count($refund) > 0) {
				$total_pembayaran = $this->model_trans_tiket->get_total_pembayaran_tiket($tiket_transaction_id); // get total pembayaran made
				$total_refund = 0;
				$total_fee = 0;
				foreach ($refund as $key => $value) :  $total_refund = $total_refund + $this->text_ops->hide_currency($value);
				endforeach; // get total refund
				foreach ($fee as $key => $value) : $total_fee = $total_fee + $this->text_ops->hide_currency($value);
				endforeach; // get total fee
				if (($total_refund + $total_fee) <= $total_pembayaran) {
					# get info refund
					$info_refund = $this->model_trans_tiket->get_info_detail_tiket($tiket_transaction_id);
					$tiket_refund = array();
					$list_id = array();
					foreach ($refund as $key => $value) {
						$list_id[] = $key; // get list id refund for delete in detail table
						$tiket_refund[$key] = $info_refund[$key]; // get info of tiket refund
						$total_harga_tiket = $info_refund[$key]['pax'] * $info_refund[$key]['costumer_price']; // define total harga tiket refund
						$refund_tiket = $this->text_ops->hide_currency($value) + $this->text_ops->hide_currency($fee[$key]); // define sum of refund and fee
						if ($refund_tiket > $total_harga_tiket) {
							$error = 1;
							$error_msg = 'Biaya refund ditambah fee tidak boleh lebih besar dari harga tiket';
						}
					}
					$total_transaksi = 0;
					foreach ($info_refund as $key => $value) {
						if (!array_key_exists($key, $refund)) {
							$total_transaksi = $total_transaksi + ($value['pax'] * $value['costumer_price']); // define new total transaction after refund
						}
					}
					# filter
					if ($error == 0) {
						# get receiver
						if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
							$receiver = "Administrator";
						} else {
							$receiver = $this->session->userdata($this->config->item('apps_name'))['fullname'];
						}
						# data tiket transaction refund
						$data_tiket_transaction_refund = array();
						$airlines_id = array();
						foreach ($tiket_refund  as $key => $value) {
							$airlines_id[] = $value['airlines_id']; // define airline id for define param to get akun in akun secondary table
							// define tiket transaction refund for tiket transaction refund table
							$data_tiket_transaction_refund[] = array(
								'tiket_transaction_id' => $tiket_transaction_id,
								'invoice' => $this->input->post('no_invoice'),
								'company_id' => $this->company_id,
								'pax' => $value['pax'],
								'code_booking' => $value['code_booking'],
								'airlines_name' => $value['airlines_name'],
								'departure_date' => $value['departure_date'],
								'travel_price' => $value['travel_price'],
								'costumer_price' => $value['costumer_price'],
								'refund' => $this->text_ops->hide_currency($refund[$key]),
								'fee' => $this->text_ops->hide_currency($fee[$key]),
								'receiver' => $receiver,
								'input_date' => date('Y-m-d H:i:s')
							);
						}
						// hitung ulang total transaksi di tiket transaction
						$data_transaksi = array();
						$data_transaksi['total_transaksi'] = $total_transaksi; // set new total transaction in array
						// define data history transaction in this refund transaction
						$data_history = array(); //insert
						$data_history['company_id'] = $this->company_id;
						$data_history['tiket_transaction_id'] = $tiket_transaction_id;
						$data_history['costumer_name'] = $this->input->post('nama_pelanggan');
						$data_history['costumer_identity'] = $this->input->post('nomor_identitas');
						$data_history['invoice'] = $this->input->post('no_invoice');
						$data_history['biaya'] = $total_refund + $total_fee;
						$data_history['ket'] = 'refund';
						$data_history['receiver'] = $receiver;
						$data_history['input_date'] = date('Y-m-d H:i:s');
						$data_history['last_update'] = date('Y-m-d H:i:s');
						// nomor register
						$no_register = $this->model_trans_tiket->get_no_register($tiket_transaction_id); // get nomor register
						// define data jurnal array
						$data_jurnal = $this->_jurnal_refund($no_register, $tiket_refund, $refund,  $airlines_id);
						# insert data to database
						if (!$this->model_trans_tiket_cud->insert_refund($tiket_transaction_id, $data_transaksi, $list_id, $data_history, $data_tiket_transaction_refund, $data_jurnal)) :
							$error = 1;
							$error_msg = 'Proses refund tiket gagal dilakukan.';
						endif;
					}
				} else {
					$error = 1;
					$error_msg = 'Total refund ditambah total fee adalah '. $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' ' . ($total_refund + $total_fee) . ' tidak boleh lebih dari total pembayaran yaitu : ' . $total_pembayaran;
				}
			} else {
				$error = 1;
				$error_msg = 'Anda wajib menyertakan tiket yang direfund minimal 1 codebooking';
			}
			# filter
			if ($error == 0) {
				# generated printing session
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'refund_trans_tiket',
					'invoice' => $this->input->post('no_invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info detail transaksi tiket berhasil ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// generated session
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
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
