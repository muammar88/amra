<?php

/**
 *  -----------------------
 *	Trans paket la Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_paket_la extends CI_Controller
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
		$this->load->model('Model_trans_paket_la', 'model_trans_paket_la');
		# model fasilitas cud
		$this->load->model('Model_trans_paket_la_cud', 'model_trans_paket_la_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function _ck_paket_la_id( $id ){
		if( ! $this->model_trans_paket_la->check_paket_la_id($id) ){
			$this->form_validation->set_message('_ck_paket_la_id', 'Paket La ID tidak ditemukan.');
			return FALSE;
		}else{	
			return TRUE;
		}
	}

	// close paket la
	function close_paket_la(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Id Paket La<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_paket_la_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$id = $this->input->post('id');
			# verify close trans paket la
			$verify = $this->model_trans_paket_la->verify_trans_paket_la( $id ) ;
			

			// filter
			if( $verify['status_paket_la'] != 'close' ) {
				# check
				if ( $verify['status'] ) {
					# get info kas transaction paket la
					$datas = $this->model_trans_paket_la->get_info_kas_transaksi_paket_la($id);
					// get last periode
					$last_periode = $this->model_trans_paket_la->get_last_periode();
					// jurnal
					$data = array();
					$data['jurnal']['company_id']  = $this->company_id;
					$data['jurnal']['source']  = 'paket_la_id:' . $id;
					$data['jurnal']['ref']  = ' Pendapatan Paket La ID dengan ID ' . $id;
					$data['jurnal']['ket']  = ' Pendapatan Paket La ID dengan ID ' . $id;
					$data['jurnal']['akun_debet']  = '11010';
					$data['jurnal']['akun_kredit']  = '42000';
					$data['jurnal']['saldo']  = $datas['keuntungan'];
					$data['jurnal']['periode_id']  = $last_periode;
					$data['jurnal']['input_date']  = date('Y-m-d H:i:s');
					$data['jurnal']['last_update']  = date('Y-m-d H:i:s');
					// paket la transaction
					$data['paket_la_transaction']['status'] = 'close';
					$data['paket_la_transaction']['last_update'] = date('Y-m-d');
					// close paket la
					if( ! $this->model_trans_paket_la_cud->close_paket_la( $data, $id ) ){
						$return = array(
							'error'	=> true,
							'error_msg' => 'Proses tutup paket la gagal dilakukan.',
							$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
						);
					}else{
						$return = array(
							'error'	=> false,
							'error_msg' => 'Proses tutup paket la berhasil dilakukan.',
							$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
						);
					}
				} else {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Paket La ini tidak dapat ditutup, karena masih terdapat sisa pembayaran.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Paket La ini sudah ditutup.',
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

	function daftar_trans_paket_la()
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
			$total = $this->model_trans_paket_la->get_total_daftar_transaksi_paket_la($search);
			$list = $this->model_trans_paket_la->get_index_daftar_transaksi_paket_la($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi paket la tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi paket la berhasil ditemukan.',
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

	function get_info_trans_paket_la()
	{
		$error = 0;
		$error_msg = '';
		# get nomor register
		$nomor_register = $this->random_code_ops->generated_nomor_register_trans_paket_la();
		if ($nomor_register == '') {
			$error = 1;
			$error_msg = 'Nomor register gagal di generated.';
		}
		# get paket type la
		$paket_type = $this->model_trans_paket_la->paket_type_la();
		if (count($paket_type) == 0) {
			$error = 1;
			$error_msg = 'Tipe paket la tidak ditemukan.';
		}
		# list fasilitas paket la
		$fasilitas_paket_la = $this->model_trans_paket_la->fasilitas_paket_la();
		if (count($fasilitas_paket_la) == 0) {
			$error = 1;
			$error_msg = 'Fasilitas paket la tidak ditemukan.';
		}
		# filter
		if ($error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => $error_msg,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info tambah paket la berhasil ditemukan.',
				'nomor_register' => $nomor_register,
				'paket_type' => $paket_type,
				'fasilitas' => $fasilitas_paket_la,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_id_trans_paket_la_exist()
	{
		if ($this->input->post('paket_la_id')) {
			if ($this->model_trans_paket_la->check_id_trans_paket_la_exist($this->input->post('paket_la_id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_trans_paket_la_exist', 'Id trans paket la tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_no_register_exist()
	{
		$id = '';
		if ($this->input->post('paket_la_id')) {
			$id = $this->input->post('paket_la_id');
		}
		if ($this->model_trans_paket_la->checking_no_register($this->input->post('no_register'), $id)) {
			$this->form_validation->set_message('_ck_no_register_exist', 'Nomor register tidak ditemukan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# check harga not null
	function _ck_harga_not_null($harga)
	{
		$price = $this->text_ops->hide_currency($harga);
		if ($price > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_not_null', 'Harga tidak boleh NOL.');
			return FALSE;
		}
	}

	function _ck_jenis_paket($jenis_paket)
	{
		if ($this->model_trans_paket_la->check_jenis_paket_id($jenis_paket)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_jenis_paket', 'Jenis paket id tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_jenis_fasilitas_id($id)
	{
		if ($this->model_trans_paket_la->check_jenis_fasilitas_id($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_jenis_fasilitas_id', 'Fasilitas id tidak ditemukan.');
			return FALSE;
		}
	}

	# proses addupdate trans paket la
	function proses_addupdate_trans_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_la_id', '<b>Id Trans Paket LA<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		if ( ! $this->input->post('paket_la_id') ) {
			$this->form_validation->set_rules('no_register', '<b>Nomor Register Paket LA<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_no_register_exist');
		}
		$this->form_validation->set_rules('nama_klien', '<b>Nama Klien<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_hp', '<b>Nomor HP Klien<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jenis_paket', '<b>Jenis Paket<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_jenis_paket');
		$this->form_validation->set_rules('diskon', '<b>Diskon<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jamaah', '<b>Jumlah Jamaah<b>', 'trim|required|xss_clean|numeric|min_length[1]');
		$this->form_validation->set_rules('tanggal_keberangkatan', '<b>Tanggal Keberangakatan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_kepulangan', '<b>Tanggal Kepulangan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_klien', '<b>Alamat Klien<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('description', '<b>Deskripsi<b>', 'trim|required|xss_clean|min_length[1]');
		# jenis_fasilitas
		foreach ( $this->input->post('jenis_fasilitas') as $key => $value ) {
			$this->form_validation->set_rules("jenis_fasilitas[" . $key . "]", "Jenis Fasilitas", 'trim|required|numeric|xss_clean|min_length[1]|callback__ck_jenis_fasilitas_id');
		}
		# pax
		foreach ( $this->input->post('pax') as $key => $value ) {
			$this->form_validation->set_rules("pax[" . $key . "]", "Pax Fasilitas", 'trim|required|numeric|xss_clean|min_length[1]');
		}
		# harga
		foreach ( $this->input->post('harga') as $key => $value ) {
			$this->form_validation->set_rules("harga[" . $key . "]", "Harga Fasilitas", 'trim|required|xss_clean|min_length[1]|callback__ck_harga_not_null');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get fasilitas
			$fasilitas = $this->model_trans_paket_la->get_name_fasilitas();
			# get post
			$jenis_fasilitas = $this->input->post('jenis_fasilitas');
			$jumlah_jamaah = $this->input->post('jamaah');
			$pax = $this->input->post('pax');
			$harga = $this->input->post('harga');
			$diskon = $this->text_ops->hide_currency($this->input->post('diskon'));
			# calculate
			$daftar_fasilitas = array();
			if ($this->input->post('jenis_paket') != 0) {
				$daftar_fasilitas['tipe_paket_la_id'] = $this->input->post('jenis_paket');
			}
			$list_fasilitas = array();
			$total = 0;
			foreach ($jenis_fasilitas as $key => $value) {
				$list_fasilitas[] = array(
					'id' 	  => $value,
					'name'  => $fasilitas[$value],
					'pax'   => $pax[$key],
					'harga' => trim($this->text_ops->hide_currency($harga[$key]))
				);
				$total = $total + ($pax[$key] * $this->text_ops->hide_currency($harga[$key]));
			}
			$daftar_fasilitas['list_fasilitas'] = $list_fasilitas;
			$total_price = ($total * $jumlah_jamaah) - $diskon;
			# get data
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['client_name'] = $this->input->post('nama_klien');
			$data['client_hp_number'] = $this->input->post('nomor_hp');
			$data['client_address'] = $this->input->post('alamat_klien');
			$data['description'] = $this->input->post('description');
			$data['facilities'] = serialize($daftar_fasilitas);
			$data['discount'] = $this->text_ops->hide_currency($this->input->post('diskon'));
			$data['total_price'] = $total_price;
			$data['departure_date'] = $this->input->post('tanggal_keberangkatan');
			$data['arrival_date'] = $this->input->post('tanggal_kepulangan');
			$data['jamaah'] = $this->input->post('jamaah');
			$data['last_update'] = date('Y-m-d');
			# filter process
			if ( $this->input->post('paket_la_id') ) {
				if ( ! $this->model_trans_paket_la_cud->update_paket_la( $this->input->post('paket_la_id'), $data ) ) {
					$error = 1;
					$error_msg = 'Proses update paket la gagal dilakukan';
				}else{
					$this->session->set_userdata(array('cetak_invoice' => array(
						'type' => 'cetak_kwitansi_pertama_paket_la',
						'id' => $this->input->post('paket_la_id')
					)));
				}
			} else {
				$data['input_date'] = date('Y-m-d');
				$data['register_number'] = $this->input->post('no_register');
				if (!$this->model_trans_paket_la_cud->insert_paket_la($data)) {
					$error = 1;
					$error_msg = 'Proses insert paket la gagal dilakukan';
				}else{
					$paket_la_id = $this->model_trans_paket_la->get_paket_la_id_by_register_number($this->input->post('no_register'));
					$this->session->set_userdata(array('cetak_invoice' => array(
						'type' => 'cetak_kwitansi_pertama_paket_la',
						'id' => $paket_la_id
					)));
				}
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi paket la berhasil disimpan.',
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

	# info edit paket la
	function info_edit_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get data
			$data = $this->model_trans_paket_la->get_data_trans_paket_la($this->input->post('id'));
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi paket la berhasil ditemukan.',
					'value' => $data['value'],
					'tipe_paket_la' => $data['list_tipe_paket_la'],
					'fasilitas' => $data['list_fasilitas'],
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

	# get info pembayaran
	function get_info_pembayaran()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get data
			$data = $this->model_trans_paket_la->get_data_history_trans_paket_la($this->input->post('id'));

			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi paket la berhasil ditemukan.',
					'data' => $data,
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

	# chekc invoice paket la history
	function _ck_invoice_paket_la_exist($invoice)
	{
		if ($this->model_trans_paket_la->check_exist_invoice_paket_la($invoice)) {
			$this->form_validation->set_message('_ck_invoice_paket_la_exist', 'Nomor invoice sudah terdaftar dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# proses pembayaran trans paket la
	function proses_addupdate_pembayaran_trans_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_paket_la_exist');
		$this->form_validation->set_rules('bayar', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_harga_not_null');
		$this->form_validation->set_rules('nama_penyetor', '<b>Nama Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_hp_penyetor', '<b>Nomor HP Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_penyetor', '<b>Alamat Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get data
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['paket_la_transaction_id	'] = $this->input->post('id');
			$data['invoice'] = $this->input->post('invoice');
			$data['paid'] = $this->text_ops->hide_currency($this->input->post('bayar'));
			$data['status'] = 'payment';
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['deposit_name'] = $this->input->post('nama_penyetor');
			$data['deposit_hp_number'] = $this->input->post('no_hp_penyetor');
			$data['deposit_address'] = $this->input->post('alamat_penyetor');
			$data['input_date'] = date('Y-m-d');
			$data['last_update'] = date('Y-m-d');
			# filter feedBack
			if ($this->model_trans_paket_la_cud->insert_pembayaran($data)) {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'pembayaran_paket_la',
					'invoice' => $this->input->post('invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data pembayaran transaksi paket la berhasil disimpan.',
					'data' => $data,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data pembayaran transaksi paket la gagal disimpan',
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

	# ID transaksi paket la
	function info_refund_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# generated invoice
			$invoice = $this->random_code_ops->generated_invoice_history_paket_la();
			# get data
			$wasPaid = $this->model_trans_paket_la->get_total_pembayaran($this->input->post('id'));
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data pembayaran sudah dibayarkan ditemukan.',
					'wasPaid' => $wasPaid,
					'invoice' => $invoice,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data pembayaran sudah dibayar gagal ditemukan',
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

	# proses refund trans paket la
	function proses_refund_trans_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_paket_la_exist');
		$this->form_validation->set_rules('refund', '<b>Refund Trans Paket LA<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_harga_not_null');
		$this->form_validation->set_rules('nama_penyetor', '<b>Nama Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_hp_penyetor', '<b>Nomor HP Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_penyetor', '<b>Alamat Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// # generated invoice
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['paket_la_transaction_id'] = $this->input->post('id');
			$data['invoice'] = $this->input->post('invoice');
			$data['paid'] = $this->text_ops->hide_currency($this->input->post('refund'));
			$data['status'] = 'refund';
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['deposit_name'] = $this->input->post('nama_penyetor');
			$data['deposit_hp_number'] = $this->input->post('no_hp_penyetor');
			$data['deposit_address'] = $this->input->post('alamat_penyetor');
			$data['input_date'] = date('Y-m-d');
			$data['last_update'] = date('Y-m-d');
			# filter feedBack
			if ($this->model_trans_paket_la_cud->insert_refund($data)) {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'refund_paket_la',
					'invoice' => $this->input->post('invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data refund berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data refund gagal disimpan',
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

	# get info kas transaksi
	function get_info_kas_transaksi()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info kas transaction paket la
			$data = $this->model_trans_paket_la->get_info_kas_transaksi_paket_la($this->input->post('id'));
			# filter feedBack
			if (count($data) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data refund berhasil disimpan.',
					'data' => $data,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data refund gagal disimpan',
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

	function _ck_kas_trans_paket_la($id)
	{
		if ($this->input->post('ket') == 'add' and $this->input->post('action') == 'insert') {
			return TRUE;
		} else {
			if ($this->model_trans_paket_la->check_kas_trans_paket_la($id, $this->input->post('ket'), $this->input->post('action'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_kas_trans_paket_la', 'ID tidak ditemukan.');
				return FALSE;
			}
		}
	}

	# update kas transaksi paket la
	function proses_addupdate_kas_transaksi_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_la_id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		$this->form_validation->set_rules('ket', '<b>Keterangan<b>', 'trim|required|xss_clean|min_length[1]|in_list[fasilitas,add]');
		$this->form_validation->set_rules('action', '<b>Aksi<b>', 'trim|required|xss_clean|min_length[1]|in_list[insert,update]');
		if ($this->input->post('ket') != 'add' and $this->input->post('action') != 'insert') {
			$this->form_validation->set_rules('id', '<b>Id<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_kas_trans_paket_la');
		}
		$this->form_validation->set_rules('harga', '<b>Harga<b>', 'trim|required|xss_clean|min_length[1]');
		if ($this->input->post('ket') == 'add') {
			$this->form_validation->set_rules('uraian', '<b>Uraian<b>', 'trim|required|xss_clean|min_length[1]');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info kas transaction paket la
			$data = array();
			$data['uraian'] = $this->input->post('uraian');
			$data['price'] = $this->text_ops->hide_currency($this->input->post('harga'));
			if ($this->input->post('ket') == 'fasilitas') {
				if ($this->input->post('action') == 'update') {
					if (!$this->model_trans_paket_la_cud->update_kas_trans_fasilitas($data, $this->input->post('id'), $this->input->post('ket'))) {
						$error = 1;
						$error_msg = 'Proses update kas transaksi gagal dilakukan.';
					}
				} elseif ($this->input->post('action') == 'insert') {
					$data['ket'] = $this->input->post('ket');
					$data['company_id'] = $this->company_id;
					$data['paket_la_transaction_id'] = $this->input->post('paket_la_id');
					$data['fasilitas_la_id'] = $this->input->post('id');
					if (!$this->model_trans_paket_la_cud->insert_kas_trans_fasilitas($data)) {
						$error = 1;
						$error_msg = 'Proses insert kas transaksi gagal dilakukan.';
					}
				}
			} else {
				if ($this->input->post('action') == 'update') {
					if (!$this->model_trans_paket_la_cud->update_kas_transaksi($data, $this->input->post('id'))) {
						$error = 1;
						$error_msg = 'Proses update data kas transaksi gagal dilakukan.';
					}
				} elseif ($this->input->post('action') == 'insert') {
					$data['ket'] = $this->input->post('ket');
					$data['company_id'] = $this->company_id;
					$data['paket_la_transaction_id'] = $this->input->post('paket_la_id');
					$data['fasilitas_la_id'] = 0;
					if (!$this->model_trans_paket_la_cud->insert_kas_transaksi($data)) {
						$error = 1;
						$error_msg = 'Proses insert kas transaksi gagal dilakukan.';
					}
				}
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data kas transaksi berhasil disimpan.',
					'data' => $this->model_trans_paket_la->get_info_kas_transaksi_paket_la($this->input->post('paket_la_id')),
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

	function _ck_delete_id_kas_trans_paket_la($id)
	{
		if ($this->model_trans_paket_la->check_kas_trans_paket_la($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_delete_id_kas_trans_paket_la', 'ID tidak ditemukan.');
			return FALSE;
		}
	}

	function delete_kas_transaksi_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_la_id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		$this->form_validation->set_rules('id', '<b>Id<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_delete_id_kas_trans_paket_la');

		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_trans_paket_la_cud->delete_kas_transaksi_paket_la($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data kas transaksi berhasil dihapus.',
					'data' => $this->model_trans_paket_la->get_info_kas_transaksi_paket_la($this->input->post('paket_la_id')),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data kas transaksi gagal dihapus.',
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

	function delete_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_trans_paket_la_cud->delete_paket_la($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data paket la berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data paket la gagal dihapus.',
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

	# last kwitansi ash
	function lastKwitansiPembayaran()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			$feedBack = $this->model_trans_paket_la->getLastInfoKwitansiPembayaran($this->input->post('id'));
			if ($feedBack != '') {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'pembayaran_paket_la',
					'invoice' => $feedBack
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Info cetak kwitansi paket la terakhir berhasil dibentuk.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info cetak kwitansi paket la terakhir gagal dibentuk.',
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


	// check invoice transaction paket la
	function _ck_invoice_trans_paket_la_exist( $invoice ){
		if( ! $this->model_trans_paket_la->check_invoice_exist( $invoice ) ){
			$this->form_validation->set_message('_ck_invoice_trans_paket_la_exist', 'Nomor invoice tidak terdaftar dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}	
	}


	function cetak_kwitansi_paket_la(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('invoice', '<b>Invoice Trans Paket LA<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			$invoice = $this->input->post('invoice');
			// create session priting here
			if ($error == 0) {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'pembayaran_paket_la',
					'invoice' => $this->input->post('invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Info cetak kwitansi paket la berhasil dibentuk.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info cetak kwitansi paket la gagal dibentuk.',
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

	// cetak kwitansi pertama paket la
	function cetak_kwitansi_pertama_paket_la(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_trans_paket_la_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// create session priting here
			if ($error == 0) {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_kwitansi_pertama_paket_la',
					'id' => $this->input->post('id')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Info cetak kwitansi paket la berhasil dibentuk.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info cetak kwitansi paket la gagal dibentuk.',
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
