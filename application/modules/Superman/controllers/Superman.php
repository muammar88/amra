<?php

/**
 *  -----------------------
 *	Superman Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Superman extends CI_Controller
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
		$this->load->model('Model_superman', 'model_superman');
		$this->load->model('Model_superman_cud', 'model_superman_cud');
		# checking is not Login
		$this->auth_library->Is_superman_not_login();
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function index(){
		// define property setting
		$this->index_loader->settingProperty(array('title'));
		// get setting data
		$this->index_loader->Setting();
		// // extract modul and submodul tab
		$modul_submodul_tab = $this->model_superman->get_modul_submodul_tab($this->session->userdata('superman')['module_access']);
		// // add js files
		$this->index_loader->addData(array(
			'js' => array(
				'Superman/dashboard_superman',
				'Superman/perusahaan_superman',
				'Superman/daftar_produk',
				'Superman/daftar_operator',
				'Superman/daftar_operator_iak',
				'Superman/daftar_produk_iak',
				'Superman/daftar_produk_tripay',
				'Superman/sinkronisasi_produk',
				'Superman/pelanggan_ppob',
				'Superman/transaksi_ppob',
				'Superman/request_tambah_saldo',
				'Superman/daftar_aktifitas_perusahaan'
			),
			'modul_access' => $this->session->userdata('superman')['module_access'],
			'modul_tab' => $modul_submodul_tab['modul_tab'],
			'submodul_tab' => $modul_submodul_tab['submodul_tab'],
		));
		// get setting values
		$data = $this->index_loader->Response();
		// generate authentication templating
		$this->templating->superman_templating($data);
	}

	// get info superman dashboard
	function get_info_superman_dashboard(){
		$error = 0;
		$error_msg = '';
		// get total perusahaan
		$total_perusahaan = $this->model_superman->get_total_perusahaan();
		// saldo perusahaan
		$saldo_perusahaan = $this->model_superman->get_saldo_perusahaan();
		// saldo pelanggan
		$saldo_pelanggan = $this->model_superman->get_saldo_pelanggan();
		// saldo amra
		$saldo_iak = $this->check_saldo_iak();
		// saldo tripay
		$saldo_tripay = $this->check_saldo_tripay();
		// get total ppob constimer
		$total_ppob_costumer = $this->model_superman->total_ppob_costumer();
		// laba amra
		$laba_amra = $this->model_superman->get_laba_amra();
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
				'error_msg' => 'Data dashboard berhasil ditemukan.',
				'data' => array('total_perusahaan' => $total_perusahaan,
								'saldo_perusahaan' => $saldo_perusahaan,
								'saldo_pelanggan' => $saldo_pelanggan,
								'saldo_amra' => array('iak' => $saldo_iak, 'tripay' => $saldo_tripay),
								'laba_amra' => $laba_amra,
								'total_ppob_costumer' => $total_ppob_costumer
							),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function check_saldo_tripay(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		# update harga produk
		$check = $this->tripay->check_balance();
		$balance = 0;
		# check
		if ( isset( $check->data ) ) {
			$balance = $check->data;
		}
		return $balance;
	}


	function check_saldo_iak(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		# update harga produk
		$check = $this->iak->check_balance();
		$balance = 0;
		# check
		if ( isset( $check->data->balance ) ) {
			$balance = $check->data->balance;
		}
		return $balance;
	}

   	function daftar_perusahaan(){
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
			$total 	= $this->model_superman->get_total_daftar_perusahaan($search);
			$list 	= $this->model_superman->get_index_daftar_perusahaan($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar perusahaan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar perusahaan berhasil ditemukan.',
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

   	// get data ppob superman
   	function get_data_ppob_superman(){
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
			$total 	= $this->model_superman->get_total_daftar_ppob_superman($search);
			$list 	= $this->model_superman->get_index_daftar_ppob_superman($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi ppob tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi ppob berhasil ditemukan.',
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

   	function get_request_data_tambah_saldo() {
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
			$total 	= $this->model_superman->get_total_daftar_request_tambah_saldo($search);
			$list 	= $this->model_superman->get_index_daftar_request_tambah_saldo($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar request tambah saldo tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar request tambah saldo berhasil ditemukan.',
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

   	function _ck_request_tambah_saldo_id($id) {
   		if( ! $this->model_superman->check_request_tambah_saldo($id) ){
   			$this->form_validation->set_message('_ck_request_tambah_saldo_id', 'ID Request Tambah Saldo tidak ditemukan.');
			return FALSE;
   		}else{
   			return TRUE;
   		}
   	}

   	# reject tambah saldo
   	function rejectTambahSaldo() {
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Tambah Saldo ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_request_tambah_saldo_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# receive id
			$id = $this->input->post('id');
			# reject process 
			if( ! $this->model_superman_cud->reject_tambah_saldo( $id ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses reject tambah saldo gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses reject tambah saldo berhasil dilakukan.',
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

   	# proses approved
   	function approveTambahSaldo(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Tambah Saldo ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_request_tambah_saldo_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# receive id
			$id = $this->input->post('id');
			# get info perusahaan
			$info_perusahaan = $this->model_superman->get_info_perusahaan( $id );
			# reject process 
			if( ! $this->model_superman_cud->approve_tambah_saldo( $id, $info_perusahaan ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses approve tambah saldo gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses approve tambah saldo berhasil dilakukan.',
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

   	function get_info_tambah_saldo_perusahaan(){
   		$error = 0;
		$error_msg = '';
		# get total perusahaan
		$get_list_perusahaan = $this->model_superman->get_list_perusahaan();
		# filter
		if ( count($get_list_perusahaan) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Daftar info tambah saldo gagal ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Daftar info tambah saldo berhasil ditemukan.',
				'data' => $get_list_perusahaan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
   	}

   	# check id perusahaan
   	function _ck_id_perusahaan($id){
   		if( ! $this->model_superman->check_id_perusahaan( $id ) ){
   			$this->form_validation->set_message('_ck_id_perusahaan', 'ID Perusahaan tidak ditemukan.');
			return FALSE;
   		}else{
   			return TRUE;
   		}
   	}

   	# count saldo perusahaan terakhir
   	function countSaldoPerusahaanTerakhir(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('perusahaan',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('saldo',	'<b>Saldo<b>', 	'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$id_perusahaan = $this->input->post('perusahaan');
			$saldo = trim($this->text_ops->hide_currency($this->input->post('saldo')));
			$get_saldo_sekarang = $this->model_superman->get_saldo_sekarang_perusahaan( $id_perusahaan ); 
			# filter
			if( $error == 0 ){
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $get_saldo_sekarang + $saldo,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// proses tambah saldo perusahaan
   	function proses_tambah_saldo_perusahaan(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('perusahaan',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('saldo',	'<b>Saldo<b>', 	'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$id_perusahaan = $this->input->post('perusahaan');
			$saldo = trim($this->text_ops->hide_currency($this->input->post('saldo')));
			$get_saldo_sekarang = $this->model_superman->get_saldo_sekarang_perusahaan( $id_perusahaan );
			# get data
			$data = array();
			$data['company']['saldo'] = $get_saldo_sekarang + $saldo;
			$data['company']['last_update'] = date('Y-m-d');
			$data['company_saldo_transaction']['company_id'] = $id_perusahaan;
			$data['company_saldo_transaction']['saldo'] = $saldo;
			$data['company_saldo_transaction']['request_type'] = 'deposit';
			$data['company_saldo_transaction']['ket'] = '';
			$data['company_saldo_transaction']['status'] = 'accepted';
			$data['company_saldo_transaction']['input_date'] = date('Y-m-d H:i:s');
			$data['company_saldo_transaction']['last_update'] = date('Y-m-d H:i:s');

			// tambahkan riwayat tambah saldo


			# filter
			if( $this->model_superman_cud->tambah_saldo_perusahaan( $id_perusahaan, $data ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					// 'data' => $get_saldo_sekarang + $saldo,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// get data request waktu berlangganan
   	function get_data_request_waktu_berlangganan(){
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
			$total 	= $this->model_superman->get_total_daftar_request_tambah_waktu_berlangganan($search);
			$list 	= $this->model_superman->get_index_daftar_request_tambah_waktu_berlangganan($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar request tambah waktu berlangganan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar request tambah waktu berlangganan berhasil ditemukan.',
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


   	function _ck_id_request($id){
   		if( ! $this->model_superman->check_id_request($id) ){
   			$this->form_validation->set_message('_ck_id_request', 'ID Request tidak ditemukan.');
   			return FALSE;
   		}else{
   			return TRUE;
   		}
   	}

   	// approve request waktu berlangganan
   	function approveRequestWaktuBerlangganan(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Request<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_request');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# get info request waktu berlangganan
			$info = $this->model_superman->get_info_request_waktu_berlangganan( $this->input->post('id') );
			# info deposit
			$info_deposit = $this->model_superman->get_info_deposit_subscription($info['order_id']);
			# data
			$data = array();
			$data['company']['start_date_subscribtion'] = $info['start_date_subscribtion'];
			$data['company']['end_date_subscribtion'] = $info['end_date_subscribtion'];
			$data['company']['last_update'] = date('Y-m-d');
			$data['subscribtion_payment_history']['payment_status'] = 'accept';
			$data['subscribtion_payment_history']['last_update'] = date('Y-m-d H:i:s');
			if( $info_deposit != '' ) {
				$data['company_saldo_transaction']['status'] = 'accepted';
				$data['company_saldo_transaction']['last_update'] = date('Y-m-d H:i:s');
			}
			// filter
			if ( ! $this->model_superman_cud->approve_tambah_waktu_berlangganan( $this->input->post('id'), $data, $info['company_id'], $info['order_id'], $info_deposit ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function rejectRequestWaktuBerlangganan(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Request<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_request');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# get info request waktu berlangganan
			$info = $this->model_superman->get_info_request_waktu_berlangganan( $this->input->post('id') );
			# info deposit
			$info_deposit = $this->model_superman->get_info_deposit_subscription($info['order_id']);
			# data
			$data = array();
			$data['subscribtion_payment_history']['payment_status'] = 'reject';
			$data['subscribtion_payment_history']['last_update'] = date('Y-m-d H:i:s');
			if( $info_deposit != '' ) {
				$data['company_saldo_transaction']['status'] = 'rejected';
				$data['company_saldo_transaction']['last_update'] = date('Y-m-d H:i:s');
			}
			// filter
			if ( ! $this->model_superman_cud->reject_tambah_waktu_berlangganan( $this->input->post('id'), $data, $info['order_id'], $info_deposit ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function get_info_tambah_waktu_berlangganan(){
   		$error = 0;
		$error_msg = '';
		// get total perusahaan
		$get_list_perusahaan = $this->model_superman->get_list_perusahaan_limited();
		# filter
		if ( count($get_list_perusahaan) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Daftar info tambah waktu berlangganan gagal ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Daftar info tambah waktu berlangganan berhasil ditemukan.',
				'data' => $get_list_perusahaan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
   	}

   	function count_tambah_waktu_berlangganan(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id_perusahaan',	'<b>ID Request<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('durasi',	'<b>Durasi<b>','trim|required|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			$id_perusahaan = $this->input->post('id_perusahaan');
			$duration = $this->input->post('durasi');
			# get id perusahaan
			$info_perusahaan = $this->model_superman->get_info_company( $id_perusahaan );
			# data 
			$data = array();
			if( $info_perusahaan['end_date_subscribtion'] < date('Y-m-d') ){
				$start_date = date('Y-m-d');
				$date_2 = strtotime($start_date);
				$date_2 = strtotime("+".$duration." month", $date_2);
				$end_date = date('Y-m-d', $date_2);
			}else{
				$date = strtotime($info_perusahaan['end_date_subscribtion']);
				$date = strtotime("+0 day", $date);
				$start_date = date('Y-m-d', $date);
				$date_2 = strtotime($start_date);
				$date_2 = strtotime("+".$duration." month", $date_2);
				$end_date = date('Y-m-d', $date_2);
			}
			$data['start_date_subscribtion'] = $info_perusahaan['start_date_subscribtion'];
			$data['end_date_subscribtion'] = $end_date;
			// filter
			if( count($data) > 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $data,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function proses_tambah_waktu_berlangganan_perusahaan() {
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('perusahaan',	'<b>ID Request<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('durasi',	'<b>Durasi<b>','trim|required|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			$id_perusahaan = $this->input->post('perusahaan');
			$duration = $this->input->post('durasi');
			# get id perusahaan
			$info_perusahaan = $this->model_superman->get_info_company( $id_perusahaan );
			# data 
			$data = array();
			if( $info_perusahaan['end_date_subscribtion'] < date('Y-m-d') ){
				$start_date = date('Y-m-d');
				$date_2 = strtotime($start_date);
				$date_2 = strtotime("+".$duration." month", $date_2);
				$end_date = date('Y-m-d', $date_2);
			}else{
				$date = strtotime($info_perusahaan['end_date_subscribtion']);
				$date = strtotime("+0 day", $date);
				$start_date = date('Y-m-d', $date);
				$date_2 = strtotime($start_date);
				$date_2 = strtotime("+".$duration." month", $date_2);
				$end_date = date('Y-m-d', $date_2);
			}
			$data['start_date_subscribtion'] = $info_perusahaan['start_date_subscribtion'];
			$data['end_date_subscribtion'] = $end_date;
			$data['last_update'] = date('Y-m-d');
			// filter
			if( $this->model_superman_cud->tambah_waktu_berlangganan_perusahaan($id_perusahaan, $data) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// daftar perusahaan
   	function server_perusahaan(){
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
			$total 	= $this->model_superman->get_total_list_perusahaan($search);
			$list 	= $this->model_superman->get_index_list_perusahaan($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar perusahaan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar perusahaan berhasil ditemukan.',
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

   	function get_info_tambah_perusahaan(){
   		$error = 0;
		$error_msg = '';
		// get total perusahaan
		$get_perusahaan_code = $this->model_superman->generated_company_code();
		# filter
		if ( $error == 1 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Daftar info tambah perusahaan gagal ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Daftar info tambah perusahaan berhasil ditemukan.',
				'data' => $get_perusahaan_code,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
   	}

   	// check id tambah perusahaan
   	function _ck_id_tambah_perusahaan(){
   		if( $this->input->post('id') ) {
   			if( ! $this->model_superman->check_perusahaan_id( $this->input->post('id') ) ) {
   				$this->form_validation->set_message('_ck_id_tambah_perusahaan', 'ID Perusahaan tidak ditemukan.');
   				return FALSE;
   			}else{
   				return TRUE;
   			}
   		}else{
   			return TRUE;
   		}
   	}

   	// check kode perusahaan
   	function _ck_kode_perusahaan( $kode ){
   		// check id
   		if ( $this->input->post('id') ){
   			// check kode
   			if ( $this->model_superman->check_kode_perusahaan( $kode, $this->input->post('id') ) ) {
   				$this->form_validation->set_message('_ck_kode_perusahaan', 'Kode Perusahaan Sudah Terdaftar Dipangkalan Data.');
   				return FALSE;
   			} else {
   				return TRUE;
   			}
   		} else {
   			if ( $this->model_superman->check_kode_perusahaan( $kode ) ) {
   				$this->form_validation->set_message('_ck_kode_perusahaan', 'Kode Perusahaan Sudah Terdaftar Dipangkalan Data.w');
   				return FALSE;
   			} else {
   				return TRUE;
   			}
   		}
   	}

   	function _ck_whatsapp_perusahaan( $nomor_whatsapp ) {
   		// check id
   		if ( $this->input->post('id') ) {
   			// check kode
   			if ( $this->model_superman->check_nomor_whatsapp( $nomor_whatsapp, $this->input->post('id') ) ) {
   				$this->form_validation->set_message('_ck_whatsapp_perusahaan', 'Nomor Whatsapp Sudah Terdaftar Dipangkalan Data.');
   				return FALSE;
   			} else {
   				return TRUE;
   			}
   		} else {
   			if ( $this->model_superman->check_nomor_whatsapp( $nomor_whatsapp ) ) {
   				$this->form_validation->set_message('_ck_whatsapp_perusahaan', 'Nomor Whatsapp Sudah Terdaftar Dipangkalan Data.');
   				return FALSE;
   			} else {
   				return TRUE;
   			}
   		}	
   	}

   	// check email exist
   	function _ck_email_exist($nomor_whatsapp){
   		// check id
   		if ( $this->input->post('id') ) {
   			// check kode
   			if ( $this->model_superman->check_email_perusahaan( $nomor_whatsapp, $this->input->post('id') ) ) {
   				$this->form_validation->set_message('_ck_email_exist', 'Email Sudah Terdaftar Dipangkalan Data.');
   				return FALSE;
   			} else {
   				return TRUE;
   			}
   		} else {
   			if ( $this->model_superman->check_email_perusahaan( $nomor_whatsapp ) ) {
   				$this->form_validation->set_message('_ck_email_exist', 'Email Sudah Terdaftar Dipangkalan Data.');
   				return FALSE;
   			} else {
   				return TRUE;
   			}
   		}
   	}

   	// check password
   	function _ck_password(){
   		if( ! $this->input->post('id') ) {
   			if( $this->input->post('password') AND $this->input->post('password') != '' ) {
   				return TRUE;
   			}else{
   				$this->form_validation->set_message('_ck_password', 'Password Tidak Boleh Kosong.');
   				return FALSE;
   			}
   		}else{
   			return TRUE;
   		}
   	}

   	# proses tambah perusahaan
   	function proses_tambah_perusahaan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|xss_clean|min_length[1]|numeric|callback__ck_id_tambah_perusahaan');
		$this->form_validation->set_rules('kode', '<b>Kode Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_kode_perusahaan');
		$this->form_validation->set_rules('nama', '<b>Nama Perusahaan<b>','trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tipe', '<b>Tipe Perusahaan<b>','trim|required|xss_clean|min_length[1]|in_list[limited,unlimited]');
		$this->form_validation->set_rules('whatsapp', '<b>Nomor Whatsapp<b>','trim|required|xss_clean|min_length[1]|callback__ck_whatsapp_perusahaan');
		$this->form_validation->set_rules('mulai_berlangganan',	'<b>Mulai Berlangganan<b>','trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('durasi',	'<b>Durasi<b>','trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('email',	'<b>Email<b>','trim|required|xss_clean|min_length[1]|valid_email|callback__ck_email_exist');
		$this->form_validation->set_rules('saldo',	'<b>Saldo<b>','trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('password',	'<b>Password<b>','trim|xss_clean|min_length[1]|callback__ck_password');
		$this->form_validation->set_rules('password_conf',	'<b>Konfirmasi Password<b>','trim|xss_clean|min_length[1]|matches[password]');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# duration
			$durasi = $this->input->post('durasi');
			$start_date = $this->input->post('mulai_berlangganan');
			$date = strtotime($start_date);
			$date = strtotime("+".$durasi." month", $date);
			$end_date = date('Y-m-d', $date);

			# receive data
			$data = array();
			$data['name'] = $this->input->post('nama');
			$data['code'] = $this->input->post('kode');
			$data['company_type'] = $this->input->post('tipe');
			$data['verified'] = 'verified';
			$data['whatsapp_number'] = $this->input->post('whatsapp');
			$data['start_date_subscribtion'] = $start_date;
			if( $this->input->post('saldo') AND $this->input->post('saldo') != '' ){
				$data['saldo'] = $this->text_ops->hide_currency( $this->input->post('saldo') ); 
			}
			if( $durasi != '' AND $durasi != '0' ) {
				$data['end_date_subscribtion'] = $end_date;
			}
			$data['email'] = $this->input->post('email');
			if( $this->input->post('password') AND $this->input->post('password') != '' ){
				$data['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			$data['last_update'] = date('Y-m-d');
			// filter
			if( $this->input->post('id') ) {
				$data['input_date'] = date('Y-m-d');
				if( ! $this->model_superman_cud->update_data_perusahaan( $this->input->post('id'), $data ) ) {
					$error = 1;
					$error_msg = 'Proses update data perusahaan gagal dilakukan.';
				}
			}else{
				if( ! $this->model_superman_cud->insert_data_perusahaan( $data ) ) {
					$error = 1;
					$error_msg = 'Proses insert data perusahaan gagal dilakukan.';
				}
			}
			// filter
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
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
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// hit waktu berlangganan
   	function hit_waktu_berlangganan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('mulai_berlangganan',	'<b>Mulai Berlangganan<b>','trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('durasi',	'<b>Durasi<b>','trim|required|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			$mulai_berlangganan = $this->input->post('mulai_berlangganan');
			$durasi = $this->input->post('durasi');
			$date = strtotime($mulai_berlangganan);
			$date = strtotime("+".$durasi." month", $date);
			$end_date = date('Y-m-d', $date);
			// filter
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $end_date,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
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
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// delete perusahaan
   	function delete_perusahaan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			// filter
			if(  $this->model_superman_cud->delete_perusahaan( $this->input->post('id') ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function get_info_edit_perusahaan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {

			$get_perusahaan_code = $this->model_superman->generated_company_code();

			$value = $this->model_superman->get_value_edit_perusahaan( $this->input->post('id') );
			// filter
			if(  $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $get_perusahaan_code,
					'value' => $value,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// get info saldo perusahaan
   	function get_info_saldo_perusahaan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			// data
			$data = $this->model_superman->get_value_edit_perusahaan( $this->input->post('id') );
			// filter
			if(  $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $data,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function proses_tambah_saldo(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('saldo',	'<b>Saldo<b>','trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			
			# receive data
			$id = $this->input->post('id');
			$saldo = $this->text_ops->hide_currency( $this->input->post('saldo') );
			// data
			$feedBack = $this->model_superman->get_value_edit_perusahaan( $this->input->post('id') );
			// saldo sekarang
			$saldo_sekarang = ( intval($feedBack['saldo']) + intval($saldo));;
			# prepare data
			$data = array();
			$data['company']['saldo'] = $saldo_sekarang;
			$data['company']['last_update'] = date('Y-m-d');
			$data['company_saldo_transaction']['company_id'] = $id; 
			$data['company_saldo_transaction']['saldo'] = $saldo_sekarang; 
			$data['company_saldo_transaction']['request_type'] = 'deposit'; 
			$data['company_saldo_transaction']['ket'] = ''; 
			$data['company_saldo_transaction']['status'] = 'accepted'; 
			$data['company_saldo_transaction']['input_date'] = date('Y-m-d H:i:s'); 
			$data['company_saldo_transaction']['last_update'] = date('Y-m-d H:i:s');
			// filter
			if(  $this->model_superman_cud->tambah_saldo($id, $data)  ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);

   	}

   	// get info tambah waktu by berlanggana
	function get_info_tambah_waktu_by_berlangganan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# get value edit
			$feedBack = $this->model_superman->get_value_edit_perusahaan( $this->input->post('id') );
			// filter
			if(  $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}

   	// count durasi berlangganan
   	function CountDurasiBerlangganan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('durasi',	'<b>Durasi<b>','trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {

			$id = $this->input->post('id');
			$durasi = $this->input->post('durasi') == '' ? 0 : $this->input->post('durasi');
			# get value edit
			$feedBack = $this->model_superman->get_value_edit_perusahaan( $this->input->post('id') );
			// filter
			if( $feedBack['end_date_subscribtion'] < date('Y-m-d') ) {
				$start_date = date('Y-m-d');
			}else{
				$start_date = $feedBack['end_date_subscribtion'];
			}
			$date = strtotime($start_date);
			$date = strtotime("+".$durasi." month", $date);
			$end_date = date('Y-m-d', $date);
			// filter
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => array('start_date' => $feedBack['start_date_subscribtion'], 
									'end_date' => $end_date),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
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
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function proses_tambah_waktu_berlangganan_per_perusahaan(){
   		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('durasi',	'<b>Saldo<b>','trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			$id = $this->input->post('id');
			$durasi = $this->input->post('durasi') == '' ? 0 : $this->input->post('durasi');
			# get value edit
			$feedBack = $this->model_superman->get_value_edit_perusahaan( $this->input->post('id') );
			// filter
			if( $feedBack['end_date_subscribtion'] < date('Y-m-d') ) {
				$start_date = date('Y-m-d');
			}else{
				$start_date = $feedBack['end_date_subscribtion'];
			}
			$date = strtotime($start_date);
			$date = strtotime("+".$durasi." month", $date);
			$end_date = date('Y-m-d', $date);
			# 
			$data = array();
			if( $feedBack['end_date_subscribtion'] < date('Y-m-d') ) {
				$data['start_date_subscribtion'] = date('Y-m-d');
			}else{
				$data['start_date_subscribtion'] = $feedBack['start_date_subscribtion'];
			}
			$data['end_date_subscribtion'] = $end_date;
			$data['last_update'] = date('Y-m-d');
			// print_r($data);
			// filter
			if(  $this->model_superman_cud->tambah_waktu_berlangganan_per_perusahaan($id, $data)  ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
   	}


   	function daftar_request_tambah_saldo(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('status', '<b>Status</b>', 'trim|required|xss_clean|min_length[1]|in_list[proses,disetujui,ditolak]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$status = $this->input->post('status');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_superman->get_total_daftar_request_tambah_saldo_superman($search, $status);
			$list 	= $this->model_superman->get_index_daftar_request_tambah_saldo_superman($perpage, $start_at, $search, $status);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar perusahaan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar perusahaan berhasil ditemukan.',
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

   	function _ck_id_request_tambah_saldo($id){
   		$check = $this->model_superman->check_request_id_tambah_saldo($id);
   		if ( ! $check ) { 
   			$this->form_validation->set_message('_ck_id_request_tambah_saldo', 'ID Request tambah saldo tidak tidak ditemukan.');
   			return FALSE;
   		}else{
   			return TRUE;
   		}
   	}

   	// proses approve request tambah saldo
   	function proses_approve_request_tambah_saldo(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Request Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request_tambah_saldo');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$id = $this->input->post('id');
			// get info request tambah saldo
			$info = $this->model_superman->get_info_saldo_request_tambah_saldo( $id );

			if( count($info) > 0 ) {
				// get saldo company 
				$saldo = $this->model_superman->get_saldo_company( $info['company_id'] );
				$saldo_sekarang = $saldo + $info['saldo'];

				$data = array();
				$data['request_tambah_saldo_company']['status'] = 'disetujui';
				$data['request_tambah_saldo_company']['last_update'] = date('Y-m-d H:i:s');
				$data['company']['saldo'] = $saldo_sekarang;
				$data['company']['last_update'] = date('Y-m-d H:i:s');
				// filter			
				if ( ! $this->model_superman_cud->approve_request_tambah_saldo( $id, $info['company_id'], $data ) ) { 
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses approve request tambah saldo gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses approve request tambah saldo berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			}else{
				$return = array(
						'error'	=> true,
						'error_msg' => 'Proses approve request tambah saldo gagal dilakukan.',
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


   	function proses_tolak_request_tambah_saldo() {
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Request Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request_tambah_saldo');
		$this->form_validation->set_rules('alasan',	'<b>Alasan Penolakan<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$id = $this->input->post('id');
			$alasan = $this->input->post('alasan');
			// receive data
			$data = array();
			$data['status'] = 'ditolak';
			$data['alasan_tolak'] = $alasan;
			$data['last_update'] = date('Y-m-d H:i:s');
			// penolakan proses
			if( ! $this->model_superman_cud->tolak_request_tambah_saldo( $id, $data ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses penolakan request tambah saldo gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penolakan request tambah saldo berhasil dilakukan.',
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


   	function proses_delete_request_tambah_saldo(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Request Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request_tambah_saldo');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$id = $this->input->post('id');
			// proses delete
			if( ! $this->model_superman_cud->delete_request_tambah_saldo( $id ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete request tambah saldo gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete request tambah saldo berhasil dilakukan.',
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


   	function _ck_biaya_take_charge($biaya){
   		// get saldo member
   		$biaya = $this->text_ops->hide_currency( $biaya );
		$saldo = $this->model_superman->get_saldo_sekarang_perusahaan( $this->input->post('id') );
		// saldo
		if( $saldo < $biaya ) {
			$this->form_validation->set_message('_ck_biaya_take_charge', 'SALDO PERUSAHAAN TIDAK MENCUKUPI.');
   		 	return FALSE;
		}else{
			return TRUE;
		}
   	}

   	function TakeCharge(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Perusahaan<b>','trim|required|xss_clean|min_length[1]|callback__ck_id_perusahaan');
		$this->form_validation->set_rules('tipe',	'<b>Tipe Berlangganan<b>', 'trim|required|xss_clean|min_length[1]|in_list[pilih_tipe,berlangganan,pembelian]');
		$this->form_validation->set_rules('biaya',	'<b>Biaya Biaya Berlangganan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_biaya_take_charge');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$id = $this->input->post('id');

			if( $this->input->post('tipe') != 'pilih_tipe') {
				$biaya = $this->text_ops->hide_currency($this->input->post('biaya'));
				$saldo = $this->model_superman->get_saldo_sekarang_perusahaan( $this->input->post('id') );
				$tipe = $this->input->post('tipe');

				$data_transaksi = array();
				$data_transaksi['company_id'] = $id;
				$data_transaksi['saldo'] = $biaya;
				$data_transaksi['request_type'] = $tipe == 'berlangganan' ? 'payment_subscription' : 'pruchase' ;
				$data_transaksi['status'] = 'accepted';
				$data_transaksi['ket'] = $tipe == 'berlangganan' ? 'Pembayaran berlangganan aplikasi AMRA' : '';
				$data_transaksi['input_date'] = date('Y-m-d H:i:s');
				$data_transaksi['last_update'] = date('Y-m-d H:i:s'); 

				$data_company = array();
				$data_company['saldo'] = $saldo - $biaya;
				$data_company['last_update'] = date('Y-m-d');

				if ( $this->model_superman_cud->takeCharge($id, $data_transaksi, $data_company)) {
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses Pemotongan Biaya Berhasil Dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses Pemotongan Biaya Gagal Dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Anda Wajib Memilih Salah Satu Tipe Transaksi.',
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