<?php

/**
 *  -----------------------
 *	Daftar paket Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_paket extends CI_Controller
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
		$this->load->model('Model_daftar_paket', 'model_daftar_paket');
		# model fasilitas cud
		$this->load->model('Model_daftar_paket_cud', 'model_daftar_paket_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_pakets()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('status_keberangkatan',	'<b>Status Keberangkatan<b>', 'trim|required|xss_clean|min_length[1]|in_list[semua,belum_berangkat,sudah_berangkat]');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$status = $this->input->post('status_keberangkatan');
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total = $this->model_daftar_paket->get_total_daftar_paket($search, $status);
			$list = $this->model_daftar_paket->get_index_daftar_paket($perpage, $start_at, $search, $status);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar paket tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar paket berhasil ditemukan.',
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

	function error_filter($array, $name){
		$error = 1;
		$error_msg = '';
		if(count($array) == 0 ){
			$error = 1;
			$error_msg = $name;
		}
	}

	# get info
	function get_info_paket()
	{
		$error = 0;
		$error_msg = '';
		# get kode paket
		$kode = $this->random_code_ops->gen_kode_paket();
		# paket type
		$paket_type = $this->model_daftar_paket->get_paket_type();
		if( count($paket_type) == 0 ):
			$error = 1; $error_msg .= '<b>Tipe Paket,</b> ';
		endif;
		# provider
		$provider_visa = $this->model_daftar_paket->get_provider_visa();
		if( count($provider_visa) == 0 ):
			$error = 1; $error_msg .= '<b>Provider Visa,</b> ';
		endif;
		# get asuransi
		$asuransi = $this->model_daftar_paket->get_asuransi();
		if( count($asuransi) == 0 ):
			$error = 1; $error_msg .= '<b>Asuransi,</b> ';
		endif;
		# fisilitas
		$fasilitas = $this->model_daftar_paket->get_fasilitas_paket();
		if( count($fasilitas) == 0 ):
			$error = 1; $error_msg .= '<b>Fasilitas,</b> ';
		endif;
		# kota kunjungan
		$kota_kunjungan = $this->model_daftar_paket->get_kota();
		if( count($kota_kunjungan) == 0 ):
			$error = 1; $error_msg .= '<b>Kota Kunjungan,</b> ';
		endif;
		# airlines
		$airlines = $this->model_daftar_paket->get_airLines();
		if( count($airlines) == 0 ):
			$error = 1; $error_msg .= '<b>Maskapai,</b> ';
		endif;
		# hotel
		$hotel = $this->model_daftar_paket->get_hotel();
		if( count($hotel) == 0 ):
			$error = 1; $error_msg .= '<b>Hotel,</b> ';
		endif;
		# muthawif
		$muthawif = $this->model_daftar_paket->get_muthawif();
		if( count($muthawif) == 0 ):
			$error = 1; $error_msg .= '<b>Muthawif,</b> ';
		endif;
		# get beranda
		$bandara = $this->model_daftar_paket->get_bandara();
		if( count($bandara) == 0 ):
			$error = 1; $error_msg .= '<b>Bandara,</b> ';
		endif;
		# filter
		if ( $error == 1 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Terdapat preferensi yang belum ada setting seperti : '.$error_msg,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data info tambah paket la berhasil ditemukan.',
				'data' => array(
					'paket_type' => $paket_type,
					'fasilitas' => $fasilitas,
					'kota_kunjungan' => $kota_kunjungan,
					'airlines' => $airlines,
					'hotel' => $hotel,
					'muthawif' => $muthawif,
					'bandara' => $bandara,
					'kode' => $kode,
					'asuransi' => $asuransi,
					'provider_visa' => $provider_visa
				),
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	# Check paket id
	function _ck_paket_id_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_daftar_paket->check_paket_id_exist($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_paket_id_exist', 'Paket id tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
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

	function _ck_biaya_mahram( $harga ){
		$price = $this->text_ops->hide_currency( $harga );
		if ($price > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_biaya_mahram', 'Biaya Mahram tidak boleh NOL.');
			return FALSE;
		}
	}

	# Check id if in array
	function _ck_in_array($id, $jsonData)
	{
		$json = json_decode($jsonData, true);
		if (in_array($id, $json['list'])) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_in_array', 'ID ' . $json['title'] . ' tidak ditemukan.');
			return FALSE;
		}
	}

	# check kode paket is exist
	function _ck_kode_paket_is_exist($kode_paket)
	{
		if (!$this->model_daftar_paket->check_kode_paket_is_exist($kode_paket)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_kode_paket_is_exist', 'Kode paket sudah terdaftar dipangkalan data.');
			return FALSE;
		}
	}

	function _ck_exist_tipe_paket()
	{
		if (!$this->input->post('tipe_paket')) {
			$this->form_validation->set_message('_ck_exist_tipe_paket', 'Anda wajib memilih minimal 1 tipe paket.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# check provider visa id
	function _ck_provider_visa_id( $provider_visa_id ) {
		if( $provider_visa_id != '0' ){
			if( ! $this->model_daftar_paket->check_provider_visa_id($provider_visa_id) ){
				$this->form_validation->set_message('_ck_provider_visa_id', 'Provider Visa Id tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	# check asuransi id
	function _ck_asuransi_id( $asuransi_id ) {
		if( $asuransi_id != '0' ){
			if( ! $this->model_daftar_paket->check_asuransi_id( $asuransi_id ) ) {
				$this->form_validation->set_message('_ck_asuransi_id', 'Asuransi Id tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	# proses add n update data paket
	function proses_addupdate_paket()
	{
		# get list all tipe paket
		$list_tipe_paket_id = $this->model_daftar_paket->get_list_tipe_paket_id();
		# get list muthwawif id
		$list_muthawif_id = $this->model_daftar_paket->get_list_muthawif_id();
		# get bandara
		$list_bandara = $this->model_daftar_paket->get_list_airport();
		# get list fasilitas
		$list_fasilitas = $this->model_daftar_paket->get_list_fasilitas();
		# get list kota
		$list_kota = $this->model_daftar_paket->get_list_kota();
		# get_list_airlines
		$list_airlines = $this->model_daftar_paket->get_list_airlines();
		# list hotel
		$list_hotel = $this->model_daftar_paket->get_list_hotel();
		# validation rules
		$return = array();
		$error = 0;
		$error_msg = 'Anda wajib mengupload photo';
		if (!$this->input->post('id')) {
			$this->form_validation->set_rules('kode_paket', '<b>Kode Paket<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_kode_paket_is_exist');
		}
		# paket id
		$this->form_validation->set_rules('id', '<b>Paket ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_paket_id_exist');
		# nama paket
		$this->form_validation->set_rules('nama_paket',	'<b>Nama Paket<b>', 'trim|required|xss_clean|min_length[1]');
		# deskripsi paket
		$this->form_validation->set_rules('deskripsi_paket', '<b>Deskripsi Paket<b>', 'trim|required|xss_clean|min_length[1]');
		# jenis kegiatan
		$this->form_validation->set_rules('jenis_kegiatan', '<b>Jenis Kegiatan<b>', 'trim|required|xss_clean|min_length[1]|in_list[haji,umrah,haji_umrah]|callback__ck_exist_tipe_paket');
		# tipe paket
		if ($this->input->post('tipe_paket')) {
			foreach ($this->input->post('tipe_paket') as $key => $value) {
				$this->form_validation->set_rules("tipe_paket[" . $key . "]", "Tipe Paket", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_in_array[' . json_encode(array('list' => $list_tipe_paket_id, 'title' => "Tipe Paket")) . ']');
				# harga tipe paket
				$this->form_validation->set_rules("paket_type_price[" . $key . "]", "Harga tipe paket", 'trim|required|xss_clean|min_length[1]|callback__ck_harga_not_null');
			}
		}
		# provider_visa
		$this->form_validation->set_rules('provider_visa', '<b>Provider Visa<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_provider_visa_id');
		# asuransi
		$this->form_validation->set_rules('asuransi', '<b>Asuransi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_asuransi_id');
		# nopolis
		$this->form_validation->set_rules('nopolis', '<b>Asuransi<b>', 'trim|xss_clean|min_length[1]');
		# tanggal input polis
		$this->form_validation->set_rules('tgl_input_polis', '<b>Tanggal Input Polis<b>', 'trim|xss_clean|min_length[1]');
		# tanggal awal polis
		$this->form_validation->set_rules('tgl_awal_polis', '<b>Tanggal Awal Polis<b>', 'trim|xss_clean|min_length[1]');
		# tanggal akhir polis
		$this->form_validation->set_rules('tgl_akhir_polis', '<b>Tanggal Akhir Polis<b>', 'trim|xss_clean|min_length[1]');
		# tgl keberangkatan
		$this->form_validation->set_rules('tgl_keberangkatan', '<b>Tanggal Keberangkatan<b>', 	'trim|required|xss_clean|min_length[1]');
		# tgl kepulangan
		$this->form_validation->set_rules('tgl_kepulangan', '<b>Tanggal Kepulangan<b>', 'trim|required|xss_clean|min_length[1]');
		# biaya mahram
		$this->form_validation->set_rules('biaya_mahram', '<b>Biaya Mahram<b>', 'trim|required|xss_clean|min_length[1]');
		# berangkat_dari
		$this->form_validation->set_rules('berangkat_dari', '<b>Berangkat Dari<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_kota, 'title' => "Kota Keberangkatan")) . ']');
		# quota jamaah
		$this->form_validation->set_rules('quota',	'<b>Quota<b>', 	'trim|required|xss_clean|min_length[1]|numeric|greater_than[0]');
		# muthawif
		foreach ($this->input->post('muthawif') as $key => $value) {
			$this->form_validation->set_rules("muthawif[" . $key . "]", "Muthawif", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_in_array[' . json_encode(array('list' => $list_muthawif_id, 'title' => "Muthawif")) . ']');
		}
		# show
		$this->form_validation->set_rules('show',	'<b>Tampilkan Paket<b>', 'trim|xss_clean|min_length[1]');
		# bandara asal
		$this->form_validation->set_rules('bandara_asal', '<b>Bandara Asal<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_bandara, 'title' => "Bandara Asal")) . ']');
		# bandara tujuan
		$this->form_validation->set_rules('bandara_tujuan', '<b>Bandara Tujuan<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_bandara, 'title' => "Bandara Tujuan")) . ']');
		# waktu keberangkatan
		$this->form_validation->set_rules('waktu_keberangkatan',	'<b>Waktu Keberangkatan<b>', 'trim|xss_clean|min_length[1]');
		# waktu sampai
		$this->form_validation->set_rules('waktu_sampai',	'<b>Waktu Sampai<b>', 	'trim|xss_clean|min_length[1]');
		# tanggal aktifitas
		foreach ($this->input->post('tanggal_aktifitas') as $key => $value) {
			$this->form_validation->set_rules("tanggal_aktifitas[" . $key . "]", "Tanggal Aktifitas", 'trim|xss_clean|min_length[1]');
		}
		# judul aktifitas
		if ($this->input->post('judul_aktifitas')) {
			foreach ($this->input->post('judul_aktifitas') as $key => $value) {
				$this->form_validation->set_rules("judul_aktifitas[" . $key . "]", "Judul Aktifitas", 'trim|xss_clean|min_length[1]');
			}
		}
		# deskripsi aktifitas
		if ($this->input->post('deskripsi_aktifitas')) {
			foreach ($this->input->post('deskripsi_aktifitas') as $key => $value) {
				$this->form_validation->set_rules("deskripsi_aktifitas[" . $key . "]", "Deskripsi Aktifitas", 'trim|xss_clean|min_length[1]');
			}
		}
		# fasilitas
		if ($this->input->post('fasilitas')) {
			foreach ($this->input->post('fasilitas') as $key => $value) {
				$this->form_validation->set_rules("fasilitas[" . $key . "]", "Fasilitas", 'trim|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_fasilitas, 'title' => "Fasilitas")) . ']');
			}
		}
		# kota
		if ($this->input->post('kota')) {
			foreach ($this->input->post('kota') as $key => $value) {
				$this->form_validation->set_rules("kota[" . $key . "]", "Kota", 'trim|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_kota, 'title' => "Kota")) . ']');
			}
		}
		# airlines
		if ($this->input->post('airlines')) {
			foreach ($this->input->post('airlines') as $key => $value) {
				$this->form_validation->set_rules("airlines[" . $key . "]", "Airlines", 'trim|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_airlines, 'title' => "Airlines")) . ']');
			}
		}
		# hotel
		if ($this->input->post('hotel')) {
			foreach ($this->input->post('hotel') as $key => $value) {
				$this->form_validation->set_rules("hotel[" . $key . "]", "Hotel", 'trim|xss_clean|min_length[1]|callback__ck_in_array[' . json_encode(array('list' => $list_hotel, 'title' => "Hotel")) . ']');
			}
		}
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# define data
			$data = array();
			if (isset($_FILES['photo']) and $_FILES['photo']['size'] > 0) {
				# receiver post
				$_FILES['userFile']['name'] = $_FILES['photo']['name'];
				$_FILES['userFile']['type'] = $_FILES['photo']['type'];
				$_FILES['userFile']['tmp_name'] = $_FILES['photo']['tmp_name'];
				$_FILES['userFile']['error'] = $_FILES['photo']['error'];
				$_FILES['userFile']['size'] = $_FILES['photo']['size'];
				# define photo name
				$photo_with_extention = '';
				if ($this->input->post('id')) {
					$photo_with_extention = $this->model_daftar_paket->get_photo_name($this->input->post('id')); # ger photo name from database
					$photo_name = explode('.', $photo_with_extention)[0];
				} else {
					$photo_name = md5(date('Y-m-d H:i:s')); #  generateed photo name
				}
				# define config photo
				$path = 'image/paket/';
				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = $photo_name;
				$config['overwrite'] = TRUE;
				$config['max_size'] = 900;
				$this->load->library('upload', $config);
				$this->upload->overwrite = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ($this->upload->do_upload('userFile')) {
					$fileData = $this->upload->data();
					$data['photo'] = $fileData['file_name'];
					if ($photo_with_extention != $fileData['file_name'] and $photo_with_extention != '') {
						$src = FCPATH . 'image/paket/' . $photo_with_extention;
						if (file_exists($src)) {
							unlink($src);
						}
					}
				} else {
					$error 		= 1;
					$error_msg 	= $this->upload->display_errors();
				}
			}
			# insert and update process
			if ($error == 0) {
				$departure_date = strtotime($this->input->post('tgl_keberangkatan'));
				$return_date = strtotime($this->input->post('tgl_kepulangan'));
				# paket type and paket type price
				$tipe_paket = $this->input->post('tipe_paket');
				$paket_type_price = $this->input->post('paket_type_price');
				# itinerary data
				$tanggal_aktifitas = $this->input->post('tanggal_aktifitas');
				$judul_aktifitas = $this->input->post('judul_aktifitas');
				$deskripsi_aktifitas = $this->input->post('deskripsi_aktifitas');
				# receiver data and index
				$data['company_id'] = $this->company_id;
				$data['paket_name'] = $this->input->post('nama_paket'); // nama paket
				$data['jenis_kegiatan'] = $this->input->post('jenis_kegiatan'); // jenis kegiatan
				$data['slug'] = $this->text_ops->checkSlugPaket($this->text_ops->createSlug($this->input->post('nama_paket'))); // create slug paket name
				$data['description'] = $this->input->post('deskripsi_paket');
				$data['departure_date'] = $this->input->post('tgl_keberangkatan');

				$data['provider_id'] = $this->input->post('provider_visa');
				$data['asuransi_id'] = $this->input->post('asuransi');
				$data['no_polis'] = $this->input->post('nopolis');
				$data['tgl_input_polis'] = $this->input->post('tgl_input_polis');
				$data['tgl_awal_polis'] = $this->input->post('tgl_awal_polis');
				$data['tgl_akhir_polis'] = $this->input->post('tgl_akhir_polis');

				$data['return_date'] = $this->input->post('tgl_kepulangan');
				$data['departure_from'] = $this->input->post('berangkat_dari');
				$data['duration_trip'] = round(($return_date - $departure_date) / (60 * 60 * 24));
				$data['mahram_fee'] = $this->text_ops->hide_currency($this->input->post('biaya_mahram'));
				$data['show_homepage'] = $this->input->post('show') == '1' ? 'tampilkan' : 'sembunyikan';
				$data['jamaah_quota'] = $this->input->post('quota');
				$data['city_visited'] = empty($this->input->post('kota')) ? '' : serialize($this->input->post('kota'));
				$data['facilities'] = empty($this->input->post('fasilitas')) ? '' : serialize($this->input->post('fasilitas'));
				$data['airlines'] = empty($this->input->post('airlines')) ? '' : serialize($this->input->post('airlines'));
				$data['hotel'] = empty($this->input->post('hotel')) ? '' : serialize($this->input->post('hotel'));
				$data['departure_time'] = $this->input->post('waktu_keberangkatan');
				$data['time_arrival'] = $this->input->post('waktu_sampai');
				$data['last_update'] = date('Y-m-d H:i:s');
				# data muthawif
				$data_muthawif = array();
				foreach ($this->input->post('muthawif') as $key => $value) {
					$data_muthawif[]  = array(
						'company_id' => $this->company_id,
						'muthawif_id' => $value
					);
				}
				# data itinerary
				$data_itinerary = array();
				foreach ($this->input->post('tanggal_aktifitas') as $key => $value) {
					$data_itinerary[] = array(
						'company_id' => $this->company_id,
						'activity_date' => $value,
						'activity_title' => $judul_aktifitas[$key],
						'description' => $deskripsi_aktifitas[$key],
						'input_date' => date('Y-m-d H:i:s'),
						'last_update' => date('Y-m-d H:i:s')
					);
				}
				# data paket price
				$data_paket_price = array();
				// echo "+++++++++<br>";
				// print_r( $paket_type_price );
				// echo "+++++++++<br>";
				foreach ($tipe_paket as $key => $value) {
					// echo "======<br>";
					// echo $key."<br>";
					// echo $value."<br>";
					// echo "======<br>";
					$data_paket_price[] = array(
						'company_id' => $this->company_id,
						'paket_type_id' => $value,
						'price' => $this->text_ops->hide_currency($paket_type_price[$key]),
						'input_date' => date('Y-m-d H:i:s'),
						'last_update' => date('Y-m-d H:i:s')
					);
				}
				# filter
				if ($this->input->post('id')) {
					if (!$this->model_daftar_paket_cud->update_paket($this->input->post('id'), $data, $data_muthawif, $data_itinerary, $data_paket_price)) {
						$error = 1;
						$error_msg = 'Proses update data paket gagal dilakukan';
					}
				} else {
					$data['kode'] = $this->input->post('kode_paket');
					$data['input_date'] = date('Y-m-d H:i:s');
					if (!$this->model_daftar_paket_cud->insert_paket($data, $data_muthawif, $data_itinerary, $data_paket_price)) {
						$error = 1;
						$error_msg = 'Proses insert data paket gagal dilakukan.';
					}
				}
			}
			// filter
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data paket berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error' => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	# delete paket
	function delete_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Paket<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_paket_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# filter feedBack
			if ($this->model_daftar_paket_cud->delete_paket($this->input->post('id'))) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data paket berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data paket gagal dihapus',
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

	function get_info_edit_paket()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Paket<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_paket_id_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get kode paket
			$kode = $this->random_code_ops->gen_kode_paket();
			# paket type
			$paket_type = $this->model_daftar_paket->get_paket_type();
			# fisilitas
			$fasilitas = $this->model_daftar_paket->get_fasilitas_paket();
			# kota kunjungan
			$kota_kunjungan = $this->model_daftar_paket->get_kota();
			# airlines
			$airlines = $this->model_daftar_paket->get_airLines();
			# hotel
			$hotel = $this->model_daftar_paket->get_hotel();
			# muthawif
			$muthawif = $this->model_daftar_paket->get_muthawif();
			# get beranda
			$bandara = $this->model_daftar_paket->get_bandara();
			# provider
			$provider_visa = $this->model_daftar_paket->get_provider_visa();
			# get asuransi
			$asuransi = $this->model_daftar_paket->get_asuransi();
			# get value paket
			$value = $this->model_daftar_paket->get_info_edit_paket($this->input->post('id'));
			# filter feedBack
			if (count($value) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data paket berhasil dihapus.',
					'value' => $value,
					'data' => array(
						'paket_type' => $paket_type,
						'fasilitas' => $fasilitas,
						'kota_kunjungan' => $kota_kunjungan,
						'airlines' => $airlines,
						'hotel' => $hotel,
						'muthawif' => $muthawif,
						'bandara' => $bandara,
						'kode' => $kode,
						'asuransi' => $asuransi,
						'provider_visa' => $provider_visa
					),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data paket gagal dihapus',
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
