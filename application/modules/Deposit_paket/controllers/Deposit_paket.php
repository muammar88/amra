<?php

/**
 *  -----------------------
 *	Deposit paket Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Deposit_paket extends CI_Controller
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
      $this->load->model('Model_deposit_paket', 'model_deposit_paket');
      # model fasilitas cud
      $this->load->model('Model_deposit_paket_cud', 'model_deposit_paket_cud');
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

   function daftar_deposit_paket(){
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage',   '<b>Perpage<b>',    'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber',   '<b>pageNumber<b>',    'trim|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('filterTransaksi',   '<b>filter Transaksi<b>',    'trim|required|xss_clean|min_length[1]|in_list[belum,sudah,batal]');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         $search    = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $filterTransaksi = $this->input->post('filterTransaksi');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
           $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total    = $this->model_deposit_paket->get_total_deposit_paket($search, $filterTransaksi);
         $list    = $this->model_deposit_paket->get_index_deposit_paket($perpage, $start_at, $filterTransaksi, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar deposit paket tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar deposit paket berhasil ditemukan.',
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

   function get_info_update_target_paket() {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         # list target paket
         $list_paket = $this->model_deposit_paket->get_list_paket();
         # get value
         $value = $this->model_deposit_paket->get_value_tabungan( $this->input->post('id') );
         # filter
         if ($error != 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data info target paket tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data info target paket berhasil ditemukan.',
               'data' => $list_paket,
               'value' => $value, 
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

   function update_target_paket() {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      $this->form_validation->set_rules('target_paket', '<b>Target Paket ID</b>', 'trim|required|xss_clean|min_length[1]|callback__ck_target_paket_id');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {

         $id = $this->input->post('id');
         $target_paket = $this->input->post('target_paket');
         // process update target paket
         if( ! $this->model_deposit_paket_cud->update_target_paket($id, $target_paket) ) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data info target paket tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'   => false,
               'error_msg' => 'Data info target paket berhasil ditemukan.',
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

   function get_info_deposit_paket(){
      $error = 0;
      # generated invoice
      $nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
      # get list member
      $list_member = $this->model_deposit_paket->get_list_member();
      # list target paket
      $list_paket = $this->model_deposit_paket->get_list_paket();
      // filter error
      if ($error != 0) {
         $return = array(
            'error'   => true,
            'error_msg' => 'Data info deposit paket tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'   => false,
            'error_msg' => 'Data info deposit paket berhasil ditemukan.',
            'data' => array(
               'nomor_transaksi' => $nomor_transaksi,
               'list_member' => $list_member, 
               'list_paket' => $list_paket
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

   function _ck_jamaah_id($jamaah_id)
   {
      if ($this->model_deposit_paket->check_jamaah_id_exist($jamaah_id)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_jamaah_id', 'Jamaah id tidak ditemukan didalam pangkalan data.');
         return FALSE;
      }
   }

   // check nomor transaksi
   function _ck_nomor_transaksi($nomor_transaksi)
   {
      if ($this->model_deposit_paket->check_nomor_transaksi($nomor_transaksi)) {
         $this->form_validation->set_message('_ck_nomor_transaksi', 'Nomor transaksi sudah terdaftar dipangkalan data.');
         return FALSE;
      } else {
         return TRUE;
      }
   }

   // check saldo deposit paket
   function _ck_saldo_deposit_paket($sumber_dana){
      if( $sumber_dana == 'deposit') {
         // get biaya tambungan
         $biaya_deposit = $this->text_ops->hide_currency($this->input->post('biaya_deposit'));
         // check saldo jamaah
         $saldo_jamaah = $this->model_deposit_paket->check_saldo_deposit_jamaah( $this->input->post('jamaah_id') );
         if( $saldo_jamaah['status'] == false ){
            $this->form_validation->set_message('_ck_saldo_deposit_paket', 'Nomor transaksi sudah terdaftar dipangkalan data.');
            return FALSE;
         }else{
            if( $biaya_deposit > $saldo_jamaah['saldo'] ) {
               $this->form_validation->set_message('_ck_saldo_deposit_paket', 'Saldo deposit tidak mencukupi.');
            return FALSE;
            }else{
               return TRUE;
            }
         }
      }else{
         return TRUE;
      }
   }

   function _ck_target_paket_id( $value ) {
      if($value != 0 ) {
         if( $this->model_deposit_paket->check_target_paket_id( $value ) ) {
            return TRUE;
         }else{
             $this->form_validation->set_message('_ck_target_paket_id', 'Check Target Paket ID Tidak Ditemukan.');
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }

   function proses_addupdate_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('nomor_transaksi', '<b>Nomor Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_transaksi');
      $this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_jamaah_id');
      $this->form_validation->set_rules('biaya_deposit', '<b>Biaya Deposit<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_currency_not_null');
      $this->form_validation->set_rules('info', '<b>Info Deposit<b>', 'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('target_paket', '<b>Target Paket ID<b>', 'trim|xss_clean|min_length[1]|callback__ck_target_paket_id');
      $this->form_validation->set_rules('sumber_dana', '<b>Sumber Dana<b>', 'trim|required|xss_clean|min_length[1]|in_list[cash,deposit]|callback__ck_saldo_deposit_paket');
      if( $this->input->post('fee_agen') ) {
         foreach ($this->input->post('fee_agen') as $key => $value) {
            $this->form_validation->set_rules("fee_agen[" . $key . "]", "Fee Agen", 'trim|xss_clean|min_length[1]');
         }
      }
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         $total_fee = 0;
         $biaya_deposit = $this->text_ops->hide_currency( $this->input->post('biaya_deposit') );
         if( $this->input->post('fee_agen')){
            $fee_agen = $this->input->post('fee_agen');
            foreach ($fee_agen as $key => $value) {
               $total_fee = $total_fee + $this->text_ops->hide_currency($value);
            }
         }
         if( $biaya_deposit < $total_fee ){
            $error = 1;
            $error_msg = 'Biaya deposit tidak boleh lebih kecil dari total fee.';
         }else{
            # personal id
            $personal_id = $this->model_deposit_paket->get_personal_id($this->input->post('jamaah_id'));
            # level agen
            $level_agen = $this->model_deposit_paket->get_level_agen();
            // data fee agen
            $data_fee_keagenan = array();
            # data detail fee keagenan
            $data_detail_fee_keagenan = array();
            # fee keagenan
            if( $this->input->post('fee_agen')){
               $data_fee_keagenan['company_id'] = $this->company_id;
               $data_fee_keagenan['personal_id'] = $personal_id;
               $data_fee_keagenan['input_date'] = date('Y-m-d');
               $data_fee_keagenan['last_update'] = date('Y-m-d');
               # detail fee keagenan
               foreach ($fee_agen as $key => $value) {
                  $data_detail_fee_keagenan[] = array('transaction_number' => $this->random_code_ops->number_transaction_detail_fee_keagenan(),
                                                      'company_id' => $this->company_id,
                                                      'agen_id' => $key,
                                                      'level_agen_id' => $level_agen[$key],
                                                      'fee' => $this->text_ops->hide_currency($value),
                                                      'input_date' => date('Y-m-d H:i:s'),
                                                      'last_update' => date('Y-m-d H:i:s'));
               }
            }
            # data pool
            $data_pool = array();
            $data_pool['company_id'] = $this->company_id;
            $data_pool['active'] = 'active';
            if( $this->input->post('target_paket') != 0 ) {
               $data_pool['target_paket_id'] = $this->input->post('target_paket');
            }
            $data_pool['input_date'] = date('Y-m-d H:i:s');
            $data_pool['last_update'] = date('Y-m-d H:i:s');
            $data_pool['jamaah_id'] = $this->input->post('jamaah_id');
            # data deposit transaction
            $data_deposit_transaction = array();
            $data_deposit_transaction[0]['nomor_transaction'] = $this->input->post('nomor_transaksi');
            $data_deposit_transaction[0]['company_id'] = $this->company_id;
            $data_deposit_transaction[0]['personal_id'] = $personal_id;
            $data_deposit_transaction[0]['debet'] = $this->text_ops->hide_currency($this->input->post('biaya_deposit'));
            $data_deposit_transaction[0]['kredit'] = 0;
            $data_deposit_transaction[0]['transaction_requirement'] = 'paket_deposit';
            $data_deposit_transaction[0]['sumber_dana'] = $this->input->post('sumber_dana');
            # penerima
            if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
              $data_deposit_transaction[0]['approver'] = "Administrator";
            } else {
              $data_deposit_transaction[0]['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }

            $data_deposit_transaction[0]['info'] = $this->input->post('info');
            $data_deposit_transaction[0]['input_date'] = date('Y-m-d H:i:s');
            $data_deposit_transaction[0]['last_update'] = date('Y-m-d H:i:s');
            // jurnal
            $data_jurnal = array();
            # deposit
            if( $this->input->post('sumber_dana') == 'deposit' ) {
               # generated invoice
               $nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
               $data_deposit_transaction[0]['no_tansaksi_sumber_dana'] = $nomor_transaksi;

               $data_deposit_transaction[1]['nomor_transaction'] = $nomor_transaksi;
               $data_deposit_transaction[1]['company_id'] = $this->company_id;
               $data_deposit_transaction[1]['personal_id'] = $personal_id;
               $data_deposit_transaction[1]['debet'] = 0;
               $data_deposit_transaction[1]['kredit'] = $this->text_ops->hide_currency($this->input->post('biaya_deposit'));
               $data_deposit_transaction[1]['transaction_requirement'] = 'deposit';
               # penerima
               if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
                 $data_deposit_transaction[1]['approver'] = "Administrator";
               } else {
                 $data_deposit_transaction[1]['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
               }
               $data_deposit_transaction[1]['info'] = 'Pemindahan Deposit Ke Tabungan dengan Nomor Transaksi Tabungan '.$this->input->post('nomor_transaksi') ;
               $data_deposit_transaction[1]['input_date'] = date('Y-m-d H:i:s');
               $data_deposit_transaction[1]['last_update'] = date('Y-m-d H:i:s');

               // akun
               $data_jurnal[] = array('company_id' => $this->company_id,
                                         'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
                                         'ref' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                         'ket' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                         'akun_debet' => '23000',
                                         'akun_kredit' => '24000',
                                         'saldo' => $this->text_ops->hide_currency($this->input->post('biaya_deposit')),
                                         'periode_id' => 0,
                                         'input_date' => date('Y-m-d H:i:s'),
                                         'last_update'  => date('Y-m-d H:i:s'));

            }else{
               // akun
               $data_jurnal[] = array('company_id' => $this->company_id,
                                       'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
                                       'ref' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                       'ket' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                       'akun_debet' => '11010',
                                       'akun_kredit' => '24000',
                                       'saldo' => $this->text_ops->hide_currency($this->input->post('biaya_deposit')),
                                       'periode_id' => 0,
                                       'input_date' => date('Y-m-d H:i:s'),
                                       'last_update'  => date('Y-m-d H:i:s'));

            }
            # data pool transaction
            $data_pool_transaction = array();
            $data_pool_transaction['company_id'] = $this->company_id;

            # insert process
            $insert = $this->model_deposit_paket_cud->insert_deposit_paket( $data_pool, $data_deposit_transaction, $data_pool_transaction, $data_fee_keagenan, $data_detail_fee_keagenan, $data_jurnal );
            if ( $insert['status'] == true) {
               # create session
               $this->session->set_userdata(array('cetak_invoice' => array(
                'type' => 'cetak_kwitansi_deposit_paket',
                'deposit_id' => $insert['id']
               )));
            }else{
               $error = 1;
               $error_msg = 'Proses simpan data deposit gagal dilakukan.';
            }
         }
        # filter
        if ( $error == 0 ) {
           # create return
           $return = array(
             'error'   => false,
             'error_msg' => 'Proses deposit paket berhasil dilukan.',
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

   # agen
   function get_info_agen_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('jamaah_id', '<b>Jamaah ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_jamaah_id');
      /*
       Validation process
      */
      if ($this->form_validation->run()) {
         # agen
         $agen = $this->model_deposit_paket->fee_keagenan_deposit_paket( $this->input->post('jamaah_id') );
         # filter
         if (  $error == false ) {
           # create return
           $return = array(
            'error'   => false,
            'error_msg' => 'Info agen deposit berhasil ditemukan.',
            'data' => $agen,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
           );
         } else {
           $return = array(
            'error'   => true,
            'error_msg' => 'Proses deposit paket gagal dilakukan.',
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

   function _ck_pool_id($pool_id){
      if( ! $this->model_deposit_paket->check_pool_id($pool_id) ){
         $this->form_validation->set_message('_ck_pool_id', 'Pool id tidak ditemukan dipangkalan data.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function get_info_pembayaran_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      /*
       Validation process
      */
      if ($this->form_validation->run()) {
         # get pool id
         $pool_id = $this->input->post('id');
         # info pool
         $total_pembayaran = $this->model_deposit_paket->get_info_pembayaran_pool($pool_id);
         # generated invoice
         $nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
         # info agen
         $info_agen = $this->model_deposit_paket->get_info_agen_by_pool_id($pool_id);
         # info jamaah
         $info_jamaah = $this->model_deposit_paket->get_info_jamaah_by_pool_id($pool_id);
         # filter
         if ( $nomor_transaksi != '' ) {
           # create return
           $return = array(
            'error'   => false,
            'error_msg' => 'Info total pembayaran deposit paket berhasil ditemukan.',
            'data' => array('total_pembayaran' => $total_pembayaran,
                            'nomor_transaksi' => $nomor_transaksi,
                            'fullname' => $info_jamaah['fullname'],
                            'identity_number' => $info_jamaah['identity_number'],
                            'nama_agen' => $info_agen, ),
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
           );
         } else {
           $return = array(
            'error'   => true,
            'error_msg' => 'Info total pembayaran deposit paket gagal ditemukan.',
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

   # check tenor pinjam
   function _ck_tenor_pinjaman($tenor){
      if( $this->input->post('sumber_pembayaran') == 'pinjam' ) {
         if( $tenor != '' ){
            return TRUE;
         }else{
            $this->form_validation->set_message('_ck_tenor_pinjaman', 'Untuk sumber pembayaran pinjaman koperasi, <b>Tenor</b> tidak boleh kosong.');
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }

   function _ck_mulai_pembayaran( $mulai_pembayaran ){
      if( $this->input->post('sumber_pembayaran') == 'pinjam' ) {
         if( $mulai_pembayaran != '' ){
            return TRUE;
         }else{
            $this->form_validation->set_message('_ck_mulai_pembayaran', 'Untuk sumber pembayaran pinjaman koperasi, <b>Tanggal Mulai Pembayaran</b> tidak boleh kosong.');
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }

   function _ck_jamaah_id_not_loan( $sumber_pembayaran ) {
      if( $sumber_pembayaran == 'pinjam' ) {
         if( ! $this->model_deposit_paket->check_jamaah_id_not_loan_by_pool_id( $this->input->post('id') ) ){
            return TRUE;
         }else{
            $this->form_validation->set_message('_ck_jamaah_id_not_loan', 'Jamaah ini tidak dapat meminjam karena masih terdapat perminjaman yang belum lunas.');
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }

   # proses pembayaran deposit paket
   function proses_pembayaran_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool Id<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      $this->form_validation->set_rules('nomor_transaksi', '<b>Nomor Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_transaksi');
      $this->form_validation->set_rules('biaya_deposit', '<b>Biaya Deposit<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_currency_not_null');
      $this->form_validation->set_rules('info', '<b>Info Deposit<b>', 'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('sumber_pembayaran', '<b>Sumber Pembayaran<b>', 'trim|required|xss_clean|min_length[1]|in_list[cash,pinjam]|callback__ck_jamaah_id_not_loan');
      $this->form_validation->set_rules('tenor', '<b>Tenor<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_tenor_pinjaman');
      $this->form_validation->set_rules('tanggal_mulai', '<b>Tanggal Mulai Pembayaran Peminjaman<b>', 'trim|xss_clean|min_length[1]|callback__ck_mulai_pembayaran');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         # personal id
         $personal_id = $this->model_deposit_paket->get_personal_id_by_pool_id($this->input->post('id'));
         # data deposit transaction
         $data_deposit_transaction = array();
         $data_deposit_transaction['nomor_transaction'] = $this->input->post('nomor_transaksi');
         $data_deposit_transaction['company_id'] = $this->company_id;
         $data_deposit_transaction['personal_id'] = $personal_id;
         $data_deposit_transaction['debet'] = $this->text_ops->hide_currency($this->input->post('biaya_deposit'));
         $data_deposit_transaction['kredit'] = 0;
         $data_deposit_transaction['transaction_requirement'] = 'paket_deposit';
         # penerima
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
           $data_deposit_transaction['approver'] = "Administrator";
         } else {
           $data_deposit_transaction['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $data_deposit_transaction['info'] = $this->input->post('info');
         $data_deposit_transaction['input_date'] = date('Y-m-d H:i:s');
         $data_deposit_transaction['last_update'] = date('Y-m-d H:i:s');
         # data pool transaction
         $data_pool_transaction = array();
         $data_pool_transaction['company_id'] = $this->company_id;
         $data_pool_transaction['pool_id'] = $this->input->post('id');


         $data = array();
         if( $this->input->post('sumber_pembayaran') == 'pinjam') {
            // tenor
            $tenor = $this->input->post('tenor');
            // get biaya
            $biaya = $this->text_ops->hide_currency($this->input->post('biaya_deposit'));
            // mulai pembayaran
            $mulai_pembayaran = $this->input->post('tanggal_mulai');
            // jamaah id
            $jamaah_id = $this->model_deposit_paket->get_jamaah_id_by_pool_id( $this->input->post('id') );
            # data peminjaman
            $data['peminjaman']['company_id'] = $this->company_id;
            $data['peminjaman']['register_number'] = $this->random_code_ops->generated_no_register_info_peminjaman();
            $data['peminjaman']['jamaah_id'] = $jamaah_id;
            $data['peminjaman']['biaya'] = $biaya;
            $data['peminjaman']['dp'] = 0;
            $data['peminjaman']['pool_id'] = $this->input->post('id');
            $data['peminjaman']['status_peminjaman'] = 'belum_lunas';
            $data['peminjaman']['tenor'] = $tenor;
            if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
               $data['peminjaman']['petugas'] = 'Administrator';
            } else {
               $data['peminjaman']['petugas'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $data['peminjaman']['last_update'] = date('Y-m-d H:i:s');
            $data['peminjaman']['input_date'] = date('Y-m-d H:i:s');
            # schema peminjaman
            $utang = $biaya;
            $perbulan = $utang / $tenor; // pembagian utang dengan tenor
            $floor_perbulan = ceil( $perbulan );
            $cost_reduction = $utang; // perubahan pada pengurangan utang
            for ( $i=1; $i <= $tenor ; $i++ ) { 
               # cost reduction
               if(  $cost_reduction <= $floor_perbulan ) {
                  $amount  = $cost_reduction;
               }else{
                  $amount = $floor_perbulan;
               }
               # mulai_pembayaran
               if( $i == 1 ){
                  $duedate = $mulai_pembayaran;
               }else{
                  $duedate = date('Y-m-d', strtotime($mulai_pembayaran. ' + '.($i-1).' months'));
               }
               
               # skema peminjaman
               $data['skema_peminjaman'][] = array('company_id' => $this->company_id, 
                                                   'term' => $i, 
                                                   'amount' => $amount, 
                                                   'due_date' => $duedate);

               $cost_reduction = $cost_reduction - $floor_perbulan;
            }
         }
         // akun
         $data_jurnal = array('company_id' => $this->company_id,
                              'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
                              'ref' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                              'ket' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                              'akun_debet' => '11010',
                              'akun_kredit' => '24000',
                              'saldo' => $this->text_ops->hide_currency($this->input->post('biaya_deposit')),
                              'periode_id' => 0,
                              'input_date' => date('Y-m-d H:i:s'),
                              'last_update'  => date('Y-m-d H:i:s'));
         # insert
         $insert = $this->model_deposit_paket_cud->insert_pembayaran_deposit_paket($data_pool_transaction, $data_deposit_transaction, $data, $data_jurnal);
         # insert proses
         if ( $insert['status'] == true) {
            # create session
            $this->session->set_userdata(
               array('cetak_invoice' => array(
                     'type' => 'cetak_kwitansi_deposit_paket',
                     'deposit_id' => $insert['id'])));
         }else{
            $error = 1;
            $error_msg = 'Proses insert data pembayaran deposit paket gagal dilakukan.';
         }
         # filter
         if ( $error == 0 ) {
            # create return
            $return = array(
               'error'   => false,
               'error_msg' => 'Proses deposit paket berhasil dilakukan.',
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

   # 
   function get_info_handover_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      /*
       Validation process
      */
      if ($this->form_validation->run()) {
         # get pool id
         $pool_id = $this->input->post('id');
         # nomor transaksi
         $nomor_transaksi = $this->model_deposit_paket->gen_nomor_transaksi_handover_facilities();
         # list facilities
         $list = $this->model_deposit_paket->get_list_facilities();
         # value facilities
         $facilities = $this->model_deposit_paket->get_value_facilities($pool_id, $list);
         # filter
         if ( $nomor_transaksi != '' ) {
            # create return
            $return = array(
               'error'   => false,
               'error_msg' => 'Info handover berhasil ditemukan.',
               'data' => array('nomor_transaksi' => $nomor_transaksi,
                               'riwayat_handover' => $facilities['riwayat_handover'],
                               'sisa' => $facilities['sisa'],
                               'list' => $list),
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Info total pembayaran deposit paket gagal ditemukan.',
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

   function _ck_fasilitas_deposit_paket($fasilitas_id){
      if( ! $this->model_deposit_paket->check_fasilitas_deposit_paket_ID($fasilitas_id) ) {
         $this->form_validation->set_message('_ck_fasilitas_deposit_paket', 'Fasilitas ID tidak ditemukan.');
         return FALSE;
      }else{
         $pool_id = $this->input->post('id');
         if( $this->model_deposit_paket->check_fasilitas_was_checked($pool_id, $fasilitas_id) ){
            $this->form_validation->set_message('_ck_fasilitas_deposit_paket', 'Fasilitas ID sudah diserahkan sebelumnya.');
            return FALSE;
         }else{
            return TRUE;
         }
      }
   }

   function _ck_nomor_transaksi_handover_fasilitas_deposit_paket($nomor_transaksi){
      if( $this->model_deposit_paket->check_nomor_transaksi_fasilitas($nomor_transaksi) ) {
         $this->form_validation->set_message('_ck_nomor_transaksi_handover_fasilitas_deposit_paket', 'Nomor transaksi handover fasilitas sudah terdaftar di pangkalan data.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function proses_handover_fasilitas_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool Id<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      $this->form_validation->set_rules('nomor_transaksi', '<b>Nomor Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_transaksi_handover_fasilitas_deposit_paket');
      # Fee Agen
      if( $this->input->post('fasilitas')){
         foreach ($this->input->post('fasilitas') as $key => $value) {
            $this->form_validation->set_rules("fasilitas[" . $key . "]", "Fasilitas", 'trim|xss_clean|min_length[1]|callback__ck_fasilitas_deposit_paket');
         }
      }
      $this->form_validation->set_rules('nama_penerima', '<b>Nama Penerima<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('no_identitas', '<b>Nomor Identitas Penerima<b>', 'trim|required|xss_clean|min_length[1]');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         # get fasilitas
         $fasilitas = $this->input->post('fasilitas');
         # count fasilitas
         if( count($fasilitas) > 0 ){
            $data = array();
            if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
               $officer = "Administrator";
            } else {
               $officer = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            foreach ($fasilitas as $key => $value) {
               $data[] = array('invoice' => $this->input->post('nomor_transaksi'),
                               'pool_id' => $this->input->post('id'),
                               'company_id' => $this->company_id,
                               'facilities_id' => $value,
                               'officer' => $officer,
                               'receiver_name' => $this->input->post('nama_penerima'),
                               'receiver_identity' => $this->input->post('no_identitas'),
                               'date_transaction' => date('Y-m-d H:i:s'));
            }
            if( ! $this->model_deposit_paket_cud->insert_handover_deposit_paket($this->input->post('nomor_transaksi'), $data) ) {
               $error = 1;
               $error_msg = 'Proses insert handover deposit paket gagal dilakukan.';
            }
         }else{
            $error = 1;
            $error_msg = 'Untuk melanjutkan proses ini, anda wajib menambahkan minimal 1 fasilitas';
         }
         # filter
         if ( $error == 0 ) {
            $this->session->set_userdata(array('cetak_invoice' => array(
               'type' => 'cetak_handover_fasilitas_deposit_paket',
               'invoice' => $this->input->post('nomor_transaksi')
            )));
            # create return
            $return = array(
               'error'   => false,
               'error_msg' => 'Proses handover paket berhasil dilakukan.',
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

   # delete pool deposit paket
   function delete_pool_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pool Id<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_otoritas|callback__ck_pool_id');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         # pool id
         $pool_id = $this->input->post('id');
         # get nomor_transaction 
         $nomor_transaksi = $this->model_deposit_paket->get_nomor_transaction_by_pool_id($this->input->post('id')); 
         # filter
         if ( $this->model_deposit_paket_cud->delete_deposit_paket($pool_id, $nomor_transaksi) ) {
            # create return
            $return = array(
               'error'   => false,
               'error_msg' => 'Proses handover paket berhasil dilakukan.',
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

   function _ck_invoice_deposit_paket($invoice){
      if( ! $this->model_deposit_paket->check_invoice_deposit_paket($invoice) ){
         $this->form_validation->set_message('_ck_invoice_deposit_paket', 'Nomor invoice tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function cetak_kwitansi_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_deposit_paket');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         # pool id
         $invoice = $this->input->post('invoice');
         # deposit id
         $deposit_id =$this->model_deposit_paket->get_deposit_paket_by_invoice($invoice);
         # filter
         if ( $deposit_id != 0  ) {
            # create session
            $this->session->set_userdata(
               array('cetak_invoice' => array(
                  'type' => 'cetak_kwitansi_deposit_paket',
                  'deposit_id' => $deposit_id
            )));
            # create return
            $return = array(
               'error'   => false,
               'error_msg' => 'Sukses.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Gagal',
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

   function _ck_invoice_handover_deposit_paket($invoice){
      if( ! $this->model_deposit_paket->check_invoice_handover_deposit_paket($invoice) ){
         $this->form_validation->set_message('_ck_invoice_deposit_paket', 'Nomor invoice tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }
   }


   function cetak_kwitansi_handover_fasilitas_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_handover_deposit_paket');
     /*
      Validation process
     */
     if ($this->form_validation->run()) {
        # pool id
        $invoice = $this->input->post('invoice');
        # filter
        if ( $invoice != ''  ) {
           # create session
           $this->session->set_userdata(array('cetak_invoice' => array(
              'type' => 'cetak_handover_fasilitas_deposit_paket',
              'invoice' => $invoice
           )));
           # create return
           $return = array(
             'error'   => false,
             'error_msg' => 'Sukses.',
             $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
           );
        } else {
           $return = array(
             'error'   => true,
             'error_msg' => 'Gagal',
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


   function delete_transaksi_handover_fasilitas_deposit_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('invoice', '<b>Invoice<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_otoritas|callback__ck_invoice_handover_deposit_paket');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         # pool id
         $invoice = $this->input->post('invoice');
         # filter
         if ( $this->model_deposit_paket_cud->delete_transaksi_handover_fasilitas_deposit_paket($invoice) ) {
           # create return
            $return = array(
               'error'   => false,
               'error_msg' => 'Sukses.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Gagal',
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

   function download_manifest(){

      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('filterTransaksi',   '<b>filter Transaksi<b>',    'trim|required|xss_clean|min_length[1]|in_list[belum,sudah]');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         # search
         $search = $this->input->post('search');
         $filterTransaksi = $this->input->post('filterTransaksi');
         # set session
         $this->session->set_userdata(array('download_to_excel' => 
                                      array('type' => 'download_manifest_tabungan_umrah',
                                             'filter' => array('search' => $search, 
                                                               'filterTransaksi' => $filterTransaksi))));
         # return
         $return = array(
            'error'  => false,
            'error_msg' => 'Proses download manifes tabungan umrah berhasil dilakukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );

         // # pool id
         // $invoice = $this->input->post('invoice');
         // # filter
         // if ( $this->model_deposit_paket_cud->delete_transaksi_handover_fasilitas_deposit_paket($invoice) ) {
         //   # create return
         //    $return = array(
         //       'error'   => false,
         //       'error_msg' => 'Sukses.',
         //       $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         //    );
         // } else {
         //    $return = array(
         //       'error'   => true,
         //       'error_msg' => 'Gagal',
         //       $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         //    );
         // }



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

      // # echo
      // echo json_encode($return);
   }


   function check_deposit_jamaah() {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('jamaah_id',   '<b>Search<b>',    'trim|xss_clean|min_length[1]|callback__ck_jamaah_id');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         // check to database
         $check = $this->model_deposit_paket->check_saldo_deposit_jamaah( $this->input->post('jamaah_id') );
         if( $check['status'] == false ) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Jamaah Tidak Memiliki Deposit',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'data' => $check['saldo'],
               'error'   => false,
               'error_msg' => 'Sukses',
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

   // get info refund
   function get_info_refund(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id',   '<b>ID Tabungan<b>',    'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         $error = 0;
         # generated invoice
         $nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
         # total tabungan 
         $total_tabungan = $this->model_deposit_paket->get_total_tabungan($this->input->post('id'));
         # filter
         if ($error != 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data info  refund tabungan tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data info refund tabungan berhasil ditemukan.',
               'data' => array(
                  'nomor_transaksi' => $nomor_transaksi,
                  'total_tabungan' => $total_tabungan
               ),
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

   // refund tabungan
   function refund_tabungan(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Tabungan Umrah<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pool_id');
      $this->form_validation->set_rules('nomor_transaksi', '<b>Nomor Transaksi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_nomor_transaksi');
      $this->form_validation->set_rules('refund', '<b>Refund<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_currency_not_null');
      $this->form_validation->set_rules('batal_berangkat', '<b>Batal Berangkat Umrah<b>', 'trim|xss_clean|min_length[1]|in_list[batal]');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         # personal id
         $personal_id = $this->model_deposit_paket->get_personal_id_by_pool_id($this->input->post('id'));
         # data
         $data['deposit_transaction'] = array();
         $data['deposit_transaction']['nomor_transaction'] = $this->input->post('nomor_transaksi');
         $data['deposit_transaction']['company_id'] = $this->company_id;
         $data['deposit_transaction']['personal_id'] = $personal_id;
         $data['deposit_transaction']['debet'] = 0;
         $data['deposit_transaction']['kredit'] = $this->text_ops->hide_currency( $this->input->post('refund') );
         $data['deposit_transaction']['transaction_requirement'] = 'paket_deposit';
         # penerima
         if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
           $data['deposit_transaction']['approver'] = "Administrator";
         } else {
           $data['deposit_transaction']['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $data['deposit_transaction']['info'] = ' Refund ';
         $data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
         $data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');
         # data pool transaction
         $data['pool_deposit_transaction']['company_id'] = $this->company_id;
         $data['pool_deposit_transaction']['pool_id'] = $this->input->post('id');
         $data['pool_deposit_transaction']['transaction_status'] = 'refund';
         # data pool
         if( $this->input->post('batal_berangkat') == 'batal' ) {
            $data['pool']['batal_berangkat'] = 'ya';
            $data['pool']['active'] = 'non_active';

            // get semua yang telah dibayar 
            $telah_bayar = $this->model_deposit_paket->get_telah_bayar_by_pool_id( $this->input->post('id') );
            // fee agen
            $fee_agen = $this->model_deposit_paket->get_fee_agen_by_pool_id( $this->input->post('id') );
            // pendapatan adalah yang telah dibayar dikurang yang direfund
            $pendapat = $telah_bayar - ( $fee_agen + $this->text_ops->hide_currency( $this->input->post('refund') ) );
            // akun
            // pendapatan
            $data['jurnal'][] = array('company_id' => $this->company_id,
                                 'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
                                 'ref' => 'Pendapatan Refund Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'ket' => 'Pendapatan Refund Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'akun_debet' => '11010',
                                 'akun_kredit' => '41000',
                                 'saldo' => $pendapat,
                                 'periode_id' => 0,
                                 'input_date' => date('Y-m-d H:i:s'),
                                 'last_update'  => date('Y-m-d H:i:s'));
            // refund tabungan
            $data['jurnal'][] = array('company_id' => $this->company_id,
                                 'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
                                 'ref' => 'Refund Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'ket' => 'Refund Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'akun_debet' => '24000',
                                 'akun_kredit' => '11010',
                                 'saldo' => $telah_bayar,
                                 'periode_id' => 0,
                                 'input_date' => date('Y-m-d H:i:s'),
                                 'last_update'  => date('Y-m-d H:i:s'));
         }else{
            // akun
            $data['jurnal'][] = array('company_id' => $this->company_id,
                                 'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
                                 'ref' => 'Refund Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'ket' => 'Refund Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
                                 'akun_debet' => '24000',
                                 'akun_kredit' => '11010',
                                 'saldo' => $this->text_ops->hide_currency($this->input->post('refund')),
                                 'periode_id' => 0,
                                 'input_date' => date('Y-m-d H:i:s'),
                                 'last_update'  => date('Y-m-d H:i:s'));
         }

         // filter insert 
         if( ! $this->model_deposit_paket_cud->insert_refund_tabungan_umrah( $this->input->post('id'), $data ) ) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Proses refund gagal dilakukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            # create session
            $this->session->set_userdata(array('cetak_invoice' => array(
             'type' => 'cetak_kwitansi_refund_tabungan',
             'nomor_transaction' => $data['deposit_transaction']['nomor_transaction']
            )));
            $return = array(
               'error'   => false,
               'error_msg' => 'Proses refund berhasil dilakukan.',
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

   // cetak kwitansi refund tangungan
   function cetak_kwitansi_refund_tabungan(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('invoice', '<b>Invoice Tabungan Umrah<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_invoice_deposit_paket');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         # create session
         $this->session->set_userdata(array('cetak_invoice' => array(
          'type' => 'cetak_kwitansi_refund_tabungan',
          'nomor_transaction' => $this->input->post('invoice')
         )));
         $return = array(
            'error'   => false,
            'error_msg' => 'Proses cetak kwitansi refund tabungan berhasil dilakukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
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
