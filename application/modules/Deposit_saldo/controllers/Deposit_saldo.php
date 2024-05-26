<?php

/**
 *  -----------------------
 *	Deposit saldo Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Deposit_saldo extends CI_Controller
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
      $this->load->model('Model_deposit_saldo', 'model_deposit_saldo');
      # model fasilitas cud
      $this->load->model('Model_deposit_saldo_cud', 'model_deposit_saldo_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function _ck_otoritas(){
      if($this->session->userdata($this->config->item('apps_name'))['level_akun'] != 'administrator'){
         $this->form_validation->set_message('_ck_otoritas', 'Anda Tidak Berhak Untuk Melakukan Proses Hapus.');
         return FALSE;
      }else{
         return TRUE;
      }
   }


   function daftar_deposit_saldo()
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
         $total    = $this->model_deposit_saldo->get_total_deposit_saldo($search);
         $list    = $this->model_deposit_saldo->get_index_deposit_saldo($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar fasilitas tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar fasilitas berhasil ditemukan.',
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

   function get_info_deposit_saldo()
   {
      $error = 0;
      # generated invoice
      $nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
      # get list member
      $list_member = $this->model_deposit_saldo->get_list_member();
      if ($error != 0) {
         $return = array(
            'error'   => true,
            'error_msg' => 'Data info deposit tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'   => false,
            'error_msg' => 'Data info deposit berhasil ditemukan.',
            'data' => array(
               'nomor_transaksi' => $nomor_transaksi,
               'list_member' => $list_member
            ),
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   function _ck_currency_not_null($biaya)
   {
      if ($this->text_ops->hide_currency($biaya) > 0) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_currency_not_null', 'Biaya tidak boleh Nol!!!.');
         return FALSE;
      }
   }

   function _ck_member_id($member_id)
   {
      if ($this->model_deposit_saldo->check_member_id_exist($member_id)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_member_id', 'Member id tidak ditemukan didalam pangkalan data.');
         return FALSE;
      }
   }

   function _ck_nomor_transaksi($nomor_transaksi)
   {
      if ($this->model_deposit_saldo->check_nomor_transaksi($nomor_transaksi)) {
         $this->form_validation->set_message('_ck_nomor_transaksi', 'Nomor transaksi sudah terdaftar dipangkalan data.');
         return FALSE;
      } else {
         return TRUE;
      }
   }

   function _ck_id_deposit_transaksi($id)
   {
      if ($this->model_deposit_saldo->check_id_deposit_transaksi($id)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_id_deposit_transaksi', 'ID deposit transaksi tidak ditemukan.');
         return FALSE;
      }
   }

   function proses_addupdate_deposit_saldo()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('nomor_transaksi', '<b>Nomor Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_transaksi');
      $this->form_validation->set_rules('member', '<b>Member ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_member_id');
      $this->form_validation->set_rules('biaya_deposit', '<b>Biaya Deposit<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_currency_not_null');
      // $this->form_validation->set_rules('keperluan', '<b>Keperluan Deposit<b>', 'trim|required|xss_clean|min_length[1]|in_list[deposit,deposit_paket]');
      $this->form_validation->set_rules('info', '<b>Info Deposit<b>', 'trim|required|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['deposit_transaction']['nomor_transaction'] = $this->input->post('nomor_transaksi');
         $data['deposit_transaction']['company_id'] = $this->company_id;
         $data['deposit_transaction']['personal_id'] = $this->input->post('member');
         $data['deposit_transaction']['debet'] = $this->text_ops->hide_currency($this->input->post('biaya_deposit'));
         $data['deposit_transaction']['kredit'] = 0;

         $data['deposit_transaction']['saldo_sebelum'] = 0;
         $data['deposit_transaction']['saldo_sebelum'] = 0;

         $data['deposit_transaction']['transaction_requirement'] = 'deposit';
         # penerima
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $data['deposit_transaction']['approver'] = "Administrator";
         } else {
            $data['deposit_transaction']['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $data['deposit_transaction']['info'] = $this->input->post('info');
         $data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
         $data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');
         // akun
         $data['jurnal'] = array('company_id' => $this->company_id,
                                 'source' => 'depositsaldo:notransaction:'.$this->input->post('nomor_transaksi'),
                                 'ref' => 'Deposit Saldo Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'ket' => 'Deposit Saldo Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'akun_debet' => '11010',
                                 'akun_kredit' => '23000',
                                 'saldo' => $this->text_ops->hide_currency($this->input->post('biaya_deposit')),
                                 'periode_id' => 0,
                                 'input_date' => date('Y-m-d H:i:s'),
                                 'last_update'  => date('Y-m-d H:i:s'));
         // insert deposit saldo
         $feedBack = $this->model_deposit_saldo_cud->insert_deposit_saldo($data);
         # filter feedBack
         if ( $feedBack['status'] ) {

            $this->session->set_userdata(array('cetak_invoice' => array(
               'type' => 'cetak_kwitansi_deposit_saldo',
               'deposit_id' => $feedBack['id']
            )));

            $return = array(
               'error'   => false,
               'error_msg' => 'Data deposit saldo berhasil disimpan.',
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

   function delete_riwayat_saldo()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Deposit Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_otoritas|callback__ck_id_deposit_transaksi');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {

         // depositsaldo:notransaction:
         $nomor_transaksi = $this->model_deposit_saldo->get_nomor_transaksi_deposit_saldo( $this->input->post('id') );

         # filter feedBack
         if ($this->model_deposit_saldo_cud->delete_deposit_transaksi($this->input->post('id'),  $nomor_transaksi )) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data deposit saldo berhasil dihapus.',
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

   function cetak_kwitansi_deposit_saldo(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Deposit Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_deposit_transaksi');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         // deposit id
			$deposit_id = $this->input->post('id');
			// create session priting here
			$this->session->set_userdata(array('cetak_invoice' => array(
				'type' => 'cetak_kwitansi_deposit_saldo',
				'deposit_id' => $deposit_id
			)));
         # filter feedBack
         if ( $error == 0 ) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Success.',
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

 
}
