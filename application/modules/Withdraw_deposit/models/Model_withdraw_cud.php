<?php

/**
 *  -----------------------
 *	Model artikel cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_withdraw_cud extends CI_Model
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

   // tolak withdraw
   public function tolak_request_withdraw($id, $data) {
      # Starting Transaction
      $this->db->trans_start();
      # update data airlines
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->update('withdraw_member', $data);
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
         $this->content = 'Melakukan proses reject request member dengan id ' . $id . '.';
      }
      return $this->status;
   }

   function approve_request_withdraw($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update data airlines
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->update('withdraw_member', $data['withdraw_member']);
      # insert deposit transaction data
      $this->db->insert('deposit_transaction', $data['deposit_transaction']);
      # insert jurnal data
      foreach ($data['jurnal'] as $key => $value) {
         $this->db->insert('jurnal', $value);
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
         $this->content = 'Melakukan proses approve request member dengan id ' . $id . '.';
      }
      return $this->status;
   }

   // update markup withdraw
   function update_markup_withdraw($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update data airlines
      $this->db->where('id', $id)
               ->update('company', $data);
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
         $this->content = 'Melakukan proses update markup withdraw dengan id ' . $id . '.';
      }
      return $this->status;
   }

      /* Write log mst topik */
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