<?php

/**
 *  -----------------------
 *	Model trans passport cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_passport_cud extends CI_Model
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

   # insert transaksi passport
   function insert_transaksi_passport($data, $data_detail)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert passport transaction data
      $this->db->insert('passport_transaction', $data);
      # get transaction passport id
      $transaction_passport_id = $this->db->insert_id();
      # insert data detail
      foreach ($data_detail as $key => $value) {
         $value['transaction_passport_id'] = $transaction_passport_id;
         $this->db->insert('passport_transaction_detail', $value);
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
         $this->content = 'Melakukan penambahan transaksi passport dengan nomor invoice ' . $data['invoice'] . ' dan dengan transaksi passport id ' . $transaction_passport_id . '.';
      }
      return $this->status;
   }

   # delete passport
   function delete_passport($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete passport transaction detail
      $this->db->where('transaction_passport_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('passport_transaction_detail');
      # delete passport transaction
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('passport_transaction');
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
         $this->content = 'Melakukan penghapusan data transaksi passport dengan transaksi id ' . $id . '.';
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
