<?php

/**
 *  -----------------------
 *	Daftar member Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_member extends CI_Controller
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
		$this->load->model('Model_daftar_member', 'model_daftar_member');
		# model daftar mobil cud
		$this->load->model('Model_daftar_member_cud', 'model_daftar_member_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}


	function _ck_otoritas(){
      if($this->session->userdata($this->config->item('apps_name'))['level_akun'] != 'administrator'){
         $this->form_validation->set_message('_ck_otoritas', 'Anda Tidak Berhak Untuk Melakukan Proses Hapus.');
         return FALSE;
      }else{
         return TRUE;
      }
   }


	function daftar_members()
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
			$total = $this->model_daftar_member->get_total_daftar_member($search);
			$list = $this->model_daftar_member->get_index_daftar_member($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar member tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar member berhasil ditemukan.',
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

	function _ck_id_member_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_daftar_member->check_id_member_exist($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_member_exist', 'ID member tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_no_whatsapp_exist( $nomor_whatsapp ){
		//validasi nomor hp
		$error = 0;
		$error_msg = '';
		$sub1 = substr($nomor_whatsapp,0,1);
		if( $sub1 != '0' ){
			$sub2 = substr($nomor_whatsapp,0,2);
			if( $sub2 != '62') {
				$error = 1;
				$error_msg = 'Nomor  whatsapp tidak valid. nomor whatsapp harus diawali dengan <b style="color:red;">0</b> atau <b style="color:red;">62</b>';
			}
		}
		# filter error
		if( $error == 0 ) {
			$id = '';
			if( $this->input->post('id') ) {
				$id = $this->input->post('id');
			}
			if( ! $this->model_daftar_member->check_no_whatsapp_exist( $nomor_whatsapp, $id ) ) {
				return TRUE;
			}else{
				$this->form_validation->set_message('_ck_no_whatsapp_exist', 'Nomor whatsapp sudah terdaftar dipangkalan data.');
				return FALSE;
			}
		}else{
			$this->form_validation->set_message('_ck_nomor_whatsapp_exist', $error_msg);
			return FALSE;
		}
	}

	function _ck_list_bank_id_exist($bank_id){
		if(! $this->model_daftar_member->check_list_bank_id( $bank_id ) ){
			$this->form_validation->set_message('_ck_list_bank_id_exist', 'ID Bank tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function proses_addupdate_member()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Member<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_member_exist');
		$this->form_validation->set_rules('nama', '<b>Nama<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jenis_kelamin', '<b>Jenis Kelamin<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,1]');
		$this->form_validation->set_rules('tempat_lahir', '<b>Tempat Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_lahir', '<b>Tanggal Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('email', '<b>Email<b>', 'trim|required|valid_email|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat', '<b>Alamat<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|required|numeric|xss_clean|min_length[1]|callback__ck_no_whatsapp_exist');
		$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('conf_password', '<b>Password Konfirmasi<b>', 'trim|xss_clean|min_length[1]|matches[password]');
		$this->form_validation->set_rules('nama_akun_bank', '<b>Nama Akun Bank<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_akun_bank', '<b>Nomor Akun Bank<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('list_bank', '<b>Daftar Bank<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_list_bank_id_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			#  receive data
			$data = array();
			$data['fullname'] = $this->input->post('nama');
			$data['identity_number'] = $this->input->post('nomor_identitas');
			$data['gender'] = $this->input->post('jenis_kelamin');
			$data['birth_place'] = $this->input->post('tempat_lahir');
			$data['birth_date'] = $this->input->post('tanggal_lahir');
			$data['email'] = $this->input->post('email');
			$data['address'] = $this->input->post('alamat');
			$data['account_name'] = $this->input->post('nama_akun_bank');
			$data['number_account'] = $this->input->post('nomor_akun_bank');
			$data['bank_id'] = $this->input->post('list_bank');
			$data['nomor_whatsapp'] = $this->input->post('nomor_whatsapp');

			if ($this->input->post('password') and $this->input->post('password') != '') {
				$data['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			$data['last_update'] = date('Y-m-d');

			# Upload Photo
			if (isset($_FILES['photo']) and $_FILES['photo']['size'] > 0) {
				$_FILES['userFile']['name'] = $_FILES['photo']['name'];
				$_FILES['userFile']['type'] = $_FILES['photo']['type'];
				$_FILES['userFile']['tmp_name'] = $_FILES['photo']['tmp_name'];
				$_FILES['userFile']['error'] = $_FILES['photo']['error'];
				$_FILES['userFile']['size'] = $_FILES['photo']['size'];

				$path = 'image/personal/';

				$photo_with_extention = '';
				if ($this->input->post('id')) {
					$photo_with_extention = $this->model_daftar_member->get_name_photo($this->input->post('id'));
					$photo_name = explode('.', $photo_with_extention)[0];
				} else {
					$photo_name = md5(date('Y-m-d H:i:s'));
				}

				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = $photo_name;
				$config['overwrite'] = TRUE;
				$config['max_size'] = 2000;
				$this->load->library('upload', $config);
				$this->upload->overwrite = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ($this->upload->do_upload('userFile')) {
					$fileData = $this->upload->data();
					$data['photo'] = $fileData['file_name'];
					if ($photo_with_extention != $fileData['file_name'] and $photo_with_extention != '') {
						# src path
						$src = FCPATH . 'image/personal/' . $photo_with_extention;
						if (file_exists($src)) {
							unlink($src);
						}
					}
					// photo was upload
					$photo_uploaded = 1;
				} else {
					$error 		= 1;
					$error_msg 	= $this->upload->display_errors();
				}
			}
			if ($this->input->post('id')) {
				if (!$this->model_daftar_member_cud->update_member($this->input->post('id'), $data)) {
					$error = 1;
					$error_msg = 'Proses pembaharuan data member gagal dilakukan.';
				}
			} else {
				$data['input_date'] = date('Y-m-d');
				$data['company_id'] = $this->company_id;
				if (!$this->model_daftar_member_cud->insert_member($data)) {
					$error = 1;
					$error_msg = 'Proses penambahan data member gagal dilakukan.';
				}
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data member berhasil disimpan.',
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

	// get_info_edit_member

	# delete member
	function delete_member()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Member<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_otoritas|callback__ck_id_member_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {

			# check akun user, agen, jamaah and muthawif exist
			$feedBack = $this->model_daftar_member->check_other_level_akun_exist($this->input->post('id'));
			# filter process
			if ($feedBack['error'] == true) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Member tidak dapat dihapus, karena ' . $feedBack['error_msg'] . '. <br>Silahkan hapus akun ' . $feedBack['as'] . ' tersebut terlebih dahulu jika ingin melanjutkan proses penghapusan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				# get photo name
				$photo_name = $this->model_daftar_member->get_name_photo($this->input->post('id'));
				# filter feedBack
				if ($this->model_daftar_member_cud->delete_member($this->input->post('id'))) {
					# src path
					$src = FCPATH . 'image/personal/' . $photo_name;
					if (file_exists($src)) {
						unlink($src);
					}
					$return = array(
						'error'	=> false,
						'error_msg' => 'Data member berhasil dihapus.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				} else {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Data member gagal dihapus.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
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

	function get_info_edit_member()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Member<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_member_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# list bank
			$list_bank = $this->model_daftar_member->get_list_bank();
			# get photo name
			$feedBack = $this->model_daftar_member->get_info_member($this->input->post('id'));
			# filter feedBack
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data member berhasil ditemukan.',
					'data' => array('list_bank' => $list_bank),
					'value' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data member gagal dihapus.',
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

	// get info member
	function get_info_member(){
		$error = 0;
		# list bank
		$list_bank = $this->model_daftar_member->get_list_bank();
		// filter
		if ($error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data info tambah member tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info tambah member berhasil ditemukan.',
				'data' => array(
					'list_bank' => $list_bank,
				),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function as_muthawif()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Member<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_member_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_daftar_member_cud->set_as_muthawif($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Member berhasil dijadikan muthawif.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Member gagal dijadikan muthawif.',
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

	function info_as_agen()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Member<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_member_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$info_member = $this->model_daftar_member->get_name_member($this->input->post('id'));
			# filter feedBack
			$agen = $this->model_daftar_member->get_agen();
			# level keagenan
			$level_keagenan = $this->model_daftar_member->get_level_keagenan();
			# filter
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Info berhasil ditemukan.',
					'data' => array(
						'agen' => $agen,
						'level_keagenan' => $level_keagenan,
						'nama' => $info_member['fullname'],
						'no_identitas' =>  $info_member['identity_number'],
						'id' => $this->input->post('id')
					),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'info tidak ditemukan.',
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

	function _ck_id_uplink_exist($id)
	{
		if ($id != 0) {
			if ($this->model_daftar_member->check_uplink_id_exist($id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_uplink_exist', 'ID Uplink tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_level_keagenan($level){
		if( ! $this->model_daftar_member->check_level_keagenan_axist($level) ) {
			$this->form_validation->set_message('_ck_level_keagenan', 'Level agen tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function proses_addupdate_as_agen()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Member<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_member_exist');
		$this->form_validation->set_rules('upline', '<b>UpLink ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_uplink_exist');
		$this->form_validation->set_rules('level_agen', '<b>Level Agen<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_level_keagenan');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['level_agen_id'] = $this->input->post('level_agen');
			$data['upline'] = $this->input->post('upline');
			$data['personal_id'] = $this->input->post('id');
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			# filter feedBack
			if ($this->model_daftar_member_cud->set_member_as_agen($data)) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data member berhasil disimpan.',
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
