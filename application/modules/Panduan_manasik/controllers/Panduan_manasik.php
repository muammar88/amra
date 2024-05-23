<?php

/**
 *  -----------------------
 *	Panduan Manasik Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Panduan_manasik extends CI_Controller
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
      $this->load->model('Model_panduan_manasik', 'model_panduan_manasik');
      $this->load->model('Model_panduan_manasik_cud', 'model_panduan_manasik_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function get_info_panduan(){
      $return 	= array();
      $error 		= 0;
      $error_msg = '';
      $this->form_validation->set_rules('param','<b>Param<b>', 'trim|required|xss_clean|min_length[1]|in_list[perjalanan,manasik,pelaksanaan,hikmah,tempat_ziarah,tanya_jawab]');
      /*
       Validation process
      */
      if ($this->form_validation->run()) {
         $param 	= $this->input->post('param');
         # info
         $info = $this->model_panduan_manasik->get_info_panduan_manasik($param);
         # filter
         if (count($info) == 0 ) {
            $return = array(
               'error'	=> true,
               'error_msg' => 'Daftar info perjalanan manasik haji dan umrah tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Daftar info perjalanan berhasil ditemukan.',
               'data' => $info,
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

   function _ck_part($param){
      $tab = $this->input->post('tab');
      if( $this->model_panduan_manasik->check_panduan_manasik($tab, $param)){
         return TRUE;
      }else{
         $this->form_validation->set_message('_ck_part', 'Part tidak ditemukan.');
         return FALSE;
      }

   }

   function get_detail_panduan(){
      $return 	= array();
      $error 		= 0;
      $error_msg = '';
      $this->form_validation->set_rules('tab','<b>Tab<b>', 'trim|required|xss_clean|min_length[1]|in_list[perjalanan,manasik,pelaksanaan,hikmah,tempat_ziarah,tanya_jawab]');
      $this->form_validation->set_rules('param','<b>Param<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_part');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $tab = $this->input->post('tab');
         $param 	= $this->input->post('param');
         # info
         $detail = $this->model_panduan_manasik->get_detail_panduan_manasik($tab, $param);
         # filter
         if ($error == 1 ) {
           $return = array(
               'error'	=> true,
               'error_msg' => 'Detail tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
           );
         } else {
           $return = array(
               'error'	=> false,
               'error_msg' => 'Detail berhasil ditemukan.',
               'data' => $detail,
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

   function addUpdatePanduanManasik(){
      $return 	= array();
      $error 		= 0;
      $error_msg = '';
      $this->form_validation->set_rules('tab','<b>Tab<b>', 'trim|required|xss_clean|min_length[1]|in_list[perjalanan,manasik,pelaksanaan,hikmah,tempat_ziarah,tanya_jawab]');
      $this->form_validation->set_rules('part','<b>Part<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_part');
      $this->form_validation->set_rules('artikel','<b>Artikel<b>', 'trim|required|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $tab = $this->input->post('tab');
         $part 	= $this->input->post('part');
         $artikel = $this->input->post('artikel');

         $data = array();
         $data['content'] = $this->input->post('artikel');
         // save prcess
         if( $this->model_panduan_manasik->check_exist_content($tab, $part) ){
            # get detail id
            $get_detail_id = $this->model_panduan_manasik->get_detail_id($tab, $part);
            // update process
            if( ! $this->model_panduan_manasik_cud->update( $get_detail_id, $data ) ) {
               $error = 1;
               $error_msg = 'Proses update panduan manasik gagal dilakukan.';
            }
         }else{
            $data['panduan_manasik_id'] = $this->model_panduan_manasik->get_id($tab, $part);
            // insert process
            if( ! $this->model_panduan_manasik_cud->insert( $data ) ) {
               $error = 1;
               $error_msg = 'Proses insert panduan manasik gagal dilakukan.';
            }
         }
         # filter
         if ($error == 1 ) {
           $return = array(
               'error'	=> true,
               'error_msg' => $error_msg,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
           );
         } else {
           $return = array(
               'error'	=> false,
               'error_msg' => 'Proses berhasil dilakukan.',
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
