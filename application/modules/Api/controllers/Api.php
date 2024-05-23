<?php

/**
 *  -----------------------
 *	Api Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_api', 'model_api');
		# model api cud
		$this->load->model('Model_api_cud', 'model_api_cud');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}


	function md5(){

		$var = md5('gapajaD7VQKodev-82309dd0-8684-11ee-bada-e3aa4ec369e9001');
		echo $var;
	}

	function _check_token( $token ) {
		# get token info
		$token_info = $this->model_api->get_token_info( $token, $this->input->post('company_code') );
		// count array token info
		if( count( $token_info ) > 0 ) {
			// expire date
			$expire_datetime = new DateTime($token_info['token_expired_datetime']);
			// FILTER
			if(date('Y-m-d H:i:s') > $expire_datetime ) {
			    $this->form_validation->set_message('_check_token', 'Sesi sudah berakhir, silahkan login ulang.');
				return FALSE;
			} else {
				return TRUE;
			}
		}else{
			echo $token;
			$this->form_validation->set_message('_check_token', 'Token tidak ditemukan.');
			return FALSE;
		}
	}

	function Login()
	{
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('whatsapp_number',	'<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_no_whatsapp');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|required|xss_clean|min_length[1]');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {

			$whatsapp_number = $this->input->post('whatsapp_number');
			$company_code = $this->input->post('company_code');
			$password = $this->input->post('password');

			# aunthentication process
			$return = $this->model_api->username_password_authentication($company_code, $whatsapp_number, $password);

		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function Home(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			# get headline
			$headline = $this->model_api->get_headline($company_id_personal_id['company_id']);
			# get info paket
			$info_paket = $this->model_api->get_info_paket($this->input->post('company_code'));
			# get info akun
			$info_akun = $this->model_api->get_info_akun($this->input->post('token'), $this->input->post('company_code'));
			# deposit
			$info_deposit_tabungan = $this->model_api->info_deposit_tabungan($company_id_personal_id['company_id'], $company_id_personal_id['id']);
			# get notif 
			$get_notif = $this->model_api->get_notif($company_id_personal_id['company_id'], $company_id_personal_id['id']);
			# return
			$return =  	array('error' => false, 
							  'error_msg' => 'Success', 
							  'data' => array('fullname' => $info_akun['fullname'],
											  'identity_number' => $info_akun['identity_number'],
											  'nomor_whatsapp' => $info_akun['nomor_whatsapp'],
											  'birth_place' => $info_akun['birth_place'],
											  'birth_date' => $info_akun['birth_date'],
											  'deposit' =>  'Rp' . number_format($info_deposit_tabungan['deposit']),
											  'tabungan' => 'Rp' . number_format($info_deposit_tabungan['tabungan']),
											  'markup_withdraw' => 'Rp ' . number_format($info_deposit_tabungan['markup_withdraw']),
											  'total_markup' => 'Rp ' . number_format($info_deposit_tabungan['deposit'] - $info_deposit_tabungan['markup_withdraw']),
											  'headline' => $headline,
											  'list_paket' => $info_paket, 
											  'notif' => strval($get_notif) ) );
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function InfoTambahSaldo(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {

			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$token = $this->input->post('token');

			// $token = $this->input->post('token');
			$list_bank = $this->model_api->get_rek_info($company_id_personal_id['company_id']);
			$data = array();
			$data['list_bank'] = $list_bank;
			if (count($list_bank) == 0) {
				$error = 1;
				$error_msg = 'Daftar Bank Tidak Ditemukan.';
			}
			$list_tambah_saldo = array();
			if ($error == 0) {
				$list_tambah_saldo = $this->model_api->get_list_tambah_saldo($company_id_personal_id['company_id'],$token);
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
					'list_bank' => $list_bank,
					'list_tambah_saldo' => $list_tambah_saldo
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					'token' => $this->input->post('token')
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function _ck_bank_exist($bank_name){
		if( ! $this->model_api->check_bank_exist( $bank_name, $this->input->post('company_code') ) ){
			$this->form_validation->set_message('_ck_bank_exist', 'Bank tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}


	function _ck_nominal($nominal)
	{
		$c_nominal  = $this->text_ops->hide_currency($nominal);
		if ($c_nominal == 0) {
			$this->form_validation->set_message('_ck_nominal', 'Nominal tambah saldo tidak boleh nol.');
			return FALSE;
		} else {
			return TRUE;
		}
	}


	function _ck_jumlah_deposit_member($sumber_biaya){
		if( $this->input->post('keperluan') == 'Tabungan Umrah') {
			if( $this->input->post('sumber_biaya') == 'Deposit' ) {
				$nominal = $this->text_ops->hide_currency($this->input->post('nominal'));
				# token
				$token = $this->input->post('token');
				$company_code = $this->input->post('company_code');
				# get personal id
				$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
				if( ! $this->model_api->check_jumlah_deposit_member($company_id_personal_id['company_id'], $company_id_personal_id['id'], $nominal) ){
					$this->form_validation->set_message('_ck_jumlah_deposit_member', 'Jumlah deposit member/jamaah tidak mencukupi.');
					return FALSE;
				}else{
					return TRUE;
				}
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}


	function _ck_status_get_tiket(){
		$token = $this->input->post('token');
		$company_code = $this->input->post('company_code');
		# get personal id
		$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
		# filter
		if( $this->model_api->check_status_proses_exist($company_id_personal_id['company_id'], $company_id_personal_id['id']) ){
			$this->form_validation->set_message('_ck_status_get_tiket', 'Anda tidak dapat menambah tiket karena masih terdapat tiket yang belum diproses.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// get tiket
	function GetTiket(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('nominal', '<b>Nominal<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nominal');
		$this->form_validation->set_rules('bank_pembayaran', '<b>Bank Pembayaran<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank_exist');
		$this->form_validation->set_rules('keperluan', '<b>Keperluan<b>', 'trim|required|xss_clean|min_length[1]|in_list[Deposit,Tabungan Umrah]|callback__ck_status_get_tiket');
		if ( $this->input->post('keperluan') == 'Tabungan Umrah' ) {
			$this->form_validation->set_rules('sumber_biaya', '<b>Sumber Biaya<b>', 'trim|required|xss_clean|min_length[1]|in_list[Cash,Deposit]|callback__ck_jumlah_deposit_member');
		}
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# retrive post data
			$token = $this->input->post('token');
			$company_code = $this->input->post('company_code');
			$token = $this->input->post('token');
			$nominal = $this->text_ops->hide_currency($this->input->post('nominal'));
			$nama_bank = $this->input->post('bank_pembayaran');
			$keperluan = $this->input->post('keperluan');
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			# get company id
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			# get bank id
			$bank_info = $this->model_api->get_bank_info($company_id, $nama_bank);
			# data
			$data = array();
			$data['transaction_number'] = $this->model_api->gen_transction_number( $company_id );
			$data['company_id'] = $company_id;
			$data['personal_id'] = $personal_id;
			$data['amount'] = $this->text_ops->hide_currency($this->input->post('nominal'));
			$data['activity_type'] = $keperluan == 'Tabungan Umrah' ? 'deposit_paket' : 'deposit';
			if( $this->input->post('sumber_biaya') AND $this->input->post('sumber_biaya') == 'Deposit'){
				$data['payment_source'] = 'deposit';
				$data['sending_payment_status'] = 'sudah_dikirim';
				$data['amount_code'] = 0;
			}else{
				$data['amount_code'] = $this->model_api->gen_amount_code($company_id, $personal_id);
				$data['payment_source'] = 'transfer';
			}
			$data['bank_id'] = $bank_info['bank_id'];
			$data['bank_account'] = $bank_info['account_number'];
			$data['account_name'] = $bank_info['account_name'];
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');


			// check apakah member sudah terdaftar jadi jamaah 

			// apabila belum, maka daftar jamaah dulu.

			// check apakah

			# insert process
			if (!$this->model_api->insert_member_transaction_request( $data ) ) {
				$error = 1;
				$error_msg = 'Tiket gagal dibuat';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if ( validation_errors() ) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function _ck_trx_number($trx_number)
	{
		$token = $this->input->post('token');
		$company_code = $this->input->post('company_code');
		if (!$this->model_api->check_transaction_number_axist($trx_number, $token, $company_code)) {
			$this->form_validation->set_message('_ck_trx_number', 'Nomor transaksi tidak ditemukan dipangkalan data.');
			return FALSE;
		} else {
			return  TRUE;
		}
	}

	function WasSend(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('trx_number', '<b>Nomor Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_trx_number');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			# retrive post data
			$company_code = $this->input->post('company_code');
			$token = $this->input->post('token');
			$trx_number = $this->input->post('trx_number');
			$personal_id = $company_id_personal_id['id'];
			$company_id = $company_id_personal_id['company_id'];
			# define data
			$data = array();
			$data['sending_payment_status'] = 'sudah_dikirim';
			$data['last_update'] = date('Y-m-d H:i:s');
			# insert process
			if ( !$this->model_api->sudahdikirim( $data, $trx_number, $personal_id, $company_id ) ) {
				$error = 1;
				$error_msg = 'Gagal update status kirim';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Success',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function DeleteRequestAddSaldo(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			# retrive post data
			$personal_id = $company_id_personal_id['id'];
			$company_id = $company_id_personal_id['company_id'];
			// filter
			if( $this->model_api->check_request_proses_axist($personal_id, $company_id) ) {
				# insert process
				if ( ! $this->model_api->deleteRequestTiket( $personal_id, $company_id ) ) {
					$error = 1;
					$error_msg = 'Tiket gagal dihapus';
				}
			}else{
				$error = 1;
				$error_msg = 'Tiket yang bisa dihapus hanya tiket dengan status diproses dan belum dikirim.';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses pembatalan request tiket deposit & tabungan berhasil dilakukan.',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	# get info akun
	function GetInfoAkun(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			# get info personal 
			$get_info_personal = $this->model_api->get_info_personal($company_id_personal_id['company_id'], $company_id_personal_id['id']);
			// list bank
			$list_bank = $this->model_api->get_rek_info($company_id_personal_id['company_id']);
			$data = array();
			$data['fullname'] = $get_info_personal['fullname'];
			$data['identity_number'] = $get_info_personal['identity_number'];
			$data['birth_place'] = $get_info_personal['birth_place'];
			$data['birth_date'] = $get_info_personal['birth_date'];
			$data['account_name'] = $get_info_personal['account_name'];
			$data['number_account'] = $get_info_personal['number_account'];
			$data['bank_id'] = $get_info_personal['bank_id'];
			$data['list_bank'] = $list_bank;
			# filter feedBack
			if (count( $get_info_personal ) > 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses pembatalan request tiket deposit & tabungan berhasil dilakukan.',
					'data' => $data
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// check bank name
	function _ck_bank_name($bank_name){
		if( ! $this->model_api->check_bank_exist_by_name( $bank_name ) ) {
			$this->form_validation->set_message('_ck_bank_name', 'Nama bank tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}


	function _ck_password($password_baru) {
		if( $this->input->post('password_baru') != '' ) {
			if( $this->input->post('password_lama') == '' ) {
				$this->form_validation->set_message('_ck_password', 'Untuk mengubah password, anda wajib mengisi password lama.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	function _ck_password_lama() {
		if( $this->input->post('password_lama') != '' ) {
			$token = $this->input->post('token');
			$company_code = $this->input->post('company_code');
			if( ! $this->model_api->check_password_lama($token, $company_code, $this->input->post('password_lama') ) ) {
				$this->form_validation->set_message('_ck_password_lama', 'Password Lama tidak cocok.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}


	// save data akun
	function SaveDataAkun(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('nama_lengkap', '<b>Nama Lengkap<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tempat_lahir', '<b>Tempat Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_lahir', '<b>Tanggal Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_akun', '<b>Nama Akun<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_akun', '<b>Nomor Akun<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('bank', '<b>ID Bank<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank_name');
		$this->form_validation->set_rules('password_lama', '<b>Password Lama<b>', 'trim|xss_clean|callback__ck_password_lama');
		$this->form_validation->set_rules('password_baru', '<b>Kode Perusahaan<b>', 'trim|xss_clean|callback__ck_password');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			// data
			$data = array();
			$data['fullname'] = $this->input->post('nama_lengkap');
			$data['identity_number'] = $this->input->post('nomor_identitas');
			$data['birth_place'] = $this->input->post('tempat_lahir');
			$data['birth_date'] = $this->input->post('tanggal_lahir');
			$data['account_name'] = $this->input->post('nama_akun');
			$data['number_account'] = $this->input->post('nomor_akun');
			$data['bank_id'] = $this->model_api->get_bank_id_by_bank_name($this->input->post('bank'));
			if( $this->input->post('password_lama') != '' AND $this->input->post('password_baru')){
				$data['password'] = password_hash($this->input->post('password_baru') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			$data['last_update'] = date('Y-m-d');
			// $data['list_bank'] = $list_bank;
			# filter feedBack
			if ( $this->model_api->update_data_akun($company_id_personal_id['id'], $data) ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses update data akun berhasil dilakukan.',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses update akun gagal dilakukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()) . $this->input->post('token'),
				);
			}
		}
		echo json_encode($return);
	}
	
	function ListWithDraw(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );

			$get_list_withdraw = $this->model_api->get_list_withdraw( $company_id_personal_id['company_id'], $company_id_personal_id['id']);

			// # filter feedBack
			if (count( $get_list_withdraw ) > 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Daftar withdraw berhasil ditemukan.',
					'list_withdraw' => $get_list_withdraw
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar withdraw gagal ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// save with draw
	function SaveWithDraw(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('nominal', '<b>Nominal<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nominal');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			# nominal
			$nominal = $this->text_ops->hide_currency( $this->input->post('nominal') );
			// get markup withdraw
			$markup_withdraw = $this->model_api->get_markup_withdraw($company_id_personal_id['company_id']);
			# check maksimal withdraw
			if( ! $this->model_api->check_maksimal_with_draw( $company_id_personal_id['company_id'], $company_id_personal_id['id'], ($nominal + $markup_withdraw) ) ) {
				$error = 1;
				$error_msg = 'Anda tidak dapat melakukan withdraw, karena jumlah withdraw anda melebihi jumlah deposit.';
			}
			// check withdraw process is exis
			if( $this->model_api->check_with_draw_exist( $company_id_personal_id['company_id'], $company_id_personal_id['id'] ) AND $error == 0 ) {
				$error = 1;
				$error_msg = 'Anda tidak dapat melakukan withdraw, karena masih terdapat withdraw yang sedang diproses.';
			}
			// insert proses
			if( $error == 0 ) {
				# get info account bank
				$get_info_akun_member = $this->model_api->get_info_akun_member($company_id_personal_id['company_id'],$company_id_personal_id['id']);
				// receive data
				$data = array();
				$data['company_id'] = $company_id_personal_id['company_id'];
				$data['personal_id'] = $company_id_personal_id['id'];
				$data['transaction_number'] = $this->model_api->gen_transction_number_withdraw( $company_id_personal_id['company_id'] );
				$data['amount'] = $nominal;
				$data['status_request'] = 'diproses';
				$data['account_name'] = $get_info_akun_member['account_name'];
				$data['account_number'] = $get_info_akun_member['number_account'];
				$data['bank_id'] = $get_info_akun_member['bank_id'];
				$data['input_date'] = date('Y-m-d H:i:s');
				$data['last_update'] = date('Y-m-d H:i:s');
				// filter
				if( ! $this->model_api->saveWithDraw( $data ) ) {
					$error = 1;
					$error_msg = 'Proses withdraw gagal dilakukan.';
				}
			}
			// filter feedBack
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses withdraw berhasil dilakukan.',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	function _ck_paket_kode_exist($kode_paket){
		# get personal id
		$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
		// check paket id exist
		if( ! $this->model_api->check_paket_kode_exist( $kode_paket, $company_id_personal_id['company_id'] ) ){
			$this->form_validation->set_message('_ck_paket_kode_exist', 'Kode paket tidak ditemukan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}


	function GetListTipePaket(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('paket_code', '<b>Kode Paket<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_paket_kode_exist');
		/*
	        Validation process
	    */
		if ($this->form_validation->run()) {
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			// check apakah deposit dia mencukupi
			$list_tipe_paket = $this->model_api->get_list_tipe_paket($company_id_personal_id['company_id'], $this->input->post('paket_code'));
			// filter feedBack
			if ( count( $list_tipe_paket ) > 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Tipe paket berhasil ditemukan.',
					'list_tipe_paket' => $list_tipe_paket
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Tipe paket gagal ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// tipe paket id
	function _ck_tipe_paket_id_exist( $tipe_paket_id ){
		if( $this->input->post('paket_code') ) {
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			if( ! $this->model_api->check_tipe_paket_id( $company_id_personal_id['company_id'], $this->input->post('paket_code'), $tipe_paket_id ) ){
				$this->form_validation->set_message('_ck_tipe_paket_id_exist', 'Tipe paket ID tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;	
			}
		}else{
			return TRUE;
		}
	}

	// beli paket
	function BeliPaket() {
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('paket_code', '<b>Kode Paket<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_paket_kode_exist');
		$this->form_validation->set_rules('tipe_paket_id', '<b>Tipe Paket ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_tipe_paket_id_exist');
		// $this->form_validation->set_rules('nomor_visa', '<b>Nomor Visa<b>', 'trim|xss_clean|min_length[1]');
		// $this->form_validation->set_rules('mulai_berlaku', '<b>Mulai Berlaku<b>', 'trim|xss_clean|min_length[1]');
		// $this->form_validation->set_rules('akhir_berlaku', '<b>Akhir Berlaku<b>', 'trim|xss_clean|min_length[1]');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			$paket_code = $this->input->post('paket_code');
			$tipe_paket_id = $this->input->post('tipe_paket_id');
			# get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			# get info paket
			$get_info_paket = $this->model_api->get_info_tipe_paket($company_id, $paket_code, $tipe_paket_id);
			$paket_id = $get_info_paket['paket_id'];
			$tipe_paket_id = $get_info_paket['tipe_paket_id'];
			// apakah jamaah sudah membeli paket ini
			if( $this->model_api->check_paket_sudah_dibeli( $company_id, $paket_id, $tipe_paket_id, $personal_id ) ) {
				$error = 1;
				$error_msg = 'Member/Jamaah sudah membeli paket ini.';
			}
			$total_tabungan = 0;
			// get info deposit tabungan umrah
			if( $error == 0 ) {
				// total tabungan
				$total_tabungan = $this->model_api->get_info_tabungan_member_jamaah($company_id, $personal_id);
				if( $total_tabungan < $get_info_paket['price'] ){
					$error = 1;
					$error_msg = 'Total tabungan umrah anda tidak mencukupi untuk membeli tipe paket ini. Silahkan lakukan deposit untuk melanjutkan pembelian.';
				}
			}	
			// filter
			if( $error == 0 ) {
				# get jamaah id by personal id
				$jamaah_id = $this->model_api->get_jamaah_id_by_personal_id($company_id, $personal_id);
				# data
				$data = array();
				$data['info_paket']['kode'] = $this->input->post('paket_code');
				$data['info_paket']['paket_name'] = $get_info_paket['paket_name'];
				# paket transaction
				$data['paket_transaction']['no_register'] = $this->text_ops->get_no_register();
				$data['paket_transaction']['company_id'] = $company_id;
				$data['paket_transaction']['paket_id'] = $paket_id;
				$data['paket_transaction']['paket_type_id'] = $tipe_paket_id;
				$data['paket_transaction']['diskon'] = 0;
				$data['paket_transaction']['payment_methode'] = 0;
				$data['paket_transaction']['price_per_pax'] = $get_info_paket['price'];
				// nomor visa
				// if ( $this->input->post('nomor_visa') != '' ) {
				// 	$data['paket_transaction']['no_visa'] = $this->input->post('nomor_visa');
				// }
				// if ( $this->input->post('nomor_visa') != '' ) {
				// 	$data['paket_transaction']['tgl_berlaku_visa'] = $this->input->post('mulai_berlaku');
				// }
				// if ( $this->input->post('nomor_visa') != '' ) {
				// 	$data['paket_transaction']['tgl_akhir_visa'] = $this->input->post('akhir_berlaku');
				// }
				$data['paket_transaction']['input_date'] = date('Y-m-d H:i:s');
				$data['paket_transaction']['last_update'] = date('Y-m-d H:i:s');
				# jamaah
				$data['paket_transaction_jamaah']['jamaah_id'] = $jamaah_id;
				$data['paket_transaction_jamaah']['company_id'] = $company_id;
				# total paket
				$total_paket = $data['paket_transaction']['price_per_pax'] - $data['paket_transaction']['diskon'];
				# check apakah jamaah butuh mahram
				if( $this->model_api->get_info_need_mahram( $data['paket_transaction_jamaah']['jamaah_id'], $company_id ) ) {
					# biaya mahram
					$data['paket_transaction']['total_mahram_fee'] = $this->model_api->getBiayaMahram( $paket_id, $company_id );
					# total paket
					$total_paket = $total_paket + $data['paket_transaction']['total_mahram_fee'];
				}
				$data['paket_transaction']['total_paket_price'] = $total_paket;
				# get deposit
				$total_deposit = $this->model_api->get_total_deposit_paket( $data['paket_transaction_jamaah']['jamaah_id'], $company_id );
				# count sisa
				$sisa = $data['paket_transaction']['total_paket_price'] - $total_deposit;
				# sisa
				if ( $sisa <= 0 ) {
					# paid
					$paid = $data['paket_transaction']['total_paket_price'];
					if ( $sisa < 0 ) {
						# proses insert sisa deposit ke deposit transaction
						$data['deposit_transaction'][] = array('nomor_transaction' => $this->random_code_ops->random_invoice_deposit_transaction(),
																			'personal_id' => $personal_id,
																			'company_id' => $company_id,
																			'debet' => abs($sisa),
																			'approver' => 'self',
																			'transaction_requirement' => 'deposit',
																			'info' => 'Sisa pembayaran paket dari deposit paket',
																			'input_date' => date('Y-m-d H:i:s'),
																			'last_update' => date('Y-m-d H:i:s'));
					}
					# pembayaran deposit transaction
					$data['deposit_transaction'][] = array('nomor_transaction' => $this->random_code_ops->random_invoice_deposit_transaction(),
														   'personal_id' => $personal_id,
														   'company_id' => $company_id,
														   'kredit' => abs($paid),
														   'approver' => 'self',
														   'transaction_requirement' => 'paket_payment',
														   'info' => 'Pembayaran paket',
														   'input_date' => date('Y-m-d H:i:s'),
														   'last_update' => date('Y-m-d H:i:s'));
					# filter by metode pembayaran
					$infoDepositor = $this->model_api->getInfoDepositorByJamaahId( $data['paket_transaction_jamaah']['jamaah_id'], $company_id );
					# paket_transaction_history
					$data['paket_transaction_history']['invoice'] =  $this->text_ops->get_invoice_transaksi_paket(); // $this->input->post('invoiceID');
					$data['paket_transaction_history']['company_id'] = $company_id;
					$data['paket_transaction_history']['source'] = 'deposit';
					$data['paket_transaction_history']['paid'] = $paid;
					$data['paket_transaction_history']['receiver'] = 'self';
					$data['paket_transaction_history']['ket'] = 'cash';
					$data['paket_transaction_history']['deposit_name'] = $infoDepositor['fullname'];
					$data['paket_transaction_history']['deposit_phone'] = $infoDepositor['nomor_whatsapp'];
					$data['paket_transaction_history']['deposit_address'] = $infoDepositor['address'];
					$data['paket_transaction_history']['input_date'] = date('Y-m-d H:i:s');
					$data['paket_transaction_history']['last_update'] = date('Y-m-d H:i:s');
					# pool info
					$pool_info = $this->model_api->get_info_pool($data['paket_transaction_jamaah']['jamaah_id'], $company_id);
					# pool
					$data['pool']['active'] = 'non_active';
					$data['pool_id'] = $pool_info['pool_id'];
					# handover facilities
					if ( count( $pool_info['handover_facilities'] ) > 0 ) {
						$handover_facilities = $pool_info['handover_facilities'];
						$data_handover = array();
						foreach ( $handover_facilities as $key => $value ) {
							$data_handover[] = array('company_id' => $company_id,
													 'invoice' => $value['invoice'],
													 'facilities_id' => $value['facilities_id'],
													 'officer' => $value['officer'],
													 'jamaah_id' => $data['paket_transaction_jamaah']['jamaah_id'],
													 'receiver_name' => $value['receiver_name'],
													 'receiver_identity' => $value['receiver_identity'],
													 'date_transaction' => $value['date_transaction']);
						}
						$data['handover_facilities'] = $data_handover;
					}
					# move fee keagenan
					if ( $pool_info['fee_keagenan_id'] != 0 ) {
						$data['paket_transaction']['fee_keagenan_id'] = $pool_info['fee_keagenan_id'];
					}
					# insert process
					if ( ! $this->model_api_cud->insert_transaction_process( $data, $company_id ) ) {
						$error = 1;
						$error_msg = 'Proses pembelian paket gagal dilakukan.';
					}
				} else {
					$error = 1;
					$error_msg = 'Proses tidak dapat dilanjutkan, karena deposit jamaah tidak mencukupi untuk melakukan pembelian paket.';
				}
			}
			// filter feedBack
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses pembelian paket berhasil dilakukan.',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function ListPaket(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('page','<b>Page Number<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$perpage = $this->input->post('perpage');
			$limit = $perpage * $this->input->post('page');
			// get list
			$list 	= $this->model_api->get_index_daftar_paket($limit, $company_id, $personal_id);
			// filter
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Data daftar paket berhasil ditemukan.',
					'last_page' => true,
					'data' 		=> $list
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data daftar paket tidak ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	// paket id
	function _ck_paket_id($paket_id){
		$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
		if( ! $this->model_api->check_paket_id( $company_id, $paket_id ) ) {
			$this->form_validation->set_message('_ck_paket_id', 'Paket ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// detail info paket
	function DetailInfoPaket(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('id', '<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$paket_id = $this->input->post('id');
			// get data paket
			$data_paket = $this->model_api->get_paket_info( $company_id, $personal_id, $paket_id );
			// filter
			if ( count( $data_paket ) > 0) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Data daftar paket berhasil ditemukan.',
					'paket_name' => $data_paket['paket_name'],
					'kode' => $data_paket['kode'], 
					'departure_date' => $this->date_ops->change_date($data_paket['departure_date']),
					'description' => $data_paket['description'],
					'number_member' => $data_paket['number_member'],
					'status' => ( $data_paket['departure_date'] <= date('Y-m-d') ? "Sudah Berangkat" : "Belum Berangkat" ),
					'price' => 'Rp '.number_format($data_paket['price']).',-'
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data daftar paket tidak ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// riwayat transaksi
	function RiwayatTransaksi(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('page','<b>Page Number<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$perpage = $this->input->post('perpage');
			$limit = $perpage * $this->input->post('page');
			// get list
			$list 	= $this->model_api->get_index_riwayat_transaksi($limit, $company_id, $personal_id);
			// filter
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Daftar Riwayat Transaksi Berhasil ditemukan.',
					'last_page' => true,
					'data' 		=> $list
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar Riwayat Transaksi Gagal ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function ListNotif(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('page','<b>Page Number<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$perpage = $this->input->post('perpage');
			$limit = $perpage * $this->input->post('page');
			// get list
			$list 	= $this->model_api->get_index_notif($limit, $company_id, $personal_id);
			// filter
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Daftar Riwayat Transaksi Berhasil ditemukan.',
					'last_page' => true,
					'data' 		=> $list
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar Riwayat Transaksi Gagal ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function _ck_message_id($id){
		$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
		$company_id = $company_id_personal_id['company_id'];
		if( ! $this->model_api->check_message_id( $company_id, $id ) ) {
			$this->form_validation->set_message('_ck_message_id', 'Message ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// detail notif
	function DetailNotif(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('id', '<b>ID Message<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_message_id');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			$message_id = $this->input->post('id');
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			# get 
			$data = $this->model_api->get_detail_message($company_id, $message_id);
			// filter
			if ( count( $data ) > 0 ) {
				// update
				$this->model_api_cud->update_notif($message_id, $company_id, $personal_id);
				# return
				$return = array(
					'error'		=> false,
					'error_msg' => 'Data Detail Notif Berhasil ditemukan.',
					'data' 		=> $data
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data Detail Notif Gagal ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// code perusahaan
	function _ck_code_perusahaan_exist($codeCompany)
	{
		if (!$this->model_api->check_company_code($codeCompany)) {
			$this->form_validation->set_message('_ck_code_perusahaan_exist', 'Kode perusahaan tidak ditemukan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// check no whatsapp
	function _ck_no_whatsapp($no_whatsapp)
	{
		// if (substr($no_whatsapp, 0, 1) == '0') {
		// 	$no_whatsapp = '62' . substr($no_whatsapp, 1);
		// }
		if (!$this->model_api->check_nomor_wa_by_company_code($no_whatsapp, $this->input->post('company_code'))) {
			$this->form_validation->set_message('_ck_no_whatsapp', 'Nomor whatsapp tidak ditemukan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	

	function _get_otp()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('noWA',	'<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_no_whatsapp');
		/*
	    	Validation process
	    */
		if ($this->form_validation->run()) {

			$this->load->library('smsgateway');

			$company_code = $this->input->post('company_code');
			$nomor_whatsapp = $this->input->post('noWA');
			$sub1 = substr($nomor_whatsapp, 0, 2);
			$sub2 = substr($nomor_whatsapp, 0, 1);
			if ($sub1 == '62') {
				$nomor_whatsapp = $this->input->post('nomor_whatsapp');
			} elseif ($sub2 == '0') {
				$nomor_whatsapp = '62' . substr($nomor_whatsapp, 1);
			} else {
				$error = 1;
				$error_msg = 'Format Nomor Whatsapp tidak valid';
			}
			// $otp = $this->model_api->gen_otp_member( $nomor_whatsapp, $company_code );
			$otp = '123456';
			# filter error before sending otp
			if ($error == 0) {
				$this->smsgateway->send_otp($otp, $nomor_whatsapp);
			}
			# total
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'OTP Berhasil dikirimkan.',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'OTP Gagal dikirimkan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// token validation
	function _token_validation()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token',	'<b>Token<b>', 'trim|required|min_length[1]');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			$company_code = $this->input->post('company_code');
			$token = $this->input->post('token');
			// check token is valid
			$token_info = $this->model_api->check_token_info($token, $company_code);
			// error
			if ($token_info['error'] == false) {
				if (!$this->session->userdata($this->config->item('apps_name')) and (!$this->session->userdata($this->config->item('apps_name'))['user_id'])) {
					print("masuk kalang");
					$feedBack = $this->model_api->get_info_akun_by_token($token, $company_code);

					$this->session->set_userdata(array($this->config->item('apps_name') => array(
						'user_id' => $feedBack['personal_id'],
						'company_id' => $feedBack['company_id'],
						'fullname' => $feedBack['fullname'],
						'start_date_subscribtion' => $feedBack['start_date_subscribtion'],
						'end_date_subscribtion' => $feedBack['end_date_subscribtion']
					)));
					$this->output->set_header('Cookie: PHPSESSID=' . $this->session->session_id . '; ci_session=' . $this->session->session_id . ';');
				}
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses login berhasil dilakukan.',
					'token' => $token,
					'csrf_name' => $this->security->get_csrf_token_name(),
					'csrf_code' => $this->security->get_csrf_hash(),
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $token_info['error_msg'],
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	// check ppob
	function _ck_ppob($kode_produk){
		$type = $this->input->post('type');
		$category = $this->input->post('category');
		if ( ! $this->model_api->check_category_ppob( $type, $category ) ) {
			$this->form_validation->set_message('_ck_ppob', 'Kategori tidak ditemukan.');
			return FALSE;
		} else {
			if( $type == 'prabayar' ) {
				$nomor_tujuan = $this->input->post('nomor_tujuan');
				$arr = array('PIU', 'PT', 'PI', 'PD', 'PTP', 'PS');
				if ( in_array( $category, $arr ) ) {
				  	$prefix = substr($nomor_tujuan,0,4);
					if ( ! $this->model_api->check_kode_product_ppob( $type, $category, $prefix, $kode_produk ) ) {
						$this->form_validation->set_message('_ck_ppob', 'Terdapat kesalahan pada kode produk / nomor tujuan.');
						return FALSE;
					} else {
						return TRUE;
					}
				}else{
					return TRUE;
				}
			} else {
				return TRUE;
			}
		}
	}

	// send
	function _send($url){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->config->item('serpul_main_url').$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Authorization: '. $this->config->item('serpul_api_key')
			),
			)
		);
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	}

	// send transaction
	function _send_transaction($url){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->config->item('serpul_main_url').$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET')
		);
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	}

	// send order
	function _send_order($destination, $kode_produk, $ref_id){
      	$curl = curl_init();
      	curl_setopt_array($curl, array(
	        CURLOPT_URL => $this->config->item('serpul_main_url').'/prabayar/order',
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_ENCODING => '',
	        CURLOPT_MAXREDIRS => 10,
	        CURLOPT_TIMEOUT => 0,
	        CURLOPT_FOLLOWLOCATION => true,
	        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	        CURLOPT_CUSTOMREQUEST => 'POST',
	        CURLOPT_POSTFIELDS => 'destination='.$destination.'&product_id='.$kode_produk.'&ref_id='.$ref_id,
	        CURLOPT_HTTPHEADER => array(
	          	'Accept: application/json',
	          	'Authorization: '. $this->config->item('serpul_api_key')
	        ),
	    ));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
   	}

   	// check category ppob
   	function _ck_category_ppob( $category ) {
   		$type = $this->input->post('type');
   		$arr = array('UDLA', 'UDOVO', 'UDBRI', 'UDGP', 'UDDNA');
   		if( in_array( $category, $arr ) ){
   			return TRUE;
   		}else{
   			if( ! $this->model_api->check_category_ppob_2( $type, $category ) ) {
	   			$this->form_validation->set_message('_ck_category_ppob', 'Kategory tidak ditemukan.');
	   			return FALSE;
	   		}else{
	   			return TRUE;
	   		}	
   		}
   	}

   	// check nomor tujuan ppob
   	function _ck_nomor_tujuan_ppob( $nomor_tujuan ) {
   		$not_in = array('TL', 'UDLA', 'UDOVO', 'UDBRI', 'UDGP', 'UDDNA');
   		$type = $this->input->post('type');
   		$category = $this->input->post('category');
		if ( ! in_array( $category, $not_in ) ) {
			if ( $type == 'prabayar' ) {
				$prefix = substr($nomor_tujuan,0,4);
				if ( ! $this->model_api->check_nomor_tujuan_ppob( $category, $prefix )  ) {
					$this->form_validation->set_message('_ck_nomor_tujuan_ppob', 'Nomor tujuan tidak ditemukan.');
					return FALSE;
				} else {
					return TRUE;
				}
	   		} else {
				return TRUE;
	   		}
		} else {
			return TRUE;
		}
   	}

   	// check nomor pulsa reguler
   	function CheckNomorPulsaRegulerUrl(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token',	'<b>Token<b>', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('type', '<b>Tipe<b>', 'trim|required|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
		$this->form_validation->set_rules('category', '<b>Category Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_category_ppob');
		$this->form_validation->set_rules('nomor_tujuan', '<b>Nomor Tujuan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_tujuan_ppob');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			// filter
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => "Gagal",
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
   	}

   	// get list product ppob
   	function ListProductPPOB(){
   		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token',	'<b>Token<b>', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('type', '<b>Tipe<b>', 'trim|required|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
		$this->form_validation->set_rules('category', '<b>Category Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_category_ppob');
		$this->form_validation->set_rules('nomor_tujuan', '<b>Nomor Tujuan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_tujuan_ppob');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$type = $this->input->post('type');
			$category = $this->input->post('category');
			$nomor_tujuan = $this->input->post('nomor_tujuan');
			$mark_up = $this->model_api->get_markup_all_product($type);
			$get_markup_company = $this->model_api->get_company_markup($company_id);
			# feedBack
			$feedBack = array();
			// prabayar
			if( $type == 'prabayar' ) {
				$get_operator_code = $this->model_api->get_operator_code($category, $nomor_tujuan);
				if( count($get_operator_code) > 0 ) {
					// daftar produk ppob
					$feedBack = $this->model_api->get_list_produk_ppob($get_operator_code, $get_markup_company);
				}else{
					$error = 1;
					$error_msg = 'Kode operator tidak ditemukan.';
				}
			}
			// filter
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $feedBack
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
   	}


   	function _ck_nomor_tujuan_exist_today($value){
   		$token = $this->input->post('token');
		$company_code = $this->input->post('company_code');
		$cp_info = $this->model_api->get_company_id_personal_id( $token, $company_code );
		$company_id = $cp_info['company_id'];
		$personal_id = $cp_info['id'];

  		if ( $this->model_api->check_nomor_tujuan_exist_today($value, $personal_id, $company_id) ) {
			$this->form_validation->set_message('_ck_nomor_tujuan_exist_today', 'Nomor tujuan sudah pernah diisi hari ini.');
			return FALSE; 
		} else {
			return TRUE;
		}
   	}

	// transaksi PPOB
	function TransaksiPPOB(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('token',	'<b>Token<b>', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('type', '<b>Tipe<b>', 'trim|required|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
		$this->form_validation->set_rules('category', '<b>Category<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_tujuan', '<b>Nomor Tujuan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_tujuan_exist_today');
		$this->form_validation->set_rules('kode_produk', '<b>Kode Produk<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_ppob');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {

			// receive data
			$type = $this->input->post('type');
			$category = $this->input->post('category');
			$nomor_tujuan = $this->input->post('nomor_tujuan');
			$kode_produk = $this->input->post('kode_produk');
			$token = $this->input->post('token');
			$company_code = $this->input->post('company_code');
			$gen_transction_number = '';
			// info product
			$ip = $this->model_api->get_info_product( $kode_produk );
			$ip_id = $ip['id'];
			$ip_product_code = $ip['product_code'];
			$ip_product_name = $ip['product_name'];
			$ip_price = $ip['price'];
			$ip_markup_amra = $ip['markup_amra'];
			$ip_status = $ip['status'];
			$ip_server = $ip['server'];

			if( $ip_status == 'active' ) {
				if( $ip_server != 'none' ) {
					// ambil personal id dan perusahaan id
					$cp_info = $this->model_api->get_company_id_personal_id( $token, $company_code );
					$company_id = $cp_info['company_id'];
					$personal_id = $cp_info['id'];

					// ambil daftar seluruh markup produk oleh travel
					$list_markup_perusahaan = $this->model_api->get_company_markup($company_id);
					// tentukan markup produk oleh travel
					$markup_produk_travel = ( isset( $list_markup_perusahaan[$kode_produk] ) ? $list_markup_perusahaan[$kode_produk] : $list_markup_perusahaan['all']);
					
					$harga_jual_amra = 0;
					$harga_server = 0;
					// check harga ke server h2h
					if( $ip_server == 'iak') { 
						// check harga ke iak
						$harga_status_iak = $this->iak->check_product_exist_in_server($kode_produk); // yang dikeluarkan list harga dan status
						if( count( $harga_status_iak) != 0  ) {
							if( $harga_status_iak['status'] == 'active' ) { 
								// tentukan harga jual oleh amra 
								$harga_jual_amra = $ip_markup_amra + $harga_status_iak['product_price']; // penjumlahan markup amra dengan harga beli diserver
								// harga server
								$harga_server = $harga_status_iak['product_price'];
							}else{
								$error = 1;
								$error_msg = 'Produk sedang kosong.'; 
							}
						}else{
							$error = 1;
							$error_msg = 'Produk tidak tersedia.'; 
						}
					}elseif( $ip_server == 'tripay' ) {
						// check harga ke tripay
						$harga_status_tripay = $this->tripay->check_product_exist_in_server($kode_produk);
						if( count( $harga_status_tripay) != 0  ) {
							// tentukan harga jual oleh amra 
							if( $harga_status_tripay['status'] == 'active' ) { 
								// tentukan harga jual oleh amra 
								$harga_jual_amra = $ip_markup_amra + $harga_status_tripay['product_price']; // penjumlahan markup amra dengan harga beli diserver
								// harga server
								$harga_server = $harga_status_tripay['product_price'];
							}else{
								$error = 1;
								$error_msg = 'Produk sedang kosong.'; 
							}
						}else{
							$error = 1;
							$error_msg = 'Produk tidak tersedia.'; 
						}
					}else{
						$error = 1;
						$error_msg = 'Server tidak ditemukan.';
					}

					if( $error == 0 AND $harga_jual_amra != 0 ) {
						// ambil saldo terakhir perusahaan 
						$saldo_perusahaan = $this->model_api->get_saldo_perusahaan( $company_id );
						// filter saldo perusahaan
						if( $saldo_perusahaan >= $harga_jual_amra ){
							// menghitung harga jual travel
							$harga_jual_travel = $harga_jual_amra + $markup_produk_travel;
							// ambil info tabungan member / jamaah
							$info_deposit_tabungan = $this->model_api->info_deposit_tabungan($company_id, $personal_id);
							// check deposit member
			 				if ( $info_deposit_tabungan['deposit'] >= $harga_jual_travel ) {
			 					// get nomor transaksi
			                	$nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo_api($company_id);
			                	// generate transaction number
			                	$gen_transction_number = $this->model_api->gen_transction_number_ppob();

								$trxid = '';
			                	if( $ip_server == 'iak') { 
			                		// topup process	
			                		$topUP = $this->iak->topUp($gen_transction_number, $kode_produk, $nomor_tujuan );
			                		if ( isset( $topUP->data->status )  ) {
										if( $topUP->data->status != 0  ) {
											$error = 1;
											$error_msg = $topUP->data->message;
										}
									}else{
										$error = 1;
										$error_msg = 'Internal Server Error';
									}

			                	}elseif( $ip_server == 'tripay' ) {
			                		// topup process	
			                		$topUP = $this->tripay->topUp($gen_transction_number, $kode_produk, $nomor_tujuan );
			                		if ( isset( $topUP->success )  ) {
			                			if( $topUP->success == true ) {
			                				$trxid = $topUP->trxid;
			                			}else{
											$error = 1;
											$error_msg = $topUP->message;
										}
			                		}else{
			                			$error = 1;
										$error_msg = 'Internal Server Error';
			                		}
			                	}
			                	// filter error
			                	if( $error == 0 ) {

			                		# receive data
									$data = array();

									$data['ppob_transaction_history']['transaction_code'] = $gen_transction_number;
									$data['ppob_transaction_history']['product_code'] = $kode_produk;
									$data['ppob_transaction_history']['nomor_tujuan'] = $nomor_tujuan;
									$data['ppob_transaction_history']['server'] = $ip['server'];
									$data['ppob_transaction_history']['server_price'] = $harga_server; // harga produk server
									$data['ppob_transaction_history']['application_price'] = $harga_jual_amra;
									$data['ppob_transaction_history']['status'] = 'process';
									$data['ppob_transaction_history']['trxid'] = $trxid;
									$data['ppob_transaction_history']['created_at'] = date('Y-m-d H:i:s');

									$data['ppob_transaction_history_company']['company_id'] = $company_id;
									$data['ppob_transaction_history_company']['personal_id'] = $personal_id;
									$data['ppob_transaction_history_company']['company_markup'] = $markup_produk_travel;
									$data['ppob_transaction_history_company']['company_price'] = $harga_jual_travel;
									$data['ppob_transaction_history_company']['created_at'] = date('Y-m-d H:i:s');

									$data['deposit_transaction']['nomor_transaction'] = $nomor_transaksi;
									$data['deposit_transaction']['personal_id'] = $personal_id;
									$data['deposit_transaction']['company_id'] = $company_id;
									$data['deposit_transaction']['debet'] = 0;
									$data['deposit_transaction']['kredit'] = $harga_jual_travel;
									$data['deposit_transaction']['approver'] = 'self';
									$data['deposit_transaction']['sumber_dana'] = 'cash';
									$data['deposit_transaction']['no_tansaksi_sumber_dana'] = '';
									$data['deposit_transaction']['transaction_requirement'] = 'deposit';
									$data['deposit_transaction']['info'] = 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$gen_transction_number;
									$data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
									$data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');

									// jurnal
									$data['jurnal']['company_id'] = $company_id;
									$data['jurnal']['source'] = 'ppob:transaction_code:'.$gen_transction_number;
									$data['jurnal']['ref'] = 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$gen_transction_number;
									$data['jurnal']['ket'] = 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$gen_transction_number;
									$data['jurnal']['akun_debet'] = '23000';
									$data['jurnal']['akun_kredit'] = '11010';
									$data['jurnal']['saldo'] = $harga_jual_amra;
									$data['jurnal']['periode_id'] = '0';
									$data['jurnal']['input_date'] = date('Y-m-d H:i:s');
									$data['jurnal']['last_update'] = date('Y-m-d H:i:s');

									$data['company']['saldo'] = $saldo_perusahaan - $harga_jual_amra;

									$data['company_saldo_transaction']['company_id'] = $company_id;
									$data['company_saldo_transaction']['saldo'] = $harga_jual_amra;
									$data['company_saldo_transaction']['request_type'] = 'pruchase';
									$data['company_saldo_transaction']['ket'] = 'PPOB:transaction_code:'.$gen_transction_number.'';
									$data['company_saldo_transaction']['status'] = 'accepted';
									$data['company_saldo_transaction']['input_date'] = date('Y-m-d H:i:s');
									$data['company_saldo_transaction']['last_update'] = date('Y-m-d H:i:s');

									// insert to database
									if ( ! $this->model_api_cud->insert_ppob_transaction( $company_id, $data ) ) {
										$error = 1;
										$error_msg = 'Proses simpan data transaksi ppob gagal dilakukan.';
									}
			                	}
			 				} else {
			 					$error = 1;
		            	 		$error_msg = 'Saldo deposit anda tidak mencukupi untuk melakukan transaksi ini.';
			 				}
						}else{
							$error = 1;
		            	 	$error_msg = 'Saldo perusahaan tidak mencukupi.';
						}
					}

				}else{
					$error = 1;
	            	$error_msg = 'Server tidak ditemukan.';
				}				
			}else{
				$error = 1;
	            $error_msg = 'Produk tidak ditemukan.';
			}
            # total
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Berhasil.',
					'data' => $gen_transction_number
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
				);
			}
		} else {
			if ( validation_errors() ) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	function Callback_PPOB(){
		$ip = (@$_SERVER['HTTP_X_FORWARDED_FOR']=='') ? @$_SERVER['REMOTE_ADDR'] : @$_SERVER['HTTP_X_FORWARDED_FOR']; 
		// record ip
		// $this->model_api_cud->record_ip_insert($ref_id);
		$this->model_api_cud->record_ip_insert($ip);
		// filter
	    if($ip=='174.138.26.227'){ // memastikan data terikirim dari server serpul.co.id
	        $ref_id = $this->input->get('ref_id');
			$product_id = $this->input->get('product_id');
			$product_name = $this->input->get('product_name');
			$price = $this->input->get('price');
			$serial_number = $this->input->get('serial_number');
			$message = $this->input->get('message');
			$status = $this->input->get('status');
			$balance = $this->input->get('balance');
			// ref id
			if( $this->model_api->check_ref_id( $ref_id, $product_id ) ) {
				if( $status != 'SUCCESS' ) {
					// delete deposit and jurnal
					$this->model_api_cud->delete_deposit_and_jurnal_ppob($ref_id);
				}
				$data_update = array();
				$data_update['status'] = $status == 'SUCCESS' ? 'success' : 'failed';
				$this->model_api_cud->update_status_transaksi_ppob($ref_id, $data_update);
			}
	    }
	}

	function ListRiwayatPPOB(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('page','<b>Page Number<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$perpage = $this->input->post('perpage');
			$limit = $perpage * $this->input->post('page');
			// get list
			$list 	= $this->model_api->get_index_riwayat_ppob($limit, $company_id, $personal_id);
			// filter
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Data daftar paket berhasil ditemukan.',
					'last_page' => true,
					'data' 		=> $list
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data daftar paket tidak ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}


	function _ck_kode_transaksi($kode_transaksi){
		// personal id
		$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
		// check kode transaksi
		if( ! $this->model_api->check_kode_transaksi( $kode_transaksi, $company_id_personal_id['company_id'] ) ){
			$this->form_validation->set_message('_ck_kode_transaksi', 'Kode transaksi tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function unit_test(){

   		// $list = $this->model_api->info_deposit_tabungan(1, 15);

   		// $list = $this->tripay->check_product_exist_in_server('PIUTSL5');

		// $list = $this->tripay->check_product_exist_in_server('PIUTSL5');   		

   		// echo "<br>=====<br>";
   		// print_r($list);
   		// echo "<br>=====<br>";

   		// $get_info_transaksi = $this->model_api->get_info_transaksi('213191', '2');
   		// $check_status = $this->tripay->check_status_transaksi('213191');

   		// // print_r($check_status);
   		// print("<pre>".print_r($check_status,true)."</pre>");

   		// print_r($check_status->success);
   		// echo "<br>";
   		// print_r($check_status->data->status);

   		// print_r($get_info_transaksi);



   	}

	function detailTransaksiPPOB(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('transaction_code', '<b>Kode Transaksi<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_kode_transaksi');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') ); ##
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			$transaction_code = $this->input->post('transaction_code');
			$get_info_transaksi = $this->model_api->get_info_transaksi($transaction_code, $company_id); ##

			$feedBack = array();
			$feedBack['transaction_code'] = $transaction_code;
			$feedBack['product_code'] = $get_info_transaksi['product_code'];
			$feedBack['nomor_tujuan'] = $get_info_transaksi['nomor_tujuan'];
			$feedBack['price'] = 'Rp '.number_format($get_info_transaksi['company_price']);
			// 
			if( $get_info_transaksi['status'] == 'process') {
				// update status
				if( $get_info_transaksi['server'] == 'iak' ) {
					$check_status = $this->iak->check_status_transaksi($transaction_code);
					
					if( $get_info_transaksi['category_code'] == 'TL' ) {
						// check
						if ( isset( $check_status->data->status ) AND $check_status->data->status == 1  ) {
							$feedBack['pesan'] = $check_status->data->sn;
							$feedBack['status'] = 'Sukses';
						} else if( isset( $check_status->data->status ) AND $check_status->data->status == 2 ) {
							$feedBack['pesan'] = 'Pembelian '.$get_info_transaksi['product_code'].' ke '.$get_info_transaksi['nomor_tujuan'].' Gagal dilakukan';
							$feedBack['status'] = 'Gagal';
							// saldo company now
	                        $saldo_company_now = $this->model_api->get_saldo_company_now($company_id);
	                        # get saldo
	                        $get_back_saldo = $saldo_company_now + $get_info_transaksi['application_price'];
	                        $feedBack['get_back_saldo'] = $get_back_saldo;
						}else{
							$feedBack['pesan'] = 'Proses';
							$feedBack['status'] = 'Proses';
						}
					}else{
						if ( isset( $check_status->data->status ) AND $check_status->data->status == 1  ) {
							$feedBack['pesan'] = 'Pembelian '.$get_info_transaksi['product_code'].' ke '.$get_info_transaksi['nomor_tujuan'].' Berhasil dilakukan';
							$feedBack['status'] = 'Sukses';
						} else if( isset( $check_status->data->status ) AND $check_status->data->status == 2 ) {
							$feedBack['pesan'] = 'Pembelian '.$get_info_transaksi['product_code'].' ke '.$get_info_transaksi['nomor_tujuan'].' Gagal dilakukan';
							$feedBack['status'] = 'Gagal';
							// saldo company now
	                        $saldo_company_now = $this->model_api->get_saldo_company_now($company_id);
	                        # get saldo
	                        $get_back_saldo = $saldo_company_now + $get_info_transaksi['application_price'];
	                        $feedBack['get_back_saldo'] = $get_back_saldo;
						}else{
							$feedBack['pesan'] = 'Proses';
							$feedBack['status'] = 'Proses';
						}
					}
					// filter
					if ( $this->model_api_cud->update_status_ppob( $feedBack, $company_id  ) ) {
						$return = array(
							'error'		=> false,
							'error_msg' => 'Data transaksi berhasil ditemukan.',
							'data' 		=> $feedBack
						);
					} else {
						$return = array(
							'error'	=> true,
							'error_msg' => 'Data transaksi tidak ditemukan.',
						);
					}
				} else if( $get_info_transaksi['server'] == 'tripay' ) {

					$check_status = $this->tripay->check_status_transaksi($transaction_code);

					$feedBack = array();
					$feedBack['transaction_code'] = $transaction_code;
					$feedBack['product_code'] = $get_info_transaksi['product_code'];
					$feedBack['nomor_tujuan'] = $get_info_transaksi['nomor_tujuan'];
					$feedBack['price'] = 'Rp '.number_format($get_info_transaksi['company_price']);
					if( $get_info_transaksi['category_code'] == 'TL' ) {
						if ( isset( $check_status->success ) AND $check_status->success == 1  ) {
							if ( isset( $check_status->data->status ) AND $check_status->data->status == 1 ) { // sukses
								$feedBack['pesan'] = $check_status->data->note;
								$feedBack['status'] = 'Sukses';
							} else if ( isset( $check_status->data->status ) AND ( $check_status->data->status == 2 || $check_status->data->status == 3 ) ) {
								$feedBack['pesan'] = 'Pembelian '.$get_info_transaksi['product_code'].' ke '.$get_info_transaksi['nomor_tujuan'].' Gagal dilakukan';
								$feedBack['status'] = 'Gagal';
								// saldo company now
	                            $saldo_company_now = $this->model_api->get_saldo_company_now($company_id);
	                            # get saldo
	                            $get_back_saldo = $saldo_company_now + $get_info_transaksi['application_price'];
	                            $feedBack['get_back_saldo'] = $get_back_saldo;
							} else {
								$feedBack['pesan'] = 'Proses';
								$feedBack['status'] = 'Proses';
							}
						}
					}else{
						if ( isset( $check_status->data->status ) AND $check_status->data->status == 1 ) { // sukses
							$feedBack['pesan'] = 'Pembelian '.$get_info_transaksi['product_code'].' ke '.$get_info_transaksi['nomor_tujuan'].' Berhasil dilakukan';
							$feedBack['status'] = 'Sukses';
						} else if ( isset( $check_status->data->status ) AND ( $check_status->data->status == 2 || $check_status->data->status == 3 ) ) {
							$feedBack['pesan'] = 'Pembelian '.$get_info_transaksi['product_code'].' ke '.$get_info_transaksi['nomor_tujuan'].' Gagal dilakukan';
							$feedBack['status'] = 'Gagal';
							// saldo company now
	                        $saldo_company_now = $this->model_api->get_saldo_company_now($company_id);
	                        # get saldo
	                        $get_back_saldo = $saldo_company_now + $get_info_transaksi['application_price'];
	                        $feedBack['get_back_saldo'] = $get_back_saldo;
						} else {
							$feedBack['pesan'] = 'Proses';
							$feedBack['status'] = 'Proses';
						}
					}
					// 
					if ( $this->model_api_cud->update_status_ppob( $feedBack, $company_id ) ) {
						$return = array(
							'error'		=> false,
							'error_msg' => 'Data transaksi berhasil ditemukan.',
							'data' 		=> $feedBack
						);
					} else {
						$return = array(
							'error'	=> true,
							'error_msg' => 'Data transaksi tidak ditemukan.',
						);
					}
				}else{
					$return = array(
						'error'	=> true,
						'error_msg' => 'Server tidak ditemukan.',
					);
				}

			}else{
				$feedBack['pesan'] = $get_info_transaksi['pesan'];
				$feedBack['status'] = $get_info_transaksi['status'] == 'failed' ? 'Gagal' : 'Sukses' ;
				$return = array(
					'error'		=> false,
					'error_msg' => 'Data transaksi berhasil ditemukan.',
					'data' 		=> $feedBack
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}

	function callback_tripay(){
		$secret = $this->tripay->tripay_callback_key();
		$incomingSecret = isset($_SERVER['HTTP_X_CALLBACK_SECRET']) ? $_SERVER['HTTP_X_CALLBACK_SECRET'] : '';
		if( hash_equals($secret, $incomingSecret) ) {
			$jsonArray = json_decode(file_get_contents('php://input'),true)[0];
			$trxid = $jsonArray['trxid'];
			$transaction_code = $jsonArray['api_trxid'];
			$harga = $jsonArray['harga'];
			$status = $jsonArray['status']; // 0 proses / 1 berhasil / 2 gagal
			$note = $jsonArray['note'];
			$check = $this->model_api->get_info_transaksi_by_id_transaksi($transaction_code, $trxid);
			// echo $status;
			if( $status != 0 ) {
				// check keberadaan
				if( $check['status'] == 'ada' ) {
					echo "masuk";
					$feedBack = array();
					$feedBack['transaction_code'] = $transaction_code;
					if ( $status == 1 ) { // sukses
						if( $check['category_code'] == 'TL'){
							$feedBack['pesan'] = $note;
						}else{
							$feedBack['pesan'] = 'Pembelian '.$check['product_code'].' ke '.$check['nomor_tujuan'].' Berhasil dilakukan';
						}
						
						$feedBack['status'] = 'Sukses';
					} else if ( $status == 2 )  {
						$feedBack['pesan'] = 'Pembelian '.$check['product_code'].' ke '.$check['nomor_tujuan'].' Gagal dilakukan';
						$feedBack['status'] = 'Gagal';
						// saldo company now
	                    $saldo_company_now = $this->model_api->get_saldo_company_now($check['company_id']);
	                    # get saldo
	                    $get_back_saldo = $saldo_company_now + $check['application_price'];
	                    $feedBack['get_back_saldo'] = $get_back_saldo;
					} 
					// update status ppob
					$this->model_api_cud->update_status_ppob( $feedBack, $check['company_id'] );
				}
			}
		}
	}

	function callback_iak_ppob(){
		$return	= array();
		$error = 0;
		$error_msg = 'Berhasil';
		$param = $this->input->get('param');
		if( $param == $this->iak->iak_callback_key() ) {
			$jsonArray = json_decode(file_get_contents('php://input'),true); 
			$data = $jsonArray['data'];
			// get transaction
			$transaction = $this->model_api->get_transaction($data['ref_id']);
			// count transaction
			if( count( $transaction ) > 0 ) {
				if ( isset( $data['status'] ) AND $data['status'] == 1  ) {
	                $d = array();
	                $d['status'] = 'success';
	                if ( isset( $data['sn'] ) ) {
                         $d['ket'] = $data['sn'];
                    }else{
                         $d['ket'] = 'Pembelian ' . $transaction['product_code'] . ' ke ' . $transaction['nomor_tujuan'] . ' Berhasil dilakukan';
                    }
	                // update
	                if( ! $this->model_api->update_status_transaksi_ppob( $data['ref_id'], $d ) ) {
	                    $error = 1;
	                    $error_msg = 'Proses update status ppob gagal dilakukan.';
	                }
	            } else if ( isset( $data['status'] ) AND $data['status'] == 2 ) {
	                $d = array();
	                $d['status'] = 'failed';
	                $d['ket'] = 'Pembelian ' . $transaction['product_code'] . ' ke ' . $transaction['nomor_tujuan'] . ' Gagal dilakukan';
	                // update data
	                if( $this->model_api->update_status_transaksi_ppob( $data['ref_id'], $d ) ) {
	                    // info saldo
	                    $is = $this->model_api->get_info_saldo_by_ref_id( $data['ref_id'] );
	                    if ( count ($is)  > 0 ) {
	                    	if( isset( $is['company_id'] ) ) {
	                    		$get_back_saldo = $is['company_saldo'] + $transaction['application_price'];
	                    		// delete failed ppob transaction company
	                    		$this->model_api_cud->delete_failed_ppob_transaction_company( $is['company_id'], $data['ref_id'], $get_back_saldo );
	                    	}else if ( isset( $is['costumer_id'] ) ) {
	                    		$get_back_saldo = $is['costumer_saldo'] + $transaction['application_price'];
	                    		// delete failed ppob transaction costumer
	                    		$this->model_api_cud->delete_failed_ppob_transaction_costumer( $is['costumer_id'], $get_back_saldo );
	                    	}
	                    }
	                };
	            }
			}else{
				$error == 1;
				$error_msg = 'Ref Id tidak ditemukan.';
			}
		}else{
			$error == 1;
			$error_msg = 'Unauthorized';
		}
		// filter
		if ( $error == 0 ) {
			$return = array(
				'error'		=> false,
				'error_msg' => $error_msg,
			);
		} else {
			$return = array(
				'error'	=> true,
				'error_msg' => $error_msg,
			);
		}
		echo json_encode($return);
	}


	function _ck_kode_produk_pascabayar($kode_produk){
		$category = $this->input->post('category');
		if( ! $this->model_api->check_kode_produk_pascabayar( $kode_produk, $category ) ){
			$this->form_validation->set_message('_ck_kode_produk_pascabayar', 'Kode Produk tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _ck_kode_operator($kode_operator){
		if( ! $this->model_api->check_kode_operator_uang_digital( $kode_operator ) ){
			$this->form_validation->set_message('_ck_kode_operator', 'Kode Operator tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;	
		}
	}

	// list product uang digital
	function listProductUangDigital(){
		# return
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
		$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
		$this->form_validation->set_rules('kode_operator', '<b>Kode Operator<b>', 	'trim|required|xss_clean|min_length[1]callback__ck_kode_operator');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			// # get personal id
			$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
			$company_id = $company_id_personal_id['company_id'];
			$personal_id = $company_id_personal_id['id'];
			// kode operator UD
			$kode_operator_UD = $this->input->post('kode_operator');
			// get list uang digital
			// $get_list_uang_digital = $this->model_api->get_list_uang_digital( $kode_operator_UD, $company_id );

			$mark_up = $this->model_api->get_markup_all_product('prabayar');
			// $company_markup = $this->model_api->get_company_markup($company_id);
			$get_markup_company = $this->model_api->get_company_markup($company_id);
			
			$feedBack = array();
			$res_product = $this->_send('/prabayar/product?product_id='.$kode_operator_UD);
			if( $res_product->responseCode == 200 ) {
				$product =  $res_product->responseData;
				foreach ( $product as $row2 ) {
					$company_markup = ( isset( $get_markup_company[$row2->product_id] ) ? $get_markup_company[$row2->product_id] : $get_markup_company['all']);
					$feedBack[] = array('product_code' => $row2->product_id, 
										'product_name' => $row2->product_name, 
										'product_price' => 'Rp. '.number_format($row2->product_price + $mark_up[$row2->product_id] + $company_markup),
										'status' => $row2->status);
				}
          	}
                  	
			// filter
			if ( $error == 0 ) {
				$return = array(
					'error'		=> false,
					'error_msg' => 'Daftar uang digital berhasil ditemukan.',
					'data' 		=> $feedBack
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar uang digital tidak ditemukan.',
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg' => strip_tags(validation_errors()),
				);
			}
		}
		echo json_encode($return);
	}



	// // check nomor tujuan
	// function checkBillPascabayar(){
	// 	# return
	// 	$return 	= array();
	// 	$error 		= 0;
	// 	$error_msg = '';
	// 	$this->form_validation->set_rules('token', '<b>Token<b>', 'trim|required|xss_clean|min_length[1]|callback__check_token');
	// 	$this->form_validation->set_rules('company_code', '<b>Kode Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_perusahaan_exist');
	// 	$this->form_validation->set_rules('nomor_tujuan', '<b>Nomor Tujuan<b>', 	'trim|required|xss_clean|min_length[1]');
	// 	$this->form_validation->set_rules('category', '<b>Category Produk<b>', 	'trim|required|xss_clean|min_length[1]');
	// 	$this->form_validation->set_rules('product_code', '<b>Kode Produk<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_kode_produk_pascabayar');
	// 	/*
	//         Validation process
	//     */
	// 	if ( $this->form_validation->run() ) {
	// 		// # get personal id
	// 		$company_id_personal_id = $this->model_api->get_company_id_personal_id( $this->input->post('token'), $this->input->post('company_code') );
	// 		$company_id = $company_id_personal_id['company_id'];
	// 		$personal_id = $company_id_personal_id['id'];
	// 		$gen_transction_number = $this->model_api->gen_transction_number_ppob();
	// 		// 
	// 		$nomor_tujuan = $this->input->post('nomor_tujuan');
	// 		$category = $this->input->post('category');
	// 		$product_code = $this->input->post('product_code');
	// 		# feedBack
	// 		//$feedBack = $this->_check_bill_pascabayar( $nomor_tujuan, $product_code, $gen_transction_number );

	// 		$feedBack = $this->_get_info_pelanggan_pln( $nomor_tujuan );
	// 		// filter
	// 		if ( $error == 0 ) {
	// 			$return = array(
	// 				'error'		=> false,
	// 				'error_msg' => 'Data daftar paket berhasil ditemukan.',
	// 				'data' 		=> $feedBack
	// 			);
	// 		} else {
	// 			$return = array(
	// 				'error'	=> true,
	// 				'error_msg' => 'Data daftar paket tidak ditemukan.',
	// 			);
	// 		}
	// 	} else {
	// 		if (validation_errors()) {
	// 			// define return error
	// 			$return = array(
	// 				'error' => true,
	// 				'error_msg' => strip_tags(validation_errors()),
	// 			);
	// 		}
	// 	}
	// 	echo json_encode($return);
	// }

	// "company_code": _companyCode,
    //   "token": token,
    //   "kode_operator": kode_operator,



	// 
	function _check_bill_pascabayar( $nomor_tujuan, $product_code, $ref_id ) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
		 	CURLOPT_URL => $this->config->item('serpul_main_url').'/pascabayar/check',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'no_pelanggan='.$nomor_tujuan.'&product_id='.$product_code.'&ref_id='.$ref_id,
			CURLOPT_HTTPHEADER => array(
		        'Accept: application/json',
                'Authorization: '. $this->config->item('serpul_api_key')
		  	),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode( $response );
	}


	function _get_info_pelanggan_pln($nomor_pelanggan) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  	CURLOPT_URL => $this->config->item('serpul_main_url').'/checkplnprepaid?no_pelanggan='.$nomor_pelanggan,
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => '',
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 0,
		  	CURLOPT_FOLLOWLOCATION => true,
		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => 'GET',
		  	CURLOPT_HTTPHEADER => array(
		    	'Accept: application/json',
                'Authorization: '. $this->config->item('serpul_api_key')
		  	),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		// echo $response;
		return json_decode( $response );
	}
}

// $curl = curl_init();
//         curl_setopt_array($curl, array(
//             CURLOPT_URL => $this->config->item('serpul_main_url').'/prabayar/order',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'POST',
//             CURLOPT_POSTFIELDS => 'destination='.$destination.'&product_id='.$kode_produk.'&ref_id='.$ref_id,
//             CURLOPT_HTTPHEADER => array(
                // 'Accept: application/json',
                // 'Authorization: '. $this->config->item('serpul_api_key')
//             ),
//         ));
//         $response = curl_exec($curl);
//         curl_close($curl);
//         return json_decode($response);