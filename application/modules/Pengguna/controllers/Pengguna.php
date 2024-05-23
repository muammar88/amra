<?php

/**
 *  -----------------------
 *	Daftar pengguna Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Pengguna extends CI_Controller
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
		$this->load->model('Model_pengguna', 'model_pengguna');
		#load modal pengaturan cud
		$this->load->model('Model_pengguna_cud', 'model_pengguna_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# daftar pengguna
	function daftar_pengguna()
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
			$total = $this->model_pengguna->get_total_pengguna($search);
			$list = $this->model_pengguna->get_index_pengguna($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar pengguna tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar pengguna berhasil ditemukan.',
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

	# get info add pengguna
	function info_add_pengguna()
	{
		# get daftar grup
		$daftar_grup = $this->model_pengguna->get_daftar_grup();
		# filter
		if (count($daftar_grup) > 0) {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Daftar grup ditemukan.',
				'data' => $daftar_grup,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Daftar grup tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	# check user id
	function _ck_user_id()
	{
		if ($this->input->post('id')) {
			if ($this->model_pengguna->check_user_id_exist($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_user_id', 'User id tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	# grup id
	function _ck_grup_id($grup_id)
	{
		if ($this->model_pengguna->check_grup_id($grup_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_grup_id', 'Grup id tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_nomor_whatsapp_exist($nomor_whatsapp)
	{
		//validasi nomor hp
		$error = 0;
		$error_msg = '';
		// $sub1 = substr($nomor_whatsapp,0,1);
		// if( $sub1 == '0' ){
		// 	$nomor_whatsapp = '62' . substr($nomor_whatsapp, 1);
		// }
		# filter error
		if( $error == 0 ) {
			$id = 0;
			if ( $this->input->post('id') ) {
				$id = $this->input->post('id');
			}
			if ( $this->model_pengguna->check_nomor_whatsapp_exist( $nomor_whatsapp, $id ) ) {
				$this->form_validation->set_message('_ck_nomor_whatsapp_exist', 'Nomor Whatsapp sudah digunakan.');
				return FALSE;
			} else {
				return TRUE;
			}
		}else{
			$this->form_validation->set_message('_ck_nomor_whatsapp_exist', $error_msg);
			return FALSE;
		}
	}

	# add update pengguna
	function addUpdatePengguna()
	{
		$return    = array();
		$error_msg = '';
		$error     = 0;
		# validation
		$this->form_validation->set_rules('id', '<b>User ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_user_id');
		$this->form_validation->set_rules('fullname', '<b>User ID<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('grup', '<b>Grup ID<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_grup_id');
		$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|required|numeric|xss_clean|min_length[1]|callback__ck_nomor_whatsapp_exist');
		if ( $this->input->post('id') ) {
			$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|xss_clean|min_length[1]');
		} else {
			$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|required|xss_clean|min_length[1]');
		}
		if ($this->input->post('password')) {
			$this->form_validation->set_rules('conf_password', '<b>Konfirmasi Password<b>', 'trim|required|xss_clean|min_length[1]|matches[password]');
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# data
			$data = array();
			$data['group_id'] = $this->input->post('grup');
			$data['last_update'] = date('Y-m-d');
			# data personal
			$data_personal = array();
			$data_personal['fullname'] = $this->input->post('fullname');
			
			$nomor_whatsapp = $this->input->post('nomor_whatsapp');
			// if ( substr($nomor_whatsapp, 0, 1) == '0') {
			// 	$nomor_whatsapp = '62' . substr($nomor_whatsapp, 1);
			// }
			$data_personal['nomor_whatsapp'] = $nomor_whatsapp;
			$data_personal['otp'] = '123456';
			$data_personal['otp_expire'] = '2023-06-03 15:00:33';

			if ($this->input->post('password')  and $this->input->post('password') != '') :
				$data_personal['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			endif;
			$data_personal['last_update'] = date('Y-m-d');
			# filter
			if ($this->input->post('id')) :
				$personal_id = $this->model_pengguna->get_personal_id($this->input->post('id'));
				# update pengguna
				if (!$this->model_pengguna_cud->updatePengguna($this->input->post('id'), $personal_id, $data, $data_personal)) :
					$error = 1;
					$error_msg = 'Proses update data pengguna gagal dilakukan.';
				endif;
			else :
				$data['company_id'] = $this->company_id;
				$data['input_date'] = date('Y-m-d');
				# data personal
				$data_personal['input_date'] = date('Y-m-d');
				$data_personal['company_id'] = $this->company_id;
				if (!$this->model_pengguna_cud->insertPengguna($data, $data_personal)) :
					$error = 1;
					$error_msg = 'Proses insert data pengguna baru gagal dilakukan';
				endif;
			endif;
			# filter error
			if ($error == 1) {
				$return = array(
					'error'     => true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'     => false,
					'error_msg' => 'Data pengguna berhasil disimpan.',
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

	# check nomor_whatsapp
	function check_nomor_whatsapp()
	{
		$return    = array();
		$error_msg = '';
		$error     = 0;
		# validation
		$this->form_validation->set_rules('id', '<b>User ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_user_id');
		$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|required|numeric|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# check nomor whatsapp
			if ($this->input->post('id')) {
				$data = $this->model_pengguna->check_nomor_whatsapp_exist($this->input->post('nomor_whatsapp'), $this->input->post('id'));
			} else {
				$data = $this->model_pengguna->check_nomor_whatsapp_exist($this->input->post('nomor_whatsapp'));
			}
			# filter error
			if ($data == true) {
				$return = array(
					'error'     => true,
					'error_msg' => 'Nomor Whatsapp sudah terdaftar didalam pangkalan data.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error' => false,
					'error_msg' => 'Nomor Whatsapp tidak terdaftar didalam pangkalan data',
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

	function get_info_edit_pengguna()
	{
		$return    = array();
		$error_msg = '';
		$error     = 0;
		# validation
		$this->form_validation->set_rules('id', '<b>User ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_user_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get daftar grup
			$daftar_grup = $this->model_pengguna->get_daftar_grup();
			# get value
			$value = $this->model_pengguna->get_value_pengguna($this->input->post('id'));
			# filter error
			if (count($value) == 0) {
				$return = array(
					'error'     => true,
					'error_msg' => 'Data pengguna tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error' => false,
					'data' => $daftar_grup,
					'value' => $value,
					'error_msg' => 'Data pengguna berhasil ditemukan.',
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

	# delete pengguna
	function delete_pengguna()
	{
		$return    = array();
		$error_msg = '';
		$error     = 0;
		# validation
		$this->form_validation->set_rules('id', '<b>User ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_user_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# delete process
			if (!$this->model_pengguna_cud->delete_pengguna($this->input->post('id'))) {
				$return = array(
					'error'     => true,
					'error_msg' => 'Data pengguna gagal dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error' => false,
					'error_msg' => 'Data pengguna berhasil dihapus. Untuk menghapus semua data pengguna, anda dapat menggunakan fitur hapus member di menu member.',
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
