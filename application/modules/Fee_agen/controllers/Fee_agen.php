<?php

/**
 *  -----------------------
 *	Fee Agen Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Fee_agen extends CI_Controller
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
		$this->load->model('Model_fee_agen', 'model_fee_agen');
		# model fee agen cud
		$this->load->model('Model_fee_agen_cud', 'model_fee_agen_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# company
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

   # daftar agen
   function daftar_agen(){
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
			$total 	= $this->model_fee_agen->get_total_agen($search);
			$list 	= $this->model_fee_agen->get_index_agen($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data agen berhasil ditemukan.',
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

	function _ck_agen_id($id){
		if( ! $this->model_fee_agen->check_agen_is_exist($id) ){
			$this->form_validation->set_message('_ck_agen_id', 'ID Agen tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function get_info_tambah_komisi(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Agen Id<b>', 	'trim|required|xss_clean|numeric|min_length[1]|callback__ck_agen_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# info agen id
			$data_agen = $this->model_fee_agen->get_info_agen( $this->input->post('id') );
			# filter
			if ( count($data_agen) > 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data agen berhasil ditemukan.',
					'data' => $data_agen,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen gagal ditemukan.',
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

	function _ck_not_null_currency( $komisi ){
		if( $this->text_ops->hide_currency($komisi) == 0 ){
			$this->form_validation->set_message('_ck_not_null_currency', 'Komisi tidak boleh NULL.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function tambah_fee_agen(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Agen Id<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_agen_id');
		$this->form_validation->set_rules('komisi', '<b>Komisi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_not_null_currency');
		$this->form_validation->set_rules('info', '<b>Info Komisi<b>', 'trim|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# info agen id
			$data_agen = $this->model_fee_agen->get_info_agen( $this->input->post('id') );
			# data
			$data = array();
			$data['transaction_number'] = $this->random_code_ops->number_transaction_detail_fee_keagenan();
			$data['company_id'] = $this->company_id;
			$data['agen_id'] = $this->input->post('id');
			$data['info'] = $this->input->post('info');
			$data['fee'] = $this->text_ops->hide_currency($this->input->post('komisi'));
			$data['level_agen_id'] = $data_agen['level_agen_id'];
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			# filter
			if ( $this->model_fee_agen_cud->insert_fee_agen( $data ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Komisi agen berhasil ditambahkan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Komisi agen gagal ditembahkan.',
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

	# riwayat komisi agen
	function riwayat_komisi_agen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Agen ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_agen_id');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$agen_id = $this->input->post('id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_fee_agen->get_total_riwayat_komisi_agen($search, $agen_id);
			$list 	= $this->model_fee_agen->get_index_riwayat_komisi_agen($perpage, $start_at, $search, $agen_id);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data agen berhasil ditemukan.',
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

	function _ck_riwayat_komisi_id($id){
		if( ! $this->model_fee_agen->check_riwayat_komisi_id( $id ) ) {
			$this->form_validation->set_message('_ck_riwayat_komisi_id', 'ID riwayat Komisi tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function delete_riwayat_komisi_agen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Riwayat Komisi Agen ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_riwayat_komisi_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get id
			$id = $this->input->post('id');
			# get agen id
			$agen_id = $this->model_fee_agen->get_agen_id_from_detail_fee_agen($id);
			# delete process
			if ( ! $this->model_fee_agen_cud->delete_riwayat_komisi_agen( $id ) ) {
				# return
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete riwayat komisi gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				# return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete riwayat komisi berhasil dilakukan.',
					'agen_id' => $agen_id,
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


	function get_info_pembayaran_fee_komisi_agen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Agen ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_agen_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get agen id
			$agen_id = $this->input->post('id');
			# get info
			$info_fee = $this->model_fee_agen->get_info_fee_agen( $agen_id );
			# agen info
			$agen_info = $this->model_fee_agen->get_info_agen($agen_id);
			# filter
			if ( count( $info_fee ) == 0 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Fee / Komisi yang belum dibayar tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Fee / Komisi yang belum dibayar telah ditemukan.',
					'data' => array('info_pembayaran' => $info_fee,
										 'invoice' => $this->random_code_ops->invoice_pembayaran_fee_agen(),
										 'agen_name' => $agen_info['fullname'],
										 'agen_identity' => $agen_info['identity_number']),
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

	function _ck_invoice_bayar_fee_agen($invoice){
		if( $this->model_fee_agen->check_invoice_bayar_fee_exist( $invoice ) ){
			$this->form_validation->set_message('_ck_invoice_bayar_fee_agen', 'Nomor Invoice sudah terdaftar di dalam database.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	# bayar fee keagenan
	function bayar_fee_keaganan(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Agen ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_agen_id');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_bayar_fee_agen');
		foreach ( $this->input->post('bayar') as $key => $value ) {
			$this->form_validation->set_rules("bayar[" . $key . "]", "Pembayaran Fee", 'trim|xss_clean|min_length[1]');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# level akun
			$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
			# get agen id
			$agen_id = $this->input->post('id');
			#  invoice
			$invoice = $this->input->post('invoice');
			# bayar
			$pembayaran = $this->input->post('bayar');
			# get info fee
			$info_fee = $this->model_fee_agen->get_info_fee_agen_2( $agen_id );
			# info agen
			$info_agen = $this->model_fee_agen->get_info_agen( $agen_id );
			# check
			$data = array();
			$data_detail = array();
			# looping
			foreach ( $pembayaran as $key => $value ) {
				$fee = $info_fee[$key]['fee'];
				$sudah_bayar = $info_fee[$key]['sudah_bayar'];
				$bayar = $this->text_ops->hide_currency($value);
				if ( ( $sudah_bayar + $bayar ) > $fee ) {
					$error = 1;
					$error_msg = 'Terdapat pembayaran yang melebihi fee keagenan.';
				} else {
					# filter
					if ( $level_akun == 'administrator' ) {
						$receiver = "Administrator";
					} else {
						$receiver = $this->session->userdata($this->config->item('apps_name'))['fullname'];
					}
					# data
					if( $bayar != 0 ) {
						$data[] = array('company_id' => $this->company_id,
											 'detail_fee_keagenan_id' => $key,
										  	 'agen_id' => $agen_id,
										 	 'invoice' => $invoice,
										 	 'biaya' => $bayar,
										 	 'applicant_name' => $info_agen['fullname'],
										 	 'applicant_identity' => $info_agen['identity_number'],
										 	 'receiver' => $receiver,
										 	 'date_transaction' => date('Y-m-d H:i:s'));
						$status_fee = 'belum_lunas';
						if( ( $sudah_bayar + $bayar ) == $fee ) {
							$status_fee = 'lunas';
						}
						$data_detail[$key] = array('sudah_bayar' => $sudah_bayar + $bayar,
													  	 	'status_fee' => $status_fee,
												  	  	 	'last_update' => date('Y-m-d H:i:s'));
					}

				}
			}
			# insert process
			if( $error == 0 ) {
				if( ! $this->model_fee_agen_cud->insert_pembayaran_fee_agen( $data, $data_detail ) ) {
					$error = 1;
					$error_msg = 'Proses pembayaran fee agen gagal dilakukan.';
				}
			}
			# filter
			if ( $error == 1 ) {
				# return
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				# create session priting here
				$this->session->set_userdata( array('cetak_invoice' => array( 'type' => 'pembayaran_fee_agen', 'invoice' => $invoice ) ) );
				# return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses pembayaran fee agen berhasil ditemukan.',
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


	function riwayat_pembayaran_fee_agen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Agen ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_agen_id');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$agen_id = $this->input->post('id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_fee_agen->get_total_riwayat_pembayaran_fee_agen($search, $agen_id);
			$list 	= $this->model_fee_agen->get_index_riwayat_pembayaran_fee_agen($perpage, $start_at, $search, $agen_id);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data riwayat pembayaran fee agen tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data riwayat pembayaran fee agen berhasil ditemukan.',
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

	function _ck_invoice_delete_riwayat_pembayaran_fee_agen( $invoice ){
		# level akun
		$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
		# filter hak akses
		if( $level_akun == 'administrator' ) {
			# filter
			if( $this->model_fee_agen->check_invoice_riwayat_pembayaran_exist( $invoice ) ) {
				return TRUE;
			}else{
				$this->form_validation->set_message('_ck_invoice_delete_riwayat_pembayaran_fee_agen', 'Invoice pembayaran tidak ditemukan didalam pangkalan data.');
				return FALSE;
			}
		}else{
			$this->form_validation->set_message('_ck_invoice_delete_riwayat_pembayaran_fee_agen', 'Anda tidak memiliki akses untuk menghapus transaksi pembayaran fee agen.');
			return FALSE;
		}
	}

	function delete_riwayat_pembayaran_fee_agen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('invoice',	'<b>Invoice<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_invoice_delete_riwayat_pembayaran_fee_agen');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# get invoice
			$invoice = $this->input->post('invoice');
			# recount fee keagenan sudah bayar
			$data_recount = $this->model_fee_agen->recount_detail_sudah_bayar_fee_keagenan($invoice);
			# filter
			if ( ! $this->model_fee_agen_cud->delete_riwayat_pembayaran_fee_agen( $invoice, $data_recount ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data riwayat pembayaran fee agen gagal dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data riwayat pembayaran fee agen berhasil dihapus.',
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

	function _ck_invoice_cetak_riwayat_pembayaran_fee_agen( $invoice ){
		# check invoice exist
		if( $this->model_fee_agen->check_invoice_riwayat_pembayaran_exist( $invoice ) ) {
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_invoice_delete_riwayat_pembayaran_fee_agen', 'Invoice pembayaran tidak ditemukan didalam pangkalan data.');
			return FALSE;
		}
	}

	function cetak_invoice_riwayat_pembayaran_fee_agen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('invoice',	'<b>Invoice<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_invoice_cetak_riwayat_pembayaran_fee_agen');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# create session priting here
			$this->session->set_userdata( array('cetak_invoice' => array( 'type' => 'pembayaran_fee_agen', 'invoice' => $this->input->post('invoice') ) ) );
			# filter
			if ( $error == 1 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
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
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}
}
