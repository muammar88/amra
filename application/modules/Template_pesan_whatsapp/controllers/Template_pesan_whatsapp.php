<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Template Pesan Whatsapp Controller
 *	Created by Muammar Kadafi
 */
class Template_pesan_whatsapp extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load model perangkat whatsapp
      $this->load->model('Model_template_pesan_whatsapp', 'model_template_pesan_whatsapp');
      #load modal perangkat whatsapp cud
      $this->load->model('Model_template_pesan_whatsapp_cud', 'model_template_pesan_whatsapp_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   # daftar template pesan whatsapp
   function daftar_template_pesan_whatsapp(){
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
         $total   = $this->model_template_pesan_whatsapp->get_total_daftar_template_pesan_whatsapp($search);
         $list    = $this->model_template_pesan_whatsapp->get_index_daftar_template_pesan_whatsapp($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Daftar Template Pesan Whatsapp Tidak Ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Daftar Template Pesan Whatsapp Berhasil Ditemukan.',
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

   # get variable template pesan whatsapp
   function get_variable_template_pesan_whatsapp(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $cetak_invoice = false;
      $invoice = '';
      $this->form_validation->set_rules('jenis_pesan', '<b>Jenis Pesan<b>', 'trim|required|xss_clean|min_length[1]|in_list[pesan_biasa,agen,semua_jamaah,staff,jamaah_paket,jamaah_sudah_berangkat,jamaah_tabungan_umrah,jamaah_utang_koperasi]');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # ge variable
         $getVariable = $this->model_template_pesan_whatsapp->get_variable_by_jenis_pesan( $this->input->post('jenis_pesan') );
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Proses get variable berhasil dilakukan.',
               'data' => $getVariable, 
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Proses get variable gagal dilakukan.',
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

   # check template id
   function _ck_template_id(){
      if( $this->input->post('id') ) {
         if( $this->model_template_pesan_whatsapp->check_template_id( $this->input->post('id') ) ) {
            return TRUE;
         }else{
            $this->form_validation->set_message('_ck_template_id', 'Template ID tidak ditemukan.');
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }

   # add update template pesan whatsapp
   function add_update_template_pesan_whatsapp(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $cetak_invoice = false;
      $invoice = '';
      $this->form_validation->set_rules('id', '<b>Template ID<b>', 'trim|xss_clean|min_length[1]|callback__ck_template_id');
      $this->form_validation->set_rules('nama_template', '<b>Nama Template<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('jenis_pesan', '<b>Jenis Pesan<b>', 'trim|required|xss_clean|min_length[1]|in_list[pesan_biasa,semua_jamaah,staff,agen,jamaah_paket,jamaah_tabungan_umrah,jamaah_utang_koperasi]');
      $this->form_validation->set_rules('pesan', '<b>Pesan<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # define data
         $data = array();
         $data['nama_template'] = $this->input->post('nama_template');
         $data['jenis_pesan'] = $this->input->post('jenis_pesan');
         $data['pesan'] = $this->input->post('pesan');
         $data['variable'] = $this->model_template_pesan_whatsapp->get_variable_by_jenis_pesan($this->input->post('jenis_pesan'));
         $data['last_update'] = date('Y-m-d H:i:s');
         # filter
         if( $this->input->post('id') ) {
            if( ! $this->model_template_pesan_whatsapp_cud->update_template_pesan( $this->input->post('id'), $data ) ) {
               $error = 1;
               $error_msg = 'Update template pesan whatsapp gagal dilakukan';
            }
         }else{
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d H:i:s');
            if( ! $this->model_template_pesan_whatsapp_cud->insert_template_pesan( $data ) ){
               $error = 1;
               $error_msg = 'Insert template pesan whatsapp gagal dilakukan.';
            }
         }
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Proses simpan data template pesan whatsapp berhasil dilakukan.',
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

   # delete template
   function delete_template(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $cetak_invoice = false;
      $invoice = '';
      $this->form_validation->set_rules('id', '<b>Template ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_template_id');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # filter
         if ( $this->model_template_pesan_whatsapp_cud->delete_template_pesan( $this->input->post('id') ) ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Delete data template pesan whatsapp proses berhasil dilakukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Delete data template pesan whatsapp proses gagal dilakukan.',
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


   function get_info_edit_template(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $cetak_invoice = false;
      $invoice = '';
      $this->form_validation->set_rules('id', '<b>Template ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_template_id');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # get data
         $get_data = $this->model_template_pesan_whatsapp->get_info_edit_template( $this->input->post('id') );
         # filter
         if ( count($get_data) > 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Data template berhasil ditemukan.',
               'data' => $get_data,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data template gagal ditemukan.',
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