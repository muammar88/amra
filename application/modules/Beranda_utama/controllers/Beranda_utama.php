<?php

/**
*  -----------------------
*	Slider Controller
*	Created by Muammar Kadafi
*  -----------------------
*/

defined('BASEPATH') or exit('No direct script access allowed');

class Beranda_utama extends CI_Controller
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
		$this->load->model('Model_beranda_utama', 'model_beranda_utama');
		# model beranda utama cud
		$this->load->model('Model_beranda_utama_cud', 'model_beranda_utama_cud');
		# model general
		$this->load->model('ModelRead/Model_general', 'model_general');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# midtrans
		$params = array('server_key' => $this->config->item('midtrans_server_key'), 'production' => true);
		$this->load->library('midtrans');
		$this->midtrans->config($params);
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function _ck_saldo($saldo){
		$saldo = explode('.', $saldo)[0];
		// _ck_saldo
		if($this->text_ops->hide_currency($saldo) > 0 ){
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_saldo', 'Untuk melanjutkan, Saldo tidak boleh nol.');
			return FALSE;
		}
	}

	function get_token(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('saldo',	'<b>Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_saldo');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			// Required
			$transaction_details = array(
				'order_id' => $this->model_beranda_utama->gen_order_id(),
				'gross_amount' => $this->text_ops->hide_currency( $this->input->post('saldo') ), // no decimal allowed for creditcard
			);
			// Costumer Detail
			$customer_details = array(
				'first_name'    => $this->session->userdata($this->config->item('apps_name'))['company_code'],
				'last_name'     => $this->session->userdata($this->config->item('apps_name'))['company_name'],
				'email'         => $this->session->userdata($this->config->item('apps_name'))['email']
			);
			// Data yang akan dikirim untuk request redirect_url.
			$credit_card['secure'] = true;
			// time
			$time = time();
			$custom_expiry = array(
				'start_time' => date("Y-m-d H:i:s O",$time),
				'unit' => 'hour',
				'duration'  => 2
			);
			# data transaction
			$transaction_data = array(
				'transaction_details'=> $transaction_details,
				'customer_details'   => $customer_details,
				'credit_card'        => $credit_card,
				'expiry'             => $custom_expiry
			);
			# snap
			$snapToken = $this->midtrans->getSnapToken($transaction_data);
			# filter error
			if ($snapToken == '' ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Token gagal di generated.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Token berhasil digenerated.',
					'token' => $snapToken,
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

	# save log
	function save_process_log_saldo(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('status_code', '<b>Status Code<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('status_message', '<b>Status Message<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('transaction_id', '<b>Transaksi ID<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('order_id', '<b>Order ID<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('gross_amount', '<b>Gross Amount<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_saldo');
		$this->form_validation->set_rules('payment_type', '<b>Payment Type<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('transaction_time',	'<b>Transaction Time<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('transaction_status', '<b>Transaction Status<b>', 'trim|required|xss_clean|min_length[1]|in_list[pending,capture,settlement,deny,cancel,expire,failure,refund,chargeback,partial_refund,partial_changeback,authorize]');
		$this->form_validation->set_rules('fraud_status', '<b>Fraud Status<b>', 'trim|required|xss_clean|min_length[1]|in_list[accept,deny,challenge]');
		$this->form_validation->set_rules('bill_key', '<b>Bill Key<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('biller_code', '<b>Biller Code<b>', 'trim|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pdf_url',	'<b>Pdf URL<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('finish_redirect_url',	'<b>Finish Redirect URL<b>', 'trim|required|xss_clean|min_length[1]');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			# gros amount
			$gross_amount = explode('.', $this->input->post('gross_amount'));
			# data
			$data = array();
			$data['status_code'] = $this->input->post('status_code');
			$data['transaction_type'] = 'deposit_saldo';
			$data['status_message'] = $this->input->post('status_message');
			$data['transaction_id'] = $this->input->post('transaction_id');
			$data['order_id'] = $this->input->post('order_id');
			$data['gross_amount'] = $this->input->post('gross_amount');
			$data['payment_type'] = $this->input->post('payment_type');
			$data['transaction_time'] = $this->input->post('transaction_time');
			$data['transaction_status'] = $this->input->post('transaction_status');
			$data['fraud_status'] = $this->input->post('fraud_status');
			$data['bill_key'] = $this->input->post('bill_key');
			$data['biller_code'] = $this->input->post('biller_code');
			$data['pdf_url'] = $this->input->post('pdf_url');
			$data['finish_redirect_url'] = $this->input->post('finish_redirect_url');
			# insert process
			if( ! $this->model_beranda_utama_cud->insert_saldo_payment_log( $data ) ) {
				$error = 1;
				$error_msg = 'Proses penyimpanan log pembayaran saldo gagal dilakukan.';
			}
			# filte process
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penyimpanan log pembayaran saldo berhasil dilakukan.',
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

	function get_info_beranda_utama(){
		$error = 0;
		$error_msg = '';
		// # list akun
		// $list_akun = $this->model_buku_besar->get_list_akun();
		// if (count($list_akun) == 0) {
		// 	$error = 1;
		// 	$error_msg = 'List akun tidak ditemukan.';
		// }
		// # list periode
		// $list_periode = $this->model_buku_besar->get_list_periode();
		// if (count($list_periode) == 0) {
		// 	$error = 1;
		// 	$error_msg = 'List periode tidak ditemukan.';
		// }

		// $('#saldo').html(e.data.saldo);
		// $('#jamaah_terdaftar').html(e.data.jamaah_terdaftar);
		// $('#paket_berangkat').html(e.data.paket_berangkat);
		// $('#jamaah_berangkat').html(e.data.jamaah_berangkat);
		// $('#tiket_terjual').html(e.data.tiket_terjual);
		$saldo = $this->model_beranda_utama->get_saldo();
		$jamaah_terdaftar = $this->model_beranda_utama->get_jamaah();
		$paket_berangkat = $this->model_beranda_utama->paket_berangkat();
		$jamaah_berangkat = 0;
		if( $paket_berangkat['num'] > 0 ) {
			$jamaah_berangkat = $this->model_beranda_utama->jamaah_berangkat($paket_berangkat['list_paket']);
		}
		$tiket_terjual = $this->model_beranda_utama->get_terjual();
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
				'error_msg' => 'Data buku besar berhasil ditemukan.',
				'data' => array('saldo' => $saldo,
				'jamaah_terdaftar' => $jamaah_terdaftar,
				'paket_berangkat' => $paket_berangkat['num'],
				'jamaah_berangkat' => $jamaah_berangkat,
				'tiket_terjual' => $tiket_terjual),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function daftar_jamaah_terdaftar(){
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
			$total 	= $this->model_beranda_utama->get_total_daftar_jamaah_terdaftar($search);
			$list 	= $this->model_beranda_utama->get_index_daftar_jamaah_terdaftar($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar artikel tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar artikel berhasil ditemukan.',
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

	# daftar paket berangkat
	function daftar_paket_berangkat(){
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
			$total 	= $this->model_beranda_utama->get_total_daftar_paket_berangkat($search);
			$list 	= $this->model_beranda_utama->get_index_daftar_paket_berangkat($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar paket yang akan berangkat tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar paket yang akan berangkat berhasil ditemukan.',
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

	//
	function daftar_jamaah_berangkat(){
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
			$total 	= $this->model_beranda_utama->get_total_daftar_jamaah_berangkat($search);
			$list 	= $this->model_beranda_utama->get_index_daftar_jamaah_berangkat($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar jamaah yang akan berangkat tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar jamaah yang akan berangkat berhasil ditemukan.',
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

	# tiket terjual
	function daftar_tiket_terjual(){
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
			$total 	= $this->model_beranda_utama->get_total_daftar_tiket_terjual($search);
			$list 	= $this->model_beranda_utama->get_index_daftar_tiket_terjual($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar tiket terjual tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar tiket terjual berhasil ditemukan.',
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

	# riwayat saldo
	function daftar_riwayat_saldo(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('start_date',	'<b>Mulai Tanggal<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('end_date', '<b>Sampai Tanggal<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			// $search 	= $this->input->post('search');
			$start_date = $this->input->post('start_date');
			$end_date = $this->input->post('end_date');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_beranda_utama->get_total_daftar_riwayat_saldo($start_date, $end_date);
			$list 	= $this->model_beranda_utama->get_index_daftar_riwayat_saldo($perpage, $start_at, $start_date, $end_date);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar riwayat saldo tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar riwayat saldo berhasil ditemukan.',
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

	# daftar headline
	function daftar_headline(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
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
			$total 	= $this->model_beranda_utama->get_total_daftar_headline();
			$list 	= $this->model_beranda_utama->get_index_daftar_headline($perpage, $start_at);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar headline tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar headline berhasil ditemukan.',
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

	# get request deposit member
	function daftar_request_deposit(){
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
			$search = $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ( $this->input->post('pageNumber') ) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total = $this->model_beranda_utama->get_total_daftar_request_deposit($search);
			$list = $this->model_beranda_utama->get_index_daftar_request_deposit($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar request deposit tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar request deposit berhasil ditemukan.',
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

	function _ck_request_member_id($id){
		if( ! $this->model_beranda_utama->check_request_member_id($id) ){
			$this->form_validation->set_message('_ck_request_member_id', 'Request member ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function proses_penolakan_request(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Request ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_request_member_id');
		$this->form_validation->set_rules('alasan_penolakan', '<b>Alasan Penolakan<b>', 'trim|xss_clean|min_length[1]');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			$data = array();
			$data['status_request'] = 'ditolak';
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['approver'] = 'Administrator';
			} else {
				$data['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['status_note'] = $this->input->post('alasan_penolakan');
			$data['last_update'] = date('Y-m-d H:i:s');
			# filter model pengaturan cud
			if ( $this->model_beranda_utama_cud->penolakan_request( $this->input->post('id'), $data ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Penolakan request member berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Penolakan request member gagal dilakukan.',
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

	function approve_request(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Request ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_request_member_id');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			#  retrive id
			$id = $this->input->post('id');
			// echo "masuk";
			# get info request
			$info_request = $this->model_beranda_utama->get_info_request($id);
			# define approver
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$approver = 'Administrator';
			} else {
				$approver = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			# get nomor transaction umrau
			$nomor_transaction_tabungan_umrah = $this->random_code_ops->gen_nomor_transaction();

			$data = array();
			if( $info_request['activity_type'] == 'deposit_paket' ) {
				$data['deposit_transaction'][] = array('member_request_id' => $id,
													   'nomor_transaction' => $nomor_transaction_tabungan_umrah,
													   'personal_id' => $info_request['personal_id'],
													   'company_id' => $this->company_id,
													   'debet' => $info_request['amount'],
													   'kredit' => 0,
													   'approver' => $approver,
													   'transaction_requirement' => 'paket_deposit',
													   'info' => 'Deposit Paket',
													   'input_date' => date('Y-m-d H:i:s'),
													   'last_update' => date('Y-m-d H:i:s'));
				if( $info_request['payment_source'] == 'transfer' ) {
					// akun
               		$data['jurnal'][] = array('company_id' => $this->company_id,
		                                      'source' => 'deposittabungan:notransaction:'.$nomor_transaction_tabungan_umrah,
		                                      'ref' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$nomor_transaction_tabungan_umrah,
		                                      'ket' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$nomor_transaction_tabungan_umrah,
		                                      'akun_debet' => '11010',
		                                      'akun_kredit' => '24000',
		                                      'saldo' => $info_request['amount'],
		                                      'periode_id' => 0,
		                                      'input_date' => date('Y-m-d H:i:s'),
                                       		  'last_update'  => date('Y-m-d H:i:s'));
					# define deposit data
					$data['deposit_transaction'][] = array('member_request_id' => $id,
														   'nomor_transaction' => $this->random_code_ops->gen_nomor_transaction(),
														   'personal_id' => $info_request['personal_id'],
														   'company_id' => $this->company_id,
														   'debet' => $info_request['amount_code'],
														   'kredit' => 0,
														   'approver' => $approver,
														   'transaction_requirement' => 'deposit',
														   'info' => 'Deposit',
														   'input_date' => date('Y-m-d H:i:s'),
														   'last_update' => date('Y-m-d H:i:s'));
				}else{
					// akun
               		$data['jurnal'][] = array('company_id' => $this->company_id,
                                         	  'source' => 'deposittabungan:notransaction:'.$nomor_transaction_tabungan_umrah,
                                         	  'ref' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$nomor_transaction_tabungan_umrah,
                                         	  'ket' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$nomor_transaction_tabungan_umrah,
                                         	  'akun_debet' => '23000',
                                         	  'akun_kredit' => '24000',
                                         	  'saldo' => $info_request['amount'],
                                         	  'periode_id' => 0,
                                         	  'input_date' => date('Y-m-d H:i:s'),
                                         	  'last_update'  => date('Y-m-d H:i:s'));
					# define deposit data
					$data['deposit_transaction'][] = array('member_request_id' => $id,
														   'nomor_transaction' => $this->random_code_ops->gen_nomor_transaction(),
														   'personal_id' => $info_request['personal_id'],
														   'company_id' => $this->company_id,
														   'debet' => '',
														   'kredit' => $info_request['amount'],
														   'approver' => $approver,
														   'transaction_requirement' => 'deposit',
														   'info' => 'Request Transfer Deposit Ke Tabungan Umrah',
														   'input_date' => date('Y-m-d H:i:s'),
														   'last_update' => date('Y-m-d H:i:s'));
				}
			
			}else{
				// akun
               	$data['jurnal'][] = array('company_id' => $this->company_id,
	                                      'source' => 'depositsaldo:notransaction:'.$nomor_transaction_tabungan_umrah,
	                                      'ref' => 'Deposit Saldo Dengan No Transaction :'.$nomor_transaction_tabungan_umrah,
	                                      'ket' => 'Deposit Saldo Dengan No Transaction :'.$nomor_transaction_tabungan_umrah,
	                                      'akun_debet' => '11010',
	                                      'akun_kredit' => '23000',
	                                      'saldo' => $info_request['amount'],
	                                      'periode_id' => 0,
	                                      'input_date' => date('Y-m-d H:i:s'),
                                   		  'last_update'  => date('Y-m-d H:i:s'));
				$data['deposit_transaction'][] = array('member_request_id' => $id,
													   'nomor_transaction' => $this->random_code_ops->gen_nomor_transaction(),
													   'personal_id' => $info_request['personal_id'],
													   'company_id' => $this->company_id,
													   'debet' => $info_request['amount_code'] + $info_request['amount'],
													   'kredit' => 0,
													   'approver' => $approver,
													   'transaction_requirement' => 'deposit',
													   'info' => 'Deposit',
													   'input_date' => date('Y-m-d H:i:s'),
													   'last_update' => date('Y-m-d H:i:s'));
			}
			// // pool process
			$get_pool_id = $this->model_general->get_pool_id( $info_request['personal_id'] );
			if( $get_pool_id == 0 ) {
				# get jamaah id
				$jamaah_id = $this->model_general->get_jamaah_id_by_personal_id( $info_request['personal_id'] );
				# data pool
				$data['pool']['company_id'] = $this->company_id;
				$data['pool']['jamaah_id'] = $jamaah_id;
				$data['pool']['active'] = 'active';
				$data['pool']['input_date'] = date('Y-m-d H:i:s');
				$data['pool']['last_update'] = date('Y-m-d H:i:s');
				#  data pool deposit transaction
				$data['pool_deposit_transaction']['company_id'] = $this->company_id;
				# get fee keagenan
				$fee_keagenan  = $this->model_general->fee_keagenan_deposit_paket($jamaah_id);
				if( count($fee_keagenan) > 0 ) {
					$data['fee_keagenan']['company_id'] = $this->company_id;
					$data['fee_keagenan']['personal_id'] = $info_request['personal_id'];
					$data['fee_keagenan']['input_date'] = date('Y-m-d');
					$data['fee_keagenan']['last_update'] = date('Y-m-d');
					# detail fee keagenan
					foreach ( $fee_keagenan as $key => $value ) {
						$data['detail_fee_keagenan'][] = array('transaction_number' => $this->random_code_ops->number_transaction_detail_fee_keagenan(),
						'company_id' => $this->company_id,
						'agen_id' => $value['id'],
						'level_agen_id' => $value['level_agen_id'],
						'fee' => $value['fee'],
						'sudah_bayar' => '0',
						'status_fee' => 'belum_lunas',
						'input_date' => date('Y-m-d H:i:s'),
						'last_update' => date('Y-m-d H:i:s'));
					}
				}
			} else {
				$data['pool_deposit_transaction']['company_id'] = $this->company_id;
				$data['pool_deposit_transaction']['pool_id'] = $get_pool_id;
			}
			# update member transaction request
			$data['member_transaction_request']['status_request'] = 'disetujui';
			$data['member_transaction_request']['approver'] = $approver;
			$data['member_transaction_request']['last_update'] = date('Y-m-d H:i:s');
			# filter model pengaturan cud
			if ( $this->model_beranda_utama_cud->approve_request($id, $data) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses approve request member berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses approve request member gagal dilakukan.',
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

	function _ck_headline_id(){
		if( $this->input->post('id') ) {
			if( ! $this->model_beranda_utama->check_headline_id($this->input->post('id')) ){
				$this->form_validation->set_message('_ck_headline_id', 'Headline ID tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	# proses add update headline
	function proses_addupdate_headline(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Request ID<b>', 'trim|xss_clean|min_length[1]|callback__ck_headline_id');
		$this->form_validation->set_rules('headline', '<b>Headline<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('show', '<b>Tampilkan Headline<b>', 'trim|xss_clean|min_length[1]|in_list[tampilkan]');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {

			$data = array();
			$data['headline'] = $this->input->post('headline');
			if( $this->input->post('show') ) {
				$data['tampilkan'] = 'tampilkan';
			}else{
				$data['tampilkan'] = 'sembunyikan';
			}
			$data['last_update'] = date('Y-m-d H:i:s');
			if( $this->input->post('id') ) {
				if( ! $this->model_beranda_utama_cud->update_headline($this->input->post('id'), $data) ){
					$error = 1;
					$error_msg = 'Proses update headline gagal dilakukan.';
				}
			}else{
				$data['input_date'] = date('Y-m-d H:i:s');
				$data['company_id'] = $this->company_id;
				if( ! $this->model_beranda_utama_cud->insert_headline($data) ){
					$error = 1;
					$error_msg = 'Proses insert headline gagal dilakukan.';
				}
			}
			# filter model pengaturan cud
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses approve request member berhasil dilakukan.',
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

	function editHeadline(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Request ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_headline_id');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			# value
			$value = $this->model_beranda_utama->get_info_headline( $this->input->post('id') );
			# filter model pengaturan cud
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses approve request member berhasil dilakukan.',
					'value' => $value,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error' => true,
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

	# delete headline
	function deleteHeadline(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Request ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_headline_id');
		/*
		Validation process
		*/
		if ($this->form_validation->run()) {
			# filter model pengaturan cud
			if ( $this->model_beranda_utama_cud->delete_headline( $this->input->post('id')) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete headline berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error' => true,
					'error_msg' => 'Proses delete headline gagal dilakukan.',
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
