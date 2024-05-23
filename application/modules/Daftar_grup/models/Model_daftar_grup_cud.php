<?php

/**
 *  -----------------------
 *	Model daftar grup cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_grup_cud extends CI_Model
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

   # update daftar grup
   function update_daftar_grup($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data group
      $this->db->where('group_id', $id)
         ->where('company_id', $this->company_id)
         ->update('base_groups', $data);
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
         $this->content = 'Melakukan perubahan data grup dengan id grup ' . $id . '.';
      }
      return $this->status;
   }

   # insert daftar grup
   function insert_daftar_grup($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert base groups
      $this->db->insert('base_groups', $data);
      # get last id
      $id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan grup dengan grup id ' . $id;
      }
      return $this->status;
   }

   # delete grup
   function delete_grup($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete base users
      $this->db->where('group_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('base_users');
      # delete base group
      $this->db->where('group_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('base_groups');
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
         $this->content = 'Melakukan penghapusan data grup dengan grup id ' . $id . '.';
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
