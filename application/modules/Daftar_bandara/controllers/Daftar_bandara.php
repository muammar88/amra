<?php

/**
 *  -----------------------
 *	Daftar bandara Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_bandara extends CI_Controller
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
		$this->load->model('Model_daftar_bandara', 'model_daftar_bandara');
		# model daftar bank cud
		$this->load->model('Model_daftar_bandara_cud', 'model_daftar_bandara_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

   // daftar bandaras
   function daftar_bandaras(){
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
         $total 	= $this->model_daftar_bandara->get_total_daftar_bandara($search);
         $list 	= $this->model_daftar_bandara->get_index_daftar_bandara($perpage, $start_at, $search);
         if ( $total == 0 ) {
            $return = array(
               'error'	=> true,
               'error_msg' => 'Daftar bandara tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Daftar bandara berhasil ditemukan.',
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

	# add update bandara
	function get_info_addupdate_bandara(){
		$error = 0;
		# get list city
		$city = $this->model_daftar_bandara->get_list_city();
		# filter
		if (count($city) == 0) {
			$return = array(
				'error'   => true,
				'error_msg' => 'Data kota tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'   => false,
				'error_msg' => 'Data kota berhasil ditemukan.',
				'data' => $city,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_city_id_exist( $city_id ) {
		if( ! $this->model_daftar_bandara->check_city_id_exist( $city_id ) ){
			$this->form_validation->set_message('_ck_city_id_exist', 'ID Kota tidak ditemukan.');
			return  FALSE;
		}else{
			return TRUE;
		}
	}

	function _ck_id_bandara_exist(){
		if( $this->input->post('id') ) {
			if( ! $this->model_daftar_bandara->check_bandara_id_exist( $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_id_bandara_exist', 'ID Bandara tidak ditemukan.');
				return  FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	# proses add update bandara
	function proses_addupdate_bandara(){
		$return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Bandara<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_bandara_exist');
      $this->form_validation->set_rules('nama_bandara', '<b>Nama Bandara<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('city', '<b>Nama Kota<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_city_id_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
			$data['company_id'] = $this->company_id;
			$data['airport_name'] = $this->input->post('nama_bandara');
			$data['city_id'] = $this->input->post('city');
			# filter
			if( $this->input->post('id') ) {
				# update
				if( ! $this->model_daftar_bandara_cud->update_bandara( $this->input->post('id'), $data ) ) {
					$error = 1;
					$error_msg = 'Proses update gagal dilakukan.';
				}
			}else{
				# insert
				if( ! $this->model_daftar_bandara_cud->insert_bandara( $data ) ) {
					$error = 1;
					$error_msg = 'Proses insert gagal dilakukan.';
				}
			}
         # filter feedBack
         if ($error == 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data bandara berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
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

	# get info edit bandara
	function get_info_edit_bandara() {
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bandara<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_bandara_exist');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# get list city
			$city = $this->model_daftar_bandara->get_list_city();
			# receive data
			$value = $this->model_daftar_bandara->get_value( $this->input->post('id') );
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'   => false,
					'error_msg' => 'Data edit bandara berhasil disimpan.',
					'data' => $city,
					'value' => $value,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'   => true,
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

	# delete daftar bandara
	function delete_daftar_bandara(){
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Bandara<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_bandara_exist');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			# delete bandara
			if( ! $this->model_daftar_bandara_cud->delete_bandara( $this->input->post('id') ) ){
				$error = 1;
				$error_msg = 'Data bandara gagal dihapus.';
			}
			# filter feedBack
			if ( ! $this->model_daftar_bandara_cud->delete_bandara( $this->input->post('id') ) ) {
				$return = array(
					'error'   => true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'   => false,
					'error_msg' => 'Data bandara berhasil dihapus.',
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
