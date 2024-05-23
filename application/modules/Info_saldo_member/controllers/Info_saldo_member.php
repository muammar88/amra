<?php

/**
 *  -----------------------
 *	Info Saldo Member Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Info_saldo_member extends CI_Controller
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
      $this->load->model('Model_info_saldo_member', 'model_info_saldo');
      # model fasilitas cud
      // $this->load->model('Model_fasilitas_cud', 'model_fasilitas_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   // daftar member
   function daftar_member(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage', '<b>Perpage<b>',  'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>',  'trim|xss_clean|min_length[1]|numeric');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $search  = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }

         $total   = $this->model_info_saldo->get_total_daftar_member($search);
         $list    = $this->model_info_saldo->get_index_daftar_member($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Daftar member tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Daftar member berhasil ditemukan.',
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

}