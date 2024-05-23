<?php

/**
 *  -----------------------
 *	Daftar asuransi Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_asuransi extends CI_Controller
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
		$this->load->model('Model_daftar_asuransi', 'model_daftar_asuransi');
		# model asuransi cud
		$this->load->model('Model_daftar_asuransi_cud', 'model_daftar_asuransi_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_asuransi_server(){
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
			$status = $this->input->post('status_keberangkatan');
			$search = $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total = $this->model_daftar_asuransi->get_total_daftar_asuransi($search, $status);
			$list = $this->model_daftar_asuransi->get_index_daftar_asuransi($perpage, $start_at, $search, $status);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar asuransi tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar asuransi berhasil ditemukan.',
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

	# check asuransi id
	function _ck_asuransi_id( $id ) {
		if( $this->input->post('id') ) {
			if( ! $this->model_daftar_asuransi->check_asuransi_id( $id ) ) {
				$this->form_validation->set_message('_ck_asuransi_id', 'ID Asuransi tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	# process addupdate asuransi
	function proses_addupdate_asuransi(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID<b>', 'trim|xss_clean|min_length[1]|callback__ck_asuransi_id');
		$this->form_validation->set_rules('nama',	'<b>Nama Asuransi<b>', 'trim|required|xss_clean|min_length[1]');
		/*
        	Validation process
      	*/
		if ($this->form_validation->run()) {
			$data = array();
			$data['nama_asuransi'] = $this->input->post('nama');
			$data['last_update'] = date('Y-m-d');
			if( $this->input->post('id') ) {
				if( ! $this->model_daftar_asuransi_cud->update_asuransi( $this->input->post('id'), $data ) ){
					$error = 1;
					$error_msg = 'Proses update data asuransi gagal dilakukan.';
				}
			}else{
				$data['company_id'] = $this->company_id;
				$data['input_date'] = date('Y-m-d');
				# insert process
				if( ! $this->model_daftar_asuransi_cud->insert_asuransi( $data ) ){
					$error = 1;
					$error_msg = 'Proses insert data asuransi baru gagal dilakukan.';
				}
			}
			# error filter
			if ( $error == 1 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses berhasil.',
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

	# get data asuransi
	function info_edit_asuransi(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_asuransi_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# get data process
			$data   = $this->model_daftar_asuransi->get_data_asuransi_by_id( $this->input->post('id') );
			# error filter
			if ( count( $data ) == 0 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data asuransi gagal tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data asuransi berhasil ditemukan.',
					'data' => $data,
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

	# delete asuransi
	function delete_asuransi(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_asuransi_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# delete provider visa
			if( ! $this->model_daftar_asuransi_cud->delete_asuransi( $this->input->post('id') ) ) {
				$error = 1;
				$error_msg = 'Proses delete data asuransi gagal dilakukan.';
			}
			# error filter
			if ( $error == 1 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data asuransi berhasil dihapus.',
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