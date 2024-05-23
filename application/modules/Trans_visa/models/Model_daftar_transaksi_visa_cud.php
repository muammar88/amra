<?php

/**
 *  -----------------------
 *	Model daftar transaksi visa cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_transaksi_visa_cud extends CI_Model
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

   # insert transaksi visa
   function insert_transaksi_visa($data, $data_detail)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert visa transaction data
      $this->db->insert('visa_transaction', $data);
      # get transaction visa id
      $transaction_visa_id = $this->db->insert_id();
      # insert data detail
      foreach ($data_detail as $key => $value) {
         $value['transaction_visa_id'] = $transaction_visa_id;
         $this->db->insert('visa_transaction_detail', $value);
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
         $this->content = 'Melakukan penambahan transaksi visa dengan nomor invoice ' . $data['invoice'] . ' dan dengan transaksi visa id ' . $transaction_visa_id . '.';
      }
      return $this->status;
   }

   #  update transaksi visa
   function update_transaksi_visa($id, $data, $data_detail)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data transaksi visa
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('visa_transaction', $data);
      # delete data detail
      $this->db->where('transaction_visa_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('visa_transaction_detail');
      # insert new data detail
      foreach ($data_detail as $key => $value) {
         $this->db->insert('visa_transaction_detail', $value);
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
         $this->content = 'Melakukan perubahan data transaksi visa dengan transaksi visa id ' . $id . '.';
      }
      return $this->status;
   }

   # delete transaksi visa
   function delete_transaksi_visa($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete visa transaction detail
      $this->db->where('transaction_visa_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('visa_transaction_detail');
      # delete visa transaction
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('visa_transaction');
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
         $this->content = 'Melakukan penghapusan data transaksi visa dengan transaksi id ' . $id . '.';
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
