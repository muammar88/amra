<?php

/**
 *  -----------------------
 *	Saldo perusahaan Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Saldo_perusahaan extends CI_Controller
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
		$this->load->model('Model_saldo_perusahaan', 'model_saldo_perusahaan');
		# model slider cud
		$this->load->model('Model_saldo_perusahaan_cud', 'model_saldo_perusahaan_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_riwayat_mutasi_saldo(){
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
			$total 	= $this->model_saldo_perusahaan->get_total_daftar_riwayat_mutasi_saldo($search);
			$list 	= $this->model_saldo_perusahaan->get_index_daftar_riwayat_mutasi_saldo($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar riwayat mutasi saldo tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar riwayat mutasi saldo berhasil ditemukan.',
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


	function riwayat_tambah_saldo(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		// $this->form_validation->set_rules('status', '<b>Status</b>', 'trim|required|xss_clean|min_length[1]|in_list[proses,disetujui,ditolak]');
		/*
			Validation process
		*/ 
		if ($this->form_validation->run()) {
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			// $status = $this->input->post('status');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_saldo_perusahaan->get_total_daftar_riwayat_tambah_saldo($search);
			$list 	= $this->model_saldo_perusahaan->get_index_daftar_riwayat_tambah_saldo($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar riwayat tambah saldo tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar riwayat tambah saldo berhasil ditemukan.',
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

	// check nominal tambah saldo
	function _ck_nominal_tambah_saldo($value){

		if( $this->input->post('id') ) {
			$check_request_proses = $this->model_saldo_perusahaan->check_request_proses( $this->input->post('id') );
		}else{
			$check_request_proses = $this->model_saldo_perusahaan->check_request_proses();
		}
		
		if( $check_request_proses == false ) {
			$nominal = $this->text_ops->hide_currency( $value );
			if( $nominal == 0 ) {
				$this->form_validation->set_message('_ck_nominal_tambah_saldo', 'Nominal request tambah saldo tidak boleh nol');
				return FALSE;
			}else{
				if( $nominal < 100000) {
					$this->form_validation->set_message('_ck_nominal_tambah_saldo', 'Nominal request tidak boleh lebih kecil dari Rp. 100.000,-');
					return FALSE;
				}else{
					$back_nominal = substr($nominal,-3);
					if( $back_nominal == '000' ) {
						return TRUE;
					}else{
						$this->form_validation->set_message('_ck_nominal_tambah_saldo', 'Tiga angka paling belakang wajib diisi dengan angka 0.');
						return FALSE;
					}
				}
			}
		}else{
			$this->form_validation->set_message('_ck_nominal_tambah_saldo', 'Anda tidak dapat melakukan request tambah saldo selama masih ada request yang masih berstatus PROSES.');
			return FALSE;
		}
	}

	// check bank transfer
	function _ck_bank_transfer($value){
		if( $this->input->post('id') ) {
			// check bank transfer
			$check = $this->model_saldo_perusahaan->check_bank_transfer( $value );
			if( $check ) {
				return TRUE;
			}else{ 
				$this->form_validation->set_message('_ck_bank_transfer', 'ID Bank transfer tidak ditemukan.');
				return FALSE;
			}
		}else{
			return TRUE;
		}
	}

	// proses request tambah saldo perusahaan
	function proses_request_tambah_saldo_perusahaan(){
		$return = array();
		$error 	= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Request<b>', 'trim|xss_clean|min_length[1]|callback__ck_bank_transfer');
		$this->form_validation->set_rules('bank_transfer','<b>Bank Transfer<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank_transfer');
		$this->form_validation->set_rules('nominal','<b>Nominal Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nominal_tambah_saldo');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get
			$nominal = $this->text_ops->hide_currency( $this->input->post('nominal') );
			$get_info_bank = $this->model_saldo_perusahaan->get_info_bank( $this->input->post('bank_transfer') );
			# data
			$data =  array();
			$data['bank'] = $get_info_bank['bank_name'];
			$data['nomor_akun_bank'] = $get_info_bank['account_bank_number'];
			$data['nama_akun_bank'] = $get_info_bank['account_bank_name'];
			$data['biaya'] = $nominal;
			$data['kode_biaya'] = $this->random_code_ops->generated_kode_biaya();
			$data['last_update'] = date('Y-m-d H:i:s');
			// filter
			if ( $this->input->post('id') ) {
				if( ! $this->model_saldo_perusahaan_cud->update_tambah_saldo_perusahaan( $this->input->post('id'), $data ) ) {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses update data request tambah saldo perusahaan gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses update data request tambah saldo perusahaan berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			}else{
				$data['company_id'] = $this->company_id;
				$data['kode'] = $this->random_code_ops->generated_kode_tambah_saldo();
				$data['input_date'] = date('Y-m-d H:i:s');
				// request tambah saldo
				if ( ! $this->model_saldo_perusahaan_cud->request_tambah_saldo( $data ) ) {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses request tambah saldo perusahaan gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				} else {
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses request tambah saldo perusahaan berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
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

	// get info tambah saldo
	function get_info_tambah_saldo(){
		$error = 0;
		// list bank
		$list_bank = $this->model_saldo_perusahaan->get_bank_admin();
		// error 
		if ( count( $list_bank ) <= 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data info tambah saldo tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info tambah saldo berhasil ditemukan.',
				'list_bank' => $list_bank,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_id_request_tambah_saldo( $value ) {
		// check id request tambah saldo
		$check = $this->model_saldo_perusahaan->check_id_request_tambah_saldo( $value );
		if( ! $check ) { 
			$this->form_validation->set_message('_ck_id_request_tambah_saldo', 'ID Request tambah saldo tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// delete request tambah saldo
	function delete_request_tambah_saldo(){
		$return = array();
		$error 	= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Request Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request_tambah_saldo');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// request tambah saldo
			if ( ! $this->model_saldo_perusahaan_cud->delete_request_tambah_saldo( $this->input->post('id') ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar riwayat tambah saldo tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar riwayat tambah saldo berhasil ditemukan.',
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

	function get_info_edit_riwayat_tambah_saldo(){
		$return = array();
		$error 	= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Request Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request_tambah_saldo');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get data
			$get_data = $this->model_saldo_perusahaan->get_info_edit_riwayat( $this->input->post('id') );
			// list bank
			$list_bank = $this->model_saldo_perusahaan->get_bank_admin();
			// request tambah saldo
			if ( count( $get_data ) == 0 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data riwayat tambah saldo tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data riwayat tambah saldo berhasil ditemukan.',
					'value' => $get_data,
					'list_bank' => $list_bank,
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


	function konfirmasi_pengiriman_tambah_saldo(){
		$return = array();
		$error 	= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Request Tambah Saldo<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request_tambah_saldo');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get data
			$data = array();
			$data['status_kirim'] = 'sudah_dikirim';
			$data['waktu_kirim'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			// request tambah saldo
			if ( ! $this->model_saldo_perusahaan_cud->update_tambah_saldo_perusahaan( $this->input->post('id'), $data ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses konfirmasi pembayaran biaya gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses konfirmasi pembayaran biaya berhasil dilakukan.',
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