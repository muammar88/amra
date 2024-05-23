<?php

/**
 *  -----------------------
 *	Daftar kota Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_kota extends CI_Controller
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
      $this->load->model('Model_daftar_kota', 'model_daftar_kota');
      # model fasilitas cud
      $this->load->model('Model_daftar_kota_cud', 'model_daftar_kota_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   # daftar kota server side
   function daftar_kotas()
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
         $total    = $this->model_daftar_kota->get_total_daftar_kota($search);
         $list    = $this->model_daftar_kota->get_index_daftar_kota($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar kota tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar kota berhasil ditemukan.',
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

   function _ck_id_city_exist()
   {
      if ($this->input->post('id')) {
         if ($this->model_daftar_kota->check_id_city_exist($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_id_city_exist', 'ID kota tidak ditemukan.');
            return FALSE;
         }
      } else {
         return TRUE;
      }
   }

   function proses_addupdate_daftar_kota()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Kota<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_city_exist');
      $this->form_validation->set_rules('nama_kota', '<b>Nama Kota<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('kode_kota', '<b>Kode kota<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['city_name'] = $this->input->post('nama_kota');
         $data['city_code'] = $this->input->post('kode_kota');
         $data['last_update'] = date('Y-m-d H:i:s');
         // filter proses
         if ($this->input->post('id')) {
            if (!$this->model_daftar_kota_cud->update_city($this->input->post('id'), $data)) {
               $error = 1;
               $error_msg = 'Data kota gagal diperharui';
            }
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d H:i:s');
            if (!$this->model_daftar_kota_cud->insert_city($data)) {
               $error = 1;
               $error_msg = 'Data kota gagal disimpan';
            }
         }
         # filter feedBack
         if ($error == 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data kota berhasil disimpan.',
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

   function get_edit_info_city()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Kota<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_city_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get info city
         $feedBack = $this->model_daftar_kota->get_info_city($this->input->post('id'));
         # filter feedBack
         if (count($feedBack) > 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data kota berhasil ditemukan.',
               'data' => $feedBack,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data kota tidak ditemukan.',
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


   function _ck_id_city_for_delete_process($id)
   {
      if ($this->model_daftar_kota->check_id_city_exist($id)) {
         if ($this->model_daftar_kota->check_city_was_use($id)) {
            $this->form_validation->set_message('_ck_id_city_for_delete_process', 'ID kota tidak dapat dihapus, karena id kota masih digunakan pada paket lain');
            return FALSE;
         } else {
            return TRUE;
         }
      } else {
         $this->form_validation->set_message('_ck_id_city_for_delete_process', 'ID kota  tidak ditemukan.');
         return FALSE;
      }
   }


   function delete_city()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Kota<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_city_for_delete_process');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # filter feedBack
         if ($this->model_daftar_kota_cud->delete_city($this->input->post('id'))) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data kota berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data kota tidak dihapus.',
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
