<?php

/**
 *  -----------------------
 *	Request Keageanan Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Request_keagenan extends CI_Controller
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
      $this->load->model('Model_request_keagenan', 'model_request_keagenan');
      $this->load->model('Model_request_keagenan_cud', 'model_request_keagenan_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function daftar_request_keagenan()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]|in_list[pilih_semua,disetujui,ditolak,diproses]');
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
         $total = $this->model_request_keagenan->get_total_request_keagenan($search);
         $list = $this->model_request_keagenan->get_index_request_keagenan($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar request keagenan tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar request keagenan berhasil ditemukan.',
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

   function _ck_id_request($id)
   {
      if ($this->model_request_keagenan->check_id_request_exist($id)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_id_request', 'ID Request tidak ditemukan.');
         return FALSE;
      }
   }

   function approve()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id',   '<b>ID Request<b>',    'trim|required|xss_clean|min_length[1]|callback__ck_id_request');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $data = array();
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $data['approver'] = "Administrator";
         } else {
            $data['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $data['status_request'] = 'disetujui';
         $data['last_update'] = date('Y-m-d H:i:s');
         $upline = 0;
         $member_id = 0;
         $info_request_keagenan = $this->model_request_keagenan->get_upline($this->input->post('id'));
         if (count($info_request_keagenan) > 0) {
            $upline = $info_request_keagenan['upline'];
            $member_id = $info_request_keagenan['member_id'];
         }

         # data agen
         $data_agen = array();
         $data_agen['company_id'] = $this->company_id;
         $data_agen['personal_id'] = $member_id;
         $data_agen['level_agen'] = $this->company_id;
         $data_agen['upline'] = $upline;
         $data_agen['input_date'] = date('Y-m-d H:i:s');
         $data_agen['last_update'] = date('Y-m-d H:i:s');

         if (!$this->model_request_keagenan_cud->approve($this->input->post('id'), $data, $data_agen)) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Request keagenan gagal disejutui.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Request keagenan berhasil disetujui.',
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

   function proses_decline_request_keagenan()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Request<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request');
      $this->form_validation->set_rules('note', '<b>Catatan Request Keagenan<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         # update agen request
         $data = array();
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $data['approver'] = "Administrator";
         } else {
            $data['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }

         $data['status_request'] = 'ditolak';
         $data['status_note'] = $this->input->post('note');
         $data['last_update'] = date('Y-m-d H:i:s');

         # get member id for delete agen
         $agen_member_id = $this->model_request_keagenan->get_agen_member_id($this->input->post('id'));
         # get upline and member id
         // $upline = 0;
         $member_id = 0;
         $info_request_keagenan = $this->model_request_keagenan->get_upline($this->input->post('id'));
         if (count($info_request_keagenan) > 0) {
            // $upline = $info_request_keagenan['upline'];
            $member_id = $info_request_keagenan['member_id'];
         }

         if (!$this->model_request_keagenan_cud->decline($this->input->post('id'), $agen_member_id, $data, $member_id)) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Request keagenan gagal disejutui.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Request keagenan berhasil disetujui.',
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

   function info_decline()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Request<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_request');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         if ($this->model_request_keagenan->check_agen_is_active($this->input->post('id'))) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Member sudah terdaftar sebagai agen. Jika anda melanjutkan proses penolakan request, maka semua transaksi agen juga akan ikut terhapus. Apakah anda ingin melanjutkan proses ini?.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Member belum terdaftar sebagai agen.',
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
