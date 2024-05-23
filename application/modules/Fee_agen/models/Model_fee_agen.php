<?php

/**
 *  -----------------------
 *	Model trans paket
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_fee_agen extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_agen($search ){
      $this->db->select('id')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_agen($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('a.id, p.fullname, p.identity_number, p.gender, p.nomor_whatsapp, p.address, p.email, lv.nama,
                        (SELECT SUM(fee)
                           FROM detail_fee_keagenan
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") AS fee_keagenan,
                        (SELECT SUM(biaya)
                           FROM fee_keagenan_payment
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") total_pembayaran,
                        (SELECT COUNT(id)
                           FROM detail_fee_keagenan
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") AS total_transaksi')
         ->from('agen AS a')
         ->join('level_keagenan AS lv', 'a.level_agen_id=lv.id', 'inner')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('a.last_update', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'gender' => $row->gender,
               'nomor_whatsapp' => $row->nomor_whatsapp,
               'level_agen' => $row->nama,
               'address' => $row->address,
               'email' => $row->email,
               'paid_fee' => $row->total_pembayaran == '' ? 0 : $row->total_pembayaran,
               'unpaid_fee' => ($row->fee_keagenan - $row->total_pembayaran) == '' ? 0 : ($row->fee_keagenan - $row->total_pembayaran),
               'total_transaksi' => $row->total_transaksi
            );
         }
      }
      return $list;
   }

   function get_info_agen($id){
      $this->db->select('p.fullname, p.identity_number, lv.nama, a.id, a.level_agen_id')
         ->from('agen AS a')
         ->join('level_keagenan AS lv', 'a.level_agen_id=lv.id', 'inner')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id)
         ->where('a.id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['fullname'] = $rows->fullname;
            $list['identity_number'] = $rows->identity_number;
            $list['level_keagenan'] = $rows->nama;
            $list['level_agen_id'] = $rows->level_agen_id;
         }
      }
      return $list;
   }

   function check_agen_is_exist($id){
      $this->db->select('id')
         ->from('agen')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_total_riwayat_komisi_agen($search, $agen_id){
      $this->db->select('dft.id')
         ->from('detail_fee_keagenan AS dft')
         ->join('agen AS a', 'dft.agen_id=a.id', 'inner')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('dft.company_id', $this->company_id)
         ->where('dft.agen_id', $agen_id);
     if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
           ->like('dft.transaction_number', $search)
           ->group_end();
     }
     $r    = $this->db->get();
     return $r->num_rows();
   }

   # paket transaction info
   function paket_transaction_info( $fee_keagenan_id ) {
      $this->db->select('pt.no_register, p.paket_name, per.fullname')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('paket_transaction_jamaah AS ptj', 'pt.id=ptj.paket_transaction_id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('pt.company_id', $this->company_id)
         ->where('pt.fee_keagenan_id', $fee_keagenan_id);
      $q = $this->db->get();
      $info = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $info['nomor_register'] = $rows->no_register;
            $info['paket_name'] = $rows->paket_name;
            $info['fullname'] = $rows->fullname;
         }
      }
      return $info;
   }

   function get_index_riwayat_komisi_agen($limit = 6, $start = 0, $search = '', $agen_id){
      $this->db->select('dft.id, dft.fee_keagenan_id, dft.agen_id, dft.transaction_number, p.fullname, p.identity_number, dft.fee,
                         dft.status_fee, dft.info, dft.last_update')
        ->from('detail_fee_keagenan AS dft')
        ->join('agen AS a', 'dft.agen_id=a.id', 'inner')
        ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
        ->where('dft.company_id', $this->company_id)
        ->where('dft.agen_id', $agen_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('dft.transaction_number', $search)
            ->group_end();
      }
       $this->db->order_by('dft.status_fee', 'desc');
      $this->db->order_by('dft.last_update', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            # fee keagenan id
            if( $rows->fee_keagenan_id != 0 ){
               $paket_transaction_info = $this->paket_transaction_info($rows->fee_keagenan_id);
            }else{
               $paket_transaction_info = array();
            }
            # info agen
            $info_agen = $this->get_info_agen($rows->agen_id);
            # list
            $list[] = array(
                  'id' => $rows->id,
                  'transaction_number' => $rows->transaction_number != '' ? $rows->transaction_number : 'Tidak ditemukan',
                  'fullname' => $rows->fullname,
                  'identity_number' => $rows->identity_number,
                  'fee' => $rows->fee,
                  'status_fee' => $rows->status_fee,
                  'info' => $rows->info,
                  'tanggal_transaksi' => $rows->last_update,
                  'paket_info' => $paket_transaction_info);
         }
      }
      return $list;
   }

   # check riwayat komisi id
   function check_riwayat_komisi_id($id){
      $this->db->select('id')
         ->from('detail_fee_keagenan')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function get_agen_id_from_detail_fee_agen($id){
      $this->db->select('agen_id')
         ->from('detail_fee_keagenan')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $agen_id = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $agen_id = $rows->agen_id;
         }
      }
      return $agen_id;
   }

   function get_pool_info($fee_keagenan_id){
      $this->db->select('p.fullname')
               ->from('pool AS po')
               ->join('jamaah AS j', 'po.jamaah_id=j.id', 'inner')
               ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
               ->where('po.fee_keagenan_id', $fee_keagenan_id)
               ->where('po.company_id', $this->company_id)
               ->where('po.active', 'active');
      $q = $this->db->get();
      $fullname = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $fullname['fullname'] = $rows->fullname;
         }
      }
      return $fullname;
   }

   # get info fee agen
   function get_info_fee_agen($agen_id){
      # select data from database
      $this->db->select('id, fee_keagenan_id, fee, sudah_bayar, transaction_number')
         ->from('detail_fee_keagenan')
         ->where('company_id', $this->company_id)
         ->where('agen_id', $agen_id)
         ->where('status_fee', 'belum_lunas');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            if ( $rows->fee_keagenan_id != 0 ) {
               # check paket
               $paket_transaction_info = $this->paket_transaction_info($rows->fee_keagenan_id);
               if( count($paket_transaction_info) == 0  ){
                  $paket_transaction_info = $this->get_pool_info($rows->fee_keagenan_id);
               }
            } else {
               $paket_transaction_info = array();
            }

           $list[] = array('id' => $rows->id,
                           'transaction_number' => $rows->transaction_number,
                           'fee' => $rows->fee,
                           'sudah_bayar' => $rows->sudah_bayar,
                           'paket_name' => isset($paket_transaction_info['paket_name']) ? $paket_transaction_info['paket_name'] : 'Tidak ditemukan',
                           'jamaah' => isset($paket_transaction_info['fullname']) ? $paket_transaction_info['fullname'] : 'Tidak ditemukan');
         }
      }
      return $list;
   }


   # get info fee agen
   function get_info_fee_agen_2($agen_id){
      # select data from database
      $this->db->select('id, fee, sudah_bayar')
         ->from('detail_fee_keagenan')
         ->where('company_id', $this->company_id)
         ->where('agen_id', $agen_id)
         ->where('status_fee', 'belum_lunas');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
           $list[$rows->id] = array('id' => $rows->id,
                           'fee' => $rows->fee,
                           'sudah_bayar' => $rows->sudah_bayar);
         }
      }
      return $list;
   }

   function check_invoice_bayar_fee_exist( $invoice ){
      $this->db->select('id')
               ->from('fee_keagenan_payment')
               ->where('company_id', $this->company_id)
               ->where('invoice', $invoice);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return TRUE;
      }else{
         return FALSE;
      }
   }

   function get_total_riwayat_pembayaran_fee_agen( $search, $agen_id ) {
      $this->db->select('DISTINCT(fkp.invoice)')
               ->from('fee_keagenan_payment AS fkp')
               ->join('detail_fee_keagenan AS dfk', 'fkp.detail_fee_keagenan_id=dfk.id', 'inner')
               ->where('fkp.company_id', $this->company_id)
               ->where('fkp.agen_id', $agen_id);
      if ( $search != '' or $search != null or !empty($search) ) {
         $this->db->group_start()
               ->like('dft.invoice', $search)
               ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index riwayat pembayaran fee agen
   function get_index_riwayat_pembayaran_fee_agen($limit = 6, $start = 0, $search = '', $agen_id){
      $this->db->select('DISTINCT(fkp.invoice), fkp.date_transaction')
               ->from('fee_keagenan_payment AS fkp')
               ->join('detail_fee_keagenan AS dfk', 'fkp.detail_fee_keagenan_id=dfk.id', 'inner')
               ->where('fkp.company_id', $this->company_id)
               ->where('fkp.agen_id', $agen_id);
      if ( $search != '' or $search != null or !empty($search) ) {
         $this->db->group_start()
               ->like('fkp.invoice', $search)
               ->group_end();
      }
      $this->db->order_by('fkp.date_transaction', 'desc')->limit($limit, $start);
      $r    = $this->db->get();
      $list = array();
      $n = $start;
      if( $r->num_rows() > 0 ) {
         foreach ( $r->result() as $row ) {
            $n++;
            $this->db->select('dfk.transaction_number, fkp.biaya, dfk.fee, fkp.applicant_name, fkp.applicant_identity, fkp.receiver,
                               fkp.date_transaction')
                     ->from('fee_keagenan_payment AS fkp')
                     ->join('detail_fee_keagenan AS dfk', 'fkp.detail_fee_keagenan_id=dfk.id', 'inner')
                     ->where('fkp.company_id', $this->company_id)
                     ->where('fkp.invoice', $row->invoice)
                     ->where('fkp.agen_id', $agen_id);
            $q = $this->db->get();
            $list_detail = array();
            if( $q->num_rows() > 0 ) {
               foreach ( $q->result() as $rows ) {
                  $list_detail[] = array('transaction_number' => $rows->transaction_number,
                                          'fee' => $rows->fee,
                                          'biaya' => $rows->biaya,
                                          'applicant_name' => $rows->applicant_name,
                                          'applicant_identity' => $rows->applicant_identity,
                                          'receiver' => $rows->receiver);
               }
            }
            $list[] = array( 'agen_id' => $agen_id, 'no' => $n, 'invoice' => $row->invoice, 'date_transaction' => $row->date_transaction, 'detail' => $list_detail);
         }
      }
      return $list;
   }

   function check_invoice_riwayat_pembayaran_exist($invoice){
      $this->db->select('id')
         ->from('fee_keagenan_payment')
         ->where('invoice', $invoice)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   # recount detail sudah bayar fee keagenan
   function recount_detail_sudah_bayar_fee_keagenan( $invoice ) {
      $this->db->select('fkp.detail_fee_keagenan_id, dfk.fee')
         ->from('fee_keagenan_payment AS fkp')
         ->join('detail_fee_keagenan AS dfk', 'fkp.detail_fee_keagenan_id=dfk.id', 'inner')
         ->where('fkp.company_id', $this->company_id)
         ->where('fkp.invoice', $invoice);
      $q = $this->db->get();
      $list_detail_fee_keagenan_id = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $this->db->select('biaya')
               ->from('fee_keagenan_payment AS fkp')
               ->where('company_id', $this->company_id)
               ->where('detail_fee_keagenan_id', $rows->detail_fee_keagenan_id)
               ->where('invoice !=', $invoice);
            $r = $this->db->get();
            $sisa_sudah_bayar = 0;
            if( $r->num_rows() > 0 ){
               foreach ($r->result() as $row) {
                  $sisa_sudah_bayar = $sisa_sudah_bayar + $row->biaya;
               }
            }
            # list sudah bayar
            $list_detail_fee_keagenan_id[$rows->detail_fee_keagenan_id] = array('sudah_bayar' => $sisa_sudah_bayar,
                                                                                'status_fee' => ($rows->fee > $sisa_sudah_bayar ? 'belum_lunas' : 'lunas'));
         }
      }
      return $list_detail_fee_keagenan_id;
   }

}
