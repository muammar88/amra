<?php

/**
 *  -----------------------
 *	Kostumer Paket LA Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Kostumer_paket_la extends CI_Controller
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
      $this->load->model('Model_kostumer_paket_la', 'model_kostumer_paket_la');
      # model fasilitas cud
      $this->load->model('Model_kostumer_paket_la_cud', 'model_kostumer_paket_la_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }


   // server daftar komplain
   function server_side(){
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage',   '<b>Perpage<b>',    'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber',   '<b>pageNumber<b>',    'trim|xss_clean|min_length[1]|numeric');
      // $this->form_validation->set_rules('status',   '<b>Status Komplain<b>', 'trim|required|xss_clean|min_length[1]|in_list[all,proses,selesai,ditolak]');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         $search    = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $status_komplain = $this->input->post('status');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
           $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total   = $this->model_kostumer_paket_la->get_total_server_side($search, $status_komplain);
         $list    = $this->model_kostumer_paket_la->get_index_server_side($perpage, $start_at, $search, $status_komplain);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar Kostumer Paket LA tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar Kostumer Paket LA berhasil ditemukan.',
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

   function _ck_id_kostumer_paket_la_exist($id){
      if( $this->input->post('id') ) {
         if( $this->model_kostumer_paket_la->check_kostumer_id($id) ) {
            return TRUE;
         }else{
            $this->form_validation->set_message('_ck_costumer_id', 'Kostumer id tidak ditemukan.');
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }


   function _ck_id_kostumer_paket_la_exist_2($id){
      if( $this->model_kostumer_paket_la->check_kostumer_id($id) ) {
         return TRUE;
      }else{
         $this->form_validation->set_message('_ck_costumer_id', 'Kostumer id tidak ditemukan.');
         return FALSE;
      }
   }

   function proses_addupdate_kostumer_paket_la(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_kostumer_paket_la_exist');
      $this->form_validation->set_rules('name', '<b>Kostumer ID<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('mobile_number', '<b>Diskon<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('address', '<b>Alamat<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get data
         $data = array();
         $data['name'] = $this->input->post('name');
         $data['mobile_number'] = $this->input->post('mobile_number');
         $data['address'] = $this->input->post('address');
         $data['last_update'] = date('Y-m-d');
         # filter process
         if ( $this->input->post('id') ) {
            if ( ! $this->model_kostumer_paket_la_cud->update_kostumer_paket_la( $this->input->post('id'), $data ) ) {
               $error = 1;
               $error_msg = 'Proses update kostumer paket la gagal dilakukan';
            }
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d');
            if ( ! $this->model_kostumer_paket_la_cud->insert_kostumer_paket_la( $data ) ) {
               $error = 1;
               $error_msg = 'Proses insert kostumer paket la gagal dilakukan';
            }
         }
         # filter feedBack
         if ($error == 0) {
            $return = array(
               'error'  => false,
               'error_msg' => 'Data kostumer paket la berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => true,
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

   function delete()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Kostumer Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_kostumer_paket_la_exist_2');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         // proses penghapusan
         if ($error == 0) {
            if (!$this->model_kostumer_paket_la_cud->delete_kostumer_paket_la($this->input->post('id'))) {
               $error = 1;
               $error_msg = 'Proses delete kostumer paket LA gagal dilakukan';
            }
         }
         // filte feedBack
         if ($error == 0) {
            $return = array(
               'error'  => false,
               'error_msg' => 'Data kostumer paket la berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => true,
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


   function get_info_edit(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Kostumer Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_kostumer_paket_la_exist_2');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $feedBack = $this->model_kostumer_paket_la->get_info_edit($this->input->post('id'));

         if ( count($feedBack) > 0 ) {
            $return = array(
               'error'  => false,
               'error_msg' => 'Data kostumer paket la berhasil ditemukan.',
               'data' => $feedBack,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => true,
               'error_msg' => 'Data kostumer paket la gagal ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      } else {
         if ( validation_errors() ) {
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