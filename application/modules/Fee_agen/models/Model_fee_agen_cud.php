<?php

/**
 *  -----------------------
 *	Model fee agen cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_fee_agen_cud extends CI_Model
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

   # insert fee agen
   function insert_fee_agen( $data ) {
      # trans start
      $this->db->trans_start();
      # insert detail fee keagenan
      $this->db->insert('detail_fee_keagenan', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menambahkan komisi agen dengan agen ID ' . $data['agen_id'];
      }
      return $this->status;
   }

   # delete riwayat komisi process
   function delete_riwayat_komisi_agen($id){
      # trans start
      $this->db->trans_start();
      # delete detail fee keagenan
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->delete('detail_fee_keagenan');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menghapus riwayat komisi agen dengan riwayat agen id ' . $id;
      }
      return $this->status;
   }

   # insert pembayaran fee agen
   function insert_pembayaran_fee_agen( $data, $data_detail ) {
      # trans start
      $this->db->trans_start();
      # insert pembayaran fee agen
      $invoice = '';
      foreach ( $data as $key => $value ) {
         $invoice = $value['invoice'];
         $this->db->insert('fee_keagenan_payment', $value);
      }
      # update data agen
      foreach ( $data_detail as $key => $value ) {
         $this->db->where('id', $key)
                  ->where('company_id', $this->company_id)
                  ->update('detail_fee_keagenan', $value);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Proses pembayaran fee agen dengan nomor invoice '. $invoice;
      }
      return $this->status;
   }

   # delete riwayat pembayaran fee agen
   function delete_riwayat_pembayaran_fee_agen($invoice, $data_recount ){
      # trans start
      $this->db->trans_start();
      # delete riwayat pembayaran fee keagenan
      $this->db->where('invoice', $invoice)
               ->where('company_id', $this->company_id)
               ->delete('fee_keagenan_payment');
      # update
      if( count( $data_recount ) > 0 ) {
         foreach ($data_recount as $key => $value) {
            $this->db->where('id', $key)
                     ->where('company_id', $this->company_id)
                     ->update('detail_fee_keagenan', array('sudah_bayar' => $value['sudah_bayar'],
                                                           'status_fee' => $value['status_fee']) );
         }
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menghapus riwayat pembayaran fee / komisi agen dengan riwayat nomor invoice ' . $invoice;
      }
      return $this->status;
   }


}
