<?php

/**
 *  -----------------------
 *	Model trans paket la cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_paket_la_cud extends CI_Model
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

   // close paket la
   function close_paket_la( $data, $id ) {
      # Starting Transaction
      $this->db->trans_start();
      // insert jurnal 
      $this->db->insert('jurnal', $data['jurnal']);
      # update status paket la transaction
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->update('paket_la_transaction', $data['paket_la_transaction']);
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
         $this->content = 'Melakukan penutupan paket la dengan id : ' . $id . '.';
      }
      return $this->status;
   }

   # update paket la
   function update_paket_la($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data paket la transaction
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->update('paket_la_transaction', $data);
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
         $this->content = 'Melakukan perubahan data transaksi paket la dengan paket la transaksi id : ' . $id . '.';
      }
      return $this->status;
   }

   function insert_paket_la($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert paket la transaction data
      $this->db->insert('paket_la_transaction', $data);
      # get paket la transaction id
      $paket_la_transction_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan transaksi paket la dengan paket la transaction id : ' . $paket_la_transction_id . '.';
      }
      return $this->status;
   }

   # insert pembayaran
   function insert_pembayaran($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert data paket la transaction history
      $this->db->insert('paket_la_transaction_history', $data);
      # get paket la transaction history id
      $paket_la_transction_history_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan transaksi pembayaran paket la dengan paket la transaction history id : ' . $paket_la_transction_history_id . '.';
      }
      return $this->status;
   }

   # insert refund
   function insert_refund($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert data paket la transaction history
      $this->db->insert('paket_la_transaction_history', $data);
      # get paket la transaction history id
      $paket_la_transction_history_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan transaksi refund pembayaran paket la dengan paket la transaction history id : ' . $paket_la_transction_history_id . '.';
      }
      return $this->status;
   }

   // update_kas_trans_fasilitas
   function update_kas_trans_fasilitas($data, $fasilitas_id, $ket)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data kas transaksi fasilitas
      $this->db->where('fasilitas_la_id', $fasilitas_id)
         ->where('ket', $ket)
         ->where('company_id', $this->company_id)
         ->update('kas_paket_la', $data);
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
         $this->content = 'Melakukan perubahan data pada kas transaksi paket la dengan fasilitas id : ' . $fasilitas_id . '.';
      }
      return $this->status;
   }

   # insert kas transaksi paket la
   function insert_kas_trans_fasilitas($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert data kas transaksi paket la
      $this->db->insert('kas_paket_la', $data);
      # get kas transaksi paket la id
      $kas_transaksi_paket_la_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan data kas transaksi paket la dengan id kas transaksi paket la : ' . $kas_transaksi_paket_la_id . '.';
      }
      return $this->status;
   }

   # update kas transaksi
   function update_kas_transaksi($data, $id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data kas transaksi
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('kas_paket_la', $data);
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
         $this->content = 'Melakukan perubahan data pada kas transaksi paket la dengan id : ' . $id . '.';
      }
      return $this->status;
   }

   function insert_kas_transaksi($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert data kas transaksi paket la
      $this->db->insert('kas_paket_la', $data);
      # get kas transaksi paket la id
      $kas_transaksi_paket_la_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan data kas transaksi paket la dengan id kas transaksi paket la : ' . $kas_transaksi_paket_la_id . '.';
      }
      return $this->status;
   }

   # delete kas transaksi paket la
   function delete_kas_transaksi_paket_la($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete request agen
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('kas_paket_la');
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
         $this->content = 'Melakukan penghapusan kas transaksi paket la dengan id ' . $id . '.';
      }
      return $this->status;
   }

   # delete paket la
   function delete_paket_la($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete kas transaksi paket la
      $this->db->where('paket_la_transaction_id	', $id)
         ->where('company_id', $this->company_id)
         ->delete('kas_paket_la');
      # delete hitory paket la
      $this->db->where('paket_la_transaction_id	', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_la_transaction_history');
      # delete paket la
      $this->db->where('id	', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_la_transaction');
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
         $this->content = 'Melakukan penghapusan data  paket la dengan id ' . $id . '.';
      }
      return $this->status;
   }

   /* Write log mst airlines */
   public function __destruct()
   {
      if ($this->write_log == 1) {
         if ($this->status == true) {
            if ($this->error == 0) {
               $this->syslog->write_log($this->content);
            }
         }
      }
   }
}
