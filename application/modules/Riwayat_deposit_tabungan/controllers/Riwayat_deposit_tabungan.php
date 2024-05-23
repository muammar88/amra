<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Riwayat deposit tabungan Controller
 *	Created by Muammar Kadafi
 */
class Riwayat_deposit_tabungan extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load model pesan whatsapp
      $this->load->model('Model_riwayat_deposit_tabungan', 'model_riwayat_deposit_tabungan');
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

   // check member id
   function _ck_member_id($id){
      if( $id != 0 ){
         if( ! $this->model_riwayat_deposit_tabungan->check_member_id( $id ) ){
            $this->form_validation->set_message('_ck_member_id', 'Member id tidak ditemukan.');
            return FALSE;
         }else{
            return TRUE;
         }
      }else{
         return TRUE;
      }
   }

   // daftar riwayat deposit tabungan
   function daftar_riwayat_deposit_tabungan(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('tipe_transaksi',  '<b>Search<b>',   'trim|required|xss_clean|min_length[1]|in_list[semua,tabungan_umrah,deposit_saldo]');
      $this->form_validation->set_rules('start_date',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('end_date',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('member',  '<b>Search<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_member_id');
      $this->form_validation->set_rules('perpage', '<b>Perpage<b>',  'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>',  'trim|xss_clean|min_length[1]|numeric');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         // search
         $search = array();
         $search['tipe_transaksi'] = $this->input->post('tipe_transaksi');
         $search['start_date'] = $this->input->post('start_date');
         $search['end_date'] = $this->input->post('end_date');
         $search['member'] = $this->input->post('member');
         // perpage
         $perpage = $this->input->post('perpage');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total   = $this->model_riwayat_deposit_tabungan->get_total_daftar_riwayat_deposit_tabungan($search);
         $list    = $this->model_riwayat_deposit_tabungan->get_index_daftar_riwayat_deposit_tabungan($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Daftar airlines tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Daftar airlines berhasil ditemukan.',
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

   // get list member deposit tabungan
   function get_list_member_deposit_tabungan(){
      // get info
      $info = $this->model_riwayat_deposit_tabungan->get_list_member_deposit_tabungan();
      // filter
      if ( count($info) == 0  ) {
         $return = array(
            'error'  => true,
            'error_msg' => 'Data member tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'  => false,
            'error_msg' => 'Data member berhasil ditemukan.',
            'data' => $info,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }


   function cetak_riwayat_deposit_tabungan(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('tipe_transaksi',  '<b>Search<b>',   'trim|required|xss_clean|min_length[1]|in_list[semua,tabungan_umrah,deposit_saldo]');
      $this->form_validation->set_rules('start_date',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('end_date',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('member',  '<b>Search<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_member_id');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         $tipe_transaksi = $this->input->post('tipe_transaksi');
         $start_date = $this->input->post('start_date');
         $end_date = $this->input->post('end_date');
         $member = $this->input->post('member');

         $this->session->set_userdata(array(
            'cetak_invoice' => array(
               'type' => 'cetak_riwayat_deposit_tabungan',
               'tipe_transaksi' => $tipe_transaksi,
               'start_date' => $start_date,
               'end_date' => $end_date,
               'member' => $member
            )
         ));
         // filter
         if ( $error == 1 ) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Cetak riwayat deposit tabungan gagal dilakukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Cetak riwayat deposit tabungan berhasil dilakukan.',
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