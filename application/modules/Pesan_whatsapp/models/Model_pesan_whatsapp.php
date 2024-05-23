<?php
/**
 *  -----------------------
 *	Model pesan whatsapp
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pesan_whatsapp extends CI_Model
{
   private $company_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   # get nomor asal
   function get_nomor_asal(){
      $this->db->select('device_number')
         ->from('company')
         ->where('id', $this->company_id);
      $nomor_asal = '';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $nomor_asal = $rows->device_number;
         }
      }
      return $nomor_asal;
   }

   function get_total_daftar_pesan_whatsapp($search){
      $this->db->select('p.id, (SELECT GROUP_CONCAT( nomor_tujuan SEPARATOR \';\' )
                                 FROM detail_pesan_whatsapp
                                 WHERE company_id="' . $this->company_id . '" AND pesan_whatsapp_id=p.id ) AS list_nomor_tujuan')
         ->from('pesan_whatsapp AS p')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('list_nomor_tujuan', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_pesan_whatsapp($limit = 6, $start = 0, $search = ''){
      $this->db->select('p.id, p.nomor_asal, p.jenis_pesan, tp.nama_template, p.status_pesan, p.tanggal_input, p.template_pesan,
                           (SELECT GROUP_CONCAT( nomor_tujuan SEPARATOR \';\' )
                              FROM detail_pesan_whatsapp
                              WHERE company_id="' . $this->company_id . '" AND pesan_whatsapp_id=p.id ) AS list_nomor_tujuan')
         ->from('pesan_whatsapp AS p')
         ->join('template_pesan_whatsapp AS tp', 'p.template_id=tp.id', 'left')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('list_nomor_tujuan', $search)
            ->group_end();
      }
      $this->db->order_by('p.id', 'desc')->limit($limit, $start);
      $q   = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 
                           'nomor_asal' => $rows->nomor_asal,
                           'jenis_pesan' => ucwords(str_replace("_"," ",$rows->jenis_pesan)), 
                           'nama_template' => ($rows->nama_template == null ? '-' : $rows->nama_template), 
                           'pesan' => $rows->template_pesan,
                           'status_pesan' => $rows->status_pesan,
                           'total_pesan' => $this->total_pesan($rows->id), 
                           'tanggal_input' => $rows->tanggal_input);
         }
      }
      return $list;
   }

   function total_pesan($id){
      $this->db->select('id')
         ->from('detail_pesan_whatsapp')
         ->where('company_id', $this->company_id)
         ->where('pesan_whatsapp_id', $id);
      $q = $this->db->get();
      return $q->num_rows();   
   }

   # get paket active
   function get_paket_active_non_active() {
      $this->db->select('id, kode, paket_name, departure_date')
               ->from('paket')
               ->where('company_id', $this->company_id);
               // ->where('departure_date > NOW()');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $status = 'Sudah Berangkat';
            if( $rows->departure_date > date('Y-m-d')){
               $status = 'Belum Berangkat';
            }
            $list[$rows->id] = '<b>'.$rows->kode . '</b> -> ' . $rows->paket_name .' ('. $status.')';
         }
      }
      return $list;
   }
   
   # get nomor tujuan by jenis pesan
   function get_nomor_tujuan_by_jenis_pesan($jenis_pesan){
      $list = array();
      if( $jenis_pesan == 'semua_jamaah' ) { // semua_jamaah
         $this->db->select('p.nomor_whatsapp')
                  ->from('jamaah AS j')
                  ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                  ->where('j.company_id', $this->company_id)
                  ->where('p.nomor_whatsapp !=', '');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               $list[] = $rows->nomor_whatsapp;
            }
         }   
      }elseif ( $jenis_pesan == 'staff' ) { // staff
         $this->db->select('p.nomor_whatsapp')
                  ->from('base_users AS u')
                  ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
                  ->where('u.company_id', $this->company_id)
                  ->where('p.nomor_whatsapp !=', '');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               $list[] = $rows->nomor_whatsapp;
            }
         }   
      }elseif ( $jenis_pesan == 'agen' ) { // staff
         // nomor whatsapp
         $this->db->select('p.nomor_whatsapp')
                  ->from('agen AS a')
                  ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
                  ->join('level_keagenan AS lk', 'a.level_agen_id=lk.id', 'inner')
                  ->where('a.company_id', $this->company_id)
                  ->where('p.nomor_whatsapp !=', '');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
              $list[] = $rows->nomor_whatsapp;
            }
         }  
      }elseif ( $jenis_pesan == 'jamaah_paket' ) { // jamaah_paket
         $this->db->select('p.nomor_whatsapp')
                  ->from('paket_transaction_jamaah AS ptj')
                  ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
                  ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
                  ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
                  ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                  ->where('ptj.company_id', $this->company_id)
                  ->where('pkt.departure_date > NOW()')
                  ->where('p.nomor_whatsapp !=', '');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
               $list[] = $rows->nomor_whatsapp;
            }
         }   
       }elseif ( $jenis_pesan == 'jamaah_sudah_berangkat' ) { 
            $this->db->select('nomor_whatsapp')
                     ->from('paket_transaction_jamaah AS ptj')
                     ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
                     ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
                     ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
                     ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                     ->where('ptj.company_id', $this->company_id)
                     ->where('pkt.departure_date < NOW()')
                     ->where('p.nomor_whatsapp !=', '');
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
               foreach ( $q->result() as $rows ) {
                  $list[$rows->nomor_whatsapp] = $rows->nomor_whatsapp;
               }
            }   
       }elseif ( $jenis_pesan == 'jamaah_tabungan_umrah' ) { // jamaah_tabungan_umrah
         $this->db->select('p.nomor_whatsapp')
                  ->from('pool AS po')
                  ->join('jamaah AS j', 'po.jamaah_id=j.id', 'inner')
                  ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                  ->where('po.company_id', $this->company_id)
                  ->where('po.active','active')
                  ->where('p.nomor_whatsapp !=', '');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               $list[] = $rows->nomor_whatsapp;
            }
         }   
      }elseif ( $jenis_pesan == 'jamaah_utang_koperasi' ) { // jamaah_tabungan_umrah
         $this->db->select('p.nomor_whatsapp')
                  ->from('peminjaman AS pem')
                  ->join('jamaah AS j', 'pem.jamaah_id=j.id', 'inner')
                  ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                  ->where('pem.company_id', $this->company_id)
                  ->where('pem.status_peminjaman','belum_lunas')
                  ->where('p.nomor_whatsapp !=', '');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               $list[] = $rows->nomor_whatsapp;
            }
         }   
      }
      return $list;
   }

   # get info sudah bayar cicilan
   function get_info_sudah_bayar_cicilan( $peminjaman_id ) {
      $this->db->select('bayar')
         ->from('pembayaran_peminjaman')
         ->where('company_id', $this->company_id)
         ->where('peminjaman_id', $peminjaman_id);
      $q = $this->db->get();
      $sudah_bayar = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $sudah_bayar = $sudah_bayar + $rows->bayar;
         }
      }
      return $sudah_bayar;
   }

   function total_tabungan($pool_id){
      $this->db->select('d.debet')
         ->from('pool_deposit_transaction AS pd')
         ->join('deposit_transaction AS d', 'pd.deposit_transaction_id=d.id', 'inner')
         ->where('pd.company_id', $this->company_id)
         ->where('pd.pool_id', $pool_id);
      $q = $this->db->get();
      $total = 0;
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ){
            $total = $total + $rows->debet;
         }
      }
      return $total;
   }

   # get template by jenis pesan
   function get_template_by_jenis_pesan( $jenis_pesan ) {
      $this->db->select('id, nama_template')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id)
         ->where('jenis_pesan', $jenis_pesan);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[$rows->id] = $rows->nama_template;
         }
      }
      return $list;
   }

   # template id
   function get_template_id($id){
      $this->db->select('id')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   # pesan
   function get_pesan_template_by_template_id( $template_id ) {
      $this->db->select('pesan')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id)
         ->where('id', $template_id);
      $q = $this->db->get();
      $pesan = '';
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $pesan = $rows->pesan;
         }
      }
      return $pesan;
   }


   # paket id 
   function check_paket_id($paket_id){
      $this->db->select('id')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('departure_date > NOW()');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   # get nomor tujuan by paket
   function get_nomor_tujuan_by_paket($paket_id){
      $this->db->select('p.nomor_whatsapp')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('ptj.company_id', $this->company_id)
         
         ->where('pt.paket_id', $paket_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = $rows->nomor_whatsapp;
         }
      }
      return $list;
   }

   # check template id
   function check_template_id($id){
      $this->db->select('id')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }


   function get_detail_pesan_whatsapp($id){
      $this->db->select('nomor_tujuan, status, pesan, send_at, message_id')
         ->from('detail_pesan_whatsapp')
         ->where('pesan_whatsapp_id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = array('nomor_tujuan' => $rows->nomor_tujuan, 
                            'status' => $rows->status, 
                            'pesan' => $rows->pesan, 
                            'send_at' => $rows->send_at, 
                            'message_id' => $rows->message_id);
         }
      }
      return $list; 
   }

   # chec pesa whatsapp
   function check_pesan_whatsapp_id($id){
      $this->db->select('id')
         ->from('pesan_whatsapp')
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // count number whatsapp paket
   function countNumberWhatsappPaket( $status_paket ){
      $this->db->select('p.nomor_whatsapp')
            ->from('paket_transaction_jamaah AS ptj')
            ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
            ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
            ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
            ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
            ->where('ptj.company_id', $this->company_id)
            ->where('p.nomor_whatsapp !=', '');
         if( $status_paket == 'belum_berangkat' ) {
            $this->db->where('pkt.departure_date > NOW()');
         }elseif ( $status_paket == 'sudah_berangkat' ) {
            $this->db->where('pkt.departure_date < NOW()');
         }
         $q = $this->db->get();
         return $q->num_rows();
   }

}