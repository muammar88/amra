<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Riwayat transaksi peminjaman Controller
 *	Created by Muammar Kadafi
 */
class Riwayat_transaksi_peminjaman extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load model pesan whatsapp
      $this->load->model('Model_riwayat_transaksi_peminjaman', 'model_riwayat_transaksi_peminjaman');
      #load modal perangkat whatsapp cud
      // $this->load->model('Model_pesan_whatsapp_cud', 'model_pesan_whatsapp_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   // dafta riwayat deposit tabungan
   function daftar_riwayat_transaksi_peminjaman(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('start_date',  '<b>Start Date<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('end_date',  '<b>End Date<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage', '<b>Perpage<b>',  'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>',  'trim|xss_clean|min_length[1]|numeric');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $search = array();
         $search['search'] = $this->input->post('search');
         $search['start_date'] = $this->input->post('start_date');
         $search['end_date'] = $this->input->post('end_date');

         $perpage = $this->input->post('perpage');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total   = $this->model_riwayat_transaksi_peminjaman->get_total_riwayat_transaksi_peminjaman($search);
         $list    = $this->model_riwayat_transaksi_peminjaman->get_index_riwayat_transaksi_peminjaman($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Riwayat transaksi peminjaman tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Riwayat transaksi peminjaman berhasil ditemukan.',
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

   // cetak riwayat transaksi peminjaman
   function cetak_riwayat_transaksi_peminjaman(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('start_date',  '<b>Start Date<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('end_date',  '<b>End Data<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('search',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $start_date = $this->input->post('start_date');
         $end_date = $this->input->post('end_date');
         $search = $this->input->post('search');

         $this->session->set_userdata(array(
            'cetak_invoice' => array(
               'type' => 'cetak_riwayat_transaksi_peminjaman',
               'start_date' => $start_date,
               'end_date' => $end_date,
               'search' => $search
            )
         ));
         // filter
         if ( $error == 1 ) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Cetak riwayat transaksi peminjaman gagal dilakukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Cetak riwayat transaksi peminjaman berhasil dilakukan.',
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