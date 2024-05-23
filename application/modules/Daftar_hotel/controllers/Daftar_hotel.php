<?php

/**
 *  -----------------------
 *	Daftar hotel Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_hotel extends CI_Controller
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
      $this->load->model('Model_daftar_hotel', 'model_daftar_hotel');
      # model daftar mobil cud
      $this->load->model('Model_daftar_hotel_cud', 'model_daftar_hotel_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function daftar_hotels()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage',   '<b>Perpage<b>',    'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber',   '<b>pageNumber<b>',    'trim|xss_clean|min_length[1]|numeric');
      /*
			Validation process
		*/
      if ($this->form_validation->run()) {
         $search    = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total    = $this->model_daftar_hotel->get_total_daftar_hotel($search);
         $list    = $this->model_daftar_hotel->get_index_daftar_hotel($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar hotel tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar hotel berhasil ditemukan.',
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

   function get_info_hotel()
   {
      $error = 0;
      # get list city
      $city = $this->model_daftar_hotel->get_list_city();
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
            'city' => $city,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   function _ck_id_hotel_exist()
   {
      if ($this->input->post('id')) {
         if ($this->model_daftar_hotel->check_hotel_id_exist($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_id_hotel_exist', 'ID hotel tidak ditemukan.sss');
            return FALSE;
         }
      } else {
         return TRUE;
      }
   }

   function _ck_city_exist($id)
   {
      if ($this->model_daftar_hotel->check_city_id_exist($id)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_city_exist', 'ID kota tidak ditemukan.');
         return FALSE;
      }
   }

   # proses addupdate daftar hotel
   function proses_addupdate_daftar_hotel()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Hotel<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_hotel_exist');
      $this->form_validation->set_rules('nama_hotel', '<b>Nama Hotel<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('kota', '<b>Nama Kota<b>', 'trim|numeric|required|xss_clean|min_length[1]|callback__ck_city_exist');
      $this->form_validation->set_rules('bintang_hotel', '<b>Bintang Hotel<b>', 'trim|required|numeric|xss_clean|min_length[1]|in_list[1,2,3,4,5,6,7]');
      $this->form_validation->set_rules('description_hotel', '<b>Deskripsi Hotel<b>', 'trim|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['hotel_name'] = $this->input->post('nama_hotel');
         $data['description'] = $this->input->post('description_hotel');
         $data['star_hotel'] = $this->input->post('bintang_hotel');
         $data['city_id'] = $this->input->post('kota');
         $data['last_update'] = date('Y-m-d H:i:s');
         # filter proses
         if ($this->input->post('id')) {
            if (!$this->model_daftar_hotel_cud->update_hotel($this->input->post('id'), $data)) {
               $error = 1;
               $error_msg = 'Data hotel gagal diperharui';
            }
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d');
            if (!$this->model_daftar_hotel_cud->insert_hotel($data)) {
               $error = 1;
               $error_msg = 'Data hotel gagal disimpan';
            }
         }
         # filter feedBack
         if ($error == 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data hotel berhasil disimpan.',
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

   # get info edit hotel
   function get_info_edit_hotel()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Hotel<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_hotel_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # receive data
         $city = $this->model_daftar_hotel->get_list_city();
         # get data
         $feedBack = $this->model_daftar_hotel->get_info_hotel($this->input->post('id'));
         # filter feedBack
         if (count($feedBack)) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data hotel berhasil disimpan.',
               'city' => $city,
               'value' => $feedBack,
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

   function delete_hotel()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Hotel<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_hotel_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # filter feedBack
         if ($this->model_daftar_hotel_cud->delete_hotel($this->input->post('id'))) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data hotel berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data hotel gagal dihapus.',
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
