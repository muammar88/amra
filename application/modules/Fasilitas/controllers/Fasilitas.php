<?php

/**
 *  -----------------------
 *	Fasilitas Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Fasilitas extends CI_Controller
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
      $this->load->model('Model_fasilitas', 'model_fasilitas');
      # model fasilitas cud
      $this->load->model('Model_fasilitas_cud', 'model_fasilitas_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   # daftar fasilitas
   function daftar_fasilitas()
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
         $total    = $this->model_fasilitas->get_total_daftar_fasilitas($search);
         $list    = $this->model_fasilitas->get_index_daftar_fasilitas($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar fasilitas tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar fasilitas berhasil ditemukan.',
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

   function _ck_id_fasilitas_exist()
   {
      if ($this->input->post('id')) {
         if ($this->model_fasilitas->check_id_fasilitas_exist($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_id_fasilitas_exist', 'ID Fasilitas tidak ditemukan.');
            return FALSE;
         }
      }
   }

   function proses_addupdate_fasilitas()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Fasilitas<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_exist');
      $this->form_validation->set_rules('nama_fasilitas', '<b>Nama Fasilitas<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['facilities_name'] = $this->input->post('nama_fasilitas');
         $data['last_update'] = date('Y-m-d H:i:s');
         #s filter add update process
         if ($this->input->post('id')) {
            $feedBack = $this->model_fasilitas_cud->update_fasilitas($this->input->post('id'), $data);
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d H:i:s');
            $feedBack = $this->model_fasilitas_cud->insert_fasilitas($data);
         }
         # filter feedBack
         if ($feedBack) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data fasilitas berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data fasilitas gagal disimpan.',
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

   function get_info_edit_fasilitas()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Fasilitas<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $feedBack = $this->model_fasilitas->get_info_edit_fasilitas($this->input->post('id'));

         if (count($feedBack) > 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data fasilitas berhasil ditemukan.',
               'data' => $feedBack,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data fasilitas gagal ditemukan.',
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

   function delete_fasilitas()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Fasilitas<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         if ($this->model_fasilitas_cud->delete_fasilitas($this->input->post('id'))) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data fasilitas berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data fasilitas gagal dihapus.',
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
