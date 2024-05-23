<?php

/**
 *  -----------------------
 *	Model ppob cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_ppob_cud extends CI_Model
{
   // private $company_id;
   private $status;
   // private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
      $this->write_log = 1;
   }

    // update
   function update_status_ppob( $feedBack, $company_id ){
      # Starting Transaction
      $this->db->trans_start();  
      // status
      if( $feedBack['status'] == 'Gagal' ) {
         # update data ppob_transaction_history
         $this->db->where('transaction_code', $feedBack['transaction_code'])
                  ->update('ppob_transaction_history', array('ket' => $feedBack['pesan'], 'status' => 'failed'));
         # delete deposit transaction
         $this->db->where('info', 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$feedBack['transaction_code'])->delete('deposit_transaction');
         # delete jurnal
         $this->db->where('source', 'ppob:transaction_code:'.$feedBack['transaction_code'])->delete('jurnal');
         # update data saldo company
         $this->db->where('id', $company_id)
                  ->update('company', array('saldo' => $feedBack['get_back_saldo']));
      }else if( $feedBack['status'] == 'Sukses' ){
         # update data ppob_transaction_history
         $this->db->where('transaction_code', $feedBack['transaction_code'])
                  ->update('ppob_transaction_history', array('ket' => $feedBack['pesan'], 'status' => 'success'));
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // update markup
   public function update_markup( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update markup
      $this->db->where('id', $id)
               ->update('ppob_prabayar_product', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // update produk
   public function update_produk( $id , $data ){
      # Starting Transaction
      $this->db->trans_start();
      # update markup
      $this->db->where('id', $id)
               ->update('ppob_prabayar_product', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // insert new produk
   public function insert_produk( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert new data ppob product
      $this->db->insert('ppob_prabayar_product', $data);
      # get id transaksi
      $id_transkasi = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
       } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
       }
      return $this->status;
   }

   // delete produk
   public function delete_produk( $id ) {
      # Starting Transaction
      $this->db->trans_start();
      # delete produk
      $this->db->where('id', $id)
               ->delete('ppob_prabayar_product');
      # delete koneksi produk ke server
      $this->db->where('product_id', $id)
               ->delete('ppob_product_local_to_server_product');
      # get id transaksi
      $id_perusahaan = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // update operator
   function update_operator( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update markup
      $this->db->where('id', $id)
               ->update('ppob_prabayar_operator', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // insert operator
   function insert_operator( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert new data ppob prabayar category
      $this->db->insert('ppob_prabayar_operator', $data);
      # get id transaksi
      $id_transkasi = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
       } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
       }
      return $this->status;
   }

   // delete operator
   function delete_operator( $id ) {
      # Starting Transaction
      $this->db->trans_start();
       # delete operator
      $this->db->where('id', $id)
               ->delete('ppob_prabayar_operator');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // update koneksi ke server
   function updateKoneksiServer( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update koneksi ke server
      $this->db->where('product_id', $id)
               ->update('ppob_product_local_to_server_product', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // insert koneksi server
   function insertKoneksiServer( $data ) {
       # Starting Transaction
      $this->db->trans_start();
      # insert koneksi ke server
      $this->db->insert('ppob_product_local_to_server_product', $data);
      # get id transaksi
      $id_transkasi = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
       } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
       }
      return $this->status;
   }

   function update_harga_product($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update harga product
      $this->db->where('id', $id)
               ->update('ppob_prabayar_product', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }
}