<?php

/**
 *  -----------------------
 *	Fasilitas LA Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Fasilitas_la extends CI_Controller
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
      $this->load->model('Model_fasilitas_la', 'model_fasilitas_la');
      # model fasilitas cud
      $this->load->model('Model_fasilitas_la_cud', 'model_fasilitas_la_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function daftar_fasilitas_la()
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
         $total    = $this->model_fasilitas_la->get_total_daftar_fasilitas_la($search);
         $list    = $this->model_fasilitas_la->get_index_daftar_fasilitas_la($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar fasilitas la tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar fasilitas la berhasil ditemukan.',
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

   function get_info_fasilitas_la()
   {
      $error = 0;
      # get list header
      $header = $this->model_fasilitas_la->get_list_header();
      if ($error != 0) {
         $return = array(
            'error'   => true,
            'error_msg' => 'Data airlines tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'   => false,
            'error_msg' => 'Data airlines berhasil ditemukan.',
            'header' => $header,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   function _ck_id_fasilitas_la_exist()
   {
      if ($this->input->post('id')) {
         if ($this->model_fasilitas_la->check_id_fasilitas_la_exist($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_id_fasilitas_la_exist', 'ID Fasilitas LA tidak ditemukan.');
            return FALSE;
         }
      } else {
         return TRUE;
      }
   }

   function _ck_id_header_exist($header)
   {
      if ($this->model_fasilitas_la->check_id_header_exist($header)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_id_header_exist', 'ID Header Fasilitas LA tidak ditemukan.');
         return FALSE;
      }
   }

   function proses_addupdate_fasilitas_la()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Fasilitas LA<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_la_exist');
      $this->form_validation->set_rules('header', '<b>Nama Header<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_header_exist');
      $this->form_validation->set_rules('nama_fasilitas_la', '<b>Nama Fasilitas LA<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('harga_fasilitas_la', '<b>Harga Fasilitas LA<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['header_id'] = $this->input->post('header');
         $data['facilities_name'] = $this->input->post('nama_fasilitas_la');
         $data['price'] = $this->text_ops->hide_currency($this->input->post('harga_fasilitas_la'));
         $data['last_update'] = date('Y-m-d');
         // filter
         if ($this->input->post('id')) {
            $feedBack = $this->model_fasilitas_la_cud->update_fasilitas_la($this->input->post('id'), $data);
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d');
            $feedBack = $this->model_fasilitas_la_cud->insert_fasilitas_la($data);
         }
         # filter feedBack
         if ($feedBack) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data fasilitas la berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data fasilitas la gagal disimpan.',
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

   function get_info_edit_fasilitas_la()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Fasilitas LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_la_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $header = $this->model_fasilitas_la->get_list_header();

         $feedBack = $this->model_fasilitas_la->get_info_edit_fasilitas_la($this->input->post('id'));

         if (count($feedBack) > 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data fasilitas la berhasil ditemukan.',
               'data' => $feedBack,
               'header' => $header,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data fasilitas la gagal ditemukan.',
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

   function delete_fasilitas_la()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Fasilitas LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_fasilitas_la_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         if ($this->model_fasilitas_la_cud->delete_fasilitas_la($this->input->post('id'))) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data fasilitas la berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data fasilitas la gagal dihapus.',
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
