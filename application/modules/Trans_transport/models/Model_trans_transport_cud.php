<?php

/**
 *  -----------------------
 *	Model trans transport cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_transport_cud extends CI_Model
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

   function insert_transaksi_transport($data, $data_detail)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert transport transaction data
      $this->db->insert('transport_transaction', $data);
      # get transaction transport id
      $transaction_transport_id = $this->db->insert_id();
      # insert data detail
      foreach ($data_detail as $key => $value) {
         $value['transport_transaction_id'] = $transaction_transport_id;
         $this->db->insert('transport_transaction_detail', $value);
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
         $this->content = 'Melakukan penambahan transaksi transport dengan nomor invoice ' . $data['invoice'] . ' dan dengan transaksi transport id ' . $transaction_transport_id . '.';
      }
      return $this->status;
   }

   # delete transport
   function delete_transport($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete transport transaction detail
      $this->db->where('transport_transaction_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('transport_transaction_detail');
      # delete transport transaction
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('transport_transaction');
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
         $this->content = 'Melakukan penghapusan data transaksi transport dengan transaksi id ' . $id . '.';
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
