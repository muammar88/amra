<?php

/**
 *  -----------------------
 *	Model deposit saldo cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_deposit_saldo_cud extends CI_Model
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

   # insert deposit saldo
   function insert_deposit_saldo($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert deposit transaction data
      $this->db->insert('deposit_transaction', $data['deposit_transaction']);
      // get insert id
      $insert_id = $this->db->insert_id();
      # insert jurnal data
      $this->db->insert('jurnal', $data['jurnal']);
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
         $this->content = 'Melakukan penambahan data deposit dengan nomor transaksi ' . $data['deposit_transaction']['nomor_transaction'];
      }
      return array('status' => $this->status, 'id' => $insert_id);
   }

   # deposit transaction id
   function delete_deposit_transaksi($id, $nomor_transaction)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete deposit transaction
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('deposit_transaction');
      # delete in jurnal
     $this->db->where('source', 'depositsaldo:notransaction:'.$nomor_transaction)
         ->where('company_id', $this->company_id)
         ->delete('jurnal');    
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
         $this->content = 'Melakukan penghapusan data deposit transaction dengan id deposit transactions ' . $id . '.';
      }
      return $this->status;
   }

   /* Write log master data*/
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
