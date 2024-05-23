<?php

/**
 *  -----------------------
 *	Daftar grup Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_grup extends CI_Controller
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
		$this->load->model('Model_daftar_grup', 'model_daftar_grup');
		#load modal pengaturan cud
		$this->load->model('Model_daftar_grup_cud', 'model_daftar_grup_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# daftar grup
	public function daftar_grups()
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
			$total = $this->model_daftar_grup->get_total_daftar_grup($search);
			$list = $this->model_daftar_grup->get_index_daftar_grup($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar grup tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar grup berhasil ditemukan.',
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

	# get info grup
	function get_info_grup()
	{
		$this->load->library('access');
		$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		$company_type = $this->session->userdata($this->config->item('apps_name'))['company_type'];
		$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
		# JSON Encode
		echo json_encode(array(
			'error' => false,
			'data' => $this->access->modul_access($company_type,'staff',array('id' => $company_id)),
			'error_msg' => validation_errors(),
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
		));
	}

	function _ckmenu()
	{
		if ($this->input->post('nama_group') != '') {
			if ($this->input->post('menu')) {
				if (count($this->input->post('menu')) > 0) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckmenu', 'Anda Wajib Memilih Salah Satu Menu Untuk Group Anggota Ini.');
					return FALSE;
				}
			} else {
				$this->form_validation->set_message('_ckmenu', 'Anda Wajib Memilih Salah Satu Menu Untuk Group Anggota Ini.');
				return FALSE;
			}
		} else {
			$this->form_validation->set_message('_ckmenu', 'Anda Wajib Mengisi Nama Grup.');
			return FALSE;
		}
	}

	# check modul exist
	function _ck_modul_id($modul, $list_modul)
	{
		if (in_array($modul, json_decode($list_modul))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_modul_id', 'Modul id tidak terdefinisi didalam pangkalan data.');
			return FALSE;
		}
	}

	function _ck_submodul_id($submodul, $list_submodul)
	{
		if (in_array($submodul, json_decode($list_submodul))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_submodul_id', 'Submodul id tidak terdefinisi didalam pangkalan data.');
			return FALSE;
		}
	}

	# check group id
	function _ck_group_id()
	{
		if ($this->input->post('id')) {
			if ($this->model_daftar_grup->check_group_id($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_group_id', 'Group id tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	# proses addupdate grup
	function proses_addupdate_grup()
	{
		$return    = array();
		$error_msg = '';
		$error     = 0;
		# get modul and submodul id
		$menu_submenu_id = $this->model_daftar_grup->get_menu_submenu_id();
		# validation
		$this->form_validation->set_rules('id',   '<b>Group ID<b>',    'trim|xss_clean|min_length[1]|numeric|callback__ck_group_id');
		$this->form_validation->set_rules('nama_group', '<b>Nama Group<b>',  'trim|required|xss_clean|min_length[1]|callback__ckmenu');
		# validation modul id
		foreach ($this->input->post('menu') as $key => $val) {
			$this->form_validation->set_rules("menu[" . $key . "]", "Menu", 'trim|xss_clean|min_length[1]|numeric|callback__ck_modul_id[' . json_encode($menu_submenu_id['modul_id']) . ']');
		}
		# validation submodul id
		if ($this->input->post('submenu')) {
			foreach ($this->input->post('submenu') as $key => $val) {
				$this->form_validation->set_rules("submenu[" . $key . "]", "Submenu", 'trim|xss_clean|min_length[1]|numeric|callback__ck_submodul_id[' . json_encode($menu_submenu_id['submodul_id']) . ']');
			}
		}
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# retrive data
			$data = array();
			$data['nama_group'] = $this->input->post('nama_group');
			$data['group_access'] = serialize(array(
				'modul' => $this->input->post('menu'),
				'submodul' => $this->input->post('submenu')
			));
			$data['last_update'] = date('Y-m-d');
			# filter
			if ($this->input->post('id')) {
				# update process
				if (!$this->model_daftar_grup_cud->update_daftar_grup($this->input->post('id'), $data)) {
					$error = 1;
					$error_msg = 'Proses update daftar grup gagal dilakukan.';
				}
			} else {
				$data['company_id'] = $this->company_id;
				$data['input_date'] = date('Y-m-d');
				# insesrt process
				if (!$this->model_daftar_grup_cud->insert_daftar_grup($data)) {
					$error = 1;
					$error_msg = 'Proses insert daftar grup gagal dilakukan.';
				}
			}
			if ($error == 1) {
				$return = array(
					'error'     => true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'     => false,
					'error_msg' => 'Data daftar grup berhasil disimpan.',
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

	# delete grup
	function delete_grup()
	{
		$return    = array();
		$error_msg = '';
		$error     = 0;
		# validation
		$this->form_validation->set_rules('id', '<b>Group ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_group_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# delete process
			if (!$this->model_daftar_grup_cud->delete_grup($this->input->post('id'))) {
				$return = array(
					'error'     => true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'     => false,
					'error_msg' => 'Data daftar grup berhasil dihapus.',
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

	// $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
	// $company_type = $this->session->userdata($this->config->item('apps_name'))['company_type'];
	// $level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
	// # JSON Encode
	// echo json_encode(array(
	// 	'error' => false,
	// 	'data' => $this->access->modul_access($company_type,'staff',array('id' => $company_id)),
	// 	'error_msg' => validation_errors(),
	// 	$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
	// ));

	# get info grup edit
	function get_info_grup_edit()
	{
		$return  = array();
		$error_msg = '';
		$error = 0;
		# validation
		$this->form_validation->set_rules('id', '<b>Group ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_group_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$this->load->library('access');

			$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$company_type = $this->session->userdata($this->config->item('apps_name'))['company_type'];
			$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];

			# get data info edit
			$value = $this->model_daftar_grup->get_info_edit_grup($this->input->post('id'));

			// $this->model_daftar_grup->get_info_grup(),
			# delete process
			if (count($value) > 0) {
				$return = array(
					'error'     => false,
					'error_msg' => 'Data daftar grup berhasil dihapus.',
					'value' => $value,
					'data' => $this->access->modul_access($company_type,'staff',array('id' => $company_id)),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'     => true,
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
}
