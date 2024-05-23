<?php

/**
 *  -----------------------
 *	Daftar jurnal Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */
//
defined('BASEPATH') or exit('No direct script access allowed');

class Neraca_lajur extends CI_Controller
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
      $this->load->model('Model_neraca_lajur', 'model_neraca_lajur');
      # model daftar mobil cud
      // $this->load->model('Model_jurnal_cud', 'model_jurnal_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   # get filter neraca lajur
   function get_filter_neraca_lajur()
   {
      $error = 0;
      $error_msg = '';
      # list periode
      $list_periode = $this->model_neraca_lajur->get_list_periode();
      # filter
      if (count($list_periode) == 0) {
         $error = 1;
         $error_msg = 'List periode tidak ditemukan.';
      }
      # filter error
      if ($error == 1) {
         $return = array(
            'error'   => true,
            'error_msg' => $error_msg,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'   => false,
            'error_msg' => 'Data neraca lajur berhasil ditemukan.',
            'list_periode' => $list_periode,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   function _ck_periode_exist($id)
   {
      if( $id == 0 ){
         return TRUE;
      }else{
         if ($this->model_neraca_lajur->check_periode_exist($id)) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_periode_exist', 'ID Periode tidak ditemukan.');
            return FALSE;
         }
      }
   }

   # daftar neraca lajur
   function daftar_neraca_lajur()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('periode',   '<b>Periode<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_periode_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $param = array();
         $param['periode'] = $this->input->post('periode');
         $list = $this->model_neraca_lajur->get_index_daftar_neraca_lajur($param);
         if ($error == 1) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar neraca lajur tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar neraca lajur berhasil ditemukan.',
               'data' => $list['list'],
               'total' => $list['total'],
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

   function download_excel_neraca_lajur()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('periode',   '<b>Periode<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_periode_exist');
      /*
		  Validation process
	  */
      if ($this->form_validation->run()) {
         # set session
         $this->session->set_userdata(array('download_to_excel' => array(
            'type' => 'download_neraca_lajur',
            'periode' => $this->input->post('periode')
         )));
         if (!$this->session->userdata('download_to_excel')) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar data neraca ajur tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar data neraca lajur berhasil ditemukan.',
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
