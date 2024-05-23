<?php
/**
*  -----------------------
*	Daftar agen Controller
*	Created by Muammar Kadafi
*  -----------------------
*/

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_agen extends CI_Controller
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
		# model daftar bank
		$this->load->model('Model_daftar_agen', 'model_daftar_agen');
		# model daftar bank cud
		$this->load->model('Model_daftar_agen_cud', 'model_daftar_agen_cud');
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


   function daftar_agens(){
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
         if( $this->input->post('pageNumber') ) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total 	= $this->model_daftar_agen->get_total_daftar_agen($search);
         $list 	= $this->model_daftar_agen->get_index_daftar_agen($perpage, $start_at, $search);
         if ( $total == 0 ) {
            $return = array(
               'error'	=> true,
               'error_msg' => 'Daftar agen tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Daftar agen berhasil ditemukan.',
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

   function _ck_id_agen_exist($id){
      if( $this->model_daftar_agen->check_id_agen_exist($id) ){
         return TRUE;
      }else{
         $this->form_validation->set_message('_ck_id_agen_exist', 'ID Agen tidak ditemukan.');
         return FALSE;
      }
   }

   function delete_agen(){
      $return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>Id Agen<b>','trim|required|xss_clean|numeric|min_length[1]|callback__ck_otoritas|callback__ck_id_agen_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {

         $personal_id = $this->model_daftar_agen->get_personal_id_agen( $this->input->post('id') );

			if ( $this->model_daftar_agen_cud->delete_agen($this->input->post('id'), $personal_id) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data agen berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data agen gagal dihapus.',
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

   function upgrade_level_agen(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id','<b>Id Agen<b>','trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_agen_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # filter level agen
         if( ! $this->model_daftar_agen->check_level_agen( $this->input->post('id')) ){
            $error = 1;
            $error_msg = 'Level agen tidak dapat ditingkatkan lagi..';
         }
         # upgrade process
         if( $error == 0 ){
            if( ! $this->model_daftar_agen_cud->upgrade_level_agen( $this->input->post('id') ) ){
               $error = 1;
               $error_msg = 'Upgrade level agen ke level cabang gagal dilakukan';
            }
         }
         # filter
         if ( $error == 0  ) {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Level agen berhasil diupgrade.',
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

	function level_agen(){
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
         if( $this->input->post('pageNumber') ) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total 	= $this->model_daftar_agen->get_total_level_agen($search);
         $list 	= $this->model_daftar_agen->get_index_level_agen($perpage, $start_at, $search);
         if ( $total == 0 ) {
            $return = array(
               'error'	=> true,
               'error_msg' => 'Daftar level agen tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Daftar level agen berhasil ditemukan.',
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

	# check id level agen is exist
	function _ck_id_level_agen_exist(){
		if( $this->input->post('id') ) {
			if( ! $this->model_daftar_agen->check_id_level_keagenan( $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_id_level_agen_exist', 'ID Level Keagenan tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	# check level keagenan is exist
	function _ck_level_keagenan_is_exist($level){
		if( $this->input->post('id') ) {
			if( $this->model_daftar_agen->check_level_keagenan_is_exist($level, $this->input->post('id'))){
				$this->form_validation->set_message('_ck_level_keagenan_is_exist', 'Level ini sudah tersedia.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			if( $this->model_daftar_agen->check_level_keagenan_is_exist($level)){
				$this->form_validation->set_message('_ck_level_keagenan_is_exist', 'Level ini sudah tersedia.');
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}

	function proses_addupdate_level_keagenan(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Level Keagenan<b>','trim|xss_clean|numeric|min_length[1]|callback__ck_id_level_agen_exist');
		$this->form_validation->set_rules('nama_level_keagenan','<b>Id Agen<b>','trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('level_keagenan','<b>Level Keagenan<b>','trim|required|xss_clean|numeric|min_length[1]|callback__ck_level_keagenan_is_exist');
		$this->form_validation->set_rules('default_fee_keagenan','<b>Default Fee Agen<b>','trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$data = array();
			$data['nama'] = $this->input->post('nama_level_keagenan');
			$data['level'] = $this->input->post('level_keagenan');
			$data['default_fee'] = $this->text_ops->hide_currency($this->input->post('default_fee_keagenan'));
			$data['last_update'] = date('Y-m-d');
			if( $this->input->post('id')) {
				// update process
				if( ! $this->model_daftar_agen_cud->update_level_keagenan($this->input->post('id'), $data)){
					$error = 1;
					$error_msg = 'Proses update level keagenan gagal dilakukan.';
				}
			}else{
				$data['company_id'] = $this->company_id;
				$data['input_date'] = date('Y-m-d');
				// insert process
				if( ! $this->model_daftar_agen_cud->insert_level_keagenan($data) ){
					$error = 1;
					$error_msg = 'Proses insert level gagal dilakukan.';
				}
			}
			# filter
			if ( $error == 0  ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data level keagenan berhasil di'.($this->input->post('id') ? 'Perbaharui' : 'Tambahkan'),
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

	function info_level_keagenan(){
		# get level keagenan
		$level_keagenan = $this->model_daftar_agen->get_level_keagenan();
		# filter
		if ($level_keagenan == '') {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Info level keagenan gagal ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Info level keagenan berhasil ditemukan.',
				'data' => $level_keagenan,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	#  edit level keagenan
	function edit_level_keagenan() {
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Level Keagenan<b>','trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_level_agen_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get level keagenan
			$level_keagenan = $this->model_daftar_agen->get_level_keagenan();
			# get value
			$value = $this->model_daftar_agen->get_value_level_keagenan($this->input->post('id'));
			# filter
			if ( $error == 0  ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'data berhasil ditemukan',
					'data' => $level_keagenan,
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


	function _ck_delete_level_agen_exist($id){
		if( ! $this->model_daftar_agen->check_id_level_keagenan( $id ) ) {
			$this->form_validation->set_message('_ck_delete_level_agen_exist', 'ID Level Keagenan tidak ditemukan.');
			return FALSE;
		}else{
			if( $this->model_daftar_agen->check_id_level_keagenan_is_use($id) ) {
				$this->form_validation->set_message('_ck_delete_level_agen_exist', 'ID Level keagenan ini tidak dapat dihapus, karena masih digunakan oleh beberapa agen.');
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}

	# delete level keagenan
	function delete_level_keagenan(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id','<b>ID Level Keagenan<b>','trim|required|xss_clean|numeric|min_length[1]|callback__ck_otoritas|callback__ck_delete_level_agen_exist');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# get id
			$id = $this->input->post('id');
			# filter
			if ( $this->model_daftar_agen_cud->delete_level_keagenan( $id ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'data level keagenan berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'data level keagenan gagal dihapus.',
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


	# download excel
	function download_excel_daftar_agen()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		# set session
		$this->session->set_userdata(array('download_to_excel' => array(
			'type' => 'download_excel_daftar_agen',
		)));
		if (!$this->session->userdata('download_to_excel')) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Daftar data buku besar tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Daftar data buku besar berhasil ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

}

// 	$this->form_validation->set_rules('akun',	'<b>Akun<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_akun_id_exist');
// 	$this->form_validation->set_rules('periode',	'<b>Periode<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_periode_exist');
// 	/*
// 	  Validation process
  // */
// 	if ($this->form_validation->run()) {
		
// 	} else {
// 		if (validation_errors()) {
// 			# define return error
// 			$return = array(
// 				'error'         => true,
// 				'error_msg'    => validation_errors(),
// 				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
// 			);
// 		}
// 	}
