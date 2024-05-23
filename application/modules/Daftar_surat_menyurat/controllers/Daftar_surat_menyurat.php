<?php

/**
 *  -----------------------
 *	Daftar surat menyurat Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_surat_menyurat extends CI_Controller
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
		$this->load->model('Model_daftar_surat_menyurat', 'model_daftar_surat_menyurat');
		# model fasilitas cud
		$this->load->model('Model_daftar_surat_menyurat_cud', 'model_daftar_surat_menyurat_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	// server surat menyurat
	function server_surat_menyurat(){
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
			$total = $this->model_daftar_surat_menyurat->get_total_surat_menyurat($search);
			$list = $this->model_daftar_surat_menyurat->get_index_surat_menyurat($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar surat menyurat tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar surat berhasil ditemukan.',
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

	// check konfigurasi surat menyurat
	function check_konfigurasi_surat(){
		// filter
		if ( ! $this->model_daftar_surat_menyurat->check_konfigurasi_surat() ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Untuk melakukan cetak surat. Anda wajib mengisi terlebih melakukan pengaturan surat menyurat.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Success.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);

	}

	// get info konfigurasi surat menyurat
	function get_info_konfigurasi_surat_menyurat(){
		// get data
		$data = $this->model_daftar_surat_menyurat->get_info_konfigurasi_surat_menyurat();
		// filter
		if ( count($data) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data setting surat menyurat perusahaan tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Success.',
				'data' => $data,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// update setting surat menyurat
	function proses_update_setting_surat_menyurat(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('nama_pejabat', '<b>Nama Pejabat<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jabatan', '<b>Jabatan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat', '<b>Alamat<b>',  'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nama_perusahaan', '<b>Nama Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kota_perusahaan', '<b>Kota Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('provinsi_perusahaan', '<b>Provinsi Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('alamat_perusahaan', '<b>Alamat Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('no_kontak_perusahaan', '<b>Nomor Kontak Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('izin_perusahaan', '<b>Izin Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('website_perusahaan', '<b>Website Perusahaan<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('email_perusahaan', '<b>Email Perusahaan<b>', 'trim|required|xss_clean|valid_email|min_length[1]');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			// insert data
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['nama_tanda_tangan'] = $this->input->post('nama_pejabat');
			$data['jabatan_tanda_tangan'] = $this->input->post('jabatan');
			$data['alamat_tanda_tangan'] = $this->input->post('alamat');
			$data['nama_perusahaan'] = $this->input->post('nama_perusahaan');
			$data['izin_perusahaan'] = $this->input->post('izin_perusahaan');
			$data['kota_perusahaan'] = $this->input->post('kota_perusahaan');
			$data['provinsi_perusahaan'] = $this->input->post('provinsi_perusahaan');
			$data['alamat_perusahaan'] = $this->input->post('alamat_perusahaan');
			$data['no_kontak_perusahaan'] = $this->input->post('no_kontak_perusahaan');
			$data['website_perusahaan'] = $this->input->post('website_perusahaan');
			$data['email_perusahaan'] = $this->input->post('email_perusahaan');
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			// filter
			if ( ! $this->model_daftar_surat_menyurat_cud->update_setting_surat_menyurat( $data ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses update setting surat menyurat gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses update setting surat menyurat berhasil dilakukan.',
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

	function get_jamaah_surat_menyurat(){
		// get data
		$data = $this->model_daftar_surat_menyurat->get_jamaah();
		// filter
		if ( count($data) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data jamaah tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Success.',
				'data' => $data,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_jenis_surat($jenis_surat){
		if( $jenis_surat == 'pilih'){
			$this->form_validation->set_message('_ck_jenis_surat', 'Silahkan pilih salah satu jenis surat.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _ck_jamaah_id( $jamaah_id ) {
		if( $this->input->post('jenis_surat') == 'rekom_paspor' ) {
			if( $jamaah_id == 0 ) {
				$this->form_validation->set_message('_ck_jamaah_id', 'Untuk jenis surat <b>REKOMENDASI PEMBUATAN PASPOR</b> anda <b style="color:red">wajib</b> memilih salah satu jamaah.');
				return FALSE;
			}else{
				if( ! $this->model_daftar_surat_menyurat->check_jamaah_id_exist( $jamaah_id ) ) {
					$this->form_validation->set_message('_ck_jamaah_id', 'Nama jamaah tidak ditemukan dipangkalan data.');
					return FALSE;	
				}else{
					return TRUE;
				}
			}
		}
	}

	// check nomor surat exist
	function _ck_nomor_surat_exis( $nomor_surat ) {
		if( $this->model_daftar_surat_menyurat->check_nomor_surat_exit( $nomor_surat ) ) {
			$this->form_validation->set_message('_ck_nomor_exis', 'Nomor surat sudah terdaftar dipangkalan data.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// proses tambah surat menyurat
	function proses_tambah_surat_menyurat(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('nomor_surat',	'<b>Nama Pejabat<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_nomor_surat_exis');
		$this->form_validation->set_rules('tanggal_surat',	'<b>Tanggal Surat<b>', 	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('tujuan',	'<b>Tujuan<b>', 	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jenis_surat', '<b>Jenis Surat<b>', 	'trim|required|xss_clean|min_length[1]|in_list[pilih,rekom_paspor,surat_cuti]|callback__ck_jenis_surat');
		$this->form_validation->set_rules('jamaah',	'<b>Jamaah<b>', 	'trim|xss_clean|min_length[1]|callback__ck_jamaah_id');
		$this->form_validation->set_rules('bulan_tahun_berangkat',	'<b>Bulan dan Tahun Berangkat<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('jabatan',	'<b>Jabatan<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('keberangkatan',	'<b>Keberangkatan<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('kepulangan',	'<b>Kepulangan<b>', 	'trim|xss_clean|min_length[1]');

		/*
        	Validation process
      	*/
		if ($this->form_validation->run()) {
			// info
			$info = array();
			if( $this->input->post('jenis_surat') == 'rekom_paspor' ) {
				$info['jamaah_id'] = $this->input->post('jamaah');
				$info['bulan_tahun_berangkat'] = $this->input->post('bulan_tahun_berangkat');
			} else if ( $this->input->post('jenis_surat') == 'surat_cuti' ) {
				$info['jamaah_id'] = $this->input->post('jamaah');
				$info['jabatan'] = $this->input->post('jabatan');
				$info['keberangkatan'] = $this->input->post('keberangkatan');
				$info['kepulangan'] = $this->input->post('kepulangan');
			}
			// insert data
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['nomor_surat'] = $this->input->post('nomor_surat');
			$data['tipe_surat'] = $this->input->post('jenis_surat');
			$data['tanggal_surat'] = $this->input->post('tanggal_surat');
			$data['tujuan'] = $this->input->post('tujuan');
			// check level
			if ( $this->session->userdata( $this->config->item( 'apps_name' ) )['level_akun'] == 'administrator' ) {
				$data['nama_petugas'] = 'administrator';
				$data['petugas_id'] = 0;
			} else {
				$data['nama_petugas'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
				$data['petugas_id'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
			}
			$data['info'] = json_encode($info);
			$data['input_date'] = date('Y-m-d H:i:s');
			$data['last_update'] = date('Y-m-d H:i:s');
			// filter
			if ( ! $this->model_daftar_surat_menyurat_cud->insert_surat_menyurat( $data ) ) {
				// return
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses cetak surat menyurat gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// create session kwitansi
				if( $data['tipe_surat'] == 'rekom_paspor' ) {
					$this->session->set_userdata(array('cetak_invoice' => array(
						'type' => 'cetak_surat_rekom_paspor',
						'nomor_surat' => $data['nomor_surat']
					)));
				}elseif ( $data['tipe_surat'] == 'surat_cuti' ) {
					$this->session->set_userdata(array('cetak_invoice' => array(
						'type' => 'cetak_surat_cuti',
						'nomor_surat' => $data['nomor_surat']
					)));
				}
				// return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses cetak surat menyurat berhasil dilakukan.',
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

	// check riwayat surat menyurat exist
	function _ck_riwayat_surat_menyurat_exist($id){
		if( ! $this->model_daftar_surat_menyurat->check_riwayat_surat_menyurat_exist( $id ) ) {
			$this->form_validation->set_message('_ck_riwayat_surat_menyurat_exist', 'ID riwayat surat menyurat tidak terdaftar dipangkalan data.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// cetak riwayat surat menyurat
	function cetak_riwayat_surat_menyurat(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Riwayat Surat Menyurat<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_riwayat_surat_menyurat_exist');
		/*
        	Validation process
      	*/
		if ($this->form_validation->run()) {
			// nomor surat
			$arr = $this->model_daftar_surat_menyurat->get_nomor_surat_by_id( $this->input->post('id') );
			// filter
			if ( count($arr) == 0 ) {
				// return
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses cetak surat menyurat gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// create session kwitansi
				if( $arr['tipe_surat'] == 'rekom_paspor' ) {
					$this->session->set_userdata(array('cetak_invoice' => array(
						'type' => 'cetak_surat_rekom_paspor',
						'nomor_surat' => $arr['nomor_surat']
					)));
				}elseif ( $arr['tipe_surat'] == 'surat_cuti' ) {
					$this->session->set_userdata(array('cetak_invoice' => array(
						'type' => 'cetak_surat_cuti',
						'nomor_surat' => $arr['nomor_surat']
					)));
				}
				// return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses cetak surat menyurat berhasil dilakukan.',
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

	// delete surat menyurat
	function delete_surat_menyurat(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Riwayat Surat Menyurat<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_riwayat_surat_menyurat_exist');
		/*
        	Validation process
      	*/
		if ($this->form_validation->run()) {
			// delete process
			if ( ! $this->model_daftar_surat_menyurat_cud->delete_riwayat_surat( $this->input->post('id') ) ) {
				// return
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete surat menyurat gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				// return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete surat menyurat berhasil dilakukan.',
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