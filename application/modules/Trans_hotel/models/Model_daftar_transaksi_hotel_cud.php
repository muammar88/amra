<?php

/**
 *  -----------------------
 *	Model airlines cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_transaksi_hotel_cud extends CI_Model
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

   function insert_transaksi_hotel($data, $data_detail)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert hotel transaction data
      $this->db->insert('hotel_transaction', $data);
      # get transaction hotel id
      $transaction_hotel_id = $this->db->insert_id();
      # insert data detail
      foreach ($data_detail as $key => $value) {
         $value['transaction_hotel_id'] = $transaction_hotel_id;
         $this->db->insert('hotel_transaction_detail', $value);
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
         $this->content = 'Melakukan penambahan transaksi hote dengan nomor invoice ' . $data['invoice'] . ' dan dengan transaksi hote id ' . $transaction_hotel_id . '.';
      }
      return $this->status;
   }

   function delete_hotel($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete hotel transaction detail
      $this->db->where('transaction_hotel_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('hotel_transaction_detail');
      # delete hotel transaction
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('hotel_transaction');
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
         $this->content = 'Melakukan penghapusan data transaksi hotel dengan transaksi id ' . $id . '.';
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
