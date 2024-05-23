<?php

/**
 *  -----------------------
 *	Model pengaturan cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengaturan_cud extends CI_Model
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

   # update pengaturan
   function update_pengaturan($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data kas
      $this->db->where('id', $this->company_id)
         ->update('company', $data);
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
         $this->content = 'Melakukan perubahan data pengaturan dengan ';
      }
      return $this->status;
   }

   function update_bank_transfer_company($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update data kas
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('company_bank_transfer', $data);
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
         $this->content = 'Melakukan perubahan data bank transfer.';
      }
      return $this->status;
   }

   function insert_bank_transfer_company($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert data
      $this->db->insert('company_bank_transfer', $data);
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
         $this->content = 'Melakukan penambahan data bank transfer.';
      }
      return $this->status;
   }

   # delete bank transfer
   function delete_bank_transfer($id){
      $this->db->trans_start();
     // handover falities
     $this->db->where('id', $id);
     $this->db->where('company_id', $this->company_id);
     $this->db->delete('company_bank_transfer');
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
         $this->content = 'Menghapus data bank transfer.';
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
