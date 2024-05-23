<?php

/**
 *  -----------------------
 *	Tipe paket la Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan extends CI_Controller
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
		$this->load->model('Model_pengaturan', 'model_pengaturan');
		#load modal pengaturan cud
		$this->load->model('Model_pengaturan_cud', 'model_pengaturan_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# get info pengaturan
	function get_info_pengaturan()
	{
		# get info pengaturan
		$data = $this->model_pengaturan->get_info_pengaturan($this->company_id);
		# filter
		if (count($data) == 0) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data pengaturan tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data pengaturan berhasil ditemukan.',
				'data' => $data,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	# controllers update pengaturan
	function updatePengaturan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		// deskripsi_perusahaan
		$this->form_validation->set_rules('deskripsi_perusahaan', '<b>Deskripsi Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_perusahaan', '<b>Alamat Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_kota_perusahaan', '<b>Nama Kota Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kode_pos', '<b>Kode Pos Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('telpon_perusahaan', '<b>Telepon Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_wa_perusahaan',	'<b>Nomor WA Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('email_invoice_perusahaan', '<b>Email Invoice Perusahaan<b>', 'trim|required|xss_clean|min_length[1]|valid_email');
		$this->form_validation->set_rules('judul_invoice_perusahaan', '<b>Judul Invoice Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_invoice_perusahaan',	'<b>Alamat Invoice Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('catatan_invoice_perusahaan', '<b>Catatan Invoice Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			#  receive data
			$data = array();
			$adaPhoto = false;

			# check upload is exist
			if (isset($_FILES['photo']) and $_FILES['photo']['size'] > 0) {
				$adaPhoto = true;
				// echo "masuk";
				# get logo name
				$logo = $this->model_pengaturan->get_logo($this->company_id);
				if ($logo != '') {
					$logo_name = explode('.', $logo)[0];
				} else {
					$logo_name = md5(date('Ymdhis'));
				}
				# path
				$path = 'image/company/invoice_logo/';
				# define config
				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = $logo_name;
				$config['max_size']   = 1024;
				$config['max_width']  = 300;
				$config['max_height'] = 80;

				$config['overwrite'] = TRUE;
				$this->load->library('upload', $config);
				$this->upload->overwrite 	= true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ( $this->upload->do_upload('photo') ) {
					$fileData = $this->upload->data();

					// echo $fileData['file_name'];
					$data['logo'] = $fileData['file_name'];
					if ($logo != '') {
						if ($fileData['file_name'] != $logo) {
							$src = FCPATH . $path . $logo;
							if (file_exists($src)) {
								unlink($src);
							}
						}
					}
				} else {
					$error = 1;
					$error_msg = $this->upload->display_errors();
				}
			}

			if( $error == 0 ) {
				$data['description'] = $this->input->post('deskripsi_perusahaan');
				$data['address'] = $this->input->post('alamat_perusahaan');
				$data['city'] = $this->input->post('nama_kota_perusahaan');
				$data['pos_code'] = $this->input->post('kode_pos');
				$data['telp'] = $this->input->post('telpon_perusahaan');
				$data['whatsapp_number'] = $this->input->post('nomor_wa_perusahaan');
				$data['invoice_email'] = $this->input->post('email_invoice_perusahaan');
				$data['invoice_title'] = $this->input->post('judul_invoice_perusahaan');
				$data['invoice_address'] = $this->input->post('alamat_invoice_perusahaan');
				$data['invoice_note'] = $this->input->post('catatan_invoice_perusahaan');
				if( ! $this->model_pengaturan_cud->update_pengaturan($data) ){
					$error = 1;
					$error_msg = 'Proses update pengaturan gagal dilakukan.';
				}
			}
			# filter model pengaturan cud
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data pengaturan berhasil disimpan.',
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

	# daftar bank transfer
	function daftar_bank_transfer(){
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
			$total 	= $this->model_pengaturan->get_total_daftar_bank_transfer($search);
			$list 	= $this->model_pengaturan->get_index_daftar_bank_transfer($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar bank transfer tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar bank transfer berhasil ditemukan.',
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

	function get_info_bank_transfer(){
		$error = 0;
		$info_bank = $this->model_pengaturan->get_info_bank_transfer();
		if ( count($info_bank) <= 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data info bank tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info bank berhasil ditemukan.',
				'data' => $info_bank,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_bank_transfer_id(){
		if( $this->input->post('id') ) {
			if( ! $this->model_pengaturan->check_company_bank_transfer_id( $this->input->post('id') ) ){
				$this->form_validation->set_message('_ck_bank_transfer_id', 'ID bank transfer id tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	function _ck_bank( $bank_id ) {
		if( ! $this->model_pengaturan->check_bank( $bank_id ) ) {
			$this->form_validation->set_message('_ck_bank', 'ID bank tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	# proses update bank tranfer
	function proses_addupdate_bank_transfer(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bank Transfer<b>', 'trim|xss_clean|min_length[1]|callback__ck_bank_transfer_id');
		$this->form_validation->set_rules('bank', '<b>Bank<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank');
		$this->form_validation->set_rules('nomor_rekening', '<b>Nomor Rekening<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_rekening', '<b>Rekening Atas Nama<b>', 'trim|required|xss_clean|min_length[1]');

		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$data = array();
			$data['bank_id'] = $this->input->post('bank');
			$data['account_name'] = $this->input->post('nama_rekening');
			$data['account_number'] = $this->input->post('nomor_rekening');
			$data['last_update'] = date('Y-m-d');
			if( $this->input->post('id') ) {
				if( ! $this->model_pengaturan_cud->update_bank_transfer_company($this->input->post('id'), $data)){
					$error = 1;
					$error_msg = 'Data bank gagal diperbaharui.';
				}
			}else{
				$data['company_id'] = $this->company_id;
				$data['input_date'] = date('Y-m-d');
				if( ! $this->model_pengaturan_cud->insert_bank_transfer_company( $data ) ) {
					$error = 1;
					$error_msg = 'Data bank transfer gagal di tambahkan.';
				}
			}
			# filter model pengaturan cuds
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => $error_msg,
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


	function _ck_bank_transfer_id_exist($id){
		if( ! $this->model_pengaturan->check_company_bank_transfer_id( $id ) ){
			$this->form_validation->set_message('_ck_bank_transfer_id_exist', 'ID bank transfer tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}


	function edit_bank_transfer(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bank Transfer<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank_transfer_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# info bank
			$info_bank = $this->model_pengaturan->get_info_bank_transfer();
			# get value
			$value = $this->model_pengaturan->get_value_bank_transfer( $this->input->post('id') );
			# filter model pengaturan cuds
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => $error_msg,
					'data' => $info_bank,
					'value' => $value,
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

	function delete_bank_transfer(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bank Transfer<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_bank_transfer_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter model pengaturan cuds
			if ( $this->model_pengaturan_cud->delete_bank_transfer( $this->input->post('id') ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete bank berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete bank gagal dilakukan.',
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
