<?php
/**
*  -----------------------
*	Model airlines
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_peminjaman extends CI_Model
{
   private $company_id;

   function __construct(){
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_peminjaman($search, $status) {
      $this->db->select('p.id')
               ->from('peminjaman AS p')
               ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
               ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
               ->where('p.company_id', $this->company_id)
               ->where('p.status_peminjaman', $status);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function mulai_pembayaran($peminjaman_id){
      $this->db->select('due_date')
               ->from('skema_peminjaman')
               ->where('company_id', $this->company_id)
               ->where('peminjaman_id', $peminjaman_id)
               ->where('term', '1');
      $q = $this->db->get();
      $due_date = '';
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $due_date = $rows->due_date;
         }
      }
      return $due_date;
   }

   function get_biaya_perbulan($peminjaman_id){
      $this->db->select('MAX(amount) AS perbulan')
         ->from('skema_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('peminjaman_id', $peminjaman_id);
      $q = $this->db->get();
      $perbulan = 0;
      if( $q->num_rows() > 0 ) {
         foreach( $q->result()  AS  $rows ){
            $perbulan = $rows->perbulan;
         }
      }
      return $perbulan;
   }

   # get index daftar peminjaman
   function get_index_daftar_peminjaman($limit = 6, $start = 0, $search = '', $status) {
       $this->db->select('p.id, per.fullname, per.identity_number, p.register_number, p.tenor, p.dp, p.biaya, p.status_peminjaman')
               ->from('peminjaman AS p')
               ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
               ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
               ->where('p.company_id', $this->company_id)
               ->where('p.status_peminjaman', $status);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('per.fullname', $search)
                  ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            # bayar
            $sudah_bayar = $this->sudah_bayar_peminjaman($rows->id);
            # bayar
            $list[] = array('id' => $rows->id, 
                            'fullname' => $rows->fullname, 
                            'identity_number' => $rows->identity_number, 
                            'register_number' => $rows->register_number, 
                            'biaya' => $rows->biaya,
                            'tenor' => $rows->tenor, 
                            'dp' => $rows->dp, 
                            'sudah_bayar' => $sudah_bayar, 
                            'status_peminjaman' => $rows->status_peminjaman, 
                            'perbulan' => $this->get_biaya_perbulan($rows->id),
                            'mulai_pembayaran' => $this->date_ops->change_date_t5($this->mulai_pembayaran($rows->id)), 
                            'detail_pembayaran' => $this->detail_pembayaran($rows->id)
                         );
         }
      }
      return $list;
   }


   function detail_pembayaran( $peminjaman_id ) {
      $this->db->select('id, invoice, bayar, status, transaction_date')
         ->from('pembayaran_peminjaman')
         ->where('peminjaman_id', $peminjaman_id)
         ->where('company_id', $this->company_id)
         ->order_by('transaction_date', 'desc');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 
                            'invoice' => $rows->invoice, 
                            'bayar' => $rows->bayar, 
                            'status' => $rows->status, 
                            'transaction_date' => $rows->transaction_date);
         }
      }
      return $list;
   }


   # sudah bayar peminjaman
   function sudah_bayar_peminjaman($peminjaman_id){
      $this->db->select('bayar')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('peminjaman_id', $peminjaman_id);
      $q =$this->db->get();
      $bayar = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $bayar = $bayar + $rows->bayar;
         }
      }
      return $bayar;
   }

   # jamaah pool id
   function jamaah_pool_id_active(){
      // $this->db->select('p.jamaah_id')
      //    ->from('peminjaman AS p')
      //    ->join('pool AS po', 'p.pool_id=po.id','inner')
      //    ->where('po.active', 'active')
      //    ->where('p.company_id', $this->company_id);

      $this->db->select('jamaah_id')
         ->from('pool')
         ->where('company_id', $this->company_id)
         ->where('active', 'active');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[] = $rows->jamaah_id;
         }
      }
      return $list;
   }

   # daftar jamaah peminjaman
   function get_daftar_jamaah_peminjaman(){
      // jamaah id
      $jamaah_active = $this->jamaah_pool_id_active();
      // get jamaah id
      $this->db->select('j.id, per.fullname')
               ->from('jamaah AS j')
               ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
               ->where('j.company_id', $this->company_id);
      if( count( $jamaah_active ) > 0 ) {
         $this->db->where_not_in('j.id', $jamaah_active);
      }         
      $q = $this->db->get();
      $list = array();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[$rows->id] = $rows->fullname;
         }
      }
      return $list;
   }

   // check jamaah id
   function check_jamaah_id($jamaah_id){
      // jamaah id
      $jamaah_active = $this->jamaah_pool_id_active();
      // get jamaah id
      $this->db->select('j.id, per.fullname')
               ->from('jamaah AS j')
               ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
               ->where('j.company_id', $this->company_id)
               ->where('j.id', $jamaah_id);
      if( count( $jamaah_active ) > 0 ) {
         $this->db->where_not_in('j.id', $jamaah_active);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   # agen id
   function get_info_jamaah($jamaah_id){
      $this->db->select('agen_id, id, personal_id')
               ->from('jamaah')
               ->where('company_id', $this->company_id)
               ->where('id', $jamaah_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list = array('agen_id' => $rows->agen_id, 
                          'jamaah_id' => $rows->id, 
                          'personal_id' => $rows->personal_id);
         }
      }
      return $list;
   }


   function fee_default_level_agen(){
      $this->db->select('id, default_fee')
         ->from('level_keagenan')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->default_fee;
         }
      }
      return $list;
   }

   function agen_upline_tree($agen_id){
      $list = array();

      $feedBack = false;
      # level keagenan
      $level_keagenan = $this->fee_default_level_agen();
      $last_level = 0;
      do {
         $this->db->select('a.id, p.fullname, a.level_agen_id, a.upline, la.nama')
            ->from('agen AS a')
            ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
            ->join('level_keagenan AS la', 'a.level_agen_id=la.id', 'inner')
            ->where('a.company_id', $this->company_id)
            ->where('a.id', $agen_id);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               if($last_level < $rows->level_agen_id) {
                  $list[$rows->id] = array('id' => $rows->id,
                                           'level_agen_id' => $rows->level_agen_id,
                                           'level' => $rows->nama,
                                           'nama_agen' => $rows->fullname,
                                           'fee' => $level_keagenan[$rows->level_agen_id]);
                  $last_level = $rows->level_agen_id;

                  if( $rows->upline != 0 ) {
                     $agen_id = $rows->upline;
                  }else{
                     $feedBack = true;
                  }
               }else{
                  $feedBack = true;
               }
            }
         }else{
            $feedBack = true;
         }
      } while ($feedBack == false);

      return $list;
   }


   function fee_keagenan_deposit_paket($jamaah_id){
      // get_personal_id
      $this->db->select('a.level_agen_id, a.upline')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
         ->where('a.company_id', $this->company_id)
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            if( $rows->upline != 0 ){
               $tree = $this->agen_upline_tree($rows->upline);
               if( $tree[$rows->upline]['level_agen_id'] > $rows->level_agen_id ) { // apabila level upline lebih besar dari level jamaah
                  $list = $tree;
               }
            }
         }
      }else{
         $this->db->select('agen_id')
            ->from('jamaah')
            ->where('company_id', $this->company_id)
            ->where('id', $jamaah_id);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               if( $rows->agen_id != 0 ) {
                  $list = $this->agen_upline_tree($rows->agen_id);
               }
            }
         }
      }
      return $list;
   }


   # check register number
   function check_register_number($register_number){
      $this->db->select('id')
         ->from('peminjaman')
         ->where('company_id', $this->company_id)
         ->where('register_number', $register_number);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check peminjaman id
   function check_peminjaman_id($peminjaman_id){
      $this->db->select('id')
         ->from('peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $peminjaman_id);
      $q  = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_info_skema_peminjaman($peminjaman_id){
      $this->db->select('id, term, amount, due_date')
         ->from('skema_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('peminjaman_id', $peminjaman_id)
         ->order_by('due_date', 'asc');
      $q = $this->db->get();
      $list = array();
      $total_utang = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 
                            'term' => $rows->term, 
                            'amount' => $rows->amount, 
                            'due_date' => $rows->due_date);
            $total_utang = $total_utang + $rows->amount;
         }
      }
      # get info biaya dp
      $this->db->select('biaya, dp')
         ->from('peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $peminjaman_id);
      $biaya = 0;
      $dp = 0;   
      $r = $this->db->get();   
      if( $r->num_rows() > 0 ) {
         foreach ($r->result() as $rowr) {
            $biaya = $rowr->biaya;
            $dp = $rowr->dp;
         }
      }
      return array('peminjaman_id' => $peminjaman_id, 'skema' => $list, 'total_utang' => $total_utang, 'dp' => $dp);
   }

   # register number
   function get_register_number_peminjaman( $peminjaman_id ){
      $this->db->select('register_number')
         ->from('peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $peminjaman_id);
      $q = $this->db->get();
      $register_number = '';
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $register_number = $rows->register_number;
         }
      }
      return $register_number;
   }

   # invoice
   function get_invoice_pembayaran_peminjaman( $id ) {
      $this->db->select('invoice')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $invoice = '';
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $invoice = $rows->invoice;
         }
      } 
      return $invoice;  
   }

   function check_pembayaran_peminjaman_id($pembayaran_peminjaman_id){
      $this->db->select('id')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $pembayaran_peminjaman_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function get_sisa_pembayaran($peminjaman_id){
      # get peminjaman
      $this->db->select('biaya, dp')
         ->from('peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $peminjaman_id);
      $q = $this->db->get();
      $biaya = 0;
      $dp = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $biaya = $rows->biaya;
            $dp = $rows->dp;
         }
      }
      # get total pembayaran
      $this->db->select('bayar')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('peminjaman_id', $peminjaman_id);
      $q =$this->db->get();
      $total_bayar = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $total_bayar = $total_bayar + $rows->bayar;
         }
      }
      return $biaya - $total_bayar; // sisa hutang
   }

   # check invoice pembayaran pinjaman
   function check_invoice_pembayaran_pinjaman($invoice){
      $this->db->select('id')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function get_info_pembayaran( $peminjaman_id ){

      $this->db->select('biaya')
         ->from('peminjaman')
         ->where('company_id', $this->company_id)
         ->where('id', $peminjaman_id);
      $q = $this->db->get();
      $total_biaya = 0;
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $total_biaya = $rows->biaya;
         }
      }   


      $this->db->select('bayar')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('peminjaman_id', $peminjaman_id);
      $q = $this->db->get();
      $total_bayar = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $total_bayar = $total_bayar + $rows->bayar;
         }
      }

      return array('total_biaya' => $total_biaya, 'total_bayar' => $total_bayar);  
   }

}