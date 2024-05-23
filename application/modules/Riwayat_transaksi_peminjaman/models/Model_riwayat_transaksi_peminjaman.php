<?php

/**
 *  -----------------------
 *	Model riwayat transaksi peminjaman
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_riwayat_transaksi_peminjaman extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      $this->error = 0;
      $this->write_log = 1;
   }


   function get_total_riwayat_transaksi_peminjaman( $search ) {

      $this->db->select('pp.id')
         ->from('pembayaran_peminjaman AS pp')
         ->join('peminjaman AS p', 'pp.peminjaman_id=p.id', 'inner')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('pp.company_id', $this->company_id);
      // start date
      if ( array_key_exists( "start_date" , $search ) AND $search['start_date'] != ''  ) {
         $this->db->where( 'pp.transaction_date >=' , $search['start_date'] );
         if (array_key_exists( "end_date" , $search ) AND $search['end_date'] != '' ) {
            $this->db->where( 'pp.transaction_date <=', $search['end_date']. ' 23:59:59' );
         }else{
            $this->db->where( 'pp.transaction_date <= NOW()' );
         }
      }
      // search
      if ( array_key_exists( "search" , $search ) AND $search['search'] != ''  ) {
         $this->db->group_start()
                  ->like('pp.invoice', $search['search'])
                  ->or_like('p.register_number', $search['search'])
                  ->group_end();
      }

      $r    = $this->db->get();
      return $r->num_rows();

   }

   function get_index_riwayat_transaksi_peminjaman( $limit = 6, $start = 0, $search){
      $this->db->select('pp.id, pp.invoice, p.register_number, pp.bayar, pp.status,  pp.petugas, 
                         per.fullname, per.identity_number, pp.transaction_date')
         ->from('pembayaran_peminjaman AS pp')
         ->join('peminjaman AS p', 'pp.peminjaman_id=p.id', 'inner')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('pp.company_id', $this->company_id);
     
     if ( array_key_exists( "start_date",$search ) AND $search['start_date'] != ''  ) {
         $this->db->where( 'pp.transaction_date >=' , $search['start_date'] );
         if (array_key_exists( "end_date" , $search ) AND $search['end_date'] != '' ) {
            $this->db->where( 'pp.transaction_date <=', $search['end_date']. ' 23:59:59' );
         }else{
            $this->db->where( 'pp.transaction_date <= NOW()' );
         }
      }

      // search
      if ( array_key_exists( "search" , $search ) AND $search['search'] != ''  ) {
         $this->db->group_start()
                  ->like('pp.invoice', $search['search'])
                  ->or_like('p.register_number', $search['search'])
                  ->group_end();
      }

      $this->db->order_by('pp.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'invoice' => $row->invoice,
               'register_number' => $row->register_number,
               'bayar' => $row->bayar,
               'status' => $row->status,
               'petugas' => $row->petugas,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'transaction_date' => $row->transaction_date
            );
         }
      }
      return $list;
   }

}