<?php

/**
 *  -----------------------
 *	Model deposit paket cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_deposit_paket_cud extends CI_Model
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

   function update_target_paket($id, $target_paket_id) {
      # Starting Transaction
      $this->db->trans_start();
      # update pool
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->update('pool', array('target_paket_id' => $target_paket_id ) );
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
         $this->content = 'Melakukan update target paket.';
      }
      return $this->status;
   }

   # insert deposit paket
   function insert_deposit_paket( $data_pool, $data_deposit_transaction, $data_pool_transaction, $data_fee_keagenan, $data_detail_fee_keagenan, $data_jurnal ){
      # Starting Transaction
      $this->db->trans_start();
      # check if data fee keagenan is exist
      if( count($data_fee_keagenan) > 0 ) {
         // insert data fee keagenan
         $this->db->insert('fee_keagenan', $data_fee_keagenan);
         // get last id fee keagenan
         $fee_keagenan_id = $this->db->insert_id();
         # insert detail fee keagenan
         if( count($data_detail_fee_keagenan) > 0  ) {
            foreach ( $data_detail_fee_keagenan as $key => $value ) {
               $value['fee_keagenan_id'] = $fee_keagenan_id;
               $this->db->insert('detail_fee_keagenan', $value);
            }
         }
         # define fee keagenan id
         $data_pool['fee_keagenan_id'] = $fee_keagenan_id;
      }
      // insert data pool
      $this->db->insert('pool', $data_pool);
      // get last id data pool
      $pool_id = $this->db->insert_id();
      $nomor_transaction = '';
      // insert data deposit transaction
      foreach ($data_deposit_transaction as $key => $value) {
         $this->db->insert('deposit_transaction', $value);
         if(  $key == 0 ) {
            // get id deposit transaction
            $deposit_transaction_id = $this->db->insert_id();
            // nomor transaction 
            $nomor_transaction = $data_deposit_transaction[$key]['nomor_transaction'];
         }
      }

      // insert pool deposit transaction
      $data_pool_transaction['pool_id'] = $pool_id;
      $data_pool_transaction['deposit_transaction_id'] = $deposit_transaction_id;
      $this->db->insert('pool_deposit_transaction', $data_pool_transaction);
      // insert data jurnal 
      if( count($data_jurnal) > 0 ){
         foreach ($data_jurnal as $key => $value) {
            $this->db->insert('jurnal', $value);
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
         $this->content = 'Melakukan penambahan data deposit paket dengan nomor transaksi ' . $nomor_transaction;
      }
      return array('status' => $this->status, 'id' => $deposit_transaction_id);
   }

   function insert_pembayaran_deposit_paket($data_pool_transaction, $data_deposit_transaction, $data, $data_jurnal){
      # Starting Transaction
      $this->db->trans_start();
      // insert data deposit transaction
      $this->db->insert('deposit_transaction', $data_deposit_transaction);
      // get id deposit transaction
      $deposit_transaction_id = $this->db->insert_id();
      // insert pool deposit transaction
      $data_pool_transaction['deposit_transaction_id'] = $deposit_transaction_id;
      // pool deposit transaction
      $this->db->insert('pool_deposit_transaction', $data_pool_transaction);
      // count data
      if( count( $data ) > 0 ) {
         // insert peminjaman
         $this->db->insert('peminjaman', $data['peminjaman']);
         // peminjaman id
         $peminjaman_id = $this->db->insert_id();
         // insert schema 
         foreach ($data['skema_peminjaman'] as $key => $value) {
            // add peminjaman id
            $value['peminjaman_id'] = $peminjaman_id;
            // insert skema_peminjaman
            $this->db->insert('skema_peminjaman', $value);
         }
      }
      # jurnal
      $this->db->insert('jurnal', $data_jurnal);
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
         $this->content = 'Melakukan pembayaran deposit paket dengan nomor transaksi ' . $data_deposit_transaction['nomor_transaction'];
      }
      return array('status' => $this->status, 'id' => $deposit_transaction_id);
   }

   # insert handover deposit paket
   function insert_handover_deposit_paket($invoice, $data) {
      # Starting Transaction
      $this->db->trans_start();
      // insert data pool handover fasilitas deposit paket
      foreach ($data as $key => $value) {
         $this->db->insert('pool_handover_facilities', $value);
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
         $this->content = 'Melakukan handover fasilitas deposit paket dengan invoice ' . $invoice;
      }
      return $this->status;
   }

   function delete_deposit_paket($pool_id, $nomor_transaction){
      # Starting Transaction
      $this->db->trans_start();
      $fee_keagenan_id = 0;
      # delete in jurnal
      foreach ($nomor_transaction as $key => $value) {
         $this->db->where('source', 'deposittabungan:notransaction:'.$value)
                  ->where('company_id', $this->company_id)
                  ->delete('jurnal');
      }
      // delete pool deposit transaction
      $this->db->select('pdt.deposit_transaction_id, p.fee_keagenan_id, sumber_dana, no_tansaksi_sumber_dana')
         ->from('pool_deposit_transaction AS pdt')
         ->join('pool AS p',  'pdt.pool_id=p.id', 'inner')
         ->join('deposit_transaction AS dt', 'pdt.deposit_transaction_id=dt.id', 'inner')
         ->where('pdt.pool_id', $pool_id)
         ->where('pdt.company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            if( $fee_keagenan_id == 0 ) {
               $fee_keagenan_id = $rows->fee_keagenan_id;
            }
            # delete sumber data
            if( $rows->sumber_dana == 'deposit') {
               # delete deposit transaction
               $this->db->where('nomor_transaction', $rows->no_tansaksi_sumber_dana)
                 ->where('company_id', $this->company_id)
                 ->delete('deposit_transaction');
            }
            # delete deposit transaction
            $this->db->where('id', $rows->deposit_transaction_id)
              ->where('company_id', $this->company_id)
              ->delete('deposit_transaction');
         }
      }
      # delete pool deposit transaction
      $this->db->where('pool_id', $pool_id)
        ->where('company_id', $this->company_id)
        ->delete('pool_deposit_transaction');
      # delete pool handover facilities
      $this->db->where('pool_id', $pool_id)
        ->where('company_id', $this->company_id)
        ->delete('pool_handover_facilities');
      # filter fee keagenan id
      if( $fee_keagenan_id != 0 ){
         # delete detail fee keagenan
         $this->db->where('fee_keagenan_id', $fee_keagenan_id)
            ->where('company_id', $this->company_id)
            ->delete('detail_fee_keagenan');
         # delete fee_keagenan
         $this->db->where('id', $fee_keagenan_id)
            ->where('company_id', $this->company_id)
            ->delete('fee_keagenan');
      }
      # delete pool
      $this->db->where('id', $pool_id)
         ->where('company_id', $this->company_id)
         ->delete('pool');
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
         $this->content = 'Menghapus transaksi deposit paket dengan pool id  ' . $pool_id;
      }
      return $this->status;
   }

   # delete transaksi handover fasilitas deposit paket
   function delete_transaksi_handover_fasilitas_deposit_paket($invoice){
      # Starting Transaction
     $this->db->trans_start();
     # delete handover fasilitas deposit paket by invoice
     $this->db->where('invoice', $invoice)
        ->where('company_id', $this->company_id)
        ->delete('pool_handover_facilities');
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
        $this->content = 'Menghapus handover deposit paket invoice ' . $invoice;
     }
     return $this->status;
   }

   // insert refund tabungan umrah
   function insert_refund_tabungan_umrah( $id, $data ){
      # Starting Transaction
      $this->db->trans_start();
      // insert deposit transaction
      $this->db->insert('deposit_transaction', $data['deposit_transaction']);
      // deposit transaction id
      $data['pool_deposit_transaction']['deposit_transaction_id'] = $this->db->insert_id();
      // pool_deposit_transaction
      $this->db->insert('pool_deposit_transaction', $data['pool_deposit_transaction']);
      // update pool 
      if( isset( $data['pool'] ) ) {
         // update pool
         $this->db->where('id', $id)->where('company_id', $this->company_id)->update('pool', $data['pool'] );
      }
      // insert data jurnal 
      if( count($data['jurnal']) > 0 ) {
         foreach ($data['jurnal'] as $key => $value) {
            $this->db->insert('jurnal', $value);
         }
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE ) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan proses refund tabungan umrah dengan nomor transaksi ' . $data['deposit_transaction']['nomor_transaction'];
      }
      return $this->status;
   }
}
