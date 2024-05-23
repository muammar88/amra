<?php

/**
 *  -----------------------
 *	Daftar tipe paket Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_tipe_paket extends CI_Controller
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
      $this->load->model('Model_daftar_tipe_paket', 'model_daftar_tipe_paket');
      # model daftar bank cud
      $this->load->model('Model_daftar_tipe_paket_cud', 'model_daftar_tipe_paket_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function daftar_tipe_pakets()
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
         $total    = $this->model_daftar_tipe_paket->get_total_daftar_tipe_paket($search);
         $list    = $this->model_daftar_tipe_paket->get_index_daftar_tipe_paket($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar tipe paket tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar tipe paket berhasil ditemukan.',
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

   function _ck_id_tipe_paket_exist()
   {
      if ($this->input->post('id')) {
         if ($this->model_daftar_tipe_paket->check_id_tipe_paket_exist($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_id_tipe_paket_exist', 'ID Tipe Paket tidak ditemukan.');
            return FALSE;
         }
      } else {
         return TRUE;
      }
   }

   function proses_addupdate_tipe_paket()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Tipe Paket<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_tipe_paket_exist');
      $this->form_validation->set_rules('nama_tipe_paket', '<b>Nama Tipe Paket<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['paket_type_name'] = $this->input->post('nama_tipe_paket');
         $data['last_update'] = date('Y-m-d H:i:s');
         #s filter add update process
         if ($this->input->post('id')) {
            $feedBack = $this->model_daftar_tipe_paket_cud->update_tipe_paket($this->input->post('id'), $data);
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d H:i:s');
            $feedBack = $this->model_daftar_tipe_paket_cud->insert_tipe_paket($data);
         }
         # filter feedBack
         if ($feedBack) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data tipe paket berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data tipe paket gagal disimpan.',
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

   function get_info_addupdate_tipe_paket()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Tipe Paket<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_tipe_paket_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $feedBack = $this->model_daftar_tipe_paket->get_info_edit_tipe_paket($this->input->post('id'));
         if (count($feedBack) > 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data tipe paket berhasil ditemukan.',
               'data' => $feedBack,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data tipe paket gagal ditemukan.',
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

   function delete_tipe_paket()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Tipe Paket<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_tipe_paket_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         if ($this->model_daftar_tipe_paket_cud->delete_tipe_paket($this->input->post('id'))) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data tipe paket berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data tipe paket gagal dihapus.',
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
