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
		$kostumer = $this->model_trans_paket_la->kostumer_type_la();
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
				'kostumer' => $kostumer,
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
		if($harga == '' ){
			$this->form_validation->set_message('_ck_harga_not_null', 'Harga tidak boleh NOL.');
			return FALSE;
		}else{
			$price = $this->text_ops->hide_currency($harga);
			if ($price > 0) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_harga_not_null', 'Harga tidak boleh NOL.');
				return FALSE;
			}	
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

	function _ck_costumer_id($id){
		if( $this->model_trans_paket_la->check_kostumer_id($id) ) {
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_costumer_id', 'Kostumer id tidak ditemukan.');
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
		$this->form_validation->set_rules('kostumer_paket_la', '<b>Kostumer ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_costumer_id');
		$this->form_validation->set_rules('diskon', '<b>Diskon<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jamaah', '<b>Jumlah Jamaah<b>', 'trim|required|xss_clean|numeric|min_length[1]');
		$this->form_validation->set_rules('tanggal_keberangkatan', '<b>Tanggal Keberangakatan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_kepulangan', '<b>Tanggal Kepulangan<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// jumlah jamaah
			$jumlah_jamaah = $this->input->post('jamaah');
			$diskon = $this->input->post('diskon') != '' ? $this->text_ops->hide_currency($this->input->post('diskon')) : 0 ;
			$tanggal_keberangkatan = $this->input->post('tanggal_keberangkatan');
			$tanggal_kepulangan = $this->input->post('tanggal_kepulangan');
			$kostumer_id = $this->input->post('kostumer_paket_la');
			# get data
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['costumer_id'] = $kostumer_id;
			$data['discount'] = $diskon;
			$data['departure_date'] = $this->input->post('tanggal_keberangkatan');
			$data['arrival_date'] = $this->input->post('tanggal_kepulangan');
			$data['jamaah'] = $this->input->post('jamaah');
			$data['last_update'] = date('Y-m-d');
			# filter process
			if ( $this->input->post('paket_la_id') ) {
				if ( ! $this->model_trans_paket_la_cud->update_paket_la( $this->input->post('paket_la_id'), $data ) ) {
					$error = 1;
					$error_msg = 'Proses update paket la gagal dilakukan';
				}
			} else {
				$data['input_date'] = date('Y-m-d');
				$data['register_number'] = $this->input->post('no_register');
				if (!$this->model_trans_paket_la_cud->insert_paket_la($data)) {
					$error = 1;
					$error_msg = 'Proses insert paket la gagal dilakukan';
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
					'kostumer' => $data['kostumer'],
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
			// get id
			$id = $this->input->post('id');
			// get info
			$info = $this->model_trans_paket_la->get_info_paket_la($id);
			# filter feedBack
			if ($this->model_trans_paket_la_cud->delete_paket_la($this->input->post('id'), $info)) {
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

	function _ck_id_detail_item_paket_la( $id ){
		if( $this->model_trans_paket_la->check_id_detail_item_paket_la($id) > 0 ) {
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_id_detail_item_paket_la', 'ID Detail Item tidak terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	function delete_detail_item_paket_la(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Detail Item Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_detail_item_paket_la');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// id
			$id = $this->input->post('id');
			// info
			$info = $this->model_trans_paket_la->get_info_id_transaksi_paket_la_by_detail_item_id( $id );
			// num 
			$num_not_id = $this->model_trans_paket_la->check_id_detail_item_paket_la_not_id($info['paket_la_fasilitas_transaction_id'], $id);

			// delete
			$delete = $this->model_trans_paket_la_cud->delete_detail_item_paket_la($id, $num_not_id, $info);
			// jika delete process berhasil
			if( $delete ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses Delete Item Detail Paket LA Berhasil Dilakukan',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses Delete Item Detail Paket LA Gagal Dilakukan',
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

	function _ck_id_paket_la_transaction($id){
		if( $this->model_trans_paket_la->check_id_paket_la($id) ) {
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_id_paket_la_transaction', 'ID Paket LA Transaksi tidak terdaftar dipangkalan data.');
			return FALSE;
		}
	}


	function _ck_day(){

	}

	function add_update_new_item(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Paket La Transaction<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_paket_la_transaction');
		if ($this->input->post('deskripsi')) {
			foreach ($this->input->post('deskripsi') as $key => $value) {
				$this->form_validation->set_rules("deskripsi[" . $key . "]", "Deskripsi Item", 'trim|required|xss_clean|min_length[1]');
			}
		}
		if ($this->input->post('pax')) {
			foreach ($this->input->post('pax') as $key => $value) {
				$this->form_validation->set_rules("pax[" . $key . "]", "Pax Item", 'trim|required|xss_clean|min_length[1]');
			}
		}
		if ($this->input->post('price')) {
			foreach ($this->input->post('price') as $key => $value) {
				$this->form_validation->set_rules("price[" . $key . "]", "Harga", 'trim|required|xss_clean|min_length[1]|callback__ck_harga_not_null');
			}
		}
		if ($this->input->post('check_in')) {
			foreach ($this->input->post('check_in') as $key => $value) {
				$this->form_validation->set_rules("check_in[" . $key . "]", "Date Check In", 'trim|xss_clean|min_length[1]');
			}
		}
		if ($this->input->post('check_out')) {
			foreach ($this->input->post('check_out') as $key => $value) {
				$this->form_validation->set_rules("check_out[" . $key . "]", "Date Check Out", 'trim|xss_clean|min_length[1]');
			}
		}
		if ($this->input->post('day')) {
			foreach ($this->input->post('day') as $key => $value) {
				$this->form_validation->set_rules("day[" . $key . "]", "Day", 'trim|xss_clean|min_length[1]');
			}
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// post		
			$id = $this->input->post('id');
			// generate invoice 
			$invoice = $this->random_code_ops->generate_invoice_item_paket_la();
			// get total
			$total = $this->model_trans_paket_la->get_total_price($id);
			# array post
			$deskripsi = $this->input->post('deskripsi');
			$pax = $this->input->post('pax');
			$price = $this->input->post('price');
			$check_in = $this->input->post('check_in');
			$check_out = $this->input->post('check_out');
			$day = $this->input->post('day');
			// receive data item
			$data_item = array();
			$total_price = 0;
			foreach ($deskripsi as $key => $value) {
				$temp_data = array();
				$temp_data['company_id'] = $this->company_id;
				$temp_data['description'] = $value;
				$temp_data['check_in'] = $check_in[$key];
				$temp_data['check_out'] = $check_out[$key];
				$temp_data['day'] = $day[$key];
				$temp_data['pax'] = $pax[$key];
				$temp_data['price'] = $price[$key] == '' ? 0 : $this->text_ops->hide_currency($price[$key]);
				$temp_data['input_date'] = date('Y-m-d');
				// receive data
				$data_item[] = $temp_data;

				$local_total = $day[$key] != '' ? ( $day[$key] * $pax[$key] * $this->text_ops->hide_currency($price[$key]) ) : ($pax[$key] * $this->text_ops->hide_currency($price[$key]));
				$total_price = $total_price + $local_total;
			}
			// receive data fasilitas
			$data_fasilitas = array();
			$data_fasilitas['company_id'] = $this->company_id;
			$data_fasilitas['paket_la_transaction_id'] = $id;
			$data_fasilitas['invoice'] = $invoice;
			$data_fasilitas['total_price'] = $total_price;
			$data_fasilitas['input_date'] = date('Y-m-d');
			$data_fasilitas['last_update'] = date('Y-m-d');
			// total paket transaction la
			$total_paket_transaction_la = $total + $total_price;
			// input process
			if( ! $this->model_trans_paket_la_cud->add_new_item($id, $data_fasilitas, $data_item, $total_paket_transaction_la) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses penambahan item Paket LA Gagal Dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penambahan item Paket LA Berhasil Dilakukan.',
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


	function _ck_id_fasilitas_trans_paket_la($id) {
		if ($this->model_trans_paket_la->check_fasilitas_id_trans_paket_la($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_id_fasilitas_trans_paket_la', 'ID Fasilitas Transaksi Paket LA Tidak Ditemukan');
			return FALSE;
		}
	}

	function cetak_kwitansi_detail_item_paket_la() {
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Fasilitas Trans Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_trans_paket_la');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// create session priting here
			$this->session->set_userdata(array('cetak_invoice' => array(
				'type' => 'kwitansi_detail_item_paket_la',
				'id' => $this->input->post('id')
			)));
			// feedBack
			$return = array(
				'error'	=> false,
				'error_msg' => 'Proses cetak kwitansi berhasil dilakukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
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
