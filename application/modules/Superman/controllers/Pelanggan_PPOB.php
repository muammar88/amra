<?php

/**
 *  -----------------------
 *	Pelanggan PPOB Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggan_PPOB extends CI_Controller
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
		$this->load->model('Model_pelanggan_ppob', 'model_pelanggan_ppob');
		# model ppob cud
		$this->load->model('Model_pelanggan_ppob_cud', 'model_pelanggan_ppob_cud');
		# checking is not Login
		$this->auth_library->Is_superman_not_login();
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_pelanggan_ppob(){
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
			$total 	= $this->model_pelanggan_ppob->get_total_daftar_pelanggan_ppob($search);
			$list 	= $this->model_pelanggan_ppob->get_index_daftar_pelanggan_ppob($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar pelanggan ppob tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar pelanggan ppob berhasil ditemukan.',
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


	function generated_code_new_pelanggan_ppob(){
		$error = 0;
		$error_msg = '';
		// get total perusahaan
		$kode_pelanggan = $this->model_pelanggan_ppob->generated_pelanggan_ppob();
		# filter
		if ( $kode_pelanggan == '' ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Kode pelanggan gagal dihasilkan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Kode pelanggan berhasil dihasilkan.',
				'data' => $kode_pelanggan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// id pelanggan
	function _ck_id_pelanggan(){
		if( $this->input->post('id') ) {
			if( ! $this->model_pelanggan_ppob->check_id_pelanggan( $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_id_pelanggan', 'ID pelanggan ppob tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	// kode pelanggan
	function _ck_kode_pelanggan($kode){
		if( $this->input->post('id') ) {
			if( $this->model_pelanggan_ppob->check_kode_pelanggan($kode, $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_kode_pelanggan', 'Kode pelanggan tidak tersedia.');
				return FALSE;	
			}else{
				return TRUE;
			}
		}else{
			if( $this->model_pelanggan_ppob->check_kode_pelanggan($kode) ) {
				$this->form_validation->set_message('_ck_kode_pelanggan', 'Kode pelanggan tidak tersedia.');
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}

	function _ck_nomor_whatsapp($nomor_whatsapp){
		if( $this->input->post('id') ) {
			if( $this->model_pelanggan_ppob->check_nomor_whatsapp( $nomor_whatsapp, $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_nomor_whatsapp', 'Nomor whatsapp sudah terdaftar dipangkalan data.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			if( $this->model_pelanggan_ppob->check_nomor_whatsapp( $nomor_whatsapp ) ) {
				$this->form_validation->set_message('_ck_nomor_whatsapp', 'Nomor whatsapp sudah terdaftar dipangkalan data.');
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}

	// pelanggan ppob
	function _ck_password_pelanggan_ppob(){
		if( ! $this->input->post('id') ){
			if( ! $this->input->post('password') ) {
				$this->form_validation->set_message('_ck_password_pelanggan_ppob', 'Password tidak boleh kosong.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	// proses add update ppob
	function proses_add_update_pelanggan_ppob(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Pelanggan<b>','trim|xss_clean|min_length[1]|numeric|callback__ck_id_pelanggan');
		$this->form_validation->set_rules('kode', '<b>Kode Pelanggan<b>','trim|required|xss_clean|min_length[1]|callback__ck_kode_pelanggan');
		$this->form_validation->set_rules('name', '<b>Nama Pelanggan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('whatsappnumber',	'<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_whatsapp');
		$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|xss_clean|min_length[1]|callback__ck_password_pelanggan_ppob');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$data = array();
			$data['name'] = $this->input->post('name');
			$data['whatsappnumber'] = $this->input->post('whatsappnumber');
			$data['updatedAt'] = date('Y-m-d H:i:s');
			if( $this->input->post('password') && $this->input->post('password') != '' ) {
				$data['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			if( $this->input->post('id') ) {
				if( ! $this->model_pelanggan_ppob_cud->update_pelanggan_ppob( $this->input->post('id'), $data ) ) {
					$error = 1;
					$error_msg = 'Proses update data pelanggan gagal dilakukan.';
				}else{
					$error_msg = 'Proses update data pelanggan berhasil dilakukan.';
				}
			}else{
				$data['code'] = $this->input->post('kode');
				$data['createdAt'] = date('Y-m-d H:i:s');
				if( ! $this->model_pelanggan_ppob_cud->insert_new_pelanggan_ppob( $data ) ) {
					$error = 1;
					$error_msg = 'Proses penambahan pelanggan baru gagal dilakukan.';
				}else{
					$error_msg = 'Proses penambahan pelanggan baru berhasil dilakukan.';
				}
			}
			// send feedback
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => $error_msg,
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


	// get edit info pelanggan ppob
	function get_edit_info_pelanggan_ppob(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Pelanggan<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_pelanggan');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get value
			$value = $this->model_pelanggan_ppob->get_value_pelanggan_ppob( $this->input->post('id') );
			// send feedback
			if( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => $error_msg,
					'data' => '',
					'value' => $value,
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

	// delete pelanggan ppob
	function delete_pelanggan_ppob(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Pelanggan<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_pelanggan');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// send feedback
			if( $this->model_pelanggan_ppob_cud->delete_pelanggan_ppob( $this->input->post('id') ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete pelanggan ppob berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete pelanggan ppob gagal dilakukan.',
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

	function _ck_saldo_not_empty($saldo){
		$sal = $this->text_ops->hide_currency( $saldo );
		if( $sal  <= 0 ){
			$this->form_validation->set_message('_ck_saldo_not_empty', 'Saldo yang ditambahkan tidak boleh nol.');
				return FALSE;
		}else{
			return TRUE;
		}
	}

	// tambahkan saldo pelanggan
	function tambah_saldo_pelanggan(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Pelanggan<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_pelanggan');
		$this->form_validation->set_rules('saldo',	'<b>Saldo<b>','trim|required|xss_clean|min_length[1]|callback__ck_saldo_not_empty');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get saldo
			$saldo = $this->text_ops->hide_currency( $this->input->post('saldo') );
			// last saldo
			$last_saldo =  $this->model_pelanggan_ppob->get_last_saldo( $this->input->post('id') );
			// data
			$data = array();
			$data['ppob_costumer']['saldo'] = $last_saldo + $saldo;
			$data['ppob_costumer']['updatedAt'] = date('Y-m-d H:i:s');
			$data['ppob_costumer_deposit_history']['ppob_costumer_id'] = $this->input->post('id');
			$data['ppob_costumer_deposit_history']['debet'] = $saldo;
			$data['ppob_costumer_deposit_history']['kredit'] = 0;
			$data['ppob_costumer_deposit_history']['ket'] = 'Melakukan penambahan saldo pelanggan';
			$data['ppob_costumer_deposit_history']['createdAt'] = date('Y-m-d H:i:s');
			$data['ppob_costumer_deposit_history']['updatedAt'] = date('Y-m-d H:i:s');
			// send feedback
			if( $this->model_pelanggan_ppob_cud->tambah_saldo_pelanggan( $this->input->post('id'), $data ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penambahan saldo pelanggan ppob berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses penambahan saldo pelanggan ppob gagal dilakukan.',
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

	// id riwayat deposit ppob
	function _ck_id_riwayat_deposit_ppob($id){
		if( ! $this->model_pelanggan_ppob->check_riwayat_deposit_ppob_id( $id ) ){
			$this->form_validation->set_message('_ck_id_riwayat_deposit_ppob', 'Riwayat ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// delete riwayat deposit ppob
	function deleteRiwayatDepositPPOB(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Riwayat Deposit PPOB<b>','trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_riwayat_deposit_ppob');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// value
			$value =  $this->model_pelanggan_ppob->get_value_riwayat_deposit_ppob( $this->input->post('id') );
			// send feedback
			if( $this->model_pelanggan_ppob_cud->delete_riwayat_deposit_ppob( $this->input->post('id'), $value ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penambahan saldo pelanggan ppob berhasil dilakukan.',
					'data' => $this->model_pelanggan_ppob->get_list_deposit_saldo( $value['costumer_id'] ),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses penambahan saldo pelanggan ppob gagal dilakukan.',
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


	function _ck_status_pelanggan( $status ) {
		$arr = array('semua', 'perusahaan', 'pelanggan');
		if ( in_array( $status, $arr ) ) {
			return TRUE;
		}else{
		  	$this->form_validation->set_message('_ck_status_pelanggan', 'Status pelanggan tidak ditemukan.');
			return FALSE;
		}
	}

	function daftar_transaksi_ppob(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('status',	'<b>Status<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_status_pelanggan');
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
			$total 	= $this->model_pelanggan_ppob->get_total_daftar_transaksi_ppob($search, $status);
			$list 	= $this->model_pelanggan_ppob->get_index_daftar_transaksi_ppob($perpage, $start_at, $search, $status);
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

}