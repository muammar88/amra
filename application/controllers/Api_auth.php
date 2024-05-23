<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Api_auth extends REST_Controller  {

	public function __construct(){
		parent::__construct();
		//$this->auth_library->checkApiLogin();
		$this->systems->Hash();
	}

	function index(){
		$error 		= 0;
		$error_mess = '';
		$return 	= array();
		$this->form_validation->set_rules('token',		'<b>Token<b>',		'trim|required|xss_clean|min_length[1]|in_list[123TOKENTES]');
		$this->form_validation->set_rules('username',	'<b>Username<b>',	'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('password',	'<b>Password<b>',	'trim|required|xss_clean|min_length[1]');
		/* 
			Validation process
		*/
		if ( $this->form_validation->run() ) 
		{

			// $this->db->select('s.santri_id, s.nama, s.nis')
			// 		 ->from('santri_kelas AS sk')
			// 		 ->join('santri AS s', 'sk.santri_id=s.santri_id', 'inner')
			// 		 ->where('sk.kelas_id', $this->input->post('kelas_id'));
			// $q = $this->db->get();
			// $list_santri = array();
			// if( $q->num_rows() > 0 )
			// {
			// 	foreach ($q->result() as $rows)
			// 	{
			// 		$list_santri[$rows->santri_id] = $rows->nama.' ( NIS => '.$rows->nis.')';
			// 	}
			// }else{
			// 	$error = 1;
			// 	$error_mess = ' Santri Tidak Ditemukan. ';
			// }
			$user = array();
			$this->db->select('username')
					 ->from('base_users')
					 ->where('username', $this->input->post('username') )
					 ->where('password', md5( $this->input->post('password').'_'.$this->session->Hash ) );
			$q = $this->db->get();
			if( $q->num_rows() > 0)
			{
				foreach ($q->result() as $row) 
				{
					$user = $row->username;
				}
				$error_mess = 'Login Berhasil';
			}else
			{
				$error 		= 1;
				$error_mess = 'ID Atau Username Admin Tidak Terdaftar Di Pangkalan Data';
			}

			if( $error == 1 )
			{
				$return = array( 'error' 		=> true,
								 'error_msg'	=> $error_mess);
				

			}else
			{
				$return = array( 'error' 		=> false,
								 'error_msg'	=> $error_mess,
								 'user' 		=> $user);

			}
		}else
		{
			if( validation_errors() )
			{
	 			// define return error
	            $return = array('error'         => true,
	                            'error_msg'    => validation_errors() );
	        }
		}

		$this->response($return, 200);
		// echo json_encode( $return );	

		// $array = array();
		// $array[] = array('nama_kelas' => "Kelas A", "jumlah" => "20", "mata_pelajaran" => "TAUHID", "jadwal" => "Dhuha", "ustad" => "ustad Maulana");
		// $array[] = array('nama_kelas' => "Kelas C", "jumlah" => "10", "mata_pelajaran" => "BAHASA ARAB", "jadwal" => "Ashar", "ustad" => "ustad Ilham");
		// $array[] = array('nama_kelas' => "Kelas B", "jumlah" => "22", "mata_pelajaran" => "SHARAF", "jadwal" => "Magrib", "ustad" => "ustad Mirwan");
		// $array[] = array('nama_kelas' => "Kelas B", "jumlah" => "22", "mata_pelajaran" => "ULUMUL QUR'AN", "jadwal" => "Subuh", "ustad" => "ustad Harun");
		// // $array[] = array('nama_kelas' => "Kelas D", "jumlah" => "15", "mata_pelajaran" => "Ianuttalibin");
		// // // $array[] = array('nama_kelas' => "Kelas F", "jumlah" => "13", "mata_pelajaran" => "Nahwu");
		// // // $array[] = array('nama_kelas' => "Kelas G", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas Z", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas R", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas T", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas H", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas Q", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas K", "jumlah" => "25", "mata_pelajaran" => "Fiqh");
		// // // $array[] = array('nama_kelas' => "Kelas L", "jumlah" => "25", "mata_pelajaran" => "Fiqh");

		// echo json_encode(array('error' => false,
		// 						'error_message' => "Berhasil Ditemukan",
		// 						'data' => $array));

	}

	// function detailAbsensi(){
	// 	// $array[] = array('nama_santri' => "Muammar Kadafi", "tanggal_lahir" => "1990-01-01");
	// 	// $array[] = array('nama_santri' => "Muammar Kadafi", "tanggal_lahir" => "1990-01-01");
	// 	// $array[] = array('nama_santri' => "Muammar Kadafi", "tanggal_lahir" => "1990-01-01");
	// 	$array[] = array('nama_santri' => "Muammar Kadafi", "tanggal_lahir" => "1990-01-01");
	// 	$array[] = array('nama_santri' => "Muammar Kadafi", "tanggal_lahir" => "1990-01-01");
		

	// 	echo json_encode(array('error' => false,
	// 							'error_message' => "Berhasil Ditemukan",
	// 							'data' => $array));
	// }


}