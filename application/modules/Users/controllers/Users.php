<?php

/**
 *  -----------------------
 *	Users Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

	private $company_code;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_users', 'model_users');
		$this->load->model('Model_users_cud', 'model_users_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	/**
	 * Index User Controllers
	 */
	public function index()
	{

		// print_r($this->session->all_userdata())
		# checking company code exist
		$this->auth_library->is_company_code_exist();
		// define property setting
		$this->index_loader->settingProperty(array('title'));
		// get setting data
		$this->index_loader->Setting();
		// extract modul and submodul tab
		$modul_submodul_tab = $this->model_users->get_modul_submodul_tab($this->session->userdata($this->config->item('apps_name'))['modul_access']);
		// add js files
		$this->index_loader->addData(array(
			'js' => array(
				'Users/pesan_whatsapp',
				'Users/template_pesan_whatsapp',
				'Users/pengaturan_perangkat_whatsapp',
				'Users/users',
				'Users/trans_paket',
				'Users/trans_tiket',
				'Users/daftar_bank',
				'Users/airlines',
				'Users/daftar_fasilitas',
				'Users/daftar_tipe_paket',
				'Users/tipe_paket_la',
				'Users/supplier',
				'Users/daftar_kota',
				'Users/daftar_mobil',
				'Users/daftar_hotel',
				'Users/jurnal',
				'Users/kas_keluar_masuk',
				'Users/trans_visa',
				'Users/trans_hotel',
				'Users/trans_passport',
				'Users/trans_transport',
				'Users/rekapitulasi_tiket',
				'Users/daftar_member',
				'Users/daftar_jamaah',
				'Users/daftar_muthawif',
				'Users/daftar_agen',
				'Users/deposit_saldo',
				'Users/request_keagenan',
				'Users/trans_member',
				'Users/trans_paket_la',
				'Users/daftar_paket',
				'Users/system_log',
				'Users/pengaturan',
				'Users/daftar_grup',
				'Users/pengguna',
				'Users/akun',
				'Users/slider',
				'Users/topik',
				'Users/artikel',
				'Users/buku_besar',
				'Users/neraca_lajur',
				'Users/laba_rugi',
				'Users/neraca',
				'Users/modal',
				'Users/investor',
				'Users/notif',
				'Users/perjalanan',
				'Users/manasik',
				'Users/pelaksanaan',
				'Users/hikmah',
				'Users/tempat_ziarah',
				'Users/tanya_jawab',
				'Users/beranda_utama',
				'Users/daftar_bandara',
				'Users/bank_transfer',
				'Users/level_agen',
				'Users/deposit_paket',
				'Users/fee_agen',
				'Users/daftar_provider_visa',
				'Users/daftar_asuransi',
				'Users/daftar_peminjaman',
				'Users/complain',
				'Users/riwayat_deposit_tabungan',
				'Users/daftar_surat_menyurat',
				'Users/withdraw_deposit',
				'Users/notification',
				'Users/daftar_trans_ppob',
				'Users/markup_produk',
				'Users/info_saldo_member',
				'Users/riwayat_transaksi_peminjaman',
				'Users/riwayat_mutasi_saldo',
				'Users/riwayat_tambah_saldo', 
				'Users/kostumer_paket_la'
			),
			'modul_access' => $this->session->userdata($this->config->item('apps_name'))['modul_access'],
			'modul_tab' => $modul_submodul_tab['modul_tab'],
			'submodul_tab' => $modul_submodul_tab['submodul_tab'],
			'photo' => $this->session->userdata($this->config->item('apps_name'))['photo'],
			'midtrans_client_key' => $this->config->item('midtrans_client_key'), 
			'kurs' => $this->session->userdata($this->config->item('apps_name'))['kurs']
		));
		// get setting values
		$data = $this->index_loader->Response();
		// generate authentication templating
		$this->templating->users_templating($data);
	}

	# get profil info
	function get_info_profil()
	{
		$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
		if ($level_akun == 'administrator') {
			$param = $this->session->userdata($this->config->item('apps_name'))['email'];
		} else {
			$param = $this->session->userdata($this->config->item('apps_name'))['nomor_whatsapp'];
		}
		$feedBack = $this->model_users->get_info_profil($param, $level_akun);
		# filter feedBack
		if (count($feedBack) > 0) {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data profil berhasil ditemukan.',
				'data' => $feedBack,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data profil gagal ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ckEmailUsername()
	{
		$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
		if ($level_akun == 'administrator') {
			$email_username_lama = $this->session->userdata($this->config->item('apps_name'))['email'];
		} else {
			$email_username_lama = $this->session->userdata($this->config->item('apps_name'))['username'];
		}
		# email and username
		$email_username = $this->input->post('email');
		if ($email_username != '') {
			if ($email_username == $email_username_lama) {
				return TRUE;
			} else {
				$feedBack = $this->model_users->check_email_username($email_username);
				if ($feedBack['error'] == false) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckEmailUsername', $feedBack['error_msg']);
					return  FALSE;
				}
			}
		} else {
			$this->form_validation->set_message('_ckEmailUsername', ($level_akun == 'administrator' ? 'Email tidak boleh kosong!.' : 'Nomor Whatsapp tidak boleh kosong!.'));
			return  FALSE;
		}
	}

	// function test() {
	// 	$var = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);


	// 	echo $var;
	// }

	# profil update controllers
	function updateUserProfil()
	{
		$return = array();
		$error = 0;
		$error_mess = '';
		$photo_uploaded = 0;
		$this->form_validation->set_rules('name',	'<b>Nama Perusahaan / Nama Pengguna<b>',  'trim|required|xss_clean|min_length[1]|max_length[256]');
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$this->form_validation->set_rules('email', '<b>Email / Nomor Whatsapp<b>',  'trim|required|xss_clean|min_length[1]|max_length[256]|callback__ckEmailUsername');
		}
		$this->form_validation->set_rules('password',	'<b>Password<b>',  'trim|xss_clean|min_length[1]|max_length[256]|alpha_numeric');
		if ($this->input->post('password') != '') {
			$this->form_validation->set_rules('conf_password',	'<b>Konfirmasi Password<b>',  'trim|xss_clean|min_length[1]|max_length[256]|alpha_numeric|matches[password]');
		}
		/**
		 *	Validation process
		 */
		if ($this->form_validation->run()) {
			# receive session
			$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
			# receive post
			$data = array();
			$data['name'] = $this->input->post('name');
			if( $level_akun == 'administrator'){
				if( $this->input->post('email') != '') {
					$data['email'] = $this->input->post('email');
				}
			}
			if ($this->input->post('password') != '') {
				$data['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			# Upload Photo
			if (isset($_FILES['photo']) and $_FILES['photo']['size'] > 0) {
				$_FILES['userFile']['name'] = $_FILES['photo']['name'];
				$_FILES['userFile']['type'] = $_FILES['photo']['type'];
				$_FILES['userFile']['tmp_name'] = $_FILES['photo']['tmp_name'];
				$_FILES['userFile']['error'] = $_FILES['photo']['error'];
				$_FILES['userFile']['size'] = $_FILES['photo']['size'];

				if ($level_akun == 'administrator') {
					$path = 'image/company/';
				} else {
					$path = 'image/personal/';
				}

				# get photo name
				if ($level_akun == 'administrator') {
					$photo_name = $this->session->userdata($this->config->item('apps_name'))['company_code'];
				} else {
					$photo_name = $this->session->userdata($this->config->item('apps_name'))['user_id'];
				}

				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = md5($photo_name);
				$config['overwrite'] = TRUE;
				$config['max_size'] = 2000;
				$this->load->library('upload', $config);
				$this->upload->overwrite = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ($this->upload->do_upload('userFile')) {
					$fileData = $this->upload->data();
					$data['photo'] = $fileData['file_name'];
					// photo was upload
					$photo_uploaded = 1;
				} else {
					$error 		= 1;
					$error_msg 	= $this->upload->display_errors();
				}
			}
			# level filter
			if ($level_akun == 'administrator') {
				$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
				$feedBack = $this->model_users_cud->updateDataProfilAdministrator($data, $company_id);
			} else {
				$user_id = $this->session->userdata($this->config->item('apps_name'))['user_id'];
				$feedBack = $this->model_users_cud->updateDataProfilUsers($data, $user_id);
			}
			# filter feedBack
			if ($feedBack) {

				// 'logo' => $data_staff['logo'] != '' ? $data_staff['logo'] : 'logo.svg' ,
                //    'icon' => $data_staff['icon'] != '' ? 'company/icon/'.$data_staff['icon'] : 'icon.ico' ,
				# old session
				$old_session = $this->session->userdata($this->config->item('apps_name'));
				if ($level_akun == 'administrator') {
					$new_session = array(
							'Is_login' => true,
                     'company_type' => $old_session['company_type'],
                     'verified' => $old_session['verified'],
                     'start_date_subscribtion' => $old_session['start_date_subscribtion'],
                     'end_date_subscribtion' => $old_session['end_date_subscribtion'],
                     'company_id' => $old_session['company_id'],
                     'company_code' => $old_session['company_code'],
                     'company_name' => $data['name'],
                     'photo' => isset($data['photo']) ? $data['photo'] : $old_session['photo'] ,
                     'email' => $data['email'],
                     'logo' => $old_session['logo'],
                     'icon' => $old_session['icon'], 
                     'level_akun' => $old_session['level_akun'] ,
                     'modul_access' => $old_session['modul_access']);
				} else {
					$new_session =  array(
						'Is_login' => true,
						'company_type' => $old_session['company_type'],
						'verified' => $old_session['verified'],
						'start_date_subscribtion' => $old_session['start_date_subscribtion'],
						'end_date_subscribtion' => $old_session['end_date_subscribtion'],
						'company_id' => $old_session['company_id'],
						'company_code' => $old_session['company_code'],
						'company_name' => $old_session['company_name'],
						'photo' => isset($data['photo']) ? $data['photo'] : $old_session['photo'] ,
						'user_id' => $old_session['user_id'],
						'logo' => $old_session['logo'],
                     	'icon' => $old_session['icon'],
						'nomor_whatsapp' => $old_session['nomor_whatsapp'],
						'fullname' => $data['name'],
						'level_akun' => 'staff',
						'modul_access' => $old_session['modul_access']);
				}
				# delete old photo if exist
				if ($photo_uploaded == 1) {
					# get old photo name
					$photo = $this->session->userdata($this->config->item('apps_name'))['photo'];
					if ($level_akun == 'administrator') {
						$src = FCPATH . 'image/company/' . $photo;
						# create new photo session
						$new_session['photo'] = 'company/' . $data['photo'];
					} else {
						$src = FCPATH . 'image/personal/' . $photo;
						# create new photo session
						$new_session['photo'] = 'personal/' . $data['photo'];
					}
					if (file_exists($src)) {
						unlink($src);
					}
				} else {
					$new_session['photo'] = $old_session['photo'];
				}
				# update Session
				$this->session->set_userdata(array($this->config->item('apps_name') => $new_session));
				# create return variable
				$return = array(
					'error'		=> false,
					'error_msg' => 'Proses update data profil berhasil dilakukan.',
					'company_code' => $this->session->userdata($this->config->item('apps_name'))['company_code'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses update data profil gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error' => true,
					'error_msg' => validation_errors('<span class="error">', '</span>'),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	function perpanjang_berlangganan(){
		$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		$company_code = $this->session->userdata($this->config->item('apps_name'))['company_code'];
		# filter
		if ( ! $this->model_users_cud->update_process_peyment($company_id) ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Proses pembayaran gagal dilakukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Success.',
				'data' => $company_code,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}
}
