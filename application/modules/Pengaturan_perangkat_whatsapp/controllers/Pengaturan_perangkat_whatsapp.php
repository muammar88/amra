<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Pengaturan Perangkat Whatsapp Controller
 *	Created by Muammar Kadafi
 */
class Pengaturan_perangkat_whatsapp extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load model perangkat whatsapp
      // $this->load->model('Model_perangkat_whatsapp', 'model_perangkat_whatsapp');
      // #load modal perangkat whatsapp cud
      // $this->load->model('Model_perangkat_whatsapp_cud', 'model_perangkat_whatsapp_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }


   function get_info_perangkat(){
      $error = 0;
      $error_msg = '';
      $data = array();
      # api key 
      $this->whatsapp_ops->define_api();
      # get info device 
      $this->whatsapp_ops->define_device_key();
      # check api key
      if( $this->whatsapp_ops->check_api_key() ) {
         # check device key
         if( $this->whatsapp_ops->check_device_key() ) {
            # check whatsapp number
            if( $this->whatsapp_ops->check_whatsapp_number() ) {
               # get info device from server
               $this->whatsapp_ops->get_info_device();
               # check response
               if( $this->whatsapp_ops->status_response() == 'ok' ) {
                  # response
                  $response = $this->whatsapp_ops->response();
                  # data
                  $data['api_id'] = $this->whatsapp_ops->get_api_key();
                  $data['whatsapp_number'] = $this->whatsapp_ops->get_whatsapp_number();
                  $data['device_key'] = $this->whatsapp_ops->get_device_key();
                  $data['start_date'] = $response->data->created_at;
                  $data['end_date'] = $response->data->expired_at;
                  $data['status'] = $response->data->status;
               }else{
                  $error = 1;
                  $error_msg = 'Perangkat tidak ditemukan';
               }
            }else{
               $error = 1;
               $error_msg = 'Nomor Whatsapp Tidak Ditemukan';
            }
         }else{
            $error = 1;
            $error_msg = 'Info Device Tidak Ditemukan';
         }
      }else{
         $error = 1;
         $error_msg = 'API Key Tidak Ditemukan';
      }
      # filter
      if ( $error == 0 ) {
         $return = array(
            'error'  => false,
            'error_msg' => 'Daftar grup ditemukan.',
            'data' => $data,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'  => true,
            'error_msg' => $error_msg,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   function restart_perangkat(){
      $error = 0;
      $error_msg = '';
      $data = array();
      # api key 
      $this->whatsapp_ops->define_api();
      # get info device 
      $this->whatsapp_ops->define_device_key();
      # check api key
      if( $this->whatsapp_ops->check_api_key() ) {
         # check device key
         if( $this->whatsapp_ops->check_device_key() ) {
            # check whatsapp number
            if( $this->whatsapp_ops->check_whatsapp_number() ) {
               # restart device
               $this->whatsapp_ops->restart_device();
               # check response
               if( $this->whatsapp_ops->status_response() == 'ok' ) {
                  $error_msg = 'Proses restart perangkat berhasil dilakukan.';
               }else{
                  $error = 1;
                  $error_msg = 'Perangkat tidak ditemukan';
               }
            }else{
               $error = 1;
               $error_msg = 'Nomor Whatsapp Tidak Ditemukan';
            }
         }else{
            $error = 1;
            $error_msg = 'Info Device Tidak Ditemukan';
         }
      }else{
         $error = 1;
         $error_msg = 'API Key Tidak Ditemukan';
      }
      # filter
      if ( $error == 0 ) {
         $return = array(
            'error'  => false,
            'error_msg' => $error_msg,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'  => true,
            'error_msg' => $error_msg,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }
}