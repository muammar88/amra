<?php

/**
 *  -----------------------
 *	Model superman
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_superman extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->userdata = $this->session->userdata('superman');
      $this->error = 0;
      $this->write_log = 1;
   }

   // check username
   function check_username_superman( $username ){
      $this->db->select('fullname, username, password')
         ->from('admin_account')
         ->where('username', $username);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list = array('fullname' => $row->fullname, 
                          'username' => $row->username, 
                          'password' => $row->password);
         }
      }
      return $list;
   }

   // get module access
   function get_module_access(){
      //
      $this->db->select('id, name, icon, path, description')
               ->from('admin_tab');
      $q = $this->db->get();
      $tab = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $tab[$rows->id] = array('name' => $rows->name, 
                                    'icon' => $rows->icon, 
                                    'path' => $rows->path, 
                                    'description' => $rows->description);
         }
      } 

      $this->db->select('id, module_name, module_path, module_icon, tab')
               ->from('admin_modules');
      $q = $this->db->get();
      $list = array();
      // filter
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows ) {
            $access = array();
            $access['id'] = $rows->id;
            $access['module_name'] = $rows->module_name;
            $access['module_path'] = $rows->module_path;
            $access['module_icon'] = $rows->module_icon;
            if( $rows->module_path != '#') {
               $arrTab = array();
               if( $rows->tab != '' ) {
                  foreach ( unserialize( $rows->tab ) as $key => $value ) {
                     if ( array_key_exists( $value, $tab ) ) {
                        $arrTab[$value] = array('name' => $tab[$value]['name'], 
                                                'icon' => $tab[$value]['icon'], 
                                                'path' => $tab[$value]['path'], 
                                                'description' => $tab[$value]['description']);
                     }
                  }
               }
               // tab
               $access['tab'] = $arrTab;
            }else{
               $this->db->select('id, submodule_name, submodule_path, tab')
                        ->from('admin_submodules')
                        ->where('module_id', $rows->id);
               $r = $this->db->get();
               $submodul_access = array();
               if( $r->num_rows() > 0 ) {
                  foreach ( $r->result() as $row ) {
                     $arrTab = array();
                     if( $row->tab != '' ) {
                        foreach ( unserialize( $row->tab ) as $key => $value ) {
                           if ( array_key_exists( $value, $tab ) ) {
                              $arrTab[$value] = array('name' => $tab[$value]['name'], 
                                                      'icon' => $tab[$value]['icon'], 
                                                      'path' => $tab[$value]['path'], 
                                                      'description' => $tab[$value]['description']);
                           }
                        }
                     }
                     // code...
                     $submodul_access[$row->id] = array('id' => $row->id, 
                                                         'name' => $row->submodule_name,
                                                         'path' => $row->submodule_path, 
                                                         'tab' => $arrTab);
                  }
               }         
               $access['submodule'] = $submodul_access;   
            }
            // list            
            $list[] = $access;
         }
      }
      // echo "<br>===========<br>";
      // print_r($list);
      // echo "<br>===========<br>";
      return $list;   
   }

   // get modul submodul tab
   function get_modul_submodul_tab( $modul_access ){
      # get all tab
      $all_tab = $this->get_all_tab();
      $modulTab = array();
      $submodulTab = array();
      foreach ($modul_access as $key => $value) {
         if( $value['module_path'] != '#'){
            if( isset( $value['tab']  ) && $value['tab'] != '' ){
               $modulTab[$value['module_path']] = $value['tab'];
            }
         }else{
            foreach ($value['submodule'] as $keySUB => $valueSUB) {
               if( isset($valueSUB['tab']) && $valueSUB['tab'] != '' ){
                  $submodulTab[ $valueSUB['path'] ] = $valueSUB['tab'];
               }
            }
         }
      }
      return array('modul_tab' => $modulTab, 'submodul_tab' => $submodulTab);
   }


   # model get all tab
   function get_all_tab(){
      $this->db->select('id, name, icon, path, description')
         ->from('admin_tab');
      $q  = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $feedBack[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
         }
      }
      return $feedBack;
   }

   function get_total_perusahaan(){
      $this->db->select('id')
         ->from('company');
      $q = $this->db->get();
      return $q->num_rows();   
   }

   // get saldo perusahaan
   function get_saldo_perusahaan(){
      $this->db->select('saldo')
               ->from('company');
      $q = $this->db->get();
      $saldo = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $saldo = $saldo + $row->saldo;
         }
      }
      return $saldo;
   }

   function get_laba_amra(){
      $this->db->select('application_price, server_price')
         ->from('ppob_transaction_history')
         ->where('status', 'success');
      $q = $this->db->get();
      $laba_aplikasi = 0;
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $laba_aplikasi = $laba_aplikasi + ($rows->application_price - $rows->server_price);
         }
      } 
      return $laba_aplikasi;
   }

   function get_total_daftar_perusahaan($search){
      $this->db->select('id')
               ->from('company');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar perusahaan
   function get_index_daftar_perusahaan($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, code, name, company_type, start_date_subscribtion, end_date_subscribtion, saldo')
               ->from('company');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'code' => $row->code,
                            'name' => $row->name,
                            'company_type' => $row->company_type,
                            'start_date_subscribtion' => $row->start_date_subscribtion,
                            'end_date_subscribtion' => $row->end_date_subscribtion,
                            'saldo' => $row->saldo);
         }
      }
      return $list;
   }

   function get_total_daftar_ppob_superman($search){
      $this->db->select('id')
               ->from('ppob_transaction_history');
               // ->where('created_at == NOW()');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('transaction_code', $search)
            ->or_like('nomor_tujuan', $search)
            ->or_like('product_code', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_ppob_superman($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, transaction_code, nomor_tujuan, product_code, server_price, application_price, status, created_at')
               ->from('ppob_transaction_history');
               // ->where('created_at == NOW()');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('transaction_code', $search)
            ->or_like('nomor_tujuan', $search)
            ->or_like('product_code', $search)
            ->group_end();
      }
      $this->db->order_by('created_at', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {

            $list[] = array('id' => $row->id, 
                            'transaction_code' => $row->transaction_code, 
                            'nomor_tujuan' => $row->nomor_tujuan, 
                            'product_code' => $row->product_code, 
                            'status' => $row->status, 
                            'server_price' => $row->server_price, 
                            'application_price' => $row->application_price, 
                            'created_at' => $row->created_at);
         }
      }
      return $list;
   }

   // get total daftar request tambah saldo perusahaan
   function get_total_daftar_request_tambah_saldo($search){
      $this->db->select('cst.id')
               ->from('company_saldo_transaction AS cst')
               ->join('company AS c', 'cst.company_id=c.id', 'inner')
               ->where('cst.request_type', 'deposit');
               // ->where('cst.status', 'process');
               // ->where('created_at == NOW()');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('c.code', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_request_tambah_saldo($limit = 6, $start = 0, $search = ''){
      $this->db->select('cst.id, c.code, c.name, c.saldo AS saldo_perusahaan, cst.saldo, cst.last_update, cst.status')
               ->from('company_saldo_transaction AS cst')
               ->join('company AS c', 'cst.company_id=c.id', 'inner')
               ->where('cst.request_type', 'deposit');
               // ->where('cst.status', 'process');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('c.code', $search)
            ->group_end();
      }
      $this->db->order_by('cst.last_update', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list[] =array('id' => $row->id, 
                           'code' => $row->code, 
                           'name' => $row->name, 
                           'saldo_perusahaan' => $row->saldo_perusahaan, 
                           'saldo' => $row->saldo, 
                           'status' => $row->status,
                           'transaction_date' => $row->last_update);
         }
      }
      return $list;
   }

   // check request tambah saldo
   function check_request_tambah_saldo($id){
      $this->db->select('id')
               ->from('company_saldo_transaction')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   // get info perusahaan
   function get_info_perusahaan( $id ){
      $this->db->select('c.saldo, cst.company_id, cst.saldo AS saldo_ditambah')
               ->from('company_saldo_transaction AS cst')
               ->join('company AS c', 'cst.company_id=c.id', 'inner')
               ->where('cst.id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list = array('saldo_perusahaan' => $row->saldo, 
                          'saldo_ditambah' => $row->saldo_ditambah,
                          'company_id' => $row->company_id);
         }
      }
      return $list;
   }

   // get list perusahaan
   function get_list_perusahaan(){
      $this->db->select('id, code, name')
               ->from('company');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $list[$rows->id] = $rows->name ." -> (".$rows->code.") "; 
         }
      }
      return $list;
   }

      // get list perusahaan
   function get_list_perusahaan_limited(){
      $this->db->select('id, code, name')
               ->from('company')
               ->where('company_type', 'limited');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $list[$rows->id] = $rows->name ." -> (".$rows->code.") "; 
         }
      }
      return $list;
   }

   // check id perusahaan
   function check_id_perusahaan($id){
      $this->db->select('id')->from('company')->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }


   function get_saldo_sekarang_perusahaan( $id ){
      $this->db->select('saldo')
               ->from('company')
               ->where('id', $id);
      $saldo  = 0;
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $saldo = $rows->saldo;
         }
      }    
      return $saldo;     
   }

   // get total daftar request tambah waktu berlangganan
   function get_total_daftar_request_tambah_waktu_berlangganan($search){
      $this->db->select('sph.id')
               ->from('subscribtion_payment_history AS sph')
               ->join('company AS c', 'sph.company_id=c.id', 'inner');
               // ->where('sph.transaction_date = NOW()');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('sph.order_id', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar request tambah waktu berlangganan
   function get_index_daftar_request_tambah_waktu_berlangganan($limit = 6, $start = 0, $search = ''){
      $this->db->select('c.name, c.code, sph.id, sph.order_id, sph.payment_status, sph.duration, sph.start_date_subscribtion, 
                         sph.pay_per_month, sph.total,
                         sph.end_date_subscribtion, sph.transaction_date, sph.last_update')
               ->from('subscribtion_payment_history AS sph')
               ->join('company AS c', 'sph.company_id=c.id', 'inner');
               // ->where('sph.transaction_date = NOW()');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('sph.order_id', $search)
            ->group_end();
      }
      $this->db->order_by('sph.transaction_date', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list[] =array('id' => $row->id, 
                           'code' => $row->code, 
                           'name' => $row->name, 
                           'order_id' => $row->order_id,
                           'status' => $row->payment_status,
                           'pay_per_month' => $row->pay_per_month,
                           'total' => $row->total,
                           'duration' => $row->duration,
                           'start_date_subscribtion' => $row->start_date_subscribtion,
                           'end_date_subscribtion' => $row->end_date_subscribtion,
                           'transaction_date' => $row->transaction_date,
                           'last_update' => $row->last_update);
         }
      }
      return $list;
   }

   function check_id_request($id){
      $this->db->select('id')
               ->from('subscribtion_payment_history')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // get info request waktu berlangganan
   function get_info_request_waktu_berlangganan( $id ) {
      $this->db->select('*')
               ->from('subscribtion_payment_history')
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list['id'] = $row->id;
            $list['order_id'] = $row->order_id;
            $list['start_date_subscribtion'] = $row->start_date_subscribtion;
            $list['end_date_subscribtion'] = $row->end_date_subscribtion;
            $list['company_id'] = $row->company_id;
         }
      }
      return $list;         
   }

   // get info deposit subscription
   function get_info_deposit_subscription( $order_id ) {
      $this->db->select('id')
               ->from('company_saldo_transaction')
               ->where('request_type', 'payment_subscription')
               ->like('ket', $order_id);
      $q = $this->db->get();
      $id = '';
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $id = $row->id;
         }
      }
      return $id;
   }

   // get info company
   function get_info_company($id_perusahaan){
      $this->db->select('id, start_date_subscribtion, end_date_subscribtion')
               ->from('company')
               ->where('id', $id_perusahaan);
      $list = array();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list['id'] = $rows->id;
            $list['start_date_subscribtion'] = $rows->start_date_subscribtion;
            $list['end_date_subscribtion'] = $rows->end_date_subscribtion;
         }
      }
      return $list;
   }


   function get_total_list_perusahaan($search){
      $this->db->select('id')
               ->from('company');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('name', $search)
            ->or_like('code', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }



   // get index daftar perusahaan
   function get_index_list_perusahaan($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, code, name, whatsapp_number, email, company_type, start_date_subscribtion, end_date_subscribtion, saldo, address, city, pos_code')
               ->from('company');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('name', $search)
            ->or_like('code', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'code' => $row->code,
                            'name' => $row->name,
                            'address' => $row->address,
                            'city' => $row->city, 
                            'pos_code' => $row->pos_code,
                            'whatsapp_number' => $row->whatsapp_number,
                            'email' => $row->email,
                            'company_type' => $row->company_type,
                            'start_date_subscribtion' => $row->start_date_subscribtion,
                            'end_date_subscribtion' => $row->end_date_subscribtion,
                            'saldo' => $row->saldo);
         }
      }
      return $list;
   }

   function generated_company_code(){
      $feedBack = false;
      $rand = '';
      do {
         $rand = $this->text_ops->random_num(7);
         $q = $this->db->select('code')
                      ->from('company')
                      ->where('code', $rand)
                      ->get();
         if($q->num_rows() == 0){
            $feedBack = true;
         }
      } while ($feedBack == false);
      return $rand;
   }

   // check perusahaan id
   function check_perusahaan_id( $id ){
      $this->db->select('id')
               ->from('company')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // check kode perusahaan
   function check_kode_perusahaan( $kode, $id = 0 ) {
      $this->db->select('id')
               ->from('company')
               ->where('code', $kode);
      if( $id != 0 ) {
         $this->db->where('id != ', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // nomor whatsapp 
   function check_nomor_whatsapp( $whatsapp_number, $id = 0 ){
      $this->db->select('id')
               ->from('company')
               ->where('whatsapp_number', $whatsapp_number);
      if( $id != 0 ) {
         $this->db->where('id != ', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // check email perusahaan
   function check_email_perusahaan( $email, $id = 0 ) {
      $this->db->select('id')
               ->from('company')
               ->where('email', $email);
      if( $id != 0 ) {
         $this->db->where('id != ', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // get value edit perusahaan
   function get_value_edit_perusahaan($id){
      $this->db->select('id, code, name, whatsapp_number, email, company_type, start_date_subscribtion, end_date_subscribtion, saldo ')
               ->from('company')
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $row ) {
            $list['id'] = $row->id;
            $list['code'] = $row->code;
            $list['name'] = $row->name;
            $list['whatsapp_number'] = $row->whatsapp_number;
            $list['email'] = $row->email;
            $list['company_type'] = $row->company_type;
            $list['start_date_subscribtion'] = $row->start_date_subscribtion;
            $list['end_date_subscribtion'] = $row->end_date_subscribtion;
            $list['saldo'] = $row->saldo;
         }
      }
      return $list;
   }


   function total_ppob_costumer(){
      $this->db->select('id')
               ->from('ppob_costumer');
      $q = $this->db->get();
      return $q->num_rows();   
   }

   function get_saldo_pelanggan(){
      $this->db->select('saldo')
               ->from('ppob_costumer');
      $q = $this->db->get();
      $saldo = 0;
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $saldo = $saldo + $rows->saldo;
         }
      }
      return $saldo;
   }


   function get_total_daftar_request_tambah_saldo_superman($search, $status){
      $this->db->select('r.id')
               ->from('request_tambah_saldo_company AS r')
               ->join('company AS c', 'r.company_id=c.id', 'inner')
               ->where('r.status', $status)
               ->where('r.status_kirim', 'sudah_dikirim');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('r.kode', $search)
            ->or_like('c.code', $search)
            ->or_like('c.name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar perusahaan
   function get_index_daftar_request_tambah_saldo_superman($limit = 6, $start = 0, $search = '', $status){
      $this->db->select('r.id, c.name, c.code, r.kode, r.bank, r.nomor_akun_bank, r.nama_akun_bank, r.biaya, r.kode_biaya, r.status, r.waktu_kirim')
               ->from('request_tambah_saldo_company AS r')
               ->join('company AS c', 'r.company_id=c.id', 'inner')
               ->where('r.status', $status)
               ->where('r.status_kirim', 'sudah_dikirim');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('r.kode', $search)
            ->or_like('c.code', $search)
            ->or_like('c.name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 
                            'kode_perusahaan' => $rows->code,
                            'nama_perusahaan' => $rows->name, 
                            'nomor_akun_bank' => $rows->nomor_akun_bank, 
                            'nama_akun_bank' => $rows->nama_akun_bank,
                            'kode' => $rows->kode, 
                            'nama_bank' => $rows->bank,
                            'nominal' => ($rows->biaya + $rows->kode_biaya), 
                            'status' => $rows->status, 
                            'waktu_kirim' => $rows->waktu_kirim) ;

         }
      }
      return $list;
   }

   // check request id tambah saldo
   function check_request_id_tambah_saldo($id){
      $this->db->select('id')
               ->from('request_tambah_saldo_company')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }       
   }

   // get saldo request tambah saldo
   function get_info_saldo_request_tambah_saldo($id){
      $this->db->select('biaya, kode_biaya, company_id')
               ->from('request_tambah_saldo_company')
               ->where('id', $id);
      $q = $this->db->get();
      $info = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
           $info['saldo'] = $rows->biaya + $rows->kode_biaya;
           $info['company_id'] = $rows->company_id;
         }
      }
      return $info;
   }


   function get_saldo_company( $company_id ){
      $this->db->select('saldo')
               ->from('company')
               ->where('id', $company_id);
      $q = $this->db->get();
      $saldo = 0;
      if( $q->num_rows() > 0 ) { 
         foreach ( $q->result() as $rows ) {
            $saldo = $rows->saldo;
         }
      }
      return $saldo;
   }

}