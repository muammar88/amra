<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Daftar Peminjaman Controller
 *	Created by Muammar Kadafi
 */
class Daftar_peminjaman extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load user model
      $this->load->model('Model_daftar_peminjaman', 'model_daftar_peminjaman');
      # model daftar peminjaman cud
      $this->load->model('Model_daftar_peminjaman_cud', 'model_daftar_peminjaman_cud');
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


   # server daftar peminjaman
   function server_daftar_peminjaman(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search', '<b>Search<b>', 'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage', '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber', '<b>PageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('status', '<b>Status<b>',  'trim|required|xss_clean|min_length[1]|in_list[belum_lunas,lunas]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $search  = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $status = $this->input->post('status');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total   = $this->model_daftar_peminjaman->get_total_daftar_peminjaman($search, $status);
         $list    = $this->model_daftar_peminjaman->get_index_daftar_peminjaman($perpage, $start_at, $search, $status);
         if ($total == 0) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Daftar peminjaman tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Daftar peminjaman berhasil ditemukan.',
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

   # info peminjaman
   function info_peminjaman() {
      # register number
      $register_number = $this->random_code_ops->generated_no_register_info_peminjaman();
      # get daftar jamaah
      $daftar_jamaah = $this->model_daftar_peminjaman->get_daftar_jamaah_peminjaman();
      # filter
      if ( $daftar_jamaah == '') {
         $return = array(
            'error'  => true,
            'error_msg' => 'Info jamaah gagal ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'  => false,
            'error_msg' => 'Info jamaah berhasil ditemukan.',
            'data' => array('jamaah' => $daftar_jamaah, 'register_number' => $register_number),
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }


   function _ck_register_number($register_number){
      if( $this->model_daftar_peminjaman->check_register_number( $register_number ) ){
         $this->form_validation->set_message('_ck_register_number', 'Nomor Register Sudah Terdaftar.');
         return FALSE;
      }else{
         return TRUE;
      }
   }


   function _ck_jamaah_id( $jamaah_id ){
      if( $jamaah_id != 0 ){
         if( ! $this->model_daftar_peminjaman->check_jamaah_id( $jamaah_id )  ) {
            $this->form_validation->set_message('_ck_jamaah_id', 'Nama Jamaah tidak terdaftar dipangkalan data.');
            return FALSE;
         }else{
            return TRUE;
         }
      }else{
         $this->form_validation->set_message('_ck_jamaah_id', 'Anda wajib memilih salah satu nama jamaah.');
         return FALSE;
      }
   }


   function _not_null_currency(){
      if( $this->input->post('biaya') ) {
         if( $this->text_ops->hide_currency( $this->input->post('biaya') ) == 0 ) {
            $this->form_validation->set_message('_not_null_currency', 'Biaya tidak boleh kosong.');
            return FALSE;
         }else{
            return TRUE;
         }
      }else{
         $this->form_validation->set_message('_not_null_currency', 'Biaya tidak boleh kosong.');
         return FALSE;
      }
   }


   function _ck_tenor($tenor){
      if( $tenor > 0 ){
         return TRUE;
      }else{
          $this->form_validation->set_message('_ck_tenor', 'Tenor tidak boleh nol.');
         return FALSE;
      }
   }

   function _ck_dp() {
      if( $this->input->post('dp') ) {
         $biaya = $this->text_ops->hide_currency($this->input->post('biaya'));
         $dp = $this->text_ops->hide_currency( $this->input->post('dp') );
         # filter
         if( $dp >= $biaya ){
            $this->form_validation->set_message('_ck_dp', 'DP tidak boleh lebih besar atau sama dengan biaya peminjaman.');
            return FALSE;
         }else{
            return TRUE;
         }
      }else{
         return TRUE;
      }
   }

   # proses add update peminjaman
   function proses_addupdate_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('register_number', '<b>Nomor Register<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_register_number');
      $this->form_validation->set_rules('jamaah', '<b>Jamaah<b>',  'trim|required|xss_clean|min_length[1]|callback__ck_jamaah_id');
      $this->form_validation->set_rules('biaya', '<b>Biaya<b>',  'trim|required|xss_clean|min_length[1]|callback__not_null_currency');
      $this->form_validation->set_rules('mulai_pembayaran', '<b>Mulai Pembayaran Pertama<b>',  'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('dp', '<b>DP<b>',  'trim|required|xss_clean|min_length[1]|callback__ck_dp');
      $this->form_validation->set_rules('tenor', '<b>Tenor<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_tenor');
      $this->form_validation->set_rules('sudah_berangkat', '<b>Sudah Berangkat<b>', 'trim|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # receive
         $biaya = $this->text_ops->hide_currency($this->input->post('biaya'));
         $dp = $this->text_ops->hide_currency($this->input->post('dp')); // 
         $mulai_pembayaran = $this->input->post('mulai_pembayaran'); // tanggal mulai pembayaran
         $tenor = $this->input->post('tenor'); // tenor
         $jamaah = $this->input->post('jamaah');
         $register_number = $this->input->post('register_number');
         $data = array();
         # data peminjaman
         $data['peminjaman']['company_id'] = $this->company_id;
         $data['peminjaman']['register_number'] = $register_number;
         $data['peminjaman']['jamaah_id'] = $jamaah;
         $data['peminjaman']['biaya'] = $biaya;
         $data['peminjaman']['dp'] = $dp;
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
         $utang = $biaya - $dp;
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
         # pembayaran peminjaman
         if( $dp > 0 ) {
            $data['pembayaran_peminjaman']['company_id'] = $this->company_id;
            $data['pembayaran_peminjaman']['invoice'] = $this->random_code_ops->generated_no_invoice_pembayaran_peminjaman();
            $data['pembayaran_peminjaman']['bayar'] = $dp;
            $data['pembayaran_peminjaman']['status'] = 'dp'; 
            if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
               $data['pembayaran_peminjaman']['petugas'] = 'Administrator';
            } else {
               $data['pembayaran_peminjaman']['petugas'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $data['pembayaran_peminjaman']['transaction_date'] = date('Y-m-d H:i:s');
         }

         // $sudah_berangkat = $this->input->post('sudah_berangkat');
         # filter
         if(  ! $this->input->post('sudah_berangkat') ) {
            # get info jamaah
            $get_info_jamaah = $this->model_daftar_peminjaman->get_info_jamaah( $jamaah );
            # filter
            if( $get_info_jamaah['agen_id'] > 0 ) {
               # get fee agen
               $fee_agen  = $this->model_daftar_peminjaman->fee_keagenan_deposit_paket( $jamaah );
               # fee keagenan 
               $data['fee_keagenan']['company_id'] = $this->company_id;
               $data['fee_keagenan']['personal_id'] = $get_info_jamaah['personal_id'];
               $data['fee_keagenan']['input_date'] = date('Y-m-d');
               $data['fee_keagenan']['last_update'] = date('Y-m-d');
               # detail fee keagenan
               foreach ( $fee_agen as $key => $value ) {
                  $data['detail_fee_keagenan'][] = array('transaction_number' => $this->random_code_ops->number_transaction_detail_fee_keagenan(),
                                                         'company_id' => $this->company_id,
                                                         'agen_id' => $key,
                                                         'level_agen_id' => $value['level_agen_id'],
                                                         'fee' => $value['fee'],
                                                         'input_date' => date('Y-m-d H:i:s'),
                                                         'last_update' => date('Y-m-d H:i:s'));
               }
            }
            # pool
            $data['pool']['company_id'] = $this->company_id;
            $data['pool']['active'] = 'active';
            $data['pool']['input_date'] = date('Y-m-d H:i:s');
            $data['pool']['last_update'] = date('Y-m-d H:i:s');
            $data['pool']['jamaah_id'] = $jamaah;
            # data deposit transaction 
            $data['deposit_transaction']['nomor_transaction'] = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
            $data['deposit_transaction']['personal_id'] = $get_info_jamaah['personal_id'];
            $data['deposit_transaction']['company_id'] = $this->company_id;
            $data['deposit_transaction']['debet'] = $biaya;
            if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
               $data['deposit_transaction']['approver'] = "Administrator";
            } else {
               $data['deposit_transaction']['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $data['deposit_transaction']['transaction_requirement'] = 'paket_deposit';
            $data['deposit_transaction']['info'] = '';
            $data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
            $data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');
            # data pool deposit transaction 
            $data['pool_deposit_transaction']['company_id'] = $this->company_id;
         }

         # filter
         if ( ! $this->model_daftar_peminjaman_cud->insert_daftar_peminjaman( $data ) ) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Data proses peminjaman gagal disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            # create session
            $this->session->set_userdata( array('cetak_invoice' => array(
                                                'type' => 'cetak_kwitansi_peminjaman',
                                                'register_number' => $register_number)));
            $return = array(
               'error'  => false,
               'error_msg' => 'Data proses peminjaman berhasil disimpan.',
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

   function _ck_peminjaman_id($peminjaman_id){
      if( ! $this->model_daftar_peminjaman->check_peminjaman_id($peminjaman_id) ) {
         $this->form_validation->set_message('_ck_peminjaman_id', 'ID Peminjaman tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function info_skema_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_peminjaman_id');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get info skema peminjaman 
         $get_info_skema_peminjaman = $this->model_daftar_peminjaman->get_info_skema_peminjaman( $this->input->post('id') );
         # filter 
         if( count($get_info_skema_peminjaman) > 0 ){
            $return = array(
               'error'  => false,
               'error_msg' => 'Data skema peminjaman berhasi ditemukan.',
               'data' => $get_info_skema_peminjaman, 
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'  => true,
               'error_msg' => 'Data skema peminjaman gagal ditemukan.',
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

   function proses_addupdate_skema_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('peminjaman_id', '<b>Peminjaman ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_peminjaman_id');
      # amount
      foreach ( $this->input->post('amount') as $key => $value ) {
         $this->form_validation->set_rules("amount[" . $key . "]", "Amount", 'trim|xss_clean|min_length[1]');
      }
      # due date
      foreach ( $this->input->post('due_date') as $key => $value ) {
         $this->form_validation->set_rules("due_date[" . $key . "]", "Tanggal Jatuh Tempo", 'trim|xss_clean|min_length[1]');
      }
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get info skema peminjaman 
         $get_info_skema_peminjaman = $this->model_daftar_peminjaman->get_info_skema_peminjaman( $this->input->post('peminjaman_id') );
         $amount = $this->input->post('amount');
         $due_date = $this->input->post('due_date');
         $total_amount = 0;
         foreach ($amount as $key => $value) {
            $total_amount = $total_amount + $this->text_ops->hide_currency( $value );
         }
         # filter total utang == total amount
         if ( $get_info_skema_peminjaman['total_utang'] == $total_amount ){
            # receive data
            $data = array();
            foreach ($amount as $key => $value) {
               $data[$key] = array('amount' => $this->text_ops->hide_currency($value), 'due_date' => $due_date[$key]);
            }
            # update process
            if( ! $this->model_daftar_peminjaman_cud->update_skema_peminjaman($this->input->post('peminjaman_id'), $data) ){
               $error = 1;
               $error_msg = 'Proses update skema gagal dilakukan.';
            }
         }else{
            $error = 1;
            $error_msg = 'Total utang tidak sama dengan total Amount!!!';
         }
         # filter 
         if( $error == 1 ){
            $return = array(
               'error'  => true,
               'error_msg' => $error_msg,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );  
         }else{
            $return = array(
               'error'  => false,
               'error_msg' => 'Data Skema Cicilan Berhasil Dirubah.',
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


   function cetak_kwitansi_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('peminjaman_id', '<b>ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_peminjaman_id');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # register number
         $register_number = $this->model_daftar_peminjaman->get_register_number_peminjaman( $this->input->post('peminjaman_id'));
         # filter 
         if( $register_number != '' ){
              # create session
            $this->session->set_userdata( array('cetak_invoice' => array(
                                                'type' => 'cetak_kwitansi_peminjaman',
                                                'register_number' => $register_number)));

            $return = array(
               'error'  => false,
               'error_msg' => 'Data skema peminjaman berhasi ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'  => true,
               'error_msg' => 'Data skema peminjaman gagal ditemukan.',
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

   function _ck_pembayaran_peminjaman_id($id){
      if( ! $this->model_daftar_peminjaman->check_pembayaran_peminjaman_id($id) ){
         $this->form_validation->set_message('_ck_pembayaran_peminjaman_id', 'Pembayaran Peminjaman ID tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   # cetak kwitansi cicilan
   function cetak_kwitansi_cicilan(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_pembayaran_peminjaman_id');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get invoice
         $get_invoice = $this->model_daftar_peminjaman->get_invoice_pembayaran_peminjaman( $this->input->post('id') );
         # filter 
         if( $get_invoice == '' ){
            $return = array(
               'error'  => true,
               'error_msg' => $error_msg,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );  
         }else{
             # create session
            $this->session->set_userdata( array('cetak_invoice' => array(
                                                'type' => 'cetak_kwitansi_invoice_pembayaran_peminjaman',
                                                'invoice' => $get_invoice) ) );
            # return
            $return = array(
               'error'  => false,
               'error_msg' => 'Data Skema Cicilan Berhasil Dirubah.',
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


   function info_pembayaran_cicilan(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('peminjaman_id', '<b>ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_peminjaman_id');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # register number
         $data = array();
         $data['peminjaman_id'] = $this->input->post('peminjaman_id');
         $data['invoice'] = $this->random_code_ops->generated_no_invoice_pembayaran_peminjaman();;
         $data['sisa_utang'] = $this->model_daftar_peminjaman->get_sisa_pembayaran($this->input->post('peminjaman_id'));
         # filter 
         if( $error == 0 ){
            $return = array(
               'error'  => false,
               'error_msg' => 'Data skema peminjaman berhasi ditemukan.',
               'data' => $data,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'  => true,
               'error_msg' => 'Data skema peminjaman gagal ditemukan.',
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


   function _check_invoice_pembayaran_pinjaman($invoice){
      if( $this->model_daftar_peminjaman->check_invoice_pembayaran_pinjaman( $invoice ) ) {
         $this->form_validation->set_message('_check_invoice_pembayaran_pinjaman', 'Invoice sudah terdaftar dipangkalan data.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function proses_addupdate_pembayaran_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('peminjaman_id', '<b>ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_peminjaman_id');
      $this->form_validation->set_rules('invoice', '<b>Invoice<b>',   'trim|required|xss_clean|min_length[1]|callback__check_invoice_pembayaran_pinjaman');
      $this->form_validation->set_rules('biaya', '<b>Biaya Pembayaran<b>',   'trim|required|xss_clean|min_length[1]|callback__not_null_currency');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # register number
         $data = array();
         $data['invoice'] = $this->input->post('invoice');
         $data['peminjaman_id'] = $this->input->post('peminjaman_id');
         $data['bayar'] = $this->text_ops->hide_currency( $this->input->post('biaya') ) ;
         $data['status'] = 'cicilan';
         $data['transaction_date'] = date('Y-m-d H:i:s');
         $data['company_id'] = $this->company_id;
         if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
            $data['petugas'] = 'Administrator';
         } else {
            $data['petugas'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $info_pembayaran = $this->model_daftar_peminjaman->get_info_pembayaran( $this->input->post('peminjaman_id') ) ;
         $data_peminjaman = array();
         if( ( $info_pembayaran['total_bayar'] + $data['bayar'] ) == $info_pembayaran['total_biaya'] ) {
            $data_peminjaman['status_peminjaman'] = 'lunas';
            $data_peminjaman['last_update'] = date('Y-m-d H:i:s');
         }
         # filter 
         if( $this->model_daftar_peminjaman_cud->insert_pembayaran_peminjaman( $data, $data_peminjaman ) ) {
             # create session
            $this->session->set_userdata( array('cetak_invoice' => array(
                                                'type' => 'cetak_kwitansi_invoice_pembayaran_peminjaman',
                                                'invoice' => $this->input->post('invoice')) ) );
            $return = array(
               'error'  => false,
               'error_msg' => 'Data pembayaran peminjaman berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'  => true,
               'error_msg' => 'Data pembayaran peminjaman gagal disimpan.',
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

   function delete_cicilan_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('peminjaman_id', '<b>ID<b>',   'trim|required|xss_clean|min_length[1]|callback__ck_otoritas|callback__ck_peminjaman_id');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # filter 
         if( $this->model_daftar_peminjaman_cud->delete_cicilan_peminjaman( $this->input->post('peminjaman_id') ) ) {
            $return = array(
               'error'  => false,
               'error_msg' => 'Data pembayaran peminjaman berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'  => true,
               'error_msg' => 'Data pembayaran peminjaman gagal dihapus.',
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

   function download_excel_daftar_peminjaman(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('filter',   '<b>filter Transaksi<b>',    'trim|required|xss_clean|min_length[1]|in_list[belum_lunas,lunas]');
      /*
      Validation process
      */
      if ($this->form_validation->run()) {
         # search
         $search = $this->input->post('search');
         $filter = $this->input->post('filter');
         # set session
         $this->session->set_userdata(array('download_to_excel' => 
                                      array('type' => 'download_daftar_peminjaman',
                                             'filter' => array('search' => $search, 
                                                               'filter' => $filter))));
         # return
         $return = array(
            'error'  => false,
            'error_msg' => 'Proses download daftar peminjaman berhasil dilakukan.',
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