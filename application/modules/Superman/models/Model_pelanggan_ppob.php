<?php

/**
 *  -----------------------
 *	Model pelanggan ppob
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pelanggan_ppob extends CI_Model
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


   function get_total_daftar_pelanggan_ppob($search){
      $this->db->select('id')
               ->from('ppob_costumer');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('name', $search)
                  ->or_like('code', $search)
                  ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }


   function get_index_daftar_pelanggan_ppob($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, code, name, whatsappnumber, saldo')
               ->from('ppob_costumer');
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
                            'whatsappnumber' => $row->whatsappnumber,
                            'saldo' => $row->saldo, 
                            'transaksi_hari_ini' => $this->get_today_transaction($row->id), 
                            'riwayat_deposit_saldo' => $this->get_list_deposit_saldo($row->id));
         }
      }
      return $list;
   }

   // get list deposit saldo
   function get_list_deposit_saldo($id){
      $this->db->select('id, debet, kredit, ket')
               ->from('ppob_costumer_deposit_history')
               ->where('ppob_costumer_id', $id)
               ->order_by('id', 'desc')
               ->limit(5);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list[] = array('id' => $row->id, 
                            'debet' => $row->debet, 
                            'kredit' => $row->kredit, 
                            'ket' => $row->ket);
         }
      }
      return $list;
   }

   function get_today_transaction($id){
      $date = date('Y-m-d');
      $this->db->select('pth.id')
               ->from('ppob_transaction_history AS pth')
               ->join('ppob_transaction_history_costumer AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
               ->where('pthc.ppob_costumer_id', $id)
               ->where('pth.status', 'success')
               ->like('pth.created_at', $date);
      $q = $this->db->get();
      return $q->num_rows();         
   }

   // generate pelanggan ppob code
   function generated_pelanggan_ppob(){
      $feedBack = false;
      $rand = '';
      do {
         $rand = $this->random_code_ops->random_alpha_numeric(6);
         $q = $this->db->select('id')
                       ->from('ppob_costumer')
                       ->where('code', $rand)
                       ->get();
         if ($q->num_rows() == 0) {
            $feedBack = true;
         }
      } while ($feedBack == false);
      return $rand;
   }

   // check id pelanggan
   function check_id_pelanggan($id){
         $this->db->select('id')
                  ->from('ppob_costumer')
                  ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   // check kode pelanggan
   function check_kode_pelanggan($kode, $id = ''){
      $this->db->select('id')
               ->from('ppob_costumer')
               ->where('code', $kode);
      if( $id != '' ) {
         $this->db->where('id != ', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return TRUE;
      }else{
         return FALSE;
      }
   }

   // check keberadaan nomor whatsapp didalam database
   function check_nomor_whatsapp($nomor_whatsapp, $id = ''){
      $this->db->select('id')
               ->from('ppob_costumer')
               ->where('whatsappnumber', $nomor_whatsapp);
      if( $id != '' ) {
         $this->db->where('id != ', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return TRUE;
      }else{
         return FALSE;
      }
   }

   // get value pelanggan ppob
   function get_value_pelanggan_ppob( $id ) {
      $this->db->select('id, code, name, whatsappnumber')
               ->from('ppob_costumer')
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list['id'] = $row->id;
            $list['kode'] = $row->code;
            $list['name'] = $row->name;
            $list['whatsappnumber'] = $row->whatsappnumber;
         }
      }
      return $list;
   }  

   // get last saldo
   function get_last_saldo( $id ) {
      $this->db->select('saldo')
               ->from('ppob_costumer')
               ->where('id', $id);
      $q = $this->db->get();
      $saldo = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row) {
            $saldo = $row->saldo;
         }
      }
      return $saldo;
   }

   // check riwayat deposit ppob id
   function check_riwayat_deposit_ppob_id($id){
      $this->db->select('id')
               ->from('ppob_costumer_deposit_history')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // get value riwayat deposit ppob
   function get_value_riwayat_deposit_ppob( $id ){
      $this->db->select('pc.id, pc.saldo, ph.debet, ph.kredit')
               ->from('ppob_costumer_deposit_history AS ph')
               ->join('ppob_costumer AS pc', 'ph.ppob_costumer_id=pc.id', 'inner')
               ->where('ph.id', $id);
      $q = $this->db->get();
      // $list = array();
      $saldo = 0;
      $id =0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $saldo = $rows->saldo;
            if( $rows->debet != 0 ) {
               $saldo = $saldo - $rows->debet;
            }
            if( $rows->kredit != 0 ) {
               $saldo = $saldo + $rows->kredit;
            }
            $id = $rows->id;
         }
      }
      return array('saldo' => $saldo, 'costumer_id' => $id);
   }



   function get_total_daftar_transaksi_ppob($search, $status){

      $joinPerusahaan = $status == 'perusahaan' ? 'inner' : 'left';
      $joinPelanggan = $status == 'pelanggan' ? 'inner' : 'left';

      $this->db->select('ph.id')
                ->from('ppob_transaction_history AS ph')
               ->join('ppob_prabayar_product AS pp', 'ph.product_code=pp.product_code', 'inner')
               ->join('ppob_transaction_history_company AS ptcom', 'ph.id=ptcom.ppob_transaction_history_id', $joinPerusahaan)
               ->join('company AS c', 'ptcom.company_id=c.id', $joinPerusahaan)
               ->join('ppob_transaction_history_costumer AS ptcos', 'ph.id=ptcos.ppob_transaction_history_id', $joinPelanggan)
               ->join('ppob_costumer AS pc', 'ptcos.ppob_costumer_id=pc.id', $joinPelanggan);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('ph.transaction_code', $search)
                  ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_transaksi_ppob($limit = 6, $start = 0, $search = '', $status){

      $joinPerusahaan = $status == 'perusahaan' ? 'inner' : 'left';
      $joinPelanggan = $status == 'pelanggan' ? 'inner' : 'left';

      $this->db->select('ph.id, ph.transaction_code, ph.nomor_tujuan, ph.product_code, pp.product_name, ph.server, 
                         ph.server_price, ph.application_price, ph.status, ph.created_at, c.name AS nama_perusahaan, pc.name AS costumer_name ')
               ->from('ppob_transaction_history AS ph')
               ->join('ppob_prabayar_product AS pp', 'ph.product_code=pp.product_code', 'inner')
               ->join('ppob_transaction_history_company AS ptcom', 'ph.id=ptcom.ppob_transaction_history_id', $joinPerusahaan)
               ->join('company AS c', 'ptcom.company_id=c.id', $joinPerusahaan)
               ->join('ppob_transaction_history_costumer AS ptcos', 'ph.id=ptcos.ppob_transaction_history_id', $joinPelanggan)
               ->join('ppob_costumer AS pc', 'ptcos.ppob_costumer_id=pc.id', $joinPelanggan);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('ph.transaction_code', $search)
                  ->group_end();
      }
      $this->db->order_by('ph.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $status_pelanggan = '';
            $nama_pelanggan = '';
            if( $row->nama_perusahaan != null ) {
                 $status_pelanggan = 'Perusahaan'; 
                 $nama_pelanggan = $row->nama_perusahaan ;
            }
            if( $row->costumer_name != null ) {
                 $status_pelanggan = 'Pelanggan PPOB'; 
                 $nama_pelanggan = $row->costumer_name ;
            }
            $list[] = array('id' => $row->id,
                            'transaction_code' => $row->transaction_code,
                            'nomor_tujuan' => $row->nomor_tujuan,
                            'product_code' => $row->product_code,
                            'product_name' => $row->product_name,
                            'server' => $row->server, 
                            'server_price' => $row->server_price, 
                            'application_price' => $row->application_price, 
                            'status' => $row->status, 
                            'created_at' => $row->created_at, 
                            'status_pelanggan' => $status_pelanggan, 
                            'nama_pelanggan' => $nama_pelanggan);
         }
      }
      return $list;
   }

}