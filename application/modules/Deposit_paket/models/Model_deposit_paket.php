<?php

/**
 *  -----------------------
 *	Model deposit paket
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_deposit_paket extends CI_Model
{
   private $company_id;
   private $status;
   private $content;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function check_target_paket_id( $id ){
      $this->db->select('id')
               ->from('paket')
               ->where('id', $id)
               ->where('departure_date >= NOW()')
               ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // get paket list
   function get_list_paket() {
      $this->db->select('id, paket_name')
               ->from('paket')
               ->where('departure_date >= NOW()')
               ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 
                            'paket_name' => $rows->paket_name);
         }
      }
      return $list;
   }

   function check_jamaah_id_not_loan_by_pool_id( $pool_id ) {  
      // jamaah id
      $jamaah_id = $this->get_jamaah_id_by_pool_id( $pool_id );
      // peminjaman
      $this->db->select('id')
               ->from('peminjaman')
               ->where('company_id', $this->company_id)
               ->where('jamaah_id', $jamaah_id)
               ->where('status_peminjaman', 'belum_lunas');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_jamaah_id_by_pool_id($pool_id){
      $this->db->select('jamaah_id')
         ->from('pool')
         ->where('company_id', $this->company_id)
         ->where('id', $pool_id);
      $jamaah_id = '';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $jamaah_id = $rows->jamaah_id;
         }
      }
      return $jamaah_id;
   }

   // check saldo deposit jamaah
   function check_saldo_deposit_jamaah($jamaah_id) {
      $this->db->select('dt.debet, dt.kredit')
         ->from('deposit_transaction AS dt')
         ->join('jamaah AS j', 'dt.personal_id=j.personal_id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('dt.transaction_requirement','deposit')
         ->where('j.id', $jamaah_id);
      $q  = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $debet = $rows->debet + $debet;
            $kredit = $rows->kredit + $kredit;
         }
      }   
      // total debet
      $total = $debet - $kredit;
      if( $total <= 0 ){
         return array('saldo' => 0, 'status' => false);
      }else{
         return array('saldo' => $total, 'status' => true);
      }
   }

 
   // get total deposit paket
   function get_total_deposit_paket($search, $filterTransaksi){
      $this->db->select('p.id')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if( $filterTransaksi == 'batal' ) {
         $this->db->where('p.active', 'non_active')
         ->where('p.batal_berangkat', 'ya');
      }elseif( $filterTransaksi == 'sudah' ) {
         $this->db->where('p.active', 'non_active')
         ->where('p.batal_berangkat', 'tidak');
      }elseif( $filterTransaksi == 'belum' ) {
         $this->db->where('p.active', 'active')
         ->where('p.batal_berangkat', 'tidak');
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_target_paket($id) {
      $this->db->select('paket_name')
               ->from('paket')
               ->where('company_id', $this->company_id)
               ->where('id', $id);
      $q = $this->db->get();
      return $q->row()->paket_name;
   }

   function get_value_tabungan($id) {
      $this->db->select('target_paket_id')
               ->from('pool')
               ->where('id', $id)
               ->where('company_id', $this->company_id);
      $q = $this->db->get();
      return $q->row()->target_paket_id;       
   }

   function get_index_deposit_paket($limit = 6, $start = 0, $filterTransaksi, $search = '' ){
      $this->db->select('p.id, p.active, per.fullname, per.identity_number, per.birth_place, per.birth_date, p.jamaah_id, p.target_paket_id,
                        (SELECT CONCAT_WS(\'$\', person.fullname, l.nama )
                           FROM agen AS a
                           INNER JOIN personal AS person ON a.personal_id=person.personal_id
                           INNER JOIN level_keagenan AS l ON a.level_agen_id=l.id
                           WHERE a.id=j.agen_id) AS info_agen')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if( $filterTransaksi == 'batal' ) {
         $this->db->where('p.active', 'non_active')
         ->where('p.batal_berangkat', 'ya');
      }elseif( $filterTransaksi == 'sudah' ) {
         $this->db->where('p.active', 'non_active')
         ->where('p.batal_berangkat', 'tidak');
      }elseif( $filterTransaksi == 'belum' ) {
         $this->db->where('p.active', 'active')
         ->where('p.batal_berangkat', 'tidak');
      }
      if ($search != '' or $search != null or !empty($search)) {
       $this->db->group_start()
           ->like('per.fullname', $search)
           ->or_like('per.identity_number', $search)
           ->group_end();
      }
      $this->db->order_by('p.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $agen = '-';
            $level_agen = '-';
            if( $row->info_agen != '' ){
               $exp = explode("$", $row->info_agen);
               $agen = $exp[0];
               $level_agen = $exp[1];
            }
            # get info deposit paket
            $info_deposit_paket = $this->get_info_deposit_jamaah($row->jamaah_id, $row->id);
            # barang handover
            $riwayat_handover =  $this->riwayat_handover_fasilitas_deposit_paket($row->id);
            # list
            $list[] = array(
               'id' => $row->id,
               'target_paket' => ($row->target_paket_id == 0 ? '<b style="color:red;">Target Paket Tidak Ditemukan</b>' : $this->get_target_paket($row->target_paket_id)),
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'birth_place' => $row->birth_place,
               'birth_date' => $this->date_ops->change_date($row->birth_date),
               'agen' => $agen,
               'active' => $row->active,
               'level_agen' => $level_agen,
               'total_deposit' => $info_deposit_paket['total'],
               'list_deposit' => $info_deposit_paket['list'],
               'riwayat_handover' => $riwayat_handover
            );
         }
      }
      return $list;
   }

   function riwayat_handover_fasilitas_deposit_paket($pool_id){
      $this->db->select('DISTINCT(invoice) AS invoice, date_transaction')
         ->from('pool_handover_facilities')
         ->where('pool_id', $pool_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $this->db->select('f.facilities_name')
               ->from('pool_handover_facilities AS phf')
               ->join('mst_facilities AS f', 'phf.facilities_id=f.id', 'inner')
               ->where('phf.company_id', $this->company_id)
               ->where('phf.pool_id', $pool_id)
               ->where('phf.invoice', $rows->invoice);
            $r = $this->db->get();
            $fasilitas = array();
            if( $r->num_rows() > 0 ) {
               foreach ($r->result() as $row) {
                  $fasilitas[] = $row->facilities_name;
               }
            }
            $date = explode(' ', $rows->date_transaction);
            $list[] = array('invoice' => $rows->invoice, 'fasilitas' => $fasilitas, 'date_transaction_1' => $date[0], 'date_transaction_2' => $date[1] );
         }
      }
      return $list;
   }

   function get_info_deposit_jamaah($jamaah_id, $pool_id){
      $this->db->select('dt.nomor_transaction, dt.debet, dt.kredit, dt.approver, dt.last_update, pdt.transaction_status')
         ->from('deposit_transaction AS dt')
         ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         // ->where('p.active', 'active')
         ->where('p.jamaah_id', $jamaah_id)
         ->where('dt.company_id', $this->company_id)
         ->where('p.id', $pool_id)
         ->order_by('dt.last_update', 'desc');
      $q = $this->db->get();
      $total = 0;
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {

            $biaya = ( $rows->transaction_status == 'cash' ? $rows->debet : $rows->kredit   );
            
            $list[] = array('invoice' => $rows->nomor_transaction,
                            'biaya' => $biaya,
                            'penerima' => $rows->approver,
                            'date_transaction' => $rows->last_update,
                            'transaction_status' => $rows->transaction_status);

            if( $rows->transaction_status == 'cash' ){
               $total = $total + $rows->debet;
            }else{
               $total = $total - $rows->kredit;
            }
         }
      }
      return array('list' => $list, 'total' => $total);
   }

   function get_list_member()
   {
      // personal in pool active
      $this->db->select('jamaah_id')
         ->from('pool ')
         ->where('active', 'active')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $jamaah_id = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $jamaah_id[] = $rows->jamaah_id;
         }
      }
      # personal
      $this->db->select('j.id, p.fullname, p.identity_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.company_id', $this->company_id);
      if( count($jamaah_id) > 0 ){
         $this->db->where_not_in('j.id', $jamaah_id);
      }
      $q = $this->db->get();
      $list_member = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_member[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'nomor_identitas' => $row->identity_number
            );
         }
      }
      return $list_member;
   }

   function check_jamaah_id_exist($jamaah_id){
      $this->db->select('id')
         ->from('jamaah')
         ->where('company_id', $this->company_id)
         ->where('id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_nomor_transaksi($nomor_transaksi){
      $this->db->select('id')
        ->from('deposit_transaction')
        ->where('company_id', $this->company_id)
        ->where('nomor_transaction', $nomor_transaksi);
     $q = $this->db->get();
     if ($q->num_rows() > 0) {
        return true;
     } else {
        return false;
     }
   }

   # get jamaah_id
   function get_jamaah_id($personal_id){
      $this->db->select('id')
         ->from('jamaah')
         ->where('personal_id', $personal_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $jamaah_id = '';
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $jamaah_id = $rows->id;
         }
      }
      return $jamaah_id;
   }

   function get_personal_id($jamaah_id){
      $this->db->select('personal_id')
         ->from('jamaah')
         ->where('company_id', $this->company_id)
         ->where('id', $jamaah_id);
      $q = $this->db->get();
      $personal_id = '';
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $personal_id = $rows->personal_id;
         }
      }
      return $personal_id;
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

   function get_level_agen(){
      $list = array();
      $this->db->select('id, level_agen_id')
         ->from('agen')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->level_agen_id;
         }
      }
      return $list;
   }

   function check_pool_id($pool_id){
      $this->db->select('id')
         ->from('pool')
         ->where('company_id', $this->company_id)
         ->where('active', 'active')
         ->where('id', $pool_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get info pembayaran pool
   function get_info_pembayaran_pool($pool_id){
      $this->db->select('dt.debet')
         ->from('pool_deposit_transaction AS dpt')
         ->join('deposit_transaction AS dt', 'dpt.deposit_transaction_id=dt.id', 'inner')
         ->where('dpt.company_id', $this->company_id)
         ->where('dpt.pool_id', $pool_id);
      $q = $this->db->get();
      $total = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $total = $total + $rows->debet;
         }
      }
      return $total;
   }

   function get_info_agen_by_pool_id($pool_id){
      $this->db->select('per.fullname')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('agen AS a', 'j.agen_id=a.id', 'inner')
         ->join('personal AS per', 'a.personal_id=per.personal_id', 'inner')
         ->where('p.id', $pool_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $nama_agen = '';
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $nama_agen = $rows->fullname;
         }
      }
      return $nama_agen;
   }

   function get_info_jamaah_by_pool_id($pool_id){
      $this->db->select('per.fullname, per.identity_number')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('p.id', $pool_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list['fullname'] = $rows->fullname;
            $list['identity_number'] = $rows->identity_number;
         }
      }
      return $list;
   }

   function get_personal_id_by_pool_id($pool_id){
      $this->db->select('j.personal_id')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->where('p.id', $pool_id)
         ->where('p.company_id', $this->company_id)
         ->where('p.active', 'active');
      $personal_id = '';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $personal_id = $rows->personal_id;
         }
      }
      return $personal_id;
   }

   function get_list_facilities(){
      $this->db->select('id, facilities_name')
         ->from('mst_facilities')
         ->where('company_id', $this->company_id);
      $list = array();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->facilities_name;
         }
      }
      return $list;
   }

   function get_value_facilities($pool_id, $list_facilities){
      $this->db->select('facilities_id, id, invoice, date_transaction, receiver_name, receiver_identity, officer')
         ->from('pool_handover_facilities')
         ->where('pool_id', $pool_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      $riwayat_handover = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
           $list[$rows->facilities_id] = $list_facilities[$rows->facilities_id];
           $riwayat_handover[] = array('id' => $rows->id,
                                       'invoice' => $rows->invoice,
                                       'facilities_name' => $list_facilities[$rows->facilities_id],
                                       'receiver_name' => $rows->receiver_name,
                                       'receiver_identity' => $rows->receiver_identity,
                                       'date_transaction' => $rows->date_transaction,
                                       'petugas' => $rows->officer);
         }

      }

      $sisa = array();
      foreach ($list_facilities as $key => $value) {
         if( ! array_key_exists($key, $list) ){
            $sisa[$key] = $value;
         }
      }

      return array('sisa' => $sisa, 'riwayat_handover' => $riwayat_handover);
   }

   function gen_nomor_transaksi_handover_facilities(){
      $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
     $rand = '';
     do {
         $rand = $this->text_ops->random_alpha_numeric(10);
         $handover_facilities = false;
         $pool_handover_facilities = false;
         $q = $this->db->select('id')
                       ->from('handover_facilities')
                       ->where('company_id', $this->company_id)
                       ->where('invoice', $rand)
                       ->get();
         if ($q->num_rows() == 0) {
            $handover_facilities = true;
         }
         $q = $this->db->select('id')
                       ->from('pool_handover_facilities')
                       ->where('company_id', $this->company_id)
                       ->where('invoice', $rand)
                       ->get();
         if ($q->num_rows() == 0) {
            $pool_handover_facilities = true;
         }
         if( $handover_facilities == true AND $pool_handover_facilities == true ){
            $feedBack = true;
         }
     } while ($feedBack == false);
     return $rand;
   }

   function check_fasilitas_deposit_paket_ID($fasilitas_id){
      $this->db->select('id')
         ->from('mst_facilities')
         ->where('company_id', $this->company_id)
         ->where('id', $fasilitas_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function check_fasilitas_was_checked($pool_id, $fasilitas_id){
      $this->db->select('id')
         ->from('pool_handover_facilities')
         ->where('company_id', $this->company_id)
         ->where('pool_id', $pool_id)
         ->where('facilities_id', $fasilitas_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function check_nomor_transaksi_fasilitas($nomor_transaksi){
      $this->db->select('id')
         ->from('pool_handover_facilities')
         ->where('invoice', $nomor_transaksi)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check invoice deposit paket
   function check_invoice_deposit_paket($invoice){
      $this->db->select('dt.id')
         ->from('deposit_transaction AS dt')
         ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('dt.nomor_transaction', $invoice);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get deposit id
   function get_deposit_paket_by_invoice($invoice){
      $this->db->select('dt.id')
         ->from('deposit_transaction AS dt')
         ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id',  'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('dt.nomor_transaction', $invoice)
         ->where('p.active', 'active');
      $q = $this->db->get();
      $deposit_id = 0;
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $deposit_id = $rows->id;
         }
      }
      return $deposit_id;
   }

   // check invoice handover fasilitas deposit paket
   function check_invoice_handover_deposit_paket($invoice){
      $this->db->select('phf.invoice')
         ->from('pool_handover_facilities AS phf')
         ->join('pool AS p', 'phf.pool_id=p.id', 'inner')
         ->where('phf.invoice', $invoice)
         ->where('phf.company_id', $this->company_id)
         ->where('p.active', 'active');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // total tabungan
   function get_total_tabungan($id){
      $this->db->select('dt.debet, dt.kredit')
         ->from('pool_deposit_transaction AS pdt')
         ->join('deposit_transaction AS dt', 'pdt.deposit_transaction_id=dt.id', 'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->where('p.id', $id)
         ->where('p.active', 'active')
         ->where('pdt.company_id', $this->company_id);
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $debet = $debet + $rows->debet;
            $kredit = $kredit + $rows->kredit;
         }
      }
      return $debet - $kredit;
   }

   // get data deposit transaction by invoice
   function get_data_deposit_transaction_by_invoice($invoice){
      $this->db->select('id')
         ->from('deposit_transaction')
         ->where('company_id', $this->company_id)
         ->where('nomor_transaction', $invoice);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows ) {
            $list['id'] = $rows->id;
         }
      }
      return $list;
   }

   function get_nomor_transaction_by_pool_id($pool_id){
      $this->db->select('dt.nomor_transaction')
         ->from('pool_deposit_transaction AS pdt')
         ->join('deposit_transaction AS dt', 'pdt.deposit_transaction_id=dt.id', 'inner')
         ->where('pdt.company_id', $this->company_id)
         ->where('pdt.pool_id', $pool_id);
      $list = array();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = $rows->nomor_transaction;
         }
      }
      return $list;
   }

   // get telah bayar by pool id
   function get_telah_bayar_by_pool_id($pool_id){
      $this->db->select('debet, kredit')
         ->from('deposit_transaction AS dt')
         ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('pdt.pool_id', $pool_id);
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $debet = $debet + $rows->debet;
            $kredit = $kredit + $rows->kredit;
         }
      }
      return $debet - $kredit;
   }

   // get fee agen by pool id
   function get_fee_agen_by_pool_id( $pool_id ){
      $this->db->select('dfk.fee')
         ->from('detail_fee_keagenan AS dfk')
         ->join('fee_keagenan AS fk', 'dfk.fee_keagenan_id=fk.id', 'inner')
         ->join('pool AS p', 'fk.id=p.fee_keagenan_id', 'inner')
         ->where('dfk.company_id', $this->company_id)
         ->where('p.id', $pool_id);
      $q = $this->db->get();
      $fee = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $fee = $fee + $rows->fee;
         }
      }
      return $fee;
   }

}
