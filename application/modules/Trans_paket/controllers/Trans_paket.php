<?php

/**
 *  -----------------------
 *	Users Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_paket extends CI_Controller
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
		$this->load->model('Model_trans_paket', 'model_trans_paket');
		# model trans paket cud
		$this->load->model('Model_trans_paket_cud', 'model_trans_paket_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# company
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

	function Daftar_paket_transaksi()
	{
		$error = 0;
		$paket = $this->model_trans_paket->get_paket_transaksi();
		if ( count( $paket ) <= 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data paket tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data paket berhasil ditemukan.',
				'data' => $paket,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function daftar_jamaah_trans_paket()
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

			$total 	= $this->model_trans_paket->get_total_all_trans_jamaah($search);
			$list 	= $this->model_trans_paket->get_index_trans_jamaah($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data biaya pembayaran SPP tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data biaya pembayaran SPP berhasil ditemukan.',
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

	function info_jamaah()
	{
		$error = 0;
		$gender = array('Laki-laki', 'Perempuan');
		$golongan_darah = array('Pilih Golongan Darah', 'O', 'A', 'B', 'AB');
		$jamaah = $this->model_trans_paket->get_jamaah();

		$status_nikah = array('MENIKAH' => 'MENIKAH', 'BELUM MENIKAH' => 'BELUM MENIKAH', 'JANDA / DUDA' => 'JANDA / DUDA');
		$title = array('TUAN' => 'TUAN', 'NONA' => 'NONA', 'NYONYA' => 'NYONYA');
		$kewarganegaraan = array('WNI' => 'WNI', 'WNA' => 'WNA');
		$jenis_identitas = array('NIK' => 'NIK', 'KITAS' => 'KITAS', 'KITAP' => 'KITAP', 'PASPOR' => 'PASPOR');

		$provinsi = $this->model_trans_paket->get_provinsi();
		$kabupaten_kota = array( '-- Pilih Kabupaten / Kota --' );
		$kecamatan = array( '-- Pilih Kecamatan --' );
		$kelurahan = array( '-- Pilih Kelurahan --' );

		$status_mahram = $this->model_trans_paket->get_status_mahram();
		$pengalaman_haji_umrah = array(
			'Belum Pernah', 'Sudah', 'Sudah 1 Kali', 'Sudah 2 Kali', 'Sudah 3 Kali', 'Sudah 4 Kali', 'Sudah 5 Kali',
			'Sudah 6 Kali', 'Sudah 7 Kali', 'Sudah 8 Kali', 'Sudah 9 Kali', 'Sudah 10 Kali', 'Sudah 11 Kali'
		);
		$pekerjaan = $this->model_trans_paket->get_pekerjaan();
		$pendidikan = $this->model_trans_paket->get_pendidikan();
		$info_agen = $this->model_trans_paket->get_list_agen();

		if ($error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data info jamaah tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info jamaah berhasil ditemukan.',
				'data' => array(
					'list_agen' => $info_agen,
					'gender' => $gender,
					'golongan_darah' => $golongan_darah,
					'jamaah' => $jamaah,
					'status_nikah' => $status_nikah,
					'title' => $title,
					'kewarganegaraan' => $kewarganegaraan,
					'jenis_identitas' => $jenis_identitas,
					'status_mahram' => $status_mahram,
					'pengalaman_haji_umrah' => $pengalaman_haji_umrah,
					'pendidikan' => $pendidikan,
					'pekerjaan' => $pekerjaan,
					'provinsi' => $provinsi, 
					'kabupaten_kota' => $kabupaten_kota, 
					'kecamatan' => $kecamatan, 
					'kelurahan' => $kelurahan
				),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// check jamaah by nomor identitas
	function _ckJamaahByNomorIdentitas($nomor_identitas)
	{
		if ($this->model_trans_paket->checkJamaahByNomorIdentitas($nomor_identitas)) {
			$this->form_validation->set_message('_ckJamaahByNomorIdentitas', 'Nomor identitas ini sudah terdaftar sebagai member');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function checkPersonalInfo()
	{
		$return 	= array();
		$error 		= 0;
		$error_mess = '';
		$this->form_validation->set_rules('nomor_identitas',	'<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckJamaahByNomorIdentitas');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$feedBack 	= $this->model_trans_paket->get_personal_info($this->input->post('nomor_identitas'));
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Nomor Identitas Sudah Terdaftar.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error' => false,
					'error_msg' => 'Nomor Identitas Belum Terdaftar.',
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

	function checkNomorWA()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('personal_id', '<b>Personal ID<b>', 'trim|xss_clean|min_length[1]');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			// nomor whatsapp
			$nomor_whatsapp = $this->input->post('nomor_whatsapp');
			// check nomor whatsapps
			if ($this->input->post('personal_id')) {
				$personal_id = $this->input->post('pesonal_id');
				# filter
				$feedBack = $this->model_trans_paket->checkNomorWhatsapp($nomor_whatsapp, $personal_id);
			} else {
				# filter
				$feedBack = $this->model_trans_paket->checkNomorWhatsapp($nomor_whatsapp);
			}

			if ($feedBack === true) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Nomor Whatsapp sudah terdaftar dipangkalan data. Silahkan pilih nomor whatsapp yang lain.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Nomor Whatsapp tersedia.',
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

	# blood type checking
	function _ck_blood_type($str)
	{
		if ($str != 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_blood_type', 'Anda wajib memilih salah satu golongan darah');
			return FALSE;
		}
	}

	# Check identity number
	function _ck_identity_number($str)
	{
		if ($this->input->post('personal_id')) {
			$personal_id = $this->input->post('personal_id');
			$feedBack = $this->model_trans_paket->check_identity_number($str, $personal_id);
		} else {
			$feedBack = $this->model_trans_paket->check_identity_number($str);
		}
		if ($feedBack) {
			$this->form_validation->set_message('_ck_identity_number', 'Nomor identitas sudah terdaftar');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# check jamaah is exist
	function _ck_jamaah_is_exist_by_personal_id($personal_id)
	{
		if ($personal_id != '') {
			if ($this->input->post('jamaah_id')) {
				$jamaah_id = $this->input->post('jamaah_id');
				$feedBack = $this->model_trans_paket->check_jamaah_is_exist_by_personal_id($personal_id, $jamaah_id);
			} else {
				$feedBack = $this->model_trans_paket->check_jamaah_is_exist_by_personal_id($personal_id);
			}
			if ($feedBack) {
				$this->form_validation->set_message('_ck_jamaah_is_exist_by_personal_id', 'Personal info ini sudah terdaftar sebagai jamaah sebelumnya.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_agen_is_exist($agen)
	{
		if( $agen != 0 ){
			if( ! $this->model_trans_paket->check_agen_is_exist($agen) ) {
				$this->form_validation->set_message('_ck_agen_is_exist', 'Id agen tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	function _ck_pendidikan($pendidikan)
	{
		if( ! $this->model_trans_paket->check_pendidikan($pendidikan) ) {
			$this->form_validation->set_message('_ck_pendidikan', 'Id Pendidikan tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _ck_pekerjaan($pekerjaan)
	{
		if( ! $this->model_trans_paket->check_pekerjaan($pekerjaan) ) {
			$this->form_validation->set_message('_ck_pekerjaan', 'Id Pekerjaan tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _ck_kelurahan_id_exist( $kelurahan_id ){
		if( ! $this->model_trans_paket->check_kelurahan_id( $kelurahan_id ) ) {
			$this->form_validation->set_message('_ck_kelurahan_id_exist', 'Kelurahan Id tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// add update jamaah
	function add_update_jamaah()
	{
		$return = array();
		$error = 0;
		$uploadPhoto = 0;
		$error_msg = 'Anda wajib mengupload photo';
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('personal_id', '<b>Personal ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist_by_personal_id');
		$this->form_validation->set_rules('nama_jamaah', '<b>Nama Jamaah<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_pasport', '<b>Nama Pasport<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_identity_number');
		$this->form_validation->set_rules('jenis_kelamin',	'<b>Jenis Kelamin<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,1]');
		$this->form_validation->set_rules('golongan_darah', '<b>Golongan Darah<b>', 'trim|xss_clean|min_length[1]|in_list[0,1,2,3,4]|callback__ck_blood_type');
		$this->form_validation->set_rules('tempat_lahir', '<b>Tempat Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_lahir',	'<b>Tanggal Lahir<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat', '<b>Alamat<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kode_pos', '<b>Kode Pos<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('agen', '<b>Agen<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_agen_is_exist');
		$this->form_validation->set_rules('telephone', '<b>Telephone<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('email', '<b>Email<b>', 'trim|xss_clean|valid_email|min_length[1]');
		$this->form_validation->set_rules('nomor_passport', '<b>Nomor Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tempat_dikeluarkan', '<b>Tempat Dikeluakan Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_dikeluarkan',	'<b>Tanggal Dikeluarkan Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('masa_berlaku', '<b>Masa Berlaku Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_ayah', '<b>Nama Ayah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_keluarga', '<b>Alamat Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('title', '<b>Title<b>', 'trim|xss_clean|required|min_length[1]|in_list[TUAN,NONA,NYONYA]');
		$this->form_validation->set_rules('kewarganegaraan', '<b>Kewarganegaraan<b>', 'trim|xss_clean|required|min_length[1]|in_list[WNI,WNA]');
		$this->form_validation->set_rules('jenis_identitas', '<b>Jenis Identitas<b>', 'trim|xss_clean|required|min_length[1]|in_list[NIK,KITAS,KITAP,PASPOR]');
		$this->form_validation->set_rules('status_nikah', '<b>Status Nikah<b>', 'trim|required|xss_clean|min_length[1]|in_list[MENIKAH,BELUM MENIKAH,JANDA / DUDA]');
		$this->form_validation->set_rules('kelurahan', '<b>Kelurahan ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_kelurahan_id_exist');
		$this->form_validation->set_rules('telephone_keluarga', '<b>Telephone Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_nikah',	'<b>Tanggal Nikah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_keluarga',	'<b>Nama Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pengalaman_haji', '<b>Pengalaman Haji<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]');
		$this->form_validation->set_rules('tahun_haji',	'<b>Tahun Haji<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pengalaman_umrah', '<b>Pengalaman Umrah<b>', 'trim|xss_clean|min_length[1]|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]');
		$this->form_validation->set_rules('tahun_umrah', '<b>Tahun Umrah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('berangkat_dari', '<b>Berangkat Dari<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pekerjaan', '<b>Pekerjaan Jamaah<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pekerjaan');
		$this->form_validation->set_rules('alamat_instansi', '<b>Alamat Instansi Pekerjaan Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_instansi',	'<b>Nama Instansi Pekerjaan Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('telephone_instansi', '<b>Telephone Instansi Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pendidikan_terakhir', '<b>Pendidikan Terakhir Jamaah<b>', 'trim|xss_clean|min_length[1]|callback__ck_pendidikan');
		$this->form_validation->set_rules('penyakit', '<b>Penyakit Yang Diderita Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('photo_4_6', '<b>Photo 4x6<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('photo_3_4', '<b>Photo 3x4<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('fc_passport', '<b>Fotocopy Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('fc_kk', '<b>Fotocopy Kartu Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('fc_ktp', '<b>Fotocopy KTP<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('buku_nikah',	'<b>Buku Nikah Asli<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('akte_lahir',	'<b>Akte Lahir<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('buku_kuning', '<b>Buku Kuning<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_whatsapp', '<b>Nomor Whatsapp<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('password', '<b>Password<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('confirm_password', '<b>Password Konfirmasi<b>', 'trim|xss_clean|min_length[1]|matches[password]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$dataParam = array();
			if ( $this->input->post('jamaah_id') ) {
				$dataParam['jamaah_id'] = $this->input->post('jamaah_id');
			}
			if ( $this->input->post('personal_id') ) {
				$dataParam['personal_id'] = $this->input->post('personal_id');
			}

			$dataPersonal = array();
			$dataPersonal['fullname'] = $this->input->post('nama_jamaah');
			$dataPersonal['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$dataPersonal['gender'] = $this->input->post('jenis_kelamin');
			$dataPersonal['birth_place'] = $this->input->post('tempat_lahir');
			$dataPersonal['birth_date'] = $this->input->post('tanggal_lahir');
			$dataPersonal['address'] = $this->input->post('alamat');
			$dataPersonal['email'] = $this->input->post('email');
			$dataPersonal['identity_number'] = $this->input->post('no_identitas');
			
			$nomor_whatsapp = $this->input->post('nomor_whatsapp');
			// if ( substr($nomor_whatsapp, 0, 1) == '0' ) {
			// 	$nomor_whatsapp = '62' . substr($nomor_whatsapp, 1);
			// }
			$dataPersonal['nomor_whatsapp'] = $nomor_whatsapp;

			if ( $this->input->post('password') != '' ) {
				$dataPersonal['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
			}
			$old_photo_name = '';
			// image
			if ( $this->input->post('base64image') and strpos($this->input->post('base64image'), 'data:image/jpeg;base64,') !== false ) {
				//define name
				if ($this->input->post('personal_id')) {
					$file_name 	= $this->model_trans_paket->getPhotoPersonalName($this->input->post('personal_id'));
					if ($file_name == '') {
						$file_name = md5(date('Ymdhis')) . '.jpeg';
					} else {
						$old_photo_name = $file_name;
					}
				} else {
					$file_name = md5(date('Ymdhis')) . '.jpeg';
				}

				$img = str_replace('data:image/jpeg;base64,', '', $this->input->post('base64image'));
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				$file =  FCPATH . '/image/personal/' . $file_name;
				$success = file_put_contents($file, $data);
				$dataPersonal['photo'] = $file_name;
			}
			// data jamaah
			$dataJamaah = array();
			$dataJamaah['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$dataJamaah['blood_type'] = $this->input->post('golongan_darah');
			$dataJamaah['pos_code'] = $this->input->post('kode_pos');
			$dataJamaah['telephone'] = $this->input->post('telephone');
			if( $this->input->post('agen') != '0' ) {
				$dataJamaah['agen_id'] = $this->input->post('agen');
			}
			$dataJamaah['title'] = $this->input->post('title');
			$dataJamaah['pasport_name'] = $this->input->post('nama_pasport');
			$dataJamaah['kewarganegaraan'] = $this->input->post('kewarganegaraan');
			$dataJamaah['jenis_identitas'] = $this->input->post('jenis_identitas');
			$dataJamaah['kelurahan_id'] = $this->input->post('kelurahan');
			$dataJamaah['passport_number'] = $this->input->post('nomor_passport');
			$dataJamaah['passport_place'] = $this->input->post('tempat_dikeluarkan');
			$dataJamaah['passport_dateissue'] = $this->input->post('tanggal_dikeluarkan');
			$dataJamaah['validity_period'] = $this->input->post('masa_berlaku');
			$dataJamaah['father_name'] = $this->input->post('nama_ayah');
			$dataJamaah['alamat_keluarga'] = $this->input->post('alamat_keluarga');
			$dataJamaah['status_nikah'] = $this->input->post('status_nikah') == '0' ? 'belum_nikah' : 'nikah';
			$dataJamaah['telephone_keluarga'] = $this->input->post('telephone_keluarga');
			$dataJamaah['tanggal_nikah'] = $this->input->post('tanggal_nikah');
			$dataJamaah['nama_keluarga'] = $this->input->post('nama_keluarga');
			$dataJamaah['hajj_experience'] = $this->input->post('pengalaman_haji');
			$dataJamaah['hajj_year'] = $this->input->post('tahun_haji');
			$dataJamaah['umrah_experience'] = $this->input->post('pengalaman_umrah');
			$dataJamaah['umrah_year'] = $this->input->post('tahun_umrah');
			$dataJamaah['departing_from'] = $this->input->post('berangkat_dari');
			$dataJamaah['pekerjaan_id'] = $this->input->post('pekerjaan');
			$dataJamaah['profession_instantion_address'] = $this->input->post('alamat_instansi');
			$dataJamaah['profession_instantion_name'] = $this->input->post('nama_instansi');
			$dataJamaah['profession_instantion_telephone'] = $this->input->post('telephone_instansi');
			$dataJamaah['last_education'] = $this->input->post('pendidikan_terakhir');
			$dataJamaah['desease'] = $this->input->post('penyakit');
			$dataJamaah['photo_4_6'] = $this->input->post('photo_4_6') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['photo_3_4'] = $this->input->post('photo_3_4') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['fc_passport'] = $this->input->post('fc_passport') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['fc_kk'] = $this->input->post('fc_kk') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['fc_ktp'] = $this->input->post('fc_ktp') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['buku_nikah'] = $this->input->post('buku_nikah') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['akte_lahir'] = $this->input->post('akte_lahir') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['buku_kuning'] = $this->input->post('buku_kuning') == '1' ? 'ada' : 'tidak ada';
			$dataJamaah['keterangan'] = $this->input->post('keterangan');

			$dataMahram = array();
			$mahrams = $this->input->post('mahram');
			$status_mahram = $this->input->post('statusMahram');
			$list_mahram_jamaah = array();
			$list_status_mahram_jamaah = array();
			foreach ($mahrams as $key => $value) {
				if ($value != 0) {
					$list_mahram_jamaah[] = $value;
					$list_status_mahram_jamaah[] = $status_mahram[$key];
				}
			}
			if (count($list_mahram_jamaah) > 0) {
				$dataMahram['mahram_id'] = $mahrams;
				$dataMahram['status'] = $status_mahram;
			}

			$feedBack = $this->model_trans_paket_cud->add_update_jamaah($dataParam, $dataPersonal, $dataJamaah, $dataMahram);
			if ($feedBack === FALSE) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data jamaah berhasil disimpan.',
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

	# delette jamaah
	function delete_jamaah()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$id = $this->input->post('id');
			# info jamaah
			$info_jamaah = $this->model_trans_paket->fullname_jamaah($id);
			# feed back
			$feedBack = $this->model_trans_paket_cud->delete_jamaah($id, $info_jamaah);
			if ($feedBack === FALSE) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data jamaah gagal dihapus',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data jamaah berhasil dihapus.',
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

	// check jamaah is exist
	function _ck_jamaah_is_exist($jamaah_id)
	{
		if ($jamaah_id != '') {
			if ($this->model_trans_paket->check_jamaah_is_exist($jamaah_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_jamaah_is_exist', 'ID Jamaah tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	# check exist provinsi id
	function _ck_provinsi_id($provinsi_id){
		if( ! $this->model_trans_paket->check_provinsi_id( $provinsi_id ) ) {
			$this->form_validation->set_message('_ck_provinsi_id', 'Provinsi ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	# check kabupaten kota id
	function _ck_kabupaten_kota_id( $kabupaten_kota_id ) {
		if( ! $this->model_trans_paket->check_kabupaten_kota_id( $kabupaten_kota_id ) ) {
			$this->form_validation->set_message('_ck_kabupaten_kota_id', 'Kabupaten Kota ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function get_kabupaten_kota(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('provinsi',	'<b>Provinsi ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_provinsi_id');
		/*
         Validation process
      */
		if ( $this->form_validation->run() ) {
			# kabupaten kota
			$kabupaten_kota = $this->model_trans_paket->get_kabupaten_kota_by_provinsi_id( $this->input->post('provinsi') ) ;
			// feedBack
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data kabupaten kota berhasil ditemukan.',
				'kabupaten_kota' => $kabupaten_kota,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
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

	function get_kecamatan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('provinsi',	'<b>Provinsi ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_provinsi_id');
		$this->form_validation->set_rules('kabupaten_kota',	'<b>Kabupaten Kota ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_kabupaten_kota_id');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			# kecamatan
			$kecamatan = $this->model_trans_paket->get_kecamatan_by_provinsi_id_and_kabupaten_kota_id( $this->input->post('provinsi'), $this->input->post('kabupaten_kota') ) ;
			// feedBack
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data kecamatan berhasil ditemukan.',
				'kecamatan' => $kecamatan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
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

	# Kecamatan ID
	function _ck_kecamatan_id($kecamatan_id)
	{
		if( ! $this->model_trans_paket->check_kecamatan_id( $kecamatan_id ) ) {
			$this->form_validation->set_message('_ck_kecamatan_id', 'Kecamatan ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}


	function get_kelurahan(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('kecamatan',	'<b>Kecamatan ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_kecamatan_id');
		/*
	        Validation process
	    */
		if ( $this->form_validation->run() ) {
			# kelurahan
			$kelurahan = $this->model_trans_paket->get_kelurahan_by_kecamatan_id( $this->input->post('kecamatan') ) ;
			// feedBack
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data kelurahan berhasil ditemukan.',
				'kelurahan' => $kelurahan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
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

	function edit_jamaah()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('jamaah_id',	'<b>Jamaah ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$jamaah_id = $this->input->post('jamaah_id');
			$gender = array('Laki-laki', 'Perempuan');
			$golongan_darah = array('Pilih Golongan Darah', 'O', 'A', 'B', 'AB');
			$jamaah = $this->model_trans_paket->get_jamaah($jamaah_id);
			$status_nikah = array('MENIKAH' => 'MENIKAH', 'BELUM MENIKAH' => 'BELUM MENIKAH', 'JANDA / DUDA' => 'JANDA / DUDA');
			$title = array('TUAN' => 'TUAN', 'NONA' => 'NONA', 'NYONYA' => 'NYONYA');
			$kewarganegaraan = array('WNI' => 'WNI', 'WNA' => 'WNA');
			$jenis_identitas = array('NIK' => 'NIK', 'KITAS' => 'KITAS', 'KITAP' => 'KITAP', 'PASPOR' => 'PASPOR');
			$status_mahram = $this->model_trans_paket->get_status_mahram();
			$pengalaman_haji_umrah = array(
				'Belum Pernah',
				'Sudah',
				'Sudah 1 Kali',
				'Sudah 2 Kali',
				'Sudah 3 Kali',
				'Sudah 4 Kali',
				'Sudah 5 Kali',
				'Sudah 6 Kali',
				'Sudah 7 Kali',
				'Sudah 8 Kali',
				'Sudah 9 Kali',
				'Sudah 10 Kali',
				'Sudah 11 Kali'
			);
			$pendidikan = $this->model_trans_paket->get_pendidikan();
			$info_agen = $this->model_trans_paket->get_list_agen();
			$pekerjaan = $this->model_trans_paket->get_pekerjaan();
			# value
			$value = $this->model_trans_paket->get_data_jamaah($jamaah_id);
			# get data
			$get_data_regional = $this->model_trans_paket->get_list_provinsi_kab_kota_kec_kel_by_kelurahan_id( $value['kecamatan_id'], $value['kabupaten_kota_id'], $value['provinsi_id'] );
			// feedBack
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info jamaah berhasil ditemukan.',
				'data' => array(
					'gender' => $gender,
					'golongan_darah' => $golongan_darah,
					'jamaah' => $jamaah,
					'status_nikah' => $status_nikah,
					'title' => $title,
					'kewarganegaraan' => $kewarganegaraan,
					'jenis_identitas' => $jenis_identitas,
					'status_mahram' => $status_mahram,
					'pengalaman_haji_umrah' => $pengalaman_haji_umrah,
					'pekerjaan' => $pekerjaan,
					'pendidikan' => $pendidikan,
					'list_agen' => $info_agen,
					'provinsi' => $get_data_regional['provinsi'],
					'kabupaten_kota' => $get_data_regional['kabupaten_kota'],
					'kecamatan' => $get_data_regional['kecamatan'],
					'kelurahan' => $get_data_regional['kelurahan'],
				),
				'value' => $value,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
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

	function _ck_personal_id($personal_id)
	{
		if ($this->model_trans_paket->check_personal_id($personal_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_personal_id', 'Personal ID tidak ditemukan.');
			return FALSE;
		}
	}
	# delete photo
	function delete_photo()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('personal_id',	'<b>Personal ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_personal_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# get personal id value
			$personal_id = $this->input->post('personal_id');
			# get photo name
			$photo = $this->model_trans_paket->get_photo_name($personal_id);
			if ($photo != '') {
				# define path
				$src = FCPATH . 'image/personal/' . $photo;
				# check if file exist
				if (file_exists($src)) {
					# delete file
					if (!unlink($src)) {
						$error = 1;
					}
				}
			}
			# filter feedBack
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Photo gagal dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Photo berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
				# delete photo
				$this->model_trans_paket_cud->delete_photo($personal_id);
				# write log
				$this->syslog->write_log('Mengupdate dengan menghapus photo member dengan personal ID ' . $personal_id . ' dan photo name ' . $photo);
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

	function _ck_paket_id($paket_id)
	{
		if ($this->model_trans_paket->check_paket_id($paket_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_paket_id', 'Paket ID tidak ditemukan.');
			return FALSE;
		}
	}

	# paket name
	function get_paket_name()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id',	'<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# receive paket id
			$paket_id = $this->input->post('paket_id');
			# get status paket
			$status_paket = $this->model_trans_paket->get_status_paket($paket_id);
			# get paket name
			$paket_name = $this->model_trans_paket->get_paket_name($paket_id);
			# filter feedBack
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Nama paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Nama paket berhasil ditemukan.',
					'paket_name' => $paket_name,
					'status_paket' => $status_paket,
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

	# update Info Visa
	function updateInfoVisa(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id',	'<b>Paket Transaction ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info visa
			$getInfoVisa = $this->model_trans_paket->getInfoVisa( $this->input->post('paket_transaction_id') );
			# filter
			if (  count( $getInfoVisa ) == 0 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info visa tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info visa berhasil ditemukan.',
					'data' => $getInfoVisa,
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

	function prosesUpdateInfoVisa(){

		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id',	'<b>Paket Transaction ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('nomor_visa',	'<b>Nomor Visa<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tgl_berlaku_visa',	'<b>Tanggal Berlaku Visa<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tgl_akhir_visa',	'<b>Tanggal Akhir Visa<b>', 	'trim|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$data = array();
			if( $this->input->post('nomor_visa') ) {
				$data['no_visa'] = $this->input->post('nomor_visa');
			}
			if( $this->input->post('tgl_berlaku_visa') ) {
				$data['tgl_berlaku_visa'] = $this->input->post('tgl_berlaku_visa');
			}
			if( $this->input->post('tgl_akhir_visa') ) {
				$data['tgl_akhir_visa'] = $this->input->post('tgl_akhir_visa');
			}
			# update data visa process
			if (  ! $this->model_trans_paket_cud->update_data_visa($this->input->post('paket_transaction_id'), $data )  ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses update data visa gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses update data visa berhasil dilakukan.',
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

	function daftar_transaksi_paket_by_paket_id()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id',	'<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$status_paket = $this->model_trans_paket->get_status_paket($paket_id);
			$total 	= $this->model_trans_paket->get_total_transaksi_paket($search, $paket_id);
			$list 	= $this->model_trans_paket->get_index_transaksi_paket($perpage, $start_at, $search, $paket_id);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi paket berhasil ditemukan.',
					'total' => $total,
					'data' => $list,
					'status_paket' => $status_paket,
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


	function get_daftar_jamaah_paket_by_paket_id()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id',	'<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			// $status_paket = $this->model_trans_paket->get_status_paket( $paket_id );
			$total 	= $this->model_trans_paket->get_total_jamaah_paket($search, $paket_id);
			$list 	= $this->model_trans_paket->get_index_jamaah_paket($perpage, $start_at, $search, $paket_id);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data jamaah paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data jamaah paket berhasil ditemukan.',
					'total' => $total,
					'data' => $list,
					// 'status_paket' => $status_paket,
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

	function get_info_transaction()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			// receive paket id
			$paket_id = $this->input->post('paket_id');
			// jenis paket
			$paket_type = $this->model_trans_paket->get_price_list_paket_by_paket_id($paket_id);
			// jamaah tim
			$jamaah = $this->model_trans_paket->get_jamaah_not_in_paket($paket_id);
			// agen
			$agen = $this->model_trans_paket->get_agen();
			// get invoice
			$invoice = $this->text_ops->get_invoice_transaksi_paket();
			// nomor Registrasi
			$no_register = $this->text_ops->get_no_register();
			// paket_name
			$paket_name = $this->model_trans_paket->get_paket_name($paket_id);
			// filter error
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info transaksi paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info transaksi paket berhasil ditemukan.',
					'data' 		=> array(
						'paket_name' => strtoupper($paket_name),
						'paket_type' => $paket_type,
						'jamaah' => $jamaah,
						'agen' => $agen,
						'invoice' => $invoice,
						'no_register' => $no_register,
					),
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

	function _ck_get_price_transaksi_paket($paket_type_id)
	{
		if ($this->model_trans_paket->check_price_paket($this->input->post('paket_id'), $paket_type_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_get_price_transaksi_paket', 'Tipe paket tidak ditemukan didalam paket.');
			return FALSE;
		}
	}

	function _ck_jumlah_jamaah($jumlah)
	{
		if ($jumlah > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_jumlah_jamaah', 'Jumlah jamaah wajib lebih dari satu.');
			return FALSE;
		}
	}

	// get price transaction paket
	function get_price_transaksi_paket()
	{
		$return = array();
		$error = 0;
		$harga = 0;
		$jumlah = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id',	'<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('paket_type_id', '<b>Jenis Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_get_price_transaksi_paket');
		$this->form_validation->set_rules('pembayaran', '<b>Pembayaran<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('diskon', '<b>Diskon<b>', 'trim|xss_clean|min_length[1]');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$paket_type_id = $this->input->post('paket_type_id');
			$jumlah_jamaah = $this->input->post('jumlah');
			$jamaah = $this->input->post('jamaah');
			$pembayaran = $this->text_ops->hide_currency($this->input->post('pembayaran') != '' ? $this->input->post('pembayaran') : $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' 0');
			$dp = $this->text_ops->hide_currency($this->input->post('dp') != '' ? $this->input->post('dp') : $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' 0');
			$diskon = $this->text_ops->hide_currency($this->input->post('diskon') != '' ? $this->input->post('diskon') : $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' 0');
			// PROCESS
			// get harga paket
			$harga = $this->model_trans_paket->get_price_transaksi_paket($paket_id, $paket_type_id);
			// get biaya mahram
			$biaya_mahram = $this->model_trans_paket->get_biaya_mahram($paket_id);
			// total harga
			$total_paket = $harga;
			// kurang diskon
			$total_paket = $total_paket - $diskon;
			$total_biaya_mahram = 0;
			if ( $this->model_trans_paket->get_info_need_mahram($jamaah) === TRUE ) {
				$total_paket = $total_paket + $total_biaya_mahram;
				$total_biaya_mahram = $biaya_mahram;
			}
			// sisa pembayaran paket
			$totalpembayaran = $pembayaran + $dp;
			$sisa = $total_paket - $totalpembayaran;
			// return
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info transaksi paket berhasil ditemukan.',
				'harga_per_pax' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($harga) . ',-',
				'harga_total' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_paket) . ',-',
				'biaya_mahram' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_biaya_mahram) . ',-',
				'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($sisa) . ',-',
				// 'needMahram' => $jamaah_needMahram,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
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

	function getInvoice()
	{
		$return = array();
		$invoice = $this->text_ops->get_invoice_transaksi_paket();
		if ($invoice == '0') {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Invoice gagal di generated.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Success.',
				'invoice' => $invoice,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	# get personal info
	function depositorPaket()
	{
		// personal
		$error = 0;
		$listpersonal = $this->model_trans_paket->depositorPaket();
		if ($error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data personal tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data personal berhasil ditemukan.',
				'data' => array('listpersonal' => $listpersonal),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	# check paket transaction id
	function _ck_paket_transaction_id()
	{
		if ($this->input->post('paket_transaction_id')) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			if ($this->model_trans_paket->check_paket_transaction_id($paket_transaction_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_paket_transaction_id', 'ID paket transaksi tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	// callback nomor registration
	function _ckNoRegister($nomor_register)
	{
		if ($this->input->post('paket_transaction_id')) {
			$feedBack = $this->model_trans_paket->checkNoRegisterExist($nomor_register, $this->input->post('paket_transaction_id'));
		} else {
			$feedBack = $this->model_trans_paket->checkNoRegisterExist($nomor_register);
		}
		if ($feedBack) {
			$this->form_validation->set_message('_ckNoRegister', 'Nomor register sudah terdaftar.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ckPaketTypeExist($paket_type_id)
	{
		if ($this->model_trans_paket->checkPaketTypePaket($this->input->post('paket_id'), $paket_type_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckPaketTypeExist', 'Tipe paket tidak ditemukan pada paket ini.');
			return FALSE;
		}
	}

	function _ckAgen($agen)
	{
		if ($agen != 0) {
			if ($this->model_trans_paket->checkAgenExist($agen)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ckAgen', 'ID agen tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	# fungsi ini untuk mengecek id agen
	function _ckIDAgen($agen_id)
	{
		if ($agen_id != 0) {
			if ($this->model_trans_paket->checkAgenExist($agen_id)) {
				# agen id
				$id = $this->input->post('agen_id');
				if (in_array($agen_id, $id)) {
					if (count($id) > 1) {
						$upline = $this->model_trans_paket->get_upline_agen($agen_id);
						if (in_array($upline, $id)) {
							return TRUE;
						} else {
							$this->form_validation->set_message('_ckIDAgen', 'ID Cabang tidak terdaftar di list fee keagenan.');
							return FALSE;
						}
					} else {
						return TRUE;
					}
				} else {
					$this->form_validation->set_message('_ckIDAgen', 'ID Agen tidak terdaftar di list fee keagenan.');
					return FALSE;
				}
			} else {
				$this->form_validation->set_message('_ckIDAgen', 'ID agen tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ckLeaderTim($leader_tim)
	{
		$jamaah = $this->input->post('jamaah');
		$realJamaah = array_unique($jamaah);
		if (($key = array_search('0', $realJamaah)) !== false) {
			unset($realJamaah[$key]);
		}

		if (count($realJamaah) > 0) {
			if (in_array($leader_tim, $realJamaah)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ckLeaderTim', 'Leader tim harus salah satu dari jamaah.');
				return FALSE;
			}
		} else {
			$this->form_validation->set_message('_ckLeaderTim', 'Jumlah jamaah minimal 1 orang.');
			return FALSE;
		}
	}

	# Check sumber biaya
	function _ck_sumber_biaya($sumber_biaya)
	{
		if ( $sumber_biaya == 1 ) {
			$jamaah = $this->input->post('jamaah');
			$paket_id = $this->input->post('paket_id');
			$paket_type_id = $this->input->post('jenis_paket');
			if ( ! $this->model_trans_paket->check_deposit_jamaah( $paket_id, $paket_type_id, $jamaah ) ) {
				$this->form_validation->set_message('_ck_sumber_biaya', 'Deposit jamaah tidak mencukupi.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	// check penyetor
	function _ckPenyetor($penyetor)
	{
		if ($penyetor == '') {
			$this->form_validation->set_message('_ckPenyetor', 'Kolom penyetor tidak boleh kosong.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function _ckInvoiceExist($invoice)
	{
		if ($this->input->post('paket_transaction_id')) {
			$feedBack = $this->model_trans_paket->checkNoInvoiceExist($invoice, $this->input->post('paket_transaction_id'));
		} else {
			$feedBack = $this->model_trans_paket->checkNoInvoiceExist($invoice);
		}
		if ($feedBack) {
			$this->form_validation->set_message('_ckInvoiceExist', 'Invoice ID sudah terdaftar.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// check bulan
	function _ckBulan($bulan)
	{
		if ($this->input->post('metode_pembayaran') == 1) {
			if ($bulan != '') {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ckBulan', 'Untuk pembayaran cicilan, kolom bulan tidak boleh kosong.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ckPembayaran($pembayaran)
	{
		if ($this->input->post('metode_pembayaran') == 0) {
			if ($this->text_ops->hide_currency($pembayaran) == 0) {
				$this->form_validation->set_message('_ckPembayaran', 'Untuk melanjutkan proses ini, kolom pembayaran tidak boleh kosong!!!.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	function _ckDepositPembayaran($pembayaran)
	{
		if ($this->model_trans_paket->checkDepositPembayaran($this->text_ops->hide_currency($pembayaran), $this->input->post('penyetor'))) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckDepositPembayaran', 'Deposit tidak mencukupi.');
			return FALSE;
		}
	}

	# Check harga not null
	function _ck_harga_not_null($harga)
	{
		$price = $this->text_ops->hide_currency($harga);
		if ($price > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_not_null', 'Harga tidak boleh NOL.');
			return FALSE;
		}
	}

	function _ck_validasi_jamaah($jamaah_id){
		if( ! $this->model_trans_paket->check_jamaah_is_exist( $jamaah_id ) ){
			$this->form_validation->set_message('_ck_validasi_jamaah', 'Jamaah tidak terdaftar dipangkalan data.');
			return FALSE;
		}else{
			$paket_id = $this->input->post('paket_id');
			if( ! $this->model_trans_paket->check_jamaah_is_in_paket($paket_id, $jamaah_id) ) {
				return TRUE;
			}else{
				$this->form_validation->set_message('_ck_validasi_jamaah', 'Jamaah sudah terdaftar didalam paket.');
				return FALSE;
			}
		}
	}

	# transaction paket process
	function transaction_paket_process()
	{
		$return = array();
		$uploadPhoto = 0;
		$error = 0;
		$error_msg = 'Data paket berhasil disimpan';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('no_register', '<b>Nomor Register<b>', 'trim|required|xss_clean|min_length[1]|callback__ckNoRegister');
		$this->form_validation->set_rules('invoiceID', '<b>Invoice ID<b>', 'trim|required|xss_clean|callback__ckInvoiceExist');
		$this->form_validation->set_rules('jenis_paket', '<b>Jenis Paket<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckPaketTypeExist');
		$this->form_validation->set_rules('jamaah', '<b>Jamaah<b>', 'trim|required|xss_clean|callback__ck_validasi_jamaah');
		$this->form_validation->set_rules('nomor_visa', '<b>Nomor Visa<b>', 'trim|xss_clean');
		$this->form_validation->set_rules('tanggal_berlaku_visa', '<b>Tanggal Berlaku Visa<b>', 'trim|xss_clean');
		$this->form_validation->set_rules('tanggal_akhir_visa', '<b>Tanggal Berakhir Visa<b>', 'trim|xss_clean');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# company_id
			$company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			# level akun
			$level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
			# data
			$data = array();
			# info paket
			$info_paket = $this->model_trans_paket->get_simple_info_paket($this->input->post('paket_id'));
			$data['info_paket']['kode'] = $info_paket['kode'];
			$data['info_paket']['paket_name'] = $info_paket['paket_name'];
			# paket transaction
			$data['paket_transaction']['no_register'] = $this->input->post('no_register');
			$data['paket_transaction']['company_id'] = $company_id;
			$data['paket_transaction']['paket_id'] = $this->input->post('paket_id');
			$data['paket_transaction']['paket_type_id'] = $this->input->post('jenis_paket');
			// $data['paket_transaction']['diskon'] = $this->text_ops->hide_currency($this->input->post('diskon') != '' ? $this->text_ops->hide_currency($this->input->post('diskon')) : 0);
			$data['paket_transaction']['diskon'] = 0;
			$data['paket_transaction']['payment_methode'] = 0;
			$data['paket_transaction']['price_per_pax'] = $this->model_trans_paket->getPaketPricePerType($data['paket_transaction']['paket_id'], $data['paket_transaction']['paket_type_id']);

			if( $this->input->post('nomor_visa') != '' ) {
				$data['paket_transaction']['no_visa'] = $this->input->post('nomor_visa');
			}
			if( $this->input->post('nomor_visa') != '' ) {
				$data['paket_transaction']['tgl_berlaku_visa'] = $this->input->post('tanggal_berlaku_visa');
			}
			if( $this->input->post('nomor_visa') != '' ) {
				$data['paket_transaction']['tgl_akhir_visa'] = $this->input->post('tanggal_akhir_visa');
			}
			$data['paket_transaction']['input_date'] = date('Y-m-d H:i:s');
			$data['paket_transaction']['last_update'] = date('Y-m-d H:i:s');
			# jamaah
			$data['paket_transaction_jamaah']['jamaah_id'] = $this->input->post('jamaah');
			$data['paket_transaction_jamaah']['company_id'] = $company_id;
			# total paket
			$total_paket = $data['paket_transaction']['price_per_pax'] - $data['paket_transaction']['diskon'];
			# check apakah jamaah butuh mahram
			if( $this->model_trans_paket->get_info_need_mahram( $data['paket_transaction_jamaah']['jamaah_id'] ) ) {
				# biaya mahram
				$data['paket_transaction']['total_mahram_fee'] = $this->model_trans_paket->getBiayaMahram( $data['paket_transaction']['paket_id'] );
				# total paket
				$total_paket = $total_paket + $data['paket_transaction']['total_mahram_fee'];
			}
			$data['paket_transaction']['total_paket_price'] = $total_paket;
			# get personal id
			$personal_id = $this->model_trans_paket->get_personal_id_by_jamaah_id( $data['paket_transaction_jamaah']['jamaah_id'] );
			# get deposit
			$total_deposit = $this->model_trans_paket->get_total_deposit_paket($data['paket_transaction_jamaah']['jamaah_id']);
			# count sisa
			$sisa = $data['paket_transaction']['total_paket_price'] - $total_deposit;
			if ( $sisa <= 0 ) {
				$paid = $data['paket_transaction']['total_paket_price'];
				if ( $sisa < 0 ) {
					$nomor_transaksis = $this->random_code_ops->random_invoice_deposit_transaction();
					# proses insert sisa deposit ke deposit transaction
					$data['deposit_transaction'][] = array('nomor_transaction' =>$nomor_transaksis,
															'personal_id' => $personal_id,
															'company_id' => $company_id,
															'debet' => abs($sisa),
															'approver' => $level_akun == 'administrator' ? "Administrator" : $this->session->userdata($this->config->item('apps_name'))['fullname'],
															'transaction_requirement' => 'deposit',
															'info' => 'Sisa pembayaran paket dari deposit paket',
															'input_date' => date('Y-m-d H:i:s'),
															'last_update' => date('Y-m-d H:i:s'));
					// proses jurnal 
					 $data['jurnal'] = array('company_id' => $company_id,
	                                         'source' => 'depositsaldo:notransaction:'.$nomor_transaksis,
	                                         'ref' => 'Deposit Saldo Dengan No Transaction :'.$nomor_transaksis,
	                                         'ket' => 'Deposit Saldo Dengan No Transaction :'.$nomor_transaksis,
	                                         'akun_debet' => '24000',
	                                         'akun_kredit' => '23000',
	                                         'saldo' => abs($sisa),
	                                         'periode_id' => 0,
	                                         'input_date' => date('Y-m-d H:i:s'),
	                                         'last_update'  => date('Y-m-d H:i:s'));
				}
				# pembayaran deposit transaction
				$data['deposit_transaction'][] = array('nomor_transaction' => $this->random_code_ops->random_invoice_deposit_transaction(),
													   'personal_id' => $personal_id,
													   'company_id' => $company_id,
													   'kredit' => abs($paid),
													   'approver' => $level_akun == 'administrator' ? "Administrator" : $this->session->userdata($this->config->item('apps_name'))['fullname'],
													   'transaction_requirement' => 'paket_payment',
													   'info' => 'Pembayaran paket',
													   'input_date' => date('Y-m-d H:i:s'),
													   'last_update' => date('Y-m-d H:i:s'));
				# filter by metode pembayaran
				$infoDepositor = $this->model_trans_paket->getInfoDepositorByJamaahId( $data['paket_transaction_jamaah']['jamaah_id'] );
				# paket_transaction_history
				$data['paket_transaction_history']['invoice'] = $this->input->post('invoiceID');
				$data['paket_transaction_history']['company_id'] = $company_id;
				$data['paket_transaction_history']['source'] = 'deposit';
				$data['paket_transaction_history']['paid'] = $paid;
				# filter by level_akun
				if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
					$data['paket_transaction_history']['receiver'] = "Administrator";
				} else {
					$data['paket_transaction_history']['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				}
				$data['paket_transaction_history']['ket'] = 'cash';
				$data['paket_transaction_history']['deposit_name'] = $infoDepositor['fullname'];
				$data['paket_transaction_history']['deposit_phone'] = $infoDepositor['nomor_whatsapp'];
				$data['paket_transaction_history']['deposit_address'] = $infoDepositor['address'];
				$data['paket_transaction_history']['input_date'] = date('Y-m-d H:i:s');
				$data['paket_transaction_history']['last_update'] = date('Y-m-d H:i:s');
				# pool info
				$pool_info = $this->model_trans_paket->get_info_pool($data['paket_transaction_jamaah']['jamaah_id']);
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
				if ( ! $this->model_trans_paket_cud->insert_transaction_process( $data ) ) {
					$error = 1;
					$error_msg = 'Proses insert transaksi gagal dilakukan.';
				}
			} else {
				$error = 1;
				$error_msg = 'Proses tidak dapat dilanjutkan, karena deposit jamaah tidak mencukupi untuk melakukan pembelian paket.';
			}
			# filter
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				# create session priting here
				$this->session->set_userdata(
					array('cetak_invoice' => array(
							'type' => 'paket',
							'nomor_registrasi' => $this->input->post('no_register'),
							'invoice' => $this->input->post('invoiceID')
				)));
				# return
				$return = array(
					'error'	=> false,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error' => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	# delete transaksi paket
	function deleteTransaksiPaket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {

			$paket_transaction_id = $this->input->post('paket_transaction_id');

			$paket_id = $this->input->post('paket_id');

			$data = $this->model_trans_paket->getInfoTransaksiPaket($paket_id, $paket_transaction_id);

		    $this->db->select('id')
		         ->from('pool')
		         ->where('jamaah_id', $data['jamaah_id'])
		         ->where('active', 'active');
		    $q = $this->db->get();
		    if( $q->num_rows() > 0 ) {
		        $row = $q->row();
				// create deposit_transaction
				$data['deposit_transaction']['nomor_transaction'] = $this->random_code_ops->random_invoice_deposit_transaction();
				$data['deposit_transaction']['personal_id'] = $data['personal_id'];
				$data['deposit_transaction']['company_id'] = $this->company_id;
				$data['deposit_transaction']['debet'] = $data['total_price'];
				$data['deposit_transaction']['kredit'] = 0;
				if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
					$data['deposit_transaction']['approver'] = 'Administrator';
				} else {
					$data['deposit_transaction']['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				}
				$data['deposit_transaction']['sumber_dana'] = 'cash';
				$data['deposit_transaction']['no_tansaksi_sumber_dana'] = '';
				$data['deposit_transaction']['transaction_requirement'] = 'paket_deposit';
				$data['deposit_transaction']['info'] = 'Pengembalian Dana Pembelian Paket Ke Tabungan Umrah';
				$data['deposit_transaction']['paket_transaction_id'] = $paket_transaction_id;
				$data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
				$data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');
				// create pool_deposit_transaction
				$data['pool_deposit_transaction']['company_id'] = $this->company_id;
				$data['pool_deposit_transaction']['pool_id'] = $row->id ;
		    }else{
		         // create pool baru 
		    	$data['pool']['company_id']= $this->company_id;
		    	$data['pool']['jamaah_id']= $data['jamaah_id'];
		    	$data['pool']['fee_keagenan_id']= $data['fee_keagenan_id'];
		    	$data['pool']['active']= 'active';
		    	$data['pool']['input_date']= date('Y-m-d H:i:s');
		    	$data['pool']['last_update']= date('Y-m-d H:i:s');
				// create deposit_transaction
		        $data['deposit_transaction']['nomor_transaction'] = $this->random_code_ops->random_invoice_deposit_transaction();
		        $data['deposit_transaction']['personal_id'] = $data['personal_id'];
		        $data['deposit_transaction']['company_id'] = $this->company_id;
		        $data['deposit_transaction']['debet'] = $data['total_price'];
		        $data['deposit_transaction']['kredit'] = 0;
		        if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
					$data['deposit_transaction']['approver'] = 'Administrator';
				} else {
					$data['deposit_transaction']['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				}
		        $data['deposit_transaction']['sumber_dana'] = 'cash';
		        $data['deposit_transaction']['no_tansaksi_sumber_dana'] = '';
		        $data['deposit_transaction']['transaction_requirement'] = 'paket_deposit';
		        $data['deposit_transaction']['info'] = 'Pengembalian Dana Pembelian Paket Ke Tabungan Umrah';
		        $data['deposit_transaction']['paket_transaction_id'] = $paket_transaction_id;
		        $data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
		        $data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');
		        // create pool_deposit_transaction
		        $data['pool_deposit_transaction']['company_id'] = $this->company_id;
		    }

			$feedBack = $this->model_trans_paket_cud->deleteTransaksiPaket($paket_id, $paket_transaction_id, $data);
			if ($feedBack === false) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi Paket Gagal Dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Transaksi Paket Berhasi Dihapus.',
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

	function getInfoRefund()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$jumlahPembayaran = $this->model_trans_paket->getJumlahPembayaran($paket_transaction_id);

			if ($jumlahPembayaran['bayar'] == 0) {
				$error = 1;
				$error_msg = 'Proses refund tidak dapat dilanjutkan karena transaksi ini belum pernah melakukan pembayaran.';
			}

			$feedBack = $this->model_trans_paket->getRiwayatTransactionCash($paket_transaction_id);
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'listRiwayat' => $feedBack['list'],
					'totalPembayaran' => $feedBack['total_bayar'],
					'invoiceID' => $this->text_ops->get_invoice_transaksi_paket_cash(),
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

	function getInfoPembayaranCash()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules(
			'paket_transaction_id',
			'<b>Paket Transaction ID<b>',
			'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id'
		);
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$feedBack = $this->model_trans_paket->getInfoPembayaranCash($paket_transaction_id);
			# filter
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Riwayat info pembayaran cicilan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack['list'],
					'total_harga' => $feedBack['total_harga'],
					'total_bayar' => $feedBack['total_bayar'],
					'sisa' => $feedBack['sisa'],
					'invoice' => $feedBack['invoice'],
					'sumber_biaya' => array('Tunai', 'Deposit'),
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

	function lastKwitansiCash()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$feedBack = $this->model_trans_paket->getLastInfoKwitansiCash($paket_transaction_id);
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Riwayat info pembayaran cash terakhir tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'paket',
					'metode_pembayaran' => 0,
					'nomor_registrasi' => $feedBack['no_register'],
					'invoice' => $feedBack['invoice']
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
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

	// check invoice pembayaran cash
	function _ckInvoicePembayaranCash($invoice)
	{
		if ($this->model_trans_paket->checkInvoiceCash($invoice)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckInvoicePembayaranCash', 'Invoice sudah tersedia didalam database.');
			return FALSE;
		}
	}

	// bayar
	function _ckBayar($bayar)
	{
		$bayar = $this->text_ops->hide_currency($bayar);
		if ($bayar == 0) {
			$this->form_validation->set_message('_ckBayar', 'Untuk melanjutkan proses ini, kolom bayaran tidak boleh kosong!!!.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# check persona saldo penyetor
	function _ckPersonalSaldoPenyetor()
	{
		if ($this->input->post('penyetorDeposit')) {
			if ($this->model_trans_paket->checkPersonalExist($this->input->post('penyetorDeposit'))) {
				$bayar = $this->text_ops->hide_currency($this->input->post('bayar'));
				$deposit = $this->model_trans_paket->getDepositInfo($this->input->post('penyetorDeposit'));
				if ($deposit < $bayar) {
					$this->form_validation->set_message('_ckPersonalSaldoPenyetor', 'Deposit penyetor tidak mencukupi untuk melanjutkan transaksi ini. pilih sumber pembiayaan yang lain untuk melanjutkan.');
					return FALSE;
				} else {
					return TRUE;
				}
			} else {
				$this->form_validation->set_message('_ckPersonalSaldoPenyetor', 'Data penyetor tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	// pembayaran cash
	function pembayaranCash()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket transaksi id<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ckInvoicePembayaranCash');
		$this->form_validation->set_rules('bayar', '<b>Pembayaran<b>', 'trim|required|xss_clean|min_length[1]|callback__ckBayar');
		$this->form_validation->set_rules('sumber_biaya', '<b>Sumber Biaya<b>', 'trim|required|xss_clean|numeric|min_length[1]');
		$this->form_validation->set_rules('deposit_name', '<b>Nama Pen_ckBayaryetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('hp_deposit', '<b>HP Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_penyetor', '<b>Alamat Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$no_register = $this->model_trans_paket->getNoRegister( $paket_transaction_id );
			$data['paket_transaction_id'] = $paket_transaction_id;
			$data['invoice'] = $this->input->post('invoice');
			$data['paid'] = $this->text_ops->hide_currency($this->input->post('bayar'));
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = 'Administrator';
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['ket'] = 'cash';
			$data['source'] = $this->input->post('sumber_biaya') == '0' ? 'tunai' : 'Deposit';
			$data['deposit_name'] = $this->input->post('deposit_name');
			$data['deposit_phone'] = $this->input->post('hp_deposit');
			$data['deposit_address'] = $this->input->post('alamat_penyetor');
			$data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			$feedBack = $this->model_trans_paket_cud->insertPembayaranCash($data);

			if ($feedBack === FALSE) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi pembayaran cash gagal disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'paket',
					'metode_pembayaran' => '0',
					'nomor_registrasi' => $no_register,
					'invoice' => $data['invoice']
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Transaksi pembayaran cash berhasil disimpan.',
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

	function _ckRefund($refund)
	{
		$refund = $this->text_ops->hide_currency($refund);
		if ($refund == 0) {
			$this->form_validation->set_message('_ckRefund', 'Untuk melanjutkan proses ini, kolom refund tidak boleh kosong!!!.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function refundProcess()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('invoiceID', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ckInvoiceExist');
		$this->form_validation->set_rules('refund', '<b>Amount<b>', 'trim|required|xss_clean|min_length[1]|callback__ckRefund');
		$this->form_validation->set_rules('deposit_name', '<b>Nama Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('hp_deposit', '<b>HP Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_penyetor', '<b>Alamat Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('batalBerangkat', '<b>Batal Beragkat<b>', 'trim|xss_clean|min_length[1]');

		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$no_register = $this->model_trans_paket->getNoRegister($paket_transaction_id);

			# filter keberangkatan
			if ($this->input->post('batalBerangkat')) {
				$batal_berangkat = $this->input->post('batalBerangkat');
			} else {
				$batal_berangkat = 0;
			}

			$data = array();
			$data['paket_transaction_id'] = $this->input->post('paket_transaction_id');
			$data['invoice'] = $this->input->post('invoiceID');
			$data['paid'] = $this->text_ops->hide_currency($this->input->post('refund'));
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = 'Administrator';
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}

			$data['ket'] = 'refund';
			$data['source'] = 'tunai';
			$data['source_id'] = '';
			$data['deposit_name'] = $this->input->post('deposit_name');
			$data['deposit_phone'] = $this->input->post('hp_deposit');
			$data['deposit_address'] = $this->input->post('alamat_penyetor');
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			if (!$this->model_trans_paket_cud->insertRefund($paket_transaction_id, $data, $batal_berangkat)) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi pembayaran refund gagal disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'paket',
					'metode_pembayaran' => '0',
					'ket' => 'refund',
					'batal_berangkat' => $batal_berangkat,
					'nomor_registrasi' => $no_register,
					'invoice' => $data['invoice']
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Transaksi pembayaran refund berhasil disimpan.',
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

	function getInvoiceHandoverBarang()
	{
		$invoice = $this->text_ops->get_invoice_handover();
		if ($invoice == '') {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Invoice gagal di generated.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Transaksi pembayaran cicilan berhasil disimpan.',
				'invoice' => $invoice,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function infoHandOverBarang()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$jamaah_id = $this->input->post('jamaah_id');
			$feedBack = $this->model_trans_paket->getInfoHandOverBarang($paket_transaction_id, $jamaah_id);
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info handover barang tidak ditemukan.',
					'invoice' => $this->text_ops->get_invoice_returned(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
					'invoice' => $this->text_ops->get_invoice_returned(),
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

	function _ckInvoiceHandover($invoice)
	{
		if ($this->model_trans_paket->checkInvoiceHandover($invoice)) {
			$this->form_validation->set_message('_ckInvoiceHandover', 'Invoice ID sudah terdaftar dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// proses handover barang
	function prosesHandOverBarang()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('nama_pemberi_barang', '<b>Nama Pemberi Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_identitas_pemberi_barang', '<b>No Identitas Pemberi Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_hp_pemberi_barang', '<b>No HP Pemberi Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_pemberi_barang', '<b>Alamat Pemberi Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ckInvoiceHandover');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$data = array();
			$data['jamaah_id'] = $this->input->post('jamaah_id');
			$data['paket_transaction_id'] = $this->input->post('paket_transaction_id');
			$data['invoice_handover'] = $this->input->post('invoice');
			$data['giver_handover'] = $this->input->post('nama_pemberi_barang');
			$data['giver_handover_identity'] = $this->input->post('no_identitas_pemberi_barang');
			$data['giver_handover_hp'] = $this->input->post('no_hp_pemberi_barang');
			$data['giver_handover_address'] = $this->input->post('alamat_pemberi_barang');
			$data['date_taken'] = date('Y-m-d H:i:s');
			$feedBack = $this->model_trans_paket_cud->insertHandOverBarang($data, $this->input->post('item'));
			# filter
			if ($feedBack) {
				// create session
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_handover_paket',
					'status' => 'diambil',
					'paket_transaction_id' => $this->input->post('paket_transaction_id'),
					'jamaah_id' => $this->input->post('jamaah_id'),
					'invoice_handover' => $this->input->post('invoice')
				)));
				// return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// return
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info handover barang tidak ditemukan.',
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

	function _ckInvoiceReturned($invoice)
	{
		if ($this->model_trans_paket->checkInvoiceReturned($invoice)) {
			$this->form_validation->set_message('_ckInvoiceReturned', 'Invoice ID sudah terdaftar dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// return handover item
	function returnHandOverItem()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ckInvoiceReturned');
		$this->form_validation->set_rules('nama_penerima_barang', '<b>Nama Penerima Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_identitas_penerima_barang', '<b>No Identitas Penerima Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_hp_penerima_barang', '<b>No HP Penerima Barang<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_penerima_barang', '<b>Alamat Penerima Barang<b>', 'trim|required|xss_clean|min_length[1]');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$jamaah_id = $this->input->post('jamaah_id');
			$invoice = $this->input->post('invoice');
			$item = $this->input->post('item');

			$data = array();
			$data['invoice_returned'] = $invoice;
			$data['status'] = 'dikembalikan';
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['giver_returned'] = 0;
			} else {
				$data['giver_returned'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
			}
			$data['receiver_returned'] = $this->input->post('nama_penerima_barang');
			$data['receiver_returned_identity'] =  $this->input->post('no_identitas_penerima_barang');
			$data['receiver_returned_hp'] =  $this->input->post('no_hp_penerima_barang');
			$data['receiver_returned_address'] =  $this->input->post('alamat_penerima_barang');
			$data['date_returned'] = date('Y-m-d H:i:s');

			if ($this->model_trans_paket_cud->returnHandOverBarang($item, $data)) {
				// create session kwitansi
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_handover_paket',
					'status' => 'dikembalikan',
					'invoice_returned' => $invoice,
					'paket_transaction_id' => $paket_transaction_id,
					'jamaah_id' => $jamaah_id
				)));
				$data = $this->model_trans_paket->getInfoHandOverBarang($paket_transaction_id, $jamaah_id);
				$return = array(
					'error'	=> false,
					'error_msg' => 'Barang yang diambil berhasil dikembalikan.',
					'data' => $data,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Barang yang diambil gagal dikembalikan.',
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

	function infoHandOverFasilitas()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$jamaah_id = $this->input->post('jamaah_id');
			$feedBack = $this->model_trans_paket->getInfoHandOverFasilitas($paket_transaction_id, $jamaah_id);
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info handover barang tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'list_barang' => $feedBack['list_barang'],
					'list_fasilitas' => $feedBack['list_fasilitas'],
					'invoice' => $this->text_ops->get_invoice_fasilitas(),
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

	function _ckFasilitasExist()
	{
		$fasilitas = $this->input->post('fasilitas');
		if (count($fasilitas) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckFasilitasExist', 'Untuk Melanjutkan, Anda Wajib Memilih Minimal Satu Fasilitas.');
			return FALSE;
		}
	}

	function _ckInvoiceFasilitas($invoice)
	{
		if ($this->model_trans_paket->checkInvoiceFasilitas($invoice)) {
			$this->form_validation->set_message('_ckInvoiceFasilitas', 'Invoice ID sudah terdaftar dipangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function handoverFasilitas()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('nama_penerima', '<b>Nama Penerima Fasilitas<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_identitas', '<b>Nomor Identitas Penerima Fasilitas<b>', 'trim|required|xss_clean|min_length[1]|callback__ckFasilitasExist');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ckInvoiceFasilitas');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {

			$fasilitas = $this->input->post('fasilitas');

			$data = array();
			$data['paket_transaction_id'] = $this->input->post('paket_transaction_id');
			$data['invoice'] = $this->input->post('invoice');
			$data['receiver_name'] = $this->input->post('nama_penerima');
			$data['jamaah_id'] = $this->input->post('jamaah_id');
			$data['date_transaction'] = date('Y-m-d H:i:s');
			$data['receiver_identity'] = $this->input->post('no_identitas');

			$feedBack = $this->model_trans_paket_cud->insertFasilitasJamaah($fasilitas, $data);
			if ($feedBack == false) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info handover barang tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'cetak_handover_fasilitas',
					'paket_transaction_id' => $this->input->post('paket_transaction_id'),
					'jamaah_id' => $this->input->post('jamaah_id'),
					'invoice' => $this->input->post('invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',


					//
					// 'list_barang' => $feedBack['list_barang'],
					// 'list_fasilitas' => $feedBack['list_fasilitas'],
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

	function _ck_handover_facilities_id($handover_facilities_id)
	{
		if ($this->model_trans_paket->check_handover_facilities_id($handover_facilities_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_handover_facilities_id', 'Handover facilities ID tidak terdaftar.');
			return FALSE;
		}
	}

	function delete_handover_fasilitas()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('handover_facilities_id', '<b>Handover Facilities ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_otoritas|callback__ck_handover_facilities_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$handover_facilities_id = $this->input->post('handover_facilities_id');
			// list handover facilities
			$list_handover_facilities = $this->model_trans_paket->list_handover_facilities($handover_facilities_id);
			// delete handover facilities
			if ($this->model_trans_paket_cud->delete_handover_fasilitas(
				$handover_facilities_id,
				$list_handover_facilities['fullname'],
				$list_handover_facilities['paket_name']
			)) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'list_handover_facilities' => $list_handover_facilities['facilities_list'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info handover fasilitas barang tidak ditemukan.',
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


	function infoPindahPaketJamaah()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$jamaah_id = $this->input->post('jamaah_id');

			$list_paket = $this->model_trans_paket->getPaketNotThis($paket_id, $jamaah_id);
			$info_jamaah = $this->model_trans_paket->getInfoJamaah($jamaah_id);
			$info_paket_sekarang = $this->model_trans_paket->getInfoPaketSekarang($paket_transaction_id);


			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info handover barang tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'list_paket' => $list_paket,
					'info_jamaah' => $info_jamaah,
					'paket_sekarang' => $info_paket_sekarang['paket_sekarang'],
					'total_harga_paket_sekarang' => $info_paket_sekarang['total_harga_paket_sekarang'],
					'harga_per_paket_sekarang' => $info_paket_sekarang['harga_per_paket_sekarang'],
					'biaya_yang_sudah_dibayar_sekarang' => $info_paket_sekarang['biaya_yang_sudah_dibayar_sekarang'],
					'sisa_pembayaran_sekarang' => $info_paket_sekarang['sisa_pembayaran_sekarang'],

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

	function _ckPaketTypePriceNoReg($tipe_paket_tujuan)
	{
		$paket_id = $this->input->post('paket_id');
		switch ($this->input->post('tipe_aksi')) {
			case 0:
				if ($this->model_trans_paket->checkTipePaketPrice($paket_id, $tipe_paket_tujuan)) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckPaketTypePriceNoReg', 'Tipe paket tidak ditemukan.');
					return FALSE;
				}
				break;
			case 1:
				if ($this->model_trans_paket->checkNoRegisterPaketPrice($paket_id, $this->input->post('tipe_paket_no_reg_tujuan'))) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckPaketTypePriceNoReg', 'No Register tidak ditemukan.');
					return FALSE;
				}
				break;
			default:
				$this->form_validation->set_message('_ckPaketTypePriceNoReg', 'Tipe aksi tidak ditemukan.');
				return FALSE;
				break;
		}
	}

	function _ckBiayaPindah($biaya_yang_dipindah)
	{
		$biaya_yang_dipindah = $this->text_ops->hide_currency($biaya_yang_dipindah);
		if ($this->input->post('paket_transaction_id_now')) {
			$paket_transaction_id = $this->input->post('paket_transaction_id_now');
		} else {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
		}
		$sudahBayar = $this->model_trans_paket->getTransaksiPaketSudahBayar($paket_transaction_id);
		if ($biaya_yang_dipindah > $sudahBayar) {
			$this->form_validation->set_message('_ckBiayaPindah', 'Biaya pindah tidak boleh lebih besar dari biaya yang sudah dibayar .');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// check refund
	function _ckRefundPindahPaket($refund)
	{
		$refund = $this->text_ops->hide_currency($refund);
		$biaya_yang_dipindah = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
		if ($refund > $biaya_yang_dipindah) {
			$this->form_validation->set_message('_ckRefundPindahPaket', 'Biaya refund tidak boleh lebih besar dari biaya yang dipindahkan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function getPriceByTipePaketNoReg()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id_now', '<b>Paket ID Now<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('paket_transaction_id_now', '<b>Paket Transaction ID Now<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('tipe_paket_no_reg_tujuan', '<b>Tipe Paket Tujuan<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckPaketTypePriceNoReg');
		$this->form_validation->set_rules('tipe_aksi', '<b>Tipe Aksi<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,1]');
		$this->form_validation->set_rules('biaya_yang_dipindah', '<b>Biaya Yang Dipindah<b>', 'trim|required|xss_clean|min_length[1]|callback__ckBiayaPindah');
		$this->form_validation->set_rules('refund', '<b>Refund<b>', 'trim|required|xss_clean|min_length[1]|callback__ckRefundPindahPaket');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$tipe_aksi  = $this->input->post('tipe_aksi');
			$jamaah_id = $this->input->post('jamaah_id');
			$paket_id = $this->input->post('paket_id');
			$paket_id_now = $this->input->post('paket_id_now');
			$paket_transaction_id_now = $this->input->post('paket_transaction_id_now');
			$tipe_paket_no_reg_tujuan = $this->input->post('tipe_paket_no_reg_tujuan');
			$biaya_yang_dipindah = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
			$refund = $this->text_ops->hide_currency($this->input->post('refund'));
			$feedBack = 0;
			if ($paket_id_now != $paket_id) {
				if ($tipe_aksi == 0) {
					$feedBack = $this->model_trans_paket->getPriceByPaketTipePaket($paket_id, $tipe_paket_no_reg_tujuan, $biaya_yang_dipindah, $paket_transaction_id_now, $refund, $jamaah_id);
				} elseif ($tipe_aksi == 1) {
					$feedBack = $this->model_trans_paket->getPriceByPaketNoRegister($paket_id, $tipe_paket_no_reg_tujuan, $biaya_yang_dipindah, $paket_transaction_id_now, $refund, $jamaah_id);
				}
			} else {
				$error = 1;
				$error_msg = ' ID Paket tujuan tidak boleh sama dengan ID paket sekarang.';
			}
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
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

	// get tipe paket
	function getTipePaket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id_now', '<b>Paket ID Now<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$paket_id_now = $this->input->post('paket_id_now');
			$feedBack = array();

			if ($paket_id_now == $paket_id) {
				$error = 1;
				$error_msg = 'ID Paket tujuan tidak boleh sama dengan ID paket sekarang.';
			} else {
				$feedBack = $this->model_trans_paket->getInfoTipePaket($paket_id);
			}

			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
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

	function getPaketNoRegister()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id_now', '<b>Paket ID Now<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$paket_id_now = $this->input->post('paket_id_now');
			$feedBack = array();
			if ($paket_id_now == $paket_id) {
				$error = 1;
				$error_msg = 'ID Paket tujuan tidak boleh sama dengan ID paket sekarang.';
			} else {
				$feedBack = $this->model_trans_paket->getInfoNoRegister($paket_id);
			}

			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
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

	public function _ckJamaahPindahPaket($jamaah_id)
	{
		$paket_transaction_id = $this->input->post('paket_transaction_id');
		$checkJamaahInPaket = $this->model_trans_paket->checkJamaahInPaket($jamaah_id, $paket_transaction_id);
		if ($checkJamaahInPaket) {
			$biaya_yang_dipindahkan = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
			$sudah_bayar = $this->model_trans_paket->getTransaksiPaketSudahBayar($paket_transaction_id);
			if ($this->model_trans_paket->checkTotalJamaahNotIn($paket_transaction_id, $jamaah_id)) {
				return TRUE;
			} else {
				if ($biaya_yang_dipindahkan == $sudah_bayar) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckJamaahPindahPaket', 'Jika tidak ada jamaah yang sisa pada transaksi paket asal. Semua biaya yang sudah dibayar harus dipindahkan ke transaksi paket yang baru.');
					return FALSE;
				}
			}
		} else {
			$this->form_validation->set_message('_ckJamaahPindahPaket', 'ID Jamaah tidak terdaftar didalam Transaksi Paket');
			return FALSE;
		}
	}

	public function _ckPaketTujuan($paket_tujuan)
	{
		$feedBack = $this->model_trans_paket->checkPaketExist($paket_tujuan);
		if ($feedBack['success'] == true) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckPaketTujuan', 'Paket ID tidak ditemukan.');
			return FALSE;
		}
	}

	function _ckTipeNoRegisterPaketTujuan($tipe_paket_no_reg_tujuan)
	{
		$paket_tujuan = $this->input->post('paket_tujuan');
		$tipe_aksi = $this->input->post('tipe_aksi');
		if ($this->model_trans_paket->checkTipeNoRegisterTujuan($paket_tujuan, $tipe_aksi, $tipe_paket_no_reg_tujuan)) {
			return TRUE;
		} else {
			if ($tipe_aksi == 0) {
				$this->form_validation->set_message('_ckTipeNoRegisterPaketTujuan', 'Tipe Paket ID tidak ditemukan didalam paket.');
				return FALSE;
			} elseif ($tipe_aksi == 1) {
				$this->form_validation->set_message('_ckTipeNoRegisterPaketTujuan', 'No Register tidak ditemukan didalam paket.');
				return FALSE;
			} else {
				$this->form_validation->set_message('_ckTipeNoRegisterPaketTujuan', 'tipe aksi tidak ditemukan.');
				return FALSE;
			}
		}
	}

	// check refund
	function _ckRefundProsesPindahPaket($refund)
	{
		$refund = $this->text_ops->hide_currency($refund);
		$biaya_yang_dipindah = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
		if ($refund > $biaya_yang_dipindah) {
			$this->form_validation->set_message('_ckRefundProsesPindahPaket', 'Biaya refund tidak boleh lebih besar dari biaya yang dipindahkan.');
			return FALSE;
		} else {
			// retrieve post
			$paket_id = $this->input->post('paket_tujuan');
			$tipe_aksi = $this->input->post('tipe_aksi');
			$tipe_paket_no_reg_tujuan = $this->input->post('tipe_paket_no_reg_tujuan');
			$biaya_yang_dipindah = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
			$paket_transaction_id_now = $this->input->post('paket_transaction_id');
			$jamaah_id = $this->input->post('jamaah_id');
			// filter aksi
			if ($tipe_aksi == 0) {
				$feedBack = $this->model_trans_paket->getPriceByPaketTipePaket($paket_id, $tipe_paket_no_reg_tujuan, $biaya_yang_dipindah, $paket_transaction_id_now, $refund, $jamaah_id);
			} elseif ($tipe_aksi == 1) {
				$feedBack = $this->model_trans_paket->getPriceByPaketNoRegister($paket_id, $tipe_paket_no_reg_tujuan, $biaya_yang_dipindah, $paket_transaction_id_now, $refund, $jamaah_id);
			}
			// get pembayaran berlebih
			if ($feedBack['pembayaran_berlebih'] > 0) {
				$this->form_validation->set_message('_ckRefundProsesPindahPaket', 'Proses pindah paket tidak dapat dilanjutkan, karena terdapat pembayaran yang berlebih, silahkan lakukan refund untuk pembayaran berlebih tersebut.');
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	# pindah paket
	public function pindahPaket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckJamaahPindahPaket');
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('paket_tujuan', '<b>Paket Tujuan<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckPaketTujuan');
		$this->form_validation->set_rules('tipe_aksi', '<b>Tipe Aksi<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,1]');
		$this->form_validation->set_rules('tipe_paket_no_reg_tujuan', '<b>Tipe Paket Atau No Register Paket Tujuan<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckTipeNoRegisterPaketTujuan');
		$this->form_validation->set_rules('biaya_yang_dipindah', '<b>Biaya Yang Dipindahkan<b>', 'trim|required|xss_clean|min_length[1]|callback__ckBiayaPindah');
		$this->form_validation->set_rules('refund', '<b>Refund<b>', 'trim|required|xss_clean|min_length[1]|callback__ckRefundProsesPindahPaket');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			// get tipe aksi
			$tipe_aksi = $this->input->post('tipe_aksi');
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$jamaah_id = $this->input->post('jamaah_id');
			$paket_id = $this->input->post('paket_id');
			$paket_tujuan = $this->input->post('paket_tujuan');
			$tipe_paket_no_reg_tujuan = $this->input->post('tipe_paket_no_reg_tujuan');

			$invoice_cash = $this->text_ops->get_invoice_transaksi_paket_cash();
			$refund = $this->text_ops->hide_currency($this->input->post('refund'));
			$invoice_refund = '';
			if ($refund != '') {
				$invoice_refund = $this->text_ops->get_invoice_transaksi_paket_cash();
			}

			$infoPaketAsal = $this->model_trans_paket->getInfoPaketAsal($paket_transaction_id, $jamaah_id);
			$infoJamaah = $this->model_trans_paket->getInfoJamaahPindahPaket($jamaah_id);
			$infoPaketTujuan = $this->model_trans_paket->getInfoPaketTujuan($paket_tujuan, $tipe_aksi, $tipe_paket_no_reg_tujuan, $jamaah_id);

			$dataInput = array();
			$dataInput['paket_id'] = $paket_id;
			$dataInput['jamaah_id'] = $jamaah_id;
			$dataInput['paket_transaction_id'] = $paket_transaction_id;
			$dataInput['infoPaketAsal'] = $infoPaketAsal;
			$dataInput['infoJamaah'] = $infoJamaah;
			$dataInput['infoPaketTujuan'] = $infoPaketTujuan;
			$dataInput['paket_tujuan'] = $paket_tujuan;
			$dataInput['tipe_aksi'] = $tipe_aksi;
			if ($tipe_aksi = 0) {
				$dataInput['tipe_paket_tujuan'] = $this->input->post('tipe_paket_no_reg_tujuan');
			} else {
				$dataInput['no_register'] = $this->input->post('tipe_paket_no_reg_tujuan');
			}
			$dataInput['biaya_yang_dipindahkan'] = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
			$dataInput['refund'] = $this->text_ops->hide_currency($this->input->post('refund'));

			// define Data Pindah paket
			$dataPindahPaket = array();
			$dataPindahPaket['kode_paket_asal'] = $infoPaketAsal['kode'];
			$dataPindahPaket['paket_asal'] = $infoPaketAsal['paket_name'];
			$dataPindahPaket['tipe_paket_asal'] = $infoPaketAsal['paket_type_name'];
			$dataPindahPaket['no_register_asal'] = $infoPaketAsal['no_register'];
			$dataPindahPaket['harga_paket_asal'] = $infoPaketAsal['price_per_pax'];
			$dataPindahPaket['jamaah_id'] = $this->input->post('jamaah_id');
			$dataPindahPaket['nama_jamaah'] = $infoJamaah['fullname'];
			$dataPindahPaket['kode_paket_tujuan'] = $infoPaketTujuan['kode'];
			$dataPindahPaket['paket_tujuan'] = $infoPaketTujuan['paket_name'];
			$dataPindahPaket['tipe_paket_tujuan'] = $infoPaketTujuan['paket_type_name'];
			$dataPindahPaket['no_register_paket_tujuan'] = $infoPaketTujuan['no_register'];
			$dataPindahPaket['harga_paket_tujuan'] = $infoPaketTujuan['price_per_pax'];
			$dataPindahPaket['biaya_yang_dipindahkan'] = $this->text_ops->hide_currency($this->input->post('biaya_yang_dipindah'));
			if ($refund != '') {
				$dataPindahPaket['refund'] = $this->text_ops->hide_currency($this->input->post('refund'));
				$dataPindahPaket['invoice_refund'] = $invoice_refund;
			}
			$dataPindahPaket['invoice_tujuan'] = $invoice_cash;
			$dataPindahPaket['transaction_date'] = date('Y-m-d H:i:s');

			// insert and update process
			$feedBack = $this->model_trans_paket_cud->addUpdatePaketPindah($dataInput, $dataPindahPaket);
			if ($feedBack['status'] == false) {
				$return = array(
					'error'	=> true,
					'error_msg' => "Proses pindah paket gagal dilakukan",
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// create session printing
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'invoice_pindah_paket',
					'pindahPaketID' => $feedBack['pindahPaketId']
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses pindah paket berhasil dilakukan.',
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


	function cetakRiwayatCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id',	'<b>Paket Transaction ID<b>',	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$feedBack = $this->model_trans_paket->get_info_transaksi_cicilan($this->input->post('paket_transaction_id'));
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi paket cicilan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$this->session->set_userdata(array(
					'cetak_invoice' => array(
						'type' => 'cetak_riwayat_paket',
						'metode_pembayaran' => $feedBack['metode_pembayaran'],
						'nomor_registrasi' => $feedBack['no_register']
					)
				));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
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

	function cetakSkemaCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$feedBack = $this->model_trans_paket->get_info_transaksi_cicilan($this->input->post('paket_transaction_id'));
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi paket cicilan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$this->session->set_userdata(array(
					'cetak_invoice' => array(
						'type' => 'cetak_skema_cicilan',
						'nomor_registrasi' => $feedBack['no_register']
					)
				));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
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

	function getInfoPembayaranCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

			$feedBack = $this->model_trans_paket->getInfoPembayaranCicilan($this->input->post('paket_transaction_id'));

			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Riwayat info pembayaran cicilan tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack['list'],
					'riwayat_angsuran' => $feedBack['riwayat_angsuran'],
					'total_harga' => $feedBack['total_harga'],
					'total_bayar' => $feedBack['total_bayar'],
					'sisa' => $feedBack['sisa'],
					'invoice' => $feedBack['invoice'],
					'sumber_biaya' => array('Tunai', 'Deposit'),
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

	function _ckInvoicePembayaranCicilan($invoice)
	{
		if ($this->model_trans_paket->checkInvoiceCicilan($invoice)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckInvoicePembayaranCicilan', 'Invoice sudah tersedia didalam database.');
			return FALSE;
		}
	}

	function pembayaranCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket transaksi id<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ckInvoicePembayaranCicilan');
		$this->form_validation->set_rules('bayar', '<b>Pembayaran<b>', 'trim|required|xss_clean|min_length[1]|callback__ckBayar');
		$this->form_validation->set_rules('deposit_name', '<b>Nama Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('hp_deposit', '<b>HP Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_penyetor', '<b>Alamat Penyetor<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$no_register = $this->model_trans_paket->getNoRegister($paket_transaction_id);
			$data['paket_transaction_id'] = $paket_transaction_id;
			$data['invoice'] = $this->input->post('invoice');
			$data['paid'] = $this->text_ops->hide_currency($this->input->post('bayar'));
			// $data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['ket'] = 'cicilan';
			$data['source'] = 'tunai';
			$data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$data['deposit_name'] = $this->input->post('deposit_name');
			$data['deposit_phone'] = $this->input->post('hp_deposit');
			$data['deposit_address'] = $this->input->post('alamat_penyetor');
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			if ($this->model_trans_paket_cud->insertPembayaranCicilan($data)) {
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'paket',
					'metode_pembayaran' => '1',
					'nomor_registrasi' => $no_register,
					'invoice' => $data['invoice']
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Transaksi pembayaran cicilan berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Transaksi pembayaran cicilan gagal disimpan.',
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

	function getInfoSkemaCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$feedBack = $this->model_trans_paket->getSkemaCicilan($this->input->post('paket_transaction_id'));
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'listSkemaCicilan' => $feedBack['listSkemaCicilan'],
					'totalCicilanView' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($feedBack['totalCicilan']),
					'totalCicilan' => $feedBack['totalCicilan'],
					'totalAmountView' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($feedBack['totalAmount']),
					'totalAmount' => $feedBack['totalAmount'],
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

	function _ckAmount()
	{
		$amount = $this->input->post('amount');
		$total_cicilan = $this->model_trans_paket->getTotalCicilan($this->input->post('paket_transaction_id'));
		$total_amount = 0;
		// echo "<br>============<br>";
		// echo $amount;
		// echo "<br>============<br>";
		foreach ($amount as $key => $value) {
			$total_amount = $total_amount  + $this->text_ops->hide_currency($value);
		}

		if ($total_amount > $total_cicilan) {
			$this->form_validation->set_message('_ckAmount', 'Total amount tidak boleh lebih besar dari total cicilan.');
			return FALSE;
		} elseif ($total_amount < $total_cicilan) {
			$this->form_validation->set_message('_ckAmount', 'Total amount tidak boleh lebih kecil dari total cicilan.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function updateSkemaCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = 'Skema cicilan berhasil di update.';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		// $this->form_validation->set_rules('amount','<b>Amount<b>','trim|xss_clean|min_length[1]|callback__ckAmount');

		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$amount = $this->input->post('amount');
			$total_cicilan = $this->model_trans_paket->getTotalCicilan($this->input->post('paket_transaction_id'));
			$total_amount = 0;
			foreach ($amount as $key => $value) {
				$total_amount = $total_amount  + $this->text_ops->hide_currency($value);
			}

			if ($total_amount > $total_cicilan) {
				$error = 1;
				$error_msg = 'Total amount tidak boleh lebih besar dari total cicilan';
			} elseif ($total_amount < $total_cicilan) {
				$error = 1;
				$error_msg = 'Total amount tidak boleh lebih kecil dari total cicilan';
			} else {
				$duedate = $this->input->post('duedate');
				$term = $this->input->post('term');
				$data = array();
				foreach ($amount as $key => $value) {
					$data[]  = array('term' => $term[$key], 'amount' => $amount[$key], 'duedate' => $duedate[$key]);
				}
				if (!$this->model_trans_paket_cud->insertSkemaCicilan($this->input->post('paket_transaction_id'), $data)) {
					$error = 1;
					$error_msg = 'Proses update skema cicilan gagal dilakukan.';
				}
			}

			if ($error == 0) {
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

	function lastKwitansiCicilan()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			$feedBack = $this->model_trans_paket->getLastInfoKwitansiCicilan($paket_transaction_id);
			if (count($feedBack) == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Riwayat info pembayaran cicilan terakhir tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'paket',
					'metode_pembayaran' => 1,
					'nomor_registrasi' => $feedBack['no_register'],
					'invoice' => $feedBack['invoice']
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
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

	function _ckTandaTangan($tanda_tangan)
	{
		if ($this->model_trans_paket->checkTandaTangan($tanda_tangan)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ckTandaTangan', 'ID tanda tangan petugas tidak ditemukan.');
			return FALSE;
		}
	}

	function cetakDataJamaah()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaksi ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_transaction_id');
		$this->form_validation->set_rules('tanda_tangan', '<b>Tanda Tangan<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ckTandaTangan');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$jamaah_id = $this->input->post('jamaah_id');
			$paket_transaction_id = $this->input->post('paket_transaction_id');
			if ($this->input->post('tanda_tangan') == 0) {
				$nama_petugas = 'Administrator ' . $this->session->userdata($this->config->item('apps_name'))['company_name'];
				$jabatan_petugas = 'Administrator';
			} else {
				$getNameJabatan = $this->model_trans_paket->getTandaTanganName($this->input->post('tanda_tangan'));
				$nama_petugas = $getNameJabatan['nama_petugas'];
				$jabatan_petugas = $getNameJabatan['jabatan_petugas'];
			}

			// create session priting here
			$this->session->set_userdata(array('cetak_invoice' => array(
				'type' => 'cetak_data_jamaah',
				'jamaah_id' => $jamaah_id,
				'nama_petugas' => $nama_petugas,
				'jabatan_petugas' => $jabatan_petugas,
				'paket_transaction_id' => $paket_transaction_id
			)));

			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
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

	function infoTandaTangan()
	{
		$tanda_tangan = $this->model_trans_paket->get_tanda_tangan();
		if (count($tanda_tangan) == 0) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Petugas tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Petuagas berhasil ditemukan.',
				'tanda_tangan' => $tanda_tangan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_tanda_tangan($tanda_tangan)
	{
		if ($tanda_tangan != 'pilih_petugas') {
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				if ($this->model_trans_paket->checkTandaTangan($tanda_tangan)) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckTandaTangan', 'ID tanda tangan petugas tidak ditemukan.');
					return FALSE;
				}
			} else {
				if ($tanda_tangan == $this->session->userdata($this->config->item('apps_name'))['user_id']) {
					return TRUE;
				} else {
					$this->form_validation->set_message('_ckTandaTangan', 'ID tanda tangan tidak sesuai dengan id petugas.');
					return FALSE;
				}
			}
		} else {
			$this->form_validation->set_message('_ck_tanda_tangan', 'Untuk melanjutkan proses ini, anda wajib memilih salah satu nama petugas.');
			return FALSE;
		}
	}

	function _ck_paket_id_exist($paket_id)
	{
		if( $this->model_trans_paket->check_paket_id( $paket_id ) ) 
		{
			return TRUE;
		}else
		{
			$this->form_validation->set_message('_ck_paket_id_exist', 'Paket ID tidak terdaftar dipangkalan data.');
			return FALSE;
		}
	}


	function download_manifest()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id',	'<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id_exist');
		/*
		  Validation process
	  	*/
		if ($this->form_validation->run()) {
			# get info paket
			$info_paket = $this->model_trans_paket->get_info_paket($this->input->post('paket_id'));
			# set session
			$this->session->set_userdata(
				array('download_to_excel' => 
					array('type' => 'download_manifest',
						  'paket_id' => $this->input->post('paket_id'), 
						  'paket_name' => $info_paket['paket_name'],
						  'kode_paket' => $info_paket['kode_paket'])));
			if (!$this->session->userdata('download_to_excel')) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses download manifes gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses download manifes berhasil dilakukan.',
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

	// cetak foto jamaah
	function download_absensi()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('tanda_tangan', '<b>Tanda Tangan<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_tanda_tangan');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$tanda_tangan = $this->input->post('tanda_tangan');

			if ($this->input->post('tanda_tangan') == 0) {
				$nama_petugas = 'Administrator ' . $this->session->userdata($this->config->item('apps_name'))['company_name'];
				$jabatan_petugas = 'Administrator';
			} else {
				$getNameJabatan = $this->model_trans_paket->getTandaTanganName($this->input->post('tanda_tangan'));
				$nama_petugas = $getNameJabatan['nama_petugas'];
				$jabatan_petugas = $getNameJabatan['jabatan_petugas'];
			}

			// create session priting here
			$this->session->set_userdata(array('cetak_invoice' => array(
				'type' => 'download_absensi',
				'paket_id' => $paket_id,
				'nama_petugas' => $nama_petugas,
				'jabatan_petugas' => $jabatan_petugas
			)));
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
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

	function daftar_manifes_paket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search', '<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}

			$total 	= $this->model_trans_paket->get_total_manifes_paket($paket_id, $search);
			$list 	= $this->model_trans_paket->get_index_manifes_paket($paket_id, $perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data manisfes tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data manisfes berhasil ditemukan.',
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

	function daftar_syarat_paket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search', '<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}

			$total 	= $this->model_trans_paket->get_total_syarat_paket($paket_id, $search);
			$list 	= $this->model_trans_paket->get_index_syarat_paket($paket_id, $perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data syarat paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data syarat paket berhasil ditemukan.',
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

	function daftar_kamar_paket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search', '<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}

			$total 	= $this->model_trans_paket->get_total_kamar_paket($paket_id, $search);
			$list 	= $this->model_trans_paket->get_index_kamar_paket($paket_id, $perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar kamar paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data kamar paket berhasil ditemukan.',
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

	function info_add_kamar()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			# get data hotel
			$data_hotel = $this->model_trans_paket->get_hotel($paket_id);
			# get jamaah
			$data_jamaah = $this->model_trans_paket->get_jamaah_by_paket($paket_id);
			# filter error
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'hotel' => $data_hotel,
					'jamaah' => $data_jamaah,
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

	// check room id
	function _ck_room_id($room_id)
	{
		if ($room_id != '') {
			if ($this->model_trans_paket->check_room_id($room_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_room_id', 'ID Kamar tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	// check nama hotel
	function _ck_nama_hotel($nama_hotel)
	{
		if ($this->model_trans_paket->check_nama_hotel($nama_hotel)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_nama_hotel', 'ID Hotel tidak ditemukan.');
			return FALSE;
		}
	}

	function add_update_kamar_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = 'Success.';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('id', '<b>ID Kamar<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_room_id');
		$this->form_validation->set_rules('nama_hotel', '<b>Nama Penginapan/Hotel<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_nama_hotel');
		$this->form_validation->set_rules('type_kamar', '<b>Nomor Kamar<b>', 'trim|required|xss_clean|min_length[1]|in_list[laki_laki,perempuan]');
		$this->form_validation->set_rules('kapasitas_kamar', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		// filter jamaah
		foreach ($this->input->post('jamaah') as $key => $val) {
			$this->form_validation->set_rules("jamaah[" . $key . "]", "Jamaah ID", 'trim|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		}
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$jamaah = $this->input->post('jamaah');

			if (!$this->text_ops->has_duplicate($jamaah)) {
				$paket_id = $this->input->post('paket_id');

				$data = array();
				$data['hotel_id'] = $this->input->post('nama_hotel');
				$data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
				$data['room_type'] = $this->input->post('type_kamar');
				$data['room_capacity'] = $this->input->post('kapasitas_kamar');
				$data['paket_id'] = $this->input->post('paket_id');
				$data['last_update'] = date('Y-m-d');
				// insert and update process
				if ($this->input->post('id')) {
					if (!$this->model_trans_paket_cud->update_rooms($this->input->post('id'), $data, $this->input->post('jamaah'))) {
						$error = 1;
						$error_msg = 'Proses update gagal dilakukan.';
					}
				} else {
					$data['input_date'] = date('Y-m-d');
					if (!$this->model_trans_paket_cud->insert_rooms($data, $this->input->post('jamaah'))) {
						$error = 1;
						$error_msg = 'Proses insert gagal dilakukan.';
					}
				}
			} else {
				$error = 1;
				$error_msg = 'Terdapat jamaah yang duplikat';
			}

			# filter error
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
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


	function delete_kamar_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('id', '<b>ID Kamar<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_room_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			# paket id
			$paket_id = $this->input->post('paket_id');
			# room id
			$room_id = $this->input->post('id');
			# get data kamar
			$data_kamar = $this->model_trans_paket->get_data_kamar($room_id, $paket_id);
			# filter error
			if ($this->model_trans_paket_cud->delete_kamar_paket($room_id, $paket_id, $data_kamar)) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	// get info kamar paket
	function get_info_kamar_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('id', '<b>ID Kamar<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_room_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			# get data hotel
			$data_hotel = $this->model_trans_paket->get_hotel($paket_id);
			# get jamaah
			$data_jamaah = $this->model_trans_paket->get_jamaah_by_paket($paket_id);
			# get data kamar
			$feedBack = $this->model_trans_paket->get_data_kamar_by_id($paket_id, $this->input->post('id'));
			# filter error
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
					'jamaah' => $data_jamaah,
					'hotel' => $data_hotel,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	# daftar bus paket
	function daftar_bus_paket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search', '<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}

			$total 	= $this->model_trans_paket->get_total_bus_paket($paket_id, $search);
			$list 	= $this->model_trans_paket->get_index_bus_paket($paket_id, $perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar bus tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data bus berhasil ditemukan.',
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

	function info_add_bus()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			# get data hotel
			$data_kota = $this->model_trans_paket->get_city($paket_id);
			# get jamaah
			$data_jamaah = $this->model_trans_paket->get_jamaah_by_paket($paket_id);
			# filter error
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'city' => $data_kota,
					'jamaah' => $data_jamaah,
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

	function _ck_city_id_exist($city_id)
	{
		if ($this->model_trans_paket->check_city_id_exist($city_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_city_id_exist', 'ID Kota tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_bus_id($bus_id)
	{
		if ($this->input->post('id')) {
			if ($this->model_trans_paket->check_bus_id($bus_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_bus_id', 'ID Bus tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function add_update_bus_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = 'Success.';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('id', '<b>ID Bus<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_bus_id');
		$this->form_validation->set_rules('nomor_bus', '<b>Nomor Bus<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kapasitas_bus', '<b>Kapasitas Bus<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('bus_leader', '<b>Bus Leader<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kota_singgah', '<b>Kota Singgah<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_city_id_exist');
		// filter jamaah
		foreach ($this->input->post('jamaah') as $key => $val) {
			$this->form_validation->set_rules("jamaah[" . $key . "]", "Jamaah ID", 'trim|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		}
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$jamaah = $this->input->post('jamaah');
			if (!$this->text_ops->has_duplicate($jamaah)) {
				$paket_id = $this->input->post('paket_id');
				$data = array();
				$data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
				$data['bus_number'] = $this->input->post('nomor_bus');
				$data['bus_capacity'] = $this->input->post('kapasitas_bus');
				$data['bus_leader'] = $this->input->post('bus_leader');
				$data['city_id'] = $this->input->post('kota_singgah');
				$data['paket_id'] = $this->input->post('paket_id');
				$data['last_update'] = date('Y-m-d');
				// insert and update process
				if ($this->input->post('id')) {
					if (!$this->model_trans_paket_cud->update_bus($this->input->post('id'), $data, $this->input->post('jamaah'))) {
						$error = 1;
						$error_msg = 'Proses update gagal dilakukan.';
					}
				} else {
					$data['input_date'] = date('Y-m-d');
					if (!$this->model_trans_paket_cud->insert_bus($data, $this->input->post('jamaah'))) {
						$error = 1;
						$error_msg = 'Proses insert gagal dilakukan.';
					}
				}
			} else {
				$error = 1;
				$error_msg = 'Terdapat jamaah yang duplikat';
			}

			# filter error
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
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

	// delete bus data bus paket
	function  delete_bus_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('id', '<b>ID Bus<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_bus_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			# paket id
			$paket_id = $this->input->post('paket_id');
			# bus id
			$bus_id = $this->input->post('id');
			# get data bus
			$data_bus = $this->model_trans_paket->get_data_bus($bus_id, $paket_id);
			# filter error
			if ($this->model_trans_paket_cud->delete_bus_paket($bus_id, $paket_id, $data_bus)) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	function get_info_bus_paket()
	{

		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('id', '<b>ID Bus<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_bus_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			# get data hotel
			$data_kota = $this->model_trans_paket->get_city($paket_id);
			# get jamaah
			$data_jamaah = $this->model_trans_paket->get_jamaah_by_paket($paket_id);
			# get data kamar
			$feedBack = $this->model_trans_paket->get_data_bus_by_id($paket_id, $this->input->post('id'));
			# filter error
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
					'jamaah' => $data_jamaah,
					'city' => $data_kota,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	function daftar_agen_paket()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 	'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('search', '<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}

			$total 	= $this->model_trans_paket->get_total_agen_paket($paket_id, $search);
			$list 	= $this->model_trans_paket->get_index_agen_paket($paket_id, $perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar agen tidak ditemukan.',
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

	# get data k t
	function get_data_k_t()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			# status paket
			$status_paket = $this->model_trans_paket->get_status_paket($paket_id);
			# feedBack
			$feedBack = $this->model_trans_paket->get_data_k_t($paket_id);
			// # filter error
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
					'status_paket' => $status_paket,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	function get_info_aktualisasi()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			// get info aktualisasi
			$feedBack = $this->model_trans_paket->get_info_aktualisasi($this->input->post('paket_id'));
			// # filter error
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	function _ck_aktualisasi_id($aktualisasi_id)
	{
		if ($this->input->post('aktualisasi_id')) {
			if ($this->model_trans_paket->check_aktualisasi_id($aktualisasi_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_aktualisasi_id', 'ID aktualisasi anggaran tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_nomor($nomor)
	{
		if ($this->input->post('aktualisasi_id')) {
			$feedBack = $this->model_trans_paket->check_nomor_aktualisasi($nomor, $this->input->post('aktualisasi_id'));
		} else {
			$feedBack = $this->model_trans_paket->check_nomor_aktualisasi($nomor);
		}
		if ($feedBack) {
			$this->form_validation->set_message('_ck_nomor', 'Nomor sudah terdaftar di rincian anggaran yang lain.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function add_update_aktualisasi_anggaran()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		$this->form_validation->set_rules('aktualisasi_id', '<b>Aktualisasi ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_aktualisasi_id');
		$this->form_validation->set_rules('nomor', '<b>Nomor<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_nomor');
		$this->form_validation->set_rules('uraian', '<b>Uraian<b>', 'trim|required|xss_clean|min_length[1]');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$paket_id = $this->input->post('paket_id');
			$data  = array();
			$data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$data['paket_id'] = $paket_id;
			$data['number'] = $this->input->post('nomor');
			$data['uraian'] = $this->input->post('uraian');
			if ($this->input->post('aktualisasi_id')) {
				$feedBack = $this->model_trans_paket_cud->update_aktualisasi_anggaran($this->input->post('aktualisasi_id'), $data);
			} else {
				$feedBack = $this->model_trans_paket_cud->insert_aktualisasi_anggaran($data);
			}
			# filter error
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'data' => $feedBack,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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



	function get_edit_info_aktualisasi_anggaran()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('aktualisasi_id', '<b>Aktualisasi ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_aktualisasi_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$feedBack = $this->model_trans_paket->get_aktualisasi_anggaran_info($this->input->post('aktualisasi_id'));
			# filter error
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'value' => $feedBack['value'],
					'data' => $feedBack['data'],
					'paket_id' => $feedBack['paket_id'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	function delete_aktualisasi_anggaran()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('aktualisasi_id', '<b>Aktualisasi ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_otoritas|callback__ck_aktualisasi_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			// get paket id
			$paket_id = $this->model_trans_paket->get_paket_id_by_aktualisasi_id($this->input->post('aktualisasi_id'));
			// delete process
			$feedBack = $this->model_trans_paket_cud->delete_aktualisasi_anggaran($this->input->post('aktualisasi_id'));
			# filter error
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'paket_id' => $paket_id,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	// check harga aktualisasi
	function _ck_harga_aktualisasi($harga)
	{
		if ($this->text_ops->hide_currency($harga) != 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_harga_aktualisasi', 'Harga tidak boleh NOL.');
			return FALSE;
		}
	}

	function _ck_aktualisasi_detail_id($aktualisasi_detail_id)
	{
		if ($this->input->post('aktualisasi_detail_id')) {
			if ($this->model_trans_paket->check_aktualisasi_detail_id($aktualisasi_detail_id)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_aktualisasi_detail_id', 'ID rincian detail tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function add_update_aktualisasi_anggaran_detail()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('aktualisasi_id', '<b>Aktualisasi ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_aktualisasi_id');
		$this->form_validation->set_rules('aktualisasi_detail_id', '<b>Aktualisasi ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_aktualisasi_detail_id');
		$this->form_validation->set_rules('uraian', '<b>Uraian<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('unit', '<b>Unit<b>', 'trim|required|xss_clean|numeric|min_length[1]');
		$this->form_validation->set_rules('harga', '<b>Harga<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_harga_aktualisasi');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			$aktualisasi_id = $this->input->post('aktualisasi_id');

			$paket_id = $this->model_trans_paket->get_paket_id_by_aktualisasi_id($this->input->post('aktualisasi_id'));

			$data  = array();
			$data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
			$data['aktualisasi_id'] = $this->input->post('aktualisasi_id');
			$data['uraian'] = $this->input->post('uraian');
			$data['unit'] = $this->input->post('unit');
			$data['biaya'] = $this->text_ops->hide_currency($this->input->post('harga'));
			if ($this->input->post('aktualisasi_detail_id')) {
				$feedBack = $this->model_trans_paket_cud->update_aktualisasi_detail_anggaran($this->input->post('aktualisasi_detail_id'), $data);
			} else {
				$feedBack = $this->model_trans_paket_cud->insert_aktualisasi_detail_anggaran($data);
			}
			# filter error
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'paket_id' => $paket_id,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	function get_edit_info_aktualisasi_anggaran_detail()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('aktualisasi_detail_id', '<b>Aktualisasi  detail ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_aktualisasi_detail_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			# get data from database
			$feedBack = $this->model_trans_paket->get_aktualisasi_anggaran_detail_info($this->input->post('aktualisasi_detail_id'));
			# filter error
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'value' => $feedBack['value'],
					'paket_id' => $feedBack['paket_id'],
					'aktualisasi_id' => $feedBack['aktualisasi_id'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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


	function delete_aktualisasi_anggaran_detail()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('aktualisasi_detail_id', '<b>Aktualisasi ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_otoritas|callback__ck_aktualisasi_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			// get paket id
			$paket_id = $this->model_trans_paket->get_paket_id_by_aktualisasi_detail_id($this->input->post('aktualisasi_detail_id'));
			// delete process
			$feedBack = $this->model_trans_paket_cud->delete_aktualisasi_anggaran_detail($this->input->post('aktualisasi_detail_id'));
			# filter error
			if ($feedBack) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Success.',
					'paket_id' => $paket_id,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Gagal',
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

	// tutup paket
	function close_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			// retrieve paket id
			$paket_id = $this->input->post('paket_id');
			// filter
			if( $this->model_trans_paket->get_jumlah_jamaah( $paket_id ) > 0 ) {
				// check pembayaran
				$jamaah_berhutang = $this->model_trans_paket->check_hutang_jamaah($paket_id);
				if (count($jamaah_berhutang) == 0) {
					$param = array(
						'akun' => $this->model_trans_paket->get_akun_number(array('kas', 'pendapatan_paket')),
						'info_paket' => $this->model_trans_paket->get_simple_info_paket($paket_id),
						'saldo' => $this->model_trans_paket->get_data_k_t($paket_id)['keuntungan'],
						'periode' => $this->model_trans_paket->get_last_periode()
					);
					// close proses
					if (!$this->model_trans_paket_cud->close_paket($paket_id, $param)) {
						$error = 1;
						$error_msg = 'Paket gagal ditutup';
					}
				} else {
					$error = 1;
					$error_msg = 'Paket tidak dapat ditutup, karena masih terdapat jamaah yang belum melunasi pembayaran. Berikut adalah nomor register transaksi yang belum melunasi pembayaran paket:<br>';
					$error_msg .= '<table class="table mt-3">
											<thead>
												<tr><th>Nomor Register</th>
													 <th>Sisa Pembayaran</th></tr>
											</thead>
											<tbody>';
					foreach ($jamaah_berhutang as $key => $value) {
						$error_msg .= '<tr><td>' . $key . '</td>
												 <td>' . $this->session->userdata($this->config->item('apps_name'))['kurs']. ' ' . number_format($value) . '</td></tr>';
					}
					$error_msg .= '</tbody>
										</table>';
				}
			}else{
				$error = 1;
				$error_msg = 'Paket tidak dapat ditutup, karena tidak terdapat jamaah dalam paket:<br>';
			}
			# filter error
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Paket berhasil ditutup.',
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


	function open_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			# filter error
			if ($this->model_trans_paket_cud->open_paket($this->input->post('paket_id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Paket berhasil dibuka.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Paket gagal dibuka',
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

	function cetak_daftar_kamar_jamaah()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_paket_id');
		/**
		 *   Validation process
		 **/
		if ($this->form_validation->run()) {
			// paket id
			$paket_id = $this->input->post('paket_id');
			// create session priting here
			$this->session->set_userdata(array('download_to_excel' => 
											array('type' => 'download_absensi_kamar',
												  'paket_id' => $paket_id)));
			# filter
			if ( ! $this->session->userdata('download_to_excel') ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses download absensi kamar gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses download absensi kamar berhasil dilakukan.',
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

	function all_daftar_transaksi_paket()
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
			$total 	= $this->model_trans_paket->get_total_all_transaksi_paket($search);
			$list 	= $this->model_trans_paket->get_index_all_transaksi_paket($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi paket berhasil ditemukan.',
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

	# all daftar transaksi paket agen
	function all_daftar_transaksi_paket_agen()
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
			$total 	= $this->model_trans_paket->get_total_all_transaksi_paket_agen($search);
			$list 	= $this->model_trans_paket->get_index_all_transaksi_paket_agen($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data transaksi paket agen tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data transaksi paket agen berhasil ditemukan.',
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

	function _ck_id_agen($id)
	{
		if ($this->model_trans_paket->check_id_keagenan($id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_id_agen', 'ID agen tidak ditemukan.');
			return FALSE;
		}
	}

	# get info fee keagenan
	function get_info_fee_keagenan()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Agen ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_id_agen');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info keagenan
			$info_keagenan = $this->model_trans_paket->get_info_fee_keagenan($this->input->post('id'));

			# filter
			if (count($info_keagenan) > 0) {

				if ($info_keagenan['unpaid'] > 0) {
					$return = array(
						'error'	=> false,
						'error_msg' => 'Informasi agen berhasil ditemukan.',
						'data' => $info_keagenan,
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				} else {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Pembayaran tidak dapat dilakukan karena biaya yang belum dibayarkan adalah '. $this->session->userdata($this->config->item('apps_name'))['kurs'] .' 0.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Informasi agen tidak ditemukan.',
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

	function _ck_pembayaran_keagenan($payment)
	{
		$payment = $this->text_ops->hide_currency($payment);
		$info_keagenan  = $this->model_trans_paket->get_info_fee_keagenan($this->input->post('id'));
		if ($info_keagenan['unpaid'] < $payment) {
			$this->form_validation->set_message('_ck_pembayaran_keagenan', 'Pembayaran tidak boleh lebih besar dari nilai Fee yang belum dibayarkan');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# check invoice keagenan
	function _ck_invoice_keagenan($invoice)
	{
		if ($this->model_trans_paket->check_invoice($invoice)) {
			$this->form_validation->set_message('_ck_invoice_keagenan', 'Invoice keagenan udah terdaftar didalam pangkalan data.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function proses_pembayaran_fee_agen()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>Agen ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_id_agen');
		$this->form_validation->set_rules('payment',	'<b>Pembayaran<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_pembayaran_keagenan');
		$this->form_validation->set_rules('invoice',	'<b>Invoie<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_invoice_keagenan');
		$this->form_validation->set_rules('applicant_name',	'<b>Nama Pemohonan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('applicant_identity',	'<b>Nomor Identitas Pemohon<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info keagenan
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['agen_id'] = $this->input->post('id');
			$data['invoice'] = $this->input->post('invoice');
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['receiver'] = "Administrator";
			} else {
				$data['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['biaya'] = $this->text_ops->hide_currency($this->input->post('payment'));
			$data['applicant_name'] = $this->input->post('applicant_name');
			$data['applicant_identity'] = $this->input->post('applicant_identity');
			$data['date_transaction'] = date('Y-m-d H:i:s');
			# filter
			if ($this->model_trans_paket_cud->proses_pembayaran_fee_agen($this->input->post('id'), $data)) {
				// create session priting here
				$this->session->set_userdata(array('cetak_invoice' => array(
					'type' => 'payment_fee',
					'invoice' => $this->input->post('invoice')
				)));
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses pembayaran fee keagenan berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses pembayaran fee keagenan gagal dilakukan.',
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

	# get info fee
	function get_info_fee()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('agen_id',	'<b>Agen ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_id_agen');
		$this->form_validation->set_rules('paket_id', '<b>Paket ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_paket_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info keagenan
			$info_agen = $this->model_trans_paket->get_info_agen($this->input->post('agen_id'));
			# filter
			if (count($info_agen) > 0) {
				// create session priting here
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data agen berhasil ditemukan.',
					'data' => $info_agen,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
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

	# check penyetor in database
	function _ck_penyetor_exist( $jamaah_id ) {
		if( $jamaah_id != 0 ) {
			if( ! $this->model_trans_paket->check_penyetor_exist( $jamaah_id ) ) {
				$this->form_validation->set_message('_ck_penyetor_exist', 'Biaya deposit penyetor tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	#  get agen info
	function getInfoDeposit(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('jamaah_id',	'<b>Jamaah<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_penyetor_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# pool id
			$jamaah_id = $this->input->post('jamaah_id');
			# get pool id
			$pool_id = $this->model_trans_paket->get_pool_id_by_jamaah_id($jamaah_id);
			# list_agen
			// $list_agen = $this->model_trans_paket->get_list_agen();
			# get nama agen selected
			$get_agen_selected = $this->model_trans_paket->get_agen_selected($pool_id);
			# get fee agen
			$fee_agen = $this->model_trans_paket->get_fee_agen($pool_id);
			# filter
			if( $error == 0 ){
					$return = array(
						'error'	=> false,
						'error_msg' => 'Data agen berhasil ditemukan.',
						// 'list_agen' => $list_agen,
						'data' => array('agen_selected' => $get_agen_selected,
										 	 'fee_agen' => $fee_agen),
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
					// 'list_agen' => $list_agen,
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

	# get info fee agen
	function getInfoFeeAgen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('jamaah_id',	'<b>Jamaah<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_penyetor_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# pool id
			$jamaah_id = $this->input->post('jamaah_id');
			# get agen info
			$agen_info = $this->model_trans_paket->get_agen_info($jamaah_id);

			// print_r($agen_info);
			# filter
			if( count($agen_info) > 0 ){

					$fee_agen = $this->model_trans_paket->agen_upline_tree($agen_info['agen_id']);

					$return = array(
						'error'	=> false,
						'error_msg' => 'Data agen berhasil ditemukan.',
						'nama_agen' => $agen_info['nama_agen'],
						'agen_fee' => $fee_agen,
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
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

	function get_list_agen(){
		$error = 0;
		# list_agen
		$list_agen = $this->model_trans_paket->get_list_agen();
		# filter
		if ( $error == 1) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Daftar agen tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Daftar agen berhasil ditemukan.',
				'list_agen' => $list_agen,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	# info deposit pembayaran jamaah
	function infoDepositPembayaranJamaah(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('jamaah_id',	'<b>Jamaah<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_penyetor_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# pool id
			$jamaah_id = $this->input->post('jamaah_id');
			# get info pembayaran
			$pembayaran = $this->model_trans_paket->info_pembayaran_deposit_jamaah($jamaah_id);
			# filter
			if( $error == 0 ){
					$return = array(
						'error'	=> false,
						'error_msg' => 'Data agen berhasil ditemukan.',
						'pembayaran' => $pembayaran,
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
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

	// get info fee agen
	function getInfoFeeAgen2(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_paket_transaction_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get info paket transaction
			$info_paket_transaction = $this->model_trans_paket->get_info_paket_transaction( $this->input->post('paket_transaction_id') );
			# filter
			if( count($info_paket_transaction) > 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data agen berhasil ditemukan.',
					'data' => $info_paket_transaction,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen tidak ditemukan.',
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

	// proses update fee agen
	function prosesUpdateFeeAgen(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('paket_transaction_id', '<b>Paket Transaction ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_paket_transaction_id');
     	foreach ($this->input->post('fee_agen') as $key => $value) {
        	$this->form_validation->set_rules("fee_agen[" . $key . "]", "Fee Agen", 'trim|required|xss_clean|min_length[1]');
     	}
      	/*
        	Validation process
      	*/
      	if ( $this->form_validation->run() ) {
      		// personal id
      		$paket_transaction_info = $this->model_trans_paket->get_info_paket_transaction_by_paket_transaction_id($this->input->post('paket_transaction_id'));
      		# level agen
            $level_agen = $this->model_trans_paket->get_level_agen();
      		// data
     		$data = array();
     		if( $paket_transaction_info['fee_keagenan_id'] != 0 ) {
     			$data['paket_transaction']['fee_keagenan_id'] = $paket_transaction_info['fee_keagenan_id'];
     		}
            # fee keagenan
            if( $this->input->post('fee_agen') ) {
               $data['fee_keagenan']['company_id'] = $this->company_id;
               $data['fee_keagenan']['personal_id'] = $paket_transaction_info['personal_id'];
               $data['fee_keagenan']['input_date'] = date('Y-m-d');
               $data['fee_keagenan']['last_update'] = date('Y-m-d');
               # receive fee agen
               $fee_agen = $this->input->post('fee_agen');
               # detail fee keagenan
               foreach ( $fee_agen as $key => $value ) {
                  	$data['detail_fee_keagenan'][] = array('transaction_number' => $this->random_code_ops->number_transaction_detail_fee_keagenan(),
                                                      	  'company_id' => $this->company_id,
                                                      	  'agen_id' => $key,
                                                      	  'level_agen_id' => $level_agen[$key],
                                                      	  'fee' => $this->text_ops->hide_currency($value),
                                                      	  'input_date' => date('Y-m-d H:i:s'),
                                                      	  'last_update' => date('Y-m-d H:i:s'));
               }
            }
            // filter
            if( ! $this->model_trans_paket_cud->update_fee_keagenan( $this->input->post('paket_transaction_id'), $paket_transaction_info['no_register'], $data ) ) {
            	# create return
	           	$return = array(
					'error'   => true,
					'error_msg' => 'Proses update fee keagenan gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
	           	);
            }else{
            	# create return
				$return = array(
					'error'   => false,
					'error_msg' => 'Proses update fee keagenan berhasil dilakukan.',
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

	// download all jamaah to excel
	function download_all_jamaah_to_excel()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		# set session
		$this->session->set_userdata(array('download_to_excel' => array(
			'type' => 'download_all_jamaah_to_excel',
		)));
		if (!$this->session->userdata('download_to_excel')) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Download all jamaah tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Download all jamaah berhasil ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function get_member_not_jamaah(){
		$return = array();
		$error = 0;
		$error_msg = '';
		// get info
		$info_member = $this->model_trans_paket->get_member_not_jamaah();
		// filter
		if ( count( $info_member ) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data member not jamaah tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'data' => $info_member,
				'error'	=> false,
				'error_msg' => 'Data member not jamaah berhasil ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_member_id($id){
		if( ! $this->model_trans_paket->check_member_id_exist($id) ){
        	$this->form_validation->set_message('_ck_member_id', 'Member ID tidak ditemukan.');
         	return FALSE;
      	}else{
         	return TRUE;
      	}
	}

	function info_jamaah_by_member(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('member_id', '<b>Member ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_member_id');
      	/*
        	Validation process
      	*/
      	if ( $this->form_validation->run() ) {

			$error = 0;
			$gender = array('Laki-laki', 'Perempuan');
			$golongan_darah = array('Pilih Golongan Darah', 'O', 'A', 'B', 'AB');
			$jamaah = $this->model_trans_paket->get_jamaah();

			$status_nikah = array('MENIKAH' => 'MENIKAH', 'BELUM MENIKAH' => 'BELUM MENIKAH', 'JANDA / DUDA' => 'JANDA / DUDA');
			$title = array('TUAN' => 'TUAN', 'NONA' => 'NONA', 'NYONYA' => 'NYONYA');
			$kewarganegaraan = array('WNI' => 'WNI', 'WNA' => 'WNA');
			$jenis_identitas = array('NIK' => 'NIK', 'KITAS' => 'KITAS', 'KITAP' => 'KITAP', 'PASPOR' => 'PASPOR');

			$provinsi = $this->model_trans_paket->get_provinsi();
			$kabupaten_kota = array( '-- Pilih Kabupaten / Kota --' );
			$kecamatan = array( '-- Pilih Kecamatan --' );
			$kelurahan = array( '-- Pilih Kelurahan --' );

			$status_mahram = $this->model_trans_paket->get_status_mahram();
			$pengalaman_haji_umrah = array(
				'Belum Pernah', 'Sudah', 'Sudah 1 Kali', 'Sudah 2 Kali', 'Sudah 3 Kali', 'Sudah 4 Kali', 'Sudah 5 Kali',
				'Sudah 6 Kali', 'Sudah 7 Kali', 'Sudah 8 Kali', 'Sudah 9 Kali', 'Sudah 10 Kali', 'Sudah 11 Kali'
			);
			$pekerjaan = $this->model_trans_paket->get_pekerjaan();
			$pendidikan = $this->model_trans_paket->get_pendidikan();
			$info_agen = $this->model_trans_paket->get_list_agen();
			// get member id
			$member_info = $this->model_trans_paket->get_member_info( $this->input->post('member_id') );
			// filter errors
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info jamaah tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info jamaah berhasil ditemukan.',
					'data' => array(
						'list_agen' => $info_agen,
						'gender' => $gender,
						'golongan_darah' => $golongan_darah,
						'jamaah' => $jamaah,
						'status_nikah' => $status_nikah,
						'title' => $title,
						'kewarganegaraan' => $kewarganegaraan,
						'jenis_identitas' => $jenis_identitas,
						'status_mahram' => $status_mahram,
						'pengalaman_haji_umrah' => $pengalaman_haji_umrah,
						'pendidikan' => $pendidikan,
						'pekerjaan' => $pekerjaan,
						'provinsi' => $provinsi, 
						'kabupaten_kota' => $kabupaten_kota, 
						'kecamatan' => $kecamatan, 
						'kelurahan' => $kelurahan,
						'member_info' => $member_info
					),
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

	// add update jamaah by member
	function add_update_jamaah_by_member(){
		$return = array();
		$error = 0;
		$uploadPhoto = 0;
		$error_msg = 'Anda wajib mengupload photo';
		$this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist');
		$this->form_validation->set_rules('personal_id', '<b>Personal ID<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_jamaah_is_exist_by_personal_id');
		$this->form_validation->set_rules('title', '<b>Title<b>', 'trim|xss_clean|required|min_length[1]|in_list[TUAN,NONA,NYONYA]');
		$this->form_validation->set_rules('nama_pasport', '<b>Nama Jamaah<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kewarganegaraan', '<b>Kewarganegaraan<b>', 'trim|xss_clean|required|min_length[1]|in_list[WNI,WNA]');
		$this->form_validation->set_rules('jenis_identitas', '<b>Jenis Identitas<b>', 'trim|xss_clean|required|min_length[1]|in_list[NIK,KITAS,KITAP,PASPOR]');
		$this->form_validation->set_rules('golongan_darah', '<b>Golongan Darah<b>', 'trim|xss_clean|min_length[1]|in_list[0,1,2,3,4]|callback__ck_blood_type');
		$this->form_validation->set_rules('kode_pos', '<b>Kode Pos<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('telephone', '<b>Telephone<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_ayah', '<b>Nama Ayah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_keluarga',	'<b>Nama Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('telephone_keluarga', '<b>Telephone Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_keluarga', '<b>Alamat Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nomor_passport', '<b>Nomor Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tempat_dikeluarkan', '<b>Tempat Dikeluakan Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tanggal_dikeluarkan',	'<b>Tanggal Dikeluarkan Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('masa_berlaku', '<b>Masa Berlaku Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kelurahan', '<b>Kelurahan ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_kelurahan_id_exist');
		$this->form_validation->set_rules('status_nikah', '<b>Status Nikah<b>', 'trim|required|xss_clean|min_length[1]|in_list[MENIKAH,BELUM MENIKAH,JANDA / DUDA]');
		$this->form_validation->set_rules('tanggal_nikah',	'<b>Tanggal Nikah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pengalaman_haji', '<b>Pengalaman Haji<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]');
		$this->form_validation->set_rules('tahun_haji',	'<b>Tahun Haji<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pengalaman_umrah', '<b>Pengalaman Umrah<b>', 'trim|xss_clean|min_length[1]|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]');
		$this->form_validation->set_rules('tahun_umrah', '<b>Tahun Umrah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('berangkat_dari', '<b>Berangkat Dari<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pekerjaan', '<b>Pekerjaan Jamaah<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pekerjaan');
		$this->form_validation->set_rules('alamat_instansi', '<b>Alamat Instansi Pekerjaan Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_instansi',	'<b>Nama Instansi Pekerjaan Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('telephone_instansi', '<b>Telephone Instansi Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('pendidikan_terakhir', '<b>Pendidikan Terakhir Jamaah<b>', 'trim|xss_clean|min_length[1]|callback__ck_pendidikan');
		$this->form_validation->set_rules('penyakit', '<b>Penyakit Yang Diderita Jamaah<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('telephone_keluarga', '<b>Telephone Keluarga<b>', 'trim|xss_clean|min_length[1]');
		// checkbox kelengkapan
		$this->form_validation->set_rules('photo_4_6', '<b>Photo 4x6<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('photo_3_4', '<b>Photo 3x4<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('fc_passport', '<b>Fotocopy Passport<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('fc_kk', '<b>Fotocopy Kartu Keluarga<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('fc_ktp', '<b>Fotocopy KTP<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('buku_nikah',	'<b>Buku Nikah Asli<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('akte_lahir',	'<b>Akte Lahir<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('buku_kuning', '<b>Buku Kuning<b>', 'trim|xss_clean|min_length[1]');
		// get agen id
		$this->form_validation->set_rules('agen', '<b>Agen<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_agen_is_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// receive data
			$data = array();
			$data['jamaah']['company_id'] =  $this->company_id;
			$data['jamaah']['personal_id'] = $this->input->post('personal_id');
			$data['jamaah']['title'] = $this->input->post('title');
			$data['jamaah']['pasport_name'] = $this->input->post('nama_pasport');
			$data['jamaah']['kewarganegaraan'] = $this->input->post('kewarganegaraan');
			$data['jamaah']['jenis_identitas'] = $this->input->post('jenis_identitas');
			$data['jamaah']['blood_type'] = $this->input->post('golongan_darah');
			$data['jamaah']['pos_code'] = $this->input->post('kode_pos');
			$data['jamaah']['telephone'] = $this->input->post('telephone');
			$data['jamaah']['father_name'] = $this->input->post('nama_ayah');
			$data['jamaah']['nama_keluarga'] = $this->input->post('nama_keluarga');
			$data['jamaah']['telephone_keluarga'] = $this->input->post('telephone_keluarga');
			$data['jamaah']['alamat_keluarga'] = $this->input->post('alamat_keluarga');
			$data['jamaah']['passport_number'] = $this->input->post('nomor_passport');
			$data['jamaah']['passport_place'] = $this->input->post('tempat_dikeluarkan');
			$data['jamaah']['passport_dateissue'] = $this->input->post('tanggal_dikeluarkan');
			$data['jamaah']['validity_period'] = $this->input->post('masa_berlaku');
			$data['jamaah']['kelurahan_id'] = $this->input->post('kelurahan');
			$data['jamaah']['status_nikah'] = $this->input->post('status_nikah') == '0' ? 'belum_nikah' : 'nikah';
			$data['jamaah']['tanggal_nikah'] = $this->input->post('tanggal_nikah');
			$data['jamaah']['hajj_experience'] = $this->input->post('pengalaman_haji');
			$data['jamaah']['hajj_year'] = $this->input->post('tahun_haji');
			$data['jamaah']['umrah_experience'] = $this->input->post('pengalaman_umrah');
			$data['jamaah']['umrah_year'] = $this->input->post('tahun_umrah');
			$data['jamaah']['departing_from'] = $this->input->post('berangkat_dari');
			$data['jamaah']['pekerjaan_id'] = $this->input->post('pekerjaan');
			$data['jamaah']['profession_instantion_address'] = $this->input->post('alamat_instansi');
			$data['jamaah']['profession_instantion_name'] = $this->input->post('nama_instansi');
			$data['jamaah']['profession_instantion_telephone'] = $this->input->post('telephone_instansi');
			$data['jamaah']['last_education'] = $this->input->post('pendidikan_terakhir');
			$data['jamaah']['desease'] = $this->input->post('penyakit');
			$data['jamaah']['keterangan'] = $this->input->post('keterangan');
			if( $this->input->post('agen') != '0' ) {
				$data['jamaah']['agen_id'] = $this->input->post('agen');
			}
			$data['jamaah']['photo_4_6'] = $this->input->post('photo_4_6') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['photo_3_4'] = $this->input->post('photo_3_4') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['fc_passport'] = $this->input->post('fc_passport') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['fc_kk'] = $this->input->post('fc_kk') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['fc_ktp'] = $this->input->post('fc_ktp') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['buku_nikah'] = $this->input->post('buku_nikah') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['akte_lahir'] = $this->input->post('akte_lahir') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['buku_kuning'] = $this->input->post('buku_kuning') == '1' ? 'ada' : 'tidak ada';
			$data['jamaah']['keterangan'] = $this->input->post('keterangan');
			$data['jamaah']['last_update'] = date('Y-m-d');

			// image
			if ( $this->input->post('base64image') and strpos($this->input->post('base64image'), 'data:image/jpeg;base64,') !== false ) {
				//define name
				if ( $this->input->post('personal_id') ) {
					$file_name 	= $this->model_trans_paket->getPhotoPersonalName($this->input->post('personal_id'));
					if ( $file_name == '' ) {
						$file_name = md5(date('Ymdhis')) . '.jpeg';
					} else {
						$old_photo_name = $file_name;
					}
				} else {
					$file_name = md5(date('Ymdhis')) . '.jpeg';
				}
				$img = str_replace('data:image/jpeg;base64,', '', $this->input->post('base64image'));
				$img = str_replace(' ', '+', $img);
				$data_image = base64_decode($img);
				$file =  FCPATH . '/image/personal/' . $file_name;
				$success = file_put_contents($file, $data_image);
				$data['personal']['photo'] = $file_name;
			}
			// mahram			
			$mahrams = $this->input->post('mahram');
			$status_mahram = $this->input->post('statusMahram');
			$list = array();
			foreach ( $mahrams as $key => $value ) {
				$list[] = array('company_id' => $this->company_id,
								'mahram_id' => $value,
								'status' => $status_mahram[$key], 
								'input_date' =>  date('Y-m-d'),
								'last_update' =>  date('Y-m-d'));
			}
			$data['mahram'] = $list;

			// print_r( $data );
			// filter jamaah
			if( $this->input->post('jamaah_id') ) 
			{
				if( ! $this->model_trans_paket_cud->update_jamaah_by_member($this->input->post('jamaah_id'), $data) ) 
				{
					$error = 1;
					$error_msg = 'Proses update data jamaah gagal dilakukan.';
				}
			}else{
				$data['jamaah']['input_date'] = date('Y-m-d');
				if( ! $this->model_trans_paket_cud->insert_jamaah_by_member($data) )
				{
					$error = 1;
					$error_msg = 'Proses input data jamaah gagal dilakukan.';
				}
			}
			// filter
			if ( $error == 1 ) 
			{
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data jamaah berhasil disimpan.',
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


	// $dataMahram = array();
	// $list_mahram_jamaah = array();
	// $list_status_mahram_jamaah = array();
	// if ($value != 0) {
	// 	$list_mahram_jamaah[] = $value;
	// 	$list_status_mahram_jamaah[] = $status_mahram[$key];
	// }
	// if (count($list_mahram_jamaah) > 0) {
	// 	$dataMahram['mahram_id'] = $mahrams;
	// 	$dataMahram['status'] = $status_mahram;
	// }

	// $dataPersonal = array();
	// $dataParam['personal_id'] = $this->input->post('personal_id');

	// $dataPersonal['fullname'] = $this->input->post('nama_jamaah');
	// $dataPersonal['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
	// $dataPersonal['gender'] = $this->input->post('jenis_kelamin');
	// $dataPersonal['birth_place'] = $this->input->post('tempat_lahir');
	// $dataPersonal['birth_date'] = $this->input->post('tanggal_lahir');
	// $dataPersonal['address'] = $this->input->post('alamat');
	// $dataPersonal['email'] = $this->input->post('email');
	// $dataPersonal['identity_number'] = $this->input->post('no_identitas');
	
	// $nomor_whatsapp = $this->input->post('nomor_whatsapp');
	// // if ( substr($nomor_whatsapp, 0, 1) == '0' ) {
	// // 	$nomor_whatsapp = '62' . substr($nomor_whatsapp, 1);
	// // }
	// $dataPersonal['nomor_whatsapp'] = $nomor_whatsapp;

	// if ( $this->input->post('password') != '' ) {
	// 	$dataPersonal['password'] = password_hash($this->input->post('password') . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
	// }
	// $old_photo_name = '';
	// // image
	// if ( $this->input->post('base64image') and strpos($this->input->post('base64image'), 'data:image/jpeg;base64,') !== false ) {
	// 	//define name
	// 	if ($this->input->post('personal_id')) {
	// 		$file_name 	= $this->model_trans_paket->getPhotoPersonalName($this->input->post('personal_id'));
	// 		if ($file_name == '') {
	// 			$file_name = md5(date('Ymdhis')) . '.jpeg';
	// 		} else {
	// 			$old_photo_name = $file_name;
	// 		}
	// 	} else {
	// 		$file_name = md5(date('Ymdhis')) . '.jpeg';
	// 	}

	// 	$img = str_replace('data:image/jpeg;base64,', '', $this->input->post('base64image'));
	// 	$img = str_replace(' ', '+', $img);
	// 	$data = base64_decode($img);
	// 	$file =  FCPATH . '/image/personal/' . $file_name;
	// 	$success = file_put_contents($file, $data);
	// 	$dataPersonal['photo'] = $file_name;
	// }
	// // data jamaah
	// $dataJamaah = array();
	// $dataJamaah['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
	// $dataJamaah['blood_type'] = $this->input->post('golongan_darah');
	// $dataJamaah['pos_code'] = $this->input->post('kode_pos');
	// $dataJamaah['telephone'] = $this->input->post('telephone');
	// if( $this->input->post('agen') != '0' ) {
	// 	$dataJamaah['agen_id'] = $this->input->post('agen');
	// }
	// $dataJamaah['title'] = $this->input->post('title');
	// $dataJamaah['kewarganegaraan'] = $this->input->post('kewarganegaraan');
	// $dataJamaah['jenis_identitas'] = $this->input->post('jenis_identitas');
	// $dataJamaah['kelurahan_id'] = $this->input->post('kelurahan');
	// $dataJamaah['passport_number'] = $this->input->post('nomor_passport');
	// $dataJamaah['passport_place'] = $this->input->post('tempat_dikeluarkan');
	// $dataJamaah['passport_dateissue'] = $this->input->post('tanggal_dikeluarkan');
	// $dataJamaah['validity_period'] = $this->input->post('masa_berlaku');
	// $dataJamaah['father_name'] = $this->input->post('nama_ayah');
	// $dataJamaah['alamat_keluarga'] = $this->input->post('alamat_keluarga');
	// $dataJamaah['status_nikah'] = $this->input->post('status_nikah') == '0' ? 'belum_nikah' : 'nikah';
	// $dataJamaah['telephone_keluarga'] = $this->input->post('telephone_keluarga');
	// $dataJamaah['tanggal_nikah'] = $this->input->post('tanggal_nikah');
	// $dataJamaah['nama_keluarga'] = $this->input->post('nama_keluarga');
	// $dataJamaah['hajj_experience'] = $this->input->post('pengalaman_haji');
	// $dataJamaah['hajj_year'] = $this->input->post('tahun_haji');
	// $dataJamaah['umrah_experience'] = $this->input->post('pengalaman_umrah');
	// $dataJamaah['umrah_year'] = $this->input->post('tahun_umrah');
	// $dataJamaah['departing_from'] = $this->input->post('berangkat_dari');
	// $dataJamaah['pekerjaan_id'] = $this->input->post('pekerjaan');
	// $dataJamaah['profession_instantion_address'] = $this->input->post('alamat_instansi');
	// $dataJamaah['profession_instantion_name'] = $this->input->post('nama_instansi');
	// $dataJamaah['profession_instantion_telephone'] = $this->input->post('telephone_instansi');
	// $dataJamaah['last_education'] = $this->input->post('pendidikan_terakhir');
	// $dataJamaah['desease'] = $this->input->post('penyakit');
	// $dataJamaah['keterangan'] = $this->input->post('keterangan');
	// keterangan
	// $dataJamaah['photo_4_6'] = $this->input->post('photo_4_6') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['photo_3_4'] = $this->input->post('photo_3_4') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['fc_passport'] = $this->input->post('fc_passport') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['fc_kk'] = $this->input->post('fc_kk') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['fc_ktp'] = $this->input->post('fc_ktp') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['buku_nikah'] = $this->input->post('buku_nikah') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['akte_lahir'] = $this->input->post('akte_lahir') == '1' ? 'ada' : 'tidak ada';
	// $dataJamaah['buku_kuning'] = $this->input->post('buku_kuning') == '1' ? 'ada' : 'tidak ada';

	// $feedBack = ;



